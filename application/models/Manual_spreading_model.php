<?php
class Manual_spreading_model extends CI_Model
{

	function insertSummaryDataExcel($data){
		$this->db->insert('tbl_bank_statement_summary_level_data', $data);
		return $this->db->insert_id();
	}
	
	function updateSummaryDataExcel($data){
	    $this->db->where('id',$user_id);
	    $result = $this->db->update(tbl_bank_statement_summary_level_data,$data);
	    //echo $this->db->last_query();
	    //die;
	    return $result;
	}

	function insertTxnDataExcel($data){
		$this->db->insert('tbl_customer_txn_data', $data);
		return $this->db->insert_id();
	}

	function getTransactionDataForSpread($history_id)
	{
		$this->db->select('tbl_customer_txn_data.id,tbl_customer_txn_data.history_id,tbl_customer_txn_data.file_no,tbl_customer_txn_data.description,tbl_customer_txn_data.check_no,tbl_customer_txn_data.txn_date,tbl_customer_txn_data.type,tbl_customer_txn_data.txn_amt,tbl_customer_txn_data.timestamp,tbl_customer_txn_data.level_1,tbl_customer_txn_data.currency as txn_currency,tbl_bank_statement_summary_level_data.currency,tbl_bank_statement_summary_level_data.open_balance,tbl_tpl_history.unique_id,tbl_tpl_history.business_name');
		$this->db->join('tbl_bank_statement_summary_level_data','tbl_bank_statement_summary_level_data.file_no = tbl_customer_txn_data.file_no and tbl_bank_statement_summary_level_data.history_id=tbl_customer_txn_data.history_id','INNER');
		$this->db->join('tbl_tpl_history','tbl_tpl_history.id = tbl_customer_txn_data.history_id','LEFT');
	    $this->db->where('tbl_customer_txn_data.history_id',$history_id);
	    $this->db->order_by('tbl_customer_txn_data.id', 'asc');
	    $query = $this->db->get('tbl_customer_txn_data');
	    $result = $query->result();
	    return $result;
	}

	function getSummaryLevelDataForSpread($history_id)
	{
		$this->db->select('bank_sumry.id,bank_sumry.history_id,bank_sumry.file_no,bank_sumry.account_number,bank_sumry.account_holder_name,bank_sumry.account_type,bank_sumry.name_of_bank,bank_sumry.bank_address,bank_sumry.bank_city,bank_sumry.bank_state,bank_sumry.bank_zip,bank_sumry.current_balance,bank_sumry.start_date,bank_sumry.end_date,bank_sumry.open_balance,bank_sumry.closing_balance,bank_sumry.total_deposits,bank_sumry.count_deposits,bank_sumry.total_withdrawals,bank_sumry.count_withdrawals,bank_sumry.transaction_all_level_spreading_done,bank_sumry.native_vs_non_native,bank_sumry.check_sum,,tbl_tpl_history.unique_id,tbl_tpl_history.business_name');
		$this->db->join('tbl_tpl_history','tbl_tpl_history.id = bank_sumry.history_id','LEFT');
	    $this->db->where('bank_sumry.history_id',$history_id);
	    // $this->db->order_by('bank_sumry.start_date', 'desc');
	    // $this->db->order_by('STR_TO_DATE(bank_sumry.start_date, "%m/%d/%Y")','desc');
	    //$this->db->limit(3);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data as bank_sumry');
	    // $this->db->query(" ".$this->db->last_query()." ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y') DESC LIMIT 3 ");
	    $this->db->query(" ".$this->db->last_query()." ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y') DESC ");
	    $res_query = $this->db->query("SELECT * FROM (".$this->db->last_query().") AS `table` ORDER BY account_number, STR_TO_DATE(start_date, '%m/%d/%Y') ASC");
	    $result = $res_query->result();
	    return $result;
	}

	function getMinStartDateOfSummary($history_id)
	{
		// $this->db->select('bank_sumry.id,bank_sumry.start_date,bank_sumry.end_date');
	 //    $this->db->where('bank_sumry.history_id',$history_id);
	 //    $this->db->order_by('bank_sumry.start_date', 'desc');
	 //    $this->db->limit(3);
	 //    $query = $this->db->get('tbl_bank_statement_summary_level_data as bank_sumry');
	 //    $res_query = $this->db->query("SELECT * FROM (".$this->db->last_query().") AS `table` ORDER by start_date ASC");
	 //    $result = $res_query->row();
	 //    return $result;

	    $this->db->select('bank_sumry.id,bank_sumry.start_date,bank_sumry.end_date');
	    $this->db->where('bank_sumry.history_id',$history_id);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data as bank_sumry');
	    $this->db->query(" ".$this->db->last_query()." ORDER BY STR_TO_DATE(start_date, '%m/%d/%Y') DESC LIMIT 3 ");
	    $res_query = $this->db->query("SELECT * FROM (".$this->db->last_query().") AS `table` ORDER BY account_number, STR_TO_DATE(start_date, '%m/%d/%Y') ASC");
	    $result = $res_query->row();
	    return $result;
	}

