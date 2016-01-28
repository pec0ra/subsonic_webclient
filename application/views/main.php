<!DOCTYPE html>
<html lang="en">
   <head>
      	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
       	<title>Subsonic</title>
	<link rel="icon" type="image/png" href="<?php echo base_url();?>assets/img/icon.png" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrap-spinner.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/material-fullpalette.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/roboto.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ripples.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/player.css">
      	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/css/style-material.css" />
   </head>
   <body>
	<nav class="navbar navbar-inverse navbar-fixed-top shadow-z-2">
  		<div class="container-fluid">
    		<!-- Brand and toggle get grouped for better mobile display -->
    		<div class="navbar-header">
    			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
        			<span class="sr-only">Toggle navigation</span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
      			</button>
      			<a class="navbar-brand" href="#">Subsonic</a>
    		</div>

    		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="navbar-collapse-wrapper">
    		<div class="collapse navbar-collapse" id="navbar-collapse">
			<div class="menu-controls">
				<a><i class="mdi-navigation-arrow-back close-menu"></i></a>
				<h3><a class="menu-brand" href="#">Subsonic</a></h3>
			</div>
      			<ul class="nav navbar-nav">
        			<li class="artists-link active"><a href="#" id="artists-link">Artists<span class="sr-only">(current)</span></a></li>
        			<li class="playlists-link"><a href="#" id="playlist-link">Playlists</a></li>
        			<li class="new-link"><a href="#" id="new-link">Albums</a></li>
				<li class="config-link"><a href="#" id="config-link">Config</a></li>
      			</ul>
      			<ul class="nav navbar-nav navbar-right">
      				<form class="navbar-form navbar-left" role="search" id="ajax-search-form" autocomplete="off">
        				<div class="form-group">
          					<input type="text" class="search-input form-control" placeholder="Search" />
        				</div>
        				<!--<button type="submit" class="btn btn-default">Submit</button>-->
				</form>
        			<li class="search-link"><a href="#" id="search-link">Search</a></li>
      			</ul>
    		</div><!-- /.navbar-collapse -->
		<div class="navbar-collapse-background"></div>
		</div>
  	</div><!-- /.container-fluid -->
</nav>
	<div class="container-fluid height-100">
		<div class="row height-100">
		<div class="text-center loader body-loader" id="body-loader">
			<div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div>
		</div>
		<div id="body">
<?php if(isset($is_shared) && $is_shared){
	$this->load->view('album', $data);
 }?>
			</div>
			<div id="player" class="shadow-z-2"></div>
		</div>
	</div>
	<div class="modal fade in" id="playlist-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
    			<div class="modal-content">
				<div class="modal-header">
        				<button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
        				<h3>New Playlist</h3>
	      			</div>
	      			<div class="modal-body">
					<div class="loader playlist-loader"><div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div></div>
					<form id="new-playlist-form">
	                    			<div class="form-group">
	                        			<label class="control-label">Playlist name</label>
	                            			<input id="playlist-name" type="text" class="form-control" name="playlist-name" />
	                    			</div>
	                		</form>
	      			</div>
	      			<div class="modal-footer">
	        			<button class="btn btn-flat close-modal">Close</button>
	        			<button class="btn btn-primary" id="save-playlist">Save changes</button>
	      			</div>
			</div>
		</div>
	</div>

	<div class="modal fade in" id="share-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
    			<div class="modal-content">
				<div class="modal-header">
        				<button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
        				<h3>Share Playlist</h3>
	      			</div>
	      			<div class="modal-body">
					<div class="loader share-loader"><div class="spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div></div>
					<form id="share-form">
	                    			<div class="form-group">
	                        			<label class="control-label">Link</label>
	                            			<input id="share-link" type="text" class="form-control" name="share-link" />
	                    			</div>
	                		</form>
					<form id="guest-config-form" action="#" method="POST" accept-charset="utf-8">
						<div class="form-group">
							<label for="server" class=" control-label">Server</label>
    							<div class="">
								<input type="text" class="form-control" name="server" id="server" placeholder="https://<your_server>.com" <?php
							if($this->session->server)
								echo 'value="' . $this->session->server . '"';
?>>	
							</div>
	  					</div>
						<div class="form-group">
							<label for="guest-pseudo" class=" control-label">Guest pseudo</label>
	    						<div class="">
								<input type="text" class="form-control" name="guest-pseudo" id="guest-pseudo" placeholder="Enter pseudo" value="guest" />
							</div>
	  					</div>
	  					<div class="form-group">
							<label for="guest-password" class=" control-label">Guest password</label>
	    						<div class="">
	    							<input type="password" class="form-control" name="guest-password" id="guest-password" placeholder="Password">
							</div>
	  					</div>
					</form>
	      			</div>
	      			<div class="modal-footer">
	        			<button class="btn btn-flat close-modal">Close</button>
	        			<button class="btn btn-primary" id="get-link">Create link</button>
	      			</div>
			</div>
		</div>
	</div>
	<script src="<?php echo base_url();?>assets/js/jquery-1.11.3.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/ttw-music-player.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.jplayer.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/search.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.spinner.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/listgroup.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/json2.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/jstorage.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/ripples.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/material.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/main.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/config.js"></script>
	<script type="text/javascript">
	<?php if(isset($is_shared) && $is_shared){?>
		is_shared = true;
		history.pushState({controller: 'main', method: 'shared', arg1: ''}, 'shared', '#shared');
		albumPage();
		$(function(){
			var album = [];
			$(':checkbox').each(function(){
				album.push($(this).data('music'));
			});
			player.changePlaylist(album);
			player.playlistAdvance(0);
		});
		<?php } else {?>
		is_shared = false;
	<?php }?>
		serverAddress = '<?php echo $this->session->server;?>';
		base_address = '<?php echo base_url();?>';



            $(document).ready(function() {
                // This command is used to initialize some elements and make them work properly
                $.material.init();
            });
	</script>
   </body>
</html>
