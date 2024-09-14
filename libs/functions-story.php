<?php

/**
 * Update DB config for all stories by status [scheduled]
 */
function kahuk_update_stories_total_scheduled() {
    $story_statuses = kahuk_story_statuses(false);

    $last_update = kahuk_get_config('_time_story_totals_updated');
    $cur_time = time();

    // $estimate_update = ($last_update + (60*10)); // 10 minutes
    $estimate_update = ($last_update + (24*60*60)); // 24 hours

    if ($cur_time > $estimate_update) {
        kahuk_log_schedule("Updating stories total on scheduled.");
        // Let's update
        kahuk_update_config('_time_story_totals_updated', "{$cur_time}");
        kahuk_update_stories_total($story_statuses);
    }
}

/**
 * 
 */
function kahuk_get_stories_total($statuses = '') {

    $story_statuses = kahuk_story_statuses(false);

    if (!is_array($statuses)) {
        $statuses = [$statuses];
    }

    //
    kahuk_update_stories_total_scheduled();

    //
    $output = 0;

    foreach($statuses as $status) {
        if (!in_array($status, $story_statuses)) {
            continue;
        }

        $rs = kahuk_get_config("_story_status_{$status}_total", 0);
        $output += sanitize_number($rs);
    }

    return $output;
}

/**
 * Count the number of stories
 * 
 * @return int number
 */
function kahuk_count_stories ($argsCustom = []) {
    global $globalStories;

    $defaults = [];

    $args = array_merge($defaults, $argsCustom);

    return $globalStories->get_count($args);
}

/**
 * Analyze the search term/keyword
 * 
 * @since 5.0.8
 * 
 * @return array
 */
function kahuk_analyse_keyword($keyword) {
    global $hooks;

	$output = [];
	$keyword = trim($keyword);

	//
	if (is_numeric($keyword)) {
		$output["type"] = "numeric";
		$output["keyword"] = sanitize_number($keyword);
	}

	//
    if (!$output) {
        $findme   = "domain:";
        $pos = strpos($keyword, $findme);

        if ($pos === 0) {
            $output["type"] = "domain";
            $output["keyword"] = str_replace($findme, "", $keyword, $count);
        }
    }
 
	//
    if (!$output) {
        $findme   = "author:";
        $pos = strpos($keyword, $findme);

        if ($pos === 0) {
            $output["type"] = "author";
            $output["keyword"] = str_replace($findme, "", $keyword, $count);
        }
    }

    //
    if (!$output) {
        if (($keyword[0] === '"') && ($keyword[-1] === '"')) {
            $output["type"] = "quoted";
            $output["keyword"] = trim($keyword, "\"");
        }


        // $pattern = '"[^"]*"!';

        // if (preg_match($pattern, $keyword)) {
        //     $output["type"] = "quoted";
        //     $output["keyword"] = trim($keyword, "\"");
        // }
    }

    return $hooks->apply_filters('analyse_keyword', $output, $keyword);
}

/**
 * Get Story
 * 
 * @since 5.0.7
 * 
 * return array
 */
function kahuk_story($story_id = '') {
	global $globalStories;

    $args = [];

    $args['link_status'] = kahuk_story_statuses();
    
    if (kahuk_check_user_role("admin")) {
        $args['link_status'] = kahuk_story_statuses(false);
    }

    if ($story_id) {
        $args['link_id'] = $story_id;
    } else {
        $pagename = kahuk_get_pagename();

        if (in_array($pagename, ['story', 'page', 'story-edit', 'comment-edit'])) {
            $slug = kahuk_create_slug_story(_get('slug'));
            $id = sanitize_number(_get('id'));

            if ($id) {
                $args['link_id'] = $id;
            } else if ($slug) {
                $args['link_title_url'] = $slug;
            }

            if ($pagename == "page") {
                $args['link_status'] = ['page'];
            }
        }
    }

    $story = $globalStories->get_story($args);

    if (!$story) {
        $story = [];
    }

	return $story;
}

/**
 * Get Story
 * 
 * @since 5.0.7
 * 
 * return array
 */
function kahuk_get_story($story_id = '') {
    global $hooks;

    $story = kahuk_story($story_id);

    if ($story) {
        $story = $hooks->apply_filters('process_story', $story);
    }

	return $story;
}

/**
 * Get Stories
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_stories($args = []) {
	global $hooks, $globalStories;
    
    $stories = $globalStories->get_stories($args);
    $output = [];

    if ($stories) {
        foreach($stories as $row) {
            $output[] = $hooks->apply_filters('process_story', $row);
        }        
    }

	return $output;
}

/**
 * Get the stories by Group ID
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_shared_stories($args = []) {
	global $hooks, $globalStories;

    $args["count_query"] = false;
    $stories = $globalStories->get_shared_stories($args);
    $output = [];

    if ($stories) {
        foreach($stories as $row) {
            $output[] = $hooks->apply_filters('process_story', $row);
        }        
    }

	return $output;
}

/**
 * Count the stories by Group ID
 * 
 * @since 5.0.7
 * 
 * return string
 */
