<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * My awesome closure command
 *
 * <message>
 * : An awesome message to display
 *
 * --append=<message>
 * : An awesome message to append to the original message.
 *
 * @when before_wp_load
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	if( ! class_exists('importOrderCLI') ) {
		class importOrderCLI {

			public function __construct() {
				// example constructor called when plugin loads
			}

			public function import_orders( $args = array() ) {
//				if( empty( $args ) ) $args[0] = 3;

				//print_r( $args ); die;
				global $wpdb;
				$counter = 0;
				$fail_counter = 0;
				$pi_order_data = array();
				$pi_order_address =array();

				$pi_order_get_query = "SELECT * FROM `monetisation_order`";
				//$pi_order_get_query = 'SELECT * FROM `monetisation_order` ORDER BY `monetisation_order`.`amount_paid` DESC LIMIT 2';
				$pi_order_results = $wpdb->get_results( $pi_order_get_query );

				if ( is_wp_error( $pi_order_results ) ) {
					WP_CLI::warning( 'Result Error: ' . $pi_order_results->get_error_message() );
				}

				if( !empty( $pi_order_results ) ) {
					foreach ( $pi_order_results as $pi_order ) {
						$pi_order_data['pi_order_uuid'] = $pi_order->uuid;
						$pi_order_data['pi_order_account_id'] = $pi_order->account_id;
						$pi_order_data['pi_order_time_created'] = $pi_order->time_created;
						$pi_order_data['pi_order_is_paid'] = $pi_order->is_paid;
						$pi_order_data['pi_order_amount_paid'] = $pi_order->amount_paid;
						$pi_order_data['pi_order_reference_code'] = $pi_order->reference_code;
						$pi_order_data['pi_order_apply_card_charge'] = $pi_order->apply_card_charge;
						$pi_order_data['pi_order_xero_invoice_id'] = $pi_order->xero_invoice_id;
						$pi_order_data['pi_order_sent_to_xero'] = $pi_order->sent_to_xero;
						$pi_order_data['pi_order_notes'] = $pi_order->notes;
						$pi_order_data['pi_order_supplier_product_id'] = $pi_order->supplier_product_id;
						$pi_order_data['pi_order_supplier_profile_id'] = $pi_order->supplier_profile_id;


						$pi_order_address['email'] = $pi_order->invoice_email;
						$pi_order_address['address_1'] = $pi_order->address1;
						$pi_order_address['address_2'] = $pi_order->address2;
						$pi_order_address['city'] = $pi_order->city;
						$pi_order_address['postcode'] = $pi_order->postcode;
						$pi_order_address['country'] = $pi_order->country;
						$pi_order_address['state'] = $pi_order->state;
						$pi_order_address['company'] = $pi_order->company_name;
						$pi_order_address['first_name'] = $pi_order->company_name;
						$pi_order_address['last_name'] = $pi_order->last_name;

						$result = $this->import_woocommerce_order( $pi_order_data, $pi_order_address );

						if( $result ) {
							WP_CLI::success('Order Created = ' . $result);
							$counter++;
						} else {
							WP_CLI::warning( 'Unable to create order at the moments', false );
							$fail_counter++;
						}
					}
					WP_CLI::success('Total Order created = ' . $counter);
					WP_CLI::success('Unable to create Orders = ' . $fail_counter);
				} else {
					WP_CLI::warning( 'No orders data found for query ' . $pi_order_get_query, false );
				}
			}

			public function import_woocommerce_order( $order_data, $address ) {
				$order_args = array();
				if( !empty( $order_data['pi_order_account_id'] ) ) {
					$users = get_users(array(
						'meta_key'     => 'django_user_id',
						'meta_value'   => $order_data['pi_order_account_id'],
						'meta_compare' => '=',
					));
					$user_id = isset($users[0]->data->ID) ? $users[0]->data->ID : '' ;
					if( !empty( $user_id ) ) {
						$order_args['customer_id'] = $user_id;
						WP_CLI::success( 'User Added to order. User ID is : ' .$user_id );
					} else {
						WP_CLI::warning( 'User not available for assign the order. User djongo ID is: ' . $order_data['pi_order_account_id'], true );
					}
					if( !empty( $order_data['pi_order_amount_paid'] ) ) {
						$order_args['total'] = $order_data['pi_order_amount_paid'];
					}
				}

				//Create Order.
				$pi_order = wc_create_order($order_args);

				if( ! $pi_order ) {
					WP_CLI::warning( 'We are unable to place order at the moment.' . print_r($order_data, true) );
					return $pi_order;
				}

				$product_response = $this->pi_add_products_order($pi_order, $order_data['pi_order_uuid']);

				//Adds the products in the order.
				if( $product_response ) {
					WP_CLI::success('Products added to the order = ' . print_r($product_response, true));
				} else {
					WP_CLI::warning( 'Unable to add products for Order: ' . $order_data['pi_order_uuid'] );
				}

				//Set order create & paid date.
				$pi_order->set_date_created( strtotime($order_data['pi_order_time_created']) );
				$pi_order->set_date_paid( strtotime($order_data['pi_order_time_created']) );
				unset( $order_data['pi_order_time_created'] );

				//Set WooCommerce Billing & Shipping Addresses.
				$pi_order->set_address( $address, 'billing' );
				$pi_order->set_address( $address, 'shipping' );

				//Set status completed if paid Or it will set as pending payment.
				if( $order_data['pi_order_is_paid'] ) {
					$pi_order->set_status('completed');
					//Set order reference code.
					if( $order_data['pi_order_reference_code'] ) {
						$pi_order->set_transaction_id( $order_data['pi_order_reference_code'] );
					}
				}

				//Ap
				if( 0 > intval($order_data['pi_order_amount_paid'] ) || 0 === intval($order_data['pi_order_amount_paid'] ) ) {
					if( wc_get_coupon_id_by_code('free_credit')) {
						if( $pi_order->apply_coupon('free_credit') ) {
							WP_CLI::success('Coupon Applied to this order. Coupon Code is = free_credit', true);
						}
					} else {
						WP_CLI::warning( 'Coupon free_credit not exist. Please create the Coupon for discount orders.' );
					}
				} else {
					$pi_order->add_order_note( 'Customer Paid '.$order_data['pi_order_amount_paid']. ' amount for this order.' );
				}

				$this->pi_save_extra_order_meta( $pi_order, $order_data );

				if( !empty( $order_data['pi_order_notes'] ) ) {
					$pi_order->add_order_note( $order_data['pi_order_notes'] );
				}

				//$pi_order->save();
				$pi_order->calculate_totals();
				if( $pi_order )
					return $pi_order->get_id();
				else
					return false;
			}

			protected function pi_add_products_order( $pi_order, $pi_order_id ) {
				global $wpdb;
				$success = array();
				$pi_get_products_id = 'SELECT `job_product_id` FROM `monetisation_jobproductorderitem` WHERE `order_id`="'.$pi_order_id.'"';
				$pi_product_temp_arr = array();
				$pi_product_results = $wpdb->get_results( $pi_get_products_id );
				if ( is_wp_error( $pi_product_results ) ) {
					WP_CLI::warning( 'Product not found for the order: ' . $pi_order_id, false );
				}
				if( !empty( $pi_product_results ) ) {
					foreach ( $pi_product_results as $pi_djongo_pid ) {
						$product_id = isset($pi_djongo_pid->job_product_id) ? $pi_djongo_pid->job_product_id : '';
						if (!isset($pi_product_temp_arr[$product_id]) && !empty($product_id)) {
							$pi_product_temp_arr[$product_id] = 1;
						} elseif (isset($pi_product_temp_arr[$product_id]) && !empty($product_id)) {
							$pi_product_temp_arr[$product_id]++;
						}
					}

					if( !empty( $pi_product_temp_arr ) ) {
						foreach ( $pi_product_temp_arr as $key => $value ) {
							$product = $this->pi_get_product_id_by_djongo_pid($key);
							if ($product) {
								$pi_order->add_product(wc_get_product($product), $value);
								$success[] = $product;
							}
						}
					}
				}
				if( empty( $success ) ) {
					return false;
				}
				return $success;
			}

			protected function pi_get_product_id_by_djongo_pid( $pi_djongo_pid ) {
				$params = array(
					'post_type' => 'product',
					'meta_query' => array(
						array('key' => 'pi_product_uuid',
							'value' => $pi_djongo_pid,
							'compare' => '=',
						)
					),
					'fields' => 'ids'
				);
				$wc_query = new WP_Query($params);
				$postsIds = $wc_query->posts;
				wp_reset_postdata();
				wp_reset_query();
				return isset($postsIds[0]) ? $postsIds[0] : false;
			}

			protected function pi_save_extra_order_meta( $order, $order_data ) {
				if( empty( $order ) || empty( $order_data ) ) {
					return;
				}

				foreach ( $order_data as $order_meta_key => $order_meta_val ) {
					$order->update_meta_data( '_'.$order_meta_key, $order_meta_val );
				}
			}

			public function delete_orders( $args = array() ) {
				$query = new WC_Order_Query( array(
					'limit' => -1,
					'orderby' => 'date',
					'order' => 'DESC',
					'return' => 'ids',
				) );
				$orders = $query->get_orders();
				if( !empty( $orders ) ) {
					foreach ( $orders as $order_id ) {
						wp_delete_post( $order_id, true);
						WP_CLI::success( 'Order deleted. Order ID = '. $order_id, true );
					}
				} else {
					WP_CLI::warning( 'No orders found.' );
				}
			}

		}
	}
	WP_CLI::add_command( 'perfomance', 'importOrderCLI' );
}
