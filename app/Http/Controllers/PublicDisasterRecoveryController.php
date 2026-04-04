<?php

namespace App\Http\Controllers;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use App\Services\DisasterRecovery\NodeStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicDisasterRecoveryController extends Controller
{
    public function __construct(
        protected DisasterRecoveryService $recovery,
        protected NodeStateService $nodeState
    ) {
    }

    public function create(): Response
    {
        return Inertia::render('DisasterRecovery/Restore', [
            'settings' => $this->recovery->settings(),
            'nodeState' => $this->nodeState->get(),
            'availableNasBundles' => array_slice($this->recovery->availableNasBundles(), 0, 20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'passphrase' => ['required', 'string'],
            'nas_path' => ['nullable', 'string'],
            'bundle_uuid' => ['nullable', 'string'],
            'bundle_path' => ['nullable', 'string'],
            'role' => ['required', 'in:primary,standby'],
        ]);

        if (filled($validated['bundle_path'] ?? null)) {
            $this->recovery->applyBundle($validated['bundle_path'], $validated['passphrase'], [
                'restore_env' => true,
                'run_migrations' => true,
            ]);
        } else {
            $this->recovery->restoreFromNas(
                $validated['nas_path'] ?: $this->recovery->settings()->nas_path,
                $validated['passphrase'],
                $validated['bundle_uuid'] ?: null,
            );
        }

        $this->nodeState->setRole($validated['role']);
        $this->recovery->saveSettings(['node_role' => $validated['role']]);

        return redirect()->route('login')->with('success', 'Backup restore completed successfully.');
    }
}
