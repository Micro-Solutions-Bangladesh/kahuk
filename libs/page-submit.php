<?php
/**
 * 
 */
function kahuk_link_summary( $content ) {
    if (utf8_strlen($content) > StorySummary_ContentTruncate) {
        $content = truncate_strings_html( $content, StorySummary_ContentTruncate );
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
 * 
 */
function kahuk_unique_title_slug( $title_slug ) {
    $countSlug = kahuk_link_title_url( $title_slug, false );

    if ( 0 == $countSlug ) {
        return $title_slug;
    }

    $maxNumberOfDuplicateTitle = MAX_NUMBER_OF_DUPLICATE_TITLE;

    $uniqueSlug = '';
    $slugNameCheck = true;
    $maxLoopCount = 1;

    do {
        $proposedSlug = $title_slug . "-" . ( $maxLoopCount + 1 );
        // echo "proposedSlug: " . $proposedSlug . "<br>";
        $countSlugNew = kahuk_link_title_url( $proposedSlug, false );

        if ( 0 == $countSlugNew ) {
            $slugNameCheck = false;
            $uniqueSlug = $proposedSlug;
        }

        //
        $maxLoopCount++;

        if ( $maxNumberOfDuplicateTitle < $maxLoopCount ) {
            $slugNameCheck = false;
            $uniqueSlug = false;

            kahuk_debug_log( "Fail to create new slug for {$title_slug}", __LINE__, __FILE__, __FILE__ );
        }
    } while ( $slugNameCheck );

    return $uniqueSlug;
}
