<?php

namespace App\Http\Controllers;

use App\Models\CartCode;
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
        $this->validateCartCode($request);
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

        $this->validateCartCode($request, $cartcode);

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

    public function validateCartCode(Request $request, $cartcode = null): \Illuminate\Http\JsonResponse | null
    {
        $cartcodeadd = '';
        if ($cartcode != null){
            $cartcodeadd = ',' . $cartcode->id;
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cartcode,code' . $cartcodeadd . '|max:255',
            'percent_discount' => 'required|numeric',
            'fixed_discount' => 'required|numeric',
            'available_uses' => 'required|integer',
            'expiration_date' => 'required|date',
        ]);

        // Convertir los valores a números flotantes
        $cartcode->percent_discount = (float) $cartcode->percent_discount;
        $cartcode->fixed_discount = (float) $cartcode->fixed_discount;


        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }else{
            return null;
        }
    }


}
