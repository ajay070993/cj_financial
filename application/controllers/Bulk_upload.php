<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bulk_upload extends CI_Controller {
    private $storageFolder;
    private $bulkSpreadResult;
    private $newFolderName;
    private $checkAllPdfProcess="";
    private $snFile;
    private $zipFileName;
    private $history_id;
    private $accArray=array();
    private $queue_no=0;
    
    function __construct() {
        Parent::__construct();
        if(!$this->session->userdata('email')){
            $this->session->set_userdata(array('user_id' => 3,'email' => 'shubha.joshi@ollosoft.com','username' => 'shubha', 'type' => 1, 'user_role' => 1));
        }
        $this->common_model->checkCjXtractUser();
        $this->load->model('bank_statement_model', 'bank_statement');
        $this->load->model('banks_model', 'banks');
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Tpl_content_model', 'tpl_content');
        $this->load->model('Bulk_upload_model', 'bulk_upload');
        $this->load->model('bank_address_model', 'bank_address');
        $this->load->model('Tpl_case_error_log', 'case_error_log');
        $this->load->model('Bank_summary_level_data', 'summary_level_data');
        $this->session->set_userdata(array('type_of_upload'=>2));
    }  
  
    function index() { 
        set_time_limit(0);
        if (!file_exists('./assets/uploads/bulk_upload')) {
            mkdir('./assets/uploads/bulk_upload', 0777, true);
            chmod('./assets/uploads/bulk_upload',  0777);
        }
        $date = date('d_m_Y_H_i_s');
        $directory = './assets/uploads/bulk_upload/'.$date; 
        mkdir($directory, 0777, true);
        chmod($directory,  0777);
        //die('here');
        //print_r($_FILES);
        //print_r($_REQUEST);
        $header = apache_request_headers();
        foreach ($header as $headers => $value) {
            //echo "$headers: $value <br />\n";
            if($headers=='Authorization'){
                //echo $headers;echo $value;die('here');
                if($value=='Basic YXBpa2V5OjEyMzRhYmNk'){
                    
                    $uploadfile = $_FILES['upload_file']['name'];
                    if(move_uploaded_file($_FILES['upload_file']['tmp_name'], $directory."/".$uploadfile))
                    {
                        $bulkUpload =array();
                        $bulkUpload['hashkey'] = $_REQUEST['hashkey'];
                        $bulkUpload['file_name'] = $uploadfile;
                        $bulkUpload['folder_name'] = $date;
                        $bulkUpload['status'] = '0';
                        $bulkUpload['created_on'] = date("Y-m-d h:i:sa");
                        $this->bulk_upload->addNewRecords($bulkUpload);
                        echo "The file has been uploaded successfully";
                    }
                    else
                    {
                        echo "There was an error uploading the file";
                    }
                }else{
                    echo "Invalid Token";die;
                }
            }
        }
       
    } 
    
    function download(){
        $header = apache_request_headers();
        foreach ($header as $headers => $value) { 
            if($headers=='Authorization'){
                if($value=='Basic YXBpa2V5OjEyMzRhYmNk'){
                    $hashkey = $_REQUEST['hashkey'];
                    $result = $this->bulk_upload->getRecordByHashkey($hashkey);
                    if($result){
                        echo json_encode($result);
                    }else{
                        echo json_encode($result);
                    }
                }else{
                    echo "301";die;
                }
            }
        }
    }
    
    function createZip($source, $destination,$flag = '')
    {
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
    
    function bulkUploadSpread($id) {
        //$this->createZip('./assets/uploads/bulk_upload/07_02_2020_10_59_35/07_02_2020_10_59_35','./assets/uploads/bulk_upload/07_02_2020_10_59_35/07_02_2020_10_59_35.zip',true);
        //die('here');
        $summary_rec = $this->summary_level_data->getSummaryRecordByHistoryId($id);
        if($summary_rec){
            echo "Extraction process already done.";
        }
        else{
            $countSpreadProgress = $this->bulk_upload->checkSpreadInProgress();
            if($countSpreadProgress==0){
                // $result = $this->bulk_upload->getLastUploadedFile();
                $result = $this->bulk_upload->getLatestFileForExtractionByHistoryId($id);
                /*echo"<pre>";
                print_r($result);
                die('here');*/
                if($result){
                    $lastSpreadId = $result->id;
                    $storageFolder = './assets/uploads/bulk_upload/'.$result->folder_name;
                    $this->storageFolder = $storageFolder;
                    $bulkSpreadResult = './assets/uploads/bulk_upload/'.$result->folder_name.'/'.$result->folder_name;
                    $this->bulkSpreadResult = $bulkSpreadResult;
                    $this->newFolderName = $result->folder_name;
                    $this->zipFileName = $result->file_name;
                    $this->history_id = $result->history_id;
                    if (!file_exists($bulkSpreadResult) && !is_dir($bulkSpreadResult)) {
                        mkdir($bulkSpreadResult, 0777, true);
                        chmod($bulkSpreadResult,  0777);
                        mkdir($bulkSpreadResult.'/success', 0777, true);
                        chmod($bulkSpreadResult,  0777);
                        mkdir($bulkSpreadResult.'/failed', 0777, true);
                        chmod($bulkSpreadResult,  0777);
                        $fp = fopen($bulkSpreadResult.'/log.txt', 'w');
                        fclose($fp);      
                    }
                    /*echo $result->folder_name;
                    print_r($result);*/
                    //die('here');
                    /**Call Bank_statement controllers*/
                    /*Update status to 1.Its indicate process is in progress*/
                    $uploadArray =array();
                    $uploadArray['status'] = '2';
                    $this->bulk_upload->updateStatusSpreadFile($lastSpreadId,$uploadArray);
                    
                    $file = './assets/uploads/bulk_upload/'.$result->folder_name.'/'.$result->file_name;
                    $path = './assets/uploads/bulk_upload/'.$result->folder_name.'/';
                    $zip = new ZipArchive;
                    $res = $zip->open($file);
                    if ($res === TRUE) {
                        // extract it to the path we determined above
                        $zip->extractTo($path);
                        $zip->close();
                        //echo "WOOT! $file extracted to $path"."<br/>";
                    }
                    $files = scandir($path);
                    
                    $countPdfExt = 0; 
                    foreach($files as $file){
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if($ext == 'pdf' or $ext == 'PDF'){
                            $countPdfExt++;
                        }
                    }
                    /*echo $countPdfExt;
                    echo"<pre>";
                    print_r($files);
                    die('here');*/
                    foreach($files as $file){
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if($ext == 'pdf' or $ext == 'PDF'){
                            $this->db->where('folder_name',$result->folder_name);
                            $this->db->set('input_cnt','input_cnt+1',FALSE);
                            $this->db->update('tbl_bulk_upload');
                        }
                    }
                    
                    $count = 0;
                    foreach($files as $file){
                        
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if($ext == 'pdf' or $ext == 'PDF'){
                            $this->snFile = $count;
                            if(preg_replace('/\s+/', '_', $file)){
                                $new_name = preg_replace('/\s+/', '_', $file);
                                if(file_exists($new_name)) {
                                    echo "Error While Renaming $path.$file" ;
                                }else{
                                    if(!rename( $path.$file, $path.$new_name))
                                    {
                                        echo "A File With The Same Name Already Exists" ;
                                    }
                                }
                            }
                            $count++;
                            /*if($countPdfExt==$count){
                                $this->checkAllPdfProcess = true;
                            }*/
                            //$this->db->where('folder_name',$result->folder_name);
                            //$this->db->set('input_cnt','input_cnt+1',FALSE);
                            //$this->db->update('tbl_bulk_upload');
                            $this->detectTplConvertBankStatement($new_name,$countPdfExt,$count);
                            //break;
                        }
                    }
                    //$this->createZip($this->bulkSpreadResult,$this->storageFolder.'/'.$this->newFolderName.'.zip',true);
                    $uploadArray =array();
                    $uploadArray['status'] = '2';
                    $this->bulk_upload->updateStatusSpreadFile($lastSpreadId,$uploadArray);
                    return true;  
                }
            }
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
    
    function detectTplConvertBankStatement($upload_pdf_file,$countPdfExt,$count) {
        $isMutiple = false;
        set_time_limit(0);
        error_reporting(0);
        ini_set("memory_limit", "2048M");
        
        $output['page_title'] = 'Convert File';
        $output['message']    = '';
        $extractData = array();
        
        $input = array();
        
        if($upload_pdf_file)
        {
            $directory = './assets/uploads/bank_statement';
            @mkdir($directory, 0777);
            @chmod($directory,  0777);
            if (copy('./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$upload_pdf_file, $directory.'/'.$upload_pdf_file))
            {
                //$image_data = $this->upload->data();
                //$file_name = $_FILES['image_name']['name'];
                $file_name = $upload_pdf_file;
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
                    $jobID = $response['id'];
                    $target_id = $this->recursiveFunctionTargetId($jobID);
                } 
                
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
                    $content = file_get_contents($actualFilePath);
                    #echo$upload_pdf_file;die('herexs');
                    
                    
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
                        $this->queue_no++;
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(183,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        //die('here');
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
                        
                        $this->queue_no++;
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(182,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        
                    }
                    /*End Split file*/
                    /*CitiBanamex Old Multiple Accounts*/
                    /*$splitPageNumArray = array();
                    $breakarray = array();
                    $startInit = 0;
                    if(preg_match('/RESUMEN GENERAL/', $content, $matches)){
                        $second_page_start = $matches[1][0];
                        $handle = @fopen ($actualFilePath, "r");
                        while ($line = fgets($handle)) {
                            $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
                            if($startInit==1){
                                $check = true;
                                $startInit = 0;
                            }
                            
                            if(in_array('RESUMEN GENERAL', $lineBreakArray)){
                                $startInit = 1;
                            }
                            
                            if(in_array('Domiciliación Banamex', $lineBreakArray)){
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
                            
                            
                            $pregMatchString = '/SALDO MINIMO REQUERIDO/';
                            //$pregMatchString1 = '/IN CASE OF ERRORS OR QUESTIONS ABOUT YOUR ELECTRONIC FUNDS TRANSFERS/';
                            //$pregMatchString1 = '/Ending Balance\s*+0/';
                            
                            
                            if (preg_match($pregMatchString, $line, $matches)) {
                                $q++;
                                $pregMatchString= '/DETALLE DE OPERACIONES/'.$q;
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
                        
                        //$txtSplitFileName = array("a.txt","b.txt");
                        //print_r($txtSplitFileName);
                        //die;
                        $this->directConvertBankStatement(178,$txtSplitFileName[0],$txtSplitFileName,"",$_FILES['image_name']['name'],$splitPageNumArray);
                        $txtFilename = end($txtSplitFileName);
                        
                        
                    }*/
                    /*end*/
                    
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
                        //echo $txtSplitFileName[0]."First ";
                        $this->queue_no++;
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(187,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        //$txtFilename = end($txtSplitFileName);
                        //echo end($txtSplitFileName)."Second  ";
                        flush();
                        //die('here');
                    }
                    
                    /* Banrote multiple account*/
                    $singleAcc = false;
                    $banrote = false;
                    $accStart = false;
                    $splitPageNumArray = array();
                    $startInit = 0;
                    if(preg_match('/RESUMEN INTEGRAL/', $content, $matches)){
                        $banrote = true;
                        $second_page_start = $matches[1][0];
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
                            
                            $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
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
                        //echo"<pre>";
                        //print_r($txtSplitFileName);
                        //die('here');
                        $this->queue_no++;
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(179,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        //die('here');
                        //$txtFilename = $txtSplitFileName[1];
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
                        
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(1,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        //die('here');
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
                        foreach($txtSplitFileName as $value){
                            $this->directConvertBankStatement(58,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                        }
                        $txtSplitFileName = array();
                        $isMutiple = true;
                        //die('here');
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
                         foreach($txtSplitFileName as $value){
                             $this->directConvertBankStatement(30,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                         }
                         $txtSplitFileName = array();
                         $isMutiple = true;
                         //die('here');
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
                         foreach($txtSplitFileName as $value){
                             $this->directConvertBankStatement(2,$value,"","",$_FILES['image_name']['name'],$splitPageNumArray);
                         }
                         $txtSplitFileName = array();
                         $isMutiple = true;
                         //die('here');
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
                        /*echo "<pre>";
                        print_r($bankMatches);
                        echo "</pre>";
                        die('here');*/
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
                            //echo max($bankCountInt);
                            //die('Here');
                            
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
                                
                                
                                copy($this->storageFolder.'/'.$upload_pdf_file, $this->bulkSpreadResult.'/success/'.$upload_pdf_file);
                                $data = 'PDF file successfully processed with name '.$upload_pdf_file.PHP_EOL;
                                $fp = fopen($this->bulkSpreadResult.'/log.txt', 'a');
                                fwrite($fp, $data);
                                fclose($file);
                                
                                $output['callBackFunction'] = 'createXLSBankStatement';
                                $extractData['original_pdf_file_name'] = $upload_pdf_file;
                                $extractData['upload_pdf_file'] = $file_name;
                                $output['extractData'] = $extractData;
                                $output['bank_data_val'] = $string_record;
                                //$output['string_record'] = $string_record;
                                $output['textFileName'] = $txtFilename;
                                $output['multiple_account'] = false;
                                //$this->queue_no = 0;
                                $message = '';
                                $success = true;
                            }else{
                                $this->db->where('folder_name',$this->newFolderName);
                                $this->db->set('output_cnt','output_cnt+1',FALSE);
                                $this->db->update('tbl_bulk_upload');
                                copy($this->storageFolder.'/'.$upload_pdf_file, $this->bulkSpreadResult.'/failed/'.$upload_pdf_file);
                                $data = 'PDF file not detected any template.Pdf File name '.$upload_pdf_file.PHP_EOL;
                                $fp = fopen($this->bulkSpreadResult.'/log.txt', 'a');
                                fwrite($fp, $data);
                                fclose($file);
                                if($this->checkAllPdfProcess){
                                    $this->createZip('./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName,'./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName.'.zip',true);
                                }
                                
                                $cntHistoryId = $this->summary_level_data->getCountHistoryId($this->history_id);
                                $file_no = $cntHistoryId+1;
                                $update_case_error_log = array();
                                $update_case_error_log['history_id'] = $this->history_id;
                                $update_case_error_log['file_no'] = $file_no;
                                $update_case_error_log['tpl_not_found'] =0;
                                $this->case_error_log->addRecord($update_case_error_log);
                                return true;
                            }
                            
                        }else{
                            $this->db->where('folder_name',$this->newFolderName);
                            $this->db->set('output_cnt','output_cnt+1',FALSE);
                            $this->db->update('tbl_bulk_upload');
                            copy($this->storageFolder.'/'.$upload_pdf_file, $this->bulkSpreadResult.'/failed/'.$upload_pdf_file);
                            $data = 'PDF file not detected any template.Pdf File name '.$upload_pdf_file.PHP_EOL;
                            $fp = fopen($this->bulkSpreadResult.'/log.txt', 'a');
                            fwrite($fp, $data);
                            fclose($file);
                            if($this->checkAllPdfProcess){
                                $this->createZip('./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName,'./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName.'.zip',true);
                            }
                            $cntHistoryId = $this->summary_level_data->getCountHistoryId($this->history_id);
                            $file_no = $cntHistoryId+1;
                            $update_case_error_log = array();
                            $update_case_error_log['history_id'] = $this->history_id;
                            $update_case_error_log['file_no'] = $file_no;
                            $update_case_error_log['tpl_not_found'] =0;
                            $this->case_error_log->addRecord($update_case_error_log);
                            return true;
                        }
                        
                    }else{
                        $this->db->where('folder_name',$this->newFolderName);
                        $this->db->set('output_cnt','output_cnt+1',FALSE);
                        $this->db->update('tbl_bulk_upload');
                        copy($this->storageFolder.'/'.$upload_pdf_file, $this->bulkSpreadResult.'/failed/'.$upload_pdf_file);
                        $data = 'PDF file not detected any template.Pdf File name '.$upload_pdf_file.PHP_EOL;
                        $fp = fopen($this->bulkSpreadResult.'/log.txt', 'a');
                        fwrite($fp, $data);
                        fclose($file);
                        if($this->checkAllPdfProcess){
                            $this->createZip('./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName,'./assets/uploads/bulk_upload/'.$this->newFolderName.'/'.$this->newFolderName.'.zip',true);
                        }
                        $cntHistoryId = $this->summary_level_data->getCountHistoryId($this->history_id);
                        $file_no = $cntHistoryId+1;
                        $update_case_error_log = array();
                        $update_case_error_log['history_id'] = $this->history_id;
                        $update_case_error_log['file_no'] = $file_no;
                        $update_case_error_log['tpl_not_found'] =0;
                        $this->case_error_log->addRecord($update_case_error_log);
                        return true;
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
        if($isMutiple==false){
            $output['message'] = $message;
            $output['success'] = $success;
            $output['bulk_upload'] = True;
            $output['newFolderName'] = $this->newFolderName;
            $output['zipFileName'] = $this->zipFileName;
            $output['history_id'] = $this->history_id;
            $output['accType'] = 'single';
            //echo json_encode($output);die;
            //}
            /*if($this->snFile==0){
                //echo json_encode($output);die;
            }
            
            if($this->snFile==1){
                echo json_encode($output);die;
            }*/
            $output['allBanks'] = $this->banks->getAllBanksRecords();
            $data = array();
            $data['bulk_upload'] = True;
            //$countPdfExt,$count
            $output['countPdfExt'] = $countPdfExt;
            $output['count'] = $count;
            $output['check_all_pdf_process'] = $this->checkAllPdfProcess;
            $output['account_array'] = $this->accArray;
            $output['multiple_account'] = false;
            $output['queue_no'] = $this->queue_no;
            $data['output'] = $output;
            $this->load->view('spreading',$data);
        }
        
    }
    
    function directConvertBankStatement($bank_id,$txtFilename,$txtSplitFileName,$uploadedXlsFileName="",$original_pdf_file_name="",$splitPageNumArray="") {
        $output['page_title'] = $txtFilename;
        $output['message']    = '';
        
        $input = array();
        
        $string_record = $this->bank_statement->getSingleRecordByBankId($bank_id);
        $extractData = $this->common_model->getExtractDataRegx($string_record,$txtFilename,$bank_id);
        
        copy($this->storageFolder.'/'.$original_pdf_file_name, $this->bulkSpreadResult.'/success/'.$original_pdf_file_name);
        $data = 'PDF file successfully processed with name '.$original_pdf_file_name.PHP_EOL;
        $fp = fopen($this->bulkSpreadResult.'/log.txt', 'a');
        fwrite($fp, $data);
        fclose($file);
        
        //$string_record = $this->bank_statement->getRecordWithoutRegex(1);
        //$txtSplitFileName
        $output['callBackFunction'] = 'createXLSBankStatement';
        $output['txtSplitFileName'] = $txtSplitFileName;
        $output['multiple_account'] = true;
        $output['multiple_process'] = false;
        $extractData['upload_pdf_file'] = '';
        $output['newFolderName'] = $this->newFolderName;
        $output['zipFileName'] = $this->zipFileName;
        $output['history_id'] = $this->history_id;
        $extractData['original_pdf_file_name'] = $original_pdf_file_name;
        $extractData['split_page_num_array'] = $splitPageNumArray;
        $output['extractData'] = $extractData;
        $output['bank_data_val'] = $string_record;
        //$output['string_record'] = $string_record;
        $output['textFileName'] = $txtFilename;
        $success = 'success';
        $res = $this->banks->getBankName($bank_id);
        $output['bank_name'] = $res->bank_name;
        $output['bank_id'] = $bank_id;
        
        if($this->session->userdata('type')==2){
            $output['accType'] = 'single';
        }else{
            if($uploadedXlsFileName!=""){
                $output['uploadedXlsFileName'] = $uploadedXlsFileName;
            }else{
                $output['uploadedXlsFileName'] = '';
            }
        }
        $output['check_all_pdf_process'] = $this->checkAllPdfProcess;
        //$output['message'] = $message;
        $output['success'] = $success;
        //echo json_encode($output);die;
        
        $output['allBanks'] = $this->banks->getAllBanksRecords();
        $data = array();
        $data['bulk_upload'] = True;
        $output['queue_no'] = $this->queue_no;
        $data['output'] = $output;
        /*echo"<pre>";
        print_r($output['txtSplitFileName']);
        echo"</pre>";*/
        //die('here');
        $this->load->view('spreading',$data);
        flush();
    }
    
    /*function getExtractDataRegx($string_record,$txtFilename,$bank_id){
        
        $extractData = array();
        $realPath = FCPATH.'assets/uploads/bank_statement/';
        $actualFilePath = $realPath.''.$txtFilename;
        
        if($string_record->bank_type==2){
            $content = file_get_contents($actualFilePath);
            if($bank_id==188){
                $changeDateArray = array("-ENE-"=>"-01-","-FEB-"=>"-02-","-MAR-"=>"-03-","-MZO-"=>"-03-","-ABR-"=>"-04-",
                    "-MAY-"=>"-05-","-JUN-"=>"-06-","-JUL-"=>"-07-","-AGO-"=>"-08-","-SEP-"=>"-09-","-OCT-"=>"-10-","-NOV-"=>"-11-","-DIC-"=>"-12-","-Ene-"=>"-01-","-Feb-"=>"-02-","-Mar-"=>"-03-",
                    "-Mzo-"=>"-03-","-Abr-"=>"-04-","-May-"=>"-05-","-Jun-"=>"-06-","-Jul-"=>"-07-","-Ago-"=>"-08-","-Sep-"=>"-09-","-Oct-"=>"-10-","-Nov-"=>"-11-","-Dic-"=>"-12-"
                );
            }elseif($bank_id==177){
                $changeDateArray = array("/ENE"=>"/01","/FEB"=>"/02","/MAR"=>"/03","/MZO"=>"/03","/ABR"=>"/04","/MAY"=>"/05","/JUN"=>"/06","/JUL"=>"/07","/AGO"=>"/08",
                    "/SEP"=>"/09","/OCT"=>"/10","/NOV"=>"/11","/DIC"=>"/12"
                );
            }elseif($bank_id==184){
                $changeDateArray = array("-Ene-"=>"-01-","-Feb-"=>"-02-","-Mar-"=>"-03-",
                    "-Mzo-"=>"-03-","-Abr-"=>"-04-","-May-"=>"-05-","-Jun-"=>"-06-","-Jul-"=>"-07-","-Ago-"=>"-08-","-Sep-"=>"-09-","-Oct-"=>"-10-","-Nov-"=>"-11-","-Dic-"=>"-12-"
                );
            }elseif($bank_id==185){
                $changeDateArray = array(
                    "ENE "=>"01/","FEB "=>"02/","MAR "=>"03/","MZO "=>"03/","ABR "=>"04/","MAY "=>"05/","JUN "=>"06/","JUL "=>"07/","AGO "=>"08/","SEP "=>"09/",
                    "OCT "=>"10/","NOV "=>"11/","DIC "=>"12/","$ "=>"","P08"=>"PAGO"
                );
            }elseif($bank_id==178){
                $changeDateArray = array(
                    " ENE  "=>"/01 "," FEB  "=>"/02 ", " MAR  "=>"/03 ", " MZO  "=>"/03 "," ABR  "=>"/04 "," MAY  "=>"/05 ",
                    " JUN  "=>"/06 "," JUL  "=>"/07  "," AGO  "=>"/08  "," SEP  "=>"/09 "," OCT  "=>"/10 "," NOV  "=>"/11 "," DIC  "=>"/12 ",
                    "/ENE/"=>"/01/","/FEB/"=>"/02/","/MAR/"=>"/03/","/MZO/"=>"/03/","/ABR/"=>"/04/","/MAY/"=>"/05/","/JUN/"=>"/06/","/JUL/"=>"/07/","/AGO/"=>"/08/",
                    "/SEP/"=>"/09/","/OCT/"=>"/10/","/NOV/"=>"/11/","/DIC/"=>"/12/"
                );
            }else{
                $changeDateArray = array("/ENE"=>"/01","/FEB"=>"/02","/MAR"=>"/03","/MZO"=>"/03","/ABR"=>"/04","/MAY"=>"/05","/JUN"=>"/06","/JUL"=>"/07","/AGO"=>"/08",
                    "/SEP"=>"/09","/OCT"=>"/10","/NOV"=>"/11","/DIC"=>"/12","-ENE-"=>"-01-","-FEB-"=>"-02-","-MAR-"=>"-03-","-MZO-"=>"-03-","-ABR-"=>"-04-",
                    "-MAY-"=>"-05-","-JUN-"=>"-06-","-JUL-"=>"-07-","-AGO-"=>"-08-","-SEP-"=>"-09-","-OCT-"=>"-10-","-NOV-"=>"-11-","-DIC-"=>"-12-","/Enero/"=>"/01/",
                    "/Febrero/"=>"/02/","/Marzo/"=>"/03/","/Abril/"=>"/04/","/Mayo/"=>"/05/","/Junio/"=>"/06/","/Julio/"=>"/07/","/Agosto/"=>"/08/","/Septiembre/"=>"/09/",
                    "/Octubre/"=>"/10/","/Noviembre/"=>"/11/","/Diciembre/"=>"/12/"," ENE "=>"/01 "," FEB "=>"/02 ", " MAR "=>"/03 ", " MZO "=>"/03 "," ABR "=>"/04 "," MAY "=>"/05 ",
                    " JUN "=>"/06 "," JUL "=>"/07 "," AGO "=>"/08 "," SEP "=>"/09 "," OCT "=>"/10 "," NOV "=>"/11 "," DIC "=>"/12 ","-Ene-"=>"-01-","-Feb-"=>"-02-","-Mar-"=>"-03-",
                    "-Mzo-"=>"-03-","-Abr-"=>"-04-","-May-"=>"-05-","-Jun-"=>"-06-","-Jul-"=>"-07-","-Ago-"=>"-08-","-Sep-"=>"-09-","-Oct-"=>"-10-","-Nov-"=>"-11-","-Dic-"=>"-12-"
                );
            }
            
            if($bank_id==186){
                //print_r($changeDateArray);
                $monthNameArray = array(
                    "$ "=>""
                );
                $changeDateArray = array_merge($changeDateArray, $monthNameArray);
                //print_r($changeDateArray);die;
            }
            
            
            
            foreach($changeDateArray as $key=>$date){
                $content = str_replace($key,$date,$content);
            }
            
            if($bank_id==180){
                $content = str_replace(array("   /","  /"," /"), '/', $content);
            }
            
            file_put_contents($actualFilePath, $content);
        }
        
        $handle = @fopen($actualFilePath, "r");
        $contents = file_get_contents($actualFilePath);
        $results = $this->bank_address->getRecordsByBankId($bank_id);
        
        if ($handle)
        {
            
            foreach($string_record as $key=>$value){
                $i = 1;
                if(!in_array($key,array('id','bank_id','is_deleted','add_date','status','uploader_type'))){
                    if($value!='' && $value[0]=='<' && $value[strlen($value) - 1] == '>'){
                        //echo $value;
                        $string = substr($value, 1);
                        $subString = substr($string, 0, -1);
                        $extractData[$key] = $subString;
                    }
                }
            }
            fclose($handle);
            
        }
        
        foreach($results as $result){
            if(preg_match_all('/'.$result->bank_address.'/', $contents, $matches)){
                $extractData['bank_address'] = $result->bank_address;
                $extractData['bank_city'] = $result->bank_city;
                $extractData['bank_state'] = $result->bank_state;
                $extractData['bank_zip'] = $result->bank_zip;
            }
        }
        
        $extractData['account_type'] = '';
        if(isset($string_record->account_type)){
            if(strpos($string_record->account_type,"|")!==false){
                $array = explode("|",$string_record->account_type);
                foreach($array as $arr){
                    if(preg_match_all('/'.$arr.'/', $contents, $matches)){
                        $extractData['account_type'] = $arr;
                        break;
                    }
                }
                
            }else{
                $extractData['account_type'] = $string_record->account_type;
            }
        }
        
        $extractData['account_holder_name'] = '';
        if(isset($string_record->account_holder_name)){
            $array = explode("|",$string_record->account_holder_name);
            foreach($array as $arr){
                if(strpos($arr,"[rgx]")!==false){
                    $account_holder_name = substr($arr,5,-6);
                    if(preg_match_all($account_holder_name, $contents, $matches)){
                        if(isset($matches[1][0]) && !isset($matches[2][0])){
                            $extractData['account_holder_name'] = $matches[1][0];
                        }
                        
                        if(isset($matches[1][0]) && isset($matches[2][0])){
                            $extractData['account_holder_name'] = $matches[1][0].' '.$matches[2][0];
                        }
                        if(trim($extractData['account_holder_name'])!=""){
                            break;
                        }
                        
                    }
                }
            }
        }
        
        $extractData['account_number_string'] = '';
        if(isset($string_record->account_number_string)){
            $array = explode("|",$string_record->account_number_string);
            foreach($array as $arr){
                if(strpos($arr,"[rgx]")!==false){
                    $account_number_string = substr($arr,5,-6);
                    if(preg_match_all($account_number_string, $contents, $matches)){
                        if($matches[1][0]){
                            $extractData['account_number_string'] = filter_var($matches[1][0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                        }
                        break;
                    }
                }
            }
        }
        
        
        $extractData['begining_balance'] = '';
        if(isset($string_record->open_balance)){
            $array = explode("|",$string_record->open_balance);
            foreach($array as $arr){
                if(strpos($arr,"[rgx]")!==false){
                    $open_balance = substr($arr,5,-6);
                    if(preg_match_all($open_balance, $contents, $matches)){
                        if($matches[1][0]){
                            if(strpos($matches[1][0],'-')!=false){
                                $matches[1][0] = '-'.str_replace('-',"",$matches[1][0]);
                            }
                            $extractData['begining_balance'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        }
                        break;
                    }
                }
            }
        }
        
        
        $extractData['closing_balance'] = '';
        if(isset($string_record->close_balance)){
            $array = explode("|",$string_record->close_balance);
            foreach($array as $arr){
                if(strpos($arr,"[rgx]")!==false){
                    $close_balance = substr($arr,5,-6);
                    if(preg_match_all($close_balance, $contents, $matches)){
                        if($matches[1][0]){
                            if(strpos($matches[1][0],'-')!=false){
                                $matches[1][0] = '-'.str_replace('-',"",$matches[1][0]);
                            }
                            $extractData['closing_balance'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        }
                        break;
                    }
                }
            }
        }
        
        if(strpos($string_record->pages,"[rgx]")!==false){
            $pages = substr($string_record->pages,5,-6);
            if(preg_match_all($pages, $contents, $matches)){
                if($matches[1][0]){
                    $extractData['pages'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }
            }
        }
        
        $extractData['end_date'] = '';
        if(isset($string_record->end_date)){
            $array = explode("|",$string_record->end_date);
            foreach($array as $arr){
                if(strpos($arr,"[rgx]")!==false){
                    
                    $end_date = substr($arr,5,-6);
                    if(preg_match_all($end_date, $contents, $matches)){
                        if($matches[1][0]){
                            //echo $matches[1][0];
                            //$matches[1][0] = str_replace("-","/",$matches[1][0]);
                            if($bank_id==178 || $bank_id==181){
                                $matches[1][0] = str_replace("/","-",$matches[1][0]);
                            }else if($bank_id==182 || $bank_id==183){
                                $matches[1][0] = str_replace("/","-",$matches[1][0]);
                                $changeDateString = array(" del"=>""," DE"=>""," ENERO"=>"/01"," FEBRERO"=>"/02"," MARZO"=>"/03"," ABRIL"=>"/04"," MAYO"=>"/05",
                                    " JUNIO"=>"/06"," JULIO"=>"/07"," AGOSTO"=>"/08"," SEPTIEMBRE"=>"/09"," OCTUBRE"=>"/10",
                                    " NOVIEMBRE"=>"/11"," DICIEMBRE "=>"/12"," AL "=>"");
                                
                                //echo $matches[1][0]."RRRRRRRRRRRR";
                                foreach($changeDateString as $key=>$date){
                                    $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
                                }
                                
                                $matches[1][0] = str_replace(" ","/",$matches[1][0]);
                                $dateExplode = explode("/",$matches[1][0]);
                                $year = $dateExplode[2];
                                $month = $dateExplode[1];
                                //echo $matches[1][0];die('here');
                            }else{
                                $matches[1][0] = str_replace("-","/",$matches[1][0]);
                            }
                            
                            if($bank_id==155){
                                $matches[1][0] = str_replace("ENE","01",$matches[1][0]);
                                $matches[1][0] = str_replace("FEB","02",$matches[1][0]);
                                $matches[1][0] = str_replace("MAR","03",$matches[1][0]);
                                $matches[1][0] = str_replace("ABR","04",$matches[1][0]);
                                $matches[1][0] = str_replace("MAY","05",$matches[1][0]);
                                $matches[1][0] = str_replace("JUN","06",$matches[1][0]);
                                $matches[1][0] = str_replace("JUL","07",$matches[1][0]);
                                $matches[1][0] = str_replace("AGO","08",$matches[1][0]);
                                $matches[1][0] = str_replace("SEP","09",$matches[1][0]);
                                $matches[1][0] = str_replace("OCT","10",$matches[1][0]);
                                $matches[1][0] = str_replace("NOV","11",$matches[1][0]);
                                $matches[1][0] = str_replace("DIC","12",$matches[1][0]);
                                
                                $matches[1][0] = str_replace(" ","-",$matches[1][0]);
                            }
                            
                            if($bank_id==188){
                                $changeDateString = array("DE"=>"/","ENERO"=>"01","FEBRERO"=>"02","MARZO"=>"03","ABRIL"=>"04","MAYO"=>"05",
                                    "JUNIO"=>"06","JULIO"=>"07","AGOSTO"=>"08","SEPTIEMBRE"=>"09","OCTUBRE"=>"10",
                                    "NOVIEMBRE"=>"11","DICIEMBRE "=>"12");
                                foreach($changeDateString as $key=>$date){
                                    $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
                                }
                                
                                $matches[1][0] = str_replace(' ', '', $matches[1][0]);
                                $dateExplodeSantander = explode("/",$matches[1][0]);
                                $yearSantander = $dateExplodeSantander[2];
                                $monthSantander = $dateExplodeSantander[1];
                                //echo $matches[1][0];die;
                            }
                            
                            if($bank_id==185){
                                $changeDateString = array("Ene"=>"/01/","Feb"=>"/02/","Mar"=>"/03/","Abr"=>"/04/","May"=>"/05/","Jun"=>"/06/",
                                    "Jul"=>"/07/","Ago"=>"/08/","Sep"=>"/09/","Oct"=>"/10/","Nov"=>"/11/",
                                    "Dic"=>"/12/");
                                foreach($changeDateString as $key=>$date){
                                    $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
                                }
                                
                                $matches[1][0] = str_replace(' ', '', $matches[1][0]);
                                //echo $matches[1][0];die;
                                $expDate = explode("/",$matches[1][0]);
                                if(count($expDate)==3){
                                    $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
                                }
                                if(count($expDate)==2){
                                    $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
                                }
                            }
                            
                            $matches[1][0] = str_replace("-","/",$matches[1][0]);
                            if($string_record->bank_date_format=='d-m-y'){
                                if(strpos($matches[1][0],'/') !== false) {
                                    $expDate = explode("/",$matches[1][0]);
                                    if(count($expDate)==3){
                                        $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
                                    }
                                    if(count($expDate)==2){
                                        $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
                                    }
                                }
                            }
                            
                            if(!strtotime($matches[1][0])){
                                $end_date = preg_replace('/[^a-zA-Z0-9, ]/s','',$matches[1][0]);
                                $extractData['end_date'] = date('m/d/Y', strtotime($end_date));
                            }else{
                                $end_date = $matches[1][0];
                                $extractData['end_date'] = date('m/d/Y', strtotime($matches[1][0]));
                            }
                            break;
                        }
                    }
                }
            }
        }
        //echo $extractData['end_date'] = '31-01-2019';
        //echo date("m-d-Y", strtotime($extractData['end_date']));
        
        $extractData['start_date'] = '';
        if(isset($string_record->start_date)){
            $array = explode("|",$string_record->start_date);
            //print_r($array);die;
            foreach($array as $arr){
                
                if(strpos($arr,"[rgx]")!==false){
                    $start_date = substr($arr,5,-6);
                    //echo$start_date;die;
                    
                    if(preg_match_all($start_date, $contents, $matches)){
                        if($matches[1][0]){
                            //echo $matches[1][0];die('here');
                            //$matches[1][0] = str_replace("-","/",$matches[1][0]);
                            if($bank_id==178 || $bank_id==181){
                                $matches[1][0] = str_replace("/","-",$matches[1][0]);
                            }else if($bank_id==182 || $bank_id==183){
                                $matches[1][0] = str_replace("/","-",$matches[1][0]);
                                $changeDateString = array(" del"=>"","del"=>""," DE"=>""," ENERO"=>"/01"," FEBRERO"=>"/02"," MARZO"=>"/03"," ABRIL"=>"/04"," MAYO"=>"/05",
                                    " JUNIO"=>"/06"," JULIO"=>"/07"," AGOSTO"=>"/08"," SEPTIEMBRE"=>"/09"," OCTUBRE"=>"/10",
                                    " NOVIEMBRE"=>"/11"," DICIEMBRE "=>"/12"," AL "=>"","al"=>"");
                                
                                //echo $matches[1][0]."RRRRRRRRRRRR";
                                foreach($changeDateString as $key=>$date){
                                    $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
                                }
                                
                                $matches[1][0] = str_replace(" ","/",$matches[1][0]);
                                if($bank_id==182){
                                    $matches[1][0] = $matches[1][0].'/'.$year;
                                }else if($bank_id==183){
                                    $matches[1][0] = $matches[1][0].'/'.$month.'/'.$year;
                                    $matches[1][0] = str_replace("//","/",$matches[1][0]);
                                }
                                if($matches[1][0][0]=='/'){
                                    $matches[1][0] = ltrim($matches[1][0], '/');
                                }
                                //echo $matches[1][0];die('hree');
                            }else{
                                $matches[1][0] = str_replace("-","/",$matches[1][0]);
                            }
                            if($bank_id==155){
                                $matches[1][0] = str_replace("ENE","01",$matches[1][0]);
                                $matches[1][0] = str_replace("FEB","02",$matches[1][0]);
                                $matches[1][0] = str_replace("MAR","03",$matches[1][0]);
                                $matches[1][0] = str_replace("ABR","04",$matches[1][0]);
                                $matches[1][0] = str_replace("MAY","05",$matches[1][0]);
                                $matches[1][0] = str_replace("JUN","06",$matches[1][0]);
                                $matches[1][0] = str_replace("JUL","07",$matches[1][0]);
                                $matches[1][0] = str_replace("AGO","08",$matches[1][0]);
                                $matches[1][0] = str_replace("SEP","09",$matches[1][0]);
                                $matches[1][0] = str_replace("OCT","10",$matches[1][0]);
                                $matches[1][0] = str_replace("NOV","11",$matches[1][0]);
                                $matches[1][0] = str_replace("DIC","12",$matches[1][0]);
                                $matches[1][0] = str_replace(" ","/",$matches[1][0]);
                                //echo $matches[1][0];die('here');
                            }
                            
                            if($bank_id==188){
                                $matches[1][0] = $matches[1][0].'/'.$monthSantander.'/'.$yearSantander;
                            }
                            
                            if($bank_id==185){
                                $changeDateString = array("Ene"=>"/01/","Feb"=>"/02/","Mar"=>"/03/","Abr"=>"/04/","May"=>"/05/","Jun"=>"/06/",
                                    "Jul"=>"/07/","Ago"=>"/08/","Sep"=>"/09/","Oct"=>"/10/","Nov"=>"/11/",
                                    "Dic"=>"/12/");
                                foreach($changeDateString as $key=>$date){
                                    $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
                                }
                                
                                $matches[1][0] = str_replace(' ', '', $matches[1][0]);
                                $expDate = explode("/",$matches[1][0]);
                                if(count($expDate)==3){
                                    $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
                                }
                                if(count($expDate)==2){
                                    $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
                                }
                                //echo $matches[1][0];die;
                            }
                            
                            $matches[1][0] = str_replace("-","/",$matches[1][0]);
                            if($string_record->bank_date_format=='d-m-y'){
                                if(strpos($matches[1][0],'/') !== false) {
                                    $expDate = explode("/",$matches[1][0]);
                                    if(count($expDate)==3){
                                        $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
                                    }
                                    if(count($expDate)==2){
                                        $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
                                    }
                                }
                            }
                            
                            if(!strtotime($matches[1][0])){
                                $start_date = preg_replace('/[^a-zA-Z0-9, ]/s','',$matches[1][0]);
                                $extractData['start_date'] = date('m/d/Y', strtotime($start_date));
                            }else{
                                if($bank_id==13){
                                    $strt_date = explode(',', $end_date);
                                    $matches[1][0] = $matches[1][0].', '.$strt_date[1];
                                }
                                
                                if($bank_id==151){
                                    $strt_date = explode(',', $end_date);
                                    $matches[1][0] = $matches[1][0].'/'.trim($strt_date[1]);
                                }
                                
                                $extractData['start_date'] = date('m/d/Y', strtotime($matches[1][0]));
                                
                                
                                if($bank_id==13){
                                    $tempArr=explode('/', $extractData['start_date']);
                                    $date1 = date("m/d/Y", mktime(0, 0, 0, $tempArr[0], $tempArr[1], $tempArr[2]));
                                    $tempArr=explode('/', $extractData['end_date']);
                                    $date2 = date("m/d/Y", mktime(0, 0, 0, $tempArr[0], $tempArr[1], $tempArr[2]));
                                    if(strtotime($date1) > strtotime($date2)){
                                        $tempArr=explode('/', $extractData['start_date']);
                                        $year = $tempArr[2]-1;
                                        $extractData['start_date'] = $tempArr[0].'/'.$tempArr[1].'/'.$year;
                                    }
                                }
                                
                            }
                            break;
                        }
                    }
                }
            }
        }
        
       
        $extractData['service_fee_pattern_1'] = '';
        if(isset($string_record->service_fee_pattern_1)){
            if(strpos($string_record->service_fee_pattern_1,"[rgx]")!==false){
                $pages = substr($string_record->service_fee_pattern_1,5,-6);
                if(preg_match_all($pages, $contents, $matches)){
                    if($matches[1][0]){
                        $extractData['service_fee_pattern_1'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    }
                }
            }
        }
        
        $extractData['service_fee_pattern_2'] = '';
        if(isset($string_record->service_fee_pattern_2)){
            if(strpos($string_record->service_fee_pattern_2,"[rgx]")!==false){
                $pages = substr($string_record->service_fee_pattern_2,5,-6);
                if(preg_match_all($pages, $contents, $matches)){
                    if($matches[1][0]){
                        $extractData['service_fee_pattern_2'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    }
                }
            }
        }
        
        if($bank_id==13){
            if($extractData['end_date']<$extractData['start_date']){
                $strt_date = explode("/",$extractData['start_date']);
                if($strt_date[2]){
                    $year = $strt_date[2]-1;
                    $month = $strt_date[0];
                    $date = $strt_date[1];
                    $extractData['start_date'] = date('m/d/Y', strtotime($month.'/'.$date.'/'.$year));
                }
            }
        }
        
        //echo $string_record->end_date;
        //echo $string_record->start_date;
        if($string_record->remove_string!= null && $string_record->remove_string!=""){
            $array = explode("|",$string_record->remove_string);
            $content = file_get_contents(FCPATH.'assets/uploads/bank_statement/'.$txtFilename);
            
            if ($bank_id!=182 && $extractData['account_holder_name'] != "") {
                $content = preg_replace('/'.trim($extractData['account_holder_name']).'/', ' ', $content);
            }
            
            foreach($array as $arr){
                //echo$arr."</br>";
                if(strpos($arr,"[rgx]")!==false){
                    $rm_string = substr($arr,5,-6);
                    //$content = preg_replace($rm_string, '       ', $content);
                    if(preg_match_all($rm_string, $content, $matches)){
                        //incase if matches[1][0] = 0
                        if($matches[1][0] !== false && $matches[1][0] != ""){
                            $rm = str_replace($matches[1][0],'  ',$matches[0][0]);
                            $content = preg_replace($rm_string, $rm, $content);
                        }else{
                            $content = preg_replace($rm_string, '       ', $content);
                        }
                        
                    }
                }
            }
            
            file_put_contents(FCPATH.'assets/uploads/bank_statement/'.$txtFilename, $content);
        }
        
        
        
        
        return $extractData;
    }*/
    
}