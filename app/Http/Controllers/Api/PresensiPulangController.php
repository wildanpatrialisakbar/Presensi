<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiPulangRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PresensiPulangController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param PresensiPulangRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(PresensiPulangRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $request->ensureTheUserHasTakePresensiMasuk($request->user()->id);

        $request->ensureThatThisIsTheTimeToPresensiPulang($validated['waktu']);

        $request->ensureThatTheDistanceIsCloseToTheLocation($validated['latitude_pulang'], $validated['longitude_pulang']);

        $distance = $request->getDistance($validated['latitude_pulang'], $validated['longitude_pulang']);

        $attendance = $request->getAttendance($request->user()->id);

        $attendance->latitude_pulang = $validated['latitude_pulang'];
        $attendance->longitude_pulang = $validated['longitude_pulang'];
        $attendance->distance_pulang = round($distance, 2);
        $attendance->jam_pulang = Carbon::create($validated['waktu']);

        $attendance->status = $attendance->status === "Terlambat" ? "Terlambat" : 'Hadir';

        $attendance->save();

        return response()->json([
            'message' => 'Presensi berhasil, terima kasih atas kerja kerasnya ' . $request->user()->name . '.',
            'presensi' => $attendance
        ]);

    }
}
