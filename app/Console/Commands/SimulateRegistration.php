<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimulateRegistration extends Command
{
    protected $signature = 'debug:register';
    protected $description = 'Simulate the registration process';

    public function handle()
    {
        $this->info('Starting registration simulation...');

        $request = new Request([
            'license_key' => 'R6FFWZOUQU-06M0K',
            'name' => 'Kolawole Oluwapelumi',
            'email' => 'test_' . uniqid() . '@example.com',
            'business_name' => 'Enapel Supermart',
            'logo' => 'http://127.0.0.1:8000/storage/logos/h7P5iXx5BHoK9FMRMVCbOYguU5bhslmxGKEVbk6a.png',
            'module' => 'inventory,sales,finance,staff,supplier,appointments,bookings',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $controller = app(RegisteredUserController::class);

        try {
            $response = $controller->store($request);
            $this->info('Response status: ' . $response->getStatusCode());
            if ($response->isRedirection()) {
                $this->info('Redirecting to: ' . $response->getTargetUrl());
                $errors = session()->get('errors');
                if ($errors) {
                    $this->error('Session errors:');
                    print_r($errors->getMessages());
                }
            }
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $this->error('Validation errors:');
            print_r($ve->errors());
        } catch (\Exception $e) {
            $this->error('Error during simulation: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