	function getFileNumbeForTxnData($file_no,$history_id)
	{
		$this->db->select('SUM(count_deposits) + SUM(count_withdrawals) as total', FALSE);
	    $this->db->where('file_no',$file_no);
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data');
	    $result = $query->row();
	    return $result;
	}

	function getMaxFileNumbeFromSumryData($history_id)
	{
		$this->db->select('MAX(file_no) as max_file_no');
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data');
	    $result = $query->row();
	    return $result;
	}

	function updateTransactionDataRecordForFileNumber($history_id){
		$this->load->model('Tpl_history_model', 'tpl_history');
		$txn_data = $this->getTransactionDataForSpread($history_id);
        $max_file_no = $this->getMaxFileNumbeFromSumryData($history_id); 
        $input = array();
        if($max_file_no->max_file_no > 1){
        	$input['type'] = 'multiple';
        } 
        else{
        	$input['type'] = 'single';
        }
        $this->tpl_history->updateRecords($history_id,$input);
        $search_index = 0;
        for($file_no=1; $file_no<=$max_file_no->max_file_no; $file_no++)
        {
            $count_number_txn = $this->manual_spreading->getFileNumbeForTxnData($file_no,$history_id);
            if($count_number_txn->total){
                $searchData['limit'] = $count_number_txn->total;
                $searchData['search_index'] = $search_index; 
                $sql = "select id from tbl_customer_txn_data where history_id = ".$history_id." order by id asc limit ".$searchData['search_index'].",".$searchData['limit'];
		        $query = $this->db->query($sql);
		        if($query->num_rows()>0){
		            foreach($query->result()as $row){
			            $update_data = "update tbl_customer_txn_data set file_no = ".$file_no." where id = ".$row->id;
						$this->db->query($update_data);
		            }
		            $search_index = $search_index+$count_number_txn->total; 
		        }  
            }
        }
        if($file_no==$max_file_no->max_file_no){
        	return true;
        }
	}


	function deleteRecordsSummaryData($history_id){
		$this->db->where('history_id',$history_id);
		$this->db->delete('tbl_bank_statement_summary_level_data');
	}

	function deleteRecordsTxnData($history_id){
		$this->db->where('history_id',$history_id);
		$this->db->delete('tbl_customer_txn_data');
	}
	
	function getRecordsSummaryLevelData($history_id,$account_number,$startDate){
	    $this->db->select('id,history_id,account_number,start_date');
	    $this->db->where('history_id',$history_id);
	    $this->db->where('account_number',$account_number);
	    $this->db->where('start_date',$startDate);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data');
	    $result = $query->result();
	    return $result;
	}

	function updateRecordForTxnData($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update('tbl_customer_txn_data', $data);
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}

	function updateRecordForSummaryData($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update('tbl_bank_statement_summary_level_data', $data);
		$affected_rows = $this->db->affected_rows();
		return $affected_rows;
	}

	function getTxnAmountTypeForSummary($history_id,$file_no)
	{
		$this->db->select('tbl_customer_txn_data.id,tbl_customer_txn_data.txn_amt,tbl_customer_txn_data.type');
	    $this->db->where('file_no',$file_no);
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get('tbl_customer_txn_data');
	    $result = $query->result();
	    return $result;
	}

	function getPreviousDataForSummary($history_id,$file_no)
	{
		$this->db->select('id,open_balance,total_deposits,total_withdrawals,closing_balance,account_number');
	    $this->db->where('file_no',$file_no);
	    $this->db->where('history_id',$history_id);
	    $query = $this->db->get('tbl_bank_statement_summary_level_data');
	    $result = $query->row();
	    return $result;
	}
	
	function getJsonResponceForStatus($history_id){
	    $this->db->select('*');
	    $this->db->where('history_id',$history_id);
	    $this->db->order_by('id','desc');
	    $query = $this->db->get('urs_io_json_details_responce');
	    $result = $query->row();
	    // echo $this->db->last_query();
	    // die();
	    return $result;
	}

}
