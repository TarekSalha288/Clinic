<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Post;
use App\Models\User;
use App\UploadImageTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
    public function uploadImage($request)
    {
        $user = auth()->user();
        $message = 'there is no file to upload';
        $url = null;

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            $imageName = md5_file($imageFile->getRealPath()) . '.' . $imageFile->getClientOriginalExtension();
            $path = "images/posts/$user->id/$imageName";

            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->putFileAs("images/posts/$user->id", $imageFile, $imageName);
            }

            $url = Storage::url($path);
            $message = 'image uploaded successfully';
        }

        return ['message' => $message, 'path' => $url];
    }

    public function updateProfile($request)
    {

        $user = User::find(auth()->user()->id);
        if ($user) {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password
            ]);
            $user->save();
            $message = 'profile updated successfully';
        }
        return ['message' => $message, 'user' => $user];
    }

}