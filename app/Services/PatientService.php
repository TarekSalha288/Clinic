<?php

namespace App\Services;

use App\Models\Patient;

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
}

