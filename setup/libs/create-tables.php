<?php

/**
 * 
 */
function kahuk_index_query($query) {
    global $kahukDB;

    // Execute CREATE TABLE query
    mysqli_query( $kahukDB, $query );

    echo kahuk_create_markup_message("Executed: {$query}", "info");

    //
    flush();
    ob_flush();
    time_nanosleep(0, 100000000);
}

/**
 * 
 */
function kahuk_create_table( $query, $table_name ) {
    global $kahukDB;

    // DLETE table if exist
    $sql = 'DROP TABLE IF EXISTS `' . $table_name . '`;';

	mysqli_query( $kahukDB, $sql );

    // Execute CREATE TABLE query
    mysqli_query( $kahukDB, $query );

    // Check if the TABLE is created
    $successful = kahuk_check_table_exists( $table_name );

    // Output Message
    $msg = "Fail to Create Table: " . $table_name;
    $msgType = 'error';

    if( $successful ) {
        $msg = "Successfully Created Table: " . $table_name;
        $msgType = 'success';
	}

    echo kahuk_create_markup_message("{$msg}<br>{$query}", $msgType);

    //
    flush();
    ob_flush();
    time_nanosleep(0, 500000000);
}


/**
 * Creating all initial tables for Kahuk site
 * 
 * @since 5.0.0
 * 
 * @return void
 */
