<?php

namespace App\Controllers;

use App\Auth as AppAuth;
use App\Controllers\Authenticated\Authenticated;
use App\Models\User;
use Core\Controller;
use Core\Http\Res;
use Core\View;

/**
 * Auth Controller
 */

class Auth extends Authenticated
{
    public function Login()
    {
        if(AppAuth::getUser()) $this->redirect('/App/'); # If already Loggedin

        if(isset($_POST) && !empty($_POST)){
            $user = User::authenticate($_POST); # Authenticate Credentials
            if (!$user) return Res::status(400)->send($user); # Return message for error
            AppAuth::login($user); #if Credentials Set session and login
            return Res::json($user->id); # return user credentials
        }

        # View Login page 
        View::draw('{/auth/login}', [
            '__title' => 'Login',
            '__class' => 'body'
        ]);
    }

    public function register()
    {
        if(AppAuth::getUser()) $this->redirect('/App/'); # If already LoggedIn
        if(isset($_POST) && !empty($_POST)){
            $save = new User($_POST); # Initiate user Class
            if($user = $save->save()) # save credentials
                return Res::status(200)->send(true); #Return if true
            return Res::status(400)->send($user); # return if error
        }

        # View Registration Page
        View::draw('{/auth/register}', [
            '__title' => 'Register',
            '__class' => 'body'
        ]);
    }

    public function destroy()
    {
        AppAuth::logout();
        $this->redirect('/App/login');
    }
}
