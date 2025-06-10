<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Doctor;
use App\Models\FavoritePost;
use App\Models\Patient;
use App\Models\Post;
use App\Models\Son;
use function PHPUnit\Framework\isNull;

class PatientService
{
    public function postPatientInformation($request)
    {
        $user_id = auth()->user()->id;
        $patient = Patient::create([
            'user_id' => $user_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_type' => $request->blood_type,
            'chronic_diseases' => $request->chronic_diseases,
            'medication_allergies' => $request->medication_allergies,
            'permanent_medications' => $request->permanent_medications,
            'previous_surgeries' => $request->previous_surgeries,
            'previous_illnesses' => $request->previous_illnesses,
            'medical_analysis' => $request->medical_analysis,
            'honest_score' => 100
        ]);
        if ($patient) {
            $message = 'patient profile added successfullt';
        } else {
            $message = 'patient profile not added to the system';
        }
        return ['message' => $message, 'patient' => $patient];
    }
    public function addChild($request)
    {
        $user_id = auth()->user()->id;
        $patient = Patient::create([
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_type' => $request->blood_type,
            'chronic_diseases' => $request->chronic_diseases,
            'medication_allergies' => $request->medication_allergies,
            'permanent_medications' => $request->permanent_medications,
            'previous_surgeries' => $request->previous_surgeries,
            'previous_illnesses' => $request->previous_illnesses,
            'medical_analysis' => $request->medical_analysis,
        ]);
        $son = Son::create([
            'patient_id' => $patient->id,
            'parent_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);
        if ($patient && $son) {
            $message = 'son profile added successfullt';
        } else {
            $message = 'son profile not added to the system';
        }
        return ['message' => $message, 'son' => $son];
    }

    public function getArticles()
    {
        $articles = Post::all();
        if ($articles) {
            $message = "articles return successfully";
        } else {
            $message = "articles return failed";
        }
        return ['message' => $message, 'articles' => $articles];
    }
    public function addArticleFav($id)
    {
        $article = Post::find($id);
        $patient = auth()->user()->patient;
        if ($article && $patient) {
            $addFav = FavoritePost::create([
                'patient_id' => $patient->id,
                'post_id' => $id
            ]);
            if ($addFav) {
                $message = "article added to favorite successfully";
                $code = 200;
            } else {
                $message = "article added to favorite failed";
                $code = 400;
            }
        } else {
            $message = "article or patient not found";
            $code = 404;
        }
        return ['fav' => $article, 'message' => $message, 'code' => $code];
    }
    public function deleteArticleFav($id)
    {
        $patient = auth()->user()->patient;
        $favArticle = $patient->favoritePosts()->where('post_id', $id);
        if ($favArticle && $patient) {
            $deleteFav = $favArticle->delete();
            if ($deleteFav) {
                $message = "article deleted from favorite successfully";
                $code = 200;
            } else {
                $message = "article deleted from favorite failed";
                $code = 400;
            }
        } else {
            $message = "article or patient not found";
            $code = 404;
        }
        return ['fav' => $favArticle, 'message' => $message, 'code' => $code];
    }
    public function getFavArticles()
    {
        $patient = auth()->user()->patient;
        $favArticles = $patient->favoritePosts()->get();
        if ($favArticles) {
            $formatedArticles = [];
            foreach ($favArticles as $favArticle) {
                $formatedArticles[] = Post::find($favArticle->post_id);
            }
            $message = "favorite articles return successfully";
            $code = 200;
        } else {
            $message = "no favorite articles";
            $code = 404;
        }
        return ['fav' => $formatedArticles, 'message' => $message, 'code' => $code];
    }
    public function bookAppointment($request, $doctor_id)
    {
        $doctor = Doctor::find($doctor_id);
        $patient = auth()->user()->patient;
        $son_id = $request->son_id ?? null;
        $son = Son::find($son_id);
        if ($son && ($son->parent_id !== auth()->user()->id)) {
            $message = "patient don't have this son";
            $code = 404;
            return ['message' => $message, 'appointment' => null, 'code' => $code];
        }
        if ($doctor && $patient) {
            $department = $doctor->department;
            if ($department) {
                $appointment = Apointment::create([
                    'patient_id' => $son_id == null ? $patient->id : $son->patient_id,
                    'doctor_id' => $doctor_id,
                    'department_id' => $department->id,
                    'apointment_date' => $request->appointment_date,
                    'apoitment_status' => "app",
                    'status' => "waiting"
                ]);
            } else {
                $code = 404;
                $message = "department not found";
            }
            if ($appointment) {
                $code = 200;
                $message = "appointment created successfully";
            } else {
                $code = 400;
                $message = "appointment created failed";
            }
        } else {
            $code = 404;
            $message = "doctor or patient not found";
        }
        return ['message' => $message, 'appointment' => $appointment ?? null, 'code' => $code];
    }
}

