<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_customer_txn_data extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_customer_txn_data';
	}	

	function addCustomerTxnData($data){
	    $this->db->insert($this->table,$data);
	    //echo $this->db->last_query()."--------------";
	    //die('her');
	    return $this->db->insert_id();  
	}
	
	function fetchCustomerTxnData($history_id,$file_no){
	    $this->db->select('*');
	    $this->db->where('history_id',$history_id);
	    $this->db->where('file_no',$file_no);
	    $this->db->order_by("id","asc");
	    $query = $this->db->get($this->table);
	    $result = $query->result();
	    return $result;
	}
	
	function fetchCustomerTxnDataForCategorization($history_id){
	    $this->db->select('id,file_no,description,txn_amt,type');
	    $this->db->where('history_id',$history_id);
	    //$this->db->limit(1);
	    $query = $this->db->get($this->table);
	    $result = $query->result();
	    return $result;
	}
	
	function updateCategories($id,$data){
		$this->db->where('id',$id);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}

	function getRecordById($id){
	    $this->db->select('id,level_1');
	    $this->db->where('id',$id);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}
	  
}
