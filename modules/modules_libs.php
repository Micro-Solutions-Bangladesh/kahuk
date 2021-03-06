<?php
if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

function module_add_action($location, $the_function, $variables, $weight = array ('weight' => 0) )
{
	global $script_name, $module_actions, $include_in_pages, $do_not_include_in_pages;
	if(is_array($include_in_pages)){
		if (in_array($script_name, $include_in_pages) || in_array('all', $include_in_pages)) {
			if(is_array($do_not_include_in_pages) && in_array($script_name, $do_not_include_in_pages)) 
				return;
			$module_actions[$location][$the_function] = $weight;
		}
	}
}

/**
 * Remove module action
 */
function module_remove_action($location, $the_function)
{
	global  $module_actions;
	if (isset($module_actions[$location][$the_function])) {
		unset($module_actions[$location][$the_function]);
	}
}

function module_add_action_tpl($location, $the_tpl, $weight = array ('weight' => 0) )
{
	
	global $script_name, $module_actions_tpl, $include_in_pages, $do_not_include_in_pages;
	if(is_array($include_in_pages)){
		if (in_array($script_name, $include_in_pages) || in_array('all', $include_in_pages)) {
			if(is_array($do_not_include_in_pages) && in_array($script_name, $do_not_include_in_pages)) 
				return;
			$module_actions_tpl[$location][$the_tpl] = get_module_weight($the_tpl,$weight);
		}
	}
}




function get_module_weight($the_tpl,$weight){
	global $db;
	if($weight['weight']==0){
		$folder_array=explode("/",$the_tpl);
		if($folder_array[2]!=""){
		$mysql="SELECT weight from " .table_modules . " where folder='".$folder_array[2]."' and enabled=1 ";
		$mod_weight = $db->get_var($mysql);
		$weight['weight']=$mod_weight;
		}
		
	}
	
	
	return $weight;
	
}

function module_add_css($the_css, $weight = array ('weight' => 0) )
{
	global $script_name, $module_css, $include_in_pages, $do_not_include_in_pages;
	if(is_array($include_in_pages)){
		if (in_array($script_name, $include_in_pages) || in_array('all', $include_in_pages)) {
			if(is_array($do_not_include_in_pages) && in_array($script_name, $do_not_include_in_pages)) 
				return;
			$module_css[$the_css] = $weight;
		}
	}
}

function module_add_js($the_js, $weight = array ('weight' => 0) )
{
	global $script_name, $module_js, $include_in_pages, $do_not_include_in_pages;
	if(is_array($include_in_pages)){
		if (in_array($script_name, $include_in_pages) || in_array('all', $include_in_pages)) {
			if(is_array($do_not_include_in_pages) && in_array($script_name, $do_not_include_in_pages)) 
				return;
			$module_js[$the_js] = $weight;
		}
	}
}

function check_for_js()
{
	global $module_js, $include_in_pages;
	if( $module_js ){
		uasort($module_js, 'actioncmp');
		foreach ( $module_js as $k => $v){
			echo '<script src="' . $k . '" type="text/javascript"></script>';
		}
	}
}

function check_for_css()
{
	global $module_css, $include_in_pages;
	if( $module_css ){
		uasort($module_css, 'actioncmp');
		foreach ( $module_css as $k => $v){
			echo '<link rel="stylesheet" type="text/css" href="' . $k . '" media="screen" />';
		}
	}
}

function check_actions($location, &$vars)
{
	global $module_actions;
	//$vars['location'] = $location;
	if(!empty($module_actions[$location])){
		uasort($module_actions[$location], 'actioncmp');
		foreach ( $module_actions[$location] as $kk => $vv ) {
			call_user_func_array($kk, array(&$vars));
		}
	}
}

function actioncmp($a, $b)
{
    
	if ($a['weight'] == $b['weight']) {
        return 0;
    }
    return ($a['weight'] < $b['weight']) ? -1 : 1;
}

