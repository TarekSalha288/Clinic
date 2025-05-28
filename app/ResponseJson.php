<?php

namespace App;

trait ResponseJson
{
    public function response($msg,$data,$status){
        return response()->json(['msg'=>$msg,'data'=>$data],$status);
    }
}