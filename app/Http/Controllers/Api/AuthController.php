<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SentsMail;
use App\Models\Api\PasswordReset;
use App\Models\Api\Team;
use App\Models\User;
use App\Traits\MyHelper;
use Auth;
use Carbon\Carbon;
use File;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mail;

class AuthController extends Controller
{
    use MyHelper;

    public function list()
    {
        try {
            $user = User::get();

            if(count($user) > 0)
            {
                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'User is ready',
                    'data' => $user,
                    'error' => 0
                ];
            } else {
                $response = [
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'User not found',
                    'error' => 1
                ];
            }

            return response()->json($response, $response['retcode']);

        } catch (QueryException $e)
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

    public function register()
    {
        $validation = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
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
            $name = Str::upper(request()->name);
            $password = Hash::make(request()->password);

            $auth = User::where('email', 'like', '%' . $email . '%')->first();
            if($auth)
            {
                $response = [
                    'retcode' => 409,
                    'status' => false,
                    'msg' => 'User is already!',
                    'data' => $auth,
                    'error' => 1,
                ];
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'pzn' => $this->encryptPin(request()->password),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Check Table Team //
                $team = Team::where('user_id', $user->id)->first();
                if (empty($team)) {
                    // Insert Table Team //
                    Team::create([
                        'user_id' => $user->id,
                        'name' => $name,
                        'personal_team' => 0
                    ]);
                }

                $response = [
                    'retcode' => 201,
                    'status' => true,
                    'msg' => 'User created is successfully',
                    'data' => $user,
                    'error' => 0
                ];
            }

            return response()->json($response, $response['retcode']);

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

    public function login()
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

                return response()->json([
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Login successfully',
                    'data' => Auth::user(),
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found!',
                'error' => 1
            ], 404);

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

    public function loginWeb()
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
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Login successfully',
                    'token' => $token,
                    'data' => Auth::user(),
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found!',
                'error' => 1
            ], 404);

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

    public function changePassword()
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|integer',
            'old_password' => 'required',
            'new_password' => 'required'
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
            $id = request()->id;
            $old_password = request()->old_password;
            $password_new = Hash::make(request()->new_password);

            $user = User::where('id', $id)->first();

            if ($user) {
                if (!Hash::check($old_password, $user->password)) {
                    return response()->json([
                        'retcode' => 406,
                        'status' => false,
                        'msg' => 'Password was wrong',
                        'error' => 1
                    ], 406);
                }

                $user->update([
                    'password' => $password_new,
                    'pzn' => $this->encryptPin(request()->new_password),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Update Password ' . $user->name . ' successfully',
                    'data' => $user,
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found!',
                'error' => 1
            ], 404);

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

    public function edit()
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|integer',
            'name' => 'required',
            'email' => 'required',
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
            $id = request()->id;
            $name = Str::upper(request()->name);
            $email = Str::lower(request()->email);

            $user = User::where('id', $id)->first();

            if ($user) {
                $user->update([
                    'name' => $name,
                    'email' => $email,
                    'updated_at' => now()
                ]);

                return response()->json([
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Update Data ' . $user->name . ' successfully',
                    'data' => $user,
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found!',
                'error' => 1
            ], 404);

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

    public function editPhoto()
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|integer',
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
            $id = request()->id;

            $user = User::where('id', $id)->first();

            if ($user) {

                if (request()->photo !== null) {
                    // Obtain the original content (usually binary data)
                    $bin = base64_decode(request()->photo);
                    $decoded = base64_decode(request()->photo, true);

                    // image verify check
                    if (!is_string(request()->photo) || false === $decoded) {
                        return response()->json([
                            'status' => false,
                            'msg' => 'invalid format image'
                        ], 422);
                    }

                    // Load GD resource from binary data
                    $im = imageCreateFromString($bin);

                    if (!$im) {
                        return response()->json([
                            'retcode' => 406,
                            'status' => false,
                            'msg' => 'file is not an image',
                            'error' => 1
                        ], 406);
                    }

                    // Spesifikasi penyimpanan file
                    $img_name = Str::random(6) . '-' . time() . '.png';
                    if (is_dir(public_path('assets/img/user')) == false) {
                        $path = public_path('assets/img/user');
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    $img_file = public_path('assets/img/user/' . $img_name);
                    imagepng($im, $img_file, 0);

                    $photo = url('assets/img/user/' . $img_name);
                } else {
                    $photo = null;
                }

                $user->update([
                    'profile_photo_path' => $photo,
                    'updated_at' => now()
                ]);

                return response()->json([
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Update Photo ' . $user->name . ' successfully',
                    'data' => $user,
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found!',
                'error' => 1
            ], 404);

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

    public function forgot()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|string|max:255',
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

            $auth = User::where('email', $email)->first();

            if(empty($auth))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'User not found!',
                    'error' => 1
                ], 404);
            }

            $code = $this->encryptPin(Str::random(), $this->unix_time());

            // Check Password //
            $userCheckPassword = PasswordReset::where('email', $email)->first();

            if (empty($userCheckPassword)) {

                $reset = PasswordReset::insert([
                    'email' => $email,
                    'token' => $code,
                    'created_at' => now()
                ]);

                $response = [
                    'retcode' => 202,
                    'status' => true,
                    'msg' => 'Your password token has been sent via email ' . $email . ' please reset your password again',
                    'data' => [
                        'token' => $code,
                    ],
                    'error' => 0
                ];
            }else{
                $userCheckPassword->update([
                    'token' => $code,
                    'created_at' => now()
                ]);
            }

            $data = [
                'subject'       => 'Forgot Your Password - ' . $email,
                'title'         => 'Forgot Your Password?',
                'token'         => $code,
                'emailto'       => $auth->name,
                'email'         => $email
            ];

            Mail::to($email)->send(new SentsMail($data));

            $response = [
                'retcode' => 202,
                'status' => true,
                'msg' => 'Your password token has been sent via email ' . $email . ' please reset your password again',
                'data' => [
                    'token' => $code,
                ],
                'error' => 0
            ];

            return response()->json($response, $response['retcode']);

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

    public function checkCode()
    {
        try {
            $code = request()->code;

            $checkCode = PasswordReset::where('token', $code)->first();
            if(empty($checkCode))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Token not found',
                    'error' => 1
                ], 404);
            }

            $one_days = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($checkCode->created_at)));
            $date = now();

            if ($date > $one_days) {
                return response()->json([
                    'retcode' => 417,
                    'status' => false,
                    'msg' => 'The token has expired',
                    'error' => 1
                ], 417);
            }

            return response()->json([
                'retcode' => 202,
                'status' => true,
                'msg' => 'Validation was successful, now you can reset your password',
                'data' => [
                    'email' => $checkCode->email
                ],
                'error' => 0
            ], 202);

        } catch (QueryException $e)
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

    public function reset()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required',
            'new_password' => 'required'
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
            $email = Str::upper(request()->email);
            $new_password = Hash::make(request()->new_password);

            $checkCode = PasswordReset::where('email', $email)->first();
            if(empty($checkCode))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Token not found',
                    'error' => 1
                ], 404);
            }

            $one_days = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($checkCode->created_at)));
            $date = now();

            if ($date > $one_days) {
                return response()->json([
                    'retcode' => 417,
                    'status' => false,
                    'msg' => 'The token has expired',
                    'error' => 1
                ], 417);
            }

            // Validate User //
            $user = User::where('email', $email)->first();
            if(empty($user))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'User not found',
                    'error' => 1
                ], 404);
            }

            $user->update([
                'password' => $new_password,
                'pzn' => $this->encryptPin(request()->new_password),
                'updated_at' => now()
            ]);

            return response()->json([
                'retcode' => 202,
                'status' => true,
                'msg' => 'Password changed successfully',
                'error' => 0
            ], 202);

        } catch (QueryException $e)
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

    public function delete()
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|integer',
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
            $id = request()->id;

            $user = User::where('id', $id)->first();

            if ($user) {

                $user->tokens()->where('id', $user->id)->delete();
                $user->delete();

                if ($user->profile_photo_path !== null) {
                    $current_photo = $user->profile_photo_path;
                    $current_photo = explode(url('') . '/', $current_photo);
                    $current_photo = end($current_photo);

                    // hapus gambar lama
                    if (file_exists(public_path($current_photo)) == true) {
                        unlink($current_photo);
                    }
                }

                return response()->json([
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Delete Data ' . $user->name . ' successfully',
                    'data' => $user,
                    'error' => 0
                ], 200);
            }

            return response()->json([
                'retcode' => 404,
                'status' => false,
                'msg' => 'User not found !',
                'error' => 1
            ], 404);

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
