<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostArticaleRequest;
use App\Http\Responses\Response;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Throwable;
use function PHPUnit\Framework\returnArgument;

class DoctorController extends Controller
{
    private DoctorService $doctorService;
    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function postArticale(PostArticaleRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->postArtical($request);
            return Response::Success($data['post'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
}
