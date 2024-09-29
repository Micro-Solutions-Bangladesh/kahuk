<?php
/**
 * 
 */
function kahuk_link_summary( $content ) {
    if (utf8_strlen($content) > MAX_NUMBER_OF_WORD_STORY_DESC) {
        $content = truncate_strings_html( $content, MAX_NUMBER_OF_WORD_STORY_DESC );
    }

    return $content;
}


/**
 * 
 */
function kahuk_create_url_records( $randkey, $url ) {
    if ( isset( $_COOKIE['mnm_user'] ) ) {
        $uniqueKey = "kahuk" . $_COOKIE['mnm_user'] . $randkey;
        setcookie( $uniqueKey, $url, time()+600 );  /* expire in 10 minutes */

        return true;
    }

    return false;
}


/**
 * 
 */
function kahuk_has_url_in_record( $randkey, $url ) {
    $output = false;

    if ( isset( $_COOKIE['mnm_user'] ) ) {
        $uniqueKey = "kahuk" . $_COOKIE['mnm_user'] . $randkey;

        if ( isset( $_COOKIE[$uniqueKey] ) && ( $url == $_COOKIE[$uniqueKey] ) ) {
            $output = true;
            setcookie( $uniqueKey, '', time()-600 );  // Set Expired by 10 minutes
        }
    }

    return $output;
}

/**
 * Check for the uniqueness of story title, slug and url
 * 
 * @updated 5.0.6
 * @since 5.0.1
 * 
 * @return array
 */
function kahuk_check_unique_story( $story_url, $story_slug, $skipId = [] ) {
    $output = [
        'status' => false
    ];

    //
    $storyCountByUrl = kahuk_count_story_by_url( $story_url );
    $maxSameUrl = kahuk_get_config("_max_same_url", "2", "number");

    if ($maxSameUrl <= $storyCountByUrl) {
        $output['code'] = "duplicate_url_reject";
        $output['message'] = $maxSameUrl . " or more stories found with the submitted url."; // TODO dynamic

        return $output;
    }

    //
    $storyCountBySlug = kahuk_count_story_by_slug( $story_slug, ['not_id' => $skipId] );
    $maxSameTitle = kahuk_get_config("_max_same_title", "1", "number");

    if ($maxSameTitle <= $storyCountBySlug) {
        $output['code'] = "duplicate_title_reject";
        $output['message'] = "Seems like matching title for the story exist, please try to make it unique and more readable."; // TODO dynamic

        return $output;
    }

    //
    $stroy_slug_new = kahuk_unique_title_slug( $story_slug );

    if ( empty( $stroy_slug_new ) ) { // This should not happen // TODO
        $output['code'] = "story_slug_fail";
        $output['message'] = sprintf( 'Sorry, We are unable to create an unique slug from your title!', $story_url ); // TODO dynamic

        kahuk_log_debug("Unable to create unique slug from: [story_slug: {$story_slug}, story_url: {$story_url}]");
    } else {
        $output['status'] = true;
        $output['story_slug'] = $stroy_slug_new;
    }

    return $output;
}

/**
 * 
 */
function kahuk_unique_title_slug( $title_slug ) {
    $countSlug = kahuk_count_story_by_slug( $title_slug );

    if ( 0 == $countSlug ) {
        return $title_slug;
    }

    $maxSameUrl = kahuk_get_config("_max_same_url", "2", "number");

    $uniqueSlug = '';
    $slugNameCheck = true;
    $maxLoopCount = 1;

    do {
        $proposedSlug = $title_slug . "-" . ( $maxLoopCount + 1 );
        // echo "proposedSlug: " . $proposedSlug . "<br>";
        $countSlugNew = kahuk_count_story_by_slug( $proposedSlug );

        if ( 0 == $countSlugNew ) {
            $slugNameCheck = false;
            $uniqueSlug = $proposedSlug;
        }

        //
        $maxLoopCount++;

        if ( $maxSameUrl < $maxLoopCount ) {
            $slugNameCheck = false;
            $uniqueSlug = '';

            kahuk_log_debug( "Fail to create new slug for {$title_slug}");
        }
    } while ( $slugNameCheck );

    return $uniqueSlug;
}
