<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
	public function sendSuccess($message, $result = NULL)
    {
    	$response = [
            'ResponseCode'      => 200,
            'Status'            => True,
            'Message'           => $message,
        ];

        if(!empty($result)){
            $response['Data'] = $result;
        }
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendFailed($errorMessages = [], $code = 200)
    {
    	$response = [
            'ResponseCode'      => $code,
            'Status'            => False,
        ];


        if(!empty($errorMessages)){
            $response['Message'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}