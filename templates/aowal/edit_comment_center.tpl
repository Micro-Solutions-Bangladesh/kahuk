<div class="main-content col mt-base">
    {$the_story}
    <br />
    <h3>{#KAHUK_Visual_Story_Comments#}</h3>
    <form action="" method="POST" id="thisform">
        <ol class="comment-list">
            {$the_comment}
            {$comment_form}
            <input type="hidden" name="process" value="newcomment" />
            <input type="hidden" name="randkey" value="{$randkey}" />
            <input type="hidden" name="link_id" value="{$link_id}" />
            <input type="hidden" name="user_id" value="{$user_id}" />
        </ol>
    </form>
</div>