<?php
/*
Plugin Name: WooCommerce Show Single Variations
Plugin URI: https://iconicwp.com
Description: Show product variations in the main product loops
Version: 1.1.7
Author: Iconic
Author URI: https://iconicwp.com
Text Domain: iconic-wssv
*/

class JCK_WSSV {

    public $slug = 'iconic-wssv';
    public $version = "1.1.7";
    public $plugin_path;
    public $plugin_url;
    public $theme = false;

    /**
     * Class prefix
     *
     * @since 1.0.0
     * @access protected
     * @var string $class_prefix
     */
    protected $class_prefix = "Iconic_WSSV_";

    /**
     * WPML Class
     *
     * @since 1.1.1
     * @access protected
     * @var Iconic_WSSV_WPML $wpml
     */
    protected $wpml;

    /**
     * WP All Import Class
     *
     * @since 1.1.1
     * @access protected
     * @var Iconic_WSSV_WP_All_Import $wpml
     */
    protected $wp_all_import;

/** =============================
    *
    * Construct the plugin
    *
    ============================= */

    public function __construct() {

		$this->define_constants();
        $this->set_constants();
        $this->load_classes();

        load_plugin_textdomain( 'iconic-wssv', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        add_action( 'init', array( $this, 'initiate_hook' ) );

    }

    /**
     * Define Constants.
     */
    private function define_constants() {

        $this->define( 'ICONIC_WSSV_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'ICONIC_WSSV_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'ICONIC_WSSV_INC_PATH', ICONIC_WSSV_PATH . 'inc/' );
        $this->define( 'ICONIC_WSSV_IS_ENVATO', true );

    }

    /**
     * Define constant if not already set.
     *
     * @param string $name
     * @param string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Load classes
     */
    private function load_classes() {
        spl_autoload_register( array( $this, 'autoload' ) );

		Iconic_WSSV_Query::init();
		Iconic_WSSV_Ajax::init();
		Iconic_WSSV_Term_Counts::init();
		Iconic_WSSV_Product::init();
		Iconic_WSSV_Licence::run();

        new Iconic_WSSV_Settings( ICONIC_WSSV_PATH . 'inc/admin/settings.php', 'iconic_wssv', 'WooCommerce Show Single Variations', 'Show Single Variations' );

        require_once( ICONIC_WSSV_PATH . 'inc/admin/vendor/class-envato-market-github.php' );

        if( $this->is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            $this->wpml = new Iconic_WSSV_WPML();
        }

        if( $this->is_plugin_active( 'wp-all-import-pro/wp-all-import-pro.php' ) ) {
            $this->wp_all_import = new Iconic_WSSV_WP_All_Import();
        }
    }

    /**
     * Autoloader
     *
     * Classes should reside within /inc and follow the format of
     * Iconic_The_Name ~ class-the-name.php or {{class-prefix}}The_Name ~ class-the-name.php
     */
    private function autoload( $class_name ) {

        /**
         * If the class being requested does not start with our prefix,
         * we know it's not one in our project
         */
        if ( 0 !== strpos( $class_name, 'Iconic_' ) && 0 !== strpos( $class_name, $this->class_prefix ) )
            return;

        $file_name = strtolower( str_replace(
            array( $this->class_prefix, 'Iconic_', '_' ),      // Prefix | Plugin Prefix | Underscores
            array( '', '', '-' ),                              // Remove | Remove | Replace with hyphens
            $class_name
        ) );

        // Compile our path from the current location
        $file = dirname( __FILE__ ) . '/inc/class-'. $file_name .'.php';

        // If a file is found
        if ( file_exists( $file ) ) {
            // Then load it up!
            require( $file );
        }

    }

/** =============================
    *
    * Setup Constants for this class
    *
    ============================= */

    public function set_constants() {

        $this->theme = wp_get_theme();

    }

/** =============================
    *
    * Run after the current user is set (http://codex.wordpress.org/Plugin_API/Action_Reference)
    *
    ============================= */

	public function initiate_hook() {

        if( is_admin() ) {

	        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

            add_action( 'woocommerce_variation_options', array( $this, 'add_variation_checkboxes' ), 10, 3 );
            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_variation_additional_fields' ), 10, 3 );
            add_action( 'woocommerce_variable_product_bulk_edit_actions', array( $this, 'add_variation_bulk_edit_actions' ), 10 );
            add_action( 'woocommerce_bulk_edit_variations_default', array( $this, 'bulk_edit_variations' ), 10, 4 );

            add_action( 'wp_ajax_jck_wssv_add_to_cart', array( $this, 'add_to_cart' ) );
            add_action( 'wp_ajax_nopriv_jck_wssv_add_to_cart', array( $this, 'add_to_cart' ) );

            add_action( 'save_post', array( $this, 'on_product_save' ), 10, 1 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'on_variation_save' ), 10, 2 );
            add_action( 'product_variation_linked', array( $this, 'on_variation_save' ), 10 );
            add_action( 'woocommerce_new_product_variation', array( $this, 'on_variation_save' ), 10 );

            add_action( 'set_object_terms', array( $this, 'set_variation_terms' ), 10, 6 );
            add_action( 'updated_post_meta', array( $this, 'updated_product_attributes' ), 10, 4 );

        } else {

            add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

            add_filter( 'post_class', array( $this, 'add_post_classes_in_loop' ) );
            add_filter( 'woocommerce_product_is_visible', array( $this, 'filter_variation_visibility' ), 10, 2 );

            add_filter( 'the_title', array( $this, 'change_variation_title' ), 10, 2 );
            add_filter( 'post_type_link', array( $this, 'change_variation_permalink' ), 10, 2 );
            add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'change_variation_add_to_cart_link' ), 10, 2 );

