<?php
/*
Plugin Name: ultimate-projectstatus
Plugin URI: http://code.zero-one.ch/projects/wordpress-ultimate-project-status/
Description: With this Plugin you can show a nice Overview of all of your projects.
Version: 0.1.3 
Author: zero-one
Author URI: http://www.zero-one.ch
*/

// ********************************************************
// ************************* INIT *************************
// Including additional files

$_basepath_UPS = WP_PLUGIN_DIR . "/ultimate-projectstatus/";
$_baseurl_UPS = WP_PLUGIN_URL . "/ultimate-projectstatus/";

require_once($_basepath_UPS."config.php");
require_once($_basepath_UPS."admin.php");
require_once($_basepath_UPS."class_base.php");
require_once($_basepath_UPS."class_ultimate-projectStatus.php");


$_ultimateprojectstatus = new UltimateProjectStatus();

/// *********************************************************
// ************************* HOOKS *************************
add_action('init', 'ZO_UPS_Plugin_Init');
add_action('wp_head', 'ZO_UPS_LoadCSS');
add_action('admin_menu', 'ZO_UPS_Register_AdminMenu');
add_filter('the_content', 'ZO_UPS_loadfrontend');

// *************************************************************
// ************************* FUNCTIONS *************************

function ZO_UPS_Plugin_Init() {
	ini_set("display_errors", TRUE);
}

function ZO_UPS_Register_AdminMenu() {
	global $ZO_UPS_config;
	
	$pluginname = $ZO_UPS_config['name_long'];
	
	add_menu_page($ZO_UPS_config['name_long'],$ZO_UPS_config['name_short'], 10, $pluginname, 'ZO_UPS_display_adminmenu'); 
	//add_submenu_page($pluginname, 'Daten Verwaltung', 'Daten Verwaltung', 10, $pluginname."datamgm", 'wedding_display_datamgm');
}

function ZO_UPS_loadfrontend($content = '') {
	global $_ultimateprojectstatus;
	
	return $_ultimateprojectstatus->loadfrontend($content);
}

function ZO_UPS_LoadCSS() {
	global $_baseurl_UPS;
	echo ( '<link rel="stylesheet" type="text/css" media="all" href="'. $_baseurl_UPS . 'style.css">' ); 
}
