<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Day;
use App\Models\User;
use App\UploadImageTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;


class UserService
{
    use UploadImageTrait;
    public function getDoctor($id)
    {
        try {
            $doctor = Doctor::with(['department', 'days', 'user'])->withAverageRating()->find($id);
            $docInfo = [];
            if ($doctor) {
                $apointments = Apointment::where('doctor_id', $doctor->id)->get();

                $docInfo = [
                    'department' => $doctor->department,
                    'days' => $doctor->days,
                    'user' => $doctor->user,
                    'apointments' => [
                        'apointments' => $apointments,
                        'patients' => $doctor->patients,

                    ],
                    'average_rating' => $doctor->average_rating ?? 5.0,


                ];

                return ['status' => 200, 'data' => $docInfo, 'message' => 'That is doctor info'];
            }

            return ['status' => 404, 'data' => null, 'message' => 'Doctor not found'];
        } catch (\Exception $e) {
            return ['status' => 500, 'errors' => $e->getMessage()];
        }
    }
    public function getDoctors()
    {
        try {
            $doctors = Doctor::with(['department', 'user'])->withAverageRating()->get();
            if ($doctors->isNotEmpty())
                return ['status' => 200, 'message' => "That is all doctors", 'data' => $doctors];
            return ['status' => 404, 'message' => "No doctors yet", 'data' => null];
        } catch (\Exception $e) {
            return ['status' => 500, 'errors' => $e->getMessage()];
        }
    }
public function getDepartments()
{
    $locale = request()->input('lang');
    App::setLocale($locale);

    if (!$locale) {
        return [
            'status' => 400,
            'message' => 'You must enter the lang type',
            'data' => null
        ];
    }

    try {
        $departments = Department::with(['doctors' => function($query) {
            $query->withAverageRating()->with('user');
        }])->get()->map(function ($department) use ($locale) {
            $data = $department->toArray();

            $data['name'] = $department->getTranslation('name', $locale);
            $data['description'] = $department->getTranslation('description', $locale);
            $data['doctors'] = $department->doctors->map(function ($doctor) {
                return [
                  'doctor'=>$doctor,
                ];
            });

            return $data;
        });

        if ($departments->isNotEmpty()) {
            return [
                'status' => 200,
                'message' => 'That is all departments',
                'data' => $departments
            ];
        }

        return [
            'status' => 404,
            'message' => 'No departments yet',
            'data' => null
        ];
    } catch (\Exception $e) {
        return [
            'status' => 500,
            'errors' => $e->getMessage()
        ];
    }
}
    public function getDepartment($id)
    {
        $locale = request()->input('lang');
        App::setLocale($locale);
        if (!$locale) {
            return [
                'status' => 400,
                'message' => 'you must enter the lang type',
                'data' => null
            ];
        }
        try {
            $department = Department::with('doctors.user')->find($id);

            if ($department) {
                $data = $department->toArray();
                $data['name'] = $department->getTranslation('name', $locale);
                $data['description'] = $department->getTranslation('description', $locale);
                return ['status' => 200, 'message' => 'That is department', 'data' => $data];
            }
            return ['status' => 404, 'message' => 'No department found', 'data' => null];
        } catch (\Exception $e) {
            return ['status' => 500, 'errors' => $e->getMessage()];
        }
    }
    public function getLeaves($doctorId)
    {
        try {
            $doctor = Doctor::find($doctorId);
            if ($doctor) {
                $leaves = $doctor->days;
                if (!$leaves->isEmpty())
                    return ['status' => 200, 'data' => $leaves];
                return ['status' => 404, 'message' => 'No leaves foound for this doctor'];
            }
            return ['status' => 404, 'message' => 'Doctor not found'];
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
            $department = Department::find($departmentId);
            if (!$department) {
                return ['status' => 404];
            }

            $doctors = $department->doctors()->with('user')->withAverageRating()->get();
            if ($doctors->isEmpty()) {
                return ['status' => 400];
            }

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
            if (!$day) {
                return ['status' => 404];
            }

            $doctors = $day->doctors()->with('user')
                ->where('department_id', $departmentId)
                ->withAverageRating()
                ->get();

            if ($doctors->isEmpty()) {
                return ['status' => 400];
            }

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
        $locale = request()->input('lang');
        App::setLocale($locale);
        if (!$locale) {
            return [
                'status' => 400,
                'message' => 'you must enter the lang type',
                'data' => null
            ];
        }
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
                ->with([
                    'doctor' => function ($query) {
                        $query->withAverageRating();
                    }
                ])
                ->get()
                ->map(function ($user) {
                    if ($user->doctor) {
                        $user->doctor->average_rating = $user->doctor->average_rating ?? 0;
                    }
                    return $user;
                });

            $departments = Department::where('name', 'LIKE', "%{$query}%")->get()->map(function ($department) use ($locale) {
                $data = $department->toArray();

                $data['name'] = $department->getTranslation('name', $locale);
                $data['description'] = $department->getTranslation('description', $locale);

                return $data;
            });


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
        $locale = request()->input('lang');
        App::setLocale($locale);
        if (!$locale) {
            return [
                'status' => 400,
                'message' => 'you must enter the lang type',
                'data' => null
            ];
        }
        try {
            $day = Day::find($dayId);
            if (!$day)
                return ['status' => 404, 'message' => "This day is not found"];

            $departments = Department::with([
                'doctors' => function ($query) use ($dayId) {
                    $query->whereHas('days', function ($q) use ($dayId) {
                        $q->where('day_id', $dayId);
                    })
                        ->with('user')
                        ->withAverageRating();
                }
            ])->get()->map(function ($department) use ($locale) {
                $data = $department->toArray();

                $data['name'] = $department->getTranslation('name', $locale);
                $data['description'] = $department->getTranslation('description', $locale);

                return $data;
            });

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
    public function uploadImage($request, $folderName)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'path' => null, 'code' => $code];
        }
        $user_id = $user->id;
        $url = $this->ImageUpload($request, $user_id, $folderName);
        if ($url) {
            if ($folderName === "Profile_Photo") {
                $user->img_path = $url;
                $user->save();
            }
            $message = "image uploaded successfully";
            $code = 200;
        } else {
            $message = 'there is no file to upload';
            $code = 400;
        }
        return ['message' => $message, 'path' => $url, 'code' => $code];
    }
    public function getProfileImage()
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'path' => null, 'code' => $code];
        }
        if ($user) {
            $path = $user->img_path;
            if ($path) {
                $message = 'image uploaded successfully';
                $code = 200;
            } else {
                $message = 'you dont uploaded image yet';
                $code = 400;
            }
        } else {
            $message = 'user not found';
            $code = 404;
        }
        return ['message' => $message, 'path' => $path, 'code' => $code];
    }
    public function deleteProfileImage()
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'path' => null, 'code' => $code];
        }
        $img_path = $user->img_path;
        if ($img_path) {
            $storagePath = str_replace('/storage/', '', $img_path);
            if (Storage::disk('public')->exists($storagePath))
                Storage::disk('public')->delete($storagePath);
            $user->img_path = null;
            $user->save();
            $message = "image deleted succussfully";
            $code = 200;
        } else {
            $message = 'image deleted failed';
            $code = 400;
        }
        return ['message' => $message, 'path' => $img_path, 'code' => $code];
    }
 public function getNotifications()
{
    try {
        $notifications = auth()->user()->notifications;

        if ($notifications->isEmpty()) {
            return ['status' => 400, 'message' => "No notifications yet", 'data' => null];
        }

        return ['status' => 200, 'data' => $notifications];
    } catch (\Exception $e) {
        return ['status' => 500, 'errors' => $e->getMessage()];
    }
}




}
