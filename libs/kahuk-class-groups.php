<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes KahukGroups Class
 *
 * @return KahukGroups
 */
function kahuk_groups_init() {
    return KahukGroups::init();
}

/**
 * 
 */
class KahukGroups
{
    public $errors;

    public $cachedItems = []; // Cached groups by group id integer

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
     * Retrieve groups from database
     * 
     * @return array
     */
    public function get_groups( $argsCustom = [] ) {
        global $db, $page_size;

        $defaults = [
            'columns'       => [
                'group_id',
                'group_creator', 'group_status', 
                'group_members', 'group_date', 
                'group_name', 'group_safename', 
                'group_description', 'group_privacy', 
                'group_avatar', 
                'group_field1', 'group_field2',
                'group_field3', 'group_field4', 
                'group_field5', 'group_field6', 
            ],

            'group_status'        => [
                'enable'
            ],
            'order_by'      => 'group_status ASC, group_members DESC, group_date DESC',
            'output_type'    => 'array', // or object

            'where_clause' => '',

            'page_size' => 0,
        ];

        $args = array_merge($defaults, $argsCustom);

        $pagesize = ((0 < $args['page_size']) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        //
        $where = "group_status IN ('" . implode("','", $args['group_status']) . "')";

        if (!empty($args['where_clause'])) {
            $where .= $args['where_clause'];
        }

        if (!empty($args['order_by'])) {
            $where .= ' ORDER BY ' . $args['order_by'];
        }

        $sql = "SELECT *";

        if (!empty($args['columns'])) {
            $sql = "SELECT " . implode(',', $args['columns']);
        }

        $sql .= " FROM " . table_groups . " WHERE " . $where . " LIMIT {$offset}, {$pagesize}";

        $output = $db->get_results($sql, ARRAY_A);

        if (!$output) {
            kahuk_log_queries("globalGroupsObj->get_groups [OUTPUT: NULL]\nSQL: {$sql}");
            $output = [];
        } else {
            $this->caching_items( $output );
        }

        return $output;
    }

    /**
     * Retrieve group from database
     * 
     * @return array
     */
    public function get_group( $argsCustom = [] ) {
        global $db;

        $defaults = [
            'group_id'         => '',
            'group_safename' => '',
        ];

        $args = array_merge($defaults, $argsCustom);
        $where = '';

        // echo "<pre>get_group() 1</pre>";

        if ( !empty( $args['group_id'] ) ) {
            $where = "WHERE group_id = '" . $db->escape( $args['group_id'] ) . "'";
        } else if ( !empty( $args['group_safename'] ) ) {
            $where = "WHERE group_safename = '" . $db->escape( $args['group_safename'] ) . "'";
        }

        if ( empty($where) ) {
            return [];
        }

        $sql = "SELECT * FROM " . table_groups . " " . $where;

        $output = $db->get_row($sql, ARRAY_A);

        if (!$output) {
            kahuk_log_queries("globalGroupsObj->get_group [OUTPUT: NULL]\nSQL: {$sql}");
            $output = [];
        }

        return $output;
    }

    /**
     * 
     */
    function caching_items( $groups ) {
        foreach($groups as $row) {
            $this->cachedItems[$row["group_id"]] = $row;
        }
    }

    /**
     * Check the user in $cachedItems variable first
     * If not exist in $cachedItems; consider query to the database
     * 
     * @return array
     */
    public function get_item( $id, $argsCustom = [] ) {
        $output = [];

        $defaults = [];

        $args = array_merge($defaults, $argsCustom);

        if ( isset( $this->cachedItems[$id] ) ) {
            $output = $this->cachedItems[$id];
        } else {
            $args["group_id"] = $id;

            $output = $this->get_group($args);

            if ($output) {
                $this->cachedItems[$id] = $output;
            }
        }

        return $output;
    }


    /**
     * Count the rows of group in table_groups table
     * 
     * @since 5.0.4
     * 
     * @return int
     */
    public function count_groups($customArgs = []) {
        global $db;

        $defaults = [
            'group_status'       => [
                'enable'
            ],

            'where_clause' => '',
        ];

        $args = array_merge($defaults, $customArgs);

        $where = "";

        if (!empty($args['group_status'])) {
            $where = "group_status IN ('" . implode("','", $args['group_status']) . "')";
        }

        if (!empty($args['where_clause'])) {
            $where .= $args['where_clause'];
        }

        $rs = $db->count_rows(table_groups, 'group_id', $where);

        return $rs;
    }

    /**
     * 
     */
    public function check_group_name($group_name) {
        global $db;

        $group_name = sanitize_text_field($group_name);
        $minWordsGroupName = kahuk_get_config("_min_words_group_name", "2", "number");
        $maxWordsGroupName = kahuk_get_config("_max_words_group_name", "10", "number");

        if (kahuk_word_count($group_name) < $minWordsGroupName) {
            $this->errors->add( 'min-word-name', "Minimum {$minWordsGroupName} word required for group name." );
            
            return $this->errors;
        }

        if (kahuk_word_count($group_name) > $maxWordsGroupName) {
            $this->errors->add( 'max-word-name', "Maximum {$maxWordsGroupName} word for group name." );
            
            return $this->errors;
        }

        //
        $rs = $db->count_rows(table_groups, 'group_id', "group_name = '{group_name}'");

        if ($rs > 0) {
            $this->errors->add( 'max-word-name', kahuk_language_config('KAHUK_Visual_Group_Title_Exists'));
            
            return $this->errors;
        }

        return $group_name;
    }

    /**
     * 
     */
    public function check_group_safename($group_safename, $group_id = 0) {
        global $db;

        $group_safename = kahuk_create_slug_story($group_safename);

        if (empty($group_safename)) {
            $this->errors->add( 'group-slug-invalid', "Group slug require word(s)." );
            
            return $this->errors;
        }

        $sql = "select COUNT(*) from " . table_groups . " WHERE group_safename='{$group_safename}'";

        if ($group_id > 0) {
            $sql .= " AND group_id != '{$group_id}'";
        }

        $exists = $db->get_var($sql);

        if ($exists) {
            $this->errors->add( 'group-slug-exist', "Group slug `{$group_safename}` exist." );
            
            return $this->errors;
        }

        return $group_safename;
    }

    /**
     * 
     */
    public function check_group_description($group_desc) {
        $group_desc = trim($group_desc);
        $minWordsGroupDesc = kahuk_get_config("_min_words_group_desc", "30", "number");

        if (kahuk_word_count($group_desc) < $minWordsGroupDesc) {
            $this->errors->add( 'min-word-desc', "Minimum {$minWordsGroupDesc} word required for group description." );

            return $this->errors;
        }

        if (kahuk_word_count($group_desc) > MAX_NUMBER_OF_WORD_GROUP_DESC) {
            $this->errors->add( 'max-word-desc', "Maximum " . MAX_NUMBER_OF_WORD_GROUP_DESC . " word for group description." );
            
            return $this->errors;
        }

        return kahuk_slashes(kahuk_kses($group_desc, ["b","strong"]));
    }

    /**
     * 
     */
    function insert($initialData) {
        global $db;

        $defaultData = [
            'group_creator' => 0,
            'group_status' => 'disable',
            'group_members' => 1,
            'group_date' => 'NOW()',
            'group_safename' => '',
            'group_name' => '',
            'group_description' => '',
            'group_privacy' => 'restricted',
        ];
    
        $data = array_merge( $defaultData, $initialData );
        $columns = '';
        $values = '';

        foreach($data as $column => $value) {
            $isError = false;

            $requiredFields = [
                'group_status', 'group_date', 'group_safename',
                'group_name', 'group_description', 'group_privacy',
            ];
            if (in_array($column, $requiredFields) && empty($value)) {
                $isError = true;
            }

            //
            $requiredFieldsNumber = [
                'group_creator', 'group_members',
            ];
            if (in_array($column, $requiredFieldsNumber) && ($value < 1)) {
                $isError = true;
            }

            if ($isError) {
                kahuk_log_queries("KahukGroups->insert [{$column} = {$value}] " . print_r($data, true));
                return false;
            }

            $columns .= $column . ",";

            //
            if (in_array($column, ['group_date'])) {
                $values .= $db->escape($value) . ",";
            } else {
                $values .= "'" . $db->escape($value) . "',";
            }
            
        }

        $columns = trim($columns, ",");
        $values = trim($values, ",");


        $sql = "INSERT IGNORE INTO " . table_groups . " ({$columns}) VALUES ({$values})";

        $output = $db->query( $sql );

        if ($output) {
            return $db->insert_id;
        }

        return false;
    }

    /**
     * 
     */
    public function update($data, $group_id) {
        global $db;

        $sql = "UPDATE " . table_groups . " set";

        foreach($data as $column => $value) {
            $sql .= " " . $column . " = '" . $db->escape($value) . "',";
        }

        $sql = trim($sql, ",");

        if ($group_id>0) {
            $sql .= " WHERE group_id = '" . $db->escape($group_id) . "'";

            return $db->query($sql);
        } else {
            return false;
        }
    }

    /**
     * Retrieve the group for group_story or, editgroup page
     * 
     * @return array
     */
    public function get_global_group($forceId = 0) {
        $output = [];
        $pagename = kahuk_get_pagename();
        $gId = sanitize_number(_request("gId", '0'));

        $pages = [
            'group',
            'group-edit', 'group-join', 'group-unjoin', 'group-share', 
            'group-withdraw', 'group-delete', 'group-update',
        ];

        if ($forceId > 0) {
            $args = ['group_id' => $forceId];
            $output = $this->get_group($args);
        } else if ($gId > 0) {
            $args = ['group_id' => $gId];
            $output = $this->get_group($args);
        } else if (in_array($pagename, $pages)) {
            $slug = kahuk_create_slug_story(_get('slug'));
            $id = sanitize_number(_get('id'));

            $args = [];

            if ($id) {
                $args['group_id'] = $id;
            } else if ($slug) {
                $args['group_safename'] = $slug;
            }

            $output = $this->get_group($args);
        }

        return $output;
    }
}

global $globalGroupsObj, $globalGroup, $globalGroupPrivacies, $globalGroupStatuses, $globalGroupTasks;

// Set the global variables to access groups class
$globalGroupsObj = kahuk_groups_init();

// Create global group variable
$globalGroup = $globalGroupsObj->get_global_group();

// Allowed Group privacies
$globalGroupPrivacies = ['public', 'private', 'restricted'];

// Allowed Group privacies
$globalGroupStatuses = ['pending','enable','disable'];

// Only the following actions are allowed through url for group
$globalGroupTasks = ['edit', 'join', 'unjoin', 'withdraw', 'delete', 'approve'];
