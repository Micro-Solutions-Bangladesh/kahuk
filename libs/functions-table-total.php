<?php
/**
 * Regenerate The Total Number of Links by Link Status
 * 
 * @since 5.0.3
 * 
 * @return array
 */
function kahuk_regenerate_total_by_status( $link_status = 'new' ) {
	global $db, $cached_totals;

	$count = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_status='$link_status';");
	$db->query("UPDATE `" . table_totals . "` set `total` = $count where `name` = '$link_status';");

    $cached_totals[$link_status] = $count;

	return true;
}
