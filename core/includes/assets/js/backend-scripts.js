/*------------------------ 
Backend related javascript
------------------------*/ 
 
/**
 * This file contains all of the backend related javascript. 
 * With backend, it is meant the WordPress admin area.
 * 
 * You can add the localized variables in here as followed: trafficsnippets.plugin_name
 * These variables are defined within the localization function in the following file:
 * core/includes/classes/class-trafficsnippets-run.php
 *
 */


jQuery(document).ready(function() {
	
	
	//----hooks page start------------------------
	jQuery(".hook-delete-icon").click(function(){
		var snipid = jQuery(this).data("snipid");
		var campid = jQuery(this).data("campid");
		//alert('hook id is"'+snipid);
		if(confirm("Are you sure you want to delete this hook and all its collected statistics?")){
			window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid+'&delhook='+snipid); 
			exit();
		}
	});
	jQuery(".hook-edit-icon").click(function(){
		var snipid = jQuery(this).data("snipid");
		var campid = jQuery(this).data("campid");
		window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid+'&edithook='+snipid); 
		exit();
	});

	jQuery('.checkbox-status-hooks').change(function(){
		var checkstatus = this.checked;
		var campid = jQuery(this).data("campid");
		var snipid = jQuery(this).data("snipid");
		//alert(checkstatus+'_'+snipid);
		
		var data = {
			'action': 'save_hooks_status',
			'snipID': snipid,
			'status': checkstatus,
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			//alert('Got this from the server: ' + response);
		});
		
	});

	//--- formulas subsection

	jQuery(".formula-delete-icon").click(function(){
		var formulaid = jQuery(this).data("formulaid");
		var campid = jQuery(this).data("campid");
		//alert('hook id is"'+snipid);
		if(confirm("Are you sure you want to delete this formula? Existing collected data will not be affected. ")){
			window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid+'&delformula='+formulaid); 
			exit();
		}
	});
	jQuery(".formula-edit-icon").click(function(){
		var formulaid = jQuery(this).data("formulaid");
		var campid = jQuery(this).data("campid");
		window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid+'&editformula='+formulaid); 
		exit();
	});


	//-----hook page end------------------------
	
	
	
	//----campaign page start------------------------
	jQuery(".campaign-view-icon").click(function(){
		var campid = jQuery(this).data("campid");
		window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid); 
		exit();
	});		

	jQuery(".campaign-delete-icon").click(function(){
		var campid = jQuery(this).data("campid");
		if(confirm("Are you sure you want to delete this campaign and all its hooks and collected statistics ?")){
			window.location.replace('admin.php?page=trafficsnippets-campaigns&campaign='+campid+'&delcamp='+campid); 
			exit();
		}
	});

	jQuery(".campaign-edit-icon").click(function(){
		var campid = jQuery(this).data("campid");
		window.location.replace('admin.php?page=trafficsnippets-campaigns&editcamp='+campid); 
	});
	
	jQuery('.checkbox-status-campaigns').change(function(){
		var checkstatus = this.checked;
		var campid = jQuery(this).data("campid");
		//alert(checkstatus+'_'+campid);
		var data = {
			'action': 'save_campaign_status',
			'campID': campid,
			'status': checkstatus,
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			//alert('Got this from the server: ' + response);
		});		
	});
	//----campaign page end------------------------
	
	
	
	//----stats page start
	jQuery(".stats-delete-icon").click(function(){
		var campid = jQuery(this).data("campid");
		if(confirm("Are you sure you want to delete all the collected statistics for this campaign?")){
			window.location.replace('admin.php?page=trafficsnippets-stats&delstats='+campid); 
			exit();
		}
	});	
	jQuery(".stats-view-icon").click(function(){
		var campid = jQuery(this).data("campid");
		window.location.replace('admin.php?page=trafficsnippets-stats&campaign='+campid); 
		exit();
	});		
	//----stats page end------------------------
	
});