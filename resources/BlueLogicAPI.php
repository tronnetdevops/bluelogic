<?php
	abstract class BlueLogicAPI {

		const BL_API_URI = "http://77.233.142.21/bluelogic/BLWP_direct.php";

		static public $requestBaseData = array(
			"ClientID" => "",
			"Version" => "1.00"
		);

		static public $available_calls = array(
			"CreateCustomer" => array(
				"Surname", "GUID", "Address1", "PostCode", "OptIn", "County", "Country"
			), 
			"CreateOrderHeader" => array(
				"CustomerID", "CustomerAddressID", "ClientOrderRef", "ShippingMethod", "PaymentMethod", "Currency", "CurrencyRate"
			), 
			"CreateOrderLine" => array(
				"OrderID", "ProductCode", "Qnty", "UnitPrice"
			), 
			"ProcessOrder" => array(
				"OrderID"
			)
		);

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
			$submittedValues = self::MeetsRequirements($params, self::$available_calls[ $data["Action"] ]);

			if (is_array($submittedValues)){
				$data = array_merge($submittedValues, $data);

			 	$response = parse_ini_string(AJAX::Request(self::BL_API_URI, $data));

				//AJAX::Response("json", $response);
				return $response;
			} else {
				AJAX::Response("json", array(), 1, $submittedValues);
			}
		}
	}
