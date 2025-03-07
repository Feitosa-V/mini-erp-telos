<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
    
        // Cache por 10 minutos
        $orders = Cache::remember("orders_user_{$userId}", 600, function () use ($userId) {
            return Order::with('products')->where('created_by', $userId)->get();
        });

        return response()->json($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $productIds = collect($request->products)->pluck('id')->toArray();
        $existingProducts = Product::whereIn('id', $productIds)->pluck('id')->toArray();

        $invalidProducts = array_diff($productIds, $existingProducts);
        if (!empty($invalidProducts)) {
            return response()->json([
                'message' => 'Erro: Alguns produtos não existem no banco de dados!',
                'invalid_products' => $invalidProducts
            ], 422);
        }

        $order = Order::create([
            'supplier_id' => $request->supplier_id,
            'created_by' => Auth::id(),
            'order_date' => now(),
            'total_value' => 0,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        $total = 0;

        foreach ($request->products as $product) {
            $order->products()->attach($product['id'], [
                'unit_price' => $product['unit_price'],
                'quantity' => $product['quantity'],
            ]);
            $total += $product['unit_price'] * $product['quantity'];
        }

        $order->update(['total_value' => $total]);

        return response()->json([
            'message' => 'Pedido criado com sucesso!',
            'order' => $order->load('products'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('products')->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Pending,Completed,Canceled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Pedido atualizado!', 'order' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Pedido excluído!']);
    }
}
