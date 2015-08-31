
/*
 * History manager
 */

page_loaded = true;

window.onpopstate = function (event){
	loadPage(event.state.controller, event.state.method, event.state.arg1, true);
}

pageXhr = null;
function loadPage(controller, method, arg1, back){
	if(pageXhr != null)
		pageXhr.abort();

	smallPlayer();
	if(method == 'playlists' || method == 'playlist'){
		$('.artists-link').removeClass('active');
		$('.config-link').removeClass('active');
		$('.playlists-link').addClass('active');
		$('.new-link').removeClass('active');
	} else if(method == 'config'){
		$('.artists-link').removeClass('active');
		$('.config-link').addClass('active');
		$('.playlists-link').removeClass('active');
		$('.new-link').removeClass('active');
	} else if(method == 'albumsType'){
		$('.artists-link').removeClass('active');
		$('.config-link').removeClass('active');
		$('.playlists-link').removeClass('active');
		$('.new-link').addClass('active');
	} else {
		$('.playlists-link').removeClass('active');
		$('.config-link').removeClass('active');
		$('.artists-link').addClass('active');
		$('.new-link').removeClass('active');
	}
	if(!back){
		hash = '#' + method;
		if(arg1 != '')
			hash += '-' + arg1;

		history.pushState({controller: controller, method: method, arg1: arg1}, method, hash);
	}
	$('.modal').modal('hide');
	$('#body').hide();
	$('#body-loader').show();
	$('body').removeClass('modal-open');
	$('.modal-backdrop').remove();
	address = base_address + controller + '/' + method;
	if(arg1 != ''){
		address += '/' + arg1;
	}

	// execute per page pre action
	if(method == 'artist'){
		preLoadArtist(arg1);
	}
	// Load the body content
	pageXhr = $.ajax({
		url: address,
	})
	.error(function(x, error){
		pageXhr = null;
		if(error != 'abort'){
			$('#body').show();
			$('#body-loader').hide();
			alert('An error occured ! Please try again.');
		}
	})
	.done(function( data ) {
		pageXhr = null;

		$('#body').html(data);
		$('.dropdown-menu a').click(function(){
			$('.btn-group').removeClass('open');
		})
		if(page_loaded){
			$('#body').show();
			$('#body').scrollTop(0);
			$('#body-loader').hide();
			if($('#config-title').html() == 'Config' && method != 'config'){
				history.pushState({controller: 'main', method: 'config', arg1: ''}, 'config', '#config');
				configPage();
				return;
			}

			// execute page actions
			if(method == 'artist'){
				artistPage(arg1);
			} else if(method == 'album' || method == 'albums' || method == 'playlist'){
				albumPage();
				if(method == 'playlist')
					playlistPage(arg1);
				$.material.init();
			} else if(method == 'search'){
				searchPage();
			} else if(method == 'playlists'){
				playlistsPage();
			} else if(method == 'config'){
				configPage();
			} else if(method == 'albumsType'){
				albumsTypePage();
			}
		} else {
			page_loaded = true;
		}

	});

}

$(window).on('beforeunload', function(e) {
	if(player.playing){
		return 'Are you sure you want to leave ?';
	}
});


/*
 * Pages
 */
