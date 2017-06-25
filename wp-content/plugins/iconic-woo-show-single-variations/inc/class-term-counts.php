<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSSV_Term_Counts.
 *
 * @class	Iconic_WSSV_Term_Counts
 * @version  1.0.0
 * @author   Iconic
 */
class Iconic_WSSV_Term_Counts {

	/**
	 * Instance.
	 */
	private static $instance;

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Iconic_WSSV_Term_Counts;
			self::$instance->add_filters();
		}
	}

	/**
	 * Add filters.
	 */
	protected static function add_filters() {
		if( is_admin() ) {
			return;
		}

		add_filter( 'woocommerce_get_filtered_term_product_counts_query', array( __CLASS__, 'filtered_term_product_counts_where_clause' ), 10, 1);
        add_filter( 'get_terms', array( __CLASS__, 'change_term_counts' ), 100, 2 );
	}

	/**
	 * Modify the "filtered term product counts" where clause
	 *
	 * Adds post_type and post_parent__not_in parameter so unpublished variable
	 * product variations are ignored in the filter counts
	 *
	 * @since 1.1.0
	 * @param array $query
	 * @return array
	 */
	public static function filtered_term_product_counts_where_clause( $query ) {

		global $wpdb, $wp_the_query;

		$query['where'] = str_replace("'product'", "'product', 'product_variation'", $query['where']);

		if( empty( $wp_the_query->query_vars['post_parent__not_in'] ) )
			return $query;

		$query['where'] = sprintf("%s AND %s.post_parent NOT IN ('%s')", $query['where'], $wpdb->posts, implode("','", $wp_the_query->query_vars['post_parent__not_in']));

		if( ! is_filtered() ) {
			$current_tax_query = WC_Query::get_main_tax_query();
			$current_tax_query_obj = new WP_Tax_Query( $current_tax_query );
			$current_tax_query_sql = $current_tax_query_obj->get_sql( $wpdb->posts, 'ID' );

			$new_tax_query = Iconic_WSSV_Query::update_tax_query( $current_tax_query, true );
			$new_tax_query_obj = new WP_Tax_Query( $new_tax_query );
			$new_tax_query_sql = $new_tax_query_obj->get_sql( $wpdb->posts, 'ID' );

			$query['where'] = str_replace( $current_tax_query_sql, $new_tax_query_sql, $query['where'] );
		}

		return $query;

	}

	/**
	 * Frontend: Change Term Counts
	 *
	 * @param arr $terms
	 * @param arr $taxonomies
	 * @return arr
	 */
	public static function change_term_counts( $terms, $taxonomies ) {

		if ( is_admin() || is_ajax() )
			return $terms;

		if ( ! isset( $taxonomies[0] ) || ! in_array( $taxonomies[0], apply_filters( 'woocommerce_change_term_counts', array( 'product_cat', 'product_tag' ) ) ) )
			return $terms;

		if ( false === ( $variation_term_counts = get_transient( 'jck_wssv_term_counts' ) ) ) {

			$variation_term_counts = array();

			foreach ( $terms as &$term ) {

				if ( !is_object( $term ) )
					continue;

				$variation_term_counts[ $term->term_id ] = absint( self::get_variations_count_in_term( $term ) );

			}

			set_transient( 'jck_wssv_term_counts', $variation_term_counts );

		}

		$term_counts = get_transient( 'wc_term_counts' );

		foreach ( $terms as &$term ) {

			if ( !is_object( $term ) )
				continue;

			if( !isset( $term_counts[ $term->term_id ] ) )
				continue;

			$child_term_count = isset( $variation_term_counts[ $term->term_id ] ) ? $variation_term_counts[ $term->term_id ] : 0;

			$term_counts[ $term->term_id ] = (int) $term_counts[ $term->term_id ] + (int) $child_term_count;

			if ( empty( $term_counts[ $term->term_id ] ) )
				continue;

			$term->count = absint( $term_counts[ $term->term_id ] );

		}

		return $terms;

	}

	/**
	 * Helper: Get Variaitons count in term
	 *
	 * @param obj $term
	 * @return int
	 */
	public static function get_variations_count_in_term( $term ) {

		global $wpdb;

		$sql = $wpdb->prepare("
			SELECT COUNT(*) FROM `$wpdb->posts` wp
			INNER JOIN `$wpdb->postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key`='_visibility')
			INNER JOIN `$wpdb->term_relationships` wtr ON (wp.`ID` = wtr.`object_id`)
			INNER JOIN `$wpdb->term_taxonomy` wtt ON (wtr.`term_taxonomy_id` = wtt.`term_taxonomy_id`)
			INNER JOIN `$wpdb->terms` wt ON (wt.`term_id` = wtt.`term_id`)
			AND wtt.taxonomy = '%s' AND wt.`slug` = '%s'
			AND wp.post_status = 'publish' AND ( wm.meta_value LIKE '%%visible%%' OR wm.meta_value LIKE '%%catalog%%' )
			AND wp.post_type = 'product_variation'
			ORDER BY wp.post_date DESC
		", $term->taxonomy, $term->slug );

		$count = $wpdb->get_var( $sql );

		return apply_filters( 'iconic_wssv_variations_count_in_term', $count, $term );

	}
}