<?php
$date = '2015-03-1';
$next = date('Y-m-d', strtotime('+1 day', strtotime($date)));
$prev = date('Y-m-d', strtotime('-1 day', strtotime($date)));
echo $next." ".$prev;
?>