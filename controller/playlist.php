<?php
/*
 * mpd remote - xhtml-mp interface to mpd
 * version 1.0 - 2005-nov-19
 *   copyright (c) 2005 Laurent Sibilla xby@skynet.be
 * version 1.2 - 2006-jan-07
 *   changes copyright (c) 2006 thomas e. morgan
 * http://iprog.com/mpdremote
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
$host = 'localhost';
$port = 6600;
$password = null;

// that's it!


print <<<BLK1
<!DOCTYPE html PUBLIC "-//OPENWAVE//DTD XHTML Mobile 1.0//EN" "http://www.openwave.com/dtd/xhtml-mobile10.dtd">
<html>
<head>
<title>mpd remote</title>
<meta http-equiv="cache-control" content="must-revalidate" forua=true />
<meta http-equiv="cache-control" content="no-cache" forua=true />
<style type="text/css">
A, A:visited {
  color: blue;
}
</style>
</head>
<body>

BLK1;

  require_once('mpd-class/mpd.class.php');
  
  $mpd = new mpd($host,$port,$password);
  $pl = $mpd->GetDir();
  
  if (isset($_GET['setTo']))
    if (in_array($_GET['setTo'],$pl['playlist'])) //test for illegit data
      $mpd->PLLoad($_GET['setTo']);
  elseif (isset($_GET['clear']))
    $mpd->PLClear();
  elseif (isset($_GET['del'])) //mpd.class tests for integer
    $mpd->PLRemove($_GET['del']);
  elseif (isset($_GET['play'])) { //mpd.class tests for integer
    $mpd->SkipTo($_GET['play']);
    $mpd->Play();
  }

  
  $me = basename($_SERVER['PHP_SELF']);
  $rnd = substr(md5(rand()),0,8); // having trouble with refreshes. this should convince the browser that everything's unique

  print "<p><a href=\"playlist.php?$rnd\" accesskey=\"*\">*. Refresh</a> | ";
  print "<a href=\"index.php?$rnd\" accesskey=\"#\">#. Back</a></p>\n";

  print "<p><a href=\"$me?clear=$rnd\">Clear Playlist</a></p>\n";

  print "<h3>Available Playlists</h3>\n<p>";

  foreach($pl['playlist'] as $key=>$val)
  {
    print "<a href=\"$me?setTo=$val&$rnd\">$val</a><br />\n";
  }

  print "</p>

<h3>Current Playlist</h3>\n<p>\n";

  $pl = $mpd->playlist; //GetPlayList();
  foreach($pl as $key=>$val)
  {
    $b1 = $b2 = '';
    if ($mpd->current_track_id == $key) {
      $b1 = '<b>';
      $b2 = '</b>';
    }
    if (is_numeric($key))
      print "<a href=\"$me?play=$key&$rnd\">$b1$key - ".$pl[$key]["Artist"]." - ".$pl[$key]['Title']."$b2</a> | <a href=\"$me?del=$key&$rnd\">del</a><br />\n";
  }
  print "</p>\n<p><a href=\"index.php\">Back</a></p>\n";
  $mpd->Disconnect();
?>
</body>
</html>
