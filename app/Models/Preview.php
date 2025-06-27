<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Preview extends Model
{
    protected $guarded = [

    ];
    protected $encryptable = [
        'diagnoseis',
        'diagnoseis_type',
        'medicine',
        'notes',
        'date',
        'status',
    ];

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeDiagnoseisType($query, $type)
    {
        return $query->where('diagnoseis_type', $type);
    }
    public function scopeOfPatientAndDoctor($query, $patientId, $doctorId)
    {
        return $query->where('patient_id', $patientId)
            ->where('doctor_id', $doctorId);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
    public function medical_analysis():HasMany{
        return $this->hasMany(MedicalAnalysis::class);
    }
    public static function encryptField($value)
    {
        if (empty($value))
            return $value;

        $key = config('app.encryption_key');
        $cipher = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        $encrypted = openssl_encrypt($value, $cipher, $key, 0, $iv);
        return $encrypted . '::' . bin2hex($iv);
    }
    public static function decryptField($encryptedValue)
    {
        if (empty($encryptedValue) || strpos($encryptedValue, '::') === false) {
            return $encryptedValue;
        }

        list($data, $iv) = explode('::', $encryptedValue);
        $key = config('app.encryption_key');
        $cipher = 'AES-256-CBC';

        return openssl_decrypt($data, $cipher, $key, 0, hex2bin($iv));
    }
    public function getEncryptableFields()
    {
        return $this->encryptable;
    }

    public function getDiagnoseisAttribute($value)
    {
        return self::decryptField($value);
    }
    public function getMedicineAttribute($value)
    {
        return self::decryptField($value);
    }
    public function getNotesAttribute($value)
    {
        return self::decryptField($value);
    }
    public function getStatusAttribute($value)
    {
        return self::decryptField($value);
    }

}
