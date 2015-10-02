var map;
var flag = true;
var curr_data = null;
var userlocation = new google.maps.Marker({
     position: null,
     map: null,
     draggable:true,
     title:"Selected Location"
});
var markersArray = [];

var i;

google.maps.event.addDomListener(window, 'load', initialize);

// Function initialize
function clearOverlays() {
  for (var i = 0; i < markersArray.length; i++ ) {
    markersArray[i].setMap(null);
  }
  markersArray.length = 0;
}

function initialize() {
     var mapOptions = {
          center: { lat: 24.728972, lng: 78.776477},
          zoom: 4
     };
     
     map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
     
     var input = /** @type {HTMLInputElement} */(document.getElementById('pac-input'));
  		
  		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  	var searchBox = new google.maps.places.SearchBox((input));

  	// [START region_getplaces]
  	// Listen for the event fired when the user selects an item from the
  	// pick list. Retrieve the matching places for that item.
  	google.maps.event.addListener(searchBox, 'places_changed', function() {
    	     var places = searchBox.getPlaces();
    	     if (places.length == 0) {
    	       return;
    	     }
    	     // For each place, get the icon, place name, and location.
   	     var bounds = new google.maps.LatLngBounds();
   	     for (var i = 0, place; place = places[i]; i++) {
   	 	    bounds.extend(place.geometry.location);
    	     }
	    map.fitBounds(bounds);
	    map.setZoom(14);
  	});

  	google.maps.event.addListener(map, "click", function (e) {
    	//lat and lng is available in e object
          if(flag){
         	      var latLng = e.latLng;
    	          userlocation.position = latLng;
    	          userlocation.setMap(map);
          }    		
    	});
}

function loadTweets(){
	if(userlocation.position == null){
        alert("First select your location");
        return false;
    }
    else{
          clearOverlays();
          $("#loader").css('visibility','visible');
          // alert(userlocation.position);
        // flag = false;
          $("#loader").css('visibility','visible');
          var latitude = userlocation.position.lat();
          var longitude = userlocation.position.lng();
          var radius = $("#user_radius").val();
          var tweets_lim = $("#user_limit").val();
          var count_tot = 0;
          var count_posi = 0;
          var count_neg = 0;
          var count_nut = 0;
          if(!(radius >= 1 && radius <= 100)){
            alert("Radius out of range");
            return false;
          }
        // alert(latitude+' '+longitude);
        // alert(userlocation.id);
          var url = 'php/getTweets.php?lat='+latitude+'&long='+longitude+'&radi='+radius+'&limit='+tweets_lim;
 
          $.ajax({
               url: url,
               dataType: 'json',
               async: false,
               success: function(data){
        	// do something here
               // map.data.loadGeoJson(data);
               // return false;
               var count = data.length;
               var image = 'images/blackdot.png';
               var tweet_id,tweet_text,user_id,user_name,user_scrname,tweet_geo,tweet_time,mood;
     
               for (i = 0; i < count; i++) {
                    count_tot++;
                    // count_nut++;
                    tweet_id = data[i].tweet_id;
                    tweet_text = data[i].tweet_text;
                    user_id = data[i].user_id;
                    user_name = data[i].user_name;
                    user_scrname = data[i].user_scr_name;                    
                    tweet_time = data[i].created_at;
                    tweet_geo = data[i].geo;
                    mood = data[i].mood;
                    
                    if(mood == "pos"){
                         image = 'images/greendot.png';
                         count_posi++;
                         // count_nut--;
                    }
                    else if(mood == "neg"){
                         image = 'images/reddot.png';
                         count_neg++;
                         // count_nut--;
                    }
                    else{
                         count_nut++;
                    }
                    
                    // $('#para').append(tweet_id+"<br>"+tweet_text+"<br>"+tweet_time+"<br>"+tweet_geo+"<br>"+tweet_location+"<br>"+user_id+"<br>"+user_name+"<br>"+user_scrname+"<hr>");
                    var content = '<p>'+
                              '<span class="name"><a href="http://www.twitter.com/'+user_scrname+'">'+user_name+'</a></span><span class="scr_name">@'+user_scrname+'</span></p>'
                              +'<p class="text">'+tweet_text+'</p>'+
                              +'<p>'+'<span class="time">'+tweet_time
                              +'</span></p>';
                    var tweet_title = '@'+user_scrname;
     
                    var infowindow = new google.maps.InfoWindow({maxWidth: 400});
     
                    var marker = new google.maps.Marker({
                         position: {lat : tweet_geo[0],lng : tweet_geo[1]},
                         map: map,
                         title: tweet_title,
                         icon: image
                         // animation: google.maps.Animation.DROP
                    });
                    markersArray.push(marker);
                    google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
                         return function() {
                              infowindow.setContent(content);
                              infowindow.open(map,marker);
                         };
                    })(marker,content,infowindow));  

               }
               }
                    // alert('done');  
          });
          document.getElementById('stats').innerHTML = '<br/><u>Stats</u> : <br>Total Tweets : '+count_tot+'<br>Positive Tweets : '+count_posi+'<br>Negative Tweets : '+count_neg+'<br>Neutral Tweets : '+count_nut;
    	}
     $("#loader").css('visibility','hidden');
	return false;
}