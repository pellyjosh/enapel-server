<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PersonnelController extends Controller
{
    public function addStaff(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/', 
                'designation' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'dob' => 'required',
                'salary' => 'required|numeric|min:0',
            ]);

            $staffData =User::create([
                'staffid' => Str::uuid()->toString(),
                'name' => $validatedData['name'],
                'phone' => $validatedData['phone'],
                'designation' => $validatedData['designation'],
                'role' => $validatedData['role'],
                'dob' => $validatedData['role'],
                'salary' => $validatedData['salary'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'New staffData created successfully',
                'data' => $staffData,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the Staff data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function staffData(Request $request){
        try {
            $staffData = User::all()->makeHidden(['updated_at']);

            if ($staffData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff data found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'staff data retrieved successfully',
                'data' => $staffData
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function updateStaff(Request $request)
    {
        try {
            $id = $request->id;

            $updatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/',
                'designation' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'dob' => 'required',
                'salary' => 'required|numeric|min:0',
            ]);

            $updated = User::where('id', $id)->update([
                'name' => $updatedData['name'],
                'phone' => $updatedData['phone'],
                'designation' => $updatedData['designation'],
                'role' => $updatedData['role'],
                'dob' => $updatedData['role'],
                'salary' => $updatedData['salary'],
            ]);
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Staff data updated successfully',
                    'data' => $updatedData,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff data not found or update failed',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the Staff data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteStaff(Request $request)
    {
        $staff = $request->id;
        User::where('id', $staff)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff deleted',
        ], 200);
    }
   
}
