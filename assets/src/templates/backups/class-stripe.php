<?php
/**
 * LoadmorePosts
 *
 * @package WeMakeContentCMS
 */

namespace WEMAKECONTENTCMS_THEME\Inc;

use WEMAKECONTENTCMS_THEME\Inc\Traits\Singleton;
use \WP_Query;

class Stripe {

	use Singleton;
	private $theTable;
	private $lastResult;
	private $stripeSecretKey;
	private $stripePublishAble;
	
	protected function __construct() {
		// Replace with your own Stripe secret key
		$this->stripePublishAble = 'sk_test_51MYvdBI8VOGXMyoFiYpojuTUhvmS1Cxwhke4QK6jfJopnRN4fT8Qq6sy2Rmf2uvyHBtbafFpWVqIHBFoZcHp0vqq00HaOBUh1P';
		$this->stripeSecretKey = 'sk_test_51MYvdBI8VOGXMyoFiYpojuTUhvmS1Cxwhke4QK6jfJopnRN4fT8Qq6sy2Rmf2uvyHBtbafFpWVqIHBFoZcHp0vqq00HaOBUh1P';
		$this->productID = 'prod_NJlPpW2S6i75vM';
		$this->lastResult = false;
		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {
		global $wpdb;$this->theTable = $wpdb->prefix . 'fwp_stripe_subscriptions';
		add_action( 'futurewordpress/project/payment/stripe', [ $this, 'paymentStripe' ], 10, 0 );
		add_filter( 'template_include', [ $this, 'template_include' ], 10, 1 );
		// add_action( 'init', function() {
			// print_r( $this->checkoutSession( [ "email" => "customer@example.com", "source" => "tok_visa", "plan" => "monthly_subscription",
//  'amount' => 200, 'currency' => 'USD', 'success_url' => site_url( 'payment/success' ), 'cancel_url' => site_url( 'payment/cancel' ) ] ) );
			// $this->checkoutSession( 200, 'USD', 'https://www.example.com/success', 'https://www.example.com/fail' )
		// 	print_r( $this->paymentStripe() );

		// 	wp_die( 'Working on it' );
		// }, 10, 0 );
	}
	public function template_include( $template ) {
    $pay_retainer = get_query_var( 'pay_retainer' );// $order_id = get_query_var( 'order_id' );
		if ( $pay_retainer == false || $pay_retainer == '' ) {
      return $template;
    } else {
			$file = WEMAKECONTENTCMS_DIR_PATH . '/templates/dashboard/cards/pay_retainer.php';
			if( file_exists( $file ) && ! is_dir( $file ) ) {
          return $file;
        } else {
          return $template;
        }
		}
	}
	public function paymentStripe() {
		// Set the API endpoint and payment information
		$endpoint = "https://api.stripe.com/v1/charges";
		$data = array(
			"email" => "customer@example.com",
			"amount" => 1000,
			"currency" => "usd",
			"source" => "tok_visa",
			"description" => "Example charge",
		);

		// Encode the payment data as JSON
		$data_string = json_encode($data);

		// Create a cURL handle
		$ch = curl_init();

		// Set the cURL options
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecretKey . ":");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded',
			'Content-Length: ' . strlen($data_string)
		));

		// Execute the cURL request
		$result = curl_exec($ch);

		// Check for errors
		if( curl_errno( $ch ) ) {
			echo 'Error: ' . curl_error($ch);
		} else {
			echo 'Success: ' . $result;
		}

		// Close the cURL handle
		curl_close($ch);

	}
	private function createSubscription( $args ) {
		// Replace with your own Stripe secret key
		$secret_key = $this->stripeSecretKey;
		// Set the API endpoint and subscription information
		$endpoint = "https://api.stripe.com/v1/customers";
		$data = [
			"email" => "customer@example.com",
			"source" => "tok_visa",
			"plan" => "monthly_subscription",
		];
		// Encode the subscription data as JSON
		$data_string = json_encode($data);
		// Create a cURL handle
		$ch = curl_init();

		// Set the cURL options
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded', // application/json
			'Content-Length: ' . strlen($data_string)
		));

		// Execute the cURL request
		$result = curl_exec($ch);

		// Check for errors
		if( curl_errno( $ch ) ) {
			// echo 'Error: ' . curl_error($ch);
			$is_error = true;
		} else {
			$this->insertIntoTable( $result );
			$is_error = false;
		}
		// Close the cURL handle
		curl_close($ch);
		$this->lastResult = $result;
		return ( $is_error ) ? false : $result;
	}
	private function cancelSubscription() {
		// Replace with your own Stripe secret key
		$secret_key = $this->stripeSecretKey;
		// Replace with the customer's Stripe ID
		$customer_id = "cus_your_customer_id";
		// Replace with the subscription's Stripe ID
		$subscription_id = "sub_your_subscription_id";
		// Set the API endpoint and subscription information
		$endpoint = "https://api.stripe.com/v1/subscriptions/" . $subscription_id;
		$data = array(
			"customer" => $customer_id,
			"cancel_at_period_end" => true,
		);

		// Encode the subscription data as JSON
		$data_string = json_encode($data);

		// Create a cURL handle
		$ch = curl_init();

		// Set the cURL options
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded',
			'Content-Length: ' . strlen($data_string)
		));

		// Execute the cURL request
		$result = curl_exec($ch);

		// Check for errors
		if (curl_errno($ch)) {
			echo 'Error: ' . curl_error($ch);
		} else {
			echo 'Success: ' . $result;
		}
		// Close the cURL handle
		curl_close($ch);
	}
	/**
	 * In this example, a cURL request is sent to the Stripe API endpoint with the customer ID and subscription ID. The pause_collection parameter is set to mark_unpaid, which pauses the subscription and marks it as unpaid. The response from the API is returned and can be processed as desired.
	 */
	private function pauseSubscription() {
		// Replace with your own Stripe secret key
		$secret_key = $this->stripeSecretKey;

		// Replace with the customer's Stripe ID
		$customer_id = "cus_your_customer_id";

		// Replace with the subscription's Stripe ID
		$subscription_id = "sub_your_subscription_id";

		// Set the API endpoint and subscription information
		$endpoint = "https://api.stripe.com/v1/subscriptions/" . $subscription_id;
		$data = array(
			"customer" => $customer_id,
			"pause_collection" => array(
				"behavior" => "mark_unpaid"
			),
		);

		// Encode the subscription data as JSON
		$data_string = json_encode($data);

		// Create a cURL handle
		$ch = curl_init();

		// Set the cURL options
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded',
			'Content-Length: ' . strlen($data_string)
		));

		// Execute the cURL request
		$result = curl_exec($ch);

		// Check for errors
		if (curl_errno($ch)) {
			echo 'Error: ' . curl_error($ch);
		} else {
			echo 'Success: ' . $result;
		}
		// Close the cURL handle
		curl_close($ch);

	}
	private function modifySubscription() {
		$subscription_id = "cus_NJajfBA0350fbk";
		$new_amount = 1000; // new amount in cents

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/subscriptions/$subscription_id",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "plan={$new_amount}",// &prorate=true
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->stripeSecretKey}",
				"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			// echo "cURL Error #:" . $err;
			$is_error = true;
		} else {
			// echo "Subscription amount successfully updated!";
			$response = json_decode( $response, true );
			$is_error = ( isset( $response[ 'error' ] ) );
		}
		$this->lastResult = $response;
		return ( $is_error ) ? $response : $response;
	}
	private function getAllSubscriptions() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.stripe.com/v1/subscriptions",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer {$this->stripeSecretKey}"
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      // echo "cURL Error #:" . $err;
			return false;
    } else {
      $subscriptions = json_decode($response, true);
      return $subscriptions['data'];
    }
	}
	public function retriveAllSubscription() {
		$subscriptions = $this->getAllSubscriptions();
		foreach ($subscriptions as $subscription) {
				echo "Subscription ID: " . $subscription['id'] . "\n";
				echo "Plan ID: " . $subscription['plan']['id'] . "\n";
				echo "Customer ID: " . $subscription['customer'] . "\n";
				echo "Start Date: " . $subscription['start'] . "\n\n";
		}
	}
	private function findSubscriptionByEmail($email) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/customers?email={$email}",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->stripeSecretKey}"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$customers = json_decode($response, true);
			$subscriptions = array();
			foreach ($customers['data'] as $customer) {
				if (array_key_exists('subscriptions', $customer) && !empty($customer['subscriptions']['data'])) {
						$subscriptions = array_merge($subscriptions, $customer['subscriptions']['data']);
				}
			}
			return $subscriptions;
		}
	}
	public function retriveSubscriptionbyEmail( $email = false ) {
		if( ! $email ) {return;}
		$subscriptions = $this->findSubscriptionByEmail($email);
		foreach ($subscriptions as $subscription) {
			echo "Subscription ID: " . $subscription['id'] . "\n";
			echo "Plan ID: " . $subscription['plan']['id'] . "\n";
			echo "Customer ID: " . $subscription['customer'] . "\n";
			echo "Start Date: " . $subscription['start'] . "\n\n";
		}
	}
	public function createCusThenSubscription() {
		// Replace these values with your own secret key and data
		$secret_key = "your_secret_key";
		$email = "customer@example.com";
		$plan_id = "plan_G3U9O13EjKrgU6";
		$source = "tok_visa";

		// Use cURL to create a new customer
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/customers",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "email=$email&source=$source",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $secret_key",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$customer = json_decode($response);
			$customer_id = $customer->id;
		}

		// Use cURL to create a new subscription for the customer
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/subscriptions",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "customer=$customer_id&items[0][plan]=$plan_id",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $secret_key",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$subscription = json_decode($response);
			$subscription_id = $subscription->id;
		}
	}
	public function createCusThenSubscriptionSandbox() {
		// Replace these values with your own sandbox secret key and data
		$secret_key = $this->stripeSecretKey;
		$email = "customer@example.com";
		$plan_id = "plan_G3U9O13EjKrgU6";
		$source = "tok_visa";

		// Use cURL to create a new customer in the sandbox environment
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/customers",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "email=$email&source=$source",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $secret_key",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$customer = json_decode($response);
			$customer_id = $customer->id;
		}

		// Use cURL to create a new subscription for the customer in the sandbox environment
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/subscriptions",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "customer=$customer_id&items[0][plan]=$plan_id",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $secret_key",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$subscription = json_decode($response);
			$subscription_url = $subscription->invoice_pdf;
			echo "Subscription URL: " . $subscription_url;
		}

	}
	public function checkoutSession( $amount, $currency, $success_url, $cancel_url ) {
		$secret_key = $this->stripeSecretKey;
	}

	private function insertIntoTable( $json ) {
		global $wpdb;$args = (array) json_decode( $json, true );
		$user_id = get_current_user_id();

		// $status = $wpdb->query( $wpdb->prepare(
		// 	"INSERT INTO {$table}(user_id, user_email, subsc_id, user_object, user_address, invoice, phone, archived) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
		// 	$user_id, $args[ 'email' ], $args[ 'id' ], $args[ 'object' ], $args[ 'address' ], $args[ 'invoice_prefix' ], $args[ 'phone' ], maybe_serialize( $json )
		// ) );
		foreach( $args as $key => $val ) {
			if( $val == NULL ) {
				$args[ $key ] = false;
			}
		}
		$wpdb->insert( $this->theTable, [
			'user_id' => $user_id,
			'user_email' => $args[ 'email' ],
			'subsc_id' => $args[ 'id' ],
			'user_object' => $args[ 'object' ],
			'user_address' => $args[ 'address' ],
			'invoice' => $args[ 'invoice_prefix' ],
			'phone' => $args[ 'phone' ],
			'archived' => $json
		] );
	}
	public function getUserData( $user_id ) {
		global $wpdb;$args = (array) json_decode( $json );
		$user_id = get_current_user_id();
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->theTable} user_id=%d", $user_id ) );
		return $rows;
	}

	public function thePaymentlink() {
		// Your secret key from the Dashboard
		$secret_key = $this->stripeSecretKey;

		// Your Stripe product/plan ID
		$product_id = "prod_NJj26vTpYGKnfA";

		// Data for the payment link
		$data = array(
			"name" => "My Product",
			"amount" => 1000, // Amount in cents
			"currency" => "usd",
			"quantity" => 1,
			"recurring" => "plan",
			"interval" => "month",
			"metadata" => array(
				"order_id" => "6735"
			),
			"proration_behavior" => "create_prorations",
			"payment_behavior" => "allow_incomplete",
			"success_url" => "https://www.example.com/success",
			"cancel_url" => "https://www.example.com/cancel",
		);

		// Create the payment link
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.stripe.com/v1/checkout/sessions", // "https://api.stripe.com/v1/products/" . $product_id . "/prices",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => http_build_query($data),
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $secret_key,
				"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		print_r( $response );

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$price = json_decode($response);
			$payment_link = $price->url;

			// Use the payment link
			// header("Location: " . $payment_link);
		}
	}
}
