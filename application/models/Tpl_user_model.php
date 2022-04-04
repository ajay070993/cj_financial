<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_user_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_users';
	}	

	function getAllUsers(){
	    $this->db->select('id,first_name,last_name,email,gender,type,status');
		$this->db->where('application_type',$this->session->userdata('application_type'));
	    $query = $this->db->get($this->table);
	    $result = $query->result();
	    return $result;
	}	
	
	function addNewUser($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}	
	
	function editUser($id){
	    $this->db->select('first_name,last_name,email,gender,type,status,user_role');
	    $this->db->where('id',$id);
        $this->db->where('application_type',$this->session->userdata('application_type'));
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}
	
	function updateRecordByUserId($user_id,$data){
	    $this->db->where('id',$user_id);
        $this->db->where('application_type',$this->session->userdata('application_type'));
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}
	
	function deleteRecordByUserId($user_id){
	    $this->db->where('id', $user_id);
        $this->db->where('application_type',$this->session->userdata('application_type'));
	    $this->db->delete($this->table);
	    //echo $this->db->last_query();
	    //die;
	    return true;
	}
	
	function make_query()
	{
	    $this->db->select('id,first_name,last_name,email,gender,type,status');
	    //$this->db->join('tbl_bank_statement', 'tbl_banks.id = tbl_bank_statement.bank_id','left');
	    //$this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->from($this->table);
		
	    	// condition for cj financial
			$this->db->group_start();
		$this->db->where('application_type',$this->session->userdata('application_type'));
		$this->db->group_end();
	    if(isset($_POST["search"]["value"]))
	    {
			$this->db->group_start();
	        $this->db->like("id", $_POST["search"]["value"]);
	        $this->db->or_like("first_name", $_POST["search"]["value"]);
	        $this->db->or_like("last_name", $_POST["search"]["value"]);
	        $this->db->or_like("email", $_POST["search"]["value"]);
	        $this->db->or_like("gender", $_POST["search"]["value"]);
	        $this->db->or_like("type", $_POST["search"]["value"]);
	        $this->db->or_like("status", $_POST["search"]["value"]);
			$this->db->group_end();
	        
	    }
	    
	    if(isset($_POST["order"]))
	    {
	        $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	    }
	    else
	    {
	        $this->db->order_by('tbl_users.id', 'ASC');
	    }
	    // echo $this->db->last_query();
	    // die(' here');
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
		// echo $this->db->last_query();
	    // die();
	    return $query->result();
	}
	
	function get_filtered_data(){
	    $this->make_query();
	    $query = $this->db->get();
	    return $query->num_rows();
	}
	
	function get_all_data(){
	    $this->db->select("*");
		$this->db->where('application_type',$this->session->userdata('application_type'));
	    $this->db->from($this->table);
	    return $this->db->count_all_results();
	}
}