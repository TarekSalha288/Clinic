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
    // some functions for formate the response
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
    //______________________________________
    public function postArtical($request)
    {
        $doctor_id = auth()->user()->doctor->id;
        if (!$doctor_id) {
            $message = "doctor not found";
            $code = 404;
            return ['message' => $message, 'post' => null, 'code' => $code];
        }
        $post = Post::query()->create([
            'doctor_id' => $doctor_id,
            'title' => $request->title,
            'body' => $request->body
        ]);
        if ($post) {
            $message = 'articale created successfully';
            $code = 200;
        } else {
            $message = 'somthing went wrong the post not created';
            $code = 400;
        }
        return ['message' => $message, 'post' => $post, 'code' => $code];
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
            $code = 200;
        } else {
            $message = "user not found";
            $code = 404;
        }
        return ['message' => $message, 'user' => $user, 'code' => $code];
    }
    public function updateArticle($request, $id)
    {
        $article = Post::find($id);
        if ($article) {
            $oldbody = $article->body;
            $newbody = $request->body;
            $updateResult = $article->update([
                'title' => $request->title,
                'body' => $request->body
            ]);
            if ($updateResult) {
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
                $message = 'article updated successfully';
                $code = 200;
            } else {
                $message = 'article updated failed';
                $code = 400;
            }
        } else {
            $message = "article not found";
            $code = 404;
        }
        return ['article' => $article, 'message' => $message, 'code' => $code];
    }
    public function deleteArticle($id)
    {
        $article = Post::find($id);
        if ($article) {
            $body = $article->body;
            $deleteResult = $article->delete();
            if ($deleteResult) {
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
                $code = 200;
            } else {
                $message = 'article deleted failed';
                $code = 400;
            }
        } else {
            $message = "article not found";
            $code = 404;
        }
        return ['article' => $article, 'message' => $message, 'code' => $code];
    }
    public function getArticles()
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'articles' => null, 'code' => $code];
        }
        $doctor = $user->doctor;
        $articles = $doctor->posts()->paginate(5);
        if ($articles) {
            $articles = $this->addDoctorInfo($articles, $doctor, $user);
            $message = 'Articles return successfully';
            $code = 200;
        } else {
            $message = "Articles return failed";
            $code = 400;
        }
        return ['articles' => $articles, 'message' => $message, 'code' => $code];
    }
    public function getArticleById($id)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'article' => null, 'code' => $code];
        }
        $doctor = $user->doctor;
        $article = $doctor->posts()->where('id', $id)->first();
        if ($article) {
            $article = $this->addDoctorInfo($article, $doctor, $user);
            $message = "article return successfully";
            $code = 200;
        } else {
            $message = "article not found";
            $code = 404;
        }
        return ['article' => $article, 'message' => $message, 'code' => $code];
    }
    public function getApointments()
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'apointments' => null, 'code' => $code];
        }
        $doctor = $user->doctor;
        $apointments = Apointment::forDoctor($doctor->id)
            ->accepted()
            ->today()
            ->get();
        // if it dont work because the date in the database not today
        if ($apointments) {
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
            $message = 'apointments return successfully';
            $code = 200;
        } else {
            $message = 'apointments not found';
            $code = 404;
        }
        return ['message' => $message, 'apointments' => $formatedApointments, 'code' => $code];
    }
    public function postPreview($request, $patient_id)
    {
        $doctor = auth()->user()->doctor;
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['message' => $message, 'preview' => null, 'code' => $code];
        }
        $preview = Preview::create([
            'patient_id' => $patient_id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'diagnoseis' => Patient::encryptField($request->diagnoseis),
            'diagnoseis_type' => $request->diagnoseis_type,
            'medicine' => Patient::encryptField($request->medicine),
            'notes' => Patient::encryptField($request->notes),
            'date' => Carbon::today(),
            'status' => Patient::encryptField($request->status)
        ]);
        if ($preview) {
            $message = "preview added successfully";
            $code = 200;
        } else {
            $message = "preview added failed";
            $code = 400;
        }
        return ['preview' => $preview, 'message' => $message, 'code' => $code];
    }
    public function updatePreview($request, $preview_id)
    {
        $preview = Preview::find($preview_id);
        if ($preview) {
            $updatePreview = $preview->update([
                'diagnoseis' => Patient::encryptField($request->diagnoseis),
                'diagnoseis_type' => $request->diagnoseis_type,
                'medicine' => Patient::encryptField($request->medicine),
                'notes' => Patient::encryptField($request->notes),
                'status' => Patient::encryptField($request->status)
            ]);
            $preview->save();
            if ($updatePreview) {
                $message = "preview updated successfully";
                $code = 200;
            } else {
                $message = "preview updated failed";
                $code = 400;
            }
        } else {
            $message = "preview not found";
            $code = 404;
        }
        return ['message' => $message, 'preview' => $preview, 'code' => $code];
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
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['message' => $message, 'previews' => null, 'code' => $code];
        }
        $previews = Preview::where('doctor_id', $doctor->id)->get();
        if ($previews) {
            $message = "previews return successfully";
            $code = 200;
        } else {
            $message = "previews return failed";
            $code = 400;
        }

        return ['message' => $message, 'previews' => $previews, 'code' => $code];
    }
}