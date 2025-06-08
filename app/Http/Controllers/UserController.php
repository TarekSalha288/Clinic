<?php

namespace App\Http\Controllers;

use App\ResponseJson;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        $doctor = $this->userService->getDoctor($id);
        if ($doctor == null) {
            return $this->response("Doctor not found", ['doctor' => null], 404);
        }
        return $this->response("That is doctor", ['doctor' => $doctor], 200);
    }
    public function getDoctors()
    {
        $doctors = $this->userService->getDoctors();
        if ($doctors == null) {
            return $this->response("No doctors yet", ['doctors' => null], 200);
        }
        return $this->response("That is all doctors", ['doctors' => $doctors], 200);
    }
    public function getDepartment($id)
    {
        $department = $this->userService->getDepartment($id);
        if ($department == null) {
            return $this->response("Department not found", ['department' => null], 404);
        }
        return $this->response("That is Department {$department->name}", ['department' => $department], 200);
    }
    public function getDepartments()
    {
        $departments = $this->userService->getDepartments();
        if ($departments == null) {
            return $this->response("No departments yet", ['departments' => null], 200);
        }
        return $this->response("That is all departments", ['departments' => $departments], 200);
    }
    public function getLeaves($doctorId)
    {
        $data = $this->userService->getLeaves($doctorId);
        return match ($data['status']) {
            200 => $this->response("That is monthly leaves of doctor : ", ['leaves' => $data['data']], 200),
            404 => $this->response("Doctor not found", null, 404),
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
    public function getDoctorsAndDepartment($dayId){
           $data = $this->userService->getDoctorsAndDepartment($dayId);
        return match ($data['status']) {
            200 => $this->response("That is doctors of this day: ",  $data['data'], 200),
            404 => $this->response("Day  not found", null, 404),
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
}
