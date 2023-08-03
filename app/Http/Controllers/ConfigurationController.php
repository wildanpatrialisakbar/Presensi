<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigurationRequest;
use App\Models\Configuration;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ConfigurationController extends Controller
{
    /**
     * Display current configuration.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize('manage', auth()->user());

        return Inertia::render('Master/Configuration/Index', [
            'configuration' => Configuration::with('location')->first(),
            'days' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'locations' => Location::all()
        ]);
    }

    /**
     * Update the configuration in storage.
     *
     * @param ConfigurationRequest $request
     * @param Configuration $configuration
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(ConfigurationRequest $request, Configuration $configuration): RedirectResponse
    {
        $this->authorize('manage', User::class);

        $configuration->update($request->all());

        $this->setLocationStatus($configuration->location->id);

        return to_route('configurations.index')->with('message', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Update the location status to active
     *
     * @param $id
     * @return void
     */
    public function setLocationStatus($id): void
    {
        DB::table('locations')->update(['status' => 'inactive']);

        DB::table('locations')->where('id', $id)->update(['status' => 'active']);
    }
}
