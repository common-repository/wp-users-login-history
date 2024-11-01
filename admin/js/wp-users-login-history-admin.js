jQuery(document).ready(function($) {
		/* hide popup, display account icon*/
		function hide_wpulh_model() {
			$("#wp-users-login-history-container").hide();
		}

		/* show popup, hide account icon*/
		function show_wpulh_model() {
        	$("#wp-users-login-history-container").show();
    	}

    	/* close popup on ESC button */
		$(document).keyup(function(e) {
	         if (e.key === "Escape") { // escape key maps to keycode `27`
	          hide_wpulh_model();
	        }
	    });

		/* Open popup */
	    $(document).on('click', '.get-wp-users-login-history', function (event) {
	    	event.preventDefault();
            var wpulh_id = $(this).data('wpulh-id');
          	if(wpulh_id == "")
            {
                console.log('error : User Id not Found'); //info,errors,warning,success
                return false;
            }
            
            $.ajax({
                url: wpulh_ajax_object.ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                type : 'POST',
                dataType: 'JSON',
                data: {
                    'action': 'get_wp_users_login_history',
                    wpulh_security : wpulh_ajax_object.wpulh_ajax_nonce,
                    'wpulh_id' : wpulh_id,
                },
                success:function(data) {
                	//console.log(data);
                    // This outputs the result of the ajax request
                    if(data.status == "success")
                    {
                    	show_wpulh_model();
                        $("h2.wpulh-username").html(data.wpulh_username);
                        $(".wpulh-history-container").html(data.wpulh_history);
                    }
                    //data.status
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });      
	    	
	    });

	    $(document).on('click', '.clear-wp-users-login-history', function (event) {
	    	event.preventDefault();
	    	var rys = confirm("Are you Sure? Want to clear Login history ?");
			if (rys == true) {
	            var wpulh_id = $(this).data('wpulh-id');
	          	if(wpulh_id == "")
	            {
	                console.log('error : User Id not Found'); //info,errors,warning,success
	                return false;
	            }
	            
	            $.ajax({
	                url: wpulh_ajax_object.ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
	                type : 'POST',
	                dataType: 'JSON',
	                data: {
	                    'action': 'clear_wp_users_login_history',
	                    wpulh_security : wpulh_ajax_object.wpulh_ajax_nonce,
	                    'wpulh_id' : wpulh_id,
	                },
	                success:function(data) {
	                	
	                    if(data.status == "success")
	                    {
	                    	alert(data.message);
	                    }
	                    if(data.status == "error")
	                    {
	                    	alert(data.message);
	                    }
	                    window.location.reload();
	                    //data.status
	                },
	                error: function(errorThrown){
	                    console.log(errorThrown);
	                }
	            });  
        		}    
	    	
	    });

	    /* Close popup */
	    $(document).on('click', '.wpulh-container-close-btn', function (event) {
	    	hide_wpulh_model();
	    });
});