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


$msg_delete_trash_stories = "";
$msg_optimize_database = "";

if (in_array($session_user_level, ['admin'])) {
    // Only if the user is ADMIN

    if ($process == "Yes Optimize") {
        $msg_optimize_database = "********* DB Optimize ********";
    }

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
$main_smarty->assign('msg_delete_trash_stories', $msg_delete_trash_stories);

// show the template
$main_smarty->assign('tpl_center', '/admin/home');
$main_smarty->display('/admin/admin.tpl');
