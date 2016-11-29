<?php
require_once __DIR__ . "/class-dp-theme-helper-option.php";

if ( ! class_exists( 'DP_Text' ) ) {
	class DP_Text {

		public function __construct( $section_name, $field_name, $slug = null, $type = 'textarea' ) {
			$this->section_name = $section_name;
			$this->field_name   = $field_name;
			$this->option       = new DP_Text_Option( $this->section_name, $this->field_name );

			if ( $slug == null ) {
				$slug = sanitize_title_with_dashes( $field_name );
			}
			$this->slug = $slug;
			$this->type = $type;
		}


		public function register_by_filter( $theme_texts ) {
			if ( ! isset( $theme_texts[ $this->section_name ] ) ) {
				$theme_texts[ $this->section_name ] = array(
					'name'     => $this->section_name,
					'settings' => array()
				);
			}
			array_push( $theme_texts[ $this->section_name ]['settings'], array(
				'type'       => $this->type,
				'slug'       => $this->slug,
				'field_name' => $this->field_name
			) );

			return $theme_texts;
		}


		public function register() {

			$section_name    = $this->section_name;
			$field_name      = $this->field_name;
			$type            = $this->type;
			$text_fields_str = get_option( DP_THEME_TEXT_CACHE );
			if ( null === $text_fields_str ) {
				$text_fields = (object) array();
			} else {
				$text_fields = json_decode( $text_fields_str );
			}
			if ( ! isset( $text_fields->$section_name ) ) {
				$text_fields->$section_name = (object) array();
			}
			$text_fields->$section_name->$field_name = $type;
			update_option( DP_THEME_TEXT_CACHE, json_encode( $text_fields ) );
		}

		public function get_value() {
			return $this->option->get_value();
		}
	}
}
