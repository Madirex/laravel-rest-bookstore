<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(): \Illuminate\Http\JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories);
    }


    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories,name|max:255'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la categoría: debe ser única, tener máximo 255 caracteres y no debe estar vacía'], 400);
        }
        $category = new Category();
        $category->name = $request->input('name');

        $category->save();


        return response()->json($category, 201);
    }


    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($category);
    }


    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id . '|max:255'
        ]);

        $category->name = $request->input('name');
        $category->save();

        return response()->json($category);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse
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
