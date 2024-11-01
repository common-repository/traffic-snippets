<?php
  
$ret = array();
$all_camps = $this->get_campaigns();
foreach($all_camps as $ck => $cv){
	
	if(!$cv->active){ 
	echo " [inactive] ";
	}
	echo "<b>{$cv->campaign_name}</b>";
	
	$formulas = $this->get_campaign_formulas($cv->id);
	$stats = $this->get_all_stats($cv->id);
	
	foreach($formulas as $fm){
		$formval = $this->parse_formula_str($fm->formula,$stats,true);
		//$fm->formula
		echo "<br>&nbsp; &nbsp; {$fm->meaning}: {$formval}";
	}
	echo "<hr>";
}$cachetime = get_option('ts_cachetime', 5);
echo "<br><i>(recalculated every {$cachetime} minutes)</i>";

?>