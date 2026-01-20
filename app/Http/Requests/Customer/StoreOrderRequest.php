<?php

namespace App\Http\Requests\Customer;

use App\Models\Product;
use App\Models\Topping;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'customer_name' => ['required', 'string', 'max:50'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.toppings' => ['nullable', 'array'],
            'items.*.toppings.*' => ['exists:toppings,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Pesanan tidak boleh kosong.',
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'items.array' => 'Format pesanan tidak valid.',
            'items.min' => 'Minimal pesan 1 item.',
            'items.*.product_id.required' => 'Produk harus dipilih.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah harus diisi.',
            'items.*.quantity.integer' => 'Jumlah harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah pesanan minimal 1.',
            'items.*.quantity.max' => 'Jumlah pesanan maksimal 99.',
            'items.*.toppings.array' => 'Format topping tidak valid.',
            'items.*.toppings.*.exists' => 'Topping yang dipilih tidak valid.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            if (empty($items))
                return;

            // Gather IDs to fetch in bulk
            $productIds = collect($items)->pluck('product_id')->unique();
            $toppingIds = collect($items)->pluck('toppings')->flatten()->filter()->unique();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $toppings = Topping::whereIn('id', $toppingIds)->get()->keyBy('id');

            foreach ($items as $index => $item) {
                // Check Product
                $productId = $item['product_id'] ?? null;
                $product = $products->get($productId);

                if ($product && !$product->is_available) {
                    $validator->errors()->add(
                        "items.{$index}.product_id",
                        "Produk '{$product->name}' stok sedang habis."
                    );
                }

                // Check Toppings
                if (isset($item['toppings']) && is_array($item['toppings'])) {
                    foreach ($item['toppings'] as $toppingId) {
                        $topping = $toppings->get($toppingId);

                        if (!$topping)
                            continue;

                        // Check Topping Availability
                        if (!$topping->is_available) {
                            $validator->errors()->add(
                                "items.{$index}.toppings",
                                "Topping '{$topping->name}' stok sedang habis."
                            );
                        }

                        // Check Category Mismatch
                        // e.g., Food topping for Drink product
                        if ($product && $product->category !== $topping->category) {
                            $validator->errors()->add(
                                "items.{$index}.toppings",
                                "Topping '{$topping->name}' ({$topping->category}) tidak bisa ditambahkan ke '{$product->name}' ({$product->category})."
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors()
        ], 422));
    }
}