            add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 2 );
            add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 2 );

            add_filter( 'post_class', array( $this, 'product_post_class' ), 20, 3 );

            add_action( 'delete_transient_wc_term_counts', array( $this, 'delete_term_counts_transient' ), 10, 1 );

            add_filter( 'woocommerce_price_filter_post_type', array( $this, 'add_product_variation_to_price_filter' ), 10, 1 );

            if ( version_compare( WC_VERSION, '2.7', '<' ) ) {

                add_filter( 'woocommerce_product_gallery_attachment_ids', array( $this, 'product_gallery_attachment_id' ), 10, 2 );

            } else {

                add_filter( 'woocommerce_product_get_gallery_image_ids', array( $this, 'product_gallery_attachment_id' ), 10, 2 );

            }

        }

        add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );
        add_action( 'woocommerce_process_shop_order_meta', array( $this, 'process_shop_order' ), 10, 2 );

        $this->register_taxonomy_for_object_type();

	}

	/**
	 * Is settings page.
	 *
	 * @return bool
	 */
	public static function is_settings_page() {
		if( empty( $_GET['page'] ) || $_GET['page'] !== 'iconic-wssv-settings' ) {
			return false;
		}

		return true;
	}

	/**
	 * Admin styles.
	 */
	public function admin_styles() {

		if( ! self::is_settings_page() ) {
			return;
		}

		wp_register_style( 'iconic-woo-show-single-variations-styles', ICONIC_WSSV_URL . 'assets/admin/css/main.min.css', array(), $this->version );

        wp_enqueue_style( 'iconic-woo-show-single-variations-styles' );

	}

	/**
	 * Admin scripts.
	 */
	public function admin_scripts() {

		if( ! self::is_settings_page() ) {
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'iconic-woo-show-single-variations-scripts', ICONIC_WSSV_URL . 'assets/admin/js/main'.$min.'.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'iconic-woo-show-single-variations-scripts' );

	}

	/**
	 * Frontend scripts.
	 */
    public function frontend_scripts() {

        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        wp_register_script( $this->slug.'_scripts', ICONIC_WSSV_URL . 'assets/frontend/js/main'.$min.'.js', array( 'jquery' ), $this->version, true );

        wp_enqueue_script( $this->slug.'_scripts' );

        $vars = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( $this->slug ),
			'pluginSlug' => $this->slug
		);

		wp_localize_script( $this->slug.'_scripts', 'jck_wssv_vars', $vars );

    }


/** =============================
    *
    * Helper: Get filtered variation ids
    *
    * @return [arr]
    *
    ============================= */

    public function get_filtered_variation_ids() {

        global $_chosen_attributes;

        $variation_ids = array();

        $args = array(
            'post_type'  => 'product_variation',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key'     => '_visibility',
                    'value'   => 'filtered',
                    'compare' => 'LIKE',
                )
            )
        );

        $min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : false;
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : false;

		if( $min_price !== false && $max_price !== false ) {

    		$args['meta_query'][] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            );

		}

		if( $_chosen_attributes && !empty( $_chosen_attributes ) ) {

            $i = 10; foreach( $_chosen_attributes as $attribute_key => $attribute_data ) {

                $attribute_meta_key = sprintf('attribute_%s', $attribute_key);

                $attribute_term_slugs = array();

                foreach( $attribute_data['terms'] as $attribute_term_id ) {
                    $attribute_term = get_term_by('id', $attribute_term_id, $attribute_key);
                    $attribute_term_slugs[] = $attribute_term->slug;
                }

                if( $attribute_data['query_type'] == "or" ) {

                    $args['meta_query'][$i] = array(
                        'key'     => $attribute_meta_key,
                        'value'   => $attribute_term_slugs,
                        'compare' => 'IN',
                    );

                } else {

                    $args['meta_query'][$i] = array(
                        'relation' => 'AND'
                    );

                    foreach( $attribute_term_slugs as $attribute_term_slug ) {
                        $args['meta_query'][$i][] = array(
                            'key'     => $attribute_meta_key,
                            'value'   => $attribute_term_slug,
                            'compare' => '=',
                        );
                    }

                }

            $i++; }

        }

        $variations = new WP_Query( $args );

        if ( $variations->have_posts() ) {

        	while ( $variations->have_posts() ) {
        		$variations->the_post();

        		$variation_ids[] = get_the_id();
        	}

        }

        wp_reset_postdata();

        return $variation_ids;

    }

