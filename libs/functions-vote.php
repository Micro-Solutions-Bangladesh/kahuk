<?php
/**
 * Hook Function
 * Add additional information into reaction record
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_process_reaction_callback($data) {
    if (!isset($data['vote_id'])) {
        return $data;
    }

    $data['user_gravatars'] = kahuk_gravatar($data['user_email']);
    $data['user_profile_url'] = kahuk_url_user_profile($data['user_login']);

    $user_names = (empty($data['user_names']) ? $data['user_login'] : $data['user_names']);
    $data['user_names'] = $user_names;

    return $data;
}

$hooks->add_filter('process_reaction', 'kahuk_process_reaction_callback');

/**
 * Get Reaction
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_reaction($argsCustom = []) {
	global $globalVotesObj, $hooks;

    $defaults = [
        'vote_id' => 0,
        "story_id" => 0,
        "user_id" => 0,
    ];

    $args = array_merge($defaults, $argsCustom);

    // print_r($args);

    if ($args['vote_id'] || ($args["story_id"] && $args["user_id"])) {
        //
    } else {
        kahuk_log_unexpected("Unexpected arguments to fetch Vote record!\n" . print_r($argsCustom, true));
        return [];
    }
    
    $rs = $globalVotesObj->get_item($args);

    if (!$rs) {
        $rs = [];
    }

	return $hooks->apply_filters('process_reaction', $rs);;
}

/**
 * Get reactions by story
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_get_reactions_group_by_reaction($story_id) {
	global $hooks, $globalVotesObj;

	$output = [];
    $publicReactions = kahuk_vote_reactions();

    foreach($publicReactions as $v) {
        $args = [
            "story_id" => $story_id,
            'vote_reaction' => [$v],
        ];

        $rs = $globalVotesObj->get_items($args);
        $rows = [];

        foreach($rs as $row) {
            $rows[] = $hooks->apply_filters('process_reaction', $row);
        }

        $output[$v] = $rows;
    }

    return $output;
}



/**
 * Count Reactions group by Reaction type
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_count_reactions_by_story($story_id) {
	global $db;

    $publicReactions = kahuk_vote_reactions();


    $sql = "SELECT vote_reaction, count(vote_reaction) AS reaction_count";

    $sql .= " FROM " . table_votes;

    $sql .= " WHERE vote_reaction IN ('" . implode("','", $publicReactions) . "')";
    $sql .= " AND vote_link_id = " . $db->escape($story_id);

    $sql .= " GROUP BY vote_reaction ORDER BY vote_reaction DESC";

    // echo "<pre>kahuk_count_reactions_by_story() :: {$sql}</pre>";
    $rs = $db->get_results($sql, ARRAY_A);

    if (!$rs) {
        $rs = [];
    }

    $output = [];

    foreach($publicReactions as $v) {
        $output[$v] = 0;

        foreach($rs as $row) {
            if ($row['vote_reaction'] == $v) {
                $output[$v] = sanitize_number($row['reaction_count']);
                break;
            }
        }
    }

	return $output;
}


/**
 * Insert/Update record in votes table
 * 
 * `vote_id` int(20) NOT NULL auto_increment,       // PRIMARY KEY
 * `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * `vote_link_id` int(20) NOT NULL default '0',
 * `vote_user_id` int(20) NOT NULL default '0',
 * `vote_reaction` enum('positive','negative','invalid') NOT NULL DEFAULT 'invalid'
 * `vote_ip` varchar(64) default NULL,
 * 
 * @since 5.0.7
 */
function kahuk_save_reaction($customArgs) {
    global $db, $hooks;

    $defaultData = [
        "story_id" => 0, // Required
        "user_id" => 0, // Required
        "reaction" => '', // Required
    ];

    $args = array_merge($defaultData, $customArgs);

    $output = [
        "status" => "error",
    ];

    if (!$args["user_id"] || !$args["story_id"] || !$args["reaction"]) {
        $output["message"] = "Required data not found!";
        return $output;
	}

    $story_id = $args["story_id"];
    $user_id = $args["user_id"];

    $record = kahuk_get_reaction($args);
    $reaction = $args["reaction"];

    if ($record && ($reaction == $record["vote_reaction"])) {
        $output["status"] = "none";

        return $output;
    }

    $vote_ip = check_ip_behind_proxy();

    $sql = "INSERT INTO " . table_votes;

    if ($record) {
        $sql = "UPDATE " . table_votes;
    }
    
    $sql .= " SET vote_reaction='" . $db->escape($reaction) . "'";

    if ($record) {
        $sql .= " WHERE vote_link_id=" . $db->escape($story_id);
        $sql .= " AND vote_user_id=" . $db->escape($user_id);
    } else {
        $sql .= ", vote_link_id = " . $db->escape($story_id);
        $sql .= ", vote_user_id = " . $db->escape($user_id);
        $sql .= ", vote_date = NOW()";
        $sql .= ", vote_ip = '" . $db->escape($vote_ip) . "'";
    }

    if ($record) {
        // Execute Query
        $rs = $db->query($sql);

        if ($rs == 1) {
            $output["status"] = "success";
            $output["message"] = ucfirst($reaction . " reaction saved!");
            $output["reaction_id"] = sanitize_number($record["vote_id"]);

            $output = $hooks->apply_filters('reaction_updated', $output, $args);

        } else {
            if ($rs > 1) {
                $output["status"] = "none";
                kahuk_log_queries("Issue Multi record Updated! [OUTPUT: {$rs}] \nSQL: {$sql}");
            } else {
                $output["message"] = "Appologies, got an error!";
            }
        }
    } else {
        // Execute Query
        $rs = $db->query_insert($sql);

        if ($rs) {
            $output["status"] = "success";
            $output["message"] = ucfirst($reaction . " reaction saved!");
            $output["reaction_id"] = sanitize_number($rs);

            $output = $hooks->apply_filters("reaction_inserted", $output, $args);

        } else {
            kahuk_log_queries("New Record Failed!\nSQL: {$sql}");

            $output["message"] = "Appologies, got an error!";
        }
    }

    $output["user_ip"] = $vote_ip;
    $output["story_id"] = sanitize_number($story_id);

    // $story = kahuk_story($story_id);
    $story = $hooks->apply_filters("refactor_story_status", ["story_id" => $story_id]);
    $output["story_karma"] = sanitize_number($story["link_karma"]);

    $output["user_karma"] = kahuk_user_karma($user_id);
    $output["reactions_count"] = kahuk_count_reactions_by_story($story_id);

    return $output;
}


/**
 * Delete reactions by user id or by story id
 * 
 * @since 5.0.7
 * 
 * @return int
 */
function kahuk_delete_reactions( $customArgs ) {
    global $db, $hooks;

    $defaultData = [
        "story_id" => 0, // Required
        "user_id" => 0, // Required
    ];

    $args = array_merge($defaultData, $customArgs);

    if (($args["story_id"] > 0) || ($args["user_id"] > 0)) {
        //
    } else {
        kahuk_log_unexpected("Invalid params to delete reaction!");
        return 0;
    }

    $sql = "DELETE FROM `" . table_votes . "`";
    $sql .= " WHERE";

    if ($args["story_id"] > 0) {
        $sql .= " vote_link_id = " . $db->escape($args["story_id"]);
    } else if ($args["user_id"] > 0) {
        $sql .= " vote_user_id = " . $db->escape($args["user_id"]);
    }

    // echo "<pre>{$sql}</pre>";
    $rs = $db->query($sql);
    $args["total_deleted"] = $rs;

    $hooks->apply_filters(
        "deleted_reactions", 
        $args
    );

    return $rs;
}
