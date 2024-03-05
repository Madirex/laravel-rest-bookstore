<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CartCode;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $cacheKey = 'orders_' . md5($request->fullUrl());
        if (Cache::has($cacheKey)) {
            $orders = Cache::get($cacheKey);
        } else {
            $orders = Order::search($request->search)
                ->orderBy($request->order ?? 'id', $request->direction ?? 'asc')
                ->where('is_deleted', false)
                ->paginate(10);

          //  Cache::put($cacheKey, $orders, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
        }
        return view('orders.index')->with('orders', $orders);
    }

    public function show($id)
    {
        $cacheKey = 'category_' . $id;
        if (Cache::has($cacheKey)) {
            $order = Cache::get($cacheKey);
        } else {
            $order = Order::find($id);
           // Cache::put($cacheKey, $order, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
        }
        return view('orders.show')->with('order', $order);
    }

    public function edit($id)
    {
        $order = Order::find($id);
        $books = Book::all();
        $cupons = CartCode::all();
        return view('orders.edit')
            ->with('order', $order)
            ->with('books', $books)
            ->with('cupons', $cupons);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();
        flash('Pedido actualizado correctamente')->success();
        return redirect()->route('orders.index');
    }

    public function validateOrderLine(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|numeric|min:1',
                'book_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();

                if ($request->expectsJson()) {
                    return response()->json(['errors' => $errors], 400);
                }

                return implode(' ', $errors);
            }
        } catch (\Brick\Math\Exception\NumberFormatException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al procesar una propiedad por no tener un número válido. Evita que exceda del tamaño límite.'], 400);
            }

            return 'Error al procesar una propiedad por no tener un número válido. Evita que exceda del tamaño límite.';
        }
        return null;
    }

    public function addOrderLine(Request $request, $id)
    {
        $order = Order::find($id);

        $type = $request->type;

        if ($errorResponse = $this->validateOrderLine($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error: ' . $errorResponse)->error()->important();
            return redirect()->back()->withInput();
        }
        $book = Book::find($request->book_id);

        if ($book == null) {
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }

        if ($book->stock < $request->quantity) {
            flash('No hay suficiente stock')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No hay suficiente stock');
        }

        $orderLine = new OrderLine();
        $orderLine->quantity = $request->quantity;
        $orderLine->price = $book->price;
        $orderLine->total = $request->quantity * $book->price;
        $orderLine->subtotal = $request->quantity * $book->price;
        $orderLine->book_id = $book->id;
        $orderLine->order_id = $order->id;
        $orderLine->selected = true;
        $orderLine->save();

        $order->subtotal += $orderLine->total;
        $order->total_lines += $orderLine->quantity;
        $order->total_amount = $order->subtotal;
        $this->aplicateCupon($order, $order->cartCodeId);
        $order->orderLines()->save($orderLine);
        $order->save();

        $book->stock -= $request->quantity;
        $book->save();



        flash('Línea de pedido añadida correctamente')->success();


        return redirect()->route('orders.edit', $order->id);
    }

    public function destroyOrderLine($id, $orderLineId)
    {
        $order = Order::find($id);
        if ($order == null) {
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $orderLine = OrderLine::find($orderLineId);
        if ($orderLine == null) {
            flash('No se ha encontrado la línea de pedido')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado la línea de pedido');
        }
        $book = Book::find($orderLine->book_id);
        if ($book == null) {
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }

        $order->subtotal -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
        $order->total_amount = $order->subtotal;
        $this->aplicateCupon($order, $order->cartCodeId);
        $order->save();

        $book->stock += $orderLine->quantity;
        $book->save();

        $orderLine->delete();

        if ($orderLine->type == 'book') {
            flash('Línea de pedido eliminada correctamente')->success();
        } elseif ($orderLine->type == 'coupon') {
            flash('Cupón eliminado correctamente')->success();
        }
        return redirect()->route('orders.edit', $order->id);
    }

    public function updateOrderLine($id, Request $request)
    {
        if ($errorResponse = $this->validateOrderLine($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error: ' . $errorResponse)->error()->important();
            return redirect()->back()->withInput();
        }

        $order = Order::find($id);
        if ($order == null) {
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $orderLine = OrderLine::find($request->order_line_id_edit);
        if ($orderLine == null) {
            flash('No se ha encontrado la línea de pedido')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado la línea de pedido');
        }
        $book = Book::find($request->book_id);
        if ($book == null) {
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }


        $order->subtotal -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
        $order->save();

        $book->stock += $orderLine->quantity;
        $book->save();

        $orderLine->quantity = $request->quantity;
        $orderLine->price = $book->price;
        $orderLine->total = $request->quantity * $book->price;
        $orderLine->subtotal = $request->quantity * $book->price;
        $orderLine->book_id = $book->id;
        $orderLine->save();

        $order->total_lines += $orderLine->quantity;
        $order->subtotal += $orderLine->subtotal;
        $order->total_amount = $order->subtotal;
        $this->aplicateCupon($order, $order->cartCodeId);
        $order->save();

        $book->stock -= $request->quantity;
        $book->save();


        return redirect()->route('orders.edit', $order->id);
    }

    public function create()
    {
        $users = User::all();
        return view('orders.create')
            ->with('users', $users);
    }

    public function store(Request $request)
    {
        $order = new Order();
        $order->status = 'pending';
        $order->user_id = $request->user_id;
        $order->total_amount = 0;
        $order->total_lines = 0;
        $order->is_deleted = false;
        $order->subtotal = 0;
        $order->save();
        flash('Pedido creado correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order == null) {
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $order->is_deleted = true;
        $order->save();
        flash('Pedido eliminado correctamente')->success();
        return redirect()->route('orders.index');
    }

    public function addCartCode($id, Request $request)
    {
        $order = Order::find($id);
        if ($order == null) {
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $cartCode = CartCode::find($request->cart_code);
        if ($cartCode == null) {
            flash('Código no válido')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        if($cartCode->available_uses <= 0){
            flash('Código no disponible')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        if($cartCode->expiration_date < date('Y-m-d')){
            flash('Código expirado')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        if($order->cartCodeId != null){
            flash('Ya hay un cupón aplicado')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        if($order->total_amount == 0){
            flash('No se puede aplicar un cupón a un pedido sin importe')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        $order->cartCodeId = $cartCode->id;

        if($cartCode->percent_discount > 0){
            $order->total_amount = $order->total_amount - ($order->total_amount * $cartCode->percent_discount / 100);
        }else{
            $order->total_amount = $order->total_amount - $cartCode->fixed_discount;
        }

        $order->total_amount = ($order->total_amount <= 0) ? 0 : $order->total_amount;

        $cartCode->available_uses -= 1;

        $cartCode->save();
        $order->save();
        flash('Cupón aplicado correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    public function removeCartCode($id)
    {
        $order = Order::find($id);
        if ($order == null) {
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $cartCode = CartCode::find($order->cartCodeId);
        if ($cartCode == null) {
            flash('No se ha encontrado el cupón')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        $order->total_amount = $order->subtotal;

        $cartCode->available_uses += 1;

        $order->cartCodeId = null;

        $cartCode->save();
        $order->save();
        flash('Cupón eliminado correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    private function aplicateCupon($order, $cartCodeId){

        if($cartCodeId == null){
            return;
        }

        $cartCode = CartCode::find($cartCodeId);

        if($cartCode->percent_discount > 0){
            $order->total_amount = $order->total_amount - ($order->total_amount * $cartCode->percent_discount / 100);
        }else{
            $order->total_amount = $order->total_amount - $cartCode->fixed_discount;
        }
        $order->total_amount = ($order->total_amount <= 0) ? 0 : $order->total_amount;

        $cartCode->available_uses -= 1;

        $order->save();

        $cartCode->save();
    }
}
