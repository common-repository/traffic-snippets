<?php

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 

$campaignID = (int)$_GET['campaign'];
 

if(isset($_POST['action']) && $_POST['action']=='new_formula'){

	//not working here
	//add_action( 'admin_notices',  array($this, 'admin_hook_saved_notice'));

	$wp_track_table = $table_prefix . "traffic_snippets_formulas";
	
	$wpdb->insert($wp_track_table,  
		array(
			'campaignID' => $campaignID,
			'meaning' => sanitize_textarea_field($_POST['meaning']),
			'formula' => $_POST['formula'], //must not be sanitized
			)
	);
	
	//$wpdb->show_errors();
	//$wpdb->print_error();
	//die();
	$lastid = $wpdb->insert_id;
	wp_redirect(admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$campaignID));
	exit;
}



?>


		        <h2><?php echo __("New Formula", 'trafficsnippets'); ?></h2>
				<br>
				<?php echo __("Formulas can be used to aggregate the collected data into a final result. For example, to get the percentage of people that reach from start to finish, you would set a formula such as this: ([finish]*100)/[start] ", 'trafficsnippets'); ?><br><br>
				<br>
				

				 <form method="POST" action="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo $campaignID; ?>&newformula=1&noheaders=true">  
			
			<div class="general_textarea_wrap">
				<?php echo __("Meaning:", 'trafficsnippets'); ?><br><input type="text"  class="general_input_info" name="meaning" value="" ><br>
			<br><br>
				<?php echo __("Formula:", 'trafficsnippets'); ?><br><textarea name="formula" class="general_textarea_info"></textarea>
				
			
				 <br><br>
				 <input type="hidden" name="action" value="new_formula">
				 <input type="submit" value="Save Formula" class="button button-primary">
		        </form> 




<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>