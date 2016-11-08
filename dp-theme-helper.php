<?php
require_once ('vendor/autoload.php');
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://khalidhoffman.solutions
 * @since             1.0.0
 * @package           Dp_Theme_Helper
 *
 * @wordpress-plugin
 * Plugin Name:       DP-theme-helper
 * Plugin URI:        http://dppad.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Khalid Hoffman
 * Author URI:        http://khalidhoffman.solutions
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dp-theme-helper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dp-theme-helper-activator.php
 */
function activate_dp_theme_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dp-theme-helper-activator.php';
	Dp_Theme_Helper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dp-theme-helper-deactivator.php
 */
function deactivate_dp_theme_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dp-theme-helper-deactivator.php';
	Dp_Theme_Helper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dp_theme_helper' );
register_deactivation_hook( __FILE__, 'deactivate_dp_theme_helper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dp-theme-helper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dp_theme_helper() {

	$plugin = new Dp_Theme_Helper();
	$plugin->run();

}
run_dp_theme_helper();
