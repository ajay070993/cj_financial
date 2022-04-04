<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_statement_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_bank_statement';
	}
	
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

	function getSingleRecordByBankId($bank_id){
		$this->db->select('tbl_bank_statement.*,tbl_tpl_content.id,tbl_tpl_content.content,tbl_tpl_content.bank_url,tbl_tpl_content.end_line_no');
		$this->db->join('tbl_tpl_content', 'tbl_tpl_content.bank_id = tbl_bank_statement.bank_id','left');
		$this->db->where('tbl_bank_statement.bank_id',$bank_id);
		$query = $this->db->get($this->table);
		$result = $query->row();
		//echo $this->db->last_query();
		//die('here');
		return $result;
	}
	
	function getTemplatesData(){
	    $this->db->select('*');
	    $this->db->join('tbl_banks', 'tbl_banks.id = tbl_bank_statement.bank_id','right');
	    $this->db->where('tbl_banks.status','Active');
	    $query = $this->db->get('tbl_bank_statement');
	    $result = $query->result();
	    //echo $this->db->last_query();
	    //die('here');
	    return $result;
	}
	
	function getTextFileName($bank_id){
	    $this->db->select('text_file_name');
	    $this->db->where('bank_id',$bank_id);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}
	
	function getRecordWithoutRegex($bank_id){
	    $this->db->select('tbl_bank_statement.id,tbl_bank_statement.bank_id,tbl_bank_statement.credit_start_string,tbl_bank_statement.credit_table_format,tbl_bank_statement.credit_end_string,tbl_bank_statement.debit_start_string,tbl_bank_statement.debit_table_format,tbl_bank_statement.debit_end_string,tbl_bank_statement.checks_start_string,tbl_bank_statement.cheque_table_format,tbl_bank_statement.checks_end_string,tbl_bank_statement.txn_sec_1,tbl_bank_statement.txn_1_start_string,tbl_bank_statement.txn_1_table_format,tbl_bank_statement.txn_1_end_string,tbl_bank_statement.txn_1_type,tbl_bank_statement.txn_sec_2,tbl_bank_statement.txn_2_start_string,tbl_bank_statement.txn_2_table_format,tbl_bank_statement.txn_2_end_string,tbl_bank_statement.txn_2_type,tbl_bank_statement.txn_sec_3,tbl_bank_statement.txn_3_start_string,tbl_bank_statement.txn_3_table_format,tbl_bank_statement.txn_3_end_string,tbl_bank_statement.txn_3_type,tbl_bank_statement.txn_sec_4,tbl_bank_statement.txn_4_start_string,tbl_bank_statement.txn_4_table_format,tbl_bank_statement.txn_4_end_string,tbl_bank_statement.txn_4_type,tbl_bank_statement.txn_sec_5,tbl_bank_statement.txn_5_start_string,tbl_bank_statement.txn_5_table_format,tbl_bank_statement.txn_5_end_string,tbl_bank_statement.txn_5_type,tbl_bank_statement.service_fee_title_1,tbl_bank_statement.service_fee_type_1,tbl_bank_statement.service_fee_title_2,tbl_bank_statement.service_fee_pattern_2,tbl_bank_statement.service_fee_type_2,tbl_bank_statement.account_type,tbl_bank_statement.name_of_bank,tbl_bank_statement.bank_address,tbl_bank_statement.bank_city,tbl_bank_statement.bank_state,tbl_bank_statement.bank_zip,tbl_bank_statement.file_name,tbl_bank_statement.text_file_name,tbl_bank_statement.txn_start_from,tbl_bank_statement.is_deleted,tbl_bank_statement.add_date,tbl_bank_statement.status,tbl_bank_statement.uploader_type,tbl_bank_statement.updated_on,tbl_tpl_content.id,tbl_tpl_content.bank_url,tbl_tpl_content.end_line_no');
	    $this->db->join('tbl_tpl_content', 'tbl_tpl_content.bank_id = tbl_bank_statement.bank_id','left');
	    $this->db->where('tbl_bank_statement.bank_id',$bank_id);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    //echo $this->db->last_query();
	    //die('here');
	    return $result;
	}
	
	function cloneTemplate($bank_id,$clone_temp){
	    $data = array();
	    $data['bank_name'] = $clone_temp;
	    $data['created_on'] = date("Y-m-d h:i:sa");
	    
	    $this->db->insert('tbl_banks',$data);
	    $last_insert_id = $this->db->insert_id();
	    
	    $this->db->where('bank_id',$bank_id);
	    $query = $this->db->get($this->table);
	    foreach ($query->result() as $row){
	        foreach($row as $key=>$val){
	            if($key != 'id' && $key != 'bank_id'){
	                $this->db->set($key, $val);
	            }else if($key == 'bank_id'){
	                $this->db->set($key, $last_insert_id);
	            }
	        }//endforeach
	    }
	    $this->db->insert($this->table);
	    return $last_insert_id;
	}
}