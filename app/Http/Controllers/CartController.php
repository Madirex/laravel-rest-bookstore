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
        $additionalQuantity = $request->input('quantity', 1);

        $cartKey = "cart:$userId";

        $currentQuantity = Redis::hGet($cartKey, $bookId);
        $newQuantity = $currentQuantity ? $currentQuantity + $additionalQuantity : $additionalQuantity;

        Redis::hSet($cartKey, $bookId, $newQuantity);

        return $request->expectsJson()
            ? response()->json(['success' => true, 'quantity' => $newQuantity])
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


        $bookIds = array_keys($cartItems);
        $bookIds = array_filter($bookIds, function($id) {
            return is_numeric($id);
        });
        $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');

        $itemsDetails = $books->map(function ($book) use ($cartItems) {
            return [
                'id' => $book->id,
                'name' => $book->name,
                'image' => $book->image,
                'price' => $book->price,
                'quantity' => $cartItems[$book->id],
                'stock' => $book->stock,
                'author' => $book->author,
            ];
        })->values()->all();

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