function albumPage(){
	checkedSong = [];
	$('#select-all-songs').click(function(){
		selectAllSongs();
		return false;
	});
	$('#play-selected').click(function(){
		player.changePlaylist(checkedSong);
		player.playlistAdvance(0);
		return false;
	})

	$('#add-selected').click(function(){
		for(var i = 0; i < checkedSong.length; i++){
			player.pushToPlaylist(checkedSong[i]);
		}
		return false;
	})

	$('.list-group').listgroup({});
	$('#add-playlist-selected').click(function(){
		first = true
		var ids = '';
		for(var i = 0; i < checkedSong.length; i++){
			if(!first){
				ids += '-';
			}
			first = false;
			ids += checkedSong[i].id;
		}
		addToPlaylist(ids);

		return false;
	})
	$('#add-playlist-album').click(function(){
		var first = true;
		ids = '';
		$(':checkbox').each(function(){
			if(!first){
				ids += '-';
			}
			first = false;
			ids += $(this).data('music').id;
		});
		addToPlaylist(ids);

		return false;
	})

	$('#play-album').click(function(){
		var album = [];
		$(':checkbox').each(function(){
			album.push($(this).data('music'));
		})
		player.changePlaylist(album);
		player.playlistAdvance(0);
		return false;
	})

	$('#add-album').click(function(){
		$(':checkbox').each(function(){
			player.pushToPlaylist($(this).data('music'));
		});
		return false;
	})
	
	$('.song').click(function(){
		player.changePlaylist([$(this).data('music')]);
		player.playlistAdvance(0);
		return false;
	})
	$(':checkbox').click(function(){
		checkSong($(this).data('music'));
	})

	$('#share-album').click(function(){
		songs = [];
		$(':checkbox').each(function(){
			songs[songs.length] = $(this).data('music');
		});
		share(songs);

		return false;
	})
	$('#share-selected').click(function(){
		songs = [];
		for(var i = 0; i < checkedSong.length; i++){
			songs[songs.length] = checkedSong[i];
		}
		share(songs);

		return false;
	})
}


function artistPage(artistId){
	checkedSong = [];
	checkedAlbums = []
	$('#select-all-albums').click(function(){
		selectAllAlbums();
		return false;
	});
	$('#play-selected').click(function(){
		playAlbums();
		return false;
	})

	$('#add-selected').click(function(){
		addAlbums();
		return false;
	})

	$('.list-group').listgroup({});
	$('#add-playlist-selected').click(function(){
		ids = '';
		first = true
		for(var j = 0; j < checkedAlbums.length; j++){
			id = checkedAlbums[j];
			for(var i in albumsData[id]){
				if(!first){
					ids += '-';
				}
				first = false;
				ids += albumsData[id][i].id[0];
			}
		}
		addToPlaylist(ids);

		return false;
	})
	$('#add-playlist-album').click(function(){
		ids = '';
		first = true
		for(var id in albumsData){
			for(var i in albumsData[id]){
				if(!first){
					ids += '-';
				}
				first = false;
				ids += albumsData[id][i].id[0];
			}
		}
		addToPlaylist(ids);

		return false;
	})

	$('#play-album').click(function(){
		checkedSong = [];
		for(var id in albumsData){
			for(var i in albumsData[id]){
				checkedSong.push({
					mp3:albumsData[id][i].mp3,
					title:albumsData[id][i].title[0],
					artist:albumsData[id][i].artist[0],
					album:albumsData[id][i].album[0],
					duration:albumsData[id][i].duration,
					cover:albumsData[id][i].cover,
					id:albumsData[id][i].id
				})
			}
		}
		player.changePlaylist(checkedSong);
		player.playlistAdvance(0);
		return false;
	})

	$('#add-album').click(function(){
		checkedSong = [];
		for(var id in albumsData){
			for(var i in albumsData[id]){
				player.pushToPlaylist({
					mp3:albumsData[id][i].mp3,
					title:albumsData[id][i].title[0],
					artist:albumsData[id][i].artist[0],
					album:albumsData[id][i].album[0],
					duration:albumsData[id][i].duration,
					cover:albumsData[id][i].cover,
					id:albumsData[id][i].id
				});
			}
		}
		return false;
	})

	$('#view-songs-selected').click(function(){
		if(checkedAlbums.length == 0)
			return false;
		if(checkedAlbums.length == 1){
			loadPage('main','album', checkedAlbums[0], false);
			return false;
		}

		var albums = checkedAlbums[0];
		for(var j = 1; j < checkedAlbums.length; j++){
			albums += '-' + checkedAlbums[j];
		}
		loadPage('main','albums', albums, false);
		return false
	})
	$('#view-songs-album').click(function(){
		if(albumsData.length == 0)
			return false;
		if(albumsData.length == 1){
			for(id in albumsData) break;
			loadPage('main','album', id, false);
			return false;
		}

		albums = '';
		first = true;
		for(var id in albumsData){
			if(!first){
				albums += '-';
			} else {
				first = false;
			}
			albums += id;
		}
		loadPage('main','albums', albums, false);
		return false;
	})
	$("#spinner").spinner('changing', function(e, newVal, oldVal){
	});
	$('#radio-modal .close-modal').click(function(){
		$('#radio-modal').modal('hide');
		return false;
	})
	$('#radio-modal .confirm').click(function(){
		count = parseInt($('#radio-modal #radio-count').val());
		getRadio(artistId, count);
		$('#radio-modal').modal('hide');
		return false;
	})
	$('#start-radio').click(function(){
		$('#radio-modal').modal('show');
		return false;
	})

	$(':checkbox').click(function(){
		checkAlbum($(this).data('albumid'));
	})
        $.material.init();
}


