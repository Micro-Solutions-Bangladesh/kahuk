<?php
define('IS_ADMIN', true);


include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();

force_authentication();

if ($session_user_level != "admin") {
    die(".");
}

$activatedPlugins = kahuk_get_activated_plugins();
$plugin = sanitize_text_field(_get('plugin'));

// Let see if there any action found
$action = sanitize_text_field(_get('action'));

if ($plugin && in_array($action, ["active", "deactive"])) {
    $key = array_search($plugin, $activatedPlugins);

    if ($action == "active") {
        if ($key !== false) {
            die("Invalid action, Plugin is already in the active list!");
        }

        if (kahuk_is_valid_plugin($plugin)) {
            $activatedPlugins[] = trim($plugin);
            $hooks->do_action("kahuk_activate_plugin_" . $plugin);
        } else {
            kahuk_set_session_message(
                "Plugin activation failed!",
                "warning"
            );
        }
    } else {
        if ($key === false) {
            die("Invalid action, Plugin is not in the active list!");
        }

        unset($activatedPlugins[$key]);

        $hooks->do_action("kahuk_deactivate_plugin_" . $plugin);
    }

    kahuk_save_activate_plugins($activatedPlugins);
    
    kahuk_redirect(kahuk_create_url("admin/admin_plugins.php"));
    exit;
}


ob_start();
?>
<div class="data-list">
<div class="data-item gap-8 p-4 font-bold">
    <div class="title col-span-3">Plugin Title</div>
    <div class="description col-span-9">Description</div>
</div>

<?php
foreach($kahukPlugins as $slug => $kahukPlugin) {
    $isActive = in_array($slug, $pluginsInstalledArray);
    $row_cls = "data-item gap-8 py-4 px-4";

    if ($isActive) {
        $row_cls .= " heighlight";
    }

    $settings_url = '';

    if (isset($kahukPlugin['has_settings']) && $kahukPlugin['has_settings']) {
        $settings_url = kahuk_create_url("admin/admin_plugin.php?plugin={$slug}&action=settings");
    }
    ?>
    <div class="<?= $row_cls ?>">
        <div class="title col-span-3">
            <strong><?php echo $kahukPlugin['title']; ?></strong>
        </div>
        <div class="description flex flex-col gap-4 col-span-9">
            <p>
            <?php
            if(isset($kahukPlugin['desc'])) {
                echo $kahukPlugin['desc'];
            }
            ?>
            </p>
            <div class="flex gap-8">
                <?php
                if ($isActive) {
                ?>
                <a href="<?php echo kahuk_create_url("admin/admin_plugins.php?plugin=" . $slug . "&action=deactive"); ?>">Deactive</a>
                <?php
                } else {
                ?>
                <a href="<?php echo kahuk_create_url("admin/admin_plugins.php?plugin=" . $slug . "&action=active"); ?>">Active</a>
                <?php
                } 
                ?>

                <?php
                if ($settings_url) {
                ?>
                <a href="<?php echo $settings_url; ?>">Settings</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

echo "</div>";

$admin_data_markup = ob_get_clean();

$main_smarty->assign('admin_data_markup', $admin_data_markup);

$main_smarty->assign('tpl_center', '/admin/plugins');

echo $main_smarty->fetch('/admin/admin.tpl');
