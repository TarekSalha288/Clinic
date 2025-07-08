<?php

namespace App\Services;

use App\Events\EnterPatient;
use App\Jobs\RemoveMonthlyLeaves;
use App\Models\Apointment;
use App\Models\Day;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MounthlyLeave;
use App\Models\Patient;
use App\Models\Preview;
use App\Notifications\EnterPatient as NotificationsEnterPatient;
use App\Notifications\Reverse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class SecretaryService
{
    public function reverseUnApp()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'birth_date' => 'required|date',
                'gender' => 'required|string|in:male,female,other',
                'age' => 'required|integer|min:0|max:120',
                'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'chronic_diseases' => 'required|string|max:500',
                'medication_allergies' => 'required|string|max:500',
                'permanent_medications' => 'required|string|max:500',
                'previous_surgeries' => 'required|string|max:500',
                'previous_illnesses' => 'required|string|max:500',
                'appointment_date' => 'required',
                'doctor_id' => 'required',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => 400,
                    'errors' => $validator->errors()->toArray()
                ];
            }

            $doctor = Doctor::find(request('doctor_id'));


            $patient = Patient::create([
                'birth_date' => request('birth_date'),
                'first_name' => Patient::encryptField(request('first_name')),
                'last_name' => Patient::encryptField(request('last_name')),
                'phone' => Patient::encryptField(request('phone')),
                'gender' => request('gender'),
                'age' => request('age'),
                'blood_type' => Patient::encryptField(request('blood_type')),
                'chronic_diseases' => Patient::encryptField(request('chronic_diseases')),
                'medication_allergies' => Patient::encryptField(request('medication_allergies')),
                'permanent_medications' => Patient::encryptField(request('permanent_medications')),
                'previous_surgeries' => Patient::encryptField(request('previous_surgeries')),
                'previous_illnesses' => Patient::encryptField(request('previous_illnesses')),
                'honest_score' => 5.0
            ]);
            $appointment = Apointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => request('doctor_id'),
                'department_id' => $doctor->department->id,
                'apointment_date' => request('appointment_date'),
                'apoitment_status' => 'unapp',
                'status' => 'accepted'
            ]);

            return [
                'status' => 201,
                'message' => 'Appointment added successfully',
                'data' => [
                    'appointment' => $appointment,
                    'patient' => $patient->makeHidden(array_merge($patient->getEncryptableFields(), ['created_at', 'updated_at']))
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }
    public function reverse()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'doctor_id' => 'required',
                'patient_id' => 'required',
                'apointment_date' => 'required'
            ]);
            if ($validator->fails()) {
                return [
                    'status' => 400,
                    'errors' => $validator->errors()->toArray()
                ];
            }
            $patient = Patient::find(request('patient_id'));
            $doctor = Doctor::find(request('doctor_id'));
            $user = $patient->user;
            if (!$patient) {
                return [
                    'status' => 404,
                    'message' => 'Patient not found'
                ];
            }
            if (!$doctor) {
                return [
                    'status' => 404,
                    'message' => 'Doctor not found'
                ];
            }


            $appointment = Apointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => request('doctor_id'),
                'department_id' => $doctor->department->id,
                'apointment_date' => request('apointment_date'),
                'apoitment_status' => 'immediate',
                'status' => 'accepted'
            ]);
            if (!$user) {
                $appointment->update(['apoitment_status' => 'unapp']);
                $appointment->save();
            }
