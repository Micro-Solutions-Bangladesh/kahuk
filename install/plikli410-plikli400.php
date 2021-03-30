<?php
session_start();

include('db-mysqli.php');

$warnings_alter_table = array();
$warnings = array();
$warnings_rename = array();

//get the name of the directory from where the upgrade is running.
$arr_script = explode("/", $_SERVER['SCRIPT_NAME']);
$upgrade_folder = $arr_script[1];

$notok = 'notok.png';
$ok = 'ok.png';

/**********************************
Redwine: checking for the MySQL Server version. If it is older than 5.0.3, then `link_url` varchar will be the maximum of 255; otherwise, we set it to 512 to accommodate long urlencoded.
**********************************/
$pattern = '/[^0-9-.]/i';
$replacement = '';
$mysqlServerVersion = $handle->server_info;
$mysqlServerVersion = preg_replace($pattern, $replacement, $mysqlServerVersion);
if (strpos($mysqlServerVersion, '-') > 0){ 
	$mysqlServerVersion = strstr($mysqlServerVersion, '-', true);
}else{
	$mysqlServerVersion = $mysqlServerVersion;
}

if ($mysqlServerVersion < '5.0.3') {
	$urllength = '255';
}else{
	$urllength = '512';
}

/* 
ALTER the database collation and character set
*/
echo '<fieldset><legend>Alter DATABASE</legend><ul>';
$sql_alter_DB = $handle->query("ALTER DATABASE '" . EZSQL_DB_NAME. "' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$result = $handle->query($sql_alter_DB);
if (!$result) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Altered DATABASE '.EZSQL_DB_NAME . ' Collation <img src="'.$marks.'" class="iconalign" /></li>';
echo '</ul></fieldset><br />'; 

/* 
First round, ALTER specific tables to change some columns
*/
echo '<fieldset><legend>ALTER specific tables to change some columns</legend><ul>';
$sql_alter_date = $handle->query("alter table `" . table_prefix . "links` change `link_date` `link_date` timestamp NULL DEFAULT NULL;");

if (!$sql_alter_date) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Altered Table '. table_prefix . 'links` changed link_date to NULL DEFAULT NULL <img src="'.$marks.'" class="iconalign" /></li>';

$sql_alter_link_published_date = $handle->query("alter table `" . table_prefix . "links` change `link_published_date` `link_published_date` timestamp NULL DEFAULT NULL;");

if (!$sql_alter_link_published_date) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Altered Table '. table_prefix . 'links` changed link_published_date to NULL DEFAULT NULL <img src="'.$marks.'" class="iconalign" /></li>';

$sql_alter_user_lastlogin = $handle->query("alter table `" . table_prefix . "users` change `user_lastlogin` `user_lastlogin` timestamp NULL DEFAULT NULL;");

if (!$sql_alter_user_lastlogin) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Altered Table '. table_prefix . 'users` changed user_lastlogin to NULL DEFAULT NULL <img src="'.$marks.'" class="iconalign" /></li>';

$sql_update_link_date = $handle->query("UPDATE `" . table_prefix . "links` set `link_published_date` = NULL WHERE `link_published_date` = '0000-00-00 00:00:00';");

if (!$sql_update_link_date) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Updated Table '. table_prefix . 'links` set link_published_date to NULL <img src="'.$marks.'" class="iconalign" /></li>';

$sql_update_user_lastlogin = $handle->query("UPDATE `" . table_prefix . "users` set `user_lastlogin` = NULL WHERE `user_lastlogin` = '0000-00-00 00:00:00';");

if (!$sql_update_user_lastlogin) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Updated Table '. table_prefix . 'users` set `user_lastlogin` to NULL <img src="'.$marks.'" class="iconalign" /></li>';

$sql_alter_tag_cache = $handle->query("ALTER TABLE  `" . table_prefix . "tag_cache` ADD PRIMARY KEY(`tag_words`);");

if (!$sql_alter_tag_cache) {
    $marks = $notok;
}else{
    $marks = $ok;
}
echo '<li>Altered '.table_prefix . 'tag_cache` table to ADD PRIMARY KEY(`tag_words`) <img src="'.$marks.'" class="iconalign" /></li>';
echo '</ul></fieldset><br />';

/* 
Get the tables size to know which one will be processed. We do this because due to the extensive altering of tables and columns and setting indexes, the server timed out when processing large tables.
So, we automatically process tables with fair sizes and the for the rest, the user has to manually execute the MySQL statements in the phpmyadmin. 
*/
$dbs = $handle->query("SELECT CONCAT(GROUP_CONCAT(table_name) , ';' ) AS statement FROM information_schema.tables WHERE table_schema = '" . EZSQL_DB_NAME. "' AND table_name LIKE  '" .table_prefix."%';");
if (!$dbs) { return -1; }
$arraytables = $dbs->fetch_array(MYSQLI_ASSOC);
		$arraytables['statement'] = str_replace(";","",$arraytables['statement']);
		$mytables = explode(",",$arraytables['statement']);
$size = 0;
$TTLsize = 0;
$not_to_include = array();
foreach ($mytables as $tname) {
    $r = $handle->query("SHOW TABLE STATUS FROM ".EZSQL_DB_NAME." LIKE '".$tname."'");
    $data = $r->fetch_array(MYSQLI_ASSOC);
    $size = ($data['Index_length'] + $data['Data_length']);
    $TTLsize += $size; //($data['Index_length'] + $data['Data_length']);
    if ($size >= 50000000) {
        $not_to_include[] = $tname;
    }
}
		
/* 
We process the tables that are NOT in the array not_to_include
*/
echo '<fieldset><legend>Converting all the Tables to utf8mb4_unicode_ci and Engine MyISAM</legend><ul>';
		foreach ($mytables as $tname) {
        if (!in_array($tname, $not_to_include)) {
            $sql_alter_tables = $handle->query("ALTER TABLE  `" . $tname . "` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
			if (!$sql_alter_tables) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			echo '<li>Converted Table '. $tname . ' TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci and ENGINE = MyISAM <img src="'.$marks.'" class="iconalign" /></li>';

            if ($tname == table_prefix."story_views") {
				$sql = "ALTER TABLE  `" . $tname . "` ADD PRIMARY KEY(`view_link_id`);";
				$sql_alter_story_views = $handle->query($sql);
				if (!$sql_alter_story_views) {
					$marks = $notok;
				}else{
					$marks = $ok;
				}
				echo '<li>Altered '.$tname . ' table to ADD PRIMARY KEY(`view_link_id`) <img src="'.$marks.'" class="iconalign" /></li>';
			}
            
            if ($tname == table_prefix."links") {
                $sql = "ALTER TABLE  `" . table_prefix."links` CHANGE  `link_url`  `link_url` VARCHAR( $urllength ) NOT NULL DEFAULT '';";
                $sql_alter_links - $handle->query($sql);
                printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
                echo '<li>Updated links Table link_url to VARCHAR '.$urllength.'</li>';
                
                $sql = "ALTER TABLE `" . table_prefix."links` DROP INDEX `link_url`, ADD INDEX `link_url` (`link_url`(50)) USING BTREE;";
				$sql_alter_key_linkurl = $handle->query($sql);
				if (!$sql_alter_key_linkurl) {
					$marks = $notok;
				}else{
					$marks = $ok;
				}
				echo '<li>Altered '.$tname . ' table modified key link_url <img src="'.$marks.'" class="iconalign" /></li>';
                
                $sql = "ALTER TABLE `" . table_prefix."links` DROP INDEX `link_title_url`, ADD INDEX `link_title_url` (`link_title_url`(50)) USING BTREE;";
				$sql_alter_key_linktitleurl = $handle->query($sql);
				if (!$sql_alter_key_linktitleurl) {
					$marks = $notok;
				}else{
					$marks = $ok;
				}
				echo '<li>Altered '.$tname . ' table modified key link_title_url <img src="'.$marks.'" class="iconalign" /></li>';
			}
            
            if ($tname == table_prefix."redirects") {
                $sql = "ALTER TABLE `" . table_prefix."redirects` DROP INDEX `redirect_old`, ADD INDEX `redirect_old` (`redirect_old`(50)) USING BTREE;";
				$sql_alter_key_redirect_old = $handle->query($sql);
				if (!$sql_alter_key_redirect_old) {
					$marks = $notok;
				}else{
					$marks = $ok;
				}
				echo '<li>Altered '.$tname . ' table modified key redirect_old <img src="'.$marks.'" class="iconalign" /></li>';
            }
            
            if ($tname == table_prefix."old_urls") {
                $sql = "ALTER TABLE `" . table_prefix."old_urls` DROP INDEX `old_title_url`, ADD INDEX `old_title_url` (`old_title_url`(50)) USING BTREE;";
				$sql_alter_key_old_title_url = $handle->query($sql);
				if (!$sql_alter_key_old_title_url) {
					$marks = $notok;
				}else{
					$marks = $ok;
				}
				echo '<li>Altered '.$tname . ' table modified key old_title_url <img src="'.$marks.'" class="iconalign" /></li>';
            }             
                
            if ($tname == table_prefix."comments") {
                $sql = "ALTER TABLE `" . table_prefix."comments` CHANGE `comment_content` `comment_content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
                $sql_alter_comments_content = $handle->query($sql);
                if (!$sql_alter_comments_content) {
                    $marks = $notok;
                }else{
                    $marks = $ok;
                }
                echo '<li>Altered '.$tname . ' table CHANGE `comment_content` type <img src="'.$marks.'" class="iconalign" /></li>';
        }
	}else{
            foreach ($not_to_include as $item) {
                $warnings_alter_table[] = "ALTER TABLE  `" . table_prefix."links` CHANGE  `link_url`  `link_url` VARCHAR( $urllength ) NOT NULL DEFAULT '';<br />";             
                
                if ($item == table_prefix."links") {
                    $warnings_alter_table[] = "ALTER TABLE  `" . $item . "` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;<br />";
                    
                    $warnings_alter_table[] = "ALTER TABLE `" . table_prefix."links` DROP INDEX `link_url`, ADD INDEX `link_url` (`link_url`(50)) USING BTREE;<br />";
                    
                    $warnings_alter_table[] = "ALTER TABLE `" . table_prefix."links` DROP INDEX `link_title_url`, ADD INDEX `link_title_url` (`link_title_url`(50)) USING BTREE;<br />";
	}

                if ($item == table_prefix."story_views") {
                    $warnings_alter_table[] = "ALTER TABLE  `" . $item . "` ADD PRIMARY KEY(`view_link_id`);<br />";
                }
                
                if ($item == table_prefix."redirects") {
                    $warnings_alter_table[] = "ALTER TABLE `" . table_prefix."redirects` DROP INDEX `redirect_old`, ADD INDEX `redirect_old` (`redirect_old`(50)) USING BTREE;<br />";
                }
	
                if ($item == table_prefix."old_urls") {
                    $warnings_alter_table[] = "ALTER TABLE `" . table_prefix."old_urls` DROP INDEX `old_title_url`, ADD INDEX `old_title_url` (`old_title_url`(50)) USING BTREE;<br />";
                }
                
                if ($item == table_prefix."comments") {
                    $warnings_alter_table[] = "ALTER TABLE `" . table_prefix."comments` CHANGE `comment_content` `comment_content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;<br />";
                }
            }
        }
    }
echo '</ul></fieldset><br />';

/*
Work on the config table
*/
echo '<fieldset><legend>MODIFICATIONS TO THE CONFIG Table.</legend><ul>';
	$sql = "INSERT INTO `" . table_prefix."config` (`var_id`, `var_page`, `var_name`, `var_value`, `var_defaultvalue`, `var_optiontext`, `var_title`, `var_desc`, `var_method`, `var_enclosein`)VALUES
            (NULL, 'Location Installed', 'allow_smtp_testing', 'false', 'false', 'true / false', 'Allow SMTP Testing?', 'If you want to test email sending on LOCALHOST, using SMTP, set it to true!', 'define', ''),
            (NULL, 'Location Installed', 'smtp_host', '', '', 'Text', 'SMTP Host', 'Enter the mail host of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
            (NULL, 'Location Installed', 'smtp_port', '587', '', 'Text', 'Port number to use.', 'Use this port number for the SMTP testing. Check the settings in cPanne -> email account.', 'define', ''),
            (NULL, 'Location Installed', 'smtp_pass', '', '', 'Text', 'Enter the domain email account password.', 'Use the password of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
            (NULL, 'Location Installed', 'smtp_fake_email', 'false', 'false', 'true / false', 'Want to use a FAKE email?', 'If you want to test the email sending with a fake email address, without actually sending it to a true email, and have the message display on the page, set it to true.<br />The recipient\'s email will be the fake email entered upon creating the account.<br /><strong style=\"color:#ff0000;\">DO NOT SET IT TO TRUE WHEN ON THE PRODUCTION SITE!</strong>', 'define', ''),
			(NULL, 'Logo', 'Default_Site_OG_Image', '', '', 'Path to the Site OpenGraph og:image', 'OpenGraph og:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site OpenGraph og:image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />Use images that are at least 1080 pixels in width for best display on high resolution devices (ratio 1.91:1) Most recommended is 1200 X 630. For square image, at the minimum you should use images that are 600 pixels in width and height (ratio 1:1).<br />Check the link <a href=\"https://developers.facebook.com/docs/sharing/best-practices/#images\" target=\"_blank\">Sharing Best Practices</a>', 'define', ''''),
			(NULL, 'Logo', 'Default_OG_Image_Width', '1200', '1200', 'Most recommended', 'OpenGraph og:image width', '<strong>IMPORTANT: ENTER THE Default WIDTH of the OpenGraph Image</strong>.<br />Example: 1200<br />', 'define', ''''),
			(NULL, 'Logo', 'Default_OG_Image_Height', '630', '630', 'Most recommended', 'OpenGraph og:image height', '<strong>IMPORTANT: ENTER THE Default HEIGHT of the OpenGraph Image</strong>.<br />Example: 600<br />', 'define', ''''),
			(NULL, 'Logo', 'Default_Site_Twitter_Image', '', '', 'Path to the Site Twitter Image', 'Twitter:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site Twitter Card Image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />You should not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported<br />Check the link <a href=\"https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image\" target=\"_blank\">Summary Card with Large Image</a>', 'define', ''''),

			(NULL, 'Misc', 'validate_password', 'true', 'true', 'true / false', 'Validate user password', 'Validate user password, when registering/password reset, to check if it is safe and not pwned?<br />If you set to true, then a check is done using HIBP API. If the provided password has been pwned, the registration is not submitted until they provide a different password!.<br /><a href=\"https://haveibeenpwned.com/\" target=\"_blank\" rel=\"noopener noreferrer\">Have I Been Pwned?</a>', 'define', ''),
			(NULL, 'Misc', 'what_is_plikli', 'true', 'true', 'true / false', 'Display What is Plikli in the sidebar?', 'Set it to false if you do not want it to display.<br /><strong>If you want it to display but with your own content, Keep it set to true and edit the language file where the entry is PLIKLI_Visual_What_Is_Plikli and PLIKLI_Visual_What_Is_Plikli_Text under the Sidebar section.<br /><a href=\"../admin/module.php?module=admin_language\" target=\"_blank\" rel=\"noopener noreferrer\">Modify Language</a>', 'normal', ''''),
            (NULL, 'Template', 'Use_New_Story_Layout', 'false', 'false', 'true / false', 'Use the new Story layout?', 'If you want to use the new Story layout, set this to true.', 'define', '');";
	$sql_new_config = $handle->query($sql);
	if (!$sql_new_config) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	$warnings[] = "Added new settings to the CONFIG Table:<ol>Under Location Installed<li>SMTP settings to test sending email.</li>Under Logo Section<li>Path to the Site OpenGraph Image</li><li>OpenGraph og:image width</li><li>OpenGraph og:image height</li><li>Path to the Site Twitter Image</li><strong>Under Miscellaneous<li>validate_password with HIBP API</li><li>Display or not What is Plikli in the sidebar.</li><li>Under Template Settings, Use the new Story layout?.</li></ol>";
	printf("Affected rows (INSERT): %d\n", $handle->affected_rows);
	echo '<li>INSERTED new settings in the CONFIG Table (read the notes at the end of the upgrade process) <img src="'.$marks.'" class="iconalign" /></li>';	
	
	// Update dblang description
	$sql = "UPDATE `" . table_prefix."config` set `var_desc` = 'Database language.<br /><strong style=\"color:#ff0000;\">DO NOT CHANGE THIS VALUE \"en\" IT WILL MESS UP THE URLS OF THE CATEGORIES!</STRONG>' WHERE `var_name` =  '\$dblang';";
	$sql_dbland = $handle->query($sql);
	if (!$sql_dbland) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
	echo '<li>Updated dblang description <img src="'.$marks.'" class="iconalign" /></li>';
    
    // Update Draft description
	$sql = "UPDATE `" . table_prefix."config` set `var_desc` = 'Set it to true to allow users to save draft articles.<br /><strong>For this feature to work, you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>' WHERE `var_name` =  'Allow_Draft';";
	$sql_draft = $handle->query($sql);
	if (!$sql_draft) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
	echo '<li>Updated Allow Draft description <img src="'.$marks.'" class="iconalign" /></li>';
    
    // Update Scheduled description
	$sql = "UPDATE `" . table_prefix."config` set `var_desc` = 'Set it to true to allow users to save scheduled articles.<br /><strong>If you set to true, then you MUST install the <u>scheduled_posts</u> Module. AND you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>' WHERE `var_name` =  'Allow_Scheduled';";
	$sql_scheduled = $handle->query($sql);
	if (!$sql_scheduled) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
	echo '<li>Updated Allow Scheduled description <img src="'.$marks.'" class="iconalign" /></li>';

	// Update Complete submission on Submit Step 2? desc.
	$sql = "UPDATE `" . table_prefix."config` SET `var_desc` ='If set to false, the user will be presented with a third step where they can preview and submit the story. <br /><strong>HOWEVER, IF YOU HAVE ALLOW DRAFT AND ALLOW SCHEDULED FEATURES SET TO TRUE, THEY WON\'T WORK!</strong>' where `var_name` = 'Submit_Complete_Step2';";
	$sql_submit_step2 = $handle->query($sql);
	if (!sql_submit_step2) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
	echo '<li>Updated description of "Complete submission on Submit Step 2" <img src="'.$marks.'" class="iconalign" /></li>';

echo '</ul></fieldset><br />';

echo '<fieldset><legend>modifying user_password column in Users table.</legend><ul>';
	$sql = "ALTER TABLE  `" . table_prefix."users`  
	CHANGE COLUMN `user_pass` `user_pass` VARCHAR(80) NOT NULL DEFAULT '';";
	$sql_alter_user_password - $handle->query($sql);
	printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
	echo '<li>Updated user_password column in Users table to VARCHAR(80)</li>';
echo '</ul></fieldset><br />';

/* 
start work on misc_data table
*/
echo '<fieldset><legend>Updating the misc_data table. If an entry needs updating it is marked <img src="ok.png" class="iconalign" />; else, it is marked <img src="notok.png" class="iconalign" /></legend><ul>';
	$sql = "select * from `" . table_prefix."misc_data` where name like '%adcopy%'";
	$sql_adcopy = $handle->query($sql);
	if ($sql_adcopy) {
		$row_cnt = $sql_adcopy->num_rows;
		if ($row_cnt) {
			while ($adcopy = $sql_adcopy->fetch_assoc()) {
				if (in_array('adcopy_lang',$adcopy)) {
					$sql_adcopy_lang = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'en' WHERE `name` = 'adcopy_lang';");
					if ($handle->affected_rows < 1) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					echo '<li>'.printf("Affected rows (UPDATED 'adcopy_lang'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('adcopy_theme',$adcopy)) {
					$sql_adcopy_theme = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'white' WHERE `name` = 'adcopy_theme';");
					if ($handle->affected_rows < 1) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					echo '<li>'.printf("Affected rows (UPDATED 'adcopy_theme'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('adcopy_pubkey',$adcopy)) {
					$sql_adcopy_pubkey = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'Rp827COlEH2Zcc2ZHrXdPloU6iApn89K' WHERE `name` = 'adcopy_pubkey';");
					if ($handle->affected_rows < 1) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					echo '<li>'.printf("Affected rows (UPDATED 'adcopy_pubkey'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('adcopy_privkey',$adcopy)) {
					$sql_adcopy_privkey = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = '7lH2UFtscdc2Rb7z3NrT8HlDIzcWD.N1' WHERE `name` = 'adcopy_privkey';");
					if ($handle->affected_rows < 1) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					echo '<li>'.printf("Affected rows (UPDATED 'adcopy_privkey'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('adcopy_hashkey',$adcopy)) {
					$sql_adcopy_hashkey = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'rWwIbi8Nd6rX-NYvuB6sQUJV6ihYHa74' WHERE `name` = 'adcopy_hashkey';");
					if ($handle->affected_rows < 1) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					echo '<li>'.printf("Affected rows (UPDATED 'adcopy_hashkey'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
				}
			}
		}else{
			$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
					VALUES ('adcopy_lang', 'en'),
					('adcopy_theme', 'white'),
					('adcopy_pubkey', 'Rp827COlEH2Zcc2ZHrXdPloU6iApn89K'),
					('adcopy_privkey', '7lH2UFtscdc2Rb7z3NrT8HlDIzcWD.N1'),
					('adcopy_hashkey', 'rWwIbi8Nd6rX-NYvuB6sQUJV6ihYHa74');";
			$sql_captcha_data = $handle->query($sql);
			if ($handle->affected_rows < 1) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			echo '<li>'.printf("Affected rows (INSERTED adcopy data values): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';		
		}
	}
	$sql = "select * from `" . table_prefix."misc_data` where name like 'captcha%'";
	$sql_captcha = $handle->query($sql);
	$toCheck = array('captcha_method','captcha_reg_en','captcha_comment_en','captcha_story_en');
	if ($sql_captcha) {
		$row_cnt_captcha = $sql_captcha->num_rows;
		if ($row_cnt_captcha) {
			while ($captcha = $sql_captcha->fetch_assoc()) {
				$all_captcha[] = $captcha['name'];
			}

			$cap_imploded = implode(",",$all_captcha);
			// find the matches in the arrays
			$matches = array_intersect($toCheck, $all_captcha);
			// find the differences in the arrays
			$diff = array_diff($toCheck, $all_captcha);
			if ($matches) {
				foreach($matches as $method) {
					if ($method == 'captcha_method') {
						$sql_captcha_method = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'solvemedia' where `name` = 'captcha_method';");
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (UPDATED 'captcha_method'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';	
					}elseif ($method == 'captcha_reg_en') {
						$sql_captcha_reg_en = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'true' where `name` = 'captcha_reg_en';");
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (UPDATED 'captcha_reg_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}elseif ($method == 'captcha_comment_en') {
						$sql_captcha_comment_en = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'true' where `name` = 'captcha_comment_en';");
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (UPDATED 'captcha_comment_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}elseif ($method == 'captcha_story_en') {
						$sql_captcha_story_en = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'true' where `name` = 'captcha_story_en';");
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (UPDATED 'captcha_story_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}
				}
			}
			if ($diff) {
				foreach($diff as $difference) {
					if ($difference == 'captcha_method') {
						$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
								VALUES ('captcha_method', 'solvemedia');";
						$sql_captcha_method = $handle->query($sql);
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (INSERTED 'solvemedia'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}elseif ($difference == 'captcha_reg_en') {
						$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
								VALUES ('captcha_reg_en', 'true');";
						$sql_captcha_reg_en = $handle->query($sql);
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (INSERTED 'captcha_reg_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}elseif ($difference == 'captcha_comment_en') {
						$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
								VALUES ('captcha_comment_en', 'true');";
						$sql_captcha_comment_en = $handle->query($sql);
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (INSERTED 'captcha_comment_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}elseif ($difference == 'captcha_story_en') {
						$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
								VALUES ('captcha_story_en', 'true');";
						$sql_captcha_story_en = $handle->query($sql);
						if ($handle->affected_rows < 1) {
							$marks = $notok;
						}else{
							$marks = $ok;
						}
						echo '<li>'.printf("Affected rows (INSERTED 'captcha_story_en'): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
					}
				}
			}
		}else{
			$sql = "INSERT INTO  `" . table_prefix."misc_data` ( `name` , `data` )
					VALUES 
						('captcha_method', 'solvemedia'),
						('captcha_comment_en', 'true'),
						('captcha_reg_en', 'true'),
						('captcha_story_en', 'true');";
			$sql_captcha_data = $handle->query($sql);
			if ($handle->affected_rows < 1) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			echo '<li>'.printf("Affected rows (INSERTED data values): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';		
		}
	}
	
	// Delete all reCaptcha entries
	$sql = "DELETE FROM `" . table_prefix."misc_data` WHERE `name` like 'reCaptcha_%';";
	$sql_delete_recaptcha_entries = $handle->query($sql);
	if ($handle->affected_rows < 1) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	echo '<li>'.printf("Affected rows (DELETED reCaptcha data): %d\n", $handle->affected_rows).' <img src="'.$marks.'" class="iconalign" /></li>';
echo '</ul></fieldset><br />';	

/* 
Renaming some values in the misc_data table to work with Plikli
*/
echo '<fieldset><legend>Renaming some values in the misc_data table to work with Plikli.</legend><ul>';	
		// Update CMS version.
			$sql = "UPDATE `" . table_prefix."misc_data` SET `data` = '". $lang['plikli_version'] ."'  WHERE `name` = 'plikli_version';";
			$sql_CMS_name = $handle->query($sql);
			if (!$sql_CMS_name) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Updated name to plikli_version and version to '. $lang['plikli_version'] .' <img src="'.$marks.'" class="iconalign" /></li>';
			
	// updating and installing Plikli and module updates
	$sql = "select * from `" . table_prefix."misc_data` where `name` like 'modules_%';";
	$sql_modules_update = $handle->query($sql);
	if ($sql_modules_update) {
		$row_cnt = $sql_modules_update->num_rows;
		if ($row_cnt) {
			while ($cms_modules_update = $sql_modules_update->fetch_assoc()) {
				if (in_array('modules_update_date',$cms_modules_update)) {
					$sql_modules_update_date = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` =  DATE_FORMAT(NOW(),'%Y/%m/%d')  WHERE `name` = 'modules_update_date';");
					if (!$sql_modules_update_date) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
					echo '<li>updated modules_update_date <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('modules_update_url',$cms_modules_update)) {
					$sql_modules_update_url = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = 'https://www.plikli.com/mods/version-update.txt' WHERE `name` = 'modules_update_url';");
					if (!$sql_modules_update_url) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
					echo '<li>updated modules_update_url <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('modules_update_unins',$cms_modules_update)) {
					$sql_modules_update_unins = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = '' WHERE `name` = 'modules_update_unins';");
					if (!$sql_modules_update_unins) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
					echo '<li>updated modules_update_unins <img src="'.$marks.'" class="iconalign" /></li>';
				}elseif (in_array('modules_upd_versions',$cms_modules_update)) {
					$sql_modules_upd_versions = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = '' WHERE `name` = 'modules_upd_versions';");
					if (!$sql_modules_upd_versions) {
						$marks = $notok;
					}else{
						$marks = $ok;
					}
					printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
					echo '<li>updated modules_upd_versions <img src="'.$marks.'" class="iconalign" /></li>';
				}
			}
		}
	}

	//inserting the update url for PLIKLI
	$sql = "INSERT INTO `" . table_prefix."misc_data` ( `name` , `data` ) VALUES 
	('plikli_update_url','https://www.plikli.com/download_plikli/');";
	$sql_insert_updateUrl = $handle->query($sql);
	if (!$sql_insert_updateUrl) {
		$marks = $notok;
	}else{
		$marks = $ok;
	}
	printf("Affected rows (INSERT): %d\n", $handle->affected_rows);
	echo '<li>Inserted the update URL for Plikli CMS <img src="'.$marks.'" class="iconalign" /></li>';
echo '</ul></fieldset><br />';

/* 
Redwine: checking if we have to detect certain settings and modules or not, to give further instructions.
*/
//get the CMS folder name
$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = '\$my_plikli_base';";
$sql_get_base_folder = $handle->query($sql);
$fetched = $sql_get_base_folder->fetch_array(MYSQLI_ASSOC);	
$folder_path = $fetched['var_value'];

	echo '<fieldset><legend>Checking the installed modules and certain config settings!</legend><ul>';
		$sql = "select `name`,`folder` from `" . table_prefix."modules`";
		$sql_modules = $handle->query($sql);

	$filename = 'version-update.txt';
	$lines = file($filename, FILE_IGNORE_NEW_LINES);
	$modules_array = array();
	foreach($lines as $line) {
		$modules_array[] = explode(',', $line);
	}
	
	$sql_modules->data_seek(0);
	while ($module = $sql_modules->fetch_assoc()) {
		foreach($modules_array as $modules) {
			if ($module['folder'] == $modules[0]) {
				$sql = "UPDATE `" . table_prefix."modules` SET `version` = '". $modules[1] ."', `latest_version` = '". $modules[1] ."' WHERE `folder` = '" .$module['folder'] ."';";
				$sql_update = $handle->query($sql);
				if (!$sql_update) {
					$marks = $notok;
				}else{
					$marks = $ok;
					printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
					echo '<li>Updated '.$module['name'] . ' module Version <img src="'.$marks.'" class="iconalign" /></li>';
				}
			}	
		}	

		if ($module['folder'] == "links") {
			$warnings[] = "Check the Links module because we added few settings to it <strong style=\"text-decoration:underline;background-color:#0100ff\">YOU HAVE TO GO TO ITS SETTINGS AND SELECT THE NEW OPTIONS THAT YOU WANT; OTHERWISE IT WILL NOT WORK UNTIL YOU DO SO!</strong>!";
		}		
		if ($module['folder'] == "upload") {
			$warnings[] = "We noticed you have the UPLOAD module installed. You have to copy the files from the old Plikli folder, in ".$folder_path."/modules/upload/attachments/ TO the same folder in the new Plikli /".$upgrade_folder."/modules/upload/attachments/.";
			/*Redwine: correcting the default upload fileplace!*/
			$sql_upload_fileplace = "select `data` from `" . table_prefix."misc_data` WHERE `name` = 'upload_fileplace'";
			$sql_fileplace = mysqli_fetch_assoc($handle->query($sql_upload_fileplace));
			if ($sql_fileplace['data'] == 'tpl_plikli_story_who_voted_start') {
				$sql_upload_fileplace_correct = $handle->query("UPDATE `" . table_prefix."misc_data` set `data` = 'upload_story_list_custom' WHERE `name` = 'upload_fileplace'");
				printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
				echo "<li>We corrected the upload_fileplace default to 'upload_story_list_custom'.</li>";
				$warnings[] = "We corrected the upload_fileplace default to 'upload_story_list_custom'.";
			}
		}
		if ($module['folder'] == "admin_snippet") {
			$sql = "ALTER TABLE `" . table_prefix."snippets` ADD `snippet_status` int(1) NOT NULL DEFAULT '1';";
			$sql_add_status = $handle->query($sql);
			if (!$sql_add_status) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Altered `'.table_prefix.'snippets` table to add a status column <img src="'.$marks.'" class="iconalign" /></li>';
			$warnings[] = "Added a Status column to allow Admins to activate/deactivate snippets!</strong>!";
			$sql = "UPDATE `" . table_prefix."snippets` SET `snippet_location` = REPLACE(`snippet_location`, 'kliqqi', 'plikli');";
			$sql_update_snippet_location = $handle->query($sql);
			if (!$sql_update_snippet_location) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Altered `'.table_prefix.'snippets` replaced "kliqqi" with "plikli"in the snippet location <img src="'.$marks.'" class="iconalign" /></li>';
			$sql = "UPDATE `" . table_prefix."snippets` SET `snippet_content` = REPLACE(`snippet_content`, 'KLIQQI', 'PLIKLI');";
			$sql_update_snippet_content = $handle->query($sql);
			if (!$sql_update_snippet_content) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Altered `'.table_prefix.'snippets` replaced "KLIQQI" with "PLIKLI" in the snippet content <img src="'.$marks.'" class="iconalign" /></li>';
			$sql = "UPDATE `" . table_prefix."snippets` SET `snippet_content` = REPLACE(`snippet_content`, 'Kliqqi', 'Plikli');";
			$sql_update_snippet_content = $handle->query($sql);
			if (!$sql_update_snippet_content) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Altered `'.table_prefix.'snippets` replaced "Kliqqi" with "Plikli" in the snippet content <img src="'.$marks.'" class="iconalign" /></li>';
			$sql = "UPDATE `" . table_prefix."snippets` SET `snippet_content` = REPLACE(`snippet_content`, 'kliqqi', 'plikli');";
			$sql_update_snippet_content = $handle->query($sql);
			if (!$sql_update_snippet_content) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Altered `'.table_prefix.'snippets` replaced "kliqqi" with "plikli" in the snippet content <img src="'.$marks.'" class="iconalign" /></li>';
		}
		if ($module['folder'] == 'anonymous') {
			$sql = "UPDATE `" . table_prefix."users` SET `user_email` = 'anonymous@plikli.com' WHERE `user_login` = 'anonymous';";
			$sql_update_email = $handle->query($sql);
			if (!$sql_update_email) {
				$marks = $notok;
			}else{
				$marks = $ok;
				echo '<li>Updated '.$module['folder'] . ' module Version <img src="'.$marks.'" class="iconalign" /></li>';
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Updated email in the Users table for '.$module['folder'] . ' user <img src="'.$marks.'" class="iconalign" /></li>';
		}
		if ($module['folder'] == 'rss_import') {
			$sql = "ALTER TABLE `" . table_prefix."feed_link` CHANGE `kliqqi_field` `plikli_field` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;";
			$sql_alter_feed_link = $handle->query($sql);
			if (!$sql_alter_feed_link) {
				$marks = $notok;
			}else{
				$marks = $ok;
				echo '<li>Altered '.$module['folder'] . ' feed_link Table <img src="'.$marks.'" class="iconalign" /></li>';
			}
			printf("Affected rows (ALTERED): %d\n", $handle->affected_rows);
		}
		if ($module['folder'] == 'xml_sitemaps') {
			$sql = "UPDATE `" . table_prefix."config` SET `var_name` = 'XmlSitemaps_Links_navigation' WHERE `var_title` = 'Sitemap links page';";
			$sql_sitepmap_nav = $handle->query($sql);
			if (!$sql_sitepmap_nav) {
				$marks = $notok;
			}else{
				$marks = $ok;
			}
			printf("Affected rows (UPDATE): %d\n", $handle->affected_rows);
			echo '<li>Updated variable name for the sitemap navigation for '.$module['folder'] . ' <img src="'.$marks.'" class="iconalign" /></li>';
		}
	}
	echo '</ul></fieldset><br />';	

/* 
Checking some settings to determine if further manual action is required.
*/
	echo '<fieldset><legend>Checking if Allow users to change language is set to 1 and if validate user email is set to true in your config table</legend><ul>';
		$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = 'user_language';";
		$sql_get_user_language = $handle->query($sql);
		$result = $sql_get_user_language->fetch_array(MYSQLI_ASSOC);
		$allowed_user_languages = '';
		$renamed_allowed_user_languages_files = '';
		if ($result['var_value'] == '1') {
			$sql = "SELECT `user_language` FROM `" . table_prefix."users` WHERE `user_language` != '';";
			$sql_used_user_language = $handle->query($sql);
			if ($sql_used_user_language) {
				$row_used_user_language = $sql_used_user_language->num_rows;
				if ($row_used_user_language > 0) {
				while ($used_language = $sql_used_user_language->fetch_assoc()) {
					$allowed_user_languages .= $used_language['user_language'] . "<br />";
					$file_rename = str_replace(".default", "", "lang_".$used_language['user_language'].".conf.default");
					rename("../languages/lang_".$used_language['user_language'].".conf.default", "../languages/$file_rename");
					chmod("../languages/$file_rename", 0777);
					echo "../languages/$file_rename<br />";
					$renamed_allowed_user_languages_files .= "../languages/$file_rename<br />";
				}
				echo $allowed_user_languages;
					echo '<li>Allow users to change language is set to "'.$result['var_value']. '" in your config table, and detected that the following languages are allowed for users:<br />'.$allowed_user_languages.' We renamed them for you! See Warnings at the end of the upgrade!</li>';
				$warnings[] = 'Allow users to change language is set to "'.$result['var_value']. '" in your config table, and detected that the following languages are allowed for users:<br />'.$allowed_user_languages.' We renamed the language files for you from .default to .conf <br />'.$renamed_allowed_user_languages_files;
				}else{
					echo '<li>Allow users to change language is set to "'.$result['var_value']. '" in your config table, and no renamed language files were detected!</li>';
					$warnings[] = 'Allow users to change language is set to "'.$result['var_value']. '" in your config table, and no renamed language files were detected!';
				}
			}
		}else{
			echo '<li>Allow users to change language is set to default 0. No action is required</li>';
			$warnings[] = 'Allow users to change language is set to "'.$result['var_value']. '". No action is required';
		}
		
			// if validate user email is false or true.
		$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = 'misc_validate';";
		$sql_get_misc_validate = $handle->query($sql);
		$result = $sql_get_misc_validate->fetch_array(MYSQLI_ASSOC);
		if (trim($result['var_value']) == 'true') {
			echo '<li>Require users to validate their email address is set to default "'. $result['var_value']. '" in your config table. See Warnings at the end of the upgrade!</li>';
			$warnings[] = 'Require users to validate their email address is set to "' .trim($result['var_value']). '" in your config table. You must enter the email you are using for your site in the language file in /languages/ and enter it as the value for PLIKLI_PassEmail_From';
		}else{
			echo '<li>Require users to validate their email address is set to default "'. $result['var_value']. '" No action is required</li>';
		}
	echo '</ul></fieldset><br />';

	echo '<br /><fieldset><legend>Updating the Site Title in all the language files to "'. $_SESSION['sitetitle'] . '"</legend><ul>';
		$replacement = 'PLIKLI_Visual_Name = "'.strip_tags($_SESSION['sitetitle']).'"';
		if (strip_tags($_SESSION['sitetitle']) != '') {
			foreach (glob("../languages/*.{conf,default}", GLOB_BRACE) as $filename) {
				$filedata = file_get_contents($filename);
				$filedata = preg_replace('/PLIKLI_Visual_Name = \"(.*)\"/iu',$replacement,$filedata);
				// print $filedata;
				
				// Write the changes to the language files
				$lang_file = fopen($filename, "w");
				fwrite($lang_file, $filedata);
				fclose($lang_file);
				echo '<li>' . $filename . '</li>';
			}
		}else{
			echo 'You did not enter a new Visual Name for the site, so the current one will remain unchanged!';
		}
	echo '</ul></fieldset><br />';
		
	echo '<fieldset><legend>Checking SEO Method and links extra fields</legend><ul>';
		// Checking if SEO method 2 is used.
		$sql = "SELECT `var_name`,`var_value` FROM `". table_prefix . "config` WHERE `var_name` = '\$URLMethod' or `var_name` = 'Enable_Extra_Fields';";
		$sql_check_seo = $handle->query($sql);
		if ($sql_check_seo) {
			$row_cnt = $sql_check_seo->num_rows;
			if ($row_cnt > 0) {
				while ($seoMethod = $sql_check_seo->fetch_assoc()) {
					if ($seoMethod['var_name'] == '$URLMethod' && $seoMethod['var_value'] == 2) {
						echo 'We detected that SEO Method 2 is used. SEE ADDITIONAL INSTRUCTIONS AT THE END OF THE UPGRADE PROCESS!<br />';
						$warnings[] = 'We detected that SEO Method 2 is used. You must EDIT (DO NOT COPY OVER THE OLD ONE) AND RENAME <strong>htaccess.default to .htaccess</strong><br />if you are using Windows, you can rename the file by opening the command line to the root of this folder and type the following and press enter:<br /><strong>rename htaccess.default .htaccess</strong>';
					}elseif ($seoMethod['var_name'] == '$URLMethod' && $seoMethod['var_value'] == 1) {
						echo 'You are using SEO method '.$seoMethod['var_value'].'. No need to rename the htaccess default and edit it!<br />';
						$warnings[] = 'you are using SEO method '.$seoMethod['var_value'].'. No need to rename the htaccess default and edit it!'; 
					}
					// Checking if extra fields are used in links table
						$sql = "SELECT `link_id` FROM `" . table_prefix . "links` WHERE 
						`link_field1` != '' OR 
						`link_field2` != '' OR 
						`link_field3` != '' OR 
						`link_field4` != '' OR 
						`link_field5` != '' OR 
						`link_field6` != '' OR 
						`link_field7` != '' OR 
						`link_field8` != '' OR 
						`link_field9` != '' OR 
						`link_field10` != '' OR
						`link_field11` != '' OR
						`link_field12` != '' OR
						`link_field13` != '' OR
						`link_field14` != '' OR
						`link_field15` != '';";

						$sql_check_extra_fields = $handle->query($sql);
						if ($sql_check_extra_fields) {
							$row_cnt_extra_fields = $sql_check_extra_fields->num_rows;
						}
					if ($seoMethod['var_name'] == 'Enable_Extra_Fields' && trim($seoMethod['var_value']) == 'true') {
						if ($row_cnt_extra_fields > 0) {
							echo 'We detected that Enable Extra Fields is set to true and one or more extra fields in links table are used. SEE ADDITIONAL INSTRUCTIONS AT THE END OF THE UPGRADE PROCESS!<br />';
							$warnings[] = 'We detected that Enable Extra Fields is set to true and one or more extra fields in links table are used. YOU MUST COPY THE <strong><em>'.$folder_path.'/LIBS/EXTRA_FIELDS.PHP</em></strong> FILE FROM YOUR OLD CMS TO <strong><em>/'.$upgrade_folder.'/LIBS/ FOLDER</em></strong>.';
						}else{
							echo 'We detected that Enable Extra Fields is set to true but Extra fields in links table are not used. No action is required! SEE ADDITIONAL INSTRUCTIONS AT THE END OF THE UPGRADE PROCESS!<br />';
							$warnings[] = 'We detected that Enable Extra Fields is set to true but Extra fields in links table are not used. No action is required!<br />Should you decide to use the extra fields, you must edit the following files:<br /><strong><em>/libs/extra_fields.php <u>using the Extra Fields Editor in the Dashboard</u></em></strong>';
						}

					}elseif ($seoMethod['var_name'] == 'Enable_Extra_Fields' && trim($seoMethod['var_value']) == 'false') {
						if ($row_cnt_extra_fields > 0) {
							echo 'We detected that Enable Extra Fields is set to false and one or more extra fields in links table are used. SEE ADDITIONAL INSTRUCTIONS AT THE END OF THE UPGRADE PROCESS!<br />';
							$warnings[] = 'We detected that Enable Extra Fields is set to false and one or more extra fields in links table are used. Should you decide to use the extra fields, you must set Enable Extra Fields to true and use the Extra Fields Editor in the Dashboard to edit extra_fields.php if needed!';
						}else{
							echo 'Extra fields in links table are not used. No action is required!<br />';
							$warnings[] = 'Extra fields in links table are not used. No action is required!';
						}
					}
				}
			}
		}
	echo '</ul></fieldset><br />';
	
	echo '<fieldset><legend>Renaming the original folder containing the old Kliqqi files</legend><ul>';
			$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = '\$my_plikli_base';";
			$sql_get_base_folder = $handle->query($sql);
			$result = $sql_get_base_folder->fetch_array(MYSQLI_ASSOC);
			$result['var_value'] = substr($result['var_value'], 1, strlen($result['var_value']));
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
			$success = rename($_SERVER['DOCUMENT_ROOT'].$result['var_value'],$_SERVER['DOCUMENT_ROOT'].$result['var_value'] . "-original");
			if (!$success) {
				$marks = $notok;
				echo '<li class="alert-danger">FAILED to rename the folder ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . ' To ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . '-original. SEE ADDITIONAL INSTRUCTIONS AT THE END OF THE UPGRADE PROCESS! <img src="'.$marks.'" class="iconalign" /></li>';
				$warnings_rename[] = 'FAILED to rename the folder ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . ' To ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . '-original <br />The browser or any other application is using one of its files!<br />You have to manually rename it as indicated in the beginning of the warning!';
			}else{
				$marks = $ok;
				echo '<li>RENAMED ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . ' to ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . '-original <img src="'.$marks.'" class="iconalign" /></li>';
				$warnings_rename[] = '<span class="warn-delete">RENAMED ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . ' to ' . $_SERVER['DOCUMENT_ROOT'].$result['var_value'] . '-original</span>';
			}
		// getting the root folder of the current CMS (the new Plikli) to rename it as it is in the config table under $my_plikli_base
		$arr = explode("/", $_SERVER['SCRIPT_NAME']);
		$first = $arr[1];
		$path = $_SERVER["DOCUMENT_ROOT"] . $first;
		echo "<br />";
		$warnings_rename[] = "you have to manually rename the current folder from:<br />". $path . " to " . $_SERVER["DOCUMENT_ROOT"] . $result['var_value'];
	}else{
		$warnings_rename[] = 'YOU HAVE to rename the folder ' . $result['var_value'] . ' To ' . $result['var_value'] . '-original!';
		// getting the root folder of the current CMS (the new Plikli) to rename it as it is in the config table under $my_plikli_base
		$arr = explode("/", $_SERVER['SCRIPT_NAME']);
		$first = $arr[1];
		$path = $_SERVER["DOCUMENT_ROOT"] . "/$first";
		echo "<br />";
		$warnings_rename[] = "you have to manually rename the current folder from:<br />". $path . " to " . $_SERVER["DOCUMENT_ROOT"] . "/".$result['var_value'];
	}
	echo '</ul></fieldset><br />';
	
	//check the CMS version & name 
	echo '<fieldset><legend>Checking and re-ordering the misc_data table!</legend><ul>';
	$sql = "select * from `" . table_prefix."misc_data` where `name` = 'plikli_version';";
	$sql_CMS_version = $handle->query($sql);
	if ($sql_CMS_version) {
		$row_cnt = $sql_CMS_version->num_rows;
		if ($row_cnt) {
			while ($cms_name_version = $sql_CMS_version->fetch_assoc()) {
				//var_dump($cms_name_version);
				echo '<li>CMS name "'. $cms_name_version['name'] .'" and CMS version is "'. $cms_name_version['data'] .'"</li>'; 
			}
		}else{
		$sql_cms_insert = $handle->query("INSERT INTO `" . table_prefix."misc_data` SET `name` = 'plikli_version', `data` = '". $lang['plikli_version'] ."';");
		printf("Affected rows (INSERT): %d\n", $handle->affected_rows);
		echo '<li>inserted CMS_version to "'. $lang['plikli_version'] .'"</li>';
		}
	}	
	
	//empty the data of plikli_update if it contains any version from the old kliqqi
	$sql = "select * from `" . table_prefix."misc_data` where `name` = 'plikli_update';";
	$sql_plikli_update = $handle->query($sql);
	if ($sql_plikli_update) {
		$row_cnt = $sql_plikli_update->num_rows;
		if ($row_cnt) {
			while ($cms_plikli_update = $sql_plikli_update->fetch_assoc()) {
				echo '<li>Plikli Update "'. $cms_plikli_update['name'] .'" and Data "'. $cms_plikli_update['data'] .'"</li>'; 
				$sql_plikli_update_insert = $handle->query("UPDATE `" . table_prefix."misc_data` SET `data` = '' WHERE `name` = 'plikli_update';");
				printf("Affected rows (UPDATE PLIKLI UPDATE DATA): %d\n", $handle->affected_rows);
			}
		}else{
		$sql_plikli_update_insert = $handle->query("INSERT INTO `" . table_prefix."misc_data` SET `name` = 'plikli_update', `data` = '';");
		printf("Affected rows (UPDATE PLIKLI UPDATE DATA): %d\n", $handle->affected_rows);
		echo '<li>Updated PLIKLI UPDATE DATA</li>';
		}
	}
	
	//reorder the misc_data table to its original sort order
	include('reorder-misc-table.php');
	//END reorder the misc_data table to its original sort order
	
	//reorder config table
	include('reorder-config-table.php');
	//END reorder config table
	
	// if check_spam is true.
	$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = 'CHECK_SPAM';";
	$sql_get_check_spam = $handle->query($sql);
	$result = $sql_get_check_spam->fetch_array(MYSQLI_ASSOC);
	if (trim($result['var_value']) == 'true') {
		echo '. You are using CHECK_SPAM feature. Files must be copied from your old CMS '.$folder_path.'/logs/  TO /'.$upgrade_folder.'/logs/. See Warnings at the end of the upgrade!';
		$warnings[] = 'CHECK_SPAM is set to "' .trim($result['var_value']). '" in your config table. You must copy the files from your old CMS '.$folder_path.'/logs/ TO the /logs/ directory of the new Plikli, from where you are running the upgrade:<br />'.$folder_path.'/logs/antispam.log TO /'.$upgrade_folder.'/logs/antispam.log<br />'.$folder_path.'/logs/approvedips.log TO /'.$upgrade_folder.'/logs/approvedips.log<br />'.$folder_path.'/logs/bannedips.log TO /'.$upgrade_folder.'/logs/bannedips.log<br />'.$folder_path.'/logs/domain-blacklist.log TO /'.$upgrade_folder.'/logs/domain-blacklist.log<br />'.$folder_path.'/logs/domain-whitelist.log TO /'.$upgrade_folder.'/logs/domain-whitelist.log';
	}else{
		echo '<br />Check Spam is set to default "'. $result['var_value']. '" No action is required';
	}
	
	//check for avatars uploads
	$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = 'allow_groups_avatar';";
	$sql_get_allow_groups_avatars = $handle->query($sql);
	$result = $sql_get_allow_groups_avatars->fetch_array(MYSQLI_ASSOC);
	$sql = mysqli_query($handle, "SELECT COUNT(`group_avatar`) as UPLOADED FROM `" . table_prefix."groups` where `group_avatar` != '';");
	$uploaded_groups = 0;
		while ($row = $sql->fetch_assoc()) {
			$uploaded_groups = $row['UPLOADED'];
		}
	if ($result) {
		if (trim($result['var_value']) == 'true' && $uploaded_groups > 0) {
			$warnings[] = 'Allow Groups to upload own avatar is set to "'.$result['var_value']. '" in your config table, and some groups have uploaded their own avatar. You must copy the avatars from your old CMS '.$folder_path.'/avatars/groups_uploaded TO /'.$upgrade_folder.'/avatars/groups_uploaded';
		}elseif (trim($result['var_value']) == 'true' && $uploaded_groups == 0) {
			$warnings[] = 'Allow Groups to upload own avatar is set to "'.$result['var_value']. '" in your config table, but no groups have already uploaded their own avatars. NO action is required!';
		}elseif (trim($result['var_value']) == 'false' && $uploaded_groups > 0) {
			$warnings[] = 'Allow Groups to upload own avatar is set to "'.$result['var_value']. '" in your config table, but some groups have already uploaded their own avatars. Just in case you allow Groups to upload own avatar in the future, you must copy the avatars from your old CMS /avatars/groups_uploaded to the the same location in this CMS!';
		}
	}

	$sql = "SELECT `var_value` FROM `" . table_prefix."config` WHERE `var_name` = 'Enable_User_Upload_Avatar';";
	$sql_get_allow_users_avatars = $handle->query($sql);
	$result = $sql_get_allow_users_avatars->fetch_array(MYSQLI_ASSOC);
	$sql = mysqli_query($handle, "SELECT COUNT(`user_avatar_source`) as UPLOADED FROM `" . table_prefix."users` where `user_avatar_source` != '';");
	$uploaded_users = 0;
		while ($row = $sql->fetch_assoc()) {
			$uploaded_users = $row['UPLOADED'];
		}
	if ($result) {
		if (trim($result['var_value']) == 'true' && $uploaded_users > 0) {
			$warnings[] = 'Allow User to Upload Avatars is set to "'.$result['var_value']. '" in your config table, and some users have uploaded their own avatar. You must copy the avatars from your old CMS '.$folder_path.'/avatars/user_uploaded TO /'.$upgrade_folder.'/avatars/user_uploaded';
		}elseif (trim($result['var_value']) == 'true' && $uploaded_users = 0) {
			$warnings[] = 'Allow User to Upload Avatars is set to "'.$result['var_value']. '" in your config table, but no users have already uploaded their own avatars. NO action is required!';
		}elseif (trim($result['var_value']) == 'false' && $uploaded_users > 0) {
			$warnings[] = 'Allow User to Upload Avatars is set to "'.$result['var_value']. '" in your config table, but some userss have already uploaded their own avatars. Just in case you allow Users to upload own avatar in the future, you must copy the avatars from your old CMS '.$folder_path.'/avatars/user_uploaded TO /'.$upgrade_folder.'/avatars/user_uploaded';
		}
	}
	//End check for avatars uploads
	echo '</ul></div></fieldset><br />';
echo '<fieldset><legend>Additional Instructions to follow!</legend><div class="alert alert-danger"><ul>';
	echo '<li><span style="background-color:#ffffff;color:#000000;font-weight:bold;">The upgrade process was successful. PLEASE PAY SPECIAL ATTENTION THE ADDITIONAL INSTRUCTIONS BELOW!</span></li>';
	$output = '';
	if ($warnings) {
		foreach ($warnings as $warning) {
			$output.="<li>$warning</li><br />";
		}
		echo $output;
	}
echo '</ul></div></fieldset><br />';

echo '<fieldset><legend>Additional Instructions to follow!</legend><div class="alert alert-danger"><ul>';
	echo '<li><span style="background-color:#ffffff;color:#000000;font-weight:bold;">The upgrade process was successful. PLEASE PAY SPECIAL ATTENTION THE ADDITIONAL INSTRUCTIONS BELOW!</span></li><br />';
    
    $output = '';
	if ($warnings_alter_table) {
		foreach ($warnings_alter_table as $warning_alter) {
			$output.="<li>$warning_alter</li><br />";
		}
		echo '<div style="padding:5px;background-color: #e69f0d;font-weight:bold;">YOU HAVE TO MANUALLY RUN THE FOLLWOING MYSQL STATEMENTS IN THE PHPMYADMIN<br />'.$output.'</div>';
	}
    
	$output = '';
	if ($warnings) {
		foreach ($warnings as $warning) {
			$output.="<li>$warning</li><br />";
		}
		echo $output;
	}
echo '</ul></div></fieldset><br />';

echo '<fieldset><legend>Renaming Directories Instructions!</legend><div class="alert alert-danger"><ul>';
	echo '<li><span style="background-color:#ffffff;color:#000000;font-weight:bold;">The upgrade process was successful. PLEASE PAY SPECIAL ATTENTION THE ADDITIONAL INSTRUCTIONS BELOW!</span></li>';
	$output = '';
	if ($warnings_rename) {
		foreach ($warnings_rename as $warning_rename) {
			$output.="<li>$warning_rename</li><br />";
		}
		echo $output;
	}
echo '</ul></div></fieldset><br />';
?>      