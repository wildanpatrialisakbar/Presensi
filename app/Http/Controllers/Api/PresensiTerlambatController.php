<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiTerlambatRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PresensiTerlambatController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param PresensiTerlambatRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(PresensiTerlambatRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $request->ensureThatThisIsTheTimeToPresensiTerlambat($validated['waktu']);

        $request->ensureThatTheUserHasTakePresensiMasuk($request->user()->id);

        $request->ensureThatTheDistanceIsCloseToTheLocation($validated['latitude_masuk'], $validated['longitude_masuk']);

        $distance = $request->getDistance($validated['latitude_masuk'], $validated['longitude_masuk']);

        $attendance = new Attendance;

        $attendance->status = 'Terlambat';
        $attendance->user_id = $request->user()->id;
        $attendance->latitude_masuk = $validated['latitude_masuk'];
        $attendance->longitude_masuk = $validated['longitude_masuk'];
        $attendance->distance_masuk = round($distance, 2);
        $attendance->jam_masuk = Carbon::create($validated['waktu']);

        $attendance->save();

        return response()->json([
            'message' => 'Presensi Berhasil. Selamat datang ' . $request->user()->name . '.',
            'presensi' => $attendance
        ]);
    }
}
