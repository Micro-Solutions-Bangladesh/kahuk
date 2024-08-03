<?php
if (!defined('KAHUKPATH')) {
	die();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require KAHUKPATH_LIBS . 'phpmailer/src/Exception.php';
require KAHUKPATH_LIBS . 'phpmailer/src/PHPMailer.php';
require KAHUKPATH_LIBS . 'phpmailer/src/SMTP.php';

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
     * 
     */
    public function sendMe($data) {
        $mail = new PHPMailer();

        $mail->IsHTML(true);
        $mail->CharSet = 'utf-8';
        $mail->Subject = $data["subject"];
        $mail->Body = $data['message'];

        $mail->Debugoutput = $data["debug_output"];
        $mail->AddAddress($data["to_email"]);
        $mail->setFrom($data['from_email'], $data["from_email_name"]);
        // $mail->FromName = $data["from_email_name"];
        $mail->AddReplyTo($data["reply_to_email"]);

        if ($data["send_type"] == "smtp") {
            $mail->isSMTP();
            $mail->Host = $data["smtp_host"];
            // $mail->Host = "ssl://smtp.yandex.com:465";
            // $mail->Host = gethostbyname('smtp.yandex.com');
            $mail->SMTPAuth = true;
            $mail->Username = $data['from_email'];
            $mail->Password = $data["from_email_pass"];
            $mail->SMTPSecure = "ssl";
            $mail->SMTPDebug = $data["debug"];
        }
        
        //
        $output = "fail";

        if ($mail->Send()) {
            $output = "success";
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

    $default = [
        "smtp_host" => kahuk_get_smtp_host(),
        "from_email" => kahuk_contact_email_auto(),
        "reply_to_email" => kahuk_contact_email_auto(),
        "from_email_name" => kahuk_get_from_email_name(),
        "to_email" => "",
        "subject" => "",
        "message" => "",

        "send_type" => "smtp",
        "debug" => (($email_debug=="true") ? 1 : 0),
        "debug_output" => "error_log",
        "from_email_pass" => kahuk_get_smtp_pass(),
    ];

    $args = array_merge($default, $customArgs);

    if ($email_debug=="true") {
        kahuk_log_unexpected("EMAIL DEBUG :: " . print_r($args, true));
    }

    return $globalMailerObj->sendMe($args);
}
