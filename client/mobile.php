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
	case MPD_STATE_PLAYING : $stat = 'Playing: '; break;
	case MPD_STATE_STOPPED : $stat = 'Stopped'; break;
	case MPD_STATE_PAUSED  : $stat = 'Paused: '; break;
	default : $stat = 'Unknown status';
}

if ($mpd->state == MPD_STATE_PLAYING || $mpd->state == MPD_STATE_PAUSED) {
  $title = $mpd->playlist[$mpd->current_track_id]['Title'];
  $artist = $mpd->playlist[$mpd->current_track_id]['Artist'];
	$nloc = sprintf('%d:%02d',floor($mpd->current_track_position/60),$mpd->current_track_position%60);
	$tloc = sprintf('%d:%02d',floor($mpd->current_track_length/60),$mpd->current_track_length%60);
	
	$current_time = $nloc.' / '.$tloc;
	
	}

$current_playlist = $mpd->playlist;

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
  <title>Ultrasonic</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
</head> 
<body> 
	<div data-role="page">
		<div data-role="header">
			<h1>Ultrasonic</h1>
		</div><!-- /header -->
				
		<div class="content" > 
			<ul data-role="listview" data-theme="a">
			<?php foreach($current_playlist as $key=>$val): ?>
				<?php if ($mpd->current_track_id == $key) { $past_current = true; }  ?>
					<?php if($past_current == true): ?>
						<li <?php echo $mpd->current_track_id == $key ? 'data-theme="e"' : '' ?>>
								<img src="<?php echo get_album_art($current_playlist[$key]['file']) ?>" />
								<h3><?php echo $current_playlist[$key]['Title'] ?></h3>
								<p><?php echo $current_playlist[$key]['Artist'] ?><span class="ui-li-count"><?php echo $mpd->current_track_id == $key ? $current_time : sprintf('%d:%02d',floor($current_playlist[$key]['Time'] / 60),$current_playlist[$key]['Time'] % 60) ?></span></p>
						</li>
					<?php endif; ?>	
				<?php endforeach; ?>  	
			</ul>
			<form data-theme="a" method="post" action="search.php">
				<input type="search" name="search" id="search-basic" value="Search" />
			</form>
			</div><!-- /content -->
		
		</div><!-- /page -->
	</div>
	<script>
	</script>
</body>
</html>

<?php
$mpd->Disconnect();
?>