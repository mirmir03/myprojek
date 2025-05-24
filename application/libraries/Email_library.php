<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/PHPMailer/src/PHPMailer.php';
require APPPATH . 'libraries/PHPMailer/src/SMTP.php';
require APPPATH . 'libraries/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email_library
{
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->initializeMailer();
    }

    private function initializeMailer()
    {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; // Your SMTP server host
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'penerbitumttesting@gmail.com'; // Your SMTP username
        $this->mail->Password = 'ytyqwzohukesyokr'; // Your SMTP password
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
    }

    public function sendPasswordResetEmail($recipientEmail, $resetToken)
    {
        try {
            $this->mail->addAddress($recipientEmail);
            $this->mail->Subject = 'Reset Kata Laluan';

            $timestamp = time(); // Get the current Unix timestamp

            // Add one hour (3600 seconds) to the PHP timestamp
            $timestampWithOneHour = $timestamp + 3600;

            // Convert the adjusted PHP timestamp to Oracle date format ('d-M-Y H:i:s')
            $expiration = date('d-m-Y H:i:s', $timestampWithOneHour);
            
            $this->mail->Body = "Reset Kata Laluan <br><br>Klik pautan untuk reset kata laluan: " . base_url('penerbit/login/resetPassword?token=' . $resetToken . "<br>Sila kemaskini kata laluan sebelum : " . $expiration . "<br><br><br>Sekian. Terima Kasih.");
            $this->mail->AltBody = 'Reset Kata Laluan: ' . base_url('penerbit/login/resetPassword?token=' . $resetToken);

            // Send email
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
}
