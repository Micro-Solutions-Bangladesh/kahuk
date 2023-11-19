<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes the Class
 *
 * @return KahukComments
 */
function kahuk_comments_init() {
    return KahukComments::init();
}

/**
 * Comments Class
 */
class KahukComments
{
    public $errors;

    // Cached items by id integer
    public $cachedItems = [];

    /**
     * Class constructor
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
     * Build query
     * 
     * @return string
     */
    function build_query($argsCustom = []) {
        global $db, $page_size;

        $defaults = [
            // 'columns_all'   => true,
            'columns'       => [
                'cmnts.comment_id',
                'cmnts.comment_parent',
                'cmnts.comment_date', 
                'cmnts.comment_content',
                'cmnts.comment_status',

                'cmnts.comment_user_id',
                'users.user_login',
                // 'users.user_level',
                // 'users.user_status',
                'users.user_email',
                'users.user_names',
                'users.user_karma',
                // 'users.user_url',

                'cmnts.comment_link_id', 
                'links.link_title',
                'links.link_title_url',
            ],

            'order_by' => 'cmnts.comment_date DESC',

            'count_query' => false,

            'comment_status' => ['published'],
            
            'page_size'    => 0,

            'comment_id' => 0,

            'story_id' => 0,

            'where_clause' => '',
        ];

        $args = array_merge($defaults, $argsCustom);

        $count_query = $args['count_query'];
        $pagesize = (($args['page_size'] > 0) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        //
        $sql = "SELECT *";

        if (!empty($args['columns'])) {
            $sql = "SELECT " . implode(',', $args['columns']);
        }

        if ($count_query) {
            $sql = "SELECT count(cmnts.comment_id)";
        }

        $sql .= " FROM " . table_comments . " AS cmnts
            LEFT JOIN " . table_links . " AS links
            ON (cmnts.comment_link_id = links.link_id)
            LEFT JOIN " . table_users . " AS users
            ON (cmnts.comment_user_id = users.user_id)
        ";

        $where = '';

        if ($args['comment_id'] > 0) {
            $sql .= " WHERE cmnts.comment_id = " . $db->escape($args['comment_id']);
        } else {
            //
            $where = "WHERE cmnts.comment_status IN ('" . implode("','", $args['comment_status']) . "')";

            if ($args['story_id'] > 0) {
                $where .= " AND cmnts.comment_link_id = " . $db->escape($args['story_id']);
            }

            if ($args['where_clause']) {
                $where .= " AND " . $args['where_clause'];
            }
        }

        if (!$count_query && !$args['comment_id']) {
            if (!empty($args['order_by'])) {
                $where .= " ORDER BY " . $args['order_by'];
            }

            $where .= " LIMIT {$offset}, {$pagesize}";
        }

        if ($where) {
            $sql .= " " . $where;
        }

        return $sql;
    }


    /**
     * Retrieve comments
     * 
     * @return array
     */
    public function get_comments($argsCustom = []) {
        global $db;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_comments: {$sql}</pre>";

        //
        $output = $db->get_results($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalCommentsObj->get_comments [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
    }


    /**
     * Retrieve comment
     * 
     * @return array
     */
    public function get_comment($comment_id) {
        global $db;

        $sql = $this->build_query(['comment_id' => $comment_id]);
        // echo "<pre>get_comment: {$sql}</pre>";

        //
        $output = $db->get_row($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalCommentsObj->get_comments [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
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
            kahuk_log_queries("globalCommentsObj->get_count [OUTPUT: NULL] SQL: {$sql}");
            $output = 0;
        }

        return $output;
    }
}

global $globalCommentsObj;

// Set the global variables to access comment class
$globalCommentsObj = kahuk_comments_init();

