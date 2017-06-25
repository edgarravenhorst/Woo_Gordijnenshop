<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSSV_Product_Variation.
 *
 * @class	Iconic_WSSV_Product_Variation
 * @version  1.0.0
 * @author   Iconic
 */
class Iconic_WSSV_Product_Variation {

	/**
	 * Set catalog visibility.
	 *
	 * @param int $variation_id
	 * @param array $visibility
	 *
	 * @return bool
	 */
	public static function set_visibility( $variation_id, $visibility = null ) {
		$set_visibility = true;
		$visibility = is_null( $visibility ) ? self::get_visibility( $variation_id ) : $visibility;

		update_post_meta( $variation_id, '_visibility', $visibility );

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$set_visibility = false;
			$variation = wc_get_product( $variation_id );
			$terms = array();
			$visibility = implode( '-', $visibility );

			switch ( $visibility ) {
				case 'catalog-filtered' :
					$terms[] = "exclude-from-search";
					break;
				case 'catalog-search' :
					$terms[] = "exclude-from-filtered";
					break;
				case 'catalog' :
					$terms[] = "exclude-from-search";
					$terms[] = "exclude-from-filtered";
					break;
				case 'filtered-search' :
					$terms[] = "exclude-from-catalog";
					break;
				case 'search' :
					$terms[] = "exclude-from-catalog";
					$terms[] = "exclude-from-filtered";
					break;
				case 'filtered' :
					$terms[] = "exclude-from-catalog";
					$terms[] = "exclude-from-search";
					break;
				case 'hidden' :
					$terms[] = "exclude-from-catalog";
					$terms[] = "exclude-from-search";
					$terms[] = "exclude-from-filtered";
					break;
			}

			if( $variation ) {
				$stock_status = $variation->get_stock_status();
				if( $stock_status === "outofstock" ) {
					$terms[] = "outofstock";
				}
			}

			if ( ! is_wp_error( wp_set_post_terms( $variation_id, $terms, 'product_visibility', false ) ) ) {
				delete_transient( 'wc_featured_products' );
				do_action( 'woocommerce_product_set_visibility', $variation_id, $terms );
				$set_visibility = true;
			}
		}

		return $set_visibility;
	}

	/**
	 * Set featured visibility.
	 *
	 * @param int $variation_id
	 * @param bool $featured
	 *
	 * @return bool
	 */
	public static function set_featured_visibility( $variation_id, $featured = null ) {
		$set_fetaured = true;
		$featured = is_null( $featured ) ? Iconic_WSSV_Helpers::string_to_bool( get_post_meta( $variation_id, '_featured', true ) ) : $featured;

		if( $featured ) {
            update_post_meta( $variation_id, '_featured', "yes" );
        } else {
	        delete_post_meta( $variation_id, '_featured' );
        }

        if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
	        if( $featured ) {
		        $set_fetaured = wp_set_object_terms( $variation_id, 'featured', 'product_visibility', true );
	        } else {
		        $set_fetaured = wp_remove_object_terms( $variation_id, 'featured', 'product_visibility' );
	        }
	    }

	    if ( is_wp_error( $set_fetaured ) ) {
		    return false;
		}

	    delete_transient( 'wc_featured_products' );

	    return true;
	}

	/**
	 * Get visibility.
	 *
	 * @param int $variation_id
	 * @return string
	 */
	public static function get_visibility( $variation_id ) {
		$visibility = get_post_meta( $variation_id, '_visibility', true );

		if( ! is_array( $visibility ) || empty( $visibility ) ) {
			return array( "hidden" );
		}

		sort( $visibility );

		return $visibility;
	}

	/**
	 * Set total sales.
	 *
	 * @param int $variation_id
	 *
	 * @return bool
	 */
	public static function set_total_sales( $variation_id ) {
		$total_sales = self::get_variation_sales( $variation_id );
        update_post_meta( $variation_id, 'total_sales', $total_sales );

        do_action( 'iconic_wssv_set_total_sales', $variation_id, $total_sales );

	    return true;
	}

	/**
     * Get total variation sales
     *
     * @param int $variation_id
     * @return int
     */
    public static function get_variation_sales( $variation_id ) {

        global $wpdb;

        $total_sales = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT SUM(`quantities`.`meta_value`)
                FROM `{$wpdb->prefix}woocommerce_order_itemmeta` as `itemmeta`
                 LEFT JOIN  `{$wpdb->prefix}woocommerce_order_itemmeta` AS  `quantities` ON `itemmeta`.`order_item_id` = `quantities`.`order_item_id`
                  AND `quantities`.`meta_key` = '_qty'
                 LEFT JOIN `{$wpdb->prefix}woocommerce_order_items` as `items` ON `items`.`order_item_id`=`itemmeta`.`order_item_id`
                WHERE `itemmeta`.`meta_key` = '_variation_id'
                 AND `itemmeta`.`meta_value` = %d
                ",
                $variation_id
            )
        );

        return apply_filters( 'iconic_wssv_variation_total_sales', $total_sales );

    }
}