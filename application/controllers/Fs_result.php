<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Fs_result extends CI_Controller
{
    function __construct()
    {
        Parent::__construct();
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjFinancialUser();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Bank_summary_level_data', 'bank_summary_level_data');
        $this->load->model('banks_model', 'banks');
        $this->load->model('Case_error_log_model', 'case_error_log');
        $this->load->model('Bank_customer_txn_data', 'bank_customer_txn_data');
        $this->load->model('Tpl_user_model', 'tpl_user');
        $this->load->model('Fs_history_model', 'fs_history');
        $this->load->library('encryption');
        $this->encryption->initialize(
            array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ01wrr'
            )
        );
    }

    function index()
    {
        // $this->load->view('fs_dashboard');
    }

    function sendToOther()
    {
        $input = array();
        $input['click_to_send'] = $this->input->get('click_to_send');

        $id = $this->input->get('history_id');
        // $histResult = $this->tpl_history->getSingleRecordById($id);
        /*echo"<pre>";
        print_r($histResult);
        die('here');*/
        // if($this->session->userdata('user_role') == 4 || $this->session->userdata('user_role') == 5){
        // echo"<pre>";print_r($input_history);
        // print_r($id);die;
        $affected_rows = $this->fs_history->caseSubmitToQA($id, $input);

        if ($affected_rows > 0) {
            $output['success'] = true;
            echo json_encode($output);
            die();
        }
        // }
    }

    function submitToQa()
    {
        $input_history = array();
        $input_history['submit_by_qa'] = $this->input->get('submit_to_qa');
        $id = $this->input->get('history_id');
        // $max_file_no = $this->manual_spreading->getMaxFileNumbeFromSumryData($id); 
        // for($file_no=1; $file_no<=$max_file_no->max_file_no; $file_no++)
        // {

        if ($this->session->userdata('user_role') == 4 || $this->session->userdata('user_role') == 5) {
            // echo"<pre>";print_r($input_history);
            // print_r($id);die;
            $affected_rows = $this->fs_history->caseSubmitToQA($id, $input_history);

            if ($affected_rows > 0) {
                $output['success'] = true;
                echo json_encode($output);
                die();
            }
        }

        //     $data = array();
        //     $data['summary'] = $this->bank_summary_level_data->fetchSummaryLevelDataForCategorization($id);
        //     foreach($data['summary'] as $key => $value){
        //         //$data['summary'][$key]->Se10 = '';
        //         $data['summary'][$key]->account_number = openssl_decrypt(base64_decode($data['summary'][$key]->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($data['summary'][$key]->account_number);
        //         $accountNumber = $data['summary'][$key]->account_number;
        //         $data['summary'][$key]->account_holder_name = openssl_decrypt(base64_decode($data['summary'][$key]->account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($data['summary'][$key]->account_holder_name);
        //     }
        //     $data['data'] = $this->customer_txn_data->fetchCustomerTxnDataForCategorization($id);

        //     foreach($data['data'] as $key => $value){
        //         foreach($value as $k=>$v){
        //             //$data['data'][$key]->Se10 = '';
        //             $data['data'][$key]->account_number = $accountNumber;
        //         }
        //     }
        //     $jsonData = json_encode($data);
        //     $curl = curl_init();
        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => "http://localhost/categorization",
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS =>$jsonData,
        //         CURLOPT_HTTPHEADER => array(
        //             "Content-Type: application/json"
        //         ),
        //     ));
        //     $response = curl_exec($curl);
        //     if ($response === false){
        //         $response = curl_error($curl);
        //     }
        //     curl_close($curl);
        //     $input = array();
        //     $input['token'] = $response;
        //     $input['status'] = 0;
        //     $input['history_id'] = $id;
        //     $input['created_at'] = date('Y-m-d H:i:s');
        //     $this->db->insert('tbl_categories_token',$input);

        // //}

        // $affected_rows = $this->tpl_history->updateRecords($id,$input_history);
        // if($affected_rows > 0){
        //     $output['success']= true;
        //     echo json_encode($output); die();     
        // }
    }

    function setCollapseIcon(){        
        $data_type = $this->input->get('data_type'); 
        if($data_type==0){
            $data_type = 1;
        }
        else if($data_type==1){
            $data_type = 0;
        }
        $this->session->set_userdata('data-type-collapse',$data_type);
        $data['success']= true;
        echo json_encode($data); die();        
    }
}
