<?php

require_once dirname(__FILE__) . '/../vendor/Mobile_Detect.php';

global $device;
$device = new Mobile_Detect;

function get_device_info(){
	global $device;
	$devices_classes = '';

	if ( $device->isMobile() ) {
		$devices_classes .= ' is_mobile';
	}

	if( $device->isTablet() ){
		$devices_classes .= ' is_tablet';
	}

	if( $device->isMobile() && !$device->isTablet() ){
		$devices_classes .= ' is_phone';
	}

	if( $device->isiOS() ){
		$devices_classes .= ' is_ios';
	}

	if( $device->isAndroidOS() ){
		$devices_classes .= ' is_android';
	}

	return $devices_classes;
}

?>
