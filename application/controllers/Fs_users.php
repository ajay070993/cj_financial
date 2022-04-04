<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Fs_users extends CI_Controller {
    function __construct() {
        Parent::__construct();  
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjFinancialUser();        
        if(!$this->common_model->checkUserPermission(18, false)){
            show_404();
        }
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('Tpl_user_model', 'tpl_user');
        $this->load->model('Fs_user_model', 'fs_user');
        $this->load->model('Fs_history_model', 'fs_history');
    }  
  
    function index() {
        $output['users'] = $this->fs_history->getAllUsers();
        // $output = array();
        $this->load->view('fs_users',$output);
    } 
    
    function addUser(){
        //print_r($_POST);die;
        $output['message']    = '';
        $output['success']    = '';
        $this->form_validation->set_rules('fname', 'First name', 'trim|required');
        $this->form_validation->set_rules('lname', 'Last name', 'trim|required');
        $this->form_validation->set_rules('user_role', 'User role', 'required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[tbl_users.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_validate_password');
        if ($this->form_validation->run()) {
            $user_data = array(); 
            $user_data['first_name'] = $this->input->post('fname');
            $user_data['last_name'] = $this->input->post('lname');
            $user_data['email'] = $this->input->post('email');
            $password = $this->input->post('password');
            $salt = 'Ijxo1A16';
            $user_data['password'] = md5(md5($password).md5($salt));
            $user_data['user_role'] = $this->input->post('user_role');
            $user_data['gender'] = $this->input->post('gender');
            $user_data['type'] = 1;
            $user_data['status'] = $this->input->post('status');
            $user_data['application_type'] = "fs";
            $this->fs_user->addNewUser($user_data);
            $message = 'Insert user successfully';
            $success = true;
            $output['callBackFunction'] = 'callBackUserList'; 
        }else {
            $success = false;
            $message = validation_errors();
        }
        //print_r($_POST);
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output); die;
        
    }
    
    function editUser(){
        $id = $this->input->post('id');
        $userDetails = $this->fs_user->editUser($id);
        echo json_encode($userDetails); die;
    }
    
    function updateUser(){
        //print_r($_POST);
        $output['message']    = '';
        $output['success']    = '';
        $this->form_validation->set_rules('fname', 'First name', 'trim|required');
        $this->form_validation->set_rules('lname', 'Last name', 'trim|required');
        $this->form_validation->set_rules('user_role', 'User role', 'required');
        if($this->input->post('password')!=""){
            $this->form_validation->set_rules('password', 'Password', 'min_length[8]|callback_validate_password');
        }
        
        if ($this->form_validation->run()) {
            $user_data = array();
            $user_data['first_name'] = $this->input->post('fname');
            $user_data['last_name'] = $this->input->post('lname');
            if($this->input->post('password')!=""){
                $password = $this->input->post('password');
                $salt = 'Ijxo1A16';
                $user_data['password'] = md5(md5($password).md5($salt));
            }
            $user_data['user_role'] = $this->input->post('user_role');
            $user_data['gender'] = $this->input->post('gender');
            // $user_data['type'] = $this->input->post('xls_format');
            $user_data['type'] = 1;
            $user_data['status'] = $this->input->post('status');
            $edit_id = $this->input->post('edit_id');
            $this->fs_user->updateRecordByUserId($edit_id,$user_data);
            $message = 'Update user successfully';
            $success = true;
            $output['callBackFunction'] = 'callBackUsers';
        }else {
            $success = false;
            $message = validation_errors();
        }
        //print_r($_POST);
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output); die;
    }
    
    function updatePassword(){
        $this->form_validation->set_rules('new_pass', 'Password', 'required|min_length[8]|callback_validate_password');
        if ($this->form_validation->run()) {
            $user_data = array();
            $p_edit_id = $this->input->post('p_edit_id');
            $password = $this->input->post('new_pass');
            $salt = 'Ijxo1A16';
            $user_data['password'] = md5(md5($password).md5($salt));
            $this->fs_user->updateRecordByUserId($p_edit_id,$user_data);
            $message = 'Password update successfully';
            $output['callBackFunction'] = 'callBackEditUser';
            $success = true;
        }else {
            $success = false;
            $message = validation_errors();
        }
        //print_r($_POST);
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output); die;
    }
    
    function  validate_password($password){
        
        if (preg_match('/[a-zA-Z]/',$password) && preg_match('/\d/',$password) && preg_match('/[^a-zA-Z\d]/',$password)) {
            return TRUE;
        }
        $this->form_validation->set_message('validate_password', 'The required password need to have atleast one special character');
        return FALSE;
        
    }
    
    function deleteUser(){
        $d_edit_id = $this->input->post('d_edit_id');
        if($d_edit_id>0){
            if($this->fs_user->deleteRecordByUserId($d_edit_id)){
                $message = 'Delete user successfully';
                $output['callBackFunction'] = 'callBackDeleteUser';
                $success = true;
            }else{
                $success = false;
                $message = 'Something went wrong';
            }
        }else{
            $success = false;
            $message = 'Something went wrong';
        }
        $output['message'] = $message;
        $output['success'] = $success;
        echo json_encode($output); die;
    }
    
    // for pagination
    function fetch_users_detail(){
        // print_r($_POST['start']);
        //die;
        //echo "hello";
        //$this->load->model("crud_model");
        $fetch_data = $this->fs_user->make_datatables();
        /*echo"<pre>";
         print_r($fetch_data);
         die;*/
        $data = array();
        $startNo = $_POST['start']+1;
        foreach($fetch_data as $key=>$value)
        {
            $sub_array = array();
            $sub_array[] = $startNo++;
            $sub_array[] = $value->first_name.' '.$value->last_name;
            $sub_array[] = $value->email;
            $sub_array[] = $value->gender;
            if($value->user_role == 4){
                $sub_array[] = "Fs-Admin";
            }
            else if($value->user_role == 5){
                $sub_array[] = "Fs-Analyst";
            }
            else if($value->user_role == 6){
                $sub_array[] = "Fs-QA";
            }else if($value->user_role == 7){
                $sub_array[] = "Fs-TL";
            }

            if($value->status=='Active'){
                $sub_array[] = 'Active';
            }else if($value->status=='Inactive'){
                $sub_array[] = 'Inactive';
            }

            if($value->user_role==4){
                $sub_array[] = '';
            }
            else{
                $sub_array[] = '<span onclick="editUser('.$value->id.')"><svg data-toggle="modal" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.119 22"><path class="a" d="M21.484 13.373a.58.58 0 00-.58.58v5.148a1.741 1.741 0 01-1.739 1.739H2.9a1.741 1.741 0 01-1.739-1.739V3.994A1.741 1.741 0 012.9 2.254h5.146a.58.58 0 100-1.159H2.9a2.9 2.9 0 00-2.9 2.9v15.106a2.9 2.9 0 002.9 2.9h16.265a2.9 2.9 0 002.9-2.9v-5.148a.58.58 0 00-.581-.58zm0 0"></path><path class="a" d="M9.065 10.323l8.465-8.465 2.73 2.73-8.462 8.465zm0 0M7.684 14.434l3.017-.836-2.181-2.181zm0 0M21.014.423a1.451 1.451 0 00-2.05 0l-.615.615 2.73 2.73.615-.615a1.451 1.451 0 000-2.05zm0 0"></path></svg></span>';
            }
            
            if($value->user_role==4){
                $sub_array[] = '';
            }
            else{
                $sub_array[] = '<span onclick="deleteUser('.$value->id.')" title="delete"><svg class="delete" data-toggle="modal" viewBox="-40 0 427 427.00131" xmlns="http://www.w3.org/2000/svg"><path d="m232.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/><path d="m114.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/><path d="m28.398438 127.121094v246.378906c0 14.5625 5.339843 28.238281 14.667968 38.050781 9.285156 9.839844 22.207032 15.425781 35.730469 15.449219h189.203125c13.527344-.023438 26.449219-5.609375 35.730469-15.449219 9.328125-9.8125 14.667969-23.488281 14.667969-38.050781v-246.378906c18.542968-4.921875 30.558593-22.835938 28.078124-41.863282-2.484374-19.023437-18.691406-33.253906-37.878906-33.257812h-51.199218v-12.5c.058593-10.511719-4.097657-20.605469-11.539063-28.03125-7.441406-7.421875-17.550781-11.5546875-28.0625-11.46875h-88.796875c-10.511719-.0859375-20.621094 4.046875-28.0625 11.46875-7.441406 7.425781-11.597656 17.519531-11.539062 28.03125v12.5h-51.199219c-19.1875.003906-35.394531 14.234375-37.878907 33.257812-2.480468 19.027344 9.535157 36.941407 28.078126 41.863282zm239.601562 279.878906h-189.203125c-17.097656 0-30.398437-14.6875-30.398437-33.5v-245.5h250v245.5c0 18.8125-13.300782 33.5-30.398438 33.5zm-158.601562-367.5c-.066407-5.207031 1.980468-10.21875 5.675781-13.894531 3.691406-3.675781 8.714843-5.695313 13.925781-5.605469h88.796875c5.210937-.089844 10.234375 1.929688 13.925781 5.605469 3.695313 3.671875 5.742188 8.6875 5.675782 13.894531v12.5h-128zm-71.199219 32.5h270.398437c9.941406 0 18 8.058594 18 18s-8.058594 18-18 18h-270.398437c-9.941407 0-18-8.058594-18-18s8.058593-18 18-18zm0 0"/><path d="m173.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/></svg></span>';
            }
            
            
            $data[] = $sub_array;
        }
        $output = array(
            "draw"                    =>     intval($_POST["draw"]),
            "recordsTotal"          =>      $this->fs_user->get_all_data(),
            "recordsFiltered"     =>     $this->fs_user->get_filtered_data(),
            "data"                    =>     $data
        );
        echo json_encode($output);
        // print_r($output);
        // die;
        
    }
    
}