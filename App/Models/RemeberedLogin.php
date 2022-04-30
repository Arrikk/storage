<?php
namespace App\Models;
use PDO;
use \App\Token;
use \App\Models\User;
use Core\Traits\Model;

/**
 * Remember login model
 */

class RememberedLogin extends \Core\Model
{
    use Model; # Use trait only if using the find methods

    /**
     * Each model class requires a unique table base on field
     * @return string $table ..... the table name e.g 
     * (users, posts, products etc based on your Model)
     */
    public static $table = "remembered_login"; # declear table only if using traitModel
    public static $error = [];
    
    /**
     * Find a remembered login model by token
     * 
     * @param string the remembered token
     * 
     * @return mixed
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHashed();
        return static::findOne(['token_hash' => $token_hash]);
    }

    /** 
     * Get user associative with remembered login
     * 
     * @return User the user model
     */ 
    public function getUser()
    {
        return User::findById($this->id);
    }

    /**
     * Check if remember me token is still valid
     * 
     * @return bool
     */
    public function notExpired()
    {
        return strtotime($this->expires_at) > time();
    }

    /**
     * Delete login
     */
    public function delete()
    {
        return static::findAndDelete(['token_hash' => $this->token_hash]);
    }
}