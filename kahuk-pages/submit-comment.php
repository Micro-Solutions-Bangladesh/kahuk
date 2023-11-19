<?php
force_authentication("Please login to submit a comment.");

$enable_comments = kahuk_get_config('_enable_comments');
// $main_smarty->assign('enable_comments', $enable_comments);
$max_no_char_comment = kahuk_get_config('_max_no_char_comment');

//
$process = sanitize_text_field(_post('process'));
$user_id = sanitize_number(_post('user_id'));
$parrent_id = sanitize_number(_post('parrent_id', 0));
$comment_id = sanitize_number(_post('comment_id'));


if ($process == 'submit-comment') {
    $hasError = false;
    $saveAbleData = [];

    //
    $saveAbleData['comment_parent'] = $parrent_id;

    if ($session_user_id == $user_id) {
        $saveAbleData['comment_user_id'] = $user_id;
    } else {
        $hasError = true;
        kahuk_set_session_message(
            $main_smarty->get_config_vars("Invalid user action!"),
            "error"
        );
    }

    //
    $saveAbleData['story_id'] = sanitize_number(_post('story_id'));

    //
    $comment_content = kahuk_sanitize_comment(_post('comment_content'));

    if ($comment_content) {
        $saveAbleData['comment_content'] = $comment_content;
    } else {
        $hasError = true;
        kahuk_set_session_message(
            $main_smarty->get_config_vars("Comment is empty!"),
            "warning"
        );
    }

    //
    $saveAbleData['comment_status'] = 'published';

    // Verify submit via action hook
    $verify = $hooks->apply_filters(
        "submit_verify",
        ['data' => $saveAbleData, 'success' => true]
    );

    if (isset($verify["success"]) && ($verify["success"] == false)) {
        $hasError = true;
        kahuk_set_session_message(
            $verify["message"],
            "warning"
        );
    }

    // Redirect if there any error found
    if ($hasError) {
        kahuk_redirect(kahuk_get_permalink());
        exit;
    }

    $saveAbleData = $verify['data'];

    // if ($comment_id>0) {
    //     // TODO edit comment
    // } else {

    // }

    $output = kahuk_save_comment($saveAbleData, $comment_id);

    if ($output['success']) {
        kahuk_set_session_message(
            $output["message"],
            "success"
        );
    } else {
        kahuk_set_session_message(
            $output["message"],
            "warning"
        );
    }

    $story_url = kahuk_permalink_story($saveAbleData['story_id']);
    kahuk_redirect($story_url);
} else {
    kahuk_redirect();
}
