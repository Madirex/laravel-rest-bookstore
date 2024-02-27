<?php

namespace App\Http\Controllers;

use App\Rules\CheckCorrectPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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
        $this->removeUserImage($user);
        Auth::logout();
        $user->isDeleted = true;
        $user->save();
        flash('Cuenta eliminada')->success()->important();
        return redirect()->route('books.index');
    }

    /**
     * Edit the authenticated user's image.
     *
     * @return \Illuminate\View\View The edit image view.
     */
    public function editImage()
    {
        $user = Auth::user();
        return view('users.image')->with('user', $user);
    }

    /**
     * Update the authenticated user's image.
     *
     * @param \Illuminate\Http\Request $request The request
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function updateImage(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $this->removeUserImage($user);
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $fileToSave = $user->id . '.' . $extension;
        $user->image = $image->storeAs('users', $fileToSave, 'public');
        $user->save();

        flash('Imagen del usuario actualizada con éxito')->success()->important();
        return redirect()->route('users.profile');
    }

    /**
     * Remove the authenticated user's image.
     *
     * @param $user User
     * @return void
     */
    public function removeUserImage($user): void
    {
        if ($user->image != User::$IMAGE_DEFAULT && Storage::exists('public/' . $user->image)) {
            Storage::delete('public/' . $user->image);
        }
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
        $request->validate($this->rules());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        flash('Detalles de la cuenta actualizados con éxito')->success()->important();
        return redirect()->route('users.profile');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['required', new CheckCorrectPassword],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }
}
