function configPage(){
	$('#config-form').submit(function(){
		$('#info').removeClass('hidden');
		$('#error').addClass('hidden');
		server = $('#server').val();
		pseudo = $('#pseudo').val();
		password = toHex($('#password').val());
		remember = $('#remember').prop('checked');
		bitrate = $('#bitrate').val();

		$.ajax({
			url: base_address + 'main/tryConfig',
			type: "POST",
			data: {server : server, pseudo: pseudo, password: password, remember: remember, bitrate: bitrate},
		})
		.error(function(){
			$('#info').addClass('hidden');
			$('#error').html('Can\'t connect to serveur').removeClass('hidden');
		})
		.done(function( data ) {

			if(data != 'ok'){
				$('#info').addClass('hidden');
				$('#error').html(data).removeClass('hidden');
			} else if(data == ''){
				$('#info').addClass('hidden');
				$('#error').html('Can\'t connect to serveur').removeClass('hidden');
			} else {
				player.changePlaylist([]);
				loadPage('main', 'artists', '', false);
				return false;
			}

		});
		return false;
	});

        $.material.init();
}

