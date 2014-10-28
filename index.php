<?php
	error_reporting(-1);
	header("Access-Control-Allow-Origin: ". trim($_SERVER["HTTP_REFERER"], "/") );

	session_start();

	include('./resources/AJAX.php');
	include('./resources/BlueLogicAPI.php');

	$required = array(
		"Surname", 
		"GUID", 
		"Address1", 
		"PostCode", 
		"County", 
		"Country", 
		"OptIn", 
		"ShippingMethod", 
		"PaymentMethod", 
		"Currency", 
		"CurrencyRate", 
		"ProductCode", 
		"Qnty", 
		"UnitPrice", 
	);

	$data = BlueLogicAPI::MeetsRequirements($_REQUEST, $required); 

	if (is_array($data)){

		error_log(var_export($data, true));

		$data["GUID"] = "customer" . $data["GUID"];

		$memcache = new Memcached;
		$memcache->addServer("localhost", 11211);

		$data["CustomerID"] = $memcache->get($data["GUID"]+"CustomerID");
		$data["CustomerAddressID"] = $memcache->get($data["GUID"]+"CustomerAddressID");

		if (!isset($CustomerID)){
			$createCustomerResponse = BlueLogicAPI::Request($data, array_merge(BlueLogicAPI::$requestBaseData, array(
				"Action" => "CreateCustomer"
			)));

			if ($createCustomerResponse["status"] == "OK"){
				$data["CustomerID"] = $createCustomerResponse["CustomerID"];
				$data["CustomerAddressID"] = $createCustomerResponse["CustomerAddressID"];

				$memcache->get($data["GUID"]+"CustomerID", $data["CustomerID"]);
				$memcache->get($data["GUID"]+"CustomerAddressID", $data["CustomerAddressID"]);
			} else {
				AJAX::Response("json", array(), 1, $createCustomerResponse);
			}
		}

		$createOrderHeaderResponse = BlueLogicAPI::Request($data, array_merge(BlueLogicAPI::$requestBaseData, array(
			"Action" => "CreateOrderHeader",
			"ClientOrderRef" => $data["CustomerID"]."-".$data["ProductCode"]."-".$data["Qnty"]
		)));

		if ($createOrderHeaderResponse["status"] == "OK"){

			$data["OrderID"] = $createOrderHeaderResponse["OrderID"];

			$createOrderLineResponse = BlueLogicAPI::Request($data, array_merge(BlueLogicAPI::$requestBaseData, array(
				"Action" => "CreateOrderLine"
			)));

			if ($createOrderLineResponse["status"] == "OK"){

				$processOrderResponse = BlueLogicAPI::Request($data, array_merge(BlueLogicAPI::$requestBaseData, array(
					"Action" => "ProcessOrder"
				)));

				if ($processOrderResponse["status"] == "OK"){
					AJAX::Response("json", $data);
				} else {
					AJAX::Response("json", array(), 1, $createOrderLineResponse);
				}
			} else {
				AJAX::Response("json", array(), 1, $createOrderLineResponse);
			}
		} else {
			AJAX::Response("json", array(), 1, $createOrderHeaderResponse);
		}

	} else {
		AJAX::Response("json", array(), 1, $data);
	}
