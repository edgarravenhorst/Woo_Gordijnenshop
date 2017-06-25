<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSSV_Ajax.
 *
 * @class	Iconic_WSSV_Ajax
 * @version  1.0.0
 * @author   Iconic
 */
class Iconic_WSSV_Ajax {

	/**
	 * Instance.
	 */
	private static $instance;

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Iconic_WSSV_Ajax;
			self::$instance->add_ajax_events();
		}
	}

	/**
	 * Hook in methods.
	 */
	private static function add_ajax_events() {
		$ajax_events = array(
			'get_product_count' => false,
			'process_product_visibility' => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_iconic_wssv_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_iconic_wssv_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Get product count.
	 */
	public static function get_product_count( $return = false ) {
		global $wpdb;

		$querystr = "
			SELECT COUNT(*) as count
			FROM $wpdb->posts
			WHERE post_type IN( 'product', 'product_variation' )
		";

		$count = $wpdb->get_var( $querystr );

		$response = array(
			'success' => true,
			'count' => $count
		);

		wp_send_json( $response );
	}

	/**
	 * Process product visibility.
	 */
	public static function process_product_visibility() {
		global $wpdb;

		 $querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type IN( 'product', 'product_variation' )
			LIMIT %d OFFSET %d
		";

		$products = $wpdb->get_results( $wpdb->prepare( $querystr, absint( $_POST['limit'] ), absint( $_POST['offset'] ) ), OBJECT );

		if( ! empty( $products ) ) {
			foreach( $products as $product ) {
				if( $product->post_type === "product_variation" ) {
					$visibility = Iconic_WSSV_Product_Variation::set_visibility( $product->ID );
					$featured_visibility = Iconic_WSSV_Product_Variation::set_featured_visibility( $product->ID );
					$total_sales = Iconic_WSSV_Product_Variation::set_total_sales( $product->ID );

					if( ! $visibility ) {
						error_log( print_r( sprintf( __( 'Error updating visibility for %d', 'iconic-wssv' ), $product->ID ), true ) );
					}

					if( ! $featured_visibility ) {
						error_log( print_r( sprintf( __( 'Error updating featured visibility for %d', 'iconic-wssv' ), $product->ID ), true ) );
					}
				} else {
					Iconic_WSSV_Product::update_visibility( $product->ID );
				}
			}
		}

		wp_reset_postdata();
		wp_send_json( array( 'success' => true ) );
	}
}