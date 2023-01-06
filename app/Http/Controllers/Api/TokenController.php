<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\MyHelper;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    use MyHelper;

    public function create()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:5'
        ]);

        $json = [
            'retcode' => 422,
            'status' => false,
            'msg' => 'Something was wrong !',
            'error' => 1,
            'error_detail' => $validation->errors()
        ];

        if ($validation->fails()) return response()->json($json, 422);

        try {
            $email = Str::lower(request()->email);
            $password = request()->password;

            $user = User::where('email', $email)->first();

            if ($user) {
                if (!Hash::check($password, $user->password)) {
                    return response()->json([
                        'retcode' => 406,
                        'status' => false,
                        'msg' => 'Password was wrong',
                        'error' => 1
                    ], 406);
                }

                $expired = Carbon::tomorrow();
                $token = $user->createToken('auth_token', ['*'], $expired)->plainTextToken;

                return response()->json([
                    'retcode' => 201,
                    'status' => true,
                    'msg' => 'Token created is successfully',
                    'authorization' => [
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ],
                    'error' => 0
                ], 201);
            }

            return response()->json([
                'retcode' => 403,
                'status' => false,
                'msg' => 'Login Failed! User not found',
                'error' => 1
            ], 403);

        } catch(QueryException $e)
        {
            return response()->json([
                'retcode' => 417,
                'status' => false,
                'msg' => 'Something was wrong !',
                'error' => 1,
                'error_detail' => $e
            ], 417);
        }
    }
}
