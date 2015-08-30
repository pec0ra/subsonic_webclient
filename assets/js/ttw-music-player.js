/**
 * Created by 23rd and Walnut for Codebasehero.com
 * www.23andwalnut.com
 * www.codebasehero.com
 * User: Saleem El-Amin
 * Date: 6/11/11
 * Time: 6:41 AM
 *
 * Version: 1.01
 * License: MIT License
 */
player = null;

(function($) {
    $.fn.ttwMusicPlayer = function(playlist, userOptions) {
        var $self = this, defaultOptions, options, cssSelector, appMgr, playlistMgr, interfaceMgr, ratingsMgr, playlist,
                layout, ratings, myPlaylist, current;

        cssSelector = {
            jPlayer: "#jquery_jplayer",
            jPlayerInterface: '.jp-interface',
            playerPrevious: ".jp-interface .jp-previous",
            playerNext: ".jp-interface .jp-next",
            playerPlay: ".jp-interface .btn-play",
            shuffle: "jp-interface .jp-shuffle",
            repeat: "jp-interface .jp-repeat",
            trackList:'.tracklist',
            tracks:'.tracks',
            track:'.track',
            trackRating:'.rating-bar',
            rating:'.rating',
            ratingLevel:'.rating-level',
            ratingLevelOn:'.on',
            title: '.title',
            trackTitle: '.track-title',
            trackArtist: '.track-artist',
            duration: '.duration',
            buy:'.buy',
            buyNotActive:'.not-active',
            playing:'.playing',
            moreButton:'.more',
            player:'.player',
            artist:'.artist',
            album:'.album',
            artistOuter:'.artist-outer',
            albumCover:'.img',
            description:'.description',
            descriptionShowing:'.showing',
            timeProgress: 'time-progress'
        };

        defaultOptions = {
            ratingCallback:null,
            currencySymbol:'$',
            buyText:'BUY',
            tracksToShow:10,
            autoPlay:false,
            jPlayer:{}
        };

        options = $.extend(true, {}, defaultOptions, userOptions);

        myPlaylist = playlist;

        current = 0;

        appMgr = function() {
            playlist = new playlistMgr();
            layout = new interfaceMgr();

            layout.buildInterface();
            playlist.init(options.jPlayer);

            //don't initialize the ratings until the playlist has been built, which wont happen until after the jPlayer ready event
            $self.bind('mbPlaylistLoaded', function() {
                $self.bind('mbInterfaceBuilt', function() {
                    ratings = new ratingsMgr();
                });
                layout.init();

            });

	    $('.ttw-music-player .shuffle').click(function(){
		    player.toggleShuffle();
	    })

	    $('.ttw-music-player .clear-queue').click(function(){
		    player.changePlaylist([]);
	    })
	    $('.ttw-music-player .save-queue').click(function(){
		    saveQueue();
	    })
        };

        playlistMgr = function() {

            var playing = false, markup, $myJplayer = {},$tracks,showHeight = 0,remainingHeight = 0,$tracksWrapper, $more;

            markup = {
                listItem:'<tr class="track">' +
                            '<td><span class="playing-icon"></span><span class="track-title"></span></td>' +
                            '<td class="track-artist"></td>' +
                            '<td class="duration"></td>' +
                        '</tr>',
                ratingBar:'<span class="rating-level rating-bar"></span>'
            };

            function init(playlistOptions) {

                $myJplayer = $('.ttw-music-player .jPlayer-container');


                var jPlayerDefaults, jPlayerOptions;

                jPlayerDefaults = {
                    swfPath: "jquery-jplayer",
                    supplied: "mp3, oga",
                    cssSelectorAncestor:  cssSelector.jPlayerInterface,
                    errorAlerts: false,
                    warningAlerts: false
                };

                //apply any user defined jPlayer options
                jPlayerOptions = $.extend(true, {}, jPlayerDefaults, playlistOptions);

                $myJplayer.bind($.jPlayer.event.ready, function() {

                    //Bind jPlayer events. Do not want to pass in options object to prevent them from being overridden by the user
                    $myJplayer.bind($.jPlayer.event.ended, function(event) {
			if(player.repeat){
				player.playlistAdvance(current);
			} else {
				player.playlistNext();
			}
                    });

                    $myJplayer.bind($.jPlayer.event.play, function(event) {
                        $myJplayer.jPlayer("pauseOthers");
                        $tracks.eq(current).addClass(attr(cssSelector.playing)).siblings().removeClass(attr(cssSelector.playing));
                    });

                    $myJplayer.bind($.jPlayer.event.playing, function(event) {
			player.playing = true;
                        playing = true;
                    });

                    $myJplayer.bind($.jPlayer.event.pause, function(event) {
			player.playing = false;
                        playing = false;
                    });

                    $myJplayer.bind($.jPlayer.event.repeat, function(event) {
			    if(player.repeat){
				    player.repeat = false;
			    } else {
				    player.repeat = true;
			    }
                    });
		    $myJplayer.bind($.jPlayer.event.timeupdate, function(event) {
			    var time = Math.floor(event.jPlayer.status.currentTime);
			    var min = Math.floor(time / 60);
			    var sec = Math.floor(time - min*60);
			    if(sec < 10)
				    sec = '0'+sec;
			    $('.ttw-music-player .time-progress').html(min + ':' + sec);
		    });

                    //Bind next/prev click events
                    $(cssSelector.playerPrevious).click(function() {
                        player.playlistPrev();
                        $(this).blur();
                        return false;
                    });

                    $(cssSelector.playerNext).click(function() {
                        player.playlistNext();
                        $(this).blur();
                        return false;
                    });

		    $(cssSelector.playerPlay).click(function(){
			    if(player.playing){
				    // We pause the player
				    player.pause();
			    } else {
				    // We play the player
				    player.play();
			    }
		    })

                    $self.bind('mbInitPlaylistAdvance', function(e) {
                        var changeTo = this.getData('mbInitPlaylistAdvance');

                        if (changeTo != current) {
                            current = changeTo;
                            player.playlistAdvance(current);
                        }
                        else {
                            if (!$myJplayer.data('jPlayer').status.srcSet) {
                                player.playlistAdvance(0);
                            }
                            else {
                                togglePlay();
                            }
                        }
                    });

                    player.buildPlaylist();
                    //If the user doesn't want to wait for widget loads, start playlist now
                    $self.trigger('mbPlaylistLoaded');

                    player.playlistInit(false);
                });

                //Initialize jPlayer
                $myJplayer.jPlayer(jPlayerOptions);
            }


	    AudioPlayer = function(container, options, cssSelector){
		    this.container = container;
		    this.options = options;
		    this.cssSelector = cssSelector;
		    this.isConfig = false;
		    this.playing = false;
		    this.shuffleList;
		    this.notification = null
	    }
	    AudioPlayer.prototype = {
		    changePlaylist: function(playlist) {
			    player.myPlaylist = $.extend(true, [], playlist);
			    player.buildPlaylist();
			    $.jStorage.set('playqueue', playlist);
		    },
		    pushToPlaylist: function(song) {
			    player.myPlaylist.push(song);
			    var $ratings = $();

			    $tracksWrapper = $self.find(cssSelector.tracks);

			    //set up the html for the track ratings
			    for (var i = 0; i < 10; i++)
				    $ratings = $ratings.add(markup.ratingBar);

			    var $track = $(markup.listItem);

			    $track.find(cssSelector.trackTitle).html(trackName(player.myPlaylist.length - 1));

			    $track.find(cssSelector.trackArtist).html(this.getArtist(player.myPlaylist.length - 1));

			    $track.find(cssSelector.duration).html(this.duration(player.myPlaylist.length - 1));

			    $track.data('index', player.myPlaylist.length - 1);

			    $tracksWrapper.append($track);

			    $tracks = $(cssSelector.track);

			    $tracks.click(function() {
				    player.playlistAdvance($(this).data('index'));
			    });
// 			    if(!this.isConfig){
// 				    this.playlistInit();
// 			    }

			    $.jStorage.set('playqueue', player.myPlaylist);
		    },
		    playlistInit: function(autoplay) {
			    current = 0;

			    if (autoplay) {
				    player.playlistAdvance(current);
			    }
			    else {
				    this.playlistConfig(current);
				    $self.trigger('mbPlaylistInit');
			    }
		    },

		    playlistConfig: function(index) {
			    current = index;
			    if(!isUndefined(player.myPlaylist[current])){
				    this.isConfig = true;
			    } else {
				    this.isCOnfig = false;
				    $('.time-progress, .total-time').html('00:00');
			    }
			    $myJplayer.jPlayer("setMedia", player.myPlaylist[current]);

		    },

		    playlistAdvance: function(index) {
			    this.playlistConfig(index);


			    $self.trigger('mbPlaylistAdvance');
			    if(player.myPlaylist.length != 0)
				    player.play();
		    },

		    playlistNext: function() {
			    if(player.shuffle){
				    index = player.shuffleList[(player.shuffleList.indexOf(current) + 1) % player.myPlaylist.length];
			    } else {
				    var index = (current + 1 < player.myPlaylist.length) ? current + 1 : 0;
			    }
			    if (("Notification" in window)){
				    Notification.requestPermission(function() {
					    title = player.myPlaylist[index].title;
					    options = {
						    body: player.myPlaylist[index].artist,
					    };
					    player.notification = new Notification(title, options);
				    });
			    }
			    player.playlistAdvance(index);
		    },

		    playlistPrev: function() {
			    if(player.shuffle){
				    index = player.shuffleList[(player.shuffleList.indexOf(current) - 1) % player.myPlaylist.length];
			    } else {
				    var index = (current - 1 >= 0) ? current - 1 : player.myPlaylist.length - 1;
			    }
			    if (("Notification" in window)){
				    Notification.requestPermission(function() {
					    title = player.myPlaylist[index].title;
					    options = {
						    body: player.myPlaylist[index].artist,
					    };
					    var notification = new Notification(title, options);
				    });
			    }
			    player.playlistAdvance(index);
		    },

		    togglePlay: function() {
			    if (!playing)
				    player.play();
			    else
				    player.pause();
		    },
		    play: function() {
			    $(cssSelector.playerPlay).children('.material-icon').removeClass('play').addClass('pause');
			    $myJplayer.jPlayer("play");
		    },
		    pause: function() {
			    $(cssSelector.playerPlay).children('.material-icon').removeClass('pause').addClass('play');
			    $myJplayer.jPlayer("pause");
		    },
		    stop: function(){
			    $(cssSelector.playerPlay).children('.material-icon').removeClass('pause').addClass('play');
			    $myJplayer.jPlayer("stop");
		    },
	            toggleShuffle: function(){
			    player.shuffleList = []
			    if(player.shuffle){
				    player.shuffle = false;
				    $('.ttw-music-player .shuffle').css('color', 'rgba(255, 255, 255, 0.90)');
			    } else {
				    player.shuffle = true;
				    for(var i = 0; i < player.myPlaylist.length; i++){
					    var newIndex = Math.floor(Math.random() * (player.shuffleList.length + 1));
					    player.shuffleList.splice(newIndex, 0, i);
				    }
				    $('.ttw-music-player .shuffle').css('color', '#009688');
			    }
		    },

		    buildPlaylist: function() {
			    var $ratings = $();

			    if(player.myPlaylist == undefined){
				    player.myPlaylist = myPlaylist;
			    }

			    $tracksWrapper = $self.find(cssSelector.tracks);
			    $tracksWrapper.empty();

			    //set up the html for the track ratings
			    for (var i = 0; i < 10; i++)
				    $ratings = $ratings.add(markup.ratingBar);

			    for (var j = 0; j < player.myPlaylist.length; j++) {
				    var $track = $(markup.listItem);

				    $track.find(cssSelector.trackTitle).html(trackName(j));
				    $track.find(cssSelector.trackArtist).html(this.getArtist(j));

				    $track.find(cssSelector.duration).html(this.duration(j));

				    $track.data('index', j);

				    $tracksWrapper.append($track);
			    }

			    $tracks = $(cssSelector.track);

			    $tracks.click(function() {
				    player.playlistAdvance($(this).data('index'));
			    });
			    this.isConfig = false;
		    },

		    duration: function(index) {
			    return !isUndefined(player.myPlaylist[index].duration) ? player.myPlaylist[index].duration : '-';
		    },

		    getArtist: function(index) {
			    return !isUndefined(player.myPlaylist[index].artist) ? player.myPlaylist[index].artist : '-';
		    },

		    setBuyLink: function($track, index) {
			    if (!isUndefined(player.myPlaylist[index].buy)) {
				    $track.find(cssSelector.buy).removeClass(attr(cssSelector.buyNotActive)).attr('href', player.myPlaylist[index].buy).html(buyText(index));
			    }
		    },

		    buyText: function(index) {
			    return (!isUndefined(player.myPlaylist[index].price) ? options.currencySymbol + player.myPlaylist[index].price : '') + ' ' + options.buyText;
		    },
	    }

	    player = new AudioPlayer($(this), options);
	    player.shuffle = false;
	    player.repeat = false;

            return{
                init:init,
                playlistInit:player.playlistInit,
                playlistAdvance:player.playlistAdvance,
                playlistNext:player.playlistNext,
                playlistPrev:player.playlistPrev,
                togglePlay:player.togglePlay,
                $myJplayer:$myJplayer,
		playlistMgr:playlistMgr
            };

        };

        ratingsMgr = function() {

            var $tracks = $self.find(cssSelector.track);

            function bindEvents() {

                //Handler for when user hovers over a rating
                $(cssSelector.rating).find(cssSelector.ratingLevel).hover(function() {
                    $(this).addClass('hover').prevAll().addClass('hover').end().nextAll().removeClass('hover');
                });

                //Restores previous rating when user is finished hovering (assuming there is no new rating)
                $(cssSelector.rating).mouseleave(function() {
                    $(this).find(cssSelector.ratingLevel).removeClass('hover');
                });

                //Set the new rating when the user clicks
                $(cssSelector.ratingLevel).click(function() {
                    var $this = $(this), rating = $this.parent().children().index($this) + 1, index;

                    if ($this.hasClass(attr(cssSelector.trackRating))) {
                        rating = rating / 2;
                        index = $this.parents('li').data('index');

                        if (index == current)
                            applyCurrentlyPlayingRating(rating);
                    }
                    else {
                        index = current;
                        applyTrackRating($tracks.eq(index), rating);
                    }


                    $this.prevAll().add($this).addClass(attr(cssSelector.ratingLevelOn)).end().end().nextAll().removeClass(attr(cssSelector.ratingLevelOn));

                    processRating(index, rating);
                });
            }

            function processRating(index, rating) {
                player.myPlaylist[index].rating = rating;
                runCallback(options.ratingCallback, index, player.myPlaylist[index], rating);
            }

            bindEvents();
        };

        interfaceMgr = function() {

            var $player, $title, $artist, $album, $albumCover;


            function init() {
                $player = $(cssSelector.player),
                        $title = $player.find(cssSelector.title),
                        $artist = $player.find(cssSelector.artist),
                        $album = $player.find(cssSelector.album),
                        $albumCover = $player.find(cssSelector.albumCover);

                setDescription();

                $self.bind('mbPlaylistAdvance mbPlaylistInit', function() {
                    setTitle();
                    setArtist();
                    setCover();
		    setTime();
                });
            }

            function buildInterface() {
                var markup, $interface;

                //I would normally use the templating plugin for something like this, but I wanted to keep this plugin's footprint as small as possible
                markup = '<div class="ttw-music-player">' +
                        '<div class="player jp-interface">' +
			'	<div class="small-player-hover"></div>' +
			'	<div class="track-info">' +
                        '		<p class="title"></p>' +
                        '       	<div class="info-outer">' +
			'			<span class="artist"></span> - ' +
			'			<span class="album"></span>' +
			'	     	</div>' +
			'	</div>' +
			'	<div class="album-controls-block">' +
                        '		<div class="album-cover">' +
                        '			<span class="img shadow-z-1"></span>' +
                        '       	 </div>' +
			'	 </div>' +
			'	 ' +
			'	 ' +
                        '        <div class="controls-block">' +
                        '            <div class="controls">' +
			'		 <span class="control-icon jp-shuffle shuffle"><i class="mdi-av-shuffle"></i></span>' +
			'		 <span class="control-icon jp-repeat repeat"><i class="mdi-av-repeat"></i></span>' +
			'		 <span class="control-icon jp-repeat-off repeat-off"><i class="mdi-av-repeat"></i></span>' +
			'		 <span class="control-icon clear-queue"><i class="mdi-content-clear"></i></span>' +
			'		 <span class="control-icon save-queue"><i class="mdi-av-playlist-add"></i></span>' +
                        '<!-- These controls aren\'t used by this plugin, but jPlayer seems to require that they exist -->' +
                        '                <span class="unused-controls">' +
                        '                    <span class="jp-video-play"></span>' +
                        '                    <span class="jp-stop"></span>' +
                        '                    <span class="jp-mute"></span>' +
                        '                    <span class="jp-unmute"></span>' +
                        '                    <span class="jp-volume-bar"></span>' +
                        '                    <span class="jp-volume-bar-value"></span>' +
                        '                    <span class="jp-volume-max"></span>' +
                        '                    <span class="jp-current-time"></span>' +
                        '                    <span class="jp-duration"></span>' +
                        '                    <span class="jp-full-screen"></span>' +
                        '                    <span class="jp-restore-screen"></span>' +
                        '                    <span class="jp-gui"></span>' +
			'		     <span class="jp-play"></span>' +
			'		     <span class="jp-pause"></span>' +
                        '                </span>' +
                        '            </div>' +
			'		 <div class="main-controls">' +
			'			 <button class="previous jp-previous btn btn-fab btn-fab-mini btn-raised btn-material-teal-50"><i class="mdi-av-skip-previous"></i></button>'+
			'			 <button class="btn btn-fab btn-raised btn-primary btn-play"><div class="material-icon play"><span class="first"></span><span class="second"></span></span><span class="third"></span></div></button>'+
			'			 <button class="next jp-next btn btn-fab btn-fab-mini btn-raised btn-material-teal-50"><i class="mdi-av-skip-next"></i></button>'+
			'		 </div>' +
                        '        </div>' +
			'	 <div class="progress-block">' +
                        '                	<div class="time-progress text-muted">00:00</div>' +
                        '            	<div class="progress-wrapper">' +
			'			<div class="progress jp-seek-bar">' +
                        '                    		<div class="progress-bar elapsed jp-play-bar"></div>' +
                        '               	</div>' +
                        '               </div>' +
                        '                	<div class="total-time text-muted">00:00</div>' +
                        '    	</div>' +
                        '    </div>' +
                        '    <div class="tracklist" id="tracklist">' +
                        '        <table class="table tracks"></table>' +
                        '    </div>' +
                        '    <div class="jPlayer-container"></div>' +
                        '</div>';

                $interface = $(markup).appendTo($self) 
                $self.trigger('mbInterfaceBuilt');
            }

            function setTitle() {
                $title.html(trackName(current));
            }

            function setArtist() {
                if (isUndefined(player.myPlaylist[current]) || isUndefined(player.myPlaylist[current].artist))
                    $artist.animate({opacity:0}, 'fast');
                else {
                    $artist.html(player.myPlaylist[current].artist).animate({opacity:1}, 'fast');
                }
                if (isUndefined(player.myPlaylist[current]) || isUndefined(player.myPlaylist[current].album))
                    $album.animate({opacity:0}, 'fast');
                else {
                    $album.html(player.myPlaylist[current].album).animate({opacity:1}, 'fast');
                }
            }

            function setCover() {
                $albumCover.animate({opacity:0}, 'fast', function() {
                    if (!isUndefined(player.myPlaylist[current]) && !isUndefined(player.myPlaylist[current].cover)) {
                        var now = current;
                        $('<img src="' + player.myPlaylist[current].cover + '" alt="album cover" />', this).imagesLoaded(function(){
                            if(now == current)
                                $albumCover.html($(this)).animate({opacity:1})
                        });
                    }
                });
            }

	    function setTime(){
                    if (!isUndefined(player.myPlaylist[current]) && !isUndefined(player.myPlaylist[current].duration)) {
			    $('.ttw-music-player .total-time').html(player.myPlaylist[current].duration);
		    }
	    }

            function setDescription() {
                if (!isUndefined(options.description))
                    $self.find(cssSelector.description).html(options.description).addClass(attr(cssSelector.descriptionShowing)).slideDown();
            }

            return{
                buildInterface:buildInterface,
                init:init
            }

        };

        /** Common Functions **/
        function trackName(index) {
            if (!isUndefined(player.myPlaylist[index]) && !isUndefined(player.myPlaylist[index].title))
                return player.myPlaylist[index].title;
            else if (!isUndefined(player.myPlaylist[index]) && !isUndefined(player.myPlaylist[index].mp3))
                return fileName(player.myPlaylist[index].mp3);
            else if (!isUndefined(player.myPlaylist[index]) && !isUndefined(player.myPlaylist[index].oga))
                return fileName(player.myPlaylist[index].oga);
            else return '';
        }

        function fileName(path) {
            path = path.split('/');
            return path[path.length - 1];
        }


        /** Utility Functions **/
        function attr(selector) {
            return selector.substr(1);
        }

        function runCallback(callback) {
            var functionArgs = Array.prototype.slice.call(arguments, 1);

            if ($.isFunction(callback)) {
                callback.apply(this, functionArgs);
            }
        }

        function isUndefined(value) {
            return typeof value == 'undefined';
        }

        appMgr();
    };
})(jQuery);

(function($) {
// $('img.photo',this).imagesLoaded(myFunction)
// execute a callback when all images have loaded.
// needed because .load() doesn't work on cached images

// mit license. paul irish. 2010.
// webkit fix from Oren Solomianik. thx!

// callback function is passed the last image to load
//   as an argument, and the collection as `this`


    $.fn.imagesLoaded = function(callback) {
        var elems = this.filter('img'),
                len = elems.length;

        elems.bind('load',
                function() {
                    if (--len <= 0) {
                        callback.call(elems, this);
                    }
                }).each(function() {
            // cached images don't fire load sometimes, so we reset src.
            if (this.complete || this.complete === undefined) {
                var src = this.src;
                // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
                // data uri bypasses webkit log warning (thx doug jones)
                this.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                this.src = src;
            }
        });

        return this;
    };
})(jQuery);
