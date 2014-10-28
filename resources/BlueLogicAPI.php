<?php
	abstract class BlueLogicAPI {

		const BL_API_URI = "http://77.233.142.21/bluelogic/BLWP_direct.php";

		static protected $requestBaseData = array(
			"ClientID" => "E4F6BB40-A353-4211-995E-6AA5713134A5",
			"Version" => "1.00"
		);

		static public $available_calls = array(
			"createCustomer" => array(
				"Surname", "GUID", "Address1", "PostCode", "OptIn", "County", "Country"
			), 
			"createOrderHeader" => array(
				"CustomerID", "CustomerAddressID", "ClientOrderRef", "ShippingMethod", "PaymentMethod", "Currency", "CurrencyRate"
			), 
			"createOrder" => array(
				"OrderID", "ProductCode", "Qnty", "UnitPrice"
			), 
			"processOrder" => array(
				"OrderID"
			)
		);

		static public function createCustomer($params){
			$data = array_merge(self::$requestBaseData, array(
				"Action" => "CreateCustomer"
			));

			$params["GUID"] = session_id();

			self::Request($params, $data);
		}

		static public function createOrderHeader($params){
			$data = array_merge(self::$requestBaseData, array(
				"Action" => "CreateOrderHeader"
			));

			self::Request($params, $data);
		}

		static public function createOrder($params){
			$data = array_merge(self::$requestBaseData, array(
				"Action" => "CreateOrder"
			));

			self::Request($params, $data);
		}


		static public function processOrder($params){
			$data = array_merge(self::$requestBaseData, array(
				"Action" => "ProcessOrder"
			));

			self::Request($params, $data);
		}

		static public function MeetsRequirements($params, $reqs){
			$data = array();

			$intersects = array_intersect(array_keys($params), $reqs);

			if (count($intersects) == count($reqs)){

				foreach($reqs as $req){
					$data[$req] = $params[$req];
				}

				return $data;
			} else {
				return "The following fields are missing: ". implode(array_diff($reqs, $intersects), ", ");
			}
		}

		static public function Request($params, $data){
			$submittedValues = self::MeetsRequirements($params, self::$available_calls[ $params["action"] ]);

			if (is_array($submittedValues)){
				$data = array_merge($submittedValues, $data);

			 	$response = parse_ini_string(AJAX::Request(self::BL_API_URI, $data));

				AJAX::Response("json", $response);
			} else {
				AJAX::Response("json", array(), 1, $submittedValues);
			}
		}
	}