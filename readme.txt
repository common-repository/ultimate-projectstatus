=== Ultimate-ProjectStatus ===
Contributors: zero-one
Donate link: http://zero-one.ch/
Tags: projects, coding, integration
Requires at least: 2.0.2
Tested up to: 3.0.1
Stable tag: 0.1.3

== Description ==

With this Plugin you can show a nice Overview of all of your projects. 

= Features =
* Fully customizable with Template Files and CSS
* Backend Interface for full controll
* perfect for Development and project Blogs
* Display Workers in each project. They are automatically linked to their wordpress accounts

== Installation ==

1. Upload the whole Plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure your Repositories over the 'UltimateProjectStatus' menubutton.
4. Create or edit a Page and put %projectstatus% anywhere in the page content. This placeholder gets replaced with the overview.

= Configuration =
In the plugins directory edit the both files `templates/projectentry.html`, `templates/projectlist.html` and `style.css` to customize the layout.

In the admin Panel of the plugin you can add your projects. The following settings are used.
1. *ProjectStart* (required): Start of the project
1. *ProjectEnd* (required): Expected end of the project. Only the duration in days will be displayed
1. *Name* (optional): Human friendly name of the project
1. *CodeName* (optional): CodeName of the Project. Every real project should one have ;-) Either Name oder CodeName is required. Both together are combined.
1. *Description* (required): Its description
1. *Workers* (optional): Comma seperated List of workers. Wordpress Usernames are getting resolved to its displayname.
1. *ProjectURL* (optional): URL to the Project
1. *Status* (optional): Status of the Project. Written in a sentence.
1. *StatusID* (optional): Status ID. See the second table
1. *UpdateDate* (optional): Date of the last update of the record. The latest Date in the whole table is taken and displayd at the top of the page.

The ProjectStatus table has the following fields:
1. *StatusName* (required): Textual Value of the Status
1. *Color* (required): Color used to display the StatusName. Can be used for CSS.


== Frequently Asked Questions ==

= Are there any Problems known? =

Yes, currently are the following Problems known:
- The Configuration Interface is not as user-friendly as it should be.


== Screenshots ==
1. **Example** - This is how the Projectstatuslist could look like

== Changelog ==

= 0.1.3 =
* Bugfixing

= 0.1.2 =
* Added Plugin Statistics

= 0.1.1 =
* Fixed the Plugin activation bug

= 0.1 =
* First development release - the configuration interface needs some usability tweaks!
