<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLogin;


class UserLoginController extends Controller
{
    
    public function details(int $id)
    {
        $users_logins = UserLogin::where('user_id', $id)->get();
        $user = User::find( $id);
        return view('users_logins.details', [
            'user' => $user,
            'users_logins' => $users_logins
        ]);
    }
}
