<?php
if (!defined('KAHUK_AJAX')) {die();}

/**
 * Save or Unsave story
 */
if (in_array($action, ['fork', 'unfork'])) {
	$story_id = sanitize_number(_post('id'));
	$user_id = 0;

	if (kahuk_is_user_authenticated()) {
		$user_id = (int) kahuk_session_user_id();
	}

	if (!$user_id || !$story_id) {
		$returnOutput["status"] = "invalid";
	} else {
		$saved_status = ($action == "fork" ? "enable" : "disable");
		// $saved_row = kahuk_get_saved_story($story_id, $user_id);

		$args = [
			"story_id" => $story_id,
			"user_id" => $user_id,
			"saved_status" => $saved_status,
		];

		$output = kahuk_save_saved_story($args);

		$returnOutput = array_merge($returnOutput, $output);

		// $returnOutput["status"] = $rs["status"];
		// $returnOutput["message"] = $rs["message"];

		// if ($rs["status"] != "none") {
		// 	$story = kahuk_story($story_id);

		// 	$returnOutput["story_id"] = $story_id;
		// 	$returnOutput["story_karma"] = sanitize_number($story["link_karma"]);
		// 	// $returnOutput["story_reactions"] = $story_reactions;
		// 	// $returnOutput["reactions_count"] = kahuk_count_reactions_by_story($story_id);
		// 	$returnOutput["user_id"] = sanitize_number($user_id);
		// 	$returnOutput["user_karma"] = kahuk_user_karma($user_id);
		// }		
	}

	echo json_encode($returnOutput);
	exit;
}
