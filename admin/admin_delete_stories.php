<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if ($canIhaveAccess == 0) {
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

function delete_storylink($todelete)
{
	global $db;

	/* creating a mysqli connection */
	$handle = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	# delete the story link
	$handle->query("DELETE FROM " . table_links . " WHERE link_id IN (" . $todelete . ");");

	# delete the story comments
	$handle->query("DELETE FROM " . table_comments . " WHERE comment_link_id IN (" . $todelete . ");");

	# delete the saved links
	$handle->query("DELETE FROM " . table_saved_links . " WHERE saved_link_id IN (" . $todelete . ");");

	# delete the story trackbacks
	$handle->query("DELETE FROM " . table_trackbacks . " WHERE trackback_link_id IN (" . $todelete . ");");

	# delete the story votes
	$handle->query("DELETE FROM " . table_votes . " WHERE vote_link_id IN (" . $todelete . ");");

	# delete additional categories
	$handle->query("DELETE FROM " . table_additional_categories . " WHERE ac_link_id IN (" . $todelete . ");");

	// module system hook
	$linksstr = explode(",", $todelete);

	foreach ($linksstr as $linkid) {
		$vars = array('link_id' => $linkid);
		check_actions('admin_story_delete', $vars);
	}
}

/* everything has been modified in this file; first to migrate to mysqli, and secondly to optimize the enormous amout of queries that are used. Below, I modified the query to only get the link_id and created an array that was imploded into a string to pass to the function above. Now the function will use the IN clause to save all the quries that were passed one at a time for each link_id. */
$sql_query = "SELECT link_id FROM " . table_links . " WHERE link_status = 'discard'";
$result_storylinks = $db->get_results($sql_query);
$num_rows = count($result_storylinks);

if ($num_rows > 0) {
	$link_to_delete = [];

	foreach ($result_storylinks as $storylink) {
		$links_to_delete[] = $storylink->link_id;
	}

	$discarded_links = implode(",", $links_to_delete);
	delete_storylink($discarded_links);
}

# set discards total to zero
$db->query("UPDATE " . table_totals . " SET total = '0' WHERE name = 'discard'");
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?php echo $main_smarty->get_config_vars('KAHUK_Visual_AdminPanel_Discarded_Stories_Removed') ?></h4>
		</div>
		<div class="modal-body">
			<?php
			echo '<p><strong>' . $num_rows . '</strong> ' . $main_smarty->get_config_vars("KAHUK_Visual_AdminPanel_Discarded_Stories_Removed_Message");
			?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div><!-- /.modal-content -->
</div>