<?php
require_once("../shared.php");

$shared = new Shared();

if ($shared->channelKeyIsValid()) {
	// Get POST request body as JSON
	$request_body = json_decode(file_get_contents('php://input'));

	// Check if actionFields are included in request
	$action_fields = isset($request_body->actionFields) ? $request_body->actionFields : false;

	if ($action_fields == false) {
		$shared->errorMessage(400, "Missing action fields");
	} else if (!isset($request_body->actionFields->channel_id)) {
		$shared->errorMessage(400, "Missing action field channel_id");
	} else if (empty($request_body->actionFields->channel_id)) {
		$shared->errorMessage(400, "channel_id cannot be empty", Shared::SKIP);
	} else {

		$channel_id = $request_body->actionFields->channel_id;
		$game_title = $request_body->actionFields->game_title;
		$game_link = $request_body->actionFields->game_link;
		$webhook_link = $request_body->actionFields->webhook_link;

		// Message to send
		$content = "__**FREE GAME ALERT**__\n\n**$game_title**\n\n$game_link";

		// POST body to send to discord
		$body = ["name" => "Free Games", "channel_id" => $channel_id, "content" => $content];
		
		$ch = curl_init($webhook_link);
		# Setup request to send json via POST.
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
		# Return response instead of printing.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		# Send request.
		$result = curl_exec($ch);
		curl_close($ch);

		// Log when games are sent
		$uniqid = uniqid();
		$msg = "Channel: $channel_id alerted of free game, $game_link.";
		file_put_contents("log.txt", "[".date('m/d/Y h:i:s a') . "] - [$uniqid] - $msg\n", FILE_APPEND);

		$response = ["data" => [["id" => $uniqid]]];
		$shared->successMessage(json_encode($response));
	}

} else {
	$shared->errorMessage(401, "Invalid channel key");
}

?>