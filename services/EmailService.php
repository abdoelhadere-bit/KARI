<?php
declare(strict_types=1);

namespace services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    public function send(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'abdoelhadere3@gmail.com';
            $mail->Password   = 'bnxraxinpnkyuduq'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email
            $mail->setFrom('abdoelhadere3@gmail.com', 'KARI');
            $mail->addAddress($to);

            $mail->isHTML(false); 
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (Exception $e) {
            throw new \RuntimeException("Erreur envoi email: " . $mail->ErrorInfo);
        }
    }
}
