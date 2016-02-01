<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User {

	protected $CI;

	private $noConfigMethods = array('index', 'config', 'tryConfig');
	private $sharedMethods = array('shared', 'guestStream', 'getCover');


	private $pseudo;
	private $password;
	private $server;
	private $bitrate;
	private $shared = False;
	private $sharedHash;
	private $isValid = False;

	private $error_message;


	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->model('xml');
		$this->CI->load->library('session');
	}

	public function isConnectionValid(){
		$method = $this->CI->router->fetch_method();

		if(in_array($method, $this->noConfigMethods))
			return True;

		if($this->CI->session->sharedHash)
			$this->sharedHash = $this->CI->session->sharedHash;

		$this->makeConfig();
		$tmp = $this->tryConfig();
		return !$this->sharedHash && $tmp || in_array($method, $this->sharedMethods);

	}

	private function isSharedConnectionValid(){
	}

	public function getError(){
		return $this->error_message;
	}

	public function testAndSetConfig($pseudo, $password, $server, $bitrate, $remember){
		$this->CI->session->unset_userdata('sharedHash');

		$this->pseudo = $pseudo;
		$this->password = $password;
		$this->server = $server;
		$this->bitrate = $bitrate;

		if($this->tryConfig()){
			$this->createSessions();

			if($remember == 'true'){
				$this->createCookies();
			}
			return True;
		}
		$this->pseudo = False;
		$this->password = False;
		$this->server = False;
		$this->bitrate = False;
		return False;
	}
	public function setSharedConfig($hash){
		if(!$this->isValid){

			$this->shared = True;
			$this->sharedHash = $hash;

			$query = $this->CI->xml->getShare($hash);
			$this->pseudo = $query->pseudo;
			$this->password = $query->password;
			$this->server = $query->server;
			$this->bitrate = '0';

			$this->createSessions();

			$newdata = array(
				'sharedHash'  => $hash,
			);
			$this->CI->session->set_userdata($newdata);
		}
		return $this->tryConfig();
	}
	private function makeConfig(){
		if($this->CI->session->pseudo && $this->CI->session->password && $this->CI->session->server && ($this->CI->session->bitrate || $this->CI->session->bitrate === '0')){
			$this->pseudo = $this->CI->session->pseudo;
			$this->password = $this->CI->session->password;
			$this->server = $this->CI->session->server;
			$this->bitrate = $this->CI->session->bitrate;

			if($this->CI->session->sharedHash)
				$this->sharedHash = $this->CI->session->sharedHash;
		} else {
			if($this->CI->input->cookie('pseudo') && $this->CI->input->cookie('password') && $this->CI->input->cookie('server') && ($this->CI->input->cookie('bitrate') || $this->CI->input->cookie('bitrate') === '0')){
				$this->pseudo = $this->CI->input->cookie('pseudo');
				$this->password = $this->CI->input->cookie('password');
				$this->server = $this->CI->input->cookie('server');
				$this->bitrate = $this->CI->input->cookie('bitrate');

				$this->createSessions();
			}
			
		}
	}

	private function createSessions(){
		$newdata = array(
			'pseudo'  => $this->pseudo,
			'password'     => $this->password,
			'server' => $this->server,
			'bitrate' => $this->bitrate
		);

		$this->CI->session->set_userdata($newdata);
	}
	private function createCookies(){
		$cookie = array(
			'name'   => 'pseudo',
			'value'  => $this->pseudo,
			'expire' => '31536000',
			'domain' => '',
			'path'   => '/',
		);
		$this->CI->input->set_cookie($cookie);
		$cookie = array(
			'name'   => 'password',
			'value'  => $this->password,
			'expire' => '31536000',
			'domain' => '',
			'path'   => '/',
		);
		$this->CI->input->set_cookie($cookie);
		$cookie = array(
			'name'   => 'server',
			'value'  => $this->server,
			'expire' => '31536000',
			'domain' => '',
			'path'   => '/',
		);
		$this->CI->input->set_cookie($cookie);
		$cookie = array(
			'name'   => 'bitrate',
			'value'  => $this->bitrate,
			'expire' => '31536000',
			'domain' => '',
			'path'   => '/',
		);
		$this->CI->input->set_cookie($cookie);

	}

	private function tryConfig(){
		if($this->pseudo && $this->password && $this->server){

			$pingXML = $this->CI->xml->ping($this->pseudo, $this->password, $this->server);
			if($pingXML->attributes()->status != 'ok'){
				$this->error_message = $pingXML->error->attributes()->message;
				return False;
			}	
			$this->isValid = True;
			return True;

		}
		return False;
	}

	public function getBitrate(){
		$this->makeConfig();
		return $this->bitrate;
	}
	public function getPseudo(){
		return $this->pseudo;
	}
	public function getPassword(){
		return $this->password;
	}
	public function getServer(){
		return $this->server;
	}
	public function getHash(){
		return $this->sharedHash;
	}
	public function getAPIPath($fileName){
		$this->makeConfig();
		return $this->server . 'rest/' . $fileName . '.view?v=1.12.0&c=webclient&u=' . $this->pseudo . '&p=enc:' . $this->password;
	}

}
