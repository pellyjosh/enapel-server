<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PersonnelController extends Controller
{
    public function createStaff(Request $request)
    {
      
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/', 
                'designation' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'dob' => 'required',
                'salary' => 'required|numeric|min:0',
            ]);

            User::create([
                'staffid' => Str::uuid()->toString(),
                'name' => $validatedData['name'],
                'phone' => $validatedData['phone'],
                'designation' => $validatedData['designation'],
                'role' => $validatedData['role'],
                'dob' => $validatedData['dob'],
                'salary' => $validatedData['salary'],
            ]);

        return redirect()->route('staff')->with('success', 'New Staff created successfully!');

    }
    public function showStaff(Request $request){
            $staff = User::all()->makeHidden(['updated_at']);
            $staffdata=[];
            foreach ($staff as $staff){
                $staffdata[]=[
                'id'=>$staff->id,    
                'staffid' => $staff->staffid,
                'name' => $staff->name,
                'phone' => $staff->phone,
                'designation' => $staff->designation,
                'role' => $staff->role,
                'dob' => $staff->dob,
                'salary' => $staff->salary,
                ];
            }
        return view('user.staff.staff', ['staff' => $staffdata]);

    }

    public function updateStaff(Request $request, $id)
    {

            $updatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/',
                'designation' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'dob' => 'required',
                'salary' => 'required|numeric|min:0',
            ]);
            $staff= User::findOrFail($id);
            $staff->update([
                'name' => $updatedData['name'],
                'phone' => $updatedData['phone'],
                'designation' => $updatedData['designation'],
                'role' => $updatedData['role'],
                'dob' => $updatedData['role'],
                'salary' => $updatedData['salary'],
            ]);
        return redirect()->route('staff')->with('success', 'Staff info updated successfully!');
            
    }
    public function deleteStaff($id)
    {
        User::where('id', $id)->delete();

        return redirect()->route('staff')->with('success', 'Staff deleted successfully!');

    }
   
}
