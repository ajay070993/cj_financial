<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Logout extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();    
        $this->user_id = $this->session->userdata('user_id');       
    }  
  
    function index() {       
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');     
        $this->session->unset_userdata('email');  
        $this->session->unset_userdata('user_role');  
        $this->session->unset_userdata('data-type-collapse');       
        $message = 'Logout successful.';
        // $this->session->set_flashdata('message', $message);
        $success = true;
        redirect('login');        
    } 
}