function kahuk_create_primary_tables() {
    $dblang = 'en';

    // ALTER TABLE database_name.table_name ENGINE=InnoDB;
    $dbEngine = "InnoDB"; // "MyISAM";

    $dbCollate = "utf8mb4_unicode_ci";
    $dbCharset = "utf8mb4";

    $dbSettings = "ENGINE={$dbEngine} DEFAULT CHARSET={$dbCharset} COLLATE={$dbCollate}";

    // ********************************	
    // $sql = "CREATE TABLE `" . table_additional_categories . "` (
    //     `ac_link_id` int(11) NOT NULL,
    //     `ac_cat_id` int(11) NOT NULL,
    //     UNIQUE KEY `ac_link_id` (`ac_link_id`,`ac_cat_id`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    // kahuk_create_table( $sql, table_additional_categories );


    //
    $sql = "
        CREATE TABLE `" . table_additional_categories . "` (
            `ac_link_id` int NOT NULL,
            `ac_cat_id` int NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_additional_categories);

    //
    $sql = "
        ALTER TABLE `" . table_additional_categories . "`
            ADD UNIQUE KEY `ac_link_id` (`ac_link_id`,`ac_cat_id`);
    ";

    kahuk_index_query($sql);


    // ********************************	
	// $sql = "CREATE TABLE `" . table_categories . "` (
    //     `category__auto_id` int(11) NOT NULL auto_increment,
    //     `category_lang` varchar(" . strlen($dblang) . ") collate utf8mb4_unicode_ci NOT NULL default " . "'" . $dblang . "',
    //     `category_id` int(11) NOT NULL default '0',
    //     `category_parent` int(11) NOT NULL default '0',
    //     `category_name` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `category_safe_name` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `rgt` int(11) NOT NULL default '0',
    //     `lft` int(11) NOT NULL default '0',
    //     `category_enabled` int(11) NOT NULL default '1',
    //     `category_order` int(11) NOT NULL default '0',
    //     `category_desc` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `category_keywords` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `category_author_level` enum('normal','moderator','admin') collate utf8mb4_unicode_ci NOT NULL default 'normal',
    //     `category_author_group` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `category_votes` varchar(4) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `category_karma` varchar(4) collate utf8mb4_unicode_ci NOT NULL default '',
    //     PRIMARY KEY  (`category__auto_id`),
    //     KEY `category_id` (`category_id`),
    //     KEY `category_parent` (`category_parent`),
    //     KEY `category_safe_name` (`category_safe_name`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_categories . "` (
            `category_id` int NOT NULL,
            `category_parent` int NOT NULL DEFAULT '0',
            `category_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `category_safe_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `category_status` enum('disable','enable','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disable',
            `category_order` int NOT NULL DEFAULT '0',
            `category_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `category_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_categories);

    //
    $sql = "
        ALTER TABLE `" . table_categories . "`
            ADD PRIMARY KEY (`category_id`),
            ADD KEY `category_parent` (`category_parent`),
            ADD KEY `category_safe_name` (`category_safe_name`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_categories . "` 
            MODIFY `category_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);



    // ********************************	
	// $sql = "CREATE TABLE `" . table_comments . "` (
    //     `comment_id` int(20) NOT NULL auto_increment,
    //     `comment_randkey` int(11) NOT NULL default '0',
    //     `comment_parent` int(20) default '0',
    //     `comment_link_id` int(20) NOT NULL default '0',
    //     `comment_user_id` int(20) NOT NULL default '0',
    //     `comment_date` datetime NOT NULL,
    //     `comment_karma` smallint(6) NOT NULL default '0',
    //     `comment_content` LONGTEXT collate utf8mb4_unicode_ci NOT NULL,
    //     `comment_votes` int(20) NOT NULL default '0',
    //     `comment_status` enum('discard','moderated','published','spam') collate utf8mb4_unicode_ci NOT NULL default 'published',
    //     PRIMARY KEY  (`comment_id`),
    //     UNIQUE KEY `comments_randkey` (`comment_randkey`,`comment_link_id`,`comment_user_id`,`comment_parent`),
    //     KEY `comment_link_id` (`comment_link_id`, `comment_parent`, `comment_date`),
    //     KEY `comment_link_id_2` (`comment_link_id`,`comment_date`),
    //     KEY `comment_date` (`comment_date`),
    //     KEY `comment_parent` (`comment_parent`,`comment_date`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
  
    $sql = "
        CREATE TABLE `" . table_comments . "` (
            `comment_id` int NOT NULL,
            `comment_parent` int DEFAULT '0',
            `comment_link_id` int NOT NULL DEFAULT '0',
            `comment_user_id` int NOT NULL DEFAULT '0',
            `comment_date` datetime NOT NULL,
            `comment_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `comment_status` enum('await','published','trash') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published'
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_comments);

    //
    $sql = "
        ALTER TABLE `" . table_comments . "`
            ADD PRIMARY KEY (`comment_id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_comments . "`
            MODIFY `comment_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


    // ********************************	
    // $sql = "CREATE TABLE `" . table_config . "` (
    //     `var_id` int(11) NOT NULL auto_increment,
    //     `var_page` varchar(50) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_name` varchar(100) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_value` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_defaultvalue` varchar(50) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_optiontext` varchar(200) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_title` varchar(200) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_desc` text collate utf8mb4_unicode_ci NOT NULL,
    //     `var_method` varchar(10) collate utf8mb4_unicode_ci NOT NULL,
    //     `var_enclosein` varchar(5) collate utf8mb4_unicode_ci default NULL,
    //     PRIMARY KEY  (`var_id`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_config . "` (
            `var_id` int NOT NULL,
            `var_page` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_defaultvalue` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_optiontext` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `var_enclosein` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_config);

    //
    $sql = "
        ALTER TABLE `" . table_config . "`
            ADD PRIMARY KEY (`var_id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_config . "`
            MODIFY `var_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);

      
  
    // ********************************	
    // $sql = "CREATE TABLE `" . table_formulas . "` (
    //     `id` int(11) NOT NULL auto_increment,
    //     `type` varchar(10) collate utf8mb4_unicode_ci NOT NULL,
    //     `enabled` tinyint(1) NOT NULL,
    //     `title` varchar(50) collate utf8mb4_unicode_ci NOT NULL,
    //     `formula` MEDIUMTEXT collate utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY  (`id`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_formulas . "` (
            `id` int NOT NULL,
            `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `enabled` tinyint(1) NOT NULL,
            `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `formula` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_formulas);

    //
    $sql = "
        ALTER TABLE `" . table_formulas . "`
            ADD PRIMARY KEY (`id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_formulas . "`
            MODIFY `id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);

  
  
    // ********************************	
    // $sql = "CREATE TABLE `" . table_friends . "` (
    //     `friend_id` int(11) NOT NULL auto_increment,
    //     `friend_from` bigint(20) NOT NULL default '0',
    //     `friend_to` bigint(20) NOT NULL default '0',
    //     PRIMARY KEY  (`friend_id`),
    //     UNIQUE KEY `friends_from_to` (`friend_from`,`friend_to`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_friends . "` (
            `friend_id` int NOT NULL,
            `friend_from` bigint NOT NULL DEFAULT '0',
            `friend_to` bigint NOT NULL DEFAULT '0'
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_friends);

    //
    $sql = "
        ALTER TABLE `" . table_friends . "`
            ADD PRIMARY KEY (`friend_id`),
            ADD UNIQUE KEY `friends_from_to` (`friend_from`,`friend_to`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_friends . "`
            MODIFY `friend_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);

  
    // ********************************	
    // $sql = "CREATE TABLE `" . table_groups . "` (
    //     `group_id` int(20) NOT NULL auto_increment,
    //     `group_creator` int(20) NOT NULL,
    //     `group_status` enum('Enable','disable') collate utf8mb4_unicode_ci NOT NULL,
    //     `group_members` int(20) NOT NULL,
    //     `group_date` datetime NOT NULL,
    //     `group_safename` mediumtext collate utf8mb4_unicode_ci NOT NULL,
    //     `group_name` mediumtext collate utf8mb4_unicode_ci NOT NULL,
    //     `group_description` mediumtext collate utf8mb4_unicode_ci NOT NULL,
    //     `group_privacy` enum('private','public','restricted') collate utf8mb4_unicode_ci NOT NULL,
    //     `group_avatar` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_vote_to_publish` int(20) NOT NULL,
    //     `group_field1` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_field2` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_field3` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_field4` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_field5` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_field6` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     `group_notify_email` tinyint(1) NOT NULL,
    //       PRIMARY KEY  (`group_id`),
    //       KEY `group_name` (`group_name`(100)),
    //       KEY `group_creator` (`group_creator`, `group_status`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_groups . "` (
            `group_id` int NOT NULL,
            `group_creator` int NOT NULL,
            `group_status` enum('pending','enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `group_members` int NOT NULL,
            `group_date` datetime NOT NULL,
            `group_safename` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_privacy` enum('public','private','restricted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `group_avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_field6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table( $sql, table_groups );

    //
    $sql = "
        ALTER TABLE `" . table_groups . "`
            ADD PRIMARY KEY (`group_id`),
            ADD KEY `group_name` (`group_name`(100)),
            ADD KEY `group_creator` (`group_creator`,`group_status`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_groups . "`
            MODIFY `group_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);
  

    // ********************************
    // $sql = "CREATE TABLE `" . table_group_member . "` (
    //     `member_id` INT( 20 ) NOT NULL auto_increment,
    //     `member_user_id` INT( 20 ) NOT NULL ,
    //     `member_group_id` INT( 20 ) NOT NULL ,
    //     `member_role` ENUM( 'admin', 'normal', 'moderator', 'flagged', 'banned' ) collate utf8mb4_unicode_ci NOT NULL,
    //     `member_status` ENUM( 'active', 'inactive') collate utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY  (`member_id`),
    //     KEY `user_group` (`member_group_id`, `member_user_id`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_group_member . "` (
            `member_id` int NOT NULL,
            `member_user_id` int NOT NULL,
            `member_group_id` int NOT NULL,
            `member_role` enum('admin','moderator','normal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `member_status` enum('requested','active','inactive','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_group_member);

    //
    $sql = "
        ALTER TABLE `" . table_group_member . "`
            ADD PRIMARY KEY (`member_id`),
            ADD KEY `user_group` (`member_group_id`,`member_user_id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_group_member . "`
            MODIFY `member_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);
  
  
    // ********************************
    // $sql = "CREATE TABLE `" . table_group_shared . "` (
    //     `share_id` INT( 20 ) NOT NULL AUTO_INCREMENT,
    //     `share_link_id` INT( 20 ) NOT NULL ,
    //     `share_group_id` INT( 20 ) NOT NULL ,
    //     `share_user_id` INT( 20 ) NOT NULL,
    //     PRIMARY KEY (`share_id`),
    //     UNIQUE KEY `share_group_id` (`share_group_id`,`share_link_id`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_group_shared . "` (
            `share_id` int NOT NULL,
            `share_link_id` int NOT NULL,
            `share_group_id` int NOT NULL,
            `share_user_id` int NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table( $sql, table_group_shared );

    //
    $sql = "
        ALTER TABLE `" . table_group_shared . "`
            ADD PRIMARY KEY (`share_id`),
            ADD UNIQUE KEY `share_group_id` (`share_group_id`,`share_link_id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_group_shared . "`
            MODIFY `share_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


    // ********************************

	/**********************************
	Redwine: checking for the MySQL Server version. If it is older than 5.0.3, then `link_url` varchar will be the maximum of 255; otherwise, we set it to 512 to accommodate long urlencoded.
	**********************************/
    $mysqlServerVersion = kahuk_mysql_server_version();

    if ( $mysqlServerVersion < '5.0.3' ) {
		$urllength = '255';
	} else {
		$urllength = '512';
	}

    $msgTemp = [];
    $msgTemp[] = sprintf( "Your MySQL Server version is: %s\n", $mysqlServerVersion );
    $msgTemp[] = sprintf( "The varchar maximum character length compatible with your $mysqlServerVersion version to be set for `link_url`, in the links table, is: %s\n", $urllength );

    echo kahuk_create_markup_message(implode("<br>", $msgTemp), 'info');

    flush();
    ob_flush();
    time_nanosleep(0, 500000000);

    // ********************************
    // $sql = "CREATE TABLE `" . table_links . "` (
    //     `link_id` int(20) NOT NULL auto_increment,
    //     `link_author` int(20) NOT NULL default '0',
    //     `link_status` enum('discard','new','published','abuse','duplicate','page','spam','moderated','draft','scheduled') collate utf8mb4_unicode_ci NOT NULL default 'discard',
    //     `link_randkey` int(20) NOT NULL default '0',
    //     `link_votes` int(20) NOT NULL default '0',
    //     `link_reports` int(20) NOT NULL default '0',
    //     `link_comments` int(20) NOT NULL default '0',
    //     `link_karma` decimal(10,2) NOT NULL default '0.00',
    //     `link_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //     `link_date` timestamp NULL DEFAULT NULL,
    //     `link_published_date` timestamp NULL DEFAULT NULL,
    //     `link_category` int(11) NOT NULL default '0',
    //     `link_lang` int(11) NOT NULL default '1',
    //     `link_url` varchar($urllength) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_url_title` text collate utf8mb4_unicode_ci,
    //     `link_title` text collate utf8mb4_unicode_ci NOT NULL,
    //     `link_title_url` varchar(255) collate utf8mb4_unicode_ci default NULL,
    //     `link_content` mediumtext collate utf8mb4_unicode_ci NOT NULL,
    //     `link_summary` text collate utf8mb4_unicode_ci,
    //     `link_tags` text collate utf8mb4_unicode_ci,
    //     `link_field1` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field2` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field3` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field4` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field5` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field6` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field7` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field8` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field9` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field10` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field11` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field12` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field13` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field14` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_field15` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `link_group_id` int(20) NOT NULL default '0',
    //     `link_group_status` enum(  'new',  'published',  'discard' ) DEFAULT 'new' NOT NULL,
    //     `link_out` int(11) NOT NULL default '0',
    //     PRIMARY KEY  (`link_id`),
    //     KEY `link_author` (`link_author`),
    //     KEY `link_url` (`link_url`(50)),
    //     KEY `link_status` (`link_status`),
    //     KEY `link_title_url` (`link_title_url`(50)),
    //     KEY `link_date` (`link_date`),
    //     KEY `link_published_date` (`link_published_date`),
    //     FULLTEXT KEY `link_url_2` (`link_url`,`link_url_title`,`link_title`,`link_content`,`link_tags`),
    //     FULLTEXT KEY `link_tags` (`link_tags`),
    //     FULLTEXT KEY `link_search` (`link_title`,`link_content`,`link_tags`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_links . "` (
            `link_id` int NOT NULL,
            `link_author` int NOT NULL DEFAULT '0',
            `link_status` enum('new','published','page','trash','draft') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
            `link_randkey` int NOT NULL DEFAULT '0',
            `link_votes` int NOT NULL DEFAULT '0',
            `link_karma` int NOT NULL DEFAULT '0',
            `link_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `link_date` timestamp NULL DEFAULT NULL,
            `link_published_date` timestamp NULL DEFAULT NULL,
            `link_category` int NOT NULL DEFAULT '0',
            `link_url` varchar({$urllength}) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `link_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `link_title_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `link_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `link_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `link_group_id` int NOT NULL DEFAULT '0',
            `link_out` int NOT NULL DEFAULT '0'
        ) {$dbSettings};
    ";

    kahuk_create_table( $sql, table_links );

    //
    $sql = "
        ALTER TABLE `" . table_links . "`
            ADD PRIMARY KEY (`link_id`),
            ADD KEY `link_author` (`link_author`),
            ADD KEY `link_url` (`link_url`(50)),
            ADD KEY `link_status` (`link_status`),
            ADD KEY `link_title_url` (`link_title_url`(50)),
            ADD KEY `link_date` (`link_date`),
            ADD KEY `link_published_date` (`link_published_date`);
        ALTER TABLE `" . table_links . "` 
            ADD FULLTEXT KEY `link_url_2` (`link_url`,`link_title`,`link_content`);
        ALTER TABLE `" . table_links . "` 
            ADD FULLTEXT KEY `link_search` (`link_title`,`link_content`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_links . "`
            MODIFY `link_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


    // ********************************
    // $sql = "CREATE TABLE `" . table_login_attempts . "` (
    //     `login_id` int(11) NOT NULL auto_increment,
    //     `login_username` varchar(100) collate utf8mb4_unicode_ci default NULL,
    //     `login_time` datetime NOT NULL,
    //     `login_ip` varchar(100) collate utf8mb4_unicode_ci default NULL,
    //     `login_count` int(11) NOT NULL default '0',
    //     PRIMARY KEY  (`login_id`),
    //     UNIQUE KEY `login_username` (`login_ip`,`login_username`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_login_attempts . "` (
            `login_id` int NOT NULL,
            `login_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `login_time` datetime NOT NULL,
            `login_ip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `login_count` int NOT NULL DEFAULT '0'
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_login_attempts);

    //
    $sql = "
        ALTER TABLE `" . table_login_attempts . "`
            ADD PRIMARY KEY (`login_id`),
            ADD UNIQUE KEY `login_username` (`login_ip`,`login_username`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_login_attempts . "`
            MODIFY `login_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


    // ********************************
	// $sql = "CREATE TABLE `" . table_messages . "` (
    //     `idMsg` int(11) NOT NULL auto_increment,
    //     `title` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `body` text NOT NULL,
    //     `sender` int(11) NOT NULL default '0',
    //     `receiver` int(11) NOT NULL default '0',
    //     `senderLevel` int(11) NOT NULL default '0',
    //     `readed` int(11) NOT NULL default '0',
    //     `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //     PRIMARY KEY  (`idMsg`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_messages . "` (
            `idMsg` int NOT NULL,
            `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `sender` int NOT NULL DEFAULT '0',
            `receiver` int NOT NULL DEFAULT '0',
            `senderLevel` int NOT NULL DEFAULT '0',
            `readed` int NOT NULL DEFAULT '0',
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_messages);

    //
    $sql = "
        ALTER TABLE `" . table_messages . "`
            ADD PRIMARY KEY (`idMsg`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_messages . "`
            MODIFY `idMsg` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_misc_data . "` (
	// 	`name` VARCHAR( 20 ) collate utf8mb4_unicode_ci NOT NULL ,
	// 	`data` TEXT collate utf8mb4_unicode_ci NOT NULL ,
	// 	PRIMARY KEY ( `name` )
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_misc_data . "` (
            `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_misc_data);

    //
    $sql = "
        ALTER TABLE `" . table_misc_data . "`
            ADD PRIMARY KEY (`name`);
    ";

    kahuk_index_query($sql);

	
	// ********************************
	// $sql = "CREATE TABLE `" . table_modules . "` (
    //     `id` int(11) NOT NULL auto_increment,
    //     `name` varchar(50) collate utf8mb4_unicode_ci NOT NULL,
    //     `version` float(10,1) NOT NULL,
    //     `latest_version` float(10,1) NOT NULL,
    //     `folder` varchar(50) collate utf8mb4_unicode_ci NOT NULL,
    //     `enabled` tinyint(1) NOT NULL,
    //     `weight` INT NOT NULL,
    //     PRIMARY KEY  (`id`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    // kahuk_create_table( $sql, table_modules );
	

	// ********************************
	// $sql = "CREATE TABLE `" . table_old_urls ."` (
    //     `old_id` int(11) NOT NULL auto_increment,
    //     `old_link_id` int(11) NOT NULL,
    //     `old_title_url` varchar(255) collate utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY  (`old_id`),
    //     KEY `old_title_url` (  `old_title_url`(50) )
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_old_urls ."` (
            `old_id` int NOT NULL,
            `old_link_id` int NOT NULL,
            `old_title_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_old_urls);

    //
    $sql = "
        ALTER TABLE `" . table_old_urls ."`
            ADD PRIMARY KEY (`old_id`),
            ADD KEY `old_title_url` (`old_title_url`(50));
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_old_urls ."`
            MODIFY `old_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_redirects . "` (
    //     `redirect_id` int(11) NOT NULL auto_increment,
    //     `redirect_old` varchar(255) NOT NULL,
    //     `redirect_new` varchar(255) NOT NULL,
    //     PRIMARY KEY  (`redirect_id`),
    //     KEY `redirect_old` (`redirect_old`(50))
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_redirects . "` (
            `redirect_id` int NOT NULL,
            `redirect_old` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `redirect_new` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_redirects);

    //
    $sql = "
        ALTER TABLE `" . table_redirects . "`
            ADD PRIMARY KEY (`redirect_id`),
            ADD KEY `redirect_old` (`redirect_old`(50));
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_redirects . "`
            MODIFY `redirect_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_saved_links ."` (
    //     `saved_id` int(11) NOT NULL auto_increment,
    //     `saved_user_id` int(11) NOT NULL,
    //     `saved_link_id` int(11) NOT NULL,
    //     `saved_privacy` ENUM( 'private', 'public' ) collate utf8mb4_unicode_ci NOT NULL default 'public',
    //     PRIMARY KEY  (`saved_id`),
    //     KEY `saved_user_id` (  `saved_user_id` )
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_saved_links ."` (
            `saved_id` int NOT NULL,
            `saved_user_id` int NOT NULL,
            `saved_link_id` int NOT NULL,
            `saved_privacy` enum('private','public') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
            `saved_status` enum('enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable'
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_saved_links);

    //
    $sql = "
        ALTER TABLE `" . table_saved_links ."`
            ADD PRIMARY KEY (`saved_id`),
            ADD KEY `saved_user_id` (`saved_user_id`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_saved_links ."`
            MODIFY `saved_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_totals . "` (
    //     `name` varchar(10) NOT NULL,
    //     `total` int(11) NOT NULL,
    //     PRIMARY KEY  (`name`)
    // ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_totals . "` (
            `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `total` int NOT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_totals);

    //
    $sql = "
        ALTER TABLE `" . table_totals . "`
            ADD PRIMARY KEY (`name`);
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_trackbacks . "` (
	//   `trackback_id` int(10) unsigned NOT NULL auto_increment,
	//   `trackback_link_id` int(11) NOT NULL default '0',
	//   `trackback_user_id` int(11) NOT NULL default '0',
	//   `trackback_type` enum('in','out') NOT NULL default 'in',
	//   `trackback_status` enum('ok','pendent','error') NOT NULL default 'pendent',
	//   `trackback_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	//   `trackback_date` timestamp NULL default NULL,
	//   `trackback_url` varchar(200) collate utf8mb4_unicode_ci NOT NULL default '',
	//   `trackback_title` text collate utf8mb4_unicode_ci DEFAULT NULL,
	//   `trackback_content` text collate utf8mb4_unicode_ci DEFAULT NULL,
	//   PRIMARY KEY  (`trackback_id`),
	//   UNIQUE KEY `trackback_link_id_2` (`trackback_link_id`,`trackback_type`,`trackback_url`),
	//   KEY `trackback_link_id` (`trackback_link_id`),
	//   KEY `trackback_url` (`trackback_url`),
	//   KEY `trackback_date` (`trackback_date`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_trackbacks . "` (
            `trackback_id` int UNSIGNED NOT NULL,
            `trackback_link_id` int NOT NULL DEFAULT '0',
            `trackback_user_id` int NOT NULL DEFAULT '0',
            `trackback_type` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in',
            `trackback_status` enum('ok','pendent','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendent',
            `trackback_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `trackback_date` timestamp NULL DEFAULT NULL,
            `trackback_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `trackback_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `trackback_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_trackbacks);

    //
    $sql = "
        ALTER TABLE `" . table_trackbacks . "`
            ADD PRIMARY KEY (`trackback_id`),
            ADD UNIQUE KEY `trackback_link_id_2` (`trackback_link_id`,`trackback_type`,`trackback_url`),
            ADD KEY `trackback_link_id` (`trackback_link_id`),
            ADD KEY `trackback_url` (`trackback_url`),
            ADD KEY `trackback_date` (`trackback_date`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_trackbacks . "`
            MODIFY `trackback_id` int UNSIGNED NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_users . "` (
    //     `user_id` int(20) NOT NULL auto_increment,
    //     `user_login` varchar(32) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_level` enum('normal','moderator','admin','Spammer') collate utf8mb4_unicode_ci NOT NULL default 'normal',
    //     `user_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //     `user_date` timestamp NOT NULL,
    //     `user_pass` varchar(80) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_email` varchar(128) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_names` varchar(128) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_karma` decimal(10,2) default '0.00',
    //     `user_url` varchar(128) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_lastlogin` timestamp NULL DEFAULT NULL,
    //     `user_facebook` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_twitter` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_linkedin` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_googleplus` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_skype` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_pinterest` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `public_email` varchar(64) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_avatar_source` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_ip` varchar(20) collate utf8mb4_unicode_ci default '0',
    //     `user_lastip` varchar(20) collate utf8mb4_unicode_ci default '0',
    //     `last_reset_request` timestamp NOT NULL,
    //     `last_reset_code` varchar(255) collate utf8mb4_unicode_ci default NULL,
    //     `user_location` varchar(255) collate utf8mb4_unicode_ci default NULL,
    //     `user_occupation` varchar(255) collate utf8mb4_unicode_ci default NULL,
    //     `user_categories` VARCHAR(255) collate utf8mb4_unicode_ci NOT NULL default '',
    //     `user_enabled` tinyint(1) NOT NULL default '1',
    //     `user_language` varchar(32) collate utf8mb4_unicode_ci default NULL,
    //     PRIMARY KEY  (`user_id`),
    //     UNIQUE KEY `user_login` (`user_login`),
    //     KEY `user_email` (`user_email`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_users . "` (
            `user_id` int NOT NULL,
            `user_login` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_level` enum('admin','moderator','normal','spammer','unverified') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unverified',
            `user_status` enum('disable','enable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable',
            `user_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `user_date` timestamp NOT NULL,
            `user_pass` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_names` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_karma` int DEFAULT '0',
            `user_url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_lastlogin` timestamp NULL DEFAULT NULL,
            `user_facebook` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_twitter` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_linkedin` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_googleplus` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_skype` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_pinterest` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `public_email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_avatar_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `user_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
            `user_lastip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
            `last_reset_request` timestamp NULL DEFAULT NULL,
            `last_reset_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `user_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `user_occupation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `user_categories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `verification_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ) {$dbSettings};
    ";

    kahuk_create_table($sql, table_users);

    //
    $sql = "
        ALTER TABLE `" . table_users . "`
            ADD PRIMARY KEY (`user_id`),
            ADD UNIQUE KEY `user_login` (`user_login`),
            ADD KEY `user_email` (`user_email`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_users . "`
            MODIFY `user_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);


	// ********************************
	// $sql = "CREATE TABLE `" . table_votes . "` (
    //     `vote_id` int(20) NOT NULL auto_increment,
    //     `vote_type` enum('links','comments') NOT NULL default 'links',
    //     `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    //     `vote_link_id` int(20) NOT NULL default '0',
    //     `vote_user_id` int(20) NOT NULL default '0',
    //     `vote_value` smallint(11) NOT NULL default '1',
    //     `vote_karma` int(11) NULL default '0',
    //     `vote_ip` varchar(64) default NULL,
    //     PRIMARY KEY  (`vote_id`),
    //     KEY `user_id` (`vote_user_id`),
    //     KEY `link_id` (`vote_link_id`),
    //     KEY `vote_type` (`vote_type`,`vote_link_id`,`vote_user_id`,`vote_ip`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    $sql = "
        CREATE TABLE `" . table_votes . "` (
            `vote_id` int NOT NULL,
            `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `vote_link_id` int NOT NULL DEFAULT '0',
            `vote_user_id` int NOT NULL DEFAULT '0',
            `vote_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `vote_reaction` enum('positive','negative','invalid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid'
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    kahuk_create_table($sql, table_votes);

    //
    $sql = "
        ALTER TABLE `" . table_votes . "`
            ADD PRIMARY KEY (`vote_id`),
            ADD KEY `user_id` (`vote_user_id`),
            ADD KEY `link_id` (`vote_link_id`),
            ADD KEY `vote_type` (`vote_link_id`,`vote_user_id`,`vote_ip`);
    ";

    kahuk_index_query($sql);

    //
    $sql = "
        ALTER TABLE `" . table_votes . "`
            MODIFY `vote_id` int NOT NULL AUTO_INCREMENT;
    ";

    kahuk_index_query($sql);



	// ********************************
	// $sql = "CREATE TABLE `" . table_widgets . "` (
    //     `id` int(11) NOT NULL auto_increment,
    //     `name` varchar(50) collate utf8mb4_unicode_ci default NULL,
    //     `version` float NOT NULL,
    //     `latest_version` float NOT NULL,
    //     `folder` varchar(50) collate utf8mb4_unicode_ci default NULL,
    //     `enabled` tinyint(1) NOT NULL,
    //     `column` enum('left','right') collate utf8mb4_unicode_ci NOT NULL,
    //     `position` int(11) NOT NULL,
    //     `display` char(5) collate utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY  (`id`),
    //     UNIQUE KEY `folder` (`folder`)
	// ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    // kahuk_create_table( $sql, table_widgets );


}
