<?php
if (!defined('KAHUKPATH')) {
    die();
}

/**
 * Initializes KahukFriends Class
 *
 * @return KahukFriends
 */
function kahuk_friends_init()
{
    return KahukFriends::init();
}

/**
 * 
 */
class KahukFriends
{
    public $errors;

    public $cachedItems = []; // Cached friends by user integer id

    /**
     * Class construcotr
     */
    private function __construct()
    {
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }


    /**
     * Create an array of people you/user is following/follower
     * 
     * @param $user_id
     * @param $friendshipType could be following or follower
     * 
     * @return array
     */
    function get_following_follower($user_id, $friendshipType = 'following')
    {
        global $db;

        $output = [
            'count' => 0,
            'users' => [],
        ];

        $sql = "
            SELECT " . 
                table_users . ".user_id, " . table_users . ".user_login, " . 
                table_users . ".user_level, " . table_users . ".user_status, " . 
                table_users . ".user_email, " . table_users . ".user_names, " . 
                table_users . ".user_karma, " . table_users . ".user_url 
            FROM " . table_friends . " 
                INNER JOIN " . table_users . " 
        ";

        if ($friendshipType == 'following') {
            $sql .= "
                ON " . table_friends . ".friend_to = " . table_users . ".user_id 
                WHERE " . table_friends . ".friend_from = {$user_id} 
            ";
        } else {
            $sql .= "
                ON " . table_friends . ".friend_from = " . table_users . ".user_id 
                WHERE " . table_friends . ".friend_to = {$user_id} 
            ";
        }

        $sql .= " AND " . table_users . ".user_id != $user_id AND user_status = 'enable'";

        // echo "<pre>{$sql}</pre>";
        $result = $db->get_results($sql, ARRAY_A);

        // print_r($result);

        if ($result) {
            $output['count'] = count($result);

            foreach($result as $i => $row) {
                $output['users'][$i] = process_user_profile($row);
            }
        }

        return $output;
    }

    /**
     * Create an array of people you/user is following
     * 
     * @return array
     */
    function get_list_following($user_id)
    {
        return $this->get_following_follower($user_id, 'following');
    }

    /** TODO
     * Create an array of people following you/user
     * 
     * @return array
     */
    function get_list_follower($user_id)
    {
        return $this->get_following_follower($user_id, 'follower');
    }


    /**
     * Find out the possible friendship type, possible values: mutual, following, follower, and none.
     * 
     * SQL: SELECT CONCAT( friend_from, ',', friend_to ) AS STATUS FROM tbl_friends WHERE (friend_from, friend_to) IN (( current_user_id,target_user_id ) , ( target_user_id,current_user_id ))
     * 
     * @return string|empty
     */
    function get_friend_status($target_user_id, $current_user_id)
    {
        global $db;

        // User ids should be more than 0
		if ((!is_numeric($target_user_id) || $target_user_id < 1) || (is_numeric($current_user_id) && $current_user_id < 1)) {
            kahuk_log_unexpected("globalFriendsObj->get_friend_status function get wrong data! [target_user_id: {target_user_id}] and [current_user_id: {$current_user_id}]");
            die();
        }

        $sql = "SELECT CONCAT( friend_from, ',', friend_to ) AS STATUS FROM " . table_friends . " WHERE (friend_from, friend_to) IN (( " . $current_user_id . "," . $target_user_id . " ) , ( " . $target_user_id . "," . $current_user_id . " ))";

        // echo "<pre>globalFriendsObj->get_friend_status:: {$sql}</pre>";
        // exit;

        $result = $db->get_results($sql, ARRAY_A);
        $friendshipType = "none";

        if ($result) {
            if (sizeof($result) == 2) {
                $friendshipType = "mutual";
            } elseif (sizeof($result) == 1) {
                foreach ($result as $val) {
                    if ($val['STATUS'] == $current_user_id . "," . $target_user_id) {
                        $friendshipType = "following";
                    } elseif ($val['STATUS'] == $target_user_id . "," . $current_user_id) {
                        $friendshipType = "follower";
                    }
                }
            } elseif (sizeof($result) > 2) {
                kahuk_log_queries("globalFriendsObj -> get_friend_status records should be 0, 1, or maximum 2. SQL: [{$sql}]\nFound Data: " . print_r($result, true));
            }
        }

        return $friendshipType;
    }

