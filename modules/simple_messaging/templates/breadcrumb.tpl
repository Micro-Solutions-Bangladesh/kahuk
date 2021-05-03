{config_load file=simple_messaging_lang_conf}
<li>{if $modulename_sm neq "simple_messaging_inbox"}<a href="{$URL_simple_messaging_inbox}">{/if}{#KAHUK_MESSAGING_Inbox#}{if $modulename_sm neq "simple_messaging_inbox"}</a>{/if}</li>
{if $modulename_sm eq "simple_messaging_viewmsg"}<li>{#KAHUK_MESSAGING_Message#}</li>{/if}
{if $modulename_sm eq "simple_messaging_sent"}<li>{#KAHUK_MESSAGING_Sent#}</li>{/if}
{if $modulename_sm eq "simple_messaging_viewsentmsg"}<li><a href="{$my_kahuk_base}/module.php?module=simple_messaging&view=sent">{#KAHUK_MESSAGING_Sent#}</a></li><li>{#KAHUK_MESSAGING_Message#}</li>{/if}
{if $modulename_sm eq "simple_messaging_reply"}<li>{#KAHUK_MESSAGING_Reply#}</li>{/if}
{if $modulename_sm eq "simple_messaging_compose"}<li>{#KAHUK_MESSAGING_Send#}</li>{/if}
{config_load file=simple_messaging_kahuk_lang_conf}