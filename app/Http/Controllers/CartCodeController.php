<?php

namespace App\Http\Controllers;

use App\Models\CartCode;
use App\Rules\CartCodeCodeExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartCodeController extends Controller
{

    public function index(): \Illuminate\Http\JsonResponse
    {
        $cartcodes = CartCode::all();

        // Convertir los valores a números flotantes
        foreach ($cartcodes as $cartcode){
            $cartcode->percent_discount = (float) $cartcode->percent_discount;
            $cartcode->fixed_discount = (float) $cartcode->fixed_discount;
        }

        return response()->json($cartcodes);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //si no es nulo, retornar
        if ($errorResponse = $this->validateCartCode($request)) {return $errorResponse;}
        $cartcode = new CartCode();
        $cartcode->code = $request->input('code');
        $cartcode->percent_discount = $request->input('percent_discount');
        $cartcode->fixed_discount = $request->input('fixed_discount');
        $cartcode->available_uses = $request->input('available_uses');
        $cartcode->expiration_date = $request->input('expiration_date');

        $cartcode->save();

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float) $cartcode->percent_discount;
        $cartcode->fixed_discount = (float) $cartcode->fixed_discount;

        return response()->json($cartcode, 201);
    }


    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'CartCode no encontrado'], 404);
        }

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float) $cartcode->percent_discount;
        $cartcode->fixed_discount = (float) $cartcode->fixed_discount;

        return response()->json($cartcode);
    }


    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'CartCode no encontrado'], 404);
        }

        if ($errorResponse = $this->validateCartCode($request, $cartcode->code)) {return $errorResponse;}

        $cartcode->code = $request->input('code');
        $cartcode->percent_discount = $request->input('percent_discount');
        $cartcode->fixed_discount = $request->input('fixed_discount');
        $cartcode->available_uses = $request->input('available_uses');
        $cartcode->expiration_date = $request->input('expiration_date');
        $cartcode->save();

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float) $cartcode->percent_discount;
        $cartcode->fixed_discount = (float) $cartcode->fixed_discount;

        return response()->json($cartcode);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $cartcode = CartCode::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'CartCode no encontrado'], 404);
        }
        $cartcode->delete();

        return response()->json(null, 204);
    }

    public function validateCartCode(Request $request, $codestr = null): \Illuminate\Http\JsonResponse | null
    {
            $rulesToAdd = '';
            if ($codestr != null){
                if ($request->code != $codestr){
                    $rulesToAdd = new CartCodeCodeExists;
                }
            }else{
                $rulesToAdd = new CartCodeCodeExists;
            }

            try{
                $validator = Validator::make($request->all(), [
                    'code' => ['required', 'string', $rulesToAdd, 'max:255'],
                    'percent_discount' => 'required|numeric|min:0|max:999.99|regex:/^\d{1,3}(\.\d{1,2})?$/',
                    'fixed_discount' => 'required|numeric|min:0|max:999999.99|regex:/^\d{1,6}(\.\d{1,2})?$/',
                    'available_uses' => 'required|integer|min:0|max:1000000000',
                    'expiration_date' => 'required|date',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    return response()->json(['errors' => $errors], 400);
                }
            } catch (\Brick\Math\Exception\NumberFormatException $e) {
                return response()->json(['message' => 'Error al procesar una propiedad por no tener un número válido. Evita que exceda del tamaño límite.'], 400);
            }

            // Convertir los valores a números flotantes
            $request->percent_discount = (float) $request->percent_discount;
            $request->fixed_discount = (float) $request->fixed_discount;

            // Forzar a que el descuento no pueda tener más de 100% de descuento
            if ($request->percent_discount > 100){
                return response()->json(['message' => 'El descuento por porcentaje no puede ser mayor a 100%'], 400);
            }


            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 400);
            }else{
                return null;
            }
    }
}
