<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_categories_token extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_categories_token';
	}
	
	function getToken(){
	    $this->db->select('*');
	    $this->db->where('status','0');
	    
	    $this->db->order_by('id', 'ASC');
	    $this->db->limit('1');
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	    
	}

	function getTokenByHistoryId($history_id){
	    $this->db->select('*');
	    $this->db->where('status','0');
	    $this->db->where('history_id',$history_id);
	    $this->db->order_by('id', 'ASC');
	    $this->db->limit('1');
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}
	
	function updateToken($token,$data){
	    $this->db->where('token',$token);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}
	
}