{************************************
****** First Sidebar Template *******
*************************************}
<!-- sidebar.tpl -->
{if $pagename neq "submit"}
	{checkActionsTpl location="tpl_kahuk_sidebar_start"}
	<!-- START SEARCH BOX -->
		{include file=$the_template."/search_box.tpl"}
	<!-- END SEARCH BOX -->
	{checkActionsTpl location="tpl_kahuk_sidebar_middle"}
	<!-- START ABOUT BOX -->
	{if $what_is_kahuk eq 'true'}
		{include file=$the_template."/about_box.tpl"}
	{/if}
	<!-- END ABOUT BOX -->
	{checkActionsTpl location="tpl_kahuk_sidebar_end"}
{/if}
<!--/sidebar.tpl -->