<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/OAuth2/Autoloader.php'); 
	
class oAuth{
	private $ci;
	private $storage;
	
	public function __construct(){
		$this->ci =& get_instance();
		$this->ci->load->database();
		
		$dsn      = 'mysql:dbname='.$this->ci->db->database.';host='.$this->ci->db->hostname;
		OAuth2\Autoloader::register();
		// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $this->ci->db->username, 'password' => $this->ci->db->password));
	}
	
	public function server(){
		// Pass a storage object or array of storage objects to the OAuth2 server class
		$server = new OAuth2\Server($this->storage);
		
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));
		$server->addGrantType(new OAuth2\GrantType\UserCredentials($this->storage));
		$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
		$server->addGrantType(new OAuth2\GrantType\RefreshToken($this->storage, array(
			'always_issue_new_refresh_token' => true
		)));
		return $server;
	}
}
?>