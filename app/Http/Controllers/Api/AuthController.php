<?php
namespace App\Http\Controllers;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }


    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:clients',
            'phone' => 'required|min:11|numeric',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response($validator->errors(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response([
            'message' => 'user successfully registered',
            'user' => $user,
        ], 201);
    }


    public function logout() {
        auth()->logout();
        return response(['message' => 'user successfully signed out']);
    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }




    protected function createNewToken($token){
        return response([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 36000000,
        ]);
    }
}
