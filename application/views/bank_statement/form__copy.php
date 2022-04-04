<!-- <style type="text/css">
	.form-style-1 {
		margin:10px auto;
		max-width: 400px;
		padding: 20px 12px 10px 20px;
		font: 13px "Lucida Sans Unicode", "Lucida Grande", sans-serif;
	}
	.form-style-1 li {
		padding: 0;
		display: block;
		list-style: none;
		margin: 10px 0 0 0;
	}
	.form-style-1 label{
		margin:0 0 3px 0;
		padding:0px;
		display:block;
		font-weight: bold;
	}
	.form-style-1 input[type=text], 
	.form-style-1 input[type=date],
	.form-style-1 input[type=datetime],
	.form-style-1 input[type=number],
	.form-style-1 input[type=search],
	.form-style-1 input[type=time],
	.form-style-1 input[type=url],
	.form-style-1 input[type=email],
	textarea, 
	select{
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		border:1px solid #BEBEBE;
		padding: 7px;
		margin:0px;
		-webkit-transition: all 0.30s ease-in-out;
		-moz-transition: all 0.30s ease-in-out;
		-ms-transition: all 0.30s ease-in-out;
		-o-transition: all 0.30s ease-in-out;
		outline: none;	
	}
	.form-style-1 input[type=text]:focus, 
	.form-style-1 input[type=date]:focus,
	.form-style-1 input[type=datetime]:focus,
	.form-style-1 input[type=number]:focus,
	.form-style-1 input[type=search]:focus,
	.form-style-1 input[type=time]:focus,
	.form-style-1 input[type=url]:focus,
	.form-style-1 input[type=email]:focus,
	.form-style-1 textarea:focus, 
	.form-style-1 select:focus{
		-moz-box-shadow: 0 0 8px #88D5E9;
		-webkit-box-shadow: 0 0 8px #88D5E9;
		box-shadow: 0 0 8px #88D5E9;
		border: 1px solid #88D5E9;
	}
	.form-style-1 .field-divided{
		width: 49%;
	}

	.form-style-1 .field-long{
		width: 100%;
	}
	.form-style-1 .field-select{
		width: 100%;
	}
	.form-style-1 .field-textarea{
		height: 100px;
	}
	.form-style-1 input[type=submit], .form-style-1 input[type=button]{
		background: #4B99AD;
		padding: 8px 15px 8px 15px;
		border: none;
		color: #fff;
	}
	.form-style-1 input[type=submit]:hover, .form-style-1 input[type=button]:hover{
		background: #4691A4;
		box-shadow:none;
		-moz-box-shadow:none;
		-webkit-box-shadow:none;
	}
	.form-style-1 .required{
		color:red;
	}
</style>
<?php //$this->load->view('includes/common'); ?>
<center><h2>Bank Statement</h2></center>
<form class="login-form ajax_form" action="<?php //echo site_url('Bank_statement/add'); ?>" method="post">
	<ul class="form-style-1">
		<li>
	        <label>Upload File</label>
	        <input type="file" name="image_name" class="field-long" style="width: 250px;">
	    </li>
	    <li>
	        <label>Account Number</label>
	        <input type="account_number" name="account_number" class="field-long" />
	    </li>
	    <li>
	        <input type="submit" value="Submit" />
	    </li>
	</ul>
</form> -->

<?php //$this->load->view('includes/common'); ?>
<?php 
// set_time_limit(0);
// $endpoint = "https://api.zamzar.com/v1/jobs";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $sourceFile = 'http://localhost/bank-statement/assets/uploads/Ryoko.pdf';
// $targetFormat = "docx";
// $postData = array(
//   "source_file" => $sourceFile,
//   "target_format" => $targetFormat
// );
// $curlCh = curl_init();
// curl_setopt($curlCh, CURLOPT_URL, $endpoint);
// curl_setopt($curlCh, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($curlCh, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($curlCh, CURLOPT_TIMEOUT, 50);
// curl_setopt($curlCh, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($curlCh, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($curlCh, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $curlData = curl_exec ($curlCh);
// curl_close ($curlCh);
// $downloadPath = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/converted_other_test.text';
// $file = fopen($downloadPath, "wb");
// fputs($file, $curlData);
// fclose($file);

// $endpoint = "https://api.zamzar.com/v1/jobs";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $sourceFile = 'https://s3.amazonaws.com/zamzar-samples/sample.pdf';
// $targetFormat = "txt";

// $postData = array(
//   "source_file" => $sourceFile,
//   "target_format" => $targetFormat
// );

// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);
// $response = json_decode($body, true);

