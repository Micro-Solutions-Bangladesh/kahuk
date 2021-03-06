<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;
include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'group.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

$requestID = '';
if(isset($_REQUEST['id'])){$requestID = strip_tags($_REQUEST['id']);}
if(!is_numeric($requestID)){$requestID = 0;}
if(isset($_REQUEST['title']))
{
	/* Redwine: Roles and permissions and Groups fixes. Modified code to save one extra query on LINE 48 */
	$requestTitle = $db->escape(strip_tags($_REQUEST['title']));
	//$requestTitle = sanitize($_GET['title'], 3);
	$requestInfo = $db->get_row("SELECT * FROM " . table_groups . " WHERE group_safename = '".$requestTitle."';");
} elseif ($requestID)
	$requestInfo = $db->get_row("SELECT * FROM " . table_groups . " WHERE group_id = '".$requestID."';");
		// get the group safe name
		$requestTitle = $requestInfo->group_safename;
		// get the group id
		$requestID = $requestInfo->group_id;
		// get the group privcy
		$privacy = $requestInfo->group_privacy;
// find the name of the current category
/* Redwine: initialized a new variable $thecat to eliminate the Notice:  Undefined variable: thecat. */
$thecat = '';
$catID = 0;
if(isset($_REQUEST['category'])){
	$thecat = get_cached_category_data('category_safe_name', sanitize($_REQUEST['category'], 1));
	$catID = $thecat->category_id;
	$thecat = $thecat->category_name;
}


// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Breadcrumb_Submit');
$navwhere['link1'] = getmyurl('submit', '');
$navwhere['text2'] = $thecat;
$main_smarty->assign('posttitle', $requestTitle);
$main_smarty = do_sidebar($main_smarty);

/* Redwine: Roles and permissions and Groups fixes. Get the Site and group roles to establish the permissions */
$isAdmin = $main_smarty->get_template_vars('isAdmin');
$isModerator = $main_smarty->get_template_vars('isModerator');
$main_smarty->assign('isAdmin', $isAdmin);
$main_smarty->assign('isModerator', $isModerator);
$is_gr_creator = get_group_creator($requestID);
$main_smarty->assign('is_gr_creator', $is_gr_creator);
$gr_roles = get_group_roles($requestID);
$is_gr_Admin = 0;
$is_gr_Moderator = 0;
if ($gr_roles == "admin") {
	$is_gr_Admin = 1;
}elseif ($gr_roles == "moderator") {
	$is_gr_Moderator = 1;
}

// pagename
define('pagename', 'group_story'); 

$main_smarty->assign('pagename', pagename); 
// Redwine: Modified code to save one extra query on LINE 48
//$privacy = $db->get_var("SELECT group_privacy FROM " . table_groups . " WHERE group_id = '$requestID';");
$view = '';
if (isset($_REQUEST["view"])) {
	$view = sanitize(sanitize($_REQUEST["view"],1),3);
}
if($requestID > 0)
{
	//For Infinit scrolling and continue reading option 
/* Redwine: Roles and permissions and Groups fixes */	
    if ($is_gr_creator == '1' || $isAdmin == '1' || $isModerator == '1' || $is_gr_Admin == '1' || $is_gr_Moderator == '1' || isMemberActive($requestID)=='active')
    {
		 $main_smarty->assign('group_shared_rows', group_shared($requestID,$catID,1));
		 $main_smarty->assign('group_published_rows', group_stories($requestID,$catID,'published',1));
		 $main_smarty->assign('group_new_rows', group_stories($requestID,$catID,'new',1));
		
        switch ($view) {
            case 'shared':
                group_shared($requestID,$catID);
                break;
			 case 'published':
                group_stories($requestID,$catID,'published');
                break;
			
			 case 'new':
                group_stories($requestID,$catID,'new');
                break;
					
            case 'members':
                member_display($requestID);
                break;
            default:
                group_stories($requestID,$catID,$view);
				
        }
    }
/* Redwine: Roles and permissions and Groups fixes */
    elseif (($privacy!='private' || isMemberActive($requestID)=='active'))
    {
	$main_smarty->assign('group_shared_display', $main_smarty->get_config_vars('KAHUK_Visual_Group_Is_Private'));
	$main_smarty->assign('group_new_display', $main_smarty->get_config_vars('KAHUK_Visual_Group_Is_Private'));
	$main_smarty->assign('group_published_display', $main_smarty->get_config_vars('KAHUK_Visual_Group_Is_Private'));
	$main_smarty->assign('member_display', $main_smarty->get_config_vars('KAHUK_Visual_Group_Is_Private'));
    }
} else 
{
	$redirect = '';
	$redirect = getmyurl("groups");
	header("Location: $redirect");
	die;
}

//displaying group as story
if(isset($requestID))
	group_display($requestID);

$main_smarty->assign('group_members', get_group_members($requestID));


if($view == '') $view = 'published';
$main_smarty->assign('groupview', $view);

if(Auto_scroll==2 || Auto_scroll==3){
		$main_smarty->assign('groupID', $requestID);
		$main_smarty->assign('viewtype', $view);
	}
	

if ($view == 'new')
    $main_smarty->assign('URL_rss_page', getmyurl('rssgroup', $requestTitle, 'new'));
elseif ($view == 'published')
    $main_smarty->assign('URL_rss_page', getmyurl('rssgroup', $requestTitle));
elseif ($view != 'members')
    $main_smarty->assign('URL_rss_page', getmyurl('rssgroup', $requestTitle, $view));

