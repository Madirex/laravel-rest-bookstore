<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Rules\CategoryNameExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{

    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */
    public function index(Request $request)
    {
        $categories = Category::search($request->search)->orderBy('name', 'asc')->paginate(8);

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        return view('categories.index', compact('categories'));
    }

    /**
     * show
     * @param string $id id
     * @return mixed view or json
     */
    public function show(string $id, Request $request)
    {
        try {
            $cacheKey = 'categories_' . $id;
            if (Cache::has($cacheKey)) {
                $category = Cache::get($cacheKey);
            } else {
                $category = Category::findOrFail($id);
                Cache::put($cacheKey, $category, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
            }
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }
            flash('Categoría no encontrada')->error()->important();
            return redirect()->back()->withInput();
        }

        // Aquí es donde obtenemos los libros de la categoría
        $query = Book::where('category_name', $category->name);

        // Si hay un término de búsqueda, lo añadimos a la consulta
        if ($request->has('search')) {
            $search = trim(strtolower($request->get('search')));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        $books = $query->paginate(8);

        if (request()->expectsJson()) {
            return response()->json(['category' => $category, 'books' => $books]);
        }

        return view('categories.show', compact('category', 'books'));
    }

    /**
     * store
     * @param Request $request request
     * @return mixed view
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', new CategoryNameExists, 'max:255']
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía'], 400);
            }

            flash('Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía')->error()->important();
            return redirect()->back()->withInput();
        }
        $category = new Category();
        $category->name = $request->input('name');

        $cacheKey = 'categories_' . $category->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $category->save();

        if ($request->expectsJson()) {
            return response()->json($category, 201);
        }

        flash('Categoría creada correctamente')->success();
        return redirect()->route('categories.index');
    }

    /**
     * update
     * @param Request $request request
     * @param string $id id
     * @return mixed view
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }
            flash('Categoría no encontrada')->error()->important();
            return redirect()->back()->withInput();
        }

        try {
            $rulesToAdd = '';
            if (trim(strtolower($request->name)) != trim(strtolower($category->name))) {
                $rulesToAdd = new CategoryNameExists;
            }

            $request->validate([
                'name' => ['required', 'string', $rulesToAdd, 'max:255']
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía'], 400);
            }
            flash('Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía')->error()->important();
            return redirect()->back()->withInput();
        }

        // Obtener todos los books relacionados con la categoría
        $books = Book::where('category_name', $category->name)->get();

        $category->name = $request->input('name');

        // Actualizar el nombre de la categoría en los books relacionados
        foreach ($books as $book) {
            $book->category_name = $category->name;
            $cacheKey = 'book_' . $book->id;
            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
            $book->save();
        }

        $cacheKey = 'categories_' . $id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $category->save();

        if ($request->expectsJson()) {
            return response()->json($category);
        }
        flash('Categoría actualizada correctamente')->success();
        return redirect()->route('categories.index');
    }

    /**
     * destroy
     * @param string $id id
     * @return mixed view
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }
            flash('Categoría no encontrada')->error()->important();
            return redirect()->back()->withInput();
        }
        $cacheKey = 'categories_' . $id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $category->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        flash('Categoría eliminada correctamente')->success();
        return redirect()->route('categories.index');
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
        return view('categories.create');
    }

    /**
     * edit
     * @param $id id
     * @return mixed view
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('categories.edit')
            ->with('category', $category);
    }
}
