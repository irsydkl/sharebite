<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterDonaturRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\.\'-]+$/u'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'regex:/^08[0-9]{8,12}$/'],
            'address' => ['required', 'string', 'max:500'],
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
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'phone.regex' => 'Nomor telepon harus dimulai dengan 08 dan 10–14 digit.',
            'email.unique' => 'Email sudah terdaftar.',
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
            'name' => 'nama lengkap',
            'email' => 'email',
            'password' => 'kata sandi',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'store_name' => 'nama toko',
            'store_description' => 'deskripsi toko',
            'store_address' => 'alamat toko',
            'store_latitude' => 'latitude toko',
            'store_longitude' => 'longitude toko',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->email)),
            'phone' => preg_replace('/\s+/', '', (string) $this->phone),
        ]);
    }
}
