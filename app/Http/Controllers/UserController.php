<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\Response;
use App\ResponseJson;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
class UserController extends Controller
{
    use ResponseJson;
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function getDoctor($id)
    {
        $data = $this->userService->getDoctor($id);
        return match ($data['status']) {
            200 => $this->response($data['message'], ['doctor' => $data['data']], 200),
            404 => $this->response($data['message'], null, 404),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDoctors()
    {
        $data = $this->userService->getDoctors();
        return match ($data['status']) {
            200 => $this->response("That is all doctors", ['doctors' => $data['data']], 200),
            404 => $this->response("No doctors found", null, 404),
            422 => $this->response("Validation error: missing search query", null, 422),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDepartment($id)
    {
        $data = $this->userService->getDepartment($id);
        return match ($data['status']) {
            200 => $this->response("That is  department", ['department' => $data['data']], 200),
            404 => $this->response("No department found", null, 404),
            422 => $this->response("Validation error: missing search query", null, 422),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDepartments()
    {
        $data = $this->userService->getDepartments();
        return match ($data['status']) {
            200 => $this->response("That is all departments", ['departments' => $data['data']], 200),
            404 => $this->response("No departments found", null, 404),
            422 => $this->response("Validation error: missing search query", null, 422),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getLeaves($doctorId)
    {
        $data = $this->userService->getLeaves($doctorId);
        return match ($data['status']) {
            200 => $this->response("That is monthly leaves of doctor : ", ['leaves' => $data['data']], 200),
            404 => $this->response($data['message'], null, 404),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDoctorsByDepartment($departmentId)
    {
        $data = $this->userService->getDoctorsByDepartment($departmentId);
        return match ($data['status']) {
            200 => $this->response("That is doctors of department : ", ['doctors' => $data['data']], 200),
            404 => $this->response("Department not found", null, 404),
            400 => $this->response("No doctors yet in this department", null, 400),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDoctorsInDayAndDepartment($dayId, $departmentId)
    {
        $data = $this->userService->getDoctorsInDayAndDepartment($dayId, $departmentId);
        return match ($data['status']) {
            200 => $this->response("That is doctors of this day: ", ['doctors' => $data['data']], 200),
            404 => $this->response("Day  not found", null, 404),
            400 => $this->response("No doctors yet in this day", null, 400),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getDoctorsAndDepartment($dayId)
    {
        $data = $this->userService->getDoctorsAndDepartment($dayId);
        return match ($data['status']) {
            200 => $this->response("That is doctors of this day: ", $data['data'], 200),
            404 => $this->response($data['message'], null, 404),
            400 => $this->response($data['message'], null, 400),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function search()
    {
        $data = $this->userService->search();
        return match ($data['status']) {
            200 => $this->response("Here are the results", ['results' => $data['data']], 200),
            404 => $this->response("No results found", null, 404),
            422 => $this->response("Validation error: missing search query", null, 422),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function getProfileImage()
    {
        $data = [];
        try {
            $data = $this->userService->getProfileImage();
            return Response::Success($data['path'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteProfileImage()
    {
        $data = [];
        try {
            $data = $this->userService->deleteProfileImage();
            return Response::Success($data['path'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }

}