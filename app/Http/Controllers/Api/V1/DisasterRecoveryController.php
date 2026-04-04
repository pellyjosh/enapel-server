<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BackupRun;
use App\Services\DisasterRecovery\ReplicationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DisasterRecoveryController extends Controller
{
    public function __construct(protected ReplicationService $replication)
    {
    }

    public function pairing(Request $request)
    {
        $validated = $request->validate([
            'pairing_token' => ['required', 'string'],
            'node_uuid' => ['required', 'uuid'],
            'node_name' => ['nullable', 'string', 'max:255'],
            'hostname' => ['nullable', 'string', 'max:255'],
            'base_url' => ['nullable', 'url', 'max:2048'],
        ]);

        return response()->json($this->replication->registerPairing($validated, $request));
    }

    public function status(Request $request)
    {
        $node = $this->replication->validateSignedRequest($request);
        $after = $request->date('after');

        $query = BackupRun::query()
            ->whereIn('status', [BackupRun::STATUS_COMPLETED, BackupRun::STATUS_MIRRORED])
            ->whereNotNull('bundle_path');

        if ($after) {
            $query->where('completed_at', '>', $after);
        }

        $bundles = $query
            ->orderBy('completed_at')
            ->limit(100)
            ->get()
            ->map(fn (BackupRun $run) => [
                'bundle_uuid' => $run->bundle_uuid,
                'type' => $run->type,
                'status' => $run->status,
                'completed_at' => optional($run->completed_at)->toIso8601String(),
                'checksum' => $run->checksum,
                'size_bytes' => $run->size_bytes,
                'full' => (bool) ($run->meta['full'] ?? false),
            ]);

        $node->update([
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'node_uuid' => $node->node_uuid,
            'bundles' => $bundles,
        ]);
    }

    public function download(Request $request, string $bundleUuid): BinaryFileResponse
    {
        $this->replication->validateSignedRequest($request);
        $run = BackupRun::query()->where('bundle_uuid', $bundleUuid)->firstOrFail();

        abort_if(blank($run->bundle_path) || ! file_exists($run->bundle_path), 404, 'Bundle file is missing.');

        return response()->download($run->bundle_path, basename($run->bundle_path), [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    public function heartbeat(Request $request)
    {
        return response()->json($this->replication->recordHeartbeat($request));
    }
}
