<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiMasukRequest;
use App\Models\Attendance;
use App\Traits\CountDistanceBetweenTwoCoordinates;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PresensiMasukController extends Controller
{
    use CountDistanceBetweenTwoCoordinates;

    /**
     * Handle the incoming request.
     *
     * @param PresensiMasukRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(PresensiMasukRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $request->ensureThatThisIsTheTimeToPresensiMasuk($validated['waktu']);

        $request->ensureThatTheDistanceIsCloseToTheLocation($validated['latitude_masuk'], $validated['longitude_masuk']);

        $distance = $request->getDistance($validated['latitude_masuk'], $validated['longitude_masuk']);

        $attendance = Attendance::firstOrCreate(
            ['status' => 'Belum ditentukan', 'user_id' => $request->user()->id, 'created_at' => Carbon::today()],
            [
                'latitude_masuk' => $validated['latitude_masuk'],
                'longitude_masuk' => $validated['longitude_masuk'],
                'distance_masuk' => round($distance, 2),
                'jam_masuk' => Carbon::create($validated['waktu']),
            ]
        );

        return response()->json([
            'message' => 'Presensi Berhasil. Selamat datang ' . $request->user()->name . '.',
            'presensi' => $attendance
        ]);
    }
}
