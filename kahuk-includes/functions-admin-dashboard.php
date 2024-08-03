<?php

/**
 * this file pulls settings directly from the DB
 */
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * 
 */
class KahukAdminMenu
{
    public $items = [];
    public $sub_items = [];

    /**
     * Class constructor
     */
    private function __construct() {

    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Return menu items
     * 
     * @return array menu items
     */
    function get_menu_items($parent_slug = "") {
        $output = [];
        $items = (
            $parent_slug ? 
            ($this->sub_items[$parent_slug] ?? []) :
            $this->items
        );

        // echo "<pre>Function: get_menu_items() " . sanitize_number("10.01") . "</pre>";

        foreach($items as $item) {
            if (kahuk_has_access($item["capability"])) {
                $pos = $item["position"];
                $output[(string) $pos] = $item;
            }
        }

        // echo "<pre>Before</pre>";
        // print_r($output);

        ksort($output);

        // echo "<pre>After</pre>";
        // print_r($output);

        return $output;
    }

    /**
     * Add menu sub item
     * 
     * @since 5.0.8
     * 
     * @param string    $page_title The text to be displayed in the title tags of the page when the menu is selected.
     * @param string    $menu_title The text to be used for the menu.
     * @param string    $capability The capability required for this menu to be displayed to the user.
     * @param string    $menu_slug  The slug name to refer to this menu by. Should be unique for this menu page and only
     *                              include lowercase alphanumeric, dashes, and underscores characters to be compatible
     *                              with sanitize_key().
     * @param callable  $callback   Optional. The function to be called to output the content for this page.
     * @param string    $icon_url   Optional. The URL to the icon to be used for this menu.
     *                              * Pass a base64-encoded SVG using a data URI, which will be colored to match
     *                                the color scheme. This should begin with 'data:image/svg+xml;base64,'.
     *                              * Pass the name of a Dashicons helper class to use a font icon,
     *                                e.g. 'dashicons-chart-pie'.
     *                              * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
     * @param int|float $position   Optional. The position in the menu order this item should appear.
     * 
     * @return void
     */
    function add_menu_item_sub($newItem, $parent_slug) {
        $menu_title = sanitize_text_field($newItem["menu_title"]);
        $position = floatval($newItem["position"] ?? "10");

        $newItem["menu_title"] = $menu_title;
        $newItem["position"] = $position;

        $sub_items = ($this->sub_items[$parent_slug] ?? []);

        if (isset($sub_items[(string) $position])) {
            $collision_avoider = base_convert(substr(md5($menu_title), -4), 16, 10) * 0.00001;
            $position = ($position + $collision_avoider);

            $newItem["position"] = $position;
        }

        $this->sub_items[$parent_slug][(string) $position] = $newItem;
    }

    /**
     * Add menu item
     * 
     * @since 5.0.8
     * 
     * @param string    $page_title The text to be displayed in the title tags of the page when the menu is selected.
     * @param string    $menu_title The text to be used for the menu.
     * @param string    $capability The capability required for this menu to be displayed to the user.
     * @param string    $menu_slug  The slug name to refer to this menu by. Should be unique for this menu page and only
     *                              include lowercase alphanumeric, dashes, and underscores characters to be compatible
     *                              with sanitize_key().
     * @param callable  $callback   Optional. The function to be called to output the content for this page.
     * @param string    $icon_url   Optional. The URL to the icon to be used for this menu.
     *                              * Pass a base64-encoded SVG using a data URI, which will be colored to match
     *                                the color scheme. This should begin with 'data:image/svg+xml;base64,'.
     *                              * Pass the name of a Dashicons helper class to use a font icon,
     *                                e.g. 'dashicons-chart-pie'.
     *                              * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
     * @param int|float $position   Optional. The position in the menu order this item should appear.
     * 
     * @return void
     */
    function add_menu_item($newItem) {
        global $hooks;

        $menu_slug = sanitize_text_field($newItem["menu_slug"]);
        $menu_title = sanitize_text_field($newItem["menu_title"]);
        $position = floatval($newItem["position"]);

        $newItem["menu_slug"] = sanitize_text_field($menu_slug);
        $newItem["menu_title"] = sanitize_text_field($menu_title);
        $newItem["position"] = $position;
        
        if (isset($this->items[(string) $position])) {
            $collision_avoider = base_convert(substr(md5($menu_title), -4), 16, 10) * 0.00001;
            $position = ($position + $collision_avoider);

            $newItem["position"] = $position;
        }

        $this->items[(string) $position] = $newItem;

        $subItems = [];
        $subItems = $hooks->apply_filters("kahuk_admin_menu_sub_" . $menu_slug, $subItems);
        
        if ($subItems) {
            foreach($subItems as $subItem) {
                $this->add_menu_item_sub($subItem, $menu_slug);
            }
        }
    }
}

/**
 * Initializes KahukAdminMenu Class
 *
 * @return KahukAdminMenu
 */
function kahuk_admin_menu_init() {
    return KahukAdminMenu::init();
}

/**
 * Create a new item for admin menu
 * 
 * @since 5.0.8
 * 
 * @return void
 */
function kahuk_add_admin_menu($new_item) {
    $defaults = [
        "menu_slug" => "", // Require
        "menu_title" => "", // Require
        "capability" => "admin", // Optional
        "icon_url" => "", // Optional
        "url" => "",
        "position" => 1,
    ];

    $newItem = array_merge($defaults, $new_item);

    $adminMenuCls = kahuk_admin_menu_init();

    $adminMenuCls->add_menu_item($newItem);
}


/**
 * Create a nested array for Admin menu
 * 
 * @since 5.0.8
 * 
 * @return array of menu properties
 */
function kahuk_admin_menu($includingSubItems = true) {
    global $hooks;

    $adminMenuCls = kahuk_admin_menu_init();
    $menuItems = $adminMenuCls->get_menu_items();

    if ($includingSubItems) {
        foreach($menuItems as $i => $menuItem) {
            // $menuItems[(string) $i]["sub_items"] = $adminMenuCls->get_menu_items($menuItem["menu_slug"]);
            $sub_items = $adminMenuCls->get_menu_items($menuItem["menu_slug"]);
            $menuItems[(string) $i]["sub_items"] = $hooks->apply_filters("kahuk_admin_menu_" . $menuItem["menu_slug"], $sub_items);
        }
    }

    $menu = [
        "items" => $menuItems,
        "wrapper-css-class" => "admin-menu",
    ];

    return $hooks->apply_filters("kahuk_admin_menu", $menu);
}


/**
 * Create HTML markup for Admin menu
 * 
 * @since 5.0.8
 * 
 * @return string Admin menu markup
 */
function kahuk_admin_menu_markup() {
    global $hooks;

    $menu = kahuk_admin_menu();
    $wrapper_css_class = $menu["wrapper-css-class"];
    // return print_r($menu, true);

    $output = "<ul class=\"{$wrapper_css_class}\">";

    foreach($menu["items"] as $item) {
        $menu_title = $item["menu_title"];
        $icon_url = ($item["icon_url"] ? $item["icon_url"] : "");
        $item_markup = "{$icon_url} {$menu_title}";

        if ($item["url"]) {
            $item_markup = "{$icon_url} <a href=\"" . $item["url"] . "\" title=\"{$menu_title}\">{$menu_title}</a>";
        }

        $sub_items_markup = "";

        if (!empty($item["sub_items"])) {
            $sub_items_markup .= "<ul class=\"menu-sub\">";
            foreach($item["sub_items"] as $sub_item) {
                $sub_items_markup .= "<li>";
                $sub_items_markup .= "<a href=\"" . $sub_item["url"] . "\" title=\"" . $sub_item["menu_title"] . "\">" . $sub_item["menu_title"] . "</a>";
                $sub_items_markup .= "</li>";
            }
            $sub_items_markup .= "</ul>";
        }

        $output .= "<li><h4>$item_markup</h4>{$sub_items_markup}</li>";
    }

    $output .= "</ul>";

    return $hooks->apply_filters('kahuk_admin_menu_markup', $output);
}

/**
 * Add initial menu items for Admin menu
 * 
 * @since 5.0.8
 * 
 * @return void
 */
function kahuk_intial_admin_menu_items() {
    /**
     * Item Story
     */
    $menuItem = [
        "menu_slug" => "manage-stories",
        "page_title" => "Manage Stories",
        "menu_title" => "Submissions",
        "capability" => "moderator",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "url" => kahuk_create_url('admin/admin_links.php'),
        "position" => 5,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Groups
     */
    $menuItem = [
        "menu_slug" => "manage-groups",
        "page_title" => "Manage Groups",
        "menu_title" => "Groups",
        "capability" => "moderator",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "url" => kahuk_create_url('admin/admin_group.php'),
        "position" => 6,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Categories
     */
    $menuItem = [
        "menu_slug" => "manage-categories",
        "page_title" => "Manage Categories",
        "menu_title" => "Categories",
        "capability" => "admin",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "url" => kahuk_create_url('admin/admin_categories.php'),
        "position" => 6,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Comments
     */
    $menuItem = [
        "menu_slug" => "manage-comments",
        "page_title" => "Manage Comments",
        "menu_title" => "Comments",
        "capability" => "moderator",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "url" => kahuk_create_url('admin/admin_comments.php'),
        "position" => 10,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Users
     */
    $menuItem = [
        "menu_slug" => "manage-users",
        "page_title" => "Manage Users",
        "menu_title" => "Users",
        "capability" => "admin",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "url" => kahuk_create_url('admin/admin_users.php'),
        "position" => 11,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Content
     */
    $menuItem = [
        "menu_slug" => "manage-content",
        "menu_title" => "Content",
        "capability" => "moderator",
        "icon_url" => "<i class=\"fa fa-gear\"></i>",
        "position" => 15,
    ];

    kahuk_add_admin_menu($menuItem);

    /**
     * Item Plugins
     */
    $menuItem = [
        "menu_slug" => "manage-plugins",
        "menu_title" => "Plugins",
        "capability" => "admin",
        "icon_url" => "<i class=\"fa fa-puzzle-piece\"></i>",
        "url" => kahuk_create_url('admin/admin_plugins.php'),
        "position" => 20,
    ];
    
    kahuk_add_admin_menu($menuItem);

    /**
     * Item Templates
     */
    $menuItem = [
        "menu_slug" => "manage-templates",
        "menu_title" => "Templates",
        "capability" => "admin",
        "icon_url" => "<i class=\"fa fa-file-o\"></i>",
        "position" => 25,
    ];

    kahuk_add_admin_menu($menuItem);

    /**
     * Item Settings
     */
    $menuItem = [
        "menu_slug" => "manage-settings",
        "menu_title" => "Settings",
        "capability" => "admin",
        "icon_url" => "<i class=\"fa fa-wrench\"></i>",
        "position" => 30,
    ];

    kahuk_add_admin_menu($menuItem);
}


/**
 * Add a menu sub item in Admin under Settings
 */
$hooks->add_filter("kahuk_admin_menu_sub_manage-settings", "kahuk_admin_menu_sub_manage_settings_callback");

function kahuk_admin_menu_sub_manage_settings_callback($items) {
    $items[] = [
        "menu_slug" => "recommended-settings",
        "page_title" => "Recommended Settings",
        "menu_title" => "Recommended Settings",
        "capability" => "admin",
        // "icon_url" => "",
        "url" => kahuk_create_url('admin/admin_settings.php'),
        "position" => "10.01",
    ];

    $items[] = [
        "menu_slug" => "admin-attention-logs",
        "page_title" => "Admin Attention Logs",
        "menu_title" => "Attention Logs",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_attention.php'),
        "position" => "10.02",
    ];

    $items[] = [
        "menu_slug" => "admin-error-logs",
        "page_title" => "Error Logs",
        "menu_title" => "Error Logs",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_log.php'),
        "position" => "10.03",
    ];

    $items[] = [
        "menu_slug" => "settings-primary-settings",
        "page_title" => "Primary Settings",
        "menu_title" => "Primary Settings",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Primary Settings'),
        "position" => "10.10",
    ];

    $items[] = [
        "menu_slug" => "settings-karma",
        "page_title" => "Karma",
        "menu_title" => "Karma",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Karma'),
        "position" => "10.11",
    ];

    $items[] = [
        "menu_slug" => "settings-email-settings",
        "page_title" => "Email Settings",
        "menu_title" => "Email Settings",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Email Settings'),
        "position" => "10.11",
    ];

    $items[] = [
        "menu_slug" => "settings-logos",
        "page_title" => "Settings Logos",
        "menu_title" => "Logos",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Logo'),
        "position" => "10.12",
    ];

    $items[] = [
        "menu_slug" => "settings-anonymous",
        "page_title" => "Settings Anonymous",
        "menu_title" => "Anonymous",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Anonymous'),
        "position" => "10.11",
    ];

    $items[] = [
        "menu_slug" => "settings-antispam",
        "page_title" => "Settings Anti-Spam",
        "menu_title" => "Anti-Spam",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=AntiSpam'),
        "position" => "10.12",
    ];

    $items[] = [
        "menu_slug" => "settings-avatars",
        "page_title" => "Settings Avatars",
        "menu_title" => "Avatars",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Avatars'),
        "position" => "10.13",
    ];

    $items[] = [
        "menu_slug" => "settings-comments",
        "page_title" => "Settings Comments",
        "menu_title" => "Comments",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Comments'),
        "position" => "10.14",
    ];

    $items[] = [
        "menu_slug" => "settings-groups",
        "page_title" => "Settings Groups",
        "menu_title" => "Groups",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Groups'),
        "position" => "10.15",
    ];

    $items[] = [
        "menu_slug" => "settings-location-installed",
        "page_title" => "Settings Location Installed",
        "menu_title" => "Location Installed",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/admin_config.php?page=Location Installed'),
        "position" => "10.16",
    ];

    $items[] = [
        "menu_slug" => "settings-misc",
        "page_title" => "Settings Miscellaneous",
        "menu_title" => "Miscellaneous",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=Misc"),
        "position" => "10.17",
    ];

    $items[] = [
        "menu_slug" => "settings-outgoing",
        "page_title" => "Settings Outgoing",
        "menu_title" => "Outgoing",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=OutGoing"),
        "position" => "10.18",
    ];

    $items[] = [
        "menu_slug" => "settings-seo",
        "page_title" => "Settings Search Engine Optimization",
        "menu_title" => "SEO",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=SEO"),
        "position" => "10.19",
    ];

    $items[] = [
        "menu_slug" => "settings-story",
        "page_title" => "Settings Story",
        "menu_title" => "Story",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=Story"),
        "position" => "10.20",
    ];

    $items[] = [
        "menu_slug" => "settings-submit",
        "page_title" => "Settings Submit",
        "menu_title" => "Submit",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=Submit"),
        "position" => "10.21",
    ];

    $items[] = [
        "menu_slug" => "settings-voting",
        "page_title" => "Settings Voting",
        "menu_title" => "Voting",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=Voting"),
        "position" => "10.22",
    ];

    $items[] = [
        "menu_slug" => "manage-domains",
        "page_title" => "Manage Domains",
        "menu_title" => "Domains",
        "capability" => "admin",
        "url" => kahuk_create_url('admin/domain_management.php'),
        "position" => "10.50",
    ];

    // .php

    return $items;
}

/**
 * Add a menu sub item in Admin under Content
 */
$hooks->add_filter("kahuk_admin_menu_sub_manage-content", "kahuk_admin_menu_sub_manage_content_callback");

function kahuk_admin_menu_sub_manage_content_callback($items) {
    $items[] = [
        "menu_slug" => "manage-pages",
        "page_title" => "Manage Pages",
        "menu_title" => "Pages",
        "capability" => "admin",
        // "icon_url" => "",
        "url" => kahuk_create_url('admin/admin_page.php'),
        // "position" => 5,
    ];

    return $items;
}

/**
 * Add a menu sub item in Admin under Plugins
 */
// $hooks->add_filter("kahuk_admin_menu_sub_manage-plugins", "kahuk_admin_menu_sub_manage_plugins_callback");

function kahuk_admin_menu_sub_manage_plugins_callback($items) {
    return $items;
}

/**
 * Add a menu sub item in Admin under Templates
 */
$hooks->add_filter("kahuk_admin_menu_sub_manage-templates", "kahuk_admin_menu_sub_manage_templates_callback");

function kahuk_admin_menu_sub_manage_templates_callback($items) {
    $items[] = [
        "menu_slug" => "settings-template",
        "page_title" => "Settings Template",
        "menu_title" => "Template",
        "capability" => "admin",
        "url" => kahuk_create_url("admin/admin_config.php?page=Template"),
        "position" => "10",
    ];

    return $items;
}

