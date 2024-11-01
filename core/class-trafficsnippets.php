<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL); 
//define( 'WP_DEBUG', true );
//define( 'WP_DEBUG_DISPLAY', true );

ob_start();
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This is the main class that is responsible for registering
 * the core functions, including the files and setting up all features. 
 * 
 * To add a new class, here's what you need to do: 
 * 1. Add your new class within the following folder: core/includes/classes
 * 2. Create a new variable you want to assign the class to (as e.g. public $helpers)
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Traffic_Snippets_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 * 
 * HELPER COMMENT END
 */

if ( ! class_exists( 'Traffic_Snippets' ) ) :

	/**
	 * Main Traffic_Snippets Class.
	 *
	 * @package		TRAFFIC_SNIPPETS
	 * @subpackage	Classes/Traffic_Snippets
	 * @since		1.0.0
	 * @author		Jon Doe
	 */
	final class Traffic_Snippets {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Traffic_Snippets
		 */
		private static $instance;

		/**
		 * TRAFFIC_SNIPPETS helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Traffic_Snippets_Helpers
		 */
		public $helpers;

		/**
		 * TRAFFIC_SNIPPETS settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Traffic_Snippets_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'trafficsnippets' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'trafficsnippets' ), '1.0.0' );
		}

		/**
		 * Main Traffic_Snippets Instance.
		 *
		 * Insures that only one instance of Traffic_Snippets exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Traffic_Snippets	The one true Traffic_Snippets
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Traffic_Snippets ) ) {
				self::$instance					= new Traffic_Snippets;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Traffic_Snippets_Helpers();
				self::$instance->deploy		= new Traffic_Snippets_Deploy();
				self::$instance->settings		= new Traffic_Snippets_Settings();

				//Fire the plugin logic
				new Traffic_Snippets_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'TRAFFIC_SNIPPETS/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once TRAFFIC_SNIPPETS_PLUGIN_DIR . 'core/includes/classes/class-trafficsnippets-helpers.php';
			require_once TRAFFIC_SNIPPETS_PLUGIN_DIR . 'core/includes/classes/class-trafficsnippets-settings.php';
			require_once TRAFFIC_SNIPPETS_PLUGIN_DIR . 'core/includes/classes/class-trafficsnippets-deploy.php';
			require_once TRAFFIC_SNIPPETS_PLUGIN_DIR . 'core/includes/classes/class-trafficsnippets-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'trafficsnippets', FALSE, dirname( plugin_basename( TRAFFIC_SNIPPETS_PLUGIN_FILE ) ) . '/languages/' );
		}



	}

endif; // End if class_exists check.