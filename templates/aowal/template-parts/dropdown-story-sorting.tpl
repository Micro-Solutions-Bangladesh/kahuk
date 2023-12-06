<ul class="dropdown-menu w-48">
    {if $upname eq "home-published"}
        {* <li><a href="{$urls_story_durations.published.day}" title="">Today</a></li> *}
        <li><a href="{$urls_story_durations.published.week}" title="">Week</a></li>
        <li><a href="{$urls_story_durations.published.month}" title="">Month</a></li>
        <li><a href="{$urls_story_durations.published.year}" title="">Year</a></li>
    {elseif $upname eq "home-new"}
        {* <li><a href="{$urls_story_durations.new.day}" title="">Today</a></li> *}
        <li><a href="{$urls_story_durations.new.week}" title="">Week</a></li>
        <li><a href="{$urls_story_durations.new.month}" title="">Month</a></li>
        <li><a href="{$urls_story_durations.new.year}" title="">Year</a></li>
    {elseif $upname eq "home-trending"}
        {* <li><a href="{$urls_story_durations.trending.day}" title="">Today</a></li> *}
        <li><a href="{$urls_story_durations.trending.week}" class="item" title="">Week</a></li>
        <li><a href="{$urls_story_durations.trending.month}" class="item" title="">Month</a></li>
        <li><a href="{$urls_story_durations.trending.year}" class="item" title="">Year</a></li>
    {/if}
</ul>
