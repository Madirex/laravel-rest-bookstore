<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Book;
use App\Models\CartCode;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Class OrdersController
 */
class OrdersController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $orders = Order::search($request->search)
            ->orderBy($request->order ?? 'id', $request->direction ?? 'asc')
            ->where('is_deleted', false)
            ->paginate(10);
        if ($request->expectsJson()) {
            return response()->json($orders);
        }
        return view('orders.index')->with('orders', $orders);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $order = Order::find($id);
        if ($request->expectsJson()) {
            return response()->json($order);
        }
        return view('orders.show')->with('order', $order);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $order = Order::find($id);
        $books = Book::all();
        $coupons = CartCode::all();

        if ($request->expectsJson()) {
            return response()->json($order);
        }
        return view('orders.edit')
            ->with('order', $order)
            ->with('books', $books)
            ->with('coupons', $coupons);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function addCouponToOrder(Request $request, $id)
    {
        try {
            $order = Order::find($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el pedido'], 400);
            }
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }

        //buscar en cartcode por code
        $cartCode = CartCode::where('code', $request->cart_code)->first();

        if ($cartCode == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el cupón'], 400);
            }
            flash('Código de cupón no válido')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        $order->cart_code = $cartCode->id;
        $order->save();
        flash('Cupón añadido correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        if ($request->expectsJson()) {
            return response()->json($order);
        }

        flash('Pedido actualizado correctamente')->success();
        return redirect()->route('orders.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function validateOrderLine(Request $request)
    {
        try {
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

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function addOrderLine(Request $request, $id)
    {
        $order = Order::find($id);

        if ($errorResponse = $this->validateOrderLine($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error: ' . $errorResponse)->error()->important();
            return redirect()->back()->withInput();
        }
        $book = Book::find($request->book_id);

        if ($book == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el libro'], 400);
            }

            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }

        if ($book->stock < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No hay suficiente stock'], 400);
            }

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
        $order->orderLines()->save($orderLine);
        $order->save();

        $book->stock -= $request->quantity;
        $cacheKey = 'book_' . $book->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $book->save();


        if ($request->expectsJson()) {
            return response()->json($orderLine);
        }

        flash('Línea de pedido añadida correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function generateInvoice($id)
    {
        $order = Order::find($id);
        $pdf = PDF::loadView('invoice', compact('order'));
        return $pdf->download('invoice.pdf');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateInvoiceToEmail($id)
    {
        $order = Order::find($id);
        $pdf = PDF::loadView('invoice', compact('order'));

        //enviar email
        $temp = tempnam(sys_get_temp_dir(), 'invoice');
        $pdf->save($temp);
        Mail::to($order->user->email)->send(new InvoiceMail($order, $temp));

        flash('Factura enviada correctamente')->success();
        return redirect()->back();
    }

    /**
     * @param Request|null $request
     * @param $id
     * @param $orderLineId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroyOrderLine(Request $request = null, $id, $orderLineId)
    {
        $order = Order::find($id);
        if ($order == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el pedido'], 400);
            }
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $orderLine = OrderLine::find($orderLineId);
        if ($orderLine == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado la línea de pedido'], 400);
            }
            flash('No se ha encontrado la línea de pedido')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado la línea de pedido');
        }
        $book = Book::find($orderLine->book_id);
        if ($book == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el libro'], 400);
            }
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }

        $order->subtotal -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
        $order->total_amount = $order->subtotal;
        $order->save();

        $book->stock += $orderLine->quantity;
        $cacheKey = 'book_' . $book->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $book->save();

        $orderLine->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Línea de pedido eliminada correctamente'], 200);
        }

        flash('Línea de pedido eliminada correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @param $id
     * @param $orderLineId
     * @return \Illuminate\Http\RedirectResponse
     */
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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el pedido'], 400);
            }
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $orderLine = OrderLine::find($request->order_line_id_edit);
        if ($orderLine == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado la línea de pedido'], 400);
            }
            flash('No se ha encontrado la línea de pedido')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado la línea de pedido');
        }
        $book = Book::find($request->book_id);
        if ($book == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el libro'], 400);
            }
            flash('No se ha encontrado el libro')->error();
            return redirect()->route('orders.edit', $order->id)->with('error', 'No se ha encontrado el libro');
        }


        $order->subtotal -= $orderLine->total;
        $order->total_lines -= $orderLine->quantity;
        $order->save();

        $book->stock += $orderLine->quantity;
        $cacheKey = 'book_' . $book->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
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
        $order->save();

        $book->stock -= $request->quantity;
        $cacheKey = 'book_' . $book->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $book->save();


        if ($request->expectsJson()) {
            return response()->json($orderLine);
        }
        flash('Línea de pedido actualizada correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::all();
        return view('orders.create')
            ->with('users', $users);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
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

        if ($request->expectsJson()) {
            return response()->json($order);
        }

        flash('Pedido creado correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($request, $id)
    {
        $order = Order::find($id);
        if ($order == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el pedido'], 400);
            }
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $order->is_deleted = true;
        $order->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pedido eliminado correctamente'], 200);
        }

        flash('Pedido eliminado correctamente')->success();
        return redirect()->route('orders.index');
    }


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function removeCartCode($request, $id) //TODO: DO
    {
        $order = Order::find($id);
        if ($order == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el pedido'], 400);
            }
            flash('No se ha encontrado el pedido')->error();
            return redirect()->route('orders.index')->with('error', 'No se ha encontrado el pedido');
        }
        $cartCode = CartCode::find($order->cart_code);
        if ($cartCode == null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se ha encontrado el cupón'], 400);
            }
            flash('No se ha encontrado el cupón')->error();
            return redirect()->route('orders.edit', $order->id);
        }

        $order->total_amount = $order->subtotal;

        $cartCode->available_uses += 1;

        $order->cart_code = null;

        $cartCode->save();
        $order->save();
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cupón eliminado correctamente'], 200);
        }
        flash('Cupón eliminado correctamente')->success();
        return redirect()->route('orders.edit', $order->id);
    }

    /**
     * @param $order
     * @param $cart_code
     */
    private function aplicateCoupon($order, $cart_code) //TODO: DO
    {

        if ($cart_code == null) {
            return;
        }

        $cartCode = CartCode::find($cart_code);

        if ($cartCode->percent_discount > 0) {
            $order->total_amount = $order->total_amount - ($order->total_amount * $cartCode->percent_discount / 100);
        } else {
            $order->total_amount = $order->total_amount - $cartCode->fixed_discount;
        }
        $order->total_amount = ($order->total_amount <= 0) ? 0 : $order->total_amount;

        $cartCode->available_uses -= 1;
        $order->save();
        $cartCode->save();
    }
}
