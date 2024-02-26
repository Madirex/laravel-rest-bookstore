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

}
