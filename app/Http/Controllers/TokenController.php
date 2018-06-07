<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Errors;

class TokenController extends Controller
{
    /**
     * generates a new token for new users
     */
    public function generate(Request $request){
        $token = $this->generateRandomString();
        while(true){
            if(!User::where('token', $token)->first())
                break;
            else
                $token = $this->generateRandomString();
        }
        $user = User::create(['token' => $token]);
        return [
            'ok'    => true,
            'user'  => $user
        ];
    }
    /**
     * checks if entered token exists
     */
    public function check(Request $request){
        $user = User::where('token', $request->token)->first();
        if($user)
            return [
                'ok'    => true,
                'user'  => $user
            ];
        else
            return [
                'ok'        => false,
                'token'     => $request->token,
                'errors'    => Errors::generate([1001])
            ];
    }

    function generateRandomString($length = 45) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
