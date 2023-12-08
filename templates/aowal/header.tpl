<header class="header dark-combination bg-secondary-700 text-white" itemscope="itemscope"
    itemtype="http://schema.org/WPHeader">
    <div class="container flex flex-wrap items-center justify-between text-base uppercase">
        <h1 class="logo flex-initial w-auto order-1 py-4" itemscope="itemscope"
            itemtype="http://schema.org/Organization">
            <a class="block" href="{$kahuk_base_url}" itemprop="url" rel="home" title="{$site_name}">
                {if $site_logo eq ""}
                    {$site_name}
                {else}
                    <img src="{$site_logo}" alt="{$site_name}" class="h-6" />
                {/if}
            </a>
        </h1>

        <div class="nav-visitor w-auto flex flex-initial order-4">
            {if $user_logged_in eq ""}
                <a class="btn-primary rounded-full mr-2" href="{$page_login_url}" data-bs-toggle="modal" data-bs-target="#LoginModal">
                    {#KAHUK_Visual_Login_Title#}
                </a>
                <a class="btn-primary rounded-full" href="{$page_register_url}">{#KAHUK_Visual_Register#}</a>
            {else}
                {php}
                    global $main_smarty, $current_user;

                    if ($current_user->user_id > 0 && $current_user->authenticated) {
                            $login=$current_user->user_login;
                    }

                    // Read the users information from the database
                    $user=new User();
                    $user->username = $login;
                    if(!$user->read()) {
                        echo "invalid user";
                        die;
                    }

                    // Assign smarty variables to use in the template.
                    $avatar_all = kahuk_gravatar( $user->email, ['note' => 'header.tpl file'] );
                    $main_smarty->assign('Avatar', $avatar_all);

                    $main_smarty->assign('user_names', $user->names);
                    $main_smarty->assign('user_id', $user->id);
                    $main_smarty->assign('user_username', $user->username);
                {/php}
                <div class="user-profile-links">
                    <a class="dropdown-toggle flex items-center" data-bs-toggle="dropdown" href="#">
                        <img src="{$Avatar.small}" class="w-6 h-6 mr-2 rounded-full" />
                        <span class="mr-2">{$user_logged_in}</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu w-48">
                        <li><a href="{$page_profile_url}">{#KAHUK_Visual_Profile#}</a></li>
                        <li><a href="{$page_profile_settings}"><span>{#KAHUK_Visual_User_Setting#}</a></li>

                        <hr class="dropdown-hr" />

                        {if isset($isadmin) && $isadmin eq 1}
                            <li><a href="{$kahuk_dashboard_url}">{#KAHUK_Visual_Dashboard#}</a></li>
                        {/if}

                        <li><a href="{$page_logout_url}">{#KAHUK_Visual_Logout#}</a></li>
                    </ul>
                </div><!-- /.user-profile-links -->
            {/if}
        </div>

        <nav class="nav-main" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement"
            aria-labelledby="navMainHeading">
            <span class="just-top-border flex lg:hidden border-t border-deep-105 lg:border-0"></span>

            <button id="nav-main-toggle" class="md:hidden uppercase flex items-center text-base h-12">
                <i class="icon icon-bars mr-2"></i>
                <span>Menu</span>
            </button>

            <ul class="nav-main-items list-caret list-caret-hover">
                <li itemprop="name" {if $pagename eq "published" || $pagename eq "index"}class="current"{/if}>
                    <a itemprop="url" href="{$kahuk_base_url}" title="{#KAHUK_Visual_Home#}">
                        <i class="icon icon-home"></i>
                        <span>{#KAHUK_Visual_Home#}</span>
                    </a>
                </li>
                <li itemprop="name" {if $pagename eq "trending"}class="current"{/if}>
                    <a itemprop="url" href="{$page_trending_story_url}" title="{#KAHUK_Visual_Trending_Stories#}">
                        <i class="icon icon-bolt"></i>
                        <span>{#KAHUK_Visual_Trending_Stories#}</span>
                    </a>
                </li>
                <li itemprop="name" {if $pagename eq "new"}class="current"{/if}>
                    <a itemprop="url" href="{$page_new_story_url}" title="{#KAHUK_Visual_New_Stories#}">
                        <i class="icon icon-bolt"></i>
                        <span>{#KAHUK_Visual_New_Stories#}</span>
                    </a>
                </li>
                {if $enable_group eq "true"}
                    <li itemprop="name"  {if $pagename eq "groups" || $pagename eq "submit_groups" || $pagename eq "group_story"}class="current"{/if}>
                        <a itemprop="url" href="{$page_groups_url}" title="{#KAHUK_Visual_Groups#}">
                            <i class="icon icon-users"></i>
                            <span>{#KAHUK_Visual_Groups#}</span>
                        </a>
                    </li>
                {/if}
                {* <li itemprop="name" {if $pagename eq "topusers"}class="current"{/if}>
                    <a itemprop="url" href="{$page_topusers_url}" title="{#KAHUK_Visual_Top_Users#}">
                        <i class="icon icon-prize-award"></i>
                        <span>{#KAHUK_Visual_Top_Users#}</span>
                    </a>
                </li> *}
                
                <li itemprop="name" {if $pagename eq "submit"}class="current"{/if}>
                    <a itemprop="url" href="{$page_submit_url_begin}" title="{#KAHUK_LANG_SUBMIT#}"
                        {if $user_logged_in eq ""}data-bs-toggle="modal" data-bs-target="#LoginModal"{/if}
                    >
                        <i class="icon icon-plus"></i>
                        <span>{#KAHUK_LANG_SUBMIT#}</span>
                    </a>
                </li>

                
            </ul>
        </nav>
    </div>
    <!-- /.container -->
</header>