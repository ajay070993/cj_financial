<?php 
function showLimitedText($string,$len = 10) 
{
	$string = strip_tags($string);
	if (strlen($string) > $len)
		$string = mb_substr($string, 0, $len-3) . "...";
	return $string;
}


if (!function_exists('pr')) {

    function pr($arr) {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}

if(!function_exists('escape_text')){
  function escape_text($s = ''){
      return str_replace('&#039;', "'", htmlspecialchars_decode(html_entity_decode($s)));
  } 
}

function getSiteOption($key = '', $value = false){
  	if($key == ''){
    	return false;
  	}
  	$CI = & get_instance();
  	$CI->load->model('admin/website_model', 'website');
  	$value = $CI->website->getValueBySlug($key, $value);
  	return $value;
}

if(!function_exists('is_logged_in')){
  function is_logged_in(){
    global $CI;
    $CI = & get_instance(); 
    if($CI->session->userdata('admin_id')){
       return true;
    }
    else
    {
       return false;
    } 
    return false;
  }
}

function getMailConstants(){
  $constants = [
    'Logo'                => '{{Logo}}',
    'Email_Address'       => '{{Email_Address}}',
    'Subject'             => '{{Subject}}',
    'Website_UR'          => '{{Website_URL}}',
    'Unsubscription_Link' => '{{Unsubscription_Link}}',
  ];
  return $constants;
}

function getImageURL($content)
{
    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $img);       
    $url = $img[1];
    if($url)
    {
        return $url;
    }
    else
    {
        return false;
    }
}

function getUserRole(){
    return array(2=>'Analyst', 3=>'QA');
}

function fs_getUserRole(){
  return array(5=>'FS-Analyst', 6=>'FS-QA',7=>'FS-TL');
}

function creaditCategoryArray(){
    $credit_array = array('Sales - Card','Sales - Non Card (UBER)','Sales - Non Card (Didi)','Sales - Non Card (Rappi)','Sales - Non Card (Sin Delantal)','Sales - Non Card (Other)','Cash Deposit','Refund/Reversals','Intra Account Transfer','NG Check','Loans','Investment Income','Insurance Claim','Miscellaneous Credits');
  return $credit_array;
}

function debitCategoryArray(){
    $debit_array = array('Vendor Payments','Salaries & Benefits','Rent','Taxes','Insurance','Cash Withdrawal','Card Processor Fees','Chargeback','Credit Card Payments',
            'Loan Repayment/EMI - Lenders','Loan Repayment/EMI - Mortgage','Loan Repayment/EMI - Auto Finance','Intra Account Transfer','Fees - NG',
            'Fees - Overdraft','Fees - Others','Investments','Deposited Check Return','Miscellaneous Debit','Travel Expenses - Airlines','Travel Expenses - Hotels',
            'Travel Expenses - Car Rental','Travel Expenses - Others','Utilities - Telephone','Utilities - Internet','Utilities - TV','Utilities - Power',
            'Utilities - Water','Utilities - Others'
        );
    return $debit_array;
}

function encryptionIV(){
  $iv = base64_decode("AJf3QItKM7+Lkh/BZT2xNg==");
  return $iv;
}

function encryptionkEY(){
  $key = "a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ0";
  return $key;
}



        