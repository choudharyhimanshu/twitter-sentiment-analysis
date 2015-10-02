<?php
// session_start();

$lat = $_GET['lat'];
$long = $_GET['long'];
$radius = $_GET['radi'];
$notweets = $_GET['limit'];

require_once __DIR__ . '\sentiment\autoload.php';
$sentiment = new \PHPInsight\Sentiment();

require_once("twitteroauth.php"); //Path to twitteroauth library
 
// $notweets = 100;
$consumerkey = "lRXWtYO5VfPKAn0C3Ii1SSYBv";
$consumersecret = "LjIemw45CjhSdzbANAPuJ2q0fVM4qTOcAE2eULACXj4g0GtHOZ";
$accesstoken = "3080055000-AwU0ABMGWxNtTbtWfieplYkGm7xY5PL1OJhH2Kr";
$accesstokensecret = "UuKRnqaMKp3p3J94XM1HBuCxtvn0P1xpdIzxa1FuYTznV";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?geocode=".$lat.",".$long.",".$radius."mi&result_type=recent&count=".$notweets);

$tweets = json_decode(json_encode($tweets),true);

$data = [];

foreach ($tweets['statuses'] as $val) {
	$id = $val['id_str'];
	$text = $val['text'];
	$time = $val['created_at'];
	$geo = $val['geo']['coordinates'];
	if($geo == null){
		continue;
	}
	$user_id = $val['user']['id_str'];
	$user_name = $val['user']['name'];
	$user_scr_name = $val['user']['screen_name'];
	$location = $val['place']['full_name'];
	$mood = $sentiment->categorise($text);
	$conf = $sentiment->score($text);
	$mood_scale = $conf[$mood];

	$document = array( 
	     "tweet_id" => $id, 
	     "tweet_text" => $text, 
	     "created_at" => $time,
	     "geo" => $geo,
	     "mood" => $mood,
	     "mood_scale" => $mood_scale,
	     "user_id" => $user_id,
	     "user_name" => $user_name,
	     "user_scr_name" => $user_scr_name
	);
	array_push($data, $document);
}
echo json_encode($data);
?>