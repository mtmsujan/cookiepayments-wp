<?php
/*
 * Plugin Name: WooCommerce CookiePayments Gateway
 * Plugin URI: https://softtechiit.com
 * Description: Take payment by cookiepayments on your store.
 * Author: Mohammad Toriqul Mowla Sujan
 * Author URI: https://softtechitltd.com
 * Version: 1.0.0
 * Textdomain: cookie
 */

if(file_exists(plugin_dir_path( __FILE__ ) . '/include/card-payment.php')){
	require_once(plugin_dir_path( __FILE__ ) . '/include/card-payment.php');
}

if(file_exists(plugin_dir_path( __FILE__ ) . '/include/vacct-payment.php')){
	require_once(plugin_dir_path( __FILE__ ) . '/include/vacct-payment.php');
}
 