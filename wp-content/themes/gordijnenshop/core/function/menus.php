<?php
//register menus
function register_default_menus() {
    register_nav_menu( 'main-menu', 'Main-menu' );
    // register_nav_menu( 'footer-menu', 'footer-menu' );
}
add_action( 'after_setup_theme', 'register_default_menus' );
