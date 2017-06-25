<?php
// require_once(dirname(__FILE__) . '/vendor/autoload.php');
$environment = "development";

// Display errors
if($environment == "development"){
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 1);
}

// Functions
require_once "core/function/scripts-styles.php";
require_once "core/function/menus.php";
require_once "core/function/theme-support.php";
require_once "core/function/sidebars.php";
require_once "core/function/editor-stylesheet.php";
require_once "core/widget/product-gallery/widget.php";
require_once "core/widget/sidebar-select/widget.php";

// Shortcode

// Widgets
// require_once "core/widget/default/default.widget.php";

// Posttypes
// require_once "core/posttype/default.ptype.php";


function my_header_add_to_cart_fragment( $fragments ) {

    ob_start();
    $count = WC()->cart->cart_contents_count;
    ?><a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php
    if ( $count > 0 ) {
        ?>
        <span class="cart-contents-count"><?php echo esc_html( $count ); ?></span>
        <?php
    }
        ?></a><?php

    $fragments['a.cart-contents'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );
