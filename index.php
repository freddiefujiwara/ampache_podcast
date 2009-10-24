<?php
define('NO_SESSION','1');
define('BASE_URL','http://music.example.com');
define('TITLE','music.example.com');
define('OWNER','Fumikazu Fujiwara');
require_once '../lib/init.php';
$user         = new User($_REQUEST['user_id']);
$playlist_ids = Playlist::get_users(intval($_REQUEST['user_id']));
$songs = array();
$updated_at = 0;
foreach($playlist_ids as $playlist_id){
  $playlist = new Playlist($playlist_id);
  $updated_at = $playlist -> date > $updated_at ? $playlist -> date : $updated_at;
  $playlist -> format();
  $songs = array_merge($songs,$playlist->get_songs());
}
$updated_at += count($songs);
header ("Content-Type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:content="http://purl.org/rss/1.0/modules/content/"
 xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd"
 version="2.0">
  <channel>
    <ttl>0</ttl>
    <title><?php echo TITLE ?></title>
    <link><?php echo BASE_URL ?>/</link>
    <description><?php echo $user->fullname ?>'s playlist</description>
    <language>ja</language>
    <lastBuildDate><?php echo date('r', $updated_at) ?></lastBuildDate>
    <copyright><?php echo OWNER ?>. All Rights Reserved.</copyright>
    <category>Music</category>
    <itunes:category text="Music"></itunes:category>
    <image>
        <url><?php echo BASE_URL ?>/themes/classic/images/ampache.png</url>
        <link><?php echo BASE_URL ?>/</link>
        <title><?php echo TITLE ?></title>
    </image>
    <itunes:author><?php echo OWNER ?></itunes:author>
    <itunes:summary><?php echo $user->fullname ?>'s playlist</itunes:summary>
    <itunes:owner>
        <itunes:name><?php echo OWNER ?></itunes:name>
    </itunes:owner>
    <itunes:image href="<?php echo BASE_URL ?>/themes/classic/images/ampache.png" />
<?php 
foreach($songs as $song_id) { 
  $song = new Song($song_id);
?>
    <item>
      <title><?php echo $song -> title ?></title>
      <link><?php echo BASE_URL ?>/podcasts/<?php echo implode("-",$playlist_ids) ?>/<?php echo $song -> id ?>.mp3</link>
      <description><?php echo $song -> get_album_name() ?> by <?php echo $song -> get_artist_name() ?></description>
      <guid><?php echo BASE_URL ?>/podcasts/<?php echo implode("-",$playlist_ids) ?>/<?php echo $song -> id ?>.mp3?updated_at=<?php echo $updated_at ?></guid>
      <dc:creator><?php echo TITLE ?></dc:creator>
      <pubDate><?php echo date('r', $updated_at) ?></pubDate>
      <enclosure url="<?php echo BASE_URL ?>/podcasts/<?php echo implode("-",$playlist_ids) ?>/<?php echo $song -> id ?>.mp3?updated_at=<?php echo $updated_at ?>" length="<?php echo $song -> size ?>" type="audio/mpeg" />
      <category>Music</category>
      <itunes:author><?php echo OWNER ?></itunes:author>
      <itunes:category text="Music"></itunes:category>
      <itunes:summary><?php echo $song -> get_album_name() ?> by <?php echo $song -> get_artist_name() ?></itunes:summary>
      <itunes:explicit>no</itunes:explicit>
      <itunes:duration><?php echo intval($song->time/60) ?>:<?php echo intval($song->time%60) < 10 ? "0".intval($song->time%60) : intval($song->time%60) ?></itunes:duration>
    </item>
<?php $updated_at--;} ?>
  
  </channel>
</rss>
