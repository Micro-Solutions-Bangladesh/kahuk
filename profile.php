<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'csrf.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

$_GET['login'] = sanitize($_GET['login'], 3);
$_REQUEST['login'] = $_GET['login'] = preg_replace('/[^\p{L}\p{N}_\s\/]/u', '', $_GET['login']);

#ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

check_referrer();

// sessions used to prevent CSRF
	$CSRF = new csrf();

// sidebar
$main_smarty = do_sidebar($main_smarty);

$canIhaveAccess = ($_GET['login'] == $current_user->user_login);
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

// If not logged in, redirect to the index page
if ( $_GET['login'] && $canIhaveAccess ) {
	$login=$_GET['login'];
} elseif ( $current_user->user_id > 0 && $current_user->authenticated ) {
	$login = $current_user->user_login;
	if ( $_GET['avatar'] != 'edit' )
		if ( urlmethod == 1 ) {
			header( "Location: $my_base_url$my_kahuk_base/profile.php?login=$login" );
		} elseif ( urlmethod == 2) {
			header( "Location: $my_base_url$my_kahuk_base/user/$login/" );
		}
} else {
	$myname = KAHUK_BASE_URL;
}

// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Breadcrumb_Profile');
$navwhere['link1'] = getmyurl('user2', $login, 'profile');
$navwhere['text2'] = $login;
$navwhere['link2'] = getmyurl('user2', $login, 'profile');
$navwhere['text3'] = $main_smarty->get_config_vars('KAHUK_Visual_Profile_ModifyProfile');
$navwhere['link3'] = getmyurl('profile', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('KAHUK_Visual_Profile_ModifyProfile'));

// read the users information from the database
$user=new User();
$user->username = $login;
if(!$user->read()) {
	header( 'Location: '.KAHUK_BASE_URL );
	die;
}

// setup the links
$main_smarty->assign('user_url_personal_data2', getmyurl('user2', $login));
$main_smarty->assign('user_url_news_sent2', getmyurl('user2', $login, 'history'));
$main_smarty->assign('user_url_news_published2', getmyurl('user2', $login, 'published'));
$main_smarty->assign('user_url_news_unpublished2', getmyurl('user2', $login, 'new'));
$main_smarty->assign('user_url_draft2', getmyurl('user2', $login, 'draft'));
$main_smarty->assign('user_url_scheduled2', getmyurl('user2', $login, 'scheduled'));
$main_smarty->assign('user_url_news_voted2', getmyurl('user2', $login, 'voted'));
$main_smarty->assign('user_url_news_upvoted2', getmyurl('user2', $login, 'upvoted'));
$main_smarty->assign('user_url_news_downvoted2', getmyurl('user2', $login, 'downvoted'));	
$main_smarty->assign('user_url_commented2', getmyurl('user2', $login, 'commented'));
$main_smarty->assign('user_url_saved2', getmyurl('user2', $login, 'saved'));
$main_smarty->assign('user_url_friends', getmyurl('user_friends', $login, 'following'));
$main_smarty->assign('user_url_friends2', getmyurl('user_friends', $login, 'followers'));
$main_smarty->assign('user_url_add', getmyurl('user_friends', $login, 'addfriend'));
$main_smarty->assign('user_url_remove', getmyurl('user_friends', $login, 'removefriend'));
$main_smarty->assign('user_rss', getmyurl('rssuser', $login));
$main_smarty->assign('URL_Profile2', getmyurl('user_edit', $login));
$main_smarty->assign('form_action', getmyurl('profile')); 
$main_smarty->assign('user_url_member_groups', getmyurl('user2', $login, 'member_groups	'));

