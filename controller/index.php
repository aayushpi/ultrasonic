<?php
/*
 * mpd remote - xhtml-mp interface to mpd
 * version 1.2 - 2006-jan-07
 * copyright (c) 2004-2006 thomas e. morgan
 * http://iprog.com/mpdremote
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */ 

// configuration //
//   (config also in playlist.php)
$host = '10.1.10.99';
$port = 6600;
$password = null;

// that's it!


print <<<BLK1
<!DOCTYPE html> 
<html> 
<head> 
  <title>My Page</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
  <link rel="stylesheet" href="css.mobile.css" />
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
</head> 
<body> 
<div data-role="page">

  <div data-role="header">
    <h1>Ultrasonic Controller</h1>
  </div><!-- /header -->


BLK1;

  require_once('mpd-class/mpd.class.php');
  
  $mpd = new mpd($host,$port,$password);
  
  if (isset($_GET['pause']))
    $mpd->Pause();
  elseif (isset($_GET['prev']))
    $mpd->Previous();
  elseif (isset($_GET['play']))
    $mpd->Play();
  elseif (isset($_GET['next']))
    $mpd->Next();
  elseif (isset($_GET['stop']))
    $mpd->Stop();
  elseif (isset($_GET['voldn']))
    $mpd->AdjustVolume(-5);
  elseif (isset($_GET['volup']))
    $mpd->AdjustVolume(+5);
  elseif (isset($_GET['stovr']))
    $mpd->SeekTo(0);
  
  switch ($mpd->state) {
    case MPD_STATE_PLAYING : $stat = 'Playing: '; break;
    case MPD_STATE_STOPPED : $stat = 'Stopped'; break;
    case MPD_STATE_PAUSED  : $stat = 'Paused: '; break;
    default : $stat = 'Unknown status';
  }
  if ($mpd->state == MPD_STATE_PLAYING || $mpd->state == MPD_STATE_PAUSED) {
    $stat .= $mpd->playlist[$mpd->current_track_id]['Title']."<br />\n".$mpd->playlist[$mpd->current_track_id]['Artist'];

    $nloc = sprintf('%d:%02d',floor($mpd->current_track_position/60),$mpd->current_track_position%60);
    $tloc = sprintf('%d:%02d',floor($mpd->current_track_length/60),$mpd->current_track_length%60);

    $loc = "Location: $nloc/$tloc";
  } else {
    $loc = '';
  }
  
  $vol = "Volume: ".$mpd->volume."%";
  
  print "<p>$stat</p>\n";

  $rnd = substr(md5(rand()),0,8); // having trouble with refreshes. this should convince the browser that everything's unique
  $me = basename($_SERVER['PHP_SELF']);
  if ($me == 'index.php')
    $me = './'; //assumes index.php is default for apache - comment out if this is a problem
  print <<<BLK2

<div data-role="content"> 
     
  
<table>
<tr><td><a href="$me?$rnd" accesskey="*">*. Refresh</a></td></tr>
<tr><td><a href="$me?pause&$rnd" accesskey="2">2. | | Pause</a></td>
 <td><a href="$me?play&$rnd" accesskey="5">5. &gt; Play</a></td></tr>
<tr><td><a href="$me?prev&$rnd" accesskey="4">4. &lt;&lt; Prev</a></td>
 <td><a href="$me?next&$rnd" accesskey="6">6. &gt;&gt; Next</a></td></tr>
<tr><td><a href="$me?stop&$rnd" accesskey="8">8. Stop</a></td>
 <td><a href="$me?stovr&$rnd" accesskey="0">0. Seek 0</a></td></tr>
<tr><td><a href="$me?voldn&$rnd" accesskey="7">7. Vol -</a></td>
 <td><a href="$me?volup&$rnd" accesskey="9">9. Vol +</a></td></tr>
</table>
</div><!-- /content -->
BLK2;

  print "<p><a href=\"playlist.php?$rnd\" accesskey=\"1\">1. Playlist</a> <br />\n";
  print "$loc<br />$vol</p>\n";
  
  $mpd->Disconnect();
?>
</body>
</html>
