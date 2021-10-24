<?php
/**
 * Regenerate The Total Number of stories by story Status
 * 
 * @since 5.0.3
 * 
 * @return integer
 */
function kahuk_regenerate_total_by_status( $story_status = 'new' ) {
	global $db, $cached_totals;

	$count = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_status='$story_status';");
	$db->query("UPDATE `" . table_totals . "` set `total` = $count where `name` = '$story_status';");

    $cached_totals[$story_status] = $count;

	return $count;
}
