<?php
/**
 * Return the max length of comments in words
 * 
 * @since 5.0.7
 * 
 * @return int
 */
function kahuk_comment_length() {
    global $hooks;

    return $hooks->apply_filters('comment_length', 1500);
}

/**
 * Sanitize Comment Content
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_sanitize_comment($comment) {
	global $hooks;

	$output = kahuk_kses($comment, allowed_html_tags);
    $comment_length = kahuk_comment_length();

    if (utf8_strlen($output) > $comment_length) {
        $output = truncate_strings_html( $output, $comment_length );
    }

	return $hooks->apply_filters('sanitize_comment', $output);
}

/**
 * 
 */
function kahuk_count_comments_by_user($user_id, $filters = []) {
    global $db;

    $comment_status = ($filters['comment_status'] ?? 'published');

    $sql = "SELECT count(comment_id) FROM " . table_comments;
    $sql .= " WHERE comment_status='" . $db->escape($comment_status) . "'";
    $sql .= " AND comment_user_id = {$user_id}";

    return $db->get_var($sql);
}

/**
 * Count the number of comments for a story
 * 
 * @return int number
 */
function kahuk_count_comments ($story_or_id, $args = []) {
    global $globalCommentsObj;

    $story_id = 0;

    if (is_numeric($story_or_id)) {
        $story_id = $story_or_id;
    } else if (is_array($story_or_id) && isset($story_or_id['link_id'])) {
        $story_id = $story_or_id['link_id'];
    }

    if (!$story_id) {
        return 0;
    }

    //
    $countArgs = [
        "story_id" => $story_id,
    ];

    if (isset($args['comment_status'])) {
        $countArgs['comment_status'] = $args['comment_status'];
    }

    return $globalCommentsObj->get_count($countArgs);
}

/**
 * Get Comment
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_comment($comment_id) {
	global $hooks, $globalCommentsObj;
    
    $comment = $globalCommentsObj->get_comment($comment_id);
    $output = [];

    if ($comment) {
        $output = $hooks->apply_filters('process_comment', $comment);
    }

	return $output;
}

/**
 * Get Comments
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_comments($args = []) {
	global $hooks, $globalCommentsObj;
    
    $comments = $globalCommentsObj->get_comments($args);
    $output = [];

    if ($comments) {
        foreach($comments as $row) {
            $output[] = $hooks->apply_filters('process_comment', $row);
        }        
    }

	return $output;
}


/**
 * Check duplicate comment
 * TODO: Not working
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_check_duplicate_comment($data, $skip_id= 0) {
    global $db;

    $sql = "SELECT count(comment_id) FROM " . table_comments;
    $sql .= " WHERE comment_parent = '" . $db->escape($data["comment_parent"]) . "'";
    $sql .= ", comment_link_id = " . $db->escape($data["comment_link_id"]) . "";
    $sql .= ", comment_user_id = " . $db->escape($data["comment_user_id"]) . "";
    $sql .= ", comment_content = '" . $db->escape($data["comment_content"]) . "'";

    //
    if ($skip_id > 0) {
        $sql .= " AND comment_id != '" . (int) $skip_id . "'";
    }

    return ($db->get_var($sql) > 0);
}


/**
 * Update / Insert Comment
 * 
 * @since 5.0.7
 * 
 * `comment_id` int NOT NULL,
 * `comment_parent` int DEFAULT '0',
 * `comment_link_id` int NOT NULL DEFAULT '0',
 * `comment_user_id` int NOT NULL DEFAULT '0',
 * `comment_date` datetime NOT NULL,
 * `comment_content` longtext NOT NULL,
 * `comment_status` enum('discard','moderated','published','spam') NOT NULL DEFAULT 'published'
 * 
 * @return array
 */
function kahuk_save_comment($data, $commentId = 0) {
	global $db, $hooks;

    $error = [
        "success" => false,
    ];

    //
    $comment_parent = ($data["comment_parent"] ?? 0);

    //
    $story_id = ($data["story_id"] ?? 0);

    if (!$story_id) {
        $error["message"] = "Invalid story found to submit a comment!";
        return $error;
    }

    //
    $comment_user_id = ($data["comment_user_id"] ?? 0);

    if (!$comment_user_id) {
        $error["message"] = "Authentication require to submit a comment!";
        return $error;
    }

    //
    $comment_content = ($data["comment_content"] ?? "");
    
    if (!$comment_content) {
        $error["message"] = "Comment is empty!";
        return $error;
    }

    //
    $allowedStatuses = kahuk_comments_statuses();
    $comment_status = ($data["comment_status"] ?? "");

    if (!in_array($comment_status, $allowedStatuses)) {
        $comment_status = kahuk_comment_status_default();
    }


    //
    $check_duplicate = kahuk_check_duplicate_comment(
        [
            'comment_parent' => $comment_parent,
            'comment_link_id' => $story_id,
            'comment_user_id' => $comment_user_id,
            'comment_content' => $comment_content,
        ],
        $commentId
    );

    if ($check_duplicate) {
        $error["message"] = "Duplicate comments not allowed!";
        return $error;
    }

    // Lets create query
    if ($commentId == 0) {
        // Insert New Record
        $sql = "INSERT INTO " . table_comments;
    } else {
        // Update Existing Record
        $sql = "UPDATE " . table_comments;
    }

    $sql .= " SET comment_parent='" . $db->escape($comment_parent) . "'";
    $sql .= ", comment_link_id=" . $db->escape($story_id) . "";
    $sql .= ", comment_user_id=" . $db->escape($comment_user_id) . "";
    $sql .= ", comment_date=NOW()";
    $sql .= ", comment_content='" . $db->escape($comment_content) . "'";
    $sql .= ", comment_status='" . $db->escape($comment_status) . "'";


    if ($commentId == 0) {
        $id = $db->query_insert($sql);

        if (!$id) {
            kahuk_log_queries("New Record Failed!\nSQL: {$sql}");
            die('Database error!');
        }

        $hooks->apply_filters(
            "saved_comment", 
            [
                'comment_id' => $id,
                'story_id' => $story_id,
                'user_id' => $comment_user_id,
            ]
        );

        $output = [
            "success" => true,
            "message" => "Comment submit successful!",
            "id" => $id,
        ];

        return $output;

    } else {
        $sql .= " WHERE comment_id='{$commentId}'";
        $output = $db->query($sql);

        if ($output !== 1) {
            kahuk_log_queries("Update Record Failed!\nSQL: {$sql}");
        }

        $hooks->apply_filters("updated_comment", $output);

        $output = [
            "success" => true,
            "message" => "Comment Update Successful!",
        ];

        return $output;
    }
}


/**
 * Delete Comment
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_delete_comment($commentId = 0) {
	global $db, $globalCommentsObj, $hooks;

    $comment = $globalCommentsObj->get_comment($commentId);

    $sql = "DELETE FROM " . table_comments . " WHERE comment_id='" . $db->escape($commentId) . "'";
    $rs = $db->query($sql);

    if ($rs > 1) {
        kahuk_log_queries("DB Warning: [output: {$rs}]\nSQL: {$sql}");
    }

    $hooks->apply_filters(
        "deleted_comment", 
        [
            'comment_id' => $commentId,
            'story_id' => $comment['comment_link_id'],
            'user_id' => $comment['comment_user_id'],
        ]
    );

    return $rs;
}