/** =============================
    *
    * Frontend: Add relevant product classes to loop item
    *
    * @param  [arr] [$classes]
    * @return [arr]
    *
    ============================= */

    public function add_post_classes_in_loop( $classes ) {

        global $post, $product;

        if( $product && $post->post_type === "product_variation" ) {

            $classes = array_diff($classes, array('hentry', 'post'));

            $classes[] = "product";
            // add other classes here, find woocommerce function

        }

        return $classes;

    }

/** =============================
    *
    * Admin: Add variation checkboxes
    *
    * @param  [str] [$loop]
    * @param  [arr] [$variation_data]
    * @param  [obj] [$variation]
    *
    ============================= */

    public function add_variation_checkboxes( $loop, $variation_data, $variation ) {

        include('inc/admin/variation-checkboxes.php');

    }

/** =============================
    *
    * Admin: Add variation options
    *
    * @param  [str] [$loop]
    * @param  [arr] [$variation_data]
    * @param  [obj] [$variation]
    *
    ============================= */

    public function add_variation_additional_fields( $loop, $variation_data, $variation ) {

        include('inc/admin/variation-additional-fields.php');

    }

/** =============================
    *
    * Admin: Add variation bulk edit actions
    *
    ============================= */

    public function add_variation_bulk_edit_actions() {

        include('inc/admin/variation-bulk-edit-actions.php');

    }

/** =============================
    *
    * Admin: Bulk edit actions
    *
    * @param  [str] [$bulk_action]
    * @param  [arr] [$data]
    * @param  [int] [$product_id]
    * @param  [arr] [$variations]
    *
    ============================= */

    public function bulk_edit_variations( $bulk_action, $data, $product_id, $variations ) {

        if ( method_exists( $this, "variation_bulk_action_$bulk_action" ) ) {
			call_user_func( array( $this, "variation_bulk_action_$bulk_action" ), $variations );
			$this->delete_term_counts_transient();
		}

    }

/** =============================
    *
    * Helper: Unset array item by the value
    *
    * @param  [arr] [$array]
    * @param  [str] [$value]
    * @return [arr]
    *
    ============================= */

    public static function unset_item_by_value( $array, $value ) {

        if(($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        return $array;

    }

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in (x)
    *
    * @param  [arr] [$variations]
    * @param  [arr] [$show]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in( $variations, $show ) {

        foreach ( $variations as $i => $variation_id ) {

            $visibility = (array) get_post_meta( $variation_id, '_visibility', true );

            if( in_array( $show, $visibility ) ) {

                $visibility = self::unset_item_by_value( $visibility, $show );

                if( $show == "filtered" ) {
                    $this->add_attributes_to_variation( $variation_id, false, "remove" );
                }

            } else {

                $visibility[] = $show;

                if( $show == "filtered" ) {
                    $this->add_attributes_to_variation( $variation_id, false, "add" );
                }

            }

            $this->add_taxonomies_to_variation( $variation_id );
            Iconic_WSSV_Product_Variation::set_visibility( $variation_id, $visibility );
            $this->delete_term_counts_transient();

        }

    }

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Search
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_search( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'search' );

	}

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Filtered
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_filtered( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'filtered' );

	}

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Catalog
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_catalog( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'catalog' );

	}

	/**
	 * Admin: Bulk Action - Toggle Featured
	 *
	 * @param array $variations
	 */
    private function variation_bulk_action_toggle_featured( $variations ) {

        foreach ( $variations as $variation_id ) {
	        $featured = get_post_meta( $variation_id, '_featured', true ) !== "yes";
            Iconic_WSSV_Product_Variation::set_featured_visibility( $variation_id, $featured );
        }

	}

