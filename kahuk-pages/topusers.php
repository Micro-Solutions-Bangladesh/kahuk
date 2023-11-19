<?php
$where_clause = "user_karma > 0";
$args = [
    'return_type' => 'profile',
    // 'add_extra_data' => true,
    'order_by' => "user_karma DESC, user_id DESC",
    'where_clause' => $where_clause,
    'user_level' => [
        'moderator','normal'
    ],
];

$users = $globalUsersObj->get_users_temporary_func($args);

$users_table_body = '';

if ($users) {
    foreach ($users as $user) {
        $main_smarty->assign('user_total_votes', $globalUsersObj->count_total_votes_by_user($user['user_id']));

        $main_smarty->assign('user_stories_total', $globalUsersObj->count_total_stories_by_user($user['user_id']));

        $main_smarty->assign('user_total_comments', kahuk_count_comments_by_user($user['user_id']));

        $main_smarty->assign('user', $user);

        //
        $users_table_body .= $main_smarty->fetch(THE_TEMPLATE . "/topusers_data.tpl");
    }
}

$main_smarty->assign('users_table_body', $users_table_body);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/topusers_center');
