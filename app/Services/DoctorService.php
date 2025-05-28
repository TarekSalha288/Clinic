<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Post;

class DoctorService
{
    public function postArtical($request)
    {
        $user_id = auth()->user()->id;
        $doctor = Doctor::query()->where('user_id', $user_id)->first();
        $doctor_id = $doctor->id;
        $post = Post::query()->create([
            'doctor_id' => $doctor_id,
            'title' => $request->title,
            'body' => $request->body
        ]);
        $message = 'articale created successfully';
        if (!$post) {
            $message = 'somthing went wrong the post not created';
        }
        return ['message' => $message, 'post' => $post];
    }

}