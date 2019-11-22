<?php
require_once("../shared.php");

class Meta {
	public $id;
	public $timestamp;

	public function __construct() {
		$this->id = uniqid();
		$this->timestamp = time();
	}
}

class Game {
	public $game_title;
	public $game_link;
	public $created_at;
	public $meta;

	public function __construct($title=null, $link=null) {
		$this->game_title = $title;
		$this->game_link = $link;

		// Set created data
		$datetime = new DateTime();
		$this->created_at = $datetime->format(DateTime::ATOM);

		// Set meta data
		$this->meta = new Meta();
	}

	public function toJson() {
		return json_encode($this);
	}
}

function gameAlreadyExists($games, $link) {
	foreach ($games as $game) {
		if (isset($game->game_link) && strcmp($game->game_link, $link) == 0) {
			return true;
		}
	}
	return false;
}

if (Shared::channelKeyIsValid()) {
	// Get JSON body from POST request
	$request_body = json_decode(file_get_contents('php://input'));

	// Check if limit set else default to 50
	$limit = isset($request_body->limit) ? $request_body->limit : 50;

	// Prepare to get free games data
	$data = file_get_contents("https://old.reddit.com/r/GameDeals/");
	$dom = new DOMDocument;
	// Suppress invalid HTML errors
	@$dom->loadHTML($data);
	$xpath = new DOMXpath($dom);

	$entries = $xpath->query('//div[contains(@class, "thing")]');

	$keywords = ["100% off", "free ", "free)", "free]"];
	$excludes = [
		"free gift", 
		"free shipping",
		"free us shipping",
		"free ship", 
		"months free",
		"free weekend"
	];

	$new_games = [];

	// Iterate all games and save free games
	foreach ($entries as $entry) {
		// Get game title and link
		$link = $entry->getElementsByTagName("a")->item(0)->getAttribute('href');
		$content = strtolower($entry->textContent);

		// Clean up game title
		$submitted_text_pos = strpos($content, "submitted");
		$title = substr($content, 0, $submitted_text_pos);
		$true_title_pos = strpos($title, "[");
		$title = substr($title, $true_title_pos);

		// Save games with title that contains any keyword(s) and exclude false positives
		foreach ($keywords as $kw) {
			if (strpos($content, $kw) !== false) {
				// Make sure isn't false positive
				foreach ($excludes as $ex) {
					if (strpos($content, $ex) !== false) {
						// False positive
						break 2;
					}
				}

				// Check if reddit link
				if (strpos($link, "/r/GameDeals") === 0) {
					$link = "https://www.reddit.com" . $link;
				}

				if (!$title) {
					break;
				}

				$new_game = new Game($title, $link);

	   			array_push($new_games, $new_game);
	   			break;
			}
		}
	}

	// Load saved games from file
	$saved_games = [];
	$file = fopen("games.txt", "r");
	while (!feof($file)) {
		$line = fgets($file);
		$decoded_game = json_decode($line);
		if (isset($decoded_game->game_title)) {
			array_push($saved_games, $decoded_game);
		}
	}
	fclose($file);

	// Save new games to file if not already saved
	foreach ($new_games as $game) {
		if (!gameAlreadyExists($saved_games, $game->game_link)) {
			array_push($saved_games, $game);
			file_put_contents("games.txt", $game->toJson()."\n", FILE_APPEND);
		}
	}

	// Return limited number of results
	$num_games_saved = count($saved_games);
	$max = $limit > $num_games_saved ? $num_games_saved : $limit;
	$data = $limit > 0 ? array_slice($saved_games, $max * -1) : [];

	Shared::successMessage(["data" => $data]);
} else {
	Shared::errorMessage(401, "Invalid channel key");
}


?>
