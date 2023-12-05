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

    $ipAddress = mysqli_real_escape_string( $kahukDB, $_SERVER['REMOTE_ADDR'] );


    //
	$saltedpass = generatePassHash($_POST['upassword']);

    $kahukcmsPass = kahuk_generate_password();
	$saltedpassForKahukcms = generatePassHash($kahukcmsPass);

    $sql = "
        INSERT INTO `" . table_users . "` (
            `user_login`, 
            `user_level`, `user_status`, `user_modification`, 
            `user_date`, `user_pass`, 
            `user_email`, 
            `user_names`, `user_karma`, `user_url`, 
            `user_lastlogin`, `user_ip`, `user_lastip`, 
            `verification_code`
        ) VALUES (
            '" . mysqli_real_escape_string($kahukDB, $_POST['uname']) . "',
            'admin', 'enable', NOW(),
            NOW(), '$saltedpass', 
            '" . mysqli_real_escape_string($kahukDB, $_POST['uemail']) . "', 
            '', '10', 'https://kahuk.com', 
            NOW(), '$ipAddress', '$ipAddress',
            'verified'
        ),(
            'kahukcms',
            'normal', 'enable', NOW(),
            NOW(), '$saltedpassForKahukcms', 
            'support@kahuk.com', 
            '', '44', 'https://kahuk.com', 
            NOW(), '$ipAddress', '$ipAddress',
            'verified'
        );
    ";
	
    _kahuk_insert_sample_data( $sql, table_users );



    // Configs
    $stmt = file_get_contents(KAHUKPATH . "setup/libs/sql/sample-data-configs.sql");
	$stmt = str_replace("---table_config---", table_config, $stmt);

    $sql = str_replace("---table_prefix---", TABLE_PREFIX, $stmt);

	_kahuk_insert_sample_data($sql, table_config);


    // Insert Categories
    $stmt = file_get_contents(KAHUKPATH . "setup/libs/sql/sample-data-categories.sql");
    $sql = str_replace("---table_categories---", table_categories, $stmt);

	_kahuk_insert_sample_data($sql, table_categories);


    // $sql = "INSERT INTO `" . table_categories . "` (`category__auto_id`, `category_lang`, `category_id`, `category_parent`, `category_name`, `category_safe_name`, `rgt`, `lft`, `category_enabled`, `category_order`, `category_desc`, `category_keywords`, `category_author_level`, `category_author_group`, `category_votes`) VALUES (0, '" . $dblang . "', 0, 0, 'all', 'all', 3, 0, 2, 0, '', '', 'normal', '', '');";
	// _kahuk_insert_sample_data( $sql, table_categories );

    // $sql = "UPDATE `" . table_categories . "` SET `category__auto_id` = '0' WHERE `category_name` = 'all' LIMIT 1;";
	// mysqli_query( $kahukDB, $sql );

    // $stmt = file_get_contents( KAHUKPATH . 'setup/libs/sql/sample-data-categories.sql');
	// $sql = str_replace( "`PREFIX_categories`", "`" . table_categories. "`", $stmt );

	// _kahuk_insert_sample_data( $sql, table_categories );


    // //
    // $sql = 'INSERT INTO `' . table_formulas . '` (`id`, `type`, `enabled`, `title`, `formula`) VALUES (1, \'report\', 1, \'Simple Story Reporting\', \'$reports > $votes * 3\');';
    // _kahuk_insert_sample_data( $sql, table_formulas );


    //
    $stmt = file_get_contents( KAHUKPATH . 'setup/libs/sql/sample-data-links.sql');
	$sql = str_replace( "---table_links---", table_links, $stmt );

	_kahuk_insert_sample_data( $sql, table_links );


    //
    $sql = "
        INSERT INTO `" . table_misc_data . "` ( `name` , `data` ) VALUES
        ('kahuk_version', '6.0.0'),
        ('captcha_method', ''),
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
        ('karma_comment_delete','-50');
    ";

    _kahuk_insert_sample_data($sql, table_misc_data);

    $randkey = '';

	for ( $i=0; $i<32; $i++ ) {
        $randkey .= rand(48, 200);
    }

	$sql = "INSERT INTO `" . table_misc_data . "` ( `name` , `data` ) VALUES ( 'hash', '$randkey' );";

    _kahuk_insert_sample_data( $sql, table_misc_data );




    //
    $stmt = file_get_contents(KAHUKPATH . "setup/libs/sql/sample-data-totals.sql");
	$sql = str_replace("---table_totals---", table_totals, $stmt);

	_kahuk_insert_sample_data( $sql, table_totals );



    //
    $stmt = file_get_contents( KAHUKPATH . "setup/libs/sql/sample-data-votes.sql");

	$stmt = str_replace("---table_votes---", table_votes, $stmt);
	$sql = str_replace("---vote_ip---", $ipAddress, $stmt);

	_kahuk_insert_sample_data($sql, table_votes);
}
