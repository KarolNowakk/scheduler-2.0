<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $http = new Client;

        try {
            return response()->json('aaaaa');
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() == 400) {
                return response()->json('Invalid Request. Please enter a username or a password.', 401);
            } elseif ($e->getCode() == 401) {
                return response()->json('Your credentials are incorrect. Please try again.', 200);
            }
            return response()->json('Something went wrong one the server.' + $e, 200);
        }
    }

    public function register(Request $request)
    {
        return response()->json('aaaaa');
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json('Logged out successfully', 200);
    }
}
