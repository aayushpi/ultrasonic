<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$host = '10.1.10.99';
$port = 6600;
$password = null;
require_once('mpd-class/mpd.class.php');
$mpd = new mpd($host,$port,$password);

switch ($mpd->state) {
	case MPD_STATE_PLAYING : $stat = 1; break;
	case MPD_STATE_STOPPED : $stat = 2; break;
	case MPD_STATE_PAUSED  : $stat = 3; break;
	default : $stat = 0;
}

if (isset($_GET['song'])) {
	$song_file = $_GET['song'];
	
	//play if paused or stopped
	if ($stat == 2 || $stat == 3){
		//Add some logic to play and skip to if it's not 0 in que
			$mpd->PLAdd($song_file);
			$mpd->SkipTo($song_file);
			$mpd->Play();
	}else{
		//add file end to que
		$mpd->PLAdd($song_file);
	}
}

$mpd->Disconnect();
header("Location: mobile.php");

?>
