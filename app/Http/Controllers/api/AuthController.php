<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $credentials = $request->only('email','password');

            $token = Auth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'error_number' => 404,
                ], 401);
            }

            $user  = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الدخول',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'status' => true,
            'message' => 'تم تسجيل الخروج'
        ];
    }

    public function pusher(){
        try {

            $pusher = [
                'PUSHER_APP_ID' => '1551946',
                'PUSHER_APP_KEY' => 'baba382db1e49c335622',
                'PUSHER_APP_SECRET' => '138b68841065f19ef521',
                'PUSHER_APP_CLUSTER' => 'mt1',
            ];

            $event = [
              'message' => 'message-sent'
            ];

            return response()->json([
                'status' => true,
                'pusher' => $pusher,
                'event' => $event
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
