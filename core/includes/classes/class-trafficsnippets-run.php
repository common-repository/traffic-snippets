<?php
//die('work');
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START 
 *  
 * This class is used to bring your plugin to life. 
 * All the other registered classed bring features which are
 * controlled and managed by this class.
 * 
 * Within the add_hooks() function, you can register all of 
 * your WordPress related actions and filters as followed:
 * 
 * add_action( 'my_action_hook_to_call', array( $this, 'the_action_hook_callback', 10, 1 ) );
 * or
 * add_filter( 'my_filter_hook_to_call', array( $this, 'the_filter_hook_callback', 10, 1 ) );
 * or
 * add_shortcode( 'my_shortcode_tag', array( $this, 'the_shortcode_callback', 10 ) );
 * 
 * Once added, you can create the callback function, within this class, as followed: 
 * 
 * public function the_action_hook_callback( $some_variable ){}
 * or
 * public function the_filter_hook_callback( $some_variable ){}
 * or
 * public function the_shortcode_callback( $attributes = array(), $content = '' ){}
 * 
 * 
 * HELPER COMMENT END
 */
 
 /*
 Info and tutorials i found helpful:
 
 https://mac-blog.org.ua/wordpress-custom-database-table-example-full
 https://blog.wplauncher.com/create-wordpress-plugin-settings-page/
 https://wpmudev.com/blog/adding-admin-notices/
 
 Max
*/ 

/**
 * Class Traffic_Snippets_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		TRAFFIC_SNIPPETS
 * @subpackage	Classes/Traffic_Snippets_Run
 * @author		Max Lumnar
 * @since		1.0.2
 */
class Traffic_Snippets_Run{

