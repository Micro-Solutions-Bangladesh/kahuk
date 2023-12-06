<?php
/**
 * Manage Reaction for Story
 */
if (!defined('KAHUK_AJAX')) {die();}

if (in_array($action, ['reaction'])) {
	$reaction = sanitize_text_field(_post('reaction'));
	$story_id = sanitize_number(_post('id'));
	$user_id = 0;

	if (kahuk_is_user_authenticated()) {
		$user_id = kahuk_session_user_id();
	}

	if (!$user_id || !$story_id || !$reaction) {
		$returnOutput["status"] = "invalid";
	} else {
		$data = [
			"story_id" => $story_id,
			"user_id" => $user_id,
			"reaction" => $reaction,
		];

		$output = kahuk_save_reaction($data);

		$returnOutput = array_merge($returnOutput, $output);
	}

	echo json_encode($returnOutput);
	exit;	
}
