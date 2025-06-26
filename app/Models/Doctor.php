<?php

namespace App\Models;

use Carbon\Month;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Doctor extends Model
{
    use Notifiable;
    use HasFactory;
    protected $guarded = [

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class, 'mounthly_leaves');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function apointments(): HasMany
    {
        return $this->hasMany(Apointment::class);
    }
    public function rates(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'rates')->withPivot('rate');
    }

    public function scopeWithAverageRating($query)
    {
        return $query->selectRaw('
        doctors.*,
        COALESCE(
            ROUND(
                (SELECT AVG(rate) FROM rates WHERE doctor_id = doctors.id),
                1
            ),
            5.0
        ) as average_rating
    ');
    }

}