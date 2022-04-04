<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        // if($this->session->userdata('application_type') == 'fs'){
        //     redirect('fs-dashboard');
        // }
        $this->common_model->checkCjXtractUser();
        $this->user_id = $this->session->userdata('user_id'); 
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Bank_summary_level_data', 'bank_summary_level_data');
        $this->load->model('banks_model', 'banks');
        $this->load->model('Case_error_log_model', 'case_error_log');
        $this->load->model('Bank_customer_txn_data', 'bank_customer_txn_data');
        $this->load->model('Tpl_user_model', 'tpl_user');
        $this->load->library('encryption');
        $this->encryption->initialize(
            array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ0'
            )
        );
    }  
  
    function index() {
        //$output['allHistory'] = $this->tpl_history->getAllHistoryRecords();
        $output['countTemplate'] = $this->banks->countTemplate();
        $output['countSpreading'] = $this->tpl_history->countSpreading();
        $output['countLastWeek'] = $this->tpl_history->countLastWeek();
        $output['avgSpreadTime'] = $this->tpl_history->avgSpreadTime();
        $this->load->view('dashboard',$output);    
    } 
    
    function fetch_template_detail(){
        //print_r($_POST['start']);
        //die;
        //$this->load->model("crud_model");
        $fetch_data = $this->tpl_history->make_datatables();
        /*echo"<pre>";
        print_r($fetch_data);
        die;*/
        $data = array();
        $startNo = $_POST['start']+1;
        foreach($fetch_data as $key=>$value)
        {

        $custormer_data = $this->bank_summary_level_data->getCustomerNameByHistoryId($value->id);
        // $txn_data = $this->bank_customer_txn_data->fetchCustomerTxnDataForCategorization($value->id);
        //$case_error_log_data = $this->case_error_log->getRecordByHistoryId($value->id);

        $minutes = abs(strtotime($value->created_on) - time()) / 60;
        $sub_array = array();
        $sub_array[] = $startNo++;
        
        /*if($value->type=='single'){
            if($value->original_pdf_file_name==""){
                $original_pdf_file_name = $value->file_name;
                
            }else{
                $original_pdf_file_name = $value->original_pdf_file_name;
            } 
            //$sub_array[] = '<td><a href="'.$this->config->item('assets').'uploads/bank_statement/'.$value->file_name.'" title="Download" target="_blank">'.substr($original_pdf_file_name,0,20).'</a></td>';
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.' ').'" title="Download" target="_blank">'.$value->unique_id.'</a></td>';
                           
        }else if($value->type=='multiple'){ 
            //$sub_array[] = '<td><a href="'.$this->config->item('assets').'uploads/bulk_upload/'.$value->folder_name.'/'.$value->original_pdf_file_name.'" title="Download" target="_blank">'.substr($value->original_pdf_file_name,0,20).'</a></td>';
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download" target="_blank">'.$value->unique_id.'</a></td>';
        }*/
        // $custormer_data = '';
        if($this->session->userdata('user_role')==1 || $this->session->userdata('user_role')==2){
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
        }else if($value->qa_user_id==$this->session->userdata('user_id')){
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
        }else{
            $sub_array[] = '<td>'.$value->unique_id.'</td>';
        }
        $sub_array[] = $value->business_name;
        $sub_array[] = ($custormer_data->native_vs_non_native=='')?'':$custormer_data->native_vs_non_native;
       
        $sub_array[] = $value->created_on; 
        $sub_array[] = $value->upload_user_name;
        // if($custormer_data!=''){
        //     $spreading_status = 'Done';
        // }
        // else if($minutes < 5 && $custormer_data==''){
        //     $spreading_status = 'In process';
        // }
        // else if($minutes > 5 && $custormer_data==''){
        //     $spreading_status = 'Fail';
        // }
        if($value->status==NULL || $value->status==0){
            if(!empty($custormer_data) && !empty($value->txn_id)){
                $spreading_status = 'Done';
            }
            else if($minutes < 1 && (empty($custormer_data) || empty($value->txn_id))){
                $spreading_status = 'Ready for execute';
            }
            else if($minutes > 1 && (empty($custormer_data) || empty($value->txn_id))){
                $spreading_status = 'Fail';
            }
        }
        else{
            if($value->status==2 && !empty($custormer_data) && !empty($value->txn_id)){
                $spreading_status = 'Done';
            }
            else if($value->status==1){
                $spreading_status = 'In process';
            }
            else if($value->status==2 && (empty($custormer_data) || empty($value->txn_id))){
                $spreading_status = 'Fail';
            }
        }
        $sub_array[] = $spreading_status;

        if($value->log_id){
            $ac_num = explode(',', $value->ac_num);
            $hol_name = explode(',', $value->hol_name);
            $ac_type = explode(',', $value->ac_type);
            $bn_nm = explode(',', $value->bn_nm);
            $bn_add = explode(',', $value->bn_add);
            $bn_cty = explode(',', $value->bn_cty);
            $bn_st = explode(',', $value->bn_st);
            $bn_zp = explode(',', $value->bn_zp);
            $curr_bal = explode(',', $value->curr_bal);
            $st_dt = explode(',', $value->st_dt);
            $en_dt = explode(',', $value->en_dt);
            $clo_bal = explode(',', $value->clo_bal);
            $chk_sm = explode(',', $value->chk_sm);
            $tpl_nt_fn = explode(',', $value->tpl_nt_fn);
            if(in_array(0, $ac_num) || in_array(0, $hol_name) || in_array(0, $ac_type) || in_array(0, $bn_nm) || in_array(0, $bn_add) || in_array(0, $bn_cty) || in_array(0, $bn_st) || in_array(0, $bn_zp) || in_array(0, $curr_bal) || in_array(0, $st_dt) || in_array(0, $en_dt) || in_array(0, $clo_bal) || in_array(0, $chk_sm) || in_array(0, $tpl_nt_fn)){
                $sub_array[] = 'Yes';
            }
            else{
                $sub_array[] = 'No';
            }
        }
        else{
            $sub_array[] = 'Not Found';
        }

        if($value->submit_by_qa=='0'){
            $workflow_status = 'Spreading';
        }else if($value->click_to_send=='1' && $value->success_type=='success'){
            //if($value->success_type=='success'){
                $workflow_status = 'Complete';
            //}
        }else if($value->click_to_send=='1' && $value->success_type=='error'){
            $workflow_status = 'Rejected-downstream';
        }else if($value->submit_by_qa=='1'){
            $workflow_status = 'QA';
        }
        $sub_array[] = $workflow_status;
        

        //$sub_array[] = '';
        if($this->common_model->checkUserPermission(17,false)) {
            if($value->qa_user_id==0 && $value->submit_by_qa=='1'){
                $sub_array[] = '<button type="button" class="assigned_to_me" id="assigend_'.$value->id.'" data-id="'.$value->id.'" style="font-size: 10px !important;">Assign to me</button>';
            }else{
                $sub_array[] = $value->qa_user_name;
                
            }
        }
        //$sub_array[] = 'Done';//$value->type;
        //$sub_array[] = '';
        /*if($value->downloaded_file_name!="" && $value->type=='single'){
            if($this->session->userdata('user_id')!=3){
                $sub_array[] = '<a href="'.$this->config->item('assets').'uploads/bank_statement_excel/'.$value->downloaded_file_name.'" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
            }else{
                $sub_array[] = '<a href="Bank_statement/createExcel/'.$value->id.'" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
            }
         }else if($value->type=='multiple'){ 
            if($value->status==0){
                $sub_array[] = 'Ready for execution';
            }else if($value->status==1){
                $sub_array[] = 'In progress';
            }else if($value->status==2){
                $sub_array[] = '<a href="'.$this->config->item('assets').'uploads/bulk_upload/'.$value->folder_name.'/'.$value->folder_name.'.zip" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
             } 
         }*/
       
        //$sub_array[] = '<a href="#" title="Download Input"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>';
        
            
        //$sub_array[] = '<a href="Bank_statement/createExcel/'.$value->id.'" title="Download Output"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>';
        //$sub_array[] = '<a href="javascript:void(0)">Refresh</a>';
        if( ($value->status==NULL || $value->status==0) && $value->type=='multiple' && empty($custormer_data)){
            $sub_array[] = '<td><a href="'.base_url('bulk-upload-spread/'.$value->id).'" target="_blank" title="Refresh" style="background: #007bff;color: #fff;padding: 4px;font-size: 13px;border-radius: 5px;">Extract</a></td>';
        }
        else{
            $sub_array[] = '';
        }
        $data[] = $sub_array;
    }
    $output = array(
        "draw"                    =>     intval($_POST["draw"]),
        "recordsTotal"          =>      '',//$this->tpl_history->get_all_data(),
        "recordsFiltered"     =>     $this->tpl_history->get_filtered_data(),
        "data"                    =>     $data
    );
    echo json_encode($output);
}  
    
    function assigned_case(){
        $output = array();
        $id = $this->input->get('history_id'); 
        $error = '';
        $output['assigned']= false;
        $output['name'] = '';
        if($id){
            $tplHistRecord = $this->tpl_history->getSingleRecordById($id);
            if($tplHistRecord->qa_user_id==0){
                $assignData = array();
                $assignData['qa_user_id'] = $this->session->userdata('user_id');
                $affected_rows = $this->tpl_history->updateRecords($id,$assignData);
                if($affected_rows==1){
                    $userDetails = $this->tpl_user->editUser($this->session->userdata('user_id'));
                    $output['name'] = $userDetails->first_name.' '.$userDetails->last_name;
                    $output['assigned']= true;
                    $error = 'Case assigned to you successfully';
                }else{
                    $output['assigned']= false;
                    $error = "Something went wrong!";
                }
            }
        }else{
            $error = "Something went wrong!";
        }
        
        
        $output['html']= $error;
        $output['success']= true;
        echo json_encode($output); die(); 
    }
}

