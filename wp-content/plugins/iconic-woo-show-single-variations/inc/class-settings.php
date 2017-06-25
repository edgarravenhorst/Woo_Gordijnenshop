<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSSV_Settings.
 *
 * @class	Iconic_WSSV_Settings
 * @version  1.0.0
 * @author   Iconic
 */
class Iconic_WSSV_Settings {

	/*
	 * Variable to hold settings framework instance
	 *
	 * @var WordPressSettingsFramework
	 */
	public $settings_framework = null;

	/*
	 * Settings
	 *
	 * @var arr|null
	 */
	public $settings = null;

	/*
	 * Page Title
	 *
	 * @var str|null
	 */
	public $page_title = null;

	/*
	 * Menu Title
	 *
	 * @var str|null
	 */
	public $menu_title = null;

	/**
	 * Init settings
	 */
	public function __construct( $settings_path = null, $option_group = null, $page_title = null, $menu_title = null ) {

		$page_title and $this->page_title = $page_title;
		$menu_title and $this->menu_title = $menu_title;

		require_once('vendor/wp-settings-framework/wp-settings-framework.php');

		$this->settings_framework = new WordPressSettingsFramework( $settings_path, $option_group );
		$this->settings = $this->settings_framework->get_settings();

		// Add admin menu
		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 20 );

		// Validate Settings
		add_filter( $option_group.'_settings_validate', array( $this, 'validate_settings' ), 10, 1 );

		add_action( 'wpsf_after_settings_'.$option_group, array( __CLASS__, 'process_modal' ), 10 );

	}

	/**
	 * Admin: Add settings menu item
	 */
	public function add_settings_page() {

		$this->settings_framework->add_settings_page( array(
			'parent_slug' => 'woocommerce',
			'page_title' => $this->page_title,
			'menu_title' => $this->menu_title,
			'capability' => 'manage_woocommerce'
		) );

	}

	/**
	 * Admin: Validate Settings
	 *
	 * @param arr $settings Un-validated settings
	 * @return arr $validated_settings
	 */
	public function validate_settings( $settings ) {

		// add_settings_error( $setting, esc_attr( 'iconic-woothumbs-error' ), $message, 'error' );

		return $settings;

	}

	/**
	 * Output process modal.
	 */
	public static function process_modal() {
		?>
		<div class="process-overlay"></div>
		<div class="process process--variation-visibility">
			<div class="process__content process__content--loading">
				<h3><?php _e( 'Loading...', 'iconic-woo-show-single-variations' ); ?></h3>
			</div>
			<div class="process__content process__content--processing">
				<h3><?php _e( 'Processing', 'iconic-woo-show-single-variations' ); ?> <span class="process__count-from"></span> <?php _e( 'to', 'iconic-woo-show-single-variations' ); ?> <span class="process__count-to"></span> <?php _e( 'of', 'iconic-woo-show-single-variations' ); ?> <span class="process__count-total"></span> <?php _e( 'items', 'iconic-woo-show-single-variations' ); ?>, <?php _e( 'please wait...', 'iconic-woo-show-single-variations' ); ?></h3>
				<div class="process__loading-bar">
					<div class="process__loading-bar-fill"></div>
				</div>
			</div>
			<div class="process__content process__content--complete">
				<h3><?php _e( 'Process complete', 'iconic-woo-show-single-variations' ); ?></h3>
				<p><span class="process__count-total"></span> <?php _e( 'items were processed.', 'iconic-woo-show-single-variations' ); ?></p>
				<a href="javascript: void(0);" class="button button-secondary process__close"><?php _e( 'Close', 'iconic-woo-show-single-variations' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Process product visibility link
	 *
	 * @return string
	 */
	public static function get_process_product_visibility_link() {
		ob_start();

		?>
		<a href="javascript: void(0);" class="button button-secondary" data-iconic-wssv-ajax="process_product_visibility"><?php _e( 'Process Product Visibility', 'iconic-woo-show-single-variations' ); ?></a>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get introduction.
	 *
	 * @return string
	 */
	public static function get_introduction() {
		ob_start();
		?>
		<h3><?php _e('Welcome to WooCommerce Show Single Variations', 'iconic-woo-show-single-variations'); ?></h3>
        <p><?php _e("You're awesome! We've been looking forward to having you onboard, and we're pleased to see the day has finally come.", 'iconic-woo-show-single-variations'); ?></p>
        <p><?php printf( __('Make yourself at home. If you get stuck, check out the <a href="%s" target="_blank">documentation</a>, or use the search beacon at the bottom right of this page.', 'iconic-woo-show-single-variations'), 'https://docs.iconicwp.com/category/31-show-single-variations' ); ?></p>
		<?php
		return ob_get_clean();
	}

    /**
     * Documentation link.
     */
    public static function documentation_link() {

        return sprintf( '<a href="https://docs.iconicwp.com/category/31-show-single-variations" class="button button-secondary" target="_blank">%s</a>', __('Read Documentation', 'iconic-woothumbs') );

    }

}