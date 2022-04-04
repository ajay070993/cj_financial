<?php

class Common_Model extends CI_Model {

	function __construct() {
		global $URI, $CFG, $IN;
        $ci = get_instance(); 
        $ci->load->config('config');
        $this->setSiteConfigData();
        $this->setMemberConfigData();
        $this->setLocalTimeZone();
        $this->load->model('bank_address_model', 'bank_address');
        
	}

	function setSiteConfigData() {
		$this->config->set_item('per_page',20);
		$this->config->set_item('per_page_front',4);
	}

	function setMemberConfigData() {
		if($this->session->userdata('user_id'))
		{
			$userData = $this->getUserDataById($this->session->userdata('user_id'));
			if($userData)
			{
				$this->config->set_item('first_name',$userData->first_name);
			}
		}
	}

	function setLocalTimeZone() {	
		if(!$this->session->userdata('local_time_zone'))
		{
			
			$timezone = 'Asia/Kolkata';
			
			$this->session->set_userdata('local_time_zone',$timezone);
			
		}
		date_default_timezone_set($this->session->userdata('local_time_zone'));
	}

	function checkRequestedDataExists($data) {
		if(!$data) {
			show_404();
		}
		return true;
	}

	function createSlugForTable($title,$table) {
		$slug = url_title($title);
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params['slug'] = $slug;
		while ($this->db->where($params)->get($table)->num_rows()) {
			if (!preg_match ('/-{1}[0-9]+$/', $slug )) {
				$slug .= '-' . ++$i;
			} else {
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
			}
			$params ['slug'] = $slug;
		}
		return $slug;
	}  

	function getDefaultToGMTTime($time) {
		$gmtTime = local_to_gmt($time);
		return $gmtTime;
	}

	function getDefaultToGMTDate($time,$format = 'Y-m-d H:i:s A') {
		$gmtTime = local_to_gmt($time);
		return date($format,$gmtTime);
	}

	function getGMTDateToLocalDate($date, $format = 'Y-m-d H:i:s') {
		$date = new DateTime($date, new DateTimeZone('GMT'));
		$date->setTimeZone(new DateTimeZone($this->session->userdata('local_time_zone')));
		return $date->format($format);
	}

	function showLimitedText($string,$len) {
		$string = strip_tags($string);
		if (strlen($string) > $len)
			$string = mb_substr($string, 0, $len-3) . "...";
		return $string;
	}
  
	function checkUseAlreadyLogin() {
		$userId = $this->session->userdata('user_id');
		if($userId) {
			redirect('');
		} else {
			return false;
		}
	}

	function getUserDataById($user_id) {
		$this->db->where('id',$user_id);
		$this->db->where('status','Active');
		$query = $this->db->get('tbl_users');
		$result = $query->row();
		return $result;
	}

  	function checkUserLogin() {
		if($this->session->userdata('user_id')) {
			return true;
		} else {
			if($this->input->is_ajax_request()) {
				$data['success'] = false;
				$data['message'] = 'Please login first';
				$data['error_type'] = 'auth';
				echo json_encode($data); die;
			} else {
				redirect('login');				
			}
		}
	}

	function checkCjFinancialUser() {
		if($this->session->userdata('application_type') == 'fs') {
			return true;
		} else {
			if($this->input->is_ajax_request()) {
				$data['success'] = false;
				$data['message'] = 'Please login first';
				$data['error_type'] = 'auth';
				echo json_encode($data); die;
			} else {
				// redirect('login');
				show_404();
			}
		}
	}

	
	function checkCjXtractUser() {
		if($this->session->userdata('application_type') == 'bs') {
			return true;
		} else {
			if($this->input->is_ajax_request()) {
				$data['success'] = false;
				$data['message'] = 'Please login first';
				$data['error_type'] = 'auth';
				echo json_encode($data); die;
			} else {
				// redirect('fs-dashboard');
				show_404();
			}
		}
	}
	


	function checkLoginUserStatus() {
		$user = $this->getUserDataById($this->session->userdata('user_id'));
		if($user) {

		} else {
			$this->session->sess_destroy();
			redirect('login');
			return false;
		}
	}

	function getUserRoleByUserId() {
		$user_id   =  $this->session->userdata('user_id');
		$this->db->select('id,user_role');
		$this->db->where('id',$user_id);
		$query = $this->db->get('tbl_users');
		$result = $query->row();
		return $result;
	}

