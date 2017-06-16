<?php

// filter: tiny_mce_before_init
function setEditorColors( $init ) {

}

//filter:mce_buttons_2
function add_editor_styleselect_btn( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
add_filter('mce_buttons_2', 'add_editor_styleselect_btn');

function set_custom_editor_stylesheet() {
    add_editor_style( 'assets/css/wp-editor-style.css' );
}
add_action( 'admin_init', 'set_custom_editor_stylesheet' );

//filter:tiny_mce_before_init
function setup_custom_editor_settings( $init_array ) {

    // Define the style_formats array
    $style_formats = array(
        array(
            'title' => 'Button',
            'inline' => 'span',
            'classes' => 'button',
            'wrapper' => false,
        ),

        array(
            'title' => 'Button-full-width',
            'inline' => 'span',
            'classes' => 'button full-width',
            'wrapper' => false,
        ),
    );

    //change default colors
    $default_colours = '[
	    "000000", "Black",
	    "ffffff", "White",
	    "4A4A4A", "Gray",
	    "E7792B", "Orange",
	]';


    $init_array['textcolor_map'] = $default_colours;
    $init_array['style_formats'] = json_encode( $style_formats );

    return $init_array;
}
add_filter( 'tiny_mce_before_init', 'setup_custom_editor_settings' );
