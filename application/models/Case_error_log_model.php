<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Case_error_log_model extends CI_Model {
	function __construct() { 
		parent::__construct();
		$this->table = 'tbl_case_error_log';
	}	
	
	function getRecordByHistoryId($history_id){
		$this->db->select('*');
		$this->db->where('history_id',$history_id);
		$query = $this->db->get($this->table);
		$result = $query->result();
		return $result;
	}

	function updateErrorLog($history_id,$file_no,$data){
	    $this->db->where('history_id',$history_id);
	    $this->db->where('file_no',$file_no);
	    $result = $this->db->update($this->table,$data);
	    return $result;
	}
	
	function addRecord($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}	
	
	function deleteRecord($history_id){
	    $this->db->where('history_id',$history_id);
	    $this->db->delete('tbl_case_error_log');
	}
  
}