$main_smarty->assign('user_followers', $user->getFollowersCount());
$main_smarty->assign('user_following', $user->getFollowingCount());

	// uploading avatar
	if(isset($_POST["avatar"]) && sanitize($_POST["avatar"], 3) == "uploaded" && Enable_User_Upload_Avatar == true){
		// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_POST['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
		if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'profile_change') == 2){
			$user_image_path = "avatars/user_uploaded" . "/";
			$user_image_apath = "/" . $user_image_path;
			$allowedFileTypes = array("image/jpeg","image/gif","image/png",'image/x-png','image/pjpeg');
			unset($imagename);

			$myfile = $_FILES['image_file']['name'];
			$imagename = basename($myfile);

			$mytmpfile = $_FILES['image_file']['tmp_name'];
			if ($_FILES['image_file']["size"]/1024 > max_avatar_size) {
				$error[] = 'Maximum file size '. max_avatar_size . 'Kb exceeded';
			}

			if(!in_array($_FILES['image_file']['type'],$allowedFileTypes)){
				$error[] = 'Only these file types are allowed : jpeg, gif, png';
			}

			if(empty($error)){
				$imagesize = getimagesize($mytmpfile);
				$width = $imagesize[0];
				$height = $imagesize[1];	
		
				$imagename = $user->id . "_original.jpg";
		
				$newimage = $user_image_path . $imagename ;

				$result = move_uploaded_file($_FILES['image_file']['tmp_name'], $newimage);
				if(empty($result))
					$error["result"] = "There was an error moving the uploaded file.";
			}else{
				$main_smarty->assign('error', $error); 
			}			
		
			// create large avatar
			include KAHUK_LIBS_DIR . "class.pThumb.php";
			$img=new pThumb();
			$img->pSetSize(Avatar_Large, Avatar_Large);
			$img->pSetQuality(100);
			$img->pCreate($newimage);
			$img->pSave($user_image_path . $user->id . "_".Avatar_Large.".jpg");
			$img = "";
	
			// create small avatar
			$img=new pThumb();
			$img->pSetSize(Avatar_Small, Avatar_Small);
			$img->pSetQuality(100);
			$img->pCreate($newimage);
			$img->pSave($user_image_path . $user->id . "_".Avatar_Small.".jpg");
			$img = "";

			$db->query($sql="UPDATE ".table_users." SET user_avatar_source='useruploaded' WHERE user_id='$user->id'");	
			unset($cached_users[$user->id]);
		} else {
			$CSRF->show_invalid_error(1);
			exit;
		}
			
	}
    if(isset($error) && is_array($error)) {
		foreach($error as $key => $val) {
			echo $val;
			echo "<br>";
            
		}
	}

// Save changes
if(isset($_POST['email'])){
	
	$savemsg = save_profile();
	
	if (is_string($savemsg)){
	   	$main_smarty->assign('savemsg', $savemsg);
				
	}else
	{
		$save_message_text=$main_smarty->get_config_vars("KAHUK_Visual_Profile_DataUpdated");
		if($savemsg['username']==1)
		 $save_message_text.="<br/>".$main_smarty->get_config_vars("KAHUK_Visual_Profile_UsernameUpdated");
		if($savemsg['pass']==1)
		 $save_message_text.="<br/>".$main_smarty->get_config_vars("KAHUK_Visual_Profile_PassUpdated");

	    // Reload the page if no error
	    $_SESSION['savemsg'] = $save_message_text;
	    header("Location: ".getmyurl('user_edit', $login));
	    exit;
	}
} else {
    // Show "Profile Updated" message on reload
    $main_smarty->assign('savemsg', $_SESSION['savemsg']);
    unset($_SESSION['savemsg']);
}

// display profile
show_profile();

