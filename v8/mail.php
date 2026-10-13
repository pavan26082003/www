<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include required files
require 'vendor/autoload.php';
include('config.php'); // Ensure this sets up $db correctly

// Set recipient email
$recipient_email = 'factorenovations@yahoo.com';

echo "Mail daemon started...\n";

while (true) {
    try {
        // Check for unread chat messages in call_service table
        $chat_query = "SELECT * FROM `call_service` WHERE status='unread' LIMIT 1";  
        $chat_row = $db->get_row($chat_query);

        if ($chat_row) {
            $from_userid = $chat_row['from_userid'];
            $id = $chat_row['id'];
            $chat_message = $chat_row['message'] ?? 'No message content';

            // Prepare mail
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'factorenovations23@gmail.com';
            $mail->Password = 'cuqvonplawmksoyb'; // Gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
            $mail->addAddress($recipient_email);

            $mail->isHTML(false);
            $mail->Subject = 'New Chat Notification from APP';
            $mail->Body = "Hello,\n\nYou have a new chat message from user ID: $from_userid\n\nMessage Content:\n$chat_message\n\nPlease check your dashboard.\n\n- Team EFACTOR";

            $mail->send();

            // Mark the message as read to prevent repeated emails
            $updateQuery = "UPDATE `call_service` SET status='read' WHERE id='$id'";
            $db->query($updateQuery);

            echo "Chat notification sent for message ID: $id\n";
        }

        // Check for unread mail messages in message table
        $mail_query = "SELECT * FROM `messages` WHERE status='unread' LIMIT 1";  
        $mail_row = $db->get_row($mail_query);

        if ($mail_row) {
            $from_user_id = $mail_row['from_user_id']; // Assuming there's an email field
            $id = $mail_row['id'];
            $subject = $mail_row['subject'] ?? 'No Subject';
            $mail_message = $mail_row['message'] ?? 'No message content';

            // Prepare mail
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'factorenovations23@gmail.com';
            $mail->Password = 'cuqvonplawmksoyb'; // Gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
            $mail->addAddress($recipient_email);

            $mail->isHTML(false);
            $mail->Subject = 'New Email Notification from Website';
            $mail->Body = "Hello,\n\nYou have a new email message from: $from_user_id\nSubject: $subject\n\nMessage Content:\n$mail_message\n\nPlease check your dashboard.\n\n- Team EFACTOR";

            $mail->send();

            // Mark the message as read to prevent repeated emails
            $updateQuery = "UPDATE `messages` SET status='read' WHERE id='$id'";
            $db->query($updateQuery);

            echo "Email notification sent for message ID: $id\n";
        }

    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}\n";
    }

    // Wait for 10 seconds before checking again
    sleep(10);
}