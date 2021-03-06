<!-- submission_status.tpl -->
<legend>{#KAHUK_Visual_Change_Link_Status#}</legend>
<div style="margin:0 10px;">
	<h2>Are you sure that you want to change the story status to {if $action eq "new"}New{else}{$action|capitalize}{/if}?</h2>
	<p>
		<a class="btn btn-primary" href="{$admin_modify_do_url}">Yes</a>
		<a class="btn btn-default" href="{$admin_modify_url}">No</a>
	</p>
</div>
<hr />
<p><strong>{#KAHUK_Visual_Change_Link_Title#}:</strong> {$link_title} </p>
<br />
<p><strong>{#KAHUK_Visual_Change_Link_URL#}:</strong> <a href="{$link_url}" target="_blank" rel="noopener noreferrer">{$link_url}</a> &ndash; <a href = "{$kahuk_base_url}/admin/domain_management.php?id={$link_id}&add={$banned_domain_url}">{#KAHUK_Visual_Ban_This_URL#}</a></p>
<br />
<p><strong>{#KAHUK_Visual_Change_Link_Content#}:</strong> {$link_content}</p>
<!--/submission_status.tpl -->