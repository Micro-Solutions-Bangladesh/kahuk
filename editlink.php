<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

check_referrer();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$main_smarty->assign('isAdmin', $canIhaveAccess);
$main_smarty->assign('isModerator', checklevel('moderator'));



if(isset($_GET['id'])){
	$theid = sanitize($_GET['id'], 3);
}
if(isset($_POST['id'])){
	$theid = sanitize($_POST['id'], 3);
}
if(!is_numeric($theid)){$theid = 0;}
// misc smarty
if (checklevel('admin'))
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_God;
elseif (checklevel('moderator'))
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Admin;
else
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Normal;
$main_smarty->assign('Story_Content_Tags_To_Allow', htmlspecialchars($Story_Content_Tags_To_Allow));

// DB 11/11/08
$link = $db->get_row("SELECT link_id, link_author, UNIX_TIMESTAMP(link_date) AS date FROM " . table_links . " WHERE link_id=".$theid.";");
/////
if ($link) {
	if ($link->link_author==$current_user->user_id || $current_user->user_level == "moderator" || $current_user->user_level == "admin")
	{
		// DB 11/11/08
		if ($current_user->user_level != "admin" && $current_user->user_level != "moderator" && limit_time_to_edit!=0 && (time()-$link->date)/60 > edit_time_limit)
		{
/*Redwine: To fix the editlink error message in the case where set time limit to edit the story is set to true. It was displaying as plain html and now the same as all other error messages, See pictures in http://mymonalisasmile.com/upload/editlink-error.html*/
				/* Redwine: we want to redirect the user to the page they came from when they click the back button and not to the editlink page, otherwise they will get stuck on the error page in an infinite loop. */
			$main_smarty->assign('submit_error', 'cantedit');
				$toredirect = $_SERVER['HTTP_REFERER'];
				$main_smarty->assign('toredirect', $toredirect);
				/* Redwine: we need the value of edit_time_limit to pass it to the submit_errors_center.tpl because the value of KAHUK_Visual_EditLink_Timeout in the tpl file displays literally as "You cannot edit link after %s minutes." */
				$edit_time_limit = edit_time_limit;
				$main_smarty->assign('edit_time', $edit_time_limit);
				
				$error = true;
				if ($error) {
					$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
					$main_smarty->assign('link_id', $_GET['id']);
			
					// pagename
					define('pagename', 'editlink'); 
					$main_smarty->assign('pagename', pagename);
					
					// show the template
					$main_smarty->display($the_template . '/kahuk.tpl');
				}
			return $error;
			
			exit;
		}
		/////

		$CSRF = new csrf();


		if(isset($_POST["id"])) {
		    // Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
			if (empty($_POST['token'])) {
				$CSRF->show_invalid_error(1);
				exit;
			}
			// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
		    if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'edit_link') == 2){
			$linkres=new Link;
			$linkres->id=$link_id = sanitize($_POST['id'], 3);
			if(!is_numeric($link_id)) die();
			$linkres->read();

			// if notify link submitter is selected
				if(isset($_POST["notify"])) {
					if(sanitize($_POST["notify"], 3) == "yes") {
					$link_author = $db->get_col("SELECT link_author FROM " . table_links . " WHERE link_id=".$theid.";");
					$user = $db->get_row("SELECT * FROM " . table_users . " WHERE user_id=".$link_author[0].";");

					$AddAddress = $user->user_email;
					$subject = $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_Subject');
					$message = $user->user_login . ", \r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_AdminMadeChange') . "\r\n";
					$message = $message . $my_base_url . getmyurl('story', sanitize($_POST['id'], 3)) . "\r\n\r\n";

                    if (!is_array($_POST["category"])) {   
						if ($linkres->category != sanitize($_POST["category"], 3)){
							$message = $message . $main_smarty->get_config_vars('KAHUK_Visual_Submit2_Category') . " change\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_PreviousText') . ": <strong>" . GetCatName($linkres->category) . "</strong>\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_NewText') . ": <strong>" . GetCatName(sanitize($_POST["category"], 3)) . "</strong>\r\n\r\n";
						}
                    } else {
                        //get the originally selected categories.
                        $previousCats = '';
                        $count = 0;

                        foreach ($_POST["additionalCats"] as $key => $value) {
                            $count++;
                            if ($count == sizeof($_POST["additionalCats"])) {
                                $previousCats .= "<strong>" . GetCatName($value) . "</strong>";
                            } else {
                                $previousCats .= "<strong>" . GetCatName($value) . "</strong>, ";
                            }
                        }
                        //get the new selected categories.
                        $newCats = '';
                        $count = 0;
                        
                        foreach ($_POST["category"] as $key => $value) {
                            $count++;
                            if ($count == sizeof($_POST["category"])) {
                                $newCats .= "<strong>" . GetCatName($value). "</strong>";
                            } else {
                                $newCats .= "<strong>" . GetCatName($value) . "</strong>, ";
                            }
                        }
                        $message = $message . $main_smarty->get_config_vars('KAHUK_Visual_Submit2_Category') . " change\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_PreviousText') . ": " . $previousCats . "\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_NewText') . ": " . $newCats . "\r\n\r\n";
                    }
						if ($linkres->title != sanitize(trim($_POST["title"]), 4, $Story_Content_Tags_To_Allow)){
							$message = $message . $main_smarty->get_config_vars('KAHUK_Visual_Submit2_Title') . " change\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_PreviousText') . ": " . $linkres->title . "\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_NewText') . ": " . sanitize(trim($_POST["title"]), 3) . "\r\n\r\n";
						}      
					
						if ($linkres->content != close_tags(sanitize($_POST["bodytext"], 4, $Story_Content_Tags_To_Allow))) {
							$message = $message . $main_smarty->get_config_vars('KAHUK_Visual_Submit2_Description') . " change\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_PreviousText') . ": " . $linkres->content . "\r\n\r\n" . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_NewText') . ": " . close_tags(sanitize($_POST["bodytext"], 3)) . "\r\n\r\n";
						}

					$message = $message . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Email_ReasonText') . ": ";
						if (sanitize($_POST["reason"], 3) == "other") {
							$message = $message . sanitize($_POST["otherreason"], 3);
						}else{
							$message = $message . $main_smarty->get_config_vars('KAHUK_Visual_EditStory_Reason_' . sanitize($_POST["reason"], 3));
						}

					$headers = 'From: ' . $main_smarty->get_config_vars("KAHUK_PassEmail_From") . "\r\n";
					$headers .= "Content-type: text/plain; charset=utf-8\r\n";
					
					//Redwine: require the file for email sending.
                    require('libs/phpmailer/sendEmail.php');

					if(!$mail->Send()) {
						$notifyStatus = $main_smarty->get_config_vars('KAHUK_Visual_Login_Delivery_Failed');
					} else {
						$notifyStatus = $main_smarty->get_config_vars("KAHUK_PassEmail_SendSuccess");
						if (allow_smtp_testing == 1 && smtp_fake_email == 1) {
							$notifyStatus .= "<br /><hr /><br />$message";
						}
					}
					$_SESSION['notifyStatus'] = $notifyStatus;
				}
			}

            if ($linkres->author != sanitize($_POST["author"],3) && !empty($_POST["author"]))
			  $linkres->author = sanitize($_POST["author"],3);
			 
				if($canIhaveAccess == 1) {
				$url = htmlspecialchars(sanitize($_POST['url'], 3));
				$url= str_replace('&amp;', '&', $url);  

				$linkres->url=$url;
			}

			$vars = '';
			check_actions('edit_link_hook', $vars);

				if (is_array($_POST['category'])) {
			    $linkres->category=sanitize($_POST['category'][0], 3);
			    $linkres->additional_cats=array_slice($_POST['category'],1);
				}else{
					$linkres->category=sanitize($_POST['category'], 3);
			}
			if($linkres->title != stripslashes(sanitize(trim($_POST['title']), 3))){
				$linkres->title = stripslashes(sanitize(trim($_POST['title']), 3));
				$linkres->title_url = makeUrlFriendly($linkres->title, $linkres->id);
			}
			
			$linkres->content = close_tags(sanitize($_POST['bodytext'], 4, $Story_Content_Tags_To_Allow));

			if(isset($_POST['summarytext']) && sanitize($_POST['summarytext'], 3) == ""){
				$linkres->link_summary = utf8_substr(sanitize($_POST['bodytext'], 4, $Story_Content_Tags_To_Allow), 0, StorySummary_ContentTruncate - 1);
				//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));	
			} else {
				$linkres->link_summary = sanitize($_POST['summarytext'], 4, $Story_Content_Tags_To_Allow);
				//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));
				if(utf8_strlen($linkres->link_summary) > StorySummary_ContentTruncate){
					loghack('SubmitAStory-SummaryGreaterThanLimit', 'username: ' . sanitize($_POST["username"], 3).'|email: '.sanitize($_POST["email"], 3), true);
					$linkres->link_summary = utf8_substr($linkres->link_summary, 0, StorySummary_ContentTruncate - 1);
					//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));
				}
			}
            /*** Redwine: Roles and permissions and Groups fixes. To add the ability to submit to a group or to change from one group to another ***/
            //get link_group_id
			if((isset($_POST['link_group_id']))&&($_POST['link_group_id']!='')){
				$linkres->link_group_id = intval($_POST['link_group_id']);
				}else{
				$linkres->link_group_id=0;
			}
			// Steef 2k7-07 security fix start ----------------------------------------------------------
			if (isset($_POST['link_field1'])) $linkres->link_field1 = sanitize($_POST['link_field1'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field2'])) $linkres->link_field2 = sanitize($_POST['link_field2'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field3'])) $linkres->link_field3 = sanitize($_POST['link_field3'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field4'])) $linkres->link_field4 = sanitize($_POST['link_field4'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field5'])) $linkres->link_field5 = sanitize($_POST['link_field5'], 4, $Story_Content_Tags_To_Allow);		
			if (isset($_POST['link_field6'])) $linkres->link_field6 = sanitize($_POST['link_field6'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field7'])) $linkres->link_field7 = sanitize($_POST['link_field7'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field8'])) $linkres->link_field8 = sanitize($_POST['link_field8'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field9'])) $linkres->link_field9 = sanitize($_POST['link_field9'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field10'])) $linkres->link_field10 = sanitize($_POST['link_field10'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field11'])) $linkres->link_field11 = sanitize($_POST['link_field11'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field12'])) $linkres->link_field12 = sanitize($_POST['link_field12'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field13'])) $linkres->link_field13 = sanitize($_POST['link_field13'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field14'])) $linkres->link_field14 = sanitize($_POST['link_field14'], 4, $Story_Content_Tags_To_Allow); 
			if (isset($_POST['link_field15'])) $linkres->link_field15 = sanitize($_POST['link_field15'], 4, $Story_Content_Tags_To_Allow); 
			// Steef 2k7-07 security fix end --------------------------------------------------------------
			
			// $linkres->content = str_replace("\n", "<br />", $linkres->content);

			if (link_errors($linkres)) {
				return;
			}

				/* Redwine: since I introduced the draft feature, I also provided for changing the status by the author, I.e keeping it as a draft, posting or discarding it, from the editlink option. So I first check the the current status of the article; if it is a draft, then if the author changed the status in the edit article, I act accordingly.*/
				$story_url = $linkres->get_internal_url();
				if ($_POST['change_status'] == $linkres->status) { // status unchanged, we  stay on the story page
			if ($linkres->status == 'draft') {
						$story_url = getmyurl('user2', $login, 'draft');
					}elseif ($linkres->status == 'scheduled') {
						$story_url = getmyurl('user2', $login, 'scheduled');
					}else{
						$story_url = $linkres->get_internal_url();
					}
				}elseif ($_POST['change_status'] == 'to_new') {
					/*Redwine: status changed from draft to post it. We have to first change the status to new and store_basic() then we call check_should_publish() to check if it is due to be published.*/	
					if (($linkres->status == 'draft' || $linkres->status == 'scheduled') && $_POST['change_status'] == 'to_new') {
						/*Redwine: then, because the check_should_publish() currently checks for the status if it is "new", we have to adjust the count of the "draft" and the "new" before calling check_should_publish() which calls the publish() to change the status and calls totals_adjust_count() to substract 1 from the "new" and adds 1 to "published". so by adding 1 to "new" in the line below, we balance the "new" count, only if the article met the criteria. Otherwise,  the 2 lines below do the trick and keep the count accurate!*/
						totals_adjust_count("$linkres->status", -1);
						totals_adjust_count('new', 1);
						$linkres->status = 'new';
						/*Redwine: we also don't want to penalize the article if the days_to_publish variable is set for a number of days (default is 10 days), so we have to change the link_date and link_published_date to the current date and time. */
						$linkres->date = time();
						$linkres->published_date = time();
					$linkres->check_should_publish();
						$story_url = getmyurl($linkres->status, ''); //to load the page depending on the status, new or published.
						/* Redwine: after calling check_should_publish(), if it should be published, the publish() function changes the status and calls totals_adjust_count function. However, if it's not to be published, then the status changes to 'new' and we have to call totals_adjust_count function. Also, the new linkres->status will serve to call getmyurl with new or publish status accordingly!*/
				}
				}elseif (($linkres->status == 'new' || $linkres->status == 'published') && $_POST['change_status'] == 'to_draft') {
					totals_adjust_count($linkres->status, -1);
					totals_adjust_count('draft', 1);
					$linkres->status = 'draft';
					$story_url = getmyurl('user2', $login, 'draft');
					/* Redwine: the status has changed and the the totals_adjust_count function is only invoked from the check_should_publish() that calls publish() function in the libs/link.php, should the story meet the conditions to be published. Therefore, I have to call totals_adjust_count function to adjust the counts.*/
				}elseif ($_POST['change_status'] == 'discard') {
					totals_adjust_count($linkres->status, -1);
					totals_adjust_count('discard', 1);
					$linkres->status = 'discard';
					$story_url = getmyurl($linkres->status, '');
			}

			$linkres->store();
				$story_url = $my_base_url.str_replace("&amp;", "&", $story_url);
			header('Location: ' . $story_url);
		    } else {
		    	$CSRF->show_invalid_error(1);
		    }
		    exit;
		}
		else
		{
			$linkres=new Link;

			$edit = false;

			$link_id = sanitize($_GET['id'], 3);
			if (!is_numeric($link_id)) die();
			$linkres->id=$link_id;
			$linkres->read();
			$link_title = $linkres->title;
			$link_content = $linkres->content;
			//$link_content = str_replace("<br />", "\n", $linkres->content);
			$link_category=$linkres->category;
			$link_summary = $linkres->link_summary;
			//$link_summary = str_replace("<br />", "\n", $link_summary);
/*** Redwine: Roles and permissions and Groups fixes. To add the ability to submit to a group or to change from one group to another ***/
			$link_group = $linkres->link_group_id;
			// Get the username
			$link_author = $db->get_col("SELECT link_author FROM " . table_links . " WHERE link_id=".$theid.";");
			$user = $db->get_row("SELECT * FROM " . table_users . " WHERE user_id=".$link_author[0].";");
			$main_smarty->assign('author', $user->user_id);
			
			$usersql = $db->get_results("SELECT user_id, user_login FROM " . table_users . " WHERE user_enabled=1 and user_login!='' ORDER BY user_login",ARRAY_A);
			$userdata = array();				
			foreach($usersql as $rows) array_push ($userdata, $rows);
			//$userdata = $db->get_results("SELECT user_id, user_login FROM " . table_users . " WHERE user_enabled=1");
			
			//echo "<pre>";
			//print_r($userdata);
			$main_smarty->assign('userdata', $userdata);
			$main_smarty->assign('submit_url', $linkres->url);
			$main_smarty->assign('submit_url_title', $linkres->url_title);
			$main_smarty->assign('submit_id', $linkres->id);
			$main_smarty->assign('submit_stat', $linkres->status);
			$main_smarty->assign('warning_message',sprintf($main_smarty->get_config_vars('KAHUK_Visual_Submit2_Edit_Draft_Notice'),$linkres->status));
			$main_smarty->assign('submit_type', $linkres->type());
			$main_smarty->assign('submit_title', htmlspecialchars($link_title));
			$main_smarty->assign('submit_content', $link_content);
			$main_smarty->assign('submit_category', $link_category);
			$main_smarty->assign('submit_additional_cats', $linkres->additional_cats);
/*** Redwine: Roles and permissions and Groups fixes. To add the ability to submit to a group or to change from one group to another ***/
$main_smarty->assign('link_group', $link_group);
			if(isset($trackback)){$main_smarty->assign('submit_trackback', $trackback);}
			//$main_smarty->assign('SubmitSummary_Allow_Edit', SubmitSummary_Allow_Edit);
			$main_smarty->assign('StorySummary_ContentTruncate', StorySummary_ContentTruncate);
			$main_smarty->assign('submit_summary', $link_summary);			
			$main_smarty->assign('submit_link_field1', $linkres->link_field1);
			$main_smarty->assign('submit_link_field2', $linkres->link_field2);
			$main_smarty->assign('submit_link_field3', $linkres->link_field3);
			$main_smarty->assign('submit_link_field4', $linkres->link_field4);
			$main_smarty->assign('submit_link_field5', $linkres->link_field5);
			$main_smarty->assign('submit_link_field6', $linkres->link_field6);
			$main_smarty->assign('submit_link_field7', $linkres->link_field7);
			$main_smarty->assign('submit_link_field8', $linkres->link_field8);
			$main_smarty->assign('submit_link_field9', $linkres->link_field9);
			$main_smarty->assign('submit_link_field10', $linkres->link_field10);
			$main_smarty->assign('submit_link_field11', $linkres->link_field11);
			$main_smarty->assign('submit_link_field12', $linkres->link_field12);
			$main_smarty->assign('submit_link_field13', $linkres->link_field13);
			$main_smarty->assign('submit_link_field14', $linkres->link_field14);
			$main_smarty->assign('submit_link_field15', $linkres->link_field15);

			$categories = kahuk_categories_init();
			$array = $categories->get_items( [0] );

			$main_smarty->assign('lastspacer', 0);
			$main_smarty->assign('submit_cat_array', $array);

			$canIhaveAccess = 0;
			$canIhaveAccess = $canIhaveAccess + checklevel('admin');
			$canIhaveAccess = $canIhaveAccess + checklevel('moderator');
			$main_smarty->assign('canIhaveAccess', $canIhaveAccess);
/*** Redwine: Roles and permissions and Groups fixes. To add the ability to submit to a group or to change from one group to another ***/
//to display group drop down
	// For now, we only want to show the group field if the current user is the same as the link_author (who submitted the story.
	if(enable_group == "true" && $current_user->user_id == $link_author[0])
	{
		$output = '';
		$group_membered = $db->get_results("SELECT group_id,group_name FROM " . table_groups . " 
			LEFT JOIN ".table_group_member." ON member_group_id=group_id
			WHERE member_user_id = $current_user->user_id AND group_status = 'Enable' 
				AND member_status='active'
				AND (member_role != 'banned' && member_role != 'flagged') 
			ORDER BY group_name ASC");
		
		if ($group_membered)
		{
			$output .= "<select name='link_group_id' tabindex='3' class='form-control submit_group_select'>";
			$output .= "<option value = ''>".$main_smarty->get_config_vars('KAHUK_Visual_Group_Select_Group')."</option>";
			foreach($group_membered as $results)
			{
				// To select the current group that the story is submitted to
				if ($results->group_id == $link_group) {
					$output .= "<option value = ".$results->group_id. ($linkres->link_group_id ? ' selected' : '') . ">".$results->group_name."</option>";
				}else{
					$output .= "<option value = ".$results->group_id . ">".$results->group_name."</option>";
				}
			}
			$output .= "</select>";
		}
		$main_smarty->assign('output', $output);
	}

			$CSRF->create('edit_link', true, true);
			
			// pagename
			define('pagename', 'editlink'); 
		        $main_smarty->assign('pagename', pagename);
			
			// sidebar
			$main_smarty = do_sidebar($main_smarty);

			// show the template
			//$main_smarty->assign('storylen', utf8_strlen(str_replace("<br />", "\n", $link_content)));
			$main_smarty->assign('tpl_extra_fields', $the_template . '/submit_extra_fields');
			$main_smarty->assign('tpl_center', $the_template . '/edit_submission_center');
			$main_smarty->display($the_template . '/kahuk.tpl');
		}
	}
	else
	{
		echo "<br /><br />" . $main_smarty->get_config_vars('KAHUK_Visual_EditLink_NotYours') . "<br/ ><br /><a href=".my_base_url.my_kahuk_base.">".$main_smarty->get_config_vars('KAHUK_Visual_Name')." home</a>";
	}
}
else
{
	echo "<br /><br />" . $main_smarty->get_config_vars('KAHUK_Visual_EditLink_NotYours') . "<br/ ><br /><a href=".my_base_url.my_kahuk_base.">".$main_smarty->get_config_vars('KAHUK_Visual_Name')." home</a>";
}


