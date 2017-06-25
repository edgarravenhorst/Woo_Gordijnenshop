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
class Iconic_WSSV_Product {

	/**
	 * Run.
	 */
	public static function init() {
		add_action( 'woocommerce_update_product', array( __CLASS__, 'on_update_product' ), 10, 2 );
		// add_action( 'woocommerce_after_single_product_ordering', array( __CLASS__, 'after_single_product_ordering' ), 10, 2 );
	}

	/**
	 * On update product.
	 *
	 * @param int $product_id
	 */
	public static function on_update_product( $product_id ) {
		self::update_visibility( $product_id );
	}

	/**
	 * On update visibility.
	 *
	 * @param int $product_id
	 */
	public static function update_visibility( $product_id ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			return;
		}

		$product = wc_get_product( $product_id );

		if( ! $product ) {
			return;
		}

		$visibility = self::get_catalog_visibility( $product );
		$visibility_terms = wp_list_pluck( wp_get_post_terms( $product->get_id(), 'product_visibility' ), 'slug' );

		if( $visibility === "hidden" && ! in_array( "exclude-from-filtered", $visibility_terms ) ) {
			$visibility_terms[] = "exclude-from-filtered";
		} else {
			$visibility_terms = JCK_WSSV::unset_item_by_value( $visibility_terms, "exclude-from-filtered" );
		}

		if ( ! is_wp_error( wp_set_post_terms( $product->get_id(), $visibility_terms, 'product_visibility', false ) ) ) {
			do_action( 'woocommerce_product_set_visibility', $product->get_id(), $product->get_catalog_visibility() );
		}
	}

	/**
	 * After single product ordering
	 */
	public static function after_single_product_ordering( $id, $index ) {
		error_log( print_r( $id, true ) );
		error_log( print_r( $index, true ) );
	}

	/**
	 * Get catalog visibility.
	 *
	 * @param WC_Product $product
	 * @return string
	 */
	public static function get_catalog_visibility( $product ) {
		if( method_exists( $product, 'get_catalog_visibility' ) ) {
			return $product->get_catalog_visibility();
		} else {
			return get_post_meta( $product->get_id(), '_visibility', true );
		}
	}
}