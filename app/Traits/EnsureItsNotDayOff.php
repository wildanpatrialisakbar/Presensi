<?php

namespace App\Traits;

use App\Models\Configuration;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

trait EnsureItsNotDayOff
{
    /**
     * @throws ValidationException
     */
    public function dayOff($tanggal)
    {
        $configuration = Configuration::first();

        if ($configuration->hari_libur === Carbon::create($tanggal)->getTranslatedDayName()){
            throw ValidationException::withMessages([
                'tanggal' => 'Presensi tidak dapat dilakukan pada hari libur.'
            ]);
        }
    }
}
