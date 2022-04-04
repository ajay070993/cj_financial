<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Templates extends CI_Controller {
    function __construct() {
        Parent::__construct();
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjXtractUser();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('bank_statement_model', 'bank_statement');
        $this->load->model('banks_model', 'banks');
    }
    
    function index() {
        $output['data'] = $this->bank_statement->getTemplatesData();
        $this->load->view('template',$output);
    }
    
    function fetch_template_detail(){
        // print_r($_POST['start']);
        //die;
        //echo "hello";
        //$this->load->model("crud_model");
        $fetch_data = $this->banks->make_datatables();
        /*echo"<pre>";
         print_r($fetch_data);
         die;*/
        $data = array();
        $startNo = $_POST['start']+1;
        foreach($fetch_data as $key=>$value)
        {
            $sub_array = array();
            $sub_array[] = $startNo++;
            $sub_array[] = $value->bank_name;
            $sub_array[] = $value->created_on;
            $sub_array[] = $value->updated_on;
            $sub_array[] = $value->uses_count;
            $sub_array[] = '<button data-toggle="modal" data-target="#cloneTplModal" data="'.$value->id.'" class="clone">Clone</button>';
            //$sub_array[] = '';
            
            $sub_array[] = '<a href="'.site_url('Templates/editTemplates').'/'.$value->id.'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.119 22"><path class="a" d="M21.484 13.373a.58.58 0 00-.58.58v5.148a1.741 1.741 0 01-1.739 1.739H2.9a1.741 1.741 0 01-1.739-1.739V3.994A1.741 1.741 0 012.9 2.254h5.146a.58.58 0 100-1.159H2.9a2.9 2.9 0 00-2.9 2.9v15.106a2.9 2.9 0 002.9 2.9h16.265a2.9 2.9 0 002.9-2.9v-5.148a.58.58 0 00-.581-.58zm0 0"/><path class="a" d="M9.065 10.323l8.465-8.465 2.73 2.73-8.462 8.465zm0 0M7.684 14.434l3.017-.836-2.181-2.181zm0 0M21.014.423a1.451 1.451 0 00-2.05 0l-.615.615 2.73 2.73.615-.615a1.451 1.451 0 000-2.05zm0 0"/></svg></a>';
            $sub_array[] = '';
            
            
            $data[] = $sub_array;
        }
        $output = array(
            "draw"                    =>     intval($_POST["draw"]),
            "recordsTotal"          =>      $this->banks->get_all_data(),
            "recordsFiltered"     =>     $this->banks->get_filtered_data(),
            "data"                    =>     $data
        );
        echo json_encode($output);
        
    }
    
    function editTemplates($bank_id) {
        $output['page_title'] = '';
        $output['edit_template']=true;
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        //$output['bank_detail'] = $this->bank_statement->getSingleRecordByBankId($bank_id);
        $output['bank_id'] = $bank_id;
        $this->load->view('editTemplate',$output,$bank_id);
    }
    
    function createTemplates() {
        //print_r($_POST);
        //die('nirdesh');
        $output['page_title'] = '';
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        if(isset($_POST) && !empty($_POST)){
            $output['create_template']=false;
            $output['convert_text_file'] = $this->input->post('convert_text_file');
            $output['upload_pdf_file'] = $this->input->post('upload_pdf_file');
            $output['original_pdf_file_name'] = $this->input->post('original_pdf_file_name');
        }else{
            $output['create_template']=true;
            $output['convert_text_file'] = '';
            $output['upload_pdf_file'] = '';
            $output['original_pdf_file_name'] = '';
        }
        $this->load->view('createTemplate',$output);
    }
    
    function cloneTemplate(){
        
        $output['page_title'] = 'Login';
        if (isset($_POST) && !empty($_POST)) {
            
            $clone_temp = $this->input->post('clone_temp');
            $bank_id = $this->input->post('bank_id');
            $lastId = $this->bank_statement->cloneTemplate($bank_id,$clone_temp);
            $output['callBackFunction'] = 'closePopup';
            $output['last_id'] = $lastId;
            echo json_encode($output); die;
        }
        $output['data'] = $this->bank_statement->getTemplatesData();
        $this->load->view('template',$output);
        
    }
}