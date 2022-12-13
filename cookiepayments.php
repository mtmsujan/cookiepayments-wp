<?php
/*
 * Plugin Name: WooCommerce CookiePayments Gateway
 * Plugin URI: https://softtechiit.com
 * Description: Take payment by cookiepayments on your store.
 * Author: Sabbir Hossain
 * Author URI: https://github.com/devsabbirhossain
 * Version: 1.0.0
 */

if(file_exists(plugin_dir_path( __FILE__ ) . '/include/card-payment.php')){
	require_once(plugin_dir_path( __FILE__ ) . '/include/card-payment.php');
}

if(file_exists(plugin_dir_path( __FILE__ ) . '/include/vacct-payment.php')){
	require_once(plugin_dir_path( __FILE__ ) . '/include/vacct-payment.php');
}
 