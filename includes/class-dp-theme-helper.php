<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://khalidhoffman.solutions
 * @since      1.0.0
 *
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/includes
 * @author     Khalid Hoffman <khalidhoffman@gmail.com>
 */
class Dp_Theme_Helper {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Dp_Theme_Helper_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'dp-theme-helper';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_api();
	}

	public function define_api() {
		if ( ! function_exists( 'add_theme_text' ) ) {
			function add_theme_text( $label, $slug = null, $type = 'text' ) {

				$dp_text = new DP_Text( $label, $slug, $type );

				add_filter( 'dp_theme_text', array( $dp_text, 'register' ) );

			}
		}

		if ( ! function_exists( 'add_theme_textarea' ) ) {
			function add_theme_textarea( $label, $slug = null, $type = 'textarea' ) {

				$dp_text = new DP_Text( $label, $slug, $type );

				add_filter( 'dp_theme_text', array( $dp_text, 'register' ) );

			}
		}

		if ( ! function_exists( 'add_section_theme_textarea' ) ) {
			function add_section_theme_textarea( $section_name, $field_name = null, $slug = null, $type = 'textarea' ) {

				$dp_text = new DP_Section_Text( $section_name, $field_name, $slug, $type );

				add_filter( 'dp_theme_text_sections', array( $dp_text, 'register' ) );

			}
		}

		if ( ! function_exists( 'get_section_theme_text' ) ) {
			function get_section_theme_text( $section_name, $label = '' ) {
				return get_option( sanitize_title_with_dashes( 'dp_theme_helper_' . $section_name . ' ' . $label ) );
			}
		}

		if ( ! function_exists( 'get_theme_text' ) ) {
			function get_theme_text( $slug = null, $label = '' ) {

				if ( $slug == null ) {
					$slug = sanitize_title_with_dashes( $label );
				}

				return get_option( 'dp_theme_helper_' . $slug );
			}
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Dp_Theme_Helper_Loader. Orchestrates the hooks of the plugin.
	 * - Dp_Theme_Helper_i18n. Defines internationalization functionality.
	 * - Dp_Theme_Helper_Admin. Defines all hooks for the admin area.
	 * - Dp_Theme_Helper_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-theme-helper-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-theme-helper-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dp-theme-helper-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dp-theme-helper-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-theme-helper-text.php';

		$this->loader = new Dp_Theme_Helper_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dp_Theme_Helper_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Dp_Theme_Helper_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Dp_Theme_Helper_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'print_messages' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

//		$plugin_public = new Dp_Theme_Helper_Public( $this->get_plugin_name(), $this->get_version() );
//
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Dp_Theme_Helper_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
