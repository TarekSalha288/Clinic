<?php

namespace App\Services;

use App\Mail\TwoFactorMail;
use App\Models\Apointment;
use App\Models\Doctor;
use App\Models\FavoritePost;
use App\Models\Patient;
use App\Models\Post;
use App\Models\Preview;
use App\Models\Rate;
use App\Models\Son;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\UploadImageTrait;
use App\Models\MedicalAnalysis;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\returnArgument;

class PatientService
{
    use UploadImageTrait;
    // some functions for formate the response
    private function addPatientInfo($array, $patient)
    {
        $patientInfo = [
            'id' => $patient->id,
            'age' => $patient->age,
            'gender' => $patient->gender,
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
    public function addInfoForAppointment($array, $patient, $user, $doctor, $son = null)
    {

        $doctorUser = User::find($doctor->user_id);
        if ($son)
            $sonPatient = Patient::find($son->patient_id);
        $appointmentInfo = [
            'imgPath' => $son == null ? $user->img_path : null,
            'patientName' => $son == null ? ($user->first_name . " " . $user->last_name) : ($son->first_name . " " . $son->last_name),
            'doctorName' => $doctorUser->first_name . " " . $doctorUser->last_name,
            'gender' => $son == null ? $patient->gender : $sonPatient->gender
        ];
        $array['appointment_info'] = $appointmentInfo;
        return $array;
    }
    // ____________________________________________
    public function postPatientInformation($request)
    {
        $user_id = auth()->user()->id;
        $patient = Patient::create([
            'user_id' => $user_id,
            'birth_date' => $request->birth_date,
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_type' => Patient::encryptField($request->blood_type),
            'chronic_diseases' => Patient::encryptField($request->chronic_diseases),
            'medication_allergies' => Patient::encryptField($request->medication_allergies),
            'permanent_medications' => Patient::encryptField($request->permanent_medications),
            'previous_surgeries' => Patient::encryptField($request->previous_surgeries),
            'previous_illnesses' => Patient::encryptField($request->previous_illnesses),
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
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_type' => Patient::encryptField($request->blood_type),
            'chronic_diseases' => Patient::encryptField($request->chronic_diseases),
            'medication_allergies' => Patient::encryptField($request->medication_allergies),
            'permanent_medications' => Patient::encryptField($request->permanent_medications),
            'previous_surgeries' => Patient::encryptField($request->previous_surgeries),
            'previous_illnesses' => Patient::encryptField($request->previous_illnesses),
        ]);
        $son = Son::create([
            'patient_id' => $patient->id,
            'parent_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);
        $this->addPatientInfo($son, $patient);
        if ($patient && $son) {
            $code = 200;
            $message = 'son profile added successfullt';
        } else {
            $message = 'son profile not added to the system';
            $code = 400;
        }
        return ['message' => $message, 'son' => $son, 'code' => $code];
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
        $articleFavFound = FavoritePost::where('post_id', $id)->where('patient_id', $patient->id)->first();
        if ($articleFavFound) {
            return ['fav' => $article, 'message' => 'article already in fav', 'code' => 200];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['fav' => null, 'message' => $message, 'code' => $code];
        }
        if ($article) {
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
            $message = "article not found";
            $code = 404;
        }
        return ['fav' => $article, 'message' => $message, 'code' => $code];
    }
    public function deleteArticleFav($id)
    {
        $article = Post::find($id);
        $patient = auth()->user()->patient;
        $favArticle = $patient->favoritePost($id);
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['fav' => null, 'message' => $message, 'code' => $code];
        }
        if (!$article) {
            $message = "article not found";
            $code = 404;
            return ['fav' => null, 'message' => $message, 'code' => $code];
        }
        if ($favArticle) {
            $deleteFav = $favArticle->delete();
            if ($deleteFav) {
                $message = "article deleted from favorite successfully";
                $code = 200;
            } else {
                $message = "article deleted from favorite failed";
                $code = 400;
            }
        } else {
            $message = "article is not in the favorite to delete";
            $code = 404;
        }
        return ['fav' => $article, 'message' => $message, 'code' => $code];
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
        $user = auth()->user();
        $patient = $user->patient;
        $son_id = $request->son_id ?? null;
        $son = Son::find($son_id);
        if ($son && ($son->parent_id !== auth()->user()->id)) {
            $message = "patient don't have this son";
            $code = 404;
            return ['message' => $message, 'appointment' => null, 'code' => $code];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['appointment' => null, 'message' => $message, 'code' => $code];
        }
        if ($doctor) {
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
                $this->addInfoForAppointment($appointment, $patient, $user, $doctor, $son);
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
            $message = "doctor not found";
        }
        return ['message' => $message, 'appointment' => $appointment ?? null, 'code' => $code];
    }
    public function updateApointment($request, $appointment_id)
    {
        $appointment = Apointment::find($appointment_id);
        $user = auth()->user();
        $patient = $user->patient;
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
                $doctor = Doctor::find($appointment->doctor_id);
                $this->addInfoForAppointment($appointment, $patient, $user, $doctor, $son);

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
        foreach ($acceptedAppointmentsForPatient as $acceptedAppointment) {
            $patient = Patient::find($acceptedAppointment['patient_id']);
            $user = User::find($patient['user_id']);
            $doctor = Doctor::find($acceptedAppointment['doctor_id']);
            $this->addInfoForAppointment($acceptedAppointment, $patient, $user, $doctor);
        }
        $waitingAppointmentsForPatient = Apointment::ofPatient($patient->id)->waiting()->get();
        foreach ($waitingAppointmentsForPatient as $waitingAppointment) {
            $patient = Patient::find($waitingAppointment['patient_id']);
            $user = User::find($patient['user_id']);
            $doctor = Doctor::find($waitingAppointment['doctor_id']);
            $this->addInfoForAppointment($waitingAppointment, $patient, $user, $doctor);
        }
        $formatedAppointments['accepted_patient'] = $acceptedAppointmentsForPatient ?? null;
        $formatedAppointments['waiting_patient'] = $waitingAppointmentsForPatient ?? null;
        $acceptedAppointmentsForPatientSon = [];
        $waitingAppointmentsForPatientSon = [];
        if ($sons) {
            foreach ($sons as $son) {
                $acceptedAppointment = Apointment::ofPatient($son->patient_id)->accepted()->get();
                foreach ($acceptedAppointment as $accepted) {
                    $patient = Patient::find($accepted['patient_id']);
                    $user = User::find($patient['user_id']);
                    $doctor = Doctor::find($accepted['doctor_id']);
                    $son = Son::where('patient_id', $patient->id)->first();
                    $this->addInfoForAppointment($accepted, $patient, $user, $doctor, $son);
                }
                $acceptedAppointmentsForPatientSon[] = $acceptedAppointment;

                $waitingAppointment = Apointment::ofPatient($son->patient_id)->waiting()->get();
                foreach ($waitingAppointment as $waiting) {
                    $patient = Patient::find($waiting['patient_id']);
                    $user = User::find($patient['user_id']);
                    $doctor = Doctor::find($waiting['doctor_id']);
                    $son = Son::where('patient_id', $patient->id)->first();
                    $this->addInfoForAppointment($waiting, $patient, $user, $doctor, $son);
                }
                $waitingAppointmentsForPatientSon[] = $waitingAppointment;
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
                'age' => $request->age,
                'gender' => Patient::encryptField($request->gender),
                'blood_type' => Patient::encryptField($request->blood_type),
                'chronic_diseases' => Patient::encryptField($request->chronic_diseases),
                'medication_allergies' => Patient::encryptField($request->medication_allergies),
                'permanent_medications' => Patient::encryptField($request->permanent_medications),
                'previous_surgeries' => Patient::encryptField($request->previous_surgeries),
                'previous_illnesses' => Patient::encryptField($request->previous_illnesses),
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
        $patient = auth()->user()->patient;
        if (!$patient) {
            return ['message' => 'patient not found', 'previews' => null, 'code' => 404];
        }
        $completePreviews = Preview::forPatient($patient->id)
            ->diagnoseisType(1)
            ->get();
        $partlyPreviews = Preview::forPatient($patient->id)
            ->diagnoseisType(0)
            ->get();
        if ($completePreviews || $partlyPreviews) {
            $formatedPreviews = [];
            $formatedPreviews['completePreviews'] = $completePreviews;
            $formatedPreviews['partlyPreviews'] = $partlyPreviews;
            $message = "preview return successfully";
            $code = 200;
        } else {
            $message = "no previews yet";
            $message = 400;
        }
        return ['message' => $message, 'previews' => $formatedPreviews, 'code' => $code];
    }
    public function updatePatientProfile($request)
    {
        $patient = auth()->user()->patient;
        if (!$patient) {
            return ['message' => 'patient not found', 'patient' => null, 'code' => 404];
        }
        $updatedStatus = $patient->update([
            'birth_date' => $request->birth_date,
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_type' => Patient::encryptField($request->blood_type),
            'chronic_diseases' => Patient::encryptField($request->chronic_diseases),
            'medication_allergies' => Patient::encryptField($request->medication_allergies),
            'permanent_medications' => Patient::encryptField($request->permanent_medications),
            'previous_surgeries' => Patient::encryptField($request->previous_surgeries),
            'previous_illnesses' => Patient::encryptField($request->previous_illnesses),
            'honest_score' => 100
        ]);
        if ($updatedStatus) {
            $message = "Patient profile updated successfully";
            $code = 200;
        } else {
            $message = "!somthing went wrong the patient information not updated";
            $code = 400;
        }
        return ['message' => $message, 'patient' => $patient, 'code' => $code];
    }
    public function updateProfileInfo($request)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'patientInfo' => null, 'code' => $code];
        }
        $old_email = $user->email;
        $updateStatus = $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        if ($updateStatus) {
            if ($request->email !== $old_email) {
                $user->generateCode();
                Mail::to($user->email)->send(new TwoFactorMail($user->code, $user->first_name));
            }
            $message = "profile updated successfully";
            $code = 200;
        } else {
            $message = "profile updated failed";
            $code = 400;
        }
        return ['message' => $message, 'patientInfo' => $user, 'code' => $code];
    }
    public function updatePassword($request)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'password' => null, 'code' => $code];
        }
        $updateStatus = $user->update([
            'password' => $request->password
        ]);
        if ($updateStatus) {
            $message = "password updated successfully";
            $code = 200;
        } else {
            $message = "password updated failed";
            $code = 400;
        }
        return ['message' => $message, 'password' => $user, 'code' => $code];
    }
    public function postMedicalAnalysis($request, $preview_id)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'filePath' => null, 'code' => $code];
        }
        $preview = Preview::find($preview_id);
        $patient = $user->patient;
        $medical_analysis = MedicalAnalysis::where('patient_id', $patient->id)->where('preview_id', $preview_id)->first();
        if ($medical_analysis) {
            $path = $medical_analysis->medical_analysis_path;
            $storagePath = str_replace('/storage/', '', $path);
            if (Storage::disk('public')->exists($storagePath))
                Storage::disk('public')->delete($storagePath);
            $medical_analysis->delete();
        }
        if ($preview->diagnoseis_type !== 0) {
            $message = "diagnoseis type for this preview is completed you can't add a medical analysis";
            $code = 400;
            return ['message' => $message, 'filePath' => null, 'code' => $code];
        }
        $patient = auth()->user()->patient;
        $user_id = $user->id;
        $url = $this->ImageUpload($request, $user_id, 'Medical_analysis', 'file');
        if ($url) {
            MedicalAnalysis::create([
                'patient_id' => $patient->id,
                'preview_id' => $preview_id,
                'medical_analysis_path' => $url
            ]);
            $message = "medical analysis uploaded successfully";
            $code = 200;
        } else {
            $message = 'there is no file to upload';
            $code = 400;
        }
        return ['message' => $message, 'filePath' => $url, 'code' => $code];
    }
    public function getMedicalAnalysis($preview_id)
    {
        $user = auth()->user();
        if ($user) {
            $preview = Preview::find($preview_id);
            if ($preview->diagnoseis_type !== 0) {
                $message = "diagnoseis type for this preview is completed you can't add a medical analysis";
                $code = 400;
                return ['message' => $message, 'Path' => null, 'code' => $code];
            }
            $patient = $user->patient;
            $medical_analysis = MedicalAnalysis::where('patient_id', $patient->id)->where('preview_id', $preview_id)->first();
            if ($medical_analysis) {
                $path = $medical_analysis->medical_analysis_path;
                if ($path) {
                    $message = 'file uploaded successfully';
                    $code = 200;
                } else {
                    $message = 'you dont uploaded file yet';
                    $code = 400;
                }
            } else {
                $message = "medical_analysis not found";
                $code = 404;
            }
        } else {
            $message = 'user not found';
            $code = 404;
        }
        return ['message' => $message, 'path' => $path, 'code' => $code];
    }
    public function deleteMedicalAnalysis($preview_id)
    {
        $user = auth()->user();
        if (!$user) {
            $message = "user not found";
            $code = 404;
            return ['message' => $message, 'filePath' => null, 'code' => $code];
        }
        $preview = Preview::find($preview_id);
        if ($preview->diagnoseis_type !== 0) {
            $message = "diagnoseis type for this preview is completed you can't add a medical analysis";
            $code = 400;
            return ['message' => $message, 'filePath' => null, 'code' => $code];
        }
        $patient = $user->patient;
        $medical_analysis = MedicalAnalysis::where('patient_id', $patient->id)->where('preview_id', $preview_id)->first();
        if ($medical_analysis) {
            $path = $medical_analysis->medical_analysis_path;
            $storagePath = str_replace('/storage/', '', $path);
            if (Storage::disk('public')->exists($storagePath))
                Storage::disk('public')->delete($storagePath);
            $medical_analysis->delete();
            $message = "medical analysis deleted succussfully";
            $code = 200;
        } else {
            $message = "there is no medical analysis for this preview yet";
            $code = 400;
        }
        return ['message' => $message, 'filePath' => $path, 'code' => $code];
    }
    public function addDoctorRate($request, $doctor_id)
    {
        $patient = auth()->user()->patient;
        $doctor = Doctor::find($doctor_id);
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        $preview = Preview::ofPatientAndDoctor($patient->id, $doctor_id)->first();
        if ($preview) {
            $rate = Rate::ofPatientAndDoctor($patient->id, $doctor_id)->first();
            if ($rate) {
                $message = "you rated this doctor before";
                $code = 400;
                return ['rate' => $rate, 'message' => $message, 'code' => $code];
            }
            $rate = Rate::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor_id,
                'rate' => $request->rate
            ]);
            if ($rate) {
                $message = "doctor rated successfully";
                $code = 200;
            } else {
                $message = "doctor rated failed";
                $code = 400;
            }
        } else {
            $message = "you dont have any preview for this doctor so you cant rated";
            $code = 400;
            $rate = null;
        }
        return ['message' => $message, 'rate' => $rate, 'code' => $code];
    }
    public function updateDoctorRate($request, $doctor_id)
    {
        $patient = auth()->user()->patient;
        $doctor = Doctor::find($doctor_id);
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        $rate = Rate::ofPatientAndDoctor($patient->id, $doctor_id)->first();
        if ($rate) {
            $updateRate = $rate->update([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor_id,
                'rate' => $request->rate
            ]);
            if ($updateRate) {
                $message = "rate updated successfully";
                $code = 200;
            } else {
                $message = "rate updated failed";
                $code = 400;
            }
        } else {
            $message = "rate not found";
            $code = 404;
        }
        return ['rate' => $rate, 'message' => $message, 'code' => $code];
    }
    public function deleteDoctorRate($doctor_id)
    {
        $patient = auth()->user()->patient;
        $doctor = Doctor::find($doctor_id);
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        $rate = Rate::ofPatientAndDoctor($patient->id, $doctor_id)->first();
        if ($rate) {
            $deleteRate = $rate->delete();
            if ($deleteRate) {
                $message = "rate deleted successfully";
                $code = 200;
            } else {
                $message = "rate deleted failed";
                $code = 400;
            }
        } else {
            $message = "rate not found";
            $code = 404;
        }
        return ['rate' => $rate, 'message' => $message, 'code' => $code];
    }
    public function getDoctorRate($doctor_id)
    {
        $patient = auth()->user()->patient;
        $doctor = Doctor::find($doctor_id);
        if (!$doctor) {
            $message = "doctor not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        if (!$patient) {
            $message = "patient not found";
            $code = 404;
            return ['rate' => null, 'message' => $message, 'code' => $code];
        }
        $rate = Rate::ofPatientAndDoctor($patient->id, $doctor_id)->first();
        if ($rate) {
            $message = "rate return successfully";
            $code = 200;
        } else {
            $message = "rate not found";
            $code = 404;
        }
        return ['rate' => $rate, 'message' => $message, 'code' => $code];
    }