function kahuk_get_shared_stories_count($args = []) {
	global $globalStories;

    $args["count_query"] = true;
    return $globalStories->get_shared_stories($args);
}

/**
 * Update DB config of stories total by statuses
 */
function kahuk_update_stories_total($statuses = '') {
    global $db;

    $story_statuses = kahuk_story_statuses(false);

    if (!is_array($statuses)) {
        $statuses = [$statuses];
    }

    foreach($statuses as $status) {
        if (!in_array($status, $story_statuses)) {
            continue;
        }

        $count_total = 0;
        $sql = "SELECT count(*) FROM " . table_links . " WHERE link_status='$status'";
	    $count_total = $db->get_var($sql);

        kahuk_update_config("_story_status_{$status}_total", "{$count_total}");
    }
}


/**
 * Get story slug by id
 * 
 * @since 5.0.6
 */
function kahuk_get_story_slug_by_id( $id ) {
	global $globalStories;

    $story = $globalStories->get_item($id);

    return ($story ? $story['link_title_url'] : "");
}


/**
 * Count the rows of story in table_links table by url
 * 
 * @since 5.0.4
 * 
 * @return int
 */
function kahuk_count_story_by_url( $url, $statuses = [] ) {
	global $db;

    $output = 0;
    $where = "link_url = '" . $db->escape( $url ) . "'";

    //
    if ( empty( $statuses ) ) {
        $statuses = [ 'published', 'new' ];
    }

    $where .= " AND link_status IN ('" . implode( "','", $statuses ) . "')";

    //
    $output = $db->count_rows( table_links, 'link_url', $where );
    
    // return output
    return $output;
}

/**
 * Count the story slug in database
 * 
 * @since 5.0.4
 * 
 * @return int
 */
function kahuk_count_story_by_slug( $story_slug, $argsCustom = [] ) {
	global $db;

    $defaults = [
        'search_type' => '', // pattern

        'link_status' => ['published', 'new'],

        'not_id' => [],
    ];

    $args = array_merge($defaults, $argsCustom);

    //
    $where = "link_title_url = '" . $db->escape( $story_slug ) . "'";

    if ( $args['search_type'] == 'pattern' ) {
        $where = "link_title_url LIKE '" . $db->escape( $story_slug ) . "%'";
    }

    $where .= " AND link_status IN ('" . implode( "','", $args['link_status'] ) . "')";

    if (!empty($args['not_id'])) {
        $where .= " AND link_id NOT IN (" . implode( ",", $args['not_id'] ) . ")";
    }

    //
    $output = $db->count_rows( table_links, 'link_url', $where );
    
    // return output
    return $output;
}

/**
 * Get row by story id
 * 
 * @since 5.0.0
 */
function kahuk_get_story_by_id( $id ) {
	global $db;

    $sql = "SELECT * FROM " . table_links . " WHERE link_id = " . $db->escape( $id );

    return $db->get_row( $sql, ARRAY_A );
}

/**
 * Get row by story slug
 * 
 * @since 5.0.0
 */
function kahuk_get_story_by_slug( $title_slug ) {
	global $db;

    $sql = "SELECT * FROM " . table_links . " WHERE link_title_url = '" . $db->escape( $title_slug ) . "'";

    return $db->get_row( $sql, ARRAY_A );
}


/**
 * Update story karma
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_update_story_karma($story_id, $karma_value, $is_increase = true) {
    global $db, $hooks;

    // SQL Query
    $sql = "UPDATE `" . table_links . "` SET";

    if ( $is_increase ) {
        $sql .= " link_karma = link_karma+" . intval( $karma_value );
    } else {
        $sql .= " link_karma = link_karma-" . intval( $karma_value );
    }
    
    $sql .= " WHERE link_id = " . $db->escape( $story_id );

    // Execute Query
	$rs = $db->query($sql);

    $output = [
        'story_id' => $story_id,
        'karma_value' => $karma_value,
        'is_increase' => $is_increase,
    ];

    if ($rs == 1) {
        $output["status"] = "success";
    } else {
        $output["status"] = "fail";

        if ($rs > 1) {
            kahuk_log_queries("Issue Multi record Updated! [OUTPUT: {$rs}] \nSQL: {$sql}");
        }
    }

    $hooks->apply_filters("story_karma_updated", $output);

    return $output;
}


/**
 * 
 * Required columns:
 *  link_author:
 *  link_status: enum( new, discard, published, abuse, duplicate, page, spam, moderated, draft, sheduled )
 *  link_randkey: int default 0 // may be no more in use
 *  link_votes: int default 0 // not require into INSERT query
 *  link_karma: decimal(10,2) default 0.0 // not require into INSERT query
 *  link_modified: timestamp default CURRENT_TIMESTAMP
 *  link_date: timestamp default null
 *  link_published_date: timestamp default null
 *  link_category: int
 * 
 *  link_url: varchar(255)
 *  link_title: text
 *  link_title_url: varchar(255)        // post slug
 *  link_content: mediumtext
 *  link_summary: text
 * 
 *  link_group_id: int
 *  link_out: int default 0
 *
 * @since 5.0.0
 *
 */
