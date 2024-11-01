/*------------------------ 
Frontend related javascript
------------------------*/

 
jQuery(document).ready(function() {
	
	
});

//outside document ready

	function save_track(campid,snipid,visitor,js1,js2){
		
		//alert(checkstatus+'_'+campid);
		var data = {
			'action': 'save_track_js_callback',
			'campid': campid,
			'snipid': snipid,
			'visitor': visitor,
			'js1': js1,
			'js2': js2,
		};
		
		//alert(JSON.stringify(data));
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajax_url, data, function(response) { //using ajax_url specified in enqueue_frontend_scripts_and_styles()
			//alert('Got this from the server: ' + response);
		});		
	} 
	
