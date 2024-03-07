<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Rules\CategoryNameNotExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Class BookController
 */
class BookController extends Controller
{

    /**
     * index
     * @param Request $request request
     * @return mixed view or json
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $books = Book::search($request->search)->orderBy('id', 'asc')->paginate(8);
            return response()->json($books);
        }
        $books = Book::where('active', true)
            ->where('stock', '>', 0)
            ->search($request->search)
            ->orderBy('id', 'asc')
            ->paginate(8);
        return view('books.index')->with('books', $books);
    }

    /**
     * show
     * @param $id id
     * @return mixed view or json
     */
    public function show($id)
    {
        $cacheKey = 'book_' . $id;

        try {
            if (Cache::has($cacheKey)) {
                $book = Cache::get($cacheKey);
            } else {
                $book = Book::findOrFail($id);
                Cache::put($cacheKey, $book, 3600); // Almacenar en caché durante 1 hora (3600 segundos)
            }
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Libro no encontrado'], 404);
            }
            flash('Libro no encontrado')->error()->important();
            return redirect()->back()->withInput();
        }

        if (request()->expectsJson()) {
            return response()->json($book);
        }

        if ($book->active == false) {
            flash('Libro no encontrado')->error()->important();
            return redirect()->back()->withInput();
        }

        return view('books.show')->with('book', $book);
    }

    /**
     * store
     * @param Request $request request
     * @return string | mixed
     */
    public function store(Request $request)
    {
        if ($errorResponse = $this->validateBook($request)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al crear el libro: ' . $errorResponse)->error()->important();
            return redirect()->back()->withInput();
        }
        $book = $this->getBookStore($request);

        $cacheKey = 'book_' . $book->id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $book->save();

        //comprobar si espera json
        if ($request->expectsJson()) {
            return response()->json($book, 201);
        }
        flash('Libro ' . $book->name . '  creado con éxito.')->success()->important();
        return redirect()->route('books.index');
    }

    /**
     * update
     * @param Request $request request
     * @param string $id id
     * @return string | mixed
     */
    public function update(Request $request, string $id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Libro no encontrado'], 404);
            }
            flash('Libro no encontrado')->error()->important();
            return redirect()->back()->withInput();
        }

        if ($errorResponse = $this->validateBook($request, $book->isbn)) {
            if ($request->expectsJson()) {
                return $errorResponse;
            }
            flash('Error al actualizar el libro: ' . $errorResponse)->error()->important();
            return redirect()->back()->withInput();
        }

        $book->isbn = $request->input('isbn');
        $book->name = $request->input('name');
        $book->author = $request->input('author');
        $book->publisher = $request->input('publisher');
        $book->description = $request->input('description');
        $book->price = $request->input('price');
        $book->stock = $request->input('stock');
        $book->category_name = $request->input('category_name');

        $cacheKey = 'book_' . $id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        $book->save();

        if ($request->expectsJson()) {
            return response()->json($book);
        }

        flash('Libro ' . $book->name . ' actualizado con éxito.')->success()->important();
        return redirect()->route('books.index');


    }

    /**
     * destroy
     * @param string $id id
     * @return mixed view
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Libro no encontrado'], 404);
            }

            flash('Libro no encontrado')->error()->important();
            return redirect()->back()->withInput();
        }
        $this->removeBookImage($book);

        $cacheKey = 'book_' . $id;
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }

        $book->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        flash('Libro ' . $book->name . '  eliminado con éxito.')->success()->important();
        return redirect()->route('books.index');
    }

    /**
     * updateImage
     * @param Request $request request
     * @param $id id
     * @return mixed view
     */
    public function updateImage(Request $request, $id)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $book = Book::find($id);
            $this->removeBookImage($book);
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $fileToSave = $book->id . '.' . $extension;
            $book->image = $image->storeAs('books', $fileToSave, 'public'); // Guardamos en storage/app/public/books

            $cacheKey = 'book_' . $id;
            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
            $book->save();

            if ($request->expectsJson()) {
                return response()->json($book);
            }

            flash('Imagen del libro ' . $book->name . ' actualizada con éxito.')->success()->important();
            return redirect()->route('books.index');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al actualizar la imagen del libro'], 400);
            }
            flash('Error al actualizar la imagen del libro' . $e->getMessage())->error()->important();
            return redirect()->back()->withInput();
        }
    }

    /**
     * validateBook
     * @param Request $request request
     * @param $bookISBN string|null
     * @return string|null error message
     */
    public function validateBook(Request $request, $bookISBN = null)
    {
        $rulesToAdd = '';
        // lo comento porque al tener varias tiendas puede haber libros iguales asignados en diferentes tiendas
        /*if ($bookISBN != null) {
            if (trim(strtolower($request->isbn)) != trim(strtolower($bookISBN))) {
                $rulesToAdd = new ISBNNameExists;
            }
        } else {
            $rulesToAdd = new ISBNNameExists;
        }*/

        try {

            $validator = Validator::make($request->all(), [
                'isbn' => ['required', 'string', $rulesToAdd, 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'author' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'description' => 'required|string|max:2040',
                'price' => 'required|numeric|min:0|max:999999.99|regex:/^\d{1,6}(\.\d{1,2})?$/',
                'stock' => 'required|integer|min:0|max:1000000000',
                'category_name' => ['required', 'string', new CategoryNameNotExists],
                'shop_id' => ['required', 'exists:shops,id'],
            ]);


            if ($validator->fails()) {
                $errors = $validator->errors()->all();

                if ($request->expectsJson()) {
                    return response()->json(['errors' => $errors], 400);
                }

                return implode(' ', $errors);
            }
        } catch (\Brick\Math\Exception\NumberFormatException $e) {

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al procesar una propiedad por no tener un número válido. Evita que exceda del tamaño límite.'], 400);
            }

            return 'Error al procesar una propiedad por no tener un número válido. Evita que exceda del tamaño límite.';
        }

        return null;
    }

    /**
     * removeBookImage
     * @param $book Book
     * @return void remove image
     */
    public function removeBookImage($book): void
    {
        if ($book->image != Book::$IMAGE_DEFAULT && Storage::exists('public/' . $book->image)) {
            Storage::delete('public/' . $book->image);
        }
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
        $categories = Category::all();
        return view('books.create')->with('categories', $categories);
    }

    /**
     * edit
     * @param $id id
     * @return mixed view
     */
    public function edit($id)
    {
        $book = Book::find($id);
        $categories = Category::all();
        return view('books.edit')
            ->with('book', $book)
            ->with('categories', $categories);
    }

    /**
     * editImage
     * @param $id id
     * @return mixed view
     */
    public function editImage($id)
    {
        $book = Book::find($id);
        return view('books.image')->with('book', $book);
    }

    /**
     * getBookStore
     * @param Request $request
     * @return Book Book
     */
    public function getBookStore(Request $request): Book
    {
        $book = new Book();
        $book->isbn = $request->input('isbn');
        $book->name = $request->input('name');
        $book->author = $request->input('author');
        $book->publisher = $request->input('publisher');
        $book->image = Book::$IMAGE_DEFAULT;
        $book->description = $request->input('description');
        $book->price = $request->input('price');
        $book->stock = $request->input('stock');
        $book->shop_id = $request->input('shop_id');
        $book->category_name = $request->input('category_name');
        $book->active = true;
        return $book;
    }


}
