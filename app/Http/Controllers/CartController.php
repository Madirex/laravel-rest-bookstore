<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Stripe\Checkout\Session;
use Stripe\Stripe;

/**
 * Class CartController
 */
class CartController extends Controller
{
    /**
     * Add a book to the cart
     * @param Request $request request
     * @return mixed view or json
     */
    public function handleCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');
        $quantity = (int)$request->input('quantity', 1);
        $action = $request->input('action');

        $cartKey = "cart:$userId";

        $book = Book::find($bookId);
        if (!$book) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Libro no encontrado'], 404)
                : back()->with('error', 'Libro no encontrado');
        }

        switch ($action) {
            case 'add':
                $currentValue = Redis::hGet($cartKey, $bookId);
                if ($currentValue) {
                    $currentData = json_decode($currentValue, true);
                    $newQuantity = $currentData['quantity'] + $quantity;
                    if ($newQuantity > $book->stock) {
                        if (!$request->expectsJson())
                            flash('No hay suficiente stock')->error()->important();
                        return $request->expectsJson()
                            ? response()->json(['error' => 'No hay suficiente stock'], 400)
                            : back()->with('error', 'No hay suficiente stock');
                    }
                    $currentData['quantity'] += $quantity;
                } else {
                    $currentData = ['book_id' => $bookId, 'quantity' => $quantity];
                }
                Redis::hSet($cartKey, $bookId, json_encode($currentData));
                break;

            case 'update':
                $currentData = ['book_id' => $bookId, 'quantity' => $quantity];
                Redis::hSet($cartKey, $bookId, json_encode($currentData));
                break;
        }
        // hacer flash si no espera json
        if (!$request->expectsJson()) {
            flash('Libro agregado el carrito')->success()->important();
        }
        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Libro agregado al carrito');
    }


    public function removeFromCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');

        $cartKey = "cart:$userId";
        Redis::hDel($cartKey, $bookId);

        // hacer flash si no espera json
        if (!$request->expectsJson()) {
            flash('Libro eliminado del carrito')->success()->important();
        }
        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Libro eliminado del carrito');
    }

    /**
     * Get the number of items in the cart
     * @param Request $request request
     * @return int|mixed view
     */
    public function itemCount(Request $request)
    {
        // obtener el carrito
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);

        $count = 0;
        foreach ($cartItems as $itemJson) {
            $item = json_decode($itemJson, true);
            if (is_array($item) && isset($item['quantity'])) {
                $count += $item['quantity'];
            }
        }

        return $count;
    }

    /**
     * Get the cart
     * @param Request $request request
     * @return mixed view
     */
    public function getCart(Request $request)
    {
        // obtener el carrito
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);
        // si el carrito esta vacio
        if (empty($cartItems)) {
            return view('cart.index', ['cartItems' => []]);
        }
        // obtener los detalles de los libros
        $itemsDetails = [];
        foreach ($cartItems as $itemJson) {
            $item = json_decode($itemJson, true);

            if (is_array($item) && isset($item['book_id']) && isset($item['quantity'])) {
                $book = Book::find($item['book_id']);
                if ($book) {
                    $itemsDetails[] = [
                        'id' => $book->id,
                        'name' => $book->name,
                        'image' => $book->image,
                        'price' => $book->price,
                        'quantity' => $item['quantity'],
                        'stock' => $book->stock,
                        'author' => $book->author,
                    ];
                }
            }
        }
        return view('cart.index', ['cartItems' => $itemsDetails]);
    }


    public function checkout(Request $request)
    {
        // Configura la clave API de Stripe con la variable de entorno correspondiente.
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // Obtiene el ID del usuario actual.
        $userId = $request->user()->id;
        // Intenta obtener un order_id del request, si es que el usuario está completando un pedido existente.
        $orderId = $request->input('order_id');
        $order = null;

        // Si se proporcionó un order_id, intenta encontrar un pedido existente que coincida.
        if ($orderId) {
            $order = Order::where('id', $orderId)->where('user_id', $userId)->where('status', 'unpaid')->first();

            // Si el pedido existe, prepara los items para la sesión de pago de Stripe.
            if ($order) {
                foreach ($order->orderLines as $orderLine) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $orderLine->book->name,
                                'images' => [$orderLine->book->image],
                                'metadata' => ['book_id' => $orderLine->book->id],
                            ],
                            'unit_amount' => $orderLine->price * 100, // El precio debe estar en centavos
                        ],
                        'quantity' => $orderLine->quantity,
                    ];
                }
            }
        }

        // Si no se encontró un pedido existente o no se proporcionó un order_id, procesa los items del carrito.
        if (!$order) {
            $cartKey = "cart:$userId";
            $cartItems = Redis::hGetAll($cartKey);

            if (empty($cartItems)) {
                flash('El carrito está vacío.')->error()->important();
                return back(); // Redirige al usuario si el carrito está vacío.
            }

            $totalPrice = 0;
            $lineItems = [];
            foreach ($cartItems as $itemJson) {
                $item = json_decode($itemJson, true);
                if (is_array($item) && isset($item['quantity']) && isset($item['book_id'])) {
                    $book = Book::find($item['book_id']);

                    if (!$book || $item['quantity'] > $book->stock) {
                        flash('No hay suficiente stock para el libro seleccionado.')->error()->important();
                        return back(); // Redirige si no hay stock suficiente.
                    }

                    $totalPrice += $item['quantity'] * $book->price;
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $book->name,
                                'images' => [$book->image],
                                'metadata' => ['book_id' => $book->id],
                            ],
                            'unit_amount' => $book->price * 100,
                        ],
                        'quantity' => $item['quantity'],
                    ];
                }
            }

            // Crea un nuevo pedido si no existe uno.
            if (!$order) {
                $order = new Order([
                    'user_id' => $userId,
                    'status' => 'unpaid',
                    'total_amount' => $totalPrice,
                    'total_lines' => count($lineItems),
                    'is_deleted' => false,
                ]);
                $order->save();
                // Debes guardar las líneas del pedido (OrderLine) aquí, usando el objeto $order recién creado.
            }
        }

        // Crea la sesión de pago con Stripe y redirige al usuario para completar el pago.
        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('cart.success', ['order_id' => $order->id], true),
            'cancel_url' => route('cart.cancel', [], true),
        ]);

        return redirect($session->url);
    }



    public function success(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('books.index')->with('error', 'Order not found.');
        }
        $order->status = 'paid';
        $order->finished_at = now();
        $order->save();

        if($order->status == 'paid'){
            $userId = $request->user()->id;
            $cartKey = "cart:$userId";
            Redis::del($cartKey);
        }
        flash('Pago realizado con éxito')->success()->important();
        return view('cart.checkout-success', compact('order'));
    }

    public function cancel()
    {
        flash('Pago cancelado')->error()->important();
        return view('cart.checkout-cancel');
    }

}
