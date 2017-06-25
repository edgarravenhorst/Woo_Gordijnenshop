<?php

function register_default_sidebars() {
    register_sidebar( array(
        'name'          => __( 'Cart Sidebar', 'cart-sidebar' ),
        'id'            => 'cart-sidebar',
        'before_widget' => '<div class="sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar( array(
        'name'          => __( 'Sidebar', 'default-sidebar' ),
        'id'            => 'page-sidebar',
        'before_widget' => '<div class="sidebar-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action( 'widgets_init', 'register_default_sidebars' );
