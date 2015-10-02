<link rel="stylesheet" type="text/css" href="style.css">
<script src="javascript/chart.js"></script>
<?php
require_once __DIR__ . '\sentiment\autoload.php';
$sentiment = new \PHPInsight\Sentiment();

set_time_limit(1000000);
require_once("twitteroauth.php");
$count = 0;
$notweets = 700;
$consumerkey = "lRXWtYO5VfPKAn0C3Ii1SSYBv";
$consumersecret = "LjIemw45CjhSdzbANAPuJ2q0fVM4qTOcAE2eULACXj4g0GtHOZ";
$accesstoken = "3080055000-AwU0ABMGWxNtTbtWfieplYkGm7xY5PL1OJhH2Kr";
$accesstokensecret = "UuKRnqaMKp3p3J94XM1HBuCxtvn0P1xpdIzxa1FuYTznV";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) 
{
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
?>
<div class="left_contentlist">
    <form class="form-wrapper" action="index.php" method="get">
        <input type="text" id="search" placeholder="Enter twitter handle! " name="handle">
        <input type="submit" value="go" id="submit">
    </form>
<?php
if(isset($_GET['handle'])){
    $handle = $_GET['handle'];
}
else{
    die("</div>");
}
$url1 = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $handle . "&count=200";

$tweets = $connection->get($url1);
$positive = 0;
$negative = 0;
$neutral = 0;
$tweets = json_decode(json_encode($tweets),true);
$s_p = 0;
$s_n = 0;
$n_1 = 0;
$n_2 = 0;
$n_3 = 0;
$n_4 = 0;
$n_5 = 0;
$p_1 = 0;
$p_2 = 0;
$p_3 = 0;
$p_4 = 0;
$p_5 = 0;
?>

<?php
if(isset($tweets['errors']))
{
    echo 'Either you have entered incorrect username or the user has not made any tweet yet!'; 
    die();
}
for($y=0;$y<sizeof($tweets);$y=$y+1) 
{
    // print_r($tweets[$y]);
    $text = $tweets[$y]['text'];
    $last_time = $tweets[$y]['created_at'];
    $location = $tweets[$y]['place']['full_name'];
    // echo $text;
    // echo '<br><br>';
    // echo $text;
    $conf = $sentiment->score($text);
    // print_r($conf);
    $class = $sentiment->categorise($text);
    $conf = $conf[$class];
    if($class == 'pos')
    {
?>
<div class="tweet" style="background:#9BECCD">
<?php echo $text; ?>
<br>
<div style="position: absolute;bottom: 0;width: 100%;">
<p style="float:left;"><?php echo $last_time;?></p>
<p style="float:right;margin-right:5%;"><?php echo $location;?></p>
</div>
</div>
<?php
        $positive = $positive + $conf;
        $s_p = $s_p + 1;
    }
    elseif ($class == 'neu') 
    {
?>
<div class="tweet" style="background:#4099FF">
<?php echo $text; ?>
<br>
<div style="position: absolute;bottom: 0;width: 100%;">
<p style="float:left;"><?php echo $last_time;?></p>
<p style="float:right;margin-right:5%;"><?php echo $location;?></p>
</div>
</div>
<?php
        $neutral = $neutral + $conf;
    }
    else
    {
?>
<div class="tweet" style="background:#FC8282">
<?php echo $text; ?>
<br>
<div style="position: absolute;bottom: 0;width: 100%;">
<p style="float:left;"><?php echo $last_time;?></p>
<p style="float:right;margin-right:5%;"><?php echo $location;?></p>
</div>
</div>
<?php
        $negative = $negative + $conf;
        $s_n = $s_n + 1;
    }
    if($y == 40)
    {
        $p_1 = $s_p;
        $n_1 = $s_n;
    }
    if($y == 80)
    {
        $p_2 = $s_p - $p_1;
        $n_2 = $s_n - $n_1;
    }
    if($y == 120)
    {
        $p_3 = $s_p - $p_2;
        $n_3 = $s_n - $n_2;
    }
    if($y == 160)
    {
        $p_4 = $s_p - $p_3;
        $n_4 = $s_n - $n_3;
    }
    if($y == 199)
    {
        $p_5 = $s_p - $p_4;
        $n_5 = $s_n - $n_4;
    }
?>


<?php
}
?>
</div>

<div class="right_contentlist">
<div>
    <canvas id="canvas" height="300" width="320"></canvas>
</div>

<div id="canvas-holder">
    <canvas id="chart-area" width="320" height="300"/>
</div>
    <script>
            // window.onload = function(){
            //     var ctx2 = document.getElementById("chart-area").getContext("2d");
            //     window.myPie = new Chart(ctx2).Pie(pieData);
            // };
    </script>
<br><b>Tweets since <?php echo $last_time;?> <b>
</div>

<script>
// Pie Chart 
    var pieData = [
            {
                value: <?php echo $positive;?>,
                color:"#08E91B",
                highlight: "#63F76F",
                label: "Positive"
            },
            {
                value: <?php echo $negative;?>,
                color: "#F7464A",
                highlight: "#FF5A5E",
                label: "Negative"
            },
            {
                value: <?php echo $neutral;?>,
                color: "#3266DD",
                highlight: "#5983E4",
                label: "Neutral"
            }
        ];

// Line chart 

    var lineChartData = {
        labels : ["100","200","300","400","500"],
        datasets : [
            {
                label: "My First dataset",
                fillColor : "rgba(220,220,220,0.2)",
                strokeColor : "rgba(220,220,220,1)",
                pointColor : "rgba(220,220,220,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(220,220,220,1)",
                data : [<?php echo $p_1;?>, <?php echo $p_2;?>, <?php echo $p_3;?>, <?php echo $p_4;?>, <?php echo $p_5;?>]
            },
            {
                label: "My Second dataset",
                fillColor : "rgba(151,187,205,0.2)",
                strokeColor : "rgba(151,187,205,1)",
                pointColor : "rgba(151,187,205,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(151,187,205,1)",
                data : [<?php echo $n_1;?>, <?php echo $n_2;?>, <?php echo $n_3;?>, <?php echo $n_4;?>, <?php echo $n_5;?>]
            }
        ]
    }
    window.onload = function(){
        var ctx2 = document.getElementById("chart-area").getContext("2d");
        window.myPie = new Chart(ctx2).Pie(pieData);
        var ctx1 = document.getElementById("canvas").getContext("2d");
        window.myLine = new Chart(ctx1).Line(lineChartData, {
            responsive: true
        });
    }
</script>