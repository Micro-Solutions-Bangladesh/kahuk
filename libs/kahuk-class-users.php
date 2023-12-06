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

    public $cachedItemsByLoginID = []; // Cached users by user login id

    public $userRanks = [];

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
     * 
     */
    public function tableColumns() {
        return [
            'users.user_id',
            'users.user_login',
            'users.user_level',
            'users.user_status', 
            'users.user_modification',
            'users.user_date', 
            'users.user_pass',
            'users.user_email', 
            'users.user_names',
            'users.user_karma', 
            'users.user_url',
            'users.user_lastlogin', 
            'users.user_facebook',
            'users.user_twitter', 
            'users.user_linkedin',

            'users.user_googleplus', 
            'users.user_skype',
            'users.user_pinterest', 
            'users.public_email',
            'users.user_avatar_source', 
            'users.user_ip',
            'users.user_lastip', 
            'users.last_reset_request',
            'users.last_reset_code', 
            'users.user_location',
            'users.user_occupation', 
            'users.user_categories',
            'users.verification_code',
        ];
    }

    /**
     * Build query 
     */
    public function build_query($argsCustom) {
        global $db, $page_size;

        $defaults = [
            "columns"       => [],

            "user_id" => 0,
            "user_email" => "",
            "user_login" => "",

            'user_level' => [],

            "user_status" => [
                "enable"
            ],

            "where_clause" => "",

            "count_query" => false,

            "order_by"      => 'users.user_modification DESC',

            "skip_user_ids" => [],
            "skip_user_logins" => [],

            "pagination_enabled" => true,
            "page_size"    => 0,

            "force_limit" => 0, // Force the rows limit over the global page_size from admin config

            // 'return_type' => 'raw', // profile, raw
            // 'add_extra_data' => true,
        ];

        $args = array_merge($defaults, $argsCustom);

        // echo "<pre>";
        // print_r($args);
        // echo "</pre>";

        if (empty($args['columns'])) {
            $args['columns'] = $this->tableColumns();
        }

        $count_query = $args['count_query'];


        $pagesize = (($args['page_size'] > 0) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        //
        $sql = "SELECT " . implode(',', $args['columns']);

        if ($count_query) {
            $sql = "SELECT count(DISTINCT links.user_id)";
        }

        $sql .= " FROM " . table_users . " AS users";

        $user_statuses = $args['user_status'];

        $where = "WHERE users.user_status IN ('" . implode("','", $user_statuses) . "')";

        if ($args['user_id'] > 0) {
            $where .= " AND users.user_id = " . $db->escape($args['user_id']);
        } else if ($args['user_email']) {
            $where .= " AND users.user_email = '" . $db->escape($args['user_email']) . "'";
        } else if ($args['user_login']) {
            $where .= " AND users.user_login = '" . $db->escape($args['user_login']) . "'";
        
        } else {
            $where .= " AND users.user_level IN ('" . implode("','", $args["user_level"]) . "')";

            if ($args["where_clause"]) {
                $where .= " AND " . $args["where_clause"];
            }
        }

        if (
            !$count_query && !$args['user_id'] 
            && empty($args["user_email"]) && empty($args["user_login"])
        ) {
            if (!empty($args["order_by"])) {
                $where .= " ORDER BY " . $args["order_by"];
            }

            if ($args["pagination_enabled"]) {
                $where .= " LIMIT {$offset}, {$pagesize}";
            }
        }

        if ($where) {
            $sql .= " " . $where;
        }

        // echo "<pre>{$sql}</pre>";

        return $sql;
    }


    /**
     * Retrieve users from database
     * 
     * @return array
     */
    public function get_users($argsCustom = []) {
        global $db;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_stories: {$sql}</pre>";

        //
        $output = $db->get_results($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalUsersObj->get_users [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
    }














    /**
     * Create an array of user information including profile url, avatar image url, etc.
     * 
     * @return array
     */
    public function process_user_profile($user, $allData = false) {
        $output = [];
        $user_id = $user['user_id'];
        $user_karma = $user['user_karma'];

        $output['user_id'] = $user_id;
        $output['user_login'] = $user['user_login'];
        $output['user_level'] = $user['user_level'];
        $output['user_names'] = (empty($user['user_names']) ? $user['user_login'] : $user['user_names']);
        $output['user_email'] = $user['user_email'];
        $output['user_karma'] = $user_karma;

        $output['user_url'] = $user['user_url'];
        $output['user_location'] = $user['user_location'] ?? '';
        $output['user_facebook'] = $user['user_facebook'] ?? '';
        $output['user_twitter'] = $user['user_twitter'] ?? '';
        $output['user_linkedin'] = $user['user_linkedin'] ?? '';
        $output['user_skype'] = $user['user_skype'] ?? '';
        $output['user_pinterest'] = $user['user_pinterest'] ?? '';
        $output['public_email'] = $user['public_email'] ?? '';
        $output['user_occupation'] = $user['user_occupation'] ?? '';
        $output['user_lastip'] = $user['user_lastip'] ?? '';

        if ($allData) {
            $output['user_date'] = $user['user_date'];
        }

        $output['user_date_formatted'] = (isset($user['user_date']) ? kahuk_date($user['user_date']) : '');
        $output['user_profile_url'] = kahuk_url_user_profile($user['user_login']);
        $output['user_gravatars'] = kahuk_gravatar($user['user_email']);
        $output['user_rank'] = $this->get_user_rank(['user_karma' => $user_karma]);

        return $output;
    }



    /**
     * 
     */
    function count_total_votes_by_user($user_id) {
        global $db;

        $storyStatusesPublic = kahuk_story_statuses();

        $sql = "SELECT count(*) FROM " . table_votes . "," . table_links . " WHERE link_status IN ('" . implode("','", $storyStatusesPublic) . "') AND vote_user_id = {$user_id} AND link_id = vote_link_id";
        // echo "<pre>count_total_votes_by_user: {$sql}</pre>";
        return $db->get_var($sql);
    }

    /**
     * 
     */
    function count_total_stories_by_user($user_id) {
        global $db;

        $storyStatusesPublic = kahuk_story_statuses();

        $sql = "SELECT count(*) FROM " . table_links . " WHERE link_author = {$user_id} AND (link_status IN ('" . implode("','", $storyStatusesPublic) . "'))";
        // echo "<pre>count_total_stories_by_user: {$sql}</pre>";
        return $db->get_var($sql);
    }

    /**
     * Calculate user rank by user karma
     * 
     * @since 5.0.6
     * 
     * @return array
     */
    public function calculate_user_ranks_by_karma() {
        global $db;

        $sql = "SELECT user_karma, COUNT(*) FROM ".table_users." WHERE user_karma>0";
        $sql .= " GROUP BY user_karma ORDER BY user_karma DESC";

        $users = $db->get_results($sql, ARRAY_N);

        if ($users) {
            $output = [];
            $rank = 1;

		    foreach ($users as $dbuser) {
				$output[$dbuser[0]] = $rank;
				$rank++;
		    }

            // print_r($output);
            $this->userRanks = $output;
		}
    }

    /**
     * Get user rank
     * 
     * @since 5.0.6
     * 
     * @return integer|false number
     */
    public function get_user_rank($argsCustom) {
        if (empty($this->userRanks)) {
            // Create the array of user ranks by user karma
            $this->calculate_user_ranks_by_karma();
        }

        if (empty($this->userRanks)) {
            kahuk_log_unexpected('User ranks array should never be empty!');
            return false;
        }

        $defaults = [
            'user_login'      => '',
            // 'user_id'         => '',
            // 'user_email'      => '',
            'user_karma'      => 0,
        ];

        $args = array_merge($defaults, $argsCustom);
        $user_karma = 0;

        if ($args['user_karma']>0) {
            $user_karma = $args['user_karma'];
        } else if (!empty($args['user_login'])) {
            $user = $this->get_item(['user_login' => $args['user_login']]);

            if ($user) {
                $user_karma = $user['user_karma'];
            }
        }

        if (array_key_exists($user_karma, $this->userRanks)) {
            return $this->userRanks[$user_karma];
        }

        return false;
    }

    /**
     * Retrieve users from database
     * 
     * @return array
     */
    public function get_users_temporary_func($argsCustom = []) {
        global $db;

        $defaults = [
            // 'columns_all'   => true,
            'columns'       => [
                'user_id',
                'user_login', 'user_level',
                'user_status', ' user_modification',
                'user_date', 'user_pass',
                'user_email', 'user_names',
                'user_karma', 'user_url',
                'user_lastlogin', 'user_facebook',
                'user_twitter', 'user_linkedin',

                'user_googleplus', 'user_skype',
                'user_pinterest', 'public_email',
                'user_avatar_source', 'user_ip',
                'user_lastip', 'last_reset_request',
                'last_reset_code', 'user_location',
                'user_occupation', 'user_categories',
                'verification_code',
            ],

            'user_level'        => [],

            'user_status' => [
                'enable'
            ],

            'where_clause' => '',

            'order_by'      => 'user_modification DESC',

            'skip_user_ids' => [],
            'skip_user_logins' => [],

            'page_size'    => 0,

            'return_type' => 'raw', // profile, raw
            'add_extra_data' => true,
        ];

        $args = array_merge($defaults, $argsCustom);

        if (empty($args['user_status'])) {
            kahuk_log_unexpected("user_status is required. Found the following args in KahukUsers->get_users_temporary_func function:\n" . print_r($args, true));
            return [];
        }

        $where = "user_status IN ('" . implode("','", $args['user_status']) . "')";

        if (!empty($args['user_level'])) {
            $where .= " AND user_level IN ('" . implode("','", $args['user_level']) . "')";
        }

        if (!empty($args['where_clause'])) {
            $where .= " AND " . $args['where_clause'];
        }

        //
        $order_by = '';

        if (!empty($args['order_by'])) {
            $order_by = " ORDER BY " . $args['order_by'];
        }

        $sql = "SELECT * FROM " . table_users . " WHERE {$where}{$order_by}";

        //
        $pagesize = $args['page_size'];

        if (0 < $pagesize) {
            $offset = (get_current_page() - 1) * $pagesize;
            $sql .= " LIMIT {$offset}, {$pagesize}";
        }

        // echo "<pre>{$sql}</pre>";
        $result = $db->get_results($sql, ARRAY_A);
        $output = [];

        if ($args['return_type'] == 'profile') {
            foreach( $result as $row ) {
                $output[] = $this->process_user_profile($row);
            }
        } else {
            $output = $result;
        }

        return $output;
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
            'user_email'      => '',

            'user_level'    => [],
            'user_status'   => [],
        ];

        $args = array_merge($defaults, $argsCustom);
        $where = '';

        if ( !empty( $args['user_login'] ) ) {
            $where = "WHERE user_login = '" . $db->escape( $args['user_login'] ) . "'";
        }

        if ( !empty( $args['user_id'] ) ) {
            $where = "WHERE user_id = '" . $db->escape( $args['user_id'] ) . "'";
        }

        if ( !empty( $args['user_email'] ) ) {
            $where = "WHERE user_email = '" . $db->escape( $args['user_email'] ) . "'";
        }

        if ( empty($where) ) {
            return [];
        }

        if ( !empty( $args['user_level'] ) ) {
            $where .= " AND user_level IN ('" . implode("','", $args['user_level']) . "')";
        }

        if ( !empty( $args['user_status'] ) ) {
            $where .= " AND user_status IN ('" . implode("','", $args['user_status']) . "')";
        }

        $sql = "SELECT * FROM " . table_users . " " . $where;

        return $db->get_row( $sql, ARRAY_A );
    }

    /**
     * Check the user in $cachedItems variable first
     * If not exist in $cachedItems; consider query to the database
     * 
     * @return array
     */
    public function get_item($argsCustom = []) {
        $output = [];

        $defaults = [
            'user_login'      => '',
            'user_id'         => '',
            'user_email'      => '',
        ];

        $args = array_merge($defaults, $argsCustom);

        $user_id = $args["user_id"];
        $user_login = $args["user_login"];

        if (!empty($user_id) && isset($this->cachedItems[$user_id])) {
            $output = $this->cachedItems[$user_id];
        } else if (!empty($user_login) && isset($this->cachedItemsByLoginID[$user_login])) {
            $output = $this->cachedItemsByLoginID[$user_login];
        } else {
            $output = $this->get_user($args);

            if ($output) {
                $this->cachedItems[$user_id] = $output;
                $this->cachedItemsByLoginID[$user_login] = $output;
            }
        }

        return $output;
    }


    /**
     * Create an array of user information (including extra: profile url, avatar image url, etc.)
     * 
     * @return array
     */
    public function get_user_profile($argsCustom = [], $allData = false) {
        $defaults = [
            'user_login'      => '',
            'user_id'         => '',
            'user_email'      => '',
        ];

        $args = array_merge($defaults, $argsCustom);
        $user = $this->get_item($args);
        $output = [];

        if ($user) {
            $output = $this->process_user_profile($user, $allData);
        }

        return $output;
    }

    /**
     * 
     */
    public function update($initialData, $user_id) {
        global $db;

        $defaultData = [
            'user_modification' => 'NOW()',
        ];
    
        $data = array_merge( $defaultData, $initialData );

        $sql = "update " . table_users . " set";

        foreach($data as $column => $value) {
            if ($column == 'user_modification') {
                $sql .= " {$column} = {$value},";
                continue;
            }

            // Salt the user password
            if ($column == 'user_pass') {
                $sql .= " {$column} = '" . generatePassHash( $value ) . "',";
                continue;
            }

            $sql .= " {$column} = '" . $db->escape($value) . "',";
        }

        $sql = trim($sql, ",");

        if ($user_id>0) {
            $sql .= " WHERE user_id = '" . $db->escape($user_id) . "'";

            return $db->query($sql);
        } else {
            kahuk_log_queries("globalUsersObj->update() User update failed! User ID: {$user_id}\nSQL: {$sql}");
            return false;
        }
    }

    /**
     * Retrieve the user globally
     * 
     * @return array
     */
    public function get_global_user($forceArgs = []) {
        $output = [];
        $pagename = kahuk_get_pagename();
        $autoLoadPages = [
            'user-profile',
            'user-settings',
            'register-complete'
        ];

        if (!empty($forceArgs)) {
            $output = $this->get_user_profile($forceArgs);

        } elseif (in_array($pagename, $autoLoadPages)) {
            $user_login = kahuk_create_slug_story(_get('slug'));

            $allData = (in_array($pagename, ['user']));

            if ($user_login) {
                $output = $this->get_user_profile(['user_login' => $user_login], $allData);
            }
        }

        return $output;
    }
}

global $globalUsersObj, $globalUser, $globalUserRoles, $globalUserStatuses;

// Set the global variables to access users class
$globalUsersObj = kahuk_users_init();

// Create global variable for single story
$globalUser = $globalUsersObj->get_global_user();

// Allowed User Roles
$globalUserRoles = ['admin','moderator','normal','spammer','unverified'];

// Allowed User Statuses
$globalUserStatuses = ['disable','enable'];
