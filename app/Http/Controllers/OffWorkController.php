<?php

namespace App\Http\Controllers;

use App\Http\Requests\OffworkStoreRequest;
use App\Models\Attendance;
use App\Models\Configuration;
use App\Models\OffWork;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Inertia\Response;

class OffWorkController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->user()->role_id == Role::isOwner) {
            $leaves = OffWork::with('user:id,name')->latest()->get();
        } else {
            $leaves = OffWork::with('user:id,name')->where('user_id', $request->user()->id)->get();
        }

        return Inertia::render('Offwork/Index', [
            'offworks' => $leaves
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Offwork/Create');
    }


    /**
     * @param OffworkStoreRequest $request
     * @return Redirector|Application|RedirectResponse
     */
    public function store(OffworkStoreRequest $request): Redirector|Application|RedirectResponse
    {
        $validated = $request->validated();

        $validated['document'] = $request->file('document')->store('public/offworks');

        $request->user()->offworks()->create($validated);

        return redirect(route('offworks.index'))->with('message', 'Permohonan cuti berhasil dibuat.');
    }

    /**
     * @param OffWork $offwork
     * @return Response
     * @throws AuthorizationException
     */
    public function show(OffWork $offwork): Response
    {
        $this->authorize('manage', $offwork);

        $offwork['document'] = Storage::url($offwork->document);

        $offwork['user'] = $offwork->user;

        return Inertia::render('Offwork/Show', [
            'offwork' => $offwork,
            'options' => OffWork::optionStatus
        ]);
    }

    /**
     * @param OffWork $offwork
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(OffWork $offwork): Response
    {
        $this->authorize('manage', $offwork);

        $offwork['document_url'] = Storage::url($offwork->document);

        return Inertia::render('Offwork/Edit', [
            'offwork' => $offwork
        ]);
    }

    /**
     * @param Request $request
     * @param OffWork $offwork
     * @return Application|RedirectResponse|Redirector
     * @throws AuthorizationException
     */
    public function update(Request $request, OffWork $offwork): Redirector|RedirectResponse|Application
    {
        $this->authorize('manage', $offwork);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'finish_date' => 'required|date',
            'reason' => 'required|string|max:1000',
        ]);

        $offwork->update($validated);

        return redirect(route('offworks.index'))->with('message', 'Permohonan cuti berhasil diperbarui.');
    }

    /**
     * @param OffWork $offwork
     * @return Application|RedirectResponse|Redirector
     * @throws AuthorizationException
     */
    public function destroy(OffWork $offwork): Redirector|RedirectResponse|Application
    {
        $this->authorize('manage', $offwork);

        Storage::delete($offwork->document);

        $offwork->delete();

        return redirect(route('offworks.index'))->with('message', 'Permohonan cuti berhasil dihapus.');
    }

    /**
     * Update status cuti
     * @param Request $request
     * @param OffWork $offwork
     * @return Application|RedirectResponse|Redirector
     * @throws AuthorizationException
     */
    public function updateStatus(Request $request, OffWork $offwork): Redirector|RedirectResponse|Application
    {
        $this->authorize('updateStatus', $offwork);

        $request->validate([
            'status' => ['required', Rule::in(OffWork::optionStatus)]
        ]);

        $offwork->update($request->only('status'));

        if ($offwork->status === 'disetujui'){
            $tanggal_mulai = Carbon::create($offwork->start_date);
            $tanggal_selesai = Carbon::create($offwork->finish_date);
            $perbedaan_hari = $tanggal_mulai->diffInDays($tanggal_selesai);

            if ($perbedaan_hari === 0){
                $attendance = new Attendance;
                $attendance->user_id = $offwork->user_id;
                $attendance->latitude_masuk = $this->getConfiguration()->location->latitude;
                $attendance->latitude_pulang = $this->getConfiguration()->location->latitude;
                $attendance->longitude_masuk = $this->getConfiguration()->location->longitude;
                $attendance->longitude_pulang = $this->getConfiguration()->location->longitude;
                $attendance->distance_masuk = 0;
                $attendance->distance_pulang = 0;
                $attendance->jam_masuk = Carbon::create($this->getConfiguration()->presensi_masuk_dari);
                $attendance->jam_pulang = Carbon::create($this->getConfiguration()->presensi_pulang_dari);
                $attendance->lembur = 0;
                $attendance->status = 'Izin';
                $attendance->created_at = $tanggal_mulai;
                $attendance->save();
            } else {
                $tanggal_mulai->subDay();

                for ($x = 0; $x <= $perbedaan_hari; $x++){
                    $attendance = new Attendance;
                    $attendance->user_id = $offwork->user_id;
                    $attendance->latitude_masuk = $this->getConfiguration()->location->latitude;
                    $attendance->latitude_pulang = $this->getConfiguration()->location->latitude;
                    $attendance->longitude_masuk = $this->getConfiguration()->location->longitude;
                    $attendance->longitude_pulang = $this->getConfiguration()->location->longitude;
                    $attendance->distance_masuk = 0;
                    $attendance->distance_pulang = 0;
                    $attendance->jam_masuk = Carbon::create($this->getConfiguration()->presensi_masuk_dari);
                    $attendance->jam_pulang = Carbon::create($this->getConfiguration()->presensi_pulang_dari);
                    $attendance->lembur = 0;
                    $attendance->status = 'Izin';
                    $attendance->created_at = $tanggal_mulai->addDay();
                    $attendance->save();
                }
            }
        }

        return redirect(route('offworks.show', $offwork->id))->with('message', 'Pengajuan cuti berhasil diperbarui');
    }

    public function getConfiguration()
    {
        return Configuration::first();
    }
}
