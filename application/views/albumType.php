<h2><?php echo $title;?></h2>
<table class="table table-hover">
<caption>
  	<button type="button" class="btn btn-material-indigo-500" id="newest">Newest</button>
  	<button type="button" class="btn btn-material-indigo-500" id="frequent">Frequent</button>
  	<button type="button" class="btn btn-material-indigo-500" id="recent">Recent</button>
  	<button type="button" class="btn btn-primary" id="select-all-albums">Select all</button>
	<div class="btn-group" role="group" aria-label="artist">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">All <span class="caret"></span></button>
  		<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="play-album">Play</a></li>
    			<li><a href="#" id="add-album">Add to queue</a></li>
    			<li><a href="#" id="view-songs-album">View Songs</a></li>
    			<li><a href="#" id="add-playlist-album">Add to playlist</a></li>
  		</ul>
	</div>
	<div class="btn-group" role="group" aria-label="selected">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Selected <span class="caret"></span></button>
  		<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="play-selected">Play</a></li>
    			<li><a href="#" id="add-selected">Add to queue</a></li>
    			<li><a href="#" id="view-songs-selected">View Songs</a></li>
    			<li><a href="#" id="add-playlist-selected">Add to playlist</a></li>
  		</ul>
	</div>
</caption>
<tr>
	<th></th>
	<th>Album cover</th>
	<th>Album name</th>
	<th>Artist</th>
	<th>Year</th>
	<th>Tracks</th>
</tr>
<?php
foreach($albums as $album){
?>
<tr>
	<td><div class="checkbox checkbox-primary"><label><input class="checkbox-input" type="checkbox" name="select-<?php echo $album->attributes()->id;?>" value="selected"  data-albumid="<?php echo $album->attributes()->id;?>"></label></div></td>
	<td><img src="<?php echo site_url('main/getCover/' . $album->attributes()->coverArt . '/80');?>" class="shadow-z-1"/></td>
	<td><a href="#album" onclick="loadPage('main', 'album', <?php echo $album->attributes()->id;?>, false);return false;"><?php echo $album->attributes()->name;?></a></td>
	<td><a class="text-muted" onclick="loadPage('main', 'artist', <?php echo $album->attributes()->artistId;?>, false);return false;" href="#artist"><?php echo $album->attributes()->artist;?></a></td>
	<td class="text-muted"><?php echo $album->attributes()->year;?></td>
	<td class="text-muted"><?php echo $album->attributes()->songCount;?></td>
</tr>
<?php
}
?>
</table>

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
        			<button class="btn btn-flat close-modal">Close</button>
        			<button class="btn btn-primary confirm">Save</button>
      			</div>
		</div>
	</div>
</div>

