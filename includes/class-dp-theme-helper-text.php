<?php
if (!class_exists('DP_Text')){
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
	}
}
