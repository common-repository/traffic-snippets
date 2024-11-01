<?php
  
/** 
 * Traffic Snippets
 *
 * @package       TRAFFIC_SNIPPETS
 * @author        Max Lumnar
 * @version       1.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   Traffic Snippets
 * Plugin URI:    https:/lumnar.tech/
 * Description:   Vertical traffic analysis based on flexible snippets and hooks. Extract the exact metrics you wish to analyse independently by injecting simple PHP or JS snippets. This allows you to analyse the relationship between the resulting data and conversion percentage or any target metrics by efficiently stripping the time vector.
 * Version:       1.0.1
 * Author:        Max Lumnar
 * Author URI:    https://lumnar.com
 * Text Domain:   trafficsnippets
 * Domain Path:   /languages
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function TRAFFIC_SNIPPETS() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define( 'TRAFFIC_SNIPPETS_NAME',			'Traffic Snippets' );

// Plugin version
define( 'TRAFFIC_SNIPPETS_VERSION',		'1.0.1' );

// Plugin Root File
define( 'TRAFFIC_SNIPPETS_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'TRAFFIC_SNIPPETS_PLUGIN_BASE',	plugin_basename( TRAFFIC_SNIPPETS_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'TRAFFIC_SNIPPETS_PLUGIN_DIR',	plugin_dir_path( TRAFFIC_SNIPPETS_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'TRAFFIC_SNIPPETS_PLUGIN_URL',	plugin_dir_url( TRAFFIC_SNIPPETS_PLUGIN_FILE ) );



/**
 * Load the main class for the core functionality
 */
require_once TRAFFIC_SNIPPETS_PLUGIN_DIR . 'core/class-trafficsnippets.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Max Lumnar
 * @since   1.0.0
 * @return  object|Traffic_Snippets
 */
function TRAFFIC_SNIPPETS() {
	return Traffic_Snippets::instance();
}

TRAFFIC_SNIPPETS();
