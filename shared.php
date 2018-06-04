<?php

class Shared {

	static private $MY_KEY = null;
	const SKIP = true;

	public function __construct() {
		// Load in ifttt service key from txt file
		self::$MY_KEY = file_get_contents($_SERVER['DOCUMENT_ROOT']."/ifttt/v1/key.txt");
	}

	public static function channelKeyIsValid() {

		$channel_key = array_key_exists('HTTP_IFTTT_CHANNEL_KEY', $_SERVER) ? $_SERVER['HTTP_IFTTT_CHANNEL_KEY'] : null;

		if ($channel_key != null && strcmp($channel_key, self::$MY_KEY) == 0) {
			// If channel key present and valid
			return true;
		} else if ($channel_key != null) {
			// If channel key present and invalid
			return false;
		} else {
			// If no channel key (for testing purposes)
			return true;
		}
	}

	public static function errorMessage($code, $msg, $skip=false) {
		http_response_code($code);
		header('Content-Type: application/json; charset=utf-8');
		if ($skip) {
			print(json_encode(["errors" => [["status" => "SKIP", "message" => $msg]]]));
		} else {
			print(json_encode(["errors" => [["message" => $msg]]]));
		}
	}

	public static function successMessage($msg) {
		header('Content-Type: application/json; charset=utf-8');
		print(json_encode($msg));
	}

}

?>