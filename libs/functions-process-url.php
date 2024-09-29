<?php
/**
 * 
 */
class KahukURL
{
    public $controller;

    public $name;

    public $task;

    public $prefix;

    public $slug;

    public $pageId;

    public $pageNumber;

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->find_controller();
        $this->find_name();
        $this->find_task();
        $this->find_slug();
        $this->find_prefix();
        $this->find_page_id();
        $this->find_page_number();
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
    private function find_controller() {
        $pageController = strtolower($_GET['con'] ?? '');

        if (in_array($pageController, ['published','new','trending'])) {
            $this->controller = 'home';
        } else {
            // Determine single group
            if ($pageController == 'groups') {
                $slug = strtolower($_GET['slug'] ?? '');

                if (!empty($slug)) {
                    $pageController = 'group';
                }
            }

            $this->controller = $pageController;
        }
    }

    /**
     * Get name from request parameters
     * 
     * @since 5.0.7
     * 
     * @return string Page name
     */
    private function find_name() {
        $this->name = kahuk_create_slug_story($_REQUEST['pn'] ?? '');
    }

    /**
     * 
     */
    private function find_task() {
        $con = strtolower($_GET['con'] ?? '');

        if (($this->controller == "home")) {
            if (in_array($con, ['published','new','trending'])) {
                $this->task = $con;
            } else {
                $this->task = 'published';
            }            
        } else {
            $this->task = strtolower($_GET['task'] ?? '');
        }
    }

    /**
     * Get/Create slug from request parameters
     * 
     * @since 5.0.7
     * 
     * @return string Page Slug
     */
    private function find_slug() {
        $this->slug = kahuk_create_slug_story($_GET['slug'] ?? '');
    }

    /**
     * Get/Create prefix from request parameters
     * 
     * @since 5.0.7
     * 
     * @return string Prefix
     */
    private function find_prefix() {
        $this->prefix = strtolower($_GET['prefix'] ?? '');

        /**
         * Set few default prefixes
         */
        if (($this->controller == 'user') && empty($this->prefix)) {
            $this->prefix = 'profile';
        }
    }

    /**
     * Get/Create id name from request parameters
     * 
     * @since 5.0.7
     * 
     * @return string Page Id
     */
    private function find_page_id() {
        $pageId = trim($_GET['id'] ?? '');

        $this->pageId = (int) strip_tags($pageId);
    }

    /**
     * Get page number from request parameters
     * 
     * @since 5.0.7
     * 
     * @return integer page number
     */
    private function find_page_number() {
        $page_number = (int) strip_tags( $_REQUEST['page'] ?? 1 );

        $this->pageNumber = ($page_number > 0 ? $page_number : 1);
    }
}

/**
 * Initializes KahukURL Class
 *
 * @return KahukURL
 */
function kahuk_url_init() {
    return KahukURL::init();
}

// Set the global variables to URL
$globalURL = kahuk_url_init();

$pageCon = $globalURL->controller; // Golbal Variable
$pageTask = $globalURL->task; // Golbal Variable


/**
 * Build unique page name to identify every page.
 * 
 * @since 5.0.7
 * 
 * @reurn string page unique name
 */
function kahuk_build_unique_page() {
    global $globalURL;

    $output = $globalURL->controller;

    if ($globalURL->controller == "home") {
        $output .= "-" . $globalURL->task;
    } else {
        if ($globalURL->prefix) {
            $output .= "-" . $globalURL->prefix;
        } else if ($globalURL->name) {
            $output .= "-" . $globalURL->name;
        }
    }

    return $output;
}


/**
 * Get slug from globalURL variable
 * 
 * @since 5.0.6
 * @updated 5.0.7
 * 
 * @return string Slug
 */
function get_page_slug() {
    global $globalURL;

    return $globalURL->slug;
}

$pageSlug = get_page_slug(); // Golbal Variable

/**
 * Get prefix from globalURL variable
 * 
 * @since 5.0.6
 * @updated 5.0.7
 * 
 * @return string Prefix
 */
function get_prefix() {
    global $globalURL;

    return $globalURL->prefix;
}

$pagePrefix = get_prefix(); // Golbal Variable

/**
 * Get id name from globalURL variable
 * 
 * @since 5.0.6
 * @updated 5.0.7
 * 
 * @return string Page Id
 */
function get_page_id() {
    global $globalURL;

    return $globalURL->pageId;
}

$pageId = get_page_id(); // Golbal Variable

/**
 * Get/Create page number from request parameters
 * 
 * @since 5.0.6
 * 
 * @return integer page number
 */
function get_page_number() {
    global $globalURL;

    return $globalURL->pageNumber;
}

$pageNum = get_page_number(); // Golbal Variable

/**
 * Determine page name from page controller or from pagename constant
 * 
 * @since 5.0.6
 * 
 * @return array
 */
function kahuk_get_pagename() {
    global $pageCon, $pagePrefix;

    $output = $pageCon;

    if (!empty($pagePrefix)) {
        $output .= "-{$pagePrefix}";
    }

    return $output;
}

/**
 * Create and return path of the page to be display
 * 
 * @since 5.0.6
 * 
 * @return string
 */
function kahuk_loadable_page_path() {
    global $pageCon;

    //
    $pagename = kahuk_get_pagename();

    if (in_array($pageCon, ['published', 'new', 'trending'])) {
        $pagename = 'home';
    }

    //
    $unexpectedMessage = '';

	$loadablePage = KAHUKPATH_PAGES . $pagename . ".php";

	if (!file_exists(KAHUKPATH_PAGES . $pagename . ".php")) {
        $unexpectedMessage = "Page: {$loadablePage} is not exist!";
        $conPageCheck = explode(".", $pageCon);

        if ((count($conPageCheck) == 2) && ($conPageCheck[0] == "php")) {
            $unexpectedMessage .= "\nConsidering as main page like - login, register, live, etc.";
            $redirect = kahuk_create_url($conPageCheck[0]);
        }

        $unexpectedMessage .= "\nRedirecting to: {$redirect}";
        // kahuk_log_debug($unexpectedMessage);

        kahuk_redirect($redirect);
        die();
	}

	return $loadablePage;
}


/**
 * 
 */
function kahuk_debug_url() {
    global $globalURL, $pagename;

    echo "<pre class=\"debug\">";
    echo "<br>====== Before Sanitize ======<br>";
    print_r($_REQUEST);
    echo "<br>====== After Sanitize ======<br>";
    print_r($globalURL);
    echo "<br>====== Page unique name: " . kahuk_build_unique_page() . " ======<br>";
    echo "</pre>";

    echo "<pre class=\"debug\">" . kahuk_loadable_page_path() . "</pre>";
    echo "<pre class=\"debug\">pagename[depricated]: " . $pagename . "</pre>";
    echo "<pre class=\"debug\">pagename[NEW]: " . kahuk_build_unique_page() . "</pre>";
}
