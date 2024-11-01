<?php 

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php";
 
  
 //uncomment these to see errors
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//@define( 'WP_DEBUG', true );
//@define( 'WP_DEBUG_DISPLAY', true );

$wp_track_table = $table_prefix . "traffic_snippets_campaign_snips";

$snipID = (int)$_GET['edithook'];

$is_saved = false;

$wpdb->update($wp_track_table, 
	array(
		'phpcheck1_test' => '', //reset
		'phpcheck2_test' => '', //reset
	),
	array('id' => $snipID)
);
	
if(isset($_POST['action']) && $_POST['action']=='save_hook'){

	//not working here
	//add_action( 'admin_notices',  array($this, 'admin_hook_saved_notice'));

	//$sniprow = $this->get_single_hook($snipID);
	
	
	
	$ord = 101;
	if(in_array($_POST['label'],array('start','begin','init','first'))){
		$ord = 11;
	}
	if(in_array($_POST['label'],array('finish','end','last'))){
		$ord = 1001;
	}
	
	$wpdb->update($wp_track_table, 
		array(
			'phpcheck1_test' => $_POST['phpcheck1_code'], //save as is, for error check, must not be sanitized
			'phpcheck2_test' => $_POST['phpcheck2_code'], //save as is, for error check, must not be sanitized
			'jscheck1_code' => $_POST['jscheck1_code'], //must not be sanitized
			'jscheck2_code' => $_POST['jscheck2_code'], //must not be sanitized	
			'label' => sanitize_text_field($_POST['label']),		
			'ord' => (int)$ord,		
			'info' => sanitize_textarea_field($_POST['info']),		
			'hook' => sanitize_text_field($_POST['hook']),		
		),
		array('id' => $snipID)
	);
	
	wp_redirect( admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.(int)$_GET['campaign'].'&edithook='.(int)$_GET['edithook'].'&dotest=yes'));
	die();
}

$hook = $this->get_single_hook($snipID);
//var_dump($hook);
//var_dump($_GET);
//for debugging
//$wpdb->show_errors();
//$wpdb->print_error();
?>

		        <h2><?php echo __("Edit Tracking Snippet"); ?></h2>
				<br>
				<?php echo __("Changing the hook payload could make the statistics collected so far irrelevant. Please consider clearing the collected stats too.<br>
				If anything goes wrong just come back to this page and edit the records."); ?><br>
				<br>


				 <form method="POST" action="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$_GET['campaign']; ?>&edithook=<?php echo $snipID; ?>&noheaders=true">  
			
			<div class="general_textarea_wrap">
				<?php echo __("Formula Label:"); ?><br><input type="text"  class="general_input_info" name="label" value="<?php echo esc_attr(stripslashes($hook->label)); ?>" maxlength="50"><br>
				<?php echo __("(no spaces no special chars, can be used inside formulas like variable #label_name#. Use 'start' and 'finish' for these special labels)"); ?>
				<br><br>
				<?php echo __("Info:"); ?><br><textarea name="info" class="general_textarea_info"><?php echo esc_html(stripslashes($hook->info)); ?></textarea>
				<br><br>
				<?php echo __("Wp hooks that trigger this payload:"); ?><br>
				<textarea name="hook" class="general_textarea_info"><?php echo esc_html(stripslashes($hook->hook)); ?></textarea>
				<br><?php echo __("Separated by comma, use visible hooks when using JS payload"); ?>
				</div>
				
				<br><br>	
				
				<div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title">php payload</div><br>
							<textarea name="phpcheck1_code" class="payload_textarea"><?php echo esc_html(stripslashes($hook->phpcheck1_code)); ?></textarea>
						</div>
					</div>
					<div class="col-4">
						<?php echo __("A single output, only a-z_0-9<br>Must contain valid PHP code, without leading &lt;?php. The output of this field will be stored for the statistics, not shown on the page, so make sure it will be something relevant for statistics like 1, 0, or a string usable as a key."); ?>
					</div>
				</div>	
				
				<!-- secondary php field not used yet but supported, will be released in future versions -->
				<!--div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title">php check 2</div><br>
							<textarea name="phpcheck2_code" class="payload_textarea"><?php echo esc_html(stripslashes($hook->phpcheck2_code)); ?></textarea>
						</div>
					</div>
					<div class="col-4">
						Use the second php code payload only for advanced statistics. For most usual tracking needs this secondary field is not needed.
					</div>
				</div-->	
				
				<div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap"> 
							<div class="payload_title_js">Javascript payload</div><br>
							<textarea name="jscheck1_code" class="payload_textarea_js"><?php echo esc_html(stripslashes($hook->jscheck1_code)); ?></textarea>
						 </div>
					</div>
					<div class="col-4">
						<?php echo __("Must contain valid Javascript, and use return(..); , whatever it is returned by the javascript return() , will be stored as string in the db and available as a variable name in formulas"); ?>
					</div>
				</div>
				
				
				<!-- removed because injected js always runs in footer, maybe if i find a way to run it in header i release this field back -->
				<!-- div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title_js">Javascript check (footer)</div><br>
							<textarea name="jscheck2_code" class="payload_textarea_js"><?php echo esc_html(stripslashes($hook->jscheck2_code)); ?></textarea>
						 </div>
					</div>
					<div class="col-4">
						<?php echo __("Same like the first js field, but this one runs in footer after the content is loaded"); ?>
					</div>
				</div -->						 
						 
				 <br><br>
				 <input type="hidden" name="action" value="save_hook">
				 <input type="submit" value="Save hook" class="button button-primary">
		        </form> 



<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>
