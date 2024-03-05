<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CartCode;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
        return view('orders.edit')
            ->with('order', $order)
            ->with('books', $books);
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
        if ($type == 'book') {
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
            $orderLine->type = 'book';
            $orderLine->save();

            $order->total_amount += $orderLine->total;
            $order->total_lines += $orderLine->quantity;
            $order->orderLines()->save($orderLine);
            $order->save();

            $book->stock -= $request->quantity;
            $book->save();

        } elseif ($type == 'coupon') {

            $cartCode = CartCode::where('code', $request->coupon)->first();
            if ($cartCode == null) {
                flash('Código no válido')->error();
                return redirect()->route('orders.edit', $order->id);
            }
            $orderLine = new OrderLine();
            $orderLine->quantity = 1;
            $orderLine->order_id = $order->id;
            $orderLine->selected = true;
            $orderLine->type = 'coupon';

            if ($cartCode->percent_discount != 0) {
                $orderLine->price = $cartCode->percent_discount;
                $orderLine->total = $order->total_amount * ($cartCode->percent_discount / 100);
                $orderLine->subtotal = $order->total_amount * ($cartCode->percent_discount / 100);
            } elseif ($cartCode->fixed_discount != 0) {
                $orderLine->price = $cartCode->fixed_discount;
                $orderLine->total = $cartCode->fixed_discount;
                $orderLine->subtotal = $cartCode->fixed_discount;
            }

            $order->total_amount -= $orderLine->total;
            $order->total_lines += $orderLine->quantity;
            $order->orderLines()->save($orderLine);

            $cartCode->available_uses -= 1;
            $cartCode->save();

            $order->save();
        }

        if ($type == 'book') {
            flash('Línea de pedido añadida correctamente')->success();
        } elseif ($type == 'coupon') {
            flash('Cupón aplicado correctamente')->success();
        }

        return redirect()->route('orders.edit', $order->id);
    }

    public function generateInvoice($id)
    {
        $order = Order::find($id);
        $pdf = PDF::loadView('invoice', compact('order'));

        return $pdf->download('invoice.pdf');
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

        $order->total_amount -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
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
        $book = Book::find($orderLine->book_id);
        if ($book == null) {
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }

        $order->total_amount -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
        $order->save();

        $book->stock += $orderLine->quantity;
        $book->save();

        $orderLine->quantity = $request->quantity;
        $orderLine->price = $book->price;
        $orderLine->total = $request->quantity * $book->price;
        $orderLine->subtotal = $request->quantity * $book->price;
        $orderLine->save();

        $order->total_amount += $orderLine->total;
        $order->total_lines += $orderLine->quantity;
        $order->save();

        $book->stock -= $request->quantity;
        $book->save();

        if ($orderLine->type == 'book') {
            flash('Línea de pedido actualizada correctamente')->success();
        } elseif ($orderLine->type == 'coupon') {
            flash('Cupón actualizado correctamente')->success();
        }
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
}
