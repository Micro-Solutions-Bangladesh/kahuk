{************************************
***** Individual Group Template *****
*************************************}
<!-- group_story_center.tpl -->
{if $enable_group eq "true"}
	{if $isAdmin eq 1 || $isModerator eq 1 || $is_gr_Admin eq 1}
		<div class="alert alert-warning expires-warning">{#KAHUK_Visual_Page_Expires#}</div>
	{/if}
	{* Redwine: The below code is to check if the SMTP testing settings are true. If true, it will display the email message sent to users. *}
	{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $groupInvitation neq ''}
	<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
	{$groupInvitation}
	</span>
	<br /><hr />
	{/if}
	{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $errorSending neq ''}
	<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
	{$errorSending}
	</span>
	<br /><hr />
	{/if}
	{* Redwine: END SMTP testing settings. *}
	{checkActionsTpl location="tpl_kahuk_group_start"}
	{include file=$the_template."/group_summary.tpl"}
	{checkActionsTpl location="tpl_kahuk_group_end"}
	<ul id="storytabs" class="nav nav-tabs">
		{checkActionsTpl location="tpl_kahuk_group_sort_start"}
		<li {if $groupview eq "published"}class="active"{/if}>
			<a href="{$groupview_published}">
				<span>{#KAHUK_Visual_Group_Published#}</span>
				{if $group_published_rows}
					<span class="badge badge-gray">{$group_published_rows}</span>
				{/if}
			</a>
		</li>
		<li {if $groupview eq "new"}class="active"{/if}>
			<a href="{$groupview_new}">
				<span>{#KAHUK_Visual_Group_New#}</span>
				{if $group_new_rows}
					<span class="badge badge-gray">{$group_new_rows}</span>
				{/if}
			</a>
		</li>
		<li {if $groupview eq "shared"}class="active"{/if}>
			<a href="{$groupview_sharing}">
				<span>{#KAHUK_Visual_Group_Shared#}</span>
				{if $group_shared_rows}
					<span class="badge badge-gray">{$group_shared_rows}</span>
				{/if}
			</a>
		</li>
		<li {if $groupview eq "members"}class="active"{/if}>
			<a href="{$groupview_members}">
				<span class="active">{#KAHUK_Visual_Group_Member#}</span>
				{*
				{if $group_members}
					<span class="badge badge-gray">{$group_members}</span>
				{/if}
				*}
			</a>
		</li>
		{checkActionsTpl location="tpl_kahuk_group_sort_end"}
	</ul>
	{if $group_privacy eq "private" && ($isAdmin neq 1 && $isModerator neq 1 && $is_gr_Admin eq 0 && $is_gr_Moderator eq 0 && $is_group_member eq 0 && ($is_member_active eq "inactive" || $is_member_active eq ""))}
		{#KAHUK_Visual_Submit_Group_Private#} {#KAHUK_Visual_Group#}
	{else}
	<div class="tab-content" id="tabbed">
		{*Redwine: added conditional statement to display a message if no articles are published in the group*}
		{if $groupview eq "published"}
			{if !empty($group_display)}
				{$group_display}
				<div style="clear:both;"></div>
				{$group_story_pagination}
			{else}
				<strong>{#KAHUK_Visual_Group_No_Published_Stories#}</strong>
			{/if}
		{elseif $groupview eq "new"}
			{*Redwine: added conditional statement to display a message if no New articles are available*}
			{if !empty($group_display)}
				{$group_display}
				<div style="clear:both;"></div>
				{$group_story_pagination}
			{else}
				<strong>{#KAHUK_Visual_Group_No_New_Stories#}</strong>
			{/if}
		{elseif $groupview eq "shared"}
			{*Redwine: added conditional statement to display a message if no articles are yet shared to this Group*}
			{if !empty($group_shared_display)}
				{$group_shared_display}
				<div style="clear:both;"></div>
				{$group_story_pagination}
			{else}
				<strong>{#KAHUK_Visual_Group_No_Shared_Stories#}</strong>
			{/if}
		{elseif $groupview eq "members"}
			<br />
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th style="width:32px">&nbsp;</th>
						<th>Username</th>
{* Redwine: Roles and permissions and Groups fixes *}
						{*if $is_group_admin*}
							<th style="width:100px;">Role</th>
							<th style="width:75px;">Edit</th>
							<th style="width:105px;">Activation</th>
						{*/if*}
					</tr>
				</thead>
				<tbody>
					{$member_display}
				</tbody>
			</table>
		{/if}
	</div>
	{/if}
{/if}
<!--/group_story_center.tpl -->