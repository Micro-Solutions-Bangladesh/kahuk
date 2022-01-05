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

/* changed the function parameters according to the query on line 62 to save 3 queries X number of comments to delete, in the delete_comment function */
function delete_comment($key, $linkid)
{
	global $db;

	if (!is_numeric($key)) {
		return;
	}

	$vars = array('comment_id' => $key);
	check_actions('comment_deleted', $vars);

	$db->query('DELETE FROM `' . table_comments . '` WHERE `comment_id` = "' . $key . '"');

	$link = new Link;
	$link->id = $linkid;
	$link->read();
	$link->recalc_comments();
	$link->store();
}

/* changed the query to save 3 queries in the above delete_comment function */
$sql_query = "SELECT comment_id, comment_link_id, comment_parent FROM " . table_comments . " WHERE comment_status = 'discard'";
$result = $db->get_results($sql_query);
$num_rows = $db->get_var("SELECT FOUND_ROWS()");

foreach ($result as $comment) {
	delete_comment($comment->comment_id, $comment->comment_link_id);
}
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?php echo $main_smarty->get_config_vars('KAHUK_Visual_AdminPanel_Discarded_Comments_Removed') ?></h4>
		</div>
		<div class="modal-body">
			<?php
			echo '<p><strong>' . $num_rows . '</strong> ' . $main_smarty->get_config_vars("KAHUK_Visual_AdminPanel_Discarded_Comments_Removed_Message") . '</p>';
			$handle = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

			/* check connection */
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}
			/* Modified the code from the deprecated mysql_ to mysqli and also fixed the Optimization code that was not working, and created a new function in /libs/utils.php for this purpose that will also be used from many other places. */
			Repair_Optmize_Table($handle, DB_NAME, table_comments);
			$handle->close();
			?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div><!-- /.modal-content -->
</div>