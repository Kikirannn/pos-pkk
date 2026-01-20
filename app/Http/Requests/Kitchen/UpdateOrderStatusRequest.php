<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderStatusRequest extends FormRequest
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
            'status' => ['required', 'in:new,processing,done'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status pesanan harus diisi.',
            'status.in' => 'Status tidak valid (pilih: new, processing, atau done).',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $newStatus = $this->input('status');

            // Verify Order Exists
            $orderId = $this->route('id'); // Get ID from route parameter
            $order = \App\Models\Order::find($orderId);

            if (!$order)
                return;

            $currentStatus = $order->status;

            // Define allowed transitions
            $transitions = [
                'new' => ['processing'], // New -> Processing
                'processing' => ['done'], // Processing -> Done
                'done' => [], // Terminal state, cannot change
            ];

            // Allow setting same status (idempotency)
            if ($currentStatus === $newStatus) {
                return;
            }

            // Check if transition is valid
            if (!isset($transitions[$currentStatus]) || !in_array($newStatus, $transitions[$currentStatus])) {
                $validator->errors()->add(
                    'status',
                    "Tidak dapat mengubah status dari '$currentStatus' ke '$newStatus'. Alur harus berurutan (New → Processing → Done)."
                );
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
