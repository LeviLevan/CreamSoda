<?php
/**
 * Wishlist Data Class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! session_id() ) {
	session_start();
}

if ( ! class_exists( 'Jet_CW_Wishlist_Data' ) ) {

	/**
	 * Define Jet_CW_Wishlist_Data class
	 */
	class Jet_CW_Wishlist_Data {

		/**
		 * Initalize integration hooks
		 *
		 * @return void
		 */
		public function __construct() {

			$this->localize_wishlist_data();

		}

		/**
		 * Update wishlist data
		 *
		 * @param $pid
		 * @param $context
		 *
		 * @return array
		 */
		function update_data_wishlist( $pid, $context ) {
			$wishlist_list  = $this->get_wish_list();
			$product_index = array_search( $pid, $wishlist_list );

			switch ( $context ) {
				case 'add':
					if ( ! $product_index ) {
						$wishlist_list[] = $pid;
					}
					break;
				case 'remove':
					if ( isset( $product_index ) ) {
						$index = array_search( $pid, $wishlist_list );
						unset( $wishlist_list[ $index ] );
					}
					break;
			}

			$this->set_wish_list( $wishlist_list );

			return $wishlist_list;

		}

		/**
		 * Returns wishlist list.
		 *
		 * @since 1.0.0
		 *
		 * @return array The array of product ids to wishlist.
		 */
		public function get_wish_list() {
			$wishlist_list = ! empty( $_SESSION['jet-wish-list'] ) ? $_SESSION['jet-wish-list'] : '';
			$save_for_logged_user = filter_var( jet_cw()->settings->get( 'save_user_wish_list' ), FILTER_VALIDATE_BOOLEAN );

			if ( is_user_logged_in() && $save_for_logged_user ){
				$wishlist_list = get_user_meta( get_current_user_id(), 'jet_wish_list', true );
			}

			if ( ! empty( $wishlist_list ) ) {
				$wishlist_list = explode( ':', $wishlist_list );
			} else{
				$wishlist_list = array();
			}

			return $wishlist_list;
		}

		/**
		 * Sets new list of products to wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param array $wishlist_list The new array of products to wishlist.
		 */
		public function set_wish_list( $wishlist_list ) {
			$save_for_logged_user = filter_var( jet_cw()->settings->get( 'save_user_wish_list' ), FILTER_VALIDATE_BOOLEAN );
			$value                        = implode( ':', $wishlist_list );
			$_SESSION['jet-wish-list'] = $value;

			if ( is_user_logged_in() && $save_for_logged_user ){
				update_user_meta( get_current_user_id(), 'jet_wish_list' , $value );
			}
		}

		/**
		 * Localize data for wishlist
		 */
		public function localize_wishlist_data() {

			$localized_data = apply_filters( 'jet-cw/wishlist/localized-data', array(
				'wishlistItemsCount' => count( $this->get_wish_list() )
			) );

			jet_cw()->widgets_store->add_localized_data( $localized_data );

		}

	}

}