	/**
	 * Our Traffic_Snippets_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
		$this->check_db_upgrade('1.0.26'); //WARNING: If you change this value demo entries will be added again. Change only when db structure changes

	}


		
	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS 
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
		
		//not sure why these dont work if placed in the if below, here they work fine
		add_action( 'wp_head',array($this,'set_ajaxurl'),5);
		add_action( 'wp_ajax_save_track_js_callback', array($this,'save_track_js_callback') );
		add_action( 'wp_ajax_nopriv_save_track_js_callback', array($this,'save_track_js_callback') );
		
		//BACKEND
		if ( is_admin() ) { 
			//options
			add_option('traffic_snippets_db_version', '');
			
			//hooks
			add_action( 'plugin_action_links_' . TRAFFIC_SNIPPETS_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
			add_action( 'admin_menu', array( $this, 'register_custom_admin_menu_pages' ), 20 );
			add_action( 'admin_init', array( $this, 'add_custom_admin_fields' ));
			
			add_action( 'wp_dashboard_setup', array( $this, 'manage_dashboard_widgets' ), 20 );
			
			add_action( 'wp_ajax_save_hooks_status', array($this,'save_hooks_status') );
			add_action( 'wp_ajax_save_campaign_status', array($this,'save_campaign_status') );
			
			register_activation_hook( TRAFFIC_SNIPPETS_PLUGIN_FILE, array( $this, 'activation_hook_callback' ) );
		
		}else{

			//FRONTEND			
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );	
			
		}
		

		/*
		if( !function_exists( 'the_field' ) && empty( get_option( 'my-trf-notice-dismissed' ) ) ) {
		  add_action( 'admin_notices', 'my_acf_admin_notice' );
		}

		*/
	
	}
	
	public function check_formula($str){
		$allow = ['0','1','2','3','4','5','6','7','8','9','0','.',
		'(',')','<','=','<=','>=','>','==','||','<=>','or','and','&&','<>',':','??',
		'if','else','{','}',';',
		'/','*','%','-','+','!','^',
		'abs','acos','acosh','asin','asinh','atan','atan2','atanh','ceil','cos','cosh','deg2rad','exp','expm1','floor','fmod','getrandmax','hexdec','hypot','intdiv','is_finite','is_infinite','is_nan','lcg_value','log','log10','log1p','max','min','mt_getrandmax','mt_rand','mt_srand','pi','pow','rad2deg','rand','round','sin','sinh','sqrt','srand','tan','tanh',
		//'base_convert','bindec','decbin','dechex','decoct','octdec',
		'INF','M_E','M_EULER','M_LNPI','M_LN2','M_LN10','M_LOG2E','M_LOG10E','M_PI','M_PI_2','M_PI_4','M_1_PI','M_2_PI','M_SQRTPI','M_2_SQRTPI','M_SQRT1_2','M_SQRT2','M_SQRT3','PHP_ROUND_HALF_UP','NAN','PHP_ROUND_HALF_DOWN','PHP_ROUND_HALF_EVEN','PHP_ROUND_HALF_ODD'];

		$allow = array_map('strtolower',$allow);
		
		usort($allow, function($a, $b) {
			return strlen($a) - strlen($b) ?: strcmp($a, $b);
		});
		
		$str2 = str_replace(" ","",$str);
		$str2 = str_replace("\n","",$str2);
		$str2 = str_replace($allow,"",$str2); //no extra markup remains
		if($str2==''){
			return $str;
		}else{
			return false;
		}
	}

	public function parse_formula_str($formula,$stats,$execute=false) {

		
		$allstats = $stats['all'];
		
		$str = "{$formula}";
		preg_match_all('/\[(.*?)\]/', $str, $match);
		
		//echo "<pre>";var_dump($allstats);echo "</pre>";
		$result_repl = array();
		if($match[1]){
			foreach($match[1] as $m){
				$p = explode(":",$m);
				if(isset($allstats[$p[1]]) && $allstats[$p[1]]){
					
					//save by [label:var]
					$result_repl["[".$m."]"] = $allstats[$p[1]];
					
					//also save just by [var], less precise, can overwrite data, but easier to use in less complex setups
					if(isset($p[1])){
						$result_repl["[".$p[1]."]"] = $allstats[$p[1]];
					}
				}else{
					//init [label:var]
					$result_repl["[".$m."]"] = 0;
					
					//also init just by [var]
					if(isset($p[1])){
						$result_repl["[".$p[1]."]"] = 0;
					}
				}
			}
		}
		//echo "<pre>"; var_dump($result_repl);
		
		$result = str_replace(array_keys($result_repl), array_values($result_repl), $formula);
		
		//---division by zero start---
		$check_divzero = str_replace("/ 0.","/0.",$result); //uniform
		$check_divzero = str_replace("/0.","|div-zero-dot|",$result); //rescue
		$check_divzero = str_replace("/ 0","/0",$result); //uniform
		if(strpos($check_divzero, "/0") !== false){ //detect
			return "";
		}
		
		//restore, BUT ITS NOT NEEDED AS THE $result var is used directly!
		$check_divzero = str_replace("|div-zero-dot|","/0.",$result);
		//--- division by zero end ----
		
		if($execute){
			ob_start();
				if($this->check_formula($result)){
					// //formula is defined in admin and must be able to contain arbitrary math, 
					echo eval("return round({$result},4);"); //sanitization is done via the check, any non math is rejected
				}else{
					echo "invalid formula";
				}
			$ret = ob_get_clean();
			return $ret;
		}else{
			return $result;
		} 
	}

	//ajax
	/*
	This is passed POST params from a js call executed right after the jscheck payload is deployed, containing the results of the js payload return();
	*/
	public function save_track_js_callback() {
		
		global $wpdb;
			
			$campid = (int)$_POST['campid'];
			$snipid = (int)$_POST['snipid'];
			$visitor = sanitize_text_field($_POST['visitor']);
			$js1 = sanitize_key($_POST['js1']); 
			$js2 = sanitize_key($_POST['js2']);
			
			
			$checkIfExists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}traffic_snippets_stats WHERE campaignID = '%d' AND snipID='%d' AND visitor='%s' ",$campid,$snipid,$visitor));
			
			if ($checkIfExists == NULL) {
				
				$wpdb->insert("{$wpdb->prefix}traffic_snippets_stats", array(
					'campaignID' => (int)$campid,
					'snipID' => (int)$snipid,
					'visitor' => $visitor,
					'jscheck1'=>$js1,
					'jscheck2'=>$js2, 
					't' => time() 
				));
			}else{
				$wpdb->update("{$wpdb->prefix}traffic_snippets_stats", array(
					'campaignID' => (int)$campid,
					'snipID' => (int)$snipid,
					'visitor' => $visitor,
					'jscheck1'=>$js1,
					'jscheck2'=>$js2, 
					't' => time() 
				),
				array(
					'campaignID'=>$campid,
					'snipID'=>$snipid,
					'visitor'=>$visitor,
				)
				);			
			}
	}
	
	//ajax
	/*
	public function jscheck(){
		global $wpdb,$table_prefix;

		$snipID = (int)$_POST['snipID'];
		$wp_track_table = $table_prefix . "traffic_snippets_campaign_snips";
	
		$wpdb->update($wp_track_table, 
			array(
				'active' => (sanitize_text_field($_POST['status'])=='true') ? 1 : 0,
			),
			array('id' => $snipID)
		);
	
	}
	*/
	
	//ajax
	public function save_hooks_status(){
		global $wpdb,$table_prefix;
		
		if ( is_admin() ) {
			$snipID = (int)$_POST['snipID'];

			$wpdb->update( "{$table_prefix}traffic_snippets_campaign_snips", 
				array(
					'active' => (sanitize_text_field($_POST['status'])=='true') ? 1 : 0,
				),
				array('id' => $snipID)
			);
		}
	
	}
	
	//ajax
	public function save_campaign_status(){
		global $wpdb,$table_prefix;
		
		if ( is_admin() ) {
			$campID = (int)$_POST['campID'];

			$wpdb->update("{$table_prefix}traffic_snippets_campaigns", 
				array(
					'active' => (sanitize_text_field($_POST['status'])=='true') ? 1 : 0,
				),
				array('id' => $campID)
			);
		}
	}
	 
	public function set_ajaxurl(){
		echo "<script>
		var ajax_url = '".admin_url('admin-ajax.php')."'; 
		</script>";
	}
	
	public function admin_db_ugrade_notice() {
		global $wpdb;
			$installed_ver = get_option('traffic_snippets_db_version');
			?>
			    <div class="error notice">
				<p><strong><?php echo sprintf("%s %s.", __("The database version changed, and the database structure was recreated. Current plugin database version is: ","trafficsnippets"),$installed_ver); ?></strong></p>
				<p>
					<?php 
					//for debugging
					//$wpdb->show_errors();
					//$wpdb->print_error();
					if($wpdb->last_error !== ''){
						$wpdb->print_error();
					}
					?>
				</p>
			</div>
			<?php
		}
	
	public function get_campaign_cache($campaignID){
		global $wpdb, $table_prefix;
		
		$campaignID = (int)$campaignID;
		if($campaignID){
			$cache_time = get_option('ts_cachetime', 5); //minutes
			

			$checkIfExists = $wpdb->get_var($wpdb->prepare("SELECT id FROM  {$table_prefix}traffic_snippets_cachelog WHERE campaignID = '%d' and t > %d ", $campaignID, (time()-(60*$cache_time)) ));
		}
		if ($checkIfExists == NULL) {			
			return false;
		}else{
					$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}traffic_snippets_cache where campaignID='%d' ", $campaignID));
					return $results;
		}
	}	
	
	
	public function save_campaign_cache($campaignID,$newresult){
		global $wpdb,$table_prefix;
		
			if ( is_admin() ) {		
				$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_cache where campaignID='%d' ",$campaignID));
				$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_cachelog  where campaignID='%d' ",$campaignID));
		


				//$allstats[$check][$stats->snipID][$stats->result] = $stats->nr;
				//$allstats['all'][$stats->result] += $stats->nr;

			

			foreach ( $newresult as $check => $snipdat){
					
					if($snipdat){
						
						if($check == 'all'){
							foreach($snipdat as $varkey => $varval){
								$wpdb->insert("{$table_prefix}traffic_snippets_cache", 
										array(
											'varkey' => $varkey,
											'varval' => $varval,
											'campaignID' => $campaignID,
											'checkval' => $check
										)
									);
							}							
						}else{
						
						
							foreach($snipdat as $snipid => $vardata){
								if($vardata){
									foreach($vardata as $varkey => $varval){
										$wpdb->insert("{$table_prefix}traffic_snippets_cache", 
												array(
													'varkey' => $varkey,
													'varval' => $varval,
													'campaignID' => $campaignID,
													'snipID' => $snipid,
													'checkval' => $check
												)
											);
									}
								}
							}
						}
					}
				}
				
				$wpdb->insert("{$table_prefix}traffic_snippets_cachelog",  array('campaignID' => $campaignID, 't' => time()) );				
			}	
	}		
		
	//checks if db version changed, and if it did it erases all data and inserts sample data
	private function check_db_upgrade($new_db_version){
		global $table_prefix, $wpdb;
		
		if ( is_admin() ) {
			$installed_ver = get_option('traffic_snippets_db_version');
			if ($installed_ver != $new_db_version) {
				
				add_action( 'admin_notices',  array($this, 'admin_db_ugrade_notice'));
				
				//$wpdb->query("DROP TABLE IF EXISTS {$table_prefix}traffic_snippets_stats");
				//$wpdb->query("DROP TABLE IF EXISTS  {$table_prefix}traffic_snippets_campaign_snips");
				//$wpdb->query("DROP TABLE IF EXISTS  {$table_prefix}traffic_snippets_campaigns");
				$this->create_plugin_database_table();		
				
				//echo "<hr>TrafficSnippets Database upgraded<hr>"; //die();
				update_option('traffic_snippets_db_version', $new_db_version);
			}
		}
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" title="More info" style="font-weight:700;">%s</a>', 'https://lumnar.tech/', __( 'More info', 'trafficsnippets' ) );

		return $links;
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		//wp_enqueue_script("jquery"); //its already included as dep
		//if needed use:
		//var $j = jQuery.noConflict();
		//$j(function(){

		//maybe there is an other way to use these directly from wp?
		wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
		wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js');
		wp_register_style('maticons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
		
		wp_enqueue_style('maticons');
		wp_enqueue_style('prefix_bootstrap');
		
		//wp_enqueue_style('bootstrap4', TRAFFIC_SNIPPETS_PLUGIN_URL . 'core/includes/assets/js/bootstrap.min.css');		

		wp_enqueue_style( 'trafficsnippets-backend-styles', TRAFFIC_SNIPPETS_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), TRAFFIC_SNIPPETS_VERSION, 'all' );
		wp_enqueue_script( 'trafficsnippets-backend-scripts', TRAFFIC_SNIPPETS_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array('jquery'), TRAFFIC_SNIPPETS_VERSION, false );
		
		wp_localize_script( 'trafficsnippets-backend-scripts', 'trafficsnippets', array(
			'plugin_name'   	=> __( TRAFFIC_SNIPPETS_NAME, 'trafficsnippets' ),
			'pluginsUrl' =>  plugins_url('',dirname(__FILE__)) ,
		));
	}


	public function enqueue_frontend_scripts_and_styles() {
		//wp_enqueue_style( 'trafficsnippets-backend-styles', TRAFFIC_SNIPPETS_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), TRAFFIC_SNIPPETS_VERSION, 'all' );
		wp_enqueue_script( 'trafficsnippets-frontend-scripts', TRAFFIC_SNIPPETS_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array('jquery'), TRAFFIC_SNIPPETS_VERSION, false );
		wp_localize_script( 'trafficsnippets-frontend-scripts', 'trafficsnippets', array(
			'plugin_name'   	=> __( TRAFFIC_SNIPPETS_NAME, 'trafficsnippets' ),
			'pluginsUrl' =>  plugins_url('',dirname(__FILE__)) ,
			'ajax_url' => admin_url( 'admin-ajax.php' ), //not working, why?
		));
	}
	
	
	/**
	 * Add custom menu pages
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	 
	
	public function register_custom_admin_menu_pages(){

		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		//https://developer.wordpress.org/resource/dashicons/#buddicons-groups.
		
		add_menu_page( 'Traffic_Snippets', TRAFFIC_SNIPPETS_NAME, TRAFFIC_SNIPPETS()->settings->get_capability( 'default' ), 'trafficsnippets-menu', array( $this, 'home_page_callback' ), 'dashicons-chart-line', 5 );
		add_submenu_page('trafficsnippets-menu', 'Home', 'Home', 'administrator', 'trafficsnippets-menu');
		add_submenu_page('trafficsnippets-menu', 'Campaigns', 'Campaigns', 'administrator', 'trafficsnippets-campaigns', array( $this, 'campaigns_page_callback' ));
		add_submenu_page('trafficsnippets-menu', 'Stats', 'Statistics', 'administrator', 'trafficsnippets-stats', array( $this, 'stats_page_callback' ));
		add_submenu_page('trafficsnippets-menu', 'Settings', 'Settings', 'administrator', 'trafficsnippets-settings', array( $this, 'settings_page_callback' ));
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		


	}

	
	public function form_action_delstats(){
		global $table_prefix, $wpdb;
		
		if ( is_admin() ) {
			$campid = (int)$_GET['delstats'];
						
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_stats where campaignID='%d' ",$campid));
			
			wp_redirect( admin_url('admin.php?page=trafficsnippets-stats') ); //&noheader=true
			exit;	
			
		}
	}
	
	public function form_action_initformula($campid){		
		global $table_prefix, $wpdb;

		$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
			'campaignID' => $campid,
			'meaning' => 'Percentage of visitors that palce orders',
			'formula' => '([finish]*100)/[start]'
		));		
	}
	
	public function form_action_initend($campid){
		global $table_prefix, $wpdb;

		$wpdb->insert( "{$table_prefix}traffic_snippets_campaign_snips" , array(
			'campaignID' => $campid,
			'info' => 'Entrance point (first visit)',
			'active' => '1',
			'ord' => '10',
			'label' => 'start',
			'phpcheck1_code'=>" echo 'start'; ", //MetaMask is installed!
			'hook' => 'wp_loaded'
		));

		$wpdb->insert( "{$table_prefix}traffic_snippets_campaign_snips", array(
			'campaignID' => $campid,
			'info' => 'Add product to cart',
			'active' => '1',
			'ord' => '100',
			'label' => 'add_to_cart',
			'phpcheck1_code'=>" echo 'yes'; ", //MetaMask is installed!
			'hook' => 'woocommerce_add_to_cart'
		));
		
		$wpdb->insert( "{$table_prefix}traffic_snippets_campaign_snips", array(
			'campaignID' => $campid,
			'info' => 'Exit point (place order)',
			'active' => '1',
			'ord' => '1000',
			'label' => 'finish',
			'phpcheck1_code'=>" echo 'purchase'; ", //MetaMask is installed!
			'hook' => 'woocommerce_thankyou'
		));
		
		//wp_redirect( admin_url('admin.php?page=trafficsnippets-stats') ); //&noheader=true
		//exit;	

			$wpdb->show_errors();
			$wpdb->print_error();		
	}
	
	
	public function form_action_delhook(){
		global $table_prefix, $wpdb;
		
		if ( is_admin() ) {
			$snipid = (int)$_GET['delhook'];
			$campid = (int)$_GET['campaign'];
						
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_campaign_snips where id='%d' ",$snipid));

			$wp_track_table = $table_prefix . "traffic_snippets_stats";
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_campaign_snips where campaignID='%d' ",$campid));
			
			wp_redirect( admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$campid.'')); 
			exit;	
			
		}			
	}
	
	public function form_action_delcamp(){
		global $table_prefix, $wpdb;
		
		if ( is_admin() ) {
			$campid = (int)$_GET['delcamp'];
					
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_campaigns where id='%d' ",$campid));
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_campaign_snips where campaignID='%d' ",$campid));
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_stats where campaignID='%d' ",$campid));
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_formulas where campaignID='%d' ",$campid));
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_cachelog where `what`='campaign_%d' ",$campid));
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_cache where `campaignID`='%d' ",$campid));
			
			wp_redirect( admin_url('admin.php?page=trafficsnippets-campaigns') ); //&noheader=true
			exit;	
				
		}
	}
	
	public function form_action_delformula(){
		global $table_prefix, $wpdb;
		if ( is_admin() ) {
			$formulaid = (int)$_GET['delformula'];
			$campid = (int)$_GET['campaign'];
			
			$wpdb->query($wpdb->prepare("DELETE from {$table_prefix}traffic_snippets_formulas where id='%d' and campaignID='%d' ",$formulaid,$campid));
	
			//$wpdb->show_errors();
			//$wpdb->print_error();	

			wp_redirect( admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$campid) ); //&noheader=true
			exit;	
				
		}
	}	
	/**
	 * Admin pages display
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function home_page_callback(){
		global $table_prefix, $wpdb;
			require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/home_page.php');
	}

	public function stats_page_callback(){
		global $table_prefix, $wpdb;
				
		if(isset($_GET['delstats'])){
			$this->form_action_delstats();
		}
				
		if(isset($_GET['campaign'])){
			require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/stats/stats_view_campaign.php');
		}else{
			require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/stats/stats_page.php');
		}
	}

	public function campaigns_page_callback(){
		global $table_prefix, $wpdb;
		
		if(isset($_GET['delcamp'])){
			$this->form_action_delcamp();
		}
				
		if(isset($_GET['campaign'])){
			if(isset($_GET['delhook'])){
				 $this->form_action_delhook();
			}
			
			if(isset($_GET['delformula'])){
				 $this->form_action_delformula();
			}			
			if(isset($_GET['initend'])){
				$campid_initend = (int)$_GET['initend'];
				$this->form_action_initend($campid_initend);
			}
			if(isset($_GET['initformula'])){
				$campid_initformula = (int)$_GET['initformula'];
				$this->form_action_initformula($campid_initformula);
			}			
			if(isset($_GET['edithook'])){
				if(isset($_GET['dotest'])){
					require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/do_test.php');
				}else{
					require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/campaign_snip_edit.php');
				}
			}elseif(isset($_GET['newhook'])){
				if(isset($_GET['dotest'])){
					require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/do_test.php');
				}else{
					require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/campaign_snip_new.php');
				}
			}elseif(isset($_GET['editformula'])){
				require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/campaign_formula_edit.php');
			}elseif(isset($_GET['newformula'])){
				require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/campaign_formula_new.php');
			}else{	
				require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/snips/campaign_snips.php');
			}
		}elseif(isset($_GET['editcamp'])){
				require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/campaign_edit.php');
		}elseif(isset($_GET['newcamp'])){
				require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/campaign_new.php');
		}else{
			require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/campaigns/campaigns_page.php');
		}
	}

	public function settings_page_callback(){
		global $table_prefix, $wpdb;
			require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/settings_page.php');
	}

	
	/**
	 * Adds all plugin related dashbaord widgets
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function manage_dashboard_widgets() {

		wp_add_dashboard_widget( 'traffic_snippets_widget', __( 'Traffic Snippets', 'trafficsnippets' ), array( $this, 'traffic_snippets_widget_callback' ), null, array() );

	}

	/**
	 * The callback for the "Demo Widget" widget
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	object	$object	Often this is the object that's the focus of the current screen, for example a WP_Post or WP_Comment object.
	 * @param	array	$args	Data that should be set as the $args property of the widget array (which is the fifth parameter passed to the wp_add_dashboard_widget function call)
	 *
	 * @return	void
	 */
	public function traffic_snippets_widget_callback( $object, $args ){
		require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/partials/widget.php');
	}


