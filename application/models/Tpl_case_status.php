<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_case_status extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_case_status';
	}	

	function addRecord($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}	
	
	function updateRecord($id,$data){
	    $this->db->where('id',$id);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}
	
	function checkRecordExist($bank_id){
	    $ql = $this->db->select('id')->from($this->table)->where('bank_id',$bank_id)->get();
	    return $ql->num_rows();
	}
	  
}