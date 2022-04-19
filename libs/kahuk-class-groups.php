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
            'columns_all'   => false,
            'columns'       => [
                'group_id',
                'group_creator', 'group_status', 
                'group_members', 'group_date', 
                'group_name', 'group_safename', 
                'group_description', 'group_privacy', 
                'group_avatar', 
                'group_vote_to_publish', 
                'group_field1', 'group_field2',
                'group_field3', 'group_field4', 
                'group_field5', 'group_field6', 
                'group_notify_email',
            ],
            'columns_minimum'       => [
                'group_id',
                'group_creator', 'group_status', 
                'group_members', 'group_date', 
                'group_name', 'group_safename', 
                'group_description', 'group_privacy', 
                'group_avatar',
            ],
            'status'        => [
                'Enable'
            ],
            'order_by'      => 'group_members DESC, group_date DESC',
            'output_type'    => 'array', // or object
            'debug'         => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        $pagesize = ((0 < $args['page_size']) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        // $orederby = ((empty($args['order_by']) ? '' : 'OREDE BY ' . $args['order_by']));

        $where = "group_status IN ('" . implode("','", $args['status']) . "')";

        if (!empty($args['order_by'])) {
            $where .= ' ORDER BY ' . $args['order_by'];
        }

        
        $sql = "SELECT " . implode(',', $args['columns']);

        if ($args['columns_all']) {
            $sql = "SELECT *";
        }

        $sql .= " FROM " . table_groups . " WHERE " . $where . " LIMIT {$offset}, {$pagesize}";

        $output = $db->get_results($sql);

        if ('array' == $args['output_type']) {
            $outputColumns = $args['columns_minimum'];

            if ($args['columns_all']) {
                $outputColumns = $args['columns'];
            }

            $output = kahuk_db_object_to_array($output, $outputColumns);
        }

        if ($args['debug']) {
            echo "<pre>SQL: {$sql}<br>";
            print_r($args);
            print_r($output);
            echo "</pre>";
        }

        $this->caching_items( $output );

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

            "debug" => false,
        ];

        $args = array_merge($defaults, $argsCustom);
        $where = '';

        if ( !empty( $args['group_id'] ) ) {
            $where = "WHERE group_id = '" . $db->escape( $args['group_id'] ) . "'";
        }

        if ( empty($where) ) {
            return [];
        }

        $sql = "SELECT * FROM " . table_groups . " " . $where;

        $output = $db->get_row( $sql, ARRAY_A );

        if ($args['debug']) {
            echo "<pre class=\"debug\">SQL: {$sql}<br>";
            print_r($args);
            print_r($output);
            echo "</pre>";
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

        $defaults = [
            "debug" => false,
        ];

        $args = array_merge($defaults, $argsCustom);

        if ( isset( $this->cachedItems[$id] ) ) {
            $output = $this->cachedItems[$id];

            if ($args['debug']) {
                echo "<p class=\"debug\">Group returned from cache</p>";
            }
        } else {
            $args["group_id"] = $id;

            $output = $this->get_group($args);

            if ($output) {
                $this->cachedItems[$id] = $output;
            }

            if ($args['debug']) {
                echo "<p class=\"debug\">Group returned from database</p>";
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
            'status'       => [
                'Enable'
            ],
            'debug'        => false,
        ];

        $args = array_merge($defaults, $customArgs);

        $where = "";

        if (!empty($args['status'])) {
            $where = "group_status IN ('" . implode("','", $args['status']) . "')";
        }

        return $db->count_rows(table_groups, 'group_id', $where, $args['debug']);
    }

    /**
     * 
     */
    public function check_group_name($group_name) {
        $group_name = sanitize_text_field($group_name);

        if (kahuk_word_count($group_name) < MIN_NUMBER_OF_WORD_GROUP_NAME) {
            $this->errors->add( 'min-word-name', "Minimum " . MIN_NUMBER_OF_WORD_GROUP_NAME . " word required for group name." );
            
            return $this->errors;
        }

        if (kahuk_word_count($group_name) > MAX_NUMBER_OF_WORD_GROUP_NAME) {
            $this->errors->add( 'max-word-name', "Maximum " . MAX_NUMBER_OF_WORD_GROUP_NAME . " word for group name." );
            
            return $this->errors;
        }

        return $group_name;
    }

    /**
     * 
     */
    public function check_group_safename($group_safename, $group_id = 0) {
        global $db;

        $group_safename = sanitize_title($group_safename);

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

        if (kahuk_word_count($group_desc) < MIN_NUMBER_OF_WORD_GROUP_DESC) {
            $this->errors->add( 'min-word-desc', "Minimum " . MIN_NUMBER_OF_WORD_GROUP_DESC . " word required for group description." );

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
    public function update($data, $group_id, $debug = false) {
        global $db;

        $sql = "update " . table_groups . " set";

        foreach($data as $column => $value) {
            $sql .= " " . $column . " = '" . $db->escape($value) . "',";
        }

        $sql = trim($sql, ",");

        if ($group_id>0) {
            $sql .= " WHERE group_id = '" . $db->escape($group_id) . "'";

            if ($debug) {
                echo "<pre class=\"debug\">SQL: $sql</pre>";
            }

            return $db->query($sql);
        } else {
            return false;
        }
    }
}

// Set the global variables to access groups class
$globalGroups = kahuk_groups_init();
