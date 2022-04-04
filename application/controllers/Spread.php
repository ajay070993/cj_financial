<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class spread extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjXtractUser();
    }  
  
    function index() {
        $this->load->view('spreading');    
    } 
    
}