<?php
class Category_summary_model extends CI_Model
{

	function insertCategorySummaryData($data){
		$this->db->insert('tbl_categories_summary', $data);
		return $this->db->insert_id();
	}

	function insertConsolidatedCategorySummaryData($data){
		$this->db->insert('tbl_categories_consolidated', $data);
		return $this->db->insert_id();
	}

	function deleteConsolidatedCategoryData($history_id){
	    $this->db->where('history_id',$history_id);
	    $this->db->delete('tbl_categories_consolidated');
	}
	
	function deleteCategorySummaryData($history_id){
	    $this->db->where('history_id',$history_id);
	    $this->db->delete('tbl_categories_summary');
	}

}
