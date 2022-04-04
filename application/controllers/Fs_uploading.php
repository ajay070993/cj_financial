<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Fs_uploading extends CI_Controller
{
    function __construct()
    {
        Parent::__construct();
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjFinancialUser();

        $this->load->model('bank_statement_model', 'bank_statement');
        $this->load->model('banks_model', 'banks');
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Tpl_content_model', 'tpl_content');
        $this->load->model('bank_address_model', 'bank_address');
        $this->load->model('Bulk_upload_model', 'bulk_upload');
        $this->load->model('Bank_summary_level_data', 'summary_level_data');
        $this->load->model('Bank_customer_txn_data', 'customer_txn_data');
        $this->load->model('Tpl_case_error_log', 'case_error_log');
        $this->load->model('Fs_history_model', 'fs_history');
        /*For AES_encrypt*/
        // $this->load->library('encryption');

        // $this->encryption->initialize(
        //     array(
        //         'cipher' => 'aes-256',
        //         'mode' => 'ctr',
        //         'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ0'
        //     )
        // );


        // $this->load->library('email');
        // $config['protocol'] = "smtp";
        // $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        // $config['smtp_port'] = 465;
        // $config['smtp_user'] = 'nirdesh.kumawat@ollosoft.com';
        // $config['smtp_pass'] = 'N!rdesh@123';
        // $config['charset'] = "utf-8";
        // $this->email->initialize($config);

        $this->session->set_userdata(array('type_of_upload' => 1));
    }

    function index()
    {
        $output['page_title'] = '';
        $output['allBanks'] = '';
        $this->load->view('fs_uploading', $output);
    }

    function cjFinancialStatement()
    {
        error_reporting(0);
        $output['page_title'] = 'Convert File';
        $output['message'] = '';
        $extractData = array();
        // $output['message'] = "We couldn't find unique Id and business name.";
        // $output['success'] = false;
        // echo json_encode($output);die;
        $input = array();
        if (isset($_FILES['image_name']['name']) && $_FILES['image_name']['name']) {
            $directory = './assets/uploads/financial_statement';
            // @mkdir($directory, 0777);
            // @chmod($directory,  0777);
            // $config['upload_path'] = $directory;
            // $config['allowed_types'] = 'pdf|zip';
            $config['allowed_types'] = '*';
            // if (pathinfo($_FILES['image_name']['name'], PATHINFO_EXTENSION) == 'zip') {
                if (pathinfo($_FILES['image_name']['name'], PATHINFO_EXTENSION)) {
                $config['encrypt_name'] = FALSE;
                // if (!file_exists('./assets/uploads/financial_statement')) {
                    // mkdir('./assets/uploads/financial_statement', 0777, true);
                    // chmod('./assets/uploads/financial_statement',  0777);
                // }
                $date = date('d_m_Y_H_i_s');
                $directory = './assets/uploads/financial_statement/' . $date;
                // mkdir($directory, 0777, true);
                // chmod($directory,  0777);
                $uploadfile = $_FILES['image_name']['name'];
                // if (move_uploaded_file($_FILES['image_name']['tmp_name'], $directory . "/" . $uploadfile)) {
                    //echo $uploadfile;die;
                    if (strpos($uploadfile, '_') !== false) {
                        $uniqueId_businessName = explode('_', $uploadfile, 2);
                        $uniqueId = $uniqueId_businessName[0];
                        $businessName = $uniqueId_businessName[1];
                    } else {
                        $output['callBackFunction'] = 'clbckCjClrForm';
                        $output['message'] = "We couldn't find unique Id and business name.";
                        $output['success'] = false;
                        $output['zip'] = true;
                        echo json_encode($output);
                        die;
                        $this->load->view('fs_uploading', $output);
                    }
                    
                    //print_r($array);
                    if (strpos($businessName, '.') !== false) {
                        $expBusinessName =  explode('.', $businessName, 2);
                        $businessName = $expBusinessName[0];
                    }
                    /*echo $uniqueId;
                    echo $businessName;
                    die;*/
                    $historyArray = array();
                    $historyArray['original_pdf_file_name'] = $uploadfile;
                    $historyArray['created_on'] = date("Y-m-d h:i:sa");
                    $historyArray['type'] = 'multiple';
                    $historyArray['unique_id'] = $uniqueId;
                    $historyArray['business_name'] = $businessName;
                    $historyArray['upload_user_id'] = $this->session->userdata('user_id');
                    $historyArray['application_type'] = "fs";
                    // echo "<pre>";
                    // print_r($historyArray);
                    $last_id = $this->fs_history->addNewRecords($historyArray);
                    // echo $last_id;

                    $bulkUpload = array();
                    $bulkUpload['history_id'] = $last_id;
                    $bulkUpload['file_name'] = $uploadfile;
                    $bulkUpload['folder_name'] = $date;
                    $bulkUpload['email'] = $this->session->userdata('user_id');
                    $bulkUpload['status'] = '0';
                    $bulkUpload['created_on'] = date("Y-m-d h:i:sa");
                    // $bulkUpload['application_type'] = 'fs';
                    // $this->fs_history->addNewRecordsBulkUpld($bulkUpload);


                    $output['callBackFunction'] = 'clbckCjClrForm';
                    $output['message'] = "Your file has been uploaded successfully.You can check output in dashboard.";
                    $output['success'] = true;
                    $output['zip'] = true;
                    echo json_encode($output);
                    die;
                    $this->load->view('fs_uploading', $output);
                // } else {
                //     echo "There was an error uploading the file";
                // }
            } else {
                // $config['encrypt_name'] = TRUE;
                // $this->load->library('upload', $config);
                // $this->upload->initialize($config);
            }

            $uploadfile = $_FILES['image_name']['name'];
            if (strpos($uploadfile, '_') !== false) {
                $uniqueId_businessName = explode('_', $uploadfile, 2);
                $uniqueId = $uniqueId_businessName[0];
                $businessName = $uniqueId_businessName[1];
            } else {
                $output['callBackFunction'] = 'clbckCjClrForm';
                $output['message'] = "We couldn't find unique Id and business name.";
                $output['success'] = false;
                $output['zip'] = true;
                echo json_encode($output);
                die;
                $this->load->view('fs_uploading', $output);
            }
            // if ($this->upload->do_upload('image_name'))
            // {
            //     $message = "Zip file has been uploaded successfully.You can check output in dashboard after 5 minutes";
            //     $success = true;
            // }
            // else
            // {
            //     $message = $this->upload->display_errors();
            //     $success = false;
            // }
        } else {
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
        echo json_encode($output);
        die;
        //}
        // $output['allBanks'] = $this->banks->getAllBanksRecords();
        $this->load->view('fs_uploading');
    }
}
