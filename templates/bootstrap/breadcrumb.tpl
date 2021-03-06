{************************************
******* Breadcrumbs Template ********
*************************************}
<!-- breadcrumb.tpl -->
<ul class="breadcrumb">
	<li><a href="{$kahuk_base_url}">{#KAHUK_Visual_Home#}</a></li>
	{if $pagename eq "404"}<li class="active">{#KAHUK_Visual_404_Error#}</li>{/if}
	{if $pagename eq "submit"}<li class="active">{#KAHUK_Visual_Submit#}</li>{/if}
	{if $pagename eq "submit_groups"}<li><a href="{$URL_groups}">{#KAHUK_Visual_Groups#}</a></li><li class="active">{#KAHUK_Visual_Submit_A_New_Group#}</li>{/if}
	{if $pagename eq "groups"}<li class="active">{#KAHUK_Visual_Groups#}</li>{/if}
	{if $pagename eq "editgroup"}<li><a href="{$URL_groups}">{#KAHUK_Visual_Groups#}</a></li><li><a href="{$group_story_url}">{$group_name}</a></li><li class="active">{#KAHUK_Visual_Group_Edit#}</li>{/if}
	{if $pagename eq "group_story" }<li><a href="{$URL_groups}">{#KAHUK_Visual_Groups#}</a></li><li class="active">{$group_name}</li>{/if}
	{if $pagename eq "login"}<li class="active">{#KAHUK_Visual_Login#}</li>{/if}
	{if $pagename eq "recover"}<li class="active">{#KAHUK_Visual_Breadcrumb_Recover_Password#}</li>{/if}
	{if $pagename eq "register"}<li class="active">{#KAHUK_Visual_Register#}</li>{/if}
	{if $pagename eq "editlink"}<li><a href="{$kahuk_base_url}/story.php?id={$submit_id}">{$submit_title}</a></li><li class="active">{#KAHUK_Visual_LS_Admin_Edit#}</li>{/if}
	{if $pagename eq "rssfeeds"}<li class="active">{#KAHUK_Visual_RSS_Feeds#}</li>{/if}
	{if $pagename eq "topusers"}<li class="active">{#KAHUK_Visual_TopUsers_Statistics#}</li>{/if}
	{if $pagename eq "live"}<li class="active">{#KAHUK_Visual_Live#}</li>{/if}
	{if $pagename eq "live_unpublished" || $pagename eq "live_published" || $pagename eq "live_comments"}<li><a href="{$URL_live}">{#KAHUK_Visual_Breadcrumb_Live#}</a> <span class="divider">/</span></li>{/if}
		{if $pagename eq "live_unpublished"}<li class="active">{#KAHUK_Visual_Breadcrumb_Unpublished#}</li>{/if}
		{if $pagename eq "live_published"}<li class="active">{#KAHUK_Visual_Breadcrumb_Published#}</li>{/if}
		{if $pagename eq "live_comments"}<li class="active">{#KAHUK_Visual_Breadcrumb_Comments#}</li>{/if}
	{if $pagename eq "advancedsearch"}<li class="active">{#KAHUK_Visual_Search_Advanced#}</li>{/if}
	{if $pagename eq "profile" || $pagename eq "user_edit"}
		<li><a href="{$URL_userNoVar}">{$user_login}</a></li> 
		<li class="active">{#KAHUK_Visual_Profile_ModifyProfile#}</li>
	{/if}
	{if $pagename eq "user"}
		{if $user_view eq 'search'}
			<li>{#KAHUK_Visual_User_Search_Users#}</li>
		{else}
			<li>{if $user_view neq 'profile'}<a href="{$user_url_personal_data2}">{/if}{$username}{if $user_view neq 'profile'}</a></li> {/if}
			{if $user_view neq 'profile'}<li class="active">{$page_header} <a href="{$user_rss}{if !empty($view_href)}{$view_href}{/if}" target="_blank" rel="noopener noreferrer"><i class="fa fa-rss-square opacity_reset" style="color:#EEA639;"></i></a></li>{/if}
		{/if}
	{/if}
	{if $pagename eq "published" && $get.category eq '' || $pagename eq "index"}<li class="active">{#KAHUK_Visual_Published_News#}{/if}
	{if $pagename eq "new" && $get.category eq ''}<li class="active">{#KAHUK_Visual_Kahuk_Queued#}{/if}
	{if !empty($get.category)}
		{if $pagename eq "published" || $pagename eq "index"}<li><a href="{$kahuk_base_url}">{#KAHUK_Visual_Published_News#}</a></li>{/if}
		{if $pagename eq "new"}<li><a href="{$URL_new}">{#KAHUK_Visual_Kahuk_Queued#}</a></li>{/if}
	{/if}
	{if $pagename eq "noresults"}<li class="active">{$posttitle}
	{* Redwine: modifcation to the line below to make sure the breadcrumb is set to the date or from date to date search terms instead of displaying a hyphen in SEO URL method 2 and just the date in URL method 1 *}
	{elseif isset($get.search)}<li class="active">{#KAHUK_Visual_Search_SearchResults#} &quot; {if $get.date && $get.date_to}<strong>{$get.search|replace:'/':''}</strong> <strong>FROM</strong>: {$get.date} <strong>TO</strong>: {$get.date_to} {elseif $get.date}{$get.date}{else}<strong>{$get.search|replace:'/':''}</strong> {/if}&quot;{/if}
	{if isset($get.q)}<li class="active">{#KAHUK_Visual_Search_SearchResults#} &quot;{$get.q}&quot;{/if} 
	{if $pagename eq "index" || $pagename eq "published" || $pagename eq "new" || isset($get.search) || isset($get.q)}
		{if isset($navbar_where.link2) && $navbar_where.link2 neq ""} <li> <a href="{$navbar_where.link2}">{$navbar_where.text2}</a> </li> {elseif isset($navbar_where.text2) && $navbar_where.text2 neq ""} <li> {$navbar_where.text2} </li> {/if}
		{if isset($navbar_where.link3) && $navbar_where.link3 neq ""} <li> <a href="{$navbar_where.link3}">{$navbar_where.text3}</a> </li> {elseif isset($navbar_where.text3) && $navbar_where.text3 neq ""} <li> {$navbar_where.text3} </li> {/if}
		{if isset($navbar_where.link4) && $navbar_where.link4 neq ""} <li> <a href="{$navbar_where.link4}">{$navbar_where.text4}</a> </li> {elseif isset($navbar_where.text4) && $navbar_where.text4 neq ""} <li> {$navbar_where.text4} </li> {/if}
		</li>
	{/if}
	{if !empty($posttitle) && $pagename eq "page"}<li>{$posttitle}</li>{/if}
	{checkActionsTpl location="tpl_kahuk_breadcrumb_end"}
	{if $pagename eq "published" || $pagename eq "index" || $pagename eq "new" || $pagename eq "cloud" || $pagename eq "groups" || $pagename eq "live" || $pagename eq "live_published" || $pagename eq "live_unpublished" || $pagename eq "live_comments" || $pagename eq "search"}
		{* Sort Dropdown *}
		<div class="btn-group pull-right breadcrumb-right">
			<ul class="nav nav-pills">
				<li class="dropdown pull-right">
{*Redwine: the word Sort was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">{#KAHUK_Visual_Kahuk_Queued_Sort#} <span class="caret"></span></a>
					<ul class="dropdown-menu" id="menu1">
						{if $pagename eq "published" || $pagename eq "index" || $pagename eq "new" || $pagename eq "search"}
						
							{if $setmeka eq "" || $setmeka eq "recent"}
								<li id="active"><a id="current" href="{$index_url_recent}"><span class="active">{#KAHUK_Visual_Recently_Pop#}</span></a></li>
							{else}
								<li><a href="{$index_url_recent}">{#KAHUK_Visual_Recently_Pop#}</a></li>
							{/if}
							
							{*if $user_logged_in*}
								{if $setmeka eq "upvoted"}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li id="active" href="{$index_url_upvoted}"><a href="{$index_url_upvoted}" id="current"><span class="active">{#KAHUK_Visual_Most_UpVoted#}</span></a></li>
								{else}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li><a href="{$index_url_upvoted}">{#KAHUK_Visual_Most_UpVoted#}</a></li>
								{/if}
								
								{if $setmeka eq "downvoted"}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li id="active" href="{$index_url_downvoted}"><a href="{$index_url_downvoted}" id="current"><span class="active">{#KAHUK_Visual_Most_DownVoted#}</span></a></li>
								{else}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li><a href="{$index_url_downvoted}">{#KAHUK_Visual_Most_DownVoted#}</a></li>
								{/if}
								
								{if $setmeka eq "commented"}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li id="active" href="{$index_url_commented}"><a href="{$index_url_commented}" id="current"><span class="active">{#KAHUK_Visual_User_Most_NewsCommented#}</span></a></li>
								{else}
{*Redwine: the word Most was hard-coded which made it incompatible with changing the site language. I added the needed Constantes to the lang file and applied here so when the site language changes, the terminology used are compatible*}
									<li><a href="{$index_url_commented}">{#KAHUK_Visual_User_Most_NewsCommented#}</a></li>
								{/if}
							{*/if*}
						{/if}
							
						{if $pagename eq "published" || $pagename eq "index" || $pagename eq "new"}
							{if $setmeka eq "today"}
								<li id="active" href="{$index_url_today}"><a href="{$index_url_today}" id="current"><span class="active">{#KAHUK_Visual_Top_Today#}</span></a></li>
							{else}
								<li><a href="{$index_url_today}">{#KAHUK_Visual_Top_Today#}</a></li>
							{/if}
								
							{if $setmeka eq "yesterday"}
								<li id="active"><a id="current" href="{$index_url_yesterday}"><span class="active">{#KAHUK_Visual_Yesterday#}</span></a></li>
							{else}
								<li><a href="{$index_url_yesterday}">{#KAHUK_Visual_Yesterday#}</a></li>
							{/if}
								
							{if $setmeka eq "week"}
								<li id="active"><a id="current" href="{$index_url_week}"><span class="active">{#KAHUK_Visual_This_Week#}</span></a></li>
							{else}
								<li><a href="{$index_url_week}">{#KAHUK_Visual_This_Week#}</a></li>
							{/if}
							
							{if $setmeka eq "month"}
								<li id="active"><a id="current" href="{$index_url_month}"><span class="active">{#KAHUK_Visual_This_Month#}</span></a></li>
							{else}
								<li><a href="{$index_url_month}">{#KAHUK_Visual_This_Month#}</a></li>
							{/if}
{*Redwine: add an additional Sort by from the sort button to sort the stories of the current month*}							
							{if $setmeka eq "curmonth"}
								<li id="active"><a id="current" href="{$index_url_curmonth}"><span class="active">{#KAHUK_Visual_Current_Month#}</span></a></li>
							{else}
								<li><a href="{$index_url_curmonth}">{#KAHUK_Visual_Current_Month#}</a></li>
							{/if}
							
							{if $setmeka eq "year"}
								<li id="active"><a id="current" href="{$index_url_year}"><span class="active">{#KAHUK_Visual_This_Year#}</span></a></li>
							{else}
								<li><a href="{$index_url_year}">{#KAHUK_Visual_This_Year#}</a></li>
							{/if}
							
							{if $setmeka eq "alltime"}
								<li id="active"><a id="current" href="{$index_url_alltime}"><span class="active">{#KAHUK_Visual_This_All#}</span></a></li>
							{else}
								<li><a href="{$index_url_alltime}">{#KAHUK_Visual_This_All#}</a></li>
							{/if}
							
						{elseif $pagename eq "groups"}
							
							<li {if !empty($sortby) && $sortby eq "members"}id="active"{/if}>
								{if !empty($sortby) && $sortby eq "members"}
									<a id="current" href="{$group_url_members}">
										<span class="active">{#KAHUK_Visual_Group_Sort_Members#}</span>
									</a>
								{else}
									<a href="{$group_url_members}">{#KAHUK_Visual_Group_Sort_Members#}</a>
								{/if}
							</li>							
							<li {if !empty($sortby) && $sortby eq "name"}id="active"{/if}>
								{if !empty($sortby) && $sortby eq "name"}
									<a id="current" href="{$group_url_name}">
										<span class="active">{#KAHUK_Visual_Group_Sort_Name#}</span>
									</a>
								{else}
									<a href="{$group_url_name}">{#KAHUK_Visual_Group_Sort_Name#}</a>
								{/if}
							</li>
							<li {if !empty($sortby) && $sortby eq "newest"}id="active"{/if}>
								{if !empty($sortby) && $sortby  eq "newest"}
									<a id="current" href="{$group_url_newest}">
										<span class="active">{#KAHUK_Visual_Group_Sort_Newest#}</span>
									</a>
								{else}
									<a href="{$group_url_newest}">{#KAHUK_Visual_Group_Sort_Newest#}</a>
								{/if}
							</li>
							<li {if !empty($sortby) && $sortby eq "oldest"}id="active"{/if}>
								{if !empty($sortby) && $sortby eq "oldest"}
									<a id="current" href="{$group_url_oldest}">
										<span class="active">{#KAHUK_Visual_Group_Sort_Oldest#}</span>
									</a>
								{else}
									<a href="{$group_url_oldest}">{#KAHUK_Visual_Group_Sort_Oldest#}</a>
								{/if}
							</li>
							
						{elseif $pagename eq "live" || $pagename eq "live_published" || $pagename eq "live_unpublished" || $pagename eq "live_comments"}
						
							<li {if $pagename eq "live"}id="active"{/if}>
								<a href="{$URL_live}">
									<span {if $pagename eq "live"}class="active"{/if}>{#KAHUK_Visual_Breadcrumb_All#}</span>
								</a>
							</li>
							<li {if $pagename eq "live_published"}id="active"{/if}>
								<a href="{$URL_published}">
									<span {if $pagename eq "live_published"}class="active"{/if}>{#KAHUK_Visual_Breadcrumb_Published_Tab#}</span>
								</a>
							</li>
							<li {if $pagename eq "live_unpublished"}id="active"{/if}>
								<a href="{$URL_unpublished}">
									<span {if $pagename eq "live_unpublished"}class="active"{/if}>{#KAHUK_Visual_Breadcrumb_Unpublished_Tab#}</span>
								</a>
							</li>
							<li {if $pagename eq "live_comments"}id="active"{/if}>
								<a href="{$URL_comments}">
									<span {if $pagename eq "live_comments"}class="active"{/if}>{#KAHUK_Visual_Breadcrumb_Comments#}</span>
								</a>
							</li>
							
						
						{/if}
						
					</ul>
				</li>
			</ul>
		</div>
	{/if}
</ul>
<!--/breadcrumb.tpl -->