<?php
class Updated_category_model extends CI_Model
{

	// function insertSummaryDataExcel($data){
	// 	$this->db->insert('tbl_bank_statement_summary_level_data', $data);
	// 	return $this->db->insert_id();
	// }

	function insertCategoryUpdated($data){
		$this->db->insert('tbl_updated_category', $data);
		return $this->db->insert_id();
	}

	// function updateRecordForTxnData($id,$data)
	// {
	// 	$this->db->where('id',$id);
	// 	$this->db->update('tbl_customer_txn_data', $data);
	// 	$affected_rows = $this->db->affected_rows();
	// 	return $affected_rows;
	// }

	

}
