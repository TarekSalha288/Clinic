<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Day;
use App\Models\User;

class UserService
{
    public function getDoctor($id)
    {
        try {
            $doctor = Doctor::find($id);
            $docInfo = [];
            if ($doctor) {
                $apointments = Apointment::where('doctor_id', $doctor->id)->get();

                        $docInfo = [
                        'department' => $doctor->department,
                        'days' => $doctor->days,
                        'user' => $doctor->user,
                        'apointments' => ['apointments' => $apointments, 'patients' => $doctor->patients],
                    ];

                return ['status'=>200,'data'=>$docInfo,'message'=>'That is doctor info'];
            }

            return ['status'=>404,'data'=>null,'message'=>'Doctor not found'];
        } catch (\Exception $e) {
            return ['status'=>500,'errors'=>$e->getMessage()];
        }
    }
    public function getDoctors()
    {
        try {
            $doctors = Doctor::all();
            if ($doctors) {
                foreach ($doctors as $doctor) {
                    $doctor->department;
                    $doctor->user;
                }
                return ['status'=>200,'message'=>"That is all doctor",'data'=>$doctors];
            }

              return ['status'=>404,'message'=>"No doctors yet",'data'=>null];;
        } catch (\Exception $e) {
            return ['status'=>500,'errors'=>$e->getMessage()] ;
        }
    }
    public function getDepartments()
    {
        try {
            $departments = Department::with('doctors.user')->get();
            if ($departments)
                return ['status'=>200,'message'=>'That is all departments','data'=>$departments];
            return ['status'=>404,'message'=>'No departments yet','data'=>null];
        } catch (\Exception $e) {
            return ['status'=>500,'errors'=>$e->getMessage()];
        }
    }
    public function getDepartment($id)
    {
        try {
            $department = Department::with('doctors.user')->first();
            if ($department)
               return ['status'=>200,'message'=>'That is department','data'=>$department];
            return ['status'=>404,'message'=>'No department found','data'=>null];
        } catch (\Exception $e) {
            return ['status'=>500,'errors'=>$e->getMessage()] ;
        }
    }
    public function getLeaves($doctorId)
    {
        try {
            $doctor = Doctor::find($doctorId);
            if ($doctor) {
                $leaves = $doctor->days;
                if(!$leaves->isEmpty())
                return ['status' => 200, 'data' => $leaves];
            return ['status'=>404,'message'=>'No leaves foound for this doctor'];
            }
            return ['status' => 404,'message'=>'Doctor not found'];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'errors' => $e->getMessage()
            ];
        }
    }
    public function getDoctorsByDepartment($departmentId)
    {
        try {
            $department = Department::with(['doctors.user'])->find($departmentId);
            if (!$department)
                return ['status' => 404];
            $doctors = $department->doctors;
            if (!$doctors)
                return ['status' => 400];
            return ['status' => 200, 'data' => $doctors];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'errors' => $e->getMessage()
            ];
        }
    }
    public function getDoctorsInDayAndDepartment($dayId, $departmentId)
    {
        try {
            $day = Day::find($dayId);
            if (!$day)
                return ['status' => 404];
            $doctors = $day->doctors->where('department_id', $departmentId);

            if ($doctors->isEmpty())
                return ['status' => 400];
            return ['status' => 200, 'data' => $doctors];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'errors' => $e->getMessage()
            ];
        }
    }
    public function search()
    {
        try {
            $query = request('search');
            if (!$query) {
                return ['status' => 422, 'message' => 'Search query is required'];
            }
            $doctors = User::where('role', 'doctor')
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                        ->orWhere('last_name', 'LIKE', "%{$query}%");
                })
                ->get();
            $departments = Department::where('name', 'LIKE', "%{$query}%")->get();
            if ($doctors->isEmpty() && $departments->isEmpty()) {
                return ['status' => 404, 'message' => 'No results found'];
            }

            return [
                'status' => 200,
                'data' => [
                    'doctors' => $doctors,
                    'departments' => $departments,
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'errors' => $e->getMessage()
            ];
        }
    }
public function getDoctorsAndDepartment($dayId)
{
    try {
       $day= Day::find($dayId);
        if(!$day)
        return ['status'=>404,'message'=>"This day is not found"];
        $departments = Department::with(['doctors' => function ($query) use ($dayId) {
            $query->whereHas('days', function ($q) use ($dayId) {
                $q->where('day_id', $dayId);
            })->with('user');
        }])->get();

        if ($departments->isEmpty()) {
            return ['status' => 404, 'message' => 'No departments found'];
        }

        return [
            'status' => 200,
            'message' => 'That is departments with doctors of this day',
            'data' => $departments
        ];

    } catch (\Exception $e) {
        return [
            'status' => 500,
            'errors' => $e->getMessage()
        ];
    }
}




}