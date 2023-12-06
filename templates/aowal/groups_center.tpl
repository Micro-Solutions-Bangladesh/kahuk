<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $session_messages  neq ''}
		<div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
		</div>
    {/if}

    <div class="mb-base">
        <h2 class="section-title">{#KAHUK_Visual_Group_Explain_title#}</h2>
        <div class="group_explain_description">
            {#KAHUK_Visual_Group_Explain#}
        </div>

        <div class="flex flex-wrap justify-between mt-6">
            {if $group_allow eq "1"}
                <div class="w-auto mr-2">
                    <a href="{$page_group_submit_url}" class="btn btn-primary {if !empty($error_max)}disabled{/if}"
                        title="{if empty($error_max)}{#KAHUK_Visual_Submit_A_New_Group_Error#}{else}{#KAHUK_Visual_Submit_A_New_Group#}{/if}">
                        {#KAHUK_Visual_Submit_A_New_Group#}
                    </a>
                </div>
            {/if}

            {if !empty($get.keyword)}
                {assign var=searchboxtext value=$get.keyword}
            {else}
                {assign var=searchboxtext value=''}
            {/if}

            <div class="w-auto">
                <form action="{$my_kahuk_base}/groups/" method="get">
                    <fieldset class="group-fields">
                        <input type="search" name="keyword" id="keyword" class="form-control"
                            placeholder="{#KAHUK_Visual_Search_SearchDefaultText#}" value="{$searchboxtext}">
                        <button type="submit" class="btn">
                            <span class="visually-hidden">{#KAHUK_Visual_Group_Search_Groups#}</span>
                            <i class="icon icon-search-1"></i>
                        </button>

                        <input type="hidden" name="view" value="search">
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    {if !empty($get.keyword)}
        {if !empty($group_display)}
            <h4 class="font-bold">{#KAHUK_Visual_Search_SearchResults#} &quot;{$search}&quot;</h4>
        {else}
            <h4 class="font-bold">{#KAHUK_Visual_Search_NoResults#} &quot;{$search}&quot;</h4>
        {/if}
        <hr class="mb-base" />
    {/if}

    {if !empty($groups_markup)}
        <div class="groups flex flex-col">
            {$groups_markup}
        </div>
    {/if}

    {if !empty($pagination)}
        <div class="pagination-wrapper mt-6">
            {$pagination}
        </div>
    {/if}
</div>