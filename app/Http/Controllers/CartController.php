<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartController extends Controller
{
    public function handleCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');
        $quantity = (int) $request->input('quantity', 1);
        $action = $request->input('action');

        $cartKey = "cart:$userId";

        switch ($action) {
            case 'add':
                $currentValue = Redis::hGet($cartKey, $bookId);
                if ($currentValue) {
                    $currentData = json_decode($currentValue, true);
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

            default:
                return response()->json(['success' => false, 'message' => 'Unknown action.']);
        }

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Item added to cart');
    }

    public function removeFromCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');

        $cartKey = "cart:$userId";
        Redis::hDel($cartKey, $bookId);

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Item removed from cart');
    }

    public function getCart(Request $request)
    {
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);

        if (empty($cartItems)) {
            return view('cart.index', ['cartItems' => []]);
        }

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
            } else {

            }
        }

        return view('cart.index', ['cartItems' => $itemsDetails]);
    }



    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $lineItems = [];
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);

        foreach ($cartItems as $itemJson) {
            $item = json_decode($itemJson, true);
            if (is_array($item) && isset($item['quantity']) && isset($item['book_id'])) {
                $book = Book::find($item['book_id']);
                if ($book) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $book->name,
                                'images' => [$book->image],
                            ],
                            'unit_amount' => $book->price * 100,
                        ],
                        'quantity' => $item['quantity'],
                    ];
                }
            } else {

            }
        }

        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ]);
        return redirect($session->url);
    }

}
