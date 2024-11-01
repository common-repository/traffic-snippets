<?php
// Exit if accessed directly. 
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Class Traffic_Snippets_Deploy 
 *
 * This class contains deployment functions used for injecting the tracking code into the frontend
 *
 * @package		TRAFFIC_SNIPPETS
 * @subpackage	Classes/Traffic_Snippets_Deploy
 * @author		Max Lumnar
 * @since		1.0.0
 */
 
class Traffic_Snippets_Deploy{

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * HELPER COMMENT START 
	 *
	 * Within this class, you can define functions that you are 
	 * going to use in different hook places to inject tracking code and collect data. 
	 * 
	 * Down below you will find a demo function called output_info()
	 * To access this function from any other class, you can call it as followed:
	 * 
	 * TRAFFIC_SNIPPETS()->deploy->output_text( 'my text' );
	 * 
	 */
	 
	/**
	 * For Testing, not used
	 *
	 * @param	string	$text	The text to output
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	 public function output_info( $text = '' ){
		 echo esc_html($text);
		// echo esc_html("@@@@@@@@@@@visitor signature: ".$this->visitor());
	 }

	 /**
	  * HELPER COMMENT END
	  */


	function __construct(){

		$this->inject_hooks(); //deploy the payload
		
	}
		
		
	/**
	 * running arbitrary named hooks is possible by interpreting the hook name at action time here
	 * processing the fake functions to extrac tdata
	 */		

	public function __call($func, $params){
			//echo "\n<br>@@@@@@@@@@@@@@@@@@@@@@@@@@NONAMEFUNC2::: ".$func;
			if(substr( $func, 0, strlen("traffic_snippets_payload_") ) === "traffic_snippets_payload_"){
				//echo "@1";
				$parts = str_replace("traffic_snippets_payload_","",$func);
				$parts = explode("_",$parts);
				$campaignID = $parts[0];
				$snipID = $parts[1];
				//echo "\n<br>Saving track1:::: ".$campaignID."_".$snipID;
				$this->save_track($campaignID, $snipID);
			}
	}
		
	/*//works, but its not used
		public static function __callStatic($func, $params){
			echo "\n<br>STATICNONAMEFUNC2::: ".$func;
			if(substr( $func, 0, 15 ) === "Traffic_Snippets_Deploy"){
				$parts = str_replace("Traffic_Snippets_Deploy::","",$func);
				$parts = str_replace("traffic_snippets_payload_","",$parts);
				$parts = explode("_",$parts);
				$campaignID = $parts[0];
				$snipID = $parts[1];
				//echo "\n<br>##############saving track2: ".$campaignID."_".$snipID;
				$this->save_track($campaignID, $snipID);
			}
		}
			
		*/
	
	//general use
	public function add_hooks(){
		if ( is_admin() ) {
			
		}else{
			
		}
	}
	

	//go through all defined campaigns and their hooks and deploy the payloads
	public function inject_hooks(){
		global $wpdb;

		$tblcheck = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}traffic_snippets_campaign_snips' ");
		
		//echo "##############".$tblcheck."=="."{$wpdb->prefix}traffic_snippets_campaign_snips";
		