//----
	public function get_campaigns(){
		global $wpdb;
		$results = $wpdb->get_results(
		"SELECT * FROM {$wpdb->prefix}traffic_snippets_campaigns"
		);
		return $results;
	}

	public function get_general_stats(){
		global $wpdb;
		$results = $wpdb->get_results(
		"SELECT campaignID, count(*) as nr FROM {$wpdb->prefix}traffic_snippets_stats group by campaignID"
		);
		return $results;
	}

	//$allstats[check][snip_ID][var] = nr;
	//$allstats[all][var] = nr;
	public function get_all_stats($campaignID,$limdays=false){
		//,phpcheck2,jscheck1,jscheck2
		global $wpdb;
		
		
		$allstats_cache = $this->get_campaign_cache($campaignID);
		if($allstats_cache){
			$allstats = array();
			foreach($allstats_cache as $ca){
				if($ca->varkey){
					$allstats[$ca->checkval][$ca->snipID][$ca->varkey] =  $ca->varval;
			
					if(!isset($allstats['all'][$ca->varkey])){
						$allstats['all'][$ca->varkey] = 0;
					}
					$allstats['all'][$ca->varkey] = $ca->varval; //+=
				}
			}
			return $allstats;
		}else{
			
			$results = array();
			$results['phpcheck1'] = $wpdb->get_results($wpdb->prepare("SELECT campaignID,snipID,phpcheck1 as result,count(*) as nr FROM {$wpdb->prefix}traffic_snippets_stats where phpcheck1<>'' and phpcheck1 is not null and campaignID='%d' group by campaignID,snipID,phpcheck1",$campaignID));
			$results['phpcheck2'] = $wpdb->get_results($wpdb->prepare("SELECT campaignID,snipID,phpcheck2 as result,count(*) as nr FROM {$wpdb->prefix}traffic_snippets_stats where phpcheck2<>'' and phpcheck2 is not null and campaignID='%d' group by campaignID,snipID,phpcheck2",$campaignID));
			$results['jscheck1'] = $wpdb->get_results($wpdb->prepare("SELECT campaignID,snipID,jscheck1 as result,count(*) as nr FROM {$wpdb->prefix}traffic_snippets_stats where jscheck1<>'' and jscheck1 is not null and campaignID='%d' group by campaignID,snipID,jscheck1",$campaignID));
			$results['jscheck2'] = $wpdb->get_results($wpdb->prepare("SELECT campaignID,snipID,jscheck2 as result,count(*) as nr FROM {$wpdb->prefix}traffic_snippets_stats where jscheck2<>'' and jscheck2 is not null and campaignID='%d' group by campaignID,snipID,jscheck2",$campaignID));

			$allstats =  array(); 
			$allstats['all'] = array();
			foreach($results as $check => $stats_rows){
				if($stats_rows){
					foreach( $stats_rows as  $stats){
						if($stats->result){	
							$allstats[$check][$stats->snipID][$stats->result] = $stats->nr;
							if(!isset($allstats['all'][$stats->result])){
								$allstats['all'][$stats->result] = 0;
							}
							$allstats['all'][$stats->result] = $stats->nr; //+=
						}
					}
				}
			}
			
			$this->save_campaign_cache($campaignID,$allstats);
			
		}
		
		
		//var_dump($results);
		
		return $allstats;
	}
	
	public function get_campaign_snips($campID){
		global $wpdb;
		$campID = (int)$campID;
		$results = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_campaign_snips WHERE campaignID='%d' order by ord asc, `active` desc ",$campID)
		);
		return $results;		
	}	
	
	public function get_campaign_formulas($campID){
		global $wpdb;
		$campID = (int)$campID;
		$results = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_formulas WHERE campaignID='%d'  ",$campID)
		);
		return $results;		
	}


	public function get_single_hook($snipID){
		global $wpdb;
		$results = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_campaign_snips WHERE id='%d' ",$snipID)
		);
		return $results;
		
	}

	public function get_single_campaign($campID){
		global $wpdb;
		$results = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_campaigns WHERE id='%d' ",$campID)
		);
		return $results;
		
	}

	public function get_single_formula($formulaID){
		global $wpdb;
		$results = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_formulas WHERE id='%d' ",$formulaID)
		);
		return $results;
		
	}	
	//Add settings fields on the admin page
		
	public function add_custom_admin_fields() {
			 /**
			* First, we add_settings_section. This is necessary since all future settings must belong to one.
			* Second, add_settings_field
			* Third, register_setting
			*/     
		add_settings_section(
		  // ID used to identify this section and with which to register options
		  'ts_general_section', 
		  // Title to be displayed on the administration page
		  '',  
		  // Callback used to render the description of the section
		   array( $this, 'display_general_info' ),    
		  // Page on which to add this section of options
		  'ts_general_settings'                   
		);

		add_settings_field(
		  'ts_cachetime',
		  'Cache time for the widget data, in minutes.',
		  array( $this, 'ts_render_settings_field' ),
		  'ts_general_settings',
		  'ts_general_section',
		  array (
				  'type'      => 'input',
				  'subtype'   => 'text',
				  'id'    => 'ts_cachetime',
				  'name'      => 'ts_cachetime',
				  'required' => 'true',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option',
				  'desc'     => __( 'Time interval in minutes to recalculate statistics for the widget data', 'trafficsnippets' ),
			  )
		);
		register_setting('ts_general_settings','ts_cachetime');

//------

		add_settings_field(
		  'ts_hideinactive_camps',
		  'Hide inactive campaigns from the dashboard widget',
		  array( $this, 'ts_render_settings_field' ),
		  'ts_general_settings',
		  'ts_general_section',
		  array (
				  'type'      => 'input',
				  'subtype'   => 'checkbox',
				  'id'    => 'ts_hideinactive_camps',
				  'name'      => 'ts_hideinactive_camps',
				  'required' => 'true',
				  'get_options_list' => '',
				  'value_type'=>'normal',
				  'wp_data' => 'option',
				   'desc'     => __( 'Will hide inacttive campaigns from the dashboard widget data', 'trafficsnippets' ),
			  )
		);
		register_setting('ts_general_settings','ts_hideinactive_camps');
		
		//set defaults
		if ( get_option('ts_cachetime') === false ){ // Nothing yet saved
			update_option( 'ts_cachetime', 5 );
		}
		
	}

	public function display_general_info() {
		require_once(TRAFFIC_SNIPPETS_PLUGIN_DIR.'core/admin/partials/general_info.php');
	} 

	public function ts_render_settings_field($args) {
			 /* EXAMPLE INPUT
					   'type'      => 'input',
					   'subtype'   => '',
					   'id'    => $this->plugin_name.'_example_setting',
					   'name'      => $this->plugin_name.'_example_setting',
					   'required' => 'required="required"',
					   'get_option_list' => "",
						 'value_type' = serialized OR normal,
			 'wp_data'=>(option or post_meta),
			 'post_id' =>
			 */     
	   if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox'){
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				# code...
				break;
		}
	}	
	
	public function populate_database_table(){
		global $table_prefix, $wpdb;
		
		if ( is_admin() ) {	

				$wpdb->insert("{$table_prefix}traffic_snippets_campaigns", array(
					'active' => '1',
					'campaign_name' => '[EXAMPLE] Visitors conversion rates',
					'description' => 'How many visitors end up completing orders, update hooks to fit your own setup. THIS ONE IS POPULATED WITH FAKE DATA FOR DEMO',
					'active' => '1',
				));
				
			//------------

				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Visitor entry point',
					'ord' => '11',
					'label' => 'init',
					'active' => '1',
					'phpcheck1_code'=>' echo \'start\'; ',
					'hook' => 'wp_loaded'
				));	
				
				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Visitors having metamask installed',
					'ord' => '101',
					'label' => 'metamask',				
					'active' => '1',
					'jscheck1_code'=>"if (typeof window.ethereum !== 'undefined') { return('yes'); }else{ return('no'); }", //MetaMask is installed!
					'hook' => 'wp_head'
				));	
				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Order placed, all hooks that might indicate the order is placed including for non standard gates that do not trigger woocommerce_thankyou action.',
					'ord' => '1001',
					'label' => 'order',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'complete\'; ",
					'hook' => 'mxl_pay_redirect_before_shuttle, mxl_omcc_after_shuttle_ok, woocommerce_receipt_blockonomics, woocommerce_thankyou, woocommerce_thankyou_wunion, woocommerce_thankyou_blockonomics, woocommerce_checkout_order_processed'
				));	
				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Order complete via custom payment method',
					'ord' => '1001',
					'label' => 'order',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'complete_custom\'; ",
					'hook' => 'woocommerce_thankyou_custompaymentmethod'
				));	
				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Marks visitor for tracking when it views the checkout page',
					'ord' => '101',
					'label' => 'checkout',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'view\'; ",
					'hook' => 'woocommerce_review_order_before_submit, woocommerce_before_checkout_form'
				));	

				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Order complete via Bitcoin',
					'ord' => '1001',
					'label' => 'order',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'complete_bitcoin\'; ",
					'hook' => 'woocommerce_thankyou_blockonomics'
				));	

				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Initiate order via bitcoin, maybe not pay it',
					'ord' => '101',
					'label' => 'order',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'start_bitcoin\'; ",
					'hook' => 'woocommerce_api_wc_gateway_blockonomics'
				));	
				
				$wpdb->insert("{$table_prefix}traffic_snippets_campaign_snips", array(
					'campaignID' => '1',
					'info' => 'Order complete via Western Union',
					'ord' => '1001',
					'label' => 'order',				
					'active' => '1',
					'phpcheck1_code'=>"echo \'complete_western\'; ",
					'hook' => 'woocommerce_thankyou_wunion'
				));	

				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'Percentage of visitors that order',
					'formula' => ' ([order:complete]*100)/[init:start]'
				));
				
				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'How many pay by Custom payment gateway',
					'formula' => ' ([order:complete_custom]*100)/[order:complete]'
				));
				
				
				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'What percentage complete BTC payments',
					'formula' => '([order:complete_bitcoin]*100)/[init:start_bitcoin]'
				));				

				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'How many pay by Bitcoin',
					'formula' => ' ([order:complete_bitcoin]*100)/[order:complete]'
				));		
				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'How many pay by Western',
					'formula' => ' ([order:complete_western]*100)/[order:complete]'
				));				
				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'What percentage reach checkout',
					'formula' => '([checkout:view]*100)/[init:start]'
				));	
				$wpdb->insert("{$table_prefix}traffic_snippets_formulas", array(
					'campaignID' => '1',
					'meaning' => 'What percentage abandon checkout',
					'formula' => '100-(([order:complete]*100)/[checkout:view])'
				));					
			//----------------------
			
				$wpdb->insert("{$table_prefix}traffic_snippets_stats", array(
					'campaignID' => '1',
					'snipID' => '2',
					'visitor' => '123',
					'jscheck1' => 'yes', 
				));	
			
				$wpdb->insert("{$table_prefix}traffic_snippets_stats", array(
					'campaignID' => '1',
					'snipID' => '2',
					'visitor' => '345',
					'jscheck1' => 'no',
				));	

				$wpdb->insert("{$table_prefix}traffic_snippets_stats", array(
					'campaignID' => '1',
					'snipID' => '5',
					'visitor' => '345',
					'phpcheck1' => 'view',
				));
				
				$wpdb->insert("{$table_prefix}traffic_snippets_stats", array(
					'campaignID' => '1',
					'snipID' => '7',
					'visitor' => '678',
					'phpcheck1' => 'start_bitcoin',
				));

				
			if($wpdb->last_error !== ''){
				$wpdb->print_error();
			}
		}
	}
	
	public function create_plugin_database_table(){
		global $table_prefix, $wpdb;

		if ( is_admin() ) {		
			
			$charset_collate = $wpdb->get_charset_collate();
			
			#Check to see if the table exists already, if not, then create it
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "
				CREATE TABLE IF NOT EXISTS `{$table_prefix}traffic_snippets_campaigns` (
				  `id` int NOT NULL AUTO_INCREMENT, 
				  `active` tinyint(1) NOT NULL DEFAULT '0',
				  `campaign_name` varchar(100) NOT NULL,
				  `description` varchar(1000) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  INDEX `active` (`active`)
				)  {$charset_collate};
				";
				//$wpdb->query($sql);
				dbDelta($sql);
			//}
			

			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "CREATE TABLE IF NOT EXISTS  `{$table_prefix}traffic_snippets_formulas` ( 
				`id` INT NOT NULL AUTO_INCREMENT , 
				`campaignID` INT NOT NULL , 
				`meaning` VARCHAR(400) NOT NULL , 
				`formula` VARCHAR(2000) NOT NULL , 
				PRIMARY KEY (`id`), 
				INDEX (`campaignID`)
				) {$charset_collate};
				";
				//$wpdb->query($sql);
				dbDelta($sql); //ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			//}
			
			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "
				CREATE TABLE IF NOT EXISTS `{$table_prefix}traffic_snippets_stats` (
				  `id` int NOT NULL AUTO_INCREMENT, 
				  `campaignID` int NOT NULL,   
				  `snipID` int NOT NULL, 
				  `visitor` varchar(200) DEFAULT NULL, 
				  `jscheck1` varchar(100) DEFAULT NULL, 
				  `jscheck2` varchar(100) DEFAULT NULL,  
				  `phpcheck1` varchar(100) DEFAULT NULL, 
				  `phpcheck2` varchar(100) DEFAULT NULL, 
				  `t` int NOT NULL,
				  PRIMARY KEY (`id`),
				  INDEX `visitor` (`visitor`),
				  INDEX `jscheck1` (`jscheck1`),
				  INDEX `jscheck2` (`jscheck2`),
				  INDEX `phpcheck1` (`phpcheck1`),
				  INDEX `phpcheck2` (`phpcheck2`),
				  INDEX `campaignID` (`campaignID`),
				  INDEX `snipID` (`snipID`),
				  INDEX `t` (`t`)
				) {$charset_collate};
				";
				//$wpdb->query($sql);
				dbDelta($sql);
			//}
			
			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "
				CREATE TABLE IF NOT EXISTS `{$table_prefix}traffic_snippets_campaign_snips` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `active` tinyint(1) NOT NULL DEFAULT '0', 
				  `campaignID` int NOT NULL,  
				  `ord` int DEFAULT 100, 
				  `label` varchar(100) DEFAULT NULL, 
				  `info` varchar(1000) DEFAULT NULL, 
				  `jscheck1_code` varchar(1000) DEFAULT NULL,
				  `jscheck2_code` varchar(1000) DEFAULT NULL,
				  `phpcheck1_code` varchar(1000) DEFAULT NULL,
				  `phpcheck1_test` varchar(1000) DEFAULT NULL,
				  `phpcheck2_code` varchar(1000) DEFAULT NULL,
				  `phpcheck2_test` varchar(1000) DEFAULT NULL,
				  `hook` varchar(500) NOT NULL,
				   PRIMARY KEY (`id`),
				   INDEX `ord` (`ord`),
				   INDEX `active` (`active`),
				   INDEX `campaignID` (`campaignID`)
				) {$charset_collate};
				";
				//$wpdb->query($sql);
				dbDelta($sql);
			//}

			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "
					CREATE TABLE IF NOT EXISTS  `{$table_prefix}traffic_snippets_cachelog` (
					  `id` int NOT NULL AUTO_INCREMENT,
					  `campaignID` INT UNSIGNED NOT NULL,
					  `t` int NOT NULL,
					  PRIMARY KEY (`id`),
					  INDEX `campaignID` (`campaignID`)
					)  {$charset_collate};
				";

				dbDelta($sql);
				
			//if($wpdb->get_var( "show tables like '{$wp_track_table}'" ) != $wp_track_table){
				$sql = "
					CREATE TABLE IF NOT EXISTS  `{$table_prefix}traffic_snippets_cache` ( 
						`id` int NOT NULL AUTO_INCREMENT,
						 `varkey` VARCHAR(150) NOT NULL , 
						 `varval` VARCHAR(300) NULL , 
						 `campaignID` INT NOT NULL , 
						  `snipID` INT NULL, 
						  `checkval` VARCHAR(100) NULL , 
						  `limhours` INT NULL , 
						 PRIMARY KEY (`id`), 
						 INDEX `campaignID` (`campaignID`), 
						 INDEX `varkey` (`varkey`),
						 INDEX `snipID` (`snipID`),
						 INDEX `checkval` (`checkval`),
						 INDEX `limhours` (`limhours`)
						 
						 )  {$charset_collate}; 
				";
				//$wpdb->query($sql);
				dbDelta($sql);
				
			//for debugging
			
 
			if($wpdb->last_error !== ''){ 
				$wpdb->show_errors();
				$wpdb->print_error();
				add_action( 'admin_notices',  array($this, 'get_mysql_error'));		
				die();
			}
						
				
		}
	}		
	
	
	function get_mysql_error(){
		global $wpdb;
		if($wpdb->last_error !== ''){
			echo "Mysql Errors:";
			$wpdb->show_errors();
			$wpdb->print_error();
		}
		
	}
	/**
	 * ####################
	 * ### Activation/Deactivation hooks
	 * ####################
	 */
	 
	/*
	 * This function is called on activation of the plugin
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	
	public function activation_hook_callback(){
		$this->create_plugin_database_table();
		$this->populate_database_table();
	}

	public function uninstall_hook_callback(){
		$this->delete_plugin_database_table();
	}
	
	public function delete_plugin_database_table(){
		global $table_prefix, $wpdb;
		
		$wpdb->query("DROP TABLE 
		`{$table_prefix}traffic_snippets_cache`, 
		`{$table_prefix}traffic_snippets_cachelog`, 
		`{$table_prefix}traffic_snippets_campaigns`, 
		`{$table_prefix}traffic_snippets_campaign_snips`,
		`{$table_prefix}traffic_snippets_formulas`,
		`{$table_prefix}traffic_snippets_stats`
		;");
		
		if($wpdb->last_error !== ''){ 
			//$wpdb->show_errors();
			$wpdb->print_error();
			add_action( 'admin_notices',  array($this, 'get_mysql_error'));		
		}
			
	}
	
}
