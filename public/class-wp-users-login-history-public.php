<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://chetanvaghela.cf/
 * @since      1.0.0
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/public
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Wp_Users_Login_History_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-users-login-history-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-users-login-history-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 # Update the user login.
	 #
	 # @author Chetan Vaghela
	 # @access public
	 # @return void
	 */
	/*public function wpulh_wp_login( $user_login ) {

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
		
	}*/

}
