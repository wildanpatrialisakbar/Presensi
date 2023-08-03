<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceStoreByAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required'],
            'lembur' => ['nullable', 'numeric', 'min:0', 'max: 6']
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Nama pegawai wajib diisi.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'lembur.min' => 'Lembur tidak bisa kurang dari 0 jam',
            'lembur.max' => 'Lembur tidak bisa lebih dari 6 jam',
            'tanggal.before_or_equal' => 'Presensi tidak dapat dilakukan melewati hari ini.'
        ];
    }
}
