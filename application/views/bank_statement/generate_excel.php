<!-- <img src="../assets/index.jpg" style="height: 20px;">
<object data="../assets/5c0168452e55f6ed3031dae561153aa4.pdf" type="application/pdf" style="width: 20%;"></object> -->
<?php $this->load->view('includes/common'); ?>
<?php

// echo base_url('./assets/uploads/bank_statement/5c0168452e55f6ed3031dae561153aa4.pdf');die;

// $get_current_path_to_front = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/';

// $set_new_path_to_front = str_replace('\\', '/', realpath($get_current_path_to_front . '../')) . '/';

//echo $set_new_path_to_front;

// Path to the front controller (this file)
// define('FCPATH', $set_new_path_to_front);

// echo str_replace("\\", "/", $system_path);die;
// if ($stream = fopen('http://localhost/bss/assets/uploads/bank_statement/5c0168452e55f6ed3031dae561153aa4.pdf', 'r')) {
//     // print all the page starting at the offset 10
//     $a = stream_get_contents($stream);

    
// }

// $handle = @fopen($_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/Ryoko_test.txt', "r");
// echo($handle);die;
// $uri_path =$_SERVER['REQUEST_URI'];
// $uri_segments = explode('/', $uri_path);
// print_r($uri_segments);die;
//echo date('h:i:s') . "\n";


//sleep(10);


//echo date('h:i:s') . "\n";

?>

<?php 

// echo $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/Ryoko_test.pdf';die;
// $handle = @fopen($_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/5c0168452e55f6ed3031dae561153aa4.pdf', "r");
// if ($handle)
// {
//     while (!feof($handle)) { 
//     $buffer = fgets($handle, 4096); 
//     $string .= $buffer;
//     }
//     echo $string;
// }

// $a = base_url('./assets/uploads/bank_statement/5c0168452e55f6ed3031dae561153aa4.pdf');
// $endpoint = "https://sandbox.zamzar.com/v1/jobs";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $sourceFilePath = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/5c0168452e55f6ed3031dae561153aa4.pdf';
// $targetFormat = "txt";
// $finfo = new \finfo(FILEINFO_MIME_TYPE);
// $mimetype = $finfo->file('5c0168452e55f6ed3031dae561153aa4.pdf');
// $cfile = new CURLFile('5c0168452e55f6ed3031dae561153aa4.pdf',$mimetype,basename('5c0168452e55f6ed3031dae561153aa4.pdf'));

//   echo $cfile;
// // $fp = fopen($sourceFilePath, 'r');
// $postData = array(
//   "source_file" => $cfile,
//   "target_format" => $targetFormat
// );
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $endpoint);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
// $body = curl_exec($ch);
// curl_close($ch);
// $response = json_decode($body, true);


// print_r($response);die;
// sleep(10);
// $jobID = $response['id'];
// $endpoint1 = "https://sandbox.zamzar.com/v1/jobs/$jobID";
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $endpoint1); 
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); 
// $body = curl_exec($ch);
// curl_close($ch);
// $job = json_decode($body, true);
// var_dump($job);die;
// $target_id = $job['target_files'][0]['id'];
// if(empty($target_id)){
//     echo "error";
// }
// else{
//     $fileID = $target_id;
//     $realPath = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/';
//     $txtFilename = 'b.txt';
//     $actualFilePath = $realPath.''.$txtFilename;
//     $endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
//     $ch = curl_init(); 
//     curl_setopt($ch, CURLOPT_URL, $endpoint2); 
//     curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); 
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
//     $fh = fopen($actualFilePath, "w+");
//     curl_setopt($ch, CURLOPT_FILE, $fh);
//     $body = curl_exec($ch);
//     curl_close($ch);
//     echo "File downloaded\n";
// }








// $targetFormat = "txt";
// $postData = array(
//   "source_file" => $sourceFile,
//   "target_format" => $targetFormat
// );
// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Enable the @ prefix for uploading files
// // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);
// $response = json_decode($body, true);

// sleep(10);

// $jobID = $response['id'];
// $endpoint1 = "https://sandbox.zamzar.com/v1/jobs/$jobID";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint1); // API endpoint
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);
// $job = json_decode($body, true);

// $target_id = $job['target_files'][0]['id'];

