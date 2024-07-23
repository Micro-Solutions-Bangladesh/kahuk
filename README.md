# Kahuk CMS

**Kahuk**, based on formerly PLIGG, KLIQQI, and PLIKLI, is an open-source content management system (CMS) that lets you easily create your user-powered website for link-sharing and creating a social community like Reddit. Since Kahuk CMS will be open source and is released under GNU v3.0, you are always free to contribute anytime.

Kahuk CMS powered websites offers to bookmark endless articles, conversations, images, and videos using web URLs. The community can comment on posts that provide discussion and often humor.

Feel free to contact us at [https://www.facebook.com/KahukCMS/](https://www.facebook.com/KahukCMS/) for any query related to the contribution in Kahuk CMS.

***

### Features

* Users can submit Links
* Users can write Blogs
* Users can Upvote/Downvote stories
* Users can save their favourite contents
* Users can create and share in Groups
* Users can comment on stories
* Users can follow others
* Integrated Spam Control
* Karma integration
* SEO optimized
* Live News ticker based on user's preference
* Dozens of language support
* External modules and theme integration
* Hundreds of Dashboard options to optimize the platform as per user needs
* Dozen of amazing free modules already integrated
* Marketplace for users and module and theme developers
* and many more amazing features...

***
### Requirements:

#### PHP
*Kahuk CMS* has been tested on PHP version 7.0.2. We have designed the Content Management System based on PHP 7.0.2 technologies, so certain problems may occur when using older versions of PHP. We recommend that your server run a minimum of PHP 7.0.2.

#### PHP Extensions
* cURL PHP
* GD Graphics Library

#### PHP Functions
<small>Can be checked and set in cPanel under MultiPHP INI Editor.</small>

* `fopen` (PHP Directive: allow_url_fopen)
* `file_get_contents` (PHP Directive: allow_url_fopen)
* `fwrite` (Function is used in conjunction with the `fopen` Function)

#### MySQL
Kahuk has been tested on MySQL version 5, for this reason, it is recommended that you use a server with MySQL 5.0.3+ or later to run a *Kahuk CMS* website.

### Installation
Upload the entire *KAhuk CMS* package (as it is) into you intended hosting location and browse the path into your browser.

## What Happens in The CMS

#### User Register
When a new user register he/she will be sent an email to verify the email address is correct before the new account count as a registered account.


#### Fork Story
When a user fork a story, the story will be visible in his/her profile page as a forked story. In the process, the CMS also update few Karma numbers:
 
- Increase story karma by the numbers mentioned as `FORK_KARMA_FOR_STORY`
- Increase karma for the session user by the numbers mentioned as `FORK_KARMA_FOR_USER`


#### Story Reaction

- When a user react to a story, the story will be visible in his/her profile page with the Reaction information. In the process, the CMS also update few Karma numbers:
- Increase story karma by the numbers mentioned as `REACTION_KARMA_FOR_STORY`
- Increase karma for the session user by the numbers mentioned as `REACTION_KARMA_FOR_USER`


#### Story Submit
 
- When a new story submited, the story initially shows in as a new story and also in the trending story pages. By the time when a story karma reach equal or higher then `NEW_TO_PUBLISHED_KARMA` the story will be appear in publish story and trending story pages depending on the submit time and karma value.

> In the process of new story submit, the CMS also update few Karma numbers:
 
- Initially the story karma will be `NEW_STORY_KARMA_INITIALY`.
- Increase karma for the session user by the numbers mentioned in the `_new_story_karma_for_user` config from db.

#### Recommended Plugins

- [CAPTCHA for Kahuk CMS](https://github.com/Micro-Solutions-Bangladesh/captcha) plugin enables a [Kahuk CMS](https://kahuk.com/) powered website to avoid unwanted request using Google reCaptcha.
