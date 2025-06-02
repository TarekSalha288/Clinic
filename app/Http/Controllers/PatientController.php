<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChildRequest;
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
            return Response::Success($data['patient'], $data['message']);

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
            return Response::Success($data['son'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
}
