<?php

namespace App\Http\Controllers;

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

}
