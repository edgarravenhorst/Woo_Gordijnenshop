<?php

class ProductGalleryWidget extends WP_Widget
{
	function ProductGalleryWidget()
    {
        $widget_ops = array('classname' => 'product-gallery-widget', 'description' => 'Toon product gallerij' );
        $this->WP_Widget('ProductGalleryWidget', 'CORE - Product Gallerij', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'count' => '', 'category' => '' ) );
        $count = $instance['count'];
        $category = $instance['category'];

        include "widget-admin.php";
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['count'] = $new_instance['count'];
        $instance['category'] = $new_instance['category'];
        // $instance['show_filter'] = $new_instance['show_filter'];
        return $instance;
    }

    function widget($args, $instance)
    {
        add_action('wp_footer',array($this,'init_widget_scripts'));

        extract($args, EXTR_SKIP);

        //$show_filter = $instance[ 'show_filter' ] ? 'true' : 'false';

        echo $before_widget;
        $count = $instance['count'];
        $category = $instance['category'];

        include 'widget-public.php';
        echo $after_widget;
    }

    function init_widget_scripts(){
    }
}
add_action( 'widgets_init', create_function('', 'return register_widget("ProductGalleryWidget");') );?>
