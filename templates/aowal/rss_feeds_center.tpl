<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    <table class="table header-center col-bordered my-8">
        <thead>
            <tr>
                <th>Category</th>
                <th>RSS URL</th>
            </tr>
        </thead>
        <tbody>
            {section name=thecat loop=$cat_array}
                {if $lastspacer eq ""}
                    {assign var=lastspacer value=$cat_array[thecat].spacercount}
                {/if}

                <tr>
                    <td><a href="{$URL_rsscategory, $cat_array[thecat].category_id}" target="_blank" rel="noopener noreferrer"
                            class="rss_category"><i class="fa fa-rss-square opacity_reset" style="color:#EEA639;"></i></a>
                        <a href="{$URL_rsscategory, $cat_array[thecat].category_id}" target="_blank" rel="noopener noreferrer"
                            style="border:none;">{$cat_array[thecat].category_name}</a></td>
                    <td><input type="text" class="form-control col-md-4 rss_url"
                            value="{$my_base_url}{$URL_rsscategory, $cat_array[thecat].category_id}"></td>
                </tr>

                {assign var=lastspacer value=$cat_array[thecat].spacercount}
            {/section}
        </tbody>
    </table>
</div>