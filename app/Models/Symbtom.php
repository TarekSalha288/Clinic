<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Symbtom extends Model
{
    use HasFactory, HasTranslations;
    protected $guarded = [

    ];
    protected $table = 'symbtoms';
    public $translatable = ['symbtom_name'];
}
