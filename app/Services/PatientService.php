<?php

namespace App\Services;

use App\Models\Apointment;
use App\Models\Doctor;
use App\Models\FavoritePost;
use App\Models\Patient;
use App\Models\Post;
use App\Models\Son;
use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\returnArgument;

class PatientService
{
    // some functions for formate the response
    private function addPatientInfo($array, $patient)
    {
        $patientInfo = [
            'id' => $patient->id,
            'gender' => $patient->gender,
            'age' => $patient->age,
            'birth_date' => $patient->birth_date,
        ];
        $array['patient_info'] = $patientInfo;
        return $array;
    }
    public function addFavToArticles($articles, $patient)
    {
        $formatedArticles = [];
        foreach ($articles as $article) {
            $flagFav = FavoritePost::where('post_id', $article->id)->where('patient_id', $patient->id)->get();
            if ($flagFav->isNotEmpty()) {
                $article['fav'] = true;
            } else {
                $article['fav'] = false;
            }
            $formatedArticles = $article;
        }
        return collect($formatedArticles);
    }
    // ____________________________________________
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
            'honest_score' => 100
        ]);
        if ($patient) {
            $message = 'patient profile added successfullt';
            $code = 200;
        } else {
            $message = 'patient profile not added to the system';
            $code = 400;
        }
        return ['message' => $message, 'patient' => $patient, 'code' => $code];
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
        ]);
        $son = Son::create([
            'patient_id' => $patient->id,
            'parent_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);
        $this->addPatientInfo($son, $patient);
        if ($patient && $son) {
            $message = 'son profile added successfullt';
        } else {
            $message = 'son profile not added to the system';
        }
        return ['message' => $message, 'son' => $son];
    }

    public function getArticles()
    {
        $patient = auth()->user()->patient;
        $articles = Post::paginate(5);
        $this->addFavToArticles($articles, $patient);
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
    public function updateApointment($request, $appointment_id)
    {
        $appointment = Apointment::find($appointment_id);
        $patient = auth()->user()->patient;
        $son_id = $request->son_id ?? null;
        $son = Son::find($son_id);
        if ($son && ($son->parent_id !== auth()->user()->id)) {
            $message = "patient don't have this son";
            $code = 404;
            return ['message' => $message, 'appointment' => null, 'code' => $code];
        }
        if ($appointment) {
            if ($appointment->status === "waiting") {
                $updateData = [];
                if ($request->has('apointment_date') && $request->apointment_date !== null) {
                    $updateData['apointment_date'] = $request->apointment_date;
                }
                if ($son && $son_id !== null) {
                    $updateData['patient_id'] = $son->patient_id;
                } else {
                    $updateData['patient_id'] = $patient->id;
                }
                $appointment->update($updateData);
                if ($appointment) {
                    $message = "apointment updated successfully";
                    $code = 200;
                } else {
                    $message = "apointment updated failed";
                    $code = 400;
                }
            } else {
                $message = "this appointment accepted you cant update it";
                $code = 400;
            }
        } else {
            $message = "appointment not found";
            $code = 404;
        }
        return ['message' => $message, 'appointment' => $appointment, 'code' => $code];
    }
    public function deleteAppointment($appointment_id)
    {
        $appointment = Apointment::find($appointment_id);
        if ($appointment) {
            $checkDelete = $appointment->delete();
            if ($checkDelete) {
                $message = "Appointment deleted successfully";
                $code = 200;
            } else {
                $message = "Appointment deleted failed";
                $code = 400;
            }
        } else {
            $message = "Appointment not found";
            $code = 404;
        }
        return ['message' => $message, 'appointment' => $appointment, 'code' => $code];
    }
    public function getAppointments()
    {
        $user = auth()->user();
        $patient = $user->patient;
        $sons = Son::where('parent_id', $user->id)->get();
        $formatedAppointments = [];
        $acceptedAppointmentsForPatient = Apointment::ofPatient($patient->id)->accepted()->get();
        $waitingAppointmentsForPatient = Apointment::ofPatient($patient->id)->waiting()->get();
        $formatedAppointments['accepted_patient'] = $acceptedAppointmentsForPatient ?? null;
        $formatedAppointments['waiting_patient'] = $waitingAppointmentsForPatient ?? null;
        $acceptedAppointmentsForPatientSon = [];
        $waitingAppointmentsForPatientSon = [];
        if ($sons) {
            foreach ($sons as $son) {
                $acceptedAppointmentsForPatientSon = Apointment::ofPatient($son->patient_id)->accepted()->get();
                $waitingAppointmentsForPatientSon = Apointment::ofPatient($son->patient_id)->waiting()->get();
            }
            $formatedAppointments['accepted_sons'] = $acceptedAppointmentsForPatientSon ?? null;
            $formatedAppointments['waiting_sons'] = $waitingAppointmentsForPatientSon ?? null;
        }
        if ($formatedAppointments) {
            $message = "appointemts return successfully";
        } else {
            $message = "appointemts return failed";
        }
        return ['message' => $message, 'appointments' => $formatedAppointments];
    }
    public function getChilds()
    {
        $sons = Son::where('parent_id', auth()->user()->id)->get();
        if ($sons) {
            $formatedSonArray = [];
            foreach ($sons as $son) {
                $patient = Patient::find($son->patient_id);
                $this->addPatientInfo($son, $patient);
                $formatedSonArray[] = $son;
            }
            $message = "sons return successfully";
            $code = 200;
        } else {
            $message = "this patient dont have any sons in the application";
            $code = 404;
        }
        return ['message' => $message, 'sons' => $formatedSonArray, 'code' => $code];
    }
    public function updateChild($request, $id)
    {
        $son = Son::find($id);
        if (!$son) {
            return ['message' => "son not found", 'son' => null, 'code' => 404];
        }
        $patientSonProfile = Patient::find($son->patient_id);
        if ($patientSonProfile) {
            $updatePatientResult = $patientSonProfile->update([
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'age' => $request->age,
                'blood_type' => $request->blood_type,
                'chronic_diseases' => $request->chronic_diseases,
                'medication_allergies' => $request->medication_allergies,
                'permanent_medications' => $request->permanent_medications,
                'previous_surgeries' => $request->previous_surgeries,
                'previous_illnesses' => $request->previous_illnesses,
            ]);
        }
        if ($son) {
            $updateSonResult = $son->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);
        }
        $this->addPatientInfo($son, $patientSonProfile);
        if ($updatePatientResult && $updateSonResult) {
            $message = "son profile update successfully";
            $code = 200;
        } else {
            $message = "son profile update faild";
            $code = 400;
        }
        return ['message' => $message, 'son' => $son, 'code' => $code];
    }
    public function deleteChild($id)
    {
        $son = Son::find($id);
        if ($son) {
            $deletedCheck = $son->delete();
            if ($deletedCheck) {
                $message = "Son deleted successfully";
                $code = 200;
            } else {
                $message = "Son deleted failed";
                $code = 400;
            }
        } else {
            $message = "Son not found";
            $code = 404;
        }
        return ['message' => $message, 'son' => $son, 'code' => $code];
    }
    public function getPreviews()
    {

    }
}

