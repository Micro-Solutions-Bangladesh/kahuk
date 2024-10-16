<?php
if (!defined('KAHUKPATH')) {
	die();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require KAHUKPATH_LIBS . 'PHPMailer/src/Exception.php';
require KAHUKPATH_LIBS . 'PHPMailer/src/PHPMailer.php';
require KAHUKPATH_LIBS . 'PHPMailer/src/SMTP.php';

/**
 * Initializes KahukMailer Class
 *
 * @return KahukMailer
 */
function kahuk_mailer_init() {
    return KahukMailer::init();
}

/**
 * 
 */
class KahukMailer
{
    public $toEmail;
    public $fromEmail;
    public $replyToEmail;
    public $emailSubject;

    public $sendType;


    /**
     * Class construcotr
     */
    private function __construct() {
        //
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Send Email using SMTP
     * 
     * @return string success | fail
     */
    public function sendSMTP($data) {
        $output = "fail";

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = 2;                      //Enable verbose debug output
            $mail->SMTPDebug = $data["debug"];
            $mail->Debugoutput = $data["debug_output"];
            $mail->isSMTP(); //Send using SMTP
            // $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
            $mail->Host       = $data["smtp_host"];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            // $mail->Username   = 'user@example.com';                     //SMTP username
            $mail->Username   = $data["from_email"];                     //SMTP username
            $mail->Password   = $data["from_email_pass"];                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            // $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->Port       = $data["smtp_port"];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($data['from_email'], $data["from_email_name"]);
            // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $mail->addAddress($data["to_email"]);               //Name is optional
            $mail->addReplyTo($data["reply_to_email"], 'No Reply');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $data["subject"];
            $mail->Body    = $data["message"];
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            $output = "success";
            // echo 'Message has been sent';
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            kahuk_log_unexpected("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        return $output;
    }

    /**
     * Not Used anywhere Need to Work on it.
     */
    public function sendMe($data) {
        $mail = new PHPMailer();

        $mail->isHTML(true);
        $mail->CharSet = 'utf-8';
        $mail->Subject = $data["subject"];
        $mail->Body = $data['message'];

        $mail->Debugoutput = $data["debug_output"];
        $mail->AddAddress($data["to_email"]);
        $mail->setFrom($data['from_email'], $data["from_email_name"]);
        // $mail->FromName = $data["from_email_name"];
        $mail->AddReplyTo($data["reply_to_email"]);

        $mail->Username = $data['from_email'];

        if ($data["send_type"] == "smtp") {
            $mail->isSMTP();
            $mail->Host = $data["smtp_host"];
            // $mail->Host = "ssl://smtp.yandex.com:465";
            // $mail->Host = gethostbyname('smtp.yandex.com');
            $mail->SMTPAuth = true;
            // $mail->Username = $data['from_email'];
            $mail->Password = $data["from_email_pass"];
            $mail->SMTPSecure = "ssl";
            $mail->SMTPDebug = $data["debug"];
        } else {
            $mail->isMail();
            $mail->Mailer = 'sendmail';
        }


        //
        $output = "";

        try {
            if ($mail->Send()) {
                $output = "success";
            }
        } catch (Exception $e) {
            $output = "fail";
            kahuk_log_unexpected("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        return $output;
    }
}

// Set the global variables to access mailer class
$globalMailerObj = kahuk_mailer_init();


/**
 * 
 */
function kahuk_get_smtp_host() {
    global $hooks;

    $output = kahuk_get_config("_smtp_host");

    return $hooks->apply_filters("smtp_host", $output);
}

/**
 * 
 */
function kahuk_get_smtp_port() {
    global $hooks;

    $output = kahuk_get_config("_smtp_port");

    return $hooks->apply_filters("smtp_port", $output);
}

/**
 * 
 */
function kahuk_get_smtp_pass() {
    global $hooks;

    $output = kahuk_get_config("_smtp_pass");

    return $hooks->apply_filters("smtp_pass", $output);
}

/**
 * 
 */
function kahuk_contact_email_auto() {
    global $hooks;

    $output = kahuk_get_config("_site_email_auto");

    return $hooks->apply_filters("email_auto", $output);
}

/**
 * 
 */
function kahuk_contact_email_noreply() {
    global $hooks;

    $output = kahuk_get_config("_site_email_noreply");

    return $hooks->apply_filters("email_noreply", $output);
}

/**
 * 
 */
function kahuk_get_from_email_name() {
    global $hooks;

    return $hooks->apply_filters("from_email_name", "Kahuk");
}

/**
 * 
 */
function kahuk_send_email($customArgs = []) {
    global $globalMailerObj;

    $email_debug = kahuk_get_config("_email_debug");
    $force_debug = (isset($customArgs["force_debug"]) ? $customArgs["force_debug"] : false);

    $debug_enable = (($email_debug == "true") || $force_debug) ? true : false;

    $send_type = ($customArgs["send_type"] == "sendmail") ? "sendmail" : "smtp";

    $args = [
        "send_type" => $send_type,

        "to_email" => $customArgs["to_email"],
        "subject" => $customArgs["subject"],
        "message" => $customArgs["message"],

        "from_email" => kahuk_contact_email_auto(),
        "reply_to_email" => kahuk_contact_email_noreply(),
        "from_email_name" => kahuk_get_from_email_name(),

        "debug" => ($debug_enable ? 2 : 0),
        "debug_output" => "error_log",
    ];

    $output = "";

    if ($send_type == "smtp") {
        $args["smtp_host"] = kahuk_get_smtp_host();
        $args["smtp_port"] = kahuk_get_smtp_port();
        $args["from_email_pass"] = kahuk_get_smtp_pass();

        $output = $globalMailerObj->sendSMTP($args);
    } else {
        $output = $globalMailerObj->sendMe($args);
    }

    if ($debug_enable) {
        kahuk_log_unexpected("EMAIL DEBUG :: " . print_r($args, true));
    }

    return $output;
}
