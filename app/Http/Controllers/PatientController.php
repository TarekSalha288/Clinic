<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChildRequest;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\DoctorRateRequest;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\PatientProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\Response;
use App\ResponseJson;
use App\Services\PatientService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
class PatientController extends Controller
{
    use ResponseJson;
    private PatientService $patientService;
    private UserService $userService;
    public function __construct(PatientService $patientService, UserService $userService)
    {
        $this->patientService = $patientService;
        $this->userService = $userService;
    }
    public function postPatientProfile(PatientProfileRequest $request)
    {
        $data = [];
        try {
            $data = $this->patientService->postPatientInformation($request);
            return Response::Success($data['patient'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function addChild(AddChildRequest $request)
    {
        $data = [];
        try {
            $data = $this->patientService->addChild($request);
            return Response::Success($data['son'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getArticles()
    {
        $data = [];
        try {
            $data = $this->patientService->getArticles();
            return Response::Success($data['articles'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function addArticleFav($id)
    {
        $data = [];
        try {
            $data = $this->patientService->addArticleFav($id);
            return Response::Success($data['fav'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteArticleFav($id)
    {
        $data = [];
        try {
            $data = $this->patientService->deleteArticleFav($id);
            return Response::Success($data['fav'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getArticlesFav()
    {
        $data = [];
        try {
            $data = $this->patientService->getFavArticles();
            return Response::Success($data['fav'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function bookAppointment(BookAppointmentRequest $request, $doctor_id)
    {
        $data = [];
        try {
            $data = $this->patientService->bookAppointment($request, $doctor_id);
            return Response::Success($data['appointment'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updateAppointment(BookAppointmentRequest $request, $appointment_id)
    {
        $data = [];
        try {
            $data = $this->patientService->updateApointment($request, $appointment_id);
            return Response::Success($data['appointment'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteAppointment($appointment_id)
    {
        $data = [];
        try {
            $data = $this->patientService->deleteAppointment($appointment_id);
            return Response::Success($data['appointment'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getAppointments()
    {
        $data = [];
        try {
            $data = $this->patientService->getAppointments();
            return Response::Success($data['appointments'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getChilds()
    {
        $data = [];
        try {
            $data = $this->patientService->getChilds();
            return Response::Success($data['sons'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updateChild(AddChildRequest $request, $child_id)
    {
        $data = [];
        try {
            $data = $this->patientService->updateChild($request, $child_id);
            return Response::Success($data['son'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteChild($child_id)
    {
        $data = [];
        try {
            $data = $this->patientService->deleteChild($child_id);
            return Response::Success($data['son'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getPreviews()
    {
        $data = [];
        try {
            $data = $this->patientService->getPreviews();
            return Response::Success($data['previews'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updatePatientProfile(PatientProfileRequest $request)
    {
        $data = [];
        try {
            $data = $this->patientService->updatePatientProfile($request);
            return Response::Success($data['patient'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function uploadImagesForProfile(ImageUploadRequest $request)
    {
        $data = [];
        try {
            $data = $this->userService->uploadImage($request, 'Patient_Profile_Photo');
            return Response::Success($data['path'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updateProfileInfo(UpdateProfileRequest $request)
    {
        $data = [];
        try {
            $data = $this->patientService->updateProfileInfo($request);
            return Response::Success($data['patientInfo'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updatePassword(UpdateProfileRequest $request)
    {
        $data = [];
        try {
            $data = $this->patientService->updatePassword($request);
            return Response::Success($data['password'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function postMedicalAnalysis(FileUploadRequest $request, $preview_id)
    {
        $data = [];
        try {
            $data = $this->patientService->postMedicalAnalysis($request, $preview_id);
            return Response::Success($data['filePath'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getMedicalAnalysis($preview_id)
    {
        $data = [];
        try {
            $data = $this->patientService->getMedicalAnalysis($preview_id);
            return Response::Success($data['path'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteMedicalAnalysis($preview_id)
    {
        $data = [];
        try {
            $data = $this->patientService->deleteMedicalAnalysis($preview_id);
            return Response::Success($data['filePath'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function addDoctorRate(DoctorRateRequest $request, $doctor_id)
    {
        $data = [];
        try {
            $data = $this->patientService->addDoctorRate($request, $doctor_id);
            return Response::Success($data['rate'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updateDoctorRate(DoctorRateRequest $request, $doctor_id)
    {
        $data = [];
        try {
            $data = $this->patientService->updateDoctorRate($request, $doctor_id);
            return Response::Success($data['rate'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteDoctorRate($doctor_id)
    {
        $data = [];
        try {
            $data = $this->patientService->deleteDoctorRate($doctor_id);
            return Response::Success($data['rate'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getDoctorRate($doctor_id)
    {
        $data = [];
        try {
            $data = $this->patientService->getDoctorRate($doctor_id);
            return Response::Success($data['rate'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function searchDoctors()
    {
        $data = [];
        try {
            $data = $this->patientService->searchDoctors();
            return Response::Success($data['doctors'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function searchDepartments()
    {
        $data = [];
        try {
            $data = $this->patientService->searchDepartments();
            return Response::Success($data['departments'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getSymbtoms()
    {
        $data = [];
        try {
            $data = $this->patientService->getSymbtoms();
            return Response::Success($data['symbtoms'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function analyzeSymptoms()
    {
        $data = $this->patientService->analyseSymtoms();
        return match ($data['status']) {
            200 => $this->response($data['message'], $data['data'], 200),
            400 => $this->response($data['message'], null, 400),
            500 => $this->response($data['errors'], null, 500),
            default => $this->response("Unknown Error :", null, 522),
        };
    }

}
