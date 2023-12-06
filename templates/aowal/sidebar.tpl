{php}global $hooks;{/php}
{php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_sidebar_start");{/php}

<div class="sidebar-block search-block">
    {include file=$the_template"/template-parts/search.tpl"}
</div><!-- /.search-block -->

{php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_sidebar_middle");{/php}

<div class="sidebar-block categories-block">
    <h3 class="block-header">
        <span><i class="icon icon-bullseye"></i></span>
    </h3>

    <ul class="box-content py-base-half">
    {php}
        global $main_smarty, $globalCategoriesObj;

        $args = [
			'is_hierarchical' => true,
		];
        $hierarchicalCategories = $globalCategoriesObj->get_items($args); // TODO replace cat_array variable and it's init in php file
        $main_smarty->assign('hierarchicalCategories', $hierarchicalCategories);
    {/php}
    {foreach from=$hierarchicalCategories item=catItem}
        <li class="cat-item">
            <a href="{$catItem.url}" {if $pagename eq "new" || $groupview eq "new" || $groupview eq "shared"} rel="nofollow"{/if}{if !empty($request_category) && $request_category eq $catItem.category_safe_name}class="current"{/if}>
                {$catItem.category_name}
            </a>
            {if $catItem.sub_items}
                <button class="child-toggle">
                    <i class="icon icon-bars"></i>
                </button>

                <div class="child-categories flex flex-wrap justify-center">
                    {foreach from=$catItem.sub_items item=childLabel1}
                        <span class="child-category">
                            <a href="{$childLabel1.url}"{if $pagename eq "new" || $groupview eq "new" || $groupview eq "shared"} rel="nofollow"{/if}{if !empty($request_category) && $request_category eq $childLabel1.category_safe_name} class="current"{/if}>
                                {$childLabel1.category_name} 
                            </a>
                            {if $childLabel1.sub_items}
                                <button class="dropdown-toggle" data-bs-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$childLabel1.sub_items item=childLabel2}
                                        <li>
                                            <a href="{$childLabel2.url}"{if $pagename eq "new" || $groupview eq "new" || $groupview eq "shared"} rel="nofollow"{/if}{if !empty($request_category) && $request_category eq $childLabel2.category_safe_name} class="current"{/if}>
                                                {$childLabel2.category_name}
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </span>
                    {/foreach}
                </div>
            {/if}
        </li>
    {/foreach}
    </ul>
</div><!-- /.categories-block -->

{php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_sidebar_end");{/php}
