<div class="main-content col mt-base">
    <h1 class="story-title">{#KAHUK_Visual_Top_Users#}</h1>

    <table class="tablesorter table table-bordered table-striped text-center" id="tablesorter-demo">
        <thead>
            <tr>
                <th>{#KAHUK_Visual_TopUsers_TH_User#}</th>
                <th>{#KAHUK_Visual_TopUsers_TH_News#}</th>
                <th>{#KAHUK_Visual_TopUsers_TH_PublishedNews#}</th>
                <th>{#KAHUK_Visual_TopUsers_TH_Comments#}</th>
                <th>{#KAHUK_Visual_TopUsers_TH_TotalVotes#}</th>
                <th>{#KAHUK_Visual_TopUsers_TH_Karma#}</th>
            </tr>
        </thead>

        {$users_table_body}
    </table>

    {$topusers_pagination}
</div>