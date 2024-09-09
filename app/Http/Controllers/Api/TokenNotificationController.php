<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TokenNotification;
use Illuminate\Support\Facades\Validator;

class TokenNotificationController extends Controller
{
    //
    public function createTokenDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token_notification' => 'required',
            'token' => 'required',
            'device' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }
        $token = new TokenNotification();
        $token->id_token_notification = $request->id_token_notification.uniqid();
        $token->token = $request->token;
        $token->device = $request->device;
        $check = $token->save();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'create token success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'create token fail',
            ], 200);
        }
    }
}
