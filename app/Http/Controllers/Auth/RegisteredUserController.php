<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     event(new Registered($user));

    //     Auth::login($user);

    //     return redirect(route('dashboard', absolute: false));
    // }

    public function store(Request $request): RedirectResponse
    {
        Log::info('RegisteredUserController@store hit', ['data' => $request->except(['password', 'password_confirmation'])]);
        try {
            // Validate input
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'license_key' => ['required', 'string'],
                'business_name' => ['required', 'string', 'max:255'],
                'logo' => ['nullable', 'string'],
                'module' => ['nullable', 'string'],
            ]);

            $licenseKey = strtoupper(trim($request->license_key));
            $terminalId = config('license.terminal_id') ?: (string) \Illuminate\Support\Str::uuid();
            $cloudUrl = rtrim(config('license.cloud_url'), '/');

            // ─── Top-Notch Security: Server-side License Verification ───────────────
            $licenseData = null;
            if (config('license.key') === $licenseKey && config('license.terminal_id')) {
                $licenseData = app(\App\Services\LicenseService::class)->getPayload();
            }

            if (!$licenseData || ($licenseData['valid'] ?? false) !== true) {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->post("{$cloudUrl}/api/v1/license/validate", [
                        'license_key'         => $licenseKey,
                        'terminal_identifier' => $terminalId,
                        'terminal_name'       => 'Initial Setup',
                    ]);

                if (!$response->successful() || ($response->json()['valid'] ?? false) !== true) {
                    $error = $response->json()['message'] ?? 'License validation failed.';
                    return redirect()->back()->withErrors(['license_key' => $error])->withInput();
                }
                $licenseData = $response->json();
            }

            Log::info('Register: License data ready', [
                'from_cache' => $licenseData && config('license.key') === $licenseKey,
                'is_valid' => $licenseData['valid'] ?? false
            ]);

            // Merge cloud data for duplicate check
            $email = $licenseData['tenant']['owner_email'] ?? $request->email;
            Log::info('Register: Checking for existing user', ['email' => $email]);
            if (User::where('email', $email)->exists()) {
                Log::info('Register: User already exists, redirecting back');
                return redirect()->back()->withErrors(['email' => 'An account with this email (' . $email . ') already exists. Please log in instead.'])->withInput();
            }

            Log::info('Register: Proceeding with account creation');

            Log::info('Register: About to create User', ['email' => $email]);
            // Create user
            $user = User::create([
                'name' => $licenseData['tenant']['owner_name'] ?? $request->name,
                'email' => $email,
                'password' => Hash::make($request->password),
                'is_admin' => true,
            ]);
            Log::info('Register: User created successfully', ['user_id' => $user->id]);

            // Handle company logo
            $logoUrl = $licenseData['tenant']['company_logo_url'] ?? $request->logo;
            Log::info('Register: Handling logo', ['url' => $logoUrl]);
            $fileName = 'logos/default_logo.png';
            if ($logoUrl) {
                try {
                    $imageContent = Http::timeout(5)
                        ->withoutVerifying()
                        ->get($logoUrl);
                    if ($imageContent->successful()) {
                        $fileName = 'logos/' . uniqid() . '.jpg';
                        Storage::put($fileName, $imageContent->body());
                    }
                } catch (\Exception $logoEx) {
                    Log::warning("Register: Logo fetch failed: " . $logoEx->getMessage());
                }
            }

            Log::info('Register: About to create CompanyProfile', ['email' => $email]);
            // Create and save company profile
            CompanyProfile::create([
                'name' => $licenseData['tenant']['name'] ?? $request->business_name,
                'email' => $email,
                'logo' => $fileName,
                'modules' => json_encode($licenseData['modules'] ?? explode(',', $request->module ?: '')),
            ]);
            Log::info('Register: CompanyProfile created successfully');

            event(new Registered($user));
            Log::info('Register: Dispatching Registered event');

            app(\App\Services\LicenseService::class)->refresh();

            Auth::login($user);

            // ─── Consolidate Configuration: Save to .env (DO THIS LAST TO AVOID DEV RESTART ISSUES) ───
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                $replacements = [
                    'LICENSE_KEY=' => "LICENSE_KEY={$licenseKey}",
                    'TERMINAL_IDENTIFIER=' => "TERMINAL_IDENTIFIER={$terminalId}",
                ];
                foreach ($replacements as $key => $value) {
                    if (str_contains($envContent, $key)) {
                        $envContent = preg_replace("/^{$key}.*/m", $value, $envContent);
                    } else {
                        $envContent .= "\n{$value}";
                    }
                }
                file_put_contents($envPath, $envContent);

                config([
                    'license.key' => $licenseKey,
                    'license.terminal_id' => $terminalId,
                ]);

                \Illuminate\Support\Facades\Artisan::call('config:clear');
            }

            Log::info('Register: All done, redirecting to dashboard');
            return redirect()->route('dashboard')->with('success', 'Account created and license activated!');
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $request->except(['password', 'password_confirmation']),
            ]);

            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()])->withInput();
        }
    }
}
