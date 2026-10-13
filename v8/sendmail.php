<?php
$fromEmail = 'root@efactor.tech';
$fromName = 'exampleuser';
$toEmail = 'sudha0819@gmail.com';
$subject = 'Test mail from postfix';
$message = 'This is a test email from postfix.';

// Construct the sendmail command with sed
$sendmailCmd = 'echo "' . $message . '" | sed -e \'s/from: root <factorenovations23@gmail.com>/from: ' . $fromName . ' <' . $fromEmail . '/\' -e \'s/to:/to: ' . $toEmail . '/\' | sendmail -f ' . $fromEmail . ' ' . $toEmail;

// Execute the sendmail command
$output = shell_exec($sendmailCmd);

if ($output === null) {
    echo 'Failed to send the email.';
} else {
    echo 'Email sent successfully.';
}
?>
