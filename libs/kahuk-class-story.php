<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes KahukStories Class
 *
 * @return KahukStories
 */
function kahuk_stories_init() {
    return KahukStories::init();
}

/**
 * 
 */
class KahukStories
{
    public $errors;

    public $rowCount = 0; // Integer number of total rows found

    public $itemsQuery = []; // SQL query to get the number of rows

    public $cachedItems = []; // Cached stories by story id integer from the the `$itemsQuery` query

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
    public function tableColumns() {
        return [
            'links.link_id',
            'links.link_status',
            'links.link_randkey',
            'links.link_votes',
            'links.link_karma',
            'links.link_modified',
            'links.link_date',
            'links.link_published_date',
            'links.link_category',
            'links.link_url', 
            'links.link_title', // story title
            'links.link_title_url', // Slug
            'links.link_content',
            'links.link_summary',
            'links.link_out',

            'links.link_author', 
            'users.user_login',
            'users.user_email',
            'users.user_names',
            'users.user_karma',

            'links.link_group_id',
            'grps.group_safename',
            'grps.group_name',
            'grps.group_status',
        ];
    }

    /**
     * Build query 
     */
    public function build_query($argsCustom) {
        global $db, $page_size;

        $defaults = [
            'columns'       => [],

            'link_status' => [
                'published',
            ],

            'where_clause' => '',

            'link_id' => 0, // Unique
            'link_title_url' => '', // Unique

            'link_author' => 0, // Foreign Key
            'link_group_id' => 0, // Foreign Key

            'count_query' => false,

            "pagination_enabled" => true,
            'page_size'    => 0,

            'force_limit' => 0, // Force the rows limit over the global page_size from admin config

            'order_by'      => 'links.link_date DESC, links.link_karma DESC',

            'output_type'    => 'array', // or object
        ];

        $args = array_merge($defaults, $argsCustom);

        // echo "<pre>";
        // print_r($args);
        // echo "</pre>";

        if (empty($args['columns'])) {
            $args['columns'] = $this->tableColumns();
        }

        $count_query = $args['count_query'];
        $pagesize = (($args['page_size'] > 0) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        //
        $sql = "SELECT " . implode(',', $args['columns']);

        if ($count_query) {
            $sql = "SELECT count(DISTINCT links.link_id)";
        }

        $sql .= " FROM " . table_links . " AS links
            LEFT JOIN " . table_users . " AS users
            ON (links.link_author = users.user_id)
            LEFT JOIN " . table_groups . " AS grps
            ON (links.link_group_id = grps.group_id)
        ";

        $link_statuses = $args['link_status'];

        $where = "WHERE links.link_status IN ('" . implode("','", $link_statuses) . "')";


        if ($args['link_id'] > 0) {
            $where .= " AND links.link_id = " . $db->escape($args['link_id']);
        } else if ($args['link_title_url']) {
            $where .= " AND links.link_title_url = '" . $db->escape($args['link_title_url']) . "'";
        } else {            
            //
            if ($args['link_author'] > 0) {
                $where .= " AND links.link_author = " . $db->escape($args['link_author']);
            }

            if ($args['link_group_id'] > 0) {
                $where .= " AND links.link_group_id = " . $db->escape($args['link_group_id']);
            }

            if ($args['where_clause']) {
                $where .= " AND " . $args['where_clause'];
            }
        }

        if (!$count_query && !$args['link_id'] && !$args['link_title_url']) {
            if (!empty($args['order_by'])) {
                $where .= " ORDER BY " . $args['order_by'];
            }

            if ($args["pagination_enabled"]) {
                $where .= " LIMIT {$offset}, {$pagesize}";
            }
        }

        if ($where) {
            $sql .= " " . $where;
        }

        // echo "<pre>{$sql}</pre>";

        return $sql;
    }

    /**
     * Get the stories by Group ID
     * 
     * @return array
     */
    public function get_shared_stories($argsCustom = []) {
        global $db, $page_size;
    
        $defaults = [
            "page_size" => $page_size,
        ];
        $args = array_merge($defaults, $argsCustom);

        $group_id = sanitize_number($args['group_id']);
        $count_query = sanitize_number($args['count_query']);
        $pagesize = sanitize_number($args["page_size"]);
        $columns = $this->tableColumns();
        $offset = (get_current_page() - 1) * $pagesize;
        $sql = "";

        if ($count_query) {
            $sql = "SELECT count(DISTINCT gs.share_id)";
        } else {
            $sql = "SELECT " . implode(',', $columns);
        }

        $sql .= " FROM " . table_group_shared . " gs";
        $sql .= " LEFT JOIN " . table_groups . " grps ON gs.share_group_id = grps.group_id";
        $sql .= " LEFT JOIN " . table_links . " links ON gs.share_link_id = links.link_id";
        $sql .= " LEFT JOIN " . table_users . " users ON gs.share_user_id = users.user_id";

        $sql .= " WHERE gs.share_group_id = {$group_id}";

        if ($count_query) {
            // echo "<pre>get_shared_stories count:: {$sql}</pre>";
            $rowCount = $db->get_var($sql);

            if (!$rowCount) {
                kahuk_log_queries("globalStories->get_shared_stories [OUTPUT: FALSY] SQL: {$sql}");
                $rowCount = 0;
            }

            return $rowCount;
        } else {
            $sql .= " ORDER BY links.link_date DESC LIMIT {$offset}, {$pagesize}";

            // echo "<pre>get_shared_stories:: {$sql}</pre>";
            $output = $db->get_results($sql, ARRAY_A);

            if (!$output) {
                $output = [];
            }

            return $output;
        }        
    }

    /**
     * Count number of rows in table
     * 
     * @return array
     */
    public function get_count($argsCustom = []) {
        global $db;

        $argsCustom['count_query'] = true;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_count: {$sql}</pre>";

        $output = $db->get_var($sql);

        if (!$output) {
            kahuk_log_queries("globalStories->get_count [OUTPUT: NULL] SQL: {$sql}");
            $output = 0;
        }

        return $output;
    }

    /**
     * Retrieve stories from database
     * 
     * @return array
     */
    public function get_stories( $argsCustom = [] ) {
        global $db;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_stories: {$sql}</pre>";

        //
        $output = $db->get_results($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalStories->get_stories [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
    }

    /**
     * Retrieve story
     * 
     * @return array
     */
    public function get_story($args = []) {
        global $db;

        $sql = $this->build_query($args);
        // echo "<pre>get_stories: {$sql}</pre>";

        //
        $output = $db->get_row($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalStories->get_story [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
    }


    /**
     * 
     */
    function caching_items( $rows ) {
        if (is_array($rows)) {
            foreach($rows as $row) {
                $this->cachedItems[$row["link_id"]] = $row;
            }
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

            if (isset($args['link_status'])) {
                if (!in_array($output['link_status'], $args['link_status'])) {
                    $output = [];
                }
            }

        } else {
            $args["link_id"] = $id;

            $output = $this->get_story($args);
        }

        return $output;
    }


    /**
     * Count the rows of group in table_links table
     * 
     * @since 5.0.4
     * 
     * @return int
     */
    public function count_groups($customArgs = []) {
        global $db;

        $defaults = [
            'status'       => [
                'enable'
            ],
            'debug'        => false,
        ];

        $args = array_merge($defaults, $customArgs);

        $where = "";

        if (!empty($args['status'])) {
            $where = "group_status IN ('" . implode("','", $args['status']) . "')";
        }

        return $db->count_rows(table_links, 'group_id', $where, $args['debug']);
    }

    /**
     * 
     */
    public function check_group_name($group_name) {
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

        return $group_name;
    }

    /**
     * 
     */
    public function check_group_safename($group_safename, $group_id = 0) {
        global $db;

        $group_safename = kahuk_create_slug_story($group_safename);

        $sql = "select COUNT(*) from " . table_links . " WHERE group_safename='{$group_safename}'";

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
    public function update($initialData, $story_id) {
        global $db;

        $defaultData = [
            'link_modified' => 'NOW()',
        ];
    
        $data = array_merge( $defaultData, $initialData );

        $sql = "update " . table_links . " set";

        foreach($data as $column => $value) {
            if ($column == 'link_modified') {
                $sql .= " {$column} = {$value},";
                continue;
            }
            
            $sql .= " " . $column . " = '" . $db->escape($value) . "',";
        }

        $sql = trim($sql, ",");

        if ($story_id>0) {
            $sql .= " WHERE link_id = '" . $db->escape($story_id) . "'";

            return $db->query($sql);
        } else {
            return false;
        }
    }

    /**
     * Retrieve the single story for story or, ... page
     * 
     * @return array
     */
    public function get_global_story($forceId=0) {
        $output = [];
        $pagename = kahuk_get_pagename();

        if ($forceId > 0) {
            $args = ['link_id' => $forceId];
            $output = $this->get_story($args);

        } elseif (in_array($pagename, ['story', 'page', 'story-edit', 'comment-edit'])) {
            $slug = kahuk_create_slug_story(_get('slug'));
            $id = sanitize_number(_get('id'));

            $args = [];

            if ($id) {
                $args['link_id'] = $id;
            } else if ($slug) {
                $args['link_title_url'] = $slug;
            }

            if (!kahuk_check_user_role("admin")) {
                $args['link_status'] = ['published', 'new'];
            }

            if ($pagename == "page") {
                $args['link_status'] = ['page'];
            }

            $output = $this->get_story($args);
        }

        return $output;
    }
}

global $globalStories, $globalStory;

// Set the global variables to access groups class
$globalStories = kahuk_stories_init();

