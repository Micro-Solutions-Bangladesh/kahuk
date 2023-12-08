{php}global $hooks;{/php}
<!DOCTYPE html>
<html lang="en">
<head>
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_after_head_start");{/php}

    {include file=$the_template"/template-parts/page-title.tpl"}

    {include file=$the_template"/template-parts/header-meta.tpl"}

    {include file=$the_template"/template-parts/header-styles.tpl"}

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        window.jQuery ||
            document.write(
                '<script src="{$my_kahuk_base}/templates/{$the_template}/assets/js/jquery-3.6.0.min.js"><\/script>'
            );
    </script>

    <script>
        var my_kahuk_base='{$my_kahuk_base}';
        var kahuk_url_ajax='{$kahuk_url_ajax}';
    </script>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_before_head_end");{/php}
</head>

<body class="{php}kahuk_body_css_classes();{/php}" dir="{#KAHUK_Visual_Language_Direction#}" {if isset($body_args)}{$body_args}{/if}>
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_after_body_start");{/php}

    <div class="page-wrap flex flex-col">
        {include file=$tpl_header".tpl"}

        {if $pagename eq "home" && $page_task == "published"}
            {include file=$the_template"/template-parts/hero-carousel.tpl"}
        {/if}

        {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_before_page_content");{/php}

        <div class="page-content">
            <div class="container">
                <div class="content-wrapper row">
                    {include file=$tpl_center.".tpl"}
                
                    {if $pagename neq "submit" && $pagename neq "user" && $pagename neq "profile" && $pagename neq "user_edit" && $pagename neq "register" && $pagename neq "login"}
                        <div class="sidebar col-auto">
                            {include file=$tpl_first_sidebar.".tpl"}

                            {include file=$tpl_second_sidebar.".tpl"}
                        </div><!-- /.sidebar -->
                    {/if}
                </div>
            </div>
        </div>
        <!-- /.page-content -->

        {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_after_page_content");{/php}

        {include file=$tpl_footer.".tpl"}
    </div><!-- /.page-wrap -->

    {if $user_authenticated neq true}
        {include file=$the_template"/template-parts/login-modal.tpl"}
    {/if}

    <script src="{$my_kahuk_base}/templates/{$the_template}/assets/js/tw-elements.min.js"></script>
    <script src="{$my_kahuk_base}/templates/{$the_template}/assets/js/custom.js"></script>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_before_body_end");{/php}
</body>
</html>
