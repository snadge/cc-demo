<?php
define("QUERY_URI", "https://someserver.somedomain/Dir_1/Dir_2/");
define("AUTH_USER", "someuser");
define("AUTH_PWD", "somepass");

App::uses('HttpSocket', 'Network/Http');
class TestController extends AppController {
	public $components = array('Security', 'RequestHandler');

	public function index() {
		$this->set(array(
		    'message' => "Test",
		    '_serialize' => array('message')
		));
	}

	public function test() {

	$purchase_order_ids = $this->data['purchase_order_ids'];

	$product_type_ids_total = array();
	foreach($purchase_order_ids as $po_id) {
                $link =  QUERY_URI."{$po_id}?version=5&associated=true";

                $message = null;
                $httpSocket = new HttpSocket();
		$httpSocket->configAuth('Basic', AUTH_USER, AUTH_PWD);
                $response = $httpSocket->get($link, $message );
                $test_response = json_decode($response->body, TRUE);

		$pops = $test_response['data']['PurchaseOrderProduct'];
		foreach($pops as $pop) {
			$unit_quantity_initial = $pop['unit_quantity_initial'];
			$product_type_id = $pop['product_type_id'];
			$product = $pop['Product'];

			if(!isset($product_type_ids_total[$product['id']])) {
				$product_type_ids_total[$product['id']] = $unit_quantity_initial * (($product_type_id == 2) ?
					$product['volume'] : $product['weight']);
			}
			else {
				$product_type_ids_total[$product['id']] += $unit_quantity_initial * (($product_type_id == 2) ?
					$product['volume'] : $product['weight']);
			}

		}
	}

	$result = array();
	foreach($product_type_ids_total as $pt_id => $total) {
		$result[] = array('product_type_id' => $pt_id, 'total' => $total);
	}

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result'),
        ));
    }
}
