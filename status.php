<?php
require_once("shared.php");

$shared = new Shared();

if ($shared->channelKeyIsValid()) {
	$shared->successMessage(json_encode(["data" => ["label" => "status", "value" => "success"]]));
} else {
	$shared->errorMessage(401, "Invalid channel key");
}

?>