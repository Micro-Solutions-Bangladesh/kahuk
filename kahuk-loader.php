<?php

/** Define ABSPATH as this file's directory */
if (!defined('KAHUKPATH')) {
    define('KAHUKPATH', dirname(__FILE__) . '/');
}

if (!is_readable(KAHUKPATH . 'kahuk-configs.php') && is_readable(KAHUKPATH . 'setup/index.php')) {
    header('Location: ./setup/index.php');
    exit;
}
