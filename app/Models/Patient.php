<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Patient extends Model
{
    protected $guarded = [];

    protected $encryptable = [
        'first_name',
        'last_name',

        'phone',
        'gender',
        'blood_type',
        'chronic_diseases',
        'medication_allergies',
        'permanent_medications',
        'previous_surgeries',
        'previous_illnesses',
        'medical_analysis'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            do {
                $id = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (static::where('id', $id)->exists());//unique

            $model->id = $id;
        });
    }

    public $incrementing = false;
    protected $keyType = 'string';
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'apointments');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apointments(): HasMany
    {
        return $this->hasMany(Apointment::class);
    }

    public static function encryptField($value)
    {
        if (empty($value)) return $value;

        $key = config('app.encryption_key');
        $cipher = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        $encrypted = openssl_encrypt($value, $cipher, $key, 0, $iv);
        return $encrypted . '::' . bin2hex($iv);
    }
//تابع فك التشفير
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
//بترجع البيانات مع فك التشفير من الداتا بيس
    public function getFirstNameAttribute($value) { return self::decryptField($value); }//static -> so self this don't work
    public function getLastNameAttribute($value) { return self::decryptField($value); }
    public function getPhoneAttribute($value) { return self::decryptField($value); }
    public function getGenderAttribute($value) { return self::decryptField($value); }
    public function getBloodTypeAttribute($value) { return self::decryptField($value); }
    public function getChronicDiseasesAttribute($value) { return self::decryptField($value); }
    public function getMedicationAllergiesAttribute($value) { return self::decryptField($value); }
    public function getPermanentMedicationsAttribute($value) { return self::decryptField($value); }
    public function getPreviousSurgeriesAttribute($value) { return self::decryptField($value); }
    public function getPreviousIllnessesAttribute($value) { return self::decryptField($value); }
    public function getMedicalAnalysisAttribute($value) { return self::decryptField($value); }
}