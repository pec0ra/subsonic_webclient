<?php
	echo '<h2>' . $title . '</h2>';
?>
<table class="table table-hover">
<caption>
<?php
if(isset($is_playlist) && $is_playlist){
	echo '<button type="button" class="btn btn-primary" id="delete-playlist">Delete Playlist</button>';
}
?>
  	<button type="button" class="btn btn-primary" id="select-all-songs">Select all</button>
	<div class="btn-group" role="group" aria-label="album">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">All <span class="caret"></span></button>
  		<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="play-album">Play</a></li>
    			<li><a href="#" id="add-album">Add to queue</a></li>
			<?php if(!isset($is_shared)){?>
    			<li><a href="#" id="add-playlist-album">Add to playlist</a></li>
    			<li><a href="#" id="share-album">Share</a></li>
			<?php }?>
  		</ul>
	</div>
	<div class="btn-group" role="group" aria-label="selected">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Selected <span class="caret"></span></button>
  		<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="play-selected">Play</a></li>
    			<li><a href="#" id="add-selected">Add to queue</a></li>
			<?php if(!isset($is_shared)){?>
    			<li><a href="#" id="add-playlist-selected">Add to playlist</a></li>
    			<li><a href="#" id="share-selected">Share</a></li>
			<?php }?>
<?php if(isset($is_playlist) && $is_playlist){?>
    			<li><a href="#" id="delete-playlist-selected">Remove from playlist</a></li>
<?php } ?>
  		</ul>
	</div>
</caption>
<tr>
	<th></th>
	<th>Title</th>
	<th>Artist</th>
	<th>Album name</th>
	<th>Duration</th>
</tr>
<?php
$i = 0;
foreach($songs as $song){
	$duration = floor($song->attributes()->duration / 60);
	$min = $song->attributes()->duration % 60;
	if($min < 10)
		$min = '0' . $min;
	$duration = $duration . ':' . $min;
?>
<tr>
	<td><div class="checkbox checkbox-primary"><label><input type="checkbox" data-music='{"mp3":"<?php if(isset($song->attributes()->mp3) && $song->attributes()->mp3){echo $song->attributes()->mp3;} else {echo site_url('main/stream/' . $song->attributes()->id);}?>", "title":"<?php echo str_replace('\'', '&#39;', $song->attributes()->title);?>", "artist":"<?php echo str_replace('\'', '&#39;', $song->attributes()->artist);?>", "album":"<?php echo str_replace('\'', '&#39;', $song->attributes()->album);?>", "duration":"<?php echo $duration;?>", "cover": "<?php echo site_url('main/getCover/' . $song->attributes()->coverArt);?>/200", "id":<?php echo $song->attributes()->id; if(isset($is_playlist) && $is_playlist){echo ', "position":' . $i;}?>}' name="select-<?php echo $song->attributes()->id;?>" value="selected"></label></div></td>
	<td><a class="song" href="#" data-music='{"mp3":"<?php if(isset($song->attributes()->mp3) && $song->attributes()->mp3){echo $song->attributes()->mp3;} else {echo site_url('main/stream/' . $song->attributes()->id);}?>", "title":"<?php echo str_replace('\'', '&#39;', $song->attributes()->title);?>", "artist":"<?php echo str_replace('\'', '&#39;', $song->attributes()->artist);?>", "album":"<?php echo str_replace('\'', '&#39;', $song->attributes()->album);?>", "duration":"<?php echo $duration;?>", "cover": "<?php echo site_url('main/getCover/' . $song->attributes()->coverArt);?>/200", "id":<?php echo $song->attributes()->id;?>}'><?php if($song->attributes()->track) { echo $song->attributes()->track;?> - <?php } echo $song->attributes()->title;?></a></td>
	<td class="text-muted"><?php echo $song->attributes()->artist;?></td>
	<td class="text-muted"><?php echo $song->attributes()->album;?></td>
	<td class="text-muted"><?php echo $duration;?></td>
</tr>
<?php
	$i++;
}
?>
</table>
<?php
if(isset($is_playlist) && $is_playlist){
?>
<div class="modal fade in" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    		<div class="modal-content">
			<div class="modal-header">
        			<button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
        			<h3>Delete playlist</h3>
      			</div>
      			<div class="modal-body">
				<div class="loader playlist-loader"><div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div></div>
				<p class="body-text alert alert-danger" role="alert"></p>
      			</div>
      			<div class="modal-footer">
        			<button class="btn btn-flat btn-default close-modal">Close</button>
        			<button class="btn btn-danger confirm">Delete</button>
      			</div>
		</div>
	</div>
</div>
<?php
}
?>

<div class="modal fade in" id="add-playlist-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    		<div class="modal-content">
			<div class="modal-header">
        			<button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
        			<h3>Add to playlist</h3>
      			</div>
      			<div class="modal-body">
				<div class="loader playlist-loader"><div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div></div>
				<p class="body-error alert alert-danger" role="alert">You don't have the permission to edit this playlist</p>
				<ul class="list-group">

<?php
$first = true;
foreach($playlists as $playlist){
?>
					<li class="list-group-item<?php if($first){echo ' active';$first = false;};?>" data-id="<?php echo $playlist->attributes()->id;?>"><?php echo $playlist->attributes()->name;?></li>
<?php
}
?>
				</ul>
      			</div>
      			<div class="modal-footer">
        			<a href="#" class="btn btn-default btn-flat close-modal">Close</a>
        			<a href="#" class="btn btn-primary confirm">Save</a>
      			</div>
		</div>
	</div>
</div>
