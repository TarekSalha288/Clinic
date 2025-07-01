<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use HasFactory, HasTranslations;
    protected $guarded = [

    ];

    public $translatable = ['name', 'description'];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
    public function apointments(): HasMany
    {
        return $this->hasMany(Apointment::class);
    }
}
