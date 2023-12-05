{php}global $hooks;{/php}
<div class="main-content col mt-base">
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_register_complete_start");{/php}
    <p>
        {#KAHUK_Visual_Register_Thankyou#|sprintf:$get.user}
        {#KAHUK_Visual_Register_Noemail#}
    </p>

    <ul class="list-disc pl-base mt-4">
        {#KAHUK_Visual_Register_ToDo#|sprintf:$site_email_contact}
    </ul>
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_register_complete_end");{/php}
</div>
