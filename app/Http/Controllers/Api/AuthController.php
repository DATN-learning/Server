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

    public function getProfile(Request $request){
        $user = User::with('profile')->find($request->user()->id);

        if ($user && $user->profile) {
            return response()->json([
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'User or Profile not found',
            ], 404);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            // Lấy tất cả người dùng cùng với profile của họ
            $users = User::with('profile')->get(); // Dùng `get()` để trả về collection (mảng các đối tượng)

            if ($users->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No users found',
                    'data' => [] // Trả về mảng rỗng nếu không có người dùng
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Users fetched successfully',
                'data' => $users // Trả về mảng người dùng
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function updateProfile(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validateUser = Validator::make(
            $request->all(),
            [
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'nick_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'date_of_birth' => 'nullable|date',
                'class_name' => 'nullable|string|max:255',
                'school_name' => 'nullable|string|max:255',
                'hashtag' => 'nullable|string|max:255',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
            ], 400);
        }

        // Lấy người dùng hiện tại
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Cập nhật thông tin cơ bản của người dùng
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->save();

        // Cập nhật thông tin profile của người dùng
        $profile = ProfileUser::where('user_id', $user->id)->first();

        if ($profile) {
            $profile->nick_name = $request->nick_name ?? $profile->nick_name;
            $profile->address = $request->address ?? $profile->address;
            $profile->date_of_birth = $request->date_of_birth ?? $profile->date_of_birth;
            $profile->class_name = $request->class_name ?? $profile->class_name;
            $profile->school_name = $request->school_name ?? $profile->school_name;
            $profile->hashtag = $request->hashtag ?? $profile->hashtag;
            $profile->save();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
        ], 200);
    }

    public function deleteUserByProfileId(Request $request)
    {
        // Xác thực rằng người dùng đã đăng nhập
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Lấy id_profile từ yêu cầu
        $idProfile = $request->input('id_profile');

        try {
            // Tìm người dùng dựa trên id_profile
            $profile = ProfileUser::where('id_profile', $idProfile)->first();

            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile not found',
                ], 404);
            }

            // Xóa người dùng và profile tương ứng
            $userToDelete = User::find($profile->user_id);

            if ($userToDelete) {
                // Xóa profile trước
                $profile->delete();

                // Xóa người dùng
                $userToDelete->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'User deleted successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
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
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'logout success',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Token not found',
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
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