public function analyseSymtoms() {
    try {
        $symptoms = request()->input('symptoms', []);

        if (empty($symptoms)) {
            return [
                'status' => 400,
                'message' => 'No symptoms provided.',
            ];
        }
        $identifiedDepartments = [];
        foreach ($symptoms as $symptom) {
            $cleanedSymptom = $symptom;
            $matchedDepartment = null;
            if (in_array($cleanedSymptom, ['chest pain', 'shortness of breath', 'palpitations', 'swelling in legs', 'fatigue', 'rapid heartbeat', 'fainting', 'high blood pressure'])) {
                $matchedDepartment = 'Cardiology';
            } elseif (in_array($cleanedSymptom, ['headaches', 'dizziness', 'numbness', 'muscle weakness', 'seizures', 'memory loss', 'tingling', 'loss of consciousness', 'tremors', 'vision problems'])) {
                $matchedDepartment = 'Neurology';
            } elseif (in_array($cleanedSymptom, ['abdominal pain', 'nausea', 'diarrhea', 'constipation', 'heartburn', 'bloating', 'vomiting', 'blood in stool', 'loss of appetite', 'difficulty swallowing'])) {
                $matchedDepartment = 'Gastroenterology';
            } elseif (in_array($cleanedSymptom, ['chronic cough', 'wheezing', 'shortness of breath', 'chest tightness', 'frequent respiratory infections', 'coughing up blood', 'snoring', 'hoarseness'])) {
                $matchedDepartment = 'Pulmonology';
            } elseif (in_array($cleanedSymptom, ['joint pain', 'swelling of joints', 'limited range of motion', 'muscle pain', 'fractures', 'back pain', 'neck pain', 'bone pain', 'stiffness'])) {
                $matchedDepartment = 'Orthopedics';
            } elseif (in_array($cleanedSymptom, ['fever', 'rash', 'ear infection', 'sore throat', 'growth delays', 'behavioral changes', 'poor appetite', 'developmental delays', 'bedwetting'])) {
                $matchedDepartment = 'Pediatrics';
            } elseif (in_array($cleanedSymptom, ['skin rash', 'itching', 'acne', 'eczema', 'psoriasis', 'skin lesions', 'hair loss', 'nail changes', 'skin discoloration'])) {
                $matchedDepartment = 'Dermatology';
            } elseif (in_array($cleanedSymptom, ['frequent urination', 'painful urination', 'blood in urine', 'kidney stones', 'urinary incontinence', 'urinary retention', 'testicular pain'])) {
                $matchedDepartment = 'Urology';
            }
            if ($matchedDepartment === null) {
                return [
                    'status' => 400,
                    'message' => "Our system can't categorize the symptom '{$symptom}'. Please consult a general practitioner.",
                ];
            }
            $identifiedDepartments[] = $matchedDepartment;
        }
        $uniqueDepartments = array_unique($identifiedDepartments);
        if (count($uniqueDepartments) > 1) {
            return [
                'status' => 400,
                'message' => "Your symptoms suggest multiple departments. Please consult a general practitioner for a comprehensive diagnosis.",
            ];
        }

        $finalDepartment = !empty($uniqueDepartments) ? $uniqueDepartments[0] : null;
        if ($finalDepartment) {
            return [
                'status' => 200,
                'message' => "Based on your symptoms, you should go to the **{$finalDepartment}** department.",
                'data' => [
                    'suggested_department' => $finalDepartment,
                    'symptoms_provided' => $symptoms
                ]
            ];
        } else {
            return [
                'status' => 400,
                'message' => "No clear department could be identified based on your input.",
            ];
        }
    } catch (\Exception $e) {

        return [
            'status' => 500,
            'errors' => 'An internal server error occurred: ' . $e->getMessage()
        ];
    }
}
}
