<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Users;
use Inertia\Inertia;

class UserController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::parseToken();
            $token->invalidate();
    
            return $this->successResponse('Logout successful');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->tokenErrorResponse('Access Token Expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->tokenErrorResponse('Access Token Invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->tokenErrorResponse('Access Token Absent', 401);
        }
    }
    public function register_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'password' => 'required|string|max:191',
            'role' => 'required|string',
            'division' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = new Users([
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'role' => $request->input('role'),
            'division_id' => $request->input('division'),
        ]);
        
        $user->password = $request->input('password');
        $user->save();

        return $this->successResponse('User registered successfully', $user, 201);
    }
    public function get_profile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return $this->successResponse('User profile retrieved successfully', $user);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->tokenErrorResponse('Access Token Expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->tokenErrorResponse('Access Token Invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->tokenErrorResponse('Access Token Absent', 401);
        }
    }
    public function reset_password(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
    
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);
    
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
    
            $oldPassword = $request->input('old_password');
            $newPassword = $request->input('new_password');
    
            if (!Hash::check($oldPassword, $user->password)) {
                return $this->tokenErrorResponse('Old password does not match', 401);
            }
    
            $user->password = $newPassword;
            $user->save();
    
            return $this->successResponse('Password reset successfully');
    
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->tokenErrorResponse('Access Token Expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->tokenErrorResponse('Access Token Invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->tokenErrorResponse('Access Token Absent', 401);
        }
    }    
    private function validationErrorResponse($errors)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Validation error',
            'errors' => $errors,
        ], 422);
    }
    private function tokenErrorResponse($message, $statusCode)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
        ], $statusCode);
    }
    private function successResponse($message, $data = null, $statusCode = 200)
    {
        $response = [
            'status' => $statusCode,
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }
}