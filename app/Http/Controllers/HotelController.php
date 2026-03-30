<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HotelController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'available_rooms' => \App\Models\Room::where('status', 'available')->count(),
            'occupied_rooms' => \App\Models\Room::where('status', 'occupied')->count(),
            'total_guests' => \App\Models\Guest::count(),
            'pending_orders' => \App\Models\RoomService::where('status', 'pending')->count(),
        ];

        return Inertia::render('Hotel/Dashboard', [
            'metrics' => $stats
        ]);
    }

    public function bookings()
    {
        $bookings = \App\Models\Booking::with(['guest', 'room'])->latest()->get();
        $guests = \App\Models\Guest::all();
        $rooms = \App\Models\Room::where('status', 'available')->get();
        return Inertia::render('Hotel/Bookings', [
            'bookings' => $bookings,
            'guests' => $guests,
            'rooms' => $rooms
        ]);
    }

    public function guests()
    {
        $guests = \App\Models\Guest::latest()->get();
        return Inertia::render('Hotel/Guests', ['guests' => $guests]);
    }

    public function storeGuest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:guests,email',
            'phone' => 'nullable|string|max:20',
        ]);

        \App\Models\Guest::create($validated);

        return back()->with('success', 'Guest added successfully.');
    }

    public function rooms()
    {
        $rooms = \App\Models\Room::with('category')->get();
        $categories = \App\Models\RoomCategory::all();
        return Inertia::render('Hotel/Rooms', [
            'rooms' => $rooms,
            'categories' => $categories
        ]);
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:rooms,name',
            'category_id' => 'required|exists:room_categories,id',
            'price' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        \App\Models\Room::create($validated);

        return back()->with('success', 'Room added successfully.');
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|numeric',
        ]);

        $booking = \App\Models\Booking::create(array_merge($validated, ['status' => 'confirmed']));

        // Update room status
        \App\Models\Room::where('id', $request->room_id)->update(['status' => 'occupied']);

        return back()->with('success', 'Booking created successfully.');
    }

    public function housekeeping()
    {
        $rooms = \App\Models\Room::all();
        return Inertia::render('Hotel/Housekeeping', ['rooms' => $rooms]);
    }

    public function updateHousekeeping(Request $request, $id)
    {
        $validated = $request->validate([
            'is_clean' => 'required|boolean',
            'status' => 'required|string',
        ]);

        \App\Models\Room::where('id', $id)->update($validated);

        return back()->with('success', 'Room status updated.');
    }

    public function roomService()
    {
        $currentBookings = \App\Models\Booking::with(['guest', 'room'])->where('status', 'confirmed')->get();
        $orders = \App\Models\RoomService::with('booking.guest')->latest()->get();
        return Inertia::render('Hotel/RoomService', [
            'bookings' => $currentBookings,
            'orders' => $orders
        ]);
    }

    public function storeRoomService(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'item_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
        ]);

        \App\Models\RoomService::create($validated);

        return back()->with('success', 'Room service order placed.');
    }

    public function invoices()
    {
        $bookings = \App\Models\Booking::with(['guest', 'room', 'room_service'])->where('status', 'confirmed')->get();
        return Inertia::render('Hotel/Invoices', ['bookings' => $bookings]);
    }

    public function reports()
    {
        return Inertia::render('Hotel/Reports');
    }

    public function settings()
    {
        return Inertia::render('Hotel/Settings');
    }
}
