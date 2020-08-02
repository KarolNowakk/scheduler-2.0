<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\User;

trait HelperTrait
{
    public function signInOAuth(User $user = null)
    {
        if (!$user) {
            $user = factory(User::class)->create();
        }

        Artisan::call('passport:install');
        $token = DB::table('oauth_clients')->where('id', 2)->pluck('secret')[0];
        $response = $this->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 2,
                'client_secret' => $token,
                'username' => $user->email,
                'password' => 'password'
            ]
        ]);
        dd($response);
    }
}
