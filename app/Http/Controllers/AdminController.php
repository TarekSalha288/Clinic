<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\ResponseJson;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ResponseJson;

    protected $AdminService;

    public function __construct(AdminService $AdminService)
    {
        $this->AdminService = $AdminService;
    }
  public function createSecretary()
{
    $data = $this->AdminService->createSecretary();

    return match ($data['status']) {
        201 => $this->response("Secretary Created Successfully", ['secretary' => $data['user']], 201),
        400 => $this->response("Validation failed", $data['errors'], 400),
        409 => $this->response("Secretary already exists", null, 409),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
    };
}

    public function updateSecretary(){
        $data=$this->AdminService->updateSecretary();
         if ($data->original) {
            return $this->response($data->original, null, 400);
        }
        return $this->response("Secratery Updated Successfully", ['secratery' => $data], 200);
    }
     public function deleteSecretary()
    {
        $data = $this->AdminService->deleteSecretary();
        if ($data == null) {
            return $this->response("Secretary deleted Successfully", ['secretary' => null], 200);
        }
        return $this->response($data,null,400);
    }
    public function createDoctor()
    {
        $data = $this->AdminService->createDoctor();
        if ($data->original) {
            return $this->response($data->original, null, 400);
        }
        return $this->response("Doctor Created Successfully", ['doctor' => $data], 201);
    }



    public function deleteDoctor($id)
    {
        $data = $this->AdminService->deleteDoctor($id);
        if ($data == null) {
            return $this->response("Doctor deleted Successfully", ['doctor' => null], 200);
        }
    }



    public function createDepartment()
    {
        $data = $this->AdminService->createDepartment();
        if ($data->original) {
            return $this->response($data->original, null, 400);
        }
        return $this->response("Department Created Successfully", ['department' => $data], 201);
    }


    public function deleteDepartment($id)
    {
        $data = $this->AdminService->deleteDepartment($id);
        if ($data == null) {
            return $this->response("Department deleted Successfully", ['department' => null], 200);
        }
    }



    public function deleteUser($id)
    {
        $data = $this->AdminService->deleteUser($id);
        if ($data == null) {
            return $this->response("User deleted Successfully", ['user' => null], 200);
        }
        return $this->response("User not found", ['user' => null], 404);
    }
}