<?php
define('NO_SESSION','1');
require_once '../lib/init.php';
$playlist_ids = preg_split("/-/",$_REQUEST['playlist_id']);
$song_include = false;
foreach($playlist_ids as $playlist_id){
  $playlist = new Playlist(intval($playlist_id));
  $playlist->format();
  foreach($playlist -> get_songs() as $song_id){
    if($song_id == intval($_REQUEST['song_id'])){
      $song_include = true;
    }
  }
}
$basename = "";
$file     = "";
if($song_include){
  $song = new Song(intval($_REQUEST['song_id']));
  $file = $song -> file;
  $basename = basename($file);
}else{
  $file = $basename = "nosound.mp3";
}
header("Content-Type: audio/mpeg");
header("Content-Disposition: attachment; filename=$basename");
readfile($file);