function albumsTypePage(){
	checkedSong = [];
	checkedAlbums = []
	$('#select-all-albums').click(function(){
		selectAllAlbums();
		return false;
	});
	$('#play-selected').click(function(){
		playAlbums();
		return false;
	})

	$('#add-selected').click(function(){
		addAlbums();
		return false;
	})

	$('.list-group').listgroup({});
	$('#add-playlist-selected').click(function(){
		ids = '';
		first = true
		for(var j = 0; j < checkedAlbums.length; j++){
			id = checkedAlbums[j];
			for(var i in albumsData[id]){
				if(!first){
					ids += '-';
				}
				first = false;
				ids += albumsData[id][i].id[0];
			}
		}
		addToPlaylist(ids);

		return false;
	})
	$('#add-playlist-album').click(function(){
		ids = '';
		first = true
		for(var id in albumsData){
			for(var i in albumsData[id]){
				if(!first){
					ids += '-';
				}
				first = false;
				ids += albumsData[id][i].id[0];
			}
		}
		addToPlaylist(ids);

		return false;
	})

	$('#play-album').click(function(){
		checkedSong = [];
		for(var id in albumsData){
			for(var i in albumsData[id]){
				checkedSong.push({
					mp3:albumsData[id][i].mp3,
					title:albumsData[id][i].title[0],
					artist:albumsData[id][i].artist[0],
					album:albumsData[id][i].album[0],
					duration:albumsData[id][i].duration,
					cover:albumsData[id][i].cover,
					id:albumsData[id][i].id
				})
			}
		}
		player.changePlaylist(checkedSong);
		player.playlistAdvance(0);
		return false;
	})

	$('#add-album').click(function(){
		checkedSong = [];
		for(var id in albumsData){
			for(var i in albumsData[id]){
				player.pushToPlaylist({
					mp3:albumsData[id][i].mp3,
					title:albumsData[id][i].title[0],
					artist:albumsData[id][i].artist[0],
					album:albumsData[id][i].album[0],
					duration:albumsData[id][i].duration,
					cover:albumsData[id][i].cover,
					id:albumsData[id][i].id
				});
			}
		}
		return false;
	})

	$('#view-songs-selected').click(function(){
		if(checkedAlbums.length == 0)
			return false;
		if(checkedAlbums.length == 1){
			loadPage('main','album', checkedAlbums[0], false);
			return false;
		}

		var albums = checkedAlbums[0];
		for(var j = 1; j < checkedAlbums.length; j++){
			albums += '-' + checkedAlbums[j];
		}
		loadPage('main','albums', albums, false);
		return false
	})
	$('#view-songs-album').click(function(){
		if(albumsData.length == 0)
			return false;
		if(albumsData.length == 1){
			for(id in albumsData) break;
			loadPage('main','album', id, false);
			return false;
		}

		albums = '';
		first = true;
		for(var id in albumsData){
			if(!first){
				albums += '-';
			} else {
				first = false;
			}
			albums += id;
		}
		loadPage('main','albums', albums, false);
		return false;
	})
	$('#newest, #frequent, #recent').click(function(){
		loadPage('main', 'albumsType', $(this).attr('id'), false);
		return false;
	})

	$(':checkbox').click(function(){
		checkAlbum($(this).data('albumid'));
	})
        $.material.init();
}

