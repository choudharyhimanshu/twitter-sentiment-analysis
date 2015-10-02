<?php

	require_once __DIR__ . '\sentiment\autoload.php';
	$sentiment = new \PHPInsight\Sentiment();
	
	// connect to mongodb
	set_time_limit(1000000000000);
   	$m = new MongoClient();
   	//echo "Connection to database successfully";
   	// select a database
   	$db = $m->tweetsdata;
   	//echo "Database mydb selected";

   	$collection = $db->indiantweets;
	echo "Collection selected succsessfully";

   	$query = file_get_contents('query.txt');

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
	
	$senti_url = 'http://sentiment.vivekn.com/api/text/';
	// $query = "?q=place:b850c1bfd38f30e0";

	for ($i=0; $i < 5000; $i++) { 
		$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json".$query);
		// echo '<pre>';
		// print_r($tweets);
		// echo '</pre>';
		$tweets = json_decode(json_encode($tweets),true);
		$query = $tweets['search_metadata']['next_results'];

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


			$data = array('txt' => $text);
			$options = array(
			    'http' => array(
			        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			        'method'  => 'POST',
			        'content' => http_build_query($data),
			    ),
			);

			$context  = stream_context_create($options);
			$temp_mood = file_get_contents($senti_url, false, $context);
			$temp_mood = json_decode($temp_mood,true);

			$mood = $temp_mood['result']['sentiment'];
			$mood_scale = $temp_mood['result']['confidence'];

			if($mood == null){
				continue;
			}

			// echo "$mood $mood_scale <br>";

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
			try{
				$collection->insert($document);
			}
			catch(Exception $e) {
				echo "skip<br>";	
				continue;			
			}
		}
		echo $query;
		echo "<br>";
		file_put_contents('query.txt', $query);
	}

	file_put_contents('query.txt', $query);
	echo "Done";
	// echo json_encode($tweets); 
   	// $tweets = $connection->get("https://api.twitter.com/1.1/geo/search.json?query=IN&granularity=country");

?>