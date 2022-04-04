<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bulk_upload_model extends CI_Model {
	function __construct() { 
		parent::__construct();
		$this->table = 'tbl_bulk_upload';
	}	
	
	function addNewRecords($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}
	
	function checkSpreadInProgress(){
	    $ql = $this->db->select('id')->from($this->table)->where('status','1')->get();
	    return $ql->num_rows();
	}
	
	function getLastUploadedFile(){
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
	
	function updateStatusSpreadFile($id,$data){
	    $this->db->where('id',$id);
	    $result = $this->db->update($this->table,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	    
	}
	
	function getRecordByHashkey($hashkey){
	    $this->db->select('hashkey,folder_name,file_name');
	    $this->db->where('hashkey',$hashkey);
	    $this->db->where('status','2');
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    //echo $this->db->last_query();
	    //die('here');
	    return $result;
	}
	
	function getRecordByFolderName($newFolderName){
	    $this->db->select('*');
	    $this->db->where('folder_name',$newFolderName);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    //echo $this->db->last_query();
	    //die('here');
	    return $result;
	}
	
	function updateRecordByFolderName($newFolderName,$data){
	    $this->db->where('folder_name',$newFolderName);
	    $result = $this->db->update($this->table,$data);
	    return $result;
	}

	function getSingleRecordByHistoryId($history_id){
		$this->db->select('id,folder_name,status');
		$this->db->where('history_id',$history_id);
		$query = $this->db->get($this->table);
		$result = $query->row();
		return $result;
	}

	function getLatestFileForExtractionByHistoryId($history_id){
	    $this->db->select('*');
	    $this->db->where('status','0');
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get($this->table);
	    $result = $query->row();
	    // echo $this->db->last_query();
	    // die;
	    return $result;
	}
  
}