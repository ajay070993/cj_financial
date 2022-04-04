<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_summary_level_data extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_bank_statement_summary_level_data';
	}	

	function addSummaryLevelData($data){
	    $this->db->insert($this->table,$data);
	    //echo $this->db->last_query();
	    //die('her');
	    return $this->db->insert_id();  
	}
	
	/*function fetchSummaryLevelData($history_id){
	    /*$this->db->select('*');
	    $this->db->where('history_id',$history_id);
	    //$this->db->order_by("start_date","asc");
	    
	    $query = $this->db->query( "SELECT * FROM (
         SELECT *
         FROM tbl_bank_statement_summary_level_data
         WHERE (history_id = ?)
         ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y') DESC
         LIMIT 3
         ) AS `table` ORDER by STR_TO_DATE(start_date, '%m/%d/%Y') ASC", array( $history_id));
	    
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}*/
	
	
	/*This function used for get data ASC by DATE
	 *
	 */
	function fetchSummaryLevelData($history_id){
	    /*$this->db->select('*');
	     $this->db->where('history_id',$history_id);
	     //$this->db->order_by("start_date","asc");*/
	    
	    $query = $this->db->query( "SELECT * FROM (
         SELECT *
         FROM tbl_bank_statement_summary_level_data
         WHERE (history_id = ?)
         ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y') 
         ) AS `table` ORDER by account_number, STR_TO_DATE(start_date, '%m/%d/%Y') ASC", array( $history_id));
	    
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}
	
	function fetchSummaryLevelDataCategory($history_id){
	    /*$this->db->select('*');
	     $this->db->where('history_id',$history_id);
	     //$this->db->order_by("start_date","asc");*/
	    
	    $query = $this->db->query( "SELECT * FROM (
         SELECT *
         FROM tbl_bank_statement_summary_level_data
         WHERE (history_id = ?)
         ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y')
         ) AS `table` ORDER by STR_TO_DATE(start_date, '%m/%d/%Y') ASC", array( $history_id));
	    
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}
	
	/*This function used for get data ASC by ID
	 * 
	 */
	function fetchSummaryLevelDataByIdAsc($history_id){
	    $this->db->select('*');
	     $this->db->where('history_id',$history_id);
	     //$this->db->order_by("start_date","asc");*/
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('tbl_bank_statement_summary_level_data');
        $result = $query->result();
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}
	
	function fetchSummaryLevelDataForCategorization($history_id){
	    $this->db->select('account_number,file_no,account_holder_name,account_type');
	    $this->db->where('history_id',$history_id);
	    // $this->db->where('file_no',$file_no);
	    $query = $this->db->get($this->table);
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}
	
	function getCountHistoryId($last_history_id){
	    $ql = $this->db->select('*')->from($this->table)->where('history_id',$last_history_id)->get();
	    return $ql->num_rows();
	}

	function getCustomerNameByHistoryId($history_id){
		$this->db->select('account_holder_name,native_vs_non_native');
		$this->db->where('history_id',$history_id);
		$this->db->order_by('id', 'asc');
		$query = $this->db->get('tbl_bank_statement_summary_level_data');
		$result = $query->row();
		return $result;
	}

	function getSummaryRecordByHistoryId($history_id){
		$this->db->select('id');
		$this->db->where('history_id',$history_id);
		$query = $this->db->get('tbl_bank_statement_summary_level_data');
		$result = $query->row();
		return $result;
	}

}