<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');

include(KAHUKPATH_LIBS.'smartyvariables.php');
include(KAHUKPATH_LIBS.'csrf.php');

check_referrer();

force_authentication();

if (!in_array($session_user_level, ['admin','moderator'])) {
    die(".");
}

$CSRF = new csrf();

// pagename
define('pagename', 'admin_index');

//
$process = sanitize_text_field(_post('submit'));


$msg_test_or_send_email = "";
$msg_delete_trash_stories = "";
$msg_optimize_database = "";

if (in_array($session_user_level, ['admin'])) {
    // Only if the user is ADMIN

    if ($process == "Yes Optimize") {
        $msg_optimize_database = "********* TODO DB Optimize ********";
    }

    //
    if ($process == "Send Email") {
        $messages = [];

        $email_send_reason = sanitize_text_field(_post('email_send_reason'));
        $email_receiver = sanitize_email(_post('email_receiver'));
        $email_massage = kahuk_kses(nl2br(_post("email_massage")), "<p><br><b>");

        if (empty($email_send_reason) || empty($email_receiver) || empty($email_massage)) {
            $messages[] = [
                "msg" => "Required information not found to send email!",
                "msgtype" => "error",
            ];
        } else {
            $subject = sanitize_text_field(_post('subject'));
            $send_type = sanitize_text_field(_post('send_type'));

            $data = [
                "to_email" => $email_receiver,
                "subject" => $subject,
                "message" => $email_massage,
                "force_debug" => true,
                "send_type" => $send_type,
            ];

            $isMailSent = kahuk_send_email($data);

            if ($isMailSent == "success") {
                $messages[] = [
                    "msg" => "Email Send Successful!",
                    "msgtype" => "info",
                ];
            } else {
                $messages[] = [
                    "msg" => "Failed to Send Email!",
                    "msgtype" => "error",
                ];
            }
        }

        $msg_test_or_send_email = kahuk_markup_messages($messages);
    }

    // 
    if ($process == "Yes Delete") {
        $rs = kahuk_delete_stories_by_status("trash");

        if (sanitize_number($rs["total_deleted"]) > 0) {
            $messages = [];

            foreach($rs["success"] as $rows) {
                $messages[] = [
                    "msg" => "Story [" . $rows["link_id"] . "]: " . $rows["link_title"] . " deleted.",
                    "msgtype" => "success",
                ];
            }

            foreach($rs["error"] as $rows) {
                $messages[] = [
                    "msg" => "Story [" . $rows["link_id"] . "]: " . $rows["link_title"] . " not deleted.",
                    "msgtype" => "warning",
                ];
            }

            
        } else {
            $messages[] = [
                "msg" => "No story found in the trash to delete!",
                "msgtype" => "info",
            ];
        }

        $msg_delete_trash_stories = kahuk_markup_messages($messages);
    }
}

// $CSRF->create('delete_trash_stories', true, true);




$main_smarty->assign('msg_optimize_database', $msg_optimize_database);
$main_smarty->assign('msg_test_or_send_email', $msg_test_or_send_email);
$main_smarty->assign('msg_delete_trash_stories', $msg_delete_trash_stories);

// show the template
$main_smarty->assign('tpl_center', '/admin/home');
$main_smarty->display('/admin/admin.tpl');
