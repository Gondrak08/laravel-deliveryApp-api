<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function get()
    {
        $categories = Category::orderBy('name')->get();
        return response()->json($categories);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'store_id' => 'required|exists:stores,id',
        ]);

        $user = Auth::user();

        // Verifica se o store_id pertence ao usuário autenticado
        $store = Store::find($request->store_id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        if ($store->user_id !== $user->id) {
            return response()->json(['message' => 'You do not own this store'], 403);
        }
        $category = Category::create([
            'name' => $request->name,
            'store_id' => $request->store_id,
        ]);

        return response()->json(['message' => 'Category added successfully'], 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string',
        ]);

        $category = Category::find($request->id);
        $category->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category updated successfully'], 200);
    }

    // Excluir uma categoria
    public function delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
