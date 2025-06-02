<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Son;

class PatientService
{
    public function postPatientInformation($request)
    {
        $user_id = auth()->user()->id;
        $patient = Patient::create([
            'user_id' => $user_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_type' => $request->blood_type,
            'chronic_diseases' => $request->chronic_diseases,
            'medication_allergies' => $request->medication_allergies,
            'permanent_medications' => $request->permanent_medications,
            'previous_surgeries' => $request->previous_surgeries,
            'previous_illnesses' => $request->previous_illnesses,
            'medical_analysis' => $request->medical_analysis,
            'honest_score' => 100
        ]);
        if ($patient) {
            $message = 'patient profile added successfullt';
        } else {
            $message = 'patient profile not added to the system';
        }
        return ['message' => $message, 'patient' => $patient];
    }
    public function addChild($request)
    {
        $user_id = auth()->user()->id;
        $patient = Patient::create([
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_type' => $request->blood_type
        ]);
        $son = Son::create([
            'patient_id' => $patient->id,
            'parent_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);
        if ($patient && $son) {
            $message = 'son profile added successfullt';
        } else {
            $message = 'son profile not added to the system';
        }
        return ['message' => $message, 'son' => $son];

    }
}

