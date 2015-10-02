<?php
	set_time_limit(1000000000000);
   	$m = new MongoClient();
   	//echo "Connection to database successfully";
   	// select a database
   	$db = $m->tweetsdata;
   	//echo "Database mydb selected";

   	$collection = $db->hashtagged;

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
	
	$hashtag = "aap";
	$query = file_get_contents('query.txt');
	if($query == ""){
		$query = "?q=%23".$hashtag.",place:b850c1bfd38f30e0&result_type=recent&count=100";
	}
	for ($i=0; $i < 1; $i++) { 
		$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json".$query);
		// echo '<pre>';
		// print_r($tweets);
		// echo '</pre>';
		$tweets = json_decode(json_encode($tweets),true);
		try{
			if($tweets['statuses'] == null){
				echo "<script>alert('empty');</script>";
				die('empty query');
			}
		}
		catch (Exception $e){
			print_r($e);
			echo "<script>alert('error');</script>";
			die('error query');
		}
		$query = $tweets['search_metadata']['next_results'];

		foreach ($tweets['statuses'] as $val) {
			$id = $val['id_str'];
			$text = $val['text'];
			$time = $val['created_at'];
			$geo = $val['geo']['coordinates'];
			// if($geo == null){
			// 	continue;
			// }
			$user_id = $val['user']['id_str'];
			$user_name = $val['user']['name'];
			$user_scr_name = $val['user']['screen_name'];
			$location = $val['place']['full_name'];
			$mood = null;
			$mood_scale = 0;

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
			     "hashtag"=> $hashtag,
			     "place"=>$location
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
	echo "Done :D";
?>