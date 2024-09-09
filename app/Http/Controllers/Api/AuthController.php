<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\TokenAccess;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     * @param Request $request
     * @return User
     */

    public function register(Request $request)
    {
        try {
            //code...
            $validateUser = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6',
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $validateUser->errors()
                ], 400);
            }
            $name = trim($request->first_name  . $request->last_name);
            $name = strtolower($name);
            $name = preg_replace('/[^a-zA-Z0-9]/', '-', $name);
            $name = preg_replace('/-{2,}/', '-', $name);
            $name = trim($name, '-');
            $check = $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            if (!$check) {
                return response()->json([
                    'status' => false,
                    'message' => 'register failed',
                ], 400);
            }
            $date = date('Y-m-d H:i:s');
            $profile = new ProfileUser();
            $profile->user_id = $user->id;
            $profile->id_profile = 'profile' . uniqid() . '-' . $user->id . $date;
            $profile->id_image = '01.jpg';
            $profile->id_cover_image = '01.jpg';
            $profile->hashtag = '#' . $name;
            $profile->save();
            $user->profile = $profile;
            return response()->json([
                'status' => true,
                'message' => 'register success',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }


    public function login(Request $request)
    {
        try {
            //code...
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $validateUser->errors()
                ], 200);
            }
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email Password  does not match with our records',
                ], 200);
            }
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("API TOKEN")->plainTextToken;
            // Update the last_used_at column of the personal access token +3 months
            $now = now();
            $user->tokens()->update(['last_used_at' => $now->addMonths(2)]);
            $user->profile;
            return response()->json([
                'status' => true,
                'message' => 'login success',
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),

            ], 200);
        }
    }
    public function adminLogin(Request $request)
    { {
            try {
                //code...
                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required|string|email|max:255',
                        'password' => 'required|string|min:6',
                    ]
                );
                if ($validateUser->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'validate error',
                        'error' => $validateUser->errors()
                    ], 200);
                }
                if (!Auth::attempt($request->only(['email', 'password']))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Email Password  does not match with our records',
                    ], 200);
                }
                $user = User::where('email', $request->email)->first();
                $user->profile;
                if ($user->position !== 'User') {
                    $token = $user->createToken("API TOKEN")->plainTextToken;
                    $now = now();
                    $user->tokens()->update(['last_used_at' => $now->addMonths(5)]);
                    return response()->json([
                        'status' => true,
                        'message' => 'login success',
                        'token' => $token,
                        'user' => $user
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are not admin',
                    ], 200);
                }
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),

                ], 200);
            }
        }
    }

    public function loginByToken(Request $request)
    {
        try {
            //code...
            $tokendata = $request->bearerToken();
            // config token 
            $tokenfind = PersonalAccessToken::findToken($tokendata)->token;
            $token = TokenAccess::where('token', $tokenfind)->first();
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token does not match with our records',
                    'token' => $token,
                ], 200);
            }
            $user = User::where('id', $token->tokenable_id)->first();
            $user->profile;
            return response()->json([
                'status' => true,
                'message' => 'login success',
                'user' => $user,
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        try {
            //code...
            $user = User::where('email', $request->email)->first();
            $tokendata = $request->bearerToken();
            $check = $user->tokens()->where('id', $tokendata)->delete();
            if ($check) {
                return response()->json([
                    'status' => true,
                    'message' => 'logout success',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'logout fail',
                ], 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 450);
        }
    }
    public function logoutAll(Request $request)
    {
        try {
            //code...
            $user = User::where('email', $request->email)->first();
            $user->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'logout success',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 450);
        }
    }
}
