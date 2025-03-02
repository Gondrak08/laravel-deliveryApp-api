<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class OrderController extends Controller
{
    /**
     * Lista todos os pedidos.
     */
    public function index()
    {
        try {
            $orders = Order::all();
            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtém pedidos de uma loja específica.
     */
    public function getByStore($id)
    {
        try {
            $orders = Order::where('store_id', $id)->get();
            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cria um novo pedido.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'list' => 'required|array',
            'address' => 'required|string',
            'payment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $address = $request->address;
            $payment = $request->payment;

            foreach ($request->list as $item) {
                Order::create([
                    'uuid' => Str::uuid(),
                    'store_id' => $item['store_id'],
                    'shopping_list' => json_encode($item['shoppingList']),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'address' => $address,
                    'payment' => $payment,
                ]);
            }

            return response()->json(['message' => 'Your order arrived successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove um pedido pelo ID.
     */
    public function destroy($id)
    {
        try {
            $order = Order::find($id);
            if ($order) {
                $order->delete();
                return response()->json(['message' => 'Order deleted'], 200);
            } else {
                return response()->json(['message' => 'Order not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
