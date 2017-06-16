<?php

add_action( 'init', 'create_post_type_default');
function create_post_type_default() {

  $name = 'Defaults';
  $singular = 'Default';
  $plural = 'Defaults';

  $lowname = strtolower($name);

  register_post_type(
    $lowname,
    array(
      'labels' => array(
        'name'               => __( $name, 'coderehab-base' ),
        'singular_name'      => __( $singular, 'coderehab-base' ),
        'all_items'          => __( 'Alle ' . $plural, 'coderehab-base' ),
        'add_new'            => __( 'Nieuwe ' . $singular . ' toevoegen', 'coderehab-base' ),
        'add_new_item'       => __( 'Nieuwe ' . $singular . ' toevoegen', 'coderehab-base' ),
        'edit'               => __( 'Aanpassen', 'coderehab-base' ),
        'edit_item'          => __( $singular . ' bewerken', 'coderehab-base' ),
        'new_item'           => __( 'Nieuwe ' . $singular, 'coderehab-base' ),
        'view'               => __( 'Bekijk ' . $singular, 'coderehab-base' ),
        'view_item'          => __( 'Bekijk ' . $singular, 'coderehab-base' ),
        'search_items'       => __( 'Zoek ' . $plural, 'coderehab-base' ),
        'not_found'          => __( 'Geen ' . $plural. ' gevonden', 'coderehab-base' ),
        'not_found_in_trash' => __( 'Geen ' . $plural. ' in de prullenbak gevonden', 'coderehab-base' ),
        'parent'             => __( 'Hoofd ' . $singular, 'coderehab-base' )
      ),
      'public' => true,
      'has_archive' => true,
      'hierarchical' => true,
      'rewrite' => array(
        'slug'       => $plural,
        'with_front' => false,
      ),
      'show_in_nav_menus' => true,

      'menu_position' => 5, // Onder berichten plaatsen
      'menu_icon'           => 'dashicons-location',

      'rewrite' => array(
        'slug' => $lowname,
        'with_front' => false
      ),

      'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'excerpt',
        'page-attributes'
      ),
      )
    );
    flush_rewrite_rules();
  }

  // add_action( 'init', 'build_taxonomies', 0 );
  // function build_taxonomies() {
  //   register_taxonomy(
  //     'cat_agenda',
  //     'agenda',  // this is the custom post type(s) I want to use this taxonomy for
  //     array(
  //       'hierarchical' => true,
  //       'label' => 'Categorieën',
  //       'query_var' => true,
  //       'rewrite' => true
  //     )
  //   );
  // }
