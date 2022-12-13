<?php

//Silence Is Good

/*
 * This action hook registers WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'cookiepayments_add_gateway_class' );
function cookiepayments_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_CookiePayments_Gateway'; // your class name is here
	return $gateways;
}



/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'cookiepayments_init_gateway_class' );
function cookiepayments_init_gateway_class() {

	class WC_CookiePayments_Gateway extends WC_Payment_Gateway {

 		/**
 		 * Class constructor, more about it in Step 3
 		 */
 		public function __construct() {
	        $this->id = 'cookiepayments'; // payment gateway plugin ID
	        $this->icon =  plugin_dir_url( ( __FILE__ ) ) . 'assets/img/cookiepayments-logo.png';; // URL of the icon that will be displayed on checkout page near your gateway name
	        $this->has_fields = true; // in case you need a custom credit card form
	        $this->method_title = 'CookiePayments Gateway';
	        $this->method_description = 'CookiePayments payment gateway'; // will be displayed on the options page

	        // gateways can support subscriptions, refunds, saved payment methods,
	        // but in this tutorial we begin with simple payments
	        $this->supports = array(
	            'products'
	        );

	        // Method with all the options fields
	        $this->init_form_fields();
	        //CookiePayments
	        // Load the settings.
	        $this->init_settings();
	        $this->title = $this->get_option( 'cookiepayments_title' );
	        $this->description = $this->get_option( 'cookiepayments_description' );
	        $this->enabled = $this->get_option( 'enabled' );
	        $this->get_option( 'cookiepayments_api_key' );
	        $this->get_option( 'cookiepayments_api_id' );
	        // This action hook saves the settings
	        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

	        add_action('wp_head', function(){
	        	//none
	        });

	        // thank you page hook
	        add_action('woocommerce_thankyou', function($order_id){
	        	$success = isset( $_GET['success'] ) ? $_GET['success'] : 0;
				$order = wc_get_order( $order_id );

				echo  $this->get_return_url( $order ) . '&success=' . $success;
				return;
				$order_data = $order->get_data();
         		$payment_method = $order_data['payment_method'];

	         	if($order->get_status() == 'pending' && $payment_method == 'cookiepayments'){
		         	$api_id = get_option('woocommerce_cookiepayments_settings')['cookiepayments_api_id'];    
		            $api_key = get_option('woocommerce_cookiepayments_settings')['cookiepayments_api_key']; 


					$order_id = $order_data['id']; 

		         	$order_total = $order_data['total'];

		         	$order_payment_method = $order_data['payment_method'];

		         	$order_billing_first_name = $order_data['billing']['first_name'];
					$order_billing_last_name = $order_data['billing']['last_name'];
					$order_billing_fullname = $order_billing_first_name." ".$order_billing_last_name;

					$order_billing_country = $order_data['billing']['country'];
					$order_billing_email = $order_data['billing']['email'];
					$order_billing_phone = $order_data['billing']['phone'];

					$order_customer_id = $order_data['customer_id'];

					$order_billing_address_1 = $order_data['billing']['address_1'];
					$order_billing_address_2 = $order_data['billing']['address_2'];

					$order_billing_address = $order_billing_address_1." ".$order_billing_address_2;

					$product_names = array();
					$product_ids = array();

				    foreach( $order->get_items() as $item_id => $item ){
				        $product = $item->get_product(); 
				        $product_id = $item->get_product_id();
				        $product_ids[] = $item->get_product_id();
				        $product_names[] = $item->get_name();
				    }

					$headers = array(); 

					array_push($headers, "Content-Type: application/json; charset=utf-8");
					array_push($headers, "ApiKey: $api_key");

					$cookiepayments_url = "https://www.cookiepayments.com/pay/ready";

					$product1 = isset($product_names[0]) ? $product_names[0] : '';
					$product2 = isset($product_names[1]) ? $product_names[1] : '';
					$product3 = isset($product_names[2]) ? $product_names[2] : '';
					$product4 = isset($product_names[3]) ? $product_names[3] : '';
					$product5 = isset($product_names[4]) ? $product_names[4] : '';

					$request_data_array = array(
					    'API_ID' => $api_id,
					    'ORDERNO' => $order_id,
					    'PRODUCTNAME' => $product_names[0],
					    'AMOUNT' => $order_total,
					    'BUYERNAME' => $order_billing_fullname,
					    'BUYEREMAIL' => $order_billing_email,
					    'RETURNURL' => $this->get_return_url( $order ) . '&success=' . $success,
					    'PRODUCTCODE' => $product_ids[0],
					    'PAYMETHOD' => $order_payment_method,
					    'BUYERID' => $order_customer_id,
					    'BUYERADDRESS' => $order_billing_address,
					    'BUYERPHONE' => $order_billing_phone,
					    'ETC1' => $product1,
					    'ETC2' => $product2,
					    'ETC3' => $product3,
					    'ETC4' => $product4,
					    'ETC5' => $product5,
					);

					$cookiepayments_json = json_encode($request_data_array, JSON_UNESCAPED_UNICODE);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, $cookiepayments_url);
					curl_setopt($ch, CURLOPT_POST, false);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $cookiepayments_json);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
					curl_setopt($ch, CURLOPT_TIMEOUT, 20);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					$response = curl_exec($ch);
					curl_close($ch);

					echo $response;
				}
	        });
 		}

		/**
 		* Plugin options, we deal with it in Step 3 too
 		*/
 		public function init_form_fields(){

	        $this->form_fields = array(
	            'enabled' => array(
	               'title'       => 'Enable/Disable',
	               'label'       => 'Enable CookiePayments Gateway',
	               'type'        => 'checkbox',
	               'description' => '',
	               'default'     => 'no'
	            ),
	            'cookiepayments_title' => array(
	               'title'       => 'Title',
	               'type'        => 'text',
	               'description' => 'This controls the title which the user sees during checkout.',
	               'default'     => 'CookiePayments Payment',
	               'desc_tip'    => true,
	            ),
	            'cookiepayments_description' => array(
	               'title'       => 'Description',
	               'type'        => 'textarea',
	               'description' => 'This controls the description which the user sees during checkout.',
	               'default'     => 'Pay with cookiepayments payment gateway.',
	            ),
	            'cookiepayments_api_id' => array(
	               'title'       => 'API ID',
	               'type'        => 'text'
	            ),
	            
	            'cookiepayments_api_key' => array(
	               'title'       => 'API Key',
	               'type'        => 'text'
	            ),
	           
	        );
      
	 	}

		/*
		 * We're processing the payments here, everything about it is in Step 5
		 */
		public function process_payment( $order_id ) {

	        global $woocommerce;
	        // we need it to get any order detailes
	        $order = wc_get_order( $order_id );
			return array(
		        'result' => 'success',
		        'redirect' => $this->get_return_url( $order )
		    );
					
	 	}
 	}
}