<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

check_referrer();

$post_id = sanitize($_POST['id'], 3);

if(is_numeric($post_id) && $post_id > 0){
	
	$link = new Link;
	$link->id=$post_id;
	$link->read_basic();
	
	if ($current_user->user_id == 0 && !anonymous_vote) {
		error($main_smarty->get_config_vars('KAHUK_Visual_Vote_NoAnon'));
	}

	$post_user = sanitize($_POST['user'], 3);
	if($current_user->user_id != $post_user) {
		error($main_smarty->get_config_vars('KAHUK_Visual_Vote_BadUser'));
	}

	$md5=md5($post_user.$link->randkey);
	if($md5 !== sanitize($_POST['md5'], 3)){
		error($main_smarty->get_config_vars('KAHUK_Visual_Vote_BadKey'));
	}

	if($link->votes($current_user->user_id) > 0 || $link->reports($current_user->user_id) > 0) {
	}


	$value = sanitize($_POST['value'], 3);
	if(sanitize($_POST['unvote'], 3) == 'true'){
		$link->remove_vote($current_user->user_id, $value);
	} else {
	if($link->votes($current_user->user_id) > 0 || $link->reports($current_user->user_id) > 0 ||
	   // DB 11/10/08
	   (votes_per_ip > 0 && $link->votes_from_ip() + $link->reports_from_ip() >= votes_per_ip)) {
	   /////
		error($main_smarty->get_config_vars('KAHUK_Visual_Vote_AlreadyVoted'));
	}

	$link->insert_vote($current_user->user_id, $value);
	}

	if ($link->status == 'discard') {
	$link->read();
	$link->status = 'new';
	$link->store();
	}

	if(Voting_Method == 2){
		$link_rating = $link->rating($link->id)/2;
		$rating_width = $link_rating * 25;
		$vote_count = $link->countvotes();

		echo $rating_width . "~" . $link_rating . "~" . $vote_count;
	}
	else
	{
		$count=$link->votes;
		echo "$count ~--~".$post_id;
	}


	$link->evaluate_formulas();

}
