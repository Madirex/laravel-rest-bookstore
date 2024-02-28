<?php

namespace App\Http\Controllers;

use App\Rules\CheckCorrectPassword;
use App\Rules\UniqueCaseInsensitive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * The UserController class.
 */
class UserController extends Controller
{
     /// /// ///
    /// ADMIN ///
     /// /// ///
    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */
    public function index(Request $request)
    {
        $users = User::search($request->search)->orderBy('id', 'asc')->paginate(8);

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        return view('users.admin.index')->with('users', $users);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse Redirect to the users page.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->removeUserImage($user);
        $user->delete();
        flash('Usuario eliminado correctamente')->success()->important();
        return redirect()->route('users.admin.index');
    }

    /**
     * Show the form for editing the specified user's image.
     *
     * @param  int  $id
     * @return \Illuminate\View\View The edit user image view.
     */
    public function editImageUser($id)
    {
        $user = User::findOrFail($id);
        return view('users.admin.image')->with('user', $user);
    }

    /**
     * Update the specified user's image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function updateImageUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $this->removeUserImage($user);
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $fileToSave = $user->id . '.' . $extension;
        $user->image = $image->storeAs('users', $fileToSave, 'public');
        $user->save();
        flash('Imagen de usuario actualizada correctamente')->success()->important();
        return redirect()->route('users.admin.show', $user);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View The create user view.
     */
    public function create()
    {
        return view('users.admin.create');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View The edit user view.
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('users.admin.edit')->with('user', $user);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules());
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->image = User::$IMAGE_DEFAULT;
        $user->cart = json_encode("");
        $user->orders = json_encode([]);
        $user->save();

        flash('Usuario creado correctamente')->success()->important();
        return redirect()->route('users.admin.show', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate($this->rules($user));
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->update();
        flash('Usuario actualizado correctamente')->success()->important();
        return redirect()->route('users.admin.show', $user);
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return view('users.admin.show')->with('user', $user);
    }


       /// /// /// /// ///
    /// USER AUTENTICADO ///
      /// /// /// /// ///
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
     * Edit the authenticated user's image
     *
     * @return \Illuminate\View\View The edit image view.
     */
    public function editImage()
    {
        $user = Auth::user();
        return view('users.image')->with('user', $user);
    }

    /**
     * Update the authenticated user's image
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
     * Remove the authenticated user's image
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
    public function rules(User $user = null)
    {
        if ($user == null) {
            $user = Auth::user();
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', new UniqueCaseInsensitive('users', 'email', $user->id)],
            'username' => ['required', 'string', 'max:255', new UniqueCaseInsensitive('users', 'username', $user->id)],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];

        // si el usuario no es admin, se pide la contraseña
        if (!auth()->user()->hasRole('admin')) {
            $rules['password'] = ['required', new CheckCorrectPassword];
        }

        return $rules;
    }
}
