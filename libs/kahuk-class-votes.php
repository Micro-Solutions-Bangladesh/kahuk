<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes the Class
 *
 * @return KahukVotes
 */
function kahuk_votes_init() {
    return KahukVotes::init();
}

/**
 * Votes Class
 */
class KahukVotes
{
    public $errors;

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
                'vts.vote_id',
                'vts.vote_reaction', 
                'vts.vote_ip', 
                'vts.vote_date', 

                'vts.vote_user_id',
                'users.user_login',
                'users.user_email',
                'users.user_names',

                'vts.vote_link_id', 
                'links.link_title',
                'links.link_title_url',
            ],

            'vote_reaction' => ['positive', 'negative'],

            'order_by' => 'vts.vote_date DESC',

            'count_query' => false,
            
            'page_size'    => 0,

            'vote_id' => 0, // Unique
            'story_id' => 0, // Foreign Key
            'user_id' => 0, // Foreign Key

            'where_clause' => '',
        ];

        $args = array_merge($defaults, $argsCustom);

        $count_query = $args['count_query'];
        $pagesize = (($args['page_size'] > 0) ? $args['page_size'] : $page_size);
        $offset = (get_current_page() - 1) * $pagesize;

        //
        $isSingleQuery = false;

        if (
            $args['vote_id'] || 
            ($args['story_id'] && $args['user_id'])
        ) {
            $isSingleQuery = true;
        }


        //
        $sql = "SELECT *";

        if (!empty($args['columns'])) {
            $sql = "SELECT " . implode(',', $args['columns']);
        }

        if ($count_query) {
            $sql = "SELECT count(vts.vote_id)";
        }

        $sql .= " FROM " . table_votes . " AS vts
            LEFT JOIN " . table_links . " AS links
            ON (vts.vote_link_id = links.link_id)
            LEFT JOIN " . table_users . " AS users
            ON (vts.vote_user_id = users.user_id)
        ";

        $where = "WHERE vts.vote_reaction IN ('" . implode("','", $args['vote_reaction']) . "')";

        if ($args['vote_id']) {
            $where .= " AND vts.vote_id = " . sanitize_number($args['vote_id']);
        
        } else if ($args['story_id'] && $args['user_id']) {
            $where .= " AND vts.vote_link_id = " . $db->escape($args['story_id']);
            $where .= " AND vts.vote_user_id = " . $db->escape($args['user_id']);
        } else {
            if ($args['story_id'] > 0) {
                $where .= " AND vts.vote_link_id = " . $db->escape($args['story_id']);
            }

            if ($args['user_id'] > 0) {                
                $where .= " AND vts.vote_user_id = " . $db->escape($args['user_id']);
            }

            if ($args['where_clause']) {
                $where .= " AND " . $args['where_clause'];
            }
        }

        if (
            !$count_query && !$isSingleQuery
        ) {
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
     * Retrieve votes
     * 
     * @return array
     */
    public function get_items($argsCustom = []) {
        global $db;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_votes: {$sql}</pre>";

        //
        $output = $db->get_results($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalVotesObj->get_items [OUTPUT: NULL] SQL: {$sql}");
        }

        return $output;
    }


    /**
     * Retrieve vote
     * 
     * @return array
     */
    public function get_item($argsCustom) {
        global $db;

        $sql = $this->build_query($argsCustom);
        // echo "<pre>get_vote: {$sql}</pre>";

        //
        $output = $db->get_row($sql, ARRAY_A);

        if (!$output) {
            $output = [];
            kahuk_log_queries("globalVotesObj->get_item [OUTPUT: NULL] SQL: {$sql}");
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
            kahuk_log_queries("globalVotesObj->get_count [OUTPUT: NULL] SQL: {$sql}");
            $output = 0;
        }

        return $output;
    }
}

global $globalVotesObj;

// Set the global variables to access votes class
$globalVotesObj = kahuk_votes_init();

