<?php

require '../../php/Mailer.php';

$mailer    = new Mailer();
$post_data = [
    'name'    => $_POST['name'],
    'email'   => $_POST['email'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
];

$errors = $mailer->validateContactForm($post_data);
echo ($errors) ? json_encode(['errors' => $errors]) : $mailer->sendMail($post_data);