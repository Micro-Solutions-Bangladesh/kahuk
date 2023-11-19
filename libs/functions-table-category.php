<?php
/**
 * Insert new category
 * 
 * @since 5.0.7
 * 
 * Columns are: 
 * `category_id` int NOT NULL,
 * `category_parent` int NOT NULL DEFAULT '0',
 * `category_name` varchar(64) NOT NULL DEFAULT '',
 * `category_safe_name` varchar(64) NOT NULL DEFAULT '',
 * `category_status` enum('disable','enable','hidden') NOT NULL DEFAULT 'disable',
 * `category_order` int NOT NULL DEFAULT '0',
 * `category_desc` varchar(255) NOT NULL,
 * `category_keywords` varchar(255) NOT NULL
 * 
 * @return array
 */
function kahuk_save_category($data, $catId = 0) {
	global $db, $hooks, $globalCategoryStatuses;

    $error = [
        "status" => false,
    ];

    //
    $category_parent = sanitize_number($data["category_parent"] ?? 0);

    //
    $category_name = ($data["category_name"] ?? "");

    if (!$category_name) {
        $error["message"] = "Category name is empty!";

        return $error;
    }

    //
    $category_safe_name = kahuk_create_slug($data["category_safe_name"] ?? "");

    if (!$category_safe_name) {
        $error["message"] = "Category slug is empty!";

        return $error;
    }

    //
    $category_status = sanitize_text_field($data["category_status"] ?? "");

    if (!in_array($category_status, $globalCategoryStatuses)) {
        $category_status = "disable";
    }

    //
    $category_order = sanitize_number($data["category_order"] ?? 0);

    //
    $category_desc = $data["category_desc"] ?? "";

    if ($category_desc) {
        $html_tags = "<p>,<strong>,<h3>,<h4>,<h5>,<u>"; // TODO create config option
        $category_desc = kahuk_kses($category_desc, $html_tags);
    }

    //
    $category_keywords = sanitize_text_field($data["category_keywords"] ?? "");

    if ($catId == 0) {
        // Insert New Record
        $sql = "INSERT INTO ";
    } else {
        // Update Existing Record
        $sql = "UPDATE ";
    }

    $sql .= TABLE_PREFIX. "categories SET category_parent='{$category_parent}'";
    $sql .= ", category_name='" . $db->escape($category_name) . "'";
    $sql .= ", category_safe_name='" . $db->escape($category_safe_name) . "'";
    $sql .= ", category_status='" . $db->escape($category_status) . "'";
    $sql .= ", category_order='{$category_order}'";
    $sql .= ", category_desc='" . $db->escape($category_desc) . "'";
    $sql .= ", category_keywords='" . $db->escape($category_keywords) . "'";

    //
    $check_duplicate = kahuk_count_group_by_slug($category_safe_name, $catId);

    if ($check_duplicate > 0) {
        $error["message"] = "Duplicate slug for category exist!";

        return $error;
    }

    if ($catId == 0) {
        $id = $db->query_insert($sql);

        if (!$id) {
            kahuk_log_queries("New Record Failed!\nSQL: {$sql}");
            die('Database error!');
        }

        $hooks->apply_filters("saved_category", $id);

        $output = [
            "status" => true,
            "message" => "Category Insert Successful!",
            "id" => $id,
        ];

        

        return $output;
    } else {
        $sql .= " WHERE category_id='{$catId}'";
        $output = $db->query($sql);

        if ($output !== 1) {
            kahuk_log_queries("Update Record Failed!\nSQL: {$sql}");
        }

        $hooks->apply_filters("updated_category", $output);

        $output = [
            "status" => true,
            "message" => "Category Update Successful!",
        ];

        return $output;
    }
}

/**
 * Count category by slug
 * 
 * @since 5.0.7
 * 
 * @return int
 */
function kahuk_count_group_by_slug($slug, $skip_id= 0) {
    global $db;

    $sql = "SELECT count(category_id) FROM " . TABLE_PREFIX . "categories";
    $sql .= " WHERE category_safe_name = '" . $db->escape($slug) . "'";

    //
    if ($skip_id > 0) {
        $sql .= " AND category_id != '" . (int) $skip_id . "'";
    }

    return $db->get_var($sql);
}

/**
 * Get category by id
 * 
 * @since 5.0.6
 */
function kahuk_get_category_by_id( $id ) {
	global $globalCategoriesObj;

    return $globalCategoriesObj->get_item(['category_id' => $id]);
}

/**
 * Get category by id
 * 
 * @since 5.0.6
 */
function kahuk_get_category_name_by_id( $id ) {
	$cat = kahuk_get_category_by_id($id);

    if ($cat) {
        return $cat['category_name'];
    }

    return '';
}
