<?php
$storyStatuses = kahuk_story_statuses();

if (in_array($pageTask, $storyStatuses)) {
	$storyStatuses = [$pageTask];
}

/**
 * Extract stories by duration
 * https://www.w3schools.com/sql/func_mysql_date_sub.asp
 */
$duration = _get("duration");

$where_clause = "(links.link_group_id = 0)";
$order_by = "";
$storiesArgs = [];

/**
 * Story Sorting Order are:
 *  default: `link_date` recent to old
 * 	trending: `link_karma` highest to lowest
 */
$storiesArgs["link_status"] = $storyStatuses;
$order_by = "links.link_date DESC, links.link_karma DESC";

// if ($pageTask == 'trending') {
// 	$order_by = "links.link_karma DESC, links.link_date DESC";
// }

if ($duration) {
	$order_by = "links.link_karma DESC, links.link_date DESC";

	switch($duration) {
		case "day":
			$where_clause .= " AND (links.link_date > DATE_SUB(NOW(),INTERVAL 24 HOUR))";
			break;

		case "week":
			$where_clause .= " AND (links.link_date > DATE_SUB(NOW(),INTERVAL 158 HOUR))";
			break;

		case "month":
			$where_clause .= " AND links.link_date > DATE_SUB(NOW(),INTERVAL 1 MONTH)";
			break;

		case "year":
			$where_clause .= " AND links.link_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
			break;

		default:
			break;
	}
}

$storiesArgs["where_clause"] = $where_clause;

if ($order_by) {
	$storiesArgs["order_by"] = $order_by;
}

$stories = kahuk_get_stories($storiesArgs);
$stories_markup = '';

foreach($stories as $i => $story) {
	$main_smarty->assign("story", $story);
	$story_summary = $main_smarty->fetch($the_template . "/link_summary.tpl");

	$stories_markup .= $hooks->apply_filters("kahuk_story_markup", $story_summary, $i);
}

$main_smarty->assign("stories_markup", $stories_markup);


/**
 * Pagination - Stories
 */
$rowCount = kahuk_get_stories_total($storiesArgs["link_status"]);

$pagingArgs = [
	"total" => $rowCount,
];

$main_smarty->assign("pagination", kahuk_pagination($pagingArgs));

//
$main_smarty->assign("tpl_center", $the_template . "/stories_center");
