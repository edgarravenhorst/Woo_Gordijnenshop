<?php
add_filter( 'wpsf_register_settings_iconic_wssv', 'iconic_wssv_settings' );

/**
 * WooCommerce Show Single variations Settings
 *
 * @param arr $wpsf_settings
 * @return arr
 */
function iconic_wssv_settings( $wpsf_settings ) {

	$wpsf_settings = array(

		/**
		 * Define: Tabs
		 *
		 * Define the tabs and their IDs
		 */
		'tabs' => array(

			array(
				'id' => 'dashboard',
				'title' => __( 'Dashboard', 'iconic-woo-show-single-variations' )
			),

		),

		/**
		 * Define: Sections
		 *
		 * Define the sections within our tabs, and give each
		 * section a related tab ID
		 */
		'sections' => array(

			// Welcome

			'welcome' => array(
                'tab_id' => 'dashboard',
                'section_id' => 'welcome',
                'section_title' => __( 'Welcome', 'iconic-woo-show-single-variations' ),
                'section_description' => Iconic_WSSV_Settings::get_introduction(),
                'section_order' => 0,
                'type' => 'message',
                'fields' => array()

            ),

            'license' => array(
                'tab_id' => 'dashboard',
                'section_id' => 'general',
                'section_title' => __('License & Account Settings', 'iconic-wssv'),
                'section_description' => '',
                'section_order' => 10,
                'fields' => array(
                    array(
                        'id' => 'account',
                        'title' => __('License', 'iconic-wssv'),
                        'subtitle' => __('Activate or sync your license, cancel your subscription, and manage your account information.', 'iconic-wssv'),
                        'type' => 'custom',
                        'default' => Iconic_WSSV_Licence::account_link()
                    ),
                    array(
                        'id' => 'billing',
                        'title' => __('Billing', 'iconic-wssv'),
                        'subtitle' => __('Update your billing information and view previous invoices.', 'iconic-wssv'),
                        'type' => 'custom',
                        'default' => Iconic_WSSV_Licence::billing_link()
                    ),
                )

            ),

            'tools' => array(
                'tab_id' => 'dashboard',
                'section_id' => 'tools',
                'section_title' => __('Tools', 'iconic-woo-show-single-variations'),
                'section_description' => '',
                'section_order' => 20,
                'fields' => array(
                    array(
                        'id' => 'process-product-visibility',
                        'title' => __( 'Process Product Visibility', 'iconic-woo-show-single-variations' ),
                        'subtitle' => __( 'Run this to set the visibility of all products.', 'iconic-woo-show-single-variations' ),
                        'type' => 'custom',
                        'default' => Iconic_WSSV_Settings::get_process_product_visibility_link()
                    )
                )

            ),

            array(
                'tab_id' => 'dashboard',
                'section_id' => 'support',
                'section_title' => __('Support', 'iconic-woothumbs'),
                'section_description' => '',
                'section_order' => 30,
                'fields' => array(
                    array(
                        'id' => 'support',
                        'title' => __('Support', 'iconic-woothumbs'),
                        'subtitle' => __('Get premium support with a valid license.', 'iconic-woothumbs'),
                        'type' => 'custom',
                        'default' => Iconic_WSSV_Licence::contact_link()
                    ),
                    array(
                        'id' => 'documentation',
                        'title' => __('Documentation', 'iconic-woothumbs'),
                        'subtitle' => __('Read the plugin documentation.', 'iconic-woothumbs'),
                        'type' => 'custom',
                        'default' => Iconic_WSSV_Settings::documentation_link()
                    ),
                )

            ),

		)

	);

	if( ICONIC_WSSV_IS_ENVATO ) {
	    unset( $wpsf_settings['sections']['license'] );
	    $wpsf_settings['sections']['welcome']['section_description'] .=
	    	'<p>' . __('Below you will find some useful plugin tools, and a link to support.', 'iconic-wssv') . '</p>' .
	    	'<p class="iconic-wssv-notice" style="padding: 20px; background-color: #DB5C59; margin: 2em 0 1em; border-radius: 5px; color: #fff; -webkit-font-smoothing: antialiased; font-weight: bold;">'.
	    	sprintf( __('NOTICE! All Iconic plugins will soon be moving away from Envato, so you will no longer be able to receive updates from CodeCanyon. <br><br>Please <a style="color: #fff; text-decoration: underline;" href="%s">send a request</a>, along with your <a style="color: #fff; text-decoration: underline;" href="%s" target="_blank">purchase code</a>, and I will provide you with a new yearly license and instructions on how to set it up. You will also be given a full year of support from your original date or purchase.', 'iconic-wssv'), Iconic_WSSV_Licence::get_contact_url( 'billing_issue', __( "Hello, I would like to request a new yearly license code. My Envato purchase code is: ", 'iconic-wssv' ) ), 'https://iconicwp.com/files/purchase-code.png' ) .
	    	'</p>';
    } else {
	    $wpsf_settings['sections']['welcome']['section_description'] .= '<p>' . __('Below you will find useful links to manage your license and billing, some plugin tools, and a link to support.', 'iconic-wssv') . '</p>';
    }

	return $wpsf_settings;

}