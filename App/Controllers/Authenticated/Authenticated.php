<?php
namespace App\Controllers\Authenticated;

use Core\Controller;
use App\Auth;
use App\Flash;
use Core\Http\Res;

/**
 * Authenticated Controller
 */

class Authenticated extends Controller
{
    protected function before(){
        parent::before();
        $user = Auth::getUser();
        if(!$user){
            Flash::addMessage('Unauthorized Access');
            $this->redirect('/App/login');
            return false;
        }
        $this->user = $user;
    }
}
