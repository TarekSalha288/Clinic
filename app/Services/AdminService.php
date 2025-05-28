<?php

namespace App\Services;

use App\Mail\TwoFactorMail;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminService
{


public function createSecretary()
{
    try {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|unique:users,phone|regex:/^\+963\d{9}$/',
            'password'   => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 400,
                'errors' => $validator->errors()
            ];
        }

        // Check for existing secretary if needed
        $existing = User::where('role', 'secretary')->first();
        if ($existing) {
            return ['status' => 409]; // Conflict
        }

        $user = User::create([
            'first_name' => request('first_name'),
            'last_name'  => request('last_name'),
            'email'      => request('email'),
            'phone'      => request('phone'),
            'password'   => bcrypt(request('password')),
            'role'       => 'secretary',
        ]);

        return [
            'status' => 201,
            'user'   => $user,
        ];

    } catch (\Exception $e) {
        return [
            'status' => 500,
            'error'  => $e->getMessage(),
        ];
    }
}

     public function updateSecretary(){
        try{
  $validator = Validator::make(request()->all(), [
    'first_name' => 'required',
    'last_name' => 'required',
    'email' => 'required|email|unique:users,email,',
    'phone' => 'required|regex:/^\+963\d{9}$/|unique:users,phone,',
    'password' => 'confirmed|min:8',
]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
$user = User::where('role','secretary')->first();
        $user->update([
            'email' => request('email'),
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'phone' => request('phone'),
            'password' => bcrypt(request('password')),
        ]);
       // $user->save();
        return $user;
        }catch(\Exception $e){
             return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function deleteSecretary(){
        try{
           $secretary=User::where('role','secretary')->first();
           if($secretary){
            $secretary->delete();
            return null;
           }
           return "No secretary account for delete it";
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
 public function createDoctor(){
    try {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'bio' => 'required',
            'department'=>'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users|regex:/^\+963\d{9}$/',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'email' => request('email'),
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'phone' => request('phone'),
            'password' => bcrypt(request('password')),
            'role'=>'doctor',
        ]);

        $department = Department::where('name', request('department'))->first();

        if(!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'bio' => request('bio')
        ]);
       return $doctor;

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

  public function deleteDoctor($id){
      try {
          $doctor=Doctor::findOrFail($id);
          if($doctor){
              Doctor::destroy($id);
              User::destroy($doctor->user_id);
              return null;
          }
          return "Doctor Not Found";
      } catch (\Exception $e) {
          throw $e;
      }
  }

  public function createDepartment(){
      try {
          $validator = Validator::make(request()->all(), [
                'name' => 'required|unique:departments',
                'description'=>'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            $department=Department::create([
                'name'=>request('name'),
                'description'=>request('description'),
            ]);
            return $department;
      } catch (\Exception $e) {
          throw $e;
      }
  }

  public function deleteDepartment($id){
      try {
          $department=Department::findOrFail($id);
          if($department){
              Department::destroy($id);
              return null;
          }
      } catch (\Exception $e) {
          throw $e;
      }
  }

  public function deleteUser($id){
      try {
          $user=User::findOrFail($id);
          if($user){
              User::destroy($id);
              return null;
          }
          return "User Not Found";
      } catch (\Exception $e) {
          throw $e;
      }
  }
}