<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}