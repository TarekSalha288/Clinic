<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Doctor;
use App\Models\Post;
use App\Models\User;
use App\UploadImageTrait;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class DoctorService
{
    use UploadImageTrait;
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
    public function uploadImage($request, $folderName)
    {
        $user_id = auth()->user()->id;

        $url = $this->ImageUpload($request, $user_id, $folderName);
        if ($url) {
            $message = "image uploaded successfully";
        } else {
            $message = 'there is no file to upload';
        }

        return ['message' => $message, 'path' => $url];
    }

    public function updateArticle($request, $id)
    {
        $article = Post::where('id', $id)->first();
        $oldbody = $article->body;
        $newbody = $request->body;
        $article->update([
            'title' => $request->title,
            'body' => $request->body
        ]);

        preg_match_all('/\[https?:\/\/[^\/]+(\/storage\/[^\]]+)\]/', $oldbody, $matches);
        $oldPaths = $matches[1];
        preg_match_all('/\[https?:\/\/[^\/]+(\/storage\/[^\]]+)\]/', $newbody, $matches);
        $newPaths = $matches[1];

        foreach ($oldPaths as $oldPath) {
            $flag = true;
            foreach ($newPaths as $newPath) {
                if ($oldPath === $newPath) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                if (Storage::disk('public')->exists($oldPath))
                    Storage::disk('public')->delete($oldPath);
            }
        }


        if ($article) {
            $message = 'article updated successfully';
        } else {
            $message = 'article updated failed';
        }
        return ['article' => $article, 'message' => $message];
    }
    public function deleteArticle($id)
    {
        $article = Post::find($id);
        $body = $article->body;
        $article->delete();
        preg_match_all('/\[https?:\/\/[^\/]+(\/storage\/[^\]]+)\]/', $body, $matches);

        $imagePaths = $matches[1];

        foreach ($imagePaths as $path) {
            $storagePath = str_replace('/storage/', '', $path);

            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
                $deleted[] = $storagePath;
            }
        }
        $message = 'article deleted successfully';
        return ['article' => $article, 'message' => $message];
    }
    public function getArticles()
    {
        $doctor = auth()->user()->doctor;
        $articles = $doctor->posts;
        if ($articles->isNotEmpty()) {
            $message = 'Articles return successfully';
        } else {
            $message = "Articles failed";
        }
        return ['articles' => $articles, 'message' => $message];
    }
    public function getArticleById($id)
    {
        $doctor = auth()->user()->doctor;
        $article = $doctor->posts()->where('id', $id)->first();
        if ($article) {
            $message = "article return successfully";
        } else {
            $message = "article return failed";
        }
        return ['article' => $article, 'message' => $message];
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
    public function getApointments()
    {
        $user_id = auth()->user()->id;
        $doctor_id = Doctor::find($user_id)->id;
        $apointments = Apointment::forDoctor($doctor_id)
            ->accepted()
            ->today()
            ->get();
        if ($apointments) {
            $message = 'apointments return successfully';
        } else {
            $message = 'apointments return failed';
        }
        return ['message' => $message, 'apointments' => $apointments];
    }

}