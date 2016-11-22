<?php
if ( ! class_exists( 'DP_Text' ) ) {
	class DP_Text {

		public function __construct( $label, $slug = null, $type = 'text' ) {
			$this->label = $label;

			if ( $slug == null ) {
				$slug = sanitize_title_with_dashes( $label );
			}
			$this->slug = $slug;
			$this->type = $type;
		}

		public function register( $theme_texts ) {
			array_push( $theme_texts, array(
				'type'  => $this->type,
				'slug'  => $this->slug,
				'label' => $this->label
			) );

			return $theme_texts;
		}

		public function registerToSectionSettings( $theme_texts ) {
			array_push( $theme_texts['settings'], array(
				'type'  => $this->type,
				'slug'  => $this->slug,
				'label' => $this->label
			) );

			return $theme_texts;
		}
	}

}

if ( ! class_exists( 'DP_Section_Text' ) ) {
	class DP_Section_Text {

		public function __construct( $section_name, $label, $slug = null, $type = 'text' ) {
			$this->section_name = $section_name;
			$this->label        = $label;

			if ( $slug == null ) {
				$slug = sanitize_title_with_dashes( $label );
			}
			$this->slug = $slug;
			$this->type = $type;
		}


		public function register( $theme_texts ) {
			if ( ! isset( $theme_texts[ $this->section_name ] ) ) {
				$theme_texts[ $this->section_name ] = array(
					'name'     => $this->section_name,
					'settings' => array()
				);
			}
			array_push( $theme_texts[ $this->section_name ]['settings'], array(
				'type'  => $this->type,
				'slug'  => $this->slug,
				'label' => $this->label
			) );

			return $theme_texts;
		}
	}
}
