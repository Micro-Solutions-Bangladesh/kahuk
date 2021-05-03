<?php

if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

// this file pulls settings directly from the DB

	include_once KAHUK_LIBS_DIR.'db.php';

	if(caching == 1){
		$db->cache_dir = KAHUKPATH.'cache';
		$db->use_disk_cache = true;
		$db->cache_queries = true;
	}

	// if this query changes, update the 'cache clear' query in /libs/admin_config.php
	$usersql = $db->get_results('SELECT var_name, var_value, var_method, var_enclosein FROM ' . TABLE_PREFIX . 'config');

	if(!$usersql){die('Error. The ' . TABLE_PREFIX . 'config table is empty or does not exist');}
	
	foreach($usersql as $row) {
		$value = $row->var_value;
		if ($row->var_method == "normal"){
			/*Redwine: the var_method normal are only assigned a smarty varibale. SEE NOTE AT THE BOTTOM OF THE FILE. ACCESSIBLE FROM EVERY PAGE / FILE.*/
			$kahuk_vars[$row->var_name] = $value;
			if ($main_smarty) $main_smarty->assign(str_replace("$","",$row->var_name), $value);
		}elseif ($row->var_method == "define"){
			/*Redwine: all the var_method define are assigned a smarty variable and defined as well. ACCESSIBLE FROM EVERY PAGE / FILE.*/
			if($row->var_name != 'TABLE_PREFIX'){
				$thenewval = $value;
				if($row->var_enclosein == ""){
					if($value == "true"){
						$thenewval = true;
					} elseif($value == "false"){
						$thenewval = false;
					} else {
						$thenewval = $value;
					}
				} else {
					$thenewval = $value;
				}
				define($row->var_name, $thenewval);
				if ($main_smarty) $main_smarty->assign($row->var_name, $thenewval);
			}
		}else{
			if ($main_smarty) $main_smarty->assign($row->var_name, $value);
		}
	}
	$db->cache_queries = false;

define('StorySummary_ContentTruncate', maxSummaryLength);

/*
	*Redwine: the below variable settings are taken from the config table with var_method = normal
	* with the exception of"
	* $my_base_url
	* $my_kahuk_base
	* $dblang
	* $language
	* that are set in /settings.php
	* THESE VARIABLES ARE ACCESSIBLE FROM EVERY PAGE / FILE.
	* THESE VARIABLES ARE ONLY ACCESSIBLE AS VARIABLES AND NOT AS DEFINED.
*/
$maintenance_mode = $kahuk_vars['$maintenance_mode'] ;
$URLMethod = $kahuk_vars['$URLMethod'] ;
$trackbackURL = $kahuk_vars['$trackbackURL'];
$MAIN_SPAM_RULESET = $kahuk_vars['$MAIN_SPAM_RULESET'];
$USER_SPAM_RULESET = $kahuk_vars['$USER_SPAM_RULESET'];
$FRIENDLY_DOMAINS = $kahuk_vars['$FRIENDLY_DOMAINS'];
$SPAM_LOG_BOOK = $kahuk_vars['$SPAM_LOG_BOOK'];
$CommentOrder = $kahuk_vars['$CommentOrder'];
$anon_karma = $kahuk_vars['$anon_karma'];
$page_size = $kahuk_vars['$page_size'];
$top_users_size = $kahuk_vars['$top_users_size'];
$thetemp = $kahuk_vars['$thetemp'];
$user_language = $kahuk_vars['user_language'];
