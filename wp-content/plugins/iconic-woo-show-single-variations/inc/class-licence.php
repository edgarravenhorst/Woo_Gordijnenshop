<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSSV_Licence.
 *
 * @class    Iconic_WSSV_Licence
 * @version  1.0.0
 * @category Class
 * @author   Iconic
 */
class Iconic_WSSV_Licence {

    /**
     * Run.
     */
    public static function run() {

        self::configure();
        self::add_filters();

    }

    /**
     * Configure.
     */
    public static function configure() {

        global $iconic_wssv_fs;

        if ( ! isset( $iconic_wssv_fs ) ) {
            // Include Freemius SDK.
            require_once ICONIC_WSSV_INC_PATH . 'freemius/start.php';

            $iconic_wssv_fs = fs_dynamic_init( array(
                'id'                  => '1036',
                'slug'                => 'show-single-variations',
                'type'                => 'plugin',
                'public_key'          => 'pk_e6402c968382fd116b38f146a3c83',
                'is_premium'          => ! ICONIC_WSSV_IS_ENVATO,
                'is_premium_only'     => ! ICONIC_WSSV_IS_ENVATO,
                'has_premium_version' => ! ICONIC_WSSV_IS_ENVATO,
                'has_paid_plans'      => ! ICONIC_WSSV_IS_ENVATO,
                'has_addons'          => false,
                'is_org_compliant'    => false,
                'menu'                => array(
                    'slug'           => 'iconic-wssv-settings',
                    'contact'        => false,
                    'support'        => false,
                    'account'        => false,
                    'pricing'		 => ! ICONIC_WSSV_IS_ENVATO,
                    'parent'         => array(
                        'slug' => 'woocommerce',
                    ),
                ),
            ) );
        }

        return $iconic_wssv_fs;

    }

    /**
     * Add filters.
     */
    public static function add_filters() {

        global $iconic_wssv_fs;

        $iconic_wssv_fs->add_filter( 'show_trial', '__return_false' );
        $iconic_wssv_fs->add_filter( 'templates/account.php', array( __CLASS__, 'back_to_settings_link' ), 10, 1 );
		$iconic_wssv_fs->add_filter( 'templates/billing.php', array( __CLASS__, 'back_to_settings_link' ), 10, 1 );
        add_filter( 'parent_file', array( __CLASS__, 'highlight_menu' ), 10, 1 );

    }

    /**
     * Highlight menu.
     */
    public static function highlight_menu( $parent_file ) {
	    global $plugin_page;

	    $page = empty( $_GET['page'] ) ? false : $_GET['page'];

	    if( 'iconic-wssv-settings-account' == $page ) {
		    $plugin_page = 'iconic-wssv-settings';
	    }

	    return $parent_file;
    }

    /**
     * Account link.
     */
    public static function account_link() {

        global $iconic_wssv_fs;

        return sprintf( '<a href="%s" class="button button-secondary">%s</a>', $iconic_wssv_fs->get_account_url(), __('Manage Licence', 'iconic-wssv') );

    }

    /**
     * Billing link.
     */
    public static function billing_link() {

        global $iconic_wssv_fs;

        return sprintf( '<a href="%s" class="button button-secondary">%s</a>', $iconic_wssv_fs->get_account_tab_url('billing'), __('Manage Billing', 'iconic-wssv') );

    }

    /**
     * Contact link.
     */
    public static function contact_link() {

        global $iconic_wssv_fs;

        return sprintf( '<a href="%s" class="button button-secondary">%s</a>', $iconic_wssv_fs->contact_url(), __('Create Support Ticket', 'iconic-wssv') );

    }

    /**
     * Get contact URL.
     */
    public static function get_contact_url( $subject = false, $message = false ) {

        global $iconic_wssv_fs;

        return $iconic_wssv_fs->contact_url( $subject, $message );

    }

    /**
     * Back to settings link.
     */
    public static function back_to_settings_link( $html ) {
	    return $html . sprintf( '<a href="%s" class="button button-secondary">&larr; %s</a>', admin_url( 'admin.php?page=iconic-wssv-settings' ), __('Back to Settings', 'iconic-wssv') );
    }

}