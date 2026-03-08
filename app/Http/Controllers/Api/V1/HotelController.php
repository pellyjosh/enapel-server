<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\RoomCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    /**
     * Get all rooms with availability status.
     */
    public function getAllRooms(Request $request)
    {
        try {
            $query = $request->query('search', ''); // Get search query if provided

            $roomsQuery = Room::with('category');

            // Apply search filter if query is not empty
            if (!empty($query)) {
                $roomsQuery->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%")
                        ->orWhereHas('category', function ($categoryQuery) use ($query) {
                            $categoryQuery->where('name', 'LIKE', "%$query%");
                        });
                });
            }

            $rooms = $roomsQuery->get()->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'category' => $room->category->name,
                    'status' => $room->status,
                    'price' => $room->price,
                    'is_clean' => (bool) $room->is_clean,
                ];
            });

            Log::info('Rooms retrieved successfully', ['rooms' => $rooms]);

            return response()->json([
                'success' => true,
                'rooms' => $rooms,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching rooms: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving rooms.',
                'error' => $e->getMessage(), // Remove this in production
            ], 500);
        }
    }



    public function getRoomStatistics(Request $request)
    {
        try {
            $date = $request->query('date', Carbon::today()->toDateString());

            $categories = RoomCategory::with(['rooms'])->get();

            $statistics = $categories->map(function ($category) use ($date) {
                $totalRooms = $category->rooms->count();
                $bookedRooms = Booking::whereIn('room_id', $category->rooms->pluck('id'))
                    ->whereDate('booking_date', $date)
                    ->count();
                $availableRooms = $totalRooms - $bookedRooms;
                return [
                    'category' => $category->name,
                    'total' => $totalRooms,
                    'available' => $availableRooms,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $statistics
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching room statistics: " . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving room statistics.',
                'error' => $e->getMessage() // Remove in production
            ], 500);
        }
    }


    public function getBookedDates(Request $request, $roomId)
    {
        $bookedDates = Booking::where('room_id', $roomId)
            ->pluck('booking_date')
            ->toArray();

        return response()->json([
            'success' => true,
            'booked_dates' => $bookedDates,
        ]);
    }

    /**
     * Book a room for a guest.
     */
    public function bookRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string|max:20',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date_format:Y-m-d H:i:s|after_or_equal:today',
            'check_out' => 'required|date_format:Y-m-d H:i:s|after:check_in',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roomId = $request->room_id;
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Check if the room is available for the given dates
        $existingBooking = Booking::where('room_id', $roomId)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Room is not available for the selected dates.',
            ], 400);
        }

        // Create or find the guest
        $guest = Guest::firstOrCreate(
            ['email' => $request->guest_email],
            ['name' => $request->guest_name, 'phone' => $request->guest_phone]
        );

        // Create a new booking
        $booking = Booking::create([
            'guest_id' => $guest->id,
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room booked successfully',
            'booking' => $booking,
        ], 201);
    }
}
