<?php

/* ===================== STORY ===================== */

/**
 * Hook Function
 * Add additional information into story record
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_process_story_callback($story) {
    if (!$story) {
        return $story;
    }

    $story_id = $story['link_id'];
    $story_url = kahuk_permalink_story($story);

    $story['is_rtl'] = kahuk_is_rtl($story['link_title']);

    $story['story_url'] = $story_url;
    $story['story_url_domain'] = kahuk_shorten_the_url($story['link_url']);

    $story['author_url'] = kahuk_create_url("user/" . $story['user_login'] . "/", []);

    $story['submit_timeago'] = txt_time_diff(strtotime($story['link_date']));
    
    
    //
    $story['category_primary'] = [];
    $cat_primary = kahuk_get_category_by_id($story['link_category']);

    if ($cat_primary) {
        $story['category_primary']['url'] = $cat_primary['url'];
        $story['category_primary']['category_name'] = $cat_primary['category_name'];
    } else {
        kahuk_log_debug("Story: [{$story_id}: " . $story_url . "] does not have a category!!");
    }

    //
    $story['comment_count'] = kahuk_count_comments($story);

    //
    $story['reactions_count'] = kahuk_count_reactions_by_story($story_id);

    // Need to fill-up when user is logged in
    $story['user_acted'] = [];

    if (kahuk_is_user_authenticated()) {
        $user_id = kahuk_session_user_id();

        //
        $story['story_edit'] = "";

        if (
            kahuk_check_user_role("admin") || 
            kahuk_check_user_role("moderator") || 
            ($user_id == $story['link_author'])
        ) {
            $story['story_edit'] = add_query_arg(['prefix' => 'edit'], $story_url);
        }

        //
        $story['user_acted']['saved_story'] = kahuk_is_saved_story($story_id, $user_id);

        $customArgs = [
            'story_id' => $story_id,
            'user_id' => $user_id,
        ];
        $reaction = kahuk_get_reaction($customArgs);

        $story['user_acted']['did_react'] = false;
        $story['user_acted']['vote_reaction'] = '';

        if ($reaction) {
            $story['user_acted']['did_react'] = true;
            $story['user_acted']['vote_reaction'] = $reaction['vote_reaction'];
        }
        

        // TODO
        // $story['user_acted']['shareable_groups'] = kahuk_get_groups_to_share_story_for_user($story_id, $user_id);
    }

    // print_r($story);

    return $story;
}

$hooks->add_filter('process_story', 'kahuk_process_story_callback');


/**
 * Hook Function
 * Increase karma value and total vote for story
 * Increase karma value for user
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_reaction_inserted_callback($output = [], $args = []) {
    $is_increase = true;
    $reactionKarmaStory = kahuk_get_config("_reaction_karma_for_story", "0");
    $reactionKarmaUser = kahuk_get_config("_reaction_karma_for_user", "0");

    //
    kahuk_update_story_karma($args['story_id'], $reactionKarmaStory, $is_increase);

    // Update user karma
    kahuk_update_user_karma($args['user_id'], $reactionKarmaUser, $is_increase);

    return $output;
}

$hooks->add_filter("reaction_inserted", "kahuk_reaction_inserted_callback");



/**
 * Hook Function
 * Check story karma to update story status when karma reach to certain point.
 * 
 * @since 5.0.7
 * 
 * @return array story record
 */
function kahuk_refactor_story_status_callback($args = []) {
    global $db;
    $story = kahuk_story($args['story_id']);
    $publishedKarma = kahuk_get_config("_new_to_published_karma");

    if (
        ($story["link_status"] == "new") &&
        (sanitize_number($story["link_karma"]) > sanitize_number($publishedKarma))
    ) {
        $sql = "UPDATE " . table_links . "";
        $sql .= " SET link_status = 'published'";
        $sql .= ", link_published_date = NOW()";

        $sql .= " WHERE link_id = '" . $db->escape($args['story_id']) . "'";

        // Execute Query
        $rs = $db->query($sql);

        if ($rs == 1) {
            $story["link_status"] = "published";
        } else {
            if ($rs > 1) {
                kahuk_log_debug("Issue Multi record Updated! [OUTPUT: {$rs}] \nSQL: {$sql}");
            } else {
                kahuk_log_debug("Update Failed! [OUTPUT: {$rs}] \nSQL: {$sql}");
            }
        }
    }

    return $story;
}

$hooks->add_filter('refactor_story_status', 'kahuk_refactor_story_status_callback');


