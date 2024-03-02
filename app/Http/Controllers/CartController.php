<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
    public function addToCart(Request $request)
    {
        $userId = $request->user()->id;
        $bookId = $request->input('book_id');
        $additionalQuantity = $request->input('quantity', 1);

        $cartKey = "cart:$userId";

        $currentQuantity = Redis::hGet($cartKey, $bookId);
        $newQuantity = $currentQuantity ? $currentQuantity + $additionalQuantity : $additionalQuantity;

        Redis::hSet($cartKey, $bookId, $newQuantity);

        // hacer flash si no espera json
        if (!$request->expectsJson()) {
            flash('Libro agregado el carrito')->success()->important();
        }
        return $request->expectsJson()
            ? response()->json(['success' => true, 'quantity' => $newQuantity])
            : back()->with('success', 'Libro agregado al carrito');
    }


    /**
     * Remove a book from the cart
     * @param Request $request request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
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
     * Get the cart
     * @param Request $request request
     * @return mixed view
     */
    public function getCart(Request $request)
    {
        $userId = $request->user()->id;
        $cartKey = "cart:$userId";
        $cartItems = Redis::hGetAll($cartKey);

        if (empty($cartItems)) {
            return view('cart.index', ['cartItems' => []]);
        }

        $bookIds = array_keys($cartItems);
        $bookIds = array_filter($bookIds, function ($id) {
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
