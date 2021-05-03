<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

check_referrer();
$CSRF = new csrf();

$errorMessage = '';

// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Submit_A_New_Group');
$navwhere['link1'] = getmyurl('submit', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('KAHUK_Visual_Submit_A_New_Group'));
$main_smarty = do_sidebar($main_smarty);
$main_smarty->assign('auto_approve_group', auto_approve_group);

// make sure user is logged in
force_authentication();

$current_user_level = $current_user->user_level;
/* Redwine: Roles and permissions and Groups fixes. Check if the user didn't exceed the max number of creating groups.*/
$numGr = $db->get_var("SELECT count(*) FROM " .table_groups . " WHERE `group_creator` = " . $current_user->user_id);
$max_user_groups_allowed = $main_smarty->get_template_vars('max_user_groups_allowed');

if ( $numGr >= $max_user_groups_allowed ) {
	$errors = $main_smarty->get_config_vars('KAHUK_Visual_Submit_A_New_Group_Error');
	$main_smarty->assign('error', $errors);

	// pagename
	define('pagename', 'submit_groups');
	$main_smarty->assign('pagename', pagename);

	// show the template
	$main_smarty->assign('tpl_center', $the_template . '/submit_groups_center');
	$main_smarty->display($the_template . '/kahuk.tpl');
	exit;
}


if (
	enable_group == "true" && isset( $_POST['group_title'] ) &&
	( group_submit_level == $current_user_level || group_submit_level == 'normal' || $current_user_level == 'admin' )
) {
	// if TOKEN is empty, no need to continue, just display the invalid token error.
	if ( empty( $_POST['token'] ) ) {
		$CSRF->show_invalid_error( 1 );
		exit;
	}

	// if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ( $CSRF->check_valid( sanitize( $_POST['token'], 3), 'submit_group' ) != 2 ) {
		$CSRF->show_invalid_error( 1 );
		exit;
	}

	//
	$group_name = isset( $_POST['group_title'] ) ? $_POST['group_title'] : '';

	if ( ! empty( $group_name ) ) {
		// $group_name = $db->escape( stripslashes( strip_tags( trim( $group_name ) ) ) );
		$group_name = sanitize_text_field( $group_name );
	}

	if ( empty( $group_name ) ){
		$errorMessage = $main_smarty->get_config_vars( 'KAHUK_Visual_Group_Empty_Title' );
	}

	//
	$group_description = isset( $_POST['group_description'] ) ? $_POST['group_description'] : '';
	
	if ( ! empty( $group_description ) ) {
		$group_description = $db->escape( stripslashes( strip_tags( trim( $_POST['group_description'] ) ) ) );
	}

	if ( empty( $group_description ) ) {
		$errorMessage = "Add a description for the Group.";
	}

	// Check Vote count
	$group_vote_to_publish = 0;

	if ( isset( $_POST['group_vote_to_publish'] ) ) {
		$group_vote_to_publish = (int) trim( $_POST['group_vote_to_publish'] );
	}

	if ( $group_vote_to_publish <= 0 ) {
		$group_vote_to_publish = 0;
	}

	if ( empty ( $errorMessage ) ) {
		$exists = $db->get_var("select COUNT(*) from ".table_groups." WHERE group_name='$group_name'");
				
		if ( $exists ) {
			$errorMessage = $main_smarty->get_config_vars( 'KAHUK_Visual_Group_Title_Exists' );
		}
	}

	// 
	if ( isset( $_POST['group_notify_email'] ) && $_POST['group_notify_email'] > 0 ) {
		$group_notify_email = 1;
	} else {
		$group_notify_email = 0;
	}

	$group_author         = $current_user->user_id;
	$group_members        = 1;
	$g_date               = time();
	$group_date           = $g_date;
	$group_published_date = 943941600;
	// $group_name           = $group_title;
	$group_description    = $group_description;
	$group_safename       = makeUrlFriendly( $group_name, true );
	
	//
	if( isset( $_POST['group_privacy'] ) ) {
		$group_privacy = $db->escape(sanitize( $_POST['group_privacy'], 3 ) );
	} else {
		$group_privacy = 'private';
	}
	
	//
	if ( auto_approve_group == 'true' ) {
		$group_status = 'enable';
	} else {
		$group_status = 'disable';
	}

	if ( empty ( $errorMessage ) ) {
		//to insert a group
		$insert_group = "INSERT IGNORE INTO " . table_groups . " (group_creator, group_status, group_members, group_date, group_safename, group_name, group_description, group_privacy, group_vote_to_publish, group_notify_email) VALUES ($group_author, '$group_status', $group_members,FROM_UNIXTIME($group_date),'$group_safename','$group_name', '$group_description', '$group_privacy', '$group_vote_to_publish', '$group_notify_email')";
		$result = $db->query( $insert_group );
		
		//get linkid inserted above
		$in_id = $db->get_var( "select max(group_id) as group_id from ".table_groups." " );
		//echo 'sdgfdsgds'.$in_id;
		
		//to make group creator a member
		/* Redwine: Roles and permissions and Groups fixes */
		$insert_member = "INSERT IGNORE INTO ". table_group_member ." (`member_user_id` , `member_group_id`, `member_role`) VALUES (".$group_author.", ".$in_id.",'admin' )";
		$db->query( $insert_member );
		

		if( isset( $_POST['group_mailer'] ) ) {
			Global $db,$current_user;

			$names = $_POST['group_mailer'];
			$v1 = explode( ",", $names );
			$name = "";
			
			$user = new User;
			$user->id = $current_user->user_id;
			$user->read();
			
			$author_email = $user->email;
			$username = $user->username;
			
			
			foreach ( $v1 as $t ) {
				$subject = $main_smarty->get_config_vars('KAHUK_InvitationEmail_Subject');
				$AddAddress = $t;
				
				$message = sprintf($main_smarty->get_config_vars('KAHUK_InvitationEmail_Message'),"<a href='".my_base_url.my_kahuk_base."/group_story.php?id=".$in_id."'>".$group_name."</a>","<a href='".my_base_url.my_kahuk_base."/user.php?login=".$username."'>".$username."</a>");
				
				/* Redwine: the $mail->From = $site_mail is wrong, because $site_mail is defined nowhere! We must set it to the value defined in the language file. The same applies to $mail->AddReplyTo */
				
				//Redwine: require the file for email sending.
				require_once('libs/phpmailer/sendEmail.php');
				
				if(!$mail->Send())
				{
					$errors .= $main_smarty->get_config_vars('KAHUK_Visual_Login_Delivery_Failed'). " To: $AddAddress<br />";
				}else{
					$errors .= $main_smarty->get_config_vars("KAHUK_Visual_Group_Email_Invitation")." To: $AddAddress<br /><hr />$message";
				}
			}
			//Redwine: this will print the message on the page, if allow smtp is set to true.
			$_SESSION['groupInvitation'] = $errors;
		}

		if ( $result ) {
			//redirect
			$redirect = '';
			$redirect = getmyurl( "group_story", $in_id );
			header( "Location: $redirect" );

			die;
		}
	}
}

// pagename
define('pagename', 'submit_groups');

if ( ! empty( $errorMessage ) ) {
	$main_smarty->assign( 'error', $errorMessage );
}

$CSRF->create('submit_group', true, true);

$main_smarty->assign('pagename', pagename);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/submit_groups_center');
$main_smarty->display($the_template . '/kahuk.tpl');
