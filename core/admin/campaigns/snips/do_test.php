<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 
 
//uncomment these to debug
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//@define( 'WP_DEBUG', true ); 
//@define( 'WP_DEBUG_DISPLAY', true );
?>


<h2><?php echo __("If you have any errors in your code, they will show up here. If so, <b>press back</b> on your browser and fix the code."); ?></h2>
<br>

<?php
$snipID = (int)$_GET['edithook'];
$campaignID =  (int)$_GET['campaign'];

$sniprow = $this->get_single_hook($snipID);

$checks = array();



echo "<br>Testing check1: <hr>";
if($sniprow->phpcheck1_test){
	$checks['phpcheck1'] = stripslashes($sniprow->phpcheck1_test);
	ob_start();
		eval($checks['phpcheck1']);
	$str = ob_get_clean();
}

echo "<br>Testing check2: <hr>";
if($sniprow->phpcheck2_test){
	$checks['phpcheck2'] = stripslashes($sniprow->phpcheck2_test);
	ob_start();
		eval($checks['phpcheck2']);
	$str = ob_get_clean();
}

//var_dump($checks); 
if(true || $checks){ //run all
	
	//here if its bad php it will kill the page, preventing the save next
	//$results = Traffic_Snippets::deploy->perform_phpchecks($campaignID,$snipID,$checks);

	//we don't actually need the results, if page fails, it just doesnt update the actual records
	echo implode("<br>",$results);

	$wp_track_table = $table_prefix . "traffic_snippets_campaign_snips";		
	$wpdb->update($wp_track_table, 
		array(
			'phpcheck1_code' => stripslashes($sniprow->phpcheck1_test), //,must not be snanitized
			'phpcheck1_test' => "",
			'phpcheck2_code' => stripslashes($sniprow->phpcheck2_test),//must not be sanitized
			'phpcheck2_test' => "",
		),
		array('id' => $snipID,'campaignID'=>$campaignID)
	);
}
	
echo "<br><hr>Tests passed, updating the real records now..<hr><br>";		

//die("all good!");	
wp_redirect( admin_url('admin.php?page=trafficsnippets-campaigns&campaign='.$campaignID.'')); 

?>

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>