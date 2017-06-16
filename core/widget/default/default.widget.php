<?php

class Default_Widget extends WP_Widget{

	function __construct() {
    $widgettitle = "Default Widget";
		parent::WP_Widget(false, $widgettitle);
	}

	function update($new_instance, $old_instance){
		return $new_instance;
	}

	function form($instance){
    // wordt niet gebruikt, dit doet ACF
	}

	function widget($args, $instance){
		$widget_id = "widget_" . $args["widget_id"];

		include(realpath(dirname(__FILE__)) . "/widget.view.php"); // Widget output
	}
}
register_widget('Default_Widget');
