<?php

use App\Models\Post;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('enter-patient.{doctorId}', function (User $user, int $doctorId) {
   return $user->doctor->id === $doctorId && $user->role === 'doctor'; // Accses just for doctor
});