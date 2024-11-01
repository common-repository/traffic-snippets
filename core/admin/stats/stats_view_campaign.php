<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 




$campaignID = (int)$_REQUEST['campaign'];
$campaign = $this->get_single_campaign($campaignID);
$hooks = $this->get_campaign_snips($campaignID);

$hooks_info = array();
foreach($hooks as $hook){ 
	$hooks_info[$hook->id] = $hook->info;
}

$allstats = $this->get_all_stats($campaignID);

//echo "<pre>"; var_dump($allstats);
?>

<h1><?php echo $campaign->campaign_name; ?></h1>
<div class="wp-die-message"><?php echo $campaign->description; ?></div>
<hr>		
        
<?php 

foreach($allstats as $check => $st){ 

		if($check != 'all'){
			?>
			<h1 class="wp-heading-inline" ><?php echo $check; ?></h1>  <!-- a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo $campaignID; ?>&newhook=1">Add new</a -->   

			<?php foreach($st as $hid => $results){ 
				if(isset($hooks_info[$hid])){
					if($results){
					?>
					<h3><?php echo $hooks_info[$hid]; ?></h3>
					<table class="wp-list-table widefat fixed striped table-view-list posts">
					<thead>
						<tr>
							<th>Result</th>
							<th>Visitors</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						
							foreach($results as $name => $nr){ ?>
						<tr>
							<td><?php echo  $name ; ?></td>
							<td><?php echo  $nr ; ?></td>
						</tr>
						<?php }
							?>
					</tbody>
					</table>

					<?php
					}	 
				} 
			}
		}		
} 

?>

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>