function searchPage(){
	$('#search-form').search({
		submit:function(plugin){

			$('#search-form').find('.search-input').blur();
			if(plugin.query == '')
				return false;
			plugin.sendSearch();
			return false
		},
		results:$('#search-results'),
		completeReturn:true
	});
	var query = $('#search-form').find('.search-input').val();
	if(query != ''){
		$('#search-form').submit();
	}
	$.material.init();
}

function playlistsPage(){

	$('#save-playlist').off('click').on('click', function(){
		$(this).attr('disabled','disabled');
		$('#playlist-modal .playlist-loader').show();
		$('#playlist-modal #new-playlist-form').hide();
		$.ajax({
			url: base_address + 'main/newPlaylist/'+$('#playlist-name').val(),
			success: function(data){
				if(data == 'ok'){
					$('#playlist-modal').modal('hide');
					loadPage('main', 'playlists', '', false);
				} else {
					alert('An error occured ! Please try again.');
				}
			},
			error: function(){
				alert('An error occured ! Please try again.');
			}
		});
		return false;
	})
	$('#new-playlist').click(function(){
		newPlaylist(function(data, callback){
			$.ajax({
				url: base_address + 'main/newPlaylist/'+$('#playlist-name').val(),
				success: function(data){
					if(data == 'ok'){
						callback();
						loadPage('main', 'playlists', '', false);
					} else {
						alert('An error occured ! Please try again.');
					}
				},
				error: function(){
					alert('An error occured ! Please try again.');
				}
			})
		}, null)
	})
	$.material.init();
}

function playlistPage(id){
	$('#delete-playlist').click(function(){
		confirmModal('Delete playlist', 'Are you sure you want to delete this playlist ?', function(id, callback){
			$.ajax({
				url: base_address + 'main/deletePlaylist/'+id,
				success: function(data){
					callback();
					if(data == 'ok'){
						loadPage('main', 'playlists', '', false);
					} else {
						alert('An error occured ! Please try again.');
					}
				},
				error: function(){
					callback();
					alert('An error occured ! Please try again.');
				}
			});
		}, id);
	})

	$('#delete-playlist-selected').click(function(){
		first = true
		var ids = '';
		for(var i = 0; i < checkedSong.length; i++){
			if(!first)
				ids += '-';
			first = false;
			ids += checkedSong[i].position;
		}
		confirmModal('Remove from playlist', 'Are you sure you want to remove the songs ?', function(data, callback){
			id = data[0];
			ids = data[1];
			$.ajax({
				url: base_address + 'main/removeFromPlaylist/'+id+'/'+ids,
				success: function(data){
					callback();
					if(data == '0'){
						loadPage('main', 'playlist', id, false);
					} else if(data == '2'){
						alert('You don\'t have the permission to edit this playlist !');
					} else {
						alert('An error occured ! Please try again.');
					}
				},
				error: function(){
					callback();
					alert('An error occured ! Please try again.');
				}
			});
		}, [id, ids]);

		return false;
	})
}






albumsData = [];
function preLoadArtist(id, html_data){
	page_loaded = false;
	$.getJSON(base_address + 'main/getAlbumsJSON/' + id, [], function(data){
		albumsData = data;
		if(page_loaded){
			$('#body').show();
			$('#body').scrollTop(0);
			$('#body-loader').hide();
			artistPage(id);
		} else {
			page_loaded = true;
		}
	});
}


/*
 * Checkboxes
 */

