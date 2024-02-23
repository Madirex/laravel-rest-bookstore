<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Rules\CategoryNameExists;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::all();

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        return view('categories.index', compact('categories'));
    }

    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($category);
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', new CategoryNameExists, 'max:255']
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía'], 400);
        }
        $category = new Category();
        $category->name = $request->input('name');

        $category->save();

        return response()->json($category, 201);
    }

    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
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
            return response()->json(['message' => 'Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía'], 400);
        }

        $category->name = $request->input('name');
        $category->save();

        return response()->json($category);
    }

    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $category->delete();

        return response()->json(null, 204);
    }
}
