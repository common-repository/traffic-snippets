<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 
 
$campaignID = (int)$_GET['editcamp'];

if(isset($_POST['action']) && $_POST['action']=='edit_campaign'){

	//not working here
	//add_action( 'admin_notices',  array($this, 'admin_hook_saved_notice'));

	$wp_track_table = $table_prefix . "traffic_snippets_campaigns";
	
	$wpdb->update($wp_track_table, 
		array(
			'campaign_name' => sanitize_text_field($_POST['campaign_name']),
			'description' => sanitize_textarea_field($_POST['description']),
		),
		array(
		'id' => $campaignID
		)
	);
	//$lastid = $wpdb->insert_id;

	//$wpdb->show_errors();
	//$wpdb->print_error();
					
	wp_redirect(admin_url('admin.php?page=trafficsnippets-campaigns'));
	exit;
}

$campaign = $this->get_single_campaign($campaignID);

?>
					<?php 
					//var_dump($_GET);
					//for debugging
					?>


		        <h2><?php echo __("Edit Campaign","trafficsnippets"); ?></h2>
				<br>
				<?php echo __("A campaign can group multiple hooks to collect very specific data to analyze user activity. Create a new campaign for each measuring target, for exmaple tracking how many of the users that visited page X performed Y action.","trafficsnippets"); ?><br><br>
				<br>

				

				
				<br><br>
				 <form method="POST" action="admin.php?page=trafficsnippets-campaigns&editcamp=<?php echo (int)$campaignID; ?>&noheader=true">  
				<div class="general_textarea_wrap">
				<?php echo __("Name:","trafficsnippets"); ?><br><input type="text"  class="general_input_info" name="campaign_name" value="<?php echo esc_html($campaign->campaign_name); ?>">
				<br><br>
				<?php echo __("Description:","trafficsnippets"); ?><br><textarea name="description" class="general_textarea_info"><?php echo esc_html($campaign->description); ?></textarea>
				</div>		 
						 
				 <br><br>
				 <input type="hidden" name="action" value="edit_campaign">
				 <input type="submit" value="<?php echo __("Save Campaign","trafficsnippets"); ?>" class="button button-primary">
		        </form> 


<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>