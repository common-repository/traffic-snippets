<?php 

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php";


$campaignID = (int)$_GET['campaign'];

 
if(isset($_POST['action']) && $_POST['action']=='new_hook'){

	//not working here
	//add_action( 'admin_notices',  array($this, 'admin_hook_saved_notice'));

	$wp_track_table = $table_prefix . "traffic_snippets_campaign_snips";
	
	$wpdb->insert($wp_track_table, 
		array(
			'campaignID' => $campaignID,
			'phpcheck1_code' => $_POST['phpcheck1_code'], //must not be sanitized
			'phpcheck2_code' => $_POST['phpcheck2_code'], //must not be sanitized
			'jscheck1_code' => $_POST['jscheck1_code'], //must not be sanitized
			'jscheck2_code' => $_POST['jscheck2_code'], //must not be sanitized
			'label' => sanitize_text_field($_POST['label']),		
			'info' => sanitize_textarea_field($_POST['info']),		
			'hook' => sanitize_text_field($_POST['hook']),		
			'ord' => (int)$_POST['ord'],		
			'active' => '0'
		)
	);
	
	//$wpdb->show_errors();
	//$wpdb->print_error();
	//exit;
	$lastid = $wpdb->insert_id;
	wp_redirect(admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$campaignID));
	exit;
}



?>
					<?php 
					//var_dump($_GET);
					//for debugging
					//$wpdb->show_errors();
					//$wpdb->print_error();
					?>


		        <h2><?php echo __("New Tracking Snippet"); ?></h2>
				<br>
				<?php echo __("Hooks payload must be valid code, and must output something in the specified parameters. Read the info for details."); ?><br><br>
				<br>

				

				 <form method="POST" action="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$campaignID; ?>&newhook=<?php echo (int)$snipID; ?>&noheaders=true">  
			
			<div class="general_textarea_wrap">
				<?php echo __("Formula Label:"); ?><br><input type="text"  class="general_input_info" name="label" value="" maxlength="50"><br>
				<?php echo __("(no spaces no special chars, can be used inside formulas like variable #label_name# )"); ?>
				<br><br>
				<?php echo __("Info:"); ?><br><textarea name="info" class="general_textarea_info"></textarea>
				<br><br>
				<?php echo __("Wp hook:"); ?><br><input type="text"  class="general_input_info" name="hook" value="" maxlength="250">
				<?php echo __("Separated by comma, use visible hooks when using JS payload"); ?>
				<br><input type="hidden"  class="general_input_info" name="ord" value="100" >
				
				</div>
				
				<br><br>	
				
				<div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title">Inject PHP check</div><br>
							<textarea name="phpcheck1_code" class="payload_textarea"></textarea>
						</div>
					</div>
					<div class="col-4">
						<?php echo __("A single output, only a-z_0-9<br>Must contain valid PHP code, without leading &lt;?php. The output of this field will be stored for the statistics, not shown on the page, so make sure it will be something relevant for statistics like 1, 0, or a string usable as a key."); ?>
					</div>
				</div>	
				
				<!-- secondary php field not used yet but supported, will be released in future versions -->
				<!-- div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title">Inject PHP check 2</div><br>
							<textarea name="phpcheck2_code" class="payload_textarea"></textarea>
						</div>
					</div>
					<div class="col-4">
						Use the second php code payload only for advanced statistics. For most usual tracking needs this secondary field is not needed.
					</div>
				</div -->	
				
				<div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title_js">Inject Javascript check</div><br>
							<textarea name="jscheck1_code" class="payload_textarea_js"></textarea>
						 </div>
					</div>
					<div class="col-4">
						<?php echo __("Must contain valid Javascript, and use return(..); , whatever it is returned by the javascript return() , will be stored as string in the db"); ?>
					</div>
				</div>
				
				<!-- removed because injected js always runs in footer, maybe fix and release in next version -->
				<!-- div class="row">
					<div class="col-8">
						<div class="payload_textarea_wrap">
							<div class="payload_title_js">Inject Javascript check 2</div><br>
							<textarea name="jscheck2_code" class="payload_textarea_js"></textarea>
						 </div>
					</div>
					<div class="col-4">
						<?php echo __("Same like the first js field, but this one runs in footer after the content is loaded"); ?>
					</div>
				</div -->						 
						 
				 <br><br>
				 <input type="hidden" name="action" value="new_hook">
				 <input type="submit" value="Save tracking snippet" class="button button-primary">
		        </form> 




<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>