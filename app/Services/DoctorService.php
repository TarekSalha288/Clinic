<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Post;
use App\Models\Preview;
use App\Models\Rate;
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
    private function addDoctorInfo($array, $doctor, $user)
    {
        $doctorInfo = [
            'id' => $doctor->id,
            'user_id' => $doctor->user_id,
            'department_id' => $doctor->department_id,
            'bio' => $doctor->bio,
            'created_at' => $doctor->created_at,
            'updated_at' => $doctor->updated_at,
            'department_name' => Department::find($doctor->department_id)->name ?? 'un known',
            'name' => $user->first_name . " " . $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'img_path' => $user->img_path,
            'rates' => Rate::where('doctor_id', $doctor->id)->first()->rate ?? 0
        ];
        $array['doctor_info'] = $doctorInfo;
        return $array;
    }
    public function postArtical($request)
    {
        $doctor_id = auth()->user()->doctor->id;
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
        $user = auth()->user();
        $user_id = $user->id;

        $url = $this->ImageUpload($request, $user_id, $folderName);
        if ($url) {
            $message = "image uploaded successfully";
            if ($folderName === "Profile_Photo") {
                $user->img_path = $url;
                $user->save();
            }
        } else {
            $message = 'there is no file to upload';
        }

        return ['message' => $message, 'path' => $url];
    }
    public function getProfileImage()
    {
        $user = auth()->user();
        if ($user) {
            $path = $user->img_path;
            if ($path) {
                $message = 'image uploaded successfully';
            } else {
                $message = 'you dont uploaded image yet';
            }
        } else {
            $message = 'user not found';
        }
        return ['message' => $message, 'path' => $path];
    }
    public function deleteProfileImage()
    {
        $user = auth()->user();
        $img_path = $user->img_path;
        if ($img_path) {
            $storagePath = str_replace('/storage/', '', $img_path);
            if (Storage::disk('public')->exists($storagePath))
                Storage::disk('public')->delete($storagePath);
            $user->img_path = null;
            $user->save();
            $message = "image deleted succussfully";
        } else {
            $message = 'image deleted failed';
        }
        return ['message' => $message, 'path' => $img_path];
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
    public function updateArticle($request, $id)
    {
        $article = Post::find($id);
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
        $user = auth()->user();
        $doctor = $user->doctor;
        $articles = $doctor->posts;
        if ($articles) {
            $articles = $this->addDoctorInfo($articles, $doctor, $user);
            $message = 'Articles return successfully';
        } else {
            $message = "Articles failed";
        }
        return ['articles' => $articles, 'message' => $message];
    }
    public function getArticleById($id)
    {
        $doctor = auth()->user()->doctor;
        $user = auth()->user();
        $article = $doctor->posts()->where('id', $id)->first();
        if ($article) {
            $article = $this->addDoctorInfo($article, $doctor, $user);
            $message = "article return successfully";
        } else {
            $message = "article return failed";
        }
        return ['article' => $article, 'message' => $message];
    }

    public function getApointments()
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        $apointments = Apointment::forDoctor($doctor->id)
            ->accepted()
            ->today()
            ->get();
        // if it dont work because the date in the database not today
        $formatedApointments = [];

        foreach ($apointments as $apointment) {
            $patient_info = User::find(Patient::where('id', $apointment->patient_id)->first()->user_id);
            $apointment = [
                'id' => $apointment->id,
                'patient_name' => $patient_info->first_name . " " . $patient_info->last_name,
                'patient_phone' => $patient_info->phone,
                'patient_photo' => $patient_info->img_path,
                'apointment_date' => $apointment->apointment_date,
                'apointment_status' => $apointment->apointment_status,
                'status' => $apointment->status,
            ];
            $formatedApointments[] = $apointment;

        }
        $formatedApointments = $this->addDoctorInfo($formatedApointments, $doctor, $user);

        if ($apointments->isNotEmpty()) {
            $message = 'apointments return successfully';
        } else {
            $message = 'apointments return failed';
        }
        return ['message' => $message, 'apointments' => $formatedApointments];
    }
    public function postPreview($request, $patient_id)
    {
        $doctor = auth()->user()->doctor;
        $preview = Preview::create([
            'patient_id' => $patient_id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'diagnoseis' => $request->diagnoseis,
            'diagnoseis_type' => $request->diagnoseis_type,
            'medicine' => $request->medicine,
            'notes' => $request->notes,
            'date' => Carbon::today(),
            'status' => $request->status
        ]);
        if ($preview) {
            $message = "preview added successfully";
        } else {
            $message = "preview added failed";
        }
        return ['preview' => $preview, 'message' => $message];
    }
    public function updatePreview($request, $preview_id)
    {
        $preview = Preview::find($preview_id);
        if ($preview) {
            $updatePreview = $preview->update([
                'diagnoseis' => $request->diagnoseis,
                'diagnoseis_type' => $request->diagnoseis_type,
                'medicine' => $request->medicine,
                'notes' => $request->notes,
                'status' => $request->status
            ]);
            $preview->save();
            if ($updatePreview) {
                $message = "preview updated successfully";
            } else {
                $message = "preview updated failed";
            }
        } else {
            $message = "preview not found";
        }
        return ['message' => $message, 'preview' => $preview];
    }
    public function deletePreview($preview_id)
    {
        $preview = Preview::find($preview_id);
        if ($preview) {
            $deletedPreview = $preview->delete();
            if ($deletedPreview) {
                $code = 200;
                $message = "preview deleted successfully";
            } else {
                $code = 400;
                $message = "preview deleted failed";
            }
        } else {
            $code = 404;
            $message = "preview not found";
        }
        return ['message' => $message, 'preview' => $preview, 'code' => $code];
    }
    public function getPreviews()
    {
        $doctor = auth()->user()->doctor;
        $previews = Preview::where('doctor_id', $doctor->id)->get();
        if ($previews) {
            $message = "previews return successfully";
        } else {
            $message = "previews return failed";
        }
        return ['message' => $message, 'previews' => $previews];
    }
}