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
require_once "core/function/editor-stylesheet.php";
require_once "core/widget/product-gallery/widget.php";

// Shortcode

// Widgets
// require_once "core/widget/default/default.widget.php";

// Posttypes
// require_once "core/posttype/default.ptype.php";