// // print_r($response);die;
// if($response){
// 	$jobID = $response['id'];
// 	$endpoint = $endpoint.'/'.$jobID;
// 	$ch = curl_init(); // Init curl
// 	curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 	curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 	$body = curl_exec($ch);
// 	curl_close($ch);
// 	$job = json_decode($body, true);
// 	// print_r($job);die;
// 	if($job){
// 		set_time_limit(0);
// 		$fileID = $job['id'];
// 		$localFilename = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/converted_other1.txt';
// 		// echo $localFilename;
// 		$fh = fopen($localFilename, "wb");
// 		$endpoint = "https://api.zamzar.com/v1/files/$fileID/content";
// 		$ch = curl_init(); // Init curl
// 		curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// 		curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_FILE, $fh);
// 		curl_exec($ch);
// 		curl_close($ch);
// 		fclose($fh);
// 		echo "File downloaded\n";
// 	}
// }



// $endpoint = "https://sandbox.zamzar.com/v1/jobs";
// $apiKey = "GiVUYsF4A8ssq93FR48H";
// $sourceFile = $_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/Ryoko.pdf';
// $targetFormat = "png";

// $postData = array(
//   "source_file" => $sourceFile,
//   "target_format" => $targetFormat
// );

// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);

// $response = json_decode($body, true);

// print_r($response);die;










// $endpoint = "https://sandbox.zamzar.com/v1/jobs";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $sourceFile = 'http://www.pdf995.com/samples/pdf.pdf'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/Ryoko_test.pdf'; //base_url('./assets/uploads/Ryoko_test.pdf');   // "/tmp/portrait.gif";
// $targetFormat = "txt";

// set_time_limit(0);
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $fileID =   58062593;//58064414;
// // $localFilename = dirname(__FILE__) . '/abcde.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
// $endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint2); // API endpoint
// curl_setopt($ch, CURLOPT_TIMEOUT, 500);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// $fh = fopen($localFilename, "wb");
// curl_setopt($ch, CURLOPT_FILE, $fh);
// $body = curl_exec($ch);
// curl_close($ch);
// echo "File downloaded\n";



// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $fileID =     58063769;// 58064355;//58064414;
// // $localFilename = dirname(__FILE__) . '/abcde.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
// $endpoint2 = "https://api.zamzar.com/v1/files/$fileID/content";
// set_time_limit(0);
// //This is the file where we save the    information
// $fp = fopen (dirname(__FILE__) . '/al.txt', 'wb');
// //Here is the file we are downloading, replace spaces with %20
// $ch = curl_init(str_replace(" ","%20",$endpoint2));
// curl_setopt($ch, CURLOPT_TIMEOUT, 500);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// // write curl response to file
// curl_setopt($ch, CURLOPT_FILE, $fp); 
// // get curl response
// curl_exec($ch); 
// curl_close($ch);
// fclose($fp);



// $postData = array(
//   "source_file" => $sourceFile,
//   "target_format" => $targetFormat
// );

// $ch = curl_init(); // Init curl
// curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Enable the @ prefix for uploading files
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);
// $response = json_decode($body, true);
//  // print_r($response).'</br>';
// if($response){
// 	$jobID = $response['id'];
// 	$endpoint1 = "https://sandbox.zamzar.com/v1/jobs/$jobID";
// 	$ch = curl_init(); // Init curl
// 	curl_setopt($ch, CURLOPT_URL, $endpoint1); // API endpoint
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 	curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 	$body = curl_exec($ch);
// 	curl_close($ch);
// 	$job = json_decode($body, true);	
// 	// var_dump($job);die;
// 	if($job){
		
// 		// $fileID = $job['id'];
// 		$fileID = 7858542;
// 		$localFilename = dirname(__FILE__) . '/test_cnvrt123.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
// 		$endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
// 		$ch = curl_init(); // Init curl
// 		curl_setopt($ch, CURLOPT_URL, $endpoint2); // API endpoint
// 		curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// 		$fh = fopen($localFilename, "w+");
// 		curl_setopt($ch, CURLOPT_FILE, $fh);
// 		$body = curl_exec($ch);
// 		curl_close($ch);
// 		echo "File downloaded\n";







		//The resource that we want to download.
		// $endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
		// $apiKey1 = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
		 
		// //The path & filename to save to.
		// $saveTo = dirname(__FILE__) . '/test_123.txt';
		 
		// //Open file handler.
		// $fp = fopen($saveTo, 'w+');
		 
		// //If $fp is FALSE, something went wrong.
		// if($fp === false){
		//     throw new Exception('Could not open: ' . $saveTo);
		// }
		 
		// //Create a cURL handle.
		
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL,$endpoint2);
		
		// curl_setopt($ch, CURLOPT_USERPWD, $apiKey1 . ":");
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		// //Pass our file handle to cURL.
		// curl_setopt($ch, CURLOPT_FILE, $fp);
		 
		// //Timeout if the file doesn't download after 20 seconds.
		// curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		 
		// //Execute the request.
		// curl_exec($ch);
		 
		// //If there was an error, throw an Exception
		// if(curl_errno($ch)){
		//     throw new Exception(curl_error($ch));
		// }
		 
		// //Get the HTTP status code.
		// $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		 
		// //Close the cURL handler.
		// curl_close($ch);
		 
		// //Close the file handler.
		// fclose($fp);
		 
		// if($statusCode == 200){
		//     echo 'Downloaded!';
		// } else{
		//     echo "Status Code: " . $statusCode;
		// }


