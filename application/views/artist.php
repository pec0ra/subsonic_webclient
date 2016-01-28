<div class="artist-header row">
<?php if($artist->largeImageUrl != ''){?>
	<div class="col-lg-2">
		<img src="<?php echo $artist->largeImageUrl;?>" class="shadow-z-1"/>
	</div>
	<div class="col-lg-10">
<?php } else { echo '<div class="col-lg-12">';} ?>
		<h2><?php echo $title;?></h2>
		<p class="lead biography"><small><?php echo $artist->biography;?></small></p>
	</div>
</div>
<table class="table table-hover">
<caption>
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
  	<button type="button" class="btn btn-primary" id="start-radio">Start radio</button>
</caption>
<tr>
	<th></th>
	<th>Album cover</th>
	<th>Album name</th>
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

<div class="modal fade in" id="radio-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    		<div class="modal-content">
			<div class="modal-header">
        			<button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
        			<h3>Start radio</h3>
      			</div>
      			<div class="modal-body">
				<div class="loader playlist-loader"><div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div></div>
				<p class="body-text">Number of songs :</p>

				<div class="input-group spinner" data-trigger="spinner" id="spinner">
  					<input type="text" class="form-control" value="50" data-max="400" data-min="0" data-step="10" id="radio-count">
					<span class="input-group-addon">
            					<a href="javascript:;" class="spin-up" data-spin="up"><i class="fa fa-sort-asc"></i></a>
              					<a href="javascript:;" class="spin-down" data-spin="down"><i class="fa fa-sort-desc"></i></a>
					</span>
				</div>
      			</div>
      			<div class="modal-footer">
        			<button class="btn btn-flat close-modal">Close</button>
        			<button class="btn btn-primary confirm">Start</button>
      			</div>
		</div>
	</div>
</div>