function kahuk_insert_story( $initialData ) {
    global $db, $current_user, $hooks;

    $defaultData = [
        'link_status' => 'new',
        'link_randkey' => '',
        'link_group_id' => 0,
    ];

    $data = array_merge( $defaultData, $initialData );
    $storyKarma = kahuk_get_config("_new_story_karma_initialy", "0");


    // SQL Query
    $sql = "INSERT INTO `" . table_links . "` SET link_author='" . $db->escape( $current_user->user_id ) . "'";

    $sql .= ", link_status='" . $db->escape( $data['link_status'] ) . "'";

    $sql .= ", link_randkey='" . $db->escape( $data['link_randkey'] ) . "'";

    // $sql .= ", link_votes=''"; // Need to update later

    $sql .= ", link_karma='" . $db->escape($storyKarma) . "'"; // Need to update while vote

    $sql .= ", link_modified=NOW()";

    $sql .= ", link_date=NOW()";

    // $sql .= ", link_published_date=NOW()"; // Need to update later

    $sql .= ", link_category='" . $db->escape( $data['link_category'] ) . "'";

    $sql .= ", link_url='" . $db->escape( $data['link_url'] ) . "'";

    $sql .= ", link_title='" . $db->escape( $data['link_title'] ) . "'";

    $sql .= ", link_title_url='" . $db->escape( $data['link_title_url'] ) . "'";

    $sql .= ", link_content='" . $db->escape( $data['link_content'] ) . "'";

    $sql .= ", link_summary='" . $db->escape( $data['link_summary'] ) . "'";

    $sql .= ", link_group_id='" . $db->escape( $data['link_group_id'] ) . "'";

    $id = $db->query_insert( $sql );

    if ($id) {
        $hooks->do_action(
            'saved_story', 
            [
                'story_id' => $id,
                'story_status' => $data['link_status'],
                'user_id' => $current_user->user_id,
            ]
        );
    } else {
        kahuk_log_queries("Insert New Story Failed!\nSQL: {$sql}");
    }

    return $id;
}


/**
 * Delete Story
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_delete_story($story_or_id) {
	global $db, $hooks;

    $story = [];

    if (!is_array($story_or_id)) {
        $story = kahuk_story($story_or_id);

        if (empty($story)) {
            // TODO :: Log Story Not Found
        }
    } else {
        $story = $story_or_id;
    }

    $story_id = sanitize_number($story["link_id"]);

    // Delete Reactions
    kahuk_delete_reactions(["story_id" => $story_id]);

    // Delete Forks
    kahuk_delete_forks(["story_id" => $story_id]);

    // Delete story
    $sql = "DELETE FROM " . table_links . " WHERE link_id='" . $db->escape($story_id) . "'";
    $rs = $db->query($sql);

    // echo "<pre>{$sql}</pre>";

    if ($rs == 1) {
        $hooks->apply_filters(
            "deleted_story", 
            $story
        );

        return true;
    } else {
        if ($rs > 1) {
            kahuk_log_unexpected("DB Warning: [output: {$rs}]\nSQL: {$sql}");
        }

        return false;
    }
}


/**
 * Delete Stories By User
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_delete_stories_by_user($user) {
	global $globalStories;

    $args = [
        "link_author" => $user["user_id"],
        "link_status" => kahuk_story_statuses(false),
        "pagination_enabled" => false,
    ];

    $stories = $globalStories->get_stories($args);

    $total_deleted = 0;
    $deleteSuccess = [];
    $deleteError = [];
    // $storyIds = [];

    // foreach($stories as $story) {
    //     $storyIds[] = $story["link_id"];
    // }

    foreach($stories as $story) {
        $rs = kahuk_delete_story($story);

        if ($rs) {
            $total_deleted++;
            $deleteSuccess[] = $story;
        } else {
            $deleteError[] = $story;
        }
    }

    $output = [
        "total_deleted" => $total_deleted,
        "success" => $deleteSuccess,
        "error" => $deleteError,
    ];

    // print_r($output);

    return $output;
}


/**
 * Delete Stories By User
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_delete_stories_by_status($status, $limit = 100) {
	global $globalStories;

    $statuses = kahuk_story_statuses(false);

    if (!in_array($status, $statuses)) {
        // TODO:: Log Invalid Request
    }

    $args = [
        "link_status" => [$status],
    ];

    if ($limit == 0) {
        $args["pagination_enabled"] = false;
    } else {
        $args["page_size"] = $limit;
    }

    $stories = $globalStories->get_stories($args);

    $total_deleted = 0;
    $deleteSuccess = [];
    $deleteError = [];

    foreach($stories as $story) {
        $rs = kahuk_delete_story($story);

        if ($rs) {
            $total_deleted++;
            $deleteSuccess[] = $story;
        } else {
            $deleteError[] = $story;
        }
    }

    $output = [
        "total_deleted" => $total_deleted,
        "success" => $deleteSuccess,
        "error" => $deleteError,
    ];

    return $output;
}
