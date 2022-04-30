<?php

namespace App\Models;

use App\Token;
use Core\Traits\Model;
use Core\Http\Res;

/**
 * User model
 *
 * PHP version 7.4.8
 */
class User extends \Core\Model
{
    use Model; # Use trait only if using the find methods

    /**
     * Each model class requires a unique table base on field
     * @return string $table ..... the table name e.g 
     * (users, posts, products etc based on your Model)
     */
    public static $table = "users"; # declear table only if using traitModel
    public static $error = [];

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * ************************************************************************ 
     * **(Use ClassName/self/static::find() to find many)********************** 
     * **(Use ClassName/self/static::findOne() to find one)********************
     * **(Use ClassName/self/static::findAndUpdate() to find and update)*******
     * **(Use ClassName/self/static::findAndDelete() to find and delete)*******
     * **(Use ClassName/self/static::findAndByEmail() to find by email)********
     * **(Use ClassName/self/static::findAndById() to find by Id*******)*******
     * ************************************************************************ 
     */
    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $token = new Token();
        $this->hashed = $token->getHashed();
        $this->token = $token->getValue();

        $this->validate();

        if (empty($this->errors)) {

            $password = password_hash($this->password, PASSWORD_DEFAULT);
            $this->time = date('y-m-d H:i:s', time() + 60 * 60 * 3);
            $user = static::dump([
                'email' => static::clean($this->email),
                'username' => static::clean($this->username),
                'ip' => Res::ip(),
                'password_hash' => $password,
                'password_reset_hash' => $this->hashed,
                'password_reset_expiry' => $this->time
            ]);
            if(!$user) return Res::status(500)->send('Server Error');
            return $user;
        };
        return Res::json($this->errors);
    }
     /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    protected function validate()
    {
        if ($this->email == '')
            $this->errors[] = 'Email is required';

        if ($this->emailExists($this->email, $this->id ?? null))
            $this->errors[] = 'Email already exists';

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            $this->errors[] = 'Invalid Email';

        if (isset($this->password) && !empty($this->password)) {
            if ($this->password == '')
                $this->errors[] = 'Password cannot be empty';
            if (!preg_match('/.*\d+.*/', $this->password))
                $this->errors[] = 'Password Must contain atleast a number';
        }

        return true;
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = self::findByEmail($email);
        if ($user) :
            if ($user->id !== $ignore_id) {
                return true;
            }
        endif;
        return false;
    }

    /**
     * @return object
     */
    public static function authenticate(array $array = [])
    {
        extract($array);
        $user = User::findByEmail($email);
        if (!$user) return Res::status(400)->json(['Invalid Email Address']);
        if (!password_verify($password, $user->password_hash))return Res::status(400)->json(['Password Mismatch']);
        return $user;
    }

    public static function getUser($id)
    {
        return User::findById($id);
    }// ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Update Account =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    public function put(array $update = [])
    {
        extract($update);
        $this->email = $email;
        $this->validate();
        
        # Set update pref according to form data

        if(empty($this->errors))
            return User::findAndUpdate(['id' => $this->id], $update);
            return Res::status(400)->json($this->errors);
    }

    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Save Remembered Login =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $token_hash = $token->getHashed();
        $this->token_value = $token->getValue();
        $this->expiry = time() + 60 * 60 * 24 * 30;

        User::dump([
            'token_hash' => $token_hash,
            'user_id' => $this->user_id,
            'expires_at' => date('Y-m-d H:i:s', $this->expiry)
        ], 'remembered_logins');
        return Res::send(true);
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Password Reset Starts =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Verify user email to send Reset link
     * 
     * @param string $email user email
     */
    public static function sendPasswordReset($email)
    {
        $user = User::findByEmail($email);
        if (!$user) return Res::status(401)->json('User does not exist');
        $user->startPasswordReset();
        if (!$user->forgotEmail()) return Res::status(400)->json('Unable to send verification email');
        return Res::json('Email Successfully sent');
    }

    /**
     * Start password reset by generating a new token and expiry
     * 
     * @return mixed
     */
    public function startPasswordReset()
    {
        $token = new Token();
        $token_hash = $token->getHashed();
        $this->token = $token->getValue();

        $expiry = time() + 60 * 60 * 2;
        return User::findAndUpdate(
            ['id' => $this->id],
            [
                'password_reset_hash' => $token_hash,
                'password_reset_expiry' => date('Y-m-d H:i:s', $expiry)
            ]
        );
    }

    /**
     * Find user Model by token
     * 
     * @param string $token User token
     * 
     * @return mixed
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHashed();
        $user = static::findOne([
            'password_reset_hash' => $token_hash
        ]);
        if (!$user) return false;

        if (strtotime($user->password_reset_expiry) > time()) {
            return $user;
        }
    }

    /**
     * Verify  Password 
     * 
     * @return mixed
     */
    public function verifyPassword($password)
    {
        if (\password_verify($password, $this->password_hash)) {
            return true;
        }
        return false;
    }

    /**
     * Reset account Password
     * 
     * @param string $password New password
     * 
     * @return void
     */
    public function resetPassword($password)
    {
        $this->password = $password;
        // $this->validate();
            $password = password_hash($this->password, PASSWORD_DEFAULT);

            $success =  static::findAndUpdate(
                ['id' => $this->id], 
                [
                'password_hash' => $password,
                'password_reset_hash' => NULL,
                'password_reset_expiry' => NULL
                ]
            );
            if(!$success) return Res::status(400)->json('server Error');
            Res::json('Password Successfully Changed');

    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // ================= Email Activation Processes ==================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Send email activation link
     * 
     * @return void
     */
    public function sendEmailActivation()
    {
        $token = new Token();
        $this->token = $token->getValue();
        $this->hashed = $token->getHashed();
        $this->expiry = date('y-m-d H:i:s', time() + 60 * 5);
        if ($activation = $this->startEmailReset()) {
            if ($this->activationEmail()) {
                return Res::send(true);
            }
        }
    }

    /**
     * Start Email activation process 
     * 
     * @return bool
     */
    protected function startEmailReset()
    {
        return static::findAndUpdate([
            'id', $this->id],[
            'password_reset_hash' => $this->hashed,
            'password_reset_expiry' => $this->expiry
        ]);
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // ==================== Send Email Templates =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    protected function activationEmail()
    {
        $to = $this->email;
        $from = \App\Models\Settings::emailSetting()->smtp_username;
        $subject = 'Verify you want to use this emaill address';
        $body = \Core\View::template('emailTemplates/emails_activate.html', [
            'email' => $this->email,
            'token' => $this->token,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }
    protected function welcomeEmail()
    {
        $to = $this->email;
        $from = \App\Models\Settings::emailSetting()->smtp_username;
        $subject = 'Thank you for signing up';
        $body = \Core\View::template('emailTemplates/emails_welcome.html', [
            'email' => $this->email,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }
    protected function forgotEmail()
    {
        $to = $this->email;
        $from = \App\Models\Settings::emailSetting()->smtp_username;
        $subject = 'Reset Account Password';
        $body = \Core\View::template('emailTemplates/emails_forgot.html', [
            'email' => $this->email,
            'token' => $this->token,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }




    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
}
