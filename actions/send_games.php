<?php
require_once("../shared.php");

if (Shared::channelKeyIsValid()) {
	// Get POST request body as JSON
	$request_body = json_decode(file_get_contents('php://input'));

	// Check if actionFields are included in request
	$action_fields = isset($request_body->actionFields) ? $request_body->actionFields : false;

	if ($action_fields == false) {
		Shared::errorMessage(400, "Missing action fields");
	} else if (!isset($action_fields->channel_id)) {
		Shared::errorMessage(400, "Missing action field channel_id");
	} else if (empty($action_fields->channel_id)) {
		Shared::errorMessage(400, "channel_id cannot be empty", Shared::SKIP);
	} else if (strpos($action_fields->webhook_link, "https://discordapp.com/") != 0) {
		Shared::errorMessage(400, "Invalid webhook_link", Shared::SKIP);
	} else {

		$channel_id = $action_fields->channel_id;
		$game_title = $action_fields->game_title;
		$game_link = $action_fields->game_link;
		$webhook_link = $action_fields->webhook_link;

		// Check if reddit link
		if (strpos($game_link, "/r/GameDeals") === 0) {
			$game_link = "https://www.reddit.com" . $game_link;
		}

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
		curl_exec($ch);
		curl_close($ch);

		// Log when games are sent
		$uniqid = uniqid();
		$msg = "trigger=free_games, channel_id=$channel_id, game_link=$game_link";
		file_put_contents("log.txt", "[".date('m/d/Y h:i:s a') . "] - [$uniqid] - $msg\n", FILE_APPEND);

		$response = ["data" => [["id" => $uniqid]]];
		Shared::successMessage($response);
	}

} else {
	Shared::errorMessage(401, "Invalid channel key");
}

?>