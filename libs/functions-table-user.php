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


/**
 * Get user record
 * 
 * Depricated in favour of the get_user() function in KahukUsers class
 * 
 * @since 5.0.0
 */
function kahuk_get_user( $argsCustom = [] ) {
    global $db;

    $defaults = [
        'user_login'      => '',
		'user_id'         => '',

        "debug" => false,
    ];

	$args = array_merge($defaults, $argsCustom);
    $where = '';

    if ( !empty( $args['user_login'] ) ) {
        $where = "WHERE user_login = '" . $db->escape( $args['user_login'] ) . "'";
    }

    if ( !empty( $args['user_id'] ) ) {
        $where = "WHERE user_id = '" . $db->escape( $args['user_id'] ) . "'";
    }

    if ( empty($where) ) {
        return '';
    }

    $sql = "SELECT * FROM " . table_users . " " . $where;

    $output = $db->get_row( $sql, ARRAY_A );

    if ($args['debug']) {
        echo "<pre>SQL: {$sql}<br>";
        print_r($args);
        print_r($output);
        echo "</pre>";
    }

    return $output;
}


/**
 * 
 * 
 * @since 5.0.0
 */
function kahuk_user_email_by_userlogin( $userlogin ) {
    $output = kahuk_get_user( [ 'user_login' => $userlogin ]  );

    if ( $output ) {
        return $output['user_email'];
    }

    return false;
}



/**
 * 
 * 
 * @since 5.0.0
 */
function kahuk_user_email_by_id( $userid ) {
    $output = kahuk_get_user( [ 'user_id' => $userid ]  );

    if ( $output ) {
        return $output['user_email'];
    }

    return false;
}
