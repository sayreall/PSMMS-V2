<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string &$error = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? '';
            $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAutoTLS = true;
            $mail->Timeout = 20;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $fromEmail = $_ENV['MAIL_FROM'] ?? $mail->Username;
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'PSMMS Dashboard';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName ?: $toEmail);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = trim(preg_replace('/\s+/', ' ', strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody))) ?? '');

            return $mail->send();
        } catch (Exception $e) {
            $error = $e->getMessage();
            return false;
        }
    }
}
