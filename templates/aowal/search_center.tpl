<div class="main-content col mt-base">
    <nav class="breadcrumbs">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $session_messages  neq ''}
        <div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
        </div>
    {/if}

    <div class="stories">
    {if $stories_markup neq ''}
        {$stories_markup}
    {else}
        <p>Sorry! nothing found with the term <strong>{$search}</strong>.</p>
    {/if}        
    </div><!-- /.stories -->

    {if $pagination neq ''}
        <div class="pagination-wrapper mt-6">
            {$pagination}
        </div>
    {/if}
</div><!-- /.main-content -->