/** =============================
    *
    * Admin: Bulk Action - Toggle Disable "Add to Cart"
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_disable_add_to_cart( $variations ) {

        foreach ( $variations as $variation_id ) {

            $disable_add_to_cart = get_post_meta( $variation_id, '_disable_add_to_cart', true );

            if( $disable_add_to_cart ) {

                delete_post_meta( $variation_id, '_disable_add_to_cart' );

            } else {

                update_post_meta( $variation_id, '_disable_add_to_cart', true );

            }

        }

	}

/** =============================
    *
    * Admin: Save variation options
    *
    * @param  [int] [$variation_id]
    * @param  [int] [$i]
    *
    ============================= */

    public function save_product_variation( $variation_id, $i ) {

        // setup posted data

        $visibility = array();
        $title = isset( $_POST['jck_wssv_display_title'] ) ? $_POST['jck_wssv_display_title'][ $i ] : false;
        $featured = isset( $_POST['jck_wssv_variable_featured'][$i] );

        if( isset( $_POST['jck_wssv_variable_show_catalog'][$i] ) ) {
            $visibility[] = "catalog";
        }

		if( isset( $_POST['jck_wssv_variable_show_filtered'][$i] ) ) {
            $visibility[] = "filtered";
        }

        if( isset( $_POST['jck_wssv_variable_show_search'][$i] ) ) {
            $visibility[] = "search";
        }

        if( empty( $visibility ) ) {
	        $visibility[] = "hidden";
        }

        // set visibility

        Iconic_WSSV_Product_Variation::set_visibility( $variation_id, $visibility );

        // set featured

        Iconic_WSSV_Product_Variation::set_featured_visibility( $variation_id, $featured );

        // set add to cart

        if( isset( $_POST['jck_wssv_variable_disable_add_to_cart'][$i] ) && $_POST['jck_wssv_variable_disable_add_to_cart'][$i] == "on" ) {
            update_post_meta( $variation_id, '_disable_add_to_cart', true );
        } else {
            delete_post_meta( $variation_id, '_disable_add_to_cart' );
        }

		// set display title

		if( $title ) {

    		global $wpdb;

    		update_post_meta( $variation_id, '_jck_wssv_display_title', $title );

    		// Update variation title to be included in search

    		$wpdb->update( $wpdb->posts, array( 'post_title' => $title ), array( 'ID' => $variation_id ) );

        }

    }

/** =============================
    *
    * Frontend: Change variation title
    *
    * @param  [str] [$title]
    * @param  [int] [$id]
    * @return [str]
    *
    ============================= */

    public function change_variation_title( $title, $id = false ) {

        if( $id && $this->is_product_variation( $id ) ) {
            $title = $this->get_variation_title( $id );
        }

        return $title;

    }

/** =============================
    *
    * Helper: Get default variation title
    *
    * @param  [int] [$variation_id]
    * @return [str]
    *
    ============================= */

    public function get_variation_title( $variation_id ) {

        if( !$variation_id || $variation_id == "" )
            return "";

        $variation = wc_get_product( absint( $variation_id ) );
        $variation_title = ( $variation->get_title() != "Auto Draft" ) ? $variation->get_title() : "";
        $variation_custom_title = get_post_meta($variation->get_id(), '_jck_wssv_display_title', true);

        return ( $variation_custom_title ) ? $variation_custom_title : $variation_title;

    }

/** =============================
    *
    * Frontend: Change variation permalink
    *
    * @param  [str] [$url]
    * @param  [str] [$post]
    * @return [str]
    *
    ============================= */

    public function change_variation_permalink( $url, $post ) {

        if ( 'product_variation' == $post->post_type ) {

            $variation = wc_get_product( absint( $post->ID ) );

            return $this->get_variation_url( $variation );

        }

        return $url;

    }

/** =============================
    *
    * Helper: Get variation URL
    *
    * @param  [str] [$variation]
    * @return [str]
    *
    ============================= */

    public function get_variation_url( $variation ) {

        $url = "";
        $variation_parent_id = method_exists( $variation, 'get_parent_id' ) ? $variation->get_parent_id() : $variation->parent->id;

        if( $variation_parent_id ) {

            $variation_data = array_filter( wc_get_product_variation_attributes( $variation->get_id() ) );
            $parent_product_url = get_the_permalink( $variation_parent_id );

            $url = add_query_arg( $variation_data, $parent_product_url );

        }

        return $url;

    }

	/**
	 * Frontend: Change variation add to cart link
     *
     * @param string $anchor
     * @param WC_Product $product
     * @return string
	 */
    public function change_variation_add_to_cart_link( $anchor, $product ) {
        $product_id = $product->get_id();

        if( empty( $product_id ) ) {
            return $anchor;
        }

        $product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;

        if( $product_type !== "variation" ) {
	        return $anchor;
        }

        $button_class = $this->is_purchasable( $product ) && $product->is_in_stock() ? 'add_to_cart add_to_cart_button' : '';

        if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
			$button_class .= ' jck_wssv_add_to_cart';
		}

        return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s" data-variation_id="%s">%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( $product_id ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            apply_filters( 'jck_wssv_add_to_cart_button_class', $button_class ),
            esc_attr( $product_type ),
            esc_html( $product_id ),
            $this->get_add_to_cart_button_text( $product )
        );
    }

    /**
     * Helper: Get add to cart button text
     *
     * @param obj $product
     * @return str
     */
    public function get_add_to_cart_button_text( $product ) {

        $text = esc_html( $product->add_to_cart_text() );

        if( $this->theme->get( 'Name' ) === "Atelier" ) {
            $text = sprintf('<i class="sf-icon-add-to-cart"></i><span>%s</span>', $text);
        }

        return $text;

    }

/** =============================
    *
    * Helper: Is product variation?
    *
    * @param  [int] [$id]
    * @return [bool]
    *
    ============================= */

    public function is_product_variation( $id ) {

        $post_type = get_post_type( $id );

        return $post_type == "product_variation" ? true : false;

    }

