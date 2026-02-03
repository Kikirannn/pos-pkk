<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Display the inventory management page.
     */
    public function index(): View
    {
        // Fetch all data for initial load
        // Sorting: Category (Ascending), Created At (Ascending - Oldest First)
        // This ensures the order matches the database insertion order unless user changes categories
        $products = Product::orderBy('category')->orderBy('id')->get();
        $toppings = Topping::orderBy('category')->orderBy('id')->get();

        return view('inventory.index', compact('products', 'toppings'));
    }

    // ==========================================
    // 📦 PRODUCT MANAGEMENT
    // ==========================================

    public function storeProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:makanan,minuman',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|max:2048', // Max 2MB
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean'
        ]);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'stock' => $validated['stock'],
            'is_available' => $request->boolean('is_available', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ]);
    }

    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:makanan,minuman',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean'
        ]);

        // Handle Image Upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image_file')->store('products', 'public');
        }

        $product->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'stock' => $validated['stock'],
            'is_available' => $request->boolean('is_available'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product
        ]);
    }

    public function destroyProduct(int $id): JsonResponse
    {
        Log::info("Attempting to delete product ID: {$id}");
        try {
            $product = Product::findOrFail($id);
            $product->delete(); // Soft delete

            Log::info("Product ID: {$id} soft deleted successfully.");

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete product ID: {$id}. Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProduct(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // ==========================================
    // 🥗 TOPPING MANAGEMENT
    // ==========================================

    public function storeTopping(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|in:makanan,minuman',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean'
        ]);

        $topping = Topping::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'is_available' => $request->boolean('is_available', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topping berhasil ditambahkan.',
            'data' => $topping
        ]);
    }

    public function updateTopping(Request $request, int $id): JsonResponse
    {
        $topping = Topping::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|in:makanan,minuman',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean'
        ]);

        $topping->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'is_available' => $request->boolean('is_available'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topping berhasil diperbarui.',
            'data' => $topping
        ]);
    }

    public function destroyTopping(int $id): JsonResponse
    {
        try {
            $topping = Topping::findOrFail($id);
            $topping->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Topping berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus topping: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTopping(int $id): JsonResponse
    {
        $topping = Topping::findOrFail($id);
        return response()->json($topping);
    }
}
