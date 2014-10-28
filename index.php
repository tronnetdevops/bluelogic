<?php
	error_reporting(-1);
	header("Access-Control-Allow-Origin: ". trim($_SERVER["HTTP_REFERER"], "/") );

	session_start();

	include('./resources/AJAX.php');
	include('./resources/BlueLogicAPI.php');

	$action = $_REQUEST["action"];

	if (isset($action) && isset(BlueLogicAPI::$available_calls[ $action ])){
		BlueLogicAPI::$action( $_REQUEST );
	} else{
		AJAX::Response("json", array(), 1, "Action provided can't be executed!");
	}