function check_actions_tpl($location,&$smarty)
{
    	global $module_actions_tpl, $main_smarty, $thetemp;
    
    	$smarty->assign("location",$location);
	if(!empty($module_actions_tpl[$location])){
		uasort($module_actions_tpl[$location], 'actioncmp');
		//$weight=sort_cloumn($module_actions_tpl[$location]);
		//array_multisort($weight, SORT_ASC,  $module_actions_tpl[$location]);

		// Override module templates		
		$path = 'templates/' . $thetemp . '/';
		foreach ( $module_actions_tpl[$location] as $kk => $vv ) {
		    $file = $path . str_replace(array('../','templates/'),'',$kk);
		    if (file_exists($file))
        	        $smarty->display($file);
		    else
        	        $smarty->display($kk);
		}
	}
} 

function sort_cloumn($myArray){
	$sort_numcie=array();
	foreach($myArray as $c=>$key) {
        $sort_numcie[] = $key['weight'];
       }
	   
	   return $sort_numcie;
}

function check_for_enabled_module($name, $version)
{
	global $db;
	
	if($name == 'PHP'){
		if(version_compare(phpversion(), '5.4', '>=')) {
			return $version;
		}
	}else{
		$sql = 'SELECT `id` FROM ' . table_modules . ' where `folder` = "' . $name . '" and `version` >= ' . $version . ' and `enabled` = 1;';
		$theId = $db->get_var($sql);
		return $theId;
	}
}

function check_module_requirements($requires)
{
	if(is_array($requires)){
		foreach($requires as $requirement){
			if(!check_for_enabled_module($requirement[0], $requirement[1])){
				die('This module requires <a href="' . $requirement[2] . '">' . $requirement[0] . '</a> version ' . $requirement[1] . ' or greater');
			}
		}
	}
}

function include_module_settings($module)
{
	if(file_exists(KAHUK_MODULES_DIR . $module . '/' . $module . '_install.php'))
	{
		include(KAHUK_MODULES_DIR . $module . '/' . $module . '_install.php');		
		return $module_info;
	} else {
		return false;
	}
}

function process_db_requirements($module_info){
	global $db;

	$db_add_table = $module_info['db_add_table'];
	if(is_array($db_add_table))	{
		foreach($db_add_table as $table_to_add){
			//print_r($field_to_add);
			if(!check_if_table_exists($table_to_add['name'])){$db->query($table_to_add['sql']);}
		}
	}

	$db_add_field = $module_info['db_add_field'];
	if(is_array($db_add_field))	{
		foreach($db_add_field as $field_to_add){
			//print_r($field_to_add);
			module_db_add_field($field_to_add[0], $field_to_add[1], $field_to_add[2], $field_to_add[3], $field_to_add[4], $field_to_add[5], $field_to_add[6]);
		}
	}

	$db_SQL = $module_info['db_sql'];
	if(is_array($db_SQL))	{
		foreach($db_SQL as $sql){
			//print_r($field_to_add);
			//echo $sql;
			$db->query($sql);
		}
	}
}


// for module installation

function module_db_add_field($field_table, $field_name, $field_type,  $field_length, $field_attributes, $field_null, $field_default){
	//field_table = the table the field will go into, without the prefix, users, comments, votes etc
	//field_name = the name of the field
	//field_type = varchar, text, int etc...
	//field_length = length of the field
	//field_attributes = unsigned etc...
	//field_null = 0=not null, 1=null
	//field_default = default value

	global $db;
	
	if($field_table == 'users'){$field_table = table_users;}

	$fieldexists = checkforfield($field_name, $field_table);
	if (!$fieldexists) {
		$sql = 'ALTER TABLE `' . $field_table . '` ADD `' . $field_name . '` ' . $field_type;
		if($field_length != '') {$sql .= '(' . $field_length . ')';}

		if($field_attributes != '') {$sql .= ' ' . $field_attributes;}
		if($field_null == 0) {$sql .= ' not null';}else{$sql .= ' null';}

		if($field_default != '') {$sql .= " default '" . $field_default . "'";}


		//echo $sql . '<br>';
		$db->query($sql);
	}

}

?>