<?php
/**
 * Edit user karma
 * 
 * @since 5.0.0
 */
function kahuk_update_user_karma( $is_add, $karma_value ) {
    global $db, $current_user;

    // SQL Query
    $sql = "UPDATE `" . table_users . "` SET";

    if ( $is_add ) {
        $sql .= " user_karma = user_karma+" . intval( $karma_value );
    } else {
        $sql .= " user_karma = user_karma-" . intval( $karma_value );
    }
    
    $sql .= " WHERE user_id = " . $db->escape( $current_user->user_id );
    

    // Execute Query and return
	return $db->query( $sql );
}
