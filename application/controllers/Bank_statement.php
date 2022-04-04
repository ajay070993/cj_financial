<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_statement extends CI_Controller {
    private $page_array = array();
    private $isCompleteMultiAcc = false;
    private $newFolderName;
    private $checkAllPdfProcess=false;
    private $history_id='';
  	function __construct() {
  		Parent::__construct();
  		   
	      
          $this->common_model->checkUserLogin();
          if($this->session->userdata('application_type') == 'fs'){
            redirect('fs-dashboard');
        }
          $this->common_model->checkLoginUserStatus();  
        $this->common_model->checkCjXtractUser();
        $this->load->model('bank_statement_model', 'bank_statement');
          $this->load->model('banks_model', 'banks');
          $this->load->model('Tpl_history_model', 'tpl_history');
          $this->load->model('Tpl_content_model', 'tpl_content');
          $this->load->model('bank_address_model', 'bank_address');
          $this->load->model('Bulk_upload_model', 'bulk_upload');
          $this->load->model('Bank_summary_level_data', 'summary_level_data');
          $this->load->model('Bank_customer_txn_data', 'customer_txn_data');
          $this->load->model('Tpl_case_error_log', 'case_error_log');
          
          /*For AES_encrypt*/
          $this->load->library('encryption');
          
          $this->encryption->initialize(
              array(
                  'cipher' => 'aes-256',
                  'mode' => 'ctr',
                  'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ0'
              )
          );
          
          
          $this->load->library('email');
          $config['protocol'] = "smtp";
          $config['smtp_host'] = 'ssl://smtp.googlemail.com';
          $config['smtp_port'] = 465;
          $config['smtp_user'] = 'nirdesh.kumawat@ollosoft.com';
          $config['smtp_pass'] = 'N!rdesh@123';
          $config['charset'] = "utf-8";
          $this->email->initialize($config);
          
          $this->session->set_userdata(array('type_of_upload'=>1));
  	}

    function index(){
        $output['page_title'] = '';
        $output['allHistory'] = $this->tpl_history->getAllHistoryRecords();
        $output['countTemplate'] = $this->banks->countTemplate();
        $output['countSpreading'] = $this->tpl_history->countSpreading();
        $output['countLastWeek'] = $this->tpl_history->countLastWeek();
        $output['avgSpreadTime'] = $this->tpl_history->avgSpreadTime();
        $this->load->view('dashboard',$output);
        
        // $this->load->view('bank_statement/form_2',$output);
    }
    
    function spreading(){
        $output['page_title'] = '';
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        $this->load->view('spreading',$output);
        // $this->load->view('bank_statement/form_2',$output);
    }
    
    function createTemplate(){
        $output['page_title'] = '';
        $output['add_template']=true;
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        error_reporting(0);
        $output['page_title'] = 'Upload file';
        $output['message']    = '';
        $success = true;
        $input = array();
        if(isset($_POST) && !empty($_POST))
        {
            $output['callBackFunction'] = 'getUploadFile';
            $output['file_name'] = $file_name;
            $output['text_file_name'] = $txtFilename;//'boa.txt';
            $success = true;
        }
        else
        {
            $message = "Something went wrong";
            $success = false;
        }
        //$output['message'] = $message;
        $output['success'] = $success;
        //echo json_encode($output);die;
        //die('here');
        //$this->load->view('createTemplate',$output);
        echo $this->load->view('createTemplate',$output, TRUE);
        die;
        //die('here');
        //$output['redirectURL'] = site_url('templates');
    }

    function uploadPdfFile(){
        error_reporting(0);
        $output['page_title'] = 'Upload file';
        $output['message']    = '';
        $success = true;    
        $input = array();       
        if(isset($_FILES['image_name']['name']) && $_FILES['image_name']['name']) 
        {
            $directory = './assets/uploads/bank_statement'; 
            @mkdir($directory, 0777); 
            @chmod($directory,  0777);  
            $config['upload_path'] = $directory;
            $config['allowed_types'] = '*';           
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('image_name')) 
            {
                $image_data = $this->upload->data();
                $file_name = $image_data['file_name'];
                
                /**Start Convert PDF to Text */
                $endpoint = "https://api.zamzar.com/v1/jobs";
                $apiKey = "798c3b57042203d967862da4b7445dce5d1121f5";
                //$sourceFile = 'https://s3.amazonaws.com/zamzar-samples/sample.pdf';
                #$sourceFile = base_url('./assets/uploads/bank_statement/'. $file_name);
                if(function_exists('curl_file_create')) {
                    $sourceFile = curl_file_create('./assets/uploads/bank_statement/'. $file_name);
                } else {
                    $sourceFile = '@' . realpath('./assets/uploads/bank_statement/'. $file_name);
                }
                $targetFormat = "txt";
                $postData = array(
                    "source_file" => $sourceFile,
                    "target_format" => $targetFormat
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
                $body = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($body, true);
                $target_id = "";
                if($response){
                    //print_r($response);
                    $jobID = $response['id'];
                    $target_id = $this->recursiveFunctionTargetId($jobID);
                } 
                
                if(empty($target_id)){
                    $message = "Something went wrong";
                    $success = false;
                }else{
                    $fileID = $target_id;
                    $realPath = FCPATH.'assets/uploads/bank_statement/';
                    $txtFilename = date('Y-m-d H:i:s').'upload.txt';
                    $actualFilePath = $realPath.''.$txtFilename;
                    $endpoint2 = "https://api.zamzar.com/v1/files/$fileID/content";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpoint2);
                    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                    $fh = fopen($actualFilePath, "w+");
                    curl_setopt($ch, CURLOPT_FILE, $fh);
                    $body = curl_exec($ch);
                    //$job = json_decode($body, true);
                    
                    curl_close($ch);
                       
                    //}
                    /**End Convert PDF to Text */
                    $results = $this->tpl_content->getAllTplContentRecords();
                    $bankContentMatches = array();
                    $bankUrlMatches = array();
                    $directory = './assets/uploads/bank_statement';
                    //$txtFilename = 'jp11.txt';
                    
                    if(count($results)>0){
                        foreach($results as $key=>$result){
                            $bankMatches[$key]['credit_end_string']=false;
                            $bankMatches[$key]['debit_end_string']=false;
                            $bankMatches[$key]['checks_end_string']=false;
                            $bankMatches[$key]['credit_start_string']=false;
                            $i = 1;
                            $content = '';
                            $checkDomain = true;
                            $domain_name = '';
                            $fh = fopen($directory.'/'.$txtFilename,'r');
                            while ($line = fgets($fh)) {
                                $i++;
                                if($checkDomain){
                                    if(preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $line, $matches)){
                                        $domain_name = $matches[0][0];
                                        if($domain_name==$result->bank_url){
                                            $bankUrlMatches[$result->bank_id] = true;
                                            $checkDomain = false;
                                        }
                                    }
                                }
                                
                                $lineArray = explode('  ', $line);
                                $filterArray = array_filter($lineArray);
                                $filterArray = array_map('trim', $filterArray);
                                if($result->credit_start_string!="" && in_array($result->credit_start_string, $filterArray)){
                                    $bankMatches[$key]['credit_start_string']=true;
                                }
                                if($result->credit_end_string!="" && in_array($result->credit_end_string, $filterArray)){
                                    $bankMatches[$key]['credit_end_string']=true;
                                }
                                if($result->debit_end_string!="" && in_array($result->debit_end_string, $filterArray)){
                                    $bankMatches[$key]['debit_end_string']=true;
                                }
                                if($result->checks_end_string!="" && in_array($result->checks_end_string, $filterArray)){
                                    $bankMatches[$key]['checks_end_string']=true;
                                }
                                
                                if($i<=$result->end_line_no){
                                    $content .= $line;
                                }
                            }
                            
                            $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Sept", "Oct", "Nov", "Dec"];
                            $content = str_replace($month, "", $content);
                            //print_r($content);
                            //die('here');
                            $content = preg_replace('/\s+/', '', $content);
                            $content = preg_replace('/[0-9]+/', '', $content);
                            fclose($fh);
                            similar_text($result->content, $content, $percent);
                            //echo $percent;
                            //$bankMatches[$result->bank_id]=$percent;
                            $bankMatches[$key]['bank_id']=$result->bank_id;
                            $bankMatches[$key]['percent']=round($percent);
                        }
                        $bankCountInt = array();
                        if(count($bankMatches)>0){
                            foreach($bankMatches as $key=>$bankMatch){
                                $bankCountInt[$bankMatch['bank_id']] = 0;
                                if($bankMatch['percent']>45){
                                    if($bankMatch['credit_end_string']==true){
                                        $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                        //$bankCountInt[$bankMatch['bank_id']];
                                        //die;
                                    }
                                    if($bankMatch['debit_end_string']==true){
                                        $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                        //$bankCountInt[$bankMatch['bank_id']];
                                        //die;
                                    }
                                    if($bankMatch['checks_end_string']==true){
                                        $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                    }
                                    if($bankMatch['credit_start_string']==true){
                                        $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                    }
                                    
                                    $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                }
                                
                            }
                            
                            foreach (array_keys($bankCountInt, 0) as $key) {
                                unset($bankCountInt[$key]);
                            }
                            
                            if(!empty($bankCountInt) && max($bankCountInt)>0){
                                $maxs = array_keys($bankCountInt, max($bankCountInt));
                                $bank_id = $maxs[0];
                                $output['accuracy'] = max($bankCountInt);
                                $res = $this->banks->getBankName(array_search (max($bankCountInt), $bankCountInt));
                                $output['bank_name'] = $res->bank_name;
                            }else{
                                $output['bank_name'] = '';
                                $output['accuracy'] = 0;
                            }
                        }else{
                            $output['bank_name'] = '';
                            $output['accuracy'] = 0;
                        }
                        
                        
                    }else{
                        $output['bank_name'] = '';
                        $output['accuracy'] = 0;
                    }
                    
                    //$message = 'Pdf insert successfully';
                    $output['callBackFunction'] = 'getUploadFile'; 
                    $output['file_name'] = $file_name;
                    $output['text_file_name'] = $txtFilename;//'boa.txt';
                    $success = true;
                    /*echo"<pre>";
                    print_r($output);
                    echo"</pre>";
                    die('here');*/
                }
            }
            else
            {
                $message = $this->upload->display_errors();
                $success = false;
            }
        }
        else
        {
            $message = "Please select a file";
            $success = false;
        }
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output);die;
    }

    function addString(){
        $output['page_title'] = 'Add statement string';
        $output['message']    = '';
        $output['id'] = ''; 
//        echo"<pre>";
//        print_r($_POST);
//        die('here');
        if(isset($_POST) && !empty($_POST)){            
            $success = true;
            
            if(trim($this->input->post('template-action'))==0){
                $this->form_validation->set_rules('bank_id', 'Bank name', 'trim|required');
            }else{
                $this->form_validation->set_rules('bank_name', 'Bank name', 'trim|required');
            }
            $this->form_validation->set_rules('account_number_string', 'Account number string', 'trim|required');
            if ($this->form_validation->run()) {
                $input = array(); 
                $input['account_number_string'] = $this->input->post('account_number_string');
                $input['credit_start_string'] = $this->input->post('credit_start_string');
                $input['credit_table_format'] = trim($this->input->post('credit_table_format'));
                $input['credit_end_string'] = $this->input->post('credit_end_string');
                $input['debit_start_string'] = $this->input->post('debit_start_string');
                $input['debit_table_format'] = trim($this->input->post('debit_table_format'));
                $input['debit_end_string'] = $this->input->post('debit_end_string');
                $input['checks_start_string'] = $this->input->post('checks_start_string');
                $input['cheque_table_format'] = trim($this->input->post('cheque_table_format'));
                $input['checks_end_string'] = $this->input->post('checks_end_string');
                $input['fetch_check_from_desc'] = $this->input->post('fetch_check_from_desc');
                
                /**Other Transaction Insert */
                for($i=1;$i<6;$i++){
                    $input['txn_sec_'.$i] = $this->input->post('txn_sec_'.$i);
                    $input['txn_'.$i.'_start_string'] = $this->input->post('txn_'.$i.'_start_string');
                    $input['txn_'.$i.'_table_format'] = trim($this->input->post('txn_'.$i.'_table_format'));
                    $input['txn_'.$i.'_end_string'] = $this->input->post('txn_'.$i.'_end_string');
                    $input['txn_'.$i.'_type'] = $this->input->post('txn_'.$i.'_type');
                }
                /**End Transaction */
                
                /**Service Fee Start*/
                $input['service_fee_title_1'] = $this->input->post('service_fee_title_1');
                $input['service_fee_pattern_1'] = $this->input->post('service_fee_pattern_1');
                $input['service_fee_type_1'] = $this->input->post('service_fee_type_1');
                $input['service_fee_title_2'] = $this->input->post('service_fee_title_2');
                $input['service_fee_pattern_2'] = $this->input->post('service_fee_pattern_2');
                $input['service_fee_type_2'] = $this->input->post('service_fee_type_2');
                
                /**End Service Fee */
                
                
                $input['account_holder_name'] = $this->input->post('account_holder_name');
                $input['account_type'] = $this->input->post('account_type');
               
                $input['name_of_bank'] = $this->input->post('name_of_bank');
                $input['bank_address'] = $this->input->post('bank_address');
                $input['bank_city'] = $this->input->post('bank_city');
                $input['bank_state'] = $this->input->post('bank_state');
                $input['bank_zip'] = $this->input->post('bank_zip');
               
                $input['start_date'] = $this->input->post('start_date');
                $input['end_date'] = $this->input->post('end_date');
                $input['open_balance'] = $this->input->post('open_balance');
                $input['close_balance'] = $this->input->post('close_balance');
                $input['txn_start_from'] = $this->input->post('txn_start_from');
                $input['unique_string'] = $this->input->post('unique_string');
                
                //$input['native_vs_non_native'] = $this->input->post('native_vs_non_native');
                $input['updated_on'] = date("Y-m-d h:i:sa");
                //$input['check_sum'] = $this->input->post('check_sum');
                //$input['summary_and_transaction_match'] = $this->input->post('summary_and_transaction_match');
                $input['pages'] = $this->input->post('pages');
                $input['remove_string'] = $this->input->post('remove_string');
                $input['ignore_string'] = $this->input->post('ignore_string');
                
                $input['currency'] = $this->input->post('currency');
                $input['bank_type'] = $this->input->post('bank_type');
                $input['bank_stmt_format'] = $this->input->post('bank_stmt_format');
                
                $input['add_date'] = $this->common_model->getDefaultToGMTDate(time());  
                $end_line_no = $this->input->post('end_line_no');
                $input['uploader_type'] = 1; 
                if(trim($this->input->post('template-action'))==0){      
                    $input['bank_id'] = $this->input->post('bank_id');
                    $bank_id = $this->input->post('bank_id');
                    $exist_record = $this->bank_statement->getSingleRecordByBankId($bank_id);
                    if($exist_record){
                        $result = $this->bank_statement->updateRecordByBankId($bank_id,$input);
                        if($this->db->affected_rows()>0){
                            /**Add content of Template for intelligence*/
                            $res = $this->bank_statement->getTextFileName($bank_id);
                            $text_file_name = $res->text_file_name;
                            $directory = './assets/uploads/bank_statement';
                            $fh = fopen($directory.'/'.$text_file_name,'r');
                            $i = 1;
                            $content = '';
                            $checkDomain = true;
                            $domain_name = '';
                            while ($line = fgets($fh)) {
                                $i++;
                                if($checkDomain){
                                    if(preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $line, $matches)){
                                        $domain_name = $matches[0][0];
                                        $checkDomain = false;
                                    }
                                }
                                if($i<=$end_line_no){
                                    $content .= $line;
                                }
                            }
                            $content = preg_replace('/\s+/', '', $content);
                            $content = preg_replace('/[0-9]+/', '', $content);
                            fclose($fh);
                            $exist_record_count = $this->tpl_content->checkRecordExist($bank_id);
                            if($exist_record_count>0){
                                $tplContentArray =array();
                                $tplContentArray['content'] = $content;
                                $tplContentArray['end_line_no'] = $end_line_no;
                                $result = $this->tpl_content->updateRecordByBankId($bank_id,$tplContentArray);
                            }else{
                                $tplContentArray =array();
                                $tplContentArray['bank_id'] = $bank_id;
                                $tplContentArray['content'] = $content;
                                $tplContentArray['bank_url'] = $domain_name;
                                $tplContentArray['end_line_no'] = $end_line_no;
                                $this->tpl_content->addNewRecords($tplContentArray);
                            }
                            
                            /**End*/
                            $message = 'Record updated successfully';
                            $output['redirectURL'] = site_url('templates');
                            $success = true;
                        }else{
                            $message = 'Something went wrong';
                            $success = false;
                        }
                    }
                    else{
                        $message = 'Bank record not found';
                        $success = false;
                    }
                    
                }else{
                    $bankArray =array();
                    $bankArray['bank_name'] = $this->input->post('bank_name');
                    $bankArray['created_on'] = date("Y-m-d h:i:sa");
                    $id = $this->banks->addNewRecords($bankArray);
                    if($id){
                        $input['bank_id'] = $id;
                        if($this->input->post('file-name')!=""){
                            $input['file_name'] = $this->input->post('file-name');
                        }
                        if($this->input->post('text-file-name')!=""){
                            $input['text_file_name'] = $this->input->post('text-file-name');
                        }
                        $this->bank_statement->addNewRecords($input);
                        
                        /**Add content of Template for intelligence*/
                        $text_file_name = $this->input->post('text-file-name');
                        $directory = './assets/uploads/bank_statement';
                        $fh = fopen($directory.'/'.$text_file_name,'r');
                        $i = 1;
                        $checkDomain = true;
                        $content = '';
                        $domain_name = '';
                        while ($line = fgets($fh)) {
                            $i++;
                            if($checkDomain){
                                if(preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $line, $matches)){
                                    $domain_name = $matches[0][0];
                                    $checkDomain = false;
                                }
                            }
                            if($i<=$end_line_no){
                                $content .= $line;
                            }
                        }
                        $content = preg_replace('/\s+/', '', $content);
                        $content = preg_replace('/[0-9]+/', '', $content);
                        fclose($fh);
                        //echo$domain_name;
                        
                        //echo $content;
                        $tplContentArray =array();
                        $tplContentArray['bank_id'] = $id;
                        $tplContentArray['content'] = $content;
                        $tplContentArray['bank_url'] = $domain_name;
                        $tplContentArray['end_line_no'] = $end_line_no;
                        $this->tpl_content->addNewRecords($tplContentArray);
                        
                        /*End code*/
                        
                        $message = 'Record inserted successfully';
                        $success = true;
                        $output['redirectURL'] = site_url('templates');
                    }else{
                        $message = 'Something went wrong';
                        $success = false;
                    }
                }
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

    function recursiveFunctionTargetId($jobID) {
        $apiKey = "798c3b57042203d967862da4b7445dce5d1121f5";
        $endpoint1 = "https://api.zamzar.com/v1/jobs/$jobID";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
        $body = curl_exec($ch);
        curl_close($ch);
        //sleep(20);
        $job = json_decode($body, true);
        
        if ($job['target_files'][0]['id']) {
            //print_r($job);
            $target_id = $job['target_files'][0]['id'];
            return $target_id;
        } else {
            return $this->recursiveFunctionTargetId($jobID);
        }
        
    }
    
    function detectTplConvertBankStatement() {
        error_reporting(0);
        $output['page_title'] = 'Convert File';
        $output['message']    = '';
        $extractData = array();
       
        $input = array();
        
        if(isset($_FILES['image_name']['name']) && $_FILES['image_name']['name'])
        { 
            $directory = './assets/uploads/bank_statement';
            @mkdir($directory, 0777);
            @chmod($directory,  0777);
            $config['upload_path'] = $directory;
            $config['allowed_types'] = 'pdf|zip';
            if(pathinfo($_FILES['image_name']['name'], PATHINFO_EXTENSION)=='zip'){
                $config['encrypt_name'] = FALSE;
                if (!file_exists('./assets/uploads/bulk_upload')) {
                    mkdir('./assets/uploads/bulk_upload', 0777, true);
                    chmod('./assets/uploads/bulk_upload',  0777);
                }
                $date = date('d_m_Y_H_i_s');
                $directory = './assets/uploads/bulk_upload/'.$date;
                mkdir($directory, 0777, true);
                chmod($directory,  0777);
                $uploadfile = $_FILES['image_name']['name'];
                if(move_uploaded_file($_FILES['image_name']['tmp_name'], $directory."/".$uploadfile))
                {
                    //cho $uploadfile;die;
                    if (strpos($uploadfile, '_') !== false) {
                        $uniqueId_businessName = explode('_', $uploadfile, 2);
                        $uniqueId = $uniqueId_businessName[0];
                        $businessName = $uniqueId_businessName[1];
                    }else{
                        $output['callBackFunction'] = 'createXLSBankStatement';
                        $output['message'] = "We couldn't find unique Id and business name.";
                        $output['success'] = false;
                        $output['zip'] = true;
                        echo json_encode($output);die;
                        $this->load->view('spreading',$output);
                    }
                    
                    //print_r($array);
                    if (strpos($businessName, '.') !== false) {
                        $expBusinessName =  explode('.', $businessName, 2);
                        $businessName = $expBusinessName[0];
                    }
                    /*echo $uniqueId;
                    echo $businessName;
                    die;*/
                    $historyArray =array();
                    $historyArray['original_pdf_file_name'] = $uploadfile;
                    $historyArray['created_on'] = date("Y-m-d h:i:sa");
                    $historyArray['type'] = 'multiple';
                    $historyArray['unique_id'] = $uniqueId;
                    $historyArray['business_name'] = $businessName;
                    $historyArray['upload_user_id'] = $this->session->userdata('user_id');
                    $last_id = $this->tpl_history->addNewRecords($historyArray);
                    
                    
                    $bulkUpload =array();
                    $bulkUpload['history_id'] = $last_id;
                    $bulkUpload['file_name'] = $uploadfile;
                    $bulkUpload['folder_name'] = $date;
                    $bulkUpload['email'] = $this->session->userdata('user_id');
                    $bulkUpload['status'] = '0';
                    $bulkUpload['created_on'] = date("Y-m-d h:i:sa");
                    $this->bulk_upload->addNewRecords($bulkUpload);
                    
                    
                    $output['callBackFunction'] = 'createXLSBankStatement';
                    $output['message'] = "Zip file has been uploaded successfully.You can check output in dashboard after 5 minutes";
                    $output['success'] = true;
                    $output['zip'] = true;
                    echo json_encode($output);die;
                    $this->load->view('spreading',$output);
                    
                }
                else
                {
                    echo "There was an error uploading the file";
                }
            }else{
                $config['encrypt_name'] = TRUE;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
            }
            
            $uploadfile = $_FILES['image_name']['name'];
            if (strpos($uploadfile, '_') !== false) {
                $uniqueId_businessName = explode('_', $uploadfile, 2);
                $uniqueId = $uniqueId_businessName[0];
                $businessName = $uniqueId_businessName[1];
            }else{
                $output['callBackFunction'] = 'createXLSBankStatement';
                $output['message'] = "We couldn't find unique Id and business name.";
                $output['success'] = false;
                $output['zip'] = true;
                echo json_encode($output);die;
                $this->load->view('spreading',$output);
            }
            
            if ($this->upload->do_upload('image_name'))
            {
                 $image_data = $this->upload->data();
                 //$file_name = $_FILES['image_name']['name'];
                 $file_name = $image_data['file_name'];
                 $endpoint = "https://api.zamzar.com/v1/jobs";
                 $apiKey = "798c3b57042203d967862da4b7445dce5d1121f5";
                 #$sourceFile = 'https://s3.amazonaws.com/zamzar-samples/sample.pdf';
                 #$sourceFile = base_url('./assets/uploads/bank_statement/'. $file_name);
                 if(function_exists('curl_file_create')) {
                     $sourceFile = curl_file_create(FCPATH.'./assets/uploads/bank_statement/'. $file_name);
                 } else {
                     $sourceFile = '@' . realpath(FCPATH.'./assets/uploads/bank_statement/'. $file_name);
                 }
                 $targetFormat = "txt";
                 $postData = array(
                 "source_file" => $sourceFile,
                 "target_format" => $targetFormat
                 );
                 $ch = curl_init();
                 curl_setopt($ch, CURLOPT_URL, $endpoint);
                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                 curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                 curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                 curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
                 $body = curl_exec($ch);
                 curl_close($ch);
                 $response = json_decode($body, true);
                 $target_id = "";
                 if($response){
                     //print_r($response);
                     $jobID = $response['id'];
                     
                     $target_id = $this->recursiveFunctionTargetId($jobID);
                 } 
                 //echo $target_id;
                 if(empty($target_id)){
                     $message = "Something went wrong";
                     $success = false;
                 }
                 else{
                     $this->db->reconnect();
                     $fileID = $target_id;
                     $realPath = FCPATH.'assets/uploads/bank_statement/';
                     $txtFilename = time().'upload.txt';
                     $actualFilePath = $realPath.''.$txtFilename;
                     $endpoint2 = "https://api.zamzar.com/v1/files/$fileID/content";
                     $ch = curl_init();
                     curl_setopt($ch, CURLOPT_URL, $endpoint2);
                     curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                     $fh = fopen($actualFilePath, "w+");
                     curl_setopt($ch, CURLOPT_FILE, $fh);
                     $body = curl_exec($ch);
                     curl_close($ch);
                     
                 
                     $results = $this->tpl_content->getAllTplContentRecords();
                     $bankContentMatches = array();
                     $bankUrlMatches = array();
                     $directory = './assets/uploads/bank_statement';
                     /*For testing(comment code before push on server)*/
                     /*//if($this->session->userdata('user_id')==3){
                         $txtFilename = 'sant_invoice.txt';
                         $realPath = FCPATH.'assets/uploads/bank_statement/';
                         $actualFilePath = $realPath.''.$txtFilename;
                     //}*/
                     /*End Testing*/
                     $content = file_get_contents($actualFilePath);
					 
					 //Santander Multiple Accounts
                     if((preg_match('/PRODUCTO/', $content, $matches)) && (preg_match('/DETALLE DE MOVIMIENTOS CUENTA DE CHEQUES/', $content, $matches))){                        
                         $handle = @fopen ($actualFilePath, "r");
                         $q = 1;
                         $j=0;
                         $txtSplitFileName = array();
                         $header = '';
                         $headerEnd = false;
                         
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($headerEnd==false){
                                 $header .= $line;
                                 if(in_array('PRODUCTO',$lineBreakArray)){
                                     $headerEnd = true;
                                 }
                             }
                             
                             if ((in_array('INFORMACION FISCAL',$lineBreakArray)) || (in_array('DETALLE DE MOVIMIENTOS DINERO CRECIENTE SANTANDER',$lineBreakArray))){
                                 $q++;                                 
                                 $j++;
                                 
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
                                 $buffer .= $header;                          
                                 
                             }
                         }
                         fclose ($handle);
                         error_reporting(1);                        
                         $this->directConvertBankStatement(188,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         
                         die('here');
                     }
                     /*End Split file*/
                     
                     //Banregio Multiple Accounts
                     if((preg_match('/Productos/', $content, $matches)) && (preg_match('/Comisiones Efectivamente Cobradas/', $content, $matches))) {
                         $handle = @fopen ($actualFilePath, "r");
                         $q = 1;
                         $j=0;
                         $a = 0;
                         $txtSplitFileName = array();
                         $header = '';
                         $headerEnd = false;
                         
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             /*foreach (array_values($lineBreakArray) as $i => $value) {
                              echo "$i: $value\n";
                              }*/
                             //die("here");
                             
                             
                             
                             if($headerEnd==false){
                                 //die("hello");
                                 $header .= $line;
                                 if(in_array('Productos',$lineBreakArray)){
                                     $headerEnd = true;
                                 }
                             }
                             //print_r($lineBreakArray);
                             
                             
                             // $pregMatchString1 = '/CUENTA   /';
                             //$pregMatchString = '/Otros Cargos/';
                             // echo($pregMatchString);
                             //echo($pregMatchString1);
                             //die("hello");
                             
                             //$lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if((in_array('CUENTA',$lineBreakArray)) || (in_array('TASA',$lineBreakArray))){
                                 $a++;
                                 if($a!=1){
                                     //die("hello");
                                     $q++;
                                     $pregMatchString= '/Saldo Inicial/'.$q;
                                     $j++;
                                     
                                     $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                     if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                         echo "Cannot open file ($fname)";
                                         exit;
                                     }
                                     if (!@fwrite($fhandle, $buffer)) {
                                         echo "Cannot write to file ($fname)";
                                         exit;
                                     }
                                     array_push($txtSplitFileName,$fname);
                                     $buffer='';
                                     $buffer .= $header;
                                     
                                 }
                             }
                             
                         }
                         
                         $j++;
                         $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                         if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                             echo "Cannot open file ($fname)";
                             exit;
                         }
                         if (!@fwrite($fhandle, $buffer)) {
                             echo "Cannot write to file ($fname)";
                             exit;
                         }
                         array_push($txtSplitFileName,$fname);
                         fclose ($handle);
                         error_reporting(1);
                         
                         //$txtSplitFileName = array("a.txt","b.txt");
                         //print_r($txtSplitFileName);
                         //die;
                         $this->directConvertBankStatement(183,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                     }
                     
                     /*End Split file*/
                     
                     //Banbajio Multiple Accounts
                     if((preg_match('/SALDO TOTAL*/', $content, $matches)) && (preg_match('/TOTAL DE MOVIMIENTOS EN EL PERIODO/', $content, $matches))){
                         $handle = @fopen ($actualFilePath, "r");
                         $q = 1;
                         $j=0;
                         $txtSplitFileName = array();
                         $header = '';
                         $headerEnd = false;
                         
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($headerEnd==false){
                                 $header .= $line;
                                 if(in_array('FECHA DE CORTE',$lineBreakArray)){
                                     $headerEnd = true;
                                 }
                             }
                             
                             $pregMatchString = '/TOTAL DE MOVIMIENTOS EN EL PERIODO/';
                             
                             if (preg_match($pregMatchString, $line, $matches)) {
                                 $q++;
                                 $pregMatchString= '/SALDO ANTERIOR/'.$q;
                                 $j++;
                                 
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
                                 $buffer .= $header;
                                 
                                 
                             }
                             
                         }
                         
                         
                         $this->directConvertBankStatement(182,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                     }
                     /*End Split file*/
                     
                     /* Citibanamex multiple account new format*/
                     if(preg_match('/INVERSION PERFILES./', $content, $matches)){
                          $handle = @fopen ($actualFilePath, "r");
                          $a = 0;
                          $j=0;
                          $txtSplitFileName = array();
                          $header = '';
                          $headerEnd = false;
                          while (!feof ($handle)) {
                              $line = @fgets($handle, 4096);
                              $buffer .= $line;
                              
                              if($headerEnd==false){
                                  $header .= $line;
                                  if(in_array('Fecha de Corte',$lineBreakArray)){
                                      $headerEnd = true;
                                  }
                              }
                              
                              $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                              if('INVERSION PERFILES.'==$lineBreakArray[0] || 'INVERSION PERFILES.'==$lineBreakArray[1]){
                                  $a++;
                                  if($a!=1){
                                      $j++;
                                      /*echo"<pre>";
                                      print_r($lineBreakArray);
                                      echo"</pre>";*/
                                      $fname = date('m_d_Y_h_i_s', time()).'_multiple_'.$j.".txt";
                                      if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                          echo "Cannot open file ($fname)";
                                          exit;
                                      }
                                      if (!@fwrite($fhandle, $buffer)) {
                                          echo "Cannot write to file ($fname)";
                                          exit;
                                      }
                                      array_push($txtSplitFileName,$fname);
                                      $buffer='';
                                      $buffer .= $header;
                                  }
                              }
                          }
                          $j++;
                          $fname = date('m_d_Y_h_i_s', time()).'_multiple_'.$j.".txt";
                          if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                              echo "Cannot open file ($fname)";
                              exit;
                          }
                          if (!@fwrite($fhandle, $buffer)) {
                              echo "Cannot write to file ($fname)";
                              exit;
                          }
                          array_push($txtSplitFileName,$fname);
                            /*echo "<pre>";
                           print_r($txtSplitFileName);
                           echo"</pre>";
                           die;*/
                           $this->directConvertBankStatement(187,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                           die('here');
                      }
                      
                     
                     /* Banrote multiple account*/
                     $singleAcc = false;
                     $banrote = false;
                     $accStart = false;
                     $splitPageNumArray = array();
                     $startInit = 0;
                     if(preg_match('/RESUMEN INTEGRAL/', $content, $matches)){
                         $banrote = true;
                         $handle = @fopen ($actualFilePath, "r");
                         $a = 0;
                         while ($line = fgets($handle)) {
                             $a++;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             //$lineBreakArray = array_filter($lineBreakArray);
                            
                             if($accStart==true && $check<=$a && $lineBreakArray[0]!='TOTAL'){
                                 array_push($splitPageNumArray,$lineBreakArray[0]);
                             }
                             
                             if(in_array('RESUMEN INTEGRAL',$lineBreakArray)){
                                 $accStart = true;
                                 $check = $a;
                                 $check +=3;
                                 $checkSingleAcc +=3;
                             }
                             if($a==$check){
                                 if($lineBreakArray[0]=='TOTAL'){
                                     $singleAcc = true;
                                     break;
                                 }
                             }else{
                                 if($lineBreakArray[0]=='TOTAL'){
                                     $singleAcc = false;
                                     break;
                                 }
                             }
                         }
                     }
                     /*echo"<pre>";
                     print_r($splitPageNumArray);die;*/
                     if($singleAcc==false && $banrote==true){
                         $q = 0;
                         $txtSplitFileName = array();
                         $pageNumber = $splitPageNumArray[1];
                         $pregMatchString = '/Page+\s'.$pageNumber.'+\sof+\s[0-9]+\s/';
                         $handle = @fopen ($actualFilePath, "r");
                         $j=0;
                         $b = 0;
                         $header = '';
                         $headerEnd = false;
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             
                             if (mb_strpos($line, 'Inversi') !== false) {
                                 $lineArray = array_map('trim', array_filter(explode(" ",$line)));
                                 if(count($lineArray)==3 && strtolower(end($lineArray))==strtolower('Personal')){
                                     $line = 'INVERSION ENLACE PERSONAL';
                                  }
                              }
                             
                             $buffer .= $line;
                             
                             //$line = str_replace('�', 'o', $line);
                             //echo $line;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             /*echo "<pre>";
                             print_r($lineBreakArray);
                             echo"</pre>";*/
                             if($headerEnd==false){
                                 $header .= $line;
                                 if(in_array('Resumen de comisiones',$lineBreakArray)){
                                     //echo $header;die('heresss');
                                     $headerEnd = true;
                                 }
                             }
                             
                             $b++;
                             if($a>=$b){
                                 continue;
                             }
                             //echo $splitPageNumArray[$q];echo count($splitPageNumArray);die('here');
                             if (in_array($splitPageNumArray[$q],$lineBreakArray) && count($splitPageNumArray)-1>=$q) {
                                 /*echo"<pre>";
                                 print_r($lineBreakArray);*/
                                 //die('here');
                                 $q++;
                                 $j++;
                                 $fname = date('m_d_Y_h_i_s', time()).'_multiple_'.$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
                                 $buffer .= $header;
                             }
                             
                         }
                         $j++;
                         $fname = date('m_d_Y_h_i_s', time()).'_multiple_'.$j.".txt";
                         if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                             echo "Cannot open file ($fname)";
                             exit;
                         }
                         if (!@fwrite($fhandle, $buffer)) {
                             echo "Cannot write to file ($fname)";
                             exit;
                         }
                         array_push($txtSplitFileName,$fname);
                         /*echo "<pre>";
                         print_r($txtSplitFileName);
                         echo"</pre>";
                         die;*/
                         $this->directConvertBankStatement(179,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                     }
                    
                     
                     /* Split file into file*/
                     $splitPageNumArray = array();
                     $startInit = 0;
                     if(preg_match('/Your deposit accounts/', $content, $matches)){
                         $second_page_start = $matches[1][0];
                         
                         $handle = @fopen ($actualFilePath, "r");
                         while ($line = fgets($handle)) {
                             //echo $line;
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($startInit==1){
                                 $check = true;
                                 $startInit = 0;
                             }
                             
                             if(in_array('Your deposit accounts', $lineBreakArray)){
                                 $startInit = 1;
                             }
                             
                             if(in_array('Total balance', $lineBreakArray)){
                                 $check = false;
                             }
                             if($check==true){
                                 if(count($lineBreakArray)>=4){
                                     array_push($splitPageNumArray,(int) filter_var(end($lineBreakArray), FILTER_SANITIZE_NUMBER_INT)-1);
                                     //echo end($lineBreakArray);
                                 }
                             }   
                         }
                         //print_r($splitPageNumArray);
                         //die;
                         $q = 1;
                         $txtSplitFileName = array();
                         $pageNumber = $splitPageNumArray[1];
                         $pregMatchString = '/Page+\s'.$pageNumber.'+\sof+\s[0-9]+\s/';
                         $handle = @fopen ($actualFilePath, "r");
                         $j=0;
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
                             
                             if (preg_match($pregMatchString, $line, $matches)) {
                                 $q++;
                                 $pregMatchString= '/Page+\s'.$splitPageNumArray[$q].'+\sof+\s[0-9]+\s/';
                                 $j++;
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
                                 
                             }
                             
                         }
                         
                         $j++;
                         $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                         if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                             echo "Cannot open file ($fname)";
                             exit;
                         }
                         if (!@fwrite($fhandle, $buffer)) {
                             echo "Cannot write to file ($fname)";
                             exit;
                         }
                         array_push($txtSplitFileName,$fname);
                         fclose ($handle);
                         error_reporting(1);
                         
                         
                         $this->directConvertBankStatement(1,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                         /*End Split file*/
                         
                        
                     }
                     
                     /*Multiple account BB&T Bank*/
                     $startInit = 0;
                     if(preg_match('/Summary of your accounts/', $content, $matches)){
                         $second_page_start = $matches[1][0];
                         $handle = @fopen ($actualFilePath, "r");
                         while ($line = fgets($handle)) {
                             // echo $line;
                             // die("here");
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($startInit==1){
                                 $check = true;
                                 $startInit = 0;
                             }
                             
                             if(in_array('Summary of your accounts', $lineBreakArray)){
                                 $startInit = 1;
                             }
                             //print_r($lineBreakArray);
                             //die;
                             
                             if(in_array('Total checking and money market savings accounts', $lineBreakArray)){
                                 $check = false;
                             }
                             if($check==true){
                                 if(count($lineBreakArray)>=4){
                                     array_push($splitPageNumArray,(int) filter_var(end($lineBreakArray), FILTER_SANITIZE_NUMBER_INT)-1);
                                     //echo end($lineBreakArray);
                                     //die;
                                 }
                             }
                         }
                         //print_r($splitPageNumArray);
                         //die;
                         $q = 1;
                         $txtSplitFileName = array();
                         //$pageNumber = $splitPageNumArray[1];
                         //$pregMatchString = '/Total deposits, credits and interest/';
                         //echo $pageNumber;
                         //die;
                         
                         $handle = @fopen ($actualFilePath, "r");
                         $j=0;
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             // print_r($line);
                             // die;
							 
                             $buffer .= $line;
                             $pregMatchString = '/Total deposits, credits and interest/';
							 $pregMatchString1 = '/BUSINESS IDA [0-9]/';
							 //$pregMatchString1 = '/Your new balance as of [0-9]*\/[0-9]*\/[0-9]*\s*+\=\s+\$[0-9. ]*\s*+(?<digit>\d+)\s*+BUSINESS/';
							 
                             
                             if ((preg_match($pregMatchString, $line, $matches)) || (preg_match($pregMatchString1, $line, $matches))){
                                 $q++;
                                 $pregMatchString= '/Account summary/'.$q;
                                 $j++;
                                 
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
									if(preg_match($pregMatchString1, $line, $matches)){
									$buffer .= $line;
									}
                                 
                             }
                             
                         }
                         
                         /* $j++;
                          $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                          if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                          echo "Cannot open file ($fname)";
                          exit;
                          }
                          if (!@fwrite($fhandle, $buffer)) {
                          echo "Cannot write to file ($fname)";
                          exit;
                          }
                          array_push($txtSplitFileName,$fname);*/
                         fclose ($handle);
                         error_reporting(1);
                         
                         //$txtSplitFileName = array("a.txt","b.txt");
                         $this->directConvertBankStatement(58,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                         /*End Split file*/
                         
                     }
                     
					  /* Multiple Account WellsFargo Bank*/
					  /* Split file into file*/
					  
                     $splitPageNumArray = array();
                     $startInit = 0;
                     if(preg_match('/Summary of accounts/', $content, $matches)){
                         $second_page_start = $matches[1][0];
                         $handle = @fopen ($actualFilePath, "r");
                         while ($line = fgets($handle)) {
                            // echo $line;
							// die("here");
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($startInit==1){
                                 $check = true;
                                 $startInit = 0;
                             }
                             
                             if(in_array('Summary of accounts', $lineBreakArray)){
                                 $startInit = 1;
                             }
							 //print_r($lineBreakArray);
							 //die;
                             
                             if(in_array('Total deposit accounts', $lineBreakArray)){
                                 $check = false;
                             }
                             if($check==true){
                                 if(count($lineBreakArray)>=4){
                                     array_push($splitPageNumArray, next($lineBreakArray));
									 }
                             }
															 
                         }
						array_shift($splitPageNumArray);
                                  	
                         
                         $q = 1;
                         $txtSplitFileName = array();
                         //$pageNumber = $splitPageNumArray[1];
                          //$pregMatchString = '/Total deposits, credits and interest/';
						 //echo $pageNumber;
						 //die;
						 
                         $handle = @fopen ($actualFilePath, "r");
                         $j=0;
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
							 
							 $breaks = array_map('trim', array_filter(explode("  ",$line)));
							 if (in_array("Account transaction fees summary", $breaks)){
							
							 //$pregMatchString = '/Account\s+transaction\s+fees\s+summary/';
							 
                             //if (preg_match($pregMatchString, $line, $matches)) {
                                 $q++;
                                 $pregMatchString= '/Activity summary/'.$q;
                                 $j++;
								 
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
								 
							
                                 $buffer='';
                               
                             }
							   
                         }
						
                         /*$j++;
                         $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                         if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                             echo "Cannot open file ($fname)";
                             exit;
                         }
                         if (!@fwrite($fhandle, $buffer)) {
                             echo "Cannot write to file ($fname)";
                             exit;
                         }
						 array_push($txtSplitFileName,$fname);*/
						 fclose ($handle);
                         error_reporting(1);
						
                         //echo"<pre>";
						 //print_r($txtSplitFileName);
						 //die('here');
						 $this->directConvertBankStatement(30,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                         /*End Split file*/
                      
                     }
                     
                     /* Multiple Account JP Morgan Bank*/
                     /* Split file into file*/
                     
                     $splitPageNumArray = array();
                     $breakarray = array();
                     $startInit = 0;
                     if(preg_match('/CONSOLIDATED BALANCE SUMMARY/', $content, $matches)){
                         $second_page_start = $matches[1][0];
                         $handle = @fopen ($actualFilePath, "r");
                         while ($line = fgets($handle)) {
                             $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                             if($startInit==1){
                                 $check = true;
                                 $startInit = 0;
                             }
                             
                             if(in_array('CONSOLIDATED BALANCE SUMMARY', $lineBreakArray)){
                                 $startInit = 1;
                             }
                             
                             if(in_array('TOTAL ASSETS', $lineBreakArray)){
                                 $check = false;
                                 $a = true;
                             }
                             if($check==true){
                                 if(count($lineBreakArray)>=4){
                                     array_push($breakarray,(current($lineBreakArray)));
                                     
                                 }
                             }
                             
                         }
                         
                         $q = 1;
                         $txtSplitFileName = array();
                         
                         
                         $handle = @fopen ($actualFilePath, "r");
                         $j=0;
                         while (!feof ($handle)) {
                             $line = @fgets($handle, 4096);
                             $buffer .= $line;
                             
                             
                             $pregMatchString = '/DAILY ENDING BALANCE/';
                             $pregMatchString1 = '/IN CASE OF ERRORS OR QUESTIONS ABOUT YOUR ELECTRONIC FUNDS TRANSFERS/';
                             //$pregMatchString1 = '/Ending Balance\s*+0/';
                             
                             
                             if ((preg_match($pregMatchString, $line, $matches)) || (preg_match($pregMatchString1, $line, $matches))) {
                                 $q++;
                                 $pregMatchString= '/CHECKING SUMMARY/'.$q;
                                 $j++;
                                 
                                 $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                                 if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                                     echo "Cannot open file ($fname)";
                                     exit;
                                 }
                                 if (!@fwrite($fhandle, $buffer)) {
                                     echo "Cannot write to file ($fname)";
                                     exit;
                                 }
                                 array_push($txtSplitFileName,$fname);
                                 $buffer='';
                                 
                                 
                             }
                             
                         }
                         
                         /*$j++;
                          $fname = date('m_d_Y_h_i_s', time()).$j.".txt";
                          if (!$fhandle = @fopen($realPath.$fname, 'w')) {
                          echo "Cannot open file ($fname)";
                          exit;
                          }
                          if (!@fwrite($fhandle, $buffer)) {
                          echo "Cannot write to file ($fname)";
                          exit;
                          }
                          array_push($txtSplitFileName,$fname);*/
                         fclose ($handle);
                         error_reporting(1);
                         
                         //$txtSplitFileName = array("a.txt","b.txt");
                         //print_r($txtSplitFileName);
                         //die;
                         $this->directConvertBankStatement(2,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                         die('here');
                         /*End Split file*/
                         
                     }
                     
                     
                     if(count($results)>0){
                         foreach($results as $key=>$result){
                             $bankMatches[$key]['credit_end_string']=false;
                             $bankMatches[$key]['debit_end_string']=false;
                             $bankMatches[$key]['checks_end_string']=false;
                             $bankMatches[$key]['credit_start_string']=false;
                             $bankMatches[$key]['unique_string']=false;
                             $i = 1;
                             $content = '';
                             $checkDomain = true;
                             $domain_name = '';
                             $fh = fopen($directory.'/'.$txtFilename,'r');
                             while ($line = fgets($fh)) {
                                 $i++;
                                 if($checkDomain){
                                     if(preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $line, $matches)){
                                         $domain_name = $matches[0][0];
                                         if($domain_name==$result->bank_url){
                                             $bankUrlMatches[$result->bank_id] = true;
                                             $checkDomain = false;
                                         }
                                     }
                                 }
                                 //echo $line;
                                 $lineArray = explode('  ', $line);
                                 $filterArray = array_filter($lineArray);
                                 $filterArray = array_map('trim', $filterArray);
                                 //die;in_array($result->credit_end_string, $filterArray)
                                 if($result->credit_start_string!="" && in_array($result->credit_start_string, $filterArray)){
                                     $bankMatches[$key]['credit_start_string']=true;
                                 }
                                 if($result->credit_end_string!="" && in_array($result->credit_end_string, $filterArray)){
                                    $bankMatches[$key]['credit_end_string']=true;
                                  }
                                  if($result->debit_end_string!="" && in_array($result->debit_end_string, $filterArray)){
                                    $bankMatches[$key]['debit_end_string']=true;
                                  }
                                  if($result->checks_end_string!="" && in_array($result->checks_end_string, $filterArray)){
                                    $bankMatches[$key]['checks_end_string']=true;
                                  }
                                  
                                  if($result->unique_string!="" && in_array($result->unique_string, $filterArray)){
                                      $bankMatches[$key]['unique_string']=true;
                                  }
                                 
                                 if($i<=$result->end_line_no){
                                     $content .= $line;
                                 }
                                 
                             }
                             $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Sept", "Oct", "Nov", "Dec"];
                             $content = str_replace($month, "", $content);
                             $content = preg_replace('/\s+/', '', $content);
                             $content = preg_replace('/[0-9]+/', '', $content);
                             fclose($fh);
                             similar_text($result->content, $content, $percent);
                             //$bankMatches[$result->bank_id]=$percent;
                             $bankMatches[$key]['bank_id']=$result->bank_id;
                             $bankMatches[$key]['percent']=round($percent);
                             /*echo "<pre>";
                             print_r($bankMatches);
                             die('here');*/
                             
                             
                         }
                         //print_r($bankMatches);
                         //die('here');
                         $bankCountInt = array();
						 $bankPercentage = array();
                         if(count($bankMatches)>0){
                             foreach($bankMatches as $key=>$bankMatch){
                                 $bankCountInt[$bankMatch['bank_id']] = 0;
                                 if($bankMatch['unique_string']==true){
                                     $bankMatch['percent'] = 80;
                                 }
                                 
                                 if($bankMatch['percent']>45){
                                     if($bankMatch['credit_end_string']==true){
                                         $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                         //$bankCountInt[$bankMatch['bank_id']];
                                         //die;
                                     }
                                     if($bankMatch['debit_end_string']==true){
                                         $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                         //$bankCountInt[$bankMatch['bank_id']];
                                         //die;
                                     }
                                     if($bankMatch['checks_end_string']==true){
                                         $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                     }
                                     
                                     if($bankMatch['credit_start_string']==true){
                                         $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
                                     }
                                     
                                     $bankCountInt[$bankMatch['bank_id']] =$bankCountInt[$bankMatch['bank_id']]+1;
									 $bankPercentage[$bankMatch['bank_id']] = $bankMatch['percent'];
                                }
                                 
                               
                             }
                             /*echo "<pre>";
                             print_r($bankCountInt);
                             die('Here');*/
                            
                             foreach (array_keys($bankCountInt, 0) as $key) {
                                 unset($bankCountInt[$key]);
                             }
                            
                             if(!empty($bankCountInt) && max($bankCountInt)>0){
                                 $maxs = array_keys($bankCountInt, max($bankCountInt));
								 //$maxsPercentage = array_keys($bankPercentage, max($bankPercentage));
                                
                                 //$intersection = array_intersect($maxsPercentage, $maxs);
                                 $bank_id = $maxs[0];
                                 $output['accuracy'] = max($bankCountInt);
                                 $res = $this->banks->getBankName($bank_id);//array_search (max($bankCountInt), $bankCountInt));
                                 $output['bank_name'] = $res->bank_name;
                                 
                                 $string_record = $this->bank_statement->getSingleRecordByBankId($bank_id);
                                 
                                 $extractData = $this->common_model->getExtractDataRegx($string_record,$txtFilename,$bank_id);
                                 //$extractData = $this->getExtractDataRegx($string_record,$txtFilename,$bank_id);
                                 
                                 $output['callBackFunction'] = 'createXLSBankStatement';
                                 $extractData['original_pdf_file_name'] = $_FILES['image_name']['name'];
                                 $extractData['upload_pdf_file'] = $file_name;
                                 $output['extractData'] = $extractData;
                                 $output['bank_data_val'] = $string_record;
                                 //$output['string_record'] = $string_record;
                                 $output['textFileName'] = $txtFilename;
                                 $output['accType'] = 'single';
                                 $output['multiple_account'] = false;
                                 
                                 $message = '';
                                 $success = true;
                             }else{
                                 //echo $file_name;
                                 //die('here');
                                 $extractData['original_pdf_file_name'] = $_FILES['image_name']['name'];
                                 $extractData['upload_pdf_file'] = $file_name;
                                 $output['extractData'] = $extractData;
                                 $output['textFileName'] = $txtFilename;
                                 $output['callBackFunction'] = 'createXLSBankStatement';
                                 $output['bank_name'] = '';
                                 $output['accuracy'] = 0;
                             }
                             
                         }else{
                             $extractData['original_pdf_file_name'] = $_FILES['image_name']['name'];
                             $extractData['upload_pdf_file'] = $file_name;
                             $output['extractData'] = $extractData;
                             $output['textFileName'] = $txtFilename;
                             $output['callBackFunction'] = 'createXLSBankStatement';
                             $output['bank_name'] = '';
                             $output['accuracy'] = 0;
                         }
                         
                     }else{
                         $extractData['original_pdf_file_name'] = $_FILES['image_name']['name'];
                         $extractData['upload_pdf_file'] = $file_name;
                         $output['extractData'] = $extractData;
                         $output['textFileName'] = $txtFilename;
                         $output['callBackFunction'] = 'createXLSBankStatement';
                         $output['bank_name'] = '';
                         $output['accuracy'] = 0;
                     }
                 
               }
            }
            else
            {
                $message = $this->upload->display_errors();
                $success = false;
            }
        }
        else
        {
            $message = "Please select a file";
            $success = false;
        }
        /*}
         else {
         $success = false;
         $message = validation_errors();
         }*/
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output);die;
        //}
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        $this->load->view('spreading',$output);
    }
    
    function callFromView(){
        $bank_id = $this->input->post('bank_id');
        $txtFilename = $this->input->post('txtFilename');
        $txtSplitFileName = $this->input->post('txtSplitFileName');
        $uploadedXlsFileName = $this->input->post('uploadedXlsFileName');
        $this->page_array = $this->input->post('page_array');
        $this->newFolderName = $this->input->post('newFolderName');
        $this->checkAllPdfProcess = $this->input->post('checkAllPdfProcess');
        $this->history_id = $this->input->post('history_id');
        if(end($txtSplitFileName)==$txtFilename){
            $this->isCompleteMultiAcc = true;
        }
        //echo"<pre>";
        //print_r($_POST);
        //print_r($this->page_array);
        //die;
        $this->directConvertBankStatement($bank_id,$txtFilename,$txtSplitFileName,$uploadedXlsFileName);
        die;
    }
    
    function directConvertBankStatement($bank_id,$txtFilename,$txtSplitFileName,$uploadedXlsFileName="",$original_pdf_file_name="",$splitPageNumArray="") {
        $output['page_title'] = $txtFilename;
        $output['message']    = '';
       
        $input = array();
        $string_record = $this->bank_statement->getSingleRecordByBankId($bank_id);
        $extractData = $this->common_model->getExtractDataRegx($string_record,$txtFilename,$bank_id);
        //$extractData = $this->getExtractDataRegx($string_record,$txtFilename,$bank_id);
        
        $output['callBackFunction'] = 'createXLSBankStatement';
        $output['txtSplitFileName'] = $txtSplitFileName;
        $output['multiple_account'] = true;
        $output['multiple_process'] = true;
        $extractData['upload_pdf_file'] = '';
        $extractData['original_pdf_file_name'] = $original_pdf_file_name;
        $extractData['split_page_num_array'] = $splitPageNumArray;
        $extractData['page_array'] = $this->page_array;
        $extractData['isCompleteMultiAcc'] = $this->isCompleteMultiAcc;
        $output['newFolderName'] = $this->newFolderName;
        $output['extractData'] = $extractData;
        $output['bank_data_val'] = $string_record;
        //$output['string_record'] = $string_record;
        $output['textFileName'] = $txtFilename;
        $success = 'success';
        $res = $this->banks->getBankName($bank_id);
        $output['bank_name'] = $res->bank_name;
        $output['bank_id'] = $bank_id;
        $output['history_id'] = $this->history_id;
        if($this->session->userdata('type')==2){
            $output['accType'] = 'single';
        }else{
            if($uploadedXlsFileName!=""){
                $output['uploadedXlsFileName'] = $uploadedXlsFileName;
            }else{
                $output['uploadedXlsFileName'] = '';
            }
        }
        
        //$output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output);die;
        $output['check_all_pdf_process'] = $this->checkAllPdfProcess;
        
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        $this->load->view('spreading',$output);
    }
    
    function convertBankStatement() {
        $output['page_title'] = 'Convert File';       
        $output['message']    = '';
        //print_r($_POST);
        //die('Here');
        if(isset($_POST) && !empty($_POST)){ 
            $this->form_validation->set_rules('bank_id', 'Bank name', 'trim|required');
            if ($this->form_validation->run()) {
                $input = array();   
                $bank_id = $this->input->post('bank_id');
                
                $string_record = $this->bank_statement->getSingleRecordByBankId($bank_id);
                
                $realPath = FCPATH.'assets/uploads/bank_statement/';
                $txtFilename = $this->input->post('convert_text_file');
                $actualFilePath = $realPath.''.$txtFilename;
                $contents = file_get_contents($actualFilePath);
                #$results = $this->bank_address->getRecordsByBankId($bank_id);
                
                $extractData = $this->common_model->getExtractDataRegx($string_record,$txtFilename,$bank_id);
                //$extractData = $this->getExtractDataRegx($string_record,$txtFilename,$bank_id);
                
                $output['callBackFunction'] = 'createXLSBankStatement';
                $extractData['upload_pdf_file'] = $this->input->post('upload_pdf_file');
                $extractData['original_pdf_file_name'] = $this->input->post('original_pdf_file_name');
                $output['extractData'] = $extractData;
                $output['bank_data_val'] = $string_record;
                //$output['string_record'] = $string_record;
                $output['textFileName'] = $txtFilename;
                $success = 'success';
                $res = $this->banks->getBankName($bank_id);
                $output['bank_name'] = $res->bank_name;;
                
            }
            else {
                $success = false;
                $message = validation_errors();
            }
            //$output['message'] = $message;
            $output['success'] = $success;
            echo json_encode($output);die;
        }     
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        $this->load->view('spreading',$output);
    }

    
    function createXLSBankStatement() {
                
        /*echo"<pre>";
        print_r($_POST);
        echo"</pre>";
        die('here');*/
        //error_reporting(0);
        $account_type = $this->input->post('account_type');
        $bank_date_format = $this->input->post('bank_date_format');
        $currency = $this->input->post('currency');
        $type = $this->input->post('type');
        $bank_id = $this->input->post('bank_id');
        if($type==1){
            $credits = $this->input->post('credits');
            $debits = $this->input->post('debits');
            $checks = $this->input->post('checks');
            //$service_fees = $this->input->post('service_fees');
        }else{
            $transactions = $this->input->post('transactions');
        }
		
		$credit_min_date = min(array_column($credits,'date'));
        $debit_min_date = min(array_column($debits,'date'));
        $check_min_date = min(array_column($checks,'date'));
		
		if (!$credit_min_date) {
           $credit_min_date = "99-99"; 
        }

        if (!$debit_min_date) {
            $debit_min_date = "99-99"; 
        }

        if (!$check_min_date) {
            $check_min_date = "99-99"; 
        }
        
        /*echo"<pre>";
        print_r($credits);
        print_r($debits);
        print_r($checks);
        echo"</pre>";
        die('here');*/
        
        $account_number = trim($this->input->post('account_number'));
        $name = trim($this->input->post('name'));
        $se10 = trim($this->input->post('se10'));
        $contract_nbr = trim($this->input->post('contract_nbr'));
        $amort_date = trim($this->input->post('amort_date'));
        $instant_decision_date = trim($this->input->post('instant_decision_date'));
        $account_holder_name = trim($this->input->post('account_holder_name'));
        $account_type = trim($this->input->post('account_type'));
        $account_ownership = trim($this->input->post('account_ownership'));
        $name_of_bank = trim($this->input->post('name_of_bank'));
        $bank_address = trim($this->input->post('bank_address'));
        $bank_city = trim($this->input->post('bank_city'));
        $bank_state = trim($this->input->post('bank_state'));
        $bank_zip = trim($this->input->post('bank_zip'));
        //$current_balance = trim($this->input->post('current_balance'));
        $start_date = trim($this->input->post('start_date'));
       
        $end_date = trim($this->input->post('end_date'));
        $mdy = explode('/', $end_date);
        $year = $mdy[2];
		
		if ($start_date == "") {
		    $start_date = str_replace("-","/",$this->getCompleteDate(strval(min($credit_min_date,$debit_min_date,$check_min_date)),$year,$bank_date_format));
        }
        
        //$open_balance = trim($this->input->post('open_balance'));
        $closing_balance_pdf = trim($this->input->post('closing_balance'));
        //$total_deposits = trim($this->input->post('total_deposits'));
        $count_deposits = trim($this->input->post('count_deposits'));
        //$total_withdrawals = trim($this->input->post('total_withdrawals'));
        $count_withdrawals = trim($this->input->post('count_withdrawals'));
        $total_count_check_return = trim($this->input->post('total_count_check_return'));
        $total_count_inward_check_return = trim($this->input->post('total_count_inward_check_return'));
        $total_inward_check_return = trim($this->input->post('total_inward_check_return'));
        $total_count_outward_check_return = trim($this->input->post('total_count_outward_check_return'));
        $total_outward_check_return = trim($this->input->post('total_outward_check_return'));
        $count_ecs_or_emi = trim($this->input->post('count_ecs_or_emi'));
        $amount_ecs_or_emi = trim($this->input->post('amount_ecs_or_emi'));
        $route = trim($this->input->post('route'));
        $transaction_all_level_spreading_done = trim($this->input->post('transaction_all_level_spreading_done'));
        //$native_vs_non_native = trim($this->input->post('native_vs_non_native'));
        $check_sum = '';
        $summary_and_transaction_match = trim($this->input->post('summary_and_transaction_match'));
        $pages = trim($this->input->post('pages'));
        $begining_balance = $this->input->post('begining_balance');
        $service_fee_1 = $this->input->post('service_fee_1');
        $service_fee_2 = $this->input->post('service_fee_2');
        //$explode_file_name = explode(".",$this->input->post('upload_pdf_file'));
        if($this->input->post('original_pdf_file_name')!=""){
            $data = substr($this->input->post('original_pdf_file_name'), 0 , (strrpos($this->input->post('original_pdf_file_name'), ".")));
        }else{
            $data = "data";
        }
        $last_txn_date = '';
        $fileName = $data.'_'.time().'.xlsx';  
        if($fileName && $this->input->post('original_pdf_file_name')!="" && !$this->input->post('zipFileName') && !$this->input->post('newFolderName') && !$this->input->post('history_id')){
            /**Insert History*/
            $uploadfile = $this->input->post('original_pdf_file_name');
            if (strpos($uploadfile, '_') !== false) {
                $uniqueId_businessName = explode('_', $uploadfile, 2);
                $uniqueId = $uniqueId_businessName[0];
                $businessName = $uniqueId_businessName[1];
            }
            
            //print_r($array);
            if (strpos($businessName, '.') !== false) {
                $expBusinessName =  explode('.', $businessName, 2);
                $businessName = $expBusinessName[0];
            }
            $histArray =array(); 
            $histArray['bank_id'] = $bank_id;
            $histArray['file_name'] = $this->input->post('upload_pdf_file');
            $histArray['original_pdf_file_name'] = $this->input->post('original_pdf_file_name');
            $histArray['created_on'] = date("Y-m-d h:i:sa");
            $histArray['downloaded_file_name'] = $fileName;
            $histArray['unique_id'] = $uniqueId;
            $histArray['business_name'] = $businessName;
            $histArray['upload_user_id'] = $this->session->userdata('user_id');
            $last_history_id = $this->tpl_history->addNewRecords($histArray);
            
            /**End History*/
            // user count from tbl_banks
            $this->db->where('id',$bank_id);
            $this->db->set('uses_count','uses_count+1',FALSE);
            $this->db->update('tbl_banks');
        }else if(($this->input->post('zipFileName') && $this->input->post('newFolderName') || $this->input->post('multiple_account')) && $this->input->post('history_id')){
            $zipFileName = $this->input->post('zipFileName');
            $newFolderName = $this->input->post('newFolderName');
            $history_id = $this->input->post('history_id');
            $zipRecord = $this->tpl_history->checkZipFileRecord($zipFileName,$newFolderName);
            $last_history_id = $history_id;
            if(count($zipRecord)==0){
                $histArray =array();
                $histArray['zip_folder_name'] = $zipFileName;
                $histArray['file_name'] = $newFolderName;
                $histArray['type'] = 'multiple';
                $histArray['created_on'] = date("Y-m-d h:i:sa");
                $this->tpl_history->updateHistoryRecord($history_id,$histArray);
            }
        }
        
        $cntHistoryId = $this->summary_level_data->getCountHistoryId($last_history_id);
        $file_no = $cntHistoryId+1;
        //add condition 2==1 for condition false
        if($this->input->post('uploadedXlsFileName') && 2==1){
            $fileName = $this->input->post('uploadedXlsFileName');
            $objPHPExcel  = PHPExcel_IOFactory::load(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
            $objPHPExcel->setActiveSheetIndex(1);
            $row = $objPHPExcel->getActiveSheet()->getHighestRow();
            $i = $row;
           
            #$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, number_format(str_replace(array("$",","), '', $begining_balance),2));
            
        }else{
            $i = 1;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex(1);
            
            $link_style_array = [
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size'  => 12
                )
            ];
            $objPHPExcel->getActiveSheet()->getStyle("A1:K1")->applyFromArray($link_style_array);
            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('1F497D');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            /*Test*/
            /*$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
             $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);*/
            
            /*Test*/
            
            $objPHPExcel->getActiveSheet()->setTitle('Customer-Transaction Data');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Unique ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Account#');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Txn id');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Description');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Check#');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Txn date');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Txn amount');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Currency');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Debit/credit');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Available balance');
            
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', number_format(str_replace(array("$",","), '', $begining_balance),2));
            $end_of_balance = number_format(str_replace(array("$",","), '', $begining_balance),2);
        }
        
        
        // Config
          
        
        
        $total_deposits = 0;
        $total_withdrawals = 0;
        $count_deposits = 0;
        $count_withdrawals = 0;
        $date = '';
        if($type==1){
            if($credits){
                /*if($this->input->post('uploadedXlsFileName')){
                    echo"<pre>";
                    print_r($credits);
                    echo"</pre>";
                    die('here');
                }*/
                foreach ($credits as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                      
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $date);
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2)) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Credit');
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $value['amount']),2);
                            $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $value['amount']),2);
                            $count_deposits ++;
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array("$",","), '', $end_of_balance));
                        
                        
                        $update_customer_txn_data = array();
                        $update_customer_txn_data['history_id'] = $last_history_id;
                        $update_customer_txn_data['file_no'] = $file_no;
                        if(isset($value['description'])){
                            $description =  preg_replace("/[\n\r]/", " ",trim($value['description']));
                        }else{
                            $description = '';
                        }
                        
                        $update_customer_txn_data['description'] = $description;
                        $update_customer_txn_data['txn_date'] = $date;
                        if(isset($value['amount'])){
                            $amount = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2));
                        }else{
                            $amount = '';
                        }
                        $update_customer_txn_data['txn_amt'] = $amount;
                        $update_customer_txn_data['type'] = 'cr';
                        $update_customer_txn_data['currency'] = $currency;
                        $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                        
                    }
                } 
            }
            if($debits){
                foreach ($debits as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $date);
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array("$",","), '', $end_of_balance));
                        
                        
                        $update_customer_txn_data = array();
                        $update_customer_txn_data['history_id'] = $last_history_id;
                        $update_customer_txn_data['file_no'] = $file_no;
                        if(isset($value['description'])){
                            $description =  preg_replace("/[\n\r]/", " ",trim($value['description']));
                        }else{
                            $description = '';
                        }
                        
                        $update_customer_txn_data['description'] = $description;
                        $update_customer_txn_data['txn_date'] = $date;
                        if(isset($value['amount'])){
                            $amount = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2));
                        }else{
                            $amount = '';
                        }
                        $update_customer_txn_data['txn_amt'] = $amount;
                        $update_customer_txn_data['type'] = 'dr';
                        $update_customer_txn_data['currency'] = $currency;
                        $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                        
                    }
                } 
            }
            
            if($checks){
                foreach ($checks as $key => $value) {
                    if(isset($value['amount'])){
                        
                        
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                       
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                        /*if($value['description']==" "){
                            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, isset($value['description']) ? 'Check ' : '');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, isset($value['description']) ? $value['description'] : '');
                        }*/
                        if($bank_id==116){
                            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '');
                        }else if ($bank_id == 61) {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : 'Check');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, 'Check');
                        }
                        if($value['cheque_no']=='Check_number_not_found'){
                            $value['cheque_no'] = '';
                        }
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, isset($value['cheque_no']) ? preg_replace("/[^0-9]/", "",trim($value['cheque_no'])) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $date);
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                        if(isset($value['amount']) && $value['amount']!=""){
                            //echo$value['amount'];
                            //echo $i;
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance)); 
                        
                        
                        $update_customer_txn_data = array();
                        $update_customer_txn_data['history_id'] = $last_history_id;
                        $update_customer_txn_data['file_no'] = $file_no;
                        if($bank_id==116){
                            $description = isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '';
                        }else if ($bank_id == 61) {
                            $description = isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : 'Check';
                        }else{
                            $description = 'Check';
                        }
                        
                        $update_customer_txn_data['description'] = $description;
                        $update_customer_txn_data['check_no'] = isset($value['cheque_no']) ? preg_replace("/[^0-9]/", "",trim($value['cheque_no'])) : '';
                        $update_customer_txn_data['txn_date'] = $date;
                        if(isset($value['amount'])){
                            $amount = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2));
                        }else{
                            $amount = '';
                        }
                        $update_customer_txn_data['txn_amt'] = $amount;
                        $update_customer_txn_data['type'] = 'dr';
                        $update_customer_txn_data['currency'] = $currency;
                        $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                        
                    }
                }
            }
            //die('gere');
            if($service_fees){
                foreach ($service_fees as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                        }else{
                            $date = '';
                        }
                      
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, isset($value['description']) ? $value['description'] : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $date);
                        
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance));
                        /**Inser Data*/
                      
                        $update_customer_txn_data = array();
                        $update_customer_txn_data['history_id'] = $last_history_id;
                        $update_customer_txn_data['file_no'] = $file_no;
                        $update_customer_txn_data['description'] = isset($value['description']) ? $value['description'] : '';
                        $update_customer_txn_data['txn_date'] = $date;
                        if(isset($value['amount'])){
                            $amount = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2));
                        }else{
                            $amount = '';
                        }
                        $update_customer_txn_data['txn_amt'] = $amount;
                        $update_customer_txn_data['type'] = 'dr';
                        $update_customer_txn_data['currency'] = $currency;
                        $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                        
                    }
                }
            }
            if(isset($service_fee_1) && $service_fee_1!=""){
                $service_fee_amount_1 = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2));
                if (is_numeric($service_fee_amount_1) && $service_fee_amount_1!=0){
                    $i++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                    
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $this->input->post('service_fee_title_1'));
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $last_txn_date);
                    
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($service_fee_1) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2)) : '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                    
                    if($this->input->post('service_fee_type_1')=='dr'){
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $service_fee_1))),2);
                        $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $service_fee_1)),2);
                        $closing_balance = $end_of_balance;
                        $count_withdrawals++;
                    }else{
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $service_fee_1),2);
                        $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $service_fee_1),2);
                        $count_deposits ++;
                    }
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance));
                    
                    /**Inser Data*/
                    
                    $update_customer_txn_data = array();
                    $update_customer_txn_data['history_id'] = $last_history_id;
                    $update_customer_txn_data['file_no'] = $file_no;
                    $update_customer_txn_data['description'] = $this->input->post('service_fee_title_1');
                    $update_customer_txn_data['txn_date'] = $last_txn_date;
                    
                    $update_customer_txn_data['txn_amt'] = isset($service_fee_1) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2)) : '';
                    $update_customer_txn_data['type'] = 'dr';
                    $update_customer_txn_data['currency'] = $currency;
                    $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                    
                }
                
            }
                        
            if(isset($service_fee_2) && $service_fee_2!=""){
                $service_fee_amount_2 = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2));
                if (is_numeric($service_fee_amount_2) && $service_fee_amount_2!=0){
                    $i++;
                   
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                    
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $this->input->post('service_fee_title_2'));
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $last_txn_date);
                    
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($service_fee_2) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2)) : '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                    
                    if($this->input->post('service_fee_type_2')=='dr'){
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $service_fee_2))),2);
                        $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $service_fee_2)),2);
                        $closing_balance = $end_of_balance;
                        $count_withdrawals++;
                    }else{
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $service_fee_1),2);
                        $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $service_fee_1),2);
                        $count_deposits ++;
                    }
                    //die('here');
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance));
                    
                    /**Inser Data*/
                    
                    $update_customer_txn_data = array();
                    $update_customer_txn_data['history_id'] = $last_history_id;
                    $update_customer_txn_data['file_no'] = $file_no;
                    $update_customer_txn_data['description'] = $this->input->post('service_fee_title_2');
                    $update_customer_txn_data['txn_date'] = $last_txn_date;
                    
                    $update_customer_txn_data['txn_amt'] = isset($service_fee_2) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2)) : '';
                    $update_customer_txn_data['type'] = 'dr';
                    $update_customer_txn_data['currency'] = $currency;
                    $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                   
                }
                
            }
            
            $check_sum = number_format(str_replace(array("$",","), '', $begining_balance) + str_replace(array("$",","), '', $total_deposits) - str_replace(array("$",","), '', $total_withdrawals) - str_replace(array("$",","), '', $closing_balance_pdf),2);
        }else{
            /*For Transactions*/
            $bank_statement_data = $this->bank_statement->getSingleRecordByBankId($bank_id); 
            
            if($transactions){
                foreach ($transactions as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            //$date = $this->getCompleteDate($value['date'],$year);
							if ($bank_id == 178 || $bank_id == 181){
                                $date = $this->newgetCompleteDate($value['date'],$year);
                            }else{
                                
                                if ($bank_id == 179){
                                    $expDate = explode("-",$value['date']);
                                    if(count($expDate)==3){
                                        $value['date'] = trim($expDate[0]).'-'.trim($expDate[1]).'-20'.trim($expDate[2]);
                                    }
                                }
                                //echo $value['date'];
                                $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                                //echo $date;
                                //die('here');
                            }
                        }else{
                            $date = '';
                        }
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $uniqueId);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');

                        
                        $isCheck = false;
                        if(isset($bank_statement_data->fetch_check_from_desc) && $bank_statement_data->fetch_check_from_desc!=""){
                            $array = explode("|",$bank_statement_data->fetch_check_from_desc);
                            foreach($array as $arr){
                                //if (preg_match('/\b'.$arr.'\b/', $value['description'])) # Change for multiva bank
                                if (preg_match('/'.$arr.'/', $value['description'])) {
                                    $isCheck = true;
                                    $fetch_check_from_desc = $arr;
                                }
                            }
                        }
                        
                        if(isset($value['cheque_no']) && $value['cheque_no']!=""){
                            $description = trim(str_replace($value['cheque_no'],"",$value['description']));
                        }else if($isCheck){
                            $split_check = trim(str_replace($fetch_check_from_desc,"",$value['description']));
							$split_check = preg_replace("/\d+[-,\/]\d+[-,\/]\d+/", ' ', $split_check);
                            $result = explode(" ", $split_check, 2);//Get first numeric value
                            if (is_numeric($result[0])){
                                $value['cheque_no'] = $result[0];
                            }
                            //If not numeric $result[0] then get first numeric value
                            if (!is_numeric($result[0])){
                                $filteredNumbers = array_filter(preg_split("/\D+/", $split_check));
                                $firstOccurence = reset($filteredNumbers);
                                if($firstOccurence){
                                    $value['cheque_no'] = $firstOccurence;
                                }
                            }
                            
                            $description = trim(str_replace($value['cheque_no'],"",$value['description']));;
                        }else{
                            $description = $value['description'];
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, preg_replace("/[\n\r]/", " ",trim($description)));
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, isset($value['cheque_no']) ? $value['cheque_no'] : '');
                        //$date = PHPExcel_Shared_Date::PHPToExcel(
                           // DateTime::createFromFormat('m/d/Y', $date)
                            //);
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $date);
                        //$objPHPExcel->getActiveSheet()->getStyle('F' . $i)->getNumberFormat()->setFormatCode('mm/dd/yyyy');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($value['amount']) ? str_replace(array("$",","), '', number_format(abs(str_replace(array("$",","), '', $value['amount'])),2)) : '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $currency);
                        
                        if(isset($value['type']) && $value['type']=="cr"){
                            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Credit');
                            $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $value['amount']),2);
                            $count_deposits ++;
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $value['amount']),2);
                            $closing_balance = $end_of_balance;
                        }else if(isset($value['type']) && $value['type']=="dr"){
                            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, 'Debit');
                            //$end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            
                            $count_withdrawals++;
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $closing_balance = $end_of_balance;
                        }
                       
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array("$",","), '', $end_of_balance));
                        
                        /**Inser Data*/
                        
                        $update_customer_txn_data = array();
                        $update_customer_txn_data['history_id'] = $last_history_id;
                        $update_customer_txn_data['file_no'] = $file_no;
                        $update_customer_txn_data['description'] = preg_replace("/[\n\r]/", " ",trim($description));
                        $update_customer_txn_data['txn_date'] = $date;
                        $update_customer_txn_data['check_no'] = isset($value['cheque_no']) ? $value['cheque_no'] : '';
                        $update_customer_txn_data['txn_amt'] = isset($value['amount']) ? str_replace(array("$",","), '', number_format(abs(str_replace(array("$",","), '', $value['amount'])),2)) : '';
                        if(isset($value['type']) && $value['type']=="cr"){
                            $type = 'cr';
                        }else if(isset($value['type']) && $value['type']=="dr"){
                            $type = 'dr';
                        }
                        $update_customer_txn_data['type'] = $type;
                        $update_customer_txn_data['currency'] = $currency;
                        $this->customer_txn_data->addCustomerTxnData($update_customer_txn_data);
                        
                    }
                }
            }
            /*End Transactions*/
            /*echo $begining_balance.'begining_balance'.'</br>';
            echo $total_deposits.'total_deposits'.'</br>';
            echo $total_withdrawals.'total_withdrawals'.'</br>';
            echo $closing_balance.'closing_balance'.'</br>';
            die('here');*/
            $check_sum = number_format(str_replace(array("$",","), '', $begining_balance) + str_replace(array("$",","), '', $total_deposits) - str_replace(array("$",","), '', $total_withdrawals) - str_replace(array("$",","), '', $closing_balance_pdf),2);
        }
        
        if($this->input->post('uploadedXlsFileName')){
            $objPHPExcel->setActiveSheetIndex(0);
            $tab1HighestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
            $rowCount = $tab1HighestRow+1;
            $fileName = $this->input->post('uploadedXlsFileName');
        }else{
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            
            $objPHPExcel->getActiveSheet()->setTitle('Summary Level Data');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Unique ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'account#');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'account_holder_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'account_type');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Name_of_bank');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'bank_address');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'bank_city');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'bank_state');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'bank_zip');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'current_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'start_date');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'end_date');
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'open_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'closing_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'total_deposits');
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'count_deposits');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'total_withdrawals');
            $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'count_withdrawals');
            $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'native_vs_non_native');
            $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'check_sum');
            $rowCount = 2;
            
        }
        
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $uniqueId);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $account_number, PHPExcel_Cell_DataType::TYPE_STRING );
        if($this->input->post('uploadedXlsFileName')){
            $cellValues = $objPHPExcel->getActiveSheet()->rangeToArray('C'.$tab1HighestRow);
            $objPHPExcel->getActiveSheet()->fromArray($cellValues, null, 'C'.$rowCount);
        }else{
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $account_holder_name  );
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $account_type);
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $name_of_bank);
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $bank_address);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $bank_city);
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $bank_state);
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $bank_zip);
        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format(str_replace(array("$",","), '', $closing_balance_pdf),2));
        if($this->input->post('uploadedXlsFileName')){
            $cellValues = $objPHPExcel->getActiveSheet()->rangeToArray('K'.$tab1HighestRow);
            $objPHPExcel->getActiveSheet()->fromArray($cellValues, null, 'K'.$rowCount);
        }else{
            $objPHPExcel->getActiveSheet()->SetCellValue('k' . $rowCount, $start_date);
        }
        
        if($this->input->post('uploadedXlsFileName')){
            $cellValues = $objPHPExcel->getActiveSheet()->rangeToArray('L'.$tab1HighestRow);
            $objPHPExcel->getActiveSheet()->fromArray($cellValues, null, 'L'.$rowCount);
        }else{
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $end_date);
        }
        
        
        $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format(str_replace(array("$",","), '', $begining_balance),2));
        $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, number_format(str_replace(array("$",","), '', $closing_balance_pdf),2));
        $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $total_deposits);
        $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $count_deposits);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $total_withdrawals);
        $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $count_withdrawals);
        $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, 'Native');
        $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $check_sum);
        
        
        
        if($this->input->post('uploadedXlsFileName')){
            $pageArray = $this->input->post('page_array');
            //$cellValues = $objPHPExcel->getActiveSheet()->rangeToArray('AI'.$tab1HighestRow);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $pageArray[$tab1HighestRow-1]);
        }else{
            
            if($this->input->post('split_page_num_array')!=""){
                $split_page_num_array =  $this->input->post('split_page_num_array');
                $pageArray = array();
                $lastPageNum = 0;
                foreach($split_page_num_array as $key=>$pageNum){
                    if($key!=0){
                        array_push($pageArray,$pageNum-$lastPageNum);
                    }
                    $lastPageNum = $pageNum;
                }
                array_push($pageArray,$pages-$pageNum);
                $objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $pageArray[0]);
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $pages);
            }
            
        }
        
        //$plain_text = 'This is a plain-text message!';
        //echo$ciphertext = $this->encryption->encrypt($plain_text);
        
        // Outputs: This is a plain-text message!
        //echo $this->encryption->decrypt($ciphertext);
        
        $update_summary_level_data = array();
        $update_summary_level_data['history_id'] = $last_history_id;
        $update_summary_level_data['bank_id'] = $bank_id;
        $update_summary_level_data['file_no'] = $file_no;
        $update_summary_level_data['account_number'] = base64_encode(openssl_encrypt(trim($account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));//$this->encryption->encrypt($account_number);
        $update_summary_level_data['account_holder_name'] = base64_encode(openssl_encrypt(trim($account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));//$this->encryption->encrypt($account_holder_name);
        $update_summary_level_data['account_type'] = $account_type;
        $update_summary_level_data['name_of_bank'] = $name_of_bank;
        $update_summary_level_data['bank_address'] = $bank_address;
        $update_summary_level_data['bank_city'] = $bank_city;
        $update_summary_level_data['bank_state'] = $bank_state;
        $update_summary_level_data['bank_zip'] = $bank_zip;
        $update_summary_level_data['current_balance'] = str_replace(array("$",","), '', $closing_balance_pdf);
        $update_summary_level_data['start_date'] = $start_date;
        $update_summary_level_data['end_date'] = $end_date;
        $update_summary_level_data['open_balance'] = str_replace(array("$",","), '', $begining_balance);
        $update_summary_level_data['closing_balance'] = str_replace(array("$",","), '', $closing_balance_pdf);
        $update_summary_level_data['total_deposits'] = str_replace(array("$",","), '', $total_deposits);
        $update_summary_level_data['count_deposits'] = $count_deposits;
        $update_summary_level_data['total_withdrawals'] = str_replace(array("$",","), '', $total_withdrawals);
        $update_summary_level_data['count_withdrawals'] = $count_withdrawals;
        $update_summary_level_data['transaction_all_level_spreading_done'] = $check_sum==0 ? 'Yes' : 'No';
        $update_summary_level_data['native_vs_non_native'] = 'Native';
        $update_summary_level_data['check_sum'] = str_replace(array("$",","), '', $check_sum);
        $update_summary_level_data['summary_and_transaction_match'] = $check_sum==0 ? 'Yes' : 'No';
        $update_summary_level_data['pages'] = $pages;
        $update_summary_level_data['currency'] = $currency;
        $this->summary_level_data->addSummaryLevelData($update_summary_level_data);
        
        $update_case_error_log = array();
        $update_case_error_log['history_id'] = $last_history_id;
        $update_case_error_log['file_no'] = $file_no;
        if($account_number!=""){ $update_case_error_log['account_number'] = 1; }else{ $update_case_error_log['account_number'] = 0;}
        if($account_holder_name!=""){ $update_case_error_log['aaccount_holder_name'] = 1; }else{ $update_case_error_log['aaccount_holder_name'] = 0;}
        if($account_type!=""){ $update_case_error_log['account_type'] = 1; }else{ $update_case_error_log['account_type'] = 0;}
        if($name_of_bank!=""){ $update_case_error_log['name_of_bank'] = 1; }else{ $update_case_error_log['name_of_bank'] = 0;}
        if($bank_address!=""){ $update_case_error_log['bank_address'] = 1; }else{ $update_case_error_log['bank_address'] = 0;}
        if($bank_city!=""){ $update_case_error_log['bank_city'] = 1; }else{ $update_case_error_log['bank_city'] = 0;}
        if($bank_state!=""){ $update_case_error_log['bank_state'] = 1; }else{ $update_case_error_log['bank_state'] = 0;}
        if($bank_zip!=""){ $update_case_error_log['bank_zip'] = 1; }else{ $update_case_error_log['bank_zip'] = 0;}
        if($closing_balance_pdf!=""){ $update_case_error_log['current_balance'] = 1; }else{ $update_case_error_log['current_balance'] = 0;}
        if($start_date!=""){ $update_case_error_log['start_date'] = 1; }else{ $update_case_error_log['start_date'] = 0;}
        if($end_date!=""){ $update_case_error_log['end_date'] = 1; }else{ $update_case_error_log['end_date'] = 0;}
        if($end_date!=""){ $update_case_error_log['closing_balance'] = 1; }else{ $update_case_error_log['closing_balance'] = 0;}
        if($check_sum==0){ $update_case_error_log['check_sum'] = 1; }else{ $update_case_error_log['check_sum'] = 0;}
        $update_case_error_log['tpl_not_found'] =1;
        $this->case_error_log->addRecord($update_case_error_log);
        
        
        
        
       
        //die('here');
        //die('here');
        
        
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment;filename="'.$fileName.'" ');
        $objWriter->save(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        // $objWriter->save('php://output'); 
        $newFolderName = $this->input->post('newFolderName');
        if($newFolderName!="" && $this->input->post('accType')=='single'){
            
            $targetPath = FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'/success/'.$fileName;
            copy(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName, $targetPath);
            chmod($targetPath,  0777);
            if($this->input->post('check_all_pdf_process')!=""){
                $this->createZip(FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName,FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'.zip',true);
            }
        }
        $check_all_pdf_process = $this->input->post('check_all_pdf_process');
        if($newFolderName!="" && $this->input->post('isCompleteMultiAcc')){
            $targetPath = FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'/success/'.$fileName;
            copy(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName, $targetPath);
            chmod($targetPath,  0777);
            if($this->input->post('check_all_pdf_process')!=""){
                $this->createZip(FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName,FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'.zip',true);
            }
        }
        
        
        /*$data = array();
        $data['summary'] = $this->summary_level_data->fetchSummaryLevelDataForCategorization($last_history_id,$file_no);
        foreach($data['summary'] as $key => $value){
            //$data['summary'][$key]->Se10 = '';
            $data['summary'][$key]->account_number = $this->encryption->decrypt($data['summary'][$key]->account_number);
            $accountNumber = $data['summary'][$key]->account_number;
            $data['summary'][$key]->account_holder_name = $this->encryption->decrypt($data['summary'][$key]->account_holder_name);
        }
        
        $data['data'] = $this->customer_txn_data->fetchCustomerTxnDataForCategorization($last_history_id,$file_no);
        
        foreach($data['data'] as $key => $value){
            foreach($value as $k=>$v){
                //$data['data'][$key]->Se10 = '';
                $data['data'][$key]->account_number = $accountNumber;
            }
        }
        
        $jsonData = json_encode($data);
        //die;
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://13.93.202.124:5000/categorization",
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
        //echo $response;
        
        //die('here');
        $input = array();
        $input['token'] = $response;
        $input['status'] = 0;
        $input['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_categories_token',$input);*/
            
        
        
		$output['filename'] = $fileName;
		/**Add new Params*/
		$output['check_sum'] = $check_sum;
		$output['total_deposits'] = $total_deposits;
		$output['count_deposits'] = $count_deposits;
		$output['total_withdrawals'] = $total_withdrawals;
		$output['count_withdrawals'] = $count_withdrawals;
		if($this->input->post('multiple_account')){
		      $output['multiple_account'] = true;
		      $output['page_array'] = $pageArray;
		      $output['newFolderName'] = $newFolderName;
		}else{
		      $output['multiple_account'] = false;
		}
		$output['check_all_pdf_process'] = $check_all_pdf_process;
		/**End Params*/
        $output['success'] = true;
        $output['history_id'] = $last_history_id;
        echo json_encode($output);die;     
    }

    /*function getCompleteDate($date,$year){
        if(strpos($date,'/') !== false) {
            $data = explode('/', $date);
            if(count($data)==2){
                return date('m/d/Y', strtotime(trim($date)."/".$year));
            }
        }
        if(strpos($date,'-') !== false) {
            $data =  explode('-', $date);
            if(count($data)==2){
                return date('m/d/Y', strtotime(trim($date)."-".$year));
            }
        }
        if(strpos($date,'.') !== false) {
            $data = explode('.', $date);
            if(count($data)==2){
                return date('m/d/Y', strtotime(trim($date).".".$year));
            }
        }
        return $date;
    }*/
    
    function getCompleteDate($date,$year,$bank_date_format=''){
        $date = trim($date);
        $year = trim($year);
        /*if($year!=""){
            $date = str_replace("/","-",$date);
            $date = date('m-d-Y', strtotime($date."-".$year));
        }
        return $date;*/
        if($bank_date_format=='m-d-y'){
            if($year!=""){
                if(strpos($date,'/') !== false) {
                    $data = explode('/', $date);
                    if(count($data)==2 && $data[0]>0 && $data[1]>0){
                        return DateTime::createFromFormat('m/d/Y',$date."/".$year)->format('m/d/Y');
                    }
                }
                if(strpos($date,'-') !== false) {
                    $data =  explode('-', $date);
                    if(count($data)==2 && $data[0]>0 && $data[1]>0){
                        return DateTime::createFromFormat('m-d-Y',$date."-".$year)->format('m-d-Y');
                    }
                }
                if(strpos($date,'.') !== false) {
                    $data = explode('.', $date);
                    if(count($data)==2 && $data[0]>0 && $data[1]>0){
                        return DateTime::createFromFormat('m.d.Y',$date.".".$year)->format('m.d.Y');
                    }
                }
            }
        }else if($bank_date_format=='d-m-y'){
            
            if(strpos($date,'/') !== false) {
                $data = explode('/', $date);
                if(count($data)==2 && $data[0]>0 && $data[1]>0){
                    $date = str_replace("/","-",$date);
                    $date = date('m-d-Y', strtotime($date."-".$year));
                    $date = str_replace("-","/",$date);
                    return $date;
                }
                if(count($data)==3 && $data[0]>0 && $data[1]>0){
                    $date = str_replace("/","-",$date);
                    $date = date('m-d-Y', strtotime($date));
                    $date = str_replace("-","/",$date);
                    return $date;
                }
            }else if(strpos($date,'-') !== false) {
                $data = explode('-', $date);
                
                if(count($data)==2 && $data[0]>0 && $data[1]>0){
                    $date = date('m-d-Y', strtotime($date."-".$year));
                    $date = str_replace("-","/",$date);
                    return $date;
                }
                if(count($data)==3 && $data[0]>0 && $data[1]>0){
                    $date = date('m-d-Y', strtotime($date));
                    $date = str_replace("-","/",$date);
                    return $date;
                }
            }
        }
        $date = str_replace("-","/",$date);
        return $date;
    }
	
	function newgetCompleteDate($date,$year){
        $date = trim($date);
        $year = trim($year);
        if($year!=""){
            if(strpos($date,'/') !== false) {
                $data = explode('/', $date);
                if(count($data)==2 && $data[0]>0 && $data[1]>0){  
                    $date = str_replace("/","-",$date);
                    $date = date('m-d-Y', strtotime($date."-".$year)); 
                    $date = str_replace("-","/",$date);
                    return $date;
                }
                if(count($data)==3 && $data[0]>0 && $data[1]>0){  
                    $date = str_replace("/","-",$date);
                    $date = date('m-d-Y', strtotime($date)); 
                    $date = str_replace("-","/",$date);
                    return $date;
                }
            }
            
        }
        $date = str_replace("-","/",$date);
        return $date;
    }
    
    function getYearFromDate($date){
        if(strpos($date,'/') !== false) {
            $data = explode('/', $date);
            if(count($data)==3){
                return trim($data[2]);
            }
        }
        if(strpos($date,'-') !== false) {
            $data =  explode('-', $date);
            if(count($data)==3){
                return trim($data[2]);
            }
        }
        if(strpos($date,'.') !== false) {
            $data = explode('.', $date);
            if(count($data)==3){
                return trim($data[2]);
            }
        }
    }
    
    function getBankStatementDataByBankId(){
        $bank_id = $this->input->get('bank_id');               
        $st_data = $this->bank_statement->getSingleRecordByBankId($bank_id); 
        if($st_data){
            $data['data'] = $st_data;
            $data['success']= true;
        }                     
        else{
            $data['data'] = '';
            $data['success']= false;
        }
        echo json_encode($data); die();   
    }
    
    function createZip($source, $destination,$flag = ''){
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }
        
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }
        
        $source = str_replace('\\', '/', realpath($source));
        if($flag)
        {
            $flag = basename($source) . '/';
            //$zip->addEmptyDir(basename($source) . '/');
        }
        
        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', realpath($file));
                
                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $flag.$file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $flag.$file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString($flag.basename($source), file_get_contents($source));
        }
        
        return $zip->close();
    }
    
    function createXLSBankStatementNewFormat(){
        error_reporting(0);
        $account_type = $this->input->post('account_type');
        $bank_date_format = $this->input->post('bank_date_format');
        $currency = $this->input->post('currency');
        $type = $this->input->post('type');
        $bank_id = $this->input->post('bank_id');
        if($type==1){
            $credits = $this->input->post('credits');
            $debits = $this->input->post('debits');
            $checks = $this->input->post('checks');
            //$service_fees = $this->input->post('service_fees');
        }else{
            $transactions = $this->input->post('transactions');
        }
        
        
        $account_number = trim($this->input->post('account_number'));
        $name = trim($this->input->post('name'));
        $se10 = trim($this->input->post('se10'));
        $contract_nbr = trim($this->input->post('contract_nbr'));
        $amort_date = trim($this->input->post('amort_date'));
        $instant_decision_date = trim($this->input->post('instant_decision_date'));
        $account_holder_name = trim($this->input->post('account_holder_name'));
        $account_type = trim($this->input->post('account_type'));
        $account_ownership = trim($this->input->post('account_ownership'));
        $name_of_bank = trim($this->input->post('name_of_bank'));
        $bank_address = trim($this->input->post('bank_address'));
        $bank_city = trim($this->input->post('bank_city'));
        $bank_state = trim($this->input->post('bank_state'));
        $bank_zip = trim($this->input->post('bank_zip'));
        //$current_balance = trim($this->input->post('current_balance'));
        $start_date = trim($this->input->post('start_date'));
        
        $end_date = trim($this->input->post('end_date'));
        $mdy = explode('/', $end_date);
        $year = $mdy[2];
        
        $closing_balance_pdf = trim($this->input->post('closing_balance'));
        $count_deposits = trim($this->input->post('count_deposits'));
        $count_withdrawals = trim($this->input->post('count_withdrawals'));
        $total_count_check_return = trim($this->input->post('total_count_check_return'));
        $total_count_inward_check_return = trim($this->input->post('total_count_inward_check_return'));
        $total_inward_check_return = trim($this->input->post('total_inward_check_return'));
        $total_count_outward_check_return = trim($this->input->post('total_count_outward_check_return'));
        $total_outward_check_return = trim($this->input->post('total_outward_check_return'));
        $count_ecs_or_emi = trim($this->input->post('count_ecs_or_emi'));
        $amount_ecs_or_emi = trim($this->input->post('amount_ecs_or_emi'));
        $route = trim($this->input->post('route'));
        $transaction_all_level_spreading_done = trim($this->input->post('transaction_all_level_spreading_done'));
        $check_sum = '';
        $summary_and_transaction_match = trim($this->input->post('summary_and_transaction_match'));
        $pages = trim($this->input->post('pages'));
        $begining_balance = $this->input->post('begining_balance');
        $service_fee_1 = $this->input->post('service_fee_1');
        $service_fee_2 = $this->input->post('service_fee_2');
        if($this->input->post('original_pdf_file_name')!=""){
            $data = substr($this->input->post('original_pdf_file_name'), 0 , (strrpos($this->input->post('original_pdf_file_name'), ".")));
        }else{
            $data = "data";
        }
        $last_txn_date = '';
        $fileName = $data.'_'.time().'.xlsx';
        if($fileName && $this->input->post('original_pdf_file_name')!=""){
            /**Insert History*/
            $bankArray =array();
            $bankArray['bank_id'] = $bank_id;
            $bankArray['file_name'] = $this->input->post('upload_pdf_file');
            $bankArray['original_pdf_file_name'] = $this->input->post('original_pdf_file_name');
            $bankArray['created_on'] = date("Y-m-d h:i:sa");
            $bankArray['downloaded_file_name'] = $fileName;
            $this->tpl_history->addNewRecords($bankArray);
            /**End History*/
            // user count from tbl_banks
            $this->db->where('id',$bank_id);
            $this->db->set('uses_count','uses_count+1',FALSE);
            $this->db->update('tbl_banks');
        }
        
        $newFolderName =  $this->input->post('newFolderName');
        $xlsx_file_name = "";
        if($newFolderName!=""){
            $result = $this->bulk_upload->getRecordByFolderName($newFolderName);
            $xlsx_file_name  = $result->xlsx_file_name; 
        }
        
        if($this->input->post('uploadedXlsFileName') && $xlsx_file_name!=""){
            if($this->input->post('uploadedXlsFileName')){
                $fileName = $this->input->post('uploadedXlsFileName');
            }else{
                $fileName = $xlsx_file_name;
            }
            
            
            $objPHPExcel  = PHPExcel_IOFactory::load(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
            $objPHPExcel->setActiveSheetIndex(1);
            $row = $objPHPExcel->getActiveSheet()->getHighestRow();
            $i = $row;
            /*$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, 'Nirdesh');
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            header("Content-Type: application/vnd.ms-excel");
            header('Content-Disposition: attachment;filename="'.$fileName.'" ');
            $objWriter->save(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
            die('here');*/
            //echo $fileName;echo $i;die('here');
            #$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, number_format(str_replace(array("$",","), '', $begining_balance),2));
            
        }else{
            $i = 29;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex(1);
            
            $link_style_array = [
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size'  => 12
                )
            ];
            $objPHPExcel->getActiveSheet()->getStyle("B29:K29")->applyFromArray($link_style_array);
            //$objPHPExcel->getActiveSheet()->getStyle('B29:K29')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BEDBFA');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            
            $this->setBorderCell($objPHPExcel,"A2:A11");
            $this->cellStringAttr($objPHPExcel,'Currency ->','A1','000000',true);
            $this->cellStringAttr($objPHPExcel,'Cutomer Demographic','A2','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Customer ID','A3');
            $this->cellStringAttr($objPHPExcel,'Case ID','A4');
            $this->cellStringAttr($objPHPExcel,'Business ID','A5');
            $this->cellStringAttr($objPHPExcel,'SE 10','A6');
            $this->cellStringAttr($objPHPExcel,'Container Type','A7');
            $this->cellStringAttr($objPHPExcel,'Data Source','A8');
            $this->cellStringAttr($objPHPExcel,'Spreading done on','A9');
            
            $this->cellStringAttr($objPHPExcel,'Version','A11','4E21DF',true,'BEDBFA','center');
            
            $this->setBorderCell($objPHPExcel,"B2:B11");
            $this->cellStringAttr($objPHPExcel,'Data Inputs','B2','4E21DF',true,'BEDBFA');
            
            $file_name = $this->bulk_upload->getRecordByFolderName($newFolderName);
            $this->cellStringAttr($objPHPExcel,pathinfo($file_name->file_name, PATHINFO_FILENAME),'B6');
            $this->cellStringAttr($objPHPExcel,'Bank','B7');
            $now = new DateTime();
            $now->setTimezone(new DateTimezone('Asia/Kolkata'));
            $now->format('Y-m-d H:i:s');
            $this->cellStringAttr($objPHPExcel,$now,'B9','','','','right');
            $this->mergeCell($objPHPExcel,'A10:B10','A10','Application ID');
            $this->cellStringAttr($objPHPExcel,'Application ID','A10','','','','left');
            $this->cellStringAttr($objPHPExcel,'V8','B11','4E21DF',true,'BEDBFA','center');
            
            
            $this->setBorderCell($objPHPExcel,"C2:C27");
            $this->cellStringAttr($objPHPExcel,'Country ->','C1','000000',true);
            $this->cellStringAttr($objPHPExcel,'General information','C2','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Business Unit','C3');
            $this->cellStringAttr($objPHPExcel,'Account Number','C4');
            $this->cellStringAttr($objPHPExcel,'Account Holder Name','C5');
            $this->cellStringAttr($objPHPExcel,'Secondary Account Holder Name','C6');
            $this->cellStringAttr($objPHPExcel,'Account Type','C7');
            $this->cellStringAttr($objPHPExcel,'Account Ownership','C8','',false,'F9F907');
            $this->cellStringAttr($objPHPExcel,'Bank Name','C9');
            $this->cellStringAttr($objPHPExcel,'Routing No.','C10','',false,'C5D9BF');
            $this->cellStringAttr($objPHPExcel,'Current Balance','C11');
            $this->cellStringAttr($objPHPExcel,'As of Date','C12');
            $this->cellStringAttr($objPHPExcel,'Tax Payment Indicator','C13','',false,'C5D9BF');
            $this->cellStringAttr($objPHPExcel,'Drawing Power','C14','',false,'C5D9BF');
            $this->cellStringAttr($objPHPExcel,'Total count of over utilization','C15','',false,'C5D9BF');
            $this->cellStringAttr($objPHPExcel,'Interest Servicing Days','C16','',false,'C5D9BF');
            $this->cellStringAttr($objPHPExcel,'Address line 1','C17');
            $this->cellStringAttr($objPHPExcel,'Address line 2','C18');
            $this->cellStringAttr($objPHPExcel,'Address line 3','C19');
            $this->cellStringAttr($objPHPExcel,'City','C20');
            $this->cellStringAttr($objPHPExcel,'State','C21');
            $this->cellStringAttr($objPHPExcel,'Zip','C22');
            $this->cellStringAttr($objPHPExcel,'Country','C23');
            $this->cellStringAttr($objPHPExcel,'Country Code','C24');
            
            $this->setBorderCell($objPHPExcel,"D2:D27");
            $this->cellStringAttr($objPHPExcel,'Account 1','D2','000000',true,'808080');
            $this->cellStringAttr($objPHPExcel,'MF','D3');
            
            if($account_number!=""){
                $this->cellStringAttr($objPHPExcel,$account_number,'D4','',false,'','left',true);
            }
            
            
            if($account_holder_name!=""){
                $this->cellStringAttr($objPHPExcel,$account_holder_name,'D5');
            }
            $this->cellStringAttr($objPHPExcel,$account_type,'D7');
            
            if($name_of_bank!=""){
                $this->cellStringAttr($objPHPExcel,$name_of_bank,'D9');
            }
            
            if($closing_balance_pdf){
                $this->cellStringAttr($objPHPExcel,number_format(str_replace(array("$",","), '', $closing_balance_pdf),2),'D11');
            }
            
            $this->cellStringAttr($objPHPExcel,$end_date,'D12');
            
            if($bank_address){
                $this->cellStringAttr($objPHPExcel,$bank_address,'D17');
            }
            
            if($bank_city){
                $this->cellStringAttr($objPHPExcel,$bank_city,'D20');
            }
            
           
            if($bank_state){
                $this->cellStringAttr($objPHPExcel,$bank_state,'D21');
            }
            
            if($bank_zip){
                $this->cellStringAttr($objPHPExcel,$bank_zip,'D22');
            }
            
            $this->cellStringAttr($objPHPExcel,'USA','D23');
           
            
            $this->setBorderCell($objPHPExcel,"B29:K29");
            
            /*Test*/
            /*$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
             $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);*/
            
            /*Test*/
            
            $objPHPExcel->getActiveSheet()->setTitle('Customer-Transaction Data');
            $this->cellStringAttr($objPHPExcel,'Account Number','B29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Transaction ID','C29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Transaction date','D29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Description','E29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Check number','F29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Transaction Amount','G29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Transaction Currency code','H29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Posted order','I29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Available balance','J29','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Credit or Debit','K29','4E21DF',true,'BEDBFA');
            
            //$objPHPExcel->getActiveSheet()->SetCellValue('K29', number_format(str_replace(array("$",","), '', $begining_balance),2));
            $end_of_balance = number_format(str_replace(array("$",","), '', $begining_balance),2);
        }
        
        
        // Config
        
        
        
        $total_deposits = 0;
        $total_withdrawals = 0;
        $count_deposits = 0;
        $count_withdrawals = 0;
        $date = '';
        if($type==1){
            if($credits){
                foreach ($credits as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                        
                        
                        $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                        $this->cellStringAttr($objPHPExcel,'','C' . $i);
                        $this->cellStringAttr($objPHPExcel,$date,'D' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '','E' . $i);
                        $this->cellStringAttr($objPHPExcel,'','F' . $i);
                        
                        $this->cellStringAttr($objPHPExcel,isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '', $value['amount'])),2)) : '','G' . $i);
                        $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                        $this->cellStringAttr($objPHPExcel,'','I' . $i);
                        
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $value['amount']),2);
                            $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $value['amount']),2);
                            $count_deposits ++;
                        }
                        $this->cellStringAttr($objPHPExcel,str_replace(array("$",","), '', $end_of_balance),'J' . $i);
                        $this->cellStringAttr($objPHPExcel,'Credit','K' . $i);
                    }
                }
            }
            
           
            if($debits){
                foreach ($debits as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                        
                        $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                        $this->cellStringAttr($objPHPExcel,'','C' . $i);
                        $this->cellStringAttr($objPHPExcel,$date,'D' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['description']) ?  preg_replace("/[\n\r]/", " ",trim($value['description'])) : '','E' . $i);
                        $this->cellStringAttr($objPHPExcel,'','F' . $i);
                        
                        $this->cellStringAttr($objPHPExcel,isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '','G' . $i);
                        $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                        $this->cellStringAttr($objPHPExcel,'','I' . $i);
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        $this->cellStringAttr($objPHPExcel,str_replace(array("$",","), '', $end_of_balance),'J' . $i);
                        $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                    }
                }
            }
            
            if($checks){
                foreach ($checks as $key => $value) {
                    if(isset($value['amount'])){
                        
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                            $last_txn_date = $date;
                        }else{
                            $date = '';
                        }
                        
                        $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                        $this->cellStringAttr($objPHPExcel,'','C' . $i);
                        $this->cellStringAttr($objPHPExcel,$date,'D' . $i);
                        if($bank_id==116){
                            $this->cellStringAttr($objPHPExcel,$value['description'],'E' . $i);
                        }else{
                            $this->cellStringAttr($objPHPExcel,'Check','E' . $i);
                        }
                        
                        if($value['cheque_no']=='Check_number_not_found'){
                            $value['cheque_no'] = '';
                        }
                        
                       
                        $this->cellStringAttr($objPHPExcel,isset($value['cheque_no']) ? $value['cheque_no'] : '','F' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '','G' . $i);
                        $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                        $this->cellStringAttr($objPHPExcel,'','I' . $i);
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        
                        $this->cellStringAttr($objPHPExcel,str_replace(array(","), '', $end_of_balance),'J' . $i);
                        $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                    }
                }
            }
            
            if($service_fees){
                foreach ($service_fees as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                        }else{
                            $date = '';
                        }
                     
                        
                        $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                        $this->cellStringAttr($objPHPExcel,'','C' . $i);
                        $this->cellStringAttr($objPHPExcel,$date,'D' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['description']) ? $value['description'] : '','E' . $i);
                        $this->cellStringAttr($objPHPExcel,'','F' . $i);
                        
                        
                        
                        $this->cellStringAttr($objPHPExcel,isset($value['amount']) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$value['amount'])),2)) : '','G' . $i);
                        $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                        $this->cellStringAttr($objPHPExcel,'','I' . $i);
                        if(isset($value['amount']) && $value['amount']!=""){
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            $closing_balance = $end_of_balance;
                            $count_withdrawals++;
                        }
                        $this->cellStringAttr($objPHPExcel,str_replace(array(","), '', $end_of_balance),'J' . $i);
                        $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                    }
                }
            }
            if(isset($service_fee_1) && $service_fee_1!=""){
                $service_fee_amount_1 = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2));
                if (is_numeric($service_fee_amount_1) && $service_fee_amount_1!=0){
                    $i++;
                   
                    
                    //$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','C' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $last_txn_date);
                    $this->cellStringAttr($objPHPExcel,$last_txn_date,'D' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $this->input->post('service_fee_title_1'));
                    $this->cellStringAttr($objPHPExcel,$this->input->post('service_fee_title_1'),'E' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','F' . $i);
                    
                    
                    
                    //$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($service_fee_1) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2)) : '');
                    $this->cellStringAttr($objPHPExcel,isset($service_fee_1) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_1)),2)) : '','G' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, 'USD');
                    $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','I' . $i);
                    
                    if($this->input->post('service_fee_type_1')=='dr'){
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $service_fee_1))),2);
                        $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $service_fee_1)),2);
                        $closing_balance = $end_of_balance;
                        $count_withdrawals++;
                    }else{
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $service_fee_1),2);
                        $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $service_fee_1),2);
                        $count_deposits ++;
                    }
                    
                    //$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance));
                    $this->cellStringAttr($objPHPExcel,str_replace(array(","), '', $end_of_balance),'J' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, 'Debit');
                    $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                }
                
            }
            
            if(isset($service_fee_2) && $service_fee_2!=""){
                $service_fee_amount_2 = str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2));
                if (is_numeric($service_fee_amount_2) && $service_fee_amount_2!=0){
                    $i++;
                   
                    
                    //$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','C' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $last_txn_date);
                    $this->cellStringAttr($objPHPExcel,$last_txn_date,'D' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $this->input->post('service_fee_title_2'));
                    $this->cellStringAttr($objPHPExcel,$this->input->post('service_fee_title_2'),'E' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','F' . $i);
                   
                    
                    
                    //$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, isset($service_fee_2) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2)) : '');
                    $this->cellStringAttr($objPHPExcel,isset($service_fee_2) ? str_replace(array("$",","), '',number_format(abs(str_replace(array("$",","), '',$service_fee_2)),2)) : '','G' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, 'USD');
                    $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, '');
                    $this->cellStringAttr($objPHPExcel,'','I' . $i);
                    
                    if($this->input->post('service_fee_type_2')=='dr'){
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $service_fee_2))),2);
                        $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $service_fee_2)),2);
                        $closing_balance = $end_of_balance;
                        $count_withdrawals++;
                    }else{
                        $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $service_fee_1),2);
                        $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $service_fee_1),2);
                        $count_deposits ++;
                    }
                    //die('here');
                    
                    //$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, str_replace(array(","), '', $end_of_balance));
                    $this->cellStringAttr($objPHPExcel,str_replace(array(","), '', $end_of_balance),'J' . $i);
                    //$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, 'Debit');
                    $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                }
                
            }
            
            $check_sum = number_format(str_replace(array("$",","), '', $begining_balance) + str_replace(array("$",","), '', $total_deposits) - str_replace(array("$",","), '', $total_withdrawals) - str_replace(array("$",","), '', $closing_balance_pdf),2);
            
        }else{
            /*For Transactions*/
            $bank_statement_data = $this->bank_statement->getSingleRecordByBankId($bank_id);
            
            if($transactions){
                foreach ($transactions as $key => $value) {
                    if(isset($value['amount'])){
                        $i++;
                        if(isset($value['date'])){
                            $date = $this->getCompleteDate($value['date'],$year,$bank_date_format);
                        }else{
                            $date = '';
                        }
                       
                        //$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account_number, PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->cellStringAttr($objPHPExcel,$account_number,'B' . $i,'',false,'','left',true);
                        //$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, '');
                        $this->cellStringAttr($objPHPExcel,'','C' . $i);
                        //$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $date);
                        $this->cellStringAttr($objPHPExcel,$date,'D' . $i);
                        
                        $isCheck = false;
                        if(isset($bank_statement_data->fetch_check_from_desc) && $bank_statement_data->fetch_check_from_desc!=""){
                            $array = explode("|",$bank_statement_data->fetch_check_from_desc);
                            foreach($array as $arr){
                                if (preg_match('/\b'.$arr.'\b/', $value['description'])) {
                                    $isCheck = true;
                                    $fetch_check_from_desc = $arr;
                                }
                            }
                        }
                        
                        if(isset($value['cheque_no']) && $value['cheque_no']!=""){
                            $description = trim(str_replace($value['cheque_no'],"",$value['description']));
                        }else if($isCheck){
                            $split_check = trim(str_replace($fetch_check_from_desc,"",$value['description']));
                            $result = explode(" ", $split_check, 2);//Get first numeric value
                            if (is_numeric($result[0])){
                                $value['cheque_no'] = $result[0];
                            }
                            //If not numeric $result[0] then get first numeric value
                            if (!is_numeric($result[0])){
                                $filteredNumbers = array_filter(preg_split("/\D+/", $split_check));
                                $firstOccurence = reset($filteredNumbers);
                                if($firstOccurence){
                                    $value['cheque_no'] = $firstOccurence;
                                }
                            }
                            
                            $description = trim(str_replace($value['cheque_no'],"",$value['description']));;
                        }else{
                            $description = $value['description'];
                        }
                        $this->cellStringAttr($objPHPExcel,preg_replace("/[\n\r]/", " ",trim($description)),'E' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['cheque_no']) ? $value['cheque_no'] : '','F' . $i);
                        $this->cellStringAttr($objPHPExcel,isset($value['amount']) ? str_replace(array("$",","), '', number_format(abs(str_replace(array("$",","), '', $value['amount'])),2)) : '','G' . $i);
                        $this->cellStringAttr($objPHPExcel,$currency,'H' . $i);
                        $this->cellStringAttr($objPHPExcel,'','I' . $i);
                        
                        if(isset($value['type']) && $value['type']=="cr"){
                            $total_deposits = number_format(str_replace(array("$",","), '', $total_deposits) + str_replace(array("$",","), '', $value['amount']),2);
                            $count_deposits ++;
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + str_replace(array("$",","), '', $value['amount']),2);
                            $closing_balance = $end_of_balance;
                            $this->cellStringAttr($objPHPExcel,str_replace(array("$",","), '', $end_of_balance),'J' . $i);
                            $this->cellStringAttr($objPHPExcel,'Credit','K' . $i);
                            
                        }else if(isset($value['type']) && $value['type']=="dr"){
                            $total_withdrawals = number_format(str_replace(array("$",","), '', $total_withdrawals) + abs(str_replace(array("$",","), '', $value['amount'])),2);
                            
                            $count_withdrawals++;
                            $end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            $closing_balance = $end_of_balance;
                            //$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, 'Debit');
                            $this->cellStringAttr($objPHPExcel,str_replace(array("$",","), '', $end_of_balance),'J' . $i);
                            $this->cellStringAttr($objPHPExcel,'Debit','K' . $i);
                            //$end_of_balance = number_format(str_replace(array("$",","), '', $end_of_balance) + (-1 * abs(str_replace(array("$",","), '', $value['amount']))),2);
                            
                        }
                        
                        
                    }
                }
            }
            /*End Transactions*/
            /*echo $begining_balance.'begining_balance'.'</br>';
             echo $total_deposits.'total_deposits'.'</br>';
             echo $total_withdrawals.'total_withdrawals'.'</br>';
             echo $closing_balance.'closing_balance'.'</br>';
             die('here');*/
            $check_sum = number_format(str_replace(array("$",","), '', $begining_balance) + str_replace(array("$",","), '', $total_deposits) - str_replace(array("$",","), '', $total_withdrawals) - str_replace(array("$",","), '', $closing_balance_pdf),2);
        }
        
        if($this->input->post('uploadedXlsFileName') && $xlsx_file_name!=""){
            $fileName = $xlsx_file_name;
            //$objPHPExcel  = PHPExcel_IOFactory::load(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
            $objPHPExcel->setActiveSheetIndex(0);
            foreach(range('D','H') as $v){
                if($objPHPExcel->getActiveSheet()->getCell($v.'6')->getValue()==""){
                    $coulmName = $v;
                    break;
                }
            }
            $this->setBorderCell($objPHPExcel,$v."3:".$v."48");
        }else{
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
            
            $objPHPExcel->getActiveSheet()->setTitle('Summary Level Data');
            
            //$this->cellColor($objPHPExcel,'B2:AF2', 'F28A8C');
            
            $this->mergeCell($objPHPExcel,'C1:H1','C1','Account 1');
            $this->mergeCell($objPHPExcel,'I1:N1','I1','Account 2');
            $this->mergeCell($objPHPExcel,'O1:T1','O1','Account 3');
            $this->mergeCell($objPHPExcel,'U1:Z1','U1','Account 4');
            $this->mergeCell($objPHPExcel,'AA1:AF1','AA1','Account 5');
            
            
            
            $this->cellStringAttr($objPHPExcel,'Particulars','B2','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Start statement date','B3');
            $this->cellStringAttr($objPHPExcel,'End statement date','B4');
            $this->cellStringAttr($objPHPExcel,'Statement Period','B5');
            $this->cellStringAttr($objPHPExcel,'Currency Code','B6');
            $this->cellStringAttr($objPHPExcel,'Unit of Currency','B7');
            $this->cellStringAttr($objPHPExcel,'Number of months','B8','',false,'F3C6C5');
            $this->cellStringAttr($objPHPExcel,'Opening Balance','B10');
            $this->cellStringAttr($objPHPExcel,'Ending Balance','B11');
            $this->cellStringAttr($objPHPExcel,'Total $ Deposits','B12');
            $this->cellStringAttr($objPHPExcel,'Total # Deposits','B13');
            $this->cellStringAttr($objPHPExcel,'Total $ Withdrawals','B14');
            $this->cellStringAttr($objPHPExcel,'Total # Withdrawals','B15');
            $this->cellStringAttr($objPHPExcel,'Total Number of Check Returns','B16');
            $this->cellStringAttr($objPHPExcel,'Total # Inward Check Return','B17','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total $ Inward Check Return','B18','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total # Outward Check Return','B19','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total $ Outward Check Return','B20','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Count ECS or EMI (Monthly)','B21','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Amount ECS or EMI (Monthly)','B22','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Minimum Balance','B23');
            $this->cellStringAttr($objPHPExcel,'Checksum','B24');
            
            $this->cellStringAttr($objPHPExcel,'Customers Concentration','B26','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Top Customer 1','B27','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Customer 2','B28','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Customer 3','B29','','','','center');
            $this->cellStringAttr($objPHPExcel,'Concentration %','B30','','','','center');
   
           
            $this->cellStringAttr($objPHPExcel,'Customers share in credits','B31','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Customer share 1','B32','','','','center');
            $this->cellStringAttr($objPHPExcel,'Customer share 2','B33','','','','center');
            $this->cellStringAttr($objPHPExcel,'Customer share 3','B34','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total credits Amount','B35','','','','center');
            
            $this->cellStringAttr($objPHPExcel,'Vendors Concentration','B36','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 1','B37','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 2','B38','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 3','B39','','','','center');
            $this->cellStringAttr($objPHPExcel,'Concentration %','B40','','','','center');
            
            $this->cellStringAttr($objPHPExcel,'Vendors share in debits','B41','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Vendor share 1','B42','','','','center');
            $this->cellStringAttr($objPHPExcel,'Vendor share 2','B43','','','','center');
            $this->cellStringAttr($objPHPExcel,'Vendor share 3','B44','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total credits Amount','B45','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total debits Amount','B46','','','','center');
            
            
            $this->cellStringAttr($objPHPExcel,'Credit card details','B47','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'AXP','B48','4E21DF',false,'F3C6C5','center');
            $this->cellStringAttr($objPHPExcel,'Other card','B49','4E21DF',false,'F3C6C5','center');
            
            if(trim($account_number)!=""){
                $this->mergeCell($objPHPExcel,'C2:H2','C2','');
                $this->cellStringAttr($objPHPExcel,'Account 1:'.$account_number,'C2','4E21DF',true,'BEDBFA','center');
            }
            
            $this->setBorderCell($objPHPExcel,"C2:H2");
            $this->setBorderCell($objPHPExcel,"B2:B49");
            $this->setBorderCell($objPHPExcel,"C3:C49");
            $coulmName = 'C';
        }
        
        if($start_date!=""){
            $this->cellStringAttr($objPHPExcel,$start_date,$coulmName.'3','','','','right');
        }
        
        if($end_date!=""){
            $this->cellStringAttr($objPHPExcel,$end_date,$coulmName.'4','','','','right');
        }
        
        if($start_date!="" && $end_date!=""){
            $this->cellStringAttr($objPHPExcel,'1',$coulmName.'5','','','','center');
        }
        
        $this->cellStringAttr($objPHPExcel,$currency,$coulmName.'6','','','','center');
        
        $this->cellStringAttr($objPHPExcel,'Actuals',$coulmName.'7','','','','center');
        
        if($begining_balance){
            $begining_balance = number_format(str_replace(array("$",","), '', $begining_balance),2);
            $this->cellStringAttr($objPHPExcel,$begining_balance,$coulmName.'10','','','','right');
        }
        
        if($closing_balance_pdf){
            $this->cellStringAttr($objPHPExcel,number_format(str_replace(array("$",","), '', $closing_balance_pdf),2),$coulmName.'11','','','','right');
        }
        
        if($total_deposits){
            $this->cellStringAttr($objPHPExcel,$total_deposits,$coulmName.'12','','','','right');
        }
        
        if($count_deposits){
            $this->cellStringAttr($objPHPExcel,$count_deposits,$coulmName.'13','','','','right');
        }
        
        if($total_withdrawals){
            $this->cellStringAttr($objPHPExcel,$total_withdrawals,$coulmName.'14','','','','right');
        }
        
        if($count_withdrawals){
            $this->cellStringAttr($objPHPExcel,$count_withdrawals,$coulmName.'15','','','','right');
        }
        
        if($check_sum){
            $this->cellStringAttr($objPHPExcel,$check_sum,$coulmName.'24','',false,'FFCC99','right');
        }
        
       
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment;filename="'.$fileName.'" ');
        $objWriter->save(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        // $objWriter->save('php://output');
        $this->db->where('folder_name',$newFolderName);
        $this->db->set('output_cnt','output_cnt+1',FALSE);
        $this->db->update('tbl_bulk_upload');
        $newFolderName = $this->input->post('newFolderName');

        if($newFolderName!="" && $this->input->post('accType')=='single'){
            
            $targetPath = FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'/success/'.$fileName;
            copy(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName, $targetPath);
            chmod($targetPath,  0777);
            
            $result_multi_file = $this->bulk_upload->getRecordByFolderName($newFolderName);
            //if($this->input->post('countPdfExt')==$this->input->post('count')){
            if($result_multi_file->input_cnt==$result_multi_file->output_cnt){
                $xlsx_date_info = array();
                $xlsx_path = FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'/success/';
                foreach (glob($xlsx_path.'*.xlsx') as $key=>$filename) {
                    $objPHPExcel  = PHPExcel_IOFactory::load($filename);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $xlsx_date_info[$key]['date'] = $objPHPExcel->getActiveSheet()->getCell('C3')->getValue();
                    $xlsx_date_info[$key]['account_number'] = $objPHPExcel->getActiveSheet()->getCell('C2')->getValue();
                    $xlsx_date_info[$key]['file_name'] = $filename;
                    $objPHPExcel->disconnectWorksheets();
                    unset($objPHPExcel);
                }
                
                /*echo"<pre>";
                print_r($xlsx_date_info);
                echo"</pre>";*/
                
                usort($xlsx_date_info, function ($a, $b) {
                    $dateA = DateTime::createFromFormat('m/d/Y', $a['date']);
                    $dateB = DateTime::createFromFormat('m/d/Y', $b['date']);
                    // ascending ordering, use `<=` for descending
                    return $dateA >= $dateB;
                });
                $mainFile = ""; 
               
                $xlsx_date_info = array_reverse($xlsx_date_info);
                $filterData = array();
                foreach($xlsx_date_info as $key=>$data){
                    if (!array_key_exists($data['account_number'],$filterData)){
                        $filterData[$data['account_number']] = 1;
                    }else if(array_key_exists($data['account_number'],$filterData) && $filterData[$data['account_number']]<6){
                        $filterData[$data['account_number']] = $filterData[$data['account_number']]+1;
                    }else{
                        unlink($data['file_name']);
                        unset($xlsx_date_info[$key]);
                    }
                    
                }
                $xlsx_date_info = array_reverse($xlsx_date_info);
                echo"<pre>";
                print_r($xlsx_date_info);
                
                
                $checkAccountNumber = array();
                foreach($xlsx_date_info as $key=>$xlsx_info){
                    $accNumberInfo = explode(":",$xlsx_info['account_number']);
                    $accNumber = $accNumberInfo[1];
                    if($key==0){
                        //echo $xlsx_info['file_name'];
                        $mainFile = $xlsx_info['file_name'];
                        $objPHPExcelMain  = PHPExcel_IOFactory::load($xlsx_info['file_name']);
                        $objPHPExcelMain->setActiveSheetIndex(0);
                        echo $accNumber;
                        print_r($checkAccountNumber);
                        if (!in_array($accNumber, $checkAccountNumber)){
                           array_push($checkAccountNumber,$accNumber);
                        }
                        
                        /*echo $xlsx_info['file_name'];
                        die('here');
                        
                        $bulkUploadArr =array();
                        $bulkUploadArr['xlsx_file_name'] = $fileName;
                        $this->bulk_upload->updateRecordByFolderName($newFolderName,$bulkUploadArr);*/
                        echo"ABCD";
                        echo"<pre>";
                        print_r($checkAccountNumber);
                    }else{
                        $objPHPExcelMain->setActiveSheetIndex(0);
                        echo $xlsx_info['file_name'];
                        $objPHPExcel_other  = PHPExcel_IOFactory::load($xlsx_info['file_name']);
                        $objPHPExcel_other->setActiveSheetIndex(0);
                        
                        if (!in_array($accNumber, $checkAccountNumber)){
                            array_push($checkAccountNumber,$accNumber);
                            if(count($checkAccountNumber)==1){
                                foreach(range('D','H') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            if(count($checkAccountNumber)==2){
                                $objPHPExcelMain->setActiveSheetIndex(1);
                                $objPHPExcel_other->setActiveSheetIndex(1);
                                
                                $this->addMultieAcctTxnTab($objPHPExcelMain,$objPHPExcel_other,"E","Account 2");
                                
                                $objPHPExcelMain->setActiveSheetIndex(0);
                                $objPHPExcel_other->setActiveSheetIndex(0);
                                
                                $this->mergeCell($objPHPExcelMain,'I2:N2','I2','');
                                $this->cellStringAttr($objPHPExcelMain,'Account 2:'.$accNumber,'I2','4E21DF',true,'BEDBFA','center');
                                $this->setBorderCell($objPHPExcelMain,"I2:N2");
                                foreach(range('I','N') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            
                            if(count($checkAccountNumber)==3){
                                $objPHPExcelMain->setActiveSheetIndex(1);
                                $objPHPExcel_other->setActiveSheetIndex(1);
                                
                                $this->addMultieAcctTxnTab($objPHPExcelMain,$objPHPExcel_other,"F","Account 3");
                                
                                $objPHPExcelMain->setActiveSheetIndex(0);
                                $objPHPExcel_other->setActiveSheetIndex(0);
                                $this->mergeCell($objPHPExcelMain,'O2:T2','O2','');
                                $this->cellStringAttr($objPHPExcelMain,'Account 3:'.$accNumber,'O2','4E21DF',true,'BEDBFA','center');
                                $this->setBorderCell($objPHPExcelMain,"O2:T2");
                                foreach(range('O','T') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            
                            echo"IF";
                            echo"<pre>";
                            print_r($checkAccountNumber);
                           
                        }else{
                            foreach($checkAccountNumber as $z=>$value){
                                if($value==trim($accNumber)){
                                    break; 
                                }
                            }
                            
                            $objPHPExcelMain->setActiveSheetIndex(1);
                            $objPHPExcel_other->setActiveSheetIndex(1);
                            if($z==0){
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D11')->getValue(),'D11');
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D12')->getValue(),'D12');
                                $objPHPExcelMain->setActiveSheetIndex(0);
                                $objPHPExcel_other->setActiveSheetIndex(0);
                                
                                foreach(range('D','H') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            if($z==1){
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D11')->getValue(),'E11');
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D12')->getValue(),'E12');
                                $objPHPExcelMain->setActiveSheetIndex(0);
                                $objPHPExcel_other->setActiveSheetIndex(0);
                                
                                foreach(range('I','N') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            
                            if($z==2){
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D11')->getValue(),'F11');
                                $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D12')->getValue(),'F12');
                                $objPHPExcelMain->setActiveSheetIndex(0);
                                $objPHPExcel_other->setActiveSheetIndex(0);
                                
                                foreach(range('O','T') as $v){
                                    if($objPHPExcelMain->getActiveSheet()->getCell($v.'6')->getValue()==""){
                                        $coulmName = $v;
                                        break;
                                    }
                                }
                            }
                            echo"ELSE";
                            echo$z;
                            echo"<pre>";
                            print_r($checkAccountNumber);
                        }
                        
                        
                        $this->setBorderCell($objPHPExcelMain,$v."3:".$v."49");
                        
                        
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C3')->getValue(),$coulmName.'3','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C4')->getValue(),$coulmName.'4','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C5')->getValue(),$coulmName.'5','','','','center');
                        
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C6')->getValue(),$coulmName.'6','','','','center');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C7')->getValue(),$coulmName.'7','','','','center');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C10')->getValue(),$coulmName.'10','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C11')->getValue(),$coulmName.'11','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C12')->getValue(),$coulmName.'12','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C13')->getValue(),$coulmName.'13','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C14')->getValue(),$coulmName.'14','','','','right');
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C15')->getValue(),$coulmName.'15','','','','right');
                        
                        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('C24')->getValue(),$coulmName.'24','',false,'FFCC99','right');
                        
                        $objPHPExcel_other->setActiveSheetIndex(1);
                        $getHighestRow = $objPHPExcel_other->getActiveSheet()->getHighestRow();
                        
                        $objPHPExcelMain->setActiveSheetIndex(1);
                        $j = $objPHPExcelMain->getActiveSheet()->getHighestRow();
                        
                        /**For second Account*/
                        
                        
                        for($n=30;$n<=$getHighestRow;$n++){
                            echo $objPHPExcel_other->getActiveSheet()->getCell('B'.$n)->getValue();
                            $j++;
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('B'.$n)->getValue(),'B'.$j,'',false,'','left',true);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D'.$n)->getValue(),'D'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('E'.$n)->getValue(),'E'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('F'.$n)->getValue(),'F'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('G'.$n)->getValue(),'G'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('H'.$n)->getValue(),'H'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('I'.$n)->getValue(),'I'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('J'.$n)->getValue(),'J'.$j);
                            $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('K'.$n)->getValue(),'K'.$j);
                            //echo$i;
                        }
                        
                        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcelMain);
                        header("Content-Type: application/vnd.ms-excel");
                        header('Content-Disposition: attachment;filename="'.$mainFile.'" ');
                        $objWriter->save($mainFile);
                        
                        $objPHPExcel_other->disconnectWorksheets();
                        unset($objPHPExcel_other);
                        unlink($xlsx_info['file_name']);
                        /*if($key==7){
                            die('here');
                        }*/
                    }
                }
                
                $this->createZip(FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName,FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'.zip',true);
                $content = 'Your file name is '.$result_multi_file->file_name.' successfully processed.';
                //You can check it on < a href="'.site_url().'">BSS</ a>
                if($result_multi_file->email!=""){
                    $this->sendEmail($result_multi_file->email,$content,'Spreading done');
                }
            }
        }
        $check_all_pdf_process = $this->input->post('check_all_pdf_process');
        /*if($newFolderName!="" && $this->input->post('isCompleteMultiAcc')){
            $targetPath = FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'/success/'.$fileName;
            copy(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName, $targetPath);
            chmod($targetPath,  0777);
            if($this->input->post('check_all_pdf_process')!=""){
                $this->createZip(FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName,FCPATH.'/assets/uploads/bulk_upload/'.$newFolderName.'/'.$newFolderName.'.zip',true);
            }
        }*/
        
        /*if($newFolderName!=""){
            if($xlsx_file_name==""){
                $bulkUploadArr =array();
                $bulkUploadArr['xlsx_file_name'] = $fileName;
                $this->bulk_upload->updateRecordByFolderName($newFolderName,$bulkUploadArr);
            }
        }*/
        
        $output['filename'] = $fileName;
        /**Add new Params*/
        $output['check_sum'] = $check_sum;
        $output['total_deposits'] = $total_deposits;
        $output['count_deposits'] = $count_deposits;
        $output['total_withdrawals'] = $total_withdrawals;
        $output['count_withdrawals'] = $count_withdrawals;
        if($this->input->post('multiple_account')){
            $output['multiple_account'] = true;
            $output['page_array'] = $pageArray;
            $output['newFolderName'] = $newFolderName;
        }else{
            $output['multiple_account'] = false;
        }
        $output['check_all_pdf_process'] = $check_all_pdf_process;
        /**End Params*/
        $output['success'] = true;
        echo json_encode($output);die;  
    }
    
    function sendEmail($emailAddress,$content,$subject) {
        //$smtpUser = getSiteOption('smtp_user',true);
        $this->email->from($smtpUser,'BSS');
        $this->email->to($emailAddress);
        $this->email->mailtype = $this->mailtype;
        $this->email->set_newline("\r\n");
        $this->email->subject($subject);
        $this->email->message($content);
        $this->email->send();
    }
    
    function addMultieAcctTxnTab($objPHPExcelMain,$objPHPExcel_other,$column_name,$acct_sn){
        $this->setBorderCell($objPHPExcelMain,$column_name."2:".$column_name."27");
        $this->cellStringAttr($objPHPExcelMain,$acct_sn,$column_name.'2','000000',true,'808080',true);
        $this->cellStringAttr($objPHPExcelMain,'MF',$column_name.'3');
        
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D4')->getValue(),$column_name.'4','','','','left',true);
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D5')->getValue(),$column_name.'5');
        
        $this->cellStringAttr($objPHPExcelMain,$account_type,$column_name.'7');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D9')->getValue(),$column_name.'9');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D11')->getValue(),$column_name.'11');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D12')->getValue(),$column_name.'12');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D17')->getValue(),$column_name.'17');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D20')->getValue(),$column_name.'20');
        
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D21')->getValue(),$column_name.'21');
        
        $this->cellStringAttr($objPHPExcelMain,$objPHPExcel_other->getActiveSheet()->getCell('D22')->getValue(),$column_name.'22');
        
        $this->cellStringAttr($objPHPExcelMain,'USA',$column_name.'23');
        return;
        
    }
    
    function cellStringAttr($objPHPExcel,$string,$cell='',$string_colr='',$bold=false,$bg_colr='',$align='left',$string_type=false){
        
        if($align=='left'){
            $style = array(
                'font'  => array(
                    'bold'  => $bold,
                    'color' => array('rgb' => $string_colr),
                    'size'  => 9,
                    'name' => 'Calibri'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                )
                
            );
        }
        
        if($align=='right'){
            $style = array(
                'font'  => array(
                    'bold'  => $bold,
                    'color' => array('rgb' => $string_colr),
                    'size'  => 9,
                    'name' => 'Calibri'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                )
                
            );
        }
        
        if($align=='center'){
            $style = array(
                'font'  => array(
                    'bold'  => $bold,
                    'color' => array('rgb' => $string_colr),
                    'size'  => 9,
                    'name' => 'Calibri'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
                
            );
        }
        
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style);
        if($string_type==false){
            $objPHPExcel->getActiveSheet()->SetCellValue($cell, $string);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $string, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        
        
        if($bg_colr!=""){
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->applyFromArray(array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => $bg_colr
                )
            ));
        }
    }
    
    function setBorderCell($objPHPExcel,$cell){
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );
    }
    
    function cellColor($objPHPExcel,$cells,$color){
        
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $color
            )
        ));
    }
    
    function mergeCell($objPHPExcel,$cells,$cell,$text){
        $style_header = array(
            'font'  => array(
                'bold'  => false,
                'size'  => 9,
                'name' => 'Calibri'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
            
        );
        $objPHPExcel->getActiveSheet()->mergeCells($cells);
        $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray($style_header);
        $objPHPExcel->getActiveSheet()->getCell($cell)->setValue($text);
    }
    
    function createExcel($id){
        ini_set("memory_limit", "2048M");
        set_time_limit(0);
        //if($this->input->post('tpl_history_id')!=""){
        $getMonthName = "";
        $tpl_history_id = $id;
        $histResult = $this->tpl_history->getSingleRecordById($tpl_history_id);
        /*echo"<pre>";
        print_r($histResult);
        echo"</pre>";
        die;*/
        if(count($histResult)==1){
            $uniqueId = $histResult->unique_id;
        }else{
            $uniqueId = "";
        }
        //if($histResult->original_pdf_file_name)
        //if($this->session->userdata('email')!='shubha.joshi@ollosoft.com'){
        if($histResult->upload_process=='manual'){
            $results = $this->summary_level_data->fetchSummaryLevelDataByIdAsc($tpl_history_id);
        }else{
            $results = $this->summary_level_data->fetchSummaryLevelData($tpl_history_id);
        }
        /*}else{
            $results = $this->summary_level_data->fetchSummaryLevelDataByIdAsc($tpl_history_id);
        }*/
        
        /*echo "<pre>";
        print_r($results);
        die;*/
        //$credit_array = creaditCategoryArray();
        //$debit_array = debitCategoryArray();
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->createSheet();
        $checkChangeMonth = 1;
        if(count($results)>0){
            
            $objPHPExcel->setActiveSheetIndex(0);
            
            $link_style_array = [
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size'  => 12
                )
            ];
            
            $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->applyFromArray($link_style_array);
            $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('1F497D');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            
            
            $objPHPExcel->getActiveSheet()->setTitle('Transaction Data');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DBID');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Unique ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Account#');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Txn id');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Description');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Check#');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Txn date');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Txn amount');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Currency');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Debit/credit');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Level 1');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Available balance');
            
            //$objPHPExcel->getActiveSheet()->SetCellValue('K1', $result->open_balance);
            
            $objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            
            
            $link_style_array = [
                'font'  => array(
                    'bold'  => true,
                    'size'  => 12
                )
            ];
            $objPHPExcel->getActiveSheet()->getStyle("A1:T1")->applyFromArray($link_style_array);
            $objPHPExcel->getActiveSheet()->setTitle('Summary Level Data');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Unique ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'account#');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'account_holder_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'account_type');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Name_of_bank');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'bank_address');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'bank_city');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'bank_state');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'bank_zip');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'current_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'start_date');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'end_date');
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'open_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'closing_balance');
            $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'total_deposits');
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'count_deposits');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'total_withdrawals');
            $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'count_withdrawals');
            //$objPHPExcel->getActiveSheet()->SetCellValue('AE1', 'transaction_all_level_spreading_done');
            $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'native_vs_non_native');
            $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'check_sum');
            //$objPHPExcel->getActiveSheet()->SetCellValue('AH1', 'summary_and_transaction_match');
            //$objPHPExcel->getActiveSheet()->SetCellValue('AI1', 'pages');
            $rowCount = 2;
            /*echo"<pre>";
            print_r($results);
            echo"</pre>";
            die;*/
            $i = 2;
            /*$crAmtArray = array();
            $drAmtArray = array();
            $totalCrAmtArray = array();
            $totalDrAmtArray = array();
            $excelOtherDetails = array('tab_name'=> 'Categories_Consolidated','tab_position'=>2);*/
            $objPHPExcel->createSheet();
            $fileSerialNum = 1;
            foreach($results as $key=>$result){
                /*echo $key."<br>";
                echo "<pre>";
                print_r($result);*/
                
                $objPHPExcel->setActiveSheetIndex(1);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $rowCount, $uniqueId );
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()), PHPExcel_Cell_DataType::TYPE_STRING );
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, openssl_decrypt(base64_decode($result->account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));
                
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $result->account_type);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $result->name_of_bank);
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $result->bank_address);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $result->bank_city);
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $result->bank_state);
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $result->bank_zip);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $result->current_balance);
                
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $result->start_date);
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $result->end_date);
                $open_balance = $result->open_balance;
                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $result->open_balance);
                $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $result->closing_balance);
                $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $result->total_deposits);
                $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $result->count_deposits);
                $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $result->total_withdrawals);
                $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $result->count_withdrawals);
                //$objPHPExcel->getActiveSheet()->SetCellValue('AE' . $rowCount, $result->check_sum==0 ? 'Yes' : 'No');
                $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, 'Native');
                $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $result->check_sum);
                //$objPHPExcel->getActiveSheet()->SetCellValue('AH' . $rowCount, $result->check_sum==0 ? 'Yes' : 'No');
                //$objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $result->pages);
                $rowCount = $rowCount+1;
                
                
                $file_no = $result->file_no;
                $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($tpl_history_id,$file_no);
                
                /**Set logic for mid month*/
                $split_start_date = $result->start_date;
                
                if (strpos($split_start_date, '/') !== false) {
                    $splitStartdate = explode("/", $split_start_date);
                    $splitStartMonthName = $splitStartdate[0];
                }else if(strpos($split_start_date, '-') !== false) {
                    $splitStartdate = explode("-", $split_start_date);
                    $splitStartMonthName = $splitStartdate[0];
                }
                
                $split_end_date = $result->end_date;
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
                /*$midMonth = false;
                if((int)$splitStartMonthName!=(int)$splitEndMonthName){
                    //die('here');
                    $midMonth = true;
                }*/
                if(count($customerTxns)>0){
                    
                    $open_balance = $result->open_balance;
                    
                    $objPHPExcel->setActiveSheetIndex(0);
                    foreach($customerTxns as $txn){
                        $txn_date = $txn->txn_date;
                        /*if($midMonth==false){
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
                                    $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$fileSerialNum,'tab_position'=>$fileSerialNum+2);
                                    $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                                    $fileSerialNum++;
                                    $crAmtArray = array();
                                    $drAmtArray = array();
                                    $getMonthName = $getTxnMonthName;
                                }
                                $objPHPExcel->setActiveSheetIndex(0);
                                
                            }
                        }else{
                            $catFile_no = $result->file_no;
                            if($checkChangeMonth!=$key+1){
                                echo $checkChangeMonth."<br>";
                                echo $key."</br>";
                                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$checkChangeMonth,'tab_position'=>$checkChangeMonth+2);
                                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                                $checkChangeMonth++;
                                $crAmtArray = array();
                                $drAmtArray = array();
                                $getMonthName = $getTxnMonthName;
                            }
                            $objPHPExcel->setActiveSheetIndex(0);
                        }*/
                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $txn->id);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $uniqueId);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $i, openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $i, '');
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $txn->description);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $txn->check_no);
                        /*$date = $txn->txn_date;
                        $date = DateTime::createFromFormat('m/d/Y',$date);
                        $date = PHPExcel_Shared_Date::PHPToExcel($date);*/
                        
                        //DateTime::createFromFormat('m/d/Y',$txn->txn_date);
                        //$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $date);
                        //$objPHPExcel->getActiveSheet()->getStyle('G' . $i)->getNumberFormat()->setFormatCode('mm/dd/yyyy');
                        //date code start
                        $date = $txn->txn_date; //date in m/d/Y
                        $date = str_replace("//","/",$date);
                        $countSlash = substr_count($date,"/");
                        $countDash = substr_count($date,"-");
                        //echo $date;
                        //die;
                        if($countSlash==2 || $countDash==2){
                            $date = new \DateTime($date); //for correction
                            $date->format('m/d/Y'); //Our format
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , PHPExcel_Shared_Date::PHPToExcel( $date )); //for setting date
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $i)->getNumberFormat()->setFormatCode( 'mm/dd/yyyy'); //Convert number to our format
                        //end
                        //$objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $txn->txn_date);
                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $i, $txn->txn_amt, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $i, $txn->currency);
                        //$txnLevel_1 = trim($txn->level_1);
                        //$txnLevel_2 = trim($txn->level_2);
                        
                        if($txn->type=='cr'){
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $i, 'Credit');
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $i, trim($txn->level_1));
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $i, number_format((float)$open_balance+$txn->txn_amt, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                            $open_balance = number_format((float)$open_balance+$txn->txn_amt, 2, '.', '');
                            /*if(in_array(strtolower($txnLevel_1), array_map('strtolower', $credit_array))){
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
                            }*/
                            /*if(in_array($txnLevel_2, $credit_array)){
                                
                            }*/
                            
                        }else{
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $i, 'Debit');
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $i, trim($txn->level_1));
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $i, number_format((float)$open_balance-$txn->txn_amt, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                            $open_balance = number_format((float)$open_balance-$txn->txn_amt, 2, '.', '');
                            //echo$txnLevel_1."<br/>";
                            /*if(in_array(strtolower($txnLevel_1), array_map('strtolower', $debit_array))){
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
                            }*/
                            
                            /*if(in_array($txnLevel_2, $debit_array)){
                                
                            }*/
                        }
                        $i++;
                    }
                    
                   
                }
                
            }
            
            /*if($midMonth==false){
                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$fileSerialNum,'tab_position'=>$fileSerialNum+2);
                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                $crAmtArray = array();
                $drAmtArray = array();
            }else{
                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$checkChangeMonth,'tab_position'=>$checkChangeMonth+2);
                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                $crAmtArray = array();
                $drAmtArray = array();
            }
            
            $excelOtherDetails = array('tab_name'=> 'Categories_Consolidated','tab_position'=>2);
            $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$totalCrAmtArray,$totalDrAmtArray);
            $objPHPExcel->setActiveSheetIndex(0);*/
        }
        
        $results = $this->summary_level_data->fetchSummaryLevelDataCategory($tpl_history_id);
        $credit_array = creaditCategoryArray();
        $debit_array = debitCategoryArray();
        $checkChangeMonth = 1;
        
        if(count($results)>0){
            $crAmtArray = array();
            $drAmtArray = array();
            $totalCrAmtArray = array();
            $totalDrAmtArray = array();
            $excelOtherDetails = array('tab_name'=> 'Categories_Consolidated','tab_position'=>2);
            $fileSerialNum = 1;
            $getChangeForMidMonth = array();
            $categoryTrigger = false;
            foreach($results as $key=>$result){
                /*echo"<pre>";
                print_r($result);
                echo"</pre>";
                die;*/
                if(empty($getChangeForMidMonth)){
                    array_push($getChangeForMidMonth,$result->start_date);
                }
                
                if(!in_array($result->start_date, $getChangeForMidMonth)){
                    array_push($getChangeForMidMonth,$result->start_date);
                    $categoryTrigger = true;
                }
                $file_no = $result->file_no;
                $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($tpl_history_id,$file_no);
                /*echo"<pre>";
                print_r($customerTxns);
                echo"</pre>";
                die;*/
                /**Set logic for mid month*/
                $split_start_date = $result->start_date;
                
                if (strpos($split_start_date, '/') !== false) {
                    $splitStartdate = explode("/", $split_start_date);
                    $splitStartMonthName = $splitStartdate[0];
                }else if(strpos($split_start_date, '-') !== false) {
                    $splitStartdate = explode("-", $split_start_date);
                    $splitStartMonthName = $splitStartdate[0];
                }
                
                $split_end_date = $result->end_date;
                if (strpos($split_end_date, '/') !== false) {
                    $splitEnddate = explode("/", $split_end_date);
                    $splitEndMonthName = $splitEnddate[0];
                }else if(strpos($split_end_date, '-') !== false) {
                    $splitEnddate = explode("-", $split_end_date);
                    $splitEndMonthName = $splitEnddate[0];
                }
                
                $midMonth = false;
                if((int)$splitStartMonthName!=(int)$splitEndMonthName){
                    //die('here');
                    $midMonth = true;
                }
                
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
                                    $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$fileSerialNum,'tab_position'=>$fileSerialNum+2);
                                    $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                                    $fileSerialNum++;
                                    $crAmtArray = array();
                                    $drAmtArray = array();
                                    $getMonthName = $getTxnMonthName;
                                }
                                $objPHPExcel->setActiveSheetIndex(0);
                                
                            }
                        }else{
                            $catFile_no = $result->file_no;
                            //if($checkChangeMonth!=$key+1){
                            if($categoryTrigger){
                                //echo $checkChangeMonth."<br>";
                                //echo $key."</br>";
                                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$checkChangeMonth,'tab_position'=>$checkChangeMonth+2);
                                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                                $checkChangeMonth++;
                                $crAmtArray = array();
                                $drAmtArray = array();
                                $categoryTrigger = false;
                                $getMonthName = $getTxnMonthName;
                            }
                            $objPHPExcel->setActiveSheetIndex(0);
                        }
                        
                        $date = $txn->txn_date; //date in m/d/Y
                        $date = str_replace("//","/",$date);
                        $countSlash = substr_count($date,"/");
                        $countDash = substr_count($date,"-");
                        //echo $date;
                        //die;
                        if($countSlash==2 || $countDash==2){
                            $date = new \DateTime($date); //for correction
                            $date->format('m/d/Y'); //Our format
                        }
                        
                        $txnLevel_1 = trim($txn->level_1);
                        if($txn->type=='cr'){
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
                            
                            
                        }else{
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
                            
                        }
                    }
                }
            }
            
            if($midMonth==false){
                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$fileSerialNum,'tab_position'=>$fileSerialNum+2);
                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                $crAmtArray = array();
                $drAmtArray = array();
            }else{
                $excelOtherDetails = array('tab_name'=> 'Categories_Mnth'.$checkChangeMonth,'tab_position'=>$checkChangeMonth+2);
                $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray);
                $crAmtArray = array();
                $drAmtArray = array();
            }
            /*echo"<pre>";
             print_r($totalDrAmtArray);
             die('here');*/
            $excelOtherDetails = array('tab_name'=> 'Categories_Consolidated','tab_position'=>2);
            $this->addExcelSheetTab($objPHPExcel,$excelOtherDetails,$totalCrAmtArray,$totalDrAmtArray);
            $objPHPExcel->setActiveSheetIndex(0);
        }
        //die('here');
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment;filename="'.$fileName.'" ');
        $fileName = $uniqueId.'_'.time().'.xlsx';
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //ob_get_clean();
        $objWriter->save(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        $this->load->helper('download');
        $data = file_get_contents(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        $name = $fileName;
        
        force_download($name,$data);
        unlink(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        
        
        
        
        // }
    }
    
    function addExcelSheetTab($objPHPExcel,$excelOtherDetails,$crAmtArray,$drAmtArray){
        $this->db->reconnect();
        ini_set("memory_limit", "2048M");
        set_time_limit(0);
        $tab_name = $excelOtherDetails['tab_name'];
        $tab_position = $excelOtherDetails['tab_position'];
        //print_r($excelOtherDetails);
        if($tab_position!=2){
            $objPHPExcel->createSheet();
        }
        if($tab_position==2){
            $objPHPExcel->createSheet();
            /*echo"<pre>";
            print_r($drAmtArray);
            die('here');*/
        }
        /*if($tab_position==3){
            echo "<pre>";
            print_r($drAmtArray);
            die('here');
        }*/
        
        $objPHPExcel->setActiveSheetIndex($tab_position);
        $objPHPExcel->getActiveSheet()->setTitle($tab_name);
        $link_style_array = [
            'font'  => array(
                'bold'  => true,
                'size'  => 12
            )
        ];
        $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->applyFromArray($link_style_array);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Credit - Categories');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Sales - Card');
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Sales - Non Card (UBER)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Sales - Non Card (Didi)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'Sales - Non Card (Rappi)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A6', 'Sales - Non Card (Sin Delantal)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A7', 'Sales - Non Card (Other)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A8', 'Cash Deposit');
        $objPHPExcel->getActiveSheet()->SetCellValue('A9', 'Refund/Reversals');
        $objPHPExcel->getActiveSheet()->SetCellValue('A10', 'Intra Account Transfer');
        $objPHPExcel->getActiveSheet()->SetCellValue('A11', 'NG Check');
        $objPHPExcel->getActiveSheet()->SetCellValue('A12', 'Loans');
        $objPHPExcel->getActiveSheet()->SetCellValue('A13', 'Investment Income');
        $objPHPExcel->getActiveSheet()->SetCellValue('A14', 'Insurance Claim');
        $objPHPExcel->getActiveSheet()->SetCellValue('A15', 'Miscellaneous Credits');
        $objPHPExcel->getActiveSheet()->SetCellValue('A16', 'Total');
        
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Count of Txn');
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', isset($crAmtArray['sales - card']['count']) ? $crAmtArray['sales - card']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', isset($crAmtArray['sales - non card (uber)']['count']) ? $crAmtArray['sales - non card (uber)']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B4', isset($crAmtArray['sales - non card (didi)']['count']) ? $crAmtArray['sales - non card (didi)']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', isset($crAmtArray['sales - non card (rappi)']['count']) ? $crAmtArray['sales - non card (rappi)']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B6', isset($crAmtArray['sales - non card (sin delantal)']['count']) ? $crAmtArray['sales - non card (sin delantal)']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B7', isset($crAmtArray['sales - non card (other)']['count']) ? $crAmtArray['sales - non card (other)']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B8', isset($crAmtArray['cash deposit']['count']) ? $crAmtArray['cash deposit']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B9', isset($crAmtArray['refund/reversals']['count']) ? $crAmtArray['refund/reversals']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B10', isset($crAmtArray['intra account transfer']['count']) ? $crAmtArray['intra account transfer']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B11', isset($crAmtArray['ng check']['count']) ? $crAmtArray['ng check']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B12', isset($crAmtArray['loans']['count']) ? $crAmtArray['loans']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B13', isset($crAmtArray['investment income']['count']) ? $crAmtArray['investment income']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B14', isset($crAmtArray['insurance claim']['count']) ? $crAmtArray['insurance claim']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B15', isset($crAmtArray['miscellaneous credits']['count']) ? $crAmtArray['miscellaneous credits']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B16', '=SUM(B02:B15)');
        
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Amount (in MXN)');
        $objPHPExcel->getActiveSheet()->SetCellValue('C2', isset($crAmtArray['sales - card']['amt']) ? $crAmtArray['sales - card']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C3', isset($crAmtArray['sales - non card (uber)']['amt']) ? $crAmtArray['sales - non card (uber)']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C4', isset($crAmtArray['sales - non card (didi)']['amt']) ? $crAmtArray['sales - non card (didi)']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', isset($crAmtArray['sales - non card (rappi)']['amt']) ? $crAmtArray['sales - non card (rappi)']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C6', isset($crAmtArray['sales - non card (sin delantal)']['amt']) ? $crAmtArray['sales - non card (sin delantal)']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C7', isset($crAmtArray['sales - non card (other)']['amt']) ? $crAmtArray['sales - non card (other)']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C8', isset($crAmtArray['cash deposit']['amt']) ? $crAmtArray['cash deposit']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C9', isset($crAmtArray['refund/reversals']['amt']) ? $crAmtArray['refund/reversals']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C10', isset($crAmtArray['intra account transfer']['amt']) ? $crAmtArray['intra account transfer']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C11', isset($crAmtArray['ng check']['amt']) ? $crAmtArray['ng check']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C12', isset($crAmtArray['loans']['amt']) ? $crAmtArray['loans']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C13', isset($crAmtArray['investment income']['amt']) ? $crAmtArray['investment income']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C14', isset($crAmtArray['insurance claim']['amt']) ? $crAmtArray['insurance claim']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C15', isset($crAmtArray['miscellaneous credits']['amt']) ? $crAmtArray['miscellaneous credits']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('C16', '=SUM(C02:C15)');
        
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Debit - Categories');
        $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Vendor Payments');
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', 'Salaries & Benefits');
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', 'Rent');
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Taxes');
        $objPHPExcel->getActiveSheet()->SetCellValue('D6', 'Insurance');
        $objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Cash Withdrawal');
        $objPHPExcel->getActiveSheet()->SetCellValue('D8', 'Card Processor Fees');
        $objPHPExcel->getActiveSheet()->SetCellValue('D9', 'Chargeback');
        $objPHPExcel->getActiveSheet()->SetCellValue('D10', 'Credit Card Payments');
        $objPHPExcel->getActiveSheet()->SetCellValue('D11', 'Loan Repayment/EMI - Lenders');
        $objPHPExcel->getActiveSheet()->SetCellValue('D12', 'Loan Repayment/EMI - Mortgage');
        $objPHPExcel->getActiveSheet()->SetCellValue('D13', 'Loan Repayment/EMI - Auto Finance');
        $objPHPExcel->getActiveSheet()->SetCellValue('D14', 'Intra Account Transfer');
        $objPHPExcel->getActiveSheet()->SetCellValue('D15', 'Fees - NG');
        $objPHPExcel->getActiveSheet()->SetCellValue('D16', 'Fees - Overdraft');
        $objPHPExcel->getActiveSheet()->SetCellValue('D17', 'Fees - Others');
        $objPHPExcel->getActiveSheet()->SetCellValue('D18', 'Investments');
        $objPHPExcel->getActiveSheet()->SetCellValue('D19', 'Deposited Check Return');
        $objPHPExcel->getActiveSheet()->SetCellValue('D20', 'Miscellaneous Debit');
        $objPHPExcel->getActiveSheet()->SetCellValue('D21', 'Travel Expenses - Airlines');
        $objPHPExcel->getActiveSheet()->SetCellValue('D22', 'Travel Expenses - Hotels');
        $objPHPExcel->getActiveSheet()->SetCellValue('D23', 'Travel Expenses - Car Rental');
        $objPHPExcel->getActiveSheet()->SetCellValue('D24', 'Travel Expenses - Others');
        $objPHPExcel->getActiveSheet()->SetCellValue('D25', 'Utilities - Telephone');
        $objPHPExcel->getActiveSheet()->SetCellValue('D26', 'Utilities - Internet');
        $objPHPExcel->getActiveSheet()->SetCellValue('D27', 'Utilities - TV');
        $objPHPExcel->getActiveSheet()->SetCellValue('D28', 'Utilities - Power');
        $objPHPExcel->getActiveSheet()->SetCellValue('D29', 'Utilities - Water');
        $objPHPExcel->getActiveSheet()->SetCellValue('D30', 'Utilities - Others');
        $objPHPExcel->getActiveSheet()->SetCellValue('D31', 'Total');
        
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Count of Txn');
        $objPHPExcel->getActiveSheet()->SetCellValue('E2', isset($drAmtArray['vendor payments']['count']) ? $drAmtArray['vendor payments']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E3', isset($drAmtArray['salaries & benefits']['count']) ? $drAmtArray['salaries & benefits']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E4', isset($drAmtArray['rent']['count']) ? $drAmtArray['rent']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', isset($drAmtArray['taxes']['count']) ? $drAmtArray['taxes']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E6', isset($drAmtArray['insurance']['count']) ? $drAmtArray['insurance']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E7', isset($drAmtArray['cash withdrawal']['count']) ? $drAmtArray['cash withdrawal']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E8', isset($drAmtArray['card processor fees']['count']) ? $drAmtArray['card processor fees']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E9', isset($drAmtArray['chargeback']['count']) ? $drAmtArray['chargeback']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E10', isset($drAmtArray['credit card payments']['count']) ? $drAmtArray['credit card payments']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E11', isset($drAmtArray['loan repayment/emi - lenders']['count']) ? $drAmtArray['loan repayment/emi - lenders']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E12', isset($drAmtArray['loan repayment/emi - mortgage']['count']) ? $drAmtArray['loan repayment/emi - mortgage']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E13', isset($drAmtArray['loan repayment/emi - auto finance']['count']) ? $drAmtArray['loan repayment/emi - auto finance']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E14', isset($drAmtArray['intra account transfer']['count']) ? $drAmtArray['intra account transfer']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E15', isset($drAmtArray['fees - ng']['count']) ? $drAmtArray['fees - ng']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E16', isset($drAmtArray['fees - overdraft']['count']) ? $drAmtArray['fees - overdraft']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E17', isset($drAmtArray['fees - others']['count']) ? $drAmtArray['fees - others']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E18', isset($drAmtArray['investments']['count']) ? $drAmtArray['investments']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E19', isset($drAmtArray['deposited check return']['count']) ? $drAmtArray['deposited check return']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E20', isset($drAmtArray['miscellaneous debit']['count']) ? $drAmtArray['miscellaneous debit']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E21', isset($drAmtArray['travel expenses - airlines']['count']) ? $drAmtArray['travel expenses - airlines']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E22', isset($drAmtArray['travel expenses - hotels']['count']) ? $drAmtArray['travel expenses - hotels']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E23', isset($drAmtArray['travel expenses - car rental']['count']) ? $drAmtArray['travel expenses - car rental']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E24', isset($drAmtArray['travel expenses - others']['count']) ? $drAmtArray['travel expenses - others']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E25', isset($drAmtArray['utilities - telephone']['count']) ? $drAmtArray['utilities - telephone']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E26', isset($drAmtArray['utilities - internet']['count']) ? $drAmtArray['utilities - internet']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E27', isset($drAmtArray['utilities - tv']['count']) ? $drAmtArray['utilities - tv']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E28', isset($drAmtArray['utilities - power']['count']) ? $drAmtArray['utilities - power']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E29', isset($drAmtArray['utilities - water']['count']) ? $drAmtArray['utilities - water']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E30', isset($drAmtArray['utilities - others']['count']) ? $drAmtArray['utilities - others']['count']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E31', '=SUM(E02:E30)');
        
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Amount (in MXN)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F2', isset($drAmtArray['vendor payments']['amt']) ? $drAmtArray['vendor payments']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F3', isset($drAmtArray['salaries & benefits']['amt']) ? $drAmtArray['salaries & benefits']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F4', isset($drAmtArray['rent']['amt']) ? $drAmtArray['rent']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', isset($drAmtArray['taxes']['amt']) ? $drAmtArray['taxes']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F6', isset($drAmtArray['insurance']['amt']) ? $drAmtArray['insurance']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F7', isset($drAmtArray['cash withdrawal']['amt']) ? $drAmtArray['cash withdrawal']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F8', isset($drAmtArray['card processor fees']['amt']) ? $drAmtArray['card processor fees']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F9', isset($drAmtArray['chargeback']['amt']) ? $drAmtArray['chargeback']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F10', isset($drAmtArray['credit card payments']['amt']) ? $drAmtArray['credit card payments']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F11', isset($drAmtArray['loan repayment/emi - lenders']['amt']) ? $drAmtArray['loan repayment/emi - lenders']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F12', isset($drAmtArray['loan repayment/emi - mortgage']['amt']) ? $drAmtArray['loan repayment/emi - mortgage']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F13', isset($drAmtArray['loan repayment/emi - auto finance']['amt']) ? $drAmtArray['loan repayment/emi - auto finance']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F14', isset($drAmtArray['intra account transfer']['amt']) ? $drAmtArray['intra account transfer']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F15', isset($drAmtArray['fees - ng']['amt']) ? $drAmtArray['fees - ng']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F16', isset($drAmtArray['fees - overdraft']['amt']) ? $drAmtArray['fees - overdraft']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F17', isset($drAmtArray['fees - others']['amt']) ? $drAmtArray['fees - others']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F18', isset($drAmtArray['investments']['amt']) ? $drAmtArray['investments']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F19', isset($drAmtArray['deposited check return']['amt']) ? $drAmtArray['deposited check return']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F20', isset($drAmtArray['miscellaneous debit']['amt']) ? $drAmtArray['miscellaneous debit']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F21', isset($drAmtArray['travel expenses - airlines']['amt']) ? $drAmtArray['travel expenses - airlines']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F22', isset($drAmtArray['travel expenses - hotels']['amt']) ? $drAmtArray['travel expenses - hotels']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F23', isset($drAmtArray['travel expenses - car rental']['amt']) ? $drAmtArray['travel expenses - car rental']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F24', isset($drAmtArray['travel expenses - others']['amt']) ? $drAmtArray['travel expenses - others']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F25', isset($drAmtArray['utilities - telephone']['amt']) ? $drAmtArray['utilities - telephone']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F26', isset($drAmtArray['utilities - internet']['amt']) ? $drAmtArray['utilities - internet']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F27', isset($drAmtArray['utilities - tv']['amt']) ? $drAmtArray['utilities - tv']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F28', isset($drAmtArray['utilities - power']['amt']) ? $drAmtArray['utilities - power']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F29', isset($drAmtArray['utilities - water']['amt']) ? $drAmtArray['utilities - water']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F30', isset($drAmtArray['utilities - others']['amt']) ? $drAmtArray['utilities - others']['amt']: '');
        $objPHPExcel->getActiveSheet()->SetCellValue('F31', '=SUM(F02:F30)');
    }
    
    function createExcelNew($id){
        //if($this->input->post('tpl_history_id')!=""){
        $tpl_history_id = $id;
        $results = $this->summary_level_data->fetchSummaryLevelData($tpl_history_id);
        //echo "<pre>";
        //print_r($results);
        //die;
        if(count($results)>0){
            
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->createSheet();
            
            // $objPHPExcel->setActiveSheetIndex(0);
            foreach(range('D','H') as $v){
                if($objPHPExcel->getActiveSheet()->getCell($v.'6')->getValue()==""){
                    $coulmName = $v;
                    break;
                }
            }
            $this->setBorderCell($objPHPExcel,$v."3:".$v."48");
            //else{
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
            
            $objPHPExcel->getActiveSheet()->setTitle('Summary Level Data');
            
            //$this->cellColor($objPHPExcel,'B2:AF2', 'F28A8C');
            
            $this->mergeCell($objPHPExcel,'C1:H1','C1','Account 1');
            $this->mergeCell($objPHPExcel,'I1:N1','I1','Account 2');
            $this->mergeCell($objPHPExcel,'O1:T1','O1','Account 3');
            $this->mergeCell($objPHPExcel,'U1:Z1','U1','Account 4');
            $this->mergeCell($objPHPExcel,'AA1:AF1','AA1','Account 5');
            
            
            
            $this->cellStringAttr($objPHPExcel,'Particulars','B2','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Start statement date','B3');
            $this->cellStringAttr($objPHPExcel,'End statement date','B4');
            $this->cellStringAttr($objPHPExcel,'Statement Period','B5');
            $this->cellStringAttr($objPHPExcel,'Currency Code','B6');
            $this->cellStringAttr($objPHPExcel,'Unit of Currency','B7');
            $this->cellStringAttr($objPHPExcel,'Number of months','B8','',false,'F3C6C5');
            $this->cellStringAttr($objPHPExcel,'Opening Balance','B10');
            $this->cellStringAttr($objPHPExcel,'Ending Balance','B11');
            $this->cellStringAttr($objPHPExcel,'Total $ Deposits','B12');
            $this->cellStringAttr($objPHPExcel,'Total # Deposits','B13');
            $this->cellStringAttr($objPHPExcel,'Total $ Withdrawals','B14');
            $this->cellStringAttr($objPHPExcel,'Total # Withdrawals','B15');
            $this->cellStringAttr($objPHPExcel,'Total Number of Check Returns','B16');
            $this->cellStringAttr($objPHPExcel,'Total # Inward Check Return','B17','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total $ Inward Check Return','B18','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total # Outward Check Return','B19','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Total $ Outward Check Return','B20','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Count ECS or EMI (Monthly)','B21','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Amount ECS or EMI (Monthly)','B22','',true,'D0DEB0');
            $this->cellStringAttr($objPHPExcel,'Minimum Balance','B23');
            $this->cellStringAttr($objPHPExcel,'Checksum','B24');
            
            $this->cellStringAttr($objPHPExcel,'Customers Concentration','B26','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Top Customer 1','B27','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Customer 2','B28','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Customer 3','B29','','','','center');
            $this->cellStringAttr($objPHPExcel,'Concentration %','B30','','','','center');
            
            
            $this->cellStringAttr($objPHPExcel,'Customers share in credits','B31','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Customer share 1','B32','','','','center');
            $this->cellStringAttr($objPHPExcel,'Customer share 2','B33','','','','center');
            $this->cellStringAttr($objPHPExcel,'Customer share 3','B34','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total credits Amount','B35','','','','center');
            
            $this->cellStringAttr($objPHPExcel,'Vendors Concentration','B36','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 1','B37','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 2','B38','','','','center');
            $this->cellStringAttr($objPHPExcel,'Top Vendor 3','B39','','','','center');
            $this->cellStringAttr($objPHPExcel,'Concentration %','B40','','','','center');
            
            $this->cellStringAttr($objPHPExcel,'Vendors share in debits','B41','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'Vendor share 1','B42','','','','center');
            $this->cellStringAttr($objPHPExcel,'Vendor share 2','B43','','','','center');
            $this->cellStringAttr($objPHPExcel,'Vendor share 3','B44','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total credits Amount','B45','','','','center');
            $this->cellStringAttr($objPHPExcel,'Total debits Amount','B46','','','','center');
            
            
            $this->cellStringAttr($objPHPExcel,'Credit card details','B47','4E21DF',true,'BEDBFA');
            $this->cellStringAttr($objPHPExcel,'AXP','B48','4E21DF',false,'F3C6C5','center');
            $this->cellStringAttr($objPHPExcel,'Other card','B49','4E21DF',false,'F3C6C5','center');
            
            
            
            $this->setBorderCell($objPHPExcel,"C2:H2");
            $this->setBorderCell($objPHPExcel,"B2:B49");
            $this->setBorderCell($objPHPExcel,"C3:C49");
            $coulmName = 'C';
            //}
            
            
            // $rowCount = 2;
            $styleArray = array(
                'font'  => array(
                    
                    'size'  => 9,
                    
                ));
            
            foreach($results as $result){
                $currency  =  $result->currency;
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $rowCount, $this->encryption->decrypt($result->account_number), PHPExcel_Cell_DataType::TYPE_STRING );
                //$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $this->encryption->decrypt($result->account_holder_name));
                $objPHPExcel->getActiveSheet()->SetCellValue('C3', $result->start_date);
                $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C4', $result->end_date);
                $objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
                //$open_balance = $result->open_balance;
                $objPHPExcel->getActiveSheet()->SetCellValue('C10', $result->open_balance);
                $objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C11', $result->closing_balance);
                $objPHPExcel->getActiveSheet()->getStyle('C11')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C12', $result->total_deposits);
                $objPHPExcel->getActiveSheet()->getStyle('C12')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C13', $result->count_deposits);
                $objPHPExcel->getActiveSheet()->getStyle('C13')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C14', $result->total_withdrawals);
                $objPHPExcel->getActiveSheet()->getStyle('C14')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('C15', $result->count_withdrawals);
                $objPHPExcel->getActiveSheet()->getStyle('C15')->applyFromArray($styleArray);
                //$objPHPExcel->getActiveSheet()->SetCellValue('AE' . $rowCount, $result->check_sum==0 ? 'Yes' : 'No');
                //$objPHPExcel->getActiveSheet()->SetCellValue('AF' . $rowCount, 'Native');
                $objPHPExcel->getActiveSheet()->SetCellValue('C24', $result->check_sum);
                $objPHPExcel->getActiveSheet()->getStyle('C24')->applyFromArray($styleArray);
                //$objPHPExcel->getActiveSheet()->SetCellValue('AH' . $rowCount, $result->check_sum==0 ? 'Yes' : 'No');
                //$objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $result->pages);
            }
            
            $customerTxns = $this->customer_txn_data->fetchCustomerTxnData($tpl_history_id);
            //echo"<pre>";
            //print_r($customerTxns);
            //die;
            if(count($customerTxns)>0){
                $objPHPExcel->setActiveSheetIndex(1);
                
                $link_style_array = [
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'FFFFFF'),
                        'size'  => 9
                    )
                ];
                $objPHPExcel->getActiveSheet()->getStyle("B29:K29")->applyFromArray($link_style_array);
                //$objPHPExcel->getActiveSheet()->getStyle('B29:K29')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('BEDBFA');
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                
                $this->setBorderCell($objPHPExcel,"A2:A11");
                $this->cellStringAttr($objPHPExcel,'Currency ->','A1','000000',true);
                $this->cellStringAttr($objPHPExcel,'Cutomer Demographic','A2','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Customer ID','A3');
                $this->cellStringAttr($objPHPExcel,'Case ID','A4');
                $this->cellStringAttr($objPHPExcel,'Business ID','A5');
                $this->cellStringAttr($objPHPExcel,'SE 10','A6');
                $this->cellStringAttr($objPHPExcel,'Container Type','A7');
                $this->cellStringAttr($objPHPExcel,'Data Source','A8');
                $this->cellStringAttr($objPHPExcel,'Spreading done on','A9');
                
                $this->cellStringAttr($objPHPExcel,'Version','A11','4E21DF',true,'BEDBFA','center');
                
                $this->setBorderCell($objPHPExcel,"B2:B11");
                $this->cellStringAttr($objPHPExcel,'Data Inputs','B2','4E21DF',true,'BEDBFA');
                
                //$file_name = $this->bulk_upload->getRecordByFolderName($newFolderName);
                //$this->cellStringAttr($objPHPExcel,pathinfo($file_name->file_name, PATHINFO_FILENAME),'B6');
                $this->cellStringAttr($objPHPExcel,'Bank','B7');
                $now = new DateTime();
                $now->setTimezone(new DateTimezone('Asia/Kolkata'));
                $now->format('Y-m-d H:i:s');
                $this->cellStringAttr($objPHPExcel,$now,'B9','','','','right');
                $this->mergeCell($objPHPExcel,'A10:B10','A10','Application ID');
                $this->cellStringAttr($objPHPExcel,'Application ID','A10','','','','left');
                $this->cellStringAttr($objPHPExcel,'V8','B11','4E21DF',true,'BEDBFA','center');
                
                
                $this->setBorderCell($objPHPExcel,"C2:C27");
                $this->cellStringAttr($objPHPExcel,'Country ->','C1','000000',true);
                $this->cellStringAttr($objPHPExcel,'General information','C2','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Business Unit','C3');
                $this->cellStringAttr($objPHPExcel,'Account Number','C4');
                $this->cellStringAttr($objPHPExcel,'Account Holder Name','C5');
                $this->cellStringAttr($objPHPExcel,'Secondary Account Holder Name','C6');
                $this->cellStringAttr($objPHPExcel,'Account Type','C7');
                $this->cellStringAttr($objPHPExcel,'Account Ownership','C8','',false,'F9F907');
                $this->cellStringAttr($objPHPExcel,'Bank Name','C9');
                $this->cellStringAttr($objPHPExcel,'Routing No.','C10','',false,'C5D9BF');
                $this->cellStringAttr($objPHPExcel,'Current Balance','C11');
                $this->cellStringAttr($objPHPExcel,'As of Date','C12');
                $this->cellStringAttr($objPHPExcel,'Tax Payment Indicator','C13','',false,'C5D9BF');
                $this->cellStringAttr($objPHPExcel,'Drawing Power','C14','',false,'C5D9BF');
                $this->cellStringAttr($objPHPExcel,'Total count of over utilization','C15','',false,'C5D9BF');
                $this->cellStringAttr($objPHPExcel,'Interest Servicing Days','C16','',false,'C5D9BF');
                $this->cellStringAttr($objPHPExcel,'Address line 1','C17');
                $this->cellStringAttr($objPHPExcel,'Address line 2','C18');
                $this->cellStringAttr($objPHPExcel,'Address line 3','C19');
                $this->cellStringAttr($objPHPExcel,'City','C20');
                $this->cellStringAttr($objPHPExcel,'State','C21');
                $this->cellStringAttr($objPHPExcel,'Zip','C22');
                $this->cellStringAttr($objPHPExcel,'Country','C23');
                $this->cellStringAttr($objPHPExcel,'Country Code','C24');
                
                $this->setBorderCell($objPHPExcel,"D2:D27");
                $this->cellStringAttr($objPHPExcel,'Account 1','D2','000000',true,'808080');
                $this->cellStringAttr($objPHPExcel,'MF','D3');
                
                
                
                $this->cellStringAttr($objPHPExcel,'USA','D23');
                
                
                $this->setBorderCell($objPHPExcel,"B29:K29");
                
                /*Test*/
                /*$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                 $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);*/
                
                /*Test*/
                
                $objPHPExcel->getActiveSheet()->setTitle('Customer-Transaction Data');
                $this->cellStringAttr($objPHPExcel,'Account Number','B29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Transaction ID','C29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Transaction date','D29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Description','E29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Check number','F29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Transaction Amount','G29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Transaction Currency code','H29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Posted order','I29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Available balance','J29','4E21DF',true,'BEDBFA');
                $this->cellStringAttr($objPHPExcel,'Credit or Debit','K29','4E21DF',true,'BEDBFA');
                
                //$objPHPExcel->getActiveSheet()->SetCellValue('K29', number_format(str_replace(array("$",","), '', $begining_balance),2));
                $end_of_balance = number_format(str_replace(array("$",","), '', $begining_balance),2);
                
                
                $objPHPExcel->getActiveSheet()->SetCellValue('N1', $result->open_balance);
                $open_balance = $result->open_balance;
                $i = 30;
                $styleArray = array(
                    'font'  => array(
                        
                        'size'  => 9,
                        
                    ));
                
                foreach($customerTxns as $txn){
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $txn->description);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $i)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $txn->check_no);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $i)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $txn->txn_date);
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $i)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $txn->txn_amt, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $i)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $i, $currency);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $i)->applyFromArray($styleArray);
                    
                    if($txn->type=='cr'){
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $i, 'Credit');
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $i)->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $i, $open_balance+$txn->txn_amt, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i)->applyFromArray($styleArray);
                        $open_balance = $open_balance+$txn->txn_amt;
                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $i, 'Debit');
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $i)->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $i, $open_balance-$txn->txn_amt, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i)->applyFromArray($styleArray);
                        $open_balance = $open_balance-$txn->txn_amt;
                    }
                    $i++;
                }
                
                $results = $this->summary_level_data->fetchSummaryLevelData($tpl_history_id);
                $styleArray = array(
                    'font'  => array(
                        
                        'size'  => 9,
                        
                    ));
                foreach($results as $result){
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D4', openssl_decrypt(base64_decode($result->account_number), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));
                    $objPHPExcel->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', openssl_decrypt(base64_decode($result->account_holder_name), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()));
                    $objPHPExcel->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D7', $result->account_type);
                    $objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D9', $result->name_of_bank);
                    $objPHPExcel->getActiveSheet()->getStyle('D9')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D17', $result->bank_address);
                    $objPHPExcel->getActiveSheet()->getStyle('D17')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D20', $result->bank_city);
                    $objPHPExcel->getActiveSheet()->getStyle('D20')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D21', $result->bank_state);
                    $objPHPExcel->getActiveSheet()->getStyle('D21')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D22', $result->bank_zip);
                    $objPHPExcel->getActiveSheet()->getStyle('D22')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D11', $result->current_balance);
                    $objPHPExcel->getActiveSheet()->getStyle('D11')->applyFromArray($styleArray);
                    
                }
                
                
            }
            
        }
        
        
        
        
        
        
        
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment;filename="'.$fileName.'" ');
        $fileName = 'test'.'_'.time().'.xlsx';
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //ob_get_clean();
        $objWriter->save(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        $this->load->helper('download');
        $data = file_get_contents(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        $name = $fileName;
        
        force_download($name,$data);
        unlink(FCPATH.'assets/uploads/bank_statement_excel/'.$fileName);
        
        
        
        
        // }
    }
   
    
    

}