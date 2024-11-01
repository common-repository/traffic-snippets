<?php 

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 

?>

 

<div class="wrap">
	<h1>Traffic Snippets - General settings</h1> 
		<!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
		<?php settings_errors(); ?>  
		<form method="POST" action="options.php">  
		<?php 
			settings_fields( 'ts_general_settings' );
			do_settings_sections( 'ts_general_settings' ); 
		?>             
		<?php submit_button(); ?>  
		</form> 
</div>

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>