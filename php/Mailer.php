<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require 'env.php';

/**
 * Mailer class
 */
class Mailer {

    private $environment;
    private $mail_authenticator;
    private $auto_sender;
    private $recipient;

    /**
     * Constructor function
     */
    function __construct() {
        $this->environment = getenv("ENVIRONMENT");

        $this->auto_sender = [
            'name'  => getenv("AUTO_MAIL_NAME"),
            'email' => getenv("AUTO_MAIL_EMAIL"),
        ];

        $this->recipient = [
            'name'  => getenv("RECIPIENT_NAME"),
            'email' => getenv("RECIPIENT_EMAIL"),
        ];

        $this->mail_authenticator = [
            'host'     => getenv("MAIL_CLIENT_HOST"),
            'username' => getenv("MAIL_CLIENT_USERNAME"),
            'password' => getenv("MAIL_CLIENT_PASSWORD"),
            'outgoing' => getenv("MAIL_CLIENT_OUTGOING"),
        ];
    }

    /**
     * Validating the contact form data
     *
     * @param  array $form_data
     * @return array
     */
    public function validateContactForm(array $form_data) {
        $errors     = [];
        $string_exp = "/^[A-Za-z0-9 .'-]*$/";

        if (!$form_data['name'] || !$form_data['email'] || !$form_data['subject'] || !$form_data['message']) {
            return ['Please fill in all fields in the form.'];
        }
     
        if (!preg_match($string_exp, $form_data['name'])) {
            $errors[] = 'Please submit a Name with only letters, numbers, and spaces.';
        }

        if (strlen($form_data['name']) > 30) {
            $errors[] = 'Please keep the Name under 30 characters.';
        }
        
        if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'The Email Address you entered does not appear to be valid.';
        }

        if (!preg_match($string_exp, $form_data['subject']) || (strlen($form_data['subject']) < 2)) {
            $errors[] = 'Please submit a Subject of more than 2 characters with only letters, numbers, and spaces.';
        }

        if (strlen($form_data['subject']) > 50) {
            $errors[] = 'Please keep the Subject under 50 characters.';
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
    private function formatMessageToSender($form_data) {
        $message  = str_replace("\n.", "\n..", $form_data['message']);

        $email    = "(This email address is from an automated system. Any mail send to this address will not be received.) \n \n";
        $email   .= "Hi " . $form_data['name'] . ", \n \n";
        $email   .= "Your message has been received - thanks! I will be taking a look at it shortly.\n \n";
        $email   .= "Message sent: \n \n";
        $email   .= "---------- \n";
        $email   .= $message . "\n";
        $email   .= "---------- \n \n";
        $email   .= "-Connor Ragas";


        return [
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
    private function formatMessageToRecipient($form_data) {
        $message  = str_replace("\n.", "\n..", $form_data['message']);

        $email    = "New message from " . $form_data['name'] . " (via connor-ragas.com): \n\n";
        $email   .= "---------- \n";
        $email   .= $message . "\n";

        return [
            'recipient'  => $this->recipient['email'],
            'subject'    => 'New Contact Message: ' . $form_data['subject'],
            'message'    => $email,
        ];
    }

    /**
     * Sending the mail to Recipient and Sender
     *
     * @param  array   $form_data
     * @return JSON
     */
    public function sendMail($form_data) {
        $sender    = $this->formatMessageToSender($form_data);
        $recipient = $this->formatMessageToRecipient($form_data);

        $to_sender    = $this->setMailAuthentication(new PHPMailer(true));
        $to_recipient = $this->setMailAuthentication(new PHPMailer(true));

        try {
            $to_sender->setFrom($this->auto_sender['email'], $this->auto_sender['name']);
            $to_sender->addAddress($sender['sender']);

            $to_sender->Subject = $sender['subject'];
            $to_sender->Body    = $sender['message'];

            $to_recipient->setFrom($this->auto_sender['email'], $this->auto_sender['name']);
            $to_recipient->addAddress($recipient['recipient']);

            $to_recipient->Subject = $recipient['subject'];
            $to_recipient->Body    = $recipient['message'];

            if ('PRODUCTION' === $this->environment) {
                $to_sender->send();
                $to_recipient->send();
            }

            return json_encode(['success' => true]);
        } catch (Exception $e) {
            return ('PRODUCTION' === $this->environment) ? json_encode(['fail' => false]) : json_encode(['fail' => $e]);
        }
    }

    /**
     * Setting up the SMTP authentication for mailing
     * @param  PHPMailer $mail
     * @return PHPMailer
     */
    private function setMailAuthentication(PHPMailer $mail) {
        $mail->SMTPDebug  = ('PRODUCTION' === $this->environment) ? 0 : 2;
        $mail->isSMTP();
        $mail->Host       = $this->mail_authenticator['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->mail_authenticator['username'];
        $mail->Password   = $this->mail_authenticator['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $this->mail_authenticator['outgoing'];

        return $mail;
    }
}