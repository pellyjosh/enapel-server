<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\PaymentRecord;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function handleApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|min:10|max:11',
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'filename' => 'required',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'establishment' => 'required',
            'module' => 'required|array',
            'module.*' => 'string|max:255',
            'description' => 'nullable',
            'amount' => 'required|numeric',
            'duration' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Log::error('Validation Errors:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $logoPath = $request->file('logo')->store('logos', 'public');
        $logoFile = $logoPath;
        $licenseKey = strtoupper(Str::random(10)) . '-' . strtoupper(Str::random(5));

        $application = Application::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'name' => $request->name,
            'gender' => $request->gender,
            'logo' => $logoFile,
            'establishment' => $request->establishment,
            'module' => json_encode($request->module),
            'description' => $request->description ?? '',
            'amount' => $request->amount,
            'duration' => $request->duration,
            'license_key' => $licenseKey,
            'status' => 'pending',
        ]);

        $netAmount = $request->amount * 100; // Amount in kobo (already multiplied by duration on frontend)
        $formData = [
            "email" => $request->email,
            "amount" => $netAmount,
            "callback_url" => route('application.callback'),
            "metadata" => [
                "license_key" => $licenseKey,
            ],
        ];

        $response = json_decode($this->initPayment($formData));
        if ($response && $response->status) {
            return redirect($response->data->authorization_url);
        }

        Log::error('Payment Initialization Failed:', (array) $response);
        return back()->withError($response->message ?? 'Payment initialization failed.');
    }

    public function paymentCallback(Request $request)
    {
        $response = json_decode($this->verifyPayment($request->reference));

        if ($response && $response->status) {
            $data = $response->data;

            if (PaymentRecord::where('transaction_id', $data->id)->exists()) {
                Log::info('Duplicate transaction detected:', ['transaction_id' => $data->id]);
                return redirect()->route('callback')->with('success', 'Transaction already processed.');
            }

            DB::transaction(function () use ($data) {
                PaymentRecord::create([
                    'transaction_id' => $data->id,
                    'status' => $data->status,
                    'license_key' => $data->metadata->license_key,
                    'amount' => $data->amount / 100, // Convert to main currency
                ]);

                Log::info('Payment record created successfully.');

                $application = Application::where('license_key', $data->metadata->license_key)->first();
                if ($application) {
                    $application->status = 'active';
                    $application->save();
                    Log::info('Application updated successfully:', $application->toArray());
                } else {
                    Log::error('Application not found for license key:', ['license_key' => $data->metadata->license_key]);
                }
            });

            return redirect()->route('callback')->with([
                'success' => 'Payment processed successfully!',
                'transaction_id' => $data->id,
                'amount' => $data->amount / 100,
                'license_key' => $data->metadata->license_key,
            ]);
        }

        Log::error('Payment Verification Failed:', (array) $response);
        return redirect()->route('callback')->with('error', 'Payment verification failed.');
    }

    private function initPayment($formData)
    {
        $url = "https://api.paystack.co/transaction/initialize";
        return $this->sendCurlRequest($url, $formData);
    }

    private function verifyPayment($reference)
    {
        $url = "https://api.paystack.co/transaction/verify/{$reference}";
        return $this->sendCurlRequest($url);
    }

    private function sendCurlRequest($url, $formData = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache"
        ]);

        if ($formData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function handleDemoApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|min:10|max:11',
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'filename' => 'required',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'establishment' => 'required',
            'module' => 'required|array',
            'module.*' => 'string|max:255',
            'description' => 'nullable',
            'amount' => 'required|numeric',
            'duration' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $logoPath = $request->file('logo')->store('logos', 'public');
        $licenseKey = strtoupper(Str::random(10)) . '-' . strtoupper(Str::random(5)) . '-DEMO';

        $application = Application::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'name' => $request->name,
            'gender' => $request->gender,
            'logo' => $logoPath,
            'establishment' => $request->establishment,
            'module' => json_encode($request->module),
            'description' => $request->description ?? '',
            'amount' => $request->amount,
            'duration' => $request->duration,
            'license_key' => $licenseKey,
            'status' => 'active', // Instantly active for demo
        ]);

        return redirect()->route('callback')->with([
            'success' => 'Demo Registration Successful! (Payment Skipped)',
            'transaction_id' => 'DEMO-' . Str::random(8),
            'amount' => $request->amount,
            'license_key' => $licenseKey,
        ]);
    }

    public function showCallback()
    {
        return view('form.callback');
    }
}
