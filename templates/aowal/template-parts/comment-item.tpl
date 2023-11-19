<div id="c{$the_comment.comment_id}" class="comment-item flex">
    <div class="flex-shrink-0 mr-3">
        <img class="mt-2 rounded-full w-8 h-8 sm:w-10 sm:h-10" src="{$the_comment.user_gravatars.medium}" alt="{$the_comment.user_names}">
    </div>
    <div class="flex-1 border rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed">
        <a href="{$the_comment.user_profile_url}"><strong>{$the_comment.user_names}</strong></a> 
        <span class="text-xs text-gray-400">{$the_comment.comment_age} ago</span>
        {$the_comment.comment_content|kahuk_autop}
    </div>
</div>
