<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(-1);

//require_once(APPPATH.'third_party/ApnsPHP/Autoload.php'); 

Class Apn {

    private $push;
    private $feedback;

    public function __construct() {
        require_once APPPATH . 'third_party/ApnsPHP/Abstract.php';
        require_once APPPATH . 'third_party/ApnsPHP/Exception.php';
        require_once APPPATH . 'third_party/ApnsPHP/Feedback.php';
        require_once APPPATH . 'third_party/ApnsPHP/Message.php';
        require_once APPPATH . 'third_party/ApnsPHP/Log/Interface.php';
        require_once APPPATH . 'third_party/ApnsPHP/Log/Embedded.php';
        require_once APPPATH . 'third_party/ApnsPHP/Message/Custom.php';
        require_once APPPATH . 'third_party/ApnsPHP/Message/Exception.php';
        require_once APPPATH . 'third_party/ApnsPHP/Push.php';
        require_once APPPATH . 'third_party/ApnsPHP/Push/Exception.php';
        require_once APPPATH . 'third_party/ApnsPHP/Push/Server.php';
        require_once APPPATH . 'third_party/ApnsPHP/Push/Server/Exception.php';

        $this->push = new ApnsPHP_Push(
            //ApnsPHP_Abstract::ENVIRONMENT_SANDBOX, APPPATH . '{PEM FILE}'
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, APPPATH . '{PEM FILE}'
        );
        //$this->push->setRootCertificationAuthority(APPPATH.'Cert/Entrust.pem');
        
        $this->feedback = new ApnsPHP_Feedback(
                ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
                APPPATH . '{PEM FILE}'
        );
        //$this->feedback->setRootCertificationAuthority(APPPATH.'Cert/Entrust.pem');
    }

    public function sendList($dataArray, $msg, $sound = true, $data = array()){
        $ioss = array();
        if(!empty($dataArray)){
            foreach($dataArray as $d){
                $ioss[] = $d["uuid"];
            }
            return $this->send($ioss, $msg, $dataArray[0]["id"], $dataArray[0]["badge"], $sound, $data);
        }
    }

    public function send($uuid, $msg, $id, $badge, $sound = true, $data = array()) {
        if(empty($uuid)){
            return;
        }
        if(mb_strlen($msg) > 50){
            $msg = mb_strcut($msg, 0, 50)."...";
        }

        // Instantiate a new Message with a single recipient
        if (is_array($uuid)) {
            $this->push->connect();
            foreach ($uuid as $uid) {
                $message = new ApnsPHP_Message_Custom($uid);

                // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
                // over a ApnsPHP_Message object retrieved with the getErrors() message.
                $message->setCustomIdentifier("Msg-" . $id);
                
                $message->setAutoAdjustLongPayload(true);

                // Set badge icon to "3"
                $message->setBadge($badge);

                // Set a simple welcome text
                $message->setText($msg);

                // Play the default sound
                if ($sound)
                    $message->setSound();

                // Set a custom property
                //$message->setCustomProperty('acme2', array('bang', 'whiz'));
                if(!empty($data)){
                    foreach($data as $key => $value){
                        $message->setCustomProperty($key, $value);
                    }
                }

                // Set the expiry value to 30 seconds
                $message->setExpiry(30);

                // Set the "View" button title.
                $message->setActionLocKey('Qlinic');

                // Set the alert-message string and variable string values to appear in place of the format specifiers.
                $message->setLocKey($msg); // This will overwrite the text specified with setText() method.
                //$message->setLocArgs(array('Steve', 5));

                // Set the filename of an image file in the application bundle.
                $message->setLaunchImage('DefaultAlert.png');

                // Add the message to the message queue
                $this->push->add($message);
            }
            // Send all messages in the message queue
            $this->push->send();

            // Disconnect from the Apple Push Notification Service
            $this->push->disconnect();

            // Examine the error message container
            $aErrorQueue = $this->push->getErrors();
            if (!empty($aErrorQueue)) {
                // test and check error msg
                return $aErrorQueue;
            }
        }
    }
    
    public function feedBack(){
        $this->feedback->connect();
        $aDeviceTokens = $this->feedback->receive();
        $deleteIDs = array();
        if (!empty($aDeviceTokens)) {
                //var_dump($aDeviceTokens);
            foreach($aDeviceTokens as $token){
                $deleteIDs[] = $token["deviceToken"];
            }
        }
        // Disconnect from the Apple Push Notification Feedback Service
        $this->feedback->disconnect();
        return $deleteIDs;
    }

}

?>