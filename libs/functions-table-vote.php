<?php
/**
 * Get row by Array Data
 * 
 * @since 5.0.0
 */
function kahuk_find_vote( $initialData ) {
	global $db;

    $defaultData = [
        'vote_type' => 'links',
        'vote_link_id' => 0, // Required
        'vote_user_id' => 0, // Required
    ];

    $data = array_merge( $defaultData, $initialData );

    // SQL Query
    $sql = "SELECT * FROM " . table_votes . " WHERE vote_type = '" . $db->escape( $data['vote_type'] ) . "'";

    $sql .= " AND vote_link_id = " . $db->escape( $data['vote_link_id'] );

    $sql .= " AND vote_user_id = " . $db->escape( $data['vote_user_id'] );

    // Execute Query and return
    return $db->get_row( $sql, ARRAY_A );
}

/**
 * Insert new record in votes table
 * 
 * `vote_id` int(20) NOT NULL auto_increment,       // PRIMARY KEY
 * `vote_type` enum('links','comments') NOT NULL default 'links',
 * `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * `vote_link_id` int(20) NOT NULL default '0',
 * `vote_user_id` int(20) NOT NULL default '0',
 * `vote_value` smallint(11) NOT NULL default '1',
 * `vote_karma` int(11) NULL default '0',       // depricated
 * `vote_ip` varchar(64) default NULL,
 * 
 * @since 5.0.0
 */
function kahuk_insert_vote( $initialData ) {
    global $db, $current_user;

    $defaultData = [
        'vote_type' => 'links',
        'vote_link_id' => 0, // Required
        'vote_value' => 10,
        'vote_user_id' => '',
    ];

    $data = array_merge( $defaultData, $initialData );

    $ip = check_ip_behind_proxy();

    $vote_user_id = empty( $data['vote_user_id'] ) ? $current_user->user_id : $data['vote_user_id'];

    // SQL Query
    $sql = "INSERT INTO `" . table_votes . "` SET vote_user_id = '" . $db->escape( $vote_user_id ) . "'";

    $sql .= ", vote_type = '" . $db->escape( $data['vote_type'] ) . "'";

    $sql .= ", vote_date = NOW()";

    $sql .= ", vote_link_id = " . $db->escape( $data['vote_link_id'] );

    $sql .= ", vote_value = '" . $db->escape( $data['vote_value'] ) . "'";

    $sql .= ", vote_karma = 0"; // depricated

    $sql .= ", vote_ip = '" . $db->escape( $ip ) . "'";

    // Execute Query
	$output = $db->query( $sql );
        
	if ( 1 == $output ) {
		return $db->insert_id;
	} else {
        kahuk_error_log( "SQL: {$sql}\n Expected output: 1 \n Found: {$output}", __LINE__, __FUNCTION__, __FILE__ );
    }

    return false;
}

/**
 * 
 */
function kahuk_delete_vote( $data ) {
    global $db, $current_user;

    // SQL Query
    $sql = "DELETE FROM `" . table_votes . "` WHERE vote_user_id = " . $db->escape( $current_user->user_id );

    $sql .= " AND vote_link_id = " . $db->escape( $data['vote_link_id'] );

    // Execute Query and return
	return $db->query( $sql );
}