    /**
     * Following a User
     * 
     * @return string|empty
     */
    function follow_user($target_user_login_or_id, $current_user_or_id)
    {
        global $db, $globalUsersObj;

        $target_user_id = 0;
        $current_user_id = 0;

        if (is_numeric($target_user_login_or_id)) {
            $target_user_id = (int) $target_user_login_or_id;
        } else {
            $target_user_login_or_id = $globalUsersObj->get_item(['user_login' => $target_user_login_or_id]);

            if ($target_user_login_or_id) {
                $target_user_id = $target_user_login_or_id['user_id'];
            }
        }

        if (is_array($current_user_or_id) && isset($current_user_or_id['user_id'])) {
            $current_user_id = (int) $current_user_or_id['user_id'];
        } else {
            $current_user_id = (int) $current_user_or_id;
        }

        // User ids should be more than 0
		if ((!is_numeric($target_user_id) || $target_user_id < 1) || (!is_numeric($current_user_id) || $current_user_id < 1)) {
            kahuk_log_unexpected("globalFriendsObj -> follow_user function get wrong data! [target_user_login_or_id: {target_user_login_or_id}] and [current_user_or_id: " . print_r($current_user_or_id, true) . "]");
            die();
        }

		$friendshipType = $this->get_friend_status($target_user_id, $current_user_id);
        $output = '';

        if (in_array($friendshipType, ['mutual', 'following'])) {
            // kahuk_log_unexpected("globalFriendsObj -> follow_user friendshipType TRYING TO FOLLOW AGAIN [current_user_id: {$current_user_id}, target_user_id: {$target_user_id}]");
            $output = $friendshipType;
        } else {
            $sql = "INSERT IGNORE INTO " . table_friends . " (friend_from, friend_to) values (" . $current_user_id . ", " . $target_user_id . ");";
            $db->query($sql);

			$output = $this->get_friend_status($target_user_id, $current_user_id);

            if (!in_array($output, ['following', 'mutual'])) {
                kahuk_log_queries("globalFriendsObj -> follow_user friendshipType found {$output}, should be 'following' or 'mutual'\n SQL: $sql");
                die("something went wrong!");
            }
		}

        return $output;
    }

    /**
     * Unfollowing a User
     * 
     * @return string|empty
     */
    function unfollow_user($target_user_login_or_id, $current_user_or_id)
    {
        global $db, $globalUsersObj;

        $target_user_id = 0;
        $current_user_id = 0;

        if (is_numeric($target_user_login_or_id)) {
            $target_user_id = (int) $target_user_login_or_id;
        } else {
            $target_user_login_or_id = $globalUsersObj->get_item(['user_login' => $target_user_login_or_id]);

            if ($target_user_login_or_id) {
                $target_user_id = $target_user_login_or_id['user_id'];
            }
        }

        if (is_array($current_user_or_id) && isset($current_user_or_id['user_id'])) {
            $current_user_id = (int) $current_user_or_id['user_id'];
        } else {
            $current_user_id = (int) $current_user_or_id;
        }

        // User ids should be more than 0
		if ((!is_numeric($target_user_id) || $target_user_id < 1) || (!is_numeric($current_user_id) || $current_user_id < 1)) {
            kahuk_log_unexpected("globalFriendsObj -> unfollow_user function get wrong data! [target_user_login_or_id: {target_user_login_or_id}] and [current_user_or_id: " . print_r($current_user_or_id, true) . "]");
            die();
        }

		$friendshipType = $this->get_friend_status($target_user_id, $current_user_id);
        $output = '';

		if (in_array($friendshipType, ['mutual', 'following'])) {
            $sql = "DELETE FROM " . table_friends . " WHERE friend_from = {$current_user_id} and friend_to = {$target_user_id};";
            $db->query($sql);

			$output = $this->get_friend_status($target_user_id, $current_user_id);
			
            if (in_array($output, ['following', 'mutual'])) {
                kahuk_log_queries("globalFriendsObj -> unfollow_user friendshipType found {$output}, should be 'follower' or empty\n SQL: $sql");
                die("something went wrong!");
            }
		} else {
            $output = $friendshipType;
            // kahuk_log_unexpected("globalFriendsObj -> unfollow_user friendshipType TRYING TO UNFOLLOW AGAIN [current_user_id: {$current_user_id}, target_user_id: {$target_user_id}]");
        }

        return $output;
    }
}

global $globalFriendsObj;

// Set the global variables to access friends class
$globalFriendsObj = kahuk_friends_init();
