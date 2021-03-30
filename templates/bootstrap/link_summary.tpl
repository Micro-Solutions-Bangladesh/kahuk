<!-- link_summary.tpl -->

{if $Use_New_Story_Layout eq '1'}
    {include  file=$the_template"/link_summary_new.tpl"}
{else}
    {include  file=$the_template"/link_summary_traditional.tpl"}
{/if}