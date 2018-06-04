<?php
require_once("../shared.php");

if (Shared::channelKeyIsValid()) {
	Shared::successMessage('{ "data": { "samples": { "actions": { "send_games": { "channel_id": "sample", "game_title": "sample", "game_link": "sample", "webhook_link": "sample" } }, "actionRecordSkipping": { "send_games": { "channel_id": "", "game_title": "", "game_link": "", "webhook_link": "" } } } } }');
} else {
	Shared::errorMessage(401, "Invalid channel key");
}

?>
