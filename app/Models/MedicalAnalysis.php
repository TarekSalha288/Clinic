<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAnalysis extends Model
{
    protected $guarded = [

    ];
    public function preview():BelongsTo{
        return $this->belongsTo(Preview::class);
    }
}