<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends CI_Controller {
	
	function __construct() {
		Parent::__construct();
        $this->common_model->checkCjXtractUser();
	}

	public function index() {
		$output['page_title'] = 'Welcome'; 
	}
}
