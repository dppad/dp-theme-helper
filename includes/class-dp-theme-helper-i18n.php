<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://khalidhoffman.solutions
 * @since      1.0.0
 *
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/includes
 * @author     Khalid Hoffman <khalidhoffman@gmail.com>
 */
class Dp_Theme_Helper_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'dp-theme-helper',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