$appointment->patient;
            return [
                'status' => 201,
                'message' => 'Appointment added successfully',
                'data' => $appointment,
            ];


        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }

    public function acceptReverse($id)
    {
        try {
            $appointment = Apointment::find($id);
            $patient = Patient::find($appointment->patient_id);
            $user = $patient->user;
            if (!$appointment) {
                return [
                    'status' => 404,
                    'message' => 'Appointment not found',
                ];
            }
            if ($appointment->status === 'accepted') {
                return [
                    'status' => 400,
                    'message' => 'You already accepted this appointment.',
                ];
            }
            $conflict = Apointment::where('department_id', $appointment->department_id)
                ->where('doctor_id', $appointment->doctor_id)
                ->where('apointment_date', $appointment->apointment_date)
                ->where('status', 'accepted')
                ->where('id', '!=', $appointment->id)
                ->exists();
            if ($conflict) {
                return [
                    'status' => 400,
                    'message' => 'Cannot accept this appointment. There is already an accepted appointment at the same time.',
                ];
            }
            $appointment->update(['status' => 'accepted']);

            if ($user) {
                // Notify the user
        if($user->fcm_token){
  app('App\Services\FcmService')->sendNotification(
                $user->fcm_token,
                "Appointment Accepted",
                "We have accepted your appointment ",
                ['appointment' => $appointment]
            );
        }
                Notification::send($user, new Reverse("Your appointment has been accepted"));
            }

            return [
                'status' => 200,
                'message' => 'Appointment accepted successfully.',
                'data' => $appointment,
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ];
        }
    }
    public function deleteReverse($id)
    {
        try {
            $appointment = Apointment::find($id);
if(!$appointment)
 return ['status' => 404, 'message' => "Appointment not found"];
 $patient = Patient::find($appointment->patient_id);
            $user = $patient->user;
                if ($user) {
                    // Notify the user
        if($user->fcm_token){
  app('App\Services\FcmService')->sendNotification(
                $user->fcm_token,
                "Appointment Rejected",
                "We have rejected your appointment ",
                ['appointment' => $appointment]
            );
        }
                    Notification::send($user, new Reverse("Your appointment has been rejected reverse again"));
                }
                $appointment->delete();
                return ['status' => 200, 'message' => "Appointment rejected sucssfully", 'data' => null];
            }
         catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ];
        }
    }
    public function appointments($doctor_id, $appointment_date)
    {
        try {

           // $date = date('Y-m-d', strtotime($appointment_date));

            $appointments = Apointment::with('patient')->whereDate('apointment_date', $appointment_date)
                ->where('doctor_id', $doctor_id)
                ->get();


            return [
                'status' => 200,
                'message' => 'Appointments found.',
                'data' => ['appointments' => $appointments]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
        }
    }
    public function monthlyLeaves()
    {
        try {
            $all = MounthlyLeave::all();
            if ($all->isEmpty())
                return ['status' => 400, 'message' => 'No monthly leaves'];
            return ['status' => 200, 'message' => 'That is all monthly leaves', 'data' => $all];
        } catch (\Exception $e) {
            return ['status' => 500, 'errors' => $e->getMessage()];
        }

    }


    public function addMonthlyLeaves()
    {
        try {
            $requests = request()->json()->all();
            $createdLeaves = [];
            $errors = [];

            foreach ($requests as $index => $request) {
                if (!isset($request['doctor_id']) || !isset($request['day_id'])) {
                    $errors[] = [
                        'index' => $index,
                        'message' => "Missing doctor_id or day_id",
                        'request' => $request
                    ];
                    continue;
                }
                $doctor = Doctor::with('department')->find($request['doctor_id']);
                $day = Day::find($request['day_id']);
                if (!$day) {
                    $errors[] = [
                        'index' => $index,
                        'message' => "Day Not Found (ID: {$request['day_id']})",
                        'request' => $request
                    ];
                    continue;
                }
                if (!$doctor) {
                    $errors[] = [
                        'index' => $index,
                        'message' => "Doctor Not Found (ID: {$request['doctor_id']})",
                        'request' => $request
                    ];
                    continue;
                }
                $doctor = Doctor::where('department_id', $doctor->department_id)->pluck('id');
                $dayAvailable = !MounthlyLeave::where('day_id', $request['day_id'])
                    ->whereIn('doctor_id', $doctor)
                    ->exists();
                if ($dayAvailable) {
                    $createdLeaves[] = MounthlyLeave::create([
                        'doctor_id' => $request['doctor_id'],
                        'day_id' => $request['day_id']
                    ]);
                } else {
                    $doctor = Doctor::find($request['doctor_id']);
                    $doctorName = $doctor->user->first_name . " " . $doctor->user->last_name;
                    $dayName = $day->available_days;
                    $errors[] = [
                        'index' => $index,
                        'message' => "Doctor {$doctorName}  can't take  Day {$dayName} because it is already taken by another doctor in the same department",
                        'request' => $request
                    ];
                }
            }

            return [
                'status' => (count($errors) > 0 && count($createdLeaves) > 0) ? 207 : // Multi-status
                    (count($errors) > 0 ? 400 : 201),
                'message' => count($createdLeaves) . ' leaves created, ' . count($errors) . ' errors',
                'data' => $createdLeaves,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Server Error',
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


            $patients = Patient::where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%");
            })->get();

            // Merge apointments
            $appointments = $patients->flatMap(function ($patient) {
                return $patient->apointments()->with(['patient', 'doctor.user', 'department'])->get();
                ;
            });
            if ($appointments->isEmpty()) {
                return ['status' => 404, 'message' => 'No appointments found'];
            }

            return [
                'status' => 200,
                'data' => $appointments
            ];

        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ];
        }


    }
    public function removeMonthlyleaves()
    {
        try {
            RemoveMonthlyLeaves::dispatch();
            return ['status' => 200];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }
    public function apointments()
    {
        try {
            $apointments = Apointment::orderBy('apointment_date', 'ASC')->with(['patient', 'doctor.user', 'department'])->get();
            if ($apointments->isEmpty())
                return ['status' => 404, 'message' => 'No appointments yet'];
            return ['status' => 200, 'message' => 'That is all appointments', 'data' => $apointments];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }
    public function relaseRate()
    {
        try {
            $apointments = Apointment::where('status', 'accepted')->get();
            if ($apointments->isEmpty())
                return ['status' => 404, 'message' => 'No latecomers'];
            foreach ($apointments as $apointment) {
                $patient = $apointment->patient;
                $patient->honest_score -= 10;
                $patient->save();

            }
            return ['status' => 200, 'message' => 'Relase rates done'];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];

        }
    }
    public function enterPatient($id)
    {
        try {
            $apointment = Apointment::find($id);
            if ($apointment) {
                $existingPreview =Apointment::where('enter',1)->where('doctor_id',$apointment->doctor_id)->exists();

                if ($existingPreview) {
                    return ['status' => 400, 'message' => "Alreday send notification"];
                }
                $existingEnter = Apointment::where('enter', true)->where('doctor_id', $apointment->doctor_id)->exists();
                if ($existingEnter)
                    return ['status' => 400, 'message' => "Alreday have a patient now"];
                $apointment->update(['enter' => true]);
                $apointment->save();
                $preview = Preview::with('medical_analysis')->where('patient_id', $apointment->patient_id)
                    ->where('department_id', $apointment->department_id)
                    ->where('diagnoseis_type', false)->first();
                if (!$preview) {
                    Preview::create([
                        'patient_id' => $apointment->patient_id,
                        'doctor_id' => $apointment->doctor_id,
                        'department_id' => $apointment->department_id,
                        'diagnoseis' => "",
                        'diagnoseis_type' => false,
                        'medicine' => "",
                        'status' => "",
                        'notes' => "",
                        'date' => now()
                    ])->with('medical_analysis');
                }

                $apointment->doctor->notify(new NotificationsEnterPatient("That is your patient", $apointment->patient));

                event(new EnterPatient("That is your patient", $apointment->doctor_id, $apointment->patient,$preview));
                return ['status' => 200, 'message' => "Enter patient done"];

            }
            return ['status' => 404, 'message' => "Appointment not found"];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];

        }
    }
}
