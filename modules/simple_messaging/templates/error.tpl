{config_load file=simple_messaging_lang_conf}
<div class="simple_messaging_wrapper">

	{include file="./modules/simple_messaging/templates/menu.tpl"}
	{config_load file=simple_messaging_lang_conf}

	<h3>{#KAHUK_MESSAGING_Error#}</h3>

	<div class="alert alert-warning">
		<strong>{$message}</strong>
	</div>
		
	<a class="btn btn-warning" href="#" onclick="history.go(-1);">{#KAHUK_MESSAGING_Back#}</a>
	
</div>

{config_load file=simple_messaging_kahuk_lang_conf}