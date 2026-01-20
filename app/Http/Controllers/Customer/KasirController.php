<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasirController extends Controller
{
    /**
     * Display the self-service cashier interface.
     */
    public function index(): View
    {
        return view('kasir.index');
    }

    /**
     * Get available products via API.
     * Optional filter: ?category=makanan
     */
    public function getProducts(Request $request): JsonResponse
    {
        $query = Product::available();

        if ($request->has('category') && in_array($request->category, ['makanan', 'minuman'])) {
            $query->byCategory($request->category);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price, // Accessor
                'image_url' => $product->image_url,             // Accessor
                'description' => $product->description,
            ];
        });

        return response()->json($products);
    }

    /**
     * Get available toppings grouped by category.
     */
    public function getToppings(): JsonResponse
    {
        $toppings = Topping::available()->get();

        $grouped = [
            'makanan' => $toppings->where('category', 'makanan')->values(),
            'minuman' => $toppings->where('category', 'minuman')->values(),
        ];

        return response()->json($grouped);
    }

    /**
     * Store a new order.
     * Handles DB transaction and price snapshots.
     */
    public function storeOrder(StoreOrderRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // 1. Generate Order Number
            $orderNumber = Order::generateOrderNumber();

            // 2. Create Order Header (Initial total 0)
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'total_price' => 0,
                'status' => 'new',
            ]);

            $grandTotal = 0;
            $itemsData = $request->items;

            // Pre-fetch all products and toppings to avoid N+1 in loop
            $productIds = collect($itemsData)->pluck('product_id')->toArray();
            $toppingIds = collect($itemsData)->pluck('toppings')->flatten()->filter()->toArray();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $toppings = Topping::whereIn('id', $toppingIds)->get()->keyBy('id');

            // 3. Process Items
            foreach ($itemsData as $itemData) {
                $product = $products->get($itemData['product_id']);

                // Safety check (should be handled by Request validation, but good for robust logic)
                if (!$product)
                    continue;

                $quantity = $itemData['quantity'];
                $productPrice = $product->price;

                // Create Order Item
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $productPrice, // Snapshot price
                ]);

                // Calculate Item Subtotal (Product only for now)
                $itemSubtotal = $productPrice * $quantity;

                // 4. Process Toppings
                if (!empty($itemData['toppings'])) {
                    foreach ($itemData['toppings'] as $toppingId) {
                        $topping = $toppings->get($toppingId);

                        if ($topping) {
                            // Attach to Pivot with Price Snapshot
                            $orderItem->toppings()->attach($topping->id, [
                                'price' => $topping->price
                            ]);

                            // Add topping price to calculation
                            // (Topping Price * Quantity of Item) -> asumsi topping berlaku per item
                            $itemSubtotal += ($topping->price * $quantity);
                        }
                    }
                }

                $grandTotal += $itemSubtotal;
            }

            // 5. Update Order Total
            $order->update(['total_price' => $grandTotal]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'order_number' => $order->order_number,
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error if needed: \Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
