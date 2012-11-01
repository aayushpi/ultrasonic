<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');
$host = '10.1.10.99';
$port = 6600;
$password = null;
require_once('mpd-class/mpd.class.php');
require_once('simple_html_dom.php');
$mpd = new mpd($host,$port,$password);

switch ($mpd->state) {
	case MPD_STATE_PLAYING : $stat = '1'; break;
	case MPD_STATE_STOPPED : $stat = '2'; break;
	case MPD_STATE_PAUSED  : $stat = '3'; break;
	default : $stat = '0';
}

if (isset($_POST['search'])){
	$search_value = $_POST['search'];
	$search_results = $mpd->Search(MPD_SEARCH_TITLE, $search_value);
	$search_results = array_slice($search_results, 0, 5);
}

function get_album_art($file) {
	$track = substr($file, 14);
	$html = file_get_html('http://open.spotify.com/track/'.$track);
	foreach ($html->find('#cover-art') as $img) {
		return $img->src;
	}
}

?>

<!DOCTYPE html> 
<html> 
<head> 
  <title>Search</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
</head> 
<body> 
	<div data-role="page">
		<div data-role="header">
			<h1>Ultrasonic Search</h1>
		</div><!-- /header -->
				
		<div class="content" > 
			<ul data-role="listview" data-theme="a">
				<?php foreach($search_results as $key=>$val): ?>
						<li data-icon="plus">
							<a href="que.php?song=<?php echo $search_results[$key]['file'] ?>" data-ajax="false">
								<img src="<?php echo get_album_art($search_results[$key]['file']) ?>" />
								<h3><?php echo $search_results[$key]['Title'] ?></h3>
								<p><?php echo $search_results[$key]['Artist'] ?></p>
							</a>
						</li>
				<?php endforeach; ?>  	
			</ul>
			<form data-theme="a" method="post" action="search.php">
				<input type="search" name="search" id="search-basic" value="Search" />
			</form>
			</div><!-- /content -->
		
		</div><!-- /page -->
	</div>
</body>
</html>

<?php
$mpd->Disconnect();
?>