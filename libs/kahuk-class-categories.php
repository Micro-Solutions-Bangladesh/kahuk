<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes KahukCategories Class
 *
 * @return KahukCategories
 */
function kahuk_categories_init() {
    return KahukCategories::init();
}

/**
 * 
 */
class KahukCategories
{
    public $errors;

    public $cachedItems = []; // Cached categories by category id integer

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->errors = new Kahuk_Error();
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }


    /**
     * 
     */
    function get_sub_items( $id, $result, $outputType = ARRAY_A ) {
        if (!$result) {
            return [];
        }

        $output = [];
        $loopCounter = 0;

        foreach( $result as $row ) {
            if ( $id == $row['category_parent'] ) {
                $output[$loopCounter] = $row;
                $output[$loopCounter]['url'] = kahuk_category_link($row['category_safe_name']);
                $output[$loopCounter]['sub_items'] = $this->get_sub_items( $row['category_id'], $result, $outputType );

                $loopCounter++;
            }            
        }

        return $output;
    }

    /**
     * Retrieve categories from database
     * 
     * @return array
     */
    public function get_categories( $argsCustom = [] ) {
        global $db;

        $defaults = [
            // 'columns_all'   => true,
            'columns'       => [
                'category_id',
                'category_parent',
                'category_name', 'category_safe_name',
                'category_status', 'category_order',
                'category_desc', 'category_keywords',
            ],
            'category_status'        => [
                'enable'
            ],
            'order_by'      => 'category_order ASC',
            // 'output_type'    => ARRAY_A, // OBJECT, ARRAY_N, ARRAY_A

            'is_hierarchical' => true,
            'skip_ids' => ['0'],

            'force_query' => false,
            'do_not_cache' => false,

            'page_size'    => 0,
        ];

        $args = array_merge($defaults, $argsCustom);

        $where = "category_status IN ('" . implode("','", $args['category_status']) . "')";

        if (!empty($args['order_by'])) {
            $where .= ' ORDER BY ' . $args['order_by'];
        }

        $sql = "SELECT * FROM " . table_categories . " WHERE " . $where;

        //
        $pagesize = $args['page_size'];

        if (0 < $pagesize) {
            $offset = (get_current_page() - 1) * $pagesize;
            $sql .= " LIMIT {$offset}, {$pagesize}";
        }

        $result = $db->get_results($sql, ARRAY_A);

        if (!$result) {
            kahuk_log_queries("globalCategoriesObj->get_categories: [EMPTY] \nSQL: {$sql}");
            return [];
        }

        $output = [];
        $counter = 0;

        foreach( $result as $row ) {
            if (in_array($row['category_id'], $args['skip_ids'])) {
                continue;
            }

            if ($args['is_hierarchical'] && (0 < $row['category_parent'])) {
                continue;
            }

            $output[$counter] = $row;
            $output[$counter]['url'] = kahuk_category_link($row['category_safe_name']);

            if ($args['is_hierarchical']) {
                $output[$counter]['sub_items'] = $this->get_sub_items($row['category_id'], $result);
            }

            $this->caching_item($output);

            $counter++;
        }

        return $output;
    }

    /**
     * 
     */
    function get_items( $argsCustom = [] ) {
        return $this->get_categories($argsCustom);
    }

    /**
     * 
     */
    function caching_item( $row ) {
        if (is_array($row) && isset($row['category_id'])) {
            $this->cachedItems[$row['category_id']] = $row;
        }        
    }

    /**
     * Retrieve category from database
     * 
     * @return array
     */
    public function get_category( $argsCustom = [] ) {
        global $db;

        $defaults = [
            'category_id'         => '',
            'category_safe_name' => '',

            'category_status' => [],
        ];

        $args = array_merge($defaults, $argsCustom);
        $where = '';

        if ( !empty( $args['category_id'] ) ) {
            $where = "WHERE category_id = '" . $db->escape( $args['category_id'] ) . "'";
        }

        if ( empty($where) && !empty($args['category_safe_name']) ) {
            $where = "WHERE category_safe_name = '" . $db->escape( $args['category_safe_name'] ) . "'";
        }

        if ( empty($where) ) {
            return [];
        }

        if (!empty($args['category_status'])) {
            $where .= " AND category_status IN ('" . implode("','", $args['category_status']) . "')";
        }

        $sql = "SELECT * FROM " . table_categories . " " . $where;

        $output = $db->get_row($sql, ARRAY_A);

        if ($output) {
            $output['url'] = kahuk_category_link($output['category_safe_name']);
            $this->caching_item($output);
        } else {
            kahuk_log_queries("globalCategoriesObj->get_category: [EMPTY] \nSQL: {$sql}");
            $output = [];
        }

        return $output;
    }

    /**
     * Check the user in $cachedItems variable first
     * If not exist in $cachedItems; consider query to the database
     * 
     * @return array
     */
    public function get_item( $argsCustom = [] ) {
        $output = [];

        $defaults = [
            'category_id' => '',

            'force_query' => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        $id = $args['category_id'];

        if (empty($id)) {
            kahuk_log_queries("globalCategoriesObj->get_item: [{$id}] ID NOT FOUND for category.");
            return $output;
        }

        if (
            $args['force_query'] || !isset($this->cachedItems[$id])
        ) {
            $output = $this->get_category($args);
        } else {
            if ($this->cachedItems[$id]) {
                $output = $this->cachedItems[$id];
            }
        }

        return $output;
    }


    /**
     * Check the category in $cachedItems variable first
     * If not exist; consider query to the database
     * 
     * @return array
     */
    public function get_item_by_slug( $slug, $argsCustom = [] ) {
        $output = [];

        $defaults = [
            'force_query' => false,
            "debug" => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        $output = [];

        foreach($this->cachedItems as $row) {
            if ($row['category_safe_name'] == $slug) {
                $output = $row;
                break;
            }
        }

        if (!$output) { // Need to query DB
            $args["category_safe_name"] = $slug;

            $output = $this->get_category($args);
        }

        return $output;
    }


    /**
     * Retrieve the single category
     * 
     * @return array
     */
    public function get_global_category() {
        $output = [];
        $pagename = kahuk_get_pagename();
        $slug = kahuk_create_slug_story(get_page_slug());

        if (empty($slug)) {
            return $output;
        }

        if (in_array($pagename, ['category'])) {
            $args = [
                'category_safe_name' => $slug,
            ];

            if (!kahuk_check_user_role("admin")) {
                $args['category_status'] = ['enable'];
            }

            $output = $this->get_category($args);
        }

        return $output;
    }
}

global $globalCategoriesObj, $globalCategory, $globalCategoryStatuses;

// Set the global variables to access categories class
$globalCategoriesObj = kahuk_categories_init();

// Create global variable for single category
$globalCategory = $globalCategoriesObj->get_global_category();

// Allowed Category Statuses
$globalCategoryStatuses = ['enable', 'disable', 'hidden'];
