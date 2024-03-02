<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Models\Address;


/**
 * Class ShopController
 */

class ShopController extends Controller
{
    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */
    public function index(Request $request)
    {
        // Aquí solo buscamos tiendas por nombre.
        $shops = Shop::where(function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }
        })->orderBy('id', 'asc')->paginate(8);

        if ($request->expectsJson()) {
            return response()->json($shops);
        }


        return view('shops.index', compact('shops'));
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
            // Asumiendo que tienes un sistema de notificaciones flash.
            flash('Tienda no encontrada')->error()->important();
            return redirect()->back();
        }

        if (request()->expectsJson()) {
            return response()->json($shop);
        }


        return view('shops.show')->with('shop', $shop);
    }


    /**
     * store
     * @param Request $request request
     * @return string | mixed
     */
    public function store(Request $request)
    {
        if ($errorResponse = $this->validateShop($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al crear la tienda: ' . $errorResponse)->error()->important();
            return redirect()->back();
        }

        $shop = $this->getShopStore($request);
        $shop->save();

        if ($request->expectsJson()) {
            return response()->json($shop, 201);
        }

        flash('Tienda ' . $shop->name . ' creada con éxito.')->success()->important();
        return redirect()->route('shops.index');
    }

    /**
     * update
     * @param Request $request request
     * @param string $id id
     * @return string | mixed
     */

    public function update(Request $request, string $id)
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

        if ($errorResponse = $this->validateShop($request, $shop->id)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al actualizar la tienda: ' . $errorResponse)->error()->important();
            return redirect()->back();
        }


        $shop->name = $request->input('name');
        $shop->address = $request->input('address');


        $shop->save();

        if ($request->expectsJson()) {
            return response()->json($shop);
        }

        flash('Tienda ' . $shop->name . ' actualizada con éxito.')->success()->important();
        return redirect()->route('shops.index');
    }


    /**
     * destroy
     * @param string $id id
     * @return mixed view
     */

    public function destroy(string $id)
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



        $shop->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        flash('Tienda ' . $shop->name . ' eliminada con éxito.')->success()->important();
        return redirect()->route('shops.index');
    }


    /**
     * validateShop
     * @param Request $request request

     * @return string|null error message
     */

    public function validateShop(Request $request, $shopId = null)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', new UniqueShopName($shopId)],
            'address' => ['required', new ValidJson],
            'active' => ['required', 'boolean'],

        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();

                if ($request->expectsJson()) {
                    return response()->json(['errors' => $errors], 400);
                }

                return implode(' ', $errors);
            }
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error durante la validación: ' . $e->getMessage()], 400);
            }

            return 'Error durante la validación: ' . $e->getMessage();
        }


        return null;
    }


    /// /// /// /// ///
    /// PARA VISTAS ///
    /// /// /// /// ///


    /**
     * create
     * @return mixed view
     */

    public function create()
    {
        return view('shops.create');
    }


    /**
     * edit
     * @param $id id
     * @return mixed view
     */

    public function edit($id)
    {
        $shop = Shop::with(['books', 'address'])->find($id);
        if (!$shop) {
            flash('Tienda no encontrada')->error();
            return redirect()->back();
        }
        return view('shops.edit', compact('shop'));
    }


    /**
     * getShopStore
     * @param Request $request
     * @return Shop Shop
     */

    public function getShopStore(Request $request): Shop
    {
        $shop = new Shop();
        $shop->name = $request->input('name');
        $shop->active = $request->input('active', true);
        return $shop;
    }




}