checkedSong = [];
function selectAllSongs(){
	if($(':checkbox').filter(':checked').length != $(':checkbox').length){
		$(':checkbox').prop( "checked", true ).trigger('change').each(function(){
			var index = checkedSong.indexOf($(this).data('music'));
			if(!(index > -1)){
				checkedSong.push($(this).data('music'));
			}
		});
	} else {
		$(':checkbox').prop( "checked", false ).trigger('change').each(function(){
			var index = checkedSong.indexOf($(this).data('music'));
			if(index > -1){
				checkedSong.splice(index, 1);
			}
		});
	}
	return false;
}

function checkSong(song){
	var index = checkedSong.indexOf(song);
	if(!(index > -1)){
		checkedSong.push(song);
	} else {
		checkedSong.splice(index, 1);
	}
}

checkedAlbums = []
function selectAllAlbums(){
	if($(':checkbox').filter(':checked').length != $(':checkbox').length){
		$(':checkbox').prop( "checked", true );
		for(var id in albumsData){
			checkAlbum(id);
		}
	} else {
		$(':checkbox').prop( "checked", false );
		checkedAlbums = [];

	}
	return false;
	for(var id in albumsData){
		checkAlbum(id);
	}
}
function checkAlbum(id){
	var index = checkedAlbums.indexOf(id);
	if(!(index > -1)){
		checkedAlbums.push(id);
	} else {
		checkedAlbums.splice(index, 1);
	}
}
function playAlbums(){
	checkedSong = [];
	for(var j = 0; j < checkedAlbums.length; j++){
		id = checkedAlbums[j];
		for(var i in albumsData[id]){
			checkedSong.push({
				mp3:albumsData[id][i].mp3,
				title:albumsData[id][i].title[0],
				artist:albumsData[id][i].artist[0],
				album:albumsData[id][i].album[0],
				duration:albumsData[id][i].duration,
				cover:albumsData[id][i].cover,
				id:albumsData[id][i].id
			})
		}
	}
	player.changePlaylist(checkedSong);
	player.playlistAdvance(0);
}
function addAlbums(){
	checkedSong = [];
	for(var j = 0; j < checkedAlbums.length; j++){
		id = checkedAlbums[j];
		for(var i in albumsData[id]){
			player.pushToPlaylist({
				mp3:albumsData[id][i].mp3,
				title:albumsData[id][i].title[0],
				artist:albumsData[id][i].artist[0],
				album:albumsData[id][i].album[0],
				duration:albumsData[id][i].duration,
				cover:albumsData[id][i].cover,
				id:albumsData[id][i].id
			});
		}
	}
}


function addToPlaylist(songIds){
		$('#add-playlist-modal .body-error').hide();
		$('#add-playlist-modal .playlist-loader').hide();
		$('#add-playlist-modal .list-group').show();
		$('#add-playlist-modal').modal('show');
		$('.close-modal').off('click').on('click', function(){
			$('#add-playlist-modal').modal('hide');
		})
		

		$('#add-playlist-modal .confirm').off('click').on('click', function(){
			playlistId = $('#add-playlist-modal .list-group .active').data('id');
			$('#add-playlist-modal .playlist-loader').show();
			$('#add-playlist-modal .list-group').hide();
			$('#add-playlist-modal .body-error').hide();
			$.ajax({
				url: base_address + 'main/addToPlaylist/'+playlistId+'/'+songIds,
				success: function(data){
					if(data == '0'){
						$('#add-playlist-modal').modal('hide');
					} else if(data == '2'){
						$('#add-playlist-modal .body-error').show();
						$('#add-playlist-modal .playlist-loader').hide();
						$('#add-playlist-modal .list-group').show();
					} else {
						$('#add-playlist-modal').modal('hide');
						alert('An error occured ! Please try again.');
					}
				},
				error: function(){
					$('#add-playlist-modal').modal('hide');
					alert('An error occured ! Please try again.');
				}
			});
			return false;
		})
}

