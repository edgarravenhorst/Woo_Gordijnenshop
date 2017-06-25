<!doctype html>

<html>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="author" content="<?php echo bloginfo('name'); ?>" />
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
  <link rel="icon" href="<?php bloginfo('template_url'); ?>/assets/images/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/assets/images/favicon.ico" type="image/x-icon" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header id="navigation">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-9 d-flex align-items-center">
          <a id="logo" href="<?php bloginfo('siteurl') ?>">
            <img src="<?php bloginfo('template_url'); ?>/assets/images/logo.png"/>
          </a>
        </div>
        <div class="col-sm-9 col-3 d-flex align-items-center justify-content-end">
          <?php get_template_part('partials/header', 'navigation') ?>
          <?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

            $count = WC()->cart->cart_contents_count;
            ?><a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php
            if ( $count > 0 ) {
              ?>
              <span class="cart-contents-count"><?php echo esc_html( $count ); ?></span>
              <?php
            }
            ?></a>

            <?php } ?>
          </div>
        </div>
      </div>
    </header>
