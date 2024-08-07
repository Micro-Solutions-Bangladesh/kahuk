<?php
/**
 * Depricated in favor of KahukFriends class
 * 
 */


if(!defined('KAHUKPATH_LIBS')){die();}


class Friend {
	var $friend = "";

	function remove($friend)
	{
		global $db,$current_user;
		if (!is_numeric($friend)) die();

		$sql = "Delete from " . table_friends . " where friend_from = " . $current_user->user_id . " and friend_to = $friend;";
		//echo $sql;
		$db->query($sql);

		$friend_status = $this->get_friend_status($friend);
		if ($friend_status == 'following'){die("there was an error");}
		header('Location: ' .my_base_url.my_kahuk_base. '/user.php?login='.$current_user->user_login);
	}
	
	function add($friend)
	{
		global $db, $current_user;
		if (!is_numeric($friend)) die();
		
		if ($current_user->user_id == 0) {
        echo "<span class='success' style='border:solid1px#269900;padding:2px2px2px2px'>Please <a href=" .my_base_url.my_kahuk_base. "/login.php?return=/user.php?view=addfriend>login</a></span><br/>";
        return;
        }
		
		$friend_status = $this->get_friend_status($friend);
		if ($friend_status == '' || $friend_status == 'follower'){
			//echo "INSERT INTO " . table_friends . " (friend_from, friend_to) values ($current_user->user_id, $friend);";
			$db->query("INSERT IGNORE INTO " . table_friends . " (friend_from, friend_to) values (" . $current_user->user_id . ", " . $friend . ");");

			$friend_status = $this->get_friend_status($friend);
			if ($friend_status == ''){die("there was an error");}
			header('Location: ' .my_base_url.my_kahuk_base. '/user.php?login='.$current_user->user_login);
		}
	}
	
	/**
	 * Depricated in favor of $globalFriendsObj->get_list_following
	 * 
	 */
	function get_list_following($user_id)
	{	
		// returns an array of people you've added as a friend
		global $db, $current_user;
		//echo "SELECT " . table_users . ".user_login FROM " . table_friends . " INNER JOIN " . table_users . " ON friends.friend_to = " . table_users . ".user_id WHERE (((friends.friend_from)=$current_user->user_id));";
		$friends = $db->get_results("SELECT " . table_users . ".user_login, " . table_users . ".user_avatar_source, " . table_users . ".user_email, " . table_users . ".user_id 
						FROM " . table_friends . " 
						INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id 
						WHERE " . table_friends . ".friend_from=$user_id AND " . table_users . ".user_id != $user_id AND user_status='enable'",
					     ARRAY_A);
		if (!empty($friends)) {
			foreach ($friends as &$friend) 
				if ($db->get_var($sql="SELECT friend_id FROM " . table_friends . " WHERE friend_to=$friend[user_id] AND friend_from='{$current_user->user_id}'")) 
				$friend['following'] = true;
			foreach ($friends as &$friend) {
				$friend['is_mutual'] = $this->get_friend_status($friend['user_id']);
			}
		}
		return $friends;

	}

	/**
	 * Depricated in favor of $globalFriendsObj->get_list_follower
	 * 
	 */
	function get_list_follower($user_id)
	{
		// returns an array of people who have added you as a friend
		global $db, $current_user;
		$friends = $db->get_results("SELECT " . table_users . ".user_login, " . table_users . ".user_avatar_source, " . table_users . ".user_email, " . table_users . ".user_id 
						FROM " . table_friends . " 
						INNER JOIN " . table_users . " ON " . table_friends . ".friend_from = " . table_users . ".user_id 
						WHERE " . table_friends . ".friend_to=$user_id AND " . table_users . ".user_id != $user_id  AND user_status='enable'",
					     ARRAY_A);
		if (!empty($friends)) {				 
		foreach ($friends as &$friend) 
		    if ($db->get_var("SELECT friend_id FROM " . table_friends . " WHERE friend_from=$user_id AND friend_to=$friend[user_id]"))
			$friend['is_friend'] = true;
		foreach ($friends as &$friend) {
			$friend['is_mutual'] = $this->get_friend_status($friend['user_id']);
		}
		return $friends;
		}
	}

	/**
	 * Depricated in favor of $globalFriendsObj->get_friend_status
	 * 
	 */
	function get_friend_status($friend)
	{
		global $db, $current_user;
		if (!is_numeric($friend)) die();
		$sql = "SELECT CONCAT( friend_from, ',', friend_to ) AS STATUS FROM " . table_friends . " WHERE (friend_from, friend_to) IN (( " . $current_user->user_id . "," . $friend . " ) , ( ". $friend . "," . $current_user->user_id. " ))";
		//$sql = "SELECT " . table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id WHERE " . table_friends . ".friend_from=" . $current_user->user_id .";";
		$result = $db->get_results($sql,ARRAY_A);
        if ($result) {
            if (sizeof($result) == 2) {
                $friends = "mutual";
            } elseif (sizeof($result) == 1) {
                foreach($result as $key => $val){
                    if ($val['STATUS'] == $current_user->user_id . "," . $friend) {
                        $friends = "following";
                    } elseif ($val['STATUS'] == $friend . "," . $current_user->user_id) {
                        $friends = "follower";
                    }
                }
            } else {
                $friends = "";
            }
            
		} else {
            $friends = "";
        }
		return $friends;
		
		// returns friend user_id if a friend
		// returns null if not
	}
}

