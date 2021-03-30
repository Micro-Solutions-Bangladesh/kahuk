<h1><code>Kahuk CMS</code></h1>
<h2>under GNU General Public License v3.0</h2>

<div align="center">
    <small><p><em>Kahuk</em>,(based on formerly PLIGG, KLIQQI and PLIKLI), is an open source content management system (CMS) that lets you easily create your own user-powered website for link sharing and creating a social community like Reddit. Since Kahuk CMS will be opensource and is releasing under GNU v3.0, you are always free to contribute anytime.</p></small>
</div>

Feel free to contact us at [https://www.facebook.com/KahukCMS/](https://www.facebook.com/KahukCMS/) to join in the contribution for Kahuk CMS.
___

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

___

## Installation

### Requirements:

#### PHP
*Kahuk CMS* has been tested on PHP version 7.0.2. We have designed the Content Management System based on PHP 7.0.2 technologies, so certain problems may occur when using older versions of PHP. We recommend that your server runs a minimum of PHP 7.0.2.

#### PHP Extensions
* cURL PHP
* GD Graphics Library

#### PHP Functions
<small>Can be checked and set in cPanel under MultiPHP INI Editor.</small>

* fopen (PHP Directive: allow_url_fopen)
* file_get_contents (PHP Directive: allow_url_fopen)
* fwrite (Function is used in conjunction with the fopen Function)

#### MySQL
Kahuk has been tested on MySQL versions 5, for this reason it is recommend that you use a server with MySQL 5.0.3+ or later to run a *Kahuk CMS* website.
			

### Troubleshooter:

To test if your server is capable of running Kahuk please view the "Troubleshooter". If any errors appear on that page you may have a problem with either installing or running Kahuk.

But, fear not, other than the requirement number 1 (Create a mysql database), Kahuk Installation system will fix all the other requirements for you! The requirements listed below are just for your info and to get familiar with them and make sure that this is what you need to install Kahuk CMS.

Once you read the readme page, proceed to "Troubleshooter" in the navigation menu and follow the process that shows you the readiness to proceed with the installation. Once you have finished with the Troubleshooter, Click on the "Install" link in the navigation menu!

### Install Steps:

* Create a mysql database. If you are unfamiliar with how to create a mysql database, please contact your web host or search their support site. Please pay careful attention when creating a database and write down your database name, username, password, and host somewhere.
* Rename the `/favicon.ico.default` to `/favicon.ico`
* Rename the `/settings.php.default` to `/settings.php`
* Rename the `/languages/lang_english.conf.default` file to `/languages/lang_english.conf`. Same instructions apply to any other language file that you might use that are located in the `/languages` directory.
* Rename the `/libs/dbconnect.php.default` file to `/libs/dbconnect.php`
* Rename the directory `/logs.default` to `/logs`
* Upload the files to your server.
* CHMOD 755 the following directories and files. If you experience any errors try 777.
    * `/admin/backup/`
    * `/avatars/groups_uploaded/`
    * `/avatars/user_uploaded/`
    * `/cache/`
    * `/languages/` (all files contained in this folder should be CHMOD 777)
    * `/logs/` (all files contained should be CHMOD 777)
* CHMOD 666 the following files
    * `/libs/dbconnect.php`
    * `/settings.php`
* Open `/install/index.php` in your web browser. If you are reading this document after you uploaded it to your server, click on the install link at the top of the page.
    * Select a language from the list. 
    * Fill out your database name, username, password, host, and your desired table prefix.
    * Create an admin account. Please write down the login credentials for future reference.
    * Make sure there are no error messages! If you see an error message, or if installation fails, [report it here](https://github.com/Micro-Solutions-Bangladesh/kahuk/issues) or [report it here](https://www.facebook.com/KahukCMS/).
* Delete your `/install` folder.
* CHMOD *644* `libs/dbconnect.php`
* Open `/index.php`
* Log in to the admin account using the credentials generated during the install process.
* Log in to the admin panel ( `/admin` ).
* Configure your Kahuk site to your liking.

## Task Lists

- [ ] Our very first task is to rename the *plikli cms*. for this purpose, we have a fresh copy of *plikli cms* in a saperate branch to rename it to *kahuk cms*.
- [ ] Once we complete renaming the cms into *plikli cms*, we will go for further update.
