<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require '../php/env.php';

/**
 * Validating the contact form data
 *
 * @param  array $form_data
 * @return array
 */
function validateContactForm(array $form_data) {
    $errors     = [];
    $string_exp = "/^[A-Za-z0-9 .'-]*$/";

    if (!$form_data['name'] || !$form_data['email'] || !$form_data['subject'] || !$form_data['message']) {
        return ['Please fill in all fields in the form.'];
    }
 
    if (!preg_match($string_exp, $form_data['name'])) {
        $errors[] = 'Please submit a Name with only letters, numbers, and spaces.';
    }
    
    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The Email Address you entered does not appear to be valid.';
    }

    if (!preg_match($string_exp, $form_data['subject']) || (strlen($form_data['subject']) < 2)) {
        $errors[] = 'Please submit a Subject of more than 2 characters with only letters, numbers, and spaces.';
    }

    if (strlen($form_data['message']) < 2) {
        $errors[] = 'Please submit a Message with more than 2 characters.';
    }

    return $errors;
}

/**
 * Formatting the email message to send to Sender
 *
 * @param  array  $form_data
 * @return array
 */
function formatMessageToSender($form_data) {
    $headers  = "MIME-Version: 1.0\r\n";

    $message  = str_replace("\n.", "\n..", $form_data['message']);

    $email    = "Hi " . $form_data['name'] . ", \n \n";
    $email   .= "Your message has been received - thanks! I will be taking a look at it shortly.\n \n";
    $email   .= "Message sent: \n \n";
    $email   .= "---------- \n";
    $email   .= $message . "\n";
    $email   .= "---------- \n \n";
    $email   .= "-Connor Ragas";


    return [
        'headers'    => $headers,
        'sender'     => $form_data['email'],
        'subject'    => 'Message sent: ' . $form_data['subject'],
        'message'    => $email,
    ];
}

/**
 * Formatting the email message to send to Recipient
 *
 * @param  array  $form_data
 * @return array
 */
function formatMessageToRecipient($form_data, $recipient) {
    $headers  = "MIME-Version: 1.0\r\n";

    $message  = str_replace("\n.", "\n..", $form_data['message']);

    $email    = "New message from " . $form_data['name'] . "(via connor-ragas.com): \n\n";
    $email   .= "---------- \n";
    $email   .= $message . "\n";

    return [
        'headers'    => $headers,
        'recipient'  => $recipient,
        'subject'    => 'New Contact Message: ' . $form_data['subject'],
        'message'    => $email,
    ];
}

/**
 * Sending the mail to Recipient and Sender
 *
 * @param  array  $form_data
 * @param  string $sender
 * @param  string $recipient
 * @return boolean
 */
function sendMail($sender, $recipient) {
    $to_sender    = new PHPMailer(true);
    $to_recipient = new PHPMailer(true);

    try {
        $to_sender->setFrom($recipient['recipient']);
        $to_sender->addAddress($sender['sender']);

        $to_sender->Subject = $sender['subject'];
        $to_sender->Body    = $sender['message'];

        $to_recipient->setFrom($recipient['recipient']);
        $to_recipient->addAddress($recipient['recipient']);

        $to_recipient->Subject = $recipient['subject'];
        $to_recipient->Body    = $recipient['message'];

        $to_sender->send();
        $to_recipient->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Running it all for the AJAX call
 */

$post_data = [
    'name'    => $_POST['name'],
    'email'   => $_POST['email'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
];

$errors = validateContactForm($post_data);

if ($errors) {
    echo json_encode(['errors' => $errors]);
} else {
    $email_to_sender = formatMessageToSender($post_data);
    $email_to_recipient = formatMessageToRecipient($post_data, $from_email);

    if ($environment === 'PRODUCTION') {
        if (!sendMail($email_to_sender, $email_to_recipient)) {
            echo json_encode(['fail' => true]);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => true]);
    }

}