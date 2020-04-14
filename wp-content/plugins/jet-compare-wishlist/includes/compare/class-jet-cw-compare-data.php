<?php
/**
 * Compare Data Class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! session_id() ) {
	session_start();
}

if ( ! class_exists( 'Jet_CW_Compare_Data' ) ) {

	/**
	 * Define Jet_CW_Compare_Data class
	 */
	class Jet_CW_Compare_Data {

		/**
		 * Initalize integration hooks
		 *
		 * @return void
		 */
		public function __construct() {

			$this->localize_compare_data();

		}


		/**
		 * Update compare data
		 *
		 * @param $pid
		 * @param $context
		 *
		 * @return array
		 */
		function update_data_compare( $pid, $context ) {
			$compare_list  = $this->get_compare_list();
			$product_index = array_search( $pid, $compare_list );

			switch ( $context ) {
				case 'add':
					if ( ! $product_index ) {
						$compare_list[] = $pid;
					}
					break;
				case 'remove':
					if ( isset( $product_index ) ) {
						$index = array_search( $pid, $compare_list );
						unset( $compare_list[ $index ] );
					}
					break;
			}

			$this->set_compare_list( $compare_list );

			return $compare_list;

		}

		/**
		 * Returns compare list.
		 *
		 * @since 1.0.0
		 *
		 * @return array The array of product ids to compare.
		 */
		public function get_compare_list() {
			$compare_list = ! empty( $_SESSION['jet-compare-list'] ) ? $_SESSION['jet-compare-list'] : '';
			$save_for_logged_user = filter_var( jet_cw()->settings->get( 'save_user_compare_list' ), FILTER_VALIDATE_BOOLEAN );

			if ( is_user_logged_in() && $save_for_logged_user ){
				$compare_list = get_user_meta( get_current_user_id(), 'jet_compare_list', true );
			}

			if ( ! empty( $compare_list ) ) {
				$compare_list = explode( ':', $compare_list );
			} else {
				$compare_list = array();
			}

			return $compare_list;
		}

		/**
		 * Sets new list of products to compare.
		 *
		 * @since 1.0.0
		 *
		 * @param array $compare_list The new array of products to compare.
		 */
		public function set_compare_list( $compare_list ) {
			$max_compare_items = filter_var( jet_cw()->settings->get( 'compare_page_max_items' ), FILTER_VALIDATE_INT );
			$save_for_logged_user = filter_var( jet_cw()->settings->get( 'save_user_compare_list' ), FILTER_VALIDATE_BOOLEAN );

			if( $max_compare_items >= count( $compare_list ) ){
				$value                        = implode( ':', $compare_list );
				$_SESSION['jet-compare-list'] = $value;

				if ( is_user_logged_in() && $save_for_logged_user ){
					update_user_meta( get_current_user_id(), 'jet_compare_list' , $value );
				}

			}
		}

		/**
		 * Localize data for compare
		 */
		public function localize_compare_data() {

			$localized_data = apply_filters( 'jet-cw/compare/localized-data', array(
				'compareMaxItems'   => filter_var( jet_cw()->settings->get( 'compare_page_max_items' ), FILTER_VALIDATE_INT ),
				'compareItemsCount' => count( $this->get_compare_list() )
			) );

			jet_cw()->widgets_store->add_localized_data( $localized_data );

		}

	}

}