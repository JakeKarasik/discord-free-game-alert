<?php
require_once("../shared.php");

$shared = new Shared();

if ($shared->channelKeyIsValid()) {
	$shared->successMessage('{ "data": { "samples": { "actions": { "send_games": { "channel_id": "sample", "game_title": "sample", "game_link": "sample", "webhook_link": "sample" } }, "actionRecordSkipping": { "send_games": { "channel_id": "", "game_title": "", "game_link": "", "webhook_link": "" } } } } }');
} else {
	$shared->errorMessage(401, "Invalid channel key");
}

?>
