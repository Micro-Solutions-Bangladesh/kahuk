<?php

if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

// taken from http://www.sitepoint.com/article/hierarchical-data-database and modified
function rebuild_tree($parent, $left, $table, $key_name, $parent_name) {
	global $db;
	if (!is_numeric($parent)) die();

	// the right value of this node is the left value + 1
	$right = $left+1;

	// get all children of this node
	$sql = 'SELECT * FROM `'.$table.'` WHERE `'.$parent_name.'`='.$parent.' and category_enabled = 1 ORDER BY category_order;';
	$result = $db->get_results($sql);

	if($result){
		foreach ($result as $row){
			// recursive execution of this function for each
			// child of this node
			// $right is the current right value, which is
			// incremented by the rebuild_tree function
			$right = rebuild_tree($row->$key_name, $right, $table, $key_name, $parent_name);
		}
	}

	// we've got the left value, and now that we've processed
	// the children of this node we also know the right value
	$db->query('UPDATE `'.$table.'` SET lft='.$left.', rgt='.$right.' WHERE `'.$key_name.'`='.$parent.';');

	// return the right value of this node + 1
	return $right+1;
}

function children_id_to_array(&$child_array, $table, $parent) {
	global $db;
	if (!is_numeric($parent)) die();

	// retrieve all children of $parent
	$sql = 'SELECT category__auto_id FROM '.$table.' WHERE category_parent="'.$parent.'" and category__auto_id <> 0;';
	$result = $db->get_results($sql);

	if($result){
        $i = 0;
		foreach ($result as $row){
			$child_array[$i] = $row->category__auto_id;

			// call this function again to display this child's children
			children_id_to_array($child_array, $table, $row->category__auto_id);
            $i++;
		}
	}
}

function GetCatName($catid){
	global $db, $dblang, $the_cats;

	foreach($the_cats as $cat){
		if($cat->category_id == $catid && $cat->category_lang == $dblang)
		{
			$x = $cat->category_name;
		}
	}
	return $x;
}

function rebuild_the_tree(){
	rebuild_tree(0, 0, table_categories, "category__auto_id", "category_parent");
}

function GetLastCategoryOrder($catParentId){
	global $db;
	if (!is_numeric($catParentId)) die();
	
	$sql = "SELECT MAX(category_order) FROM ".table_categories." where category_parent = ".$catParentId.";";
	//echo $sql;
	$MaxOrder = $db->get_var($sql);
	//echo $MaxOrder;
	return $MaxOrder;
}

function get_cached_category_data($field, $value){
	global $cached_categories;

	foreach($cached_categories as $cat){
		if($cat->$field == $value)
		{ 
			return $cat;
		}
	}
}

function get_cached_between($lft, $rgt){
	global $cached_categories;
	$results = array();

	foreach($cached_categories as $cat){
		if($cat->lft >= $lft && $cat->rgt <= $rgt)
		{ 
			$results[] = $cat;
		}
	}
	return $results;
}


function GetSubCatCount($catid){
	/* Redwine added gloabal $dblang below to eliminate all the Notice: Undefined variable: dblang and make the function work as intended */
	global $db, $dblang, $the_cats;

	$count = 0;

	foreach($the_cats as $cat){
		if(isset($cat->category_parent)){
			if($cat->category_parent == $catid && $cat->category__auto_id <> 0 && $cat->category_lang == $dblang)
			{ 
				$count = $count + 1;
			}
		}
	}

	return $count;
}

function OrderNew(){
	global $db;
	$cateogories = $db->get_results("SELECT * FROM ".table_categories.";");
	if ($cateogories) {
		foreach($cateogories as $category) {
			$sub_cateogories = $db->get_results("SELECT * FROM ".table_categories." where category_parent = ".$category->category__auto_id." and category_order = 0 AND category__auto_id<>0;");
			if ($sub_cateogories) {
				if(count($sub_cateogories) > 1){
					$OrderNum = GetLastCategoryOrder($category->category__auto_id);
					foreach($sub_cateogories as $sub_category) {
						$OrderNum = $OrderNum + 1;
						//echo $sub_category->category_name.'-'.$sub_category->category_order."<BR>";
						$sql = "Update ".table_categories." set category_order = " . $OrderNum . " where category__auto_id = ".$sub_category->category__auto_id.";";
						//echo $sql . "<BR>";
						$db->query($sql);
					}
					//echo "<hr>";
				}
			}
		}
	}
}

// function Cat_Safe_Names has been moved to admin_categories.php

?>