<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->role_id === Role::isOwner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'location_id' => ['required', 'numeric'],
            'accepted_distance' => ['required', 'numeric', 'min:50', 'max:2000'],
            'presensi_masuk_dari' => ['required', 'date_format:H:i:s'],
            'presensi_masuk_sampai' => ['required', 'date_format:H:i:s', 'after:presensi_masuk_dari'],
            'toleransi_keterlambatan' => ['required', 'date_format:H:i:s', 'after:presensi_masuk_sampai'],
            'maksimal_terlambat' => ['required', 'date_format:H:i:s', 'after:toleransi_keterlambatan'],
            'presensi_pulang_dari' => ['required', 'date_format:H:i:s', 'after:maksimal_terlambat'],
            'presensi_pulang_sampai' => ['required', 'date_format:H:i:s', 'after:presensi_pulang_dari'],
            'hari_libur' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'accepted_distance.min' => 'Radius presensi minimal adalah 50 meter.',
            'accepted_distance.max' => 'Radius presensi maksimal adalah 2 km.'
        ];
    }
}
