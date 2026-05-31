<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';

function send_verification($fullname, $email, $otp){

    $mail = new PHPMailer(true);

    try {

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'michaelramesis.villafuerte.cics@ust.edu.ph';
        $mail->Password   = 'xevq iyfa twsx njpe';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('michaelramesis.villafuerte.cics@ust.edu.ph', 'OTP Verification');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "OTP Verification";
        $mail->Body    = '<h3 style="color: #004aad; margin-bottom: 20px;">Hello, '.$fullname.'</h3>
        <p>Thank you for signing up at <strong>BR Staycation Condotel</strong>.</p>
        <p style="margin-top: 20px;">To complete your registration, please proceed to the OTP verification page and enter the code below to verify your email address.</p>
        <p>Verification code:</p>
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; font-size: 24px; color: #004aad; font-weight: bold;">
                '.$otp.' </div>
        <p style="margin-top:10px;font-size: 14px; color: #6c757d;">— BR Staycation Team</p>';

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}