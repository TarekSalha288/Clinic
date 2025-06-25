<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $guarded = [

    ];
    public function scopeOfPatientAndDoctor($query, $patientId, $doctorId)
    {
        return $query->where('patient_id', $patientId)
            ->where('doctor_id', $doctorId);
    }

}