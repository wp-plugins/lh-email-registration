
=== LH Email Registration ===
Contributors: shawfactor
Donate link: http://lhero.org/plugins/lh-email-registration/
Tags: email, backend, wp-admin, admin, registration
Requires at least: 3.0.
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Streamlines user registration in the backend by removing redundant fields and replaces usernames with email addresses

== Description ==

This plugin removes the username field from the add new user screen in the WordPress back end, instead the email address is used as the username. It also removed the website field (generally not required), as well as password fields (as passwords are auto generated). These changes make adding new users to WordPress a lot faster.

One word of caution is that this plugin replaces the username with the email address of the user when a user is saved in the database. meaning your users will no longer have a separate username. 

Check out [our documentation][docs] for more information.

All tickets for the project are being tracked on [GitHub][].

[docs]: http://lhero.org/plugins/lh-email-registration/
[GitHub]: https://github.com/shawfactor/lh-login-page

== Installation ==

1. Upload the entire `lh-email-registration` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Start adding user using the streamlined form via Users->Add New


== Changelog ==

**1.0 July 30, 2015**  
Initial release.

