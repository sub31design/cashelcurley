=== link-log ===
Contributors: smartware.cc, petersplugins
Donate link:http://smartware.cc/make-a-donation/
Tags: log, click, click counting, link analytics, tracking, visitor tracking, external links
Requires at least: 3.0
Tested up to: 4.4
Stable tag: 2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Find out where your visitors leave to. Track clicks on external links. 

== Description ==

> This Plugin allows you to track which external links your visitors click on.

**See also [Plugin Homepage](http://smartware.cc/free-wordpress-plugins/link-log/) and [Plugin Doc](http://smartware.cc/docs/link-log/)**

The link-log Plugin changes all your links to external sites. For example **http://www.google.com** is changed to something like **http://www.example.com/?goto=HUESQ2ifGipAlHg4OTUzfihAUgfz1La8**. The link change takes place when a post or page is displayed. Internal links to pages on your domain are not changed, also URLs not starting with "http" or "https" are not changed. Also attributes (like class or target) are not touched.

There is **no need to change anything**. All Links in all posts and pages are changed automatically in front end. When editing a post or page in back end all links appear unchanged.

**Notice: Version 2.0 comes with a lot of changes. See [this article](http://smartware.cc/blog/2015/12/10/link-log-version-2-0-is-out/) for details.**

= Settings (optional) =

In 'Settings' -> 'link-log' you can change several settings. **It is highly recommended to change the IP Lock Setting and the Search Engines Setting** as desired. See [Plugin Doc](http://smartware.cc/docs/link-log/) for details.

= Theme functions =

There are two functions you can use in your theme files:

**`get_linklog_url( $url )`** to get the tracking URL, 
e.g. `<?php $google = get_linklog_url( 'http://www.google.com' ); ?>`

**`the_linklog_url( $url )`** to echo the tracking URL, 
e.g. `<a href="<?php the_linklog_url( 'http://www.google.com' ); ?>" target"=_blank">Google</a>`

= Do you like the link-log Plugin? =

Thanks, I appreciate that. You don't need to make a donation. No money, no beer, no coffee. Please, just [tell the world that you like what I'm doing](http://smartware.cc/make-a-donation/)! And that's all.

= More plugins from smartware.cc =

* **[404page](https://wordpress.org/plugins/404page/)** Define any of your WordPress pages as 404 error page 
* **[hashtagger](https://wordpress.org/plugins/hashtagger/)** - Tag your posts by using #hashtags
* **[smart Archive Page Remove](https://wordpress.org/plugins/smart-archive-page-remove/)** - Completely remove unwated Archive Pages from your Blog 
* **[smart User Slug Hider](https://wordpress.org/plugins/smart-user-slug-hider/)** - Hide usernames in author pages URLs to enhance security 
* **[JavaScript AutoLoader](https://wordpress.org/plugins/javascript-autoloader/)** - Load JavaScript files without changing files in the theme directory or installing several plugins to add all the desired functionality

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins' -> 'Add New'
1. Search for 'link-log'
1. Activate the plugin through the 'Plugins' menu in WordPress

= Manually from wordpress.org =

1. Download link-log from wordpress.org and unzip the archive
1. Upload the `link-log` folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Do I have to change the links?  =

No, all your links to external sites are changed automatically.

= Will the changed links appear in the Editor? =

No, the links are changed every time when a post or page is displayed - the original links are left unchanged.

= What about all the odd characters in the generated links? =

Those characters represent the encrypted link. The encryption produces different results on different websites. Using uncoded links for redirection could be used in phishing attacks to get users to visit malicious sites without realizing it by "hijacking" a reputable domain, e.g. http://www.example.com/?goto=http://www.this-is-a-very-bad-phishing-site.com.

= Will this work with a Caching Plugin?  =

Yes, but if you change the parameter name you have to clear the cache.

= Will the plugin work with my existing Theme?  =

The plugin should work with all Themes.

= Will the plugin work on WordPress Multisite?  =

Yes, in a Multisite installation the plugin stores the link clicks per blog.

== Screenshots ==

1. General Settings
2. Advanced Settings
3. Automation Settings
4. Analysis Page Customization
5. Adding Link Descriptions
6. Link Click Analysis

== Changelog ==

= 2.1 (2015-12-11) =
* Closed SQL Injection vulnerability

= 2.0 (2015-12-10) =
* Closed HTTP Response Splitting vulnerability
* Closed Open Redirect vulnerability
* Menu item title for Link Click Analysis page is now customizable
* Page title for Link Click Analysis page is now customizable
* Customizable link descriptions to show on Link Click Analysis page instead of URLs

= 1.4 (2015-04-28) =
* Option to add rel="nofollow" to links
* Option to track only specific posts/pages
* Complete documentation accessible from back end
* Click Analysis now accessible also for Editors, not only for Admins
* Click Analysis now uses standard WP table
* Filtering of results

= 1.3 (2014-10-26) = 
* Works now with WPML  
The [WPML Plugin](http://wpml.org) changes the Home URL by adding the language to it - link-log now can handle that to work with WPML and other Plugins that change the Home URL (thanks to [GREIFF](http://greiff.de/en/) for testing)
* Performance Improvement  
The browser is now forced to redirect to the target URL **before** the data is stored to the databse
* remove trailing slashes  
To avoid duplicate entries for e.g. example.com and example.com/ all trailing slashes are removed now  
**Update Notice**: when updating to version 1.3 all trailing slashes from all existing entries in the database are removed automatically

= 1.2 (2014-09-19) =
* Omit search engines and other bots

= 1.1 (2014-06-25) =
* Omit multiple clicks from same IP

= 1.0 (2014-02-20) =
* Initial Release

== Upgrade Notice ==

= 2.1 =
Version 2.1 closes an Security Exploit. Upgrade immediately to secure your WordPress installation.

= 2.0 =
Version 2.0 comes with a lot of changes. Upgrade immediately. See [this article](http://smartware.cc/blog/2015/12/10/link-log-version-2-0-is-out/) for details.