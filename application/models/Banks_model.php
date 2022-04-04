<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Banks_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_banks';
	}	

	function getAllBanksRecords(){
		$this->db->select('id,bank_name');	
		$this->db->where('tbl_banks.status','Active');
		$this->db->order_by('bank_name','asc');
		$query = $this->db->get($this->table);
		$result = $query->result();
		return $result;
	}
        
    function addNewRecords($data){
		$this->db->insert($this->table,$data);
		return $this->db->insert_id();
	}
	function getBankName($bank_id){
	    $this->db->select('id,bank_name');
	    $this->db->where('id',$bank_id);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}
	
	function countTemplate(){
	    $this->db->select('COUNT(*) as countTemplate');
	    $this->db->join('tbl_banks', 'tbl_bank_statement.bank_id = tbl_banks.id','left');
	    $query = $this->db->get('tbl_bank_statement');
	    $result = $query->row();
	    //print_r($result);
	    //echo $this->db->last_query();
	    //die('her');
	    return $result->countTemplate; 
	}
	
	//for template
	function make_query()
	{
	    $this->db->select('tbl_banks.id,tbl_banks.bank_name,tbl_banks.created_on,tbl_bank_statement.updated_on,tbl_banks.uses_count');
	    $this->db->join('tbl_bank_statement', 'tbl_banks.id = tbl_bank_statement.bank_id','left');
	    //$this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->from($this->table);
	    
	    if(isset($_POST["search"]["value"]))
	    {
	        $this->db->like("tbl_banks.bank_name", $_POST["search"]["value"]);
	        $this->db->or_like("tbl_banks.created_on", $_POST["search"]["value"]);
	        $this->db->or_like("tbl_bank_statement.updated_on", $_POST["search"]["value"]);
	        $this->db->or_like("tbl_banks.uses_count", $_POST["search"]["value"]);
	        
	    }
	    
	    if(isset($_POST["order"]))
	    {
	        $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	    }
	    else
	    {
	        $this->db->order_by('tbl_banks.id', 'ASC');
	    }
	    //echo $this->db->last_query();
	    //die('here');
	}
	
	
	//for template
	function make_datatables(){
	    //die("hello");
	    $this->make_query();
	    if($_POST["length"] != -1)
	    {
	        $this->db->limit($_POST['length'], $_POST['start']);
	    }
	    $query = $this->db->get();
	    return $query->result();
	}
	
	function get_filtered_data(){
	    $this->make_query();
	    $query = $this->db->get();
	    return $query->num_rows();
	}
	
	function get_all_data(){
	    $this->db->select("*");
	    $this->db->from($this->table);
	    return $this->db->count_all_results();
	}

	function getBankIdByName($bank_name){
	    $this->db->select('id');
	    $this->db->where('bank_name',$bank_name);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}
}