INSERT INTO `table_config` (`var_id`, `var_page`, `var_name`, `var_value`, `var_defaultvalue`, `var_optiontext`, `var_title`, `var_desc`, `var_method`, `var_enclosein`) VALUES 
(NULL, 'Location Installed', '$my_base_url', 'http://localhost', 'http://localhost', 'http://(your site name)(no trailing /)', 'Base URL', '<strong>Examples:</strong>\r\n<br />\r\nhttp://demo.kahuk.com<br />\r\nhttp://localhost<br />\r\nhttps://kahuk.com', 'normal', ''''),
(NULL, 'Location Installed', '$my_kahuk_base', '', '', '/(folder name)', 'Kahuk Base Folder', '<strong>Examples</strong>\r\n<br />\r\n/kahuk -- if installed in the /kahuk subfolder<br />\r\nLeave blank if installed in the site root folder.', 'normal', ''''),
(NULL, 'Location Installed', 'allow_registration', 'true', 'true', 'true / false', 'Allow registration?', 'If for a reason you want to suspend registration, permanently or definitely, set it to false!', 'define', ''),
(NULL, 'Location Installed', 'disallow_registration_message', 'Registration is temporarily suspended!', '', 'Text', 'Message to display when Registration is suspended', 'Enter the message you want to display.', 'define', ''),
(NULL, 'Location Installed', '$maintenance_mode', 'false', 'false', 'true / false', 'Maintenance Mode', 'Set the mode to true when you want to notify the users of the unavailability of the site (upgrade, downtime, etc.)<br /><strong>NOTE that only Admin can still access the site during maintenance mode!</strong>', 'normal', ''''),
(NULL, 'Location Installed', 'allow_smtp_testing', 'false', 'false', 'true / false', 'Allow SMTP Testing?', 'If you want to test email sending on LOCALHOST, using SMTP, set it to true!', 'define', ''),
(NULL, 'Location Installed', 'smtp_host', '', '', 'Text', 'SMTP Host', 'Enter the mail host of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
(NULL, 'Location Installed', 'smtp_port', '587', '', 'Text', 'Port number to use.', 'Use this port number for the SMTP testing. Check the settings in cPanne -> email account.', 'define', ''),
(NULL, 'Location Installed', 'smtp_pass', '', '', 'Text', 'Enter the domain email account password.', 'Use the password of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
(NULL, 'Location Installed', 'smtp_fake_email', 'false', 'false', 'true / false', 'Want to use a FAKE email?', 'If you want to test the email sending with a fake email address, without actually sending it to a true email, and have the message display on the page, set it to true.<br />The recipient\'s email will be the fake email entered upon creating the account.<br /><strong style=\"color:#ff0000;\">DO NOT SET IT TO TRUE WHEN ON THE PRODUCTION SITE!</strong>', 'define', ''),
(NULL, 'Logo', 'Default_Site_Logo', '', '', 'Path to the Site Logo', 'Site Logo', 'Default location of the Site Logo. Just make sure the maximum height of the logo is 40 or 41 px.<BR /> You can have any image extension: PNG, JPG, GIF.<br />Example:<br />/logo.png<br />/templates/bootstrap/img/logo.png', 'define', ''''),
(NULL, 'Logo', 'Default_Site_OG_Image', '', '', 'Path to the Site OpenGraph og:image', 'OpenGraph og:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site OpenGraph og:image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />Use images that are at least 1080 pixels in width for best display on high resolution devices (ratio 1.91:1) Most recommended is 1200 X 630. For square image, at the minimum you should use images that are 600 pixels in width and height (ratio 1:1).<br />Check the link <a href="https://developers.facebook.com/docs/sharing/best-practices/#images" target="_blank">Sharing Best Practices</a>', 'define', ''''),
(NULL, 'Logo', 'Default_OG_Image_Width', '1200', '1200', 'Most recommended', 'OpenGraph og:image width', '<strong>IMPORTANT: ENTER THE Default WIDTH of the OpenGraph Image</strong>.<br />Example: 1200<br />', 'define', ''''),
(NULL, 'Logo', 'Default_OG_Image_Height', '630', '630', 'Most recommended', 'OpenGraph og:image height', '<strong>IMPORTANT: ENTER THE Default HEIGHT of the OpenGraph Image</strong>.<br />Example: 600<br />', 'define', ''''),
(NULL, 'Logo', 'Default_Site_Twitter_Image', '', '', 'Path to the Site Twitter Image', 'Twitter:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site Twitter Card Image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />You should not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported<br />Check the link <a href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image" target="_blank">Summary Card with Large Image</a>', 'define', ''''),
(NULL, 'SEO', '$URLMethod', '__url_method__', '1', '1 or 2', 'URL Method', '<strong>1</strong> = Non-SEO Links.<br /> Example: /story.php?title=Example-Title<br /><strong>2</strong> SEO Method. <br />Example: /Category-Title/Story-title/.<br /><strong>Note:</strong> You must rename htaccess.default to .htaccess <strong>AND EDIT IT WHERE MODIFICATIONS ARE NOTED!</strong>', 'normal', NULL),
(NULL, 'SEO', 'enable_friendly_urls', 'true', 'true', 'true / false', 'Friendly URL''s for stories', 'Use the story title in the url by setting this value to true. Example: /story/(story title)/<br />Keep this setting as TRUE if using URL Method 1', 'define', NULL),
(NULL, 'AntiSpam', 'CHECK_SPAM', 'false', 'false', 'true / false', 'Enable Spam Checking', 'Checks submitted domains to see if they''re on a blacklist.', 'define', NULL),
(NULL, 'AntiSpam', '$MAIN_SPAM_RULESET', 'logs/antispam.log', 'logs/antispam.log', 'Text File', 'Main Spam Ruleset', 'What file should be used to check for spam domains?', 'normal', '"'),
(NULL, 'AntiSpam', '$USER_SPAM_RULESET', 'logs/domain-blacklist.log', 'logs/domain-blacklist.log', 'Text File', 'Local Spam Ruleset', 'What file should Kahuk write to if you mark items as spam?', 'normal', '"'),
(NULL, 'AntiSpam', '$FRIENDLY_DOMAINS', 'logs/domain-whitelist.log', 'logs/domain-whitelist.log', 'Text file', 'Local Domain Whitelist File', 'File containing a list of domains that cannot be banned.', 'normal', '"'),
(NULL, 'AntiSpam', '$SPAM_LOG_BOOK', 'logs/spam.log', 'logs/spam.log', 'Text File', 'Spam Log', 'File to log spam blocks to.', 'normal', '"'),
(NULL, 'Submit', 'Enable_Submit', 'true', 'true', 'true / false', 'Allow Submit', 'Allow users to submit articles?', 'define', NULL),
(NULL, 'Submit', 'disable_Submit_message', 'Submitting articles is temporarily disabled!', '', 'Text', 'Message to display when Submitting articles is disallowed', 'Enter the message you want to display.', 'define', NULL),
(NULL, 'Submit', 'Allow_Draft', 'false', 'false', 'true / false', 'Allow Draft Articles?', 'Set it to true to allow users to save draft articles.<br /><strong>For this feature to work, you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>', 'define', ''),
(NULL, 'Submit', 'Allow_Scheduled', 'false', 'false', 'true / false', 'Allow Scheduled Articles?', 'Set it to true to allow users to save scheduled articles.<br /><strong>If you set to true, then you MUST install the <u>scheduled_posts</u> Module. AND you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>', 'define', ''),
(NULL, 'Submit', 'Validate_URL', 'true', 'true', 'true / false', 'Validate URL', 'Check to see if the page exists, gets the title from it, and checks if it is a blog that uses trackbacks. This should only be set to false for sites who have hosts that don''t allow fsockopen or for sites that want to link to media (mp3s, videos, etc.)', 'define', NULL),
(NULL, 'Submit', 'No_URL_Name', 'Editorial', 'Editorial', 'Text', 'No URL text', 'Label to show when there is no URL provided in submit step 1.', 'define', ''''),
(NULL, 'Submit', 'Submit_Complete_Step2', 'true', 'true', 'true / false', 'Complete submission on Submit Step 2?', 'If set to false, the user will be presented with a third step where they can preview and submit the story. <br /><strong>HOWEVER, IF YOU HAVE ALLOW DRAFT AND ALLOW SCHEDULED FEATURES SET TO TRUE, THEY WON\'T WORK!</strong>', 'define', NULL),
(NULL, 'Submit', 'auto_vote', 'true', 'true', 'true / false', 'Auto vote', 'Automatically vote for the story you submitted.', 'define', NULL),
(NULL, 'Submit', 'minTitleLength', '10', '10', 'number', 'Minimum Title Length', 'Minimum number of characters for the story title.', 'define', NULL),
(NULL, 'Submit', 'minStoryLength', '10', '10', 'number', 'Minimum Story Length', 'Minimum number of characters for the story description.', 'define', NULL),
(NULL, 'Submit', 'maxTitleLength', '120', '120', 'number', 'Maximum Title Length', 'Maximum number of characters for the story title.', 'define', NULL),
(NULL, 'Submit', 'maxStoryLength', '3000', '3000', 'number', 'Maximum Story Length', 'Maximum number of characters for the story description.', 'define', NULL),
(NULL, 'Submit', 'maxSummaryLength', '600', '600', 'number', 'Maximum Summary Length', 'Maximum number of characters for the story summary.', 'define', NULL),
(NULL, 'Submit', 'Multiple_Categories', 'false', 'false', 'true / false', 'Allow multiple categories', 'Setting this to true will allow users to select multiple categories on the submit page.', 'define', NULL),
(NULL, 'Submit', 'limit_time_to_edit', '0', '0', '1 = on / 0 = off', 'Limit time to edit stories', 'This feature allows you to limit the amount of time a user has before they can no longer edit a submitted story.<br /><strong>0</strong> = Unlimited amount of time to edit<br /><strong>1</strong> = specified amount of time', 'define', NULL),
(NULL, 'Submit', 'edit_time_limit', '0', '0', 'number', 'Minutes before a user is no longer allowed to edit a story', 'Requires that you enable <strong>Limit Time To Edit Stories (set to 1)</strong><br />Number in minutes. <strong>0</strong> = Disable the users ability to ever edit the story.<br />This setting only applies to the story submitter; Admins & Moderators can still edit the story at any time!', 'define', NULL),
(NULL, 'Submit', 'Story_Content_Tags_To_Allow_Normal', '', '', 'HTML tags', 'HTML tags to allow to Normal users', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '"'),
(NULL, 'Submit', 'Story_Content_Tags_To_Allow_Admin', '', '', 'HTML tags', 'HTML tags to allow for Moderators', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '"'),
(NULL, 'Submit', 'Story_Content_Tags_To_Allow_God', '', '', 'HTML tags', 'HTML tags to allow for Admins', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '"'),
(NULL, 'Story', 'use_title_as_link', 'false', 'false', 'true / false', 'Use Story Title as External Link', 'Use the story title as link to story''s website. <strong>NOTE that if you set it to true, the title will link directly to the original story link even when the story is displayed in summary mode!</strong>', 'define', NULL),
(NULL, 'Story', 'open_in_new_window', 'false', 'false', 'true / false', 'Open Story Link in New Window', 'If "Use story title as link" is set to true, setting this to true will open the link in a new window.', 'define', NULL),
(NULL, 'Story', 'link_nofollow', 'true', 'true', 'true / false', 'Use rel="nofollow"', 'nofollow is a value that can be assigned to the rel attribute of an HTML a element to instruct some search engines that the hyperlink should not influence the ranking of the link''s target in the search engine''s index.<br /><a href="https://support.google.com/webmasters/answer/96569?hl=en" target="_blank" rel="noopener noreferrer">Google: policies</a>', 'define', NULL),
(NULL, 'Comments', 'Enable_Comments', 'true', 'true', 'true / false', 'Allow Comments', 'Allow users to comment on articles?', 'define', NULL),
(NULL, 'Comments', 'disable_Comments_message', 'Comments are temporarily disabled!', '', 'Text', 'Message to display when Comments are disallowed', 'Enter the message you want to display.', 'define', NULL),
(NULL, 'Comments', 'Enable_Comment_Voting', 'true', 'true', 'true / false', 'Comment Voting', 'Allow users to vote on comments?', 'define', NULL),
(NULL, 'Comments', '$CommentOrder', '4', '4', '1 - 4', 'Comment Sort Order', '<strong>1</strong> = Top rated comments first \r\n<br /><strong>2</strong> = Newest comments first (reverse chronological) \r\n<br /><strong>3</strong> =  Lowest rated comments first \r\n<br /><strong>4</strong> = Oldest comments first (chronological)', 'normal', NULL),
(NULL, 'Comments', 'comments_length_sidebar', '75', '75', 'number', 'Sidebar Comment Length', 'The maximum number of characters shown for a comment in the sidebar', 'define', NULL),
(NULL, 'Comments', 'comments_size_sidebar', '5', '5', 'number', 'Sidebar Number of Comments', 'How many comments to show in the Latest Comments sidebar module.', 'define', NULL),
(NULL, 'Comments', 'maxCommentLength', '1200', '1200', 'number', 'Maximum Comment Length', 'Maximum number of characters for the comment.', 'define', NULL),
(NULL, 'Comments', 'comment_buries_spam', '0', '0', 'number', 'Negative votes to remove comment', 'Number of negative votes before comment is sent to discard state. <strong>0</strong> = disable feature.', 'define', NULL),
(NULL, 'Comments', 'Search_Comments', 'false', 'false', 'true / false', 'Search Comments', 'Use comment data when providing search results', 'define', NULL),
(NULL, 'Voting', 'votes_to_publish', '5', '5', 'number', 'Votes to publish', 'Number of votes before story is sent to the front page.', 'define', NULL),
(NULL, 'Voting', 'days_to_publish', '10', '10', 'number', 'Days to publish', 'After this many days posts will not be eligible to move from new to published pages', 'define', NULL),
(NULL, 'Voting', 'Voting_Method', '1', '1', '1-3', 'Voting Method', '<strong>1</strong> = Up and Down Voting<br /> <strong>2</strong> = 5 Star Ratings<br /><strong>3</strong> = Karma', 'define', NULL),
(NULL, 'Voting', 'rating_to_publish', '3', '3', 'number', 'Rating To Publish', 'How many star rating votes will publish a story? For use with Voting Method 2.', 'define', NULL),
(NULL, 'Voting', 'votes_per_ip', '1', '1', 'number', 'Votes Allowed from one IP', 'This feature is turned on by default to prevent users from voting from multiple registered accounts from the same computer network. <strong>0</strong> = disable feature.', 'define', NULL),
(NULL, 'Voting', 'buries_to_spam', '0', '0', '1 = on / 0 = off', 'Negative Votes Story Discard', 'If set to 1, stories with enough down votes will be discarded. The formula for determining what gets buried is stored in the database table_formulas. It defaults to discarding stories with 3 times more downvotes than upvotes.', 'define', NULL),
(NULL, 'Voting', 'karma_to_publish', '100', '100', 'number', 'Karma to publish', 'Minimum karma value before story is sent to the front page.', 'define', NULL),
(NULL, 'Anonymous', 'anonymous_vote', 'false', 'false', 'true / false', 'Anonymous vote', 'Allow anonymous users to vote on articles.', 'define', '"'),
(NULL, 'Anonymous', '$anon_karma', '1', '1', 'number', 'Anonymous Karma', 'Karma is an experimental feature that measures user activity and reputation.', 'normal', NULL),
(NULL, 'OutGoing', 'track_outgoing', 'false', 'false', 'true / false', 'Enable Outgoing Links', 'Out.php is used to track each click to the external story url. Do you want to enable this click tracking?', 'define', ''),
(NULL, 'OutGoing', 'track_outgoing_method', 'title', 'title', 'url, title or id', 'Outgoing Links Placement', 'What identifier should the out.php URL use?', 'define', ''''),
(NULL, 'Groups', 'enable_group', 'true', 'true', 'true/false', 'Groups', 'Activate the Group Feature?', 'define', 'NULL'),
(NULL, 'Groups', 'max_user_groups_allowed', '10', '10', 'number', 'Max Groups User Create', 'Maximum number of groups a user is allowed to create', 'define', 'NULL'),
(NULL, 'Groups', 'max_groups_to_join', '10', '10', 'number', 'Max Joinable Groups', 'Maxiumum number of groups a user is allowed to join', 'define', 'NULL'),
(NULL, 'Groups', 'auto_approve_group', 'true', 'true', 'true/false', 'Auto Approve New Groups', 'Should new groups be auto-approved? Set to false if you want to moderate all new groups being created.', 'define', 'NULL'),
(NULL, 'Groups', 'allow_groups_avatar', 'true', 'true', 'true/false', 'Allow Groups to upload own avatar', 'Should groups be allowed to upload own avatar?', 'define', 'NULL'),
(NULL, 'Groups', 'max_group_avatar_size', '200', '200KB', 'number', 'Maximum image size allowed to upload', 'Set the maximum image size for the group avatar to upload.', 'define', 'NULL'),
(NULL, 'Groups', 'group_avatar_size_width', '100', '100', 'number', 'Width of Group Avatar', 'Width in pixels for the group avatar', 'define', 'NULL'),
(NULL, 'Groups', 'group_avatar_size_height', '100', '100', 'number', 'Height of Group Avatar', 'Height in pixels for the group avatar', 'define', 'NULL'),
(NULL, 'Groups', 'group_submit_level', 'normal', 'normal', 'normal,moderator,admin', 'Group Create User Level', 'Minimum user level to create new groups', 'define', 'NULL'),
(NULL, 'Avatars', 'Default_Gravatar_Large', '/avatars/Avatar_100.png', '/avatars/Avatar_100.png', 'Path to image', 'Default  avatar (large)', 'Default location of large gravatar.', 'define', ''''),
(NULL, 'Avatars', 'max_avatar_size', '200', '200KB', 'number', 'Maximum image size allowed to upload', 'Set the maximum image size a user can upload.', 'define', ''''),
(NULL, 'Avatars', 'Default_Gravatar_Small', '/avatars/Avatar_32.png', '/avatars/Avatar_32.png', 'Path to image', 'Default avatar (small)', 'Default location of small gravatar.', 'define', ''''),
(NULL, 'Avatars', 'Avatar_Large', '100', '100', 'number', 'Large Avatar Size', 'Size of the large avatars in pixels (both width and height). Commonly used on the profile page.', 'define', NULL),
(NULL, 'Avatars', 'Avatar_Small', '32', '32', 'number', 'Small Avatar Size', 'Size of the small avatar in pixels (both width and height). Commonly used in the comments page.', 'define', NULL),
(NULL, 'Live', 'Enable_Live', 'true', 'true', 'true / false', 'Enable Live', 'Enable the live page.', 'define', NULL),
(NULL, 'Live', 'items_to_show', '20', '20', 'number', 'Live Items to Show', 'Number of items to show on the live page.', 'define', NULL),
(NULL, 'Live', 'how_often_refresh', '20', '20', 'number', 'How often to refresh', 'How many seconds between refreshes - not recommended to set it less than 5.', 'define', NULL),
(NULL, 'Template', 'Allow_User_Change_Templates', 'false', 'false', 'true / false', 'Allow User to Change Templates', 'Allow user to change the template. They can do this from the user settings page.', 'define', ''),
(NULL, 'Template', '$thetemp', 'bootstrap', 'bootstrap', 'Text', 'Template', 'Default Template', 'normal', ''''),
(NULL, 'Template', 'Use_New_Story_Layout', 'false', 'false', 'true / false', 'Use the new Story layout?', 'If you want to use the new Story layout, set this to true.', 'define', ''),
(NULL, 'Misc', '$trackbackURL', 'kahuk.com', 'kahuk.com', 'kahuk.com', 'Trackback URL', 'The url to be used in <a href="http://en.wikipedia.org/wiki/Trackback">trackbacks</a>.', 'normal', '"'),
(NULL, 'Misc', 'Enable_Extra_Fields', 'false', 'false', 'true / false', 'Enable Extra Fields', 'Enable extra fields when submitting stories?<br /><strong>When SET to TRUE, you have to edit the /libs/extra_fields.php file, using the NEW <a href="../admin/admin_xtra_fields_editor.php" target="_blank" rel="noopener noreferrer">Extra Fields Editor</a> in the Dashboard!</strong>', 'define', NULL),
(NULL, 'Misc', 'Allow_Friends', 'true', 'true', 'true / false', 'Allow friends', 'Allow adding, removing, and viewing friends.', 'define', NULL),
(NULL, 'Misc', 'SearchMethod', '3', '3', '1 - 3', 'Search Method', '<strong>1</strong> = uses MySQL MATCH for FULLTEXT indexes (or something). Problems are MySQL STOP words and words less than 4 characters. Note: these limitations do not affect clicking on a TAG to search by it.\r\n<br /><strong>2</strong> = uses MySQL LIKE and is much slower, but returns better results. Also supports "*" and "-"\r\n<br /><strong>3</strong> = is a hybrid, using method 1 if possible, but method 2 if needed.', 'define', NULL),
(NULL, 'Misc', '$dblang', 'en', 'en', 'Text', 'Database Language', 'Database language.<br /><strong style=\"color:#ff0000;\">DO NOT CHANGE THIS VALUE "en" IT WILL MESS UP THE URLS OF THE CATEGORIES!</STRONG>', 'normal', ''''),
(NULL, 'Misc', '$page_size', '8', '8', 'number', 'Page Size', 'How many stories to show on a page.', 'normal', NULL),
(NULL, 'Misc', '$top_users_size', '25', '25', 'number', 'Top Users Size', 'How many users to display in top users.', 'normal', NULL),
(NULL, 'Misc', 'table_prefix', 'kahuk_', 'kahuk_', 'Text', 'MySQL Table Prefix', 'Table prefix. Ex: kahuk_ makes the users table become kahuk_users. Note: changing this will not automatically rename your tables!', 'define', ''''),
(NULL, 'Misc', 'misc_validate', 'false', 'false', 'true / false', 'Validate user email', 'Require users to validate their email address?<br />If you set to true, then click on the link below to also set the email to be used for sending the message.<br /><a href="../module.php?module=admin_language">Set the email</a>. Type @ in the filter box and click Filter to get the value to modify. Do not forget to click save.', 'define', ''),
(NULL, 'Misc', 'validate_password', 'true', 'true', 'true / false', 'Validate user password', 'Validate user password, when registering/password reset, to check if it is safe and not pwned?<br />If you set to true, then a check is done using HIBP API. If the provided password has been pwned, the registration is not submitted until they provide a different password!.<br /><a href="https://haveibeenpwned.com/" target="_blank" rel="noopener noreferrer">Have I Been Pwned?</a>', 'define', ''),
(NULL, 'Misc', 'misc_timezone', '0', '0', 'number', 'Timezone offset', 'Should be a number between -12 and 12 for GMT -1200 through GMT +1200 timezone', 'define', ''),
(NULL, 'Misc', 'Independent_Subcategories', 'false', 'false', 'true / false', 'Show subcategories', 'Top level categories remain independent from subcategory content', 'define', NULL),
(NULL, 'Misc', 'Auto_scroll', '1', '1', '1', 'Pagination Mode', '<strong>1.</strong> Use normal pagination links.', 'define', NULL),
(NULL, 'Misc', '\$language', '---language---', 'english', 'text', 'Site Language', 'Site Language', 'normal', '\''),
(NULL, 'Misc', 'user_language', '0', '0', '1 = yes / 0 = no', 'Select Language', 'Allow users to change Kahuk language<br /><strong>When SET to 1, you have to rename the language file that you want to allow in /languages/ folder.</strong> Ex: <span style="font-style:italic;color:#004dff">RENAME lang_italian.conf.default</span> to <span style="font-style:italic;color:#004dff">lang_italian.conf</span>', 'normal', ''''),
(NULL, 'Misc', 'what_is_kahuk', 'true', 'true', 'true / false', 'Display What is Kahuk in the sidebar?', 'Set it to false if you do not want it to display.<br />If you want it to display but with your own content, Keep it set to true and edit the language file where the entry is KAHUK_Visual_What_Is_Kahuk and KAHUK_Visual_What_Is_Kahuk_Text under the Sidebar section.<br /><a href=\"../admin/module.php?module=admin_language\" target=\"_blank\" rel=\"noopener noreferrer\">Modify Language</a>', 'normal', '''');
