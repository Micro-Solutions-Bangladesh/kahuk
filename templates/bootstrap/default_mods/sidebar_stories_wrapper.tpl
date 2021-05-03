{if $ss_body ne ''}
<div class="headline">
	<div class="sectiontitle"><a href="{$kahuk_base_url}{if $pagename eq "index"}/new.php{/if}">{$ss_header}</a></div>
</div>
<div class="boxcontent">
	<ul class="sidebar-stories">
		{$ss_body}
	</ul>
</div>
{/if}