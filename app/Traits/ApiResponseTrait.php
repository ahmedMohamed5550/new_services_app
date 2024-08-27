<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function apiResponse($title, $status = 200, $data = [])
    {
        $response['message'] = $title;
        $response['data'] = $data;
        $response['status'] = $status;
        return response($response, $status);
    }

    
    protected function failed($message , $status)
    {
        $response['message'] = $message;
        $response['status'] = $status;
        return response($response , $status);
    }
}
