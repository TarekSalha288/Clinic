<?php

use App\Models\Post;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('enter-patient.{doctorId}', function () {
    return true;
});
