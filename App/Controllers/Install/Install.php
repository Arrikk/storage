<?php
namespace App\Controllers\Install;

use Core\View;
use PDO;

/**
 * Install CodeHart MVC
 * 
 * created By Bruiz (CodeHart)
 * 
 * PHP v7.4.8
 */

class Install extends \Core\Controller
{
    /**
     * Render Install Page
     */
    public function indexAction(){
        $step = $_GET['step'] ?? false;
        $stepCount = $_GET['step'] ?? 0;
        View::draw('Install/index.php', [
            'queryString' => $step,
            'step' => $stepCount,
        ], false);
    }

    /**
     * Config File
     */
    public function dbSetUpAction(){
        $_SESSION['db'] = $_POST;
        $message = '';
        if($this->connectDb() === false){
            $message =  "Db Connection Failed!";
        }else{

        if(isset($_POST['secretKey']) && !empty($_POST['secretKey'])):
            $message = 'Creating Config File';
            $file = fopen('App/Controllers/install/Config.php', 'w');
            fwrite($file, "<?php
namespace App;

/**
 * Config Settings
 * 
 * PHP version 7.4.8
 */

class Config
{
    /**
     * DB Host
     * 
     * @var string
     */
    CONST DB_HOST = '".$_POST['db-host']."';
    /**
     * DB name
     * 
     * @var string
     */
    CONST DB_NAME = '".$_POST['db-name']."';
    /**
     * DB username
     * 
     * @var string
     */
    CONST DB_USER = '".$_POST['db-user']."';
    /**
     * DB Password
     * 
     * @var string
     */
    CONST DB_PASSWORD =  '".$_POST['db-pass']."';
    /**
     * Error
     * 
     * @var bool
     */
    CONST SHOW_ERROR = false;
    /**
     * Base Url
     * 
     * @var string
     */
    CONST BASE_URL = '".$_POST['base-url']."';
    /**
     * Secret Key
     * 
     * @var string
     */
    CONST SECRET_KEY = '".$_POST['secretKey']."';
    /**
     * Show flash message
     * 
     * @var bool
     */
    CONST FLASH = true;
}
            
            ");
            if(fclose($file)){
                $message = 'true';
                $_SESSION['stp1'] = true;
            }
        endif;
            
    }
    echo $message;
    }

    /**
     * Public install db
     */
    public function connectDb(){
        $d = isset($_SESSION['db']) ? $_SESSION['db'] : [];

        error_reporting(0);
        // $con = mysqli_connect("".$d['db-host']."", "".$d['db-user']."", "".$d['db-pass']."", "".$d['db-name']."");
        $con = mysqli_connect($d['db-host'], $d['db-user'], $d['db-pass'], $d['db-name']);
        if($con)
            return $con;
        return false;
    }

    /**
     * Code Hart Index
     */
    public function setIndex(){
        $file = fopen('App/Controllers/Install/codeHart.php', 'w');
        fwrite($file, $this->generateIndex());
        fclose($file);
    }

    /**
     * Install
     */
    public function installAction(){
        if($this->installDb() === true){
            $this->setIndex();
            $configFile = file_get_contents('App/Controllers/Install/Config.php');
            $file = fopen('App/Config.php', 'w');
            fwrite($file, $configFile);

            if(fclose($file)){
                echo '<button id="completeInstallation">Complete</button>';
            }else{
                echo '<button id="" class="install btn-disabled">Try again</button>';
            }
        }else{
            echo "Something went Wromg";
        }
    }

    /**
     * Create Database tables
     */
    public function installDb(){
        $db = $this->connectDb();
        $success = true;

        $query = "CREATE TABLE IF NOT EXISTS users(
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `uniqId` VARCHAR(40) NULL DEFAULT NULL
            `name` VARCHAR(50) NULL DEFAULT NULL,
            `email` VARCHAR(255) NULL DEFAULT NULL,
            `phone` varchar(20) NULL DEFAULT NULL,
            `country` varchar(20) NOT NULL,
            `type` varchar(5) NOT NULL,
            `status` varchar(70) NOT NULL DEFAULT 'Pending',
            `is_active` int(11) NOT NULL DEFAULT 0,
            `user_profile` text NOT NULL DEFAULT '/Public/assets/img/90x90.jpg',
            `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
            `password_reset_hash` VARCHAR(64) NULL DEFAULT NULL,
            `password_reset_expiry` DATETIME NULL DEFAULT NULL,
            `created_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY(id),
            UNIQUE KEY(email)
        ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";

        if($db->query($query)){
            $success = false;
        }

        $query = "CREATE TABLE IF NOT EXISTS settings (
           id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
           `name` int(11) NOT NULL,
           `value` json NOT NULL,
           `status` varchar(50) NOT NULL DEFAULT 'active',
           created_at datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        if($db->query($query)){
            $success = false;
        }

        $query = "CREATE TABLE IF NOT EXISTS to_verify (
           verify_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
           to_verifier int(11) NOT NULL,
           from_student int(11) NOT NULL,
           file text NOT NULL,
           documentName varchar(50) NOT NULL,
           institution int(11) NOT NULL,
           institutionType varchar(20) NOT NULL,
           validity datetime NOT NULL,
           status varchar(10) NOT NULL,
            PRIMARY KEY (verify_id)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if($db->query($query)){
            $success = false;
        }


        $query = "CREATE TABLE IF NOT EXISTS remembered_logins(
            token_hash  VARCHAR (11) NOT NULL DEFAULT '',
            user_id INT(50) UNSIGNED NOT NULL DEFAULT 0,
            expires_at DATETIME,
            PRIMARY KEY(token_hash),
            UNIQUE KEY(user_id)
        ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
        if($db->query($query)){
            $success = false;
        }

        if($success === false){
           return true; 
        }
    }

    /**
     * Clean
     */
    public function cleanLogsAction(){

        $newPublic = file_get_contents('App/Controllers/Install/codeHart.php');
        $file = fopen('index.php', 'w');
        fwrite($file, $newPublic);
        fclose($file);

        //Remove Installation setup Sytles and scripts
        $install = scandir('Public/install');
        foreach($install as $file){
            if($file === '.' || $file == '..'){
                continue;
            }else{
                if(file_exists('Public/install'.$file)){
                    unlink('Public/install'.$file);
                    rmdir('Public/install');
                }
            }
        }

        // Remove installation View/html Files
        $files = scandir('App/Views/install');
        
        foreach($files as $file){
            if($file === '.' || $file == '..'){
                continue;
            }else{
                if(file_exists('App/Views/install/'.$file)){
                    unlink('App/Views/install/'.$file);
                    rmdir('App/Views/install');
                }
            }
        }
        
        // Clean installation Logs
        $files = scandir('App/Controllers/Install');
        foreach($files as $file){
            
            if($file == '.' || $file == '..'){
                continue;
            }else{
                if(file_exists('App/Controllers/Install/'.$file)){
                    echo 'App/Views/install/'.$file.'<br>';
                    unlink('App/Controllers/Install/'.$file);
                    // rmdir('App/Controllers/install');
                }
            }
        }
        // echo 'User';
    }

    public function generateIndex(){
        $data= '<?php
/**
 * Index Page
 * 
 * Created By Bruiz(@~codeHart~) 2022
 * 
 * PHP Version 7.4.8
 */

/**
 * Autoload
 */
require \'Vendor/autoload.php\';

/**
 * Twig
 */
Twig_Autoloader::register();

/**
 * Error
 */
error_reporting(E_ALL);
set_error_handler(\'Core\Error::errorHandler\');
set_exception_handler(\'Core\Error::exceptionHandler\');

/**
 * Session
 */
session_start();

/**
 * Add route to the Routing Table
*/

$router = new Core\Router;

$router->add(\'\', [\'controller\' => \'home\', \'action\' => \'home\']);
$router->add(\'{controller}/{action}\');

// Execute
$url = ltrim(rtrim($_SERVER[\'QUERY_STRING\']));
$router->dispatch($url);

// echo \'<pre>\';
// echo htmlspecialchars(print_r($_COOKIE));
// echo \'</pre>\';
        
        ';
        return $data;
    }

}