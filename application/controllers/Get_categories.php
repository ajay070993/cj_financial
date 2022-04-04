<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Get_categories extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();    
        $this->common_model->checkCjXtractUser();
        $this->user_id = $this->session->userdata('user_id'); 
        $this->load->model('Bank_categories_token', 'categories_token');
        $this->load->model('Bank_customer_txn_data', 'customer_txn_data');
    }  
  
    function index() {
        /*$data = array();
        $data['caseId'] = 547;
        $jsonData = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://127.0.0.1:8092/bank_statements_integration/submit-spreading-details?caseId=".$data['caseId'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR=> true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$jsonData,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        if ($response === false){
            $response = curl_error($curl);
            //echo $curlErrNo = curl_errno($curl)."<br>";
            //echo $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE)."<br>";
        }
        echo $response;
        curl_close($curl);
        die('here');*/
        
        $history_id = $this->input->get('id');
        if($history_id){
            $result = $this->categories_token->getTokenByHistoryId($history_id);
        }else{
            $result = $this->categories_token->getToken();
        }
        if(count($result)>0){
            echo$token = $result->token;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://52.250.20.165:5000/get_categories",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "hash-key: ".$token
                ),
            ));
            
            $response = curl_exec($curl);
            echo json_decode($response);
            curl_close($curl);
            //print_r(json_decode($response));
            $results = json_decode($response);
            //echo"<pre>";
            //print_r($results);
            $q = 0;
            foreach($results as $value){
                $q = 1;
                $data = array();
                $data['level_1'] = strtolower($value->categories);
                $this->customer_txn_data->updateCategories($value->id,$data);
            }
            if($q==1){
                $data = array();
                $data['status'] = 1;
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->categories_token->updateToken($token,$data);
            }
        }
    } 
    
}