	function checkUserPermission($permission_id,$no_return = true) {	    
		$user = $this->getUserRoleByUserId();			
		$getPermissionByType = $this->getUserPermission($user->user_role);		
		$userPermissions = explode(",",$getPermissionByType->permissions);        
		if(in_array($permission_id, $userPermissions))
		{			
			return true;
		}
		else
		{			    	
			if($no_return)
			{
				if($this->input->is_ajax_request()) {
					$data['success'] = false;
					$data['message_title'] = 'Permissions Denied';
					$data['message'] = 'Sorry You are now allowed to access this feature';
					$data['error_type'] = 'auth';
					echo json_encode($data); die;
				} else {
					echo '<h1 align="center">Sorry You are now allowed to access this feature</h1>'; die;
				}
			}
			else
			{				
				return false;
			}
		}
	}

	function getUserPermission($type) {
		$this->db->select('*');
		$this->db->where('user_role',$type);
		$query = $this->db->get('tbl_user_permissions');
		$result = $query->row();
		return $result;
	}
	
	function getExtractDataRegx($string_record,$txtFilename,$bank_id){
	    /*echo"<pre>";
	     print_r($string_record);
	     echo "</pre>";
	     die('heer');*/
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
	        }elseif($bank_id==187){
	            $changeDateArray = array(
	                "    ENE "=>"/01     ","    FEB "=>"/02     ","    MAR "=>"/03     ","    MZO "=>"/03     ","    ABR "=>"/04     ","    MAY "=>"/05     ",
	                "    JUN "=>"/06     ","    JUL "=>"/07     ","    AGO "=>"/08     ","    SEP "=>"/09     ","    OCT "=>"/10     ","    NOV "=>"/11     ","    DIC "=>"/12     ",
	                "   ENE "=>"/01    ","   FEB "=>"/02    ","   MAR "=>"/03    ","   MZO "=>"/03    ","   ABR "=>"/04    ","   MAY "=>"/05    ",
	                "   JUN "=>"/06    ","   JUL "=>"/07    ","   AGO "=>"/08    ","   SEP "=>"/09    ","   OCT "=>"/10    ","   NOV "=>"/11    ","   DIC "=>"/12    ",
	                "  ENE "=>"/01   ","  FEB "=>"/02   ","  MAR "=>"/03   ","  MZO "=>"/03   ","  ABR "=>"/04   ","  MAY "=>"/05   ",
	                "  JUN "=>"/06   ","  JUL "=>"/07   ","  AGO "=>"/08   ","  SEP "=>"/09   ","  OCT "=>"/10   ","  NOV "=>"/11   ","  DIC "=>"/12  ",
	                " ENE "=>"/01  "," FEB "=>"/02  "," MAR "=>"/03  "," MZO "=>"/03  "," ABR "=>"/04  "," MAY "=>"/05  ",
	                " JUN "=>"/06  "," JUL "=>"/07  "," AGO "=>"/08  "," SEP "=>"/09  "," OCT "=>"/10  "," NOV "=>"/11  "," DIC "=>"/12  ",
	            );
	        }elseif($bank_id==186){
	            $changeDateArray = array("$ "=>"","a|"=>"al","WA."=>"I.V.A.","|.V.A."=>"I.V.A."," ."=>".","21 9,3305"=>"21933.05","21 92.505"=>"21925.05","21 ,.92377"=>"21923.77",
	                "21 ,.62377"=>"21623.77","21 ,5.7577"=>"21575.77","19,.97577"=>"19975.77"
	            );
	        }else{
	            $changeDateArray = array("-$ "=>"-","+$ "=>"","/ENE"=>"/01","/FEB"=>"/02","/MAR"=>"/03","/MZO"=>"/03","/ABR"=>"/04","/MAY"=>"/05","/JUN"=>"/06","/JUL"=>"/07","/AGO"=>"/08",
	                "/SEP"=>"/09","/OCT"=>"/10","/NOV"=>"/11","/DIC"=>"/12","-ENE-"=>"-01-","-FEB-"=>"-02-","-MAR-"=>"-03-","-MZO-"=>"-03-","-ABR-"=>"-04-",
	                "-MAY-"=>"-05-","-JUN-"=>"-06-","-JUL-"=>"-07-","-AGO-"=>"-08-","-SEP-"=>"-09-","-OCT-"=>"-10-","-NOV-"=>"-11-","-DIC-"=>"-12-","/Enero/"=>"/01/",
	                "/Febrero/"=>"/02/","/Marzo/"=>"/03/","/Abril/"=>"/04/","/Mayo/"=>"/05/","/Junio/"=>"/06/","/Julio/"=>"/07/","/Agosto/"=>"/08/","/Septiembre/"=>"/09/",
	                "/Octubre/"=>"/10/","/Noviembre/"=>"/11/","/Diciembre/"=>"/12/"," ENE "=>"/01 "," FEB "=>"/02 ", " MAR "=>"/03 ", " MZO "=>"/03 "," ABR "=>"/04 "," MAY "=>"/05 ",
	                " JUN "=>"/06 "," JUL "=>"/07 "," AGO "=>"/08 "," SEP "=>"/09 "," OCT "=>"/10 "," NOV "=>"/11 "," DIC "=>"/12 ","-Ene-"=>"-01-","-Feb-"=>"-02-","-Mar-"=>"-03-",
	                "-Mzo-"=>"-03-","-Abr-"=>"-04-","-May-"=>"-05-","-Jun-"=>"-06-","-Jul-"=>"-07-","-Ago-"=>"-08-","-Sep-"=>"-09-","-Oct-"=>"-10-","-Nov-"=>"-11-","-Dic-"=>"-12-"
	            );
	        }
	        
