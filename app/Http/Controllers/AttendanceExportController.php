<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter;

class AttendanceExportController extends Controller
{
    /**
     * Download rekap presensi berdasarkan dari tanggal yang dipilih.
     *
     * @param Request $request
     * @return void
     */
    public function specific(Request $request)
    {
        $request->validate([
           'start' => 'required',
           'end' => 'required|after:start'
        ]);

        $attendances = DB::table('users')
            ->join('attendances', 'users.id', '=', 'attendances.user_id')
            ->select(
                'attendances.created_at AS TANGGAL',
                'users.nip as NIP', 'users.name as NAMA PEGAWAI',
                'attendances.jam_masuk as JAM MASUK',
                'attendances.jam_pulang as JAM PULANG',
                'attendances.lembur as LEMBUR',
                'attendances.status as STATUS KEHADIRAN')
            ->whereBetween('attendances.created_at', [$request->input('start'), $request->input('end')])
            ->get();

        $collections = collect($attendances)->map(fn($x) => (array)$x)->toArray();

        $fileName = 'REKAP_PRESENSI_DARI_' . $request->input('start') . '_SAMPAI_' . $request->input('end') . '.xlsx';

        SimpleExcelWriter::streamDownload($fileName)
            ->addRows($collections)
            ->toBrowser();
    }
}