function confirmModal(title, string, clickFunction, data){
	$('#confirm-modal .confirm').removeAttr('disabled');
	$('#confirm-modal .playlist-loader').hide();
	$('#confirm-modal .body-text').html(string).show();
	$('#confirm-modal .modal-header h3').html(title);
	$('#confirm-modal').modal('show');

	$('#confirm-modal .close-modal').off('click').on('click', function(){
		$('#confirm-modal').modal('hide');
		return false;
	})
	$('#confirm-modal .confirm').off('click').on('click', function(){
		$(this).attr('disabled','disabled');
		$('#confirm-modal .playlist-loader').show();
		$('#confirm-modal .body-text').hide();
		clickFunction(data, function(){
			$('#confirm-modal').modal('hide');
		});

		return false;
	})
}


function getRadio(artistId, count){
	$.getJSON(base_address + 'main/getRadioJSON/' + artistId + '/' + count, [], function(data){
		checkedSong = [];
		for(var i = 0; i < data.length; i++){
			checkedSong.push({
				mp3:data[i].mp3,
				title:data[i].title[0],
				artist:data[i].artist[0],
				album:data[i].album[0],
				duration:data[i].duration,
				cover:data[i].cover,
				id:data[i].id
			});
		}
		player.changePlaylist(checkedSong);
		player.playlistAdvance(0);
	});
}

function saveQueue(){
	first = true;
	var ids = '';
	for(var i = 0; i < player.myPlaylist.length; i++){
		if(!first)
			ids += '-';
		first = false;
		if(player.myPlaylist[i].id[0] == undefined)
			ids += player.myPlaylist[i].id;
		else
			ids += player.myPlaylist[i].id[0];
	}
	newPlaylist(function(data, callback){
		$.ajax({
			url: base_address + 'main/newPlaylistWithSongs/'+$('#playlist-name').val()+'/'+data,
			success: function(data){
				if(data == 'ok'){
					callback();
					loadPage('main', 'playlists', '', false);
				} else {
					alert('An error occured ! Please try again.');
				}
			},
			error: function(){
				alert('An error occured ! Please try again.');
			}
		})
	}, ids)
}
function newPlaylist(saveFunction, data){
	$('#playlist-modal').modal('show');
	$('.close-modal').off('click').on('click', function(){
		$('#playlist-modal').modal('hide');
		return false;
	})
	$('#save-playlist').off('click').on('click', function(){
		$(this).attr('disabled','disabled');
		$('#playlist-modal .playlist-loader').show();
		$('#playlist-modal #new-playlist-form').hide();
		saveFunction(data, function(){
			$('#playlist-modal').modal('hide');
		});
		return false;
	})
	$('#playlist-modal').off('hidden.bs.modal').on('hidden.bs.modal', function(){
		$('#save-playlist').removeAttr('disabled');
		$('#playlist-modal .playlist-loader').hide();
		$('#playlist-modal #new-playlist-form').show();
		$('#playlist-name').val('');
	})
}

function share(songs){
	$('#share-modal .close-modal').click(function(){$('#share-modal').modal('hide'); return false;});
	$('#share-modal').off('hidden.bs.modal').on('hidden.bs.modal', function(){
		$('#share-modal .loader').hide();
		$('#share-form').hide();
		$('#guest-config-form, #get-link').show();
		$('.modal-footer .close-modal').removeClass('btn-primary');
	})
	$('#get-link').off('click').on('click', function(){
		$('#guest-config-form').hide();
		$('#share-modal .loader').show();
		server = $('#server').val();
		pseudo = $('#guest-pseudo').val();
		password = toHex($('#guest-password').val());
		$.ajax({
			dataType: "json",
			url: base_address + 'main/share',
			type: "POST",
			data: {songs : JSON.stringify(songs, null, 2), pseudo: pseudo, password: password, server: server},
			success: function(data){
				if(data.status == 'ok'){
					$('#share-link').val(data.link);
					$('#share-modal .loader, #get-link').hide();
					$('.modal-footer .close-modal').addClass('btn-primary');
					$('#share-form').show();
					$('#share-link').select();
				} else {
					alert('An error occured ! Please try again.');
				}
			},
			error: function(){
				alert('An error occured ! Please try again.');
			}
		});
		return false;
	})
	$('#share-modal').modal('show');
}


