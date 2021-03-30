<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

/*Redwine: if Dashboard -> Settings -> Allow SMTP Testing? is set to true*/
if (allow_smtp_testing == 1) {
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = smtp_host;
    $mail->Port = smtp_port;
    $mail->SMTPAuth = true;
    $mail->Password = smtp_pass;
}
/*Redwine: to provision for the email entered in the subscribe to comments modules settings.*/
if (isset($fromEmail) && $fromEmail != '') {
    $mail->Username = $fromEmail;
    $mail->From = $fromEmail;
} else {
    $mail->Username = $main_smarty->get_config_vars('PLIKLI_PassEmail_From');
    $mail->From = $main_smarty->get_config_vars('PLIKLI_PassEmail_From');
}
$mail->FromName = $main_smarty->get_config_vars('PLIKLI_PassEmail_Name');
$mail->AddAddress($AddAddress);
$mail->AddReplyTo($main_smarty->get_config_vars('PLIKLI_PassEmail_From'));
$mail->IsHTML(true);
$mail->Subject = $subject;
$mail->CharSet = 'utf-8';
$mail->Body = $message;
