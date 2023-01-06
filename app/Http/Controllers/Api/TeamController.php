<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Team;
use App\Models\User;
use App\Traits\MyHelper;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    use MyHelper;

    public function list()
    {
        try {
            $team = Team::get();
            if(count($team) > 0)
            {
                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Team is ready',
                    'data' => $team,
                    'error' => 0
                ];
            } else {
                $response = [
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Team not found !',
                    'error' => 1
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

    public function store()
    {
        $validation = Validator::make(request()->all(), [
            'user_id|integer' => 'required',
            'personal_team|integer' => 'required'
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
            $user_id = request()->user_id;
            $personal_team = Str::upper(request()->personal_team);

            $user = User::where('id', $user_id)->first();

            if($user)
            {
                $validationTeam = Team::where('user_id', $user_id)->first();

                if($validationTeam)
                {
                    return response()->json([
                        'retcode' => 409,
                        'status' => false,
                        'msg' => 'Team is already exists',
                        'data' => $validationTeam,
                        'error' => 1
                    ], 409);
                }

                $store = Team::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'personal_team' => $personal_team,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 201,
                    'status' => true,
                    'msg' => 'Team regist is successfully',
                    'data' => $store,
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

    public function edit()
    {
        $validation = Validator::make(request()->all(), [
            'user_id' => 'required|integer',
            'personal_team' => 'required|integer',
            'id' => 'required'
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
            $user_id = request()->user_id;
            $personal_team = Str::upper(request()->personal_team);

            $user = User::where('id', $user_id)->first();

            if($user)
            {
                $validationTeam = Team::where('id', $id)->first();

                if(empty($validationTeam))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'Team not found !',
                        'error' => 1
                    ], 404);
                }

                $update = $validationTeam->update([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'personal_team' => $personal_team,
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 202,
                    'status' => true,
                    'msg' => 'Team update ' . $validationTeam->name . ' is successfully',
                    'data' => $validationTeam,
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

    public function delete()
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|integer'
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

            $team = Team::where('id', $id)->first();

            if($team)
            {
                $delete = $team->delete();

                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Team delete ' . $team->name . ' is successfully',
                    'error' => 0
                ];

            } else {
                $response = [
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Team not found',
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
}