// if(empty($target_id)){
// 	echo "error";
// }
// else{
// 	$fileID = $target_id;
// 	$localFilename = dirname(__FILE__) . '/arun_test_by.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
// 	$endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
// 	$apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// 	$ch = curl_init(); // Init curl
// 	curl_setopt($ch, CURLOPT_URL, $endpoint2); // API endpoint
// 	curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// 	$fh = fopen($localFilename, "w+");
// 	curl_setopt($ch, CURLOPT_FILE, $fh);
// 	$body = curl_exec($ch);
// 	curl_close($ch);
// 	echo "File downloaded\n";
// }
///Guys it looks like our database was under some bot attack this evening which caused a lot of problems.
//Our server became out of memory due to some bot attack. so this problem create.

// $file = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/Ryoko_test.txt';
// $contents = file_get_contents($file);
// $searchfor = 'Account number:';

// header('Content-Type: text/plain');

// // get the file contents, assuming the file to be readable (and exist)

// // escape special characters in the query
// $pattern = preg_quote($searchfor, '/');
// // finalise the regular expression, matching the whole line
// $pattern = "/^.*$pattern.*\$/m";
// // search, and store all matching occurences in $matches
// if(preg_match_all($pattern, $contents, $matches)){
// 	print_r($matches);die;
//    echo "Found matches:\n";
//    echo implode("\n", $matches[0]);
// }
// else{
//    echo "No matches found";
// }


// $searchthis = "number";
// $matches = array();
// $handle = @fopen($_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/bank_statement/Ryoko_test.txt', "r");
// if ($handle)
// {
// 	$i = 1;
//     while (!feof($handle))
//     {
//         $buffer = fgets($handle);
//         if($pos = strpos($buffer, $searchthis) !== FALSE){
//         	if($i==1){

//         		$match = explode('Account number:',$buffer);
//                  print_r($match);die;
//         		$mt = $match[1];
//         	}
//         	$i++;
//         }
       	
//     }
//     fclose($handle);
// }
// echo $mt;


?>


<a href="javascript:" onclick="test_other();">Test</a>
<p id="myContainer"></p>

<script type="text/javascript" src="https://requirejs.org/docs/release/2.3.5/minified/require.js"></script>
<script type="text/javascript">
function test() {
    var filepath = siteurl+'assets/uploads/bank_statement/Ryoko_test.txt'; 
    var searchString = 'Account number:';
    var type = searchString;
    var path = filepath;
    var account_number = "";
    $.get(path, function(data) {
        var array = data.split("\n");
        $.each(array, function(n, elem) {
            var array2 = elem.split(/  +/g);  
            if(elem.includes(type)){  
                var sp = elem.split(type);
                if(sp[1]==""){
                    account_number = array2[j+1];
                    console.log(type+" "+array2[j+1]);
                }else{
                    account_number = sp[1];
                    console.log(type+" "+sp[1]);
                }
            }
            // $('#myContainer').append('<div>' + elem + '</div>');
        });        
    });
}



function test_other() {
    var filepath = siteurl+'assets/uploads/bank_statement/Ryoko_test.txt'; 
    var searchString = 'Account number:';
    var type = searchString;
    var path = filepath;
    var account_number = "";


    var credits = [];
    var credits_start = "No";
    var credit_start_string = "Credits\r";
    var credit_close_string = "Withdrawals\r";
    var debit_start_string = "Withdrawals\r";
    var debit_close_string = "Checks Paid\r";
    var checks_start_string = "Checks Paid\r";
    var checks_close_string = "IMPORTANT INFORMATION\r";

    $.get(path, function(data) {
        var array = data.split("\n");
        for(var i=0;i<array.length;i++){
            var array2 =array[i].split(/  +/g);

            //alert(array2[0]);
            // console.log(array2);
            // Deposits and other credits code script start
            if(array2[0]==credit_start_string || array2[1]==credit_start_string){
                credits_start = "Yes";
            }
            if(array2[0]==credit_close_string || array2[1]==credit_close_string){
                credits_start = "No";
            }
            if(credits_start =="Yes"){
                if(array2[0]=="" && array2.length==2){
                    var desc = array2[1];
                    var length = credits.length;
                    if(length>0){
                        credits[length-1].description += desc;
                    }
                }else{
                    if(array2.length==4){
                        array2 = array2.filter(e => String(e).trim());
                    }
                    var timestamp = Date.parse(array2[0]);
                    if(isNaN(timestamp) == false && (array2[2]!=undefined || array2[2]!="")){
                        credits.push({
                            "date":array2[0],
                            "description":array2[1],
                            "amount":array2[2]
                        })
                    }
                }
            }
        }
        alert(credits);
        $.each(credits, function(n, res) {
            // alert(res.date);
            $('#myContainer').append('<div>' + res.date + '  ' + res.description + ' ' + res.amount + '</div>');
        })

        // alert(credits);
    });
}

</script>