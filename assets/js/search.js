(function($) {

	$.fn.search = function(userOptions) {
		var xhr = null;
		var timeout = null;
		var searchString = '';
		var selection = undefined;

		defaultOptions = {
			results: $('<div class="list-group search-ajax-results well" id="search-ajax-results"></div>').appendTo('body'),
			submit: function(){
				return false;
			},
			completeReturn: false
		}
		var options = $.extend(true, {}, defaultOptions, userOptions);

		var $loader = $('<div class="list-group-item text-center spinner"><svg class="circular"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"/></svg></div>')
		var $results = options.results;
		var $input = $(this).find('.search-input');
		var $submit = $(this).find('.submit');


		// Initialization
		$input.keydown(function(e){
			if(e.which == 40){
				var selectedItem = $results.find('.selected');
				if(!selectedItem.hasClass('list-title')){
					selectedItem.removeClass('active');
				}
				selectedItem.removeClass('selected');
				while(selectedItem.next().hasClass('list-title')){
					selectedItem = selectedItem.next();
				}
				if(selectedItem.is(':last-child')){
					selectedItem = $results.find('.list-group-item').first();
				}
				selectedItem.next().addClass('active selected')
				selection = selectedItem.next();
			} else if(e.which == 38){
				var selectedItem = $results.find('.selected');
				if(selectedItem.is(':first-child'))
					return;
				if(!selectedItem.hasClass('list-title')){
					selectedItem.removeClass('active');
				}
				selectedItem.removeClass('selected');
				while(selectedItem.prev().hasClass('list-title')){
					if(selectedItem.prev().is(':first-child')){
						selection = undefined;
						selectedItem.prev().addClass('selected');
						return;
					}
					selectedItem = selectedItem.prev();
				}
				selectedItem.prev().addClass('active selected');
				selection = selectedItem.prev();
			} else if(e.which == 13){
				if(selection != undefined){
					selection.click();
					e.preventDefault();
				}
			} else {
				searchString = $input.val();
				if(searchString.length > 1){
					if(timeout == null){
						timeout = setTimeout(function(){
							timeout = null;
							sendSearch();
						}, 400);
					}
				} else {
					window.clearTimeout(timeout);
					timeout = null;
					$results.empty();
					$results.hide();
				}
			}
		});
		$(this).submit(function(){
			searchString = $input.val();
			options.submit({
				query:searchString,
				sendSearch:sendSearch,
				clear:clear
			});
			return false;
		})




		var sendSearch = function(){
			$results.show();
			$results.html($loader);
			selection = undefined;
			if(xhr != null)
				xhr.abort();

			searchURL = base_address + 'main/searchAJAX/' + searchString;
			if(options.completeReturn)
				searchURL += '/true';

			xhr = $.get(searchURL,{},
					function(data) {
						$results.show();
						$results.html(data);
						$results.find('.list-group-item').first().addClass('selected');

						$('.artist-item').click(function(){
							loadPage('main', 'artist', $(this).data('artist-id'), false);
							clear();
							return false
						})

						$('.album-item').click(function(){
							loadPage('main', 'album', $(this).data('album-id'), false);
							clear();
							return false
						})
						$('.song-item').click(function(){
							// Clean the search field
							clear();

							// Load the page of the album
							loadPage('main', 'album', $(this).data('album-id'), false);

							// Play the song
							player.changePlaylist([$(this).data('music')]);
							player.playlistAdvance(0);
							return false
						})
						xhr = null;
					});
		}

		var clear = function(){
			$input.val('');
			$results.empty();
			$results.hide();
			window.clearTimeout(timeout);
			timeout = null;
			if(xhr != null)
				xhr.abort();
		}

	}
})(jQuery);
