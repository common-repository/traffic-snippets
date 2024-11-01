<?php

require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php"; 


$campaignID = (int)$_REQUEST['campaign'];
$campaign = $this->get_single_campaign($campaignID); 
$hooks = $this->get_campaign_snips($campaignID);
//var_dump($hooks);

$stats = $this->get_all_stats($campaignID);
//echo "<pre>";
//var_dump($stats);
?>

<!-- https://fonts.google.com/icons -->

<h1><?php echo $campaign->campaign_name; ?></h1>
<div class="wp-die-message"><?php echo $campaign->description; ?></div>
<hr>		
        
<?php //settings_errors(); ?>
<h1 class="wp-heading-inline" ><?php echo __("Data Collecting Snippets","trafficsnippets"); ?></h1>  
<a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$campaignID; ?>&newhook=1"><?php echo __("Add new", 'trafficsnippets'); ?></a>   

<a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$campaignID; ?>&initend=<?php echo (int)$campaignID; ?>"><?php echo __("Insert default start and finish", 'trafficsnippets'); ?></a>   


<?php //echo TRAFFIC_SNIPPETS()->deploy->save_track(1, 2, array('phpcheck1'=>'test')); ?>
<table class="wp-list-table widefat fixed xxstriped table-view-list posts">
<thead>
<tr>
<th><?php echo __("Status", 'trafficsnippets'); ?></th>
<th><?php echo __("Formula Vars", 'trafficsnippets'); ?></th>
<th><?php echo __("checks", 'trafficsnippets'); ?></th>
<th><?php echo __("Info", 'trafficsnippets'); ?></th>
<th><?php echo __("Hook", 'trafficsnippets'); ?></th>
<th><?php echo __("Actions", 'trafficsnippets'); ?></th>
</tr>
</thead>
<tbody>

<?php 

if(count($hooks)>0){
	?>
	
	

	
	<?php
	$campaign_name_collect = array();
	foreach($hooks as $hook){

		if($hook->ord<100){
			$row_class = "start_snips";
		}elseif($hook->ord>=100 && $hook->ord<1000){
			$row_class = "regular_snips";
		}elseif($hook->ord>=1000){
			$row_class = "finish_snips";
		}
			
		?>
<tr class="<?php echo esc_attr($row_class); ?>">

	<td class="check-column">	
		<div class="custom-control custom-switch">
			<input type="checkbox"  class="custom-control-input checkbox-status checkbox-status-hooks"  data-snipid="<?php echo (int)$hook->id; ?>" id="active[<?php echo (int)$hook->id; ?>]" name="active[<?php echo (int)$hook->id; ?>]" <?php echo ($hook->active) ? "checked" : "" ; ?>>
			<label class="custom-control-label" for="active[<?php echo (int)$hook->id; ?>]">#<?php echo (int)$hook->id; ?></label>
		</div>
	</td>
	
	<td class="comments column-comments">
	<?php 
	
	$vals_str = array();
		if($hook->phpcheck1_code && isset($stats['phpcheck1'][$hook->id]) && is_array($stats['phpcheck1'][$hook->id])){
			foreach($stats['phpcheck1'][$hook->id] as $kn => $kv){
				$vals_str[] = "<b>[".esc_html($hook->label).":".esc_html($kn)."]</b> = ".esc_html($kv)." ";
			}
		}

		if($hook->phpcheck2_code && isset($stats['phpcheck2'][$hook->id]) && is_array($stats['phpcheck2'][$hook->id])){
			foreach($stats['phpcheck2'][$hook->id] as $kn => $kv){
				$vals_str[] = "<b>[".esc_html($hook->label).":".esc_html($kn)."]</b> = ".esc_html($kv)." ";
			}
		}

		if($hook->jscheck1_code && isset($stats['jscheck1'][$hook->id])  && is_array($stats['jscheck1'][$hook->id])){
			foreach($stats['jscheck1'][$hook->id] as $kn => $kv){
				$vals_str[] ="<b>[".esc_html($hook->label).":".esc_html($kn)."]</b> = ".esc_html($kv)." ";
			}
		}

		if($hook->jscheck2_code && isset($stats['jscheck2'][$hook->id])  && is_array($stats['jscheck2'][$hook->id])){
			foreach($stats['jscheck2'][$hook->id] as $kn => $kv){
				$vals_str[] ="<b>[".esc_html($hook->label).":".esc_html($kn)."]</b> = ".esc_html($kv)." ";
			}
		}		
		if(count($vals_str)==0){
			echo "Collecting data....";
		}else{
			echo implode("<br>\n",$vals_str);
		}
	?>	
	</td>

	<td class="comments column-comments">
		
		 <?php if($hook->phpcheck1_test) { ?><b style="color:red;"> Failed PHP #1 </b><?php } ?>
		 <?php if($hook->phpcheck2_test) { ?><b style="color:red;"> Failed PHP #2 </b><?php } ?>
		 
		
		<?php if($hook->phpcheck1_code){ ?><span class="material-icons">php</span><?php } ?>
		<?php if($hook->phpcheck2_code){ ?><span class="material-icons">php</span><?php } ?>
		<?php if($hook->jscheck1_code){ ?><span class="material-icons">javascript</span><?php } ?>
		<?php if($hook->jscheck2_code){ ?><span class="material-icons">javascript</span><?php } ?>		
	</td>
	
	
	
	<td class="comments column-comments">
		<?php echo esc_html($hook->info); ?>
	</td>
	
	<td class="comments column-comments">
		<?php echo esc_html($hook->hook); ?>
	</td>
	
	<td>
		<div class="material-icons hook-edit-icon button" data-snipid="<?php echo (int)$hook->id; ?>" data-campid="<?php echo (int)$campaignID; ?>">edit_note</div>
		<div class="material-icons hook-delete-icon" data-snipid="<?php echo (int)$hook->id; ?>" data-campid="<?php echo (int)$campaignID; ?>">delete_forever</div>
	</td>

</tr>
		<?php
	}
}else{
	echo "<tr><td colspan='5'>".__("No hooks data yet. Define hooks to start tracking.", 'trafficsnippets')."</td></tr>";	
}
?>

</tbody>
</table>



<br>
<br>
<br>

<?php 

$formulas = $this->get_campaign_formulas($campaignID); 

?>

<div class="wp-die-message"><?php __("Formulas are used to display the results on your dashboard via a widget.", 'trafficsnippets'); ?> </div>
<hr>		
        
<?php //settings_errors(); ?>
<h1 class="wp-heading-inline" ><?php echo __("Display Formulas", 'trafficsnippets'); ?></h1>  
<a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$campaignID; ?>&newformula=1"><?php echo __("Add new", 'trafficsnippets'); ?></a>   

<a class="page-title-action" href="admin.php?page=trafficsnippets-campaigns&campaign=<?php echo (int)$campaignID; ?>&initformula=<?php echo (int)$campaignID; ?>"><?php echo __("Insert default formula", 'trafficsnippets'); ?></a>   


<?php //echo TRAFFIC_SNIPPETS()->deploy->save_track(1, 2, array('phpcheck1'=>'test')); ?>
<table class="wp-list-table widefat fixed striped table-view-list posts">
<thead>
<tr>
<th>id#</th>
<th><?php echo __("Meaning", 'trafficsnippets'); ?></th>
<th><?php echo __("Formula", 'trafficsnippets'); ?></th>
<th><?php echo __("Result", 'trafficsnippets'); ?></th>
<th><?php echo __("Actions", 'trafficsnippets'); ?></th>
</tr>
</thead>
<tbody>

<?php 

if(count($formulas)>0){

	foreach($formulas as $formula){
			
		?>
<tr class="">
	
	<td class="comments column-comments">
		#<?php echo (int)$formula->id; ?>
	</td>

	<td class="comments column-comments">
		<?php echo esc_html($formula->meaning); ?>
	</td>
	
	<td class="comments column-comments">
		<pre><?php echo esc_html($formula->formula); ?></pre>
	</td>

	<td class="comments column-comments">
		<pre><?php echo esc_html($this->parse_formula_str($formula->formula,$stats)); ?></pre>
	</td>
	
	<td>
		<div class="material-icons formula-edit-icon button" data-formulaid="<?php echo (int)$formula->id; ?>" data-campid="<?php echo (int)$campaignID; ?>">edit_note</div>
		<div class="material-icons formula-delete-icon" data-formulaid="<?php echo (int)$formula->id; ?>" data-campid="<?php echo (int)$campaignID; ?>">delete_forever</div>
	</td>

</tr>
		<?php
	}
}else{
	echo "<tr><td colspan='5'>".__("No formulas defined yet. Insert default formula or create custom, then you can use formula id# to display the results on your dashboard via a widget.", 'trafficsnippets')."</td></tr>";
}
?>

</tbody>
</table>

<br><br>
<?php echo __("Display formula is used to show an aggregated result based on the collected data. For example 	<i>([finish]*100)/[start]</i> will display the percentage of visitors that get from start to finish", 'trafficsnippets'); ?> 

<br><input type="hidden" value="save_formula">
<br><input type="submit" value="Save Formula" class="button button-primary" >

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>