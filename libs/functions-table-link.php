<?php
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
function kahuk_count_story_by_slug( $story_slug, $statuses = [], $isLike = false ) {
	global $db;

    $where = "link_title_url = '" . $db->escape( $story_slug ) . "'";

    if ( $isLike ) {
        $where = "link_title_url LIKE '" . $db->escape( $story_slug ) . "%'";
    }

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
 * Change story status depending on karma value
 * 
 * @since 5.0.4
 * 
 * @return string Mentioning the status updated as yes, not updated as no, and not applicable as na
 */
function kahuk_change_story_status( $story_id ) {
    global $db, $globalStory;

    if ( ! $globalStory || ( $story_id != $globalStory['link_id'] ) ) {
        $globalStory = kahuk_get_story_by_id( $story_id );
    }

    /**
     * Check if the story status is NEW and the karma meet score to be PUBLISHED
     */
    if ( ( "new" == $globalStory['link_status'] ) && ( STORY_KARMA_PUBLISHED <= intval( $globalStory['link_karma'] ) ) ) {
        // SQL Query
        $sql = "UPDATE `" . table_links . "` SET";

        $sql .= " link_status = 'published', link_published_date=FROM_UNIXTIME(". time() .")";
        
        $sql .= " WHERE link_id = " . intval( $story_id );

        // Execute Query and return
        $rs = $db->query( $sql );

        if ( 0 == $rs ) {
            kahuk_error_log( "FAIL to Change Story Status:\nQuery: {$sql}", __FILE__, __LINE__ );

            return 'no';
        } else {
            return 'yes';
        }
    }

    return 'na';
}

/**
 * Edit story karma and vote
 * 
 * @since 5.0.0
 * 
 * @return array link table row or false on failing to update the row
 */
function kahuk_update_story_karma( $link_id, $is_add, $karma_value ) {
    global $db;

    $post = kahuk_get_story_by_id( $link_id );
    $link_votes_new = intval( $post['link_votes'] );

    $sql = "UPDATE `" . table_links . "` SET";

    //
    $karma_value_new = 0.00;

    if ( $is_add ) {
        $karma_value_new = floatval( $post['link_karma'] ) + floatval( $karma_value );
    } else {
        $karma_value_new = floatval( $post['link_karma'] ) - floatval( $karma_value );
    }

    $karma_value_new = number_format( $karma_value_new, 2, ".", "" );
    $sql .= " link_karma = '" . $db->escape( $karma_value_new ) . "'";

    // Check if the karma score for voting/unvoting action not for favorite/unfavorite action
    if ( LINK_VOTE_KARMA == $karma_value ) {
        if ( $is_add ) {
            $link_votes_new = ( $link_votes_new + 1 );
        } else {
            $link_votes_new = ( $link_votes_new - 1 );
        }

        $sql .= ", link_votes = " . $link_votes_new;
    }

    $sql .= " WHERE link_id = " . intval( $link_id );
    
    // Execute Query and return
	$rs = $db->query( $sql );

    if ( 1 == $rs ) {
        $post['link_votes'] = $link_votes_new;
        $post['link_karma'] = $karma_value_new;

        return $post;
    } else {
        kahuk_error_log( "Query: {$sql}\nOutput: {$rs}", __FILE__, __LINE__ );

        return false;
    }
}

/**
 * 
 * Required columns:
 *  link_author:
 *  link_status: enum( new, discard, published, abuse, duplicate, page, spam, moderated, draft, sheduled )
 *  link_randkey: int default 0 // may be no more in use
 *  link_votes: int default 0 // not require into INSERT query
 *  link_reports: int default 0 // Not Sure How it is used
 *  link_comments: int default 0
 *  link_karma: decimal(10,2) default 0.0 // not require into INSERT query
 *  link_modified: timestamp default CURRENT_TIMESTAMP
 *  link_date: timestamp default null
 *  link_published_date: timestamp default null
 *  link_category: int
 *  link_lang: int default 1 // Not Sure How it is used
 * 
 *  link_url: varchar(255)
 *  link_url_title: text
 *  link_title: text
 *  link_title_url: varchar(255)        // post slug
 *  link_content: mediumtext
 *  link_summary: text
 *  link_tags: text
 * 
 *  link_group_id: int
 *  link_group_status: enum('new', 'published', 'discard') default new
 *  link_out: int default 0
 * 
 *  link_field1: varchar(255)
 *  link_field2: varchar(255)
 *  link_field3: varchar(255)
 *  link_field4: varchar(255)
 *  link_field5: varchar(255)
 *  link_field6: varchar(255)
 *  link_field7: varchar(255)
 *  link_field8: varchar(255)
 *  link_field9: varchar(255)
 *  link_field10: varchar(255)
 *  link_field11: varchar(255)
 *  link_field12: varchar(255)
 *  link_field13: varchar(255)
 *  link_field14: varchar(255)
 *  link_field15: varchar(255)
 *
 *
 * @since 5.0.0
 *
 */
function kahuk_insert_story( $initialData ) {
    global $db, $current_user;

    $defaultData = [
        'link_status' => 'new',
        'link_randkey' => '',
        'link_group_id' => 0,
        'link_group_status' => 'new',
    ];

    $data = array_merge( $defaultData, $initialData );


    // SQL Query
    $sql = "INSERT INTO `" . table_links . "` SET link_author='" . $db->escape( $current_user->user_id ) . "'";

    $sql .= ", link_status='" . $db->escape( $data['link_status'] ) . "'";

    $sql .= ", link_randkey='" . $db->escape( $data['link_randkey'] ) . "'";

    // $sql .= ", link_votes=''"; // Need to update later

    // $sql .= ", link_reports=''"; // Need to update later

    // $sql .= ", link_comments=''"; // Need to update later

    $sql .= ", link_karma='1.00'"; // Need to update while vote

    $sql .= ", link_modified=NOW()";

    $sql .= ", link_date=NOW()";

    // $sql .= ", link_published_date=NOW()"; // Need to update later

    $sql .= ", link_category='" . $db->escape( $data['link_category'] ) . "'";

    $sql .= ", link_lang='en'"; // Not Sure How it is used

    $sql .= ", link_url='" . $db->escape( $data['link_url'] ) . "'";

    $sql .= ", link_url_title='" . $db->escape( $data['link_url_title'] ) . "'";

    $sql .= ", link_title='" . $db->escape( $data['link_title'] ) . "'";

    $sql .= ", link_title_url='" . $db->escape( $data['link_title_url'] ) . "'";

    $sql .= ", link_content='" . $db->escape( $data['link_content'] ) . "'";

    $sql .= ", link_summary='" . $db->escape( $data['link_summary'] ) . "'";

    $sql .= ", link_tags='" . $db->escape( $data['link_tags'] ) . "'";

    $sql .= ", link_group_id='" . $db->escape( $data['link_group_id'] ) . "'";

    $sql .= ", link_group_status='" . $db->escape( $data['link_group_status'] ) . "'";

    // $sql .= ", link_out='" . $db->escape( $data['link_out'] ) . "'"; // Not Sure How it is used



    $link_field1 = isset( $data['link_field1'] ) ? $data['link_field1'] : '';
    $sql .= ", link_field1='" . $db->escape( $link_field1 ) . "'";

    $link_field2 = isset( $data['link_field2'] ) ? $data['link_field2'] : '';
    $sql .= ", link_field2='" . $db->escape( $link_field2 ) . "'";

    $link_field3 = isset( $data['link_field3'] ) ? $data['link_field3'] : '';
    $sql .= ", link_field3='" . $db->escape( $link_field3 ) . "'";

    $link_field4 = isset( $data['link_field4'] ) ? $data['link_field4'] : '';
    $sql .= ", link_field4='" . $db->escape( $link_field4 ) . "'";

    $link_field5 = isset( $data['link_field5'] ) ? $data['link_field5'] : '';
    $sql .= ", link_field5='" . $db->escape( $link_field5 ) . "'";

    $link_field6 = isset( $data['link_field6'] ) ? $data['link_field6'] : '';
    $sql .= ", link_field6='" . $db->escape( $link_field6 ) . "'";

    $link_field7 = isset( $data['link_field7'] ) ? $data['link_field7'] : '';
    $sql .= ", link_field7='" . $db->escape( $link_field7 ) . "'";

    $link_field8 = isset( $data['link_field8'] ) ? $data['link_field8'] : '';
    $sql .= ", link_field8='" . $db->escape( $link_field8 ) . "'";

    $link_field9 = isset( $data['link_field9'] ) ? $data['link_field9'] : '';
    $sql .= ", link_field9='" . $db->escape( $link_field9 ) . "'";

    $link_field10 = isset( $data['link_field10'] ) ? $data['link_field10'] : '';
    $sql .= ", link_field10='" . $db->escape( $link_field10 ) . "'";

    $link_field11 = isset( $data['link_field11'] ) ? $data['link_field11'] : '';
    $sql .= ", link_field11='" . $db->escape( $link_field11 ) . "'";

    $link_field12 = isset( $data['link_field12'] ) ? $data['link_field12'] : '';
    $sql .= ", link_field12='" . $db->escape( $link_field12 ) . "'";

    $link_field13 = isset( $data['link_field13'] ) ? $data['link_field13'] : '';
    $sql .= ", link_field13='" . $db->escape( $link_field13 ) . "'";

    $link_field14 = isset( $data['link_field14'] ) ? $data['link_field14'] : '';
    $sql .= ", link_field14='" . $db->escape( $link_field14 ) . "'";

    $link_field15 = isset( $data['link_field15'] ) ? $data['link_field15'] : '';
    $sql .= ", link_field15='" . $db->escape( $link_field15 ) . "'";


    // Execute Query
	$output = $db->query( $sql );
        
	if ( 1 == $output ) {
		return $db->insert_id;
	}

    return false;
}
