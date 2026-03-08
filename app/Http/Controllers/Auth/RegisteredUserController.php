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
    public function create(): View
    {
        return view('auth.register');
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
        try {
            // Validate input
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                // Company profile validation
                'business_name' => ['required', 'string', 'max:255'],
                'logo' => ['required', 'string'],
                'module' => ['required', 'string'],
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => true,
            ]);

            // Handle company logo and store it
            $logoUrl = $request->logo;
            $imageContent = Http::get($logoUrl);

            if ($imageContent->successful()) {
                // Generate a unique filename for the logo
                $fileName = 'logos/' . uniqid() . '.jpg';

                // Store the image
                Storage::put($fileName, $imageContent->body());

                // Create and save company profile
                $companyProfile = new CompanyProfile([
                    'name' => $request->business_name,
                    'email' => $request->email,
                    'logo' => $fileName,
                    'modules' => $request->modules,
                ]);
                $companyProfile->save();
            } else {
                // Log error if the image fetch failed
                Log::error("Failed to retrieve company logo from URL: {$logoUrl}");
                return redirect()->back()->withErrors(['company_logo' => 'Failed to retrieve company logo from the provided URL.']);
            }

            // Fire the Registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error during store process: ' . $e->getMessage(), [
                'exception' => $e,
                'user' => $request->user() ? $request->user()->id : null,
                'data' => $request->all(),
            ]);

            // Redirect with a generic error message
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }
}
