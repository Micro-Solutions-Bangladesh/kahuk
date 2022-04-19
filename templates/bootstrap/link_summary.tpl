{checkActionsTpl location="tpl_kahuk_story_start"}
{if $Use_New_Story_Layout eq 'default'}
    {include  file=$the_template"/link_summary_traditional.tpl"}
{else}
    {include  file=$the_template"/link_summary_new.tpl"}
{/if}
{checkActionsTpl location="tpl_kahuk_story_end"}
