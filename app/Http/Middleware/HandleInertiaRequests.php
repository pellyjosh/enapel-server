<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'branding' => function () {
                try {
                    $profile = \App\Models\CompanyProfile::first();
                    if ($profile) {
                        return [
                            'name' => $profile->name,
                            'logo' => $profile->logo ? \Illuminate\Support\Facades\Storage::url($profile->logo) : null,
                            'email' => $profile->email,
                        ];
                    }
                    return [
                        'name' => config('app.name'),
                        'logo' => null,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'name' => config('app.name'),
                        'logo' => null,
                    ];
                }
            },
            'enabledModules' => function () {
                try {
                    // 1. Try local database first (synced during activation)
                    $profile = \App\Models\CompanyProfile::first();
                    $types = $profile ? $profile->modules : null;
                    
                    // 2. Fallback to Cloud/Cache service
                    if (empty($types)) {
                        $types = app(\App\Services\LicenseService::class)->get('modules', []);
                    }

                    // 3. Normalize & Alias
                    if (is_string($types)) {
                        $types = array_filter(explode(',', $types));
                    }
                    
                    $types = is_array($types) ? $types : [];

                    // Alias 'supermarket'/ 'sales' to 'supermart' if needed for the UI
                    if ((in_array('supermarket', $types) || in_array('sales', $types)) && !in_array('supermart', $types)) {
                        $types[] = 'supermart';
                    }

                    // Alias 'bookings' to 'hotel'
                    if (in_array('bookings', $types) && !in_array('hotel', $types)) {
                        $types[] = 'hotel';
                    }

                    // For now, if they have sales, let's enable pharmacy too if that's what they expect
                    // (Or better, check for 'prescriptions' if they have it)
                    if (in_array('sales', $types) && !in_array('pharmacy', $types)) {
                        $types[] = 'pharmacy';
                    }
                    
                    \Illuminate\Support\Facades\Log::info('HandleInertia: modules fetched', [
                        'from_db' => $profile ? true : false,
                        'final_types' => array_values(array_unique($types))
                    ]);
                    
                    return array_values(array_unique($types));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('HandleInertia: modules error', ['error' => $e->getMessage()]);
                    return [];
                }
            },
            'flash' => [
                'message'         => fn() => $request->session()->get('message'),
                'success'         => fn() => $request->session()->get('success'),
                'error'           => fn() => $request->session()->get('error'),
                'status'          => fn() => $request->session()->get('status'),
                'pairing_token'   => fn() => $request->session()->get('pairing_token'),
                'license_error'   => fn() => $request->session()->get('license_error'),
                'license_message' => fn() => $request->session()->get('license_message'),
                'license_reason'  => fn() => $request->session()->get('license_reason'),
            ],
            'native_port' => env('NATIVEPHP_SERVER_PORT', 8000),
        ];
    }
}
