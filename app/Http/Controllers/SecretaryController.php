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
}