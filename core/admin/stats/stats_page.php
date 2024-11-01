<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 


$general_stats_rec = $this->get_general_stats();
$general_stats = array();
foreach($general_stats_rec as $k => $v){
	$general_stats[$v->campaignID] = $v->nr;
}
$campaigns = $this->get_campaigns();
//var_dump($campaigns);

?>


<h1>Tracking results</h1>
<hr>
		        
<?php settings_errors(); ?>

<table class="wp-list-table widefat fixed striped table-view-list posts">
<?php if(count($campaigns)>0){ ?>
<thead>
	<tr>
		<th>Status</th>
		<th>Collected</th>
		<th>Name</th>
		<th>Description</th>
		<th>Actions</th>
	</tr>
</thead>
<?php } ?>

<tbody>

<?php 

if(count($campaigns)>0){
	?>
	<h2>All stats</h2>  
	<?php
	$campaign_name_collect = array();
	foreach($campaigns as $cprow){
		?>
<tr>
	<td class="check-column"><?php echo ($cprow->active) ? '<b>Running</b>' : 'Stopped'; ?></td>
	<td><?php echo $general_stats[$cprow->id] ?? "None"; ?></td>
	<td class="author column-author"><a href='<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page='.esc_attr($_REQUEST['page']).'&campaign='.(int)$cprow->id);?>' ><?php echo esc_html($cprow->campaign_name); ?></a></td>
	<td class="comments column-comments"><?php echo $cprow->description; ?></td>
	<td>
		<div class="material-icons stats-view-icon" data-campid="<?php echo $cprow->id; ?>">preview</div>
		<div class="material-icons stats-delete-icon" data-campid="<?php echo $cprow->id; ?>">delete_sweep</div>
	</td>
</tr>
		<?php
	}
}else{
	echo "<tr>
	
	<td colspan='5' style='padding: 30px;text-align:center;'>No campaign data yet. Define a campaign to start tracking.
	<br><br>
	<a class='page-title-action' href='admin.php?page=trafficsnippets-campaigns&newcamp=1'>New campaign</a>
	
	</td></tr>";	
}
?>

</tbody>
</table>

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?> 