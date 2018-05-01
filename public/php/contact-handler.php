<?php

require_once('../php/env.php');

/**
 * Validating the contact form data
 *
 * @param  array $form_data
 * @return array
 */
function validateContactForm(array $form_data) {
    $errors     = [];
    $email_exp  = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    $string_exp = "/^[A-Za-z .'-]+$/";

    if (!$form_data['name'] || !$form_data['email'] || !$form_data['subject'] || !$form_data['message']) {
        return ['Please fill in all fields in the form.'];
    }
 
    if (!preg_match($string_exp, $form_data['name'])) {
        $error_message[] = 'The Name you entered does not appear to be valid.';
    }
    
    if (!preg_match($email_exp, $form_data['email'])) {
        $error_message[] = 'The Email Address you entered does not appear to be valid.';
    }

    if (!preg_match($string_exp,$form_data['subject']) || (strlen($form_data['subject']) < 2)) {
        $error_message[] = 'The Subject you entered does not appear to be valid.';
    }

    if (strlen($form_data['message']) < 2) {
        $error_message[] = 'The Message you entered does not appear to be valid';
    }

    return $errors;
}

/**
 * Formatting the email message
 *
 * @param  array $form_data
 * @return array
 */
function formatMessage(array $form_data) {

}

$post_data = [
    'name'    => $_POST['name'],
    'email'   => $_POST['email'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
];

if ($errors = validateContactForm($post_data)) {
    echo json_encode(['errors' => $errors]);
} else {
    $headers = "From: " . $post_data['name'];

    if ($environment === 'PRODUCTION') {
        mail($post_data['email'], $post_data['subject'], $post_data['message'], $headers);
    }
    echo json_encode(['success' => true]);
}