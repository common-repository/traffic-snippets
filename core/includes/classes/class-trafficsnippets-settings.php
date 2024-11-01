<?php

// Exit if accessed directly. 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START 
 * 
 * This class contains all of the plugin related settings.
 * Everything that is relevant data and used multiple times throughout 
 * the plugin.
 * 
 * To define the actual values, we recommend adding them as shown above
 * within the __construct() function as a class-wide variable. 
 * This variable is then used by the callable functions down below. 
 * These callable functions can be called everywhere within the plugin 
 * as followed using the get_plugin_name() as an example: 
 * 
 * TRAFFIC_SNIPPETS->settings->get_plugin_name();
 * 
 * HELPER COMMENT END
 */

/**
 * Class Traffic_Snippets_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		TRAFFIC_SNIPPETS
 * @subpackage	Classes/Traffic_Snippets_Settings
 * @author		Max Lumnar
 * @since		1.0.0
 */
class Traffic_Snippets_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * The plugin capabilities
	 *
	 * @var		array
	 * @since	1.0.0
	 */
	private $capabilities;

	/**
	 * Our Traffic_Snippets_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		$this->plugin_name = TRAFFIC_SNIPPETS_NAME;
		$this->capabilities = array(
			'default' => 'manage_options',
		);
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'TRAFFIC_SNIPPETS/settings/get_plugin_name', $this->plugin_name );
	}


	/**
	 * Return the specified plugin capability
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The chosen capability
	 */
	public function get_capability( $identifier = 'default' ) {

		$capability = $this->capabilities[ 'default' ];
		if( ! empty( $identifier ) && isset( $this->capabilities[ $identifier ] ) ){
			$capability = $this->capabilities[ $identifier ];
		}

		return apply_filters( 'TRAFFIC_SNIPPETS/settings/get_capability', $capability, $identifier, $this->capabilities );
	}

}
