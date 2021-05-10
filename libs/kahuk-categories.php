<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * TODO Check
 */
function kahuk_insert_sql_category( $data ) {
    global $db;

    $columnsList = "";
    $valuesList = "";

    if ( isset($data['link_author']) ) {
        $columnsList = "link_author";
        $valuesList = $data['link_author'];
    } else {
        return false;
    }

    $sql = "INSERT INTO `" . table_categories . "` SET category_name='" . $db->escape( sanitize_text_field( $data['category_name'] ) ) . "'";

    $dblang = ( isset( $data['category_lang'] ) ) ? $db->escape( sanitize_text_field( $data['category_lang'] ) ) : "en";
    $sql .= ", category_lang='$dblang'";

    // $sql .= ", category_id=0";

    $category_parent = ( isset( $data['category_parent'] ) ) ? $db->escape( sanitize_text_field( $data['category_parent'] ) ) : '0';
    $sql .= ", category_parent='$category_parent'";

    $sql .= ", category_safe_name='" . $db->escape( sanitize_title( $data['category_safe_name'] ) ) . "'";


    $sql .= ", rgt=0";
    $sql .= ", lft=0";

    //
    $category_enabled = ( isset( $data['category_enabled'] ) ) ? $db->escape( sanitize_text_field( $data['category_parent'] ) ) : '1';
    $sql .= ", category_enabled='$category_enabled'";

    //
    $category_order = ( isset( $data['category_order'] ) ) ? $db->escape( sanitize_text_field( $data['category_order'] ) ) : '99';
    $sql .= ", category_order='$category_order'";

    //
    $category_desc = ( isset( $data['category_desc'] ) ) ? $db->escape( sanitize_text_field( $data['category_desc'] ) ) : '';
    $sql .= ", category_desc='$category_desc'";

    //
    $category_keywords = ( isset( $data['category_keywords'] ) ) ? $db->escape( sanitize_text_field( $data['category_keywords'] ) ) : '';
    $sql .= ", category_keywords='$category_keywords'";

    //
    $category_author_level = ( isset( $data['category_author_level'] ) ) ? $db->escape( sanitize_text_field( $data['category_author_level'] ) ) : 'normal';
    $sql .= ", category_author_level='$category_author_level'";

    //
    $category_author_group = ( isset( $data['category_author_group'] ) ) ? $db->escape( sanitize_text_field( $data['category_author_group'] ) ) : '';
    $sql .= ", category_author_group='$category_author_group'";

    //
    $category_votes = ( isset( $data['category_votes'] ) ) ? $db->escape( sanitize_text_field( $data['category_votes'] ) ) : '';
    $sql .= ", category_votes='$category_votes'";

    //
    $category_karma = ( isset( $data['category_karma'] ) ) ? $db->escape( sanitize_text_field( $data['category_karma'] ) ) : '';
    $sql .= ", category_karma='$category_karma'";

    return $sql;
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


    public $rawItems;
    public $items;
    public $indexedItems = [];

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->set_items();
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
    function get_item( $id ) {
        $output = [];

        if ( isset( $this->indexedItems[$id] ) ) {
            $output = $this->indexedItems[$id];
        }

        return $output;
    }

    /**
     * 
     */
    function get_items( $isHierarchical = true, $skipIds = [ 0 ] ) {
        if ( ! $this->items ) {
            return [];
        }

        $output = [];

        foreach( $this->items as $row ) {
            if ( in_array( $row->category__auto_id, $skipIds ) ) {
                continue;
            }

            if ( $isHierarchical && ( 0 < $row->category_parent ) ) {
                continue;
            }

            $rowData = $this->build_item( $row );

            if ( $isHierarchical ) {
                // $sub_items = 
                $rowData['sub_items'] = $this->get_sub_items( $row->category__auto_id );
            }

            $output[] = $rowData;
        }

        return $output;
    }

    /**
     * 
     */
    function get_sub_items( $id ) {
        if ( ! $this->items ) {
            return [];
        }

        $output = [];

        foreach( $this->items as $row ) {
            if ( $id == $row->category_parent ) {
                $output[] = $this->build_item( $row );
            }            
        }

        return $output;
    }

    /**
     * 
     */
    function build_item( $row ) {

        $output = [
            'auto_id' => $row->category__auto_id,
            'category_id' => $row->category_id,
            // 'category_lang' => $row->category_lang,
            'name' => $row->category_name,
            'category_safe_name' => $row->category_safe_name,
            'category_parent' => $row->category_parent,
            'rgt' => $row->rgt,
            'lft' => $row->lft,
            'category_enabled' => $row->category_enabled,
            'category_order' => $row->category_order,
        ];

        return $output;
    }

    /**
     * 
     */
    function set_items() {
        global $db;

	    $sql = "select * from " . table_categories . " ORDER BY lft ASC;";
        $this->rawItems = $db->get_results( $sql );
        $this->items = $this->rawItems;

        foreach( $this->rawItems as $row ) {
            $cat_id = $row->category__auto_id;
            $this->indexedItems[$cat_id] = $this->build_item( $row );
        }
    }
}

