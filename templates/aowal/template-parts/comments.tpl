<div class="comment-items antialiased mx-auto">
    <h3 class="mb-4 text-lg font-semibold text-gray-900">{#KAHUK_Visual_Story_Comments#}</h3>

    <div class="space-y-4 mb-8">
        {$the_comments_markup}
    </div>
</div>

{if $user_authenticated}
    {$comment_form_markup}
{else}
    <h3 class="login-to-comment mt-6 text-2xl text-center">
        <a href="{$login_url}" data-bs-toggle="modal" data-bs-target="#LoginModal">{#KAHUK_Visual_Story_LoginToComment#}</a> {#KAHUK_Visual_Story_Register#} <a
            href="{$page_register_url}">{#KAHUK_Visual_Story_RegisterHere#}
        </a>.
    </h3>
{/if}