//copied directly from submit.php
function link_errors($linkres)
{
	global $main_smarty, $the_template, $cached_categories;
	$error = false;

	// if story title or description is too short
	$story = preg_replace('/[\s]+/',' ',strip_tags($linkres->content));
	if(utf8_strlen($linkres->title) < minTitleLength  || utf8_strlen($story) < minStoryLength ) {
		$main_smarty->assign('submit_error', 'incomplete');
		$error = true;
	}
	if(utf8_strlen($linkres->title) > maxTitleLength) {
		$main_smarty->assign('submit_error', 'long_title');
		$error = true;
	}
  	if (utf8_strlen($linkres->content) > maxStoryLength ) { 
		$main_smarty->assign('submit_error', 'long_content');
		$error = true;
	}

  	if (utf8_strlen($linkres->link_summary) > maxSummaryLength ) { 
		$main_smarty->assign('submit_error', 'long_summary');
		$error = true;
	}
	// if URL is submitted in story title
	if(preg_match('/.*http:\//', $linkres->title)) {
		$main_smarty->assign('submit_error', 'urlintitle');
		$error = true;
	}
	// if no category is selected
	if(!$linkres->category > 0) {
		$main_smarty->assign('submit_error', 'nocategory');
		$error = true;
	}
	foreach($cached_categories as $cat) {
		if($cat->category__auto_id == $linkres->category && !allowToAuthorCat($cat)) { // category does not allow authors of this level
			$main_smarty->assign('submit_error', 'nocategory');
			$error = true;
		}
	}

	if ($error)
	{
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
		$main_smarty->assign('link_id', $_GET['id']);
		
		// pagename
		define('pagename', 'editlink'); 
		$main_smarty->assign('pagename', pagename);
		
		// show the template
		$main_smarty->display($the_template . '/kahuk.tpl');
	}
	return $error;
}
