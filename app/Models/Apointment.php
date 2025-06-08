<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
class Apointment extends Model
{
    protected $guarded = [

    ];
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('apointment_date', Carbon::today());
    }

    public function scopeForDoctor(Builder $query, $doctorId): Builder
    {
        return $query->where('doctor_id', $doctorId);
    }
     public function department():BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
     public function doctor():BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient():BelongsTo{
        return $this->belongsTo(Patient::class);
    }
}