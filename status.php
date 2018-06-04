<?php
require_once("shared.php");

if (Shared::channelKeyIsValid()) {
	Shared::successMessage(["data" => ["label" => "status", "value" => "success"]]);
} else {
	Shared::errorMessage(401, "Invalid channel key");
}

?>