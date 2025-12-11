<?php
// core/Email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

class Email {
    /**
     * Sends an OTP code to a recipient's email address.
     */
    public function sendOtp($recipientEmail, $otpCode) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            // Use OTP-specific credentials from .env
            $mail->Username   = $_ENV['OTP_USERNAME']; 
            $mail->Password   = $_ENV['OTP_PASSWORD']; 
            
            // Handle SMTP Secure setting
            $secure_env = $_ENV['SMTP_SECURE'] ?? 'tls';
            if ($secure_env === 'PHPMailer::ENCRYPTION_STARTTLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = $secure_env;
            }
            
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;
            
            // Recipients
            $fromEmail = $_ENV['EMAIL_DEFAULT_FROM'] ?? 'no-reply@icensus.com';
            $mail->setFrom($fromEmail, 'iCensus OTP');
            $mail->addAddress($recipientEmail);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'iCensus One-Time Password (OTP)';
            $mail->Body    = "
                <h2>Your One-Time Password</h2>
                <p>Use the following code to complete your login:</p>
                <h1 style='background-color: #eee; padding: 15px; border-radius: 5px; display: inline-block;'>{$otpCode}</h1>
                <p>This code will expire in 5 minutes.</p>
                <p>If you did not request this, please ignore this email.</p>
            ";
            $mail->AltBody = "Your One-Time Password is: {$otpCode}. This code will expire in 5 minutes.";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            if (function_exists('log_action') && isset($GLOBALS['db'])) {
                $userId = $_SESSION['2fa_user_id'] ?? 'Unknown';
                log_action('ERROR', 'OTP_SEND_FAIL', "Mailer Error for user ID #{$userId}: {$mail->ErrorInfo}.");
            }
            return false;
        }
    }
    
    /**
     * Sends a password reset link to a recipient's email address.
     */
    public function sendPasswordReset($recipientEmail, $username, $resetLink) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            // Use Reset-specific credentials from .env
            $mail->Username   = $_ENV['RESET_USERNAME']; 
            $mail->Password   = $_ENV['RESET_PASSWORD'];
            
            $secure_env = $_ENV['SMTP_SECURE'] ?? 'tls';
            if ($secure_env === 'PHPMailer::ENCRYPTION_STARTTLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = $secure_env;
            }
            
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;
            
            // Recipients
            $fromEmail = $_ENV['EMAIL_DEFAULT_FROM'] ?? 'no-reply@icensus.com';
            $mail->setFrom($fromEmail, 'iCensus Password Reset');
            $mail->addAddress($recipientEmail, $username);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'iCensus Password Reset Request';
            $mail->Body    = "
                <h2>Password Reset Request for {$username}</h2>
                <p>A password reset was requested for your iCensus account. Please click the button below to set a new password:</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <a href=\"{$resetLink}\" style='background-color: #0d6efd; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Reset My Password</a>
                </div>
                <p>This link will expire in 1 hour. If you did not request a password reset, please ignore this email.</p>
            ";
            $mail->AltBody = "Password Reset link for {$username}: {$resetLink}. This link will expire in 1 hour. If you did not request a password reset, please ignore this email.";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            if (function_exists('log_action') && isset($GLOBALS['db'])) {
                log_action('ERROR', 'PASSWORD_RESET_FAIL', "Mailer Error for reset link to {$recipientEmail}: {$mail->ErrorInfo}.");
            }
            return false;
        }
    }

    /**
     * Sends a support message from a user to the System Admin.
     */
    public function sendSupportMessage($adminEmail, $replyToEmail, $replyToName, $subject, $messageBody) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings (reuse OTP credentials or create new ENV vars for support)
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['OTP_USERNAME']; 
            $mail->Password   = $_ENV['OTP_PASSWORD']; 
            
            $secure_env = $_ENV['SMTP_SECURE'] ?? 'tls';
            if ($secure_env === 'PHPMailer::ENCRYPTION_STARTTLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = $secure_env;
            }
            
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;
            
            // Sender Info
            $fromEmail = $_ENV['EMAIL_DEFAULT_FROM'] ?? 'no-reply@icensus.com';
            $mail->setFrom($fromEmail, 'iCensus Support System');
            
            // The "Reply-To" is the user asking for help so you can reply directly to them
            $mail->addReplyTo($replyToEmail, $replyToName);
            
            // Recipient is the System Admin
            $mail->addAddress($adminEmail);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = "[Support Request] " . $subject;
            $mail->Body    = "
                <h3>New Support Request</h3>
                <p><strong>From:</strong> {$replyToName} ({$replyToEmail})</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <hr>
                <p><strong>Message:</strong></p>
                <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #0d6efd;'>
                    " . nl2br(htmlspecialchars($messageBody)) . "
                </div>
                <hr>
                <p><small>This email was sent from the iCensus About Page contact form.</small></p>
            ";
            $mail->AltBody = "From: {$replyToName} ({$replyToEmail})\n\nSubject: {$subject}\n\nMessage:\n{$messageBody}";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            if (function_exists('log_action') && isset($GLOBALS['db'])) {
                log_action('ERROR', 'SUPPORT_EMAIL_FAIL', "Mailer Error: {$mail->ErrorInfo}");
            }
            return false;
        }
    }
}