<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	

{if $meta_description neq ""}
	<meta name="description" content="{$meta_description|truncate:"300"}" />
{elseif $pagename eq "search"}
	<meta name="description" content="{#KAHUK_Visual_Search_SearchResults#} {$templatelite.get.search|sanitize:2|stripslashes}" />
{else}
	<meta name="description" content="{#KAHUK_Visual_What_Is_Kahuk_Text#|sanitize:1}" />
{/if}

{if $meta_keywords neq ""}
	<meta name="keywords" content="{$meta_keywords}" />
{elseif $pagename eq "search"}
	<meta name="keywords" content="{$templatelite.get.search|sanitize:2|stripslashes}" />
{else}
	<meta name="keywords" content="{#KAHUK_Visual_Meta_Keywords#}" />
{/if}

<meta name="Language" content="{#KAHUK_Visual_Meta_Language#}" />
<meta name="Robots" content="All" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="generator" content="Kahuk" />

<!-- Open Graph Protocol  & Twitter Cards -->

{if $pagename eq "index"}
	<meta property="og:title" content="{#KAHUK_Visual_Name#} | {#KAHUK_Visual_RSS_Description#}" />
	<meta name="twitter:title" content="{#KAHUK_Visual_Name#} | {#KAHUK_Visual_RSS_Description#}" />
{else}
	<meta property="og:title" content="{$posttitle}" />
	<meta name="twitter:title" content="{$posttitle}" />
{/if}

	{* we might encounter a situation where there is no description content, like in the case of using the rss_importer module. So we provision for this case by assigning the KAHUK_Visual_What_Is_Kahuk_Text as description*}
<meta property="og:description" content="{if $meta_description neq ""}{$meta_description|truncate:300}{else}{#KAHUK_Visual_What_Is_Kahuk_Text#|sanitize:1}{/if}" />

{if $pagename eq "story"}
	<meta property="og:url" content="{$my_base_url}{$story_url}" />
	<meta name="twitter:url" content="{$my_base_url}{$story_url}" />
{else}
	<meta property="og:url" content="{$my_base_url}{$navbar_where.link1}" />
	<meta name="twitter:url" content="{$my_base_url}{$navbar_where.link1}" />
{/if}

<meta property="og:image" content="{php} if ($_SESSION['uploaded_image'] != '') {echo $_SESSION['uploaded_image'];}else{echo my_base_url.my_kahuk_base.Default_Site_OG_Image;}{/php}" />	

<meta property="og:image:width" content="{php} echo Default_OG_Image_Width;{/php}" />
<meta property="og:image:height" content="{php} echo Default_OG_Image_Height;{/php}" />
<meta name="twitter:image" content="{php} if ($_SESSION['uploaded_image'] != '') {echo $_SESSION['uploaded_image'];}else{echo my_base_url.my_kahuk_base.Default_Site_Twitter_Image;}{/php}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:description" content="{if $meta_description neq ""}{$meta_description|truncate:300}{else}{#KAHUK_Visual_What_Is_Kahuk_Text#|sanitize:1}{/if}" />