$main_smarty->assign('groupview_published', getmyurl('group_story2', $requestTitle, 'published'));
$main_smarty->assign('groupview_new', getmyurl('group_story2', $requestTitle, 'new'));
if ($view == 'shared')
    $main_smarty->assign('URL_maincategory', getmyurl('group_story2', $requestTitle, 'shared',"category"));
else
    $main_smarty->assign('URL_maincategory', getmyurl('group_story2', $requestTitle, 'published',"category"));
$main_smarty->assign('URL_newcategory', getmyurl('group_story2', $requestTitle, 'new',"category"));
$main_smarty->assign('groupview_sharing', getmyurl('group_story2', $requestTitle, 'shared'));
$main_smarty->assign('groupview_members', getmyurl('group_story2', $requestTitle, 'members'));

$main_smarty->assign('group_edit_url', getmyurl('editgroup',$requestID));
$main_smarty->assign('group_delete_url', getmyurl('deletegroup',$requestID));

$CSRF = new csrf();
/*******************
Redwine: a quick and dirty way, until we fix the group avatar issue with not displaying the uploading image unless we load another page and then hard-refresh the page, other wise we get the CSRF error to go back and refresh the page.
So the upload_time variable will be used to always reload a fresh copy of the avatar! Using it only when there is an upload will still require us to refresh the page and get the CSRF error and hard-refresh the page again!
*******************/
$upload_time = time();
$main_smarty->assign('upload_time','?='.$upload_time);
// uploading avatar
if(isset($_POST["avatar"]) && $_POST["avatar"] == "uploaded")
{
	check_referrer();

    // Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}
	// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
    if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'edit_group') == 2){

	$user_image_path = "avatars/groups_uploaded" . "/";
	$user_image_apath = "/" . $user_image_path;
	$allowedFileTypes = array("image/jpeg","image/gif","image/png",'image/x-png','image/pjpeg');
	unset($imagename);
	$myfile = $_FILES['image_file']['name'];
	$imagename = basename($myfile);
	$mytmpfile = $_FILES['image_file']['tmp_name'];
	if ($_FILES['image_file']["size"]/1024 > max_group_avatar_size) {
		$error['Type'] = 'Maximum file size '. max_group_avatar_size . 'Kb exceeded';
	}
	if(!in_array($_FILES['image_file']['type'],$allowedFileTypes))
	{
		$error['Type'] = 'Only these file types are allowed : jpeg, gif, png';
	}
 
	if(empty($error))
	{
		$imagesize = getimagesize($mytmpfile);
		$width = $imagesize[0];
		$height = $imagesize[1];
		$idname = $_POST["idname"];
		if(!is_numeric($idname)){die();}
		$imagename = $idname . "_original.jpg";
		$newimage = $user_image_path . $imagename ;
		$result = @move_uploaded_file($_FILES['image_file']['tmp_name'], $newimage);
		if(empty($result))
			$error["result"] = "There was an error moving the uploaded file.";
		else {
			$avatar_source = cleanit($_POST['avatarsource']);

			$sql = "UPDATE " . table_groups . " set group_avatar='uploaded' WHERE group_id=$idname";
			$db->query($sql);
			$main_smarty->assign('Avatar_uploaded', 'Avatar uploaded successfully. If the new avatar is not displayed, you may need to hard-refresh the page to see the new image.');
			/*if($avatar_source != "" && $avatar_source != "useruploaded"){
				loghack('Updating profile, avatar source is not one of the list options.', 'username: ' . $_POST["username"].'|email: '.$_POST["email"]);
				$avatar_source == "";
			}*/
			//$user->avatar_source=$avatar_source;
			//$user->store();
	// create large avatar
	include KAHUK_LIBS_DIR . "class.pThumb.php";
	$img=new pThumb();
	$img->pSetSize(group_avatar_size_width, group_avatar_size_height);
	$img->pSetQuality(100);
	$img->pCreate($newimage);
	$img->pSave($user_image_path . $idname . "_".group_avatar_size_width.".jpg");
	$img = "";
			
$redirect = getmyurl("group_story2", $requestTitle);
header("Location: $redirect");
		}
	}else{
		$main_smarty->assign('Avatar_uploaded', $error['Type']);
	}
	

	/*// create small avatar
	$img=new pThumb();
	$img->pSetSize(group_avatar_size_width, group_avatar_size_height);
	$img->pSetQuality(100);
	$img->pCreate($newimage);
	$img->pSave($user_image_path . $idname . "_".group_avatar_size_width.".jpg");
	$img = "";*/
    } else {
    	$CSRF->show_invalid_error(1);
	exit;
    }
}
$CSRF->create('edit_group', true, true);

/***************************
Redwine: if we are testing the smtp email send, WITH A FAKE EMAIL ADDRESS, the SESSION variable will allow us to print the email message when the register_complete.php is loaded, so that the account that is created can be validated and activated.
***************************/
if (allow_smtp_testing == 1 && smtp_fake_email == 1) {
	$main_smarty->assign('groupInvitation', $_SESSION['groupInvitation']);
	unset($_SESSION['groupInvitation']);
}

//Redwine: for the private group joining email
if (allow_smtp_testing == 1 && smtp_fake_email == 1) {
	$main_smarty->assign('errorSending', $_SESSION['errorSending']);
	unset($_SESSION['errorSending']);
}

$main_smarty->assign('tpl_center', $the_template . '/group_story_center');
$main_smarty->display($the_template . '/kahuk.tpl');

function cleanit($value)
{
	$value = strip_tags($value);
	$value = trim($value);
	return $value;
}
