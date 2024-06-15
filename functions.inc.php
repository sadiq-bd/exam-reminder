<?php

use PHPMailer\PHPMailer\PHPMailer;

function getRecipients($file) {
    $recipients = file_get_contents($file);
    $recipients = json_decode($recipients, true);

    return $recipients;
}

function en2bn($str) {
    return str_replace([
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ], [
        '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'
    ], $str);
}

function sendMail(string|array $to, string $subject, string $body) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = MAIL_USERNAME;                     //SMTP username
        $mail->Password   = MAIL_PASSWORD;                               //SMTP password
        $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'tls' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->CharSet = "UTF-8";
        $mail->Encoding = 'base64';

        //Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);

        if (is_array($to)) {
            foreach($to as $address) {
                $mail->addAddress($address);   
            }
        } else {
            $mail->addAddress($to);              
        }

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        if ($mail->send()) {
            return true;
        }

    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
    
}
