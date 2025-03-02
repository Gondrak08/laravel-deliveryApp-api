<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function getStores()
    {
        $stores = Store::with('user')->get();
        return response()->json($stores);
    }

    public function getStoresById($id)
    {
        $stores = Store::where('id', $id)->get();
        return response()->json($stores);
    }

    public function getStoresByUserId($id)
    {
        $stores = Store::where('user_id', $id)->get();
        return response()->json($stores);
    }

    public function addStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user_id' => 'required|exists:users,id',
            'address' => 'required',
            'description' => 'sometimes|string',
            'totalValue' => 'sometimes|numeric',
            'thumb' => 'sometimes|image'
        ]);

        $imageUrl = $request->file('thumb')->store('images');

        $store = Store::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'imageUrl' => $imageUrl,
            'address' => $request->address,
            'description' => $request->description,
            'totalValue' => $request->totalValue,
            'isOpen' => false
        ]);

        return response()->json(['message' => 'Store created successfully'], 200);
    }

    /**
     * @return updateStore()
     * * @param  $request
     */
    public function updateStore(Request $request)
    {
        // Log dos dados recebidos

        Log::info('Dados recebidos:', $request->all());

        // Log do arquivo
        if ($request->hasFile('thumb')) {
            Log::info('Arquivo recebido:', [
                'name' => $request->file('thumb')->getClientOriginalName(),
                'size' => $request->file('thumb')->getSize(),
                'mime' => $request->file('thumb')->getMimeType()
            ]);
        }

        $request->validate([
            'id' => 'required|exists:stores,id',
            'name' => 'sometimes|string',
            'address' => 'sometimes|string',
            'description' => 'sometimes|string',
            'thumb' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $store = Store::find($request->id);

        if ($store->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized: You are not the owner of this store'], 403);
        }

        // Atualiza os campos permitidos
        $store->update([
            'name' => $request->name ?? $store->name,
            'address' => $request->address ?? $store->address,  // <- Corrigido erro de digitação
            'description' => $request->description ?? $store->description,
        ]);

        // Processa a imagem, se enviada
        if ($request->hasFile('thumb')) {
            $imageUrl = $request->file('thumb')->store('images', 'public');  // Salva na pasta 'public/images'
            $store->update(['imageUrl' => $imageUrl]);
        }

        return response()->json(['message' => 'Store updated successfully', 'store' => $store], 200);
    }

    public function updateStoreStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:stores,id',
            'isOpen' => 'required|boolean'
        ]);

        $store = Store::find($request->id);
        if ($store->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized: You are not the owner of this store'], 403);
        }
        $store->update(['isOpen' => $request->isOpen]);

        return response()->json(['message' => 'Store status updated successfully'], 200);
    }

    public function deleteStore($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        if ($store->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized: You are not the owner of this store'], 403);
        }

        $store->delete();
        return response()->json(['message' => 'Store deleted successfully'], 200);
    }
}
