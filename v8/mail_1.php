<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'vendor/autoload.php';

// Generate OTP
$otp = rand(100000, 999999);  // 6-digit OTP
$email = 'sudha0819@gmail.com';  // Replace with recipient's email

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'factorenovations23@gmail.com';
    $mail->Password = 'cuqvonplawmksoyb';  // App Password, not your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(false);
    $mail->Subject = '🔐 Your OTP Code';
    $mail->Body = "Hello,\n\nYour one-time password (OTP) is: $otp\n\nPlease use it within 5 minutes to complete your verification.\n\nThank you,\nTeam EFACTOR";

    // Send email
    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'OTP sent via email', 'otp' => $otp]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"]);
}
?>
