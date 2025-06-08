<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    protected $guarded = [

    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            do {
                $id = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (static::where('id', $id)->exists());

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
    public function favoritePosts()
    {
        return $this->hasMany(FavoritePost::class);
    }
}