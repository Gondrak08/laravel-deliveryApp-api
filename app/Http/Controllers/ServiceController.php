<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ServiceController extends Controller
{
    public function get()
    {
        $services = DB::table('services')
            ->join('stores', 'services.store_id', '=', 'stores.id')
            ->select('services.id', 'services.name', 'services.category_id', 'services.description', 'services.image_url', 'services.price', 'services.weight', 'services.is_available', 'services.is_promoted', 'services.is_discounted', 'services.discount_value', 'stores.id as store_id', 'stores.name as store_name')
            ->get();

        return response()->json($services);
    }

    public function getByCategory($id)
    {
        $services = Service::where('category_id', $id)->get();
        return response()->json($services);
    }

    public function getByStore($id)
    {
        $services = Service::where('store_id', $id)->get();
        return response()->json($services);
    }

    public function getById($id)
    {
        $service = Service::find($id);
        return response()->json($service);
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'sometimes',
                'description' => 'required',
                'price' => 'required|numeric',
                'weight' => 'sometimes|numeric',
            ]);

            $user = Auth::user();
            $store = Store::find($request->store_id);

            if ($store->user_id !== $user->id) {
                return response()->json(['message' => 'You do not own this store'], 403);
            }

            $service = Service::create([
                'name' => $request->name,
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
                'image_url' => $request->image_url ?? '',
                'description' => $request->description,
                'price' => $request->price,
                'weight' => $request->weight ?? 0,
                'is_available' => false,
                'is_promoted' => false,
                'is_discounted' => false,
                'discount_value' => $request->discount_value ?? 0,
            ]);

            return response()->json(['message' => 'Service added successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $service = Service::find($id);
            if ($service) {
                $service->delete();
                return response()->json(['message' => 'Service deleted'], 200);
            } else {
                return response()->json(['message' => 'Service not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json(['message' => 'Service not found'], 404);
            }
            $request->validate([
                'name' => 'sometimes|string',
                'category_id' => 'required|exists:categories,id',
                'store_id' => 'required|exists:stores,id',
                'price' => 'sometimes|numeric',
                'description' => 'sometimes|string',
                'weight' => 'sometimes|numeric',
            ]);

            $user = Auth::user();
            $store = Store::find($request->store_id);
            if (!$store || $store->user_id !== $user->id) {
                return response()->json(['message' => 'You do not own this store'], 403);
            }

            $service->update([
                'name' => $request->name ?? $service->name,
                'category_id' => $request->category_id,
                'price' => $request->price ?? $service->price,
                'description' => $request->description ?? $service->description,
                'weight' => $request->weight ?? $service->weight,
            ]);

            return response()->json(['message' => 'Service updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updatePicture(Request $request, $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return response()->json(['message' => 'Service not found'], 404);
            }
            $request->validate([
                'image_url' => 'required',
            ]);

            $service->update(['image_url' => $request->image_url]);

            return response()->json(['message' => 'Picture updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return response()->json(['message' => 'Service not found'], 404);
            }
            $request->validate([
                'is_promoted' => 'required|boolean',
            ]);

            $service->update(['is_promoted' => $request->is_promoted]);

            return response()->json(['message' => 'Product status changed'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
