<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChildRequest;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\PatientProfileRequest;
use App\Http\Responses\Response;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Throwable;
class PatientController extends Controller
{
    private PatientService $patientService;
    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
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
}