/** =============================
    *
    * Admin: Get variation checkboxes
    *
    * @param  [obj] [$variation]
    * @param  [int] [$index]
    * @return [arr]
    *
    ============================= */

    public function get_variation_checkboxes( $variation, $index ) {

        $visibility = get_post_meta($variation->ID, '_visibility', true);
        $featured = get_post_meta($variation->ID, '_featured', true);
        $disable_add_to_cart = get_post_meta($variation->ID, '_disable_add_to_cart', true);

        $checkboxes = array(
            array(
                'class' => 'jck_wssv_variable_show_search',
                'name' => sprintf('jck_wssv_variable_show_search[%d]', $index),
                'id' => sprintf('jck_wssv_variable_show_search-%d', $index),
                'checked' => is_array( $visibility ) && in_array('search', $visibility) ? true : false,
                'label' => __( 'Show in Search Results?', 'iconic-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_show_filtered',
                'name' => sprintf('jck_wssv_variable_show_filtered[%d]', $index),
                'id' => sprintf('jck_wssv_variable_show_filtered-%d', $index),
                'checked' => is_array( $visibility ) && in_array('filtered', $visibility) ? true : false,
                'label' => __( 'Show in Filtered Results?', 'iconic-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_show_catalog',
                'name' => sprintf('jck_wssv_variable_show_catalog[%d]', $index),
                'id' => sprintf('jck_wssv_variable_show_catalog-%d', $index),
                'checked' => is_array( $visibility ) && in_array('catalog', $visibility) ? true : false,
                'label' => __( 'Show in Catalog?', 'iconic-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_featured',
                'name' => sprintf('jck_wssv_variable_featured[%d]', $index),
                'id' => sprintf('jck_wssv_variable_featured-%d', $index),
                'checked' => $featured === "yes" ? true : false,
                'label' => __( 'Featured', 'iconic-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_disable_add_to_cart',
                'name' => sprintf('jck_wssv_variable_disable_add_to_cart[%d]', $index),
                'id' => sprintf('jck_wssv_variable_disable_add_to_cart-%d', $index),
                'checked' => $disable_add_to_cart ? true : false,
                'label' => __( 'Disable "Add to Cart"?', 'iconic-wssv' )
            ),
        );

        return $checkboxes;

    }

/** =============================
    *
    * Helper: Filter variaiton visibility
    *
    * Set variation to is_visible() if the options are selected
    *
    * @param  [bool] [$visible]
    * @param  [bool] [$id]
    * @return [bool]
    *
    ============================= */

    public function filter_variation_visibility( $visible, $id ) {

        global $product;

        if( method_exists( $product, 'get_id' ) ) {

            $visibility = get_post_meta( $product->get_id(), '_visibility', true );

            if( is_array( $visibility ) ) {

                // visible in search

                if( $this->is_visible_when('search', $product->get_id()) ) {
                    $visible = true;
                }

                // visible in filtered

                if( $this->is_visible_when('filtered', $product->get_id()) ) {
                    $visible = true;
                }

                // visible in catalog

                if( $this->is_visible_when('catalog', $product->get_id()) ) {
                    $visible = true;
                }


            }

        }

        return $visible;

    }

/** =============================
    *
    * Helper: Is visible when...
    *
    * Check if a variation is visible when search, filtered, catalog
    *
    * @param  [str] [$when]
    * @param  [int] [$id]
    * @return [bool]
    *
    ============================= */

    public function is_visible_when( $when = false, $id ) {

        $visibility = get_post_meta($id, '_visibility', true);

        if( is_array( $visibility ) ) {

            // visible in search

            if( is_search() && in_array($when, $visibility) ) {
                return true;
            }

            // visible in filtered

            if( is_filtered() && in_array($when, $visibility) ) {
                return true;
            }

            // visible in catalog

            if( !is_filtered() && !is_search() && in_array($when, $visibility) ) {
                return true;
            }


        }

        return false;

    }

/** =============================
    *
    * Ajax: Add to cart
    *
    ============================= */

    public static function add_to_cart() {

		ob_start();

		$product_id           = apply_filters( 'jck_wssv_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$variation_id         = apply_filters( 'jck_wssv_add_to_cart_variation_id', absint( $_POST['variation_id'] ) );
		$quantity             = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$passed_validation    = apply_filters( 'jck_wssv_add_to_cart_validation', true, $variation_id, $quantity );
		$product_status       = get_post_status( $variation_id );
		$variations           = array();
		$variation            = new WC_Product_Variation( absint( $variation_id ) );
		$variation_attributes = $variation->get_variation_attributes();

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attributes ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $variation_id );
			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			$wc_ajax = new WC_AJAX();

			// Return fragments
			$wc_ajax->get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
			);

			wp_send_json( $data );

		}

		wp_die();
	}

/** =============================
    *
    * Add product_variation to tags and categories
    *
    ============================= */

    public function register_taxonomy_for_object_type() {

        register_taxonomy_for_object_type( 'product_cat', 'product_variation' );
        register_taxonomy_for_object_type( 'product_tag', 'product_variation' );

    }

/** =============================
    *
    * Admin: Add main product taxonomies to variation on variaition save
    *
    * @param  [int] [$variation_id]
    * @param  [int] [$i]
    *
    ============================= */

    public function add_taxonomies_to_variation( $variation_id, $i = false ) {

        $parent_product_id = wp_get_post_parent_id( $variation_id );

        if( $parent_product_id ) {

            // add categories and tags to variaition
            $taxonomies = array(
                'product_cat',
                'product_tag'
            );

            foreach( $taxonomies as $taxonomy ) {

                $terms = (array) wp_get_post_terms( $parent_product_id, $taxonomy, array("fields" => "ids") );
                wp_set_post_terms( $variation_id, $terms, $taxonomy );

            }

        }

    }

/** =============================
    *
    * Admin: Save variation attributes
    *
    * @param  [int] [$variation_id]
    * @param  [int] [$i]
    * @param bool $force
    *
    ============================= */

    public function add_attributes_to_variation( $variation_id, $i = false, $force = false ) {

        $attributes = wc_get_product_variation_attributes( $variation_id );

        if( $attributes && !empty( $attributes ) ) {

            foreach( $attributes as $taxonomy => $value ) {

                $taxonomy = str_replace('attribute_', '', $taxonomy);
                $term = get_term_by('slug', $value, $taxonomy);

                if( $force == "add" || isset($_POST['jck_wssv_variable_show_filtered'][$i]) && $_POST['jck_wssv_variable_show_filtered'][$i] == "on" ) {

                    wp_set_object_terms( $variation_id, $value, $taxonomy );

                } else {

                    if( $term && ( !$force || $force == "remove" ) ) {

                        $products_in_term = wc_get_term_product_ids( $term->term_id, $taxonomy );

                        if(($key = array_search($variation_id, $products_in_term)) !== false) {
                            unset($products_in_term[$key]);
                        }

                        update_woocommerce_term_meta( $term->term_id, 'product_ids', $products_in_term );
                        wp_remove_object_terms( $variation_id, $term->term_id, $taxonomy );
                    }

                }

                if( $term ) {

                    $this->delete_count_transient( $taxonomy, $term->term_taxonomy_id );

                }

            }

        }

    }

    /**
	 * Admin: Fired when a product's terms have been set.
	 *
	 * @param int    $object_id  Object ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 */
    public function set_variation_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {

        $post_type = get_post_type( $object_id );

        if( $post_type === "product" ) {

            if( $taxonomy === "product_cat" || $taxonomy === "product_tag" ) {

                $variations = get_children(array(
                    'post_parent' => $object_id,
                    'post_type' => 'product_variation'
                ), ARRAY_A);

                if( $variations && !empty( $variations ) ) {

                    $variation_ids = array_keys( $variations );

                    foreach( $variation_ids as $variation_id ) {
                        wp_set_object_terms( $variation_id, $terms, $taxonomy, $append );
                    }

                }

            }

        }

    }

/** =============================
    *
    * Admin: Clean variation attributes
    *
    * @param  [int] [$variation_id]
    *
    ============================= */

    public function clean_variation_attributes( $variation_id ) {

        $taxonomies = get_object_taxonomies( 'product_variation', 'names' );

        if( $taxonomies && !empty( $taxonomies ) ) {

            $attributes = array_filter($taxonomies, function ($v) {
                return substr($v, 0, 3) === 'pa_';
            });

            if( !empty( $attributes ) ) {

                foreach( $attributes as $attribute ) {

                    $terms = wp_get_object_terms( $variation_id, $attribute, array('fields' => 'ids') );
                    wp_remove_object_terms( $variation_id, $terms, $attribute );

                }

            }

        }

    }



/** =============================
    *
    * Frontend: is_purchasable
    *
    * @param  [obj] [$product]
    * @return [bool]
    *
    ============================= */

    public function is_purchasable( $product ) {

        $purchasable = $product->is_purchasable();
        $product_id = $product->get_id();

        if( !$product_id )
            return $purchasable;

        $disable_add_to_cart = get_post_meta( $product_id, '_disable_add_to_cart', true );

        if( $disable_add_to_cart ) {

            $purchasable = false;

        } else {

            $variation_data = wc_get_product_variation_attributes( $product_id );

            if( empty( $variation_data ) )
                return $purchasable;

            foreach( $variation_data as $value ) {

                if( !empty( $value ) )
                    continue;

                $purchasable = false;

            }

        }

        return $purchasable;

    }

/** =============================
    *
    * Frontend: Add to Cart Text
    *
    * @param  [str] [$text]
    * @param  [obg] [$product]
    * @return [str]
    *
    ============================= */

    public function add_to_cart_text( $text, $product ) {

        if( $product->get_id() ) {

            $text = $this->is_purchasable( $product ) && $product->is_in_stock() ? $text : __( 'Select options', 'woocommerce' );

        }

        return $text;

    }

/** =============================
    *
    * Frontend: Add to Cart URL
    *
    * @param  [str] [$url]
    * @param  [obg] [$product]
    * @return [str]
    *
    ============================= */

    public function add_to_cart_url( $url, $product ) {

        $product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;

        if( $product->get_id() && $product_type === "variation" ) {

            $url = $this->is_purchasable( $product ) && $product->is_in_stock() ? $url : $this->get_variation_url( $product );

        }

        return $url;

    }

/**	=============================
    *
    * Get Woo Version Number
    *
    * @return mixed bool/str NULL or Woo version number
    *
    ============================= */

    public function get_woo_version_number() {

        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];

        } else {
            // Otherwise return null
            return NULL;
        }

    }

    /**
     * Admin: When the order status changes
     *
     * @param int $order_id
     * @param str $old_status
     * @param str $new_status
     */
    public function order_status_changed( $order_id, $old_status, $new_status ) {

        $accepted_status = array('completed', 'processing', 'on-hold');

        if( in_array($new_status, $accepted_status) ) {

            $this->record_variation_sales( $order_id );

        }

    }

    /**
     * Admin: When an Admin manually creates an order
     *
     * @param int $post_id
     * @param obj $post
     */
    public function process_shop_order( $post_id, $post ) {

        $accepted_status = array('wc-completed', 'wc-processing', 'wc-on-hold');

        if( in_array($post->post_status, $accepted_status) ) {

            $this->record_variation_sales( $post_id );

        }

    }

    /**
     * Helper: Record variaiton sales
     *
     * Updates the variation sales count for an order
     *
     * @param int $order_id
     */
    public function record_variation_sales( $order_id ) {

        if ( 'yes' === get_post_meta( $order_id, '_recorded_variation_sales', true ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( sizeof( $order->get_items() ) > 0 ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item['variation_id'] > 0 ) {
					$sales = (int) get_post_meta( $item['variation_id'], 'total_sales', true );
					$sales += (int) $item['qty'];
					if ( $sales ) {
						update_post_meta( $item['variation_id'], 'total_sales', $sales );
					}
				}
			}
		}

		update_post_meta( $order_id, '_recorded_variation_sales', 'yes' );

		/**
		 * Called when sales for an order are recorded
		 *
		 * @param int $order_id order id
		 */
		do_action( 'woocommerce_recorded_variation_sales', $order_id );

    }

    /**
	 * Update variation Gallery if WooThumbs is being used
	 *
	 * @param arr $ids Array of gallery image IDs
	 * @param obj $product
	 * @return arr
	 */
    public function product_gallery_attachment_id( $ids, $product ) {

        $product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;

        if( $product_type === "variation" ) {

            $ids = array();

            // additional images

            $additional_ids = get_post_meta( $product->get_id(), 'variation_image_gallery', true );

            if( $additional_ids ) {

                $ids = explode(',', $additional_ids);

            }

        }

        return $ids;

    }

	/**
	 * Delete term counts transient
	 *
	 * When recount terms is run in backend of woo,
	 * delete our additional term counts transient, too.
	 */
    public function delete_term_counts_transient() {

        delete_transient( 'jck_wssv_term_counts' );

    }

    /**
     * Helper: Get current view
     *
     * @return str
     */
    public function get_current_view() {

        if( is_search() ) {
            return 'search';
        }

        if( is_filtered() ) {
            return 'filtered';
        }

        return 'catalog';

    }

    /**
     * Frontend: Taxonomies to change term counts for
     *
     * @param arr $taxonomies
     * @return arr
     */
    public function term_count_taxonomies( $taxonomies ) {

        $attributes = wc_get_attribute_taxonomies();

        if( $attributes && !empty( $attributes ) ) {
            foreach( $attributes as $attribute ) {
                $taxonomies[] = sprintf('pa_%s', $attribute->attribute_name);
            }
        }

        return $taxonomies;

    }

    /**
     * Admin: On product save
     *
     * @param int $post_id
     */
    public function on_product_save( $post_id ) {

        if ( wp_is_post_revision( $post_id ) )
		    return;

        $post_type = get_post_type( $post_id );

        if( $post_type != "product" )
            return;

        $this->add_non_variation_attributes_to_variation( $post_id );
        $this->delete_term_counts_transient();

    }

    /**
     * Admin: On variation save
     *
     * @param int $variation_id
     * @param int|bool $i
     */
    public function on_variation_save( $variation_id, $i = false ) {

		$this->save_product_variation( $variation_id, $i );
        $this->add_taxonomies_to_variation( $variation_id, $i );
        $this->add_attributes_to_variation( $variation_id, $i );
        $this->delete_term_counts_transient();

    }


    /**
     * Admin: Add non variaition attributes to variations
     *
     * This allows them to be seen in the layered nav query
     *
     * @param int $post_id
     */
    public function updated_product_attributes( $meta_id, $object_id, $meta_key, $_meta_value ) {

    	if( $meta_key == "_product_attributes" ) {

            $this->add_non_variation_attributes_to_variation( $object_id );

    	}
	}

    /**
     * Admin: Add non variaition attributes to variations
     *
     * This allows them to be seen in the layered nav query
     *
     * @param int $post_id
     */
	public function add_non_variation_attributes_to_variation( $post_id ) {

        if( $product = wc_get_product( $post_id ) ) {

            $variations = $product->get_children();

            if( $attributes = $product->get_attributes() ) {
                foreach( $attributes as $taxonomy => $attribute_data ) {
                    if( $attribute_data['is_variation'] == 0 ) {

                        $terms = wp_get_post_terms( $post_id, $taxonomy );

                        if( $variations && $terms && !is_wp_error( $terms ) ) {
                            foreach( $variations as $i => $variation_id ) {

                                $term_ids = array();

                                foreach( $terms as $term ) {

                                    $term_ids[] = $term->term_id;

                                }

                                $set_terms = wp_set_object_terms( $variation_id, $term_ids, $taxonomy );

                                $this->delete_count_transient( $taxonomy, $term->term_taxonomy_id );

                            }
                        }

                    }
                }
            }

        }

	}

	/**
	 * Helper: Delete count transient
	 *
	 * @param str $taxonomy
	 * @param int $taxonomy_id
	 */
    public function delete_count_transient( $taxonomy, $taxonomy_id ) {

        $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $taxonomy_id ) );
        delete_transient($transient_name);

    }

    /**
     * Add product type (product_variation) to post class
     *
     * @since 1.1.0
     * @param array $classes
     * @param string|array $class
     * @param int $post_id
     * @return array
     */
    public function product_post_class( $classes, $class = '', $post_id = '' ) {

        if (
        	! $post_id ||
        	'product_variation' !== get_post_type( $post_id ) ||
        	version_compare($this->get_woo_version_number(), '3.0.0', '>=')
        ) {
            return $classes;
        }

        $product = wc_get_product( $post_id );

        if ( $product ) {

            $product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;

            $classes[] = wc_get_loop_class();
            $classes[] = method_exists( $product, 'get_stock_status' ) ? $product->get_stock_status() : $product->stock_status;

            if ( $product->is_on_sale() ) {
                $classes[] = 'sale';
            }
            if ( $product->is_featured() ) {
                $classes[] = 'featured';
            }
            if ( $product->is_downloadable() ) {
                $classes[] = 'downloadable';
            }
            if ( $product->is_virtual() ) {
                $classes[] = 'virtual';
            }
            if ( $product->is_sold_individually() ) {
                $classes[] = 'sold-individually';
            }
            if ( $product->is_taxable() ) {
                $classes[] = 'taxable';
            }
            if ( $product->is_shipping_taxable() ) {
                $classes[] = 'shipping-taxable';
            }
            if ( $product->is_purchasable() ) {
                $classes[] = 'purchasable';
            }
            if ( $product_type ) {
                $classes[] = "product-type-" . $product_type;
            }
        }

        if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
            unset( $classes[ $key ] );
        }

        return $classes;

    }

    /**
     * Check whether the plugin is inactive.
     *
     * Reverse of is_plugin_active(). Used as a callback.
     *
     * @since 3.1.0
     * @see is_plugin_active()
     *
     * @param string $plugin Base plugin path from plugins directory.
     * @return bool True if inactive. False if active.
     */
    public function is_plugin_active( $plugin ) {

        return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->is_plugin_active_for_network( $plugin );

    }

    /**
     * Check whether the plugin is active for the entire network.
     *
     * Only plugins installed in the plugins/ folder can be active.
     *
     * Plugins in the mu-plugins/ folder can't be "activated," so this function will
     * return false for those plugins.
     *
     * @since 3.0.0
     *
     * @param string $plugin Base plugin path from plugins directory.
     * @return bool True, if active for the network, otherwise false.
     */
    public function is_plugin_active_for_network( $plugin ) {
        if ( !is_multisite() )
            return false;
        $plugins = get_site_option( 'active_sitewide_plugins');
        if ( isset($plugins[$plugin]) )
            return true;
        return false;
    }

    /**
     * Add product_variation to price filter widget
     *
     * @param arr $post_types
     * @return arr
     */
    public function add_product_variation_to_price_filter( $post_types ) {

        $post_types[] = 'product_variation';

        return $post_types;

    }

    /**
     * Invert number.
     *
     * @param int $number
     * @return string
     */
    public static function invert_number( $number ) {
	    $decimal = 1/$number;
		$decimal = explode( '.', $decimal );

		return $decimal[1];
    }

    /**
     * Toggle array value.
     *
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public static function toggle_array_value( $array, $value ) {
	    if( $key = array_search( $value, $array ) !== false ) {
		    unset( $array[ $key ] );
		    return $array;
		}

		$array[] = $value;
		return $array;
    }

}

$jck_wssv = new JCK_WSSV();