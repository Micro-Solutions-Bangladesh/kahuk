<?php

/**
 * Get Saved Story Record
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_saved_story($story_id, $user_id) {
	global $hooks, $db;
    $output = [];

    $sql = "SELECT * FROM " . table_saved_links . " WHERE saved_link_id = {$story_id} AND saved_user_id = {$user_id}";

    $output = $db->get_row($sql, ARRAY_A);

    if (!$output) {
        $output = [];
    }

    $output = $hooks->apply_filters('process_saved_story', $output);

	return $output;
}

/**
 * Is the Story Record Saved
 * 
 * @since 5.0.7
 * 
 * return boolean
 */
function kahuk_is_saved_story($story_id, $user_id) {
    $rs = kahuk_get_saved_story($story_id, $user_id);

    if ($rs && ($rs['saved_status'] == "enable")) {
        return true;
    } else {
        return false;
    }
}

/**
 * Insert/Update Saved Story Record
 * 
 * saved_id int NOT NULL,
 * saved_user_id int NOT NULL,
 * saved_link_id int NOT NULL,
 * saved_privacy enum('private','public') NOT NULL DEFAULT 'public',
 * saved_status enum('enable','disable') NOT NULL DEFAULT 'enable'
 * 
 * @since 5.0.7
 * 
 * return boolean
 */
function kahuk_save_saved_story($data) {
    global $db, $hooks;

    $output = [];

    $story_id = sanitize_number($data["story_id"]);
    $user_id = sanitize_number($data["user_id"]);

    $saved_privacy = ($data["saved_privacy"] ?? kahuk_saved_story_privacy_default());

    $saved_status = kahuk_saved_story_status_default();
    $saved_story_statuses = kahuk_saved_story_statuses();

    if (in_array($data["saved_status"], $saved_story_statuses)) {
        $saved_status = $data["saved_status"];
    }

    $record = kahuk_get_saved_story($story_id, $user_id);
    // $saved_status = $data["saved_status"];

    $msg = (($saved_status=="enable") ? "Forked" : "Unforked");
    $sql = "";

    if ($record) {
        // Update Existing Record
        $sql = "UPDATE " . table_saved_links;

    } else {
        // Insert New Record
        $sql = "INSERT INTO " . table_saved_links;
    }

    $sql .= " SET saved_status='" . $db->escape($saved_status) . "'";
    $sql .= ", saved_privacy='" . $db->escape($saved_privacy) . "'";

    if ($record) {
        $sql .= " WHERE saved_id='" . $record['saved_id'] . "'";
        
        $rs = $db->query($sql);

        if ($rs == 1) {
            $output = [
                "status" => "success",
                "message" => $msg,
            ];

            $record["saved_status_prev"] = $record["saved_status"];
            $record["saved_status"] = $saved_status;
            $record["saved_privacy"] = $saved_privacy;
            $record["user_id"] = $user_id;
            $record["story_id"] = $story_id;

            $hooks->do_action(
                "updated_saved_story", $record
            );
        } else  {
            $output = [
                "status" => "error",
                "message" => "Story fork failed!",
            ];

            if ($rs>1) {
                kahuk_log_queries("Issue Multi record Updated! [OUTPUT: {$rs}] \nSQL: {$sql}");
            }
        }
    } else {
        $sql .= ", saved_user_id='{$user_id}'";
        $sql .= ", saved_link_id='{$story_id}'";

        $id = $db->query_insert($sql);

        if ($id) {
            $output = [
                "status" => "success",
                "message" => $msg,
            ];

            $saved_story_data = [
                "saved_id" => $id,
                "user_id" => $user_id,
                "story_id" => $story_id,
                "saved_privacy" => $saved_privacy,
                "saved_status" => $saved_status,
            ];

            $hooks->do_action(
                "saved_saved_story", $saved_story_data
            );

        } else {
            $output = [
                "status" => "error",
                "message" => "Story fork failed!",
            ];

            kahuk_log_unexpected("New Record Failed!\nSQL: {$sql}");
        }
    }

    $story = $hooks->apply_filters("refactor_story_status", ["story_id" => $story_id]);
    
    $output["story_id"] = $story_id;
    $output["story_karma"] = sanitize_number($story["link_karma"]);
    $output["user_id"] = sanitize_number($user_id);
    $output["user_karma"] = kahuk_user_karma($user_id);
    
    return $output;
}

/**
 * Delete forked history
 * 
 * @since 5.0.7
 * 
 * @return int
 */
function kahuk_delete_forks( $customArgs ) {
    global $db, $hooks;

    $defaultData = [
        "story_id" => 0, // Required
        "user_id" => 0, // Required
    ];

    $args = array_merge($defaultData, $customArgs);

    if (($args["story_id"] > 0) || ($args["user_id"] > 0)) {
        //
    } else {
        kahuk_log_unexpected("Invalid params to delete fork!");
        return 0;
    }

    $sql = "DELETE FROM `" . table_saved_links . "`";
    $sql .= " WHERE";

    if ($args["story_id"] > 0) {
        $sql .= " saved_link_id = " . $db->escape($args["story_id"]);
    } else if ($args["user_id"] > 0) {
        $sql .= " saved_user_id = " . $db->escape($args["user_id"]);
    }

    // echo "<pre>{$sql}</pre>";
    $rs = $db->query($sql);
    $args["total_deleted"] = $rs;

    $hooks->apply_filters(
        "deleted_forks", 
        $args
    );

    return $rs;
}
