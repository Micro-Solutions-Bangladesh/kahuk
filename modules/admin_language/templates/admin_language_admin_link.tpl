{config_load file=admin_language_lang_conf}
<li{if $modulename eq "admin_language"} class="active"{/if}><a href="{$my_kahuk_base}/module.php?module=admin_language">{#KAHUK_Admin_Language_Menu#}</a></li>
{config_load file=admin_language_kahuk_lang_conf}