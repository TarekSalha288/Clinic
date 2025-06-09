<?php

namespace App\Http\Controllers;

use App\ResponseJson;
use App\Services\SecretaryService;
use Illuminate\Http\Request;

class SecretaryController extends Controller
{
    use ResponseJson;
    private $SecretaryServece;
    public function __construct(SecretaryService $SecretaryServece){
$this->SecretaryServece=$SecretaryServece;
    }
    public function reserve(){
        $data=$this->SecretaryServece->reserve();
        return match ($data['status']){
 201 => $this->response(" Created Successfully", ['apointment' => $data['data']], 201),
        400 => $this->response("Validation failed", $data['errors'], 400),
         404=> $this->response($data['message'], null, 404),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
        };
    }
    public function acceptReverse($id){
        $data=$this->SecretaryServece->acceptReverse($id);
         return match ($data['status']){
            200 => $this->response($data['message'], ['appointment' => $data['data']], 200),
                400 => $this->response($data['message'], null, 400),
              404=> $this->response($data['message'], null, 404),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
         };
    }
    public function rejectReverse($id){
          $data=$this->SecretaryServece->deleteReverse($id);
         return match ($data['status']){
            200 => $this->response($data['message'], ['appointment' => null], 200),
                400 => $this->response($data['message'], null, 400),
              404=> $this->response($data['message'], null, 404),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
         };
    }
  public function appointments()
{
    $data = $this->SecretaryServece->appointments();

    return match ($data['status']) {
        200 => $this->response($data['message'], $data['data'], 200),
        400 => $this->response($data['errors'], null, 400),
        404 => $this->response($data['message'], null, 404),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
    };
}

    public function search(){

        $data = $this->SecretaryServece->search();
        return match ($data['status']) {
            200 => $this->response("Here are the results", ['results' => $data['data']], 200),
            404 => $this->response("No results found", null, 404),
            422 => $this->response("Validation error: missing search query", null, 422),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520),
        };
    }
    public function apointments(){
          $data = $this->SecretaryServece->apointments();
        return match ($data['status']) {
            200 => $this->response($data['message'],  $data['data'], 200),
            404 => $this->response($data['message'], null, 404),
            500 => $this->response("Server error: " . $data['error'], null, 500),
            default => $this->response("Unknown error", null, 520)
    };}
    public function addMounthlyLeave($doctorId){
$data=$this->SecretaryServece->addMonthlyLeave($doctorId);
  return match ($data['status']) {
        201 => $this->response(" Created Successfully", ['secretary' => $data['data']], 201),
        400 => $this->response("Validation failed", $data['errors'], 400),
        409 => $this->response($data['message'], null, 409),
         404=> $this->response($data['message'], null, 404),
        500 => $this->response("Server error: " . $data['error'], null, 500),
        default => $this->response("Unknown error", null, 520),
    };

    }
public function removeMonthlyleaves(){
   $data= $this->SecretaryServece->removeMonthlyleaves();
   return match($data['status']){
200=> $this->response("Removed all monthly leaves",null,200),
500=>  $this->response($data['error'],null,500),
 default => $this->response("Unknown error", null, 520),
   };


}
public function relaseRate(){
 $data= $this->SecretaryServece->relaseRate();
   return match($data['status']){
200=> $this->response($data['message'],null,200),
404=> $this->response($data['message'],null,404),
500=>  $this->response($data['error'],null,500),
 default => $this->response("Unknown error", null, 520),
   };
}
public function enterPatient($id){
     $data= $this->SecretaryServece->enterPatient($id);
   return match($data['status']){
200=> $this->response($data['message'],null,200),
404=> $this->response($data['message'],null,404),
400=>$this->response($data['message'],null,400),
500=>  $this->response($data['error'],null,500),
 default => $this->response("Unknown error", null, 520),
   };
}
}
