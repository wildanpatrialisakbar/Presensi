<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceStoreByAdminRequest;
use App\Models\Attendance;
use App\Models\Configuration;
use App\Models\User;
use App\Traits\EnsureItsNotDayOff;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    use EnsureItsNotDayOff;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('manage', $request->user());

        $attendances = match ($request->input('filter')) {
            "today" => Attendance::with('user:id,nip,name')->whereDate('created_at', Carbon::today())->latest()->get(),
            "yesterday" => Attendance::with('user:id,nip,name')->whereDate('created_at', Carbon::yesterday())->latest()->get(),
            "this_week" => Attendance::with('user:id,nip,name')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->latest()->get(),
            "this_month" => Attendance::with('user:id,nip,name')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get(),
            default => Attendance::with('user:id,nip,name')->latest()->limit(5)->get(),
        };

        if ($request->has('start') && $request->has('end')) {
            $attendances = Attendance::with('user:id,nip,name')->whereBetween('created_at', [$request->input('start'), $request->input('end')])->get();
        }

        return Inertia::render('Attendance/Index', [
            'attendances' => $attendances,
            'filtered' => $request->input('filter')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('manage', auth()->user());

        // Get all user id and name except owner
        $users = User::whereNot('id', Auth::id())->get(['id', 'nip', 'name']);

        // Get all status attendances
        $status = collect(['Hadir', 'Terlambat', 'Izin', 'Alpha', 'Libur', 'Belum ditentukan']);

        return Inertia::render('Attendance/Create', [
            'users' => $users,
            'status' => $status,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AttendanceStoreByAdminRequest $request
     * @return RedirectResponse
     * @throws ValidationException|AuthorizationException
     */
    public function store(AttendanceStoreByAdminRequest $request): RedirectResponse
    {
        $this->authorize('manage', $request->user());

        //cek apakah tanggal yang diinputkan merupakan hari libur?
        $this->dayOff($request->tanggal);

        // mendapatkan data presensi pada tanggal tersebut
        $attendance = Attendance::where('user_id', $request->user_id)->whereDate('created_at', Carbon::create($request->tanggal))->first();

        // kalau ada presensi pada tanggal itu
        if ($attendance) {
            throw ValidationException::withMessages([
                'tanggal' => 'Pegawai telah melakukan presensi pada tanggal tersebut.'
            ]);
        }

        $attendance = new Attendance;
        $attendance->user_id = $request->user_id;
        $attendance->latitude_masuk = $this->getConfiguration()->location->latitude;
        $attendance->latitude_pulang = $this->getConfiguration()->location->latitude;
        $attendance->longitude_masuk = $this->getConfiguration()->location->longitude;
        $attendance->longitude_pulang = $this->getConfiguration()->location->longitude;
        $attendance->distance_masuk = 0;
        $attendance->distance_pulang = 0;
        $attendance->jam_masuk = Carbon::create($this->getConfiguration()->presensi_masuk_dari);
        $attendance->jam_pulang = Carbon::create($this->getConfiguration()->presensi_pulang_dari);
        $attendance->lembur = $request->lembur;
        $attendance->status = $request->status;
        $attendance->created_at = Carbon::create($request->tanggal);
        $attendance->save();

        return to_route('attendances.index')->with('message', 'Presensi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param Attendance $attendance
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Attendance $attendance): Response
    {
        $this->authorize('manage', auth()->user());

        return Inertia::render('Attendance/Show', [
            'attendance' => $attendance,
            'user' => $attendance->user,
            'status' => ['Hadir', 'Izin', 'Alpha', 'Terlambat', 'Belum ditentukan']
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Attendance $attendance
     * @return void
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Attendance $attendance
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->authorize('manage', $request->user());

        $attendance->update($request->all());

        return to_route('attendances.show', $attendance)->with('message', 'Presensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Attendance $attendance
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $this->authorize('manage', auth()->user());

        $attendance->delete();

        return to_route('attendances.index')->with('message', 'Presensi berhasil dihapus.');
    }

    public function getConfiguration()
    {
        return Configuration::first();
    }
}
