<?php

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php";

if(isset($_POST['action']) && $_POST['action']=='new_campaign'){

	//not working here
	//add_action( 'admin_notices',  array($this, 'admin_hook_saved_notice'));

	$wp_track_table = $table_prefix . "traffic_snippets_campaigns";
	
	$wpdb->insert($wp_track_table, 
		array(
			'campaign_name' => sanitize_text_field($_POST['campaign_name']),
			'description' => sanitize_tearea_field($_POST['description']),
			'active' => '0',
		)
	);
	$lastid = $wpdb->insert_id;

	//$wpdb->show_errors();
	//$wpdb->print_error();
					
	wp_redirect(admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$lastid));
	exit;
}


?>
					<?php 
					//var_dump($_GET);
					//for debugging
					?>


		        <h2>New Tracking Campaign</h2>
				<br>
				A campaign can group multiple hooks to collect very specific data to analyze user activity. Create a new campaign for each measuring target, for exmaple tracking how many of the users that visited page X performed Y action.<br><br>
				<br>

				

				
				<br><br>
				 <form method="POST" action="admin.php?page=trafficsnippets-campaigns&newcamp=1&noheader=true">  
				<div class="general_textarea_wrap">
				Name:<br><input type="text"  class="general_input_info" name="campaign_name">
				<br><br>
				Description:<br><textarea name="description" class="general_textarea_info"></textarea>
				</div>		 
						 
				 <br><br>
				 <input type="hidden" name="action" value="new_campaign">
				 <input type="submit" value="Create tracking campaign" class="button button-primary">
		        </form> 


<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>