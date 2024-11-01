<?php
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit(); 
    global $wpdb;  

	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_cache`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_cachelog`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_campaigns`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_campaign_snips`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_formulas`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}traffic_snippets_stats`");
	
	delete_option("ts_cachetime");
	delete_option("ts_hideinactive_camps");
?>

