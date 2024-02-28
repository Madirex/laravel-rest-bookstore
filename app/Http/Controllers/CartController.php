<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');
        $quantity = $request->input('quantity', 1);

        $cartKey = "cart:$userId";
        $currentQuantity = Redis::hGet($cartKey, $bookId) ?? 0;
        Redis::hSet($cartKey, $bookId, $currentQuantity + $quantity);

        $cartItems = Redis::hGetAll($cartKey);

        return view('cart.index', ['cartItems' => $cartItems]);
    }


    public function removeFromCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');

        $cartKey = "cart:$userId";
        Redis::hDel($cartKey, $bookId);

        return response()->json(['message' => 'Book removed from cart successfully']);
    }

    public function getCart(Request $request)
    {
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);

        return view('cart.index', ['cartItems' => $cartItems]);
    }

    public function clearCart(Request $request)
    {
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        Redis::del($cartKey);

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
