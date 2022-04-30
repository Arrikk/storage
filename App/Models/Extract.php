<?php
namespace App\Models;

use Core\Model;
use Core\Traits\Model as TraitsModel;

class Extract extends Model
{

    // Set table name
    static $table = 'extract';
    use TraitsModel;

    public static function extract()
    {
        $posts = Extract::find([
            '$' => ['id', 4],
            '$.type' => []
        ])->get();

        return $posts;
    }
    
}
