<?php
/**
 * Iconic_WSSV_Query Class.
 *
 * All methods for modify the WooCommerce Query.
 *
 * @since 1.1.5
 */
class Iconic_WSSV_Query {

	/**
	 * Variation IDs with missing parent
	 *
	 * @access protected
	 * @var arr $variation_ids_with_missing_parent
	 */
	protected $variation_ids_with_missing_parent = null;

	/**
	 * Init.
	 */
	public static function init() {
		if( is_admin() ) {
			return;
		}

		add_action( 'woocommerce_product_query', array( __CLASS__, 'add_variations_to_product_query' ), 50, 2 );
		add_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'add_variations_to_shortcode_query' ), 10, 2 );
		// add_filter( 'woocommerce_get_catalog_ordering_args', array( __CLASS__, 'modify_catalog_ordering_args' ), 10, 1 );
	}

	/**
	 * Add variations to the product query.
	 *
	 * @param $q
	 * @param $wc_query
	 */
	public static function add_variations_to_product_query( $q, $wc_query ) {
		if( ! is_woocommerce() || ! $q->is_main_query() || empty( $q->query_vars['wc_query'] ) ) {
			return;
		}

		global $_chosen_attributes;

		// Add product variations to the query

		$post_type = (array) $q->get('post_type');
		$post_type[] = 'product_variation';
		if( !in_array('product', $post_type) ) $post_type[] = 'product';
		$q->set('post_type', array_filter( $post_type ) );

		// Don't get variations with unpublished parents

		$unpublished_variable_product_ids = self::get_unpublished_variable_product_ids();
		if( ! empty( $unpublished_variable_product_ids ) ) {
			$post_parent__not_in = (array) $q->get('post_parent__not_in');
			$q->set('post_parent__not_in', array_merge( $post_parent__not_in, $unpublished_variable_product_ids ) );
		}

		// Don't get variations with missing parents :(

		$variation_ids_with_missing_parent = self::get_variation_ids_with_missing_parent();
		if( ! empty( $variation_ids_with_missing_parent ) ) {
			$post__not_in = (array) $q->get('post__not_in');
			$q->set('post__not_in', array_merge( $post__not_in, $variation_ids_with_missing_parent ) );
		}

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			// update the meta query to include our variations

			$meta_query = (array) $q->get('meta_query');
			$meta_query = self::update_meta_query( $meta_query );
			$q->set( 'meta_query', $meta_query );
		} else {
			// update the tax query to include our variations

			$tax_query = (array) $q->get( 'tax_query' );
			$tax_query = self::update_tax_query( $tax_query );
			$q->set( 'tax_query', $tax_query );
		}
	}

	/*
	 * Add variaitons to shortcode queries
	 *
	 * @param arr $query_args
	 * @param arr $shortcode_args
	 */
	public static function add_variations_to_shortcode_query( $query_args, $shortcode_args ) {
		// Add product variations to the query

		$post_type = (array) $query_args['post_type'];
		$post_type[] = 'product_variation';

		$query_args['post_type'] = $post_type;

		// Don't get variations with unpublished parents

		$unpublished_variable_product_ids = self::get_unpublished_variable_product_ids();
		if( $unpublished_variable_product_ids ) {
			$post_parent__not_in = isset( $query_args['post_parent__not_in'] ) ? (array) $query_args['post_parent__not_in'] : array();
			$query_args['post_parent__not_in'] = array_merge( $post_parent__not_in, $unpublished_variable_product_ids );
		}

		// Don't get variations with missing parents :(

		$variation_ids_with_missing_parent = self::get_variation_ids_with_missing_parent();
		if( $variation_ids_with_missing_parent ) {
			$post__not_in = isset( $query_args['post__not_in'] ) ? (array) $query_args['post__not_in'] : array();
			$query_args['post__not_in'] = array_merge( $post__not_in, $variation_ids_with_missing_parent );
		}

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			// update the meta query to include our variations

			$meta_query = (array) $query_args['meta_query'];
			$meta_query = self::update_meta_query( $meta_query );
			$query_args['meta_query'] = $meta_query;
		} else {
			// update the tax query to include our variations

			$tax_query = (array) $query_args['tax_query'];
			$tax_query = self::update_tax_query( $tax_query );
			$query_args['tax_query'] = $tax_query;
		}

		return $query_args;
	}

	/**
	 * Get unpublished variable product IDs
	 *
	 * Get's an array of product IDs where the product
	 * is variable and has not been published (i.e. is in the bin)
	 *
	 * @since 1.1.0
	 * @return mixed array
	 */
	public static function get_unpublished_variable_product_ids() {

		static $unpublished_variable_product_ids = null;

		if( ! is_null( $unpublished_variable_product_ids ) )
			return $unpublished_variable_product_ids;

		$statuses = array('trash','future','auto-draft','pending','draft');

		$args = array(
			'post_type' => 'product',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field'	=> 'slug',
					'terms'	=> 'variable',
				),
			),
			'posts_per_page' => -1,
			'post_status' => $statuses,
			'meta_key' => '_sku'
		);

		$products = new WP_Query( $args );

		wp_reset_postdata();

		$unpublished_variable_product_ids = wp_list_pluck( $products->posts, 'ID' );

		return $unpublished_variable_product_ids;

	}

	/**
	 * Get variation IDs with missing parents
	 *
	 * @since 1.1.2
	 * @return mixed bool|array
	 */
	public static function get_variation_ids_with_missing_parent() {

		static $variation_ids_with_missing_parent = null;

		if( ! is_null( $variation_ids_with_missing_parent ) )
			return $variation_ids_with_missing_parent;

		global $wpdb;

		$variation_ids = $wpdb->get_results(
			"
			SELECT  p1.ID
			FROM $wpdb->posts p1
			WHERE p1.post_type = 'product_variation'
			AND p1.post_parent NOT IN (
				SELECT DISTINCT p2.ID
				FROM $wpdb->posts p2
				WHERE p2.post_type = 'product'
			)
			", ARRAY_A
		);

		$variation_ids_with_missing_parent = wp_list_pluck( $variation_ids, 'ID' );

		return $variation_ids_with_missing_parent;

	}

	/*
	 * Update meta query
	 *
	 * Add OR parameters to also search for variations with specific visibility
	 *
	 * @param array $meta_query]
	 * @return array
	 */
	public static function update_meta_query( $meta_query ) {

		$index = 0;

		if( ! empty( $meta_query ) ) {
			foreach( $meta_query as $index => $meta_query_item ) {
				if( isset( $meta_query_item['key'] ) && $meta_query_item['key'] == "_visibility" ) {

					$meta_query[$index] = array();
					$meta_query[$index]['relation'] = 'OR';

					$meta_query[$index]['visibility_visible'] = array(
						'key' => '_visibility',
						'value' => 'visible',
						'compare' => 'LIKE'
					);

					if( is_search() ) {

						$meta_query[$index]['visibility_search'] = array(
							'key' => '_visibility',
							'value' => 'search',
							'compare' => 'LIKE'
						);

					} else {

						$meta_query[$index]['visibility_catalog'] = array(
							'key' => '_visibility',
							'value' => 'catalog',
							'compare' => 'LIKE'
						);

					}

					if( is_filtered() ) {

						$meta_query[$index]['visibility_filtered'] = array(
							'key' => '_visibility',
							'value' => 'filtered',
							'compare' => 'LIKE'
						);

					}

				}
			}
		}

		return $meta_query;

	}

	/**
	 * Update tax query.
	 */
	public static function update_tax_query( $tax_query, $filtered = false ) {
		$exclude_from_filtered_term = get_term_by( 'slug', 'exclude-from-filtered', 'product_visibility' );

		if( $exclude_from_filtered_term && ( $filtered || is_filtered() ) ) {
			if( empty( $tax_query ) ) {
				$tax_query['relation'] = 'AND';
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field' => 'term_taxonomy_id',
					'terms' => array( $exclude_from_filtered_term->term_taxonomy_id ),
					'operator' => 'NOT IN'
				);
			} else {
				foreach( $tax_query as $index => $tax_query_item ) {
					if( ! is_array( $tax_query_item ) )	{
						continue;
					}

					if( empty( $tax_query_item['taxonomy'] ) ) {
						continue;
					}

					if( $tax_query_item['taxonomy'] !== 'product_visibility' ) {
						continue;
					}

					$modified_tax_query = array(
						'relation' => 'OR'
					);

					$modified_tax_query[] = $tax_query[ $index ];

					$modified_tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field' => 'term_taxonomy_id',
						'terms' => array( $exclude_from_filtered_term->term_taxonomy_id ),
						'operator' => 'NOT IN',
						'include_children' => 1
					);

					$tax_query[ $index ] = $modified_tax_query;
				}
			}
		}

		return $tax_query;
	}

    /**
     * Modify catalog ordering args
     *
     * @param arr $args
     */
    public static function modify_catalog_ordering_args( $args ) {

        if( $args['orderby'] == "menu_order title" ) {
            $args['orderby'] = "meta_value_num title";
        }

        return $args;

    }
}