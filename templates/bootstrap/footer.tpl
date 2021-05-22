{************************************
********** Footer Template **********
*************************************}
<!-- footer.tpl -->
<div id="footer">
	{checkActionsTpl location="tpl_kahuk_footer_start"}
	<span class="subtext"> 
		Copyright &copy; {php} echo date('Y'); {/php} {#KAHUK_Visual_Name#}
		| <a href="{$URL_advancedsearch}">{#KAHUK_Visual_Search_Advanced#}</a> 
		{if $Enable_Live}
			| <a href="{$URL_live}">{#KAHUK_Visual_Live#}</a>
		{/if}
		| <a href="{$URL_topusers}">{#KAHUK_Visual_Top_Users#}</a>
		| Made with <a href="https://kahuk.com/" target="_blank" rel="noopener noreferrer">Kahuk CMS</a> 
		{if !empty($URL_rss_page)}
			| <a href="{$URL_rss_page}" target="_blank" rel="noopener noreferrer">{$pagename|capitalize} RSS Feed</a>
		{/if}
		| <a href="{$kahuk_base_url}/rssfeeds.php">{#KAHUK_Visual_RSS_Feeds#}</a> 
	</span>
	{checkActionsTpl location="tpl_kahuk_footer_end"}
</div>
<!--/footer.tpl -->