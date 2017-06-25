<?php

// Remove the fixed/important margin on the HTML, it breaks our flexbox footer
function remove_html_styling() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'remove_html_styling');

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

add_action('wp_enqueue_scripts', 'core_scripts');
function core_scripts()
{
  if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {


    wp_enqueue_script('jquery'); // Enqueue it!
    //wp_deregister_script('jquery'); // Deregister WordPress jQuery
    //wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', array(), '1.11.2');


    /**
    *
    * Minified and concatenated scripts
    *
    *     @vendors     plugins.min,js
    *     @custom      scripts.min.js
    *
    *     Order is important
    *
    */

    wp_register_script('core_customJs', get_template_directory_uri() . '/assets/js/site.js'); // Custom scripts
    wp_enqueue_script('core_customJs'); // Enqueue it!


    /**
    *
    * Non-minifies scripts
    *
    */

    // wp_register_script('core_abc_js', get_template_directory_uri() . '/assets/js/vendor/abc.min.js' ); // Conditional script(s)
    // wp_enqueue_script('core_abc_js'); // Enqueue it!

  }

}


/**
*
* Styles: Frontend with no conditions, Add Custom styles to wp_head
*
* @since  1.0
*
*/
add_action('wp_enqueue_scripts', 'core_styles'); // Add Theme Stylesheet
function core_styles()
{

  /**
  *
  * Minified and Concatenated styles
  *
  */
  wp_register_style('wp_style', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
  wp_enqueue_style('wp_style'); // Enqueue it!

  wp_register_style('core_style', get_template_directory_uri() . '/assets/css/site.css', array(), '1.0', 'all');
  wp_enqueue_style('core_style'); // Enqueue it!


  /**
  *
  * Google fonts
  *     Must be included this way to avoid Firefox issues
  *
  */
  // wp_register_style('core_gfonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300,800,400', array(), '1.0', 'all');
  // wp_enqueue_style('core_gfonts'); // Enqueue it!


  /**
  *
  * Non-minified or non-concatenated styles
  *
  */

  // wp_register_style('core_xyz_css', get_template_directory_uri() . '/assets/css/vendor/xyz.css', array(), '1.0', 'all');
  // wp_enqueue_style('core_xyz_css'); // Enqueue it!


}

/**
*
* Comment Reply js to load only when thread_comments is active
*
* @since  1.0.0
*
*/
add_action( 'wp_enqueue_scripts', 'core_enqueue_comments_reply' );
function core_enqueue_comments_reply() {
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
