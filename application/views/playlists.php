<h2>Playlists</h2>
<table class="table table-hover">
<caption>
	<button type="button" class="btn btn-primary" id="new-playlist">New Playlist</button>
</caption>
<tr>
	<th>Name</th>
	<th>Owner</th>
	<th>Tracks</th>
	<th>Duration</th>
</tr>
<?php
foreach($playlists as $playlist){
	$duration = floor($playlist->attributes()->duration / 3600);
	$min = floor(($playlist->attributes()->duration % 3600) / 60);
	if($min < 10)
		$min = '0' . $min;
	$duration = $duration . 'h' . $min;
?>
<tr>
	<td><a href="#album" onclick="loadPage('main', 'playlist', <?php echo $playlist->attributes()->id;?>, false);return false;"><?php echo $playlist->attributes()->name;?></a></td>
	<td class="text-muted"><?php echo $playlist->attributes()->owner;?></td>
	<td class="text-muted"><?php echo $playlist->attributes()->songCount;?></td>
	<td class="text-muted"><?php echo $duration;?></td>
</tr>
<?php
}
?>
</table>
