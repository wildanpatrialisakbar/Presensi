<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class OffworkStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->role_id === Role::isPegawai;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|after:today',
            'finish_date' => 'required|date|after_or_equal:start_date| before: + 1 week',
            'reason' => 'required|string|max:1000',
            'document' => 'required|image'
        ];
    }

    /**
     * customize  error message
     */
    public function messages(): array
    {
        return [
            'start_date.after' => 'Tanggal mulai cuti harus tanggal setelah hari ini.',
            'finish_date.after_or_equal' => 'Tanggal selesai cuti harus tanggal yang sama dengan tanggal mulai cuti atau tanggal setelah tanggal mulai cuti',
            'finish_date.before' => 'Tanggal selesai cuti maksimal adalah seminggu dari tanggal mulai cuti.',
        ];
    }
}
