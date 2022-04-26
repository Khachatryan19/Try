<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class UserController extends Controller
{
    public function generalInfo(): response
    {
        $info = 'Login page - /login' . Auth::user()->username . "<br>";
        $info .= 'Registration page - /register' . "<br>";
        $info .= 'admin page has prefix - /admin' . "<br>";

        return new Response($info, 200);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return new Response([
                'message' => 'Bad try',
            ], 401);
        }

        try {
            $token = $user->createToken('login_token')->plainTextToken;
        }catch (\Exception $e){
            return new Response([
                'message' => 'you have already logged',
                'token' => $user->tokens()
            ], 200);
        }

        /*$user = Auth::attempt([
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        $token = auth()->user()->createToken('my_token')->plainTextToken;*/

        return new Response([
            'message' => 'you logged successfully',
            'token' => $token
        ], 200);
    }

    public function store(Request $request): response
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:3|confirmed'
        ]);

        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        $token = $user->createToken('login_token')->plainTextToken;

        return new Response([
            'message' => 'data has been stored',
            'token' => $token
        ], 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'Logged out'];
    }

    public function getAllProducts()
    {
        $data = DB::select('SELECT name, price FROM products');

        return new JsonResponse(['products' => $data], 200);
    }
}
