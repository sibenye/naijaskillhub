<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/security/NSH_CryptoService.php');
require_once(APPPATH.'/core/objects/user.php');


class EmailSends_model extends CI_Model
{
    use NSH_CryptoService;
    
    public function __construct()
    {
        $this->load->library('email');
        $this->config->load('email');
    }
    
    function send_activation_email($user) {
        $activationToken = $this->getActivationToken($user);
        $to_email = $user->emailAddress;
        $from_email = $this->config->item('from_email');
        $this->email->from($from_email, EMAIL_FROM_NAME);
        $this->email->to($to_email);
        $this->email->subject(EMAIL_ACTIVATION_SUBJECT);
        //TODO: build activation url and put in message
        $this->email->message('Click the link below to activate your account. \r\n'.$activationToken);
         
        //Send mail
        return $this->email->send();
    }
    
    private function getActivationToken($user) {
        //generate random string
        $activationToken = $this->secure_random();
        //hash the random string and save in database
        $activationTokenHash = $this->secure_hash($activationToken);
    
        $this->db->update(USERS_TABLE, array('activationToken' => $activationTokenHash), array('id' => $user->id));
    
        //concat user's email + the activationToken and encrypt it
        $activationKey = $user->emailAddress.ACTIVATION_KEY_DELIMITER.$activationToken;
    
        //encode
        $activationKeyEncoded = $this->encode($activationKey);
    
        return $activationKeyEncoded;
    }
}