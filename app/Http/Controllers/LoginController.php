<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $validator = validator($credentials, [
            'username' => 'required|string|max:191',
            'password' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            $this->logFailedLoginAttempt($request->input('username'));
            return $this->invalidCredentialsResponse();
        }

        $user = auth()->guard('api')->user();
        
        $isDefaultPassword = Hash::check('Password', $user->password);

        $token = JWTAuth::fromUser($user, ['exp' => now()->addDay()->timestamp]);
        $tokenData = JWTAuth::setToken($token)->getPayload();

        $response = [
            'status' => 200,
            'message' => 'Login success!',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $tokenData->get('exp') - time(),
        ];

        if ($isDefaultPassword) {
            $response['default_password'] = true;
        }

        $this->logSuccessfulLogin($request->input('username'));

        return response()->json($response);
    }
    private function validationErrorResponse($errors)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Error Params',
            'errors' => $errors,
        ], 422);
    }
    private function invalidCredentialsResponse()
    {
        return response()->json([
            'status' => 401,
            'message' => 'Invalid Username or Password!'
        ], 401);
    }
    private function logFailedLoginAttempt($username)
    {
        Log::info("User $username failed to login!");
    }
    private function logSuccessfulLogin($username)
    {
        Log::info("User $username succesfully login!");
    }
}