<?php
/**
 * Create an array of user information including profile url, avatar image url, etc.
 * 
 * @return array
 */
function process_user_profile($user, $allData = false) {
    global $globalUsersObj;

    return $globalUsersObj->process_user_profile($user, $allData);
}

/**
 * Edit user karma
 * 
 * @since 5.0.0
 * @updated 5.0.7
 * 
 * @return array
 */
function kahuk_update_user_karma($user_id = 0, $karma_value, $is_increase = true) {
    global $db, $hooks, $current_user;

    if (!$user_id) {
        $user_id = $current_user->user_id;
    }

    // SQL Query
    $sql = "UPDATE `" . table_users . "` SET";

    if ( $is_increase ) {
        $sql .= " user_karma = user_karma+" . intval( $karma_value );
    } else {
        $sql .= " user_karma = user_karma-" . intval( $karma_value );
    }
    
    $sql .= " WHERE user_id = " . $db->escape($user_id);
    
    // Execute Query
	$rs = $db->query($sql);

    $output = [
        'success' => $rs,
        'user_id' => $user_id,
        'karma_value' => $karma_value,
        'is_increase' => $is_increase,
    ];

    $hooks->apply_filters("user_karma_updated", $output);

    return $output;
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

    return $db->get_row( $sql, ARRAY_A );
}


/**
 * Retrun user_login id using query user_id
 * 
 * @since 5.0.6
 * 
 * @return string|empty
 */
function kahuk_user_login_by_id( $user_id ) {
    if (!is_numeric($user_id)) {
		kahuk_log_debug("Function: kahuk_user_login_by_id() \n Expected: user_id should be number \n Found: {$user_id}");
	}

    $output = kahuk_get_user( [ 'user_id' => $user_id ]  );

    if ( $output ) {
        return $output['user_login'];
    }

    return '';
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

    return '';
}

/**
 * Get user email by user ID
 * 
 * @since 5.0.0
 */
function kahuk_user_email_by_id( $userid ) {
    $output = kahuk_get_user( [ 'user_id' => $userid ]  );

    if ( $output ) {
        return $output['user_email'];
    }

    return '';
}



/**
 * Get karma value by user id
 * 
 * @return int number
 */
function kahuk_user_karma($id) {
    global $db;

    $sql = "SELECT user_karma FROM " . table_users . " WHERE user_id = '" . $db->escape($id) . "'";

    $rs = $db->get_row($sql, ARRAY_A);

    $output = 0;

    if ($rs) {
        $output = $rs["user_karma"];
    }

    return (int) $output;
}

/**
 * Create a code to sent user to verify email address
 * 
 * @since 5.0.6
 * 
 * @return string
 */
function kahuk_create_user_verification_code($user_or_login) {
    $output = '';
    $user = [];

    if (is_array($user_or_login) && isset($user_or_login['user_login'])) {
        $user = $user_or_login;
    } else {
        $user = kahuk_get_user(['user_login' => $user_or_login]);
    }

    return md5($user['user_login'] . $user['user_email'] . $user['user_ip'] . time() . kahuk_hash());
}


/**
 * 
 */
function kahuk_insert_user( $initialData ) {
    global $db;

    $defaultData = [
        'user_login' => '',
        'user_email' => '',
        'user_pass' => '',
        'user_date' => '',
        'user_ip' => '',
        'verification_code' => '',

        // 'user_level' => 'normal',
    ];

    $data = array_merge( $defaultData, $initialData );

    // SQL Query
    // $sql = "INSERT INTO " . table_users . " SET";
    $sql = "INSERT INTO `" . table_users . "` SET user_login='" . $db->escape( $data['user_login'] ) . "'";

    // $sql .= ", user_email='" . $db->escape( $data['user_email'] ) . "'";


    // $isFirst = true;


    $hasError = false;

    $requiredFields = [
        'user_login',
        'user_email',
        'user_pass',
    ];

    $dateFields = [
        'user_date'
    ];

    foreach($data as $index => $value) {
        if (in_array($index, $requiredFields)) {
            if (empty($value)) {
                $hasError = true;
                // break;
            }
        }

        if ($index == 'user_login') {
            continue;
        }

        // Salt the user password
        if ($index == 'user_pass') {
            $sql .= ", {$index} = '" . kahuk_hashed_password( $value ) . "'";
            continue;
        }

        if (in_array($index, $dateFields)) {
            if (empty($value)) {
                $sql .= ", {$index} = NOW()";
                continue;
            }
        }

        $sql .= ", {$index} = '" . $db->escape( $value ) . "'";
    }

    // Required data not found
    if ($hasError) {
        kahuk_log_queries("Record insert skipped!\n{$sql}");
        return false;
    }

    // Execute Query
	$output = $db->query( $sql );
        
	if ( 1 == $output ) {
		return $db->insert_id;
	} else {
        kahuk_log_queries("Failed to insert record!\n{$sql}");
    }

    return false;
}
