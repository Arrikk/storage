<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);
    $mail = new PHPMailer();
    
    function mailer($mail, $from,$to,$subject, $msg){   
        $mail->isSMTP();                     
        $mail->Host = '';
        $mail->Port = 465; 
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        
        
        $mail->Username = '';
        $mail->Password = '';
        
        
        $mail->setFrom($from, 'sender');
        $mail->addAddress($to);     
        $mail->addReplyTo($from);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg;
        //$mail->Body = 'This is the body in plain text for non-HTML mail clients';
        
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }    
    }
    mailer($mail, 'noreply@art.com','olagunjuea1@gmail.com','Testing', 'Testing');
?>