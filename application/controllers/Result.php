<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Result extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjXtractUser();
        $this->load->model('Manual_spreading_model', 'manual_spreading');
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Banks_model', 'banks');
        $this->load->model('Bank_summary_level_data', 'bank_summary_level_data');
        $this->load->model('Bank_customer_txn_data', 'customer_txn_data');
        $this->load->model('Category_summary_model', 'category_summary');
        $this->load->model('Bulk_upload_model', 'bulk_upload');
        $this->load->model('Bank_statement_model', 'bank_statement');
        $this->load->model('Case_error_log_model', 'case_error_log');
        $this->load->model('Updated_category_model', 'updated_category');
        $this->load->model('Data_validation_check', 'validation_check');
        $this->load->library('excel');
        $this->load->library('encryption');
        $this->encryption->initialize(
            array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ0'
            )
        );
    }  
  
    function index($id) {
        $history_data = $this->tpl_history->getSingleRecordById($id);
        $bulk_upload_data = $this->bulk_upload->getSingleRecordByHistoryId($id);
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        $category_count = 0;
        $txn_data = $this->manual_spreading->getTransactionDataForSpread($id);
        foreach ($txn_data as $key => $value) {
            $txnLevel_1 = trim($value->level_1);
            if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                $category_count++;
                break;
            }
        }   

    	//$summary_level_data = $this->manual_spreading->getSummaryLevelDataForSpread($id);
        if($history_data->upload_process=='manual'){
            $summary_level_data = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }else{
            $summary_level_data = $this->bank_summary_level_data->fetchSummaryLevelData($id);
        }
        foreach ($summary_level_data as $key => $value) {
            $value->account_number = openssl_decrypt(base64_decode($value->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($value->account_number);
            $value->account_holder_name = openssl_decrypt(base64_decode($value->account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($value->account_holder_name);
        }

        $case_error_log_data = $this->case_error_log->getRecordByHistoryId($id);
        $error_count = 0;
        if($case_error_log_data){

            if($case_error_log_data[0]->tpl_not_found==0){
                $error_count++;
            }
            else{
                foreach($case_error_log_data as $log){
                    if($log->account_number==0){
                        $error_count++;
                    }
                    if($log->aaccount_holder_name==0){
                        $error_count++;
                    }
                    if($log->account_type==0){
                        $error_count++;
                    }
                    if($log->name_of_bank==0){
                        $error_count++;
                    }
                    if($log->bank_address==0){
                        $error_count++;
                    }
                    if($log->bank_city==0){
                        $error_count++;
                    }
                    if($log->bank_state==0){
                        $error_count++;
                    }
                    if($log->bank_zip==0){
                        $error_count++;
                    }
                    if($log->current_balance==0){
                        $error_count++;
                    }
                    if($log->start_date==0){
                        $error_count++;
                    }
                    if($log->end_date==0){
                        $error_count++;
                    }
                    if($log->closing_balance==0){
                        $error_count++;
                    }
                    if($log->check_sum==0){
                        $error_count++;
                    }
                }
            } 
        }
    
        $minutes = abs(strtotime($history_data->created_on) - time()) / 60;
        
        // if(count(array_filter($summary_level_data)) > 0){
        //     $spreading_status = 'Done';
        // }
        // else if($minutes < 5 && count(array_filter($summary_level_data)) == 0){
        //     $spreading_status = 'In process';
        // }
        // else if($minutes > 5 && count(array_filter($summary_level_data)) == 0){
        //     $spreading_status = 'Fail';
        // }

        if($bulk_upload_data->status==NULL || $bulk_upload_data->status==0){
            if(!empty($summary_level_data) && !empty($txn_data)){
                $spreading_status = 'Done';
            }
            else if($minutes < 1 && (empty($summary_level_data) || empty($txn_data))){
                $spreading_status = 'Ready for execute';
            }
            else if($minutes > 1 && (empty($summary_level_data) || empty($txn_data))){
                $spreading_status = 'Fail';
            }
        }
        else{
            if($bulk_upload_data->status==2 && !empty($summary_level_data) && !empty($txn_data)){
                $spreading_status = 'Done';
            }
            else if($bulk_upload_data->status==1){
                $spreading_status = 'In process';
            }
            else if($bulk_upload_data->status==2 && (empty($summary_level_data) || empty($txn_data))){
                $spreading_status = 'Fail';
            }
        }

        if($history_data->submit_by_qa=='0'){
            $workflow_status = 'Spreading';
        }
        else if($history_data->click_to_send=='1'){
            $workflow_status = 'Completed';
        }
        else if($history_data->submit_by_qa=='1'){
            $workflow_status = 'QA';
        }
        
        $json_responce = $this->manual_spreading->getJsonResponceForStatus($id);
        if(count($json_responce)>0){
            //echo "<pre>";
            $json_response_data = json_decode($json_responce->RESPONSE_JSON, true);
            //echo $json_response_data['code'];
            if($json_response_data['code']==200){
                $workflow_status = 'completed';
            }else{
                $workflow_status = 'rejected-downstream';
            }
            /*echo $json_responce->RESPONSE_JSON;
            echo "<pre>";
            print_r($json_responce);
            die('here');*/
            //die;
        }
        
        $validateResults = $this->validation_check->getAllDataValidationRecord($history_data->id);
        
        $output['error_count'] = $error_count; 
        $output['category_count'] = $category_count; 
        $output['bulk_upload_folder_name'] = $bulk_upload_data->folder_name; 
        $output['history_type'] = $history_data->type;  
        $output['original_pdf_file_name'] = $history_data->original_pdf_file_name;
        $output['history_file_name'] = $history_data->file_name;
        $output['excel_file_name'] = $history_data->downloaded_file_name;
        $output['submit_by_qa'] = $history_data->submit_by_qa;
        $output['click_to_send'] = $history_data->click_to_send;
        $output['unique_id'] = $history_data->unique_id;
        $output['business_name'] = $history_data->business_name;
        $output['workflow_status'] = $workflow_status;
        $output['spreading_status'] = $spreading_status;
        $output['history_id'] = $id; 
    	$output['summary_level_data'] = $summary_level_data;
    	$output['json_responce'] = $json_responce;
    	$output['validate_results'] = $validateResults;
        $this->load->view('result',$output);    
    } 

    function getAjaxCategory(){
        $id = $this->input->get('history_id');  
        $crAmtArray = array();
        $drAmtArray = array();
        $totalCrAmtArray = array();
        $totalDrAmtArray = array();
        $htmlArray = array();
        $getMonthName = "";
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        $fileSerialNum = 1;
        $histResult = $this->tpl_history->getSingleRecordById($id);
        
        //if($this->session->userdata('email')=='shubha.joshi@ollosoft.com'){
        /*if($histResult->upload_process=='manual'){
            $results = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }else{
            $results = $this->bank_summary_level_data->fetchSummaryLevelData($id);
        }*/
        $results = $this->bank_summary_level_data->fetchSummaryLevelDataCategory($id);
        /*}else{
            $results = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }*/
        
        /*echo"<pre>";
        print_r($results);
        echo "</pre>";
        die;*/
        $checkChangeMonth = 1;
        $getChangeForMidMonth = array();
        $categoryTrigger = false;
        foreach ($results as $key => $value) {
            if(empty($getChangeForMidMonth)){
                array_push($getChangeForMidMonth,$value->start_date);
            }
            
            if(!in_array($value->start_date, $getChangeForMidMonth)){
                array_push($getChangeForMidMonth,$value->start_date);
                $categoryTrigger = true;
            }
            /**Set logic for mid month*/
            $split_start_date = $value->start_date;
            
            if (strpos($split_start_date, '/') !== false) {
                $splitStartdate = explode("/", $split_start_date);
                $splitStartMonthName = $splitStartdate[0];
            }else if(strpos($split_start_date, '-') !== false) {
                $splitStartdate = explode("-", $split_start_date);
                $splitStartMonthName = $splitStartdate[0];
            }
            
            $split_end_date = $value->end_date;
            if (strpos($split_end_date, '/') !== false) {
                $splitEnddate = explode("/", $split_end_date);
                $splitEndMonthName = $splitEnddate[0];
            }else if(strpos($split_end_date, '-') !== false) {
                $splitEnddate = explode("-", $split_end_date);
                $splitEndMonthName = $splitEnddate[0];
            }
            //echo $splitStartMonthName."</br>";
            //echo $splitEndMonthName."</br>";
            //die;
            $midMonth = false;
            if((int)$splitStartMonthName!=(int)$splitEndMonthName){
                //die('here');
                $midMonth = true;
            }
            $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($id,$value->file_no);
            if(count($customerTxns)>0){
                foreach($customerTxns as $txn){
                    $txn_date = $txn->txn_date;
                    if($midMonth==false){
                        if($getMonthName==""){
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getMonthName = $thedate[0];
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getMonthName = $thedate[0];
                            }
                        }else{
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getTxnMonthName = $thedate[0];
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getTxnMonthName = $thedate[0];
                            }
                            
                            
                            if($getMonthName!=$getTxnMonthName){
                                $output['crAmtArray'] = $crAmtArray;  
                                $output['drAmtArray'] = $drAmtArray; 
                                $output['totalCrAmtArray'] = $totalCrAmtArray;  
                                $output['totalDrAmtArray'] = $totalDrAmtArray; 
                                $output['fileSerialNum'] = $fileSerialNum;  
                                $html = $this->load->view('ajax_categories_list',$output, true); 
                                array_push($htmlArray, $html);
                                $fileSerialNum++;
                                $crAmtArray = array();
                                $drAmtArray = array();
                                $getMonthName = $getTxnMonthName;
                            }                
                        }
                    }else{
                        //if($checkChangeMonth!=$key+1){
                        if($categoryTrigger){
                            $output['crAmtArray'] = $crAmtArray;
                            $output['drAmtArray'] = $drAmtArray;
                            $output['totalCrAmtArray'] = $totalCrAmtArray;
                            $output['totalDrAmtArray'] = $totalDrAmtArray;
                            $output['fileSerialNum'] = $fileSerialNum;
                            $html = $this->load->view('ajax_categories_list',$output, true);
                            array_push($htmlArray, $html);
                            $fileSerialNum++;
                            $checkChangeMonth++;
                            $crAmtArray = array();
                            $drAmtArray = array();
                            $categoryTrigger = false;
                            $getMonthName = $getTxnMonthName;
                        }
                    }
                    $txnLevel_1 = trim($txn->level_1);
                    //$txnLevel_2 = trim($txn->level_2);
                    if($txn->type=='cr'){
                       
                        $open_balance = $open_balance+$txn->txn_amt;
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                            if($crAmtArray[$txnLevel_1]){
                                $crAmtArray[$txnLevel_1]['count'] =  $crAmtArray[$txnLevel_1]['count']+1;
                                $crAmtArray[$txnLevel_1]['amt'] = $crAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $crAmtArray[$txnLevel_1]['count'] =  1;
                                $crAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                            if($totalCrAmtArray[$txnLevel_1]){
                                $totalCrAmtArray[$txnLevel_1]['count'] =  $totalCrAmtArray[$txnLevel_1]['count']+1;
                                $totalCrAmtArray[$txnLevel_1]['amt'] = $totalCrAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $totalCrAmtArray[$txnLevel_1]['count'] =  1;
                                $totalCrAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        // if(in_array($txnLevel_2, $credit_array)){
                            
                        // }
                        
                    }else{

                        $open_balance = $open_balance-$txn->txn_amt;
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $debit_array))){
                            if($drAmtArray[$txnLevel_1]){
                                $drAmtArray[$txnLevel_1]['count'] =  $drAmtArray[$txnLevel_1]['count']+1;
                                $drAmtArray[$txnLevel_1]['amt'] = $drAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $drAmtArray[$txnLevel_1]['count'] =  1;
                                $drAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $debit_array))){
                            if($totalDrAmtArray[$txnLevel_1]){
                                $totalDrAmtArray[$txnLevel_1]['count'] =  $totalDrAmtArray[$txnLevel_1]['count']+1;
                                $totalDrAmtArray[$txnLevel_1]['amt'] = $totalDrAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $totalDrAmtArray[$txnLevel_1]['count'] =  1;
                                $totalDrAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        // if(in_array($txnLevel_2, $debit_array)){
                            
                        // }
                    }
                    
                }
                
            }
        }
       
        $output['crAmtArray'] = $crAmtArray;  
        $output['drAmtArray'] = $drAmtArray; 
        $output['fileSerialNum'] = $fileSerialNum;   
        $html = $this->load->view('ajax_categories_list',$output, true);
        array_push($htmlArray, $html);
        $crAmtArray = array();
        $drAmtArray = array(); 
    
        $fileSerialNumConsolidated = $fileSerialNum+1;
        $output1['totalCrAmtArray'] = $totalCrAmtArray;  
        $output1['totalDrAmtArray'] = $totalDrAmtArray; 
        $output1['fileSerialNum1'] = $fileSerialNumConsolidated;
        $html = $this->load->view('ajax_categories_consolidated',$output1, true);
        array_push($htmlArray, $html);
        $data['html'] = $htmlArray;
        $data['fileSerialNum'] = $fileSerialNum;
        $data['fileSerialNumConsolidated'] = $fileSerialNumConsolidated;
        $data['success']= true;
        echo json_encode($data); die(); 
    }

    function getAjaxTranscation(){
        //$txn_data = array();
        $id = $this->input->get('history_id');
        $histResult = $this->tpl_history->getSingleRecordById($id);
        /*echo"<pre>";
        print_r($histResult);
        echo"</pre>";
        die;*/
        if(count($histResult)==1){
            $uniqueId = $histResult->unique_id;
        }else{
            $uniqueId = "";
        }
        
        if($histResult->upload_process=='manual'){
            $results = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }else{
            $results = $this->bank_summary_level_data->fetchSummaryLevelData($id);
        }
        
        //$txn_data = $this->manual_spreading->getTransactionDataForSpread($id);
        //$min_start_date = $this->manual_spreading->getMinStartDateOfSummary($id);
        $q = 0;
        $i = 0;
        $data = array();
        foreach($results as $key=>$result){
            $file_no = $result->file_no;
            $data[$i]->account_number = openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());;
            $data[$i]->open_balance = $result->open_balance;
            $data[$i]->unique_id = $uniqueId;
            $data[$i]->business_name = $histResult->business_name;
            $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($id,$file_no);
            foreach ($customerTxns as $key => $value) {
                //$signle_record_summry = $this->manual_spreading->getPreviousDataForSummary($id,$value->file_no);
                //$txn_data[$q]->account_number = openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($signle_record_summry->account_number);
                $data[$i]->txn_data[$q]->id = $value->id;
                $data[$i]->txn_data[$q]->history_id = $value->history_id;
                $data[$i]->txn_data[$q]->file_no = $value->file_no;
                $data[$i]->txn_data[$q]->description = $value->description;
                $data[$i]->txn_data[$q]->check_no = $value->check_no;
                $data[$i]->txn_data[$q]->txn_date = $value->txn_date;
                $data[$i]->txn_data[$q]->txn_amt = $value->txn_amt;
                $data[$i]->txn_data[$q]->txn_currency = $value->currency;
                $data[$i]->txn_data[$q]->type = $value->type;
                $data[$i]->txn_data[$q]->level_1 = $value->level_1;
                $data[$i]->txn_data[$q]->category_updated = $value->category_updated;
                $data[$i]->txn_data[$q]->category_updated_level_2 = $value->category_updated_level_2;
                $data[$i]->txn_data[$q]->timestamp = $value->timestamp;
                //$txn_data[$q]->open_balance = $result->open_balance;
                //$txn_data[$q]->unique_id = $uniqueId;
                //$txn_data[$q]->business_name = $result->business_name;
                $txnLevel_1 = trim($value->level_1);
                if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                    $category_count++;
                }
                $q++;
            }  
            $i++;
            
        }
         /*echo"<pre>";
         print_r($data);
         echo"</pre>";
         die;*/
        $output['history_id'] = $id;
        $output['txn_tab_data'] = $data;
        //$output['min_start_date'] = $min_start_date->start_date;
        $html = $this->load->view('ajax_transaction_data',$output, true); 
        $data['html'] = $html; 
        $data['success']= true;
        echo json_encode($data); die(); 
    } 

    function uploadCorrectExcel($history_id)
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        //$this->db->trans_begin();
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        $output['page_title'] = 'Convert File';
        $output['message']    = '';
        $output['success'] = true;

        $checkDateFormat = false;
        $isTxnDescription = false;
        $isTxnDate = false;
        $isTxnAmt = false;
        $betweenTxnDate = false;
        
        $img_arr = array();
        if(isset($_FILES['exl_file_name']['name']) && $_FILES['exl_file_name']['name']) { 
            $path = $_FILES["exl_file_name"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            /*print_r($_SESSION);
            echo $this->session->userdata('email');
            die;*/
            /*New Code */
            
                $this->validation_check->deleteAllRecords($history_id);
                $worksheet = $object->getSheet(1);
                $all_count_deposit = 0;
                $all_count_withdrawal = 0;
                $start_index = 2;
                $highestRow = $worksheet->getHighestRow();
                for($d=2;$d<=$highestRow;$d++){
                    if(trim($worksheet->getCellByColumnAndRow(1, $d)->getValue())!="" || trim($worksheet->getCellByColumnAndRow(2, $d)->getValue())!="" || trim($worksheet->getCellByColumnAndRow(3, $d)->getValue())!=""){
                        
                        $data_validation_check =array();
                        $data_validation_check['history_id'] = $history_id;
                        $data_validation_check['is_txn_description'] = 1;
                        $data_validation_check['is_txn_date'] = 1;
                        $data_validation_check['is_txn_amt'] = 1;
                        $data_validation_check['date_format'] = 1;
                        $data_validation_check['txn_date'] = 1;
                        $data_validation_check['currency'] = 1;
                        
                        
                        $worksheet = $object->getSheet(1);
                        $open_balance = $worksheet->getCellByColumnAndRow(12, $d)->getValue();
                        $close_balance = $worksheet->getCellByColumnAndRow(13, $d)->getValue();
                        
                        $count_deposits = $worksheet->getCellByColumnAndRow(15, $d)->getValue();
                        $all_count_deposit = $all_count_deposit + $count_deposits;
                        
                        $count_withdrawls = $worksheet->getCellByColumnAndRow(17, $d)->getValue();
                        $all_count_withdrawal = $all_count_withdrawal + $count_withdrawls;
                        
                        $start_date = $worksheet->getCellByColumnAndRow(10, $d)->getValue();
                        $end_date = $worksheet->getCellByColumnAndRow(11, $d)->getValue();
                        //echo "Total Txn";
                        //echo "\n";
                        $totalTxns = $all_count_deposit + $all_count_withdrawal;
                        //echo "\n";
                        $currentTotalRow = $count_deposits+$count_withdrawls;
                        //echo $count_deposits."\n";
                        //echo $count_withdrawls."\n";
                        
                        $total_deposits = $worksheet->getCellByColumnAndRow(14, $d)->getValue();
                        $total_withdrawals = $worksheet->getCellByColumnAndRow(16, $d)->getValue();
                        
                        if(trim($start_date)==""){
                            $data_validation_check['start_date'] = 0;
                        }else{
                            $data_validation_check['start_date'] = 1;
                        }
                        
                        if(trim($end_date)==""){
                            $data_validation_check['end_date'] = 0;
                        }else{
                            $data_validation_check['end_date'] = 1;
                        }
                        
                        $start_date_value = $start_date;
                        if(is_float($start_date_value)){
                            $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($start_date_value);
                            $start_date_data = $phpDateTimeObject->format('m/d/Y');
                        }else{
                            $start_date_data = $start_date_value;
                        }
                        
                        if (strpos($start_date_data, '/') !== false) {
                            list($month, $day, $year) = explode("/", $start_date_data);
                        }
                        
                        if (strpos($start_date_data, '-') !== false) {
                            list($month, $day, $year) = explode("-", $start_date_data);
                        }
                        
                        if((!checkdate($month, $day, $year) || $year<100) && $checkDateFormat==false){
                            $data_validation_check['date_format'] = 0;
                            $checkDateFormat = true;
                        }
                        
                        $end_date_value = $end_date;
                        if(is_float($end_date_value)){
                            $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($end_date_value);
                            $end_date_data = $phpDateTimeObject->format('m/d/Y');
                        }
                        else{
                            $end_date_data = $end_date_value;
                        }
                        
                        if (strpos($end_date_data, '/') !== false) {
                            list($month, $day, $year) = explode("/", $end_date_data);
                        }
                        
                        if (strpos($end_date_data, '-') !== false) {
                            list($month, $day, $year) = explode("-", $end_date_data);
                        }
                        
                        
                        if((!checkdate($month, $day, $year) || $year<100) && $checkDateFormat==false){
                            $data_validation_check['date_format'] = 0;
                            $checkDateFormat = true;
                        }
                        
                        if(trim($worksheet->getCellByColumnAndRow(1, $d)->getValue())==""){
                            $data_validation_check['is_acc_number'] = 0;
                        }else{
                            $data_validation_check['is_acc_number'] = 1;
                        }
                        
                        if(trim($worksheet->getCellByColumnAndRow(2, $d)->getValue())==""){
                            $data_validation_check['is_acc_holder_name'] = 0;
                        }else{
                            $data_validation_check['is_acc_holder_name'] = 1;
                        }
                        
                        
                        if(trim($worksheet->getCellByColumnAndRow(3, $d)->getValue())==""){
                            $data_validation_check['is_acc_type'] = 0;
                        }else{
                            $data_validation_check['is_acc_type'] = 1;
                        }
                        
                        
                        if(trim($worksheet->getCellByColumnAndRow(4, $d)->getValue())==""){
                            $data_validation_check['is_bank_name'] = 0;
                        }else{
                            $data_validation_check['is_bank_name'] = 1;
                        }
                        
                        
                        
                        $worksheet_0_tab = $object->getSheet(0);
                        if($d==2){
                            $highestRowTxnData = $worksheet_0_tab->getHighestRow();
                        }
                        
                        
                        $count_cr = 0;
                        $sum_cr = 0;
                        $count_dr = 0;
                        $sum_dr = 0;
                        
                        /*if($d==3){
                            echo $start_index;
                            echo "\n";
                            echo $totalTxns;
                            die;
                        }*/
                        
                        for($t=$start_index;$t<=$totalTxns+1;$t++){
                            if($highestRowTxnData>=$t){
                                //echo $txn_date = $worksheet_0_tab->getCellByColumnAndRow(7, $t)->getValue();
                                //die;
                                if($t==2){
                                    $currency = $worksheet_0_tab->getCellByColumnAndRow(8, $t)->getValue();
                                }
                                
                                if(strtolower(trim($currency))!=strtolower(trim($worksheet_0_tab->getCellByColumnAndRow(8, $t)->getValue()))){
                                    //echo $worksheet_0_tab->getCellByColumnAndRow(8, $t)->getValue();die('here');
                                    //$data_validation_check['currency'] = 0;
                                }
                                
                                $txn_date = $worksheet_0_tab->getCellByColumnAndRow(6, $t)->getValue();
                                
                                $tx_data_value = $txn_date;
                                if(is_float($tx_data_value)){
                                    $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($tx_data_value);
                                    $txn_date_data = $phpDateTimeObject->format('m/d/Y');
                                }else{
                                    $txn_date_data = $tx_data_value;
                                }
                                
                                
                                $is_txn_description = $worksheet_0_tab->getCellByColumnAndRow(4, $t)->getValue();
                                if(trim($is_txn_description)=="" && $isTxnDescription==false){
                                    $data_validation_check['is_txn_description'] = 0;
                                    $isTxnDescription = true;
                                }
                                
                                if(trim($txn_date_data)=="" && $isTxnDate==false){
                                    $data_validation_check['is_txn_date'] = 0;
                                    $isTxnDate = true;
                                }
                                
                                if (strpos($txn_date_data, '/') !== false) {
                                    list($month, $day, $year) = explode("/", $txn_date_data);
                                }
                                
                                if (strpos($txn_date_data, '-') !== false) {
                                    list($month, $day, $year) = explode("-", $txn_date_data);
                                }
                                
                                
                                if((!checkdate($month, $day, $year) || $year<100) && $checkDateFormat==false){
                                    $data_validation_check['date_format'] = 0;
                                    $checkDateFormat = true;
                                }else{
                                    if (strpos($start_date_data, '/') !== false) {
                                        $exp_start_date = explode("/",$start_date_data);
                                    }
                                    if (strpos($start_date_data, '-') !== false) {
                                        $exp_start_date = explode("-",$start_date_data);
                                    }
                                    $cn_start_date = $exp_start_date[2].'-'.$exp_start_date[0].'-'.$exp_start_date[1];
                                    
                                    if (strpos($txn_date_data, '/') !== false) {
                                        $exp_date_data = explode("/",$txn_date_data);
                                    }
                                    if (strpos($txn_date_data, '-') !== false) {
                                        $exp_date_data = explode("-",$txn_date_data);
                                    }
                                    
                                    $cn_txn_date = $exp_date_data[2].'-'.$exp_date_data[0].'-'.$exp_date_data[1];
                                    
                                    if (strpos($end_date_data, '/') !== false) {
                                        $exp_end_date = explode("/",$end_date_data);
                                    }
                                    if (strpos($end_date_data, '-') !== false) {
                                        $exp_end_date = explode("-",$end_date_data);
                                    }
                                    
                                    $cn_end_date = $exp_end_date[2].'-'.$exp_end_date[0].'-'.$exp_end_date[1];
                                }
                                
                                $is_txn_amt = $worksheet_0_tab->getCellByColumnAndRow(7, $t)->getValue();
                                if(trim($is_txn_amt)=="" && $isTxnAmt==false){
                                    $data_validation_check['is_txn_amt'] = 0;
                                    $isTxnAmt = true;
                                }
                                /*echo $cn_start_date;
                                echo "\n";
                                echo $cn_txn_date;
                                echo "\n";
                                echo $cn_end_date;
                                echo "\n";
                                die('here');*/
                                if(($cn_start_date>$cn_txn_date || $cn_txn_date>$cn_end_date) && $betweenTxnDate == false){
                                    $data_validation_check['txn_date'] = 0;
                                    $betweenTxnDate = true;
                                }
                                
                                
                                if($this->session->userdata('user_role')==3){
                                    if($worksheet_0_tab->getCellByColumnAndRow(9, $t)->getValue()!=""){
                                        $level_1 = $worksheet_0_tab->getCellByColumnAndRow(10, $t)->getValue();
                                        if(trim($level_1)==''){
                                            $output['message'] = "Row number:".$t.' Category <b>'.$level_1 ."</b> is blank";
                                            $output['success'] = false;
                                            echo json_encode($output);die;
                                        }
                                        
                                        if($worksheet->getCellByColumnAndRow(9, $t)->getValue()=='Credit'){
                                            if(!in_array(strtolower($level_1), array_map('strtolower', $credit_array))){
                                                $output['message'] = "Row number:".$t.' Category <b>'.$level_1 ."</b> is not correct";
                                                $output['success'] = false;
                                            }
                                        }else{
                                            if(!in_array(strtolower($level_1), array_map('strtolower', $debit_array))){
                                                $output['message'] = "Row number:".$t.' Category <b>'.$level_1 ."</b> is not correct";
                                                $output['success'] = false;
                                            }
                                        }
                                    }
                                }
                                
                                if($worksheet_0_tab->getCellByColumnAndRow(9, $t)->getValue()!=""){
                                    if(strtolower(trim($worksheet_0_tab->getCellByColumnAndRow(9, $t)->getValue()))==strtolower("Debit")){
                                        $sum_dr = $sum_dr + $worksheet_0_tab->getCellByColumnAndRow(7, $t)->getValue();
                                        $count_dr++;
                                    }else if(strtolower(trim($worksheet_0_tab->getCellByColumnAndRow(9, $t)->getValue()))==strtolower("Credit")){
                                        $sum_cr = $sum_cr + $worksheet_0_tab->getCellByColumnAndRow(7, $t)->getValue();
                                        $count_cr++;
                                    }
                                }
                               
                                
                                if($t==$totalTxns+1){
                                    $txnAvailBal = $worksheet_0_tab->getCellByColumnAndRow(11, $t)->getValue();
                                }
                            }
                        }
                        
                        $start_index = $totalTxns+2;
                        
                        if((int)$count_cr!=(int)$count_deposits){
                            $data_validation_check['count_cr'] = 0;
                        }else{
                            $data_validation_check['count_cr'] = 1;
                        }
                        
                        if((float)trim($sum_cr)!=(float)trim($total_deposits)){
                            $data_validation_check['total_cr'] = 0;
                        }else{
                            $data_validation_check['total_cr'] = 1;
                        }
                        
                        if((int)$count_dr!=(int)$count_withdrawls){
                            $data_validation_check['count_dr'] = 0;
                        }else{
                            $data_validation_check['count_dr'] = 1;
                        }
                        
                        if((float)trim($sum_dr)!=(float)trim($total_withdrawals)){
                            $data_validation_check['total_dr'] = 0;
                        }else{
                            $data_validation_check['total_dr'] = 1;
                        }
                        
                        /*echo (float)trim($close_balance);
                        echo "\n";
                        echo (float)trim($txnAvailBal);
                        die;*/
                        if((float)trim($close_balance)!=number_format((float)trim($txnAvailBal), 2, '.', '') && $currentTotalRow!=0){
                            $data_validation_check['closing_balance'] = 0;
                            //echo $close_balance."\n";
                            //echo $txnAvailBal."\n";
                            //die;
                        }else{
                            $data_validation_check['closing_balance'] = 1;
                        }
                        
                        if(number_format(((float)trim($open_balance)+(float)trim($sum_cr))-((float)trim($sum_dr)+(float)trim($close_balance)), 2)!=0){
                            $data_validation_check['checksum'] = 0;
                        }else{
                            $data_validation_check['checksum'] = 1;
                        }
                        //print_r($data_validation_check);
                        $this->validation_check->addRecord($data_validation_check);
                        //die('here');
                    }
                }
                $totalSumryCrDr = $count_deposit+$count_withdrawal;
                //echo $totalSumryCrDr."<br>";
                //echo $highestRowTxnData."<br>";
                /*if($highestRowTxnData-1!=$totalSumryCrDr){
                    $output['message'] = "Transactions not equal in Transaction Data and Summary Level Data";
                    $output['success'] = false;
                    echo json_encode($output);die;
                }*/
                //die('here');
           
            //die('here');
            //if($this->session->userdata('user_role')==3){
                /*foreach($object->getWorksheetIterator() as $worksheet){
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    $index_number =  $object->getIndex($worksheet);
                    
                    if($index_number==0){
                        $total_cr_dr = $highestRow;
                        for($row_txn=2; $row_txn<=$highestRow; $row_txn++){
                            if($worksheet->getCellByColumnAndRow(9, $row_txn)->getValue()!=""){
                                $level_1 = $worksheet->getCellByColumnAndRow(10, $row_txn)->getValue();
                                if(trim($level_1)==''){
                                    $output['message'] = "Row number:".$row_txn.' Category <b>'.$level_1 ."</b> is blank";
                                    $output['success'] = false;
                                    echo json_encode($output);die;
                                }
                                
                                if($worksheet->getCellByColumnAndRow(9, $row_txn)->getValue()=='Credit'){
                                    if(!in_array(strtolower($level_1), array_map('strtolower', $credit_array))){
                                        $output['message'] = "Row number:".$row_txn.' Category <b>'.$level_1 ."</b> is not correct";
                                        $output['success'] = false;
                                        echo json_encode($output);die;
                                    }
                                }else{
                                    if(!in_array(strtolower($level_1), array_map('strtolower', $debit_array))){
                                        $output['message'] = "Row number:".$row_txn.' Category <b>'.$level_1 ."</b> is not correct";
                                        $output['success'] = false;
                                        echo json_encode($output);die;
                                    }
                                }
                            }
                        }
                    }
                    
                    if($index_number==1){
                        //echo $highestRow;
                        $count_deposit = 0;
                        $count_withdrawal = 0;
                        for($row=2; $row<=$highestRow; $row++){
                            $deposit = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                            $count_deposit = $count_deposit + $deposit;
                            $withdrawal = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                            $count_withdrawal = $count_withdrawal + $withdrawal;
                            
                            
                        }
                        $total_cr_dr = $total_cr_dr-1;
                        $totalSumryCrDr = $count_deposit+$count_withdrawal;
                        //echo $total_cr_dr;
                        //echo $totalSumryCrDr;
                        //die('heress');
                        if($total_cr_dr!=$totalSumryCrDr){
                            $output['message'] = "Transactions not equal in Transaction Data and Summary Level Data";
                            $output['success'] = false;
                            echo json_encode($output);die;
                        }
                        
                    }
                }*/
            //}
            
            $file_name_unique_business = explode('_',$_FILES['exl_file_name']['name'], 2);
            $buss_nm = pathinfo($file_name_unique_business[1], PATHINFO_FILENAME);//explode('.', $file_name_unique_business[1], 2);
            //echo $buss_nm;
            //print_r($file_name_unique_business);
            //die('here');
            $img_arr['unique_id'] = $file_name_unique_business[0];
            $img_arr['business_name'] = $buss_nm;
            $img_arr['file_name'] = $_FILES['exl_file_name']['name'];
            $img_arr['upload_process'] = 'manual';
            //$img_arr['original_pdf_file_name'] = $_FILES['exl_file_name']['name'];
            /*print_r($img_arr);
            die('here');*/
            $this->tpl_history->updateRecords($history_id,$img_arr);
            $this->manual_spreading->deleteRecordsSummaryData($history_id);
            $this->case_error_log->deleteRecord($history_id);
            if($history_id){
                
                foreach($object->getWorksheetIterator() as $worksheet)
                {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    $index_number =  $object->getIndex($worksheet);
                    
                    if($index_number==1){
                        for($row=2; $row<=$highestRow; $row++)
                        {
                            $update_case_error_log = array();
                            $update_case_error_log['history_id'] = $history_id;
                            $update_case_error_log['file_no'] = $row-1;
                            //echo $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                            //die('here');
                            if($worksheet->getCellByColumnAndRow(4, $row)->getValue()!=""){
                                $bank_data = array();
                                $input_summry = array();
                                $input_summry['history_id'] = $history_id;
                                $input_summry['file_no'] = $row-1;
                                
                                if(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue())!=""){ $update_case_error_log['account_number'] = 1; }else{ $update_case_error_log['account_number'] = 0;}
                                $input_summry['account_number'] = base64_encode(openssl_encrypt(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));//$this->encryption->encrypt($worksheet->getCellByColumnAndRow(1, $row)->getValue());
                                
                                if(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue())!=""){ $update_case_error_log['aaccount_holder_name'] = 1; }else{ $update_case_error_log['aaccount_holder_name'] = 0;}
                                $input_summry['account_holder_name'] = base64_encode(openssl_encrypt(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));//$this->encryption->encrypt($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                
                                if(trim($worksheet->getCellByColumnAndRow(3, $row)->getValue())!=""){ $update_case_error_log['account_type'] = 1; }else{ $update_case_error_log['account_type'] = 0;}
                                $input_summry['account_type'] = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(4, $row)->getValue())!=""){ $update_case_error_log['name_of_bank'] = 1; }else{ $update_case_error_log['name_of_bank'] = 0;}
                                $input_summry['name_of_bank'] = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(5, $row)->getValue())!=""){ $update_case_error_log['bank_address'] = 1; }else{ $update_case_error_log['bank_address'] = 0;}
                                $input_summry['bank_address'] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(6, $row)->getValue())!=""){ $update_case_error_log['bank_city'] = 1; }else{ $update_case_error_log['bank_city'] = 0;}
                                $input_summry['bank_city'] = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(7, $row)->getValue())!=""){ $update_case_error_log['bank_state'] = 1; }else{ $update_case_error_log['bank_state'] = 0;}
                                $input_summry['bank_state'] = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(8, $row)->getValue())!=""){ $update_case_error_log['bank_zip'] = 1; }else{ $update_case_error_log['bank_zip'] = 0;}
                                $input_summry['bank_zip'] = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                                $input_summry['current_balance'] = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(9, $row)->getValue())!=""){ $update_case_error_log['closing_balance'] = 1; }else{ $update_case_error_log['closing_balance'] = 0;}
                                
                                if(trim($worksheet->getCellByColumnAndRow(10, $row)->getValue())!=""){ $update_case_error_log['start_date'] = 1; }else{ $update_case_error_log['start_date'] = 0;}
                                $start_date_value = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                                if(is_float($start_date_value)){
                                    $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($start_date_value);
                                    $start_date_data = $phpDateTimeObject->format('m/d/Y');
                                }
                                else{
                                    $start_date_data = $start_date_value;
                                }
                                $input_summry['start_date'] = $start_date_data;
    
                                if(trim($worksheet->getCellByColumnAndRow(11, $row)->getValue())!=""){ $update_case_error_log['end_date'] = 1; }else{ $update_case_error_log['end_date'] = 0;}
                                $end_date_value = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                                if(is_float($end_date_value)){
                                    $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($end_date_value);
                                    $end_date_data = $phpDateTimeObject->format('m/d/Y');
                                }
                                else{
                                    $end_date_data = $end_date_value;
                                }
                                $input_summry['end_date'] = $end_date_data;
                                $input_summry['open_balance'] = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                                
                                if(trim($worksheet->getCellByColumnAndRow(13, $row)->getValue())!=""){ $update_case_error_log['current_balance'] = 1; }else{ $update_case_error_log['current_balance'] = 0;}
                                $input_summry['closing_balance'] = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                                $input_summry['total_deposits'] = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                                $input_summry['count_deposits'] = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                                $input_summry['total_withdrawals'] = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                                $input_summry['count_withdrawals'] = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                                $input_summry['native_vs_non_native'] = 'Non Native';
                                if($worksheet->getCellByColumnAndRow(19, $row)->getValue()==NULL || $worksheet->getCellByColumnAndRow(19, $row)->getValue()==''){
                                    $check_sum = 0;
                                }else{
                                    $check_sum = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                                }
                                
                                if($check_sum==0){ $update_case_error_log['check_sum'] = 1; }else{ $update_case_error_log['check_sum'] = 0;}
                                $input_summry['check_sum'] = $check_sum;
                                $update_case_error_log['tpl_not_found'] =1;
                                $this->case_error_log->addRecord($update_case_error_log);
                                $account_number_encrypt  =  base64_encode(openssl_encrypt(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));
                                //$summaryLevelRecords  =  $this->manual_spreading->getRecordsSummaryLevelData($history_id,$account_number_encrypt,$start_date_data);
                                /*echo"<pre>";
                                echo $summaryLevelRecords[0]->id;
                                echo count($summaryLevelRecords);
                                print_r($summaryLevelRecords);
                                echo"</pre>";
                                die('here');*/
                                /*if(count($summaryLevelRecords)==1){
                                    $this->manual_spreading->updateSummaryDataExcel($summaryLevelRecords[0]->id,$input_summry);
                                }else{
                                    $this->manual_spreading->insertSummaryDataExcel($input_summry);
                                }*/
                                $this->manual_spreading->insertSummaryDataExcel($input_summry);
                                if($worksheet->getCellByColumnAndRow(4, $row)->getValue()!=''){
                                    $bank_detail = $this->banks->getBankIdByName($worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                    $bank_data['bank_id'] = $bank_detail->id;
                                    $this->tpl_history->updateRecords($history_id,$bank_data);
                                }
                            }
                        }
                    }
                    
                    if($index_number==0){
                        for($row_txn=2; $row_txn<=$highestRow; $row_txn++)
                        {
                            if($worksheet->getCellByColumnAndRow(9, $row_txn)->getValue()!=""){
                                $input_txn = array();
                                $input_txn['history_id'] = $history_id;
                                $txn_db_id = $worksheet->getCellByColumnAndRow(0, $row_txn)->getValue();
                                if($row_txn==2 && empty($txn_db_id)){
                                    $this->manual_spreading->deleteRecordsTxnData($history_id);
                                }
                                $input_txn['description'] = $worksheet->getCellByColumnAndRow(4, $row_txn)->getValue();
                                $input_txn['check_no'] = $worksheet->getCellByColumnAndRow(5, $row_txn)->getValue();
                                $data_value = $worksheet->getCellByColumnAndRow(6, $row_txn)->getValue();
                                if(is_float($data_value)){
                                    $phpDateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($data_value);
                                    $date_data = $phpDateTimeObject->format('m/d/Y');
                                }
                                else{
                                    $date_data = $data_value;
                                }
                                $input_txn['txn_date'] = $date_data;
                                $input_txn['txn_amt'] = $worksheet->getCellByColumnAndRow(7, $row_txn)->getValue();
                                $input_txn['currency'] = $worksheet->getCellByColumnAndRow(8, $row_txn)->getValue();
                                
                                if(strtolower(trim($worksheet->getCellByColumnAndRow(9, $row_txn)->getValue()))==strtolower(trim('Credit'))){
                                    $input_txn['type'] = 'cr';
                                }
                                else{
                                    $input_txn['type'] = 'dr';
                                }
                                $level_1 = $worksheet->getCellByColumnAndRow(10, $row_txn)->getValue();
                                $input_txn['level_1'] = strtolower($level_1);
    
                                if($txn_db_id){
                                    $txn_data_lvl = $this->customer_txn_data->getRecordById($txn_db_id); 
                                    $category_arr = explode('-',$level_1);
                                    $txn_category_arr = explode('-',$txn_data_lvl->level_1);
                                    if(trim($category_arr[0])=='loan repayment/emi' || trim($category_arr[0])=='travel expenses' || trim($category_arr[0])=='utilities'){
                                        if(trim($txn_category_arr[0])!=trim($category_arr[0])){
                                            $input_txn['category_updated'] =  '1';
                                            $input_cat['level_1'] = trim($txn_category_arr[0]);
                                        }
                                        if(trim($txn_category_arr[1])!=trim($category_arr[1])){
                                            $input_txn['category_updated_level_2'] =  '1';
                                            $input_cat['level_2'] = trim($txn_category_arr[1]);
                                        }
    
                                        if(trim($txn_category_arr[0])!=trim($category_arr[0]) || trim($txn_category_arr[1])!=trim($category_arr[1])){
                                            $input_cat['txn_id'] = $txn_db_id;
                                            $input_cat['updated_by'] = $this->session->userdata('user_id');
                                            $updated_cat_id = $this->updated_category->insertCategoryUpdated($input_cat);
                                        }
                                    }
                                    else{
                                        if($txn_data_lvl->level_1!=$level_1){
                                            $input_txn['category_updated'] =  '1';
                                            if(trim($txn_category_arr[0])=='loan repayment/emi' || trim($txn_category_arr[0])=='travel expenses' || trim($txn_category_arr[0])=='utilities'){
                                                $input_cat['level_1'] = trim($txn_category_arr[0]);
                                                $input_cat['level_2'] = trim($txn_category_arr[1]);
                                            }
                                            else{
                                                $input_cat['level_1'] = $txn_data_lvl->level_1;
                                            }
                                            $input_cat['txn_id'] = $txn_db_id;
                                            $input_cat['updated_by'] = $this->session->userdata('user_id');
                                            $updated_cat_id = $this->updated_category->insertCategoryUpdated($input_cat);
                                        }
                                    }
                                    $this->manual_spreading->updateRecordForTxnData($txn_db_id,$input_txn);
                                }
                                else{
                                    //
                                    $this->manual_spreading->insertTxnDataExcel($input_txn);
                                }
                            }
                        }
                    } 
                }
                
                $effected_data = $this->manual_spreading->updateTransactionDataRecordForFileNumber($history_id);
                if($effected_data==true){
                    $max_file_no = $this->getMaxFileNumbeFromSumryData($history_id); 
                    for($file_no=1; $file_no<=$max_file_no->max_file_no; $file_no++)
                    {
                        $count_number_txn = $this->manual_spreading->getFileNumbeForTxnData($file_no,$history_id);
                    }
                }

                $message = 'Record inserted successfully';
                $output['callBackFunction'] = 'callBackCommonImportExcel';
                $success = true;
            }
        }
        else 
        {
            $message = 'Please select a file';
            $success = false;
        }
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output);die;
    }

    function EditSpreadedFileData(){
        $output['page_title'] = 'Convert File';
        $output['message']    = '';

        if(isset($_POST) && !empty($_POST)){            
            $this->form_validation->set_rules('txn_data_description[]', 'Description', 'trim|required');
            $this->form_validation->set_rules('txn_data_txn_date[]', 'TXN date', 'trim|required');
            $this->form_validation->set_rules('txn_data_txn_amnt[]', 'TXN Amount', 'trim|required');
            //$this->form_validation->set_rules('txn_data_currency[]', 'Currency', 'trim|required');
            $this->form_validation->set_rules('txn_data_type[]', 'Debit/Credit Type', 'trim|required');
            if ($this->form_validation->run()) {                
                   
                $txn_data_history_id = $this->input->post('txn_data_history_id');
                $txn_data_row_id = $this->input->post('txn_data_row_id[]');
                $txn_data_file_no = $this->input->post('txn_data_file_no[]');
                $txn_data_unique_id = $this->input->post('txn_data_unique_id[]');
                $txn_data_txn_id = $this->input->post('txn_data_txn_id[]');
                $txn_data_description = $this->input->post('txn_data_description[]');
                $txn_data_check = $this->input->post('txn_data_check[]');
                $txn_data_txn_date = $this->input->post('txn_data_txn_date[]');
                $txn_data_txn_amnt = $this->input->post('txn_data_txn_amnt[]');
                $txn_data_currency = $this->input->post('txn_data_currency[]');
                $txn_data_type = $this->input->post('txn_data_type[]');
                $txn_data_level_1 = $this->input->post('txn_data_level_1[]');
                $txn_data_level_2 = $this->input->post('txn_data_level_2[]');
                $count_row = count(array_filter($txn_data_row_id));

                //$unique_id = array_unique($txn_data_unique_id);
                //$count_unique_id = count(array_filter($unique_id));
                
                /// summary data
                $summry_data_row_id = $this->input->post('summry_data_row_id[]');
                $summry_data_file_no = $this->input->post('summry_data_file_no[]');
                $summry_data_unique_id = $this->input->post('summry_data_unique_id[]');
                $summry_data_account_number = $this->input->post('summry_data_account_number[]');
                $summry_data_account_holder_name = $this->input->post('summry_data_account_holder_name[]');
                $summry_data_account_type = $this->input->post('summry_data_account_type[]');
                $summry_data_name_of_bank = $this->input->post('summry_data_name_of_bank[]');
                $summry_data_bank_address = $this->input->post('summry_data_bank_address[]');
                $summry_data_bank_city = $this->input->post('summry_data_bank_city[]');
                $summry_data_bank_state = $this->input->post('summry_data_bank_state[]');
                $summry_data_bank_zip = $this->input->post('summry_data_bank_zip[]');
                $summry_data_current_balance = $this->input->post('summry_data_current_balance[]');
                $summry_data_start_date = $this->input->post('summry_data_start_date[]');
                $summry_data_end_date = $this->input->post('summry_data_end_date[]');
                $summry_data_open_balance = $this->input->post('summry_data_open_balance[]');
                $summry_data_closing_balance = $this->input->post('summry_data_closing_balance[]'); 
                $summry_data_total_deposits = $this->input->post('summry_data_total_deposits[]');
                $summry_data_count_deposits = $this->input->post('summry_data_count_deposits[]');
                $summry_data_total_withdrawals = $this->input->post('summry_data_total_withdrawals[]');
                $summry_data_count_withdrawals = $this->input->post('summry_data_count_withdrawals[]');
                $summry_data_native_vs_non_native = $this->input->post('summry_data_native_vs_non_native[]');
                $summry_data_check_sum = $this->input->post('summry_data_check_sum[]');
                $count_row_summry = count(array_filter($summry_data_row_id));

                
                
                //$sumry_unique_id = array_unique($summry_data_unique_id);
                //$count_sumry_unique_id = count(array_filter($sumry_unique_id));

                for ($i=0; $i < $count_row; $i++) { 

                	$txn_data = $this->customer_txn_data->getRecordById($txn_data_row_id[$i]);   

                    $input = array(); 
                    $input_cat = array(); 
                    $id = $txn_data_row_id[$i];
                    $input['description'] = $txn_data_description[$i];
                    $input['check_no'] = $txn_data_check[$i];
                    $input['txn_date'] = $txn_data_txn_date[$i];
                    $input['txn_amt'] = $txn_data_txn_amnt[$i];
                    $input['type'] = $txn_data_type[$i];
                    $input['currency'] =  $txn_data_currency[$i];


                    $input['level_1'] = trim($txn_data_level_1[$i]);
                    $category_arr = explode('-',$txn_data_level_1[$i]);
                    $txn_category_arr = explode('-',$txn_data->level_1);
                    if(trim($category_arr[0])=='loan repayment/emi' || trim($category_arr[0])=='travel expenses' || trim($category_arr[0])=='utilities'){
                        if(trim($txn_category_arr[0])!=trim($category_arr[0])){
                            $input['category_updated'] =  '1';
                            $input_cat['level_1'] = trim($txn_category_arr[0]);
                        }
                        if(trim($txn_category_arr[1])!=trim($category_arr[1])){
                            $input['category_updated_level_2'] =  '1';
                            $input_cat['level_2'] = trim($txn_category_arr[1]);
                        }

                        if(trim($txn_category_arr[0])!=trim($category_arr[0]) || trim($txn_category_arr[1])!=trim($category_arr[1])){
                            $input_cat['txn_id'] = $id;
                            $input_cat['updated_by'] = $this->session->userdata('user_id');
                            $updated_cat_id = $this->updated_category->insertCategoryUpdated($input_cat);
                        }
                    }
                    else{
                        if($txn_data->level_1!=$txn_data_level_1[$i]){
                            $input['category_updated'] =  '1';
                            if(trim($txn_category_arr[0])=='loan repayment/emi' || trim($txn_category_arr[0])=='travel expenses' || trim($txn_category_arr[0])=='utilities'){
                                $input_cat['level_1'] = trim($txn_category_arr[0]);
                                $input_cat['level_2'] = trim($txn_category_arr[1]);

                            }
                            else{
                                $input_cat['level_1'] = $txn_data->level_1;
                            }
                            $input_cat['txn_id'] = $id;
                            $input_cat['updated_by'] = $this->session->userdata('user_id');
                            $updated_cat_id = $this->updated_category->insertCategoryUpdated($input_cat);
                        }
                        
                    }
                    $txn = $this->manual_spreading->updateRecordForTxnData($id,$input);   
                }

                for ($j=0; $j < $count_row_summry; $j++) { 
                    $input_summry = array();  
                    
                    $pre_data_summry = $this->manual_spreading->getPreviousDataForSummary($txn_data_history_id,$summry_data_file_no[$j]);
                    
                    $txn_amount_type = $this->manual_spreading->getTxnAmountTypeForSummary($txn_data_history_id,$summry_data_file_no[$j]);
                    $total_deposits = 0; $count_deposits = 0;$total_withdrawals = 0;$count_withdrawals = 0;
        
                    if($pre_data_summry->total_deposits==$summry_data_total_deposits[$j] && $pre_data_summry->total_withdrawals==$summry_data_total_withdrawals[$j]){
                        foreach ($txn_amount_type as $key => $value) {
                            if(!empty($value->type) && $value->type=="cr"){
                                $total_deposits = $total_deposits + $value->txn_amt;
                                $count_deposits ++;
                            }else{
                                $total_withdrawals = $total_withdrawals + $value->txn_amt;
                                $count_withdrawals++;
                            }
                        }
                        $input_summry['total_deposits'] = $total_deposits;
                        $input_summry['total_withdrawals'] = $total_withdrawals;
                        $input_summry['count_deposits'] = $count_deposits;
                        $input_summry['count_withdrawals'] = $count_withdrawals;

                        $a = 0;$b = 0;$c=0;
                        $b = number_format(($summry_data_open_balance[$j] + $total_deposits) - $total_withdrawals, 2, '.', '');
                        $c = number_format(($b - $summry_data_closing_balance[$j]), 2, '.', '');

                    }
                    else{
                        $input_summry['total_deposits'] = $summry_data_total_deposits[$j];
                        $input_summry['total_withdrawals'] = $summry_data_total_withdrawals[$j];
                        $input_summry['count_deposits'] = $summry_data_count_deposits[$j];
                        $input_summry['count_withdrawals'] = $summry_data_count_withdrawals[$j];
                        $a = 0;$b = 0;$c=0;
                        $b = number_format(($summry_data_open_balance[$j] + $summry_data_total_deposits[$j]) - $summry_data_total_withdrawals[$j], 2, '.', '');
                        $c = number_format(($b - $summry_data_closing_balance[$j]), 2, '.', '');

                    }
                    $summry_id = $summry_data_row_id[$j];
                    $input_summry['account_number'] =  base64_encode(openssl_encrypt(trim($summry_data_account_number[$j]), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));// $this->encryption->encrypt($summry_data_account_number[$j]);
                    $input_summry['account_holder_name'] = base64_encode(openssl_encrypt(trim($summry_data_account_holder_name[$j]), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));//$this->encryption->encrypt($summry_data_account_holder_name[$j]);
                    $input_summry['account_type'] = $summry_data_account_type[$j];
                    $input_summry['name_of_bank'] = $summry_data_name_of_bank[$j];
                    $input_summry['bank_address'] = $summry_data_bank_address[$j];
                    $input_summry['bank_city'] = $summry_data_bank_city[$j];
                    $input_summry['bank_state'] = $summry_data_bank_state[$j];
                    $input_summry['bank_zip'] = $summry_data_bank_zip[$j];
                    $input_summry['current_balance'] = $summry_data_current_balance[$j];
                    $input_summry['start_date'] = $summry_data_start_date[$j];
                    $input_summry['end_date'] = $summry_data_end_date[$j];
                    $input_summry['open_balance'] = $summry_data_open_balance[$j];
                    $input_summry['closing_balance'] = $summry_data_closing_balance[$j]; 
                    $input_summry['native_vs_non_native'] = $summry_data_native_vs_non_native[$j];
                    $input_summry['check_sum'] = $summry_data_check_sum[$j];
                    $summry = $this->manual_spreading->updateRecordForSummaryData($summry_id,$input_summry);

                    if(!empty($summry_data_account_number[$j])){
                        $error_case = array();
                        $error_case['account_number'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }

                    if(!empty($summry_data_account_holder_name[$j])){
                        $error_case = array();
                        $error_case['aaccount_holder_name'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_account_type[$j])){
                        $error_case = array();
                        $error_case['account_type'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_name_of_bank[$j])){
                        $error_case = array();
                        $error_case['name_of_bank'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_bank_address[$j])){
                        $error_case = array();
                        $error_case['bank_address'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_bank_city[$j])){
                        $error_case = array();
                        $error_case['bank_city'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_bank_state[$j])){
                        $error_case = array();
                        $error_case['bank_state'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }

                    if(!empty($summry_data_bank_zip[$j])){
                        $error_case = array();
                        $error_case['bank_zip'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_current_balance[$j])){
                        $error_case = array();
                        $error_case['current_balance'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_start_date[$j])){
                        $error_case = array();
                        $error_case['start_date'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_end_date[$j])){
                        $error_case = array();
                        $error_case['end_date'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if(!empty($summry_data_closing_balance[$j])){
                        $error_case = array();
                        $error_case['closing_balance'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }
                    if($c=='0.00'){
                        $error_case = array();
                        $error_case['check_sum'] = 1;
                        $this->case_error_log->updateErrorLog($txn_data_history_id,$summry_data_file_no[$j],$error_case);
                    }

                    if($j==0){
                        $bank_name = $summry_data_name_of_bank[$j];
                        $bank_id = $this->banks->getBankIdByName($bank_name);
                        $input_bank['category_updated'] = 1;
                        $this->bank_statement->updateRecordByBankId($bank_id->id,$input_bank);
                    }
                }

                // $count_account_number = count(array_filter($summry_data_account_number));
                // $count_account_holder_name = count(array_filter($summry_data_account_holder_name));
                // $count_account_type = count(array_filter($summry_data_account_type));
                // $count_bank_name = count(array_filter($summry_data_name_of_bank));
                // $count_bank_address = count(array_filter($summry_data_bank_address));
                // $count_bank_city = count(array_filter($summry_data_bank_city));
                // $count_bank_zip = count(array_filter($summry_data_bank_zip));
                // $count_current_balance = count(array_filter($summry_data_current_balance));
                // $count_start_date = count(array_filter($summry_data_start_date));
                // $count_end_date = count(array_filter($summry_data_end_date));
                // $count_closing_balance = count(array_filter($summry_data_closing_balance));
                // $count_check_sum = count(array_filter($summry_data_check_sum));

                // if($count_account_number==$count_row_summry){
                //     $error_case = array();
                //     $id = $txn_data_row_id[$i];
                //     $input['description'] = $txn_data_description[$i];
                // }

                $message = 'Record updated successfully';
                $success = true;
                $output['callBackFunction'] = 'callBackCommonImportExcel'; 
                
            }
            else {
                $success = false;
                $message = validation_errors();
            }
            $output['message'] = $message;
            $output['success'] = $success;
            echo json_encode($output);die;
        }   
    }

    function submitToQa(){       
        $input_history = array(); 
        $input_history['submit_by_qa'] = $this->input->get('submit_to_qa');  
        $id = $this->input->get('history_id');  
        // $max_file_no = $this->manual_spreading->getMaxFileNumbeFromSumryData($id); 
        // for($file_no=1; $file_no<=$max_file_no->max_file_no; $file_no++)
        // {

            $data = array();
            $data['summary'] = $this->bank_summary_level_data->fetchSummaryLevelDataForCategorization($id);
            foreach($data['summary'] as $key => $value){
                //$data['summary'][$key]->Se10 = '';
                $data['summary'][$key]->account_number = openssl_decrypt(base64_decode($data['summary'][$key]->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($data['summary'][$key]->account_number);
                $accountNumber = $data['summary'][$key]->account_number;
                $data['summary'][$key]->account_holder_name = openssl_decrypt(base64_decode($data['summary'][$key]->account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());//$this->encryption->decrypt($data['summary'][$key]->account_holder_name);
            }
            $data['data'] = $this->customer_txn_data->fetchCustomerTxnDataForCategorization($id);
            
            foreach($data['data'] as $key => $value){
                foreach($value as $k=>$v){
                    //$data['data'][$key]->Se10 = '';
                    $data['data'][$key]->account_number = $accountNumber;
                }
            }
            $jsonData = json_encode($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://52.250.20.165:5000/categorization",
                CURLOPT_RETURNTRANSFER => true,
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
            }
            curl_close($curl);
            $input = array();
            $input['token'] = $response;
            $input['status'] = 0;
            $input['history_id'] = $id;
            $input['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_categories_token',$input);
            
        //}

        $affected_rows = $this->tpl_history->updateRecords($id,$input_history);
        if($affected_rows > 0){
            $output['success']= true;
            echo json_encode($output); die();     
        }
    }

    function sendToOther(){       
        $input = array(); 
        $input['click_to_send'] = $this->input->get('click_to_send'); 

        $id = $this->input->get('history_id');  
        $histResult = $this->tpl_history->getSingleRecordById($id);
        /*echo"<pre>";
        print_r($histResult);
        die('here');*/
        $crAmtArray = array();
        $drAmtArray = array();
        $totalCrAmtArray = array();
        $totalDrAmtArray = array();
        $htmlArray = array();
        $getMonthName = "";
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        $fileSerialNum = 1;
        $this->category_summary->deleteConsolidatedCategoryData($id);
        $this->category_summary->deleteCategorySummaryData($id);
        
        //if($this->session->userdata('email')=='shubha.joshi@ollosoft.com'){
        /*if($histResult->upload_process=='manual'){
            $results = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }else{
            $results = $this->bank_summary_level_data->fetchSummaryLevelData($id);
        }*/
        $results = $this->bank_summary_level_data->fetchSummaryLevelDataCategory($id);
        /*}else{
            $results = $this->bank_summary_level_data->fetchSummaryLevelDataByIdAsc($id);
        }*/
        /*echo"<pre>";
        print_r($results);
        echo"</pre>";
        die('heer');*/
        $checkChangeMonth = 1;
        $getChangeForMidMonth = array();
        $categoryTrigger = false;
        foreach ($results as $key => $value) {
            if(empty($getChangeForMidMonth)){
                array_push($getChangeForMidMonth,$value->start_date);
            }
            
            if(!in_array($value->start_date, $getChangeForMidMonth)){
                array_push($getChangeForMidMonth,$value->start_date);
                $categoryTrigger = true;
            }
            /**Set logic for mid month*/
            $split_start_date = $value->start_date;
            
            if (strpos($split_start_date, '/') !== false) {
                $splitStartdate = explode("/", $split_start_date);
                $splitStartMonthName = $splitStartdate[0];
            }else if(strpos($split_start_date, '-') !== false) {
                $splitStartdate = explode("-", $split_start_date);
                $splitStartMonthName = $splitStartdate[0];
            }
            
            $split_end_date = $value->end_date;
            if (strpos($split_end_date, '/') !== false) {
                $splitEnddate = explode("/", $split_end_date);
                $splitEndMonthName = $splitEnddate[0];
            }else if(strpos($split_end_date, '-') !== false) {
                $splitEnddate = explode("-", $split_end_date);
                $splitEndMonthName = $splitEnddate[0];
            }
            //echo $splitStartMonthName."</br>";
            //echo $splitEndMonthName."</br>";
            //die;
            $midMonth = false;
            if((int)$splitStartMonthName!=(int)$splitEndMonthName){
                //die('here');
                $midMonth = true;
            }
            $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($id,$value->file_no);
            if($key==0){
                $mid_beginning_date_consolidate = $customerTxns[0]->txn_date;
            }
            if($key+1==count($results)){
                $mid_end_date_consolidate = $customerTxns[count($customerTxns)-1]->txn_date;
            }
            /*print_r($customerTxns[count($customerTxns)-1]);
            echo"<pre>";
            print_r($customerTxns);
            echo"</pre>";
            die;*/
            if(count($customerTxns)>0){
                $h = 0;
                foreach($customerTxns as $txn){
                    $txn_date = $txn->txn_date;
                    if($midMonth==false){
                        if($getMonthName==""){
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }
                            $beginning_date = $txn_date;
                            $beginning_date_consolidate = $txn_date;
                            $end_date = $txn_date;
                        }else{
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getTxnMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getTxnMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }
                            
                            //echo $txn_date."<br>";
                            if($getMonthName!=$getTxnMonthName){
                                //die;
                                
                                $cat_summry = array();
                                $cat_summry['history_id'] = $id; 
                                $cat_summry['category_type'] = $getMonthName.'_'.$getYearName;
                                $cat_summry['sales_card_count'] = $crAmtArray['sales - card']['count'];
                                $cat_summry['sales_card_amt'] = $crAmtArray['sales - card']['amt'];
                                //$cat_summry['sales_non_card_count'] = $crAmtArray['sales - non card']['count'];
                                //$cat_summry['sales_non_card_amt'] = $crAmtArray['sales - non card']['amt'];
                                /*Add new category*/
                                $cat_summry['sales_non_card_uber_count'] = $crAmtArray['sales - non card (uber)']['count'];
                                $cat_summry['sales_non_card_uber_amt'] = $crAmtArray['sales - non card (uber)']['amt'];
                                $cat_summry['sales_non_card_didi_count'] = $crAmtArray['sales - non card (didi)']['count'];
                                $cat_summry['sales_non_card_didi_amt'] = $crAmtArray['sales - non card (didi)']['amt'];
                                $cat_summry['sales_non_card_rappi_count'] = $crAmtArray['sales - non card (rappi)']['count'];
                                $cat_summry['sales_non_card_rappi_amt'] = $crAmtArray['sales - non card (rappi)']['amt'];
                                $cat_summry['sales_non_card_sin_delantal_count'] = $crAmtArray['sales - non card (sin delantal)']['count'];
                                $cat_summry['sales_non_card_sin_delantal_amt'] = $crAmtArray['sales - non card (sin delantal)']['amt'];
                                $cat_summry['sales_non_card_other_count'] = $crAmtArray['sales - non card (other)']['count'];
                                $cat_summry['sales_non_card_other_amt'] = $crAmtArray['sales - non card (other)']['amt'];
                                /*End new cateory*/
                                $cat_summry['cash_deposit_count'] = $crAmtArray['cash deposit']['count'];
                                $cat_summry['cash_deposit_amt'] = $crAmtArray['cash deposit']['amt'];
                                $cat_summry['refund_reversals_count'] = $crAmtArray['refund/reversals']['count'];
                                $cat_summry['refund_reversals_amt'] = $crAmtArray['refund/reversals']['amt'];
                                $cat_summry['intra_account_transfer_count'] = $crAmtArray['intra account transfer']['count'];
                                $cat_summry['intra_account_transfer_amt'] = $crAmtArray['intra account transfer']['amt'];
                                $cat_summry['ng_check_count'] = $crAmtArray['ng check']['count'];
                                $cat_summry['ng_check_amt'] = $crAmtArray['ng check']['amt'];
                                $cat_summry['loans_count'] = $crAmtArray['loans']['count']; 
                                $cat_summry['loans_amt'] = $crAmtArray['loans']['amt'];
                                $cat_summry['investment_income_count'] = $crAmtArray['investment income']['count'];
                                $cat_summry['investment_income_amt'] = $crAmtArray['investment income']['amt'];
                                $cat_summry['insurance_claim_count'] = $crAmtArray['insurance claim']['count'];
                                $cat_summry['insurance_claim_amt'] = $crAmtArray['insurance claim']['amt'];
                                $cat_summry['miscellaneous_credits_count'] = $crAmtArray['miscellaneous credits']['count'];
                                $cat_summry['miscellaneous_credits_amt'] = $crAmtArray['miscellaneous credits']['amt'];
                                $cat_summry['total_credit_count_of_txn'] = $crAmtArray['sales - card']['count'] + $crAmtArray['sales - non card (uber)']['count'] + $crAmtArray['sales - non card (didi)']['count'] + $crAmtArray['sales - non card (rappi)']['count'] + $crAmtArray['sales - non card (sin delantal)']['count'] + $crAmtArray['sales - non card (other)']['count'] + $crAmtArray['cash deposit']['count'] + $crAmtArray['refund/reversals']['count'] + $crAmtArray['intra account transfer']['count'] + $crAmtArray['ng check']['count'] + $crAmtArray['loans']['count'] + $crAmtArray['investment income']['count'] + $crAmtArray['insurance claim']['count'] + $crAmtArray['miscellaneous credits']['count'];
                                $cat_summry['total_credit_amount'] = $crAmtArray['sales - card']['amt'] + $crAmtArray['sales - non card (uber)']['amt'] + $crAmtArray['sales - non card (didi)']['amt'] + $crAmtArray['sales - non card (rappi)']['amt'] + $crAmtArray['sales - non card (sin delantal)']['amt'] + $crAmtArray['sales - non card (other)']['amt'] + $crAmtArray['cash deposit']['amt'] + $crAmtArray['refund/reversals']['amt'] + $crAmtArray['intra account transfer']['amt'] + $crAmtArray['ng check']['amt'] + $crAmtArray['loans']['amt'] + $crAmtArray['investment income']['amt'] + $crAmtArray['insurance claim']['amt'] + $crAmtArray['miscellaneous credits']['amt'];
                                $cat_summry['vendor_payments_count'] = $drAmtArray['vendor payments']['count'];
                                $cat_summry['vendor_payments_amt'] = $drAmtArray['vendor payments']['amt'];
                                $cat_summry['salaries_benefits_count'] = $drAmtArray['salaries & benefits']['count'];
                                $cat_summry['salaries_benefits_amt'] = $drAmtArray['salaries & benefits']['amt'];
                                $cat_summry['taxes_count'] = $drAmtArray['taxes']['count'];
                                $cat_summry['taxes_amt'] = $drAmtArray['taxes']['amt'];
                                $cat_summry['insurance_count'] = $drAmtArray['insurance']['count'];
                                $cat_summry['insurance_amt'] = $drAmtArray['insurance']['amt'];
                                $cat_summry['cash_withdrawal_count'] = $drAmtArray['cash withdrawal']['count'];
                                $cat_summry['cash_withdrawal_amt'] = $drAmtArray['cash withdrawal']['amt'];
                                $cat_summry['card_processor_fees_count'] = $drAmtArray['card processor fees']['count'];
                                $cat_summry['card_processor_fees_amt'] = $drAmtArray['card processor fees']['amt'];
                                $cat_summry['chargeback_count'] = $drAmtArray['chargeback']['count']; 
                                $cat_summry['chargeback_amt'] = $drAmtArray['chargeback']['amt'];
                                $cat_summry['credit_card_payments_count'] = $drAmtArray['credit card payments']['count'];
                                $cat_summry['credit_card_payments_amt'] = $drAmtArray['credit card payments']['amt'];
                                $cat_summry['loan_repayment_emi_lenders_count'] = $drAmtArray['loan repayment/emi - lenders']['count'];
                                $cat_summry['loan_repayment_emi_lenders_amt'] = $drAmtArray['loan repayment/emi - lenders']['amt'];
                                $cat_summry['loan_repayment_emi_mortgage_count'] = $drAmtArray['loan repayment/emi - mortgage']['count'];
                                $cat_summry['loan_repayment_emi_mortgage_amt'] = $drAmtArray['loan repayment/emi - mortgage']['amt'];
                                $cat_summry['loan_repayment_emi_auto_finance_count'] = $drAmtArray['loan repayment/emi - auto finance']['count'];
                                $cat_summry['loan_repayment_emi_auto_finance_amt'] = $drAmtArray['loan repayment/emi - auto finance']['amt'];
                                $cat_summry['intra_account_count'] = $drAmtArray['intra account transfer']['count'];
                                $cat_summry['intra_account_amt'] = $drAmtArray['intra account transfer']['amt'];
                                $cat_summry['fees_ng_count'] = $drAmtArray['fees - ng']['count']; 
                                $cat_summry['fees_ng_amt'] = $drAmtArray['fees - ng']['amt'];
                                $cat_summry['fees_overdraft_count'] = $drAmtArray['fees - overdraft']['count'];
                                $cat_summry['fees_overdraft_amt'] = $drAmtArray['fees - overdraft']['amt'];
                                $cat_summry['fees_others_count'] = $drAmtArray['fees - others']['count'];
                                $cat_summry['fees_others_amt'] = $drAmtArray['fees - others']['amt'];
                                $cat_summry['investments_count'] = $drAmtArray['investments']['count'];
                                $cat_summry['investments_amt'] = $drAmtArray['investments']['amt'];
                                $cat_summry['deposited_check_return_count'] = $drAmtArray['deposited check return']['count'];
                                $cat_summry['deposited_check_return_amt'] = $drAmtArray['deposited check return']['amt'];
                                $cat_summry['miscellaneous_debit_count'] = $drAmtArray['miscellaneous debit']['count'];
                                $cat_summry['miscellaneous_debit_amt'] = $drAmtArray['miscellaneous debit']['amt'];
                                $cat_summry['travel_expenses_airlines_count'] = $drAmtArray['travel expenses - airlines']['count']; 
                                $cat_summry['travel_expenses_airlines_amt'] = $drAmtArray['travel expenses - airlines']['amt'];
                                $cat_summry['travel_expenses_hotels_count'] = $drAmtArray['travel expenses - hotels']['count'];
                                $cat_summry['travel_expenses_hotels_amt'] = $drAmtArray['travel expenses - hotels']['amt'];
                                $cat_summry['travel_epenses_car_rental_count'] = $drAmtArray['travel expenses - car rental']['count'];
                                $cat_summry['travel_epenses_car_rental_amt'] = $drAmtArray['travel expenses - car rental']['amt'];
                                $cat_summry['travel_expenses_others_count'] = $drAmtArray['travel expenses - others']['count'];
                                $cat_summry['travel_expenses_others_amt'] = $drAmtArray['travel expenses - others']['amt'];
                                $cat_summry['utilities_telephone_count'] = $drAmtArray['utilities - telephone']['count'];
                                $cat_summry['utilities_telephone_amt'] = $drAmtArray['utilities - telephone']['amt'];
                                $cat_summry['utilities_internet_count'] = $drAmtArray['utilities - internet']['count'];
                                $cat_summry['utilities_internet_amt'] = $drAmtArray['utilities - internet']['amt'];
                                $cat_summry['utilities_tv_count'] = $drAmtArray['utilities - tv']['count']; 
                                $cat_summry['utilities_tv_amt'] = $drAmtArray['utilities - tv']['amt'];
                                $cat_summry['utilities_power_count'] = $drAmtArray['utilities - power']['count'];
                                $cat_summry['utilities_power_amt'] = $drAmtArray['utilities - power']['amt'];
                                $cat_summry['utilities_water_count'] = $drAmtArray['utilities - water']['count'];
                                $cat_summry['utilities_water_amt'] = $drAmtArray['utilities - water']['amt'];
                                $cat_summry['utilities_others_count'] = $drAmtArray['utilities - others']['count'];
                                $cat_summry['utilities_others_amt'] = $drAmtArray['utilities - others']['amt'];
                                $cat_summry['total_debit_count_of_txn'] = $drAmtArray['vendor payments']['count'] + $drAmtArray['salaries & benefits']['count'] + $drAmtArray['taxes']['count'] + $drAmtArray['insurance']['count'] + $drAmtArray['cash withdrawal']['count'] + $drAmtArray['card processor fees']['count']+ $drAmtArray['chargeback']['count'] + $drAmtArray['credit card payments']['count'] + $drAmtArray['loan repayment/emi - lenders']['count'] + $drAmtArray['loan repayment/emi - mortgage']['count'] + $drAmtArray['loan repayment/emi - auto finance']['count'] + $drAmtArray['intra account transfer']['count'] + $drAmtArray['fees - ng']['count'] + $drAmtArray['fees - overdraft']['count'] + $drAmtArray['fees - others']['count'] + $drAmtArray['investments']['count'] + $drAmtArray['deposited check return']['count'] + $drAmtArray['miscellaneous debit']['count'] + $drAmtArray['travel expenses - airlines']['count'] + $drAmtArray['travel expenses - hotels']['count'] + $drAmtArray['travel expenses - car rental']['count'] + $drAmtArray['travel expenses - others']['count'] + $drAmtArray['utilities - telephone']['count'] + $drAmtArray['utilities - internet']['count'] + $drAmtArray['utilities - tv']['count'] + $drAmtArray['utilities - power']['count'] + $drAmtArray['utilities - water']['count'] + $drAmtArray['utilities - others']['count'];
                                $cat_summry['total_debit_amt'] = $drAmtArray['vendor payments']['amt'] + $drAmtArray['salaries & benefits']['amt'] + $drAmtArray['taxes']['amt'] + $drAmtArray['insurance']['amt'] + $drAmtArray['cash withdrawal']['amt'] + $drAmtArray['card processor fees']['amt']+ $drAmtArray['chargeback']['amt'] + $drAmtArray['credit card payments']['amt'] + $drAmtArray['loan repayment/emi - lenders']['amt'] + $drAmtArray['loan repayment/emi - mortgage']['amt'] + $drAmtArray['loan repayment/emi - auto finance']['amt'] + $drAmtArray['intra account transfer']['amt'] + $drAmtArray['fees - ng']['amt'] + $drAmtArray['fees - overdraft']['amt'] + $drAmtArray['fees - others']['amt'] + $drAmtArray['investments']['amt'] + $drAmtArray['deposited check return']['amt'] + $drAmtArray['miscellaneous debit']['amt'] + $drAmtArray['travel expenses - airlines']['amt'] + $drAmtArray['travel expenses - hotels']['amt'] + $drAmtArray['travel expenses - car rental']['amt'] + $drAmtArray['travel expenses - others']['amt'] + $drAmtArray['utilities - telephone']['amt'] + $drAmtArray['utilities - internet']['amt'] + $drAmtArray['utilities - tv']['amt'] + $drAmtArray['utilities - power']['amt'] + $drAmtArray['utilities - water']['amt'] + $drAmtArray['utilities - others']['amt'];
                                $cat_summry['beginning_date'] = $beginning_date;
                                $cat_summry['end_date'] = $end_date;
                                $summry_id = $this->category_summary->insertCategorySummaryData($cat_summry);
                                $fileSerialNum++;
                                $crAmtArray = array();
                                $drAmtArray = array();
                                $getMonthName = $getTxnMonthName;
                                $getYearName = $getYearName;
                                
                                $beginning_date = $txn_date;
                                $end_date = $txn_date;
                            }else{
                                
                                if (strpos($txn_date, '/') !== false) {
                                    $dateExplode = explode("/", $txn_date);
                                    $getdateNum = $dateExplode[1];
                                    
                                    $beginningDateExplode = explode("/", $beginning_date);
                                    $getBeginingdateNum = $beginningDateExplode[1];
                                    
                                    if($getdateNum<$getBeginingdateNum){
                                        $beginning_date = $txn_date;
                                    }
                                    
                                    $endDateExplode = explode("/", $end_date);
                                    $getEndDateNum = $endDateExplode[1];
                                    
                                    if($getdateNum>$getEndDateNum){
                                        $end_date = $txn_date;
                                        $end_date_consolidate = $txn_date;
                                    }
                                    
                                }else if(strpos($txn_date, '-') !== false) {
                                    $dateExplode = explode("-", $txn_date);
                                    $getdateNum = $dateExplode[1];
                                    
                                    $beginningDateExplode = explode("-", $beginning_date);
                                    $getBeginingdateNum = $beginningDateExplode[1];
                                    
                                    if($getdateNum<$getBeginingdateNum){
                                        $beginning_date = $txn_date;
                                    }
                                    
                                    $endDateExplode = explode("-", $end_date);
                                    $getEndDateNum = $endDateExplode[1];
                                    
                                    if($getdateNum>$getEndDateNum){
                                        $end_date = $txn_date;
                                        $end_date_consolidate = $txn_date;
                                    }
                                }
                            }
                        }
                    }else{
                        
                        if($getMonthName==""){
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }
                            $beginning_date = $txn_date;
                            $beginning_date_consolidate = $txn_date;
                            $end_date = $txn_date;
                        }else{
                            if (strpos($txn_date, '/') !== false) {
                                $thedate = explode("/", $txn_date);
                                $getTxnMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }else if(strpos($txn_date, '-') !== false) {
                                $thedate = explode("-", $txn_date);
                                $getTxnMonthName = $thedate[0];
                                $getYearName = date('y',strtotime($thedate[2]));
                            }
                        }
                        
                        //if($checkChangeMonth!=$key+1){
                        if($categoryTrigger){
                            //die;
                            
                            $cat_summry = array();
                            $cat_summry['history_id'] = $id;
                            $cat_summry['category_type'] = $getMonthName.'_'.$getYearName;
                            $cat_summry['sales_card_count'] = $crAmtArray['sales - card']['count'];
                            $cat_summry['sales_card_amt'] = $crAmtArray['sales - card']['amt'];
                            //$cat_summry['sales_non_card_count'] = $crAmtArray['sales - non card']['count'];
                            //$cat_summry['sales_non_card_amt'] = $crAmtArray['sales - non card']['amt'];
                            /*Add new category*/
                            $cat_summry['sales_non_card_uber_count'] = $crAmtArray['sales - non card (uber)']['count'];
                            $cat_summry['sales_non_card_uber_amt'] = $crAmtArray['sales - non card (uber)']['amt'];
                            $cat_summry['sales_non_card_didi_count'] = $crAmtArray['sales - non card (didi)']['count'];
                            $cat_summry['sales_non_card_didi_amt'] = $crAmtArray['sales - non card (didi)']['amt'];
                            $cat_summry['sales_non_card_rappi_count'] = $crAmtArray['sales - non card (rappi)']['count'];
                            $cat_summry['sales_non_card_rappi_amt'] = $crAmtArray['sales - non card (rappi)']['amt'];
                            $cat_summry['sales_non_card_sin_delantal_count'] = $crAmtArray['sales - non card (sin delantal)']['count'];
                            $cat_summry['sales_non_card_sin_delantal_amt'] = $crAmtArray['sales - non card (sin delantal)']['amt'];
                            $cat_summry['sales_non_card_other_count'] = $crAmtArray['sales - non card (other)']['count'];
                            $cat_summry['sales_non_card_other_amt'] = $crAmtArray['sales - non card (other)']['amt'];
                            /*End new cateory*/
                            $cat_summry['cash_deposit_count'] = $crAmtArray['cash deposit']['count'];
                            $cat_summry['cash_deposit_amt'] = $crAmtArray['cash deposit']['amt'];
                            $cat_summry['refund_reversals_count'] = $crAmtArray['refund/reversals']['count'];
                            $cat_summry['refund_reversals_amt'] = $crAmtArray['refund/reversals']['amt'];
                            $cat_summry['intra_account_transfer_count'] = $crAmtArray['intra account transfer']['count'];
                            $cat_summry['intra_account_transfer_amt'] = $crAmtArray['intra account transfer']['amt'];
                            $cat_summry['ng_check_count'] = $crAmtArray['ng check']['count'];
                            $cat_summry['ng_check_amt'] = $crAmtArray['ng check']['amt'];
                            $cat_summry['loans_count'] = $crAmtArray['loans']['count'];
                            $cat_summry['loans_amt'] = $crAmtArray['loans']['amt'];
                            $cat_summry['investment_income_count'] = $crAmtArray['investment income']['count'];
                            $cat_summry['investment_income_amt'] = $crAmtArray['investment income']['amt'];
                            $cat_summry['insurance_claim_count'] = $crAmtArray['insurance claim']['count'];
                            $cat_summry['insurance_claim_amt'] = $crAmtArray['insurance claim']['amt'];
                            $cat_summry['miscellaneous_credits_count'] = $crAmtArray['miscellaneous credits']['count'];
                            $cat_summry['miscellaneous_credits_amt'] = $crAmtArray['miscellaneous credits']['amt'];
                            $cat_summry['total_credit_count_of_txn'] = $crAmtArray['sales - card']['count'] + $crAmtArray['sales - non card (uber)']['count'] + $crAmtArray['sales - non card (didi)']['count'] + $crAmtArray['sales - non card (rappi)']['count'] + $crAmtArray['sales - non card (sin delantal)']['count'] + $crAmtArray['sales - non card (other)']['count'] + $crAmtArray['cash deposit']['count'] + $crAmtArray['refund/reversals']['count'] + $crAmtArray['intra account transfer']['count'] + $crAmtArray['ng check']['count'] + $crAmtArray['loans']['count'] + $crAmtArray['investment income']['count'] + $crAmtArray['insurance claim']['count'] + $crAmtArray['miscellaneous credits']['count'];
                            $cat_summry['total_credit_amount'] = $crAmtArray['sales - card']['amt'] + $crAmtArray['sales - non card (uber)']['amt'] + $crAmtArray['sales - non card (didi)']['amt'] + $crAmtArray['sales - non card (rappi)']['amt'] + $crAmtArray['sales - non card (sin delantal)']['amt'] + $crAmtArray['sales - non card (other)']['amt'] + $crAmtArray['cash deposit']['amt'] + $crAmtArray['refund/reversals']['amt'] + $crAmtArray['intra account transfer']['amt'] + $crAmtArray['ng check']['amt'] + $crAmtArray['loans']['amt'] + $crAmtArray['investment income']['amt'] + $crAmtArray['insurance claim']['amt'] + $crAmtArray['miscellaneous credits']['amt'];
                            $cat_summry['vendor_payments_count'] = $drAmtArray['vendor payments']['count'];
                            $cat_summry['vendor_payments_amt'] = $drAmtArray['vendor payments']['amt'];
                            $cat_summry['salaries_benefits_count'] = $drAmtArray['salaries & benefits']['count'];
                            $cat_summry['salaries_benefits_amt'] = $drAmtArray['salaries & benefits']['amt'];
                            $cat_summry['taxes_count'] = $drAmtArray['taxes']['count'];
                            $cat_summry['taxes_amt'] = $drAmtArray['taxes']['amt'];
                            $cat_summry['insurance_count'] = $drAmtArray['insurance']['count'];
                            $cat_summry['insurance_amt'] = $drAmtArray['insurance']['amt'];
                            $cat_summry['cash_withdrawal_count'] = $drAmtArray['cash withdrawal']['count'];
                            $cat_summry['cash_withdrawal_amt'] = $drAmtArray['cash withdrawal']['amt'];
                            $cat_summry['card_processor_fees_count'] = $drAmtArray['card processor fees']['count'];
                            $cat_summry['card_processor_fees_amt'] = $drAmtArray['card processor fees']['amt'];
                            $cat_summry['chargeback_count'] = $drAmtArray['chargeback']['count'];
                            $cat_summry['chargeback_amt'] = $drAmtArray['chargeback']['amt'];
                            $cat_summry['credit_card_payments_count'] = $drAmtArray['credit card payments']['count'];
                            $cat_summry['credit_card_payments_amt'] = $drAmtArray['credit card payments']['amt'];
                            $cat_summry['loan_repayment_emi_lenders_count'] = $drAmtArray['loan repayment/emi - lenders']['count'];
                            $cat_summry['loan_repayment_emi_lenders_amt'] = $drAmtArray['loan repayment/emi - lenders']['amt'];
                            $cat_summry['loan_repayment_emi_mortgage_count'] = $drAmtArray['loan repayment/emi - mortgage']['count'];
                            $cat_summry['loan_repayment_emi_mortgage_amt'] = $drAmtArray['loan repayment/emi - mortgage']['amt'];
                            $cat_summry['loan_repayment_emi_auto_finance_count'] = $drAmtArray['loan repayment/emi - auto finance']['count'];
                            $cat_summry['loan_repayment_emi_auto_finance_amt'] = $drAmtArray['loan repayment/emi - auto finance']['amt'];
                            $cat_summry['intra_account_count'] = $drAmtArray['intra account transfer']['count'];
                            $cat_summry['intra_account_amt'] = $drAmtArray['intra account transfer']['amt'];
                            $cat_summry['fees_ng_count'] = $drAmtArray['fees - ng']['count'];
                            $cat_summry['fees_ng_amt'] = $drAmtArray['fees - ng']['amt'];
                            $cat_summry['fees_overdraft_count'] = $drAmtArray['fees - overdraft']['count'];
                            $cat_summry['fees_overdraft_amt'] = $drAmtArray['fees - overdraft']['amt'];
                            $cat_summry['fees_others_count'] = $drAmtArray['fees - others']['count'];
                            $cat_summry['fees_others_amt'] = $drAmtArray['fees - others']['amt'];
                            $cat_summry['investments_count'] = $drAmtArray['investments']['count'];
                            $cat_summry['investments_amt'] = $drAmtArray['investments']['amt'];
                            $cat_summry['deposited_check_return_count'] = $drAmtArray['deposited check return']['count'];
                            $cat_summry['deposited_check_return_amt'] = $drAmtArray['deposited check return']['amt'];
                            $cat_summry['miscellaneous_debit_count'] = $drAmtArray['miscellaneous debit']['count'];
                            $cat_summry['miscellaneous_debit_amt'] = $drAmtArray['miscellaneous debit']['amt'];
                            $cat_summry['travel_expenses_airlines_count'] = $drAmtArray['travel expenses - airlines']['count'];
                            $cat_summry['travel_expenses_airlines_amt'] = $drAmtArray['travel expenses - airlines']['amt'];
                            $cat_summry['travel_expenses_hotels_count'] = $drAmtArray['travel expenses - hotels']['count'];
                            $cat_summry['travel_expenses_hotels_amt'] = $drAmtArray['travel expenses - hotels']['amt'];
                            $cat_summry['travel_epenses_car_rental_count'] = $drAmtArray['travel expenses - car rental']['count'];
                            $cat_summry['travel_epenses_car_rental_amt'] = $drAmtArray['travel expenses - car rental']['amt'];
                            $cat_summry['travel_expenses_others_count'] = $drAmtArray['travel expenses - others']['count'];
                            $cat_summry['travel_expenses_others_amt'] = $drAmtArray['travel expenses - others']['amt'];
                            $cat_summry['utilities_telephone_count'] = $drAmtArray['utilities - telephone']['count'];
                            $cat_summry['utilities_telephone_amt'] = $drAmtArray['utilities - telephone']['amt'];
                            $cat_summry['utilities_internet_count'] = $drAmtArray['utilities - internet']['count'];
                            $cat_summry['utilities_internet_amt'] = $drAmtArray['utilities - internet']['amt'];
                            $cat_summry['utilities_tv_count'] = $drAmtArray['utilities - tv']['count'];
                            $cat_summry['utilities_tv_amt'] = $drAmtArray['utilities - tv']['amt'];
                            $cat_summry['utilities_power_count'] = $drAmtArray['utilities - power']['count'];
                            $cat_summry['utilities_power_amt'] = $drAmtArray['utilities - power']['amt'];
                            $cat_summry['utilities_water_count'] = $drAmtArray['utilities - water']['count'];
                            $cat_summry['utilities_water_amt'] = $drAmtArray['utilities - water']['amt'];
                            $cat_summry['utilities_others_count'] = $drAmtArray['utilities - others']['count'];
                            $cat_summry['utilities_others_amt'] = $drAmtArray['utilities - others']['amt'];
                            $cat_summry['total_debit_count_of_txn'] = $drAmtArray['vendor payments']['count'] + $drAmtArray['salaries & benefits']['count'] + $drAmtArray['taxes']['count'] + $drAmtArray['insurance']['count'] + $drAmtArray['cash withdrawal']['count'] + $drAmtArray['card processor fees']['count']+ $drAmtArray['chargeback']['count'] + $drAmtArray['credit card payments']['count'] + $drAmtArray['loan repayment/emi - lenders']['count'] + $drAmtArray['loan repayment/emi - mortgage']['count'] + $drAmtArray['loan repayment/emi - auto finance']['count'] + $drAmtArray['intra account transfer']['count'] + $drAmtArray['fees - ng']['count'] + $drAmtArray['fees - overdraft']['count'] + $drAmtArray['fees - others']['count'] + $drAmtArray['investments']['count'] + $drAmtArray['deposited check return']['count'] + $drAmtArray['miscellaneous debit']['count'] + $drAmtArray['travel expenses - airlines']['count'] + $drAmtArray['travel expenses - hotels']['count'] + $drAmtArray['travel expenses - car rental']['count'] + $drAmtArray['travel expenses - others']['count'] + $drAmtArray['utilities - telephone']['count'] + $drAmtArray['utilities - internet']['count'] + $drAmtArray['utilities - tv']['count'] + $drAmtArray['utilities - power']['count'] + $drAmtArray['utilities - water']['count'] + $drAmtArray['utilities - others']['count'];
                            $cat_summry['total_debit_amt'] = $drAmtArray['vendor payments']['amt'] + $drAmtArray['salaries & benefits']['amt'] + $drAmtArray['taxes']['amt'] + $drAmtArray['insurance']['amt'] + $drAmtArray['cash withdrawal']['amt'] + $drAmtArray['card processor fees']['amt']+ $drAmtArray['chargeback']['amt'] + $drAmtArray['credit card payments']['amt'] + $drAmtArray['loan repayment/emi - lenders']['amt'] + $drAmtArray['loan repayment/emi - mortgage']['amt'] + $drAmtArray['loan repayment/emi - auto finance']['amt'] + $drAmtArray['intra account transfer']['amt'] + $drAmtArray['fees - ng']['amt'] + $drAmtArray['fees - overdraft']['amt'] + $drAmtArray['fees - others']['amt'] + $drAmtArray['investments']['amt'] + $drAmtArray['deposited check return']['amt'] + $drAmtArray['miscellaneous debit']['amt'] + $drAmtArray['travel expenses - airlines']['amt'] + $drAmtArray['travel expenses - hotels']['amt'] + $drAmtArray['travel expenses - car rental']['amt'] + $drAmtArray['travel expenses - others']['amt'] + $drAmtArray['utilities - telephone']['amt'] + $drAmtArray['utilities - internet']['amt'] + $drAmtArray['utilities - tv']['amt'] + $drAmtArray['utilities - power']['amt'] + $drAmtArray['utilities - water']['amt'] + $drAmtArray['utilities - others']['amt'];
                            $cat_summry['beginning_date'] = $mid_beginning_date;
                            $cat_summry['end_date'] = $mid_end_date;
                            $summry_id = $this->category_summary->insertCategorySummaryData($cat_summry);
                            $fileSerialNum++;
                            $checkChangeMonth++;
                            $crAmtArray = array();
                            $drAmtArray = array();
                            $getMonthName = $getTxnMonthName;
                            $getYearName = $getYearName; 
                        }
                        if($h==0){
                            $mid_beginning_date = $customerTxns[0]->txn_date;
                            $mid_end_date = $customerTxns[count($customerTxns)-1]->txn_date;
                            $h++;
                        }
                        
                    }
                    $txnLevel_1 = trim($txn->level_1);
                    //$txnLevel_2 = trim($txn->level_2);
                    if($txn->type=='cr'){
                       
                        $open_balance = $open_balance+$txn->txn_amt;
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                            if($crAmtArray[$txnLevel_1]){
                                $crAmtArray[$txnLevel_1]['count'] =  $crAmtArray[$txnLevel_1]['count']+1;
                                $crAmtArray[$txnLevel_1]['amt'] = $crAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $crAmtArray[$txnLevel_1]['count'] =  1;
                                $crAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
                            if($totalCrAmtArray[$txnLevel_1]){
                                $totalCrAmtArray[$txnLevel_1]['count'] =  $totalCrAmtArray[$txnLevel_1]['count']+1;
                                $totalCrAmtArray[$txnLevel_1]['amt'] = $totalCrAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $totalCrAmtArray[$txnLevel_1]['count'] =  1;
                                $totalCrAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        // if(in_array($txnLevel_2, $credit_array)){
                            
                        // }
                        
                    }else{

                        $open_balance = $open_balance-$txn->txn_amt;
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $debit_array))){
                            if($drAmtArray[$txnLevel_1]){
                                $drAmtArray[$txnLevel_1]['count'] =  $drAmtArray[$txnLevel_1]['count']+1;
                                $drAmtArray[$txnLevel_1]['amt'] = $drAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $drAmtArray[$txnLevel_1]['count'] =  1;
                                $drAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        if(in_array(strtolower($txnLevel_1), array_map('strtolower', $debit_array))){
                            if($totalDrAmtArray[$txnLevel_1]){
                                $totalDrAmtArray[$txnLevel_1]['count'] =  $totalDrAmtArray[$txnLevel_1]['count']+1;
                                $totalDrAmtArray[$txnLevel_1]['amt'] = $totalDrAmtArray[$txnLevel_1]['amt']+$txn->txn_amt;
                            }else{
                                $totalDrAmtArray[$txnLevel_1]['count'] =  1;
                                $totalDrAmtArray[$txnLevel_1]['amt'] = $txn->txn_amt;
                            }
                        }
                        
                        // if(in_array($txnLevel_2, $debit_array)){
                            
                        // }
                    }
                    
                }
                
            }
        }

        if($midMonth==true){
            $beginning_date = $mid_beginning_date;
            $end_date = $mid_end_date;
            $beginning_date_consolidate = $mid_beginning_date_consolidate;
            $end_date_consolidate = $mid_end_date_consolidate;
        }
        
        $cat_summry = array();
        $cat_summry['history_id'] = $id;
        $cat_summry['category_type'] = $getMonthName.'_'.$getYearName;
        $cat_summry['sales_card_count'] = $crAmtArray['sales - card']['count'];
        $cat_summry['sales_card_amt'] = $crAmtArray['sales - card']['amt'];
        //$cat_summry['sales_non_card_count'] = $crAmtArray['sales - non card']['count'];
        //$cat_summry['sales_non_card_amt'] = $crAmtArray['sales - non card']['amt'];
        /*Add new category*/
        $cat_summry['sales_non_card_uber_count'] = $crAmtArray['sales - non card (uber)']['count'];
        $cat_summry['sales_non_card_uber_amt'] = $crAmtArray['sales - non card (uber)']['amt'];
        $cat_summry['sales_non_card_didi_count'] = $crAmtArray['sales - non card (didi)']['count'];
        $cat_summry['sales_non_card_didi_amt'] = $crAmtArray['sales - non card (didi)']['amt'];
        $cat_summry['sales_non_card_rappi_count'] = $crAmtArray['sales - non card (rappi)']['count'];
        $cat_summry['sales_non_card_rappi_amt'] = $crAmtArray['sales - non card (rappi)']['amt'];
        $cat_summry['sales_non_card_sin_delantal_count'] = $crAmtArray['sales - non card (sin delantal)']['count'];
        $cat_summry['sales_non_card_sin_delantal_amt'] = $crAmtArray['sales - non card (sin delantal)']['amt'];
        $cat_summry['sales_non_card_other_count'] = $crAmtArray['sales - non card (other)']['count'];
        $cat_summry['sales_non_card_other_amt'] = $crAmtArray['sales - non card (other)']['amt'];
        /*End new cateory*/
        $cat_summry['cash_deposit_count'] = $crAmtArray['cash deposit']['count'];
        $cat_summry['cash_deposit_amt'] = $crAmtArray['cash deposit']['amt'];
        $cat_summry['refund_reversals_count'] = $crAmtArray['refund/reversals']['count'];
        $cat_summry['refund_reversals_amt'] = $crAmtArray['refund/reversals']['amt'];
        $cat_summry['intra_account_transfer_count'] = $crAmtArray['intra account transfer']['count'];
        $cat_summry['intra_account_transfer_amt'] = $crAmtArray['intra account transfer']['amt'];
        $cat_summry['ng_check_count'] = $crAmtArray['ng check']['count'];
        $cat_summry['ng_check_amt'] = $crAmtArray['ng check']['amt'];
        $cat_summry['loans_count'] = $crAmtArray['loans']['count']; 
        $cat_summry['loans_amt'] = $crAmtArray['loans']['amt'];
        $cat_summry['investment_income_count'] = $crAmtArray['investment income']['count'];
        $cat_summry['investment_income_amt'] = $crAmtArray['investment income']['amt'];
        $cat_summry['insurance_claim_count'] = $crAmtArray['insurance claim']['count'];
        $cat_summry['insurance_claim_amt'] = $crAmtArray['insurance claim']['amt'];
        $cat_summry['miscellaneous_credits_count'] = $crAmtArray['miscellaneous credits']['count'];
        $cat_summry['miscellaneous_credits_amt'] = $crAmtArray['miscellaneous credits']['amt'];
        $cat_summry['total_credit_count_of_txn'] = $crAmtArray['sales - card']['count'] + $crAmtArray['sales - non card (uber)']['count'] + $crAmtArray['sales - non card (didi)']['count'] + $crAmtArray['sales - non card (rappi)']['count'] + $crAmtArray['sales - non card (sin delantal)']['count'] + $crAmtArray['sales - non card (other)']['count'] + $crAmtArray['cash deposit']['count'] + $crAmtArray['refund/reversals']['count'] + $crAmtArray['intra account transfer']['count'] + $crAmtArray['ng check']['count'] + $crAmtArray['loans']['count'] + $crAmtArray['investment income']['count'] + $crAmtArray['insurance claim']['count'] + $crAmtArray['miscellaneous credits']['count'];
        $cat_summry['total_credit_amount'] = $crAmtArray['sales - card']['amt'] + $crAmtArray['sales - non card (uber)']['amt'] + $crAmtArray['sales - non card (didi)']['amt'] + $crAmtArray['sales - non card (rappi)']['amt'] + $crAmtArray['sales - non card (sin delantal)']['amt'] + $crAmtArray['sales - non card (other)']['amt'] + $crAmtArray['cash deposit']['amt'] + $crAmtArray['refund/reversals']['amt'] + $crAmtArray['intra account transfer']['amt'] + $crAmtArray['ng check']['amt'] + $crAmtArray['loans']['amt'] + $crAmtArray['investment income']['amt'] + $crAmtArray['insurance claim']['amt'] + $crAmtArray['miscellaneous credits']['amt'];
        $cat_summry['vendor_payments_count'] = $drAmtArray['vendor payments']['count'];
        $cat_summry['vendor_payments_amt'] = $drAmtArray['vendor payments']['amt'];
        $cat_summry['salaries_benefits_count'] = $drAmtArray['salaries & benefits']['count'];
        $cat_summry['salaries_benefits_amt'] = $drAmtArray['salaries & benefits']['amt'];
        $cat_summry['taxes_count'] = $drAmtArray['taxes']['count'];
        $cat_summry['taxes_amt'] = $drAmtArray['taxes']['amt'];
        $cat_summry['insurance_count'] = $drAmtArray['insurance']['count'];
        $cat_summry['insurance_amt'] = $drAmtArray['insurance']['amt'];
        $cat_summry['cash_withdrawal_count'] = $drAmtArray['cash withdrawal']['count'];
        $cat_summry['cash_withdrawal_amt'] = $drAmtArray['cash withdrawal']['amt'];
        $cat_summry['card_processor_fees_count'] = $drAmtArray['card processor fees']['count'];
        $cat_summry['card_processor_fees_amt'] = $drAmtArray['card processor fees']['amt'];
        $cat_summry['chargeback_count'] = $drAmtArray['chargeback']['count']; 
        $cat_summry['chargeback_amt'] = $drAmtArray['chargeback']['amt'];
        $cat_summry['credit_card_payments_count'] = $drAmtArray['credit card payments']['count'];
        $cat_summry['credit_card_payments_amt'] = $drAmtArray['credit card payments']['amt'];
        $cat_summry['loan_repayment_emi_lenders_count'] = $drAmtArray['loan repayment/emi - lenders']['count'];
        $cat_summry['loan_repayment_emi_lenders_amt'] = $drAmtArray['loan repayment/emi - lenders']['amt'];
        $cat_summry['loan_repayment_emi_mortgage_count'] = $drAmtArray['loan repayment/emi - mortgage']['count'];
        $cat_summry['loan_repayment_emi_mortgage_amt'] = $drAmtArray['loan repayment/emi - mortgage']['amt'];
        $cat_summry['loan_repayment_emi_auto_finance_count'] = $drAmtArray['loan repayment/emi - auto finance']['count'];
        $cat_summry['loan_repayment_emi_auto_finance_amt'] = $drAmtArray['loan repayment/emi - auto finance']['amt'];
        $cat_summry['intra_account_count'] = $drAmtArray['intra account transfer']['count'];
        $cat_summry['intra_account_amt'] = $drAmtArray['intra account transfer']['amt'];
        $cat_summry['fees_ng_count'] = $drAmtArray['fees - ng']['count']; 
        $cat_summry['fees_ng_amt'] = $drAmtArray['fees - ng']['amt'];
        $cat_summry['fees_overdraft_count'] = $drAmtArray['fees - overdraft']['count'];
        $cat_summry['fees_overdraft_amt'] = $drAmtArray['fees - overdraft']['amt'];
        $cat_summry['fees_others_count'] = $drAmtArray['fees - others']['count'];
        $cat_summry['fees_others_amt'] = $drAmtArray['fees - others']['amt'];
        $cat_summry['investments_count'] = $drAmtArray['investments']['count'];
        $cat_summry['investments_amt'] = $drAmtArray['investments']['amt'];
        $cat_summry['deposited_check_return_count'] = $drAmtArray['deposited check return']['count'];
        $cat_summry['deposited_check_return_amt'] = $drAmtArray['deposited check return']['amt'];
        $cat_summry['miscellaneous_debit_count'] = $drAmtArray['miscellaneous debit']['count'];
        $cat_summry['miscellaneous_debit_amt'] = $drAmtArray['miscellaneous debit']['amt'];
        $cat_summry['travel_expenses_airlines_count'] = $drAmtArray['travel expenses - airlines']['count']; 
        $cat_summry['travel_expenses_airlines_amt'] = $drAmtArray['travel expenses - airlines']['amt'];
        $cat_summry['travel_expenses_hotels_count'] = $drAmtArray['travel expenses - hotels']['count'];
        $cat_summry['travel_expenses_hotels_amt'] = $drAmtArray['travel expenses - hotels']['amt'];
        $cat_summry['travel_epenses_car_rental_count'] = $drAmtArray['travel expenses - car rental']['count'];
        $cat_summry['travel_epenses_car_rental_amt'] = $drAmtArray['travel expenses - car rental']['amt'];
        $cat_summry['travel_expenses_others_count'] = $drAmtArray['travel expenses - others']['count'];
        $cat_summry['travel_expenses_others_amt'] = $drAmtArray['travel expenses - others']['amt'];
        $cat_summry['utilities_telephone_count'] = $drAmtArray['utilities - telephone']['count'];
        $cat_summry['utilities_telephone_amt'] = $drAmtArray['utilities - telephone']['amt'];
        $cat_summry['utilities_internet_count'] = $drAmtArray['utilities - internet']['count'];
        $cat_summry['utilities_internet_amt'] = $drAmtArray['utilities - internet']['amt'];
        $cat_summry['utilities_tv_count'] = $drAmtArray['utilities - tv']['count']; 
        $cat_summry['utilities_tv_amt'] = $drAmtArray['utilities - tv']['amt'];
        $cat_summry['utilities_power_count'] = $drAmtArray['utilities - power']['count'];
        $cat_summry['utilities_power_amt'] = $drAmtArray['utilities - power']['amt'];
        $cat_summry['utilities_water_count'] = $drAmtArray['utilities - water']['count'];
        $cat_summry['utilities_water_amt'] = $drAmtArray['utilities - water']['amt'];
        $cat_summry['utilities_others_count'] = $drAmtArray['utilities - others']['count'];
        $cat_summry['utilities_others_amt'] = $drAmtArray['utilities - others']['amt'];
        $cat_summry['total_debit_count_of_txn'] = $drAmtArray['vendor payments']['count'] + $drAmtArray['salaries & benefits']['count'] + $drAmtArray['taxes']['count'] + $drAmtArray['insurance']['count'] + $drAmtArray['cash withdrawal']['count'] + $drAmtArray['card processor fees']['count']+ $drAmtArray['chargeback']['count'] + $drAmtArray['credit card payments']['count'] + $drAmtArray['loan repayment/emi - lenders']['count'] + $drAmtArray['loan repayment/emi - mortgage']['count'] + $drAmtArray['loan repayment/emi - auto finance']['count'] + $drAmtArray['intra account transfer']['count'] + $drAmtArray['fees - ng']['count'] + $drAmtArray['fees - overdraft']['count'] + $drAmtArray['fees - others']['count'] + $drAmtArray['investments']['count'] + $drAmtArray['deposited check return']['count'] + $drAmtArray['miscellaneous debit']['count'] + $drAmtArray['travel expenses - airlines']['count'] + $drAmtArray['travel expenses - hotels']['count'] + $drAmtArray['travel expenses - car rental']['count'] + $drAmtArray['travel expenses - others']['count'] + $drAmtArray['utilities - telephone']['count'] + $drAmtArray['utilities - internet']['count'] + $drAmtArray['utilities - tv']['count'] + $drAmtArray['utilities - power']['count'] + $drAmtArray['utilities - water']['count'] + $drAmtArray['utilities - others']['count'];
        $cat_summry['total_debit_amt'] = $drAmtArray['vendor payments']['amt'] + $drAmtArray['salaries & benefits']['amt'] + $drAmtArray['taxes']['amt'] + $drAmtArray['insurance']['amt'] + $drAmtArray['cash withdrawal']['amt'] + $drAmtArray['card processor fees']['amt']+ $drAmtArray['chargeback']['amt'] + $drAmtArray['credit card payments']['amt'] + $drAmtArray['loan repayment/emi - lenders']['amt'] + $drAmtArray['loan repayment/emi - mortgage']['amt'] + $drAmtArray['loan repayment/emi - auto finance']['amt'] + $drAmtArray['intra account transfer']['amt'] + $drAmtArray['fees - ng']['amt'] + $drAmtArray['fees - overdraft']['amt'] + $drAmtArray['fees - others']['amt'] + $drAmtArray['investments']['amt'] + $drAmtArray['deposited check return']['amt'] + $drAmtArray['miscellaneous debit']['amt'] + $drAmtArray['travel expenses - airlines']['amt'] + $drAmtArray['travel expenses - hotels']['amt'] + $drAmtArray['travel expenses - car rental']['amt'] + $drAmtArray['travel expenses - others']['amt'] + $drAmtArray['utilities - telephone']['amt'] + $drAmtArray['utilities - internet']['amt'] + $drAmtArray['utilities - tv']['amt'] + $drAmtArray['utilities - power']['amt'] + $drAmtArray['utilities - water']['amt'] + $drAmtArray['utilities - others']['amt'];
        $cat_summry['beginning_date'] = $beginning_date;
        $cat_summry['end_date'] = $end_date;
        $summry_id = $this->category_summary->insertCategorySummaryData($cat_summry);
        
        $cat_consolidated = array();
        $cat_consolidated['history_id'] = $id;
        $cat_consolidated['category_type'] = 'Categories_Consolidated';
        $cat_consolidated['sales_card_count'] = $totalCrAmtArray['sales - card']['count'];
        $cat_consolidated['sales_card_amt'] = $totalCrAmtArray['sales - card']['amt'];
        //$cat_consolidated['sales_non_card_count'] = $totalCrAmtArray['sales - non card']['count'];
        //$cat_consolidated['sales_non_card_amt'] = $totalCrAmtArray['sales - non card']['amt'];
        /*Add new category*/
        $cat_consolidated['sales_non_card_uber_count'] = $totalCrAmtArray['sales - non card (uber)']['count'];
        $cat_consolidated['sales_non_card_uber_amt'] = $totalCrAmtArray['sales - non card (uber)']['amt'];
        $cat_consolidated['sales_non_card_didi_count'] = $totalCrAmtArray['sales - non card (didi)']['count'];
        $cat_consolidated['sales_non_card_didi_amt'] = $totalCrAmtArray['sales - non card (didi)']['amt'];
        $cat_consolidated['sales_non_card_rappi_count'] = $totalCrAmtArray['sales - non card (rappi)']['count'];
        $cat_consolidated['sales_non_card_rappi_amt'] = $totalCrAmtArray['sales - non card (rappi)']['amt'];
        $cat_consolidated['sales_non_card_sin_delantal_count'] = $totalCrAmtArray['sales - non card (sin delantal)']['count'];
        $cat_consolidated['sales_non_card_sin_delantal_amt'] = $totalCrAmtArray['sales - non card (sin delantal)']['amt'];
        $cat_consolidated['sales_non_card_other_count'] = $totalCrAmtArray['sales - non card (other)']['count'];
        $cat_consolidated['sales_non_card_other_amt'] = $totalCrAmtArray['sales - non card (other)']['amt'];
        /*End new cateory*/
        $cat_consolidated['cash_deposit_count'] = $totalCrAmtArray['cash deposit']['count'];
        $cat_consolidated['cash_deposit_amt'] = $totalCrAmtArray['cash deposit']['amt'];
        $cat_consolidated['refund_reversals_count'] = $totalCrAmtArray['refund/reversals']['count'];
        $cat_consolidated['refund_reversals_amt'] = $totalCrAmtArray['refund/reversals']['amt'];
        $cat_consolidated['intra_account_transfer_count'] = $totalCrAmtArray['intra account transfer']['count'];
        $cat_consolidated['intra_account_transfer_amt'] = $totalCrAmtArray['intra account transfer']['amt'];
        $cat_consolidated['ng_check_count'] = $totalCrAmtArray['ng check']['count'];
        $cat_consolidated['ng_check_amt'] = $totalCrAmtArray['ng check']['amt'];
        $cat_consolidated['loans_count'] = $totalCrAmtArray['loans']['count']; 
        $cat_consolidated['loans_amt'] = $totalCrAmtArray['loans']['amt'];
        $cat_consolidated['investment_income_count'] = $totalCrAmtArray['investment income']['count'];
        $cat_consolidated['investment_income_amt'] = $totalCrAmtArray['investment income']['amt'];
        $cat_consolidated['insurance_claim_count'] = $totalCrAmtArray['insurance claim']['count'];
        $cat_consolidated['insurance_claim_amt'] = $totalCrAmtArray['insurance claim']['amt'];
        $cat_consolidated['miscellaneous_credits_count'] = $totalCrAmtArray['miscellaneous credits']['count'];
        $cat_consolidated['miscellaneous_credits_amt'] = $totalCrAmtArray['miscellaneous credits']['amt'];
        $cat_consolidated['total_credit_count_of_txn'] = $totalCrAmtArray['sales - card']['count'] + $totalCrAmtArray['sales - non card (uber)']['count'] + $totalCrAmtArray['sales - non card (didi)']['count'] + $totalCrAmtArray['sales - non card (rappi)']['count'] + $totalCrAmtArray['sales - non card (sin delantal)']['count'] + $totalCrAmtArray['sales - non card (other)']['count'] + $totalCrAmtArray['cash deposit']['count'] + $totalCrAmtArray['refund/reversals']['count'] + $totalCrAmtArray['intra account transfer']['count'] + $totalCrAmtArray['ng check']['count'] + $totalCrAmtArray['loans']['count'] + $totalCrAmtArray['investment income']['count'] + $totalCrAmtArray['insurance claim']['count'] + $totalCrAmtArray['miscellaneous credits']['count'];
        $cat_consolidated['total_credit_amount'] = $totalCrAmtArray['sales - card']['amt'] + $totalCrAmtArray['sales - non card (uber)']['amt'] + $totalCrAmtArray['sales - non card (didi)']['amt'] + $totalCrAmtArray['sales - non card (rappi)']['amt'] + $totalCrAmtArray['sales - non card (sin delantal)']['amt'] + $totalCrAmtArray['sales - non card (other)']['amt'] + $totalCrAmtArray['cash deposit']['amt'] + $totalCrAmtArray['refund/reversals']['amt'] + $totalCrAmtArray['intra account transfer']['amt'] + $totalCrAmtArray['ng check']['amt'] + $totalCrAmtArray['loans']['amt'] + $totalCrAmtArray['investment income']['amt'] + $totalCrAmtArray['insurance claim']['amt'] + $totalCrAmtArray['miscellaneous credits']['amt'];
        $cat_consolidated['vendor_payments_count'] = $totalDrAmtArray['vendor payments']['count'];
        $cat_consolidated['vendor_payments_amt'] = $totalDrAmtArray['vendor payments']['amt'];
        $cat_consolidated['salaries_benefits_count'] = $totalDrAmtArray['salaries & benefits']['count'];
        $cat_consolidated['salaries_benefits_amt'] = $totalDrAmtArray['salaries & benefits']['amt'];
        $cat_consolidated['taxes_count'] = $totalDrAmtArray['taxes']['count'];
        $cat_consolidated['taxes_amt'] = $totalDrAmtArray['taxes']['amt'];
        $cat_consolidated['insurance_count'] = $totalDrAmtArray['insurance']['count'];
        $cat_consolidated['insurance_amt'] = $totalDrAmtArray['insurance']['amt'];
        $cat_consolidated['cash_withdrawal_count'] = $totalDrAmtArray['cash withdrawal']['count'];
        $cat_consolidated['cash_withdrawal_amt'] = $totalDrAmtArray['cash withdrawal']['amt'];
        $cat_consolidated['card_processor_fees_count'] = $totalDrAmtArray['card processor fees']['count'];
        $cat_consolidated['card_processor_fees_amt'] = $totalDrAmtArray['card processor fees']['amt'];
        $cat_consolidated['chargeback_count'] = $totalDrAmtArray['chargeback']['count']; 
        $cat_consolidated['chargeback_amt'] = $totalDrAmtArray['chargeback']['amt'];
        $cat_consolidated['credit_card_payments_count'] = $totalDrAmtArray['credit card payments']['count'];
        $cat_consolidated['credit_card_payments_amt'] = $totalDrAmtArray['credit card payments']['amt'];
        $cat_consolidated['loan_repayment_emi_lenders_count'] = $totalDrAmtArray['loan repayment/emi - lenders']['count'];
        $cat_consolidated['loan_repayment_emi_lenders_amt'] = $totalDrAmtArray['loan repayment/emi - lenders']['amt'];
        $cat_consolidated['loan_repayment_emi_mortgage_count'] = $totalDrAmtArray['loan repayment/emi - mortgage']['count'];
        $cat_consolidated['loan_repayment_emi_mortgage_amt'] = $totalDrAmtArray['loan repayment/emi - mortgage']['amt'];
        $cat_consolidated['loan_repayment_emi_auto_finance_count'] = $totalDrAmtArray['loan repayment/emi - auto finance']['count'];
        $cat_consolidated['loan_repayment_emi_auto_finance_amt'] = $totalDrAmtArray['loan repayment/emi - auto finance']['amt'];
        $cat_consolidated['intra_account_count'] = $totalDrAmtArray['intra account transfer']['count'];
        $cat_consolidated['intra_account_amt'] = $totalDrAmtArray['intra account transfer']['amt'];
        $cat_consolidated['fees_ng_count'] = $totalDrAmtArray['fees - ng']['count']; 
        $cat_consolidated['fees_ng_amt'] = $totalDrAmtArray['fees - ng']['amt'];
        $cat_consolidated['fees_overdraft_count'] = $totalDrAmtArray['fees - overdraft']['count'];
        $cat_consolidated['fees_overdraft_amt'] = $totalDrAmtArray['fees - overdraft']['amt'];
        $cat_consolidated['fees_others_count'] = $totalDrAmtArray['fees - others']['count'];
        $cat_consolidated['fees_others_amt'] = $totalDrAmtArray['fees - others']['amt'];
        $cat_consolidated['investments_count'] = $totalDrAmtArray['investments']['count'];
        $cat_consolidated['investments_amt'] = $totalDrAmtArray['investments']['amt'];
        $cat_consolidated['deposited_check_return_count'] = $totalDrAmtArray['deposited check return']['count'];
        $cat_consolidated['deposited_check_return_amt'] = $totalDrAmtArray['deposited check return']['amt'];
        $cat_consolidated['miscellaneous_debit_count'] = $totalDrAmtArray['miscellaneous debit']['count'];
        $cat_consolidated['miscellaneous_debit_amt'] = $totalDrAmtArray['miscellaneous debit']['amt'];
        $cat_consolidated['travel_expenses_airlines_count'] = $totalDrAmtArray['travel expenses - airlines']['count']; 
        $cat_consolidated['travel_expenses_airlines_amt'] = $totalDrAmtArray['travel expenses - airlines']['amt'];
        $cat_consolidated['travel_expenses_hotels_count'] = $totalDrAmtArray['travel expenses - hotels']['count'];
        $cat_consolidated['travel_expenses_hotels_amt'] = $totalDrAmtArray['travel expenses - hotels']['amt'];
        $cat_consolidated['travel_epenses_car_rental_count'] = $totalDrAmtArray['travel expenses - car rental']['count'];
        $cat_consolidated['travel_epenses_car_rental_amt'] = $totalDrAmtArray['travel expenses - car rental']['amt'];
        $cat_consolidated['travel_expenses_others_count'] = $totalDrAmtArray['travel expenses - others']['count'];
        $cat_consolidated['travel_expenses_others_amt'] = $totalDrAmtArray['travel expenses - others']['amt'];
        $cat_consolidated['utilities_telephone_count'] = $totalDrAmtArray['utilities - telephone']['count'];
        $cat_consolidated['utilities_telephone_amt'] = $totalDrAmtArray['utilities - telephone']['amt'];
        $cat_consolidated['utilities_internet_count'] = $totalDrAmtArray['utilities - internet']['count'];
        $cat_consolidated['utilities_internet_amt'] = $totalDrAmtArray['utilities - internet']['amt'];
        $cat_consolidated['utilities_tv_count'] = $totalDrAmtArray['utilities - tv']['count']; 
        $cat_consolidated['utilities_tv_amt'] = $totalDrAmtArray['utilities - tv']['amt'];
        $cat_consolidated['utilities_power_count'] = $totalDrAmtArray['utilities - power']['count'];
        $cat_consolidated['utilities_power_amt'] = $totalDrAmtArray['utilities - power']['amt'];
        $cat_consolidated['utilities_water_count'] = $totalDrAmtArray['utilities - water']['count'];
        $cat_consolidated['utilities_water_amt'] = $totalDrAmtArray['utilities - water']['amt'];
        $cat_consolidated['utilities_others_count'] = $totalDrAmtArray['utilities - others']['count'];
        $cat_consolidated['utilities_others_amt'] = $totalDrAmtArray['utilities - others']['amt'];
        $cat_consolidated['total_debit_count_of_txn'] = $totalDrAmtArray['vendor payments']['count'] + $totalDrAmtArray['salaries & benefits']['count'] + $totalDrAmtArray['taxes']['count'] + $totalDrAmtArray['insurance']['count'] + $totalDrAmtArray['cash withdrawal']['count'] + $totalDrAmtArray['card processor fees']['count']+ $totalDrAmtArray['chargeback']['count'] + $totalDrAmtArray['credit card payments']['count'] + $totalDrAmtArray['loan repayment/emi - lenders']['count'] + $totalDrAmtArray['loan repayment/emi - mortgage']['count'] + $totalDrAmtArray['loan repayment/emi - auto finance']['count'] + $totalDrAmtArray['intra account transfer']['count'] + $totalDrAmtArray['fees - ng']['count'] + $totalDrAmtArray['fees - overdraft']['count'] + $totalDrAmtArray['fees - others']['count'] + $totalDrAmtArray['investments']['count'] + $totalDrAmtArray['deposited check return']['count'] + $totalDrAmtArray['miscellaneous debit']['count'] + $totalDrAmtArray['travel expenses - airlines']['count'] + $totalDrAmtArray['travel expenses - hotels']['count'] + $totalDrAmtArray['travel expenses - car rental']['count'] + $totalDrAmtArray['travel expenses - others']['count'] + $totalDrAmtArray['utilities - telephone']['count'] + $totalDrAmtArray['utilities - internet']['count'] + $totalDrAmtArray['utilities - tv']['count'] + $totalDrAmtArray['utilities - power']['count'] + $totalDrAmtArray['utilities - water']['count'] + $totalDrAmtArray['utilities - others']['count'];
        $cat_consolidated['total_debit_amt'] = $totalDrAmtArray['vendor payments']['amt'] + $totalDrAmtArray['salaries & benefits']['amt'] + $totalDrAmtArray['taxes']['amt'] + $totalDrAmtArray['insurance']['amt'] + $totalDrAmtArray['cash withdrawal']['amt'] + $totalDrAmtArray['card processor fees']['amt']+ $totalDrAmtArray['chargeback']['amt'] + $totalDrAmtArray['credit card payments']['amt'] + $totalDrAmtArray['loan repayment/emi - lenders']['amt'] + $totalDrAmtArray['loan repayment/emi - mortgage']['amt'] + $totalDrAmtArray['loan repayment/emi - auto finance']['amt'] + $totalDrAmtArray['intra account transfer']['amt'] + $totalDrAmtArray['fees - ng']['amt'] + $totalDrAmtArray['fees - overdraft']['amt'] + $totalDrAmtArray['fees - others']['amt'] + $totalDrAmtArray['investments']['amt'] + $totalDrAmtArray['deposited check return']['amt'] + $totalDrAmtArray['miscellaneous debit']['amt'] + $totalDrAmtArray['travel expenses - airlines']['amt'] + $totalDrAmtArray['travel expenses - hotels']['amt'] + $totalDrAmtArray['travel expenses - car rental']['amt'] + $totalDrAmtArray['travel expenses - others']['amt'] + $totalDrAmtArray['utilities - telephone']['amt'] + $totalDrAmtArray['utilities - internet']['amt'] + $totalDrAmtArray['utilities - tv']['amt'] + $totalDrAmtArray['utilities - power']['amt'] + $totalDrAmtArray['utilities - water']['amt'] + $totalDrAmtArray['utilities - others']['amt'];
        $cat_consolidated['beginning_date'] = $beginning_date_consolidate;
        $cat_consolidated['end_date'] = $end_date_consolidate;
        $consolidated_id = $this->category_summary->insertConsolidatedCategorySummaryData($cat_consolidated);
        //die('here');
        
        
        $dsData = array();
        $dsData['caseId'] = $id;
        $jsonData = json_encode($dsData);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://127.0.0.1:8092/bank_statements_integration/submit-spreading-details",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR=> true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"caseId=".$id,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
            ),
        ));
        
        $response = curl_exec($curl);
        echo"<pre>";
        print_r($response);
        if ($response === false){
            $response = curl_error($curl);
        }
        $api_obj = json_decode($response, true);
        $input_res = array();
        if($api_obj['code']){
            //$input_srd['status'] = 'Complete';
            $input_res['success_type'] = 'success';
            if($histResult->qa_user_id==0){
                $input['qa_user_id'] = $this->session->userdata('user_id');
            }
            $this->tpl_history->updateRecords($id,$input);
            $data['success']= true; 
        }
        else{
            //$input_srd['status'] = 'Rejected-downstream';
            $input_res['success_type'] = 'error';
            if($histResult->qa_user_id==0){
                $input['qa_user_id'] = $this->session->userdata('user_id');
            }
            $this->tpl_history->updateRecords($id,$input);
            $data['success']= false;
        }
        
        $input_res['history_id'] = $id;
        $input_res['RESPONSE_JSON'] = $response;
        $input_res['api_type'] = 'Downstream';
        $this->tpl_history->addNewResponceSendDownStreamData($input_res);
        
        echo json_encode($data); die();  
              
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

    function showErrorMsgPopup(){       
        $id = $this->input->get('history_id');  
        $error_count = 0;
        $case_error_log_data = $this->case_error_log->getRecordByHistoryId($id);
        /*echo $case_error_log_data[0]->tpl_not_found;
        echo"<pre>";
        print_r($case_error_log_data);
        echo"</pre>";
        die;*/
        if($case_error_log_data){
            if($case_error_log_data[0]->tpl_not_found==0){
                $error = 'Template not found ';
            }
            else{
                $error ='';
                foreach($case_error_log_data as $log){
                    if($log->account_number==0){
                        $error_count++;
                        $error .= $error_count.'. Account number not found</br>';
                    }
                    if($log->aaccount_holder_name==0){
                        $error_count++;
                        $error .= $error_count.'. Account holder name not found</br>';
                    }
                    if($log->account_type==0){
                        $error_count++;
                        $error .= $error_count.'. Account type not found</br>';
                    }
                    if($log->name_of_bank==0){
                        $error_count++;
                        $error .= $error_count.'. Bank name not found</br>';
                    }
                    if($log->bank_address==0){
                        $error_count++;
                        $error .= $error_count.'. Bank address not found</br>';
                    }
                    if($log->bank_city==0){
                        $error_count++;
                        $error .= $error_count.'. Bank city not found</br>';
                    }
                    if($log->bank_state==0){
                        $error_count++;
                        $error .= $error_count.'. Bank state not found</br>';
                    }
                    if($log->bank_zip==0){
                        $error_count++;
                        $error .= $error_count.'. Bank zip not found</br>';
                    }
                    if($log->current_balance==0){
                        $error_count++;
                        $error .= $error_count.'. Current balance not found</br>';
                    }
                    if($log->start_date==0){
                        $error_count++;
                        $error .= $error_count.'. start date not found</br>';
                    }
                    if($log->end_date==0){
                        $error_count++;
                        $error .= $error_count.'. End date not found</br>';
                    }
                    if($log->closing_balance==0){
                        $error_count++;
                        $error .= $error_count.'. Closing balance not found</br>';
                    }
                    if($log->check_sum==0){
                        $error_count++;
                        $error .= $error_count.'. Checksum not zero</br>';
                    }
                }
            }
        }
        $output['html']= $error;
        $output['success']= true;
        echo json_encode($output); die();     
        
    }
    
}