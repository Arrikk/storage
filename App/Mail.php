<?php
namespace App;

use App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\Models\Settings;

/**
 * Mail
 * ==============CodeHart(Bruiz)==========
 * PHP V 7.4.8
 */

class Mail
{
    public static function mail($to, $from, $subject, $body)
    {
        
        // $config = Config::config();
        error_reporting(0);
        $mail = new PHPMailer(true);

        $mail->isSMTP();                     
        $mail->Host = (string) Settings::emailSetting()->smtp_host;
        $mail->Port = (int) Settings::emailSetting()->smtp_port; 
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = (string) Settings::emailSetting()->smtp_secure;
        $mail->Username   = (string) Settings::emailSetting()->smtp_username;   //SMTP username
        $mail->Password   = (string) Settings::emailSetting()->smtp_password;

        
        $mail->setFrom($from, (string) Settings::emailSetting()->mail_from);
        $mail->addAddress($to); 
        $mail->addReplyTo($from);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if(!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Send an email
     */
    public static function send($to, $subject, $body, $from){
       $mail = mail($to, $subject, $body, $from);
       if($mail){
           return true;
       }
     }
}