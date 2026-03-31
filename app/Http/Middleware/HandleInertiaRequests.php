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
                    $types = app(\App\Services\LicenseService::class)->get('modules', []);
                    if (is_string($types)) {
                        $types = explode(',', $types);
                    }
                    if (in_array('supermarket', $types) && !in_array('supermart', $types)) {
                        $types[] = 'supermart';
                    }
                    return $types;
                } catch (\Throwable $e) {
                    return [];
                }
            },
            'flash' => [
                'message'         => fn() => $request->session()->get('message'),
                'success'         => fn() => $request->session()->get('success'),
                'error'           => fn() => $request->session()->get('error'),
                'status'          => fn() => $request->session()->get('status'),
                'license_error'   => fn() => $request->session()->get('license_error'),
                'license_message' => fn() => $request->session()->get('license_message'),
                'license_reason'  => fn() => $request->session()->get('license_reason'),
            ],
        ];
    }
}
