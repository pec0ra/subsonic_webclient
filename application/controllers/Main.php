<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
        {
                parent::__construct();
		$this->load->helper('url', 'cookie');
		$this->load->model('xml');
		$this->load->library('user');
		if(!$this->user->isConnectionValid()){
			redirect('main/config');
		}
        }
	public function config(){
		$this->load->view('config.php');
	}
	public function tryConfig(){
		$server = $this->input->post('server');
		if(!(substr($server, -1) == '/')){
			$server = $server . '/';
		}
		$pseudo =  $this->input->post('pseudo');
		$password =  $this->input->post('password');
		$bitrate =  $this->input->post('bitrate');
		$remember = $this->input->post('remember');

		if(!$this->user->testAndSetConfig($pseudo, $password, $server, $bitrate, $remember))
			echo $this->user->getError();
		else
			echo 'ok';
	}


        public function index()
        {
		$this->load->view('main.php');
	}

	public function artists(){
		$data['artists'] = $this->xml->getArtists();
		$this->load->view('artists.php', $data);
	}

	public function playlists(){
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$this->load->view('playlists.php', $data);
	}

	public function artist($id)
	{
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$data['artist'] = $this->xml->getArtistInfo($id);
		$artist = $this->xml->getArtist($id);

		// We sort the artists by year
		$albums = array();
		foreach($artist->album as $album){
			$albums[] = $album;
		}
		usort($albums, function($a, $b){
			return $a->attributes()->year + 0 < $b->attributes()->year;
		});
		$data['albums'] = $albums;
		$data['title'] = $artist->attributes()->name;
		$this->load->view('artist.php', $data);
	}
	public function getAlbumsJSON($id){
		$data['json'] = $this->xml->getAlbumsJSON($id);
		$this->load->view('json.php', $data);
	}

	public function album($id)
	{
		$album = $this->xml->getAlbum($id);
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$data['songs'] = $album->song;
		$data['title'] = $album->attributes()->name . ' - ' . $album->attributes()->year;
		$this->load->view('album.php', $data);
	}

	public function playlist($id)
	{
		$playlist = $this->xml->getPlaylist($id);
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$data['songs'] = $playlist->entry;
		$data['title'] = $playlist->attributes()->name;
		$data['is_playlist'] = true;
		$this->load->view('album.php', $data);
	}

	public function albums($ids_string)
	{
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$ids = preg_split("/-/", $ids_string);
		$data['songs'] = array();
		foreach($ids as $id){
			$songs = $this->xml->getAlbum($id);
			foreach($songs->song as $song){
				array_push($data['songs'], $song);
			}
		}
		$data['title'] = $song->attributes()->artist;
		$this->load->view('album.php', $data);
	}

	public function search($query = ''){
		$data['query'] = $query;
		$this->load->view('search.php', $data);
	}
	public function searchAJAX($query, $complete = false){
		if($query == 'true' && !$complete)
			$query = '';
		$query = str_replace('%27', '%20', $query);
		$data['search'] = $this->xml->search($query);
		if($complete)
			$data['complete'] = true;
		else
			$data['complete'] = false;

		$this->load->view('searchAJAX.php', $data);
	}

	public function albumsType($type = 'newest'){
		switch($type){
		case 'frequent':
			$data['title'] = 'Frequent albums';
			break;
		case 'recent':
			$data['title'] = 'Recent albums';
			break;
		default:
			$data['title'] = 'Recently added albums';
			$type = 'newest';
			break;

		}
		$data['playlists'] = $this->xml->getPlaylists()->playlist;
		$data['artist'] = false;
		$data['albums'] = $this->xml->getAlbums($type);
		$this->load->view('albumType.php', $data);
	}

	public function newPlaylist($name){
		$return = $this->xml->newPlaylist($name);
		echo $return->attributes()->status;
	}
	public function newPlaylistWithSongs($name, $songIds = ''){
		$idsArray = preg_split("/-/", $songIds);
		echo $this->xml->newPlaylistWithSongs($name, $idsArray);
	}

	public function deletePlaylist($id){
		$return = $this->xml->deletePlaylist($id);
		echo $return->attributes()->status;
	}

	public function addToPlaylist($playlistID, $songIds = ''){
		$idsArray = preg_split("/-/", $songIds);

		echo $this->xml->addToPlaylist($playlistID, $idsArray);
	}
	public function removeFromPlaylist($playlistID, $songIds = ''){
		$idsArray = preg_split("/-/", $songIds);

		echo $this->xml->removeFromPlaylist($playlistID, $idsArray);
	}

	public function getRadioJSON($artistId, $count = 50){
		$data['json'] = $this->xml->getRadioJSON($artistId, $count);
		$this->load->view('json.php', $data);
	}

	public function stream($id, $path = false){

		$bitrate = $this->user->getBitrate();
		if(!$path)
			$path = $this->xml->getMusicStreamAddress($id, $bitrate);

		// If the user is a mobile we redirect it directly to the external server
		$this->load->library('user_agent');
		if($this->agent->is_mobile()){
			redirect($path);
			exit;
		}

		// Set ssl context
		$ctx = stream_context_create(['ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false
		]]);


		// Open the file
		$fp = fopen($path, 'rb', false, $ctx);

		// Copy the file's header
		$metaData = stream_get_meta_data($fp);
		$headerLines = $metaData['wrapper_data'];

		foreach($headerLines as $header){
			header($header);
		}
		fpassthru($fp);

		fclose($fp);
	}

	public function guestStream($id){

		if(!$this->user->getHash() || $this->xml->checkSharedId($this->user->getHash(), $id)){
			$this->stream($id);
		}
	}

	public function getCover($id, $size = false){
		if(!$size){
			redirect(base_url() . 'assets/img/album-cover-bg.png');
		} else {
			// Get the file path on the server
			$path = $this->xml->getCover($id, $size);

			// If the user is a mobile we redirect it directly to the external server
// 			$this->load->library('user_agent');
// 			if($this->agent->is_mobile()){
// 				redirect($path);
			// 	/ids
			// 	exit;
// 			}

			// Set ssl context
			$ctx = stream_context_create(['ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false
			]]);


			// Open the file
			$fp = fopen($path, 'rb', false, $ctx);

			// Copy the file's header
			$metaData = stream_get_meta_data($fp);
			$headerLines = $metaData['wrapper_data'];

			foreach($headerLines as $header){
				header($header);
			}
			fpassthru($fp);

			fclose($fp);
		}
	}

	public function scrobble($id){
		echo $this->xml->scrobble($id);
	}

	public function share(){
		$server = $this->input->post('server');
		if(!(substr($server, -1) == '/')){
			$server = $server . '/';
		}
		$pseudo = $this->input->post('pseudo');
		$password = $this->input->post('password');
		$songs = $this->input->post('songs');

		$hash = $this->xml->createShare($pseudo, $password, $server, $songs);

		if($hash){
			$link = base_url() . 'main/shared/' . $hash;
			$return = array('status' => 'ok', 'link' => $link);
		} else {
			$return = array('status' => 'error');
		}
		echo json_encode($return);
	}

	public function shared($hash){
		$data = array('is_shared' => true);

		if($this->user->setSharedConfig($hash))
			$data['status'] = True;
		else
			$data['status'] = False;


		$albumData = array();
		$query = $this->xml->getShare($hash);
		$songArray = json_decode($query->songs);
		$albumData['songs'] = array();
		foreach($songArray as $song){
			$tmpSong = new AnObj(array());
			$tmpSong->attributes = function() use($song){
				if(!isset($song->track))
					$track = '';
				else
					$track = $song->track;
				$duration = $this->_toSec($song->duration);
				return (object) array(
					'mp3'		=> $song->mp3,
					'title'		=> $song->title,
					'artist'	=> $song->artist,
					'album'		=> $song->album,
					'duration'	=> $duration,
					'coverArt'	=> $song->cover,
					'id'		=> $song->id,
					'position'	=> $song->position,
					'track'		=> $track,
				);
			};
			$albumData['songs'][] = $tmpSong;
		}

// 		$albumData['songs'] = json_decode($query->songs);
		$albumData['title'] = 'Playlist';

		$data['data'] = $albumData;
		$data['playlists'] = array();

		$this->load->view('main.php', $data);

	}

	private function _toSec($duration){
		$array = preg_split('/:/', $duration);
		$min = $array[0];
		$sec = $array[1];
		return $min * 60 + $sec;
	}
}

/**
 * PHP Anonymous Object
 */
class AnObj
{
	protected $methods = array();

	public function __construct(array $options)
	{
		$this->methods = $options;
	}

	public function __call($name, $arguments)
	{
		$callable = null;
		if (array_key_exists($name, $this->methods))
			$callable = $this->methods[$name];
		elseif(isset($this->$name))
			$callable = $this->$name;

		if (!is_callable($callable))
			throw new BadMethodCallException("Method {$name} does not exists");

		return call_user_func_array($callable, $arguments);
	}
}
