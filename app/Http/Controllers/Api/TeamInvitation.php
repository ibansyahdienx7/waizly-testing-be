<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Team;
use App\Models\Api\TeamInvitation as ApiTeamInvitation;
use App\Models\Api\TeamUser;
use App\Models\User;
use App\Traits\MyHelper;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamInvitation extends Controller
{
    use MyHelper;

    public function list()
    {
        try {
            $team = ApiTeamInvitation::get();
            if(count($team) > 0)
            {
                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Invitation Team is ready',
                    'data' => $team,
                    'error' => 0
                ];
            } else {
                $response = [
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Invitation Team not found !',
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
            $role = Str::upper(request()->role);

            $team = Team::where('id', $team_id)->first();

            if($team)
            {
                $validationTeam = ApiTeamInvitation::where('team_id', $team_id)->first();

                if($validationTeam)
                {
                    return response()->json([
                        'retcode' => 409,
                        'status' => false,
                        'msg' => 'Team Invitation is already exists',
                        'data' => $validationTeam,
                        'error' => 1
                    ], 409);
                }

                if($team->personal_team == 1)
                {
                    return response()->json([
                        'retcode' => 406,
                        'status' => false,
                        'msg' => 'Personal Team ' . $team->name . ' is active',
                        'error' => 1
                    ], 406);
                }

                $user = User::where('id', $team->user_id)->first();

                if(empty($user))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'User not found',
                        'error' => 1
                    ], 404);
                }

                $store = ApiTeamInvitation::create([
                    'team_id' => $team->id,
                    'email' => $user->email,
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 201,
                    'status' => true,
                    'msg' => 'Team Invitation is successfully',
                    'data' => $store,
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

    public function edit()
    {
        $validation = Validator::make(request()->all(), [
            'team_id' => 'required|integer',
            'role' => 'required',
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
            $team_id = request()->team_id;
            $role = Str::upper(request()->role);

            $team = Team::where('id', $team_id)->first();

            if($team)
            {
                $validationTeam = ApiTeamInvitation::where('id', $id)->first();

                if(empty($validationTeam))
                {
                    return response()->json([
                        'retcode' => 409,
                        'status' => false,
                        'msg' => 'Team Invitation not found',
                        'error' => 1
                    ], 409);
                }

                if($team->personal_team == 1)
                {
                    return response()->json([
                        'retcode' => 406,
                        'status' => false,
                        'msg' => 'Personal Team ' . $team->name . ' is active',
                        'error' => 1
                    ], 406);
                }

                $user = User::where('id', $team->user_id)->first();

                if(empty($user))
                {
                    return response()->json([
                        'retcode' => 404,
                        'status' => false,
                        'msg' => 'User not found',
                        'error' => 1
                    ], 404);
                }

                $update = $validationTeam->update([
                    'team_id' => $team->id,
                    'email' => $user->email,
                    'role' => $role,
                    'updated_at' => now()
                ]);

                $response = [
                    'retcode' => 202,
                    'status' => true,
                    'msg' => 'Team Invitation Update ' . $validationTeam->email . ' - ' . $validationTeam->role . ' is successfully',
                    'data' => $validationTeam,
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

            $team = ApiTeamInvitation::where('id', $id)->first();

            if($team)
            {
                $delete = $team->delete();

                $response = [
                    'retcode' => 200,
                    'status' => true,
                    'msg' => 'Team Invitation delete ' . $team->email . ' - ' . $team->role . ' is successfully',
                    'error' => 0
                ];

            } else {
                $response = [
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Team Invitation not found',
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

    public function accept($slug_email)
    {
        try{
            $team_invitation = ApiTeamInvitation::where('email', Str::lower($slug_email))->first();
            if(empty($team_invitation))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Team Invitation not found',
                    'error' => 1
                ], 404);
            }

            $team = Team::where('id', $team_invitation->team_id)->first();
            if(empty($team))
            {
                return response()->json([
                    'retcode' => 404,
                    'status' => false,
                    'msg' => 'Team not found',
                    'error' => 1
                ], 404);
            }

            // Update Personal Team CC: Table Teams //
            $team->update([
                'personal_team' => 1 /* Active */,
                'updated_at' => now()
            ]);

            // Validation Check Table Team User //
            $checkTeamUser = TeamUser::where('team_id', $team->id)
            ->where('user_id', $team->user_id)
            ->first();

            if($checkTeamUser)
            {
                return response()->json([
                    'retcode' => 409,
                    'status' => false,
                    'msg' => 'Team User is already exists!',
                    'data' => $checkTeamUser,
                    'error' => 1
                ], 409);
            }

            // Insert Table Team User //
            $team_user = TeamUser::insert([
                'user_id' => $team->user_id,
                'team_id' => $team->id,
                'role' => $team_invitation->role,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'retcode' => 201,
                'status' => true,
                'msg' => 'Store to team user is successfully',
                'data' => TeamUser::where('team_id', $team->id)
                ->where('user_id', $team->user_id)
                ->first(),
                'error' => 0
            ], 201);

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
