<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://chetanvaghela.cf/
 * @since      1.0.0
 *
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Users_Login_History
 * @subpackage Wp_Users_Login_History/includes
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Wp_Users_Login_History_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-users-login-history',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
