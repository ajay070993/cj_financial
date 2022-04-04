<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_content_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_tpl_content';
	}	

	function getAllTplContentRecords(){
	    $this->db->select('tbl_tpl_content.*,tbl_bank_statement.credit_end_string,tbl_bank_statement.debit_end_string,tbl_bank_statement.checks_end_string,tbl_bank_statement.credit_start_string,tbl_bank_statement.unique_string');
	    $this->db->join('tbl_bank_statement', 'tbl_tpl_content.bank_id = tbl_bank_statement.bank_id','left'); 
	    $query = $this->db->get('tbl_tpl_content');
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('hrere');
	    return $result;
	}
	
	/*function checkNumRows(){
	    $this->db->where('whatever');
	    $num = $this->db->count_all_results('table');
	    return $num;
	}*/
	
	function addNewRecords($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}	
	
	function updateRecordByBankId($bank_id,$data){
	    $this->db->where('bank_id',$bank_id);
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