<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if isset($user_page)}
        <div class="list-items flex flex-col">
            {$user_page}
        </div>

        {if $user_page eq ''}
            <div class="p-8 bordered border-deep-50">
                <p>{#KAHUK_User_Profile_No_Content#}</p>
            </div>
        {/if}
    {/if}

    {if isset($user_pagination) && $user_page neq ''}
        {$user_pagination}
    {/if}
</div>