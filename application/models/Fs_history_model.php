<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fs_history_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->table = 'tbl_cj_financial_data';
		$this->signalTable = 'tbl_cj_fs_signals';
		$this->historyTable = 'tbl_financial_history';
		$this->userTable = 'tbl_users';
		$this->BlkUpldTable = 'tbl_bulk_upload';
	}

	function addNewRecords($data)
	{
		$this->db->insert($this->historyTable, $data);
		//echo $this->db->last_query();
		//die('her');
		return $this->db->insert_id();
	}

	function addNewRecordsBulkUpld($data)
	{
		$this->db->insert($this->BlkUpldTable, $data);
		return $this->db->insert_id();
	}
        function updateRFCNumber($rfc_number,$history_id){
            $this->db->where('id', $history_id);
            $result = $this->db->update($this->historyTable, $rfc_number);
        }
        function updateAnalystinCases($case_ids,$data){
            $this->db->where_in('id', explode(',', $case_ids));
            $result = $this->db->update($this->historyTable, $data);
        }
        function updatePreCheckDetails($data,$history_id){
            $this->db->where('id', $history_id);
            $result = $this->db->update($this->historyTable, $data);
        }
	function addNewRecordsInCjData($data, $id, $i)
	{
		// first check if row exist in history table 
		$this->db->select("count(id) as count");
		$this->db->where('id', $id);
		$this->db->where('application_type', 'fs');
		$query = $this->db->get($this->historyTable);
		// echo $this->db->last_query();die;
		$res = ($query->row_array());
		if ($res['count'] <= 0) {
			die;
			
		} else {
			$this->db->select("count(col_year) as rowcount");
			$this->db->where('history_id', $id);
			$this->db->where('col_year', $i+1);
			// $this->db->where('application_type','fs');
			$query = $this->db->get($this->table);
			// echo $this->db->last_query();die;
			if ($query->row_array()['rowcount'] < 1) {
				$result = $this->db->insert($this->table, $data);
				// echo "this";
				return $result;
				//  die;

			} else {
				// echo "thisq";
				// print_r($data);
				$i = $i + 1;
				$this->db->where('history_id', $id);
				$this->db->where('col_year', "$i");
				$result = $this->db->update($this->table, $data);
				// echo  $this->db->last_query();
				// die;
				return $result;
				//  echo 12451;die;
			}
		}




		// $this->db->select("count(col_year) as rowcount");
		// $this->db->where('history_id',$id);
		// // $this->db->where('application_type','fs');
		// $query = $this->db->get($this->table);
		// // echo $this->db->last_query();
		//  echo json_encode($query->row_array());

		//  die;

		// $cnt = $query->row_array();
		// return $cnt;
		// // return $cnt;


		// $this->db->insert($this->table,$data);
		// // return $this->db->last_query();
		// //die('her');
		// return $this->db->insert_id();
		// $i = $i +1;
		// $this->db->where('history_id', $id);
		// $this->db->where('col_year', "$i");
		// $result = $this->db->update($this->table,$data);
		// // echo  $this->db->last_query();
		// // die;
		// return $result;
	}

	function saveBluCogCmnts($data, $id){
		// $i = 0;
		// print_r($data);die;
			$this->db->select("count(history_id) as rowcount");
			$this->db->where('history_id', $id);
			// $this->db->where('col_year', $i+1);
			// $this->db->where('application_type','fs');
			$query = $this->db->get("tbl_fs_blucog_comments");
			// echo $this->db->last_query();die;
			if ($query->row_array()['rowcount'] < 1) {
				$result = $this->db->insert("tbl_fs_blucog_comments", $data);
				// echo "this";
				return $result;
				//  die;

			} else {
				// echo "thisq";
				// print_r($data);
				// $i = $i + 1;
				$this->db->where('history_id', $id);
				// $this->db->where('col_year', "$i");
				$result = $this->db->update("tbl_fs_blucog_comments", $data);
				// echo  $this->db->last_query();
				// die;
				return $result;
				//  echo 12451;die;
			}
		// }
	}

	function addNewRecordsInCjSignals($data, $id, $i)
	{
		// first check if row exist in history table 
		$this->db->select("count(id) as count");
		$this->db->where('id', $id);
		$this->db->where('application_type', 'fs');
		$query = $this->db->get($this->historyTable);
		// echo $this->db->last_query();die;
		$res = ($query->row_array());
		if ($res['count'] <= 0) {
			die;
			
		} else {
			$this->db->select("count(col_year) as rowcount");
			$this->db->where('history_id', $id);
			$this->db->where('col_year', $i+1);
			// $this->db->where('application_type','fs');
			$query = $this->db->get($this->signalTable);
			// echo $this->db->last_query();die;
			if ($query->row_array()['rowcount'] < 1) {
				$result = $this->db->insert($this->signalTable, $data);
				// echo "this";
				return $result;
				//  die;

			} else {
				// echo "thisq";
				// print_r($data);
				$i = $i + 1;
				$this->db->where('history_id', $id);
				$this->db->where('col_year', "$i");
				$result = $this->db->update($this->signalTable, $data);
				// echo  $this->db->last_query();
				// die;
				return $result;
				//  echo 12451;die;
			}
		}
	}

	function getSingleRecordById($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->historyTable);
		$result = $query->row();
		return $result;
	}

	function updateRecords($id,$data){
		$this->db->where('id',$id);
		$this->db->update($this->historyTable,$data);
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}

	function lastUpdatedTime($id){
		// SELECT max(modified_on) FROM `tbl_cj_financial_data` WHERE history_id=2

		$this->db->select("max(modified_on) as lastUpdated");
		$this->db->where('history_id', $id);
		// $this->db->where('application_type','fs');
		$query = $this->db->get($this->table);
		// echo $this->db->last_query();die;
		return $query->row_array()['lastUpdated'];

	}
	function fsHistoryBizName($id)
	{
		// SELECT unique_id, business_name FROM `tbl_financial_history` where id=4850 and application_type="fs"
		$user_role = $this->session->userdata('user_role');

		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->where('application_type', 'fs');
		if ($user_role == 6) {
			// user role 6 for fs QA
			$this->db->group_start();
			$this->db->where('tbl_financial_history.submit_by_qa', '1');
			$this->db->or_where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
			$this->db->group_end();
		}
		$query = $this->db->get($this->historyTable);
		// echo $this->db->last_query();die;

		$cnt = $query->row_array();
		// print_r($cnt);die;
		if(!isset($cnt['unique_id'])){
			show_404();
			die;
		}
		return $cnt;
		// return $cnt;
	}

	function getCjFinancialData($id)
	{
		// SELECT unique_id, business_name FROM `tbl_financial_history` where id=4850 and application_type="fs"
		$this->db->select('*');
		$this->db->where('history_id', $id);
		$this->db->order_by('col_year', 'ASC');
		$query = $this->db->get($this->table);
			$cnt = $query->row_array();
		return $query->result_array;
		// return $cnt;
	}

	function getBluCogCmnts($id)
	{
		// SELECT unique_id, business_name FROM `tbl_financial_history` where id=4850 and application_type="fs"
		$this->db->select('*');
		$this->db->where('history_id', $id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get("tbl_fs_blucog_comments");
			$cnt = $query->row_array();
		return $query->result_array;
		// return $cnt;
	}

	
	function getSignalsData($id)
	{
		// SELECT unique_id, business_name FROM `tbl_financial_history` where id=4850 and application_type="fs"
		$this->db->select('*');
		$this->db->where('history_id', $id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get($this->signalTable);
			$cnt = $query->row_array();
		return $query->result_array;
		// return $cnt;
	}

	function make_query_old()
	{
		$user_role = $this->session->userdata('user_role');

		$this->db->select('tbl_financial_history.id,tbl_financial_history.bank_id,tbl_financial_history.file_name,original_pdf_file_name,tbl_financial_history.created_on,tbl_financial_history.qa_user_id,downloaded_file_name,tbl_financial_history.type,tbl_bulk_upload.folder_name,tbl_bulk_upload.file_name as m_file,tbl_bulk_upload.status,CONCAT_WS(" ", tbl_users.first_name,  tbl_users.last_name) AS upload_user_name,CONCAT_WS(" ", u1.first_name,  u1.last_name) AS qa_user_name,upload_user_id,unique_id,business_name,submit_by_qa,click_to_send,b.success_type,c.txn_id,group_concat(concat(" ", d.id, " ")) AS log_id,group_concat(concat(" ", d.account_number, " ")) AS ac_num,group_concat(concat(" ", d.aaccount_holder_name, " ")) AS hol_name,group_concat(concat(" ", d.account_type, " ")) AS ac_type,group_concat(concat(" ", d.name_of_bank, " ")) AS bn_nm,group_concat(concat(" ", d.bank_address, " ")) AS bn_add,group_concat(concat(" ", d.bank_city, " ")) AS bn_cty,group_concat(concat(" ", d.bank_state, " ")) AS bn_st,group_concat(concat(" ", d.bank_zip, " ")) AS bn_zp,group_concat(concat(" ", d.current_balance, " ")) AS curr_bal,group_concat(concat(" ", d.start_date, " ")) AS st_dt,group_concat(concat(" ", d.end_date, " ")) AS en_dt,group_concat(concat(" ", d.closing_balance, " ")) AS clo_bal,group_concat(concat(" ", d.check_sum, " ")) AS chk_sm,group_concat(concat(" ", d.tpl_not_found, " ")) AS tpl_nt_fn');
		//$this->db->join('tbl_banks', 'tbl_financial_history.bank_id = tbl_banks.id','left');
		$this->db->join('tbl_bulk_upload', 'tbl_financial_history.id = tbl_bulk_upload.history_id', 'left');
		$this->db->join('tbl_users', 'tbl_financial_history.upload_user_id = tbl_users.id', 'left');
		$this->db->join('tbl_users u1', 'tbl_financial_history.qa_user_id = u1.id', 'left');
		//$this->db->join('urs_io_json_details_responce', 'tbl_financial_history.id = urs_io_json_details_responce.history_id','left');

		$subquery = 'SELECT history_id,success_type FROM urs_io_json_details_responce where id in (SELECT MAX(id)
	                FROM urs_io_json_details_responce
	                GROUP BY history_id)
	                ) as';
		$this->db->join("($subquery  b", "`tbl_financial_history`.`id` = b.history_id", 'left');

		$subquery1 = 'SELECT max(id) as txn_id,history_id FROM tbl_customer_txn_data where 1 GROUP BY history_id) as';
		$this->db->join("($subquery1  c", "`tbl_financial_history`.`id` = c.history_id", 'left');

		$this->db->join('tbl_case_error_log d', 'tbl_financial_history.id = d.history_id', 'left');

		$this->db->from($this->historyTable);
		if ($user_role == 5) {
			// user role 5 for fs analysts
			$this->db->where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
		}
		// condition for cj financial
		$this->db->where('tbl_financial_history.application_type', $this->session->userdata('application_type'));
		/*else if($user_role==3){
	    	$this->db->where('tbl_financial_history.submit_by_qa','1');
		}*/
		if (isset($_POST["search"]["value"])) {

			$this->db->group_start();
			$this->db->like('original_pdf_file_name', trim($_POST['search']['value']));
			//$this->db->or_like('tbl_banks.bank_name', trim($_POST['search']['value']));
			$this->db->or_like('tbl_financial_history.created_on', trim($_POST['search']['value']));
			$this->db->or_like('downloaded_file_name', trim($_POST['search']['value']));
			$this->db->or_like('tbl_financial_history.type', trim($_POST['search']['value']));
			$this->db->group_end();
		}

		if (isset($_POST["order"])) {
			$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$this->db->order_by('tbl_financial_history.id', 'DESC');
		}

		//Assigned to me filter 
		if (isset($_POST["assigned_filter"]) && $_POST["assigned_filter"] != 'All') {
			//die("here");
			if ($_POST["assigned_filter"] == 'Assigned to me') {
				$this->db->where('tbl_financial_history.qa_user_id', $this->session->userdata('user_id'));
			} else if ($_POST["assigned_filter"] == 'Unassigned') {
				$this->db->where('tbl_financial_history.qa_user_id', '0');
			}
		}
		//end assigned to me filter 

		//workflow filter
		if (isset($_POST["workflow_filter"]) && $_POST["workflow_filter"] != 'All') {
			//echo $_POST["workflow_filter"];die;
			if ($_POST["workflow_filter"] == 'Spreading') {
				$this->db->where('tbl_financial_history.submit_by_qa', '0');
			}
			if ($_POST["workflow_filter"] == 'Completed') {
				$this->db->where('tbl_financial_history.click_to_send', '1');
				$this->db->where('b.success_type', 'success');
			}

			if ($_POST["workflow_filter"] == 'Rejected-downstream') {
				$this->db->where('tbl_financial_history.click_to_send', '1');
				$this->db->where('b.success_type', 'error');
			} else if ($_POST["workflow_filter"] == 'Qa') {
				$this->db->where('tbl_financial_history.submit_by_qa', '1');
				$this->db->where('tbl_financial_history.click_to_send', '0');
			}
		}

		//this is done because it is always as submit_by_qa 
		else if ($user_role == 6) {
			// user role 6 for fs QA
			$this->db->where('tbl_financial_history.submit_by_qa', '1');
		}
		//end workflow filter
		$this->db->group_by('tbl_financial_history.id');
	}

	function make_query()
	{
		$user_role = $this->session->userdata('user_role');

		$this->db->select('tbl_financial_history.id,tbl_financial_history.status,tbl_financial_history.case_type,tbl_financial_history.bank_id,tbl_financial_history.file_name,original_pdf_file_name,tbl_financial_history.created_on,tbl_financial_history.qa_user_id,downloaded_file_name,tbl_financial_history.type,CONCAT_WS(" ", tbl_users.first_name,  tbl_users.last_name) AS upload_user_name,CONCAT_WS(" ", u1.first_name,  u1.last_name) AS qa_user_name,upload_user_id,unique_id,business_name,submit_by_qa,click_to_send');
		//$this->db->join('tbl_banks', 'tbl_financial_history.bank_id = tbl_banks.id','left');
		// $this->db->join('tbl_bulk_upload', 'tbl_financial_history.id = tbl_bulk_upload.history_id', 'left');
		$this->db->join('tbl_users', 'tbl_financial_history.analyst_id = tbl_users.id', 'left');
		$this->db->join('tbl_users u1', 'tbl_financial_history.qa_user_id = u1.id', 'left');
		//$this->db->join('urs_io_json_details_responce', 'tbl_financial_history.id = urs_io_json_details_responce.history_id','left');

		// $subquery = 'SELECT history_id,success_type FROM urs_io_json_details_responce where id in (SELECT MAX(id)
	    //             FROM urs_io_json_details_responce
	    //             GROUP BY history_id)
	    //             ) as';
		// $this->db->join("($subquery  b", "`tbl_financial_history`.`id` = b.history_id", 'left');

		// $subquery1 = 'SELECT max(id) as txn_id,history_id FROM tbl_customer_txn_data where 1 GROUP BY history_id) as';
		// $this->db->join("($subquery1  c", "`tbl_financial_history`.`id` = c.history_id", 'left');

		// $this->db->join('tbl_case_error_log d', 'tbl_financial_history.id = d.history_id', 'left');

		$this->db->from($this->historyTable);
		if ($user_role == 5) {
			// user role 5 for fs analysts
			// $this->db->where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
		}
		// condition for cj financial
		// $this->db->where('tbl_financial_history.application_type', $this->session->userdata('application_type'));
		/*else if($user_role==3){
	    	$this->db->where('tbl_financial_history.submit_by_qa','1');
		}*/
		if (isset($_POST["search"]["value"])) {

			$this->db->group_start();
			$this->db->like('original_pdf_file_name', trim($_POST['search']['value']));
			//$this->db->or_like('tbl_banks.bank_name', trim($_POST['search']['value']));
			$this->db->or_like('tbl_financial_history.created_on', trim($_POST['search']['value']));
			$this->db->or_like('downloaded_file_name', trim($_POST['search']['value']));
			$this->db->or_like('tbl_financial_history.type', trim($_POST['search']['value']));
			$this->db->group_end();
		}

		if (isset($_POST["order"])) {
			$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$this->db->order_by('tbl_financial_history.id', 'DESC');
		}

		//Assigned to me filter 
		if (isset($_POST["assigned_filter"]) && $_POST["assigned_filter"] != 'All') {
			//die("here");
			if ($_POST["assigned_filter"] == 'Assigned to me') {
				$this->db->where('tbl_financial_history.qa_user_id', $this->session->userdata('user_id'));
			} else if ($_POST["assigned_filter"] == 'Unassigned') {
				$this->db->where('tbl_financial_history.qa_user_id', '0');
			}
		}
		//end assigned to me filter 

		//workflow filter
		if (isset($_POST["workflow_filter"]) && $_POST["workflow_filter"] != 'All') {
			//echo $_POST["workflow_filter"];die;
//			if ($_POST["workflow_filter"] == 'Spreading') {
//				$this->db->where('tbl_financial_history.submit_by_qa', '0');
//				// echo 1;
//			}
//			if ($_POST["workflow_filter"] == 'Completed') {
//				$this->db->where('tbl_financial_history.click_to_send', '1');
//				// $this->db->where('b.success_type', 'success');
//				// echo 2;
//			}
//
//			if ($_POST["workflow_filter"] == 'Rejected-downstream') {
//				$this->db->where('tbl_financial_history.click_to_send', '1');
//				// $this->db->where('b.success_type', 'error');
//				// echo 3;
//			} else if ($_POST["workflow_filter"] == 'Qa') {
//				$this->db->where('tbl_financial_history.submit_by_qa', '1');
//				$this->db->where('tbl_financial_history.click_to_send', '0');
//				// echo 4;
//			}
                     if ($_POST["workflow_filter"] == 'New') {
				$this->db->where('tbl_financial_history.status', 0);
			}
                        if ($_POST["workflow_filter"] == 'Accepted') {
				$this->db->where('tbl_financial_history.status', 1);
			}
                        if ($_POST["workflow_filter"] == 'Rejected') {
				$this->db->where('tbl_financial_history.status', 2);
			}
                        if ($_POST["workflow_filter"] == 'System-Fail') {
				$this->db->where('tbl_financial_history.status', 3);
			}
                        if ($_POST["workflow_filter"] == '2nd Review') {
				$this->db->where('tbl_financial_history.status', 4);
			}
		}

		//this is done because it is always as submit_by_qa 
		 if ($user_role == 6) {
			// user role 6 for fs QA
			$this->db->group_start();
			$this->db->where('tbl_financial_history.submit_by_qa', '1');
			$this->db->or_where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
			$this->db->group_end();
		}
                
                if ($user_role == 5) {
                    $this->db->where_in('tbl_financial_history.status', [0,6]);
                }
		//end workflow filter
		$this->db->group_by('tbl_financial_history.id');
	}
	function make_datatables()
	{
		//die("hello");
		$this->make_query();
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		// die();
		return $query->result();
	}

	function getAllUsers()
	{
		// application_type = 'fs'
		$this->db->select('id,first_name,last_name,email,gender,type,status');
		$this->db->where('application_type', 'fs');
		$query = $this->db->get($this->userTable);
		$result = $query->result();
		return $result;
		// echo $this->db->last_query();
		// die;

	}
        
        function getAllAnalysts()
	{
		// application_type = 'fs'
		$this->db->select('id,first_name,last_name,email,gender,type,status');
		$this->db->where('application_type', 'fs');
                $this->db->where('user_role', '5');
		$query = $this->db->get($this->userTable);
		$result = $query->result();
		return $result;
		// echo $this->db->last_query();
		// die;

	}

	function get_filtered_data()
	{
		$this->make_query();
		$query = $this->db->get();
		// echo $this->db->last_query();
		// die();
		return count($query->result());


		// $user_role = $this->session->userdata('user_role');
		// $this->db->select('count(*)');
		// // condition for cj financial
		// $this->db->where('application_type', $this->session->userdata('application_type'));

		// $query = $this->db->get($this->historyTable);
		// $cnt = $query->row_array();
		// return $cnt['count(*)'];

		// if ($user_role == 5) {
		// 	// user role 5 for fs analysts
		// 	// $this->db->where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
		// }
		// if ($user_role == 6) {
		// 	// user role 6 for fs QA
		// 	$this->db->where('tbl_financial_history.submit_by_qa', '1');
		// 	$this->db->or_where('tbl_financial_history.upload_user_id', $this->session->userdata('user_id'));
		// }


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
	function caseSubmitToQA($id,$data){
			$this->db->where('id',$id);
			$this->db->update($this->historyTable,$data);
			$affected_rows = $this->db->affected_rows();
			return $affected_rows;
	}


}
