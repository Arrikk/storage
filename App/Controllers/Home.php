<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use App\Controllers\Authenticated\Authenticated;
use Core\Http\Res;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends Authenticated
{

    /**
     * Show the index page
     */
    public function _index($get): array # get as GET
    {

        return [];
    }


    /**
     * return array
     */
    public function test($data) #Data as POST
    {
        // $data->username;
        // Res::json($data);
        // echo json_encode($data);
        // return;
        $folder = 'Public/Base';
        if (is_dir($folder)) {
            $userFolder = $folder.'/'.$data->enrollment_id;
            if(is_dir($userFolder) || mkdir($userFolder)){
                $bmp = $folder.'/'.$data->enrollment_id.'/bmp';
                $txt = $folder.'/'.$data->enrollment_id.'/txt';

                if(is_dir($bmp) || mkdir($bmp) &&  is_dir($txt) || mkdir($txt)){
                    foreach ($data as $key => $value) {
                        if($key == 'enrollment_id' || $key == 'request') continue;

                        $bmpFile = $bmp . '/' . $key . '.bmp';
                        $txtFile = $txt . '/' . $key . '.txt';

                        $base64Img = base64_decode($value);

                        $image = \file_put_contents($bmpFile, $base64Img);
                        $files = \file_put_contents($txtFile, $value);

                        Res::send("Image successfully created <br>");
                    }
                }
                
            }
        }
    }
}
