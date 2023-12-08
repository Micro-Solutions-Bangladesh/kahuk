<!-- settings_help.tpl -->
{literal}
<style>
.th{text-align:center;font-style:italic;}
fieldset {
width:100%;
-webkit-border-radius: 8px;
-moz-border-radius: 8px;
border-radius: 8px;
background-color: #183a52;
color:#ffffff;
-webkit-box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    7px 7px 5px 0px rgba(50, 50, 50, 0.75);
box-shadow:         7px 7px 5px 0px rgba(50, 50, 50, 0.75);
padding: 10px;
margin-bottom:20px;
}
label {padding: 3px;display: block;}
legend {
width: auto;
background: #f1f1a5;
color:#000000;
font-weight:bold;
border: solid 1px black;
-webkit-border-radius: 8px;
-moz-border-radius: 8px;
border-radius: 8px;
padding: 6px;
font-size: 0.9em;
}
.centerText{text-align:center;}
.configured{background-color:#80e68d;}
.notconfigured{background-color:#ec9cab;}
input[type="radio"] {margin-right:10px;}
@media screen and (max-width: 360px) {
	.row{margin-left:-11px;margin-right:-11px;}
}
</style>
{/literal}
<legend>Settings Help</legend>
<p>This page intends to help Admins configure their site. It lists the MUST HAVE, MUST DO and the recommended settings to enhance the performance of your site! Check the settings and features and decide what to activate and use.</p> 
<p><span style="background-color:#80e68d;padding:3px;border:1px solid #000;">This color means the setting is configured.</span>
<br /><br /><span style="background-color:#ec9cab;padding:3px;border:1px solid #000;">This color means the setting is not configured.</span></p><br /><br />
<legend class="centerText">MUST CONFIGURE</legend>

    
{*SMTP email*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Location%20Installed" target="_blank">SMTP Email</a>:</legend>
        <p>If you want to use the SMTP settings of your server's email, you can follow the instructions to set it up or to change it.<br />SMTP settings will allow to send SMTP emails, as well as testing, on LOCALHOST, all the features that sends an email to your users. Testing on LOCALHOST has an additional option to configure, which prints out the email sent on the page, and only Admins can view it.<br />The relevant configurations are:<br /></p>
        <label>Debug email send?</label>
        <input type="text" name="_email_debug" 
            value="{$emailDebug}" disabled
            class="form-control {if $emailDebug eq 'true'}configured{else}notconfigured{/if}"
        />
		<br />
        <label>SMTP Host</label>
        <input type="text" name="_smtp_host" 
            value="{$hostSMTP}" disabled
            class="form-control {if $hostSMTP neq ''}configured{else}notconfigured{/if}"
        />
		<br />
        <label>SMTP Port</label>
        <input type="text" name="_smtp_port" 
            value="{$portSMTP}" disabled
            class="form-control {if $portSMTP neq ''}configured{else}notconfigured{/if}"
        />
		<br />
        <label>SMTP Password</label>
        <input type="text" name="_smtp_pass" 
            value="{$passSMTP}" disabled
            class="form-control {if $passSMTP neq ''}configured{else}notconfigured{/if}"
        />
	</fieldset>    
    
{*Logo, openGraph and Twitter Card*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Logo" target="_blank">Logo, openGraph and Twitter Card</a>:</legend>
        <p>If you want to use a Logo in place of the site's name, click on the title above and configure the Logo, openGraph and Twitter Card</p>

        <label>OpenGraph og:image</label>
		<select name="use_open_graph" class="form-control {if $useopenGraph neq ''}configured{else}notconfigured{/if}">					
            <option selected>{$useopenGraph}</option>
		</select><br />
        <label>Twitter:image</label>
		<select name="twitter_image" class="form-control {if $useTwittercard neq ''}configured{else}notconfigured{/if}">					
            <option selected>{$useTwittercard}</option>
		</select><br />
	</fieldset>  

{*Misc settings*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Misc" target="_blank">Miscellaneous Settings</a>:</legend>
        <p>To set or change any of these settings, click on the title above. It is recommended to set the following settings:</p>
        <label>Validate user email</label>
        <p>By default, it is set to false. If you set it to true, then you have to have a site's email configured.</p>
		<select name="validate_userEmail" class="form-control {if $validateuserEmail eq 'true'}configured{else}notconfigured{/if}">					
            <option selected>{$validateuserEmail}</option>
		</select><br />
        <label>Validate user password</label>
        <p>By default, it is set to true. If you want to deactivate this feature, then set it to false (not recommended)<br />When registering/password reset, it checks if the password is safe and not pwned, using "Have I Been Pwned?" database.<br />If the provided password has been pwned, the registration is not submitted until they provide a different password!<br />Yoiu really don't want users to register with hacked passwords; it will be very easy to hack their accounts and Spam your site!</p>
		<select name="validate_userPass" class="form-control {if $validateuserPass eq 'true'}configured{else}notconfigured{/if}">					
            <option selected>{$validateuserPass}</option>
		</select><br />
	</fieldset>  

{*Recommended configuration*}
<legend class="centerText">RECOMMENDED CONFIGURATION</legend>
<fieldset>
    <p>The following are recommended configurations. To configure, click on the title of each.</p>
</fieldset>

{*Submit settings*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Submit" target="_blank">Submit Settings</a>:</legend>
        <label>Allow Draft Articles?</label>
        <p>Under Submit Settings, Admins can enable submitting <strong><u>Draft</u></strong> stories that can later be published, by the author,  manually by changing the status of the story.</p>
        <select name="submitDraft" class="form-control {if $submitDraft eq true}configured{else}notconfigured{/if}">					
            <option selected>{$submitDraft}</option>
		</select><br />
        <label>Allow Scheduled Articles?</label>
         <p>Under Submit Settings, Admins can enable submitting <strong><u>Scheduled</u></strong> stories that can later be published, by the author,  manually by changing the status of the story.</p>
        <select name="submitScheduled" class="form-control {if $submitScheduled eq true}configured{else}notconfigured{/if}">					
            <option selected>{$submitScheduled}</option>
		</select><br />
        <label>Allow multiple categories</label>
         <p>Under Submit Settings, Admins can enable multiple categories for the stories.</p>
        <select name="submitMultiCat" class="form-control {if $submitMultiCat eq true}configured{else}notconfigured{/if}">					
            <option selected>{$submitMultiCat}</option>
		</select><br />
        <label>Require a URL when Submitting</label>
         <p>Under Submit Settings, Require a URL when Submitting is by default set to true. Admins can set it to false to allow both Editorial and URL story submitting. <strong>NOTE THAT IF YOU WANT BOTH OPTIONS, YOU HAVE TO KEEP THE BELOW SETTING (Show the URL Input Box) SET TO TRUE. HOWEVER, IF YOU WANT ONLY EDITORIAL STORIES, THEN SET IT TO FALSE!</strong></p>
        <select name="submitRequireURL" class="form-control {if $submitRequireURL eq true}configured{else}notconfigured{/if}">					
            <option selected>{$submitRequireURL}</option>
		</select><br />
        <label>Show the URL Input Box</label>
         <p>Under Submit Settings, Show the URL Input Box is by default set to true. <strong>SET TO FALSE IF YOU ONLY WANT EDITORIAL STORIES! (REQUIRE THAT THE ABOVE SETTING "Require a URL when Submitting" BE SET TO FALSE)</strong></p>
        <select name="submitShowURL" class="form-control {if $submitShowURL eq true}configured{else}notconfigured{/if}">					
            <option selected>{$submitShowURL}</option>
		</select><br />
        <label>No URL text</label>
         <p>Under Submit Settings, if "Require a URL when Submitting" is set to false, the Label to show in place of the URL in the Story toolsbar is "Editorial". Admins can change it to fit their needs!</strong></p>
        <select name="submitNoURLName" class="form-control configured">					
            <option selected>{$submitNoURLName}</option>
		</select>
    </fieldset>
    
 {*Did you know?*}
    <legend class="centerText">DID YOU KNOW?</legend>
    <fieldset>
        <p>The following are tips to help you configure the site according to your needs. It is organized according to the settings that appear under the Dashboard Settings section.</p>
    </fieldset> 
	
    {*Anonymous*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Anonymous" target="_blank">Anonymous</a>:</legend>
        <label>Anonymous vote</label>
        <p>By default it is set to false. If you wish to allow anonymous (not logged in) to vote, set it to true.</p>
    </fieldset>
    
    {*Avatars*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Avatars" target="_blank">Avatars</a>:</legend>
        <label>Allow User to Upload Avatars</label>
        <p>By default it is set to true. If you wish to disallow user to upload their avatars, set it to false.</p>
        <p>Under the Avatars section, you can also set the "Maximum image size allowed to upload"</p>
    </fieldset>
    
    {*Comments*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Comments" target="_blank">Comments</a>:</legend>
        <label>Allow Comments</label>
        <p>By default it is set to true. If you wish to disallow comments (temporarily or indefinitely, set it to false.</p>
        <p>Edit the "Message to display when Comments are disallowed" setting to fit your needs.</p>
        
        <label>Comment Voting</label>
        <p>By default it is set to true. If you wish to disallow voting on comments, set it to false.</p>
        
        <label>Negative votes to remove comment</label>
        <p>By default it is set to Zero (disabled). If you wish to enable it, set it to the number of negative votes to discard the comment.</p>
        <p>In the Comments settings, you can also check "Maximum Comment Length" and "Search Comments"</p>
    </fieldset>
    
    {*Groups*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Groups" target="_blank">Groups</a>:</legend>
        <label>Groups</label>
        <p>By default it is set to true. If you wish to disallow groups (temporarily or indefinitely, set it to false.</p>
        
        <p>Under the Groups section, you can also keep the default or change the following settings:</p>
        
        <ul>
            <li>Maximum number of groups a user is allowed to create: default is 10</li>
            <li>Maxiumum number of groups a user is allowed to join: default is 10</li>
            <li>Auto Approve New Groups: default is true. If you wish to manually approve the created group, set it to false.</li>
            <li>Allow Groups to upload own avatar: default is true. Set it to false to disallow it.</li>
            <li>Maximum image size allowed to upload: default is 200KB. Set it to your needs.</li>
            <li>Minimum user level to create new groups: default is "normal".</li>
        </ul>
    </fieldset>
    
    {*Location Installed*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Location%20Installed" target="_blank">Location Installed</a>:</legend>
        <label>Allow registration?</label>
        <p>Default is true. If you wish to disallow it (temporarily or indefinitely, set it to false.</p>
        
        <label>Message to display when Registration is suspended</label>
        <p>keep or modify the message you want to display</p>
        
        <label>Maintenance Mode</label>
        <p>Default is false.</p>
        <p>Set the mode to true when you want to notify the users of the unavailability of the site (upgrade, downtime, etc.) <strong>NOTE that only Admin can still access the site during maintenance mode!</strong></p>
    </fieldset>

    {*OutGoing*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=OutGoing" target="_blank">OutGoing</a>:</legend>
        <label>Enable Outgoing Links</label>
        <p>Default is false. If you wish to track the clicks on the original link to the Story source (from the Story page, not the summary page), then set it to true.</p>
    </fieldset>

    {*Story*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Story" target="_blank">Story</a>:</legend>
        <label>Use Story Title as External Link</label>
        <p>Default is false. <strong>When set to true, the Story title will link directly to the original story link even when the story is displayed in summary mode (Home, New, profile tabs and any other page that displays a story)!<br />Users can still click on the "Discuss" link to comment and use the other links in the toolsbar to share and save the story.</strong></p>
        
        <label>Use rel="nofollow"</label>
        <p>Default is true. "nofollow" is a value that can be assigned to the "rel" attribute of an HTML a element to instruct some search engines that the hyper link should not influence the ranking of the link's target in the search engine's index.</p>
    </fieldset>

    {*Submit*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Submit" target="_blank">Submit</a>:</legend>
        <label>Allow users to submit articles?</label>
        <p>Default is true. If you wish to disallow submitting, (temporarily or indefinitely, set it to false.</p>
        <p>Edit the "Message to display when Submitting articles is disallowed" setting to fit your needs.</p>
        
        <p>Under Submit settings section you can find many other settings of interest, that you can keep as the default or edit to fit your needs:</p>
        <ul>
            <li>Complete submission on Submit Step 2?: default is true. If set to false, the user will be presented with a third step where they can preview before submitting the story.<br /><strong>BEFORE SETTING IT TO FALSE, CHECK THE ALLOW DRAFT AND SCHEDULED IN THE RECOMMENDED SECTION ABOVE!</strong></li>
            
            <li>Auto vote: default is true. Story automatically acquires ONE vote upon submission.</li>
            <li>Minimum number of characters for the story title: default is 10 (users will be prompted that the title is short)</li>
            <li>Minimum number of characters for the story description: default is 10 (users will be prompted that the description is short)</li>
            <li>Maximum number of characters for the story title: default is 120 (users will be prompted that the title is too long)</li>
            <li>Maximum number of characters for the story tags: default is 40. (users will be prompted that the tags are too long)</li>
            <li>Maximum number of characters for the story description: default is 3000 (users will be prompted that the description is too long)</li>
            <li>Maximum number of characters for the story summary: default is 600, after which the content is truncated and a "Read more" button appears in the summary view.</li>
            <li>Limit time to edit stories: default is 0 (Unlimited amount of time to edit)<br />If you wish to limit the time to edit, set it to 1 (on) and set the setting "Minutes before a user is no longer allowed to edit a story" below.</li>
            <li>Minutes before a user is no longer allowed to edit a story: default is 0.<br />Requires that you enable Limit Time To Edit Stories (set to 1).<br /> <strong>Number in minutes</strong>. 0 = Disable the users ability to ever edit the story. This setting only applies to the story submitter; Admins & Moderators can still edit the story at any time!</li>
            <li>HTML tags to allow to Normal users: default is blank</li>
            <li>HTML tags to allow for Moderators: default is blank</li>
            <li>HTML tags to allow for Admins: default is blank</li>
            <strong>If you wish to allow HTML in Story and comment content copy, all or select the <u>SAFE</u> tags provided in the description of these settings.</strong>
        </ul>
    </fieldset>

    {*Voting*}
	<fieldset>
        <legend><a href="{$kahuk_base_url}/admin/admin_config.php?page=Voting" target="_blank">Voting</a>:</legend>
        <label>Votes to publish (Number of votes before story is sent to the front page.)</label>
        <p>Default is 5. <strong>NOTE THAT if you want to immediately publish the Story, keep the default setting for Auto vote: default is true and set this one to 1. Otherwise, set the number of days that fit your needs, <u>However, I suggest never setting it to 0 because it will be discarded from the first negative vote (if the "Negative Votes Story Discard" below is set to 1.</u></strong></p>
        
        <p><strong><u></u></strong></p>
        
        <label>Days to publish (After this many days posts will not be eligible to move from new to published pages)</label>
        <p>the default is 10.<br /><strong>CAUTION: THIS SETTING AFFECTS THE STORY. IF A STORY HAS BEEN PUBLISHED FOR MORE THAN THE SPECIFIED NUMBER OF DAYS, IT WILL NOT BE PUBLISHED EVEN IF IT ACQUIRES ALL THE VOTES IN  WORLD!<br />iF YOU WISH TO USE THIS SETTING AND YOU WANT TO ALLOW ENOUGH DAYS FOR THE STORY TO BE ELIGIBLE TO BE PUBLISHED, THEN KEEP THE DEFAULT OR MODIFY IT TO YOUR NEEDS. THE HIGHER THE NUMBER OF DAYS, THE MORE CHANCE FOR THE STORY NOT BEING LEFT IN THE "NEW" SECTION!</strong></p>
        
        <p>Other Voting settings of interest:</p>
        <ul>
            <li>Voting Method: default is 1 (Up and Down Voting). Other methods are Star Ratings and Karma.</li>
            <li>Votes Allowed from one IP: default is 1 (on) to prevent users from voting from multiple registered accounts from the same computer network. Set it to 0 (off) to allow multiple Voting from the same IP.</li>
            <li>Negative Votes Story Discard: default is 0 (off).<br />If you want to set it on, enter the value 1.<br /><u><strong>How it works?</strong></u><br />If set to 1, stories with enough down votes will be discarded. The formula for determining what gets buried is stored in the database table_formulas. It defaults to discarding stories with 3 times more downvotes than upvotes. The formula in the table formulas is $reports > $votes * 3.<br />$votes is equal to the count of votes with a value of 10 (upvote). $reports is equal to the count of votes with a value of -10 (downvote). Example: a story acquired 1 vote, so $votes = 1. to be discarded, The $reports value needs to be GREATER than $votes * 3; this means that the story needs to be downvoted 4 times then, when the formula is evaluated: 4 > 1*3 and the story will be discarded.</li>
        </ul>
    </fieldset>



    
<!--/settings_help.tpl -->