function show_profile() {
	global $user, $main_smarty, $the_template, $CSRF, $db;

	$CSRF->create('profile_change', true, true);

	// assign avatar source to smarty
	$main_smarty->assign('UseAvatars', do_we_use_avatars());
	$main_smarty->assign('Avatar', $avatars = get_avatar('all', '', $user->username, $user->email));
	$main_smarty->assign('Avatar_ImgLarge', $avatars['large']);
	$main_smarty->assign('Avatar_ImgSmall', $avatars['small']);

	// module system hook
	$vars = '';
	check_actions('profile_show', $vars);
	
	// assign profile information to smarty	
	$main_smarty->assign('user_id', $user->id);
	$main_smarty->assign('user_email', $user->email);
	$main_smarty->assign('user_login', $user->username);
	$main_smarty->assign('user_names', $user->names);
	$main_smarty->assign('user_username', $user->username);
	$main_smarty->assign('userlevel', $user->level);
	$main_smarty->assign('user_url', $user->url);
	$main_smarty->assign('user_publicemail', $user->public_email);
	$main_smarty->assign('user_location', $user->location);
	$main_smarty->assign('user_occupation', $user->occupation);
	$main_smarty->assign('user_facebook', $user->facebook);
	$main_smarty->assign('user_twitter', $user->twitter);
	$main_smarty->assign('user_linkedin', $user->linkedin);
	$main_smarty->assign('user_googleplus', $user->googleplus);
	$main_smarty->assign('user_skype', $user->skype);
	$main_smarty->assign('user_pinterest', $user->pinterest);
	$main_smarty->assign('user_karma', $user->karma);
	$main_smarty->assign('user_joined', get_date($user->date));
	$main_smarty->assign('user_avatar_source', $user->avatar_source);
	$user->all_stats();
	$main_smarty->assign('user_total_links', $user->total_links);
	$main_smarty->assign('user_published_links', $user->published_links);
	$main_smarty->assign('user_total_comments', $user->total_comments);
	$main_smarty->assign('user_total_votes', $user->total_votes);
	$main_smarty->assign('user_published_votes', $user->published_votes);
	
	// If the user language setting is NULL, present the site's default language file
	$main_smarty->assign('user_language', !empty($user->language) ? $user->language : KAHUK_LANG);

	$languages = array();
	$files = glob("languages/*.conf");
	foreach ($files as $file)
	    if (preg_match('/lang_(.+?)\.conf/',$file,$m))
		$languages[] = $m[1];
	$main_smarty->assign('languages', $languages);
		
	// pagename	
	define('pagename', 'user_edit'); 
	$main_smarty->assign('pagename', pagename);
	
	$main_smarty->assign('form_action', $_SERVER["PHP_SELF"]);

	// User Settings
	$user_categories = explode(",", $user->extra_field['user_categories']);
		
	$categorysql = "SELECT * FROM " . table_categories . " where category__auto_id!='0' ";
	$results = $db->get_results($categorysql);
	$results = object_2_array($results);
	$category = array();
	foreach($results as $key => $val)
		$category[] = $val['category_name'];
			
	$main_smarty->assign('category', $results);
	$main_smarty->assign('user_category', $user_categories);
	$main_smarty->assign('view_href', 'submitted');

	if (Allow_User_Change_Templates)
	{
		$dir = "templates";
		$templates = array();
		foreach (scandir($dir) as $file)
		    if (strstr($file,".")!==0 && file_exists("$dir/$file/header.tpl"))
			$templates[] = $file;
		$main_smarty->assign('templates', $templates);
		$main_smarty->assign('current_template', sanitize($_COOKIE['template'],3));
		$main_smarty->assign('Allow_User_Change_Templates', Allow_User_Change_Templates);
	}

	// show the template
	$main_smarty->assign('tpl_center', $the_template . '/user_settings_center');
	$main_smarty->display($the_template . '/kahuk.tpl');	
}

