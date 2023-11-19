<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    <div class="stories">
        {$link_summary_output}
    </div><!-- /.stories -->

    {if $link_summary_output neq ''}
        {$link_pagination}
    {/if}
</div><!-- /.main-content -->
