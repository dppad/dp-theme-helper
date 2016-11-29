<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

require_once dirname( __DIR__ ) . "/includes/class-dp-theme-helper-option.php";

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://khalidhoffman.solutions
 * @since      1.0.0
 *
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dp_Theme_Helper
 * @subpackage Dp_Theme_Helper/admin
 * @author     Khalid Hoffman <khalidhoffman@gmail.com>
 */
class Dp_Theme_Helper_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	private $callback_namespace = 'dp_theme_helper_';
	private $plugin_text_namespace = 'dp_theme_helper';
	private $main_settings_option_name = 'general';
	private $message_queue = array();
	private $text_sections = array();

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$log = new Logger( 'dp_theme_helper_admin_log' );
//		$log->pushHandler( new StreamHandler( dirname( __DIR__ . '/../../' ) . "/data/dev.log", Logger::INFO ) );
		$log->pushHandler( new ChromePHPHandler( Logger::INFO ) );
		$this->logger = $log;


		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->perform_actions();
	}

	private function perform_actions() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'dp-theme-helper' ) {
			if ( isset( $_GET['action'] ) ) {
				$action = $_GET['action'];
				switch ( $action ) {
					case 'update':
						$this->updateCachedSettings();
						break;
					default:
						break;
				}
			}
		}
	}

	private function updateCachedSettings() {
		$this->add_message( "Updated JSON" );
	}

	private function add_message( $text, $type = 'info' ) {
		array_push( $this->message_queue, array(
			'text' => $text,
			'type' => $type
		) );
	}

	private function render_message( $message_meta ) {
		switch ( $message_meta['type'] ) {
			case 'info':
			case 'error':
			case 'warning':
			case 'success':
				$class_name = 'notice-' . $message_meta['type'];
				break;
			default:
				$class_name = 'notice-info';
				break;
		}

		return sprintf( "<div class='notice %s is-dismissible'><p>%s</p></div>", $class_name, _( $message_meta['text'] ) );
	}

	public function get_messages() {
		return $this->message_queue;
	}


	public function print_messages() {

		while ( sizeof( $this->message_queue ) > 0 ) {
			$message_meta = array_pop( $this->message_queue );
			switch ( $message_meta['type'] ) {
				case 'info':
				case 'warning':
				case 'error':
				case 'success':
					echo $this->render_message( $message_meta );
					break;
				default:
					$this->logger->addInfo( 'Invalid message type received', $message_meta );
					break;
			}
		}
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
		 * defined in Dp_Theme_Helper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dp_Theme_Helper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dp-theme-helper-admin.css', array(), $this->version, 'all' );

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
		 * defined in Dp_Theme_Helper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dp_Theme_Helper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dp-theme-helper-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'DP-theme-helper Settings', $this->plugin_text_namespace ),
			__( 'DP-theme-helper Settings', $this->plugin_text_namespace ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);

	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {
		require_once 'partials/dp-theme-helper-admin-display.php';
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function options_heading( $meta_data ) {
//		include 'partials/dp-theme-helper-admin-heading.php';
	}

	private function add_setting( $option_name, $field_name = "New Option", $type = 'textarea', $callback = false ) {

		$input_type = strtolower( $type );
		$option     = new DP_Option( $option_name );
		if ( $callback ) {
			register_setting(
				$this->plugin_name,
				$option->get_namespace(),
				array( $this, $callback )
			);
		} else {
			register_setting(
				$this->plugin_name,
				$option->get_namespace()
			);
		}

		switch ( $input_type ) {
			case 'text':
			case 'textarea':
			case 'checkbox':
				add_settings_field(
					$option->namespace,
					"<a href='#" . $option->namespace . "'>" . __( $field_name, $this->plugin_text_namespace ) . "</a>",
					array( $this, $this->callback_namespace . strtolower( $type ) . '_cb' ),
					$this->plugin_name,
					$this->callback_namespace . $this->main_settings_option_name,
					array( 'name' => $option->namespace, 'field_name' => $field_name )
				);
				break;
			default:
				error_log( 'dp-theme-helper.admin received an invalid input type:' . $type );
				break;
		}
	}

	public function register_theme_text() {

		$theme_text_sections = apply_filters( 'dp_theme_text', array() );


		$cached_theme_text_sections_str = get_option( 'dp_theme_text_cache' );
		$cached_theme_text_sections     = json_decode( $cached_theme_text_sections_str );
		$this->logger->addInfo( 'cached', array( $cached_theme_text_sections ) );

		$this->parse_theme_text_sections( $cached_theme_text_sections );
	}


	private function parse_theme_text_sections( $theme_section_settings ) {
		$this->logger->addInfo( 'parsing: ', array($theme_section_settings) );
		foreach ( $theme_section_settings as $theme_section_name => $theme_section_setting ) {
			$settings = array();

			foreach ( $theme_section_setting as $theme_setting_name => $theme_setting_type ) {
				$setting_option = new DP_Text_Option($theme_section_name, $theme_setting_name);
				$slug       = $setting_option->get_namespace();
				$field_name = $theme_setting_name;
				$type       = $theme_setting_type;
				$callback   = null;
				switch ( $type ) {
					case 'checkbox':
						$callback = 'sanitize_checkbox';
						break;
					default:
						break;
				}
				array_push( $settings, array(
					'slug'       => $slug,
					'field_name' => $field_name,
					'type'       => $type,
					'callback'   => null
				) );
			}

			$result = array(
				'section_name'  => sanitize_title_with_dashes( $theme_section_name ),
				'section_label' => $theme_section_name,
				'settings'      => $settings
			);
			$this->logger->addInfo( 'text_section', $result );
			$this->build_text_sections( $result );
		}
	}

	private function build_text_sections( $args = array() ) {
		$section_name  = $args['section_name'];
		$section_title = $args['section_label'];
		$settings      = $args['settings'];
		$section_slug  = $this->callback_namespace . sanitize_title_with_dashes( $section_name );

		array_push( $this->text_sections, $section_slug );
		add_settings_section(
			$section_slug,
			__( $section_title, $this->plugin_text_namespace ),
			array( $this, 'options_heading' ),
			$this->plugin_name
		);

		foreach ( $settings as $setting ) {
			$this->add_section_input( $section_slug, $setting );
		}
	}

	private function add_section_input( $section_slug, $args = array() ) {
		$option_name = $args['slug'];
		$field_name  = $args['field_name'];
		$type        = $args['type'];
		$callback    = false;

		$input_type = strtolower( $type );

		if ( $callback ) {
			register_setting(
				$section_slug,
				$option_name,
				array( $this, $callback )
			);
		} else {
			register_setting(
				$section_slug,
				$option_name
			);
		}

		switch ( $input_type ) {
			case 'text':
			case 'textarea':
			case 'checkbox':
				add_settings_field(
					$option_name,
					"<a href='#" . $option_name . "'>" . __( $field_name, $this->plugin_text_namespace ) . "</a>",
					array( $this, $this->callback_namespace . $input_type . '_cb' ),
					$this->plugin_name,
					$section_slug,
					array( 'name' => $option_name, 'field_name' => $field_name )
				);
				break;
			default:
				error_log( 'dp-theme-helper.admin received an invalid input type:' . $type );
				break;
		}
	}


	public function sanitize_checkbox( $val ) {
		$this->logger->addInfo( 'sanitizing checkbox', array( func_get_args(), ( isset( $val ) ) ) );

		return ( isset( $val ) ) ? 1 : 0;
	}

	/**
	 * Render the input field for API Key
	 *
	 * @since  1.0.0
	 */
	public function dp_theme_helper_checkbox_cb( $args ) {
		$option_name  = $args['name'];
		$option_value = get_option( $option_name );
		echo '<input type="checkbox" name="' . $option_name
		     . '" id="' . $option_name
		     . '" value="' . $option_value
		     . '" ' . ( $option_value ? 'checked' : '' )
		     . '/> ';
	}

	public function dp_theme_helper_text_cb( $args ) {
		$option_name  = $args['name'];
		$option_value = get_option( $option_name );
		echo '<input type="text" name="' . $option_name . '" id="' . $option_name . '" value="' . $option_value . '"/> ';
	}

	public function dp_theme_helper_textarea_cb( $args ) {
		$option_name  = $args['name'];
		$option_value = get_option( $option_name );
		echo '<textarea name="' . $option_name . '" id="' . $option_name . '">' . $option_value . '</textarea> ';
	}
}
