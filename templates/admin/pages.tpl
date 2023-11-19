<!-- pages.tpl -->
<legend>{#KAHUK_Visual_AdminPanel_Manage_Pages#}</legend>
<br />
<table class="table table-condensed table-bordered table-striped">
	<thead>
		<tr>
			
			<th class="page_th_title">{#KAHUK_Visual_AdminPanel_Page_Submit_Title#}</th>
			<th class="page_th_edit">{#KAHUK_Visual_AdminPanel_Page_Edit#}</th>
			<th class="page_th_delete">{#KAHUK_Visual_AdminPanel_Page_Delete#}</th>
			
		</tr>
	</thead>
	<tbody>
		{$page_title}
	</tbody>
</table>
{$page_text}
<a class="btn btn-success" href="{$kahuk_base_url}/admin/submit_page.php" title="{#KAHUK_Visual_AdminPanel_Page_Submit_New#}">{#KAHUK_Visual_AdminPanel_Page_Submit_New#}</a>
<!--/pages.tpl -->