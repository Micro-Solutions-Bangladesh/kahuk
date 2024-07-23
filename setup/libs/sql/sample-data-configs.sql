INSERT INTO `---table_config---` (
    `var_page`, `var_name`, `var_value`, `var_defaultvalue`, 
    `var_optiontext`, `var_title`, `var_desc`, `var_method`
) VALUES 
(
    'Primary Settings', '_site_name', 'Kahuk', 'Kahuk', '', 
    'Website name', 'Name  or title of the website', 'option'
),
(
    'Primary Settings', '_registration_disable', 'false', 'false', 
    'true / false', 'Disable registration?', 
    'If for a reason you want to disable registration feature, set it to true!', 
    'option'
),
('Primary Settings', '_maintenance_mode', 'false', 'false', 'true / false', 'Maintenance Mode', 'Set the mode to true when you want to notify the users of the unavailability of the site (upgrade, downtime, etc.)<br /><strong>NOTE that only Admin can still access the site during maintenance mode!</strong>', 'option'),
('Email Settings', '_site_email_contact', 'support@domain.com', 'support@domain.com', '', 'Contact Email', 'Contact email address for the website', 'option'),
('Email Settings', '_site_email_auto', 'auto@domain.com', 'auto@domain.com', '', 'Auto Email', 'Auto email address for the website', 'option'),
('Email Settings', '_site_email_noreply', 'noreply@domain.com', 'noreply@domain.com', '', 'No reply Email', 'No reply email address for the website', 'option'),
('Email Settings', '_smtp_host', '', '', 'Text', 'SMTP Host', 'Enter the mail host of your domain email account. Check the settings in cPanne -> email account.', 'option'),
('Email Settings', '_smtp_port', '587', '', 'Text', 'Port number to use.', 'Use this port number for the SMTP testing. Check the settings in cPanne -> email account.', 'option'),
('Email Settings', '_smtp_pass', '', '', 'Text', 'Enter the domain email account password.', 'Use the password of your domain email account. Check the settings in cPanne -> email account.', 'option'),
('Email Settings', '_email_debug', 'false', 'false', 'true / false', 'Debug email send?', 'Will log all the steps in send emails.', 'option'),
('Logo', '_site_logo', '', '', 'Path to the Site Logo', 'Site Logo', 'Location of the Site Logo. <br>Example:<br>/logo.png<br>/resources/images/logo.png<br>https://example.com/resources/logo.png', 'option'),
('Logo', 'Default_Site_OG_Image', '', '', 'Path to the Site OpenGraph og:image', 'OpenGraph og:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site OpenGraph og:image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />Use images that are at least 1080 pixels in width for best display on high resolution devices (ratio 1.91:1) Most recommended is 1200 X 630. For square image, at the minimum you should use images that are 600 pixels in width and height (ratio 1:1).<br />Check the link <a href="https://developers.facebook.com/docs/sharing/best-practices/#images" target="_blank">Sharing Best Practices</a>', 'define'),
('Logo', 'Default_OG_Image_Width', '1200', '1200', 'Most recommended', 'OpenGraph og:image width', '<strong>IMPORTANT: ENTER THE Default WIDTH of the OpenGraph Image</strong>.<br />Example: 1200<br />', 'define'),
('Logo', 'Default_OG_Image_Height', '630', '630', 'Most recommended', 'OpenGraph og:image height', '<strong>IMPORTANT: ENTER THE Default HEIGHT of the OpenGraph Image</strong>.<br />Example: 600<br />', 'define'),
('Logo', 'Default_Site_Twitter_Image', '', '', 'Path to the Site Twitter Image', 'Twitter:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site Twitter Card Image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />You should not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported<br />Check the link <a href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image" target="_blank">Summary Card with Large Image</a>', 'define'),
('Submit', 'Enable_Submit', 'true', 'true', 'true / false', 'Allow Submit', 'Allow users to submit articles?', 'define'),
('Submit', 'maxTitleLength', '120', '120', 'number', 'Maximum Title Length', 'Maximum number of characters for the story title.', 'define'),
('Submit', 'maxStoryLength', '3000', '3000', 'number', 'Maximum Story Length', 'Maximum number of characters for the story description.', 'define'),
('Submit', 'MAX_NUMBER_OF_WORD_STORY_DESC', '600', '600', 'number', 'Maximum Summary Length', 'Maximum number of characters for the story summary.', 'option'),
('Submit', 'Multiple_Categories', 'false', 'false', 'true / false', 'Allow multiple categories', 'Setting this to true will allow users to select multiple categories on the submit page.', 'define'),
('Submit', 'limit_time_to_edit', '0', '0', '1 = on / 0 = off', 'Limit time to edit stories', 'This feature allows you to limit the amount of time a user has before they can no longer edit a submitted story.<br /><strong>0</strong> = Unlimited amount of time to edit<br /><strong>1</strong> = specified amount of time', 'define'),
('Submit', 'edit_time_limit', '0', '0', 'number', 'Minutes before a user is no longer allowed to edit a story', 'Requires that you enable <strong>Limit Time To Edit Stories (set to 1)</strong><br />Number in minutes. <strong>0</strong> = Disable the users ability to ever edit the story.<br />This setting only applies to the story submitter; Admins & Moderators can still edit the story at any time!', 'define'),
('Submit', '_allowed_html_tags_normal', '', '', 'HTML tags', 'HTML tags to allow to Normal users', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'option'),
('Submit', '_allowed_html_tags_moderator', '', '', 'HTML tags', 'HTML tags to allow for Moderators', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'option'),
('Submit', '_allowed_html_tags_admin', '', '', 'HTML tags', 'HTML tags to allow for Admins', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'option'),
('Story', 'open_in_new_window', 'false', 'false', 'true / false', 'Open Story Link in New Window', 'If "Use story title as link" is set to true, setting this to true will open the link in a new window.', 'define'),
('Story', 'link_nofollow', 'true', 'true', 'true / false', 'Use rel="nofollow"', 'nofollow is a value that can be assigned to the rel attribute of an HTML a element to instruct some search engines that the hyperlink should not influence the ranking of the link''s target in the search engine''s index.<br /><a href="https://support.google.com/webmasters/answer/96569?hl=en" target="_blank" rel="noopener noreferrer">Google: policies</a>', 'define'),
('Comments', '_enable_comments', 'true', 'true', 'true / false', 'Allow Comments', 'Allow users to comment on articles?', 'option'),
('Comments', '_max_no_char_comment', '1200', '1200', 'number', 'Maximum Comment Length', 'Maximum number of characters for the comment.', 'option'),
('Groups', 'enable_group', 'true', 'true', 'true/false', 'Groups', 'Activate the Group Feature?', 'define'),
('Groups', 'max_user_groups_allowed', '10', '10', 'number', 'Max Groups User Create', 'Maximum number of groups a user is allowed to create', 'define'),
('Groups', 'max_groups_to_join', '10', '10', 'number', 'Max Joinable Groups', 'Maxiumum number of groups a user is allowed to join', 'define'),
('Groups', 'auto_approve_group', 'true', 'true', 'true/false', 'Auto Approve New Groups', 'Should new groups be auto-approved? Set to false if you want to moderate all new groups being created.', 'define'),
('Groups', 'allow_groups_avatar', 'true', 'true', 'true/false', 'Allow Groups to upload own avatar', 'Should groups be allowed to upload own avatar?', 'define'),
('Groups', 'max_group_avatar_size', '200', '200KB', 'number', 'Maximum image size allowed to upload', 'Set the maximum image size for the group avatar to upload.', 'define'),
('Groups', '_group_avatar_medium_width', '100', '100', 'number', 'Width of Group Avatar', 'Width in pixels for the group avatar', 'option'),
('Groups', '_group_avatar_medium_height', '100', '100', 'number', 'Height of Group Avatar', 'Height in pixels for the group avatar', 'option'),
('Groups', 'group_submit_level', 'normal', 'normal', 'normal,moderator,admin', 'Group Create User Level', 'Minimum user level to create new groups', 'define'),
('Groups', '_avatar_group_medium', '___base_path___/resources/images/avatar-group-medium.png', '/resources/images/avatar-group-medium.png', '', 'Default avatar (medium) image for group', 'Default medium avatar for group.', 'option'),
('Groups', '_avatar_group_small', '___base_path___/resources/images/avatar-group-small.png', '/resources/images/avatar-group-small.png', '', 'Default avatar (small) image for group', 'Default small avatar for group.', 'option'),
('Avatars', 'Avatar_Large', '100', '100', 'number', 'Large Avatar Size', 'Size of the large avatars in pixels (both width and height). Commonly used on the profile page.', 'define'),
('Avatars', 'Avatar_Small', '32', '32', 'number', 'Small Avatar Size', 'Size of the small avatar in pixels (both width and height). Commonly used in the comments page.', 'define'),
('Template', 'the_template', 'aowal', 'aowal', 'Text', 'Template', 'Default Template', 'option'),
('Misc', '_time_story_totals_updated', '0', '0', 'text', 'Last updated time', 'When is the total of stories updated by status', 'option'),
('Misc', '_story_status_published_total', '0', '0', 'text', 'Total published stories', 'Number of stories published', 'option'),
('Misc', '_story_status_new_total', '0', '0', 'text', 'Total new stories', 'Number of stories new', 'option'),
('Misc', '_story_status_trash_total', '0', '0', 'text', 'Total trash stories', 'Number of stories trash', 'option'),
('Misc', '_story_status_page_total', '0', '0', 'text', 'Total page stories', 'Number of pages', 'option'),
('Misc', '_story_status_draft_total', '0', '0', 'text', 'Total draft', 'Number of drafts', 'option'),
('Misc', 'trackback_url', 'kahuk.com', 'kahuk.com', 'kahuk.com', 'Trackback URL', 'The url to be used in <a href="http://en.wikipedia.org/wiki/Trackback">trackbacks</a>.', 'option'),
('Misc', 'page_size', '8', '8', 'number', 'Page Size', 'How many stories to show on a page.', 'option'),
('Misc', 'validate_password', 'true', 'true', 'true / false', 'Validate user password', 'Validate user password, when registering/password reset, to check if it is safe and not pwned?<br />If you set to true, then a check is done using HIBP API. If the provided password has been pwned, the registration is not submitted until they provide a different password!.<br /><a href="https://haveibeenpwned.com/" target="_blank" rel="noopener noreferrer">Have I Been Pwned?</a>', 'define'),
(
    'Misc', 'what_is_kahuk', 'true', 'true', 'true / false', 
    'Display What is Kahuk in the sidebar?', 
    'Set it to false if you do not want it to display.<br />If you want it to display but with your own content, Keep it set to true and edit the language file where the entry is KAHUK_Visual_What_Is_Kahuk and KAHUK_Visual_What_Is_Kahuk_Text under the Sidebar section.<br /><a href=\"../admin/module.php?module=admin_language\" target=\"_blank\" rel=\"noopener noreferrer\">Modify Language</a>', 
    'normal'
),
(
    'Misc', '_kahuk_cms_version', '605,6.0.5', '', 'text', 
    'Kahuk Version', 
    'Version of Kahuk CMS<br>Please do not edit this (if you are not expert in Kahuk CMS).', 
    'option'
),
(
    'Plugins', 'plugins_installed', 'snippets', '', 'text', 
    'Installed Plugins', 'CSV list of installed plugins.', 'option'
),
(
    'Karma', '_new_story_karma_for_user', '5', '5', 'text', 
    'New story karma for user', 'Karma for user when submit a new story.', 'option'
),
(
    'Karma', '_new_story_karma_initialy', '10', '10', 'text', 
    'New story karma', 'Story karma initially.', 'option'
),
(
    'Karma', '_follow_karma_for_user', '50', '50', 'text', 
    'Follow karma', 'Karma for user when somebody follow him or her.', 'option'
);