function save_profile() {
	global $user, $current_user, $db, $main_smarty, $CSRF, $canIhaveAccess, $language;
	// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}
	// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'profile_change') == 2){
		if(!isset($_POST['save_profile']) || !$_POST['process'] || (!$canIhaveAccess && sanitize($_POST['user_id'], 3) != $current_user->user_id)) return;
		//Redwine: sanitizing the POST and REQUEST
		$sanitezedPOST = array();
		foreach ($_REQUEST as $key => $value) {
			if ($key == 'login') {
				$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_STRING);
			}elseif ($key == 'user_login') {
				$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_STRING); 
			}elseif ($key == 'email') {
				if (filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
					$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
				}else{
					$sanitezedPOST[$key] = '';
				}
			}elseif ($key == 'public_email') {
				if (filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
					$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
				}else{
					$sanitezedPOST[$key] = '';
				}
			}elseif ($key == 'url') {
				if (filter_var(filter_var($value, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL)) {
					$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_URL);
				}else{
					$sanitezedPOST[$key] = '';
				}
			}elseif ($key == 'process') {
				if (filter_var(filter_var($value, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT)) {
					$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}else{
					$sanitezedPOST[$key] = '';
				}
			}elseif ($key == 'user_id') {
				if (filter_var(filter_var($value, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT)) {
					$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}else{
					$sanitezedPOST[$key] = '';
				}
			}elseif ($key == 'chack') {
				foreach ($_REQUEST['chack'] as $k => $v) {
					if (filter_var(filter_var($v, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT)) {
						$sanitezedPOST[$key][$k] = filter_var($v, FILTER_SANITIZE_NUMBER_INT);
					}else{
						$sanitezedPOST[$key][$k] = '';
					}
				}
			}else{
				$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_STRING);
			}
		}
		$_POST = $_REQUEST = $sanitezedPOST;

		if ($user->email!=sanitize($_POST['email'], 3)) {
		    if(!check_email(sanitize($_POST['email'], 3))) {
                $savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Profile_BadEmail");
                return $savemsg;
		    } elseif (email_exists(trim(sanitize($_POST['email'], 3)))) { // if email already exists
                $savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Register_Error_EmailExists");
                return $savemsg;
		    } else {
                if(kahuk_validate()) {
                    $encode=md5($_POST['email'] . $user->karma .  $user->username. kahuk_hash().$main_smarty->get_config_vars('KAHUK_Visual_Name'));

                    $domain = $main_smarty->get_config_vars('KAHUK_Visual_Name');			
                    $validation = my_base_url . my_kahuk_base . "/validation.php?code=$encode&uid=".urlencode($user->username)."&email=".urlencode($_POST['email']);
                    /*Redwine: fixed the $str to correctly print the username and the domain email address, in the message.*/
                    $str = sprintf($main_smarty->get_config_vars('KAHUK_PassEmail_Email_Change_Message'), $user->username, $main_smarty->get_config_vars('KAHUK_PassEmail_From'));
                    eval('$str = "'.str_replace('"','\"',$str).'";');
                    $message = "$str";
                    
                    $subject = $main_smarty->get_config_vars('KAHUK_Visual_User_Profile_Email_Change');
                    
                    $AddAddress = $_POST['email'];
                    
                    //Redwine: require the file for email sending.
                    require('libs/phpmailer/sendEmail.php');
                        
                    if(!$mail->Send()) {
                        $savemsg = $main_smarty->get_config_vars('KAHUK_Visual_Login_Delivery_Failed');
                        return $savemsg;
                        exit;
                    }else{
                        $savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Email_Change").'<br />' .$main_smarty->get_config_vars("KAHUK_Visual_Register_Noemail").' '.sprintf($main_smarty->get_config_vars("KAHUK_Visual_Register_ToDo"),$main_smarty->get_config_vars('KAHUK_PassEmail_From'));
                        
                        /*Redwine: to print on the page, FOR THE PURPOSE OF TESTING ON LOCALHOST ONLY!*/
                        if (allow_smtp_testing == 1 && smtp_fake_email == 1) {
                            return $savemsg. "<br /><hr /><span style=\"font-style: italic;\">You are viewing the email message for the purpose of testing SMTP email sending.<br />" .$message . "</span><hr />";
                        }else{
                            return $savemsg;
                        }
                    }
                }
                else
                    $user->email=sanitize($_POST['email'], 2);
		    }
		}

		// User settings
		if (Allow_User_Change_Templates && file_exists("./templates/".$_POST['template']."/header.tpl"))
		{
			$domain = preg_replace('/^www/','',$_SERVER['HTTP_HOST']);
			// Remove port information.
			$port = strpos($domain, ':');
			if ($port !== false)  $domain = substr($domain, 0, $port);			
			if (!strstr($domain,'.') || strpos($domain,'localhost:')===0) $domain='';
			setcookie("template", $_POST['template'], time()+60*60*24*30,'/',$domain);
		}

		$sqlGetiCategory = "SELECT category__auto_id from " . table_categories . " where category__auto_id!= 0;";
		$sqlGetiCategoryQ = $db->get_col($sqlGetiCategory);
		/* Redwine: changed the query above to get_col and therefore we do not need to create $arr anymore. */
		/*$arr = array();
		foreach($sqlGetiCategoryQ as $row) { 
			$arr[] = $row;
		}*/
		$select_check = $_POST['chack'];
		if (!$select_check) $select_check = array();
		$diff = array_diff($sqlGetiCategoryQ,$select_check);
		$select_checked = $db->escape(implode(",",$diff));

		$sql = "UPDATE " . table_users . " set user_categories='$select_checked' WHERE user_id = '{$user->id}'";	
		$query = $db->query($sql);
		/////
		
		// Santizie user input
		$user->url=sanitize($_POST['url'], 2);
		$user->public_email=sanitize($_POST['public_email'], 2);
		$user->location=sanitize($_POST['location'], 2);
		$user->occupation=sanitize($_POST['occupation'], 2);
		$user->facebook=sanitize($_POST['facebook'], 2);
		$user->twitter=sanitize($_POST['twitter'], 2);
		$user->linkedin=sanitize($_POST['linkedin'], 2);
		$user->googleplus=sanitize($_POST['googleplus'], 2);
		$user->skype=sanitize($_POST['skype'], 2);
		$user->pinterest=sanitize($_POST['pinterest'], 2);
		$user->names=sanitize($_POST['names'], 2);
		if ($main_smarty->get_template_vars('user_language')){
			$user->language=sanitize($_POST['language'], 2);
		}
		
		// Convert user input social URLs to username values
		$facebookUrl = $user->facebook;
		preg_match("/https?:\/\/(www\.)?facebook\.com\/([^\/]*)/", $facebookUrl, $matches);
		if ($matches){
			$user->facebook = $matches[2];
		}
		$twitterUrl = $user->twitter;
		preg_match("/https?:\/\/(www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/", $twitterUrl, $matches);
		if ($matches){
			$user->twitter = $matches[3];
		}
		$linkedinUrl = $user->linkedin;
		preg_match("/https?:\/\/(www\.)?linkedin\.com\/in\/([^\/]*)/", $linkedinUrl, $matches);
		if ($matches){
			$user->linkedin = $matches[2];
		}
		$googleplusUrl = $user->googleplus;
		preg_match("/https?:\/\/plus\.google\.com\/([^\/]*)/", $googleplusUrl, $matches);
		if ($matches){
			$user->googleplus = $matches[1];
		}
		$pinterestUrl = $user->pinterest;
		preg_match("/https?:\/\/(www\.)?pinterest\.com\/([^\/]*)/", $pinterestUrl, $matches);
		if ($matches){
			$user->pinterest = $matches[2];
		}
		
		// module system hook
		$vars = '';
		check_actions('profile_save', $vars);
	
/*		$avatar_source = sanitize($_POST['avatarsource'], 2);
		if($avatar_source != "" && $avatar_source != "useruploaded"){
			loghack('Updating profile, avatar source is not one of the list options.', 'username: ' . sanitize($_POST["username"], 3) . '|email: ' . sanitize($_POST["email"], 3));
			$avatar_source == "";
		}
		$user->avatar_source=$avatar_source;
*/	
	  if($user->level=="admin" || $user->level=="moderator"){
		  if ($user->username!=sanitize($_POST['user_login'], 3))
			{
			 $user_login=sanitize($_POST['user_login'], 2);
				
			if (preg_match('/\pL/u', 'a')) {	// Check if PCRE was compiled with UTF-8 support
			if (!preg_match('/^[_\d\p{L}\p{M}]+$/iu',$user_login)) { // if username contains invalid characters
			$savemsg = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserInvalid');
			return $savemsg;
			}
			} else {
				if (!preg_match('/^[^~`@%&=\\/;:\\.,<>!"\\\'\\^\\.\\[\\]\\$\\(\\)\\|\\*\\+\\-\\?\\{\\}\\\\]+$/', $user_login)) {
				$savemsg = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserInvalid');
				 return $savemsg;
				}
			}
		
					
			if(user_exists(trim($user_login)) ) {
			  $savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Register_Error_UserExists");
			  $user->username= $user_login;
			  return $savemsg;
			
			 }else{
			  $user->username=$user_login;
			  $saved['username']=1;
			  }
			 
			}
	    }
	
		if(!empty($_POST['newpassword']) || !empty($_POST['newpassword2'])) {
			$oldpass = sanitize($_POST['oldpassword'], 0);
			$userX=$db->get_row("SELECT user_id, user_pass, user_login FROM " . table_users . " WHERE user_login = '".$user->username."'");
            if(verifyPassHash($oldpass, $userX->user_pass)){
				if(sanitize($_POST['newpassword'], 3) !== sanitize($_POST['newpassword2'], 3)) {
					$savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Profile_BadPass");
					return $savemsg;
				} else {
					$saltedpass=generatePassHash(sanitize($_POST['newpassword'], 3));
					$user->pass = $saltedpass;
					$saved['pass']=1;
				}
			} else {
				$savemsg = $main_smarty->get_config_vars("KAHUK_Visual_Profile_BadOldPass");
				return $savemsg;
			}
		}
		
		$user->store();
		$user->read();
		if($saved['pass']==1 || $saved['username']==1)
		 $current_user->Authenticate($user->username, $user->pass, false);
		else{
		 $current_user->Authenticate($user->username, $user->pass);
		 $saved['profile']=1;
		}
		return $saved;
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}
}
