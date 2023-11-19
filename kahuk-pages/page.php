<?php
if (!$globalStory) {
    kahuk_redirect_404();
    exit;
}

// $slug = kahuk_create_slug_story(_request("slug"));

$story = $globalStory;
$link_id = $globalStory['link_id'];


$main_smarty->assign('page_title', $globalStory['link_title']);
$main_smarty->assign('page_content', $globalStory['link_content']);
$main_smarty->assign('posttitle', $globalStory['link_title']);
$main_smarty->assign('link_id', $globalStory['link_id']);


//
$page_dynamic_page_edit_url = "";

if ($session_user_level == "admin") {
    $page_dynamic_page_edit_url = kahuk_create_url("admin/edit_page.php", ['link_id' => $link_id]);
}

$main_smarty->assign('page_dynamic_page_edit_url', $page_dynamic_page_edit_url);


// set the template
$main_smarty->assign('tpl_center', $the_template . '/page_center');
