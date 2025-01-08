<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Configure PHPMailer
        $this->mail->isSMTP();
        $this->mail->Host       = env('MAIL_HOST'); // e.g., smtp.gmail.com
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = env('MAIL_USERNAME');
        $this->mail->Password   = env('MAIL_PASSWORD');
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION'); // 'ssl' or 'tls'
        $this->mail->Port       = env('MAIL_PORT'); // e.g., 587 for TLS, 465 for SSL

        // Set sender email
        $this->mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true); // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}