function toHex(str) {
	var hex = '';
	for(var i=0;i<str.length;i++) {
		hex += ''+str.charCodeAt(i).toString(16);
	}
	return hex;
}


/*
 * Initializations
 */
$(function(){
	// load default page
	hash = window.location.hash.substring(1);
	if(is_shared){
		$('#body-loader').hide();
		$('#body').show();
	} else if(hash != ''){
		parts = hash.split('-');
		switch(parts[0]){
			case 'artists':
				loadPage('main','artists', '', false);
				break;
			case 'artist':
				loadPage('main','artist', parts[1], false);
				break;
			case 'album':
				loadPage('main','album', parts[1], false);
				break;
			case 'albums':
				var albums = parts[1];
				for(var j = 2; j < parts.length; j++){
					albums += '-' + parts[j];
				}
				loadPage('main','albums', albums, false);
				break;
			case 'playlists':
				loadPage('main','playlists', '', false);
				break;
			case 'playlist':
				loadPage('main','playlist', parts[1], false);
				break;
			case 'search':
				if(parts[1] == undefined)
					parts[1] = '';
				loadPage('main','search', parts[1], false);
				break;
			case 'config':
				loadPage('main','config', '', false);
				break;
			case 'albumsType':
				if(parts[1] == undefined)
					parts[1] = '';
				loadPage('main','albumsType', parts[1], false);
				break
			default:
				break;
		}
	} else {
		loadPage('main','artists', '', false);
	}


	$('.navbar-brand, #artists-link, .menu-brand').click(function(){
		loadPage('main','artists', '', false);
		return false;
	})
	$('#playlist-link').click(function(){
		loadPage('main','playlists', '', false);
		return false;
	})
	$('#new-link').click(function(){
		loadPage('main','albumsType', 'newest', false);
		return false;
	})
	$('#search-link').click(function(){
		loadPage('main','search', '', false);
		return false;
	})
	$('#config-link').click(function(){
		loadPage('main','config', '', false);
		return false;
	})


	var album = [];
	if(is_shared){
		$(':checkbox').each(function(){
			album.push($(this).data('music'));
		});
	} else {
		// Restore the previous playlist
		oldList = $.jStorage.get('playqueue');
		if(oldList != null){
			album = oldList;
		}
	}


	// Create the player
	$('#player').ttwMusicPlayer(album, {
		autoPlay:false, 
		jPlayer:{
			swfPath:'assets/js/Jplayer.swf' //You need to override the default swf path any time the directory structure changes
		}
	});


	$('.small-player-hover').click(function(){
		if(isPlayerExpanded)
			smallPlayer();
		else
			bigPlayer();
	})

	$( window ).resize(function(){delay(function() {
		if($( window ).width() > 920){
			isPlayerExpanded = false;
			$('#player').removeClass('expanded');
		} else {
			isPlayerExpanded = false;
			smallPlayer()
		}
	}, 500)});

	$('#navbar-collapse a, .navbar-collapse-background').click(function(){
		$('#navbar-collapse').removeClass('in');
	})


})

isPlayerExpanded = false;
function bigPlayer(){
	$('#player').addClass('expanded');
	isPlayerExpanded = true;
}
function smallPlayer(){
	$('#player').removeClass('expanded');
	isPlayerExpanded = false;
}

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();


/*
 * Search function
 */
$(function(){
	$('#ajax-search-form').search({submit:function(plugin){
		loadPage('main', 'search', plugin.query, false);
		plugin.clear();
	}});
})

