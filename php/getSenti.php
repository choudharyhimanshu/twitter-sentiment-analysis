<?php

	require_once __DIR__ . '\sentiment\autoload.php';
	$sentiment = new \PHPInsight\Sentiment();

	$text = $_POST['text'];

	echo $sentiment->categorise($text);
?>