	        /* echo"<pre>";
	        print_r($changeDateArray); */
	        //die('here');
	        
	        if($bank_id==182){
	            $currencyChangeArray = array(" USD"=>"");
	            $changeDateArray = array_merge($changeDateArray, $currencyChangeArray);
	        }
	        /* echo"<pre>";
	        print_r($changeDateArray);
	        die('here'); */
	        /*if($bank_id==186){
	            //print_r($changeDateArray);
	            $monthNameArray = array(
	                "$ "=>""
	            );
	            $changeDateArray = array_merge($changeDateArray, $monthNameArray);
	            //print_r($changeDateArray);die;
	        }*/
	        
	        
	        
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
	    /*echo $contents;
	    die('here');*/
	    if($bank_id==179){
	        //echo $txtFilename;die;
	        $file_number = 1;
	        if (strpos($txtFilename, '_multiple_') !== false) {
    	        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $txtFilename);
    	        $file_number = $withoutExt[strlen($withoutExt)-1];
	        }
	        //echo $file_number;die;
	        $handle = @fopen($actualFilePath, "r");
	        $accStart = false;
	        $increment = 0;
	        while ($line = fgets($handle)) {
	            $increment++;
	            $lineBreakArray = array_map('trim', array_filter(explode("  ",$line)));
	            if($accStart==true && $getacc==$increment){
	                $x = 0;
	                foreach($lineBreakArray as $value){
	                    if($x==0){
	                        $extractData['account_type'] = trim($value);
	                    }
	                    if($x==1){
	                        $extractData['account_number_string'] = trim($value);
	                    }
	                    if($x==3){
	                        $extractData['begining_balance'] = trim($value);
	                    }
	                    if($x==4){
	                        $extractData['closing_balance'] = trim($value);
	                    }
	                    $x++;   	                    
	                }
	                break;
	            }
	            if(in_array('RESUMEN INTEGRAL',$lineBreakArray)){
	                $accStart = true;
	                $getacc = $increment;
	                $getacc = $getacc+$file_number+1;
	            }
	            
	            /*if(in_array('TOTAL',$lineBreakArray)){
	                die('here');
	            }*/
	        }
	    }
	    //die('here');
	    foreach($results as $result){
	        if(preg_match_all('/'.$result->bank_address.'/', $contents, $matches)){
	            $extractData['bank_address'] = $result->bank_address;
	            $extractData['bank_city'] = $result->bank_city;
	            $extractData['bank_state'] = $result->bank_state;
	            $extractData['bank_zip'] = $result->bank_zip;
	        }
	    }
	    
	    if($bank_id!=179){
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
	    
	    if($bank_id!=179){
    	    $extractData['account_number_string'] = '';
    	    if(isset($string_record->account_number_string)){
    	        $array = explode("|",$string_record->account_number_string);
    	        foreach($array as $arr){
    	            if(strpos($arr,"[rgx]")!==false){
    	                $account_number_string = substr($arr,5,-6);
    	                if(preg_match_all($account_number_string, $contents, $matches)){
    	                    if($matches[1][0]){
    	                        /*if (strpos($matches[1][0], 'X') !== false || strpos($matches[1][0], 'x') !== false) {
    	                         $extractData['account_number_string'] = $matches[1][0];
    	                         }else{
    	                         $extractData['account_number_string'] = filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    	                         }*/
    	                        $extractData['account_number_string'] = filter_var($matches[1][0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    	                    }
    	                    break;
    	                }
    	            }
    	        }
    	    }
	    }
	    
	    
	    if($bank_id!=179){
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
	    }
	    
	    
	    if($bank_id!=179){
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
	                                " NOVIEMBRE"=>"/11"," DICIEMBRE"=>"/12"," AL "=>"");
	                            
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
	                        
	                        if($bank_id==186){
	                            $extractData['end_date'] = trim($matches[1][0]);
	                            $expDate = explode("/",$matches[1][0]);
	                            if(count($expDate)==3){
	                                $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
	                            }
	                            if(count($expDate)==2){
	                                $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
	                            }
	                            $extractData['end_date'] = $matches[1][0];
	                            break;
	                        }
	                        
	                        if($bank_id==187){
	                            $changeDateString = array("DEL"=>"","DE"=>"","ENERO"=>"/01/","FEBRERO"=>"/02/","MARZO"=>"/03/","ABRIL"=>"/04/","MAYO"=>"/05/",
	                                "JUNIO"=>"/06/","JULIO"=>"/07/","AGOSTO"=>"/08/","SEPTIEMBRE"=>"/09/","OCTUBRE"=>"/10/",
	                                "NOVIEMBRE"=>"/11/","DICIEMBRE "=>"/12/");
	                            foreach($changeDateString as $key=>$date){
	                                $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
	                            }
	                            $matches[1][0] = str_replace(' ', '', $matches[1][0]);
	                            //echo $matches[1][0];die;
	                            $citiBanamexYear = '';
	                            if(strpos($matches[1][0],'/') !== false) {
	                                $expDate = explode("/",$matches[1][0]);
	                                if(count($expDate)==3){
	                                    $matches[1][0] = trim($expDate[1]).'/'.trim(sprintf("%02d", $expDate[0])).'/'.trim($expDate[2]);
	                                    $citiBanamexYear = $expDate[2];
	                                }
	                                if(count($expDate)==2){
	                                    $matches[1][0] = trim($expDate[1]).'/'.trim(sprintf("%02d", $expDate[0]));
	                                }
	                            }
	                            $extractData['end_date'] = $matches[1][0];
	                            break;
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
	                                " NOVIEMBRE"=>"/11"," DICIEMBRE"=>"/12"," AL "=>"","al"=>"");
	                            
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
	                        
	                        if($bank_id==186){
	                            $extractData['start_date'] = trim($matches[1][0]);
	                            $matches[1][0] = str_replace(' ', '', $matches[1][0]);
	                            $expDate = explode("/",$matches[1][0]);
	                            if(count($expDate)==3){
	                                $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]).'/'.trim($expDate[2]);
	                            }
	                            if(count($expDate)==2){
	                                $matches[1][0] = trim($expDate[1]).'/'.trim($expDate[0]);
	                            }
	                            $extractData['start_date'] = $matches[1][0];
	                            break;
	                        }
	                        
	                        if($bank_id==187){
	                            //echo $matches[1][0];
	                            $changeDateString = array("DEL"=>"","DE"=>"","ENERO"=>"/01/","FEBRERO"=>"/02/","MARZO"=>"/03/","ABRIL"=>"/04/","MAYO"=>"/05/",
	                                "JUNIO"=>"/06/","JULIO"=>"/07/","AGOSTO"=>"/08/","SEPTIEMBRE"=>"/09/","OCTUBRE"=>"/10/",
	                                "NOVIEMBRE"=>"/11/","DICIEMBRE "=>"/12/");
	                            foreach($changeDateString as $key=>$date){
	                                $matches[1][0] = str_ireplace($key,$date,$matches[1][0]);
	                            }
	                            $matches[1][0] = str_replace(' ', '', $matches[1][0]);
	                            //echo $matches[1][0];die('here');
	                            if(strpos($matches[1][0],'/') !== false) {
	                                $expDate = explode("/",$matches[1][0]);
	                                if($expDate[2]=="" || $expDate[2]==" "){
	                                    $matches[1][0] = trim($expDate[1]).'/'.trim(sprintf("%02d", $expDate[0])).'/'.$citiBanamexYear;
	                                }else{
	                                    $matches[1][0] = trim($expDate[1]).'/'.trim(sprintf("%02d", $expDate[0])).'/'.trim($expDate[2]);
	                                }
	                            }
	                            $extractData['start_date'] = $matches[1][0];
	                            break;
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
	                            
	                            /*$format = "m/d/Y";
	                             $date1  = \DateTime::createFromFormat($format, $extractData['start_date']);
	                             $date2  = \DateTime::createFromFormat($format, $extractData['end_date']);
	                             
	                             var_dump($date1 < $date2);*/
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
	    
	    /*echo"<pre>";
	    print_r($extractData);
	    die('here');*/
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
	    
	    
	    
	    /*echo"<pre>";
	     print_r($extractData);
	     echo"</pre>";
	     die('here');*/
	    return $extractData;
	}

}