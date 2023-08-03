<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiLemburRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PresensiLemburController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param PresensiLemburRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(PresensiLemburRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $request->ensureTheUserHasTakePresensiMasuk($request->user()->id);

        $request->ensureThatThisIsTheTimeToPresensiLembur($validated['waktu']);

        $request->ensureThatTheDistanceIsCloseToTheLocation($validated['latitude_pulang'], $validated['longitude_pulang']);

        $distance = $request->getDistance($validated['latitude_pulang'], $validated['longitude_pulang']);

        $waktu = Carbon::create($validated['waktu']);

        $attendance = $request->getAttendance($request->user()->id);
        $attendance->latitude_pulang = $validated['latitude_pulang'];
        $attendance->longitude_pulang = $validated['longitude_pulang'];
        $attendance->distance_pulang = round($distance, 2);
        $attendance->jam_pulang = $waktu;
        $attendance->status = $attendance->status === "Terlambat" ? "Terlambat" : 'Hadir';
        $attendance->lembur = Carbon::create($request->getConfiguration()->presensi_pulang_sampai)->diffInHours($waktu);

        $attendance->save();

        return response()->json([
            'message' => 'Presensi berhasil, terima kasih atas kerja kerasnya ' . $request->user()->name . '.',
            'presensi' => $attendance
        ]);
    }
}
