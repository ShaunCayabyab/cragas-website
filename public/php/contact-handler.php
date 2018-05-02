<?php

require '../../php/Mailer.php';

$post_data = [
    'name'    => $_POST['name'],
    'email'   => $_POST['email'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
];

$mailer = new Mailer();
$errors = $mailer->validateContactForm($post_data);

if ($errors) {
    echo json_encode(['errors' => $errors]);
} else {
    $email_to_sender    = $mailer->formatMessageToSender($post_data);
    $email_to_recipient = $mailer->formatMessageToRecipient($post_data);

    if ($ENVIRONMENT === 'PRODUCTION') {
        $sending_mail = $mailer->sendMail($email_to_sender, $email_to_recipient);

        if ($sending_mail instanceof Exception) {
            echo json_encode(['fail' => $sending_mail]);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => true]);
    }

}