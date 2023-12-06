<?php

/**
 * Array of values for comment status
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_comments_statuses() {
    global $hooks;

    $output = [
        'await', 'published', 'trash',
    ];

    return $hooks->apply_filters('comments_statuses', $output);
}

/**
 * Default status for new comment
 * 
 * @since 5.0.7
 * 
 * @return string
 */
function kahuk_comment_status_default() {
    global $hooks;

    return $hooks->apply_filters('comments_status_default', 'published');
}

/**
 * Array of values for story status
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_story_statuses($public = true) {
    global $hooks;

    $output = [
        'new','published',
    ];

    if (!$public) {
        $output = [
            'new','published','page','trash','draft',
        ];
    }

    return $hooks->apply_filters('story_statuses', $output);
}

/**
 * Default ststus for new story
 * 
 * @since 5.0.7
 * 
 * @return string
 */
function kahuk_story_status_default() {
    global $hooks;

    return $hooks->apply_filters('story_status', 'new');
}


/**
 * Array of values for vote reactions
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_vote_reactions($isAll = false) {
    global $hooks;

    $output = [
        'positive','negative',
    ];

    if ($isAll) {
        $output[] = 'invalid';
    }

    return $hooks->apply_filters('vote_reactions', $output);
}

/**
 * Array of values for saved stories status
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_saved_story_statuses() {
    global $hooks;

    $output = [
        'enable','disable',
    ];

    return $hooks->apply_filters('saved_story_statuses', $output);
}

/**
 * Default status for new saved story
 * 
 * @since 5.0.7
 * 
 * @return string
 */
function kahuk_saved_story_status_default() {
    global $hooks;

    return $hooks->apply_filters('saved_story_status_default', 'enable');
}

/**
 * Default privacy for new saved story
 * 
 * @since 5.0.7
 * 
 * @return string
 */
function kahuk_saved_story_privacy_default() {
    global $hooks;

    return $hooks->apply_filters('saved_story_privacy_default', 'public');
}
