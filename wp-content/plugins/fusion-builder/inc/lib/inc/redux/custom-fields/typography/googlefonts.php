<?php

if ( ! class_exists( 'Fusion_Redux_Get_GoogleFonts' ) ) {
	class Fusion_Redux_Get_GoogleFonts {

		private static $instance = null;

		public $fonts = array();

		private function __construct() {
			$this->fonts = $this->get_fonts_array();
		}

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function get_fonts_array() {
			// If we have it cached, then return it from a transient.
			$transient = get_site_transient( 'fusion_redux_googlefonts' );
			if ( $transient ) {
				return $transient;
			}
			// If it's not cached then cache for a week and return it.
			$fonts = $this->get_from_api();
			set_site_transient( 'fusion_redux_googlefonts', $fonts, 7 * 24 * HOUR_IN_SECONDS );
			return $fonts;
		}

		private function get_from_api() {
			// The path to the PHP array file containing the fonts.
			// This is auto-generated by running "grunt googlefonts"
			$path = FUSION_LIBRARY_PATH . '/inc/googlefonts-array.php';
			// Get the contents of the file
			$fonts_array = include $path;

			$final_fonts = array();
			if ( isset( $fonts_array['items'] ) ) {
				$all_variants = array();

				foreach ( $fonts_array['items'] as $font ) {
					// If font-family is not set then skip this item.
					if ( ! isset( $font['family'] ) ) {
						continue;
					}
					$final_fonts[ $font['family'] ] = array(
						'variants' => array(),
					);
					if ( isset( $font['variants'] ) && is_array( $font['variants'] ) ) {
						foreach ( $font['variants'] as $variant ) {
							$final_fonts[ $font['family'] ]['variants'][] = $this->convert_font_variants( $variant );
						}
					}
				}
			}
			return $final_fonts;
		}

		private function convert_font_variants( $variant ) {
			$variants = array(
				'regular'   => array( 'id' => '400',       'name' => esc_attr__( 'Normal 400', 'fusion-builder' ) ),
				'400italic' => array( 'id' => '400italic', 'name' => esc_attr__( 'Normal 400 Italic', 'fusion-builder' ) ),
				'italic'    => array( 'id' => '400italic', 'name' => esc_attr__( 'Normal 400 Italic', 'fusion-builder' ) ),
				'100'       => array( 'id' => '100',       'name' => esc_attr__( 'Ultra-Light 100', 'fusion-builder' ) ),
				'200'       => array( 'id' => '200',       'name' => esc_attr__( 'Light 200', 'fusion-builder' ) ),
				'300'       => array( 'id' => '300',       'name' => esc_attr__( 'Book 300', 'fusion-builder' ) ),
				'400'       => array( 'id' => '400',       'name' => esc_attr__( 'Normal 400', 'fusion-builder' ) ),
				'500'       => array( 'id' => '500',       'name' => esc_attr__( 'Medium 500', 'fusion-builder' ) ),
				'600'       => array( 'id' => '600',       'name' => esc_attr__( 'Semi-Bold 600', 'fusion-builder' ) ),
				'700'       => array( 'id' => '700',       'name' => esc_attr__( 'Bold 700', 'fusion-builder' ) ),
				'700italic' => array( 'id' => '700italic', 'name' => esc_attr__( 'Bold 700 Italic', 'fusion-builder' ) ),
				'900'       => array( 'id' => '900',       'name' => esc_attr__( 'Ultra-Bold 900', 'fusion-builder' ) ),
				'900italic' => array( 'id' => '900italic', 'name' => esc_attr__( 'Ultra-Bold 900 Italic', 'fusion-builder' ) ),
				'100italic' => array( 'id' => '100italic', 'name' => esc_attr__( 'Ultra-Light 100 Italic', 'fusion-builder' ) ),
				'300italic' => array( 'id' => '300italic', 'name' => esc_attr__( 'Book 300 Italic', 'fusion-builder' ) ),
				'500italic' => array( 'id' => '500italic', 'name' => esc_attr__( 'Medium 500 Italic', 'fusion-builder' ) ),
				'800'       => array( 'id' => '800',       'name' => esc_attr__( 'Extra-Bold 800', 'fusion-builder' ) ),
				'800italic' => array( 'id' => '800italic', 'name' => esc_attr__( 'Extra-Bold 800 Italic', 'fusion-builder' ) ),
				'600italic' => array( 'id' => '600italic', 'name' => esc_attr__( 'Semi-Bold 600 Italic', 'fusion-builder' ) ),
				'200italic' => array( 'id' => '200italic', 'name' => esc_attr__( 'Light 200 Italic', 'fusion-builder' ) ),
			);
			if ( array_key_exists( $variant, $variants ) ) {
				return $variants[ $variant ];
			}
			return array(
				'id'   => $variant,
				'name' => $variant,
			);
		}
	}
}

$fonts = Fusion_Redux_Get_GoogleFonts::get_instance();
return $fonts->fonts;
