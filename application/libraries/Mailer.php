<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Mailer{
	
    public function __construct() {
        require_once(APPPATH.'third_party/phpmailer/class.pop3.php');
        require_once(APPPATH.'third_party/phpmailer/class.smtp.php');
        require_once(APPPATH.'third_party/phpmailer/class.phpmailer.php');
    }
    
    public function getMailer(){
    	$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';

		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = '';
		$mail->Password = '';
		return $mail;
    }
}
?>