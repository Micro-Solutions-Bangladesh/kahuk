<?php

/**
 * Get list of activated plugins
 * 
 * @since 5.0.7
 * 
 * @return array|string
 */
function kahuk_get_activated_plugins($isArray = true) {
    $output = sanitize_csv(kahuk_get_config('plugins_installed', ''));

    if ($isArray) {
        return explode(",", $output);
    }

    return $output;
}

/**
 * Deactivate plugin
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_deactivate_plugin($pluginName) {
    global $hooks;

    $activatedPlugins = kahuk_get_activated_plugins();
    $key = array_search($pluginName, $activatedPlugins);

    if ($key) {
        $hooks->do_action("kahuk_deactivate_plugin_" . $pluginName);
        unset($activatedPlugins[$key]);
    }

    kahuk_save_activate_plugins($activatedPlugins);
}

/**
 * Update configuration of the csv list of Activated Plugins
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_save_activate_plugins($plugins) {
    kahuk_update_config("plugins_installed", sanitize_csv(implode(",", $plugins)));
}

/**
 * Check if the Plugin Folder is valid
 * 
 * @since 5.0.7
 * 
 * @return boolean
 */
function kahuk_is_valid_plugin($plugin) {
    $output = false;

    if (
        is_readable(KAHUKPATH_PLUGINS . $plugin . "/index.php") &&
        is_readable(KAHUKPATH_PLUGINS . $plugin . "/configs.php")
    ) {
        $output = true;
    }

    return $output;
}


/**
 * Collect the plugins detail
 */
$pluginFolders = scandir(KAHUKPATH_PLUGINS);

foreach($pluginFolders as $pluginFolder) {
    if (is_dir(KAHUKPATH_PLUGINS . $pluginFolder)) {
        if (in_array($pluginFolder, [".",".."])) {
            continue;
        }

        if (kahuk_is_valid_plugin($pluginFolder)) {
            include_once KAHUKPATH_PLUGINS . $pluginFolder . "/configs.php";

            if (!isset($kahukPlugins[$pluginFolder]['title'])) {
                // Delete the Plugin detail if Plugin title not mentioned in the `$pluginFolder/configs.php` file.
                unset($kahukPlugins[$pluginFolder]);
            }
        }
    }
}

/**
 * Include Installed Plugins
 */
$pluginsInstalledArray = kahuk_get_activated_plugins();

// $pluginsInstalledArray = empty($plugins_installed) ? [] : explode(",", sanitize_csv($plugins_installed));

foreach($pluginsInstalledArray as $pluginInstalled) {
    if (kahuk_is_valid_plugin($pluginInstalled)) {
        include_once KAHUKPATH_PLUGINS . $pluginInstalled . "/index.php";
    } else {
        kahuk_log_unexpected("Plugin folder $pluginInstalled not found!");
        kahuk_deactivate_plugin($pluginInstalled);
    }
}
