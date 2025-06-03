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
                foreach ($apointments as $apointment)

                    $docInfo = [
                        'department' => $doctor->department,
                        'days' => $doctor->days,
                        'user' => $doctor->user,
                        'apointments' => ['apointments' => $apointments, 'patients' => $doctor->apointments],
                    ];
                return $docInfo;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
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
                return $doctors;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getDepartments()
    {
        try {
            $departments = Department::all();
            if ($departments)
                return $departments;
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getDepartment($id)
    {
        try {
            $department = Department::find($id);
            if ($department)
                return $department;
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getLeaves($doctorId)
    {
        try {
            $doctor = Doctor::find($doctorId);
            if ($doctor) {
                $leaves = $doctor->days;
                return ['status' => 200, 'data' => $leaves];
            }
            return ['status' => 404];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ];
        }
    }
    public function getDoctorsByDepartment($departmentId)
    {
        try {
            $department = Department::find($departmentId);
            if (!$department)
                return ['status' => 404];
            $doctors = $department->doctors;
            if (!$doctors)
                return ['status' => 400];
            return ['status' => 200, 'data' => $doctors];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ];
        }
    }
    public function getDoctorsInDay($dayId, $departmentId)
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
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ];
        }
    }


}
