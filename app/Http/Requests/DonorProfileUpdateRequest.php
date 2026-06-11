<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonorProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDonatur() === true
            && $this->user()->donorProfile !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_address' => ['required', 'string', 'max:500'],
            'store_latitude' => ['required', 'numeric', 'between:-90,90'],
            'store_longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'store_latitude.required' => 'Silakan tentukan lokasi toko di peta.',
            'store_longitude.required' => 'Silakan tentukan lokasi toko di peta.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'store_name' => 'nama toko',
            'store_description' => 'deskripsi toko',
            'store_address' => 'alamat toko',
            'store_latitude' => 'latitude toko',
            'store_longitude' => 'longitude toko',
        ];
    }
}
