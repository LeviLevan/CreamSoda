<?php
/**
 * Jet Product Gallery tools class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Product_Gallery_Tools' ) ) {

	/**
	 * Define Jet_Woo_Product_Gallery_Tools class
	 */
	class Jet_Woo_Product_Gallery_Tools {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Returns columns classes string
		 *
		 * @param  [type] $columns [description]
		 *
		 * @return [type]          [description]
		 */
		public function col_classes( $columns = array() ) {

			$columns = wp_parse_args( $columns, array(
				'desk' => 1,
				'tab'  => 1,
				'mob'  => 1,
			) );

			$classes = array();

			foreach ( $columns as $device => $cols ) {
				if ( ! empty( $cols ) ) {
					$classes[] = sprintf( 'col-%1$s-%2$s', $device, $cols );
				}
			}

			return implode( ' ', $classes );
		}

		/**
		 * Returns disable columns gap nad rows gap classes string
		 *
		 * @param  string $use_cols_gap [description]
		 * @param  string $use_rows_gap [description]
		 *
		 * @return [type]               [description]
		 */
		public function gap_classes( $use_cols_gap = 'yes', $use_rows_gap = 'yes' ) {

			$result = array();

			foreach ( array( 'cols' => $use_cols_gap, 'rows' => $use_rows_gap ) as $element => $value ) {
				if ( 'yes' !== $value ) {
					$result[] = sprintf( 'disable-%s-gap', $element );
				}
			}

			return implode( ' ', $result );

		}

		/**
		 * Returns image size array in slug => name format
		 *
		 * @return  array
		 */
		public function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes  = get_intermediate_image_sizes();
			$result = array();

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				} else {
					$result[ $size ] = sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					);
				}
			}

			return array_merge( array( 'full' => esc_html__( 'Full', 'jet-woo-product-gallery' ), ), $result );
		}

		/**
		 * Returns icons data list.
		 *
		 * @return array
		 */
		public function get_theme_icons_data() {

			$default = array(
				'icons'  => false,
				'format' => 'fa %s',
				'file'   => false,
			);

			/**
			 * Filter default icon data before useing
			 *
			 * @var array
			 */
			$icon_data = apply_filters( 'jet-woo-product-gallery/controls/icon/data', $default );
			$icon_data = array_merge( $default, $icon_data );

			return $icon_data;
		}

		/**
		 * Returns allowed order by fields for options
		 *
		 * @return array
		 */
		public function verrtical_align_attr() {
			return array(
				'baseline'    => esc_html__( 'Baseline', 'jet-woo-product-gallery' ),
				'top'         => esc_html__( 'Top', 'jet-woo-product-gallery' ),
				'middle'      => esc_html__( 'Middle', 'jet-woo-product-gallery' ),
				'bottom'      => esc_html__( 'Bottom', 'jet-woo-product-gallery' ),
				'sub'         => esc_html__( 'Sub', 'jet-woo-product-gallery' ),
				'super'       => esc_html__( 'Super', 'jet-woo-product-gallery' ),
				'text-top'    => esc_html__( 'Text Top', 'jet-woo-product-gallery' ),
				'text-bottom' => esc_html__( 'Text Bottom', 'jet-woo-product-gallery' ),
			);
		}

		/**
		 * Returns array with numbers in $index => $name format for numeric selects
		 *
		 * @param  integer $to Max numbers
		 *
		 * @return array
		 */
		public function get_select_range( $to = 10 ) {
			$range = range( 1, $to );

			return array_combine( $range, $range );
		}

		/**
		 * Rturns image tag or raw SVG
		 *
		 * @param  string $url image URL.
		 * @param  array $attr [description]
		 *
		 * @return string
		 */
		public function get_image_by_url( $url = null, $attr = array() ) {

			$url = esc_url( $url );

			if ( empty( $url ) ) {
				return;
			}

			$ext  = pathinfo( $url, PATHINFO_EXTENSION );
			$attr = array_merge( array( 'alt' => '' ), $attr );

			if ( 'svg' !== $ext ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			$base_url = network_site_url( '/' );
			$svg_path = str_replace( $base_url, ABSPATH, $url );
			$key      = md5( $svg_path );
			$svg      = get_transient( $key );

			if ( ! $svg ) {
				$svg = file_get_contents( $svg_path );
			}

			if ( ! $svg ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			set_transient( $key, $svg, DAY_IN_SECONDS );

			unset( $attr['alt'] );

			return sprintf( '<div%2$s>%1$s</div>', $svg, $this->get_attr_string( $attr ) );;
		}

		/**
		 * Return attributes string from attributes array.
		 *
		 * @param  array  $attr Attributes string.
		 * @return string
		 */
		public function get_attr_string( $attr = array() ) {

			if ( empty( $attr ) || ! is_array( $attr ) ) {
				return;
			}

			$result = '';

			foreach ( $attr as $key => $value ) {
				$result .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			return $result;
		}

		/**
		 * Get post types options list
		 *
		 * @return array
		 */
		public function get_post_types() {

			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			$deprecated = apply_filters(
				'jet-woo-product-gallery/post-types-list/deprecated',
				array( 'attachment', 'elementor_library' )
			);

			$result = array();

			if ( empty( $post_types ) ) {
				return $result;
			}

			foreach ( $post_types as $slug => $post_type ) {

				if ( in_array( $slug, $deprecated ) ) {
					continue;
				}

				$result[ $slug ] = $post_type->label;

			}

			return $result;

		}

		/**
		 * Return availbale arrows list
		 * @return [type] [description]
		 */
		public function get_available_prev_arrows_list() {

			return apply_filters(
				'jet-woo-product-gallery/carousel/available-arrows/prev',
				array(
					'fa fa-angle-left'          => __( 'Angle', 'jet-woo-product-gallery' ),
					'fa fa-chevron-left'        => __( 'Chevron', 'jet-woo-product-gallery' ),
					'fa fa-angle-double-left'   => __( 'Angle Double', 'jet-woo-product-gallery' ),
					'fa fa-arrow-left'          => __( 'Arrow', 'jet-woo-product-gallery' ),
					'fa fa-caret-left'          => __( 'Caret', 'jet-woo-product-gallery' ),
					'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jet-woo-product-gallery' ),
					'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jet-woo-product-gallery' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jet-woo-product-gallery' ),
					'fa fa-caret-square-o-left' => __( 'Caret Square', 'jet-woo-product-gallery' ),
				)
			);

		}

		/**
		 * Return availbale arrows list
		 * @return [type] [description]
		 */
		public function get_available_next_arrows_list() {

			return apply_filters(
				'jet-woo-product-gallery/carousel/available-arrows/next',
				array(
					'fa fa-angle-right'          => __( 'Angle', 'jet-woo-product-gallery' ),
					'fa fa-chevron-right'        => __( 'Chevron', 'jet-woo-product-gallery' ),
					'fa fa-angle-double-right'   => __( 'Angle Double', 'jet-woo-product-gallery' ),
					'fa fa-arrow-right'          => __( 'Arrow', 'jet-woo-product-gallery' ),
					'fa fa-caret-right'          => __( 'Caret', 'jet-woo-product-gallery' ),
					'fa fa-long-arrow-right'     => __( 'Long Arrow', 'jet-woo-product-gallery' ),
					'fa fa-arrow-circle-right'   => __( 'Arrow Circle', 'jet-woo-product-gallery' ),
					'fa fa-chevron-circle-right' => __( 'Chevron Circle', 'jet-woo-product-gallery' ),
					'fa fa-caret-square-o-right' => __( 'Caret Square', 'jet-woo-product-gallery' ),
				)
			);

		}

		/**
		 * Trim text
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function trim_text( $text = '', $length = - 1, $trimmed_type = 'word', $after ) {

			if ( '' === $text ) {
				return $text;
			}

			if ( 0 === $length ) {
				return '';
			}

			if ( - 1 !== $length ) {
				if ( 'word' === $trimmed_type ) {
					$text = wp_trim_words( $text, $length, $after );
				} else {
					$text = wp_html_excerpt( $text, $length, $after );
				}
			}

			return $text;
		}

		public function is_builder_content_save() {

			if ( ! isset( $_REQUEST['action'] ) || 'elementor_ajax' !== $_REQUEST['action'] ) {
				return false;
			}

			if ( empty( $_REQUEST['actions'] ) ) {
				return false;
			}

			if ( false === strpos( $_REQUEST['actions'], 'save_builder' ) ) {
				return false;
			}

			return true;

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}

			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Woo_Product_Gallery_Tools
 *
 * @return object
 */
function jet_woo_product_gallery_tools() {
	return Jet_Woo_Product_Gallery_Tools::get_instance();
}
