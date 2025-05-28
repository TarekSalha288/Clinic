<?php

namespace App\Services;

use App\Jobs\RemoveMonthlyLeaves;
use App\Models\Apointment;
use App\Models\Day;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MounthlyLeave;
use App\Models\Patient;



class SecretaryService
{
// public function reserve() {
//     try {
//         $validator = Validator::make(request()->all(), [
//             'birth_date' => 'required|date',
//             'gender' => 'required|string|in:male,female,other',
//             'age' => 'required|integer|min:0|max:120',
//             'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
//             'chronic_diseases' => 'nullable|string|max:500',
//             'medication_allergies' => 'nullable|string|max:500',
//             'permanent_medications' => 'nullable|string|max:500',
//             'previous_surgeries' => 'nullable|string|max:500',
//             'previous_illnesses' => 'nullable|string|max:500',
//             'medical_analysis' => 'nullable|string|max:500',
//             'honest_score' => 'nullable|integer|min:0|max:10',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         $patient = Patient::create([
//             'birth_date' => request('birth_date'),
//             'gender' => request('gender'),
//             'age' => request('age'),
//             'blood_type' => request('blood_type'),
//             'chronic_diseases' => request('chronic_diseases'),
//             'medication_allergies' => request('medication_allergies'),
//             'permanent_medications' => request('permanent_medications'),
//             'previous_surgeries' => request('previous_surgeries'),
//             'previous_illnesses' => request('previous_illnesses'),
//             'medical_analysis' => request('medical_analysis'),
//             'honest_score' => request('honest_score'),
//         ]);
//         $apointment=Apointment::create([
//             'patient_id'=>$patient->id,
//             'doctor_id'=>
//         ])

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

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
public function removeMonthlyleaves(){
    try{
RemoveMonthlyLeaves::dispatch();
return ['status'=>200];
    }catch(\Exception $e){
       return [
            'status' => 500,
            'error' => $e->getMessage()
        ];
    }

}

}
