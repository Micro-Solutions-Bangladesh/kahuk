<!-- groups.tpl -->
<legend>{#KAHUK_Visual_AdminPanel_Manage_Groups#}</legend>
<br />
<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			
			<th>{#KAHUK_Visual_AdminPanel_Group_Name#}</th>
			<th>{#KAHUK_Visual_AdminPanel_Group_Author#}</th>
			<th>{#KAHUK_Visual_AdminPanel_Group_Privacy#}</th>
			<th>{#KAHUK_Visual_AdminPanel_Group_Date#}</th>
			<th style="text-align:center;">{#KAHUK_Visual_AdminPanel_Group_Edit#}</th>
			<th style="text-align:center;">{#KAHUK_Visual_AdminPanel_Group_Delete#}</th>

		</tr>
	</thead>
	<tbody>
		{foreach from=$groups item=group}
			<tr {if $group.group_status!='enable'}class="tr_moderated"{/if}>
				
				<td>
					{if $group.group_status!='enable'}
						<a href='?mode=approve&group_id={$group.group_id}'><i class="fa fa-warning-sign" title="{#KAHUK_Visual_AdminPanel_Group_Approve#}" alt="{#KAHUK_Visual_AdminPanel_Group_Approve#}"></i></a>
					{else}
						<i class="fa fa-check" title="{#KAHUK_Visual_AdminPanel_Group_Approve#}d" alt="{#KAHUK_Visual_AdminPanel_Group_Approve#}d"></i>
					{/if}
					<a href="{$kahuk_base_url}/group_story.php?id={$group.group_id}">{$group.group_name}</a>
				</td>
				<td><a href="{$kahuk_base_url}/admin/admin_users.php?mode=view&user={$group.user_login}">{$group.user_login}</a></td>
				<td>{$group.group_privacy}</td>
				<td>{$group.group_date}</td>
				<td style="text-align:center;"><a class="btn btn-default" href='../editgroup.php?id={$group.group_id}' rel="width:800,height:700"><i class="fa fa-edit" alt="{#KAHUK_Visual_AdminPanel_Group_Edit#}" title="{#KAHUK_Visual_AdminPanel_Group_Edit#}"></i></a></td>
{* Redwine: Roles and permissions and Groups fixes *}
				<td style="text-align:center;">{if $amIadmin eq '1'}<a class="btn btn-danger" onclick='return confirm("{#KAHUK_Visual_Group_Delete_Confirm#}");' href='?mode=delete&group_id={$group.group_id}'><i class="fa fa-trash-o" alt="{#KAHUK_Visual_AdminPanel_Group_Delete#}" title="{#KAHUK_Visual_AdminPanel_Group_Delete#}"></i></a>{/if}</td>
				
			</tr>
		{/foreach}
	</tbody>
</table>
{* Redwine: Roles and permissions and Groups fixes. Fix the button when user met group creation quota *}
<a class="{if !$error_max}btn btn-success{else}btn btn-danger disabled strike-through" title="{#KAHUK_Visual_Submit_A_New_Group_Error#}"{/if} href="{$kahuk_base_url}/submit_groups.php" onclick="window.open('{$kahuk_base_url}/submit_groups.php','popup','width=900,height=900,scrollbars=yes,resizable=yes,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false">{#KAHUK_Visual_AdminPanel_New_Group#}</a>

<!--/groups.tpl -->