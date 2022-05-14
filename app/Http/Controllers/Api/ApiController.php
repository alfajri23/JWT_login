<?php

namespace App\Http\Controllers\Api;;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('role', 'email', 'password');
        $validator = Validator::make($data, [
            'role' => '',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
        	'role' => $request->role,
        	'email' => $request->email,
        	'password' => bcrypt($request->password)
        ]);

        if (!$token = Auth::guard('api')->attempt($data)) {
            return $this->respondError();
        }

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
            'token' => $token
        ], Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');

        // if (!Auth::guard('api')->attempt($credentials)) {

        //     return response()->json([
        //         'message' => 'Tidak dapat mengakses sistem, cek berlangganan paket berlangganan Anda',
        //         'status' => false,
        //         'data' => null
        //     ], 401);
        // }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->respondError();
        }

        //$user = JWTAuth::authenticate($token);
        //$user = '';
        $user = Auth::guard('api')->user();

        return response()->json([
            'data'=>$user,
            'message' => 'Login successfully.',
            'status' => true,
            'access_token' => $token,
        ], 200);

    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }

        //$user = JWTAuth::authenticate($token);
        //$user = '';
        $user = Auth::guard('api')->user();
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'data'=>$user,
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }
}