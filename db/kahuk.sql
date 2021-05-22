-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2021 at 11:51 AM
-- Server version: 8.0.25-0ubuntu0.20.04.1
-- PHP Version: 7.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kahuk_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_additional_categories`
--

CREATE TABLE `fdfdf_additional_categories` (
  `ac_link_id` int NOT NULL,
  `ac_cat_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_categories`
--

CREATE TABLE `fdfdf_categories` (
  `category__auto_id` int NOT NULL,
  `category_lang` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `category_id` int NOT NULL DEFAULT '0',
  `category_parent` int NOT NULL DEFAULT '0',
  `category_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `category_safe_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `rgt` int NOT NULL DEFAULT '0',
  `lft` int NOT NULL DEFAULT '0',
  `category_enabled` int NOT NULL DEFAULT '1',
  `category_order` int NOT NULL DEFAULT '0',
  `category_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_author_level` enum('normal','moderator','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `category_author_group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `category_votes` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `category_karma` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_categories`
--

INSERT INTO `fdfdf_categories` (`category__auto_id`, `category_lang`, `category_id`, `category_parent`, `category_name`, `category_safe_name`, `rgt`, `lft`, `category_enabled`, `category_order`, `category_desc`, `category_keywords`, `category_author_level`, `category_author_group`, `category_votes`, `category_karma`) VALUES
(0, 'en', 0, 0, 'all', 'all', 3, 0, 2, 0, '', '', 'normal', '', '', ''),
(1, 'en', 1, 0, 'News', 'News', 2, 1, 1, 0, '', '', 'normal', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_comments`
--

