<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style/mystyle.css">
	<script src="script/jquery-1.11.2.min.js"></script>
	
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
	<script type="text/javascript" src="script/myscript.js"></script>
</head>
<body>
	<div class="left_panel">
		<p>Click on the button below once you have selected your location</p>
		<button onclick="loadTweets()">Done</button>
		<br><br>
		<div>
			Additional Options
			<br><br>
			Radius : 
			<input type="number" id="user_radius" class="input" style="width:50px;text-align: center;" min="1" max="100" value="5" required> miles
			<br><br>
			Limit :
			<input type="number" id="user_limit" class="input" style="width:50px;text-align: center;" min="1" max="100" value="50" required> tweets
		</div>
		<div id="stats"></div>
		<div style="position:absolute;width:19%;bottom:10px;font-size:.8em;text-align:center;">
			<u>Developed by</u><br>			
			<a href="http://home.iitk.ac.in/~himnshu/">Himanshu Choudhary</a><br>
			<a href="http://home.iitk.ac.in/~karanb/">Karan Bansal</a><br>
			<a href="http://home.iitk.ac.in/~rahugur/">Rahul Gurjar</a>
		</div>
	</div>
	<div class="right_panel">
		<div id="loader">
			<img src="images/loader.gif" alt="Loading.." style="position:fixed;top:40%;left:40%;" width="100" height="100">
		</div>
		<input id="pac-input" class="controls" type="text" placeholder="Search Box">
    	<div id="map-canvas"></div>
	</div>
</body>
</html>