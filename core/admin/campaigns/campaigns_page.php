<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 


?>


<h1>Tracking Campaigns</h1>
<div class="wp-die-message">Create a different campaign for each thing you wish to track</div>
<hr>


<table class="wp-list-table widefat fixed striped table-view-list posts">
<thead> 
<tr><th>Status</th>
<th>Description</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
	<h1 class="wp-heading-inline" >All Campaigns</h1>  <a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&newcamp=1">Add new</a>
	<br>
<?php 
$campaigns = $this->get_campaigns();
//var_dump($campaigns);
if(count($campaigns)>0){
	?>

	<?php
	$campaign_name_collect = array();
	foreach($campaigns as $cprow){
		?>
<tr>
	<td class="check-column">	
	<div class="custom-control custom-switch">
  <input type="checkbox"  class="custom-control-input checkbox-status checkbox-status-campaigns" data-campid="<?php echo (int)$cprow->id; ?>" id="active[<?php echo (int)$cprow->id; ?>]" name="active[<?php echo (int)$cprow->id; ?>]" <?php echo ($cprow->active) ? "checked" : "" ; ?>>
  <label class="custom-control-label" for="active[<?php echo (int)$cprow->id; ?>]"><?php echo esc_html($cprow->campaign_name); ?></label>
</div>


	</td>
	

	
	<td class="comments column-comments">
		<?php echo esc_html($cprow->description); ?>
	</td>
	
	<td class="author column-author">

		<div class="material-icons campaign-view-icon button" data-campid="<?php echo (int)$cprow->id; ?>">settings</div>
		<div class="material-icons campaign-edit-icon" data-campid="<?php echo (int)$cprow->id; ?>">edit_note</div>
		<div class="material-icons campaign-delete-icon" data-campid="<?php echo (int)$cprow->id; ?>">delete_forever</div>
	</td>
	
	
</tr>
		<?php
	}
}else{
	echo "<tr><td colspan='3'>No campaign data yet. Define a campaign to start tracking.</td></tr>";	
}
?>

</tbody> 
</table>

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>