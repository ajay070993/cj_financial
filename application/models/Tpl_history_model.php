<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Tpl_history_model extends CI_Model {
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
	    $this->db->select('tbl_tpl_history.id,tbl_tpl_history.bank_id,tbl_tpl_history.file_name,original_pdf_file_name,tbl_tpl_history.created_on,tbl_tpl_history.qa_user_id,downloaded_file_name,tbl_tpl_history.type,tbl_bulk_upload.folder_name,tbl_bulk_upload.file_name as m_file,tbl_bulk_upload.status,CONCAT_WS(" ", tbl_users.first_name,  tbl_users.last_name) AS upload_user_name,CONCAT_WS(" ", u1.first_name,  u1.last_name) AS qa_user_name,upload_user_id,unique_id,business_name,submit_by_qa,click_to_send,b.success_type,c.txn_id,group_concat(concat(" ", d.id, " ")) AS log_id,group_concat(concat(" ", d.account_number, " ")) AS ac_num,group_concat(concat(" ", d.aaccount_holder_name, " ")) AS hol_name,group_concat(concat(" ", d.account_type, " ")) AS ac_type,group_concat(concat(" ", d.name_of_bank, " ")) AS bn_nm,group_concat(concat(" ", d.bank_address, " ")) AS bn_add,group_concat(concat(" ", d.bank_city, " ")) AS bn_cty,group_concat(concat(" ", d.bank_state, " ")) AS bn_st,group_concat(concat(" ", d.bank_zip, " ")) AS bn_zp,group_concat(concat(" ", d.current_balance, " ")) AS curr_bal,group_concat(concat(" ", d.start_date, " ")) AS st_dt,group_concat(concat(" ", d.end_date, " ")) AS en_dt,group_concat(concat(" ", d.closing_balance, " ")) AS clo_bal,group_concat(concat(" ", d.check_sum, " ")) AS chk_sm,group_concat(concat(" ", d.tpl_not_found, " ")) AS tpl_nt_fn');
	    //$this->db->join('tbl_banks', 'tbl_tpl_history.bank_id = tbl_banks.id','left');
	    $this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->join('tbl_users', 'tbl_tpl_history.upload_user_id = tbl_users.id','left');
	    $this->db->join('tbl_users u1', 'tbl_tpl_history.qa_user_id = u1.id','left');
	    //$this->db->join('urs_io_json_details_responce', 'tbl_tpl_history.id = urs_io_json_details_responce.history_id','left');
	    
	    $subquery = 'SELECT history_id,success_type FROM urs_io_json_details_responce where id in (SELECT MAX(id)
	                FROM urs_io_json_details_responce
	                GROUP BY history_id)
	                ) as';
	    $this->db->join("($subquery  b","`tbl_tpl_history`.`id` = b.history_id",'left');

	    $subquery1 = 'SELECT max(id) as txn_id,history_id FROM tbl_customer_txn_data where 1 GROUP BY history_id) as';
	    $this->db->join("($subquery1  c","`tbl_tpl_history`.`id` = c.history_id",'left');

	    $this->db->join('tbl_case_error_log d', 'tbl_tpl_history.id = d.history_id','left');

	    $this->db->from($this->table);
	    if($user_role==2){
	    	$this->db->where('tbl_tpl_history.upload_user_id',$this->session->userdata('user_id'));
		}
		/*else if($user_role==3){
	    	$this->db->where('tbl_tpl_history.submit_by_qa','1');
		}*/
	    if(isset($_POST["search"]["value"]))
	    {
	        
	        $this->db->group_start();
	        $this->db->like('original_pdf_file_name', trim($_POST['search']['value']));
	        //$this->db->or_like('tbl_banks.bank_name', trim($_POST['search']['value']));
	        $this->db->or_like('tbl_tpl_history.created_on', trim($_POST['search']['value']));
	        $this->db->or_like('downloaded_file_name', trim($_POST['search']['value']));
	        $this->db->or_like('tbl_tpl_history.type', trim($_POST['search']['value']));
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
		
		//Assigned to me filter 
		if(isset($_POST["assigned_filter"]) && $_POST["assigned_filter"]!='All')
	    {
			//die("here");
	    	if($_POST["assigned_filter"]=='Assigned to me'){
	    		$this->db->where('tbl_tpl_history.qa_user_id',$this->session->userdata('user_id'));
	    	}
	    	else if($_POST["assigned_filter"]=='Unassigned'){
	    		$this->db->where('tbl_tpl_history.qa_user_id','0');
	    	}
	    }
		//end assigned to me filter 
		
		//workflow filter
		if(isset($_POST["workflow_filter"]) && $_POST["workflow_filter"]!='All')
	    {
			//echo $_POST["workflow_filter"];die;
			if($_POST["workflow_filter"]=='Spreading'){
	    		$this->db->where('tbl_tpl_history.submit_by_qa','0');
	    	}
	    	if($_POST["workflow_filter"]=='Completed'){
	    		$this->db->where('tbl_tpl_history.click_to_send','1');
	    		$this->db->where('b.success_type','success');
	    	}
	    	
	    	if($_POST["workflow_filter"]=='Rejected-downstream'){
	    	    $this->db->where('tbl_tpl_history.click_to_send','1');
	    	    $this->db->where('b.success_type','error');
	    	}
	    	
			else if($_POST["workflow_filter"]=='Qa'){
	    		$this->db->where('tbl_tpl_history.submit_by_qa','1');
	    		$this->db->where('tbl_tpl_history.click_to_send','0');
	    	}
	    }
		
		//this is done because it is always as submit_by_qa 
		else if($user_role==3){ 
	    	$this->db->where('tbl_tpl_history.submit_by_qa','1');
		}
		//end workflow filter
		$this->db->group_by('tbl_tpl_history.id'); 
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

		$this->db->select('count(*)');
		$query = $this->db->get($this->table);
		$cnt = $query->row_array();
		return $cnt['count(*)'];

		// $this->db->select('count(*)');
	 //    $this->db->from($this->table);
	 //    $query = $this->db->get();
	 //    echo $this->db->last_query();
	 //    die('her');
	 //    return $query->num_rows();

	    // $this->make_query();
	    // $query = $this->db->get();
	    // echo $this->db->last_query();
	    // die('her');
	    // return $query->num_rows();
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



	function make_query_for_rejected_queue()
	{
		$user_role = $this->session->userdata('user_role');
	    $this->db->select('tbl_tpl_history.id,tbl_tpl_history.bank_id,tbl_tpl_history.file_name,original_pdf_file_name,tbl_banks.bank_name,tbl_tpl_history.created_on,downloaded_file_name,tbl_tpl_history.type,tbl_bulk_upload.folder_name,tbl_bulk_upload.file_name as m_file,tbl_bulk_upload.status,CONCAT_WS(" ", tbl_users.first_name,  tbl_users.last_name) AS upload_user_name,upload_user_id,unique_id,business_name,submit_by_qa,click_to_send');
	    $this->db->join('tbl_banks', 'tbl_tpl_history.bank_id = tbl_banks.id','left');
	    $this->db->join('tbl_bulk_upload', 'tbl_tpl_history.id = tbl_bulk_upload.history_id','left');
	    $this->db->join('tbl_bank_statement_summary_level_data', 'tbl_tpl_history.id = tbl_bank_statement_summary_level_data.history_id','left');
	    $this->db->join('tbl_users', 'tbl_tpl_history.upload_user_id = tbl_users.id','left');
	    $this->db->from($this->table);
	    $this->db->where('tbl_bank_statement_summary_level_data.id',null);
	    $this->db->where('tbl_bulk_upload.status','2');
	    $this->db->group_by('tbl_tpl_history.id');
	    if($user_role==2){
	    	$this->db->where('tbl_tpl_history.upload_user_id',$this->session->userdata('user_id'));
		}
		
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
	function make_datatables_for_rejected_queue(){
	    $this->make_query_for_rejected_queue();
	    if($_POST["length"] != -1)
	    {
	        $this->db->limit($_POST['length'], $_POST['start']);
	    }

	    $query = $this->db->get();
	    // echo $this->db->last_query();
	    // die();
	    return $query->result();
	}
	function get_filtered_data_for_rejected_queue(){
	    $this->make_query_for_rejected_queue();
	    $query = $this->db->get();
	    return $query->num_rows();
	}
	
	function get_all_data_for_rejected_queue(){
	    $this->db->select("*");
	    $this->db->from($this->table);
	    return $this->db->count_all_results();
	}
	
	function addNewResponceSendDownStreamData($data){
	    $this->db->insert('urs_io_json_details_responce',$data);
	    return $this->db->insert_id();
	}
  
}

