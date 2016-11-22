<?php
use Monolog\Logger;
use Monolog\Handler\ChromePHPHandler;

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

	private $option_namespace = 'dp_theme_helper';
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
		array_push( $this->message_queue, array(
			'text' => 'updated JSON',
			'type' => 'info'
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

		foreach ( $this->get_messages() as $message_meta ) {
			switch ( $message_meta['type'] ) {
				case 'info':
				case 'warning':
				case 'error':
				case 'success':
					echo $this->render_message( $message_meta );
					break;
				default:
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


	private function generate_option_namespace_suffix( $option_name ) {
		return '_' . sanitize_title_with_dashes( $option_name );
	}


	public function generate_option_name( $section_name, $option_name ) {
		return sanitize_title_with_dashes( $section_name . ' ' . $option_name );
	}


	private function add_setting( $option_name, $label = "New Option", $type = 'text', $callback = false ) {

		$input_type              = strtolower( $type );
		$option_namespace_suffix = $this->generate_option_namespace_suffix( $option_name );
		$option_lookup_name      = $this->option_namespace . $option_namespace_suffix;
		if ( $callback ) {
			register_setting(
				$this->plugin_name,
				$this->option_namespace . $option_namespace_suffix,
				array( $this, $callback )
			);
		} else {
			register_setting(
				$this->plugin_name,
				$this->option_namespace . $option_namespace_suffix
			);
		}

		switch ( $input_type ) {
			case 'text':
			case 'textarea':
			case 'checkbox':
				add_settings_field(
					$option_lookup_name,
					"<a href='#" . $option_lookup_name . "'>" . __( $label, $this->plugin_text_namespace ) . "</a>",
					array( $this, $this->option_namespace . '_' . strtolower( $type ) . '_cb' ),
					$this->plugin_name,
					$this->option_namespace . $this->generate_option_namespace_suffix( $this->main_settings_option_name ),
					array( 'name' => $option_lookup_name, 'label' => $label )
				);
				break;
			default:
				error_log( 'dp-theme-helper.admin received an invalid input type:' . $type );
				break;
		}
	}

	public function register_settings() {

		$theme_settings         = apply_filters( 'dp_theme_text', array() );
		$theme_section_settings = apply_filters( 'dp_theme_text_sections', array() );


		$this->parse_theme_settings( $theme_settings );
		$this->parse_theme_section_settings( $theme_section_settings );
	}


	private function parse_theme_settings( $theme_settings ) {
		add_settings_section(
			$this->option_namespace . $this->generate_option_namespace_suffix( $this->main_settings_option_name ),
			__( 'General', $this->plugin_text_namespace ),
			array( $this, 'options_heading' ),
			$this->plugin_name
		);

		foreach ( $theme_settings as $theme_setting ) {
			$slug     = $theme_setting['slug'];
			$label    = $theme_setting['label'] ? $theme_setting['label'] : 'Option';
			$type     = isset( $theme_setting['type'] ) ? $theme_setting['type'] : 'text';
			$callback = null;
			switch ( $type ) {
				case 'checkbox':
					$callback = 'sanitize_checkbox';
					break;
				default:
					break;
			}
			$this->add_setting( $slug, $label, $type, $callback );
		}
	}

	private function add_settings_from_args( $section_name, $args = array() ) {
		$this->logger->addInfo( 'received section setting @ ' . $section_name, $args );
		$option_name = $args['slug'];
		$label       = $args['label'];
		$type        = $args['type'];
		$callback    = false;

		$input_type              = strtolower( $type );
		$option_lookup_name      = $this->generate_option_name( $section_name, $option_name );

		$this->logger->addInfo( 'registering setting w/', array(
			$section_name,
			$option_lookup_name,
			$callback
		));
		if ( $callback ) {
			register_setting(
				$section_name,
				$option_lookup_name,
				array( $this, $callback )
			);
		} else {
			register_setting(
				$section_name,
				$option_lookup_name
			);
		}

		$this->logger->addInfo( 'adding section setting', array(
			$option_lookup_name,
			"<a href='#" . $option_lookup_name . "'>" . __( $label, $this->plugin_text_namespace ) . "</a>",
			array( $this, $this->option_namespace . '_' . $input_type . '_cb' ),
			$this->plugin_name,
			$section_name,
			array( 'name' => $option_lookup_name, 'label' => $label )
		) );
		switch ( $input_type ) {
			case 'text':
			case 'textarea':
			case 'checkbox':
				add_settings_field(
					$option_lookup_name,
					"<a href='#" . $option_lookup_name . "'>" . __( $label, $this->plugin_text_namespace ) . "</a>",
					array( $this, $this->option_namespace . '_' . $input_type . '_cb' ),
					$this->plugin_name,
					$section_name,
					array( 'name' => $option_lookup_name, 'label' => $label )
				);
				break;
			default:
				error_log( 'dp-theme-helper.admin received an invalid input type:' . $type );
				break;
		}
	}

	private function add_settings_section_from_args( $args = array() ) {
		$section_name  = $args['section_name'];
		$section_title = $args['section_label'];
		$settings      = $args['settings'];
		$section_slug  = $this->option_namespace . $this->generate_option_namespace_suffix( $section_name );

		array_push( $this->text_sections, $section_slug );
		$settings_section_args = array(
			$section_slug,
			__( $section_title, $this->plugin_text_namespace ),
			array( $this, 'options_heading' ),
			$this->plugin_name
		);
		$this->logger->addInfo( 'adding setting section', $settings_section_args );
		add_settings_section(
			$section_slug,
			__( $section_title, $this->plugin_text_namespace ),
			array( $this, 'options_heading' ),
			$this->plugin_name
		);

		foreach ( $settings as $setting ) {
			$this->add_settings_from_args( $section_slug, $setting );
		}
	}

	private function parse_theme_section_settings( $theme_section_settings ) {
		$this->logger->addInfo( 'parsing: ', $theme_section_settings );
		foreach ( $theme_section_settings as $theme_section_setting ) {
			$settings = array();

			foreach ( $theme_section_setting['settings'] as $theme_setting ) {
				$slug     = $theme_setting['slug'];
				$label    = $theme_setting['label'] ? $theme_setting['label'] : 'Option';
				$type     = isset( $theme_setting['type'] ) ? $theme_setting['type'] : 'text';
				$callback = null;
				switch ( $type ) {
					case 'checkbox':
						$callback = 'sanitize_checkbox';
						break;
					default:
						break;
				}
				array_push( $settings, array(
					'slug'     => $slug,
					'label'    => $label,
					'type'     => $type,
					'callback' => null
				) );
			}

			$result = array(
				'section_name'  => sanitize_title_with_dashes( $theme_section_setting['name'] ),
				'section_label' => $theme_section_setting['name'],
				'settings'      => $settings
			);
			$this->logger->addInfo( 'result: ', $result );
			$this->add_settings_section_from_args( $result );
		}
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function options_heading() {
		include_once 'partials/dp-theme-helper-admin-heading.php';
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
