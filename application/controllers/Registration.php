<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Registration extends CI_Controller {
	
	function __construct() {
		Parent::__construct();
        $this->common_model->checkUseAlreadyLogin();
        $this->load->model('auth_model', 'auth');
        $this->load->helper('string_helper');
        
        $this->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_port'] = 465;
        $config['smtp_user'] = 'nirdesh.kumawat@ollosoft.com';
        $config['smtp_pass'] = 'N!rdesh@123';
        $config['charset'] = "utf-8";
        $this->email->initialize($config);
        $this->mailtype = 'html';
        $this->smtpUser = 'nirdesh.kumawat@ollosoft.com';
	}

  function login() {
    $output['page_title'] = 'Login';            
    if (isset($_POST) && !empty($_POST)) {
        $success = true;
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
        // $this->form_validation->set_rules('g-recaptcha-response', 'recaptcha validation', 'required|callback_validate_captcha');
        // $this->form_validation->set_message('validate_captcha', 'Please check the captcha form');
        if ($this->form_validation->run()) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $salt = 'Ijxo1A16';
            $ency_password = md5(md5($password).md5($salt));
            $users = $this->auth->checkValidUser($email,$ency_password);    
            if($users) {
                if($users->status == 'Active') {
                    $this->session->set_userdata(array('user_id' => $users->id,'email' => $users->email,'username' => $users->username,'type' => $users->type, 'user_role' => $users->user_role, 'application_type' => $users->application_type));

                    if($users->application_type=='fs'){
                      $output['redirectURL'] = site_url('fs-dashboard'); 
                    } 
                    else{
                      $output['redirectURL'] = site_url(); 
                    }                                
                    
                    $success = true;
                    $message = "Login successfully";
                }
                else {
                    $success = false;
                    $message = 'Your account not active. Please contact to team';
                }                                   
            }
            else {
                $success = false;
                $message = 'Incorrect login credentials';
            }
        }
        else {
            $success = false;
            $message = validation_errors();
        }
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output); die;
    }
    $this->load->view('login');
  }

  function forgotPassword() {
      $success = true;
      $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|min_length[3]');
	  
      if ($this->form_validation->run()) {
          $exists = $this->auth->username_exists($this->input->post('email'));
          
          $count = count($exists);
          if (!empty($count)) {
              $content = "Dear ".$exists->first_name.",\n\n";
			  $content .= '<br /><br />';
			  $content .= '<div style="font-weight:bold;">';
              $content .= "We recommend you to change the password as soon as possible. This is a one time use link and will expire in the next 24 hours. You will have to request for change the password again if not used within 24 hours.\n";
			  $content .= '<br />';
              $content .= "Below is the link to change password:\n";
			  $content .= '</div>';
			  $forgot_password_key = md5(str_shuffle($exists->email).date('m/d/Y h:i:s a', time()));
              $updateData = array();
              $updateData['forgot_password_key'] = $forgot_password_key;
              $updateData['expire_time'] = date('Y-m-d H:i:s', time());
              $updateData['is_expire'] = 1;
              $this->auth->updateUserForgotKeyByEmailid($exists->id,$updateData);
			  $content .=  '<span>Link&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
              $content .=base_url().'forget-password/'.$forgot_password_key;
			  $content .= '<br /><br />';
			  $content .= "Regards,";
			  $content .= '<br />';
			  $content .= "BLUCOGNITION";
			  if($this->sendEmail($exists->email,$content,'Forget password')){
                  $success = true;
                  $message = "Reset password email sent to your email id.Please check";
              }else{
                  $success = false;
                  $message = "Something went wrong";  
              }
          } else {
              $success = false;
              $message = "Please input valid username";
          }
          
      }else {
          $success = false;
          $message = validation_errors();
      }
      $output['message'] = $message;
      $output['success'] = $success;
      echo json_encode($output); die;
      
  }
  function validate_captcha() {
        $captcha = $this->input->post('g-recaptcha-response');
         $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc-qfUUAAAAAJKcAKzT5Ui7Vi5AUXK0tGXcqZuU=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        if ($response . 'success' == false) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
	/*function validate_captcha() {
        $recaptcha = trim($this->input->post('g-recaptcha-response'));
        $userIp= $_SERVER['REMOTE_ADDR'];
        
        $secret='6Lc-qfUUAAAAAJKcAKzT5Ui7Vi5AUXK0tGXcqZuU';
        $data = array(
            'secret' => "$secret",
            'response' => "$recaptcha",
            'remoteip' =>"$userIp"
        );

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret=6Lc-qfUUAAAAAJKcAKzT5Ui7Vi5AUXK0tGXcqZuU=");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $status= json_decode($response, true);
        if(empty($status['success'])){
            return FALSE;
        }else{
            return TRUE;
        }
    }*/
  
  function forgetPassword($params=''){
      $forgot_password_key = $this->uri->segment(2);
      $result = $this->auth->checkForgetKey($forgot_password_key);
      //echo"<pre>";
      //print_r($result);
      
      if(count($result)==1){
          $result = $result[0];
          //echo date('Y-m-d H:i:s', time())."<br/>";
          //echo $result->expire_time."<br/>";
          //echo round((strtotime(date('Y-m-d h:i:s a', time())) - strtotime($result->expire_time))/3600, 1);
          if(round((strtotime(date('Y-m-d h:i:s a', time())) - strtotime($result->expire_time))/3600, 1)<24 && $result->is_expire==1){
              $updateData = array();
              $updateData['is_expire'] = 2;
              if($this->auth->updatePassword($forgot_password_key,$updateData)){
                  $output['page_title'] = 'Forget pasword';
                  $output['forgot_password_key'] = $forgot_password_key;
                  $this->load->view('forget-password.php',$output);
              }else{
                  die('Something went wrong.');
              }
              
          }else{
              die('Token has been expired.Please generate token again for reset password');
          }
      }else{
          die('invalid token');
      }
   
  }
  
  function updatePassword(){
      $this->load->helper('security');
      $success = true;
      $message = "";
      $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|callback_validate_password');
      $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
      $this->form_validation->set_rules('g-recaptcha-response', 'recaptcha validation', 'required|callback_validate_captcha');
      $this->form_validation->set_message('validate_captcha', 'Please check the captcha form');
      if ($this->form_validation->run()) {
          $forgot_password_key = $this->input->post('forgot_password_key');
          $result = $this->auth->checkForgetKey($forgot_password_key);
          //print_r($result);
          //die;
          if(count($result)==1){
              $result = $result[0];
              if(round((strtotime(date('Y-m-d h:i:s a', time())) - strtotime($result->expire_time))/3600, 1)<24){
                  $salt = 'Ijxo1A16';
                  $updateData = array();
                  $updateData['password'] = md5(md5($this->input->post('password')).md5($salt));
                  $updateData['forgot_password_key'] = '';
                  if($this->auth->updatePassword($forgot_password_key,$updateData)){
                      $success = true;
                      $message = "Succesfully changed password.";
                      $output['redirectURL'] = site_url();
                      //echo json_encode($output); die;
                  }
              }else{
                  $success = false;
                  $message = 'Token has been expired.Please generate token again for reset password';
              }
          }else{
              $success = false;
              $message = "Invalid token.";
          }
      }else{
          $success = false;
          $message = validation_errors();
      }
      $output['message'] = $message;
      $output['success'] = $success;
      echo json_encode($output); die;
  }
  
  function sendEmail($emailAddress,$content,$subject) {
      //error_reporting(0);
      $this->email->from($this->smtpUser,'BSS');
      $this->email->to($emailAddress);
      $this->email->mailtype = $this->mailtype;
      $this->email->set_newline("\r\n");
      $this->email->subject($subject);
      $this->email->message($content);
      if($this->email->send()){
            return true;
      }else{
          return false;
      }
  }
  
  function  validate_password($password){

        if (preg_match('/[a-zA-Z]/',$password) && preg_match('/\d/',$password) && preg_match('/[^a-zA-Z\d]/',$password)) {
          return TRUE;
      }
	  $this->form_validation->set_message('validate_password', 'The required password need to have atleast one special character');
      return FALSE;

  }
  
  
}