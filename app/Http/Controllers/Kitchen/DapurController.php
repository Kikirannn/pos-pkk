<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kitchen\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DapurController extends Controller
{
    /**
     * Display the Kitchen Display System (KDS) interface.
     */
    public function index(): View
    {
        return view('dapur.index');
    }

    /**
     * Get list of pending orders (New & Processing).
     * Ordered by priority: New first, then Oldest first.
     */
    public function getPendingOrders(): JsonResponse
    {
        // Fetch all orders for today (New, Processing, Done)
        $orders = Order::today()
            ->with(['orderItems.product', 'orderItems.toppings'])
            // Custom Sort: New -> Processing -> Done
            ->orderByRaw("FIELD(status, 'new', 'processing', 'done')")
            // Sort by Time: Oldest orders first (FIFO)
            ->orderBy('created_at', 'asc')
            ->get();

        // Transform data for frontend
        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'status' => $order->status,
                'total_price' => (float) $order->total_price,
                'formatted_total' => $order->formatted_total,         // Accessor
                'formatted_created_at' => $order->formatted_created_at, // Accessor
                'elapsed_time' => $order->elapsed_time,               // Accessor
                'items_count' => $order->orderItems->count(),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => $item->product ? $item->product->name : 'Unknown Product',
                        'quantity' => $item->quantity,
                        // Map toppings names
                        'toppings' => $item->toppings->pluck('name')->toArray(),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'pending_count' => $orders->count(),
            'orders' => $formattedOrders,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        // Find order
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // Update status using Model helper to handle timestamps
        // updateStatus() returns boolean
        $updated = $order->updateStatus($request->status);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pesanan.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->order_number} berhasil diperbarui menjadi " . ucfirst($request->status),
            // Return updated status data if needed by frontend
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'processing_at' => $order->processing_at,
                'completed_at' => $order->completed_at,
            ]
        ]);
    }
}
