<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_address_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_bank_address';
	}
	
	function getRecordsByBankId($bank_id){
		$this->db->select('*');
		$this->db->where('bank_id',$bank_id);
		$query = $this->db->get($this->table);
		$result = $query->result();
		return $result;
	}
	
}