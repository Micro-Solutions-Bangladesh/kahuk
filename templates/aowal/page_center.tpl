<div class="main-content col mt-base">
    {$page_content|kahuk_autop}
    
    {if $page_dynamic_page_edit_url}
        <div class="edit">
            <a class="btn btn-primary" href="{$page_dynamic_page_edit_url}">
                {#KAHUK_Visual_AdminPanel_Page_Edit#}
            </a>
        </div>
    {/if}
</div>