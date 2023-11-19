<?php
$search = sanitize_text_field(_get('search'));

if (!$search) {
    kahuk_redirect();
}

if (strlen($search) > 200) {
    kahuk_set_session_message(
        "Please use maximum 200 character for the search.",
        'notice'
    );

    kahuk_redirect();
}

if (kahuk_word_count($search) > 200) {
    kahuk_set_session_message(
        "Please use maximum 25 words for the search.",
        'notice'
    );

    kahuk_redirect();
}

$pagination = "";
$stories = [];
$stories_markup = "";

//
$searchWords = explode(" ", $search);
$searchTerm = trim(implode("%", $searchWords), "%");
$where_clause = "links.link_title LIKE '%{$searchTerm}%'";
$where_clause .= " OR links.link_content LIKE '%{$searchTerm}%'";

$storiesArgs = [
    "link_status" => kahuk_story_statuses(),
    "where_clause" => $where_clause,
    "order_by" => "links.link_karma DESC, links.link_date DESC",
];

$rowCount = kahuk_count_stories($storiesArgs);

if ($rowCount) {
    $stories = kahuk_get_stories($storiesArgs);

    foreach($stories as $i => $story) {
		$main_smarty->assign('story', $story);
		$story_summary = $main_smarty->fetch($the_template . "/link_summary.tpl");

		$stories_markup .= $hooks->apply_filters('kahuk_story_markup', $story_summary, $i);
	}

    /**
     * Pagination - Stories
     */
    $args = [
        "total" => $rowCount,
        "permalink" => $permalink,
    ];

    $pagination = kahuk_pagination($args);
}

//
$main_smarty->assign('search', $search);
$main_smarty->assign('stories_markup', $stories_markup);
$main_smarty->assign('pagination', $pagination);

//
$main_smarty->assign('tpl_center', $the_template . '/search_center');

