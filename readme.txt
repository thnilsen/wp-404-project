=== 404-project ===
Contributors: thnilsen
Tags: 404, 404project,SANS,ISC,Internet Storm center
Requires at least: 5.0
Tested up to: 5.8
Stable tag: 1.0.0
Requires PHP: 5.6
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The 404Project collects \"Page Not Found\" errors from web servers and submits data to SANS ISC for their weblog collector to discover web application attack trends.

More information on the original 404Project can be found at https://isc.sans.edu/tools/404project.html

== Description ==
The SANS ISC 404Project collects information from web requests that ends up on a 404 \"Page Not Found\". Attackers often search for vulnerable applications, and in the process generate 404 errors on sites that do not use these applications. The error logs give us an insight into what vulnerabilities attackers attempt to exploit.
The information is collected by The Internet Storm Center (SANS ISC) at http://isc.sans.org
The code will rate limit submissions. Any 404 requests during a rate limit period will be ignored. This is to prevent overloading / DoS conditions.

The code is based on SANS original PHP code for the 404Project.

== Screenhosts ==

1. Simple settings page which makes it quick to get started.

== Frequently Asked Questions ===

= Is WP 404 Project Free? ==

Yes. There is no cost associated with using this plugin.

= Where can I get a User ID and API Key? =

In order to use this plugin you will need to have an account on isc.sans.org, or sign up for one. These are free and anyone can get one. Go to https://isc.sans.edu/login.html if you already have an account with them, or if not register using https://isc.sans.edu/register.html.
Once you have logged on, go to My Account (https://isc.sans.edu/myaccount.html) and copy the User ID and API Key to the settings screen of this plugin.

= Nothing seems to be happening? =

This plugin will not modify the content or make any visible output on your Wordpress site. It only adds a filter to the internal Wordpress 404 error handling code.

= How can I view my submissions? =

Log on to My Account at SANS and go to the My 404 Reports dashboard. This will list out details of date,time,URL,user agent and source IP. Please note that it might take some time to show new records as SANS only parses the logs twice per hour.

= What is the rate limit good for? =

Rate limit is a setting to prevent the servers of SANS ISC to be overloaded with log requests from this plugin. It will make sure that one cannot submit more than one 404 report per x seconds, where X can be chosen of 10, 30 og 60 seconds. Without the limit setting your server could be pushing too many requests onto SANS servers if your server was overloaded with 404 requests that you then forward on to SANS ISC.

= What is the IP Mask setting? =

In order to mask/hide part of the reported IP address of which host generated a 404 page not found error, one can apply a mask to change bits of the IP address. The principle is very easy. An IPv4 address consists of 4 octets x.x.x.x. If you want hide one of the 4 octets to provide some privacy to the host you can change that particular mask bit to 0 rather than F. The string must always start with 0x and can only contain 0 or F/f.
To remove the last octet from and IP use 0xFFFFFF00. This would turn 192.168.100.102 into 192.168.100.0. A mask of 0xFFFF0000 will return 192.168.0.0 for the same IP.

= Does it support IPv6? =

No. IPv6 is not supported.

= What are the differences between REDIRECT_URL and REQUEST_URI? =

With REQUEST_URL the full GET URL used in the request against your Wordpress site is logged. This would include any parameter passed as part of the URL, .ie, (/index.php?user=123&id=9939).
With REDIRECT_URL only the URL without any parameter is logged, i.e. (/index.php)

= Does data collected with this plugin have any GDPR related concerns? =

Yes. IP addresses are considered PII (Personal Identifiable Information) and as such fall in under GDRP. Use the IP Mask setting of 0xFFFFFF00 to hide the actual IP of your 404 visitors to be GDPR compliant.

= How do I troubleshoot issues? =

If you run into any problems with using this plugin, enable WP_DEBUG in your sites wp-config.php file. This should produce a php_error.log file in the root folder of your Wordpress site. Next, turn on the Debug option of this plugin and generate a 404 request by accessing your site with a non existing URL. This should normally then generate some information in the php_error.log file to give you an idea of what's going on.
Things to consider are:
- Make sure PHP is allowed to do outbound connections
- Make sure your firewall allows your PHP server to submit HTTP or HTTPS requests to isc.sans.edu
- Make sure is that the cURL library for PHP is installed on your server. (Not always needed as Wordpress has a builtin web request api!)

== Changelog ==

= 1.0.0 =
* Initial Public release of this plugin
