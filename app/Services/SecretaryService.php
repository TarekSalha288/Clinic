<?php

namespace App\Services;

use App\Events\EnterPatient;
use App\Jobs\RemoveMonthlyLeaves;
use App\Models\Apointment;
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
 public function reserve()
{
    try {
        $patient = Patient::find(request('patient_id'));
            $doctor = Doctor::find(request('doctor_id'));
            $requestData = request()->all();

        if (!empty($requestData['patient_id'])){
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
            $user_id=null;
            if($patient->user){
                $user_id=$patient->user->id;
            }

             $appointment = Apointment::create([
                'patient_id' => $patient->id,
                'user_id'=>$user_id,
                'doctor_id' => request('doctor_id'),
                'department_id' => $doctor->department->id,
                'apointment_date' => request('appointment_date'),
                'apoitment_status'=>'immediate',
                'status'=>'waiting'
            ]);

            return [
                'status' => 201,
                'message' => 'Appointment added successfully',
                'data' => $appointment
            ];

        } else {
            $validator = Validator::make(request()->all(), [
                'birth_date' => 'required|date',
                'gender' => 'required|string|in:male,female,other',
                'age' => 'required|integer|min:0|max:120',
                'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',//
                'chronic_diseases' => 'required|string|max:500',
                'medication_allergies' => 'required|string|max:500',
                'permanent_medications' => 'required|string|max:500',
                'previous_surgeries' => 'required|string|max:500',
                'previous_illnesses' => 'required|string|max:500',
                'medical_analysis' => 'required|string|max:500',

  'appointment_date'=>'required|date_format:Y-m-d H:i:s.u',
  'doctor_id'=>'required',
  'first_name'=>'required|string',
  'last_name'=>'required|string'
            ]);

            if ($validator->fails()) {
                return [
                    'status' => 400,
                    'errors' => $validator->errors()->toArray()
                ];
            }

            $patient = Patient::create([
                'birth_date' => request('birth_date'),
                'first_name'=>request('first_name'),
                'last_name'=>request('last_name'),
                'phone'=>request('phone'),
                'gender' => request('gender'),
                'age' => request('age'),
                'blood_type' => request('blood_type'),
                'chronic_diseases' => request('chronic_diseases'),
                'medication_allergies' => request('medication_allergies'),
                'permanent_medications' => request('permanent_medications'),
                'previous_surgeries' => request('previous_surgeries'),
                'previous_illnesses' => request('previous_illnesses'),
                'medical_analysis' => request('medical_analysis'),
                'honest_score' => 5.0
            ]);

            $appointment = Apointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => request('doctor_id'),
                'department_id' => $doctor->department->id,
                'apointment_date' => request('appointment_date'),
                'apoitment_status'=>'unapp',
                'status'=>'waiting'
            ]);

            return [
                'status' => 201,
                'message' => 'Appointment added successfully',
                'data' => $appointment
            ];
        }
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
        $patient=Patient::find($appointment->patient_id);
        $user=$patient->user;
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
//         if($user->fcm_token){
//   app('App\Services\FcmService')->sendNotification(
//                 $user->fcm_token,
//                 "Appointment Accepted",
//                 "We have accepted your appointment ",
//                 ['appointment' => $appointment]
//             );
//         }
    Notification::send($user,new Reverse("Your appointment has been accepted"));
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
public function deleteReverse($id){
    try{
        $appointment=Apointment::find($id);
        $patient=Patient::find($appointment->patient_id);
        $user=$patient->user;
        if($appointment){
        $appointment->delete();
        return ['status'=>200,'message'=>"Appointment rejected sucssfully",'data'=>null];
        if ($user) {
           // Notify the user
//         if($user->fcm_token){
//   app('App\Services\FcmService')->sendNotification(
//                 $user->fcm_token,
//                 "Appointment Rejected",
//                 "We have rejected your appointment ",
//                 ['appointment' => $appointment]
//             );
//         }
    Notification::send($user,new Reverse("Your appointment has been rejected reverse again"));
}
        }
        else{
            return ['status'=>404,'message'=>"Appointment not found"];
        }
    }catch(\Exception $e){
         return [
            'status' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ];
    }
}
public function appointments()
{
    try {
        $validator = Validator::make(request()->all(), [
            'doctor_id' => 'required|integer',
            'apointment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ];
        }

        $date = date('Y-m-d', strtotime(request('apointment_date')));

        $appointments = Apointment::whereDate('apointment_date', $date)
            ->where('doctor_id', request('doctor_id'))
            ->get();

        if ($appointments->isEmpty()) {
            return [
                'status' => 404,
                'message' => 'No appointments found for this doctor on the selected date.'
            ];
        }

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



    public function addMonthlyLeave($doctorId)
    {
        try {
            $doctor = Doctor::with('department')->find($doctorId);
            if (!$doctor) {
                return [
                    'status' => 404,
                    'message' => 'Doctor not found'
                ];
            }
            $dayId = request('day_id');
            if (!$dayId) {
                return [
                    'status' => 400,
                    'message' => 'Missing day_id'
                ];
            }
            $departmentDoctorIds = Doctor::where('department_id', $doctor->department->id)
                ->pluck('id');

            $exists = MounthlyLeave::where('day_id', $dayId)
                ->whereIn('doctor_id', $departmentDoctorIds)
                ->exists();

            if ($exists) {
                return [
                    'status' => 409,
                    'message' => 'That day is not available for this department'
                ];
            }

            $leave = MounthlyLeave::create([
                'day_id' => $dayId,
                'doctor_id' => $doctorId
            ]);

            return [
                'status' => 201,
                'message' => 'Leave added successfully',
                'data' => $leave
            ];
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


        $patients = Patient::where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%");
            })->get();

        // Merge apointments
        $appointments = $patients->flatMap(function ($patient) {
            return $patient->apointments()->with(['patient', 'doctor.user', 'department'])->get();;
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
    public function apointments(){
try{
$apointments=Apointment::orderBy('apointment_date','ASC')->with(['patient','doctor.user','department'])->get();
if($apointments->isEmpty())
return ['status'=>404,'message'=>'No appointments yet'];
return ['status'=>200,'message'=>'That is all appointments','data'=>$apointments];
}catch(\Exception $e){
      return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
}
    }
    public function relaseRate(){
        try{
           $apointments= Apointment::where('status','accepted')->get();
           if($apointments->isEmpty())
           return ['status'=>404,'message'=>'No latecomers'];
           foreach($apointments as $apointment){
            $patient = $apointment->patient;
$patient->honest_score -= 0.5;
$patient->save();

           }
           return ['status'=>200,'message'=>'Relase rates done'];
        }catch(\Exception $e){
         return [
                'status' => 500,
                'error' => $e->getMessage()
            ];

        }
    }
    public function enterPatient($id){
        try{
$apointment=Apointment::find($id);
   if($apointment){
  $existingPreview = Preview::where('patient_id', $apointment->patient_id)
            ->where('doctor_id', $apointment->doctor_id)
            ->whereDate('date', now()->toDateString())
            ->exists();
        if ($existingPreview) {
            return ['status' => 400, 'message' => "Alreday send notification"];
        }
  Preview::create(['patient_id'=>$apointment->patient_id,
'doctor_id'=>$apointment->doctor_id,
'department_id'=>$apointment->department_id,
'diagnoseis'=>"",
'diagnoseis_type'=>false,
'medicine'=>"",
'status'=>"",
'notes'=>"",
'date'=>now()]);
$apointment->doctor->notify(new NotificationsEnterPatient("That is your patient",$apointment->patient));

event(new EnterPatient("That is your patient",$apointment->doctor_id,$apointment->patient));
return ['status'=>200,'message'=>"Enter patient done"];

   }
   return ['status'=>404,'message'=>"Appointment not found"];
    }
        catch(\Exception $e){
         return [
                'status' => 500,
                'error' => $e->getMessage()
            ];

        }
}}