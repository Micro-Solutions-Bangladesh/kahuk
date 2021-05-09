<?php


/**
 * 
 */
function _kahuk_insert_sample_data( $sql, $tablename ) {
    global $kahukDB;

    mysqli_query( $kahukDB, $sql );

    $msg = sprintf( "INSERT default/sample data in %s, %d rows affected.", $tablename, $kahukDB->affected_rows );
    
    // _kahuk_messages_markup( [ $msg, $sql ] );
    _kahuk_messages_markup( [ $msg ] );

    // flush();
    // ob_flush();
    // time_nanosleep(0, 500000000);
}


/**
 * 
 */
function kahuk_insert_sample_data() {
    global $kahukDB;
    $dblang = "en";

    //
    $sql = "INSERT INTO `" . table_categories . "` (`category__auto_id`, `category_lang`, `category_id`, `category_parent`, `category_name`, `category_safe_name`, `rgt`, `lft`, `category_enabled`, `category_order`, `category_desc`, `category_keywords`, `category_author_level`, `category_author_group`, `category_votes`) VALUES (0, '" . $dblang . "', 0, 0, 'all', 'all', 3, 0, 2, 0, '', '', 'normal', '', '');";
	_kahuk_insert_sample_data( $sql, table_categories );

    $sql = "UPDATE `" . table_categories . "` SET `category__auto_id` = '0' WHERE `category_name` = 'all' LIMIT 1;";
	mysqli_query( $kahukDB, $sql );

    $sql = "INSERT INTO `" . table_categories . "` (`category__auto_id`, `category_lang`, `category_id`, `category_parent`, `category_name`, `category_safe_name`, `rgt`, `lft`, `category_enabled`, `category_order`, `category_desc`, `category_keywords`, `category_author_level`, `category_author_group`, `category_votes`) VALUES (1, '" . $dblang . "', 1, 0, 'News', 'News', 2, 1, 1, 0, '', '', 'normal', '', '');";
    _kahuk_insert_sample_data( $sql, table_categories );


    //
    $sql = 'INSERT INTO `' . table_formulas . '` (`id`, `type`, `enabled`, `title`, `formula`) VALUES (1, \'report\', 1, \'Simple Story Reporting\', \'$reports > $votes * 3\');';
    _kahuk_insert_sample_data( $sql, table_formulas );


    //
    $sql = "INSERT INTO `" . table_links . "` (`link_id`, `link_author`, `link_status`, `link_randkey`, `link_votes`, `link_reports`, `link_comments`, `link_karma`, `link_modified`, `link_date`, `link_published_date`, `link_category`, `link_lang`, `link_url`, `link_url_title`, `link_title`, `link_title_url`, `link_content`, `link_summary`, `link_tags`, `link_field1`, `link_field2`, `link_field3`, `link_field4`, `link_field5`, `link_field6`, `link_field7`, `link_field8`, `link_field9`, `link_field10`, `link_field11`, `link_field12`, `link_field13`, `link_field14`, `link_field15`, `link_group_id`, `link_out`) VALUES (1, 1, 'page', 0, 0, 0, 0, '0.00', NOW(), NOW(), NULL, 0, 1, '', NULL, 'About', 'about', '<legend><strong>About Us</strong></legend>\r\n<p>Our site allows you to submit an article that will be voted on by other members. The most popular posts will be published to the front page, while the less popular articles are left in an \'New\' page until they acquire the set number of votes to move to the published page. This site is dependent on user contributed content and votes to determine the direction of the site.</p>\r\n', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0);";
    _kahuk_insert_sample_data( $sql, table_links );


    //
    $sql = "INSERT INTO `" . table_misc_data . "` ( `name` , `data` ) VALUES
	('kahuk_version', '5.0.2'),
	('adcopy_lang', 'en'),
	('adcopy_theme', 'white'),
	('adcopy_pubkey', 'Q6OGz0qMg-ByU-3QZ7DevxgQfyqgrpEC'),
	('adcopy_privkey', 'HsarZukHD6Tw29fbF1INMUbH4AQs9t-R'),
	('adcopy_hashkey', 'cPI6NC6BLyW0rjTLzWEj1UAAyYxQrRi5'),
	('captcha_method', 'solvemedia'),
	('validate', 0),
	('captcha_comment_en', 'true'),
	('captcha_reg_en', 'true'),
	('captcha_story_en', 'true'),
	('karma_submit_story','+15'),
	('karma_submit_comment','+10'),
	('karma_story_publish','+50'),
	('karma_story_vote','+1'),
	('karma_story_unvote','-1'),
	('karma_comment_vote','0'),
	('karma_story_discard','-250'),
	('karma_story_spam','-10000'),
	('karma_comment_delete','-50'),
	('modules_update_date',DATE_FORMAT(NOW(),'%Y/%m/%d')),
	('modules_update_url','https://www.kahuk.com/mods/version-update.txt'),
	('kahuk_update',''),
	('kahuk_update_url','https://www.kahuk.com/download/'),
	('modules_update_unins',''),
	('modules_upd_versions','');";

    _kahuk_insert_sample_data( $sql, table_misc_data );

    $randkey = '';

	for ( $i=0; $i<32; $i++ ) {
        // $randkey .= chr( rand( 48,200 ) );
        $randkey .= rand( 48,200 );
    }

	$sql = "INSERT INTO `" . table_misc_data . "` ( `name` , `data` ) VALUES ( 'hash', '$randkey' );";

    _kahuk_insert_sample_data( $sql, table_misc_data );


    //
    $sql = "INSERT INTO `" . table_modules . "` (`name`, `version`, `latest_version`, `folder`, `enabled`, `weight`) VALUES 
	('Admin Modify Language', 2.1, 0, 'admin_language', 1,0),
	('Captcha', 2.5, 0, 'captcha', 1,0),
	('Simple Private Messaging', 3.0, 0, 'simple_messaging', 1,0),
	('Sidebar Stories', 2.2, 0, 'sidebar_stories', 1,0),
	('Sidebar Comments', 2.1, 0, 'sidebar_comments', 1,0),
	('Karma module', 1.0, 0, 'karma', 1,0);";

    _kahuk_insert_sample_data( $sql, table_modules );


    //
    $sql = "insert into `" . table_totals . "` (`name`, `total`) values
	('published', 0),
	('new', 0),
	('discard', 0),
	('draft', 0),
	('scheduled', 0);";

    _kahuk_insert_sample_data( $sql, table_totals );


    //
    $sql = "INSERT INTO `".table_widgets."` (`id`, `name`, `version`, `latest_version`, `folder`, `enabled`, `column`, `position`, `display`) VALUES 
	(NULL, 'Dashboard Tools', 0.1, 0, 'dashboard_tools', 1, 'left', 4, ''),
	(NULL, 'Statistics', 3.0, 0, 'statistics', 1, 'left', 1, ''),
	(NULL, 'Kahuk CMS', 1.0, 0, 'kahuk_cms', 1, 'right', 5, ''),
	(NULL, 'Kahuk News', 0.1, 0, 'kahuk_news', 1, 'right', 6, '');";

    _kahuk_insert_sample_data( $sql, table_widgets );


    //
    $stmt = file_get_contents( KAHUKPATH . 'setup/libs/sql/config-table-data.sql');
	$stmt = str_replace( "INSERT INTO `table_config`", "INSERT INTO `" . table_config . "`", $stmt );

    $stmt = str_replace( "'table_prefix', 'kahuk_'", "'table_prefix', '" . TABLE_PREFIX . "'", $stmt );

	$sql = str_replace( "---language---", "english", $stmt );

	_kahuk_insert_sample_data( $sql, table_config );


    //
    $userip = mysqli_real_escape_string( $kahukDB, $_SERVER['REMOTE_ADDR'] );

	$saltedpass = generatePassHash( $_POST['upassword'] );

    $sql = "INSERT INTO `" . table_users . "` (
        `user_id`, `user_login`, `user_level`, 
        `user_modification`, `user_date`, `user_pass`, 
        `user_email`, `user_names`, `user_karma`, 
        `user_url`, `user_lastlogin`, `user_ip`, 
        `user_lastip`, `last_reset_request`, `user_enabled`
    ) VALUES (
        1, '" . mysqli_real_escape_string( $kahukDB, $_POST['uname'] ) . "', 'admin', 
        NOW(), NOW(), '$saltedpass', 
        '" . mysqli_real_escape_string( $kahukDB, $_POST['uemail'] ) . "', '', '10.00', 
        'https://kahuk.com', NOW(), '$userip', 
        '0', NOW(), '1'
    );";
	
    _kahuk_insert_sample_data( $sql, table_users );
}