// 	}
	
// }







// $endpoint = "https://api.zamzar.com/v1/jobs";
// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// $sourceFile = 'http://www.pdf995.com/samples/pdf.pdf';
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
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// $body = curl_exec($ch);
// curl_close($ch);
// $response = json_decode($body, true);
// if($response['id']){
// 	$target_id = check_target_files($response['id']);
// 	// // echo $response['id'];die;
// 	// $target_id =  setTimeout(function(){
// 	//    check_target_files();
// 	// }, 10000);

// 	if(empty($target_id)){
// 		echo "error";
// 	}
// 	else{
// 		$fileID = $target_id;
// 		$localFilename = dirname(__FILE__) . '/arun_test1.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
// 		$endpoint2 = "https://api.zamzar.com/v1/files/$fileID/content";
// 		$apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// 		$ch = curl_init(); // Init curl
// 		curl_setopt($ch, CURLOPT_URL, $endpoint2); // API endpoint
// 		curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// 		$fh = fopen($localFilename, "w+");
// 		curl_setopt($ch, CURLOPT_FILE, $fh);
// 		$body = curl_exec($ch);
// 		curl_close($ch);
// 		echo "File downloaded\n";
// 	}
// }


// function check_target_files()
// {

// 	$endpoint = "https://sandbox.zamzar.com/v1/jobs";
// 	$apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// 	$sourceFile = 'http://www.pdf995.com/samples/pdf.pdf';
// 	$targetFormat = "txt";
// 	$postData = array(
// 	  "source_file" => $sourceFile,
// 	  "target_format" => $targetFormat
// 	);
// 	$ch = curl_init(); // Init curl
// 	curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// 	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Enable the @ prefix for uploading files
// 	// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 	curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 	$body = curl_exec($ch);
// 	curl_close($ch);
// 	$response = json_decode($body, true);
// 	print_r($response);die;
// 	// if ($response['status']=='successful'){
// 	// 	test($response['id']);
// 	// }

// 	test($response['id']);

// 	// echo $jobID;die;
// 	// $jobID = 7861866;
// 	// $endpoint1 = "https://api.zamzar.com/v1/jobs/$jobID";
// 	// $apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
// 	// $ch = curl_init(); // Init curl
// 	// curl_setopt($ch, CURLOPT_URL, $endpoint1); // API endpoint
// 	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
// 	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 	// curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
// 	// $body = curl_exec($ch);
// 	// curl_close($ch);
// 	// $job = json_decode($body, true);
// 	// var_dump($job);die;
// 	// return $job['target_files'][0]['id'];
// }

?>



<?php

function test($id){
	
		// $target_id = check_target_files($response['id']);
		// // echo $response['id'];die;
		// $target_id =  setTimeout(function(){
		//    check_target_files();
		// }, 10000);

		$jobID = 7862532;//$id;
		$endpoint1 = "https://sandbox.zamzar.com/v1/jobs/$jobID";
		$apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
		$ch = curl_init(); // Init curl
		curl_setopt($ch, CURLOPT_URL, $endpoint1); // API endpoint
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
		$body = curl_exec($ch);
		curl_close($ch);
		$job = json_decode($body, true);
		var_dump($job);die;
		$target_id = $job['target_files'][0]['id'];

		if(empty($target_id)){
			echo "error";
		}
		else{
			$fileID = $target_id;
			$localFilename = dirname(__FILE__) . '/arun_test_by.txt'; //$_SERVER['DOCUMENT_ROOT'].'/bank-statement/assets/uploads/test_cnvrt123.txt';
			$endpoint2 = "https://sandbox.zamzar.com/v1/files/$fileID/content";
			$apiKey = "61ea20e73ae2c0a2fd6ec3bb9e4a4bc4abfbc61f";
			$ch = curl_init(); // Init curl
			curl_setopt($ch, CURLOPT_URL, $endpoint2); // API endpoint
			curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			$fh = fopen($localFilename, "w+");
			curl_setopt($ch, CURLOPT_FILE, $fh);
			$body = curl_exec($ch);
			curl_close($ch);
			echo "File downloaded\n";
		}


	
}


?>


<?php
// Build the setTimeout function.
// This is the important part.
function setTimeout($fn, $timeout){
    // sleep for $timeout milliseconds.
    sleep(($timeout/1000));
    $fn();
}

// // Some example function we want to run.

// // This will run the function after a 3 second sleep =>
// // We're using an anonymous function to wrap the function
// // which we wish to execute.
// $a;
setTimeout(function(){
   check_target_files();
}, 5000);
// check_target_files();


?>