		if( 
		//only if tables exist already. They don't exist in the beginning of the activation
		$tblcheck  == "{$wpdb->prefix}traffic_snippets_campaign_snips"
		
		//one check is enough
		//and $wpdb->get_var("SHOW TABLES LIKE '{$table_name_hooks}'") == $table_name_hooks
		) {

			//echo "@@@Applying hooks";
			//ok $campaigns = $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s  where `active` = '1' ",$table_name_camp));	
			$qry = "SELECT * FROM {$wpdb->prefix}traffic_snippets_campaigns  where `active` = '1' ";
			//echo $qry;
			$campaigns = $wpdb->get_results($qry);	
			
			
			//$wpdb->show_errors();
			//$wpdb->print_error();
			
			foreach($campaigns as $camp){
				
				$hooks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_campaign_snips  where `active` = '1' and campaignID='%d' ",$camp->id));	
				foreach($hooks as $hook){
					
					$campaignID = $camp->id;		
					$snipID = $hook->id;		
					
					//PHP hooks
					//injecting fake funtion names to transport data
					$hook_arr = explode(",",$hook->hook);
					foreach($hook_arr as $hk){
						//the load place of the inline js is determined by the hook here, but changing the hook affects when this gets triggered. Instead, use the correct hook for the snippet
						add_action(trim($hk), array( $this,'traffic_snippets_payload_'.$campaignID.'_'.$snipID)); //Traffic_Snippets_Deploy::
					}
					//	echo "\n<br>SETTINGHOOKS::: ".$hook->hook."====".'traffic_snippets_payload_'.$campaignID.'_'.$snipID;
					//JS hooks
					//if its a jshook, the above function returns wrapped js instead, that will send its data via ajax callback
		
				}
			}
		}
	}
	
	//do the eval on the php hook payloads and collect the output
	public function perform_phpchecks($campaignID,$snipID,$checks){
		$results=array();
		foreach($checks as $k => $check){
			if(trim($check)){
				//catch the result in an output buffer
				//echo "\nPERFORM PHPCHECKS::: ".$check;
				ob_start();
					eval($check);
				$str = ob_get_clean();
			}else{
				$str = "";
			}
			$results[$k] = $str;
			
		}
		//var_dump($results);
		return $results;
	}
	
	/**
	 * Return anonymous visitor identifier
	 * This identifier is used to determine if its the same visitor experience we should track or not
	 * All data will be grouped by this, one hook will be saved only once per visitor
	 *
	 * @param	string	$text	The text to output
	 * @since	1.0.0
	 *
	 * @return	unique identifier of the visitor
	 */
	 public function visitor(){
		$visit_sig = sanitize_text_field($_SERVER['REMOTE_ADDR']); // .rand(1,999)
		
		// different within same session when ajax calls it.'|||'.$_SERVER['HTTP_USER_AGENT']; 
		//has collisions
		//$visit_sig = crc32($visit_sig); //remove this to have clear visitor details for debugging

		return $visit_sig;
	 }


	//--- deloyment funcs
	
	//prepares the js code of the check to be executed where the hook is, and call back via ajax to save its data
	public function wrap_js_check($checknr,$visitor,$snipID,$campaignID,$check_code){
		
		$handle  = $checknr.'_'.$snipID.'_'.$campaignID;
		
		$ret = "";
		$ret .= " 
			//handle: {$handle} ";
		$ret .= "
			if(typeof do_jscheck{$checknr}_{$handle} !== 'function'){
				function do_jscheck{$checknr}_{$handle}(){
					{$check_code}
				}
				var campid = '{$campaignID}';
				var snipid = '{$snipID}';
				var visitor = '{$visitor}';
				var checknr = '{$checknr}';
				
				var jscheck{$checknr} = do_jscheck{$checknr}_{$handle}();
				//alert(jscheck{$checknr}); 
			";
		
		if($checknr == 1){		
			$ret .=" 	
				save_track(campid,snipid,visitor,jscheck{$checknr},null); ";
		}elseif($checknr == 2){		
			$ret .="
				save_track(campid,snipid,visitor,null,jscheck{$checknr}); ";
		}
		$ret .= "
			}
			";
		
		
		
		/* It does not work, in my tests both ran in footer, maybe because the initiall call for this happens already in the footer. I am removing field 2 because of this, but keep the code there maybe for later use*/
		
		/*
		if($checknr==1){
			//run in header
			wp_register_script($handle, null , array("jquery") , null, false); //in header //,  array("jquery")
			wp_enqueue_script($handle); //, null, array('jquery'), null, false
			wp_add_inline_script($handle,$ret,'before');
		}else{ 
			//run in footer
			wp_register_script($handle, null, array("jquery") , '', true); //in footer
			wp_enqueue_script($handle); //, null, array('jquery'), '', true
			wp_add_inline_script($handle,$ret,'after');
		}
		*/

			wp_register_script($handle, null, array("jquery") , '', true); //in footer
			wp_enqueue_script($handle); //, null, array('jquery'), '', true
			wp_add_inline_script($handle,$ret,'after');
			
	}
	

	
	//if its php payload, executes it, 
	//if its js payload, prints it out in an ajax wrapper so it can send the result back via ajax to save_track_js_callback (in main class)
	public function save_track($campaignID, $snipID) { // static 
		global $wpdb;
		$snipiID = (int)$snipID; 
		$campaignID = (int)$campaignID;
		
		
		$visitor = sanitize_text_field($this->visitor());
		
		if($_SERVER['REMOTE_ADDR'] && $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']){
				$checkIfExists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}traffic_snippets_stats WHERE campaignID = '%d' AND snipID='%d' AND visitor='%s' ",$campaignID,$snipID,$visitor));

				//die();
			
			if (!$checkIfExists) {
				//echo "\n<br>SAVETRACK2 NOT EXIST::: ".$wpdb->prepare("SELECT id FROM {$wpdb->prefix}traffic_snippets_stats WHERE campaignID = '%d' AND snipID='%d' AND visitor='%s' ",$campaignID,$snipID,$visitor);
				//echo "\n<pre>"; echo var_dump($checkIfExists);
				
				$hook = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}traffic_snippets_campaign_snips where `active` = '1' and campaignID='%d' and id='%d'",$campaignID,$snipID));	
				
				if($hook){
					//echo "\n<br>EXISTS H::: "."SELECT * FROM {$wpdb->prefix}traffic_snippets_campaign_snips  where `active` = '1' and campaignID='{$campaignID}' and id='{$snipID}'"."<hr>";
					//process php checks
					if(trim($hook->phpcheck1_code) || trim($hook->phpcheck2_code)){
						$checks = array();
						$checks['phpcheck1'] = stripslashes($hook->phpcheck1_code);
						$checks['phpcheck2'] = stripslashes($hook->phpcheck2_code);
						
						//js injection not here
						//$checks['jscheck1'] = $hook->jscheck1_code;
						//$checks['jscheck2'] = $hook->jscheck2_code;
						
						//eval and parse the results
						$results = $this->perform_phpchecks($campaignID,$snipID,$checks);			
						
						
						//echo "<pre>";
						//var_dump($checks);
						//var_dump($results);
						//var_dump($results);
						//echo "\n<br>INJECTING::: camp:{$campaignID} hook:{$snipID} ".$hook->hook;				
						
						$wpdb->insert("{$wpdb->prefix}traffic_snippets_stats", array(
							'campaignID' => (int)$campaignID,
							'snipID' => (int)$snipID,
							'visitor' => sanitize_text_field($visitor),
							'phpcheck1'=>sanitize_key($results['phpcheck1']), 
							'phpcheck2'=>sanitize_key($results['phpcheck2']), 
							't' => time() 
						));
					
					}
					
					//JS PAYLOADS 
					
					//wrap and deply js code, that will trigger the save itself after it runs, passing the results via ajax POST
					if(trim($hook->jscheck1_code)){
						$this->wrap_js_check(1,$visitor,$snipID,$campaignID,stripslashes($hook->jscheck1_code));
					}
					
					if(trim($hook->jscheck2_code)){
						$this->wrap_js_check(2,$visitor,$snipID,$campaignID,stripslashes($hook->jscheck2_code));
					}
					
					
					
				}
				
				/*
				if($wpdb->last_error !== ''){
					$wpdb->print_error();
				}
				$wpdb->show_errors();
				$wpdb->print_error();
				*/
			}else{
				//echo "\nSAVETRACK2 EXIST::::<pre>"; echo var_dump($checkIfExists);
			}
		}else{
			//call from same server, do not track
		}
	}
}
