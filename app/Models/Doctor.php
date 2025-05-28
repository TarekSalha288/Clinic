<?php

namespace App\Models;

use Carbon\Month;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doctor extends Model
{
    protected $guarded = [

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function days(): BelongsToMany{
return $this->belongsToMany(Day::class,'mounthly_leaves');
    }
    public function department():BelongsTo{
        return $this->belongsTo(Department::class);
    }
}
