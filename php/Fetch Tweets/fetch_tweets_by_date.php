<?php
	// connect to mongodb
	set_time_limit(1000000000000);
   	$m = new MongoClient();
   	//echo "Connection to database successfully";
   	// select a database
   	$db = $m->tweetsdata;
   	//echo "Database mydb selected";

   	$collection = $db->lastweektweets;
	// echo "Collection selected succsessfully";

   	$query = file_get_contents('datequery.txt');
   	$date = file_get_contents('date.txt');
   	$count = 0;
   	$countdays = 0;

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

	// $tweets =  $connection->get("https://stream.twitter.com/1.1/statuses/sample.json");
	// echo $tweets;
	// echo '<pre>';
	// print_r($tweets);
	// echo '</pre>';
	// die();
	// $query = "?q=place:b850c1bfd38f30e0";

	for ($i=0; $i < 30000; $i++) { 
		echo "$query<br>";
		if($query == ""){
			echo "<script>alert('empty');</script>";
			die('empty query');
		}
		
		if($countdays >= 7){
			break;
		}
		if($count >= 1000){
			echo "I am here with $date";
			$date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
			file_put_contents('date.txt', $date);
			$query = "?q=place:b850c1bfd38f30e0&until=".$date;
			file_put_contents('datequery.txt', $query);
			echo " which is now $date<br>";
			$count = 0;
			$countdays++;
		}
		$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json".$query);


		// echo '<pre>';
		// print_r($tweets);
		// echo '</pre>';
		// // echo json_encode($tweets);
		// die();
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
			     "user_scr_name" => $user_scr_name,
			     "date" => $date
			);
			try{
				$collection->insert($document);
				$count++;
			}
			catch(Exception $e) {
				echo "skip<br>";	
				continue;			
			}
		}
		echo "query:".$query;
		echo "<br>";
		file_put_contents('datequery.txt', $query);
	}

	file_put_contents('datequery.txt', $query);
	echo "Done";
	// echo json_encode($tweets); 
   	// $tweets = $connection->get("https://api.twitter.com/1.1/geo/search.json?query=IN&granularity=country");

?>