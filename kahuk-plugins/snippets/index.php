<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Add a menu item in Admin under Plugins
 */
$menu_item_parent = "manage-plugins";
$hooks->add_filter("kahuk_admin_menu_sub_{$menu_item_parent}", "snippets_admin_menu_sub_callback");

function snippets_admin_menu_sub_callback($items) {
    $items[] = [
        "menu_slug" => PLUGIN_SLUG_SNIPPETS,
        "page_title" => "Snippets Settings",
        "menu_title" => "Snippets",
        "capability" => "admin",
        "icon_url" => "",
        "url" => PLUGIN_SETTINGS_SNIPPETS,
        "position" => 10.1,
    ];

    return $items;
}

/**
 * 
 */
class Snippets
{
	private $db;

    public $snippets = [];
    public $snippetsById = [];

    /**
     * Class construcotr
     */
    private function __construct() {
		global $db;

		$this->db = $db;
        $this->get_db_snippets();
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
     * Get snippets from db table
     * 
     * @since 5.0.7
     * 
     * @return void
     */
    private function get_db_snippets() {
		$sql = "SELECT * FROM " . TABLE_PREFIX . "snippets";
        $rs = $this->db->get_results($sql, ARRAY_A);

		if ($rs) {
			foreach ($rs as $row) {
                $this->snippets[$row['snippet_location']] = $row;
                $this->snippetsById[$row['snippet_id']] = $row;
            }
		}
    }
}


/**
 * Initializes Snippets Class
 *
 * @return Snippets
 */
function snippets_create_init() {
    return Snippets::init();
}

// Set the global variables to URL
$snippetsObj = snippets_create_init();


/**
 * 
 */
function snippet_action_tpl_callback($location) {
    global $snippetsObj;

    if (isset($snippetsObj->snippets[$location])) {
        $snippet = $snippetsObj->snippets[$location];

        // if ($location == "tpl_kahuk_before_head_end") {
        //     print_r($snippet);
        //     exit;
        // }

        if ($snippet['snippet_status'] == 1) {
            global $main_smarty;

            $main_smarty->assign('snippet_content', $snippet['snippet_content']);
            echo $main_smarty->fetch(KAHUKPATH_PLUGINS . PLUGIN_SLUG_SNIPPETS . "/tpl/snippet_eval.tpl");
        }
    }
}

$hooks->add_action("snippet_action_tpl", "snippet_action_tpl_callback");


/**
 * Check if there any snippet location exist
 * 
 * @return int
 */
function snippet_is_location_exist($location, $skip_snippet_id= 0) {
    global $db;

    $sql = "SELECT count(snippet_id) FROM " . TABLE_PREFIX . "snippets";
    $sql .= " WHERE snippet_location = '" . $db->escape($location) . "'";

    //
    if ($skip_snippet_id > 0) {
        $sql .= " AND snippet_id != '" . (int) $skip_snippet_id . "'";
    }

    return $db->get_var($sql);
}


/**
 * Hook Function
 * Plugin Settings Page
 */
function snippet_settings_page_callback() {
    global $db, $snippetsObj;

    $single_page_url = kahuk_create_url("admin/admin_plugin.php?plugin=" . PLUGIN_SLUG_SNIPPETS . "&page=single-snippet&action=");
    
    $page = sanitize_text_field(_get("page", "plugin-default"));
    $action = sanitize_text_field(_get("action", ""));
    $action_type = sanitize_text_field(_post("action_type", ""));
    $snippet_id = 0;

    if ($page == "single-snippet") {
        $single_snippet = [];

        if ($action && empty($action_type)) {
            if ($action == "new") {
                // New Form
                $single_snippet = [
                    "snippet_id" => 0,
                    "snippet_name" => "",
                    "snippet_location" => "",
                    "snippet_status" => 0,
                    "snippet_content" => "",
                ];
            } else {
                // Edit Mode
                $snippet_id = sanitize_number(_get("action", 0));

                $single_snippet = $snippetsObj->snippetsById[$snippet_id] ?? [];
            }

            if ($single_snippet) {
                include __DIR__ . '/tpl/single-snippet.php';
            } else {
                // No single snippet found, redirect to settings page
                kahuk_redirect(PLUGIN_SETTINGS_SNIPPETS);
                exit;
            }

        } else if ($action_type == "submit-snippet") {
            // Store into DB mode
            $snippet_id = sanitize_number(_post("action", 0));

            $snippet_name = $db->escape(sanitize_text_field(_post("snippet_name", "")));
            $snippet_location = $db->escape(sanitize_text_field(_post("snippet_location", "")));
            $snippet_content = $db->escape(_post("snippet_content", ""));
            $snippet_status = sanitize_number(_post("snippet_status", 0));

            if ($action == "new") {
                // Insert New Snippet Record
                $sql = "INSERT INTO ";
            } else {
                // Update Existing Record
                $snippet_id = sanitize_number(_post("snippet_id", ""));

                $sql = "UPDATE ";
            }

            $sql .= TABLE_PREFIX. "snippets SET snippet_name='$snippet_name'";
            $sql .= ", snippet_location='$snippet_location', snippet_content='$snippet_content'";
            $sql .= ", snippet_updated=NOW(), snippet_status=$snippet_status, snippet_order=0";
            
            //
            $check_duplicate_location = snippet_is_location_exist($snippet_location, $snippet_id);

            if ($check_duplicate_location > 0) {
                die("Save failed, duplicate location exist!");
            }

            if ($action == "new") {
                $snippet_id = $db->query_insert($sql);

                if (!$snippet_id) {
                    // kahuk_log_unexpected("New Record Failed!\nSQL: {$sql}");
                    die('Database error!');
                }                
            } else {
                $sql .= " WHERE snippet_id=$snippet_id";
                $output = $db->query($sql);

                if ($output !== 1) {
                    kahuk_log_unexpected("Update Snippet Record Failed!\nSQL: {$sql}");
                }
            }

            $url = $single_page_url . $snippet_id;
            kahuk_redirect($url);
            exit;
        } else {
            // No single snippet found, redirect to settings page
            kahuk_redirect(PLUGIN_SETTINGS_SNIPPETS);
            exit;
        }
    } else {
        // Default Page for Plugin
        include __DIR__ . '/tpl/plugin-default.php';
    }
}

$hooks->add_action("kahuk_settings_page_" . PLUGIN_SLUG_SNIPPETS, "snippet_settings_page_callback");








