<?php

	set_time_limit(1000000000000);
	$m = new MongoClient();
	//echo "Connection to database successfully";
	// select a database
	$db = $m->tweetsdata;
	//echo "Database mydb selected";

	$collection = $db->hashtagged;

	$url = 'http://sentiment.vivekn.com/api/text/';

	$data = $collection->find();

	foreach ($data as $row) {
		# code...
		if($row['mood'] != null){
			continue;
		}
		$id = $row['_id'];
		$text = $row['tweet_text'];
		$temp = array('txt' => $text);
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($temp),
		    ),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$result = json_decode($result,true);
		// echo $text;
		// print_r($result);
		$collection->update(
			array('_id'=>$id),
			array('$set' => array(
				"mood_scale"=>$result['result']['confidence'],
				"mood"=>$result['result']['sentiment']
			))
		);
		// die();
	}
	echo "Done";
?>