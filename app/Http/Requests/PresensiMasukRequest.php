<?php

namespace App\Http\Requests;

use App\Models\Configuration;
use App\Traits\CountDistanceBetweenTwoCoordinates;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PresensiMasukRequest extends FormRequest
{
    use CountDistanceBetweenTwoCoordinates;

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
     * @throws ValidationException
     */
    public function rules(): array
    {
        $this->ensureItsNotDayOff();

        return [
            'latitude_masuk' => ['required', 'numeric', 'between:-90,90'],
            'longitude_masuk' => ['required', 'numeric', 'between:-180,180'],
            'waktu' => ['required', 'date_format:H:i:s']
        ];
    }

    /**
     * @throws ValidationException
     */
    public function ensureItsNotDayOff()
    {
        if ($this->getConfiguration()->hari_libur === now()->getTranslatedDayName()) {
            throw ValidationException::withMessages([
                'message' => 'Presensi tidak dapat dilakukan pada hari libur.'
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function ensureThatTheDistanceIsCloseToTheLocation($lat, $long)
    {
        $distance = $this->getDistance($lat, $long);

        if ($distance > $this->getConfiguration()->accepted_distance) {
            throw ValidationException::withMessages([
                'message' => 'Presensi gagal karena jarak Anda terlalu jauh dari titik presensi.'
            ]);
        }

    }

    /**
     * @throws ValidationException
     */
    public function ensureThatThisIsTheTimeToPresensiMasuk($waktu)
    {
        $waktu = Carbon::create($waktu);

        if (!$waktu->isBetween(Carbon::create($this->getConfiguration()->presensi_masuk_dari), Carbon::create($this->getConfiguration()->toleransi_keterlambatan))) {
            throw ValidationException::withMessages([
                'message' => 'Bukan waktu presensi masuk.'
            ]);
        }
    }

    public function getConfiguration()
    {
        return Configuration::first();
    }

    public function getDistance($lat, $long): float|int
    {
        return $this->count($lat, $long, $this->getConfiguration()->location->latitude, $this->getConfiguration()->location->longitude);
    }
}
