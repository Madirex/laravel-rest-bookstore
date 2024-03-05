<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = 'addresses_' . md5($request->fullUrl());

        if (Cache::has($cacheKey)) {
            $addresses = Cache::get($cacheKey);
        } else {
            $addresses = Address::search($request->search)->orderBy('street', 'asc')->paginate(8);
            Cache::put($cacheKey, $addresses, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
        }

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
        try {
            $cacheKey = 'address_' . $id;
            if (Cache::has($cacheKey)) {
                $address = Cache::get($cacheKey);
            } else {
                $address = Address::findOrFail($id);
                Cache::put($cacheKey, $address, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
            }

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }
            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput();
        }

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput()->with('error', 'Dirección no encontrada');
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
        // TODO: el país se fuerza a España (si en un futuro se quiere ampliar a otros países, se debe de quitar la línea de abajo)
        $request->merge(['country' => 'España']);
        $request->merge(['province' => $this->getProvinceName($request->postal_code)]);

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
        try {
            $address = Address::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput();
        }

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput();
        }

        // TODO: el país se fuerza a España (si en un futuro se quiere ampliar a otros países, se debe de quitar la línea de abajo)
        $request->merge(['country' => 'España']);
        $request->merge(['province' => $this->getProvinceName($request->postal_code)]);

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
        try {
            $address = Address::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput();
        }

        if (!$address) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Dirección no encontrada'], 404);
            }

            flash('Dirección no encontrada')->error();
            return redirect()->back()->withInput();
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
            return redirect()->back()->withInput()->with('error', 'Dirección no encontrada');
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
            'street' => ['required', 'max:255'],
            'number' => ['required', 'max:255'],
            'city' => ['required', 'max:255'],
            //'province' => ['required', 'max:255'], TODO: esto se comenta porque se obtiene a partir del código postal, si se quiere ampliar a otros países habría que descomentarlo o modificar el sistema actual de detección de provincia a partir del código postal
            //'country' => ['required', 'max:255'], TODO: volver a poner la validación si se quiere ampliar a otros países
            'postal_code' => ['required', 'max:5', 'min:5', 'regex:/^[0-9]+$/'], //TODO: en un futuro habría que modificar esto si se quiere agregar implementación de otros países
            'addressable_id' => 'required',
            'addressable_type' => 'required',
        ]);

        $province = $this->getProvinceName($request->postal_code);
        if ($province === '-') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Código postal no válido'], 422);
            }
            flash('Código postal no válido')->error();
            return redirect()->back()->withInput();
        }

        if ($request->addressable_type === User::class) {
            $userId = intval($request->addressable_id);
            $user = \App\Models\User::find($userId);
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
                flash('Usuario no encontrado')->error();
                return redirect()->back()->withInput();
            }
        }
        return null;
    }

    /**
     * Obtener el nombre de la provincia a partir del código postal
     * @param $postalCode string
     * @return string
     */
    private function getProvinceName($postalCode)
    {
        $postalCodeProvinces = [
            '01' => "Álava", '02' => "Albacete", '03' => "Alicante", '04' => "Almería", '05' => "Ávila",
            '06' => "Badajoz", '07' => "Baleares", '08' => "Barcelona", '09' => "Burgos", '10' => "Cáceres",
            '11' => "Cádiz", '12' => "Castellón", '13' => "Ciudad Real", '14' => "Córdoba", '15' => "A Coruña",
            '16' => "Cuenca", '17' => "Girona", '18' => "Granada", '19' => "Guadalajara", '20' => "Gipuzkoa",
            '21' => "Huelva", '22' => "Huesca", '23' => "Jaén", '24' => "León", '25' => "Lleida",
            '26' => "La Rioja", '27' => "Lugo", '28' => "Madrid", '29' => "Málaga", '30' => "Murcia",
            '31' => "Navarra", '32' => "Ourense", '33' => "Asturias", '34' => "Palencia", '35' => "Las Palmas",
            '36' => "Pontevedra", '37' => "Salamanca", '38' => "Santa Cruz de Tenerife", '39' => "Cantabria", '40' => "Segovia",
            '41' => "Sevilla", '42' => "Soria", '43' => "Tarragona", '44' => "Teruel", '45' => "Toledo",
            '46' => "Valencia", '47' => "Valladolid", '48' => "Bizkaia", '49' => "Zamora", '50' => "Zaragoza",
            '51' => "Ceuta", '52' => "Melilla"
        ];

        $firstTwoDigits = substr($postalCode, 0, 2);
        $provinceName = $postalCodeProvinces[$firstTwoDigits] ?? "-";

        return $provinceName;
    }


    /** ** ** ** ** ** ** ** **
     * USUARIOS AUTENTICADOS *
     * ** ** ** ** ** ** ** ** */
    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function editUserAddress(User $user)
    {
        if ($user->address) {
            return view('users.address.edit', ['address' => $user->address]);
        } else {
            return redirect()->route('users.address.create', ['user' => $user]);
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function createUserAddress(User $user)
    {
        return view('users.address.create', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUserAddress(Request $request)
    {
        $data = $request->all();
        $data['addressable_id'] = auth()->user()->id;
        $data['addressable_type'] = User::class;

        // TODO: el país se fuerza a España (si en un futuro se quiere ampliar a otros países, se debe de quitar la línea de abajo)
        $request->merge(['country' => 'España']);
        $request->merge(['province' => $this->getProvinceName($request->postal_code)]);

        $validation_bad = $this->validateAddress(new Request($data));
        if ($validation_bad) {
            return $validation_bad;
        }

        $address = new Address($request->all());
        auth()->user()->address()->save($address);

        flash('Dirección creada correctamente')->success();
        return redirect()->route('users.profile');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserAddress(Request $request)
    {
        $data = $request->all();
        $data['addressable_id'] = auth()->user()->id;
        $data['addressable_type'] = User::class;

        // TODO: el país se fuerza a España (si en un futuro se quiere ampliar a otros países, se debe de quitar la línea de abajo)
        $request->merge(['country' => 'España']);
        $request->merge(['province' => $this->getProvinceName($request->postal_code)]);

        $validation_bad = $this->validateAddress(new Request($data));
        if ($validation_bad) {
            return $validation_bad;
        }

        $address = auth()->user()->address;
        $address->fill($request->all());
        $address->save();

        flash('Dirección actualizada correctamente')->success();
        return redirect()->route('users.profile');
    }

    /**
     * @param Request $request
     * @param $addressId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUserAddress(Request $request, $addressId)
    {
        $address = Address::find($addressId);

        if (!$address || $address->addressable_id != auth()->user()->id) {
            flash('Dirección no encontrada o no pertenece al usuario autenticado')->error();
            return redirect()->back()->withInput();
        }

        $address->delete();

        flash('Dirección eliminada correctamente')->success();
        return redirect()->route('users.profile');
    }


}
