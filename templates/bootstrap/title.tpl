<!-- title.tpl -->
{if preg_match('/index.php$/',$templatelite.server.SCRIPT_NAME)}
	{if !empty($get.category)}
		{if !empty($get.page) && $get.page > 1}
			<title>{$navbar_where.text2} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Breadcrumb_Published_Tab#} | {#KAHUK_Visual_Name#}</title>
		{else}
			<title>{$navbar_where.text2} | {#KAHUK_Visual_Breadcrumb_Published_Tab#} | {#KAHUK_Visual_Name#}</title>
		{/if}
	{elseif !empty($get.page) && $get.page > 1}
		<title>{#KAHUK_Visual_Breadcrumb_Published_Tab#} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Name#}</title>
	{else}
		<title>{#KAHUK_Visual_Name#} | {#KAHUK_Visual_RSS_Description#}</title>
	{/if}
{elseif preg_match('/new.php$/',$templatelite.server.SCRIPT_NAME)}
	{if !empty($get.category)}
		{if !empty($get.page) && $get.page > 1}
			<title>{$navbar_where.text2} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Breadcrumb_Unpublished_Tab#} | {#KAHUK_Visual_Name#}</title>
		{else}
			<title>{$navbar_where.text2} | {#KAHUK_Visual_Breadcrumb_Unpublished_Tab#} | {#KAHUK_Visual_Name#}</title>
		{/if}
	{elseif !empty($get.page) && $get.page > 1}
		<title>{#KAHUK_Visual_Breadcrumb_Unpublished_Tab#} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Name#}</title>
	{else}
		<title>{#KAHUK_Visual_Breadcrumb_Unpublished_Tab#} | {#KAHUK_Visual_Name#}</title>
	{/if}
{elseif preg_match('/submit.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Submit#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/live.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Live#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/live_unpublished.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Live#} {#KAHUK_Visual_Breadcrumb_Unpublished#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/live_published.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Live#} {#KAHUK_Visual_Breadcrumb_Published#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/live_comments.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Live#} {#KAHUK_Visual_Comments#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/editlink.php$/',$templatelite.server.SCRIPT_NAME)}	
	<title>{#KAHUK_Visual_EditStory_Header#}: {$submit_title} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/advancedsearch.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Search_Advanced#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/rssfeeds.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_RSS_Feeds#} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/search.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{#KAHUK_Visual_Search_SearchResults#} &quot;{if $get.search}{$get.search}{else}{$get.date}{/if}&quot; | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/groups.php$/',$templatelite.server.SCRIPT_NAME)}
	{if !empty($get.page) && $get.page > 1}
		<title>{#KAHUK_Visual_Groups#} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Name#}</title>
	{else}
		<title>{#KAHUK_Visual_Groups#} | {#KAHUK_Visual_Name#}</title>
	{/if}
{elseif preg_match('/editgroup.php$/',$templatelite.server.SCRIPT_NAME)}
	<title>{$group_name} | {#KAHUK_Visual_Name#}</title>
{elseif preg_match('/group_story.php$/',$templatelite.server.SCRIPT_NAME)}
	{if $groupview!='published'}
		{if $groupview eq "new"}
			{assign var='tview' value=#KAHUK_Visual_Group_New#}
		{elseif $groupview eq "shared"}
			{assign var='tview' value=#KAHUK_Visual_Group_Shared#}
		{elseif $groupview eq "members"}
			{assign var='tview' value=#KAHUK_Visual_Group_Member#}
		{/if}

		{if !empty($get.page) && $get.page > 1}
			<title>{$group_name} | {if !empty($get.category)}{$navbar_where.text2} | {/if}{$tview} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Name#}</title>
		{else}
			<title>{$group_name} | {if !empty($get.category)}{$navbar_where.text2} | {/if}{$tview} | {#KAHUK_Visual_Name#}</title>
		{/if}
	{elseif !empty($get.page) && $get.page > 1}
		<title>{$group_name} | {#KAHUK_Page_Title#} {$get.page} | {#KAHUK_Visual_Name#}</title>
	{else}
		<title>{$group_name} - {$group_description} | {#KAHUK_Visual_Name#}</title>
	{/if}
{elseif $pagename eq "register_complete"}
	<title>{#KAHUK_Validate_user_email_Title#} | {#KAHUK_Visual_Name#}</title>
{elseif $pagename eq "404"}
	<title>{#KAHUK_Visual_404_Error#} | {#KAHUK_Visual_Name#}</title>
{else}	
	<title>{if !empty($posttitle)}{$posttitle}{/if} {if !empty($pretitle)} | {$pretitle}{/if}| {#KAHUK_Visual_Name#}</title>
{/if}
<!-- /title.tpl -->