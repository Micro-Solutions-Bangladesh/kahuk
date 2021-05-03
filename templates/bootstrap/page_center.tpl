{************************************
************ Kahuk Pages ************
*************************************}
<!-- page_center.tpl -->
{eval var=$page_content}
{if isset($isadmin) && $isadmin eq 1}
	<div class="edit">
		<a class="btn btn-primary" href="{$kahuk_base_url}/admin/edit_page.php?link_id={$link_id}">{#KAHUK_Visual_AdminPanel_Page_Edit#}</a>
	</div>
{/if}
<!-- page_center.tpl -->