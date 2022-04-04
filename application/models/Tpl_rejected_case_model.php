<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_rejected_case_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_tpl_history';
	}	

	function getAllHistoryRecords(){
	    $this->db->select('bank_id,tbl_tpl_history.file_name,original_pdf_file_name,tbl_banks.bank_name,tbl_tpl_history.created_on,downloaded_file_name,type,tbl_bulk_upload.folder_name,tbl_bulk_upload.file_name as m_file,tbl_bulk_upload.status');
	    $this->db->join('tbl_banks', 'tbl_tpl_history.bank_id = tbl_banks.id','left');
	    $this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->order_by("tbl_tpl_history.id","desc");
	    $query = $this->db->get('tbl_tpl_history');
	    $result = $query->result();
	    //echo"<pre>";
	    //print_r($result);
	    //echo $this->db->last_query();
	    //die('her');
	    return $result;
	}
	
	function addNewRecords($data){
	    $this->db->insert($this->table,$data);
	    //echo $this->db->last_query();
	    //die('her');
	    return $this->db->insert_id();
	}
	
	function updateHistoryRecord($history_id,$data){
	    $this->db->where('id',$history_id);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	    
	}

	function countSpreading(){
	    $this->db->select('COUNT(*) as countSpreading');
	    $query = $this->db->get('tbl_tpl_history');
	    $result = $query->row();
	    //print_r($result);
	    //echo $this->db->last_query();
	    //die('her');
	    return $result->countSpreading;
	}
	function countLastWeek(){
	    $this->db->select('COUNT(*) as countLastWeek');
	    $this->db->where('created_on BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()');
	    //$this->db->where($conditions);
	    $query = $this->db->get('tbl_tpl_history');
	    $result = $query->row();
	    //print_r($result);
	    //echo $this->db->last_query();
	    //die('her');
	    return $result->countLastWeek;
	}
	
	function avgSpreadTime(){
	    $this->db->select('ROUND(AVG(convert_time),0) as avgSpreadTime');
	    $query = $this->db->get('tbl_tpl_history');
	    $result = $query->row();
	    //print_r($result);
	    //echo $this->db->last_query();
	    //die('her');
	    return $result->avgSpreadTime;
	}
	
	function make_query()
	{
		$user_role = $this->session->userdata('user_role');
	    $this->db->select('tbl_tpl_history.id,tbl_banks.bank_name,tbl_tpl_history.created_on,CONCAT_WS(" ", tbl_users.first_name,  tbl_users.last_name) AS upload_user_name,upload_user_id,unique_id,business_name,submit_by_qa,click_to_send,tbl_case_status.spreading_status as spreaded_status');
	    $this->db->join('tbl_banks', 'tbl_tpl_history.bank_id = tbl_banks.id','left');
	    $this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->join('tbl_users', 'tbl_tpl_history.upload_user_id = tbl_users.id','left');
	    $this->db->join('tbl_case_status', 'tbl_case_status.id = tbl_tpl_history.id','left');
	    $this->db->from($this->table);
	    /*if($user_role==2){
	    	$this->db->where('tbl_tpl_history.upload_user_id',$this->session->userdata('user_id'));
		}
		else if($user_role==3){
	    	$this->db->where('tbl_tpl_history.submit_by_qa','1');
		}*/
	    $this->db->where('tbl_case_status.spreading_status','Failed');
	    if(isset($_POST["search"]["value"]))
	    {
	        
	        $this->db->group_start();
	        $this->db->like('original_pdf_file_name', $_POST['search']['value']);
	        $this->db->or_like('tbl_banks.bank_name', $_POST['search']['value']);
	        $this->db->or_like('tbl_tpl_history.created_on', $_POST['search']['value']);
	        $this->db->or_like('downloaded_file_name', $_POST['search']['value']);
	        $this->db->or_like('tbl_tpl_history.type', $_POST['search']['value']);
	        $this->db->group_end();
	    }
	    
	    if(isset($_POST["order"]))
	    {
	        $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	    }
	    else
	    {
	        $this->db->order_by('tbl_tpl_history.id', 'DESC');
	    }
	}
	function make_datatables(){
	    $this->make_query();
	    if($_POST["length"] != -1)
	    {
	        $this->db->limit($_POST['length'], $_POST['start']);
	    }
	    $query = $this->db->get();
	    //echo $this->db->last_query();
	    //die('her');
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
	
	function checkZipFileRecord($zipFileName,$newFolderName){
	    //$ql = $this->db->select('*')->from($this->table)->where('zip_folder_name',$zipFileName)->where('file_name',$newFolderName)->get();
	    //return $ql->result();
	    
	    $this->db->select('*');
	    $this->db->where('zip_folder_name',$zipFileName);
	    $this->db->where('file_name',$newFolderName);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    return $result;
	}

	function updateRecords($id,$data){
		$this->db->where('id',$id);
		$this->db->update($this->table,$data);
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}

	function getSingleRecordById($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);
		$result = $query->row();
		return $result;
	}
  
}