/**
 * Hook Function
 * Create vote for newly created story
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_create_vote_callback($customArgs = []) {
    $defaultData = [
        'story_id' => 0, // Required
        'user_id' => 0, // Required
    ];

    $args = array_merge( $defaultData, $customArgs );

    $args['reaction'] = 'positive';

    kahuk_save_reaction($args);
}

$hooks->add_action('saved_story', 'kahuk_create_vote_callback');


/**
 * Hook Function
 * Regenerate The Total Number of stories by story Status
 * 
 * @since 5.0.7
 * 
 * @return integer|boolean
 */
function kahuk_increase_story_total_callback($argsCustom = []) {
    kahuk_update_stories_total($argsCustom['story_status']);
}

$hooks->add_action('saved_story', 'kahuk_increase_story_total_callback');


/* ===================== FORK STORY ===================== */

/**
 * Hook Function
 * Update karmas on fork story
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_saved_saved_story_callback($data) {
    $is_increase = ($data["saved_status"] == "enable");
    $forkKarmaStory = kahuk_get_config("_fork_karma_for_story", "0");
    $forkKarmaUser = kahuk_get_config("_fork_karma_for_user", "0");

    //
    kahuk_update_story_karma($data['story_id'], $forkKarmaStory, $is_increase);

    //
    kahuk_update_user_karma($data['user_id'], $forkKarmaUser, $is_increase);
}

$hooks->add_action('saved_saved_story', 'kahuk_saved_saved_story_callback');


/**
 * Hook Function
 * Update karmas on update fork/unfork story
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_updated_saved_story_callback($data) {

    // We only proceed when status changed
    if ($data["saved_status_prev"] && ($data["saved_status"] !== $data["saved_status_prev"])) {
        $is_increase = ($data["saved_status"] == "enable");
        $forkKarmaStory = kahuk_get_config("_fork_karma_for_story", "0");
        $forkKarmaUser = kahuk_get_config("_fork_karma_for_user", "0");

        //
        kahuk_update_story_karma($data['story_id'], $forkKarmaStory, $is_increase);

        //
        kahuk_update_user_karma($data['user_id'], $forkKarmaUser, $is_increase);
    }
}

$hooks->add_action('updated_saved_story', 'kahuk_updated_saved_story_callback');



/* ===================== COMMENT ===================== */

/**
 * Hook Function
 * Add additional information into comment record
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_process_comment_callback($comment) {
    if (!isset($comment['user_email'])) {
        return $comment;
    }

    $comment['user_gravatars'] = kahuk_gravatar($comment['user_email']);
    $comment['user_profile_url'] = kahuk_url_user_profile($comment['user_login']);

    $user_names = (empty($comment['user_names']) ? $comment['user_login'] : $comment['user_names']);
    $comment['user_names'] = $user_names;

    $comment['comment_age'] = txt_time_diff(strtotime($comment['comment_date']));

    return $comment;
}

$hooks->add_filter('process_comment', 'kahuk_process_comment_callback');

/**
 * Hook Function
 * Increase karma value for comment deleted
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_saved_comment_callback($argsCustom = []) {
    $defaults = [
        'comment_id' => 0,
        'story_id' => 0,
        'user_id' => 0,
    ];

    $args = array_merge($defaults, $argsCustom);

    if (!$args['comment_id'] || !$args['story_id'] || !$args['user_id']) {
        kahuk_log_debug("Invalid data for kahuk_saved_comment_callback()\n" . print_r($args, true));
        return false;
    }

    //
    $commentKarmaUser = kahuk_get_config("_comment_karma_for_user", "0");
    kahuk_update_user_karma($args['user_id'], $commentKarmaUser);

    //
    $commentKarmaStory = kahuk_get_config("_comment_karma_for_story", "0");
    kahuk_update_story_karma($args['story_id'], $commentKarmaStory);

    return true;
}

$hooks->add_filter('saved_comment', 'kahuk_saved_comment_callback');

/**
 * Hook Function
 * Decrease karma value for comment deleted
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_deleted_comment_callback($argsCustom = []) {
    $defaults = [
        'comment_id' => 0,
        'story_id' => 0,
        'user_id' => 0,
    ];

    $args = array_merge($defaults, $argsCustom);

    if (!$args['comment_id'] || !$args['story_id'] || !$args['user_id']) {
        kahuk_log_debug("Invalid data for kahuk_saved_comment_callback()\n" . print_r($args, true));
        return false;
    }

    //
    $commentKarmaUser = kahuk_get_config("_comment_karma_for_user", "0");
    kahuk_update_user_karma($args['user_id'], $commentKarmaUser, false);

    //
    $commentKarmaStory = kahuk_get_config("_comment_karma_for_story", "0");
    kahuk_update_story_karma($args['story_id'], $commentKarmaStory, false);

    return true;
}

$hooks->add_filter('deleted_comment', 'kahuk_deleted_comment_callback');
