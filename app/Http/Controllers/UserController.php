<?php

namespace App\Http\Controllers;

use App\Mail\EmailChangeMail;
use App\Models\User;
use App\Rules\CheckCorrectPassword;
use App\Rules\EmailExists;
use App\Rules\UniqueCaseInsensitive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * @param int $id
     * @return mixed view or json
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            flash('Usuario no encontrado')->error()->important();
            return redirect()->route('users.admin.index');
        }

        $this->removeUserImage($user);
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        }

        flash('Usuario eliminado correctamente')->success()->important();
        return redirect()->route('users.admin.index');
    }

    /**
     * Show the form for editing the specified user's image.
     *
     * @param int $id
     * @return \Illuminate\View\View The edit image view.
     */
    public function editImageUser($id)
    {
        $user = User::findOrFail($id);
        return view('users.admin.image')->with('user', $user);
    }

    /**
     * Update the specified user's image in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed view or json
     */
    public function updateImageUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            flash('Usuario no encontrado')->error()->important();
            return redirect()->route('users.admin.index');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $this->removeUserImage($user);
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $fileToSave = $user->id . '.' . $extension;
        $user->image = $image->storeAs('users', $fileToSave, 'public');
        $user->save();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Imagen de usuario actualizada correctamente']);
        }

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
     * @param int $id
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
     * @param \Illuminate\Http\Request $request
     * @return mixed view or json
     */
    public function store(Request $request)
    {
        $request->validate($this->rules());
        $user = new User;
        $user->name = $request->name;
        $user->email = strtolower($request->email);
        $user->username = ucfirst(strtolower($request->username));
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->image = User::$IMAGE_DEFAULT;
        $user->cart = json_encode("");
        $user->orders = json_encode([]);
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario creado correctamente']);
        }

        flash('Usuario creado correctamente')->success()->important();
        return redirect()->route('users.admin.show', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed view or json
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            flash('Usuario no encontrado')->error()->important();
            return redirect()->route('users.admin.index');
        }

        $request->validate($this->rules($user));
        $user->name = $request->name;
        $user->email = strtolower($request->email);
        $user->username = ucfirst(strtolower($request->username));
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->update();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario actualizado correctamente']);
        }

        flash('Usuario actualizado correctamente')->success()->important();
        return redirect()->route('users.admin.show', $user);
    }

    /**
     * Display the specified user.
     * @param $id
     * @return mixed view or json
     */
    public function showUser($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            flash('Usuario no encontrado')->error()->important();
            return redirect()->route('users.admin.index');
        }

        if (request()->expectsJson()) {
            return response()->json($user);
        }

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
        $request->validate($this->rules(null, false));
        $user->name = $request->name;
        $user->username = ucfirst(strtolower($request->username));
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->save();

        flash('Detalles de la cuenta actualizados con éxito')->success()->important();
        return redirect()->route('users.profile');
    }

    /**
     * Show the form for changing the authenticated user's password.
     *
     * @return \Illuminate\View\View The change password view.
     */
    public function changeEmailForm()
    {
        return view('users.change_email');
    }

    /**
     * Show the form for changing the authenticated user's password.
     * @param Request $request The request
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function requestEmailChange(Request $request)
    {
        $user = Auth::user();
        //comprobar si email es igual
        if (strtolower($request->new_email) == strtolower($user->email)) {
            flash('El nuevo correo electrónico no puede ser igual al actual.')->error()->important();
            return redirect()->back();
        }

        $request->validate([
            'new_email' => ['required', 'string', 'email', 'max:255', new UniqueCaseInsensitive('El email ya existe en la base de datos.', 'users', 'email')],
            'password' => 'required',
        ]);


        if (!Hash::check($request->password, $user->password)) {
            flash('La contraseña proporcionada es incorrecta.')->error()->important();
            return redirect()->back();
        }

        $newEmail = $request->input('new_email');

        $token = Str::random(60);
        Redis::set('email_change:' . $token, json_encode(['user_id' => $user->id, 'new_email' => $newEmail]));
        Redis::expire('email_change:' . $token, 60 * 60 * 24); // 24 horas (tiempo de expiración)

        // Envía el correo electrónico de confirmación
        Mail::to($newEmail)->send(new EmailChangeMail($token));
        flash('Se ha enviado un correo electrónico de confirmación a su nuevo correo electrónico.')->success()->important();
        return redirect()->route('users.profile');
    }

    /**
     * Confirm the email change.
     *
     * @param string $token The token
     * @return \Illuminate\Http\RedirectResponse Redirect to the user details page.
     */
    public function confirmEmailChange($token)
    {
        $emailChange = json_decode(Redis::get('email_change:' . $token));

        if (!$emailChange) {
            return back()->with('error', 'El token de confirmación es inválido.');
        }

        $user = User::find($emailChange->user_id);
        $user->email = strtolower($emailChange->new_email);
        $user->save();

        // Elimina el token de Redis
        Redis::del('email_change:' . $token);

        flash('Correo electrónico actualizado con éxito')->success()->important();
        return redirect()->route('users.profile');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(User $user = null, bool $requireEmail = true)
    {
        if ($user == null) {
            $user = Auth::user();
        }

        //si es json
        if (request()->expectsJson()) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', new UniqueCaseInsensitive('El nombre de usuario (username) ya existe en la base de datos.', 'users', 'username')],
                'surname' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255', 'regex:/^[6789][0-9]{8}$/'],
            ];

            if ($requireEmail) {
                $rules['email'] = ['required', 'string', 'email', 'max:255', new UniqueCaseInsensitive('El email ya existe en la base de datos.', 'users', 'email')];
            }
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', new UniqueCaseInsensitive('El nombre de usuario (username) ya existe en la base de datos.', 'users', 'username', $user->id)],
                'surname' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255', 'regex:/^[6789][0-9]{8}$/'],
            ];

            if ($requireEmail) {
                $rules['email'] = ['required', 'string', 'email', 'max:255', new UniqueCaseInsensitive('El email ya existe en la base de datos.', 'users', 'email', $user->id)];
            }
        }

        //si espera json
        if (request()->expectsJson()) {
            return $rules;
        }

        // si el usuario no es admin, se pide la contraseña
        if (!auth()->user()->hasRole('admin')) {
            $rules['password'] = ['required', new CheckCorrectPassword];
        }

        return $rules;
    }
}
