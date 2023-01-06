<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Team;
use App\Models\Api\TeamUser as ApiTeamUser;
use App\Models\User;
use App\Traits\MyHelper;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamUser extends Controller
{
    use MyHelper;

    public function list()
    {
        try {
            $team = ApiTeamUser::get();
            if(count($team) > 0)
            {
                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Team User is ready',
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
            'user_id' => 'required|integer',
            'team_id' => 'required|integer',
            'role' => 'required'
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
            $team_id = request()->team_id;
            $user_id = request()->user_id;
            $role = Str::upper(request()->role);

            $user = User::where('id', $user_id)->first();

            if($user)
            {
                $validationTeam = ApiTeamUser::where('user_id', $user_id)->where('team_id', $team_id)->first();

                if($validationTeam)
                {
                    return response()->json([
                        'retcode' => 409,
                        'status' => false,
                        'msg' => 'Team User is already exists',
                        'data' => $validationTeam,
                        'error' => 1
                    ], 409);
                }

                $teamId = Team::where('id', $team_id)->first();

                if(empty($teamId))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'Teams not found',
                        'error' => 1
                    ], 404);
                }

                $store = ApiTeamUser::create([
                    'user_id' => $user->id,
                    'team_id' => $teamId->id,
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 201,
                    'status' => true,
                    'msg' => 'Team User regist is successfully',
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
            'id' => 'required|integer',
            'user_id' => 'required|integer',
            'team_id' => 'required|integer',
            'role' => 'required'
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
            $team_id = request()->team_id;
            $user_id = request()->user_id;
            $role = Str::upper(request()->role);

            $user = User::where('id', $user_id)->first();

            if($user)
            {
                $validationTeam = ApiTeamUser::where('id', $id)->first();

                if(empty($validationTeam))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'Team User not found',
                        'error' => 1
                    ], 404);
                }

                $teamId = Team::where('id', $team_id)->first();

                if(empty($teamId))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'Teams not found',
                        'error' => 1
                    ], 404);
                }

                $store = $validationTeam->update([
                    'user_id' => $user->id,
                    'team_id' => $teamId->id,
                    'role' => $role,
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 202,
                    'status' => true,
                    'msg' => 'Team User ' . $teamId->name . ' update is successfully',
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

            $team = ApiTeamUser::where('id', $id)->first();

            if($team)
            {
                $delete = $team->delete();

                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Team User delete ' . $team->user_id . ' - ' . $team->team_id . ' ( ' . $team->role . ' ) is successfully',
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
