<footer itemscope="itemscope" itemtype="http://schema.org/WPFooter"
    class="footer bg-deep-100 text-light-100 border-t-4 border-t-deep-105 mt-base-double">
    <div class="container flex flex-wrap">
        <div class="footer-links w-full lg:w-1/2 flex flex-wrap">

            <div class="w-full mt-base sm:w-1/2">
                <h2 class="text-lg font-bold mb-base-half">Useful Links</h2>
                <ul>
                    {if $user_logged_in eq ""}
                        <li>
                            <a href="{$page_login_url}">
                                <i class="icon icon-link-1"></i> {#KAHUK_Visual_Login_Title#}
                            </a>
                        </li>
                    {else}
                        <li>
                            <a href="{$page_logout_url}">
                                <i class="icon icon-link-1"></i> {#KAHUK_Visual_Logout#}
                            </a>
                        </li>
                    {/if}
                    
                    <li>
                        <a href="{$page_submit_url_begin}" title="{#KAHUK_LANG_SUBMIT#}"
                            {if $user_logged_in eq ""}data-bs-toggle="modal" data-bs-target="#LoginModal"{/if}
                        >
                            <i class="icon icon-link-1"></i> {#KAHUK_LANG_SUBMIT#}
                        </a>
                    </li>

                    <li>
                        <a href="{$page_topusers_url}" title="{#KAHUK_Visual_Top_Users#}">
                            <i class="icon icon-link-1"></i> {#KAHUK_Visual_Top_Users#}
                        </a>
                    </li>

                    <li>
                        <a href="{$page_rssfeeds_url}" title="{#KAHUK_Visual_RSS_Feeds#}">
                            <i class="icon icon-link-1"></i> {#KAHUK_Visual_RSS_Feeds#}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="w-full mt-base sm:w-1/2">{* TODO: dynamic from admin *}
                <h2 class="text-lg font-bold mb-base-half">Connect With Us</h2>
                <ul>
                    <li>
                        <a href="https://www.facebook.com/KahukCMS" title="{$site_name} on Facebook">
                            <i class="icon icon-facebook"></i> Facebook
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/" title="{$site_name} on Twitter">
                            <i class="icon icon-twitter"></i> Twitter
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{$site_email_contact}" title="Contact {$site_name}">
                            <i class="icon icon-link-1"></i> Contact Us
                        </a>
                    </li>
                </ul>
            </div>
        </div><!-- /.footer-links -->

        <div class="footer-about w-full lg:w-1/2 mt-base">
            <h2 class="text-lg font-bold mb-base-half">About {$site_name}</h2>
            {#KAHUK_Visual_What_Is_Kahuk_Text#}

            {if $Default_Site_Logo neq ""}
                <a class="block py-4" href="{$kahuk_base_url}">
                    <img class="h-10 mx-auto" src="{$Default_Site_Logo}" alt="{$site_name}">
                </a>
            {/if}
        </div><!-- /.footer-about -->
    </div>

    <div class="w-full border border-t-deep-95 border-b-deep-105 mt-base"></div>

    <div class="container">
        <div class="copy-right w-full text-center pt-base-half pb-base">
            Copyright &copy; {php}echo date("Y");{/php} {$site_name}.
            <br>
            Made With <a href="https://kahuk.com/" rel="external" target="_blank">Kahuk CMS</a>
        </div><!-- /.footer-credits -->
    </div>
</footer>