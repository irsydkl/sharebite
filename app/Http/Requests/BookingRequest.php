<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'quantity_claimed' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity_claimed.required' => 'Jumlah klaim harus diisi.',
            'quantity_claimed.integer'  => 'Jumlah klaim harus berupa angka bulat.',
            'quantity_claimed.min'      => 'Jumlah klaim minimal 1.',
            'quantity_claimed.max'      => 'Jumlah klaim maksimal 50 per transaksi.',
        ];
    }
}
