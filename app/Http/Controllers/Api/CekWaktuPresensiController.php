<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CekWaktuPresensiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'waktu' => 'required|date_format:H:i:s'
        ]);

//        $waktu = CarbonImmutable::create($request->input('waktu'));

        $waktu = now();

        $configuration = Configuration::first();

        $toleransi_ketemlambatan = CarbonImmutable::create($configuration->toleransi_keterlambatan);

        $maksimal_terlambat = CarbonImmutable::create($configuration->maksimal_terlambat);

        $presensi_pulang_dari = CarbonImmutable::create($configuration->presensi_pulang_dari);

        $presensi_pulang_sampai = CarbonImmutable::create($configuration->presensi_pulang_sampai);

        // jika hari libur
        if ($configuration->hari_libur === $waktu->getTranslatedDayName()) {
            return response()->json([
                'keterangan' => 'Hari libur. Selamat beristirahat bersama orang tersayang.'
            ], 422);
        }

        // Presensi masuk dengan toleransi keterlambatan
        if ($waktu->isBetween(CarbonImmutable::create($configuration->presensi_masuk_dari), $toleransi_ketemlambatan)) {
            return response()->json([
                'keterangan' => 'Presensi masuk.',
                'button_text' => 'Presensi Masuk',
                'endpoint' => 'presensi/masuk',
                'jam' => $waktu->toTimeString(),
                'tanggal' => $waktu->toFormattedDayDateString()
            ]);
        }

        // Presensi masuk terlambat
        if ($waktu->isBetween($toleransi_ketemlambatan, $maksimal_terlambat)) {
            return response()->json([
                'keterangan' => 'Presensi terlambat.',
                'button_text' => 'Presensi Terlambat',
                'endpoint' => 'presensi/terlambat',
                'jam' => $waktu->toTimeString(),
                'tanggal' => $waktu->toFormattedDayDateString()
            ]);
        }

        // Bukan waktu presensi
        if ($waktu->isBetween($maksimal_terlambat, $presensi_pulang_dari)) {
            return response()->json([
                'keterangan' => 'Bukan waktu presensi.'
            ], 422);
        }

        // Presensi pulang
        if ($waktu->isBetween($presensi_pulang_dari, $presensi_pulang_sampai)) {
            return response()->json([
                'keterangan' => 'Presensi Pulang.',
                'button_text' => 'Presensi Pulang',
                'endpoint' => 'presensi/pulang',
                'jam' => $waktu->toTimeString(),
                'tanggal' => $waktu->toFormattedDayDateString()
            ]);
        }

        // presensi lembur
        if ($waktu->greaterThan($presensi_pulang_sampai)) {
            return response()->json([
                'keterangan' => 'Sekarang merupakan waktu presensi pulang bagi yang lembur.',
                'button_text' => 'Presensi Pulang',
                'endpoint' => 'presensi/lembur',
                'jam' => $waktu->toTimeString(),
                'tanggal' => $waktu->toFormattedDayDateString()
            ]);
        }

        return response()->json([
            'keterangan' => 'Bukan waktu presensi.'
        ], 422);
    }
}
