<?php

namespace App\Http\Controllers;

use App\Models\BackupRun;
use App\Models\ReplicationCheckpoint;
use App\Models\ReplicationNode;
use App\Services\DisasterRecovery\DisasterRecoveryService;
use App\Services\DisasterRecovery\NetworkAddressService;
use App\Services\DisasterRecovery\NodeStateService;
use App\Services\DisasterRecovery\ReplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Native\Laravel\Dialog;

class DisasterRecoveryController extends Controller
{
    public function __construct(
        protected DisasterRecoveryService $recovery,
        protected ReplicationService $replication,
        protected NodeStateService $nodeState,
        protected NetworkAddressService $networkAddressService
    ) {
    }

    public function index(): Response
    {
        $settings = $this->recovery->settings();

        return Inertia::render('Global/Settings/DisasterRecovery', [
            'settings' => [
                'id' => $settings->id,
                'node_name' => $settings->node_name,
                'node_role' => $settings->node_role,
                'service_hostname' => $settings->service_hostname,
                'nas_path' => $settings->nas_path,
                'cloud_mirror_enabled' => $settings->cloud_mirror_enabled,
                'cloud_mirror_url' => $settings->cloud_mirror_url,
                'snapshot_interval_minutes' => $settings->snapshot_interval_minutes,
                'full_backup_hour' => $settings->full_backup_hour,
                'monthly_backup_hour' => $settings->monthly_backup_hour,
                'retention_snapshot_days' => $settings->retention_snapshot_days,
                'retention_daily_backups' => $settings->retention_daily_backups,
                'retention_monthly_backups' => $settings->retention_monthly_backups,
                'standby_enabled' => $settings->standby_enabled,
                'standby_primary_url' => $settings->standby_primary_url,
                'passphrase_hint' => $settings->passphrase_hint,
                'last_successful_snapshot_at' => optional($settings->last_successful_snapshot_at)?->toIso8601String(),
                'last_successful_full_backup_at' => optional($settings->last_successful_full_backup_at)?->toIso8601String(),
                'has_backup_password' => filled($settings->encrypted_passphrase),
            ],
            'nodeState' => $this->nodeState->get(),
            'healthWarnings' => $this->recovery->healthWarnings(),
            'latestBackups' => BackupRun::query()->latest('started_at')->limit(20)->get(),
            'replicationNodes' => ReplicationNode::query()->latest('updated_at')->get(),
            'replicationCheckpoints' => ReplicationCheckpoint::query()->latest('applied_at')->limit(10)->get(),
            'availableNasBundles' => array_slice($this->recovery->availableNasBundles(), 0, 10),
            'networkAddresses' => $this->networkAddressService->privateIpv4Addresses(),
            'serverPort' => (int) (config('nativephp.server_port') ?: env('NATIVEPHP_SERVER_PORT', 8000)),
            'isNativeDesktop' => (bool) config('nativephp-internal.running', false),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $this->recovery->settings();

        $validator = Validator::make($request->all(), [
            'node_name' => ['nullable', 'string', 'max:255'],
            'node_role' => ['required', 'in:primary,standby'],
            'service_hostname' => ['nullable', 'string', 'max:255'],
            'nas_path' => ['required', 'string', 'max:2048'],
            'cloud_mirror_enabled' => ['required', 'boolean'],
            'cloud_mirror_url' => ['nullable', 'url', 'max:2048', 'required_if:cloud_mirror_enabled,1'],
            'cloud_mirror_token' => ['nullable', 'string', 'max:2048'],
            'snapshot_interval_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'full_backup_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'monthly_backup_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'retention_snapshot_days' => ['required', 'integer', 'min:1', 'max:365'],
            'retention_daily_backups' => ['required', 'integer', 'min:1', 'max:365'],
            'retention_monthly_backups' => ['required', 'integer', 'min:1', 'max:120'],
            'standby_enabled' => ['required', 'boolean'],
            'standby_primary_url' => ['nullable', 'url', 'max:2048', 'required_if:node_role,standby'],
            'passphrase' => ['nullable', 'string', 'min:12', 'confirmed'],
            'passphrase_hint' => ['nullable', 'string', 'max:255'],
        ], [
            'nas_path.required' => 'Choose the shared backup folder.',
            'cloud_mirror_url.required_if' => 'Enter the online backup address or switch off the online copy option.',
            'standby_primary_url.required_if' => 'Enter the main server address.',
            'passphrase.min' => 'Backup password must be at least 12 characters.',
            'passphrase.confirmed' => 'Backup password confirmation does not match.',
        ]);

        $validator->after(function ($validator) use ($request, $settings) {
            if (blank($settings->encrypted_passphrase) && blank($request->input('passphrase'))) {
                $validator->errors()->add('passphrase', 'Enter a backup password.');
            }
        });

        $validated = $validator->validate();

        $this->recovery->saveSettings($validated);

        return back()->with('success', 'Disaster recovery settings updated.');
    }

    public function snapshot(Request $request): RedirectResponse
    {
        $this->recovery->runSnapshot(
            type: $request->string('type', 'snapshot')->toString(),
            full: $request->boolean('full'),
            passphrase: $request->input('passphrase')
        );

        return back()->with('success', 'Disaster recovery bundle created.');
    }

    public function pickFolder(Request $request): JsonResponse
    {
        if (! config('nativephp-internal.running', false)) {
            return response()->json([
                'message' => 'Folder picker is only available inside the installed desktop app.',
            ], 409);
        }

        $path = Dialog::new()
            ->title('Choose backup folder')
            ->button('Use Folder')
            ->defaultPath((string) $request->string('current', '')->toString())
            ->folders()
            ->open();

        return response()->json([
            'path' => $path,
        ]);
    }

    public function generatePairingToken(): RedirectResponse
    {
        $token = $this->replication->generatePairingToken();

        return back()->with([
            'success' => 'Pairing token generated.',
            'pairing_token' => $token,
        ]);
    }

    public function pair(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary_url' => ['required', 'url'],
            'pairing_token' => ['required', 'string'],
        ]);

        $this->replication->pairWithPrimary($validated['primary_url'], $validated['pairing_token']);

        return back()->with('success', 'Standby node paired with primary.');
    }

    public function promote(Request $request): RedirectResponse
    {
        $request->validate([
            'passphrase' => ['nullable', 'string'],
        ]);

        $this->recovery->promote($request->input('passphrase'));

        return back()->with('success', 'Standby node promoted to primary.');
    }
}
