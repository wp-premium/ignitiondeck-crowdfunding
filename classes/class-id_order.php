<?php
class ID_Order {
	var $id;
	var $fname;
	var $lname;
	var $email;
	var $address;
	var $city;
	var $state;
	var $zip;
	var $country;
	var $project_id;
	var $txn_id;
	var $preapproval_key;
	var $level;
	var $price;
	var $status;
	var $date;

	function __construct(
			$id = null,
			$fname = '',
			$lname = '',
			$email = '',
			$address = '',
			$country = '',
			$state = '',
			$city = '',
			$zip = '',
			$project_id = 0,
			$txn_id = '',
			$preapproval_key = null,
			$level = 0,
			$price = '0',
			$status = 'P',
			$date = null
		)
	{
		if (empty($date)) {
			$tz = get_option('timezone_string');
			if (empty($tz)) {
				$tz = 'UTC';
			}
			date_default_timezone_set($tz);
			$date = date('Y-m-d H:i:s');
		}
		$this->id = $id;
		$this->fname = $fname;
		$this->lname = $lname;
		$this->email = $email;
		$this->address = $address;
		$this->country = $country;
		$this->state = $state;
		$this->city = $city;
		$this->zip = $zip;
		$this->project_id = $project_id;
		$this->txn_id = $txn_id;
		$this->preapproval_key = $preapproval_key;
		$this->level = $level;
		$this->price = $price;
		$this->status = $status;
		$this->date = $date;
	}

	function get_order() {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE id = %s', $this->id);
		$res = $wpdb->get_row($sql);
		return $res;
	}

	function check_new_order() {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE transaction_id = %s', $this->txn_id);
		$res = $wpdb->get_row($sql);
		return $res;
	}

	function check_new_preorder() {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE preapproval_key = %s', $this->preapproval_key);
		$res = $wpdb->get_row($sql);
		return $res;
	}

	function insert_order() {
		global $wpdb;
		$new = $this->check_new_order();
		if (empty($new)) {
			$sql = 'INSERT INTO '.$wpdb->prefix .'ign_pay_info (
								first_name,
								last_name,
								email,
								address,
								country,
								state,
								city,
								zip,
								product_id,
								transaction_id,
								product_level,
								prod_price,
								status,
								created_at
							)
							values (
								"'.$this->fname.'",
								"'.$this->lname.'",
								"'.$this->email.'",
								"'.$this->address.'",
								"'.$this->country.'",
								"'.$this->state.'",
								"'.$this->city.'",
								"'.$this->zip.'",
								"'.$this->project_id.'",
								"'.$this->txn_id.'",
								"'.$this->level.'",
								"'.$this->price.'",
								"'.$this->status.'",
								"'.$this->date.'"
							)';
			$res = $wpdb->query($sql);
			$this->id = $wpdb->insert_id;
			if (isset($this->id)) {
				do_action('id_payment_success', $this->id);
				return $this->id;
			}
		}
		else {
			return $new->id;
		}
	}

	function insert_preorder() {
		global $wpdb;
		$new = $this->check_new_preorder();
		if (empty($new)) {
			$sql = 'INSERT INTO '.$wpdb->prefix .'ign_pay_info (
								first_name,
								last_name,
								email,
								address,
								country,
								state,
								city,
								zip,
								product_id,
								preapproval_key,
								product_level,
								prod_price,
								status,
								created_at
							)
							values (
								"'.$this->fname.'",
								"'.$this->lname.'",
								"'.$this->email.'",
								"'.$this->address.'",
								"'.$this->country.'",
								"'.$this->state.'",
								"'.$this->city.'",
								"'.$this->zip.'",
								"'.$this->project_id.'",
								"'.$this->preapproval_key.'",
								"'.$this->level.'",
								"'.$this->price.'",
								"'.$this->status.'",
								"'.$this->date.'"
							)';
			$res = $wpdb->query($sql);
			$this->id = $wpdb->insert_id;
			return $this->id;
		}
		else {
			return $new->id;
		}
	}

	function update_order() {
		global $wpdb;
		$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'ign_pay_info SET first_name = %s, last_name = %s, email = %s, address = %s, country = %s, state = %s, city = %s, zip = %s, product_id = %d, preapproval_key = %s, product_level = %d, prod_price = %s, status = %s, created_at = %s WHERE id = %d', 
			$this->fname,
			$this->lname,
			$this->email,
			$this->address,
			$this->country,
			$this->state,
			$this->city,
			$this->zip,
			$this->project_id,
			$this->preapproval_key,
			$this->level,
			$this->price,
			$this->status,
			$this->date,
			$this->id);
		$res = $wpdb->query($sql);
	}

	public static function get_orders_by_project($project_id, $misc = null) {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE product_id = %d'.(!empty($misc) ? ' '.$misc : ''), $project_id);
		$res = $wpdb->get_results($sql);
		return $res;
	}

	public static function get_total_orders_by_project($project_id) {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT COUNT(*) AS count FROM '.$wpdb->prefix.'ign_pay_info WHERE product_id = %d', $project_id);
		$res = $wpdb->get_row($sql);
		return $res;
	}

	public static function get_preapprovals($product_id) {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE product_id = %d AND status = %s', $product_id, 'W');
		$res = $wpdb->get_results($sql);
		return $res;
	}

	public static function add_txn_id($order_id, $txn_id) {
		global $wpdb;
		$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'ign_pay_info SET transaction_id = %s WHERE id = %d', $txn_id, $order_id);
		$res = $wpdb->query($sql);
	}

	public static function get_order_by_txn($txn_id) {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ign_pay_info WHERE transaction_id = %s', $txn_id);
		$res = $wpdb->get_row($sql);
		return $res;
	}

	public static function delete_order($id) {
		global $wpdb;
		$sql = 'DELETE FROM '.$wpdb->prefix.'ign_pay_info WHERE id = '.$id;
		$res = $wpdb->query($sql);
	}

}
?>