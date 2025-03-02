<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Products::with('store')->get();
            return response()->json($products, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getByCategory($id)
    {
        try {
            $products = Products::where('category_id', $id)->get();
            return response()->json($products, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'sometimes|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $user = Auth::user();
        $store = Store::find($request->store_id);
        if ($store->user_id !== $user->id) {
            return response()->json(['message' => 'You do not own this store'], 403);
        }

        $product = Products::create([
            'name' => $request->name,
            'store_id' => $request->store_id,
            'category_id' => $request->category_id,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Product added successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Products::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $request->validate([
                'name' => 'sometimes|string',
                'category_id' => 'required|exists:categories,id',
                'store_id' => 'required|exists:stores,id',
                'price' => 'sometimes|numeric',
                'description' => 'sometimes|string',
                'quantity' => 'sometimes|numeric',
            ]);

            $user = Auth::user();
            $store = Store::find($request->store_id);
            if (!$store || $store->user_id !== $user->id) {
                return response()->json(['message' => 'You do not own this store'], 403);
            }

            $product->update($request->only(['name', 'category_id', 'description', 'price', 'quantity']));

            return response()->json(['message' => 'Product updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Products::find($id);
            if ($product) {
                $product->delete();
                return response()->json(['message' => 'Product deleted'], 200);
            } else {
                return response()->json(['message' => 'Product not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
