<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Data_validation_check extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_data_validation_check';
	}
	
	function addRecord($data){
	    $this->db->insert($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $this->db->insert_id();
	}
	
	function getAllDataValidationRecord($history_id){
	    $this->db->select('*');
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get($this->table);
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}
	
	function deleteAllRecords($history_id){
	    $this->db->where('history_id',$history_id);
	    $this->db->delete($this->table);
	}
	
	/*function updateRecord($history_id,$data){
	    $this->db->where('id',$history_id);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}*/
	
}