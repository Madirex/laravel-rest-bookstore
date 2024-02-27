<?php

namespace App\Http\Controllers;

use App\Rules\UserUsernameExists;
use Illuminate\Support\Facades\Auth;

/**
 * The UserController class.
 */
class UserController extends Controller
{
    /**
     * Display the authenticated user details.
     *
     * @return \Illuminate\View\View The user view.
     */
    public function show()
    {
        $user = Auth::user();
        return view('users.user')->with('user', $user);
    }

    /**
     * Delete the authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse Redirect to the home page.
     */
    public function delete()
    {
        $user = Auth::user();
        Auth::logout();

        if ($user->delete()) {
            //mensaje
            flash('Cuenta eliminada')->success()->important();
            return redirect()->route('books.index');
        }

        flash('No se pudo eliminar la cuenta')->error()->important();
        return redirect()->back();
    }

    /**
     * Show the form for editing the authenticated user.
     *
     * @return \Illuminate\View\View The edit user view.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The request
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate(rules());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->username = $request->username;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        flash('Detalles de la cuenta actualizados con éxito')->success()->important();
        return redirect()->route('user.show');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:255', new UserUsernameExists],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

}
