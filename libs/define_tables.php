<?php

if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

if( ! defined( 'TABLE_PREFIX' ) ) {
	define( 'TABLE_PREFIX', '' );
}

if( ! defined( 'tables_defined' ) ) {
	define( 'table_categories', TABLE_PREFIX . "categories" );
	define( 'table_comments', TABLE_PREFIX . "comments" );
	define( 'table_friends', TABLE_PREFIX . "friends" );
	define( 'table_links', TABLE_PREFIX . "links" );
	define( 'table_trackbacks', TABLE_PREFIX . "trackbacks" );
	define( 'table_users', TABLE_PREFIX . "users" );
	define( 'table_votes', TABLE_PREFIX . "votes" );
	define( 'table_config', TABLE_PREFIX . "config" ); 
	define( 'table_modules', TABLE_PREFIX . "modules" );
	define( 'table_messages', TABLE_PREFIX . "messages" );
	define( 'table_formulas', TABLE_PREFIX . "formulas" );
	define( 'table_saved_links', TABLE_PREFIX . "saved_links" );
	define( 'table_totals', TABLE_PREFIX . "totals" );
	define( 'table_misc_data', TABLE_PREFIX . "misc_data" );
	define( 'table_redirects', TABLE_PREFIX . "redirects" );
	define( 'table_groups', TABLE_PREFIX . "groups" );
	define( 'table_group_member', TABLE_PREFIX . "group_member" );
	define( 'table_group_shared', TABLE_PREFIX . "group_shared" );
	define( 'table_login_attempts', TABLE_PREFIX . "login_attempts" );
	define( 'table_widgets', TABLE_PREFIX . "widgets" );
	define( 'table_old_urls', TABLE_PREFIX . "old_urls" );
	define( 'table_additional_categories', TABLE_PREFIX . "additional_categories" );

	define( 'tables_defined', true);
}
