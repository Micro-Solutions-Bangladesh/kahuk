<!-- user_view.tpl -->
{section name=nr loop=$userdata}
	<legend>View User</legend>
	
	<br />
	<table class="table table-bordered table-striped" cellpadding="0" cellspacing="0">
		<tbody>
			
			<tr>
				<td width="160px"><strong>Username</strong></td>
				<td><a href="{$kahuk_base_url}/user.php?login={$userdata[nr].user_login}">{$userdata[nr].user_login}</a></td>
			</tr>
			<tr>
				<td><strong>{#KAHUK_Visual_Profile_CurrentAvatar#} </strong></td>
				<td><img src="{$userdata[nr].Avatar.large}" align="absmiddle"/></td>
			</tr>
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_Level#}: </strong></td>
				<td>{$userdata[nr].user_level}</td>
			</tr>
{* Redwine: Roles and permissions and Groups fixes. to hide the email from moderators *}
				{if $is_moderator eq 1}
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_Email#}: </strong></td>
{* Redwine: Roles and permissions and Groups fixes. Hiding the email from moderators*}
				<td>*********</td>
				{else}
				<td><strong>{#KAHUK_Visual_View_User_Email#}: </strong></td>
				<td>{$userdata[nr].user_email}</td>
			</tr>
{* Redwine: Roles and permissions and Groups fixes *}
			{/if}
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_LL_Date#}: </strong></td>
				<td>{$userdata[nr].user_lastlogin}</td>	
			</tr>
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_LL_Address#}: </strong></td>
				<td> {$userdata[nr].user_lastip}</td>
			</tr>
			{if $userdata[nr].user_login neq "admin"}
				<tr>
					<td><strong>{#KAHUK_Visual_View_User_IP_Address#}:</strong></td>
					<td> {$userdata[nr].user_ip}</td>
				</tr>
			{/if}
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_Groups_Belongs#}: </strong></td><td> {$userdata[nr].belongs}</td>
			</tr>
			<tr>
				<td><strong>{#KAHUK_Visual_View_User_Groups_Created#}: </strong></td><td> {$userdata[nr].created}</td>
			</tr>
			
			<tr>
				<td><a href="admin_links.php?user={$userdata[nr].user_login}">{#KAHUK_Visual_View_User_Sub_Links#}</a></td> <td> {$linkcount} Total</td>
			</tr>
			<tr>
				<td><a href="admin_comments.php?user={$userdata[nr].user_login}">{#KAHUK_Visual_View_User_Sub_Comments#}</a></td>
				<td>{$commentcount} Total</td>
			</tr>
			
		</tbody>
	</table>
{* Redwine: Roles and permissions and Groups fixes *}	
	{if $amIadmin eq '1'}		
		<a class="btn btn-primary" href="?mode=edit&user_id={$userdata[nr].user_id}">{#KAHUK_Visual_View_User_Edit_Data#}</a>
		{if $user_logged_in neq $userdata[nr].user_login && $userdata[nr].user_id neq '1'}
			{if $userdata[nr].user_status eq 'enable'}
				<a class="btn btn-warning" href="?mode=disable&user={$userdata[nr].user_login}">{#KAHUK_Visual_View_User_Disable#}</a>
			{else}
				<a class="btn btn-success" href="?mode=enable&user={$userdata[nr].user_login}">{#KAHUK_Visual_View_User_Enable#}</a>
			{/if}
			<a class="btn btn-danger" href="?mode=killspam&user={$userdata[nr].user_login}&id={$userdata[nr].user_id}">{#KAHUK_Visual_View_User_Killspam#}</a>
		{/if}
{* Redwine: Roles and permissions and Groups fixes *}
	{elseif $isModerator eq '1'}
		{if $userdata[nr].user_status eq 'disable'}
			<a class="btn btn-success" href="?mode=enable&user={$userdata[nr].user_login}">{#KAHUK_Visual_View_User_Enable#}</a>
		{/if}
	{/if}
{sectionelse}
	{include file="/templates/admin/user_doesnt_exist_center.tpl"}
{/section}
<!--/user_view.tpl -->
