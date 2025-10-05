<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $req): JsonResponse
    {
        $data = $req->validated();

        if(User::query()->where('username', $data['username'])->count() == 1){
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => [
                        'Username already registered'
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $req): UserResource
    {
        $data = $req->validated();

        $user = User::query()->where('username', $data['username'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Username or password wrong'
                    ],
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user));
    }

    public function get(Request $req): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $req): UserResource
    {
        $data = $req->validated();

        /** @var \App\Models\User $user */ // without this, intelephense will showing error 
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