CREATE TABLE `fdfdf_comments` (
  `comment_id` int NOT NULL,
  `comment_randkey` int NOT NULL DEFAULT '0',
  `comment_parent` int DEFAULT '0',
  `comment_link_id` int NOT NULL DEFAULT '0',
  `comment_user_id` int NOT NULL DEFAULT '0',
  `comment_date` datetime NOT NULL,
  `comment_karma` smallint NOT NULL DEFAULT '0',
  `comment_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_votes` int NOT NULL DEFAULT '0',
  `comment_status` enum('discard','moderated','published','spam') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_config`
--

CREATE TABLE `fdfdf_config` (
  `var_id` int NOT NULL,
  `var_page` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_defaultvalue` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_optiontext` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `var_enclosein` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_config`
--

INSERT INTO `fdfdf_config` (`var_id`, `var_page`, `var_name`, `var_value`, `var_defaultvalue`, `var_optiontext`, `var_title`, `var_desc`, `var_method`, `var_enclosein`) VALUES
(1, 'Location Installed', '$my_base_url', 'http://localhost', 'http://localhost', 'http://(your site name)(no trailing /)', 'Base URL', '<strong>Examples:</strong>\r\n<br />\r\nhttp://demo.kahuk.com<br />\r\nhttp://localhost<br />\r\nhttps://kahuk.com', 'normal', '\''),
(2, 'Location Installed', '$my_kahuk_base', '', '', '/(folder name)', 'Kahuk Base Folder', '<strong>Examples</strong>\r\n<br />\r\n/kahuk -- if installed in the /kahuk subfolder<br />\r\nLeave blank if installed in the site root folder.', 'normal', '\''),
(3, 'Location Installed', 'allow_registration', 'true', 'true', 'true / false', 'Allow registration?', 'If for a reason you want to suspend registration, permanently or definitely, set it to false!', 'define', ''),
(4, 'Location Installed', 'disallow_registration_message', 'Registration is temporarily suspended!', '', 'Text', 'Message to display when Registration is suspended', 'Enter the message you want to display.', 'define', ''),
(5, 'Location Installed', '$maintenance_mode', 'false', 'false', 'true / false', 'Maintenance Mode', 'Set the mode to true when you want to notify the users of the unavailability of the site (upgrade, downtime, etc.)<br /><strong>NOTE that only Admin can still access the site during maintenance mode!</strong>', 'normal', '\''),
(6, 'Location Installed', 'allow_smtp_testing', 'false', 'false', 'true / false', 'Allow SMTP Testing?', 'If you want to test email sending on LOCALHOST, using SMTP, set it to true!', 'define', ''),
(7, 'Location Installed', 'smtp_host', '', '', 'Text', 'SMTP Host', 'Enter the mail host of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
(8, 'Location Installed', 'smtp_port', '587', '', 'Text', 'Port number to use.', 'Use this port number for the SMTP testing. Check the settings in cPanne -> email account.', 'define', ''),
(9, 'Location Installed', 'smtp_pass', '', '', 'Text', 'Enter the domain email account password.', 'Use the password of your domain email account. Check the settings in cPanne -> email account.', 'define', ''),
(10, 'Location Installed', 'smtp_fake_email', 'false', 'false', 'true / false', 'Want to use a FAKE email?', 'If you want to test the email sending with a fake email address, without actually sending it to a true email, and have the message display on the page, set it to true.<br />The recipient\'s email will be the fake email entered upon creating the account.<br /><strong style=\"color:#ff0000;\">DO NOT SET IT TO TRUE WHEN ON THE PRODUCTION SITE!</strong>', 'define', ''),
(11, 'Logo', 'Default_Site_Logo', '', '', 'Path to the Site Logo', 'Site Logo', 'Default location of the Site Logo. Just make sure the maximum height of the logo is 40 or 41 px.<BR /> You can have any image extension: PNG, JPG, GIF.<br />Example:<br />/logo.png<br />/templates/bootstrap/img/logo.png', 'define', '\''),
(12, 'Logo', 'Default_Site_OG_Image', '', '', 'Path to the Site OpenGraph og:image', 'OpenGraph og:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site OpenGraph og:image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />Use images that are at least 1080 pixels in width for best display on high resolution devices (ratio 1.91:1) Most recommended is 1200 X 630. For square image, at the minimum you should use images that are 600 pixels in width and height (ratio 1:1).<br />Check the link <a href=\"https://developers.facebook.com/docs/sharing/best-practices/#images\" target=\"_blank\">Sharing Best Practices</a>', 'define', '\''),
(13, 'Logo', 'Default_OG_Image_Width', '1200', '1200', 'Most recommended', 'OpenGraph og:image width', '<strong>IMPORTANT: ENTER THE Default WIDTH of the OpenGraph Image</strong>.<br />Example: 1200<br />', 'define', '\''),
(14, 'Logo', 'Default_OG_Image_Height', '630', '630', 'Most recommended', 'OpenGraph og:image height', '<strong>IMPORTANT: ENTER THE Default HEIGHT of the OpenGraph Image</strong>.<br />Example: 600<br />', 'define', '\''),
(15, 'Logo', 'Default_Site_Twitter_Image', '', '', 'Path to the Site Twitter Image', 'Twitter:image', '<strong><u>IMPORTANT: CREATE THE Default</u> location of the Site Twitter Card Image</strong>.<br />Example:<br />/NAME-OF-IMAGE.(png|JPG)<br />The recommended sizes to use:<br />You should not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported<br />Check the link <a href=\"https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image\" target=\"_blank\">Summary Card with Large Image</a>', 'define', '\''),
(16, 'SEO', '$URLMethod', '2', '1', '1 or 2', 'URL Method', '<strong>1</strong> = Non-SEO Links.<br /> Example: /story.php?title=Example-Title<br /><strong>2</strong> SEO Method. <br />Example: /Category-Title/Story-title/.<br /><strong>Note:</strong> You must rename htaccess.default to .htaccess <strong>AND EDIT IT WHERE MODIFICATIONS ARE NOTED!</strong>', 'normal', NULL),
(17, 'SEO', 'enable_friendly_urls', 'true', 'true', 'true / false', 'Friendly URL\'s for stories', 'Use the story title in the url by setting this value to true. Example: /story/(story title)/<br />Keep this setting as TRUE if using URL Method 1', 'define', NULL),
(18, 'AntiSpam', 'CHECK_SPAM', 'false', 'false', 'true / false', 'Enable Spam Checking', 'Checks submitted domains to see if they\'re on a blacklist.', 'define', NULL),
(19, 'AntiSpam', '$MAIN_SPAM_RULESET', 'logs/antispam.log', 'logs/antispam.log', 'Text File', 'Main Spam Ruleset', 'What file should be used to check for spam domains?', 'normal', '\"'),
(20, 'AntiSpam', '$USER_SPAM_RULESET', 'logs/domain-blacklist.log', 'logs/domain-blacklist.log', 'Text File', 'Local Spam Ruleset', 'What file should Kahuk write to if you mark items as spam?', 'normal', '\"'),
(21, 'AntiSpam', '$FRIENDLY_DOMAINS', 'logs/domain-whitelist.log', 'logs/domain-whitelist.log', 'Text file', 'Local Domain Whitelist File', 'File containing a list of domains that cannot be banned.', 'normal', '\"'),
(22, 'AntiSpam', '$SPAM_LOG_BOOK', 'logs/spam.log', 'logs/spam.log', 'Text File', 'Spam Log', 'File to log spam blocks to.', 'normal', '\"'),
(23, 'Submit', 'Enable_Submit', 'true', 'true', 'true / false', 'Allow Submit', 'Allow users to submit articles?', 'define', NULL),
(24, 'Submit', 'disable_Submit_message', 'Submitting articles is temporarily disabled!', '', 'Text', 'Message to display when Submitting articles is disallowed', 'Enter the message you want to display.', 'define', NULL),
(25, 'Submit', 'Allow_Draft', 'false', 'false', 'true / false', 'Allow Draft Articles?', 'Set it to true to allow users to save draft articles.<br /><strong>For this feature to work, you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>', 'define', ''),
(26, 'Submit', 'Allow_Scheduled', 'false', 'false', 'true / false', 'Allow Scheduled Articles?', 'Set it to true to allow users to save scheduled articles.<br /><strong>If you set to true, then you MUST install the <u>scheduled_posts</u> Module. AND you have to keep the \"Complete submission on Submit Step 2?\" setting to its default TRUE.</strong>', 'define', ''),
(27, 'Submit', 'Validate_URL', 'true', 'true', 'true / false', 'Validate URL', 'Check to see if the page exists, gets the title from it, and checks if it is a blog that uses trackbacks. This should only be set to false for sites who have hosts that don\'t allow fsockopen or for sites that want to link to media (mp3s, videos, etc.)', 'define', NULL),
(28, 'Submit', 'No_URL_Name', 'Editorial', 'Editorial', 'Text', 'No URL text', 'Label to show when there is no URL provided in submit step 1.', 'define', '\''),
(29, 'Submit', 'Submit_Complete_Step2', 'true', 'true', 'true / false', 'Complete submission on Submit Step 2?', 'If set to false, the user will be presented with a third step where they can preview and submit the story. <br /><strong>HOWEVER, IF YOU HAVE ALLOW DRAFT AND ALLOW SCHEDULED FEATURES SET TO TRUE, THEY WON\'T WORK!</strong>', 'define', NULL),
(30, 'Submit', 'auto_vote', 'true', 'true', 'true / false', 'Auto vote', 'Automatically vote for the story you submitted.', 'define', NULL),
(31, 'Submit', 'minTitleLength', '10', '10', 'number', 'Minimum Title Length', 'Minimum number of characters for the story title.', 'define', NULL),
(32, 'Submit', 'minStoryLength', '10', '10', 'number', 'Minimum Story Length', 'Minimum number of characters for the story description.', 'define', NULL),
(33, 'Submit', 'maxTitleLength', '120', '120', 'number', 'Maximum Title Length', 'Maximum number of characters for the story title.', 'define', NULL),
(34, 'Submit', 'maxStoryLength', '3000', '3000', 'number', 'Maximum Story Length', 'Maximum number of characters for the story description.', 'define', NULL),
(35, 'Submit', 'maxSummaryLength', '600', '600', 'number', 'Maximum Summary Length', 'Maximum number of characters for the story summary.', 'define', NULL),
(36, 'Submit', 'Multiple_Categories', 'false', 'false', 'true / false', 'Allow multiple categories', 'Setting this to true will allow users to select multiple categories on the submit page.', 'define', NULL),
(37, 'Submit', 'limit_time_to_edit', '0', '0', '1 = on / 0 = off', 'Limit time to edit stories', 'This feature allows you to limit the amount of time a user has before they can no longer edit a submitted story.<br /><strong>0</strong> = Unlimited amount of time to edit<br /><strong>1</strong> = specified amount of time', 'define', NULL),
(38, 'Submit', 'edit_time_limit', '0', '0', 'number', 'Minutes before a user is no longer allowed to edit a story', 'Requires that you enable <strong>Limit Time To Edit Stories (set to 1)</strong><br />Number in minutes. <strong>0</strong> = Disable the users ability to ever edit the story.<br />This setting only applies to the story submitter; Admins & Moderators can still edit the story at any time!', 'define', NULL),
(39, 'Submit', 'Story_Content_Tags_To_Allow_Normal', '', '', 'HTML tags', 'HTML tags to allow to Normal users', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '\"'),
(40, 'Submit', 'Story_Content_Tags_To_Allow_Admin', '', '', 'HTML tags', 'HTML tags to allow for Moderators', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '\"'),
(41, 'Submit', 'Story_Content_Tags_To_Allow_God', '', '', 'HTML tags', 'HTML tags to allow for Admins', 'leave blank to not allow tags. Examples are: &lt;br&gt;&lt;p&gt;&lt;strong&gt;&lt;em&gt;&lt;u&gt;&lt;s&gt;&lt;sub&gt;&lt;sup&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;blockquote&gt;&lt;span&gt;&lt;div&gt;&lt;big&gt;&lt;small&gt;&lt;tt&gt;&lt;code&gt;&lt;kbd&gt;&lt;samp&gt;&lt;var&gt;&lt;del&gt;&lt;ins&gt;&lt;hr&gt;&lt;pre&gt;<br /><strong style=\"color:#ff0000;\">NEVER ALLOW OTHER THAN THESE TAGS, ESPECIALLY FORM, SCRIPT, IMG, SVG AND IFRAME TAGS!</strong>', 'define', '\"'),
(42, 'Story', 'use_title_as_link', 'false', 'false', 'true / false', 'Use Story Title as External Link', 'Use the story title as link to story\'s website. <strong>NOTE that if you set it to true, the title will link directly to the original story link even when the story is displayed in summary mode!</strong>', 'define', NULL),
(43, 'Story', 'open_in_new_window', 'false', 'false', 'true / false', 'Open Story Link in New Window', 'If \"Use story title as link\" is set to true, setting this to true will open the link in a new window.', 'define', NULL),
(44, 'Story', 'link_nofollow', 'true', 'true', 'true / false', 'Use rel=\"nofollow\"', 'nofollow is a value that can be assigned to the rel attribute of an HTML a element to instruct some search engines that the hyperlink should not influence the ranking of the link\'s target in the search engine\'s index.<br /><a href=\"https://support.google.com/webmasters/answer/96569?hl=en\" target=\"_blank\" rel=\"noopener noreferrer\">Google: policies</a>', 'define', NULL),
(45, 'Comments', 'Enable_Comments', 'true', 'true', 'true / false', 'Allow Comments', 'Allow users to comment on articles?', 'define', NULL),
(46, 'Comments', 'disable_Comments_message', 'Comments are temporarily disabled!', '', 'Text', 'Message to display when Comments are disallowed', 'Enter the message you want to display.', 'define', NULL),
(47, 'Comments', 'Enable_Comment_Voting', 'true', 'true', 'true / false', 'Comment Voting', 'Allow users to vote on comments?', 'define', NULL),
(48, 'Comments', '$CommentOrder', '4', '4', '1 - 4', 'Comment Sort Order', '<strong>1</strong> = Top rated comments first \r\n<br /><strong>2</strong> = Newest comments first (reverse chronological) \r\n<br /><strong>3</strong> =  Lowest rated comments first \r\n<br /><strong>4</strong> = Oldest comments first (chronological)', 'normal', NULL),
(49, 'Comments', 'comments_length_sidebar', '75', '75', 'number', 'Sidebar Comment Length', 'The maximum number of characters shown for a comment in the sidebar', 'define', NULL),
(50, 'Comments', 'comments_size_sidebar', '5', '5', 'number', 'Sidebar Number of Comments', 'How many comments to show in the Latest Comments sidebar module.', 'define', NULL),
(51, 'Comments', 'maxCommentLength', '1200', '1200', 'number', 'Maximum Comment Length', 'Maximum number of characters for the comment.', 'define', NULL),
(52, 'Comments', 'comment_buries_spam', '0', '0', 'number', 'Negative votes to remove comment', 'Number of negative votes before comment is sent to discard state. <strong>0</strong> = disable feature.', 'define', NULL),
(53, 'Comments', 'Search_Comments', 'false', 'false', 'true / false', 'Search Comments', 'Use comment data when providing search results', 'define', NULL),
(54, 'Voting', 'votes_to_publish', '5', '5', 'number', 'Votes to publish', 'Number of votes before story is sent to the front page.', 'define', NULL),
(55, 'Voting', 'days_to_publish', '10', '10', 'number', 'Days to publish', 'After this many days posts will not be eligible to move from new to published pages', 'define', NULL),
(56, 'Voting', 'Voting_Method', '1', '1', '1-3', 'Voting Method', '<strong>1</strong> = Up and Down Voting<br /> <strong>2</strong> = 5 Star Ratings<br /><strong>3</strong> = Karma', 'define', NULL),
(57, 'Voting', 'rating_to_publish', '3', '3', 'number', 'Rating To Publish', 'How many star rating votes will publish a story? For use with Voting Method 2.', 'define', NULL),
(58, 'Voting', 'votes_per_ip', '1', '1', 'number', 'Votes Allowed from one IP', 'This feature is turned on by default to prevent users from voting from multiple registered accounts from the same computer network. <strong>0</strong> = disable feature.', 'define', NULL),
(59, 'Voting', 'buries_to_spam', '0', '0', '1 = on / 0 = off', 'Negative Votes Story Discard', 'If set to 1, stories with enough down votes will be discarded. The formula for determining what gets buried is stored in the database table_formulas. It defaults to discarding stories with 3 times more downvotes than upvotes.', 'define', NULL),
(60, 'Voting', 'karma_to_publish', '100', '100', 'number', 'Karma to publish', 'Minimum karma value before story is sent to the front page.', 'define', NULL),
(61, 'Anonymous', 'anonymous_vote', 'false', 'false', 'true / false', 'Anonymous vote', 'Allow anonymous users to vote on articles.', 'define', '\"'),
(62, 'Anonymous', '$anon_karma', '1', '1', 'number', 'Anonymous Karma', 'Karma is an experimental feature that measures user activity and reputation.', 'normal', NULL),
(63, 'OutGoing', 'track_outgoing', 'false', 'false', 'true / false', 'Enable Outgoing Links', 'Out.php is used to track each click to the external story url. Do you want to enable this click tracking?', 'define', ''),
(64, 'OutGoing', 'track_outgoing_method', 'title', 'title', 'url, title or id', 'Outgoing Links Placement', 'What identifier should the out.php URL use?', 'define', '\''),
(65, 'Groups', 'enable_group', 'true', 'true', 'true/false', 'Groups', 'Activate the Group Feature?', 'define', 'NULL'),
(66, 'Groups', 'max_user_groups_allowed', '10', '10', 'number', 'Max Groups User Create', 'Maximum number of groups a user is allowed to create', 'define', 'NULL'),
(67, 'Groups', 'max_groups_to_join', '10', '10', 'number', 'Max Joinable Groups', 'Maxiumum number of groups a user is allowed to join', 'define', 'NULL'),
(68, 'Groups', 'auto_approve_group', 'true', 'true', 'true/false', 'Auto Approve New Groups', 'Should new groups be auto-approved? Set to false if you want to moderate all new groups being created.', 'define', 'NULL'),
(69, 'Groups', 'allow_groups_avatar', 'true', 'true', 'true/false', 'Allow Groups to upload own avatar', 'Should groups be allowed to upload own avatar?', 'define', 'NULL'),
(70, 'Groups', 'max_group_avatar_size', '200', '200KB', 'number', 'Maximum image size allowed to upload', 'Set the maximum image size for the group avatar to upload.', 'define', 'NULL'),
(71, 'Groups', 'group_avatar_size_width', '100', '100', 'number', 'Width of Group Avatar', 'Width in pixels for the group avatar', 'define', 'NULL'),
(72, 'Groups', 'group_avatar_size_height', '100', '100', 'number', 'Height of Group Avatar', 'Height in pixels for the group avatar', 'define', 'NULL'),
(73, 'Groups', 'group_submit_level', 'normal', 'normal', 'normal,moderator,admin', 'Group Create User Level', 'Minimum user level to create new groups', 'define', 'NULL'),
(74, 'Avatars', 'Enable_User_Upload_Avatar', 'true', 'true', 'true / false', 'Allow User to Upload Avatars', 'Should users be able to upload their own avatars?', 'define', NULL),
(75, 'Avatars', 'Default_Gravatar_Large', '/avatars/Avatar_100.png', '/avatars/Avatar_100.png', 'Path to image', 'Default  avatar (large)', 'Default location of large gravatar.', 'define', '\''),
(76, 'Avatars', 'max_avatar_size', '200', '200KB', 'number', 'Maximum image size allowed to upload', 'Set the maximum image size a user can upload.', 'define', '\''),
(77, 'Avatars', 'Default_Gravatar_Small', '/avatars/Avatar_32.png', '/avatars/Avatar_32.png', 'Path to image', 'Default avatar (small)', 'Default location of small gravatar.', 'define', '\''),
(78, 'Avatars', 'Avatar_Large', '100', '100', 'number', 'Large Avatar Size', 'Size of the large avatars in pixels (both width and height). Commonly used on the profile page.', 'define', NULL),
(79, 'Avatars', 'Avatar_Small', '32', '32', 'number', 'Small Avatar Size', 'Size of the small avatar in pixels (both width and height). Commonly used in the comments page.', 'define', NULL),
(80, 'Live', 'Enable_Live', 'true', 'true', 'true / false', 'Enable Live', 'Enable the live page.', 'define', NULL),
(81, 'Live', 'items_to_show', '20', '20', 'number', 'Live Items to Show', 'Number of items to show on the live page.', 'define', NULL),
(82, 'Live', 'how_often_refresh', '20', '20', 'number', 'How often to refresh', 'How many seconds between refreshes - not recommended to set it less than 5.', 'define', NULL),
(83, 'Template', 'Allow_User_Change_Templates', 'false', 'false', 'true / false', 'Allow User to Change Templates', 'Allow user to change the template. They can do this from the user settings page.', 'define', ''),
(84, 'Template', '$thetemp', 'bootstrap', 'bootstrap', 'Text', 'Template', 'Default Template', 'normal', '\''),
(85, 'Template', 'Use_New_Story_Layout', 'false', 'false', 'true / false', 'Use the new Story layout?', 'If you want to use the new Story layout, set this to true.', 'define', ''),
(86, 'Misc', '$trackbackURL', 'kahuk.com', 'kahuk.com', 'kahuk.com', 'Trackback URL', 'The url to be used in <a href=\"http://en.wikipedia.org/wiki/Trackback\">trackbacks</a>.', 'normal', '\"'),
(87, 'Misc', 'Enable_Extra_Fields', 'false', 'false', 'true / false', 'Enable Extra Fields', 'Enable extra fields when submitting stories?<br /><strong>When SET to TRUE, you have to edit the /libs/extra_fields.php file, using the NEW <a href=\"../admin/admin_xtra_fields_editor.php\" target=\"_blank\" rel=\"noopener noreferrer\">Extra Fields Editor</a> in the Dashboard!</strong>', 'define', NULL),
(88, 'Misc', 'Allow_Friends', 'true', 'true', 'true / false', 'Allow friends', 'Allow adding, removing, and viewing friends.', 'define', NULL),
(89, 'Misc', 'SearchMethod', '3', '3', '1 - 3', 'Search Method', '<strong>1</strong> = uses MySQL MATCH for FULLTEXT indexes (or something). Problems are MySQL STOP words and words less than 4 characters. Note: these limitations do not affect clicking on a TAG to search by it.\r\n<br /><strong>2</strong> = uses MySQL LIKE and is much slower, but returns better results. Also supports \"*\" and \"-\"\r\n<br /><strong>3</strong> = is a hybrid, using method 1 if possible, but method 2 if needed.', 'define', NULL),
(90, 'Misc', '$dblang', 'en', 'en', 'Text', 'Database Language', 'Database language.<br /><strong style=\"color:#ff0000;\">DO NOT CHANGE THIS VALUE \"en\" IT WILL MESS UP THE URLS OF THE CATEGORIES!</STRONG>', 'normal', '\''),
(91, 'Misc', '$page_size', '8', '8', 'number', 'Page Size', 'How many stories to show on a page.', 'normal', NULL),
(92, 'Misc', '$top_users_size', '25', '25', 'number', 'Top Users Size', 'How many users to display in top users.', 'normal', NULL),
(93, 'Misc', 'table_prefix', 'fdfdf_', 'kahuk_', 'Text', 'MySQL Table Prefix', 'Table prefix. Ex: kahuk_ makes the users table become kahuk_users. Note: changing this will not automatically rename your tables!', 'define', '\''),
(94, 'Misc', 'misc_validate', 'false', 'false', 'true / false', 'Validate user email', 'Require users to validate their email address?<br />If you set to true, then click on the link below to also set the email to be used for sending the message.<br /><a href=\"../module.php?module=admin_language\">Set the email</a>. Type @ in the filter box and click Filter to get the value to modify. Do not forget to click save.', 'define', ''),
(95, 'Misc', 'validate_password', 'true', 'true', 'true / false', 'Validate user password', 'Validate user password, when registering/password reset, to check if it is safe and not pwned?<br />If you set to true, then a check is done using HIBP API. If the provided password has been pwned, the registration is not submitted until they provide a different password!.<br /><a href=\"https://haveibeenpwned.com/\" target=\"_blank\" rel=\"noopener noreferrer\">Have I Been Pwned?</a>', 'define', ''),
(96, 'Misc', 'misc_timezone', '0', '0', 'number', 'Timezone offset', 'Should be a number between -12 and 12 for GMT -1200 through GMT +1200 timezone', 'define', ''),
(97, 'Misc', 'Independent_Subcategories', 'false', 'false', 'true / false', 'Show subcategories', 'Top level categories remain independent from subcategory content', 'define', NULL),
(98, 'Misc', 'Auto_scroll', '1', '1', '1', 'Pagination Mode', '<strong>1.</strong> Use normal pagination links.', 'define', NULL),
(99, 'Misc', '$language', 'english', 'english', 'text', 'Site Language', 'Site Language', 'normal', '\''),
(100, 'Misc', 'user_language', '0', '0', '1 = yes / 0 = no', 'Select Language', 'Allow users to change Kahuk language<br /><strong>When SET to 1, you have to rename the language file that you want to allow in /languages/ folder.</strong> Ex: <span style=\"font-style:italic;color:#004dff\">RENAME lang_italian.conf.default</span> to <span style=\"font-style:italic;color:#004dff\">lang_italian.conf</span>', 'normal', '\''),
(101, 'Misc', 'what_is_kahuk', 'true', 'true', 'true / false', 'Display What is Kahuk in the sidebar?', 'Set it to false if you do not want it to display.<br />If you want it to display but with your own content, Keep it set to true and edit the language file where the entry is KAHUK_Visual_What_Is_Kahuk and KAHUK_Visual_What_Is_Kahuk_Text under the Sidebar section.<br /><a href=\"../admin/module.php?module=admin_language\" target=\"_blank\" rel=\"noopener noreferrer\">Modify Language</a>', 'normal', '\'');

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_formulas`
--

CREATE TABLE `fdfdf_formulas` (
  `id` int NOT NULL,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `formula` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_formulas`
--

INSERT INTO `fdfdf_formulas` (`id`, `type`, `enabled`, `title`, `formula`) VALUES
(1, 'report', 1, 'Simple Story Reporting', '$reports > $votes * 3');

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_friends`
--

CREATE TABLE `fdfdf_friends` (
  `friend_id` int NOT NULL,
  `friend_from` bigint NOT NULL DEFAULT '0',
  `friend_to` bigint NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_groups`
--

CREATE TABLE `fdfdf_groups` (
  `group_id` int NOT NULL,
  `group_creator` int NOT NULL,
  `group_status` enum('Enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_members` int NOT NULL,
  `group_date` datetime NOT NULL,
  `group_safename` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_privacy` enum('private','public','restricted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_vote_to_publish` int NOT NULL,
  `group_field1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_field2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_field3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_field4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_field5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_field6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_notify_email` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_group_member`
--

CREATE TABLE `fdfdf_group_member` (
  `member_id` int NOT NULL,
  `member_user_id` int NOT NULL,
  `member_group_id` int NOT NULL,
  `member_role` enum('admin','normal','moderator','flagged','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_group_shared`
--

CREATE TABLE `fdfdf_group_shared` (
  `share_id` int NOT NULL,
  `share_link_id` int NOT NULL,
  `share_group_id` int NOT NULL,
  `share_user_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_links`
--

CREATE TABLE `fdfdf_links` (
  `link_id` int NOT NULL,
  `link_author` int NOT NULL DEFAULT '0',
  `link_status` enum('discard','new','published','abuse','duplicate','page','spam','moderated','draft','scheduled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'discard',
  `link_randkey` int NOT NULL DEFAULT '0',
  `link_votes` int NOT NULL DEFAULT '0',
  `link_reports` int NOT NULL DEFAULT '0',
  `link_comments` int NOT NULL DEFAULT '0',
  `link_karma` decimal(10,2) NOT NULL DEFAULT '0.00',
  `link_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `link_date` timestamp NULL DEFAULT NULL,
  `link_published_date` timestamp NULL DEFAULT NULL,
  `link_category` int NOT NULL DEFAULT '0',
  `link_lang` int NOT NULL DEFAULT '1',
  `link_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_url_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_title_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link_tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link_field1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field7` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field8` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field9` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field10` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field11` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field12` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field13` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field14` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_field15` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_group_id` int NOT NULL DEFAULT '0',
  `link_group_status` enum('new','published','discard') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `link_out` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_links`
--

INSERT INTO `fdfdf_links` (`link_id`, `link_author`, `link_status`, `link_randkey`, `link_votes`, `link_reports`, `link_comments`, `link_karma`, `link_date`, `link_published_date`, `link_category`, `link_lang`, `link_url`, `link_url_title`, `link_title`, `link_title_url`, `link_content`, `link_summary`, `link_tags`, `link_field1`, `link_field2`, `link_field3`, `link_field4`, `link_field5`, `link_field6`, `link_field7`, `link_field8`, `link_field9`, `link_field10`, `link_field11`, `link_field12`, `link_field13`, `link_field14`, `link_field15`, `link_group_id`, `link_group_status`, `link_out`) VALUES
(1, 1, 'page', 0, 0, 0, 0, '0.00', '2021-05-22 11:51:23', NULL, 0, 1, '', NULL, 'About', 'about', '<legend><strong>About Us</strong></legend>\r\n<p>Our site allows you to submit an article that will be voted on by other members. The most popular posts will be published to the front page, while the less popular articles are left in an \'New\' page until they acquire the set number of votes to move to the published page. This site is dependent on user contributed content and votes to determine the direction of the site.</p>\r\n', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'new', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_login_attempts`
--

CREATE TABLE `fdfdf_login_attempts` (
  `login_id` int NOT NULL,
  `login_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_count` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_messages`
--

CREATE TABLE `fdfdf_messages` (
  `idMsg` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender` int NOT NULL DEFAULT '0',
  `receiver` int NOT NULL DEFAULT '0',
  `senderLevel` int NOT NULL DEFAULT '0',
  `readed` int NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_misc_data`
--

CREATE TABLE `fdfdf_misc_data` (
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_misc_data`
--

INSERT INTO `fdfdf_misc_data` (`name`, `data`) VALUES
('kahuk_version', '5.0.2'),
('adcopy_lang', 'en'),
('adcopy_theme', 'white'),
('adcopy_pubkey', 'Q6OGz0qMg-ByU-3QZ7DevxgQfyqgrpEC'),
('adcopy_privkey', 'HsarZukHD6Tw29fbF1INMUbH4AQs9t-R'),
('adcopy_hashkey', 'cPI6NC6BLyW0rjTLzWEj1UAAyYxQrRi5'),
('captcha_method', 'solvemedia'),
('validate', '0'),
('captcha_comment_en', 'true'),
('captcha_reg_en', 'true'),
('captcha_story_en', 'true'),
('karma_submit_story', '+15'),
('karma_submit_comment', '+10'),
('karma_story_publish', '+50'),
('karma_story_vote', '+1'),
('karma_story_unvote', '-1'),
('karma_comment_vote', '0'),
('karma_story_discard', '-250'),
('karma_story_spam', '-10000'),
('karma_comment_delete', '-50'),
('modules_update_date', '2021/05/22'),
('modules_update_url', 'https://kahuk.com/mods/version-update.txt'),
('kahuk_update', ''),
('kahuk_update_url', 'https://kahuk.com/download/'),
('modules_update_unins', ''),
('modules_upd_versions', ''),
('hash', '18614715851991049412718319915318265641315715387991531891257951881319211850105192188');

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_modules`
--

CREATE TABLE `fdfdf_modules` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` float(10,1) NOT NULL,
  `latest_version` float(10,1) NOT NULL,
  `folder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `weight` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_modules`
--

INSERT INTO `fdfdf_modules` (`id`, `name`, `version`, `latest_version`, `folder`, `enabled`, `weight`) VALUES
(1, 'Admin Modify Language', 2.1, 0.0, 'admin_language', 1, 0),
(2, 'Captcha', 2.5, 0.0, 'captcha', 1, 0),
(3, 'Simple Private Messaging', 3.0, 0.0, 'simple_messaging', 1, 0),
(4, 'Sidebar Stories', 2.2, 0.0, 'sidebar_stories', 1, 0),
(5, 'Sidebar Comments', 2.1, 0.0, 'sidebar_comments', 1, 0),
(6, 'Karma module', 1.0, 0.0, 'karma', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_old_urls`
--

CREATE TABLE `fdfdf_old_urls` (
  `old_id` int NOT NULL,
  `old_link_id` int NOT NULL,
  `old_title_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_redirects`
--

CREATE TABLE `fdfdf_redirects` (
  `redirect_id` int NOT NULL,
  `redirect_old` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect_new` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_saved_links`
--

CREATE TABLE `fdfdf_saved_links` (
  `saved_id` int NOT NULL,
  `saved_user_id` int NOT NULL,
  `saved_link_id` int NOT NULL,
  `saved_privacy` enum('private','public') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_totals`
--

CREATE TABLE `fdfdf_totals` (
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_totals`
--

INSERT INTO `fdfdf_totals` (`name`, `total`) VALUES
('published', 0),
('new', 0),
('discard', 0),
('draft', 0),
('scheduled', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_trackbacks`
--

CREATE TABLE `fdfdf_trackbacks` (
  `trackback_id` int UNSIGNED NOT NULL,
  `trackback_link_id` int NOT NULL DEFAULT '0',
  `trackback_user_id` int NOT NULL DEFAULT '0',
  `trackback_type` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in',
  `trackback_status` enum('ok','pendent','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendent',
  `trackback_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `trackback_date` timestamp NULL DEFAULT NULL,
  `trackback_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `trackback_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `trackback_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_users`
--

CREATE TABLE `fdfdf_users` (
  `user_id` int NOT NULL,
  `user_login` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_level` enum('normal','moderator','admin','Spammer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `user_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_date` timestamp NOT NULL,
  `user_pass` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_names` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_karma` decimal(10,2) DEFAULT '0.00',
  `user_url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_lastlogin` timestamp NULL DEFAULT NULL,
  `user_facebook` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_twitter` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_linkedin` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_googleplus` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_skype` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_pinterest` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `public_email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_avatar_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `user_lastip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `last_reset_request` timestamp NOT NULL,
  `last_reset_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_occupation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_categories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `user_language` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_users`
--

INSERT INTO `fdfdf_users` (`user_id`, `user_login`, `user_level`, `user_date`, `user_pass`, `user_email`, `user_names`, `user_karma`, `user_url`, `user_lastlogin`, `user_facebook`, `user_twitter`, `user_linkedin`, `user_googleplus`, `user_skype`, `user_pinterest`, `public_email`, `user_avatar_source`, `user_ip`, `user_lastip`, `last_reset_request`, `last_reset_code`, `user_location`, `user_occupation`, `user_categories`, `user_enabled`, `user_language`) VALUES
(1, 'mohammadshah', 'admin', '2021-05-22 11:51:23', 'bcrypt:f9e1e85a3$2y$10$h.Zv/GPBtmtvG.j1P4QgXuVwBaZQnfStnBltkiwEDvw6iYSI8/ro.', 'info@kahuk.com', '', '10.00', 'https://kahuk.com', '2021-05-22 11:51:23', '', '', '', '', '', '', '', '', '127.0.0.1', '0', '2021-05-22 11:51:23', NULL, NULL, NULL, '', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_votes`
--

CREATE TABLE `fdfdf_votes` (
  `vote_id` int NOT NULL,
  `vote_type` enum('links','comments') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'links',
  `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vote_link_id` int NOT NULL DEFAULT '0',
  `vote_user_id` int NOT NULL DEFAULT '0',
  `vote_value` smallint NOT NULL DEFAULT '1',
  `vote_karma` int DEFAULT '0',
  `vote_ip` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdfdf_widgets`
--

CREATE TABLE `fdfdf_widgets` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` float NOT NULL,
  `latest_version` float NOT NULL,
  `folder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `column` enum('left','right') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `display` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fdfdf_widgets`
--

INSERT INTO `fdfdf_widgets` (`id`, `name`, `version`, `latest_version`, `folder`, `enabled`, `column`, `position`, `display`) VALUES
(1, 'Dashboard Tools', 0.1, 0, 'dashboard_tools', 1, 'left', 4, ''),
(2, 'Statistics', 3, 0, 'statistics', 1, 'left', 1, ''),
(3, 'Kahuk CMS', 1, 0, 'kahuk_cms', 1, 'right', 5, ''),
(4, 'Kahuk News', 0.1, 0, 'kahuk_news', 1, 'right', 6, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fdfdf_additional_categories`
--
ALTER TABLE `fdfdf_additional_categories`
  ADD UNIQUE KEY `ac_link_id` (`ac_link_id`,`ac_cat_id`);

--
-- Indexes for table `fdfdf_categories`
--
ALTER TABLE `fdfdf_categories`
  ADD PRIMARY KEY (`category__auto_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `category_parent` (`category_parent`),
  ADD KEY `category_safe_name` (`category_safe_name`);

--
-- Indexes for table `fdfdf_comments`
--
ALTER TABLE `fdfdf_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD UNIQUE KEY `comments_randkey` (`comment_randkey`,`comment_link_id`,`comment_user_id`,`comment_parent`),
  ADD KEY `comment_link_id` (`comment_link_id`,`comment_parent`,`comment_date`),
  ADD KEY `comment_link_id_2` (`comment_link_id`,`comment_date`),
  ADD KEY `comment_date` (`comment_date`),
  ADD KEY `comment_parent` (`comment_parent`,`comment_date`);

--
-- Indexes for table `fdfdf_config`
--
ALTER TABLE `fdfdf_config`
  ADD PRIMARY KEY (`var_id`);

--
-- Indexes for table `fdfdf_formulas`
--
ALTER TABLE `fdfdf_formulas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fdfdf_friends`
--
ALTER TABLE `fdfdf_friends`
  ADD PRIMARY KEY (`friend_id`),
  ADD UNIQUE KEY `friends_from_to` (`friend_from`,`friend_to`);

--
-- Indexes for table `fdfdf_groups`
--
ALTER TABLE `fdfdf_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `group_name` (`group_name`(100)),
  ADD KEY `group_creator` (`group_creator`,`group_status`);

--
-- Indexes for table `fdfdf_group_member`
--
ALTER TABLE `fdfdf_group_member`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `user_group` (`member_group_id`,`member_user_id`);

--
-- Indexes for table `fdfdf_group_shared`
--
ALTER TABLE `fdfdf_group_shared`
  ADD PRIMARY KEY (`share_id`),
  ADD UNIQUE KEY `share_group_id` (`share_group_id`,`share_link_id`);

--
-- Indexes for table `fdfdf_links`
--
ALTER TABLE `fdfdf_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_author` (`link_author`),
  ADD KEY `link_url` (`link_url`(50)),
  ADD KEY `link_status` (`link_status`),
  ADD KEY `link_title_url` (`link_title_url`(50)),
  ADD KEY `link_date` (`link_date`),
  ADD KEY `link_published_date` (`link_published_date`);
ALTER TABLE `fdfdf_links` ADD FULLTEXT KEY `link_url_2` (`link_url`,`link_url_title`,`link_title`,`link_content`,`link_tags`);
ALTER TABLE `fdfdf_links` ADD FULLTEXT KEY `link_tags` (`link_tags`);
ALTER TABLE `fdfdf_links` ADD FULLTEXT KEY `link_search` (`link_title`,`link_content`,`link_tags`);

--
-- Indexes for table `fdfdf_login_attempts`
--
ALTER TABLE `fdfdf_login_attempts`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `login_username` (`login_ip`,`login_username`);

--
-- Indexes for table `fdfdf_messages`
--
ALTER TABLE `fdfdf_messages`
  ADD PRIMARY KEY (`idMsg`);

--
-- Indexes for table `fdfdf_misc_data`
--
ALTER TABLE `fdfdf_misc_data`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `fdfdf_modules`
--
ALTER TABLE `fdfdf_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fdfdf_old_urls`
--
ALTER TABLE `fdfdf_old_urls`
  ADD PRIMARY KEY (`old_id`),
  ADD KEY `old_title_url` (`old_title_url`(50));

--
-- Indexes for table `fdfdf_redirects`
--
ALTER TABLE `fdfdf_redirects`
  ADD PRIMARY KEY (`redirect_id`),
  ADD KEY `redirect_old` (`redirect_old`(50));

--
-- Indexes for table `fdfdf_saved_links`
--
ALTER TABLE `fdfdf_saved_links`
  ADD PRIMARY KEY (`saved_id`),
  ADD KEY `saved_user_id` (`saved_user_id`);

--
-- Indexes for table `fdfdf_totals`
--
ALTER TABLE `fdfdf_totals`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `fdfdf_trackbacks`
--
ALTER TABLE `fdfdf_trackbacks`
  ADD PRIMARY KEY (`trackback_id`),
  ADD UNIQUE KEY `trackback_link_id_2` (`trackback_link_id`,`trackback_type`,`trackback_url`),
  ADD KEY `trackback_link_id` (`trackback_link_id`),
  ADD KEY `trackback_url` (`trackback_url`),
  ADD KEY `trackback_date` (`trackback_date`);

--
-- Indexes for table `fdfdf_users`
--
ALTER TABLE `fdfdf_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_login` (`user_login`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `fdfdf_votes`
--
ALTER TABLE `fdfdf_votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `user_id` (`vote_user_id`),
  ADD KEY `link_id` (`vote_link_id`),
  ADD KEY `vote_type` (`vote_type`,`vote_link_id`,`vote_user_id`,`vote_ip`);

--
-- Indexes for table `fdfdf_widgets`
--
ALTER TABLE `fdfdf_widgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folder` (`folder`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fdfdf_categories`
--
ALTER TABLE `fdfdf_categories`
  MODIFY `category__auto_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fdfdf_comments`
--
ALTER TABLE `fdfdf_comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_config`
--
ALTER TABLE `fdfdf_config`
  MODIFY `var_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `fdfdf_formulas`
--
ALTER TABLE `fdfdf_formulas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fdfdf_friends`
--
ALTER TABLE `fdfdf_friends`
  MODIFY `friend_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_groups`
--
ALTER TABLE `fdfdf_groups`
  MODIFY `group_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_group_member`
--
ALTER TABLE `fdfdf_group_member`
  MODIFY `member_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_group_shared`
--
ALTER TABLE `fdfdf_group_shared`
  MODIFY `share_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_links`
--
ALTER TABLE `fdfdf_links`
  MODIFY `link_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fdfdf_login_attempts`
--
ALTER TABLE `fdfdf_login_attempts`
  MODIFY `login_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_messages`
--
ALTER TABLE `fdfdf_messages`
  MODIFY `idMsg` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_modules`
--
ALTER TABLE `fdfdf_modules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fdfdf_old_urls`
--
ALTER TABLE `fdfdf_old_urls`
  MODIFY `old_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_redirects`
--
ALTER TABLE `fdfdf_redirects`
  MODIFY `redirect_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_saved_links`
--
ALTER TABLE `fdfdf_saved_links`
  MODIFY `saved_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_trackbacks`
--
ALTER TABLE `fdfdf_trackbacks`
  MODIFY `trackback_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_users`
--
ALTER TABLE `fdfdf_users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fdfdf_votes`
--
ALTER TABLE `fdfdf_votes`
  MODIFY `vote_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdfdf_widgets`
--
ALTER TABLE `fdfdf_widgets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
