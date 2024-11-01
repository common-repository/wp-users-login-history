<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://chetanvaghela.cf/
 * @since      1.0.0
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/admin
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Wp_Users_Login_History_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Users_Login_History_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Users_Login_History_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-users-login-history-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Users_Login_History_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Users_Login_History_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-users-login-history-admin.js', array( 'jquery' ), $this->version, false );
		# define ajax objext
		wp_localize_script( $this->plugin_name,'wpulh_ajax_object',array('ajaxurl' => admin_url( 'admin-ajax.php' ),'wpulh_ajax_nonce' => wp_create_nonce('wpulh_ajax_nonce')));


	}
	/**
	 # Add new section in user detail page
	 # View and clear history buttons
	 */
	public function wpulh_add_extra_user_fields( $user )
	{
		# get user history data
		$get_user_history = get_user_meta( $user->ID, 'wp-users-login-history',true);
		if (!empty($get_user_history)) {
	    ?>
	        <h3><?php _e("Login History", "wp-users-login-history"); ?></h3>
	        <table class="form-table">
	            <tr class="user-login-history-title-wrap">
	                <th><label for=""><?php _e('Manage Login History','wp-users-login-history'); ?></label></th>
	                <td><button type="button" class="button get-wp-users-login-history" data-wpulh-id="<?php echo $user->ID;?>"><?php _e('View Login History','wp-users-login-history'); ?></button>&nbsp;&nbsp;<button type="button" class="button clear-wp-users-login-history" data-wpulh-id="<?php echo $user->ID;?>"><?php _e('Clear History','wp-users-login-history'); ?></button></td>
	            </tr>
	        </table>
	    <?php
		}
	}

	/**
	 # Add wpulh popup html in admin footer
	 #
	 # @since    1.0.0
	 */
	public function wpulh_admin_footer_user(){
		# include popup in admin footer
		include(WPULH_PLUGIN_DIR.'admin/partials/wpulh-popup-html.php');		
	}

	/**
	 # Adds the new column named Login History to the network admin user list.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @param  array $cols The default columns.
	 # @return array
	 */
	public function wpulh_add_column( $cols ) {
		# Add New column name and slug in existing column
		$cols['wp-users-login-history'] = __( 'Login History', 'wp-users-login-history' );
		return $cols;
	}

	/**
	 # Adds data in the new column named Login History  to the network admin user list.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return string
	 */
	public function wpulh_add_data_in_column( $col_value, $column_name, $user_id ) {
		# check culumn
		if ( 'wp-users-login-history' === $column_name ) {
			# set value - by default
			$col_value      = __( ' - ', 'wp-users-login-history' );
			# get user login history from database
			$get_user_history = get_user_meta( $user_id, 'wp-users-login-history',true);
			# if get data
			if (!empty($get_user_history)) {
				# rearrange key of array
				$get_user_history = array_values($get_user_history);
				# get date format option
				$format = get_option( 'date_format' ) ;
				# get date
				$wpulh_date = $get_user_history[0]['wpulh_date'];	
				if($wpulh_date)
				{	
					# set new value of column with popup link
					$col_value = sprintf( '<a href="javascript:void(0);" class="get-wp-users-login-history" data-wpulh-id="%s">%s</a>', $user_id,date_i18n( $format, $wpulh_date ));	
				}
			}
		}

		return $col_value;
	}


	/**
	 # Update the user login.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return void
	 */
	public function wpulh_wp_login( $user_login ) {

		# get user by user login
		$user = get_user_by( 'login', $user_login );

		#define empty variables
		$get_user_history = $client_env_ip = $user_ip = $WpulhgetBrowser = $user_browser = $ip_data = $ip_detail = "" ;
		# get user success
		if($user)
		{
			# set default value
			$user_ip = $user_browser = $country_name = $city_name = $continent_name = $latitude = $longitude = $currency_symbol = $currency_code = $timezone = "-";

			#get user login history data
			$get_user_history = get_user_meta( $user->ID, 'wp-users-login-history',true);

			# get client environment ip
			$client_env_ip = $this->Wpulh_get_client_ip_env();
			# get client Server ip
			$client_server_ip = $this->Wpulh_get_client_ip_server();

			# check is not empty
			if($client_env_ip && $client_server_ip)
			{
				# set value of IP
				$user_ip = "<b> Environment IP : </b>".$client_env_ip." | <b> Server IP : </b>".$client_server_ip;
			}
			# get detail of user browser
			$WpulhgetBrowser= $this->WpulhgetBrowser();
			# check is not empty
			if($WpulhgetBrowser)
			{
				# set value of browser
				$user_browser = $WpulhgetBrowser['name'] . " | <b>Version :</b> " . $WpulhgetBrowser['version'] . " |  <b>Platform :</b> " .$WpulhgetBrowser['platform'];
			}

			# get data using IP 
			$ip_data = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$_SERVER['REMOTE_ADDR'])); 
			
			# check data 
			if($ip_data)
			{	  
				# get data success
				if($ip_data->geoplugin_status == 200)
				{
					$country_name =  $ip_data->geoplugin_countryName;
					$city_name =  $ip_data->geoplugin_city;
					$continent_name =  $ip_data->geoplugin_continentName;
					$latitude =  $ip_data->geoplugin_latitude;
					$longitude =  $ip_data->geoplugin_longitude;
					$currency_symbol =  $ip_data->geoplugin_currencySymbol;
					$currency_code =  $ip_data->geoplugin_currencyCode;
					$timezone =  $ip_data->geoplugin_timezone; 

					$ip_detail .= "<p><b>Country Name :</b> " . $country_name . " | <b>City Name :</b> " .$city_name;
					$ip_detail .= "</p><p><b>Continent Name :</b> " . $continent_name . " |  <b>Timezone :</b> ".$timezone;
					$ip_detail .= "</p><p><b>Latitude : </b> " . $latitude." | <b>Longitude :</b> " .$longitude;
					$ip_detail .= "</p><p><b>Currency Symbol : </b> " . $currency_symbol . " | <b>Currency Code :</b> " .$currency_code."</p>";
				}
			}

			# count previouslly added data
			$count = !empty($get_user_history) ? (count($get_user_history)+ 1) : 0;
			# prepare data for entry
			$add_user_history[$count] = array(
				"wpulh_date" => time(),
				"wpulh_ip" => $user_ip,
				"wpulh_broswer" => $user_browser,
				"wpulh_ip_detail" => $ip_detail,
			);
			
			# if not first time
			if(!empty($get_user_history)) 
			{   
				# merge new data with existing data
				$get_user_history = $add_user_history + $get_user_history; 
				# update data
				update_user_meta( $user->ID, 'wp-users-login-history', $get_user_history );
			}
			else
			{	
				# update data
			    update_user_meta( $user->ID, 'wp-users-login-history', $add_user_history );
			}
		}
		
	}


	/**
	 # Clear the user login History.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return void
	 */
	function clear_wp_users_login_history()
	{
		# verify ajax nonce
		check_ajax_referer( 'wpulh_ajax_nonce', 'wpulh_security' );

		#define empty variables
		$user_id = 0;
	    $status  = $message = "";
		
		# default messages
	 	$status = "error";
	 	$message = "Something Wrong";
	 
	 	# check data is set
	    if ( isset( $_POST['wpulh_id'] ) ) {

	    	# get user id
	        $user_id = (int) $_POST['wpulh_id'];
	        # if not empty
	        if(!empty( $user_id))
	        {
	        	# get data of user using user id
		        $user = get_user_by( 'id', $user_id );
		        # get user
		        if($user)
				{
					# get user id
				    $user_id = $user->ID;
				    if($user_id)
				    {
				    	# get history
				    	$get_user_history = get_user_meta( $user->ID, 'wp-users-login-history',true);
				    	# take backup of history before erase history
				    	update_user_meta( $user_id, 'wp-users-login-history-backup',$get_user_history);
				    	# clear History
				    	update_user_meta( $user_id, 'wp-users-login-history', "" );
				    	# messages
				    	$status = "success";
				    	$message = "Login History Cleared Successfully";
				    }
		        }
		    }	        
	    }
	    # prepare data
	    $data = array(
            "status"     => $status,
            "message"  => $message,
	        );
	   	# send response
	    echo json_encode($data);
	    wp_die();
	}

	/**
	 # Fetch the user login History.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return void
	 */
	function get_wp_users_login_history()
	{
		# verify ajax nonce
		check_ajax_referer( 'wpulh_ajax_nonce', 'wpulh_security' );

		# define impty varibles
		$user_id = 0;
	    $status  = $wpulh_username = $history_html = "";
	 	
	 	# messages
	 	$status = "error";
	 	$wpulh_username = "Something Wrong";
	 	$history_html = "";

	 	# check data is set
	    if ( isset( $_POST['wpulh_id'] ) ) {

	    	# get user id
	        $user_id = (int) $_POST['wpulh_id'];

	        # get data of user using user id
	        $user = get_user_by( 'id', $user_id );
	        # get user
	        if($user)
			{
				# get user id
			    $user_id = $user->ID;
			   	
			   	# get hostory data
	        	$get_user_history = get_user_meta( $user_id, 'wp-users-login-history',true);

	        	# history data not empty
	        	if(!empty($get_user_history))
	        	{
	        		# rearrange array key
					$get_user_history = array_values($get_user_history);
					# if not empty
					if (!empty($get_user_history)) {

						# messages
						$status = "success";
						# get date format
						$format = get_option( 'date_format' ) ;

						# define empty varible
						$history_html = "";
						# user name and email for heading
						$wpulh_username = $user->user_login." | ".$user->user_email;

						# prepare HTML of history data
						foreach ($get_user_history as $key => $get_history) {
							
							# date and time
							$wpulh_get_date = $get_history['wpulh_date'];	
							# ip address
							$wpulh_get_ip = $get_history['wpulh_ip'];	
							# browser details
							$wpulh_get_browser = $get_history['wpulh_broswer'];	
							# detail based on IP
							$wpulh_get_ip_detail = $get_history['wpulh_ip_detail'];	
							
							$wpulh_date = (!empty($wpulh_get_date)) ? date("F j, Y l -  h:i:s A",$wpulh_get_date) : "-";
							$wpulh_ip = (!empty($wpulh_get_ip)) ? $wpulh_get_ip : "-";
							$wpulh_broswer = (!empty($wpulh_get_browser)) ?  $wpulh_get_browser : "-";
							$wpulh_ip_detail = (!empty($wpulh_get_ip_detail)) ?  $wpulh_get_ip_detail : "";

							$history_html .= '<div class="login-history_detail-wrap">';
							$history_html .= '<p>';
							$history_html .= '<label><strong>'.__("Date : ","wp-users-login-history").'</strong></label><span>'.$wpulh_date.'</span>';
							$history_html .= '</p>';
							$history_html .= '<p>';
							$history_html .= '<span>'.$wpulh_ip.'</span>';
							$history_html .= '</p>';
							$history_html .= '<p>';
							$history_html .= '<label><strong>'.__("Browser : ","wp-users-login-history").'</strong></label><span>'.$wpulh_broswer.'</span>';
							$history_html .= '</p>';
							$history_html .= '<p>';
							$history_html .= $wpulh_ip_detail;
							$history_html .= '</p>';
							$history_html .= '</hr>';
							$history_html .= '</div>';
						}
					}
				}
	        }
	        
	    }
	    # prepare data
	    $data = array(
            "status"     => $status,
            "wpulh_username"  => $wpulh_username,
            "wpulh_history"  => $history_html
	        );
	    # send responce
	    echo json_encode($data);
	    wp_die();
	}

	/**
	 # Get User Browser Detail
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return Browser Detail
	 */
	function WpulhgetBrowser() 
	{ 
	    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	    
	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } 
	    elseif(preg_match('/Firefox/i',$u_agent)) 
	    { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    } 
	    elseif(preg_match('/Chrome/i',$u_agent)) 
	    { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } 
	    elseif(preg_match('/Safari/i',$u_agent)) 
	    { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } 
	    elseif(preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } 
	    elseif(preg_match('/Netscape/i',$u_agent)) 
	    { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    } 
	    
	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	    
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	    
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	    
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	} 

	/**
	 # Get Client Environment IP Address
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return Browser Detail
	 */
	function Wpulh_get_client_ip_env() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	        $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	 
	    return $ipaddress;
	}
	/**
	 # Get Client Server IP Address
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return Browser Detail
	 */
	function Wpulh_get_client_ip_server() {
	    $ipaddress = '';
	    if ($_SERVER['HTTP_CLIENT_IP'])
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_X_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if($_SERVER['HTTP_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if($_SERVER['REMOTE_ADDR'])
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	 
	    return $ipaddress;
	}

}
