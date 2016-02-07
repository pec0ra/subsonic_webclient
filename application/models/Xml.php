<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// define('ADDRESS', 'http://pec0ra.ml:4040/rest/');
define('ADDRESS', 'https://music.pec0ra.ml/');

class Xml extends CI_Model {

        public function __construct()
        {
                parent::__construct();
		$this->load->library('session', 'user');
        }

	function loadXml($file_name, $options = Array (), $json = false, $path = false){
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);
		if(!$path)
			$path = $this->user->getAPIPath($file_name);
		foreach($options as $option){
			$path .= '&' . $option[0] . '=' . $option[1];
		}
		if($json){
			$path .= '&f=json';
		}
		$content = file_get_contents($path, FALSE, $ctx);
		if ( empty($content) ) {
			die('XML is empty');
		}
		if($json){
			return $content;
		}
		if($xml = simplexml_load_string($content)){
			if($xml->attributes()->status != 'ok'){
				return false;
			}
			return $xml;
		} else {
			return false;
		}
	}

	function ping($pseudo, $password, $server){
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);
		$path = $server . 'rest/ping.view?v=1.12.0&c=webclient&u=' . $pseudo . '&p=enc:' . $password;
		$content = file_get_contents($path, FALSE, $ctx);
		if ( empty($content) ) {
			return false;
		}
		if($xml = simplexml_load_string($content)){
			return $xml;
		} else {
			return false;
		}
	}

	function getArtists(){
		session_write_close();
		return $this->loadXml('getArtists')->artists;
	}

	function getArtist($id){
		session_write_close();
		return $this->loadXml('getArtist',array(array('id', $id)))->artist;
	}

	function getArtistInfo($id){
		session_write_close();
		return $this->loadXml('getArtistInfo2',array(array('id', $id)))->artistInfo2;
	}

	function getPlaylists(){
		session_write_close();
		return $this->loadXml('getPlaylists')->playlists;
	}

	function getPlaylist($id){
		session_write_close();
		return $this->loadXml('getPlaylist',array(array('id', $id)))->playlist;
	}

	function getAlbumsJSON($id){
		session_write_close();
		$artist = $this->loadXml('getArtist',array(array('id', $id)))->artist;
		$albums = array();
		foreach($artist->album as $album){
			$albums['' . $album->attributes()->id] = Array();
			$songs = $this->getAlbum($album->attributes()->id);
			foreach($songs->song as $song){

				$duration = floor($song->attributes()->duration / 60);
				$min = $song->attributes()->duration % 60;
				if($min < 10)
					$min = '0' . $min;
				$duration = $duration . ':' . $min;

				$songData = array();
				$songData['mp3'] = site_url('main/stream/' . $song->attributes()->id);
				$songData['title'] = $song->attributes()->title[0];
				$songData['artist'] = $song->attributes()->artist;
				$songData['album'] = $album->attributes()->name;
				$songData['duration'] = $duration;
				$songData['cover'] = site_url('main/getCover/' . $song->attributes()->coverArt) . '/200';
				$songData['id'] = $song->attributes()->id;
				$albums['' . $album->attributes()->id]['' . $song->attributes()->id] = $songData;
			}
		}
		return json_encode($albums);
	}
	function getRadioJSON($artistId, $count){
		session_write_close();
		$xml = $this->loadXml('getSimilarSongs2',array(array('id', $artistId), array('count', $count)))->similarSongs2;
		$songs = array();
		foreach($xml->song as $song){

			$duration = floor($song->attributes()->duration / 60);
			$min = $song->attributes()->duration % 60;
			if($min < 10)
				$min = '0' . $min;
			$duration = $duration . ':' . $min;

			$songData = array();
			$songData['mp3'] = site_url('main/stream/' . $song->attributes()->id);
			$songData['title'] = $song->attributes()->title[0];
			$songData['artist'] = $song->attributes()->artist;
			$songData['album'] = $song->attributes()->album;
			$songData['duration'] = $duration;
			$songData['cover'] = site_url('main/getCover/' . $song->attributes()->coverArt) . '/200';
			$songData['id'] = $song->attributes()->id;
			$songs[] = $songData;
		}
		return json_encode($songs);
	}

	function getAlbum($id){
		session_write_close();
		return $this->loadXml('getAlbum',array(array('id', $id)))->album;
	}

	function getAlbums($type){
		session_write_close();
		return $this->loadXml('getAlbumList2', array(array('type', $type), array('size', 40)))->albumList2->album;
	}

	function newPlaylist($name){
		session_write_close();
		return $this->loadXml('createPlaylist', array(array('name', $name)));
	}
	function deletePlaylist($id){
		session_write_close();
		return $this->loadXml('deletePlaylist', array(array('id', $id)));
	}
	function newPlaylistWithSongs($name, $idsArray){
		session_write_close();
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);
		$path = $this->user->getAPIPath('createPlaylist') . '&name=' . $name;
		foreach($idsArray as $id){
			$path .= '&songId=' . $id;
		}
		$content = file_get_contents($path, FALSE, $ctx);
		if ( empty($content) ) {
			die('XML is empty');
		}
		if($xml = simplexml_load_string($content)){
			return $xml->attributes()->status;
		}
		return 'failed';
	}
	function addToPlaylist($playlistId, $idsArray){
		session_write_close();
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);
		$path = $this->user->getAPIPath('updatePlaylist') . '&playlistId=' . $playlistId;
		foreach($idsArray as $id){
			$path .= '&songIdToAdd=' . $id;
		}
		$content = file_get_contents($path, FALSE, $ctx);
		if ( empty($content) ) {
			die('XML is empty');
		}
		if($xml = simplexml_load_string($content)){
			if($xml->attributes()->status != 'ok'){
				if($xml->error->attributes()->code == 50)
					return 2;
			}
			return 0;
		} else {
			return 1;
		}
		return 0;
	}
	function removeFromPlaylist($playlistId, $idsArray){
		session_write_close();
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);
		$path = $this->user->getAPIPath('updatePlaylist') . '&playlistId=' . $playlistId;
		foreach($idsArray as $id){
			$path .= '&songIndexToRemove=' . $id;
		}
		$content = file_get_contents($path, FALSE, $ctx);
		if ( empty($content) ) {
			die('XML is empty');
		}
		if($xml = simplexml_load_string($content)){
			if($xml->attributes()->status != 'ok'){
				if($xml->error->attributes()->code == 50)
					return 2;
			}
			return 0;
		} else {
			return 1;
		}
		return 0;
	}

	function search($query){
		session_write_close();
		return $this->loadXml('search3', array(array('query', $query)))->searchResult3;
	}

	function getMusicStreamAddress($id, $bitrate){
		session_write_close();
		return $this->user->getAPIPath('stream') . '&id=' . $id . '&maxBitRate=' . $bitrate;
	}
	function getCover($id, $size){
		session_write_close();
		return $this->user->getAPIPath('getCoverArt') . '&id=' . $id . '&size=' . $size;
	}

	function createShare($pseudo, $password, $server, $songs){
		$hash = substr(md5(time() + $pseudo), 0, 10);
		$parsedSongs = preg_replace('#main/stream#', 'main/guestStream', $songs);

		$data = array('hash' => $hash, 'pseudo' => $pseudo, 'password' => $password, 'server' => $server, 'songs' => $parsedSongs);
		$str = $this->db->insert_string('share', $data);
		if($this->db->query($str)){
			return $hash;
		} else {
			return false;
		}
	}

	function getShare($hash){
		$sql = "SELECT * FROM share WHERE hash=" . $this->db->escape($hash) . "";
		$result = $this->db->query($sql)->result();
		if(count($result) > 0)
			return $result[0];
		else
			return null;
	}

	function checkSharedId($hash, $id){
		$share = $this->getShare($hash);
		$songArray = json_decode($share->songs);
		foreach($songArray as $song){
			if($song->id == $id)
				return true;
		}
		return false;
	}

	function scrobble($id, $submission){
		return $this->loadXml('scrobble', array(array('id', $id), array('time', time() * 1000), array('submission', $submission)))->attributes()->status;
	}


}
