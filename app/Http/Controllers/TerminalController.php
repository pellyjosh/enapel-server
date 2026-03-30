<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;
use Inertia\Inertia;

class TerminalController extends Controller
{
    public function index()
    {
        // Fetch all devices with some dummy transaction counts since we just added device_id
        $terminals = Devices::withCount(['sales', 'receipts'])->latest()->get();

        return Inertia::render('Global/Settings/Terminals', [
            'terminals' => $terminals
        ]);
    }

    public function toggleStatus(Devices $device)
    {
        $device->update([
            'status' => $device->status === 'active' ? 'disabled' : 'active'
        ]);

        return back()->with('success', 'Terminal status updated successfully.');
    }
}
