<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes KahukUsers Class
 *
 * @return KahukUsers
 */
function kahuk_users_init() {
    return KahukUsers::init();
}

/**
 * 
 */
class KahukUsers
{
    public $errors;

    public $cachedItems = []; // Cached users by user integer id

    /**
     * Class construcotr
     */
    private function __construct() {
        //
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Retrieve user from database
     * 
     * @return array
     */
    public function get_user( $argsCustom = [] ) {
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
            return [];
        }

        $sql = "SELECT * FROM " . table_users . " " . $where;

        $output = $db->get_row( $sql, ARRAY_A );

        if ($args['debug']) {
            echo "<pre class=\"debug\">SQL: {$sql}<br>";
            print_r($args);
            print_r($output);
            echo "</pre>";
        }

        return $output;
    }

    /**
     * Check the user in $cachedItems variable first
     * If not exist in $cachedItems; consider query to the database
     * 
     * @return array
     */
    public function get_item( $id, $argsCustom = [] ) {
        $output = [];

        $defaults = [
            "debug" => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        if ( isset( $this->cachedItems[$id] ) ) {
            $output = $this->cachedItems[$id];

            if ($args['debug']) {
                echo "<p class=\"debug\">User returned from cache</p>";
            }
        } else {
            $args["user_id"] = $id;

            $output = $this->get_user($args);

            if ($output) {
                $this->cachedItems[$id] = $output;
            }

            if ($args['debug']) {
                echo "<p class=\"debug\">User returned from database</p>";
            }
        }

        return $output;
    }


    /**
     * 
     * 
     * @return array
     */
    public function get_user_profile( $id, $argsCustom = [] ) {
        $defaults = [
            "debug" => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        $user = $this->get_item($id, $args);

        $output = [];
        $output["user_id"] = $user['user_id'];
        $output["user_login"] = $user['user_login'];
        $output["user_level"] = $user['user_level'];
        $output["user_names"] = (empty($user['user_names']) ? $user['user_login'] : $user['user_names']);
        $output["user_email"] = $user['user_email'];

        $output["user_profile_url"] = getmyurl('user', $user['user_login']);
        $output["user_profile_gravatars"] = kahuk_gravatar($user['user_email']);

        return $output;
    }
}

// Set the global variables to access users class
$globalUsers = kahuk_users_init();
