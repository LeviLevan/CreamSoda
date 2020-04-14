<?php
/**
 * Cherry addons functions class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_CW_Widgets_Functions' ) ) {

	/**
	 * Define Jet_CW_Widgets_Functions class
	 */
	class Jet_CW_Widgets_Functions {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		public function get_add_to_compare_button( $display_settings, $product_id ) {
			$products_in_compare = jet_cw()->compare_data->get_compare_list();
			$is_compare_product  = in_array( $product_id, $products_in_compare );
			$widget_id           = $display_settings['_widget_id'];

			$button_classes = array(
				'jet-compare-button__link',
				'jet-compare-button__link--icon-' . $display_settings['button_icon_position'],
			);

			$compare_page_id   = filter_var( jet_cw()->settings->get( 'compare_page' ), FILTER_VALIDATE_INT );
			$compare_page_link = '#';

			if ( $compare_page_id && $is_compare_product ) {
				$compare_page_link = esc_url( get_page_link( $compare_page_id ) );
			}

			if ( $is_compare_product ) {
				$button_classes[] = 'added-to-compare';
			}

			?>
				<a href="<?php echo $compare_page_link ?>" class="<?php echo implode( ' ', $button_classes ); ?>" data-widget-type="jet-compare-button" data-product-id="<?php echo $product_id ?>" data-widget-id="<?php echo $widget_id ?>">
					<div class="jet-compare-button__plane jet-compare-button__plane-normal"></div>
					<div class="jet-compare-button__plane jet-compare-button__plane-added"></div>
					<div class="jet-compare-button__state jet-compare-button__state-normal">
						<?php
						if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<span class="jet-compare-button__icon"><i class="%s"></i></span>', $display_settings['button_icon_normal'] );
						}
						printf( '<span class="jet-compare-button__label">%s</span>', $display_settings['button_label_normal'] );
						?>
					</div>
					<div class="jet-compare-button__state jet-compare-button__state-added">
						<?php
						if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<span class="jet-compare-button__icon"><i class="%s"></i></span>', $display_settings['button_icon_added'] );
						}
						printf( '<span class="jet-compare-button__label">%s</span>', $display_settings['button_label_added'] );
						?>
					</div>
				</a>
			<?

		}

		public function get_add_to_wishlist_button( $display_settings, $product_id ) {
			$products_in_wishlist = jet_cw()->wishlist_data->get_wish_list();

			$is_wishlist_product = in_array( $product_id, $products_in_wishlist );
			$widget_id           = $display_settings['_widget_id'];

			$button_classes = array(
				'jet-wishlist-button__link',
				'jet-wishlist-button__link--icon-' . $display_settings['button_icon_position'],
			);

			$wishlist_page_id   = filter_var( jet_cw()->settings->get( 'wishlist_page' ), FILTER_VALIDATE_INT );
			$wishlist_page_link = '#';

			if ( $wishlist_page_id && $is_wishlist_product ) {
				$wishlist_page_link = esc_url( get_page_link( $wishlist_page_id ) );
			}

			if ( $is_wishlist_product ) {
				$button_classes[] = 'added-to-wishlist';
			}

			?>
			<a href="<?php echo $wishlist_page_link ?>" class="<?php echo implode( ' ', $button_classes ); ?>" data-widget-type="jet-wishlist-button" data-product-id="<?php echo $product_id ?>" data-widget-id="<?php echo $widget_id ?>">
				<div class="jet-wishlist-button__plane jet-wishlist-button__plane-normal"></div>
				<div class="jet-wishlist-button__plane jet-wishlist-button__plane-added"></div>
				<div class="jet-wishlist-button__state jet-wishlist-button__state-normal">
					<?php
					if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
						printf( '<span class="jet-wishlist-button__icon"><i class="%s"></i></span>', $display_settings['button_icon_normal'] );
					}
					printf( '<span class="jet-wishlist-button__label">%s</span>', $display_settings['button_label_normal'] );
					?>
				</div>
				<div class="jet-wishlist-button__state jet-wishlist-button__state-added">
					<?php
					if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
						printf( '<span class="jet-wishlist-button__icon"><i class="%s"></i></span>', $display_settings['button_icon_added'] );
					}
					printf( '<span class="jet-wishlist-button__label">%s</span>', $display_settings['button_label_added'] );
					?>
				</div>
			</a>
			<?

		}

		public function get_compare_count_button( $display_settings ) {
			$products_in_compare = jet_cw()->compare_data->get_compare_list();
			$count               = sprintf( $display_settings['count_format'], count( $products_in_compare ) );
			$widget_id           = $display_settings['_widget_id'];

			$button_classes = array(
				'jet-compare-count-button__link',
				'jet-compare-count-button--icon-' . $display_settings['button_icon_position'],
				'jet-compare-count-button--count-' . $display_settings['count_position'],
			);

			$compare_page_id   = filter_var( jet_cw()->settings->get( 'compare_page' ), FILTER_VALIDATE_INT );
			$compare_page_link = '#';

			if ( $compare_page_id ) {
				$compare_page_link = esc_url( get_page_link( $compare_page_id ) );
			}

			?>
				<a href="<?php echo $compare_page_link ?>" class="<?php echo implode( ' ', $button_classes ); ?>" data-widget-type="jet-compare-count-button" data-widget-id="<?php echo $widget_id ?>">
					<div class="jet-compare-count-button__content">
						<?php
						if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<span class="jet-compare-count-button__icon"><i class="%s"></i></span>', $display_settings['button_icon'] );
						}

						printf( '<span class="jet-compare-count-button__label">%s</span>', $display_settings['button_label'] );

						if ( filter_var( $display_settings['show_count'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<div class="jet-compare-count-button__count"><span>%s</span></div>', $count );
						}
						?>
					</div>
				</a>
			<?

		}

		public function get_wishlist_count_button( $display_settings ) {
			$products_in_wishlist = jet_cw()->wishlist_data->get_wish_list();
			$count                = sprintf( $display_settings['count_format'], count( $products_in_wishlist ) );
			$widget_id            = $display_settings['_widget_id'];

			$button_classes = array(
				'jet-wishlist-count-button__link',
				'jet-wishlist-count-button--icon-' . $display_settings['button_icon_position'],
				'jet-wishlist-count-button--count-' . $display_settings['count_position'],
			);

			$wishlist_page_id   = filter_var( jet_cw()->settings->get( 'wishlist_page' ), FILTER_VALIDATE_INT );
			$wishlist_page_link = '#';

			if ( $wishlist_page_id ) {
				$wishlist_page_link = esc_url( get_page_link( $wishlist_page_id ) );
			}

			?>
			<a href="<?php echo $wishlist_page_link ?>" class="<?php echo implode( ' ', $button_classes ); ?>" data-widget-type="jet-wishlist-count-button" data-widget-id="<?php echo $widget_id ?>">
					<div class="jet-wishlist-count-button__content">
						<?php
						if ( filter_var( $display_settings['use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<span class="jet-wishlist-count-button__icon"><i class="%s"></i></span>', $display_settings['button_icon'] );
						}

						printf( '<span class="jet-wishlist-count-button__label">%s</span>', $display_settings['button_label'] );

						if ( filter_var( $display_settings['show_count'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<div class="jet-wishlist-count-button__count"><span>%s</span></div>', $count );
						}
						?>
					</div>
			</a>
			<?

		}

		public function get_widget_compare_table( $widget_settings ) {
			$products         = $this->get_products_added_to_compare();
			$widget_id        = $widget_settings['_widget_id'];
			$table_data_items = $widget_settings['compare_table_data'];
			$empty_text       = $widget_settings['empty_compare_text'];

			$table_wrapper_classes = array(
				'jet-compare-table__wrapper'
			);

			if ( isset( $widget_settings['scrolled_table'] ) && filter_var( $widget_settings['scrolled_table'], FILTER_VALIDATE_BOOLEAN ) && ! empty( $widget_settings['scrolled_table_on'] ) ) {
				foreach ( $widget_settings['scrolled_table_on'] as $device_type ) {
					$table_wrapper_classes[] = 'jet-compare-table-responsive-' . $device_type;
				}
			}

			if ( empty( $products ) ) {
				$this->get_no_product_in_compare_content( $table_wrapper_classes, $widget_id, $empty_text );

				return;
			}

			?>
				<div class="<?php echo implode( ' ', $table_wrapper_classes ) ?>" data-widget-type="jet-compare" data-widget-id="<?php echo $widget_id ?>">
					<table class="jet-compare-table woocommerce">
						<tbody class="jet-compare-table-body">
						  <?php
						  foreach ( $table_data_items as $table_data_item ) {
							  $data_type = $table_data_item['compare_table_data_type'];
							  if ( 'attributes' === $data_type ) {
								  $this->get_compare_table_rows_content_attributes( $table_data_item, $products );
							  } else {
								  $this->get_compare_table_rows_content( $table_data_item, $products );
							  }
						  }
						  ?>
						</tbody>
					</table>
				</div>
			<?php
		}

		public function get_no_product_in_compare_content( $table_wrapper_classes, $widget_id, $empty_text ) {
			?>
					<div class="<?php echo implode( ' ', $table_wrapper_classes ) ?>" data-widget-type="jet-compare" data-widget-id="<?php echo $widget_id ?>">
						<h5 class="jet-compare-table-empty"><?php echo $empty_text; ?></h5>
					</div>
			<?php
		}

		public function get_compare_table_rows_content( $display_settings = array(), $products ) {
			?>
				<tr class="jet-compare-table-row">
					<th class="jet-compare-table-heading"><?php echo $display_settings['compare_table_data_title'] ?></th>
					  <?php
					  foreach ( $products as $product ) {
						  $function_name = 'get_' . $display_settings['compare_table_data_type'];
						  echo '<td class="jet-compare-table-cell jet-compare-item" data-product-id="' . $product->get_id() . '">';
						  echo jet_cw_functions()->$function_name( $product, $display_settings );
						  echo '</td>';
					  }
					  ?>
				</tr>
			<?php
		}

		public function get_compare_table_rows_content_attributes( $display_settings = array(), $products ) {
			$attributes = jet_cw_functions()->get_visible_products_attributes( $products );

			foreach ( $attributes as $key => $value ) {
				echo '<tr class="jet-compare-table-row">';
				echo '<th class="jet-compare-table-heading">' . $value . '</th>';

				foreach ( $products as $product ) {
					$has_attributes = $product->has_attributes();

					if ( $has_attributes ) {
						$attributes_value = $product->get_attribute( $key );
						$attributes_value = str_replace( "|", ",", $attributes_value );
						$attributes_value = explode( ',', $attributes_value );
						$attributes_value = '<div class="jet-cw-attributes"><span>' . implode( '</span>&#44;<span>', $attributes_value ) . '</span></div>';

						echo '<td class="jet-compare-table-cell jet-compare-item" data-product-id="' . $product->get_id() . '">' . $attributes_value . '</td>';
					} else {
						echo '<td class="jet-compare-table-cell jet-compare-item jet-compare-item--empty" data-product-id="' . $product->get_id() . '">-</td>';
					}

				}

				echo '</tr>';
			}
		}

		public function get_products_added_to_compare() {
			$products_in_compare = jet_cw()->compare_data->get_compare_list();

			if ( empty( $products_in_compare ) ) {
				return;
			}

			$args = array(
				'include' => $products_in_compare,
				'limit'   => 4,
			);

			$products = wc_get_products( $args );

			return $products;

		}

		public function get_widget_wishlist( $widget_settings ) {
			$products   = $this->get_products_added_to_wishlist();
			$widget_id  = $widget_settings['_widget_id'];
			$empty_text = $widget_settings['empty_wishlist_text'];

			$wishlist_classes = array(
				'jet-wishlist__content',
				'woocommerce',
			);

			$col_classes = array(
				jet_cw_tools()->col_classes( array(
					'desk' => $widget_settings['wishlist_columns'],
					'tab'  => $widget_settings['wishlist_columns_tablet'],
					'mob'  => $widget_settings['wishlist_columns_mobile'],
				) )
			);

			if ( empty( $products ) ) {
				$this->get_no_product_in_wishlist_content( $wishlist_classes, $widget_id, $empty_text );

				return;
			}

			?>
					<div class="<?php echo implode( ' ', $wishlist_classes ) ?>" data-widget-type="jet-wishlist" data-widget-id="<?php echo $widget_id ?>">
						<div class="cw-col-row jet-wishlist-thumbnail-<?php echo $widget_settings['thumbnail_position'] ?>">
							<?php foreach ( $products as $product ): ?>
									<div class="<?php echo implode( ' ', $col_classes ); ?>">
										<div class="jet-wishlist-item">
											<?php if ( 'default' !== $widget_settings['thumbnail_position'] ) : ?>
													<div class="jet-wishlist-item__thumbnail">
							              <?php echo jet_cw_functions()->get_thumbnail( $product, $widget_settings ); ?>
													</div>
											<?php endif; ?>
											<div class="jet-wishlist-item__content">
													<?php echo jet_cw_functions()->get_wishlist_remove_button( $product, $widget_settings ); ?>
													<?php if ( 'default' === $widget_settings['thumbnail_position'] ) : ?>
													<?php echo jet_cw_functions()->get_thumbnail( $product, $widget_settings ); ?>
										  <?php endif; ?>
										  <?php echo jet_cw_functions()->get_title( $product, $widget_settings ); ?>
										  <?php echo jet_cw_functions()->get_add_to_cart_button( $product, $widget_settings ); ?>
										  <?php echo jet_cw_functions()->get_price( $product, $widget_settings ); ?>
										  <?php echo jet_cw_functions()->get_rating( $product, $widget_settings ); ?>
											</div>
										</div>
									</div>
				<?php endforeach; ?>
						</div>
					</div>
			<?php
		}

		public function get_no_product_in_wishlist_content( $wishlist_classes, $widget_id, $empty_text ) {
				?>
					<div class="<?php echo implode( ' ', $wishlist_classes ) ?>" data-widget-type="jet-wishlist" data-widget-id="<?php echo $widget_id ?>">
					<h5 class="jet-wishlist-empty"><?php echo $empty_text ?></h5>
					</div>
				<?php
		}

		public function get_products_added_to_wishlist() {
			$products_in_wishlist = jet_cw()->wishlist_data->get_wish_list();

			if ( empty( $products_in_wishlist ) ) {
				return;
			}

			$args = array(
			  'status' => 'publish',
			  'limit' => 100,
				'include' => $products_in_wishlist,
			);

			$products = wc_get_products( $args );

			return $products;

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
 * Returns instance of Jet_CW_Widgets_Functions
 *
 * @return object
 */
function jet_cw_widgets_functions() {
	return Jet_CW_Widgets_Functions::get_instance();
}
