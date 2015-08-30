<?php
$i = 0;
$j = 0;
if(count($search->artist) > 0)
	echo '<span class="list-group-item active list-title">Artists</span>';

foreach($search->artist as $artist){
	echo '<a href="#" data-artist-id="' . $artist->attributes()->id . '" class="list-group-item artist-item">' . $artist->attributes()->name . '</a>';
	$i++;
	$j++;
	if($i > 1 && !$complete)
		break;
}

if(count($search->album) > 0)
	echo '<span class="list-group-item active list-title">Albums</span>';
$i = 0;
foreach($search->album as $album){
	echo '<a href="#" data-album-id="' . $album->attributes()->id . '" class="list-group-item album-item">' . $album->attributes()->name . ' <i class="small muted italics artist-name">' . $album->attributes()->artist . '</i></a>';
	$i++;
	$j++;
	if($i > 2 && !$complete)
		break;
}

if(count($search->song) > 0)
	echo '<span class="list-group-item active list-title">Songs</span>';
$i = 0;
foreach($search->song as $song){
	$duration = floor($song->attributes()->duration / 60);
	$min = $song->attributes()->duration % 60;
	if($min < 10)
		$min = '0' . $min;
	$duration = $duration . ':' . $min;
?>
	<a href="#" data-album-id="<?php echo $song->attributes()->albumId;?>" data-music='{"mp3":"<?php echo site_url('main/stream/' . $song->attributes()->id);?>", "title":"<?php echo str_replace('\'', '&#39;', $song->attributes()->title);?>", "artist":"<?php echo str_replace('\'', '&#39;', $song->attributes()->artist);?>", "album":"<?php echo str_replace('\'', '&#39;', $song->attributes()->album);?>", "duration":"<?php echo $duration;?>", "cover": "<?php echo site_url('main/getCover/' . $song->attributes()->coverArt);?>/200"}' class="list-group-item song-item"><?php echo $song->attributes()->title;?> <i class="small muted italics artist-name"><?php echo $song->attributes()->artist;?></i></a>
<?php 
	$i++;
	$j++;
	if($i > 8 && !$complete)
		break;
}
if($j == 0){
	echo '<span class="list-group-item">No result found</span>';
}
?>
