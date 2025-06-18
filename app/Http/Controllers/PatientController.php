<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChildRequest;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\PatientProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\Response;
use App\Services\PatientService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
class PatientController extends Controller
{
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

}
