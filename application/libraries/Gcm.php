<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(-1);

class Gcm{

	const SENDER_ID = "";
	const KEY="";

	public function __construct() {

	}

	public function sendMsg($tokens, $title, $message, $link, $id=""){
		if(is_array($tokens)){
			foreach($tokens as $token){
				$this->sendMsg($token, $title, $message, $link, $id);
			}
		}else{
			$body = array(
				"to"=>$tokens,
				"data"=>array(
						"title"=>$title,
						"message"=>$message,
						"link"=>$link,
						"id"=>$id
					)
			);
			return $this->requestToServer("https://gcm-http.googleapis.com/gcm/send",$body);
		}
	}

	public function createGroup($groupName, $tokens = array()){
		$body = array(
				"operation"=>"create",
				"notification_key_name"=>$groupName,
				"registration_ids"=>$tokens
			);
		$result = $this->requestToServer("https://android.googleapis.com/gcm/notification",$body);
		if(!empty($result["error"])){
			return $result["error"];
		}else{
			return $result["notification_key"];
		}
	}

	public function addToGroup($groupName, $groupToken, $tokens=array()){
		$body = array(
				"operation"=>"add",
				"notification_key_name"=>$groupName,
				"notification_key"=>$groupToken,
				"registration_ids"=>$tokens
			);
		$result = $this->requestToServer("https://android.googleapis.com/gcm/notification",$body);
		if(!empty($result["error"])){
			return $result["error"];
		}else{
			return $result["notification_key"];
		}
	}

	public function removeFromGroup($groupName, $groupToken, $tokens=array()){
		$body = array(
				"operation"=>"remove",
				"notification_key_name"=>$groupName,
				"notification_key"=>$groupToken,
				"registration_ids"=>$tokens
			);
		$result = $this->requestToServer("https://android.googleapis.com/gcm/notification",$body);
		if(!empty($result["error"])){
			return $result["error"];
		}else{
			return $result["notification_key"];
		}
	}

	private function requestToServer($url, $body){
		$headers = array(
            'Authorization: key='.self::KEY,
            'Content-Type: application/json',
            "project_id: ".self::SENDER_ID
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
 
        // Execute post
        $result = curl_exec($ch);
        //var_dump($result);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
         
        // Close connection
        curl_close($ch);
        $result = json_decode($result, true);
        return $result;
	}

}


?>