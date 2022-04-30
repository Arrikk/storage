<?php
namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use Core\Http\Res;

/**
 * Account Controller
 */
class Account extends Authenticated
{
    public function _update()
    {
        if($update = $this->user->put($_POST))
        return Res::status()->json($update);
        return Res::send($update);
    }
}
