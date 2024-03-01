<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        Redis::hSet($cartKey, $bookId, $quantity);
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cart.cart');
    }


    public function removeFromCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');

        $cartKey = "cart:$userId";
        Redis::hDel($cartKey, $bookId);
        return redirect()->route('cart.cart');
    }

    public function getCart(Request $request)
    {
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);
        $total = 0;

        $itemsDetails = [];
        foreach ($cartItems as $bookId => $quantity) {
            $book = Book::find($bookId);
            $itemsDetails[] = [
                'id' => $book->id,
                'name' => $book->name,
                'image' => $book->image,
                'price' => $book->price,
                'quantity' => $quantity,
                'stock' => $book->stock,
                'author' => $book->author,
            ];
        }

        return view('cart.index', ['cartItems' => $itemsDetails]);
    }

//    public function clearCart(Request $request)
//    {
//        $userId = $request->user()->id;
//        $cartKey = "cart:$userId";
//        Redis::del($cartKey);
//
//        return $this->getCart($request);
//    }
}
