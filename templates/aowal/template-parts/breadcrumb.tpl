{php}global $hooks;{/php}
<ol class="breadcrumb flex flex-auto">
    {php}
        global $main_smarty;

        $pageBredcrumbs = kahuk_breadcrumbs();
        $main_smarty->assign('bredcrumbs', $pageBredcrumbs);
    {/php}

    {foreach from=$bredcrumbs item=bredcrumb}
        {if $bredcrumb.url}
            <li><a href="{$bredcrumb.url}">{$bredcrumb.title}</a></li>
        {else}
            <li class="active">{$bredcrumb.title}</li>
        {/if}
    {/foreach}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_breadcrumb_end");{/php}
</ol>

{if ($upname eq "home-published") || ($upname eq "home-new") || ($upname eq "home-trending") }
    <div class="dropdown relative">
        <button type="button" data-bs-toggle="dropdown" class="dropdown-toggle flex items-center whitespace-nowrap">
            {#KAHUK_Visual_Kahuk_Queued_Sort#} <span class="caret"></span>
        </button>

        {include file=$the_template"/template-parts/dropdown-story-sorting.tpl"}
    </div>
{/if}
