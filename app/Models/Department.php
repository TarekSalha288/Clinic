<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $guarded = [

    ];
public function doctors():HasMany{
    return $this->hasMany(Doctor::class);
}
}