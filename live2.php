<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');

if(!Enable_Live) {
	kahuk_redirect_404();
}

// number of items to show on the page
$max_items = items_to_show;

if(!($time=check_integer('time'))) {
	$time = 0;
} 

header('Content-Type: text/plain; charset=UTF-8');

$last_timestamp = 0;

// always check groups (to hide private groups)
$from = " LEFT JOIN ".table_groups." ON ".table_links.".link_group_id = ".table_groups.".group_id ";
$groups = $db->get_results("SELECT * FROM " . table_group_member . " WHERE member_user_id = {$current_user->user_id} and member_status = 'active'");
if($groups)
{
    $group_ids = array();
    foreach($groups as $group)
	$group_ids[] = $group->member_group_id;
    $group_list = join(",",$group_ids);
    $where = " AND (".table_groups.".group_privacy!='private' OR ISNULL(".table_groups.".group_privacy) OR ".table_groups.".group_id IN($group_list)) ";
}
else
{
    $group_list = '';
    $where = " AND (".table_groups.".group_privacy!='private' OR ISNULL(".table_groups.".group_privacy))";
}

get_votes($time);
get_new_stories($time);
get_new_published($time);
get_comments($time);

if($last_timestamp == 0) $last_timestamp = time();

echo "timestamp=$last_timestamp;\n";
if(count($events) < 1) exit;
ksort($events);
$keys = array_reverse(array_keys($events));
$lines = min(count($keys), $max_items);

$counter=0;
echo "new_items=$lines;\n";
echo "new_data = ([";
foreach ($keys as $key) {
	echo "{" . $events[$key] . "}, ";
	$counter++;
	if($counter>=$max_items) {
		echo "]);";
		exit();
	}
}
echo "]);";


// get latest votes
function get_votes($time) {
	global $db, $events, $last_timestamp;
	$res = $db->get_results("SELECT *, unix_timestamp(vote_date) as timestamp 
	                            FROM " . table_votes . ", " . table_links . " 
	                            WHERE vote_date > FROM_UNIXTIME($time) AND link_id = vote_link_id AND vote_user_id != link_author AND (link_status='published' OR link_status='new') AND vote_type = 'links' 
	                            ORDER BY vote_date DESC 
	                            LIMIT 20");
	if (!$res) return;
	foreach ($res as $event) {
		if(substr($event->vote_ip, 0, 3) != '0.0'){
			$id=$event->vote_id;
			$uid = $event->vote_user_id;
			if($uid > 0) {
				$user = $db->get_var("select user_login from " . table_users . " where user_id = $uid");
			} else {
				$user= preg_replace('/\.[0-9]+$/', '', $event->vote_ip);
			}
			if ($event->vote_value < 0) {
				$type = 'Down Vote';
				$who = $user;
			} elseif ($event->vote_value >= 0) {
				$type = 'Up Vote';
				$who = $user;
			} else { 
				$type = 'Problem';
				$who = $event->vote_value;
			}
			$status =  get_status($event->link_status);
			$key = $event->timestamp . ':votes:'.$id;
			if (Voting_Method == 2) {
			    $votes = $event->link_votes/2;
            } else {
                $votes = $event->link_votes;
            }
	        $events[$key] = 'ts:"'.$event->timestamp.'", type:"'.$type.'", votes:"'.$votes.'", link:"'.$event->link_id.'", title:"'.addslashes($event->link_title).'", who:"'.addslashes($who).'", status:"'.$status.'", uid:"'.$uid.'"';
			//echo "($key)". $events[$key];
	        if($event->timestamp > $last_timestamp) $last_timestamp = $event->timestamp;
		}
	}
}

// get latest stories
function get_new_stories($time) {
	global $db, $events, $last_timestamp, $from, $where;
	$res = $db->get_results("select unix_timestamp(link_date) as timestamp, user_login, link_author, link_id, link_title, link_url, link_status, link_date, link_votes 
					from " . table_links . "
					LEFT JOIN " . table_users . "  ON user_id=link_author 
					$from
					where link_status='new' and  link_date > from_unixtime($time) 
					$where
					order by link_date desc limit 20");
	if (!$res) return;
	foreach ($res as $event) {
		$id=$event->link_id;
		$uid = $event->link_author;
		$type = 'Submission';
		$who = $event->user_login;
		$status =  get_status($event->link_status);
		$key = $event->timestamp . ':new:'.$id;
		if(Voting_Method == 2){$votes = $event->link_votes/2;}
    else {$votes = $event->link_votes;}
		$events[$key] = 'ts:"'.$event->timestamp.'", type:"'.$type.'", votes:"'.$votes.'", link:"'.$event->link_id.'", title:"'.addslashes($event->link_title).'", who:"'.addslashes($who).'", status:"'.$status.'", uid:"'.$uid.'"';
		//echo "($key)". $events[$key];
		if($event->timestamp > $last_timestamp) $last_timestamp = $event->timestamp;
	}
}

// get latest published stories
function get_new_published($time) {
	global $db, $events, $last_timestamp,$from, $where;;
	$res = $db->get_results("select unix_timestamp(link_published_date) as timestamp, user_login, link_author, link_id, link_title, link_url, link_status, link_date, link_votes 
					from " . table_links . "	
					LEFT JOIN " . table_users . " ON user_id=link_author 
					$from
					where link_status='published' and link_published_date > from_unixtime($time)
					$where
					order by link_published_date desc, link_date DESC limit 20");
	if (!$res) return;
	foreach ($res as $event) {
		$id=$event->link_id;
		$uid = $event->link_author;
		$type = 'Published';
		$who = $event->user_login;
		$status =  get_status($event->link_status);
		$key = $event->timestamp . ':published:'.$id;
		if(Voting_Method == 2){$votes = $event->link_votes/2;}
    else {$votes = $event->link_votes;}
		$events[$key] = 'ts:"'.$event->timestamp.'", type:"'.$type.'", votes:"'.$votes.'", link:"'.$event->link_id.'", title:"'.addslashes($event->link_title).'", who:"'.addslashes($who).'", status:"'.$status.'", uid:"'.$uid.'"';
		//echo "($key)". $events[$key];
		if($event->timestamp > $last_timestamp) $last_timestamp = $event->timestamp;
	}
}

// get latest comments
function get_comments($time) {
	global $db, $events, $last_timestamp;
	$res = $db->get_results("select comment_id, unix_timestamp(comment_date) as timestamp, user_login, comment_user_id, link_author, link_id, link_title, link_url, link_status, link_date, link_published_date, link_votes from " . table_comments . ", " . table_links . ", " . table_users . " where comment_status='published' AND comment_date > from_unixtime($time) and link_id = comment_link_id and (link_status='published' OR link_status='new') and user_id=comment_user_id order by comment_date desc limit 20");
	if (!$res) return;
	foreach ($res as $event) {
		$id=$event->comment_id;
		$uid=$event->comment_user_id;
		$type = 'Comment';
		$who = $event->user_login;
		$status =  get_status($event->link_status);
		$key = $event->timestamp . ':comment:'.$id;
		if(Voting_Method == 2){$votes = $event->link_votes/2;}
    else {$votes = $event->link_votes;}
		$events[$key] = 'ts:"'.$event->timestamp.'", type:"'.$type.'", votes:"'.$votes.'", link:"'.$event->link_id.'", title:"'.addslashes($event->link_title).'", who:"'.addslashes($who).'", status:"'.$status.'", uid:"'.$uid.'"';
		//echo "($key)". $events[$key];
		if($event->timestamp > $last_timestamp) $last_timestamp = $event->timestamp;
	}
}

function get_status($status) {
	switch ($status) {
		case 'published':
			$status = _('Published');
			break;
		case 'new':
			$status = _('New');
			break;
		case 'discard':
			$status = _('Discarded');
			break;
	}
	return $status;
}
