<?php

namespace App\Http\Controllers;

use App\Models\CartCode;
use App\Rules\CartCodeCodeExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Clase CartCodeController
 */
class CartCodeController extends Controller
{

    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */
    public function index(Request $request)
    {
        $cartcodes = CartCode::search($request->search)->orderBy('code', 'asc')->paginate(10);

        // Convertir los valores a números flotantes
        foreach ($cartcodes as $cartcode) {
            $cartcode->percent_discount = (float)$cartcode->percent_discount;
            $cartcode->fixed_discount = (float)$cartcode->fixed_discount;
        }

        if ($request->expectsJson()) {
            return response()->json($cartcodes);
        }

        return view('cartcodes.index')->with('cartcodes', $cartcodes);
    }

    /**
     * show
     * @param string $id id
     * @return mixed view or json
     */
    public function show(string $id)
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'CartCode no encontrado'], 404);
            }
            flash('CartCode no encontrado')->error()->important();
            return redirect()->back();
        }

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float)$cartcode->percent_discount;
        $cartcode->fixed_discount = (float)$cartcode->fixed_discount;

        if (request()->expectsJson()) {
            return response()->json($cartcode);
        }
        return view('cartcodes.show')->with('cartcode', $cartcode);
    }

    /**
     * store
     * @param Request $request request
     * @return mixed
     */
    public function store(Request $request)
    {
        //si no es nulo, retornar
        if ($errorResponse = $this->validateCartCode($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al crear código de tienda: ' . $errorResponse)->error()->important();
            return redirect()->back();
        }
        $cartcode = new CartCode();
        $cartcode->code = $request->input('code');
        $cartcode->percent_discount = $request->input('percent_discount');
        $cartcode->fixed_discount = $request->input('fixed_discount');
        $cartcode->available_uses = $request->input('available_uses');
        $cartcode->expiration_date = $request->input('expiration_date');

        $cartcode->save();

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float)$cartcode->percent_discount;
        $cartcode->fixed_discount = (float)$cartcode->fixed_discount;

        if ($request->expectsJson()) {
            return response()->json($cartcode, 201);
        }
        flash('Código de tienda creado correctamente')->success();
        return redirect()->route('cartcodes.show', $cartcode->id);
    }

    /**
     * update
     * @param Request $request request
     * @param string $id id
     * @return mixed view or json
     */
    public function update(Request $request, string $id)
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CartCode no encontrado'], 404);
            }
            flash('Código de tienda no encontrado')->error()->important();
            return redirect()->back();
        }

        if ($errorResponse = $this->validateCartCode($request, $cartcode->code)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al actualizar el código de tienda: ' . $errorResponse)->error()->important();
            return redirect()->back();
        }

        $cartcode->code = $request->input('code');
        $cartcode->percent_discount = $request->input('percent_discount');
        $cartcode->fixed_discount = $request->input('fixed_discount');
        $cartcode->available_uses = $request->input('available_uses');
        $cartcode->expiration_date = $request->input('expiration_date');
        $cartcode->save();

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float)$cartcode->percent_discount;
        $cartcode->fixed_discount = (float)$cartcode->fixed_discount;

        if ($request->expectsJson()) {
            return response()->json($cartcode);
        }
        flash('Código de tienda actualizado correctamente')->success();
        return redirect()->route('cartcodes.show', $cartcode->id);
    }

    /**
     * destroy
     * @param string $id id
     * @return mixed view or json
     */
    public function destroy(string $id)
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {

            if (request()->expectsJson()) {
                return response()->json(['message' => 'CartCode no encontrado'], 404);
            }
            flash('Código de tienda no encontrado')->error()->important();
            return redirect()->back();
        }
        $cartcode->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }
        flash('Código de tienda eliminado correctamente')->success();
        return redirect()->route('cartcodes.index');
    }

    /**
     * validateCartCode
     * @param Request $request request
     * @param string|null $codestr codestr
     * @return mixed null or json
     */
    public function validateCartCode(Request $request, $codestr = null)
    {
        $rulesToAdd = '';
        if ($codestr != null) {
            if ($request->code != $codestr) {
                $rulesToAdd = new CartCodeCodeExists;
            }
        } else {
            $rulesToAdd = new CartCodeCodeExists;
        }

        try {
            $validator = Validator::make($request->all(), [
                'code' => ['required', 'string', $rulesToAdd, 'max:255'],
                'percent_discount' => 'required|numeric|min:0|max:999.99|regex:/^\d{1,3}(\.\d{1,2})?$/',
                'fixed_discount' => 'required|numeric|min:0|max:999999.99|regex:/^\d{1,6}(\.\d{1,2})?$/',
                'available_uses' => 'required|integer|min:0|max:1000000000',
                'expiration_date' => 'required|date',
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

        // Convertir los valores a números flotantes
        $request->percent_discount = (float)$request->percent_discount;
        $request->fixed_discount = (float)$request->fixed_discount;

        // Forzar a que el descuento no pueda tener más de 100% de descuento
        if ($request->percent_discount > 100) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'El descuento por porcentaje no puede ser mayor a 100%'], 400);
            }
            return 'El descuento por porcentaje no puede ser mayor a 100%';
        }

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            return $validator->errors()->first();
        } else {
            return null;
        }
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
        return view('cartcodes.create');
    }

    /**
     * edit
     * @param $id id
     * @return mixed view
     */
    public function edit($id)
    {
        $cartcode = CartCode::find($id);
        return view('cartcodes.edit')
            ->with('cartcode', $cartcode);
    }
}
