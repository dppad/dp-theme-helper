<?php
if ( ! class_exists( 'DP_Option' ) ) {
	class DP_Option {
		protected $option_namespace = 'dp_theme_helper_';

		public function __construct( $option_name ) {
			$this->namespace = $this->option_namespace . sanitize_title_with_dashes( $option_name );
		}

		public function get_namespace() {
			return $this->namespace;
		}

		public function get_value() {
			global $logger;
			$logger->addInfo('loading: '. $this->namespace);
			return get_option( $this->namespace );
		}
	}
}

if ( ! class_exists( 'DP_Text_Option' ) ) {
	class DP_Text_Option extends DP_Option {
		private $section_name;
		private $field_name;

		public function __construct( $section_name, $field_name ) {
			$this->section_name = $section_name;
			$this->field_name   = $field_name;
			parent::__construct( $this->section_name . ' ' . $this->field_name );
		}

	}
}

