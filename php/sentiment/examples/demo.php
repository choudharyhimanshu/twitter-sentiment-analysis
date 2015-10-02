<?php

require_once __DIR__ . 'senti/autoload.php';
$sentiment = new \PHPInsight\Sentiment();
foreach ($strings as $string) {

	// calculations:
	$scores = $sentiment->score($string);
	$class = $sentiment->categorise($string);

	// output:
	// echo "String: $string\n";
	echo "Dominant: $class, scores: ";
	print_r($scores[$class]);
	echo "\n";
}
