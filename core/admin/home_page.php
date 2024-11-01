<?php 


require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/header.php";


?>
 

	<h1>Traffic_Snippets</h1>
	This is the first version of the plugin..
	<hr>
 
	<br><b>What it does?</b>
	<br> Collects custom data at custom defined points. Like this you can collect any data you need, in any hook point you need, then perform statistics and see totals for the collected data.
	<br> The data is tracked by distinct visitor, so you can track the activity from the visitor point of view, not based on time. Its a bit difficult to understand at first, but once you get it, you will see the outcome is very valuable.
	<br> For example, you can collect data about visitors entering the site and reaching the checkout page or investigating their woocommerce cart, then visitors reaching the order complete page, and create a simple formula to see 	what percentage of the visitors abandon their orders.
	<br>
	<br> All data can be displayed in a widget, so you can keep an eye on whats going on with the visitors behaviour at a glance, without having to analyse complex charts.	<br> Data is stored indedenetly so the percentage stats are improved over time, but you should consider creating distinct campaigns for distinct periods or parameters you want to monitor.Creating multiple campaings also allows you to compare progress and see in what direction things are going so you can improve conversion rate.
	<br>
	<br><b>How to use it</b>
	<br>
	<br>Step1: Create a campaign
	<br>Step2: Define campaign snippets, in what hook they run, and what they output. You can define multiple hooks per snippet, to cover all possible places that could trigger that action. Output should be a string, that will be used as variable name in formulas you define after.  	Outputting 'yes' and 'no' in php, or return('yes');  in js snippets, will make the data available in formulas like this: [label_name:yes] and it will output the total number of unique visitors that triggered it once.
	<br>Step3: Let the campaign collect live data for at least 24 to get accurate results, or more.
	<br>Step4: Create formulas to display the desired total/percentages. Check the example, is very clear.
	<br>
	<br>		<a href="<?php echo admin_url('admin.php?page=trafficsnippets-campaigns'); ?>" class="button primary-button">View Tracking Campaigns</a>	<br><br>
	Feel free to contact me for custom setups
	

<?php require_once TRAFFIC_SNIPPETS_PLUGIN_DIR."core/admin/partials/footer.php"; ?>