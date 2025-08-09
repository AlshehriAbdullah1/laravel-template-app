<?php

namespace App\Interfaces\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Http\Requests\Auth\LoginRequest;
use App\Interfaces\Http\Resources\UserResource;
use App\Application\Users\UseCases\RegisterUser;
use App\Application\Users\UseCases\LoginUser;
use App\Models\User; // keep using the Eloquent user (aliased or default)

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $useCase)
    {
        $data = $request->validated();
        $dto  = $useCase->execute($data['name'], $data['email'], $data['password']);

        // Issue Sanctum token from the Eloquent model
        $user  = User::findOrFail($dto->id);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token,
            ]
        ], 201);
    }

    public function login(LoginRequest $request, LoginUser $useCase)
    {
        $data = $request->validated();
        ['userId' => $id] = $useCase->execute($data['email'], $data['password']);

        $user  = User::findOrFail($id);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token,
            ]
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
