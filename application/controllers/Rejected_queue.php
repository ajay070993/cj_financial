<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rejected_queue extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjXtractUser();
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Bank_summary_level_data', 'bank_summary_level_data');
        $this->load->model('banks_model', 'banks');
    }  
  
    function index() {
        $this->load->view('rejected_queue');    
    } 

    function fetch_template_detail(){
        //print_r($_POST['start']);
        //die;
        //$this->load->model("crud_model");
        $fetch_data = $this->tpl_history->make_datatables_for_rejected_queue();
        /*echo"<pre>";
        print_r($fetch_data);
        die;*/
        $data = array();
        $startNo = $_POST['start']+1;
        $i = 0;
        foreach($fetch_data as $key=>$value)
        {

            $custormer_data = $this->bank_summary_level_data->getCustomerNameByHistoryId($value->id);
            $minutes = abs(strtotime($value->created_on) - time()) / 60;

            $sub_array = array();
            $sub_array[] = $startNo++;
            
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
            $sub_array[] = $value->business_name;
            $sub_array[] = 'Native';
           
            $sub_array[] = $value->created_on; 
            $sub_array[] = $value->upload_user_name;
            if($custormer_data!=''){
                $spreading_status = 'Done';
            }
            else if($minutes < 5 && $custormer_data==''){
                $spreading_status = 'In process';
            }
            else if($minutes > 5 && $custormer_data==''){
                $spreading_status = 'Fail';
            }
            $sub_array[] = $spreading_status;

            $workflow_status = 'Pending';
            $sub_array[] = $workflow_status;
            $data[] = $sub_array;
            
        }

        $output = array(
            "draw"                    =>     intval($_POST["draw"]),
            // "recordsTotal"          =>      $this->tpl_history->get_all_data(),
            "recordsFiltered"     =>     $this->tpl_history->get_filtered_data_for_rejected_queue(),
            "data"                    =>     $data
        );
        echo json_encode($output);
    } 
  
}