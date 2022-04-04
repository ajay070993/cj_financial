<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_case_error_log extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_case_error_log';
	}	

	function addRecord($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}	
	  
}