<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */

    public function index(Request $request)
    {
        $shops = Shop::search($request->search)->orderBy('id', 'asc')->paginate(8);

        if ($request->expectsJson()) {
            return response()->json($shops);
        }

        return view('shops.index')->with('shops', $shops);
    }

    /**
     * show
     * @param $id id
     * @return mixed view or json
     */

    public function show($id)
    {
        try {
            $shop = Shop::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Tienda no encontrada'], 404);
            }
            flash('Tienda no encontrada')->error()->important();
            return redirect()->back();
        }

        if (request()->expectsJson()) {
            return response()->json($shop);
        }

        return view('shops.show')->with('shop', $shop);
    }




    /**
     * update
     * @param Request $request request
     * @param $id id
     * @return string | mixed
     */
    public function update(Request $request, $id)
    {
        try {
            $shop = Shop::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tienda no encontrada'], 404);
            }
            flash('Tienda no encontrada')->error()->important();
            return redirect()->back();
        }

        if ($errorResponse = $this->validateShop($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al actualizar la tienda: ' . $errorResponse)->error()->important();
            return redirect()->back();
        }

        $shop->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($shop, 200);
        }

        flash('Tienda ' . $shop->name . ' actualizada con éxito.')->success()->important();
        return redirect()->route('shops.index');
    }
}
