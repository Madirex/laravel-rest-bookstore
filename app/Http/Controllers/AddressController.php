<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class AddressController
 */
class AddressController extends Controller
{
    /**
     * index
     * @param Request $request request
     * @return mixed view
     */
    public function index(Request $request)
    {
        $addresses = Address::search($request->search)->orderBy('street', 'asc')->paginate(8);

        if ($request->expectsJson()) {
            return response()->json($addresses);
        }

        return view('addresses.index', compact('addresses'));
    }

    /**
     * show
     * @param $id id
     * @param Request $request request
     * @return mixed view
     */
    public function show($id, Request $request)
    {
        $address = Address::find($id);

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->with('error', 'Dirección no encontrada');
        }

        if ($request->expectsJson()) {
            return response()->json($address);
        }

        return view('addresses.show', compact('address'));
    }

    /**
     * store
     * @param Request $request request
     * @return mixed view
     */
    public function store(Request $request)
    {
        $validation_bad = $this->validateAddress($request);
        if ($validation_bad) {
            return $validation_bad;
        }

        $address = Address::create($request->all());

        if ($request->expectsJson()) {
            return response()->json($address, 201);
        }

        flash('Dirección creada correctamente')->success();
        return redirect()->route('addresses.index');
    }

    /**
     * update
     * @param $id id
     * @param Request $request request
     * @return mixed view
     */
    public function update($id, Request $request)
    {
        $address = Address::find($id);

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back();
        }

        $validation_bad = $this->validateAddress($request);
        if ($validation_bad) {
            return $validation_bad;
        }

        $address->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($address);
        }

        flash('Dirección actualizada correctamente')->success();
        return redirect()->route('addresses.index');
    }

    /**
     * destroy
     * @param $id id
     * @param Request $request request
     * @return mixed mixed
     */
    public function destroy($id, Request $request)
    {
        $address = Address::find($id);

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back();
        }

        $address->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        flash('Dirección eliminada correctamente')->success();
        return redirect()->route('addresses.index');
    }

    /**
     * create
     * @return mixed view
     */
    public function create()
    {
        return view('addresses.create');
    }

    /**
     * edit
     * @param $id id
     * @return mixed view
     */
    public function edit($id)
    {
        $address = Address::find($id);

        if (!$address) {
            return redirect()->back()->with('error', 'Dirección no encontrada');
        }

        return view('addresses.edit', compact('address'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validateAddress(Request $request)
    {
        $request->validate([
            'street' => 'required',
            'number' => 'required',
            'city' => 'required',
            'province' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'addressable_id' => 'required',
            'addressable_type' => 'required',
        ]);

        if ($request->addressable_type === User::class) {
            $userId = intval($request->addressable_id);
            $user = \App\Models\User::find($userId);
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
                flash('Usuario no encontrado')->error();
                return redirect()->back();
            }
        }
        return null;
    }
}
