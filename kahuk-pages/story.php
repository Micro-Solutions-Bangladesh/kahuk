<?php
$story = $globalStory;
$link_id = $story['link_id'];

// Check if the story status is valid
$stroyStatusesPublic = kahuk_story_statuses();

if (!kahuk_check_user_role("admin") && !in_array($story["link_status"], $stroyStatusesPublic)) {
    kahuk_set_session_message(
        "Story not exist!",
        "info"
    );

    kahuk_redirect();
}


$group_by_reactions = kahuk_get_reactions_group_by_reaction($link_id);
$main_smarty->assign('group_by_reactions', $group_by_reactions);
// print_r($group_by_reactions);

$main_smarty->assign('story', $story);

$story_markup = $main_smarty->fetch($the_template . "/link_summary.tpl");



// Comments
$enable_comments = kahuk_get_config('_enable_comments');
$main_smarty->assign('enable_comments', $enable_comments);

// echo "<pre>enable_comments: $enable_comments</pre>";

//
$args = [
    'story_id' => $link_id,
];
$comments = kahuk_get_comments($args);
$the_comments_markup = '';

if ($comments) {
    foreach($comments as $row) {
        $main_smarty->assign('the_comment', $row);
        $the_comments_markup .= $main_smarty->fetch($the_template . "/template-parts/comment-item.tpl");
    }    
}

// $main_smarty->assign('the_comments_items', $the_comments_items);
// $the_comments_markup = $main_smarty->fetch($the_template . "/template-parts/comment-items.tpl");

$main_smarty->assign('the_comments_markup', $the_comments_markup);


//
$comment_form_markup = '';

if ($enable_comments) {
    $comment_form_markup = $main_smarty->fetch($the_template . "/template-parts/comment-form.tpl");
}

$main_smarty->assign('comment_form_markup', $comment_form_markup);

$comments_markup = $main_smarty->fetch($the_template . "/template-parts/comments.tpl");
$main_smarty->assign('comments_markup', $comments_markup);



//
$main_smarty->assign('the_story', $story_markup);

$main_smarty->assign('tpl_center', $the_template . '/story_center');
