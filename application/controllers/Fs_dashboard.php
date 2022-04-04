<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Fs_dashboard extends CI_Controller
{
    function __construct()
    {
        Parent::__construct();
        $this->common_model->checkUserLogin();
        $this->common_model->checkLoginUserStatus();
        $this->common_model->checkCjFinancialUser();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('Tpl_history_model', 'tpl_history');
        $this->load->model('Bank_summary_level_data', 'bank_summary_level_data');
        $this->load->model('banks_model', 'banks');
        $this->load->model('Case_error_log_model', 'case_error_log');
        $this->load->model('Bank_customer_txn_data', 'bank_customer_txn_data');
        $this->load->model('Tpl_user_model', 'tpl_user');
        $this->load->model('Fs_history_model', 'fs_history');
        $this->load->library('encryption');
        $this->encryption->initialize(
            array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'a6bcv1fQchVxZ!N4Wu2Kl51yS40mmmZ01wrr'
            )
        );
    }

    function index()
    {
        // $output['countTemplate'] = $this->banks->countTemplate();
        // $output['countSpreading'] = $this->tpl_history->countSpreading();
        // $output['countLastWeek'] = $this->tpl_history->countLastWeek();
        // $output['avgSpreadTime'] = $this->tpl_history->avgSpreadTime();
        if ($this->session->userdata('application_type') == 'fs') {
            $this->load->view('fs_dashboard');
        }
    }

    function url_encrypt($id) {
        // return $id; str_replace("%2f", "__", $id)
        // echo urlencode(base64_encode(openssl_encrypt(("123"), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV())));
    return str_replace("%2F", "__", urlencode(base64_encode(openssl_encrypt(($id), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV()))));

}

function url_decrypt($id){
    // return $id;
// echo openssl_decrypt((base64_decode(urldecode("%2FRtAFnoWAARcFFEa5iiXzQ%3D%3D"))), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());
return openssl_decrypt((base64_decode(urldecode(str_replace("__", "%2F", $id)))), "AES-256-CBC", encryptionkEY(), OPENSSL_RAW_DATA, encryptionIV());

}
    function assignAnalyst(){
        $analyst_id =  $_POST['analyst_id'];
        $case_ids = $_POST['case_ids'];
        $data = array (
            analyst_id => $analyst_id
        );
        $this->fs_history->updateAnalystinCases($case_ids,$data);
        $output['message'] = "Analyst Asigned";
        $output['success'] = true;
        echo json_encode($output);
        
    }
    function getTabularFormatData($id){
        // $id = $this->url_decrypt($id);
        $output['businessNameInfo'] = $this->fs_history->fsHistoryBizName($id);
        $output['excelData'] =  $this->fs_history->getCjFinancialData($id);
        // $output['id'] = $id;
        // $output['lastUpdatedTime'] = $this->fs_history->lastUpdatedTime($id);
        // echo "<pre>";
        // print_r($excelData);die;
        // $this->load->view('fs_excelsheet', $output);

        echo json_encode($output);
    }

    function show_excelsheet($id)
    {
        $id = $this->url_decrypt($id);

        $output['businessNameInfo'] = $this->fs_history->fsHistoryBizName($id);
        $output['excelData'] =  $this->fs_history->getCjFinancialData($id);
        $output['blucogComments'] =  $this->fs_history->getBluCogCmnts($id);
        $output['id'] = $id;
        $output['lastUpdatedTime'] = $this->fs_history->lastUpdatedTime($id);

        $output['signals'] = $this->fs_history->getSignalsData($id);
        // echo "<pre>";
        // print_r($output['signals']);die;
        $this->load->view('fs_excelsheet', $output);
    }
    // h3m5
    
    function show_precheckFrom($id)
    {
        $id = $this->url_decrypt($id);

        $output['businessNameInfo'] = $this->fs_history->fsHistoryBizName($id);
        $output['excelData'] =  $this->fs_history->getCjFinancialData($id);
        $output['blucogComments'] =  $this->fs_history->getBluCogCmnts($id);
        $output['id'] = $id;
        $output['lastUpdatedTime'] = $this->fs_history->lastUpdatedTime($id);

        $output['signals'] = $this->fs_history->getSignalsData($id);
        // echo "<pre>";
        // print_r($output['signals']);die;
        $this->load->view('preCheckForm', $output);
    }
    

    function importExcelData()
    {
        $message = "";
        $success = false;
        // echo pathinfo($_FILES['imprtExcelFile']['name'], PATHINFO_EXTENSION);
        // die;

        if (isset($_FILES['imprtExcelFile']['name']) && $_FILES['imprtExcelFile']['name']) {

            if (pathinfo($_FILES['imprtExcelFile']['name'], PATHINFO_EXTENSION) === "xlsx") {

                $objPHPExcel = PHPExcel_IOFactory::load($_FILES['imprtExcelFile']['tmp_name']);
                // echo "<pre>";
                // print_r($objPHPExcel);
                $editIndexArray = array('D', 'F', 'H', 'J', 'L');

                if ($objPHPExcel) {
                    // echo 
                    $objPHPExcel->setActiveSheetIndex(0);

                    $unique_id = $objPHPExcel->getActiveSheet()->getCell('B2')->getValue();
                    $business_name = $objPHPExcel->getActiveSheet()->getCell('C2')->getValue();

                    $excelData = array();
                    $i = 0;
                    foreach ($editIndexArray as $cell) {
                        // echo "01-".explode("-", $excelDataArray[$i]['conf_sqr_amt'])[1] ."-". explode("-", $excelDataArray[$i]['conf_sqr_amt'])[0];
                        // activo table
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "5", "01-" . explode("-", $excelDataArray[$i]['conf_sqr_amt'])[1] . "-" . explode("-", $excelDataArray[$i]['conf_sqr_amt'])[0]);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "6", $excelDataArray[$i]['is_audited']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "7", $excelDataArray[$i]['audit_firm_name']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "8", $excelDataArray[$i]['audit_opinion']);

                        // $excelData[$i]['conf_sqr_amt'] = $objPHPExcel->getActiveSheet()->getCell($cell.'5')->getValue();
                        $excelData[$i]['conf_sqr_amt'] = gmdate("Y-m", ($objPHPExcel->getActiveSheet()->getCell($cell . '5')->getValue() - 25569) * 86400);
                      
                        $excelData[$i]['is_audited'] = $objPHPExcel->getActiveSheet()->getCell($cell . '6')->getValue();
                        $excelData[$i]['audit_firm_name'] = $objPHPExcel->getActiveSheet()->getCell($cell . '7')->getValue();
                        $excelData[$i]['audit_opinion'] = $objPHPExcel->getActiveSheet()->getCell($cell . '8')->getValue();


                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "15", $excelDataArray[$i]['cash_and_banks']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "17", $excelDataArray[$i]['customers']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "18", $excelDataArray[$i]['various_debtors']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "19", $excelDataArray[$i]['inventories']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "20", $excelDataArray[$i]['related_parties']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "21", $excelDataArray[$i]['taxes_to_be_recovered']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "22", $excelDataArray[$i]['projects_in_process']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "23", $excelDataArray[$i]['advances_to_suppliers']);
                        $excelData[$i]['cash_and_banks'] = $objPHPExcel->getActiveSheet()->getCell($cell . '15')->getValue();
                        $excelData[$i]['customers'] = $objPHPExcel->getActiveSheet()->getCell($cell . '17')->getValue();
                        $excelData[$i]['various_debtors'] = $objPHPExcel->getActiveSheet()->getCell($cell . '18')->getValue();
                        $excelData[$i]['inventories'] = $objPHPExcel->getActiveSheet()->getCell($cell . '19')->getValue();
                        $excelData[$i]['related_parties'] = $objPHPExcel->getActiveSheet()->getCell($cell . '20')->getValue();
                        $excelData[$i]['taxes_to_be_recovered'] = $objPHPExcel->getActiveSheet()->getCell($cell . '21')->getValue();
                        $excelData[$i]['projects_in_process'] = $objPHPExcel->getActiveSheet()->getCell($cell . '22')->getValue();
                        $excelData[$i]['advances_to_suppliers'] = $objPHPExcel->getActiveSheet()->getCell($cell . '23')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "25", $excelDataArray[$i]['other_non_current_assets']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "26", $excelDataArray[$i]['accounts_receivable_lp']);
                        $excelData[$i]['other_non_current_assets'] = $objPHPExcel->getActiveSheet()->getCell($cell . '25')->getValue();
                        $excelData[$i]['accounts_receivable_lp'] = $objPHPExcel->getActiveSheet()->getCell($cell . '26')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "28", $excelDataArray[$i]['land_real_estate']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "29", $excelDataArray[$i]['machinery_equipment']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "30", $excelDataArray[$i]['transportation_equipment']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "31", $excelDataArray[$i]['office_team']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "32", $excelDataArray[$i]['computer_equipment']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "33", $excelDataArray[$i]['accumulated_depreciation']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "34", $excelDataArray[$i]['other_assets']);
                        $excelData[$i]['land_real_estate'] = $objPHPExcel->getActiveSheet()->getCell($cell . '28')->getValue();
                        $excelData[$i]['machinery_equipment'] = $objPHPExcel->getActiveSheet()->getCell($cell . '29')->getValue();
                        $excelData[$i]['transportation_equipment'] = $objPHPExcel->getActiveSheet()->getCell($cell . '30')->getValue();
                        $excelData[$i]['office_team'] = $objPHPExcel->getActiveSheet()->getCell($cell . '31')->getValue();
                        $excelData[$i]['computer_equipment'] = $objPHPExcel->getActiveSheet()->getCell($cell . '32')->getValue();
                        $excelData[$i]['accumulated_depreciation'] = $objPHPExcel->getActiveSheet()->getCell($cell . '33')->getValue();
                        $excelData[$i]['other_assets'] = $objPHPExcel->getActiveSheet()->getCell($cell . '34')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "36", $excelDataArray[$i]['installation_expense_amortization']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "37", $excelDataArray[$i]['deferred_tax']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "38", $excelDataArray[$i]['deposits_in_guarantee']);
                        $excelData[$i]['installation_expense_amortization'] = $objPHPExcel->getActiveSheet()->getCell($cell . '36')->getValue();
                        $excelData[$i]['deferred_tax'] = $objPHPExcel->getActiveSheet()->getCell($cell . '37')->getValue();
                        $excelData[$i]['deposits_in_guarantee'] = $objPHPExcel->getActiveSheet()->getCell($cell . '38')->getValue();

                        // // pasivo table
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "45", $excelDataArray[$i]['stfl_plus_pclp']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "47", $excelDataArray[$i]['providers']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "48", $excelDataArray[$i]['p_related_parties']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "49", $excelDataArray[$i]['taxes_paying_cp']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "50", $excelDataArray[$i]['various_creditors']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "51", $excelDataArray[$i]['advance_customers']);
                        $excelData[$i]['stfl_plus_pclp'] = $objPHPExcel->getActiveSheet()->getCell($cell . '45')->getValue();
                        $excelData[$i]['providers'] = $objPHPExcel->getActiveSheet()->getCell($cell . '47')->getValue();
                        $excelData[$i]['p_related_parties'] = $objPHPExcel->getActiveSheet()->getCell($cell . '48')->getValue();
                        $excelData[$i]['taxes_paying_cp'] = $objPHPExcel->getActiveSheet()->getCell($cell . '49')->getValue();
                        $excelData[$i]['various_creditors'] = $objPHPExcel->getActiveSheet()->getCell($cell . '50')->getValue();
                        $excelData[$i]['advance_customers'] = $objPHPExcel->getActiveSheet()->getCell($cell . '51')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "53", $excelDataArray[$i]['ltfl']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "55", $excelDataArray[$i]['pst_various_creditors']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "56", $excelDataArray[$i]['pst_deferred_tax']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "57", $excelDataArray[$i]['laboral_obligations']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "58", $excelDataArray[$i]['cxp_other_lp_liabilities']);
                        $excelData[$i]['ltfl'] = $objPHPExcel->getActiveSheet()->getCell($cell . '53')->getValue();
                        $excelData[$i]['pst_various_creditors'] = $objPHPExcel->getActiveSheet()->getCell($cell . '55')->getValue();
                        $excelData[$i]['pst_deferred_tax'] = $objPHPExcel->getActiveSheet()->getCell($cell . '56')->getValue();
                        $excelData[$i]['laboral_obligations'] = $objPHPExcel->getActiveSheet()->getCell($cell . '57')->getValue();
                        $excelData[$i]['cxp_other_lp_liabilities'] = $objPHPExcel->getActiveSheet()->getCell($cell . '58')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "62", $excelDataArray[$i]['social_capital']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "63", $excelDataArray[$i]['legal_reserve']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "64", $excelDataArray[$i]['contributions_to_capitalize']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "65", $excelDataArray[$i]['share_subscription_premium']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "66", $excelDataArray[$i]['other_capital_accounts']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "67", $excelDataArray[$i]['acumulated_utilities']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "68", $excelDataArray[$i]['profit_year']);
                        $excelData[$i]['social_capital'] = $objPHPExcel->getActiveSheet()->getCell($cell . '62')->getValue();
                        $excelData[$i]['legal_reserve'] = $objPHPExcel->getActiveSheet()->getCell($cell . '63')->getValue();
                        $excelData[$i]['contributions_to_capitalize'] = $objPHPExcel->getActiveSheet()->getCell($cell . '64')->getValue();
                        $excelData[$i]['share_subscription_premium'] = $objPHPExcel->getActiveSheet()->getCell($cell . '65')->getValue();
                        $excelData[$i]['other_capital_accounts'] = $objPHPExcel->getActiveSheet()->getCell($cell . '66')->getValue();
                        $excelData[$i]['acumulated_utilities'] = $objPHPExcel->getActiveSheet()->getCell($cell . '67')->getValue();
                        $excelData[$i]['profit_year'] = $objPHPExcel->getActiveSheet()->getCell($cell . '68')->getValue();

                        // // result table
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "79", $excelDataArray[$i]['net_sales']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "80", $excelDataArray[$i]['sales_cost']);
                        $excelData[$i]['net_sales'] = $objPHPExcel->getActiveSheet()->getCell($cell . '79')->getValue();
                        $excelData[$i]['sales_cost'] = $objPHPExcel->getActiveSheet()->getCell($cell . '80')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "83", $excelDataArray[$i]['admin_expenses']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "84", $excelDataArray[$i]['selling_expenses']);
                        // // $objPHPExcel->getActiveSheet()->SetCellValue("$cell"."85", $excelDataArray[$i]['total_opr_cost']);
                        $excelData[$i]['admin_expenses'] = $objPHPExcel->getActiveSheet()->getCell($cell . '83')->getValue();
                        $excelData[$i]['selling_expenses'] = $objPHPExcel->getActiveSheet()->getCell($cell . '84')->getValue();
                        // $excelData[$i]['total_opr_cost'] = $objPHPExcel->getActiveSheet()->getCell($cell.'85')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "88", $excelDataArray[$i]['fs_expenses']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "89", $excelDataArray[$i]['fn_products']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "90", $excelDataArray[$i]['profit_or_loss']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "91", $excelDataArray[$i]['monetoty_position']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "92", $excelDataArray[$i]['other_expenses']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "93", $excelDataArray[$i]['extraordinary_items']);
                        $excelData[$i]['fs_expenses'] = $objPHPExcel->getActiveSheet()->getCell($cell . '88')->getValue();
                        $excelData[$i]['fn_products'] = $objPHPExcel->getActiveSheet()->getCell($cell . '89')->getValue();
                        $excelData[$i]['profit_or_loss'] = $objPHPExcel->getActiveSheet()->getCell($cell . '90')->getValue();
                        $excelData[$i]['monetoty_position'] = $objPHPExcel->getActiveSheet()->getCell($cell . '91')->getValue();
                        $excelData[$i]['other_expenses'] = $objPHPExcel->getActiveSheet()->getCell($cell . '92')->getValue();
                        $excelData[$i]['extraordinary_items'] = $objPHPExcel->getActiveSheet()->getCell($cell . '93')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "96", $excelDataArray[$i]['prov_of_it']);
                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "97", $excelDataArray[$i]['other_prov']);
                        $excelData[$i]['prov_of_it'] = $objPHPExcel->getActiveSheet()->getCell($cell . '96')->getValue();
                        $excelData[$i]['other_prov'] = $objPHPExcel->getActiveSheet()->getCell($cell . '97')->getValue();

                        // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "101", $excelDataArray[$i]['amortization_applied']);
                        $excelData[$i]['amortization_applied'] = $objPHPExcel->getActiveSheet()->getCell($cell . '101')->getValue();
                        
                        // echo "<pre>".$objPHPExcel->getActiveSheet()->getCell($cell . '5')->getValue();
                        $i++;
                    }

                    $notTakeComments = array(16, 41, 42, 43, 44, 46, 54, 61, 70, 72, 73, 74, 75, 77, 82, 87, 95, 99, 102);
                    $count = 0;
                    for($i=15; $i < 102; $i++){
                        if(in_array($i, $notTakeComments)){
                            continue;
                        }
                        $blucogCmnt["CMNT$count"] = $objPHPExcel->getActiveSheet()->getCell("N$i")->getValue();
                        $count++;
                    }
                    // die;
                    // echo "<pre>";
                    // print_r($blucogCmnt);die;
                    // usinessNameInfo['unique_id'] 
                    $message = "Data Imported Successfully.";
                    $success = true;
                    $output['callBackFunction'] = 'clbckImportExcel';
                    $output['excelData'] =  $excelData;
                    $output['blucogCmnt'] =  $blucogCmnt;
                    $output['businessNameInfo'] = array(
                        'unique_id' => $unique_id,
                        'business_name' => $business_name
                    );
                }
            } else {
                $message = "Only xlsx is allowed.";
                $success = false;
            }
        } else {
            $message = "Please select a file.";
            $success = false;
        }

        // echo "<pre>"; 
        // print_r($excelData);die;

        $output['message'] = $message;
        $output['success'] = $success;

        // $this->load->view('fs_excelsheet', $output);
        // $UNIX_DATE = ($output['excelData'][1]['conf_sqr_amt'] - 25569) * 86400;
        // echo ;
        echo json_encode($output);
        die;
    }
    // die;


    function createExcel($id)
    {
        //if($this->input->post('tpl_history_id')!=""){
        // SELECT * FROM `tbl_cj_financial_data` WHERE `history_id`=5540 fsHistoryBizName
        $businessNameInfo = $this->fs_history->fsHistoryBizName($id);

        $excelDataArray = $this->fs_history->getCjFinancialData($id);

        
        $blucogComments =  $this->fs_history->getBluCogCmnts($id);
        // echo"<pre>";print_r($excelDataArray);
        // die;

        // echo copy(FCPATH.'assets/uploads/financial_statement_excel/CJ_financial_Spread_Template.xlsx', FCPATH.'assets/uploads/financial_statement_excel/'. $fileName);

        // $objPHPExcel = new PHPExcel();
        // $objPHPExcel->createSheet();
        $objPHPExcel = PHPExcel_IOFactory::load(FCPATH . 'assets/uploads/financial_statement_excel/CJ_financial_Spread_Template_v2.xlsx');
        $editIndexArray = array('D', 'F', 'H', 'J', 'L');
        // print_r($editIndexArray);die;
        if (count($excelDataArray) > 0) {

            $objPHPExcel->setActiveSheetIndex(0);
            $i = 0;
            $objPHPExcel->getActiveSheet()->SetCellValue("B2", $businessNameInfo['unique_id']);
            $objPHPExcel->getActiveSheet()->SetCellValue("C2", $businessNameInfo['business_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue("D2", $businessNameInfo['rfc_number']);

            foreach ($editIndexArray as $cell) {
                if($i >= count($excelDataArray)){
                    break;
                }
                

                // echo "01-".explode("-", $excelDataArray[$i]['conf_sqr_amt'])[1] ."-". explode("-", $excelDataArray[$i]['conf_sqr_amt'])[0];
                // activo table
                // $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "5", explode("-", $excelDataArray[$i]['conf_sqr_amt'])[1] . "-" . explode("-", $excelDataArray[$i]['conf_sqr_amt'])[0]);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "5", round(25569 + (strtotime($excelDataArray[$i]['conf_sqr_amt']) / 86400)));
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "6", $excelDataArray[$i]['is_audited'] ? "Audited" : "Not Audited");
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "7", $excelDataArray[$i]['audit_firm_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "8", $excelDataArray[$i]['audit_opinion']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "15", $excelDataArray[$i]['cash_and_banks']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "17", $excelDataArray[$i]['customers']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "18", $excelDataArray[$i]['various_debtors']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "19", $excelDataArray[$i]['inventories']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "20", $excelDataArray[$i]['related_parties']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "21", $excelDataArray[$i]['taxes_to_be_recovered']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "22", $excelDataArray[$i]['projects_in_process']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "23", $excelDataArray[$i]['advances_to_suppliers']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "25", $excelDataArray[$i]['other_non_current_assets']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "26", $excelDataArray[$i]['accounts_receivable_lp']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "28", $excelDataArray[$i]['land_real_estate']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "29", $excelDataArray[$i]['machinery_equipment']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "30", $excelDataArray[$i]['transportation_equipment']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "31", $excelDataArray[$i]['office_team']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "32", $excelDataArray[$i]['computer_equipment']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "33", $excelDataArray[$i]['accumulated_depreciation']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "34", $excelDataArray[$i]['other_assets']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "36", $excelDataArray[$i]['installation_expense_amortization']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "37", $excelDataArray[$i]['deferred_tax']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "38", $excelDataArray[$i]['deposits_in_guarantee']);

                // pasivo table
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "45", $excelDataArray[$i]['stfl_plus_pclp']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "47", $excelDataArray[$i]['providers']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "48", $excelDataArray[$i]['p_related_parties']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "49", $excelDataArray[$i]['taxes_paying_cp']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "50", $excelDataArray[$i]['various_creditors']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "51", $excelDataArray[$i]['advance_customers']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "53", $excelDataArray[$i]['ltfl']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "55", $excelDataArray[$i]['pst_various_creditors']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "56", $excelDataArray[$i]['pst_deferred_tax']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "57", $excelDataArray[$i]['laboral_obligations']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "58", $excelDataArray[$i]['cxp_other_lp_liabilities']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "62", $excelDataArray[$i]['social_capital']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "63", $excelDataArray[$i]['legal_reserve']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "64", $excelDataArray[$i]['contributions_to_capitalize']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "65", $excelDataArray[$i]['share_subscription_premium']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "66", $excelDataArray[$i]['other_capital_accounts']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "67", $excelDataArray[$i]['acumulated_utilities']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "68", $excelDataArray[$i]['profit_year']);

                // result table
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "79", $excelDataArray[$i]['net_sales']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "80", $excelDataArray[$i]['sales_cost']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "83", $excelDataArray[$i]['admin_expenses']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "84", $excelDataArray[$i]['selling_expenses']);
                // $objPHPExcel->getActiveSheet()->SetCellValue("$cell"."85", $excelDataArray[$i]['total_opr_cost']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "88", $excelDataArray[$i]['fs_expenses']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "89", $excelDataArray[$i]['fn_products']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "90", $excelDataArray[$i]['profit_or_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "91", $excelDataArray[$i]['monetoty_position']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "92", $excelDataArray[$i]['other_expenses']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "93", $excelDataArray[$i]['extraordinary_items']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "96", $excelDataArray[$i]['prov_of_it']);
                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "97", $excelDataArray[$i]['other_prov']);

                $objPHPExcel->getActiveSheet()->SetCellValue("$cell" . "101", $excelDataArray[$i]['amortization_applied']);

                $i++;
            }

            // $k = 0;
            // $notTakeComments = array(16, 41, 42, 43, 44, 46, 54, 61, 70, 72, 73, 74, 75, 77, 82, 87, 95, 99, 102);
            // $takeComments = array(15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,45,47,48,49,50,51,52,53,55,56,57,58,59,60,62,63,64,65,66,67,68,69,71,76,78,79,80,81,83,84,85,86,88,89,90,91,92,93,94,96,97,98,100,101);
            //         $objPHPExcel->getActiveSheet()->SetCellValue("N$k", $k);

                    if(count($blucogComments) > 0){
                        $objPHPExcel->getActiveSheet()->SetCellValue("N15", $blucogComments[0]['cash_and_banks']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N17", $blucogComments[0]['customers']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N18", $blucogComments[0]['various_debtors']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N19", $blucogComments[0]['inventories']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N20", $blucogComments[0]['related_parties']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N21", $blucogComments[0]['taxes_to_be_recovered']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N22", $blucogComments[0]['projects_in_process']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N23", $blucogComments[0]['advances_to_suppliers']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N24", $blucogComments[0]['current_assets']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N25", $blucogComments[0]['other_non_current_assets']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N26", $blucogComments[0]['accounts_receivable_lp']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N27", $blucogComments[0]['investments_and_cxc_lP']);
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue("N28", $blucogComments[0]['land_real_estate']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N29", $blucogComments[0]['machinery_equipment']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N30", $blucogComments[0]['transportation_equipment']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N31", $blucogComments[0]['office_team']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N32", $blucogComments[0]['computer_equipment']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N33", $blucogComments[0]['accumulated_depreciation']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N34", $blucogComments[0]['other_assets']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N35", $blucogComments[0]['fixed_assets']);
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue("N36", $blucogComments[0]['installation_expense_amortization']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N37", $blucogComments[0]['deferred_tax']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N38", $blucogComments[0]['deposits_in_guarantee']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N39", $blucogComments[0]['deferred_assets']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N40", $blucogComments[0]['total_active']);
                        
                        // pasivo table
                        $objPHPExcel->getActiveSheet()->SetCellValue("N45", $blucogComments[0]['stfl_plus_pclp']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N47", $blucogComments[0]['providers']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N48", $blucogComments[0]['p_related_parties']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N49", $blucogComments[0]['taxes_paying_cp']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N50", $blucogComments[0]['various_creditors']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N51", $blucogComments[0]['advance_customers']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N52", $blucogComments[0]['pst_in_short_time']);
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue("N53", $blucogComments[0]['ltfl']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N55", $blucogComments[0]['pst_various_creditors']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N56", $blucogComments[0]['pst_deferred_tax']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N57", $blucogComments[0]['laboral_obligations']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N58", $blucogComments[0]['cxp_other_lp_liabilities']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N59", $blucogComments[0]['pst_long_term_liabilities']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N60", $blucogComments[0]['totally_passive']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N62", $blucogComments[0]['social_capital']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N63", $blucogComments[0]['legal_reserve']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N64", $blucogComments[0]['contributions_to_capitalize']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N65", $blucogComments[0]['share_subscription_premium']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N66", $blucogComments[0]['other_capital_accounts']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N67", $blucogComments[0]['acumulated_utilities']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N68", $blucogComments[0]['profit_year']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N69", $blucogComments[0]['pst_stockholders_equity']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N71", $blucogComments[0]['pst_liabilities_capital']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N76", $blucogComments[0]['months_understood']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N78", $blucogComments[0]['avg_monthly_sales']);
                
                        // result table
                        $objPHPExcel->getActiveSheet()->SetCellValue("N79", $blucogComments[0]['net_sales']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N80", $blucogComments[0]['sales_cost']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N81", $blucogComments[0]['gross_profit']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N83", $blucogComments[0]['admin_expenses']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N84", $blucogComments[0]['selling_expenses']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N85", $blucogComments[0]['total_opr_cost']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N86", $blucogComments[0]['operating_income']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N88", $blucogComments[0]['fs_expenses']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N89", $blucogComments[0]['fn_products']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N90", $blucogComments[0]['profit_or_loss']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N91", $blucogComments[0]['monetoty_position']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N92", $blucogComments[0]['other_expenses']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N93", $blucogComments[0]['extraordinary_items']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N94", $blucogComments[0]['profit_before_imps']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N96", $blucogComments[0]['prov_of_it']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N97", $blucogComments[0]['other_prov']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N98", $blucogComments[0]['net_profit']);
                        $objPHPExcel->getActiveSheet()->SetCellValue("N100", $blucogComments[0]['applied_depreciation']);
                
                        $objPHPExcel->getActiveSheet()->SetCellValue("N101", $blucogComments[0]['amortization_applied']);
                
                    }
        }

        // $h_data = $this->tpl_history->getSingleRecordById($tpl_history_id);
        // $input_fileName = $h_data->original_pdf_file_name;
        $fileName = "cj_fs_template" . '_' . time() . '.xlsx';
        $input_fileName = $businessNameInfo['unique_id'] . '_Excel_Output.xlsx';

        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //ob_get_clean();
        $objWriter->save(FCPATH . 'assets/uploads/financial_statement_excel/' . $fileName);
        $this->load->helper('download');
        $data = file_get_contents(FCPATH . 'assets/uploads/financial_statement_excel/' . $fileName);
        // $name = $fileName;

        // die;
        unlink(FCPATH . 'assets/uploads/financial_statement_excel/' . $fileName);
        force_download($input_fileName, $data);
        // die;   

    }

    function chkEmptyExcelInput($array, $i)
    {
        $flag = false;
        foreach ($array as $key => $value) {
            if ($array[$key][$i]) {
                $flag = true;
                break;
            }
        }

        if (!$flag) {
            foreach ($array as $key => $value) {
                unset($array[$key][$i]);
            }
        }
        return $array;
    }

    function savePrecheckForm($id){
       $status =  $_POST['status'];
       if($status=='Accept'){
         $statusno = 1;  
       }
       if($status=='Reject'){
          $statusno = 2; 
       }
       $data = array(
                    'status' => $statusno, 
                    'case_type' =>$_POST['case_type']
                );
       $this->fs_history->updateRFCNumber($data,$id);
       redirect('fs-dashboard');
    }
    function autosaveExcelData1(){
        echo "test";
    }
    function autosaveExcelData($id)
    {
        
        // $this->db->select('*');
		// // condition for cj financial
		// $query = $this->db->where("history_id", 15);
		// $query = $this->db->get("tbl_cj_financial_data");
        // $result = $query->result();
		// echo "<pre>";
        // print_r($result);
        //  die;
        // echo "<pre>";
        // print_r(count($_POST));
        // // echo $this->input->server('REQUEST_METHOD');
        // die;
        // $fs = "";
        
             if ($this->input->server('REQUEST_METHOD') == "POST") {
            $fetch_data = 0;

            if(trim($_POST['audit_firm_name'][0]) == ""){
                $output['message'] = "Please Enter any data";
                $output['success'] = false;
                $output['lastUpdatedTime'] = $this->fs_history->lastUpdatedTime($id);
                // SELECT max(modified_on) FROM `tbl_cj_financial_data` WHERE history_id=2
                echo json_encode($output);
                die;
            }
            // $_POST = $this->chkEmptyExcelInput($_POST, 0);
            // $_POST = $this->chkEmptyExcelInput($_POST, 1);
            // $_POST = $this->chkEmptyExcelInput($_POST, 2);
            // $_POST = $this->chkEmptyExcelInput($_POST, 3);
            // $_POST = $this->chkEmptyExcelInput($_POST, 4);

            // echo "<pre>";
            // print_r(( ));
            // echo date("Y-m", strtotime("31-08-2019"));
            // // echo ;
            // die;
            $rfc_number = array('rfc_number' => $_POST['rfc_number']);
            $this->fs_history->updateRFCNumber($rfc_number,$id);
            for ($i = 0; $i < count($_POST['conf_sqr_amt']); $i++) {

                if(trim($_POST['audit_firm_name'][$i]) != ""){
                    // date("m-Y", strtotime("2022-01-31 11:47:11"))
                $data = array(
                    'history_id' => $id, 
                    'col_year' => "" . $i + 1 . "",
                    'conf_sqr_amt' => date("Y-m-d H:i:s", strtotime($_POST['conf_sqr_amt'][$i])),
                    'is_audited' => $_POST['is_audited'][$i],
                    'audit_firm_name' => $_POST['audit_firm_name'][$i],
                    'audit_opinion' => $_POST['audit_opinion'][$i],
                    'balance_sheet' => $_POST['balance_sheet'][$i],
                    'net_income_last_year' => $_POST['net_income_last_year'][$i],
                    'cash_and_banks' => str_replace(',', '', $_POST['cash_and_banks'][$i]),
                    'customers' => str_replace(',', '', $_POST['customers'][$i]),
                    'various_debtors' => str_replace(',', '', $_POST['various_debtors'][$i]),
                    'inventories' => str_replace(',', '', $_POST['inventories'][$i]),
                    'related_parties' => str_replace(',', '', $_POST['related_parties'][$i]),
                    'taxes_to_be_recovered' => str_replace(',', '', $_POST['taxes_to_be_recovered'][$i]),
                    'projects_in_process' => str_replace(',', '', $_POST['projects_in_process'][$i]),
                    'advances_to_suppliers' => str_replace(',', '', $_POST['advances_to_suppliers'][$i]),
                    'current_assets' => str_replace(',', '', $_POST['current_assets'][$i]),

                    'other_non_current_assets' => str_replace(',', '', $_POST['other_non_current_assets'][$i]),
                    'accounts_receivable_lp' => str_replace(',', '', $_POST['accounts_receivable_lp'][$i]),
                    'investments_and_cxc_lP' => str_replace(',', '', $_POST['investments_and_cxc_lP'][$i]),

                    'land_real_estate' => str_replace(',', '', $_POST['land_real_estate'][$i]),
                    'machinery_equipment' => str_replace(',', '', $_POST['machinery_equipment'][$i]),
                    'transportation_equipment' => str_replace(',', '', $_POST['transportation_equipment'][$i]),
                    'office_team' => str_replace(',', '', $_POST['office_team'][$i]),
                    'computer_equipment' => str_replace(',', '', $_POST['computer_equipment'][$i]),
                    'accumulated_depreciation' => str_replace(',', '', $_POST['accumulated_depreciation'][$i]),
                    'other_assets' => str_replace(',', '', $_POST['other_assets'][$i]),
                    'fixed_assets' => str_replace(',', '', $_POST['fixed_assets'][$i]),

                    'installation_expense_amortization' => str_replace(',', '', $_POST['installation_expense_amortization'][$i]),
                    'deferred_tax' => str_replace(',', '', $_POST['deferred_tax'][$i]),
                    'deposits_in_guarantee' => str_replace(',', '', $_POST['deposits_in_guarantee'][$i]),
                    'deferred_assets' => str_replace(',', '', $_POST['deferred_assets'][$i]),
                    'total_active' => str_replace(',', '', $_POST['total_active'][$i]),

                    'stfl_plus_pclp' => str_replace(',', '', $_POST['stfl_plus_pclp'][$i]),
                    'providers' => str_replace(',', '', $_POST['providers'][$i]),
                    'p_related_parties' => str_replace(',', '', $_POST['p_related_parties'][$i]),
                    'taxes_paying_cp' => str_replace(',', '', $_POST['taxes_paying_cp'][$i]),
                    'various_creditors' => str_replace(',', '', $_POST['various_creditors'][$i]),
                    'advance_customers' => str_replace(',', '', $_POST['advance_customers'][$i]),
                    'pst_in_short_time' => str_replace(',', '', $_POST['pst_in_short_time'][$i]),
                    
                    'ltfl' => str_replace(',', '', $_POST['ltfl'][$i]),
                    'pst_various_creditors' => str_replace(',', '', $_POST['pst_various_creditors'][$i]),
                    'pst_deferred_tax' => str_replace(',', '', $_POST['pst_deferred_tax'][$i]),
                    'laboral_obligations' => str_replace(',', '', $_POST['laboral_obligations'][$i]),
                    'cxp_other_lp_liabilities' => str_replace(',', '', $_POST['cxp_other_lp_liabilities'][$i]),
                    'pst_long_term_liabilities' => str_replace(',', '', $_POST['pst_long_term_liabilities'][$i]),
                    'totally_passive' => str_replace(',', '', $_POST['totally_passive'][$i]),

                    'social_capital' => str_replace(',', '', $_POST['social_capital'][$i]),
                    'legal_reserve' => str_replace(',', '', $_POST['legal_reserve'][$i]),
                    'contributions_to_capitalize' => str_replace(',', '', $_POST['contributions_to_capitalize'][$i]),
                    'share_subscription_premium' => str_replace(',', '', $_POST['share_subscription_premium'][$i]),
                    'other_capital_accounts' => str_replace(',', '', $_POST['other_capital_accounts'][$i]),
                    'acumulated_utilities' => str_replace(',', '', $_POST['acumulated_utilities'][$i]),
                    'profit_year' => str_replace(',', '', $_POST['profit_year'][$i]),
                    'pst_stockholders_equity' => str_replace(',', '', $_POST['pst_stockholders_equity'][$i]),
                    'pst_liabilities_capital' => str_replace(',', '', $_POST['pst_liabilities_capital'][$i]),

                    'months_understood' => str_replace(',', '', $_POST['months_understood'][$i]),
                    'avg_monthly_sales' => str_replace(',', '', $_POST['avg_monthly_sales'][$i]),
                    'net_sales' => str_replace(',', '', $_POST['net_sales'][$i]),
                    'sales_cost' => str_replace(',', '', $_POST['sales_cost'][$i]),
                    'gross_profit' => str_replace(',', '', $_POST['gross_profit'][$i]),

                    'admin_expenses' => str_replace(',', '', $_POST['admin_expenses'][$i]),
                    'selling_expenses' => str_replace(',', '', $_POST['selling_expenses'][$i]),
                    'total_opr_cost' => str_replace(',', '', $_POST['total_opr_cost'][$i]),
                    'operating_income' => str_replace(',', '', $_POST['operating_income'][$i]),
                    'fs_expenses' => str_replace(',', '', $_POST['fs_expenses'][$i]),
                    'fn_products' => str_replace(',', '', $_POST['fn_products'][$i]),
                    'profit_or_loss' => str_replace(',', '', $_POST['profit_or_loss'][$i]),
                    'monetoty_position' => str_replace(',', '', $_POST['monetoty_position'][$i]),
                    'other_expenses' => str_replace(',', '', $_POST['other_expenses'][$i]),
                    'extraordinary_items' => str_replace(',', '', $_POST['extraordinary_items'][$i]),
                    'profit_before_imps' => str_replace(',', '', $_POST['profit_before_imps'][$i]),

                    'prov_of_it' => str_replace(',', '', $_POST['prov_of_it'][$i]),
                    'other_prov' => str_replace(',', '', $_POST['other_prov'][$i]),
                    'net_profit' => str_replace(',', '', $_POST['net_profit'][$i]),
                    'applied_depreciation' => str_replace(',', '', $_POST['applied_depreciation'][$i]),
                    'amortization_applied' => str_replace(',', '', $_POST['amortization_applied'][$i]),
                    
                    'rtnd_erng_asset_ratio' => str_replace(',', '', $_POST['rtnd_erng_asset_ratio'][$i]),
                    'equity_to_asset_ratio' => str_replace(',', '', $_POST['equity_to_asset_ratio'][$i]),
                    'current_ratio' => str_replace(',', '', $_POST['current_ratio'][$i]),
                    'debt_service_ratio' => str_replace(',', '', $_POST['debt_service_ratio'][$i]),
                    'rtrn_on_asset_ratio' => str_replace(',', '', $_POST['rtrn_on_asset_ratio'][$i]),
                    'signal_sum' => str_replace(',', '', $_POST['signal_sum'][$i]),
                    'index' => str_replace(',', '', $_POST['index'][$i]),
                );

                $signals = array(
                    'history_id' => $id, 
                    'col_year' => "" . $i + 1 . "",
                    'rtnd_erng_asset_ratio_sgnl' => str_replace(',', '', $_POST['rtnd_erng_asset_ratio_sgnl'][$i]),
                    'equity_to_asset_ratio_sgnl' => str_replace(',', '', $_POST['equity_to_asset_ratio_sgnl'][$i]),
                    'current_ratio_sgnl' => str_replace(',', '', $_POST['current_ratio_sgnl'][$i]),
                    'debt_service_ratio_sgnl' => str_replace(',', '', $_POST['debt_service_ratio_sgnl'][$i]),
                    'rtrn_on_asset_ratio_sgnl' => str_replace(',', '', $_POST['rtrn_on_asset_ratio_sgnl'][$i])
                );
                $fetch_data += $this->fs_history->addNewRecordsInCjData($data, $id, $i);
                $fetch_data += $this->fs_history->addNewRecordsInCjSignals($signals, $id, $i);
                
                // $fs = $fetch_data;
                // echo $fetch_data;  
            }

            }


            $blucogCmnt = array();
            $i = 0; 
            $blucogCmnt['history_id'] = $id;
            $blucogCmnt['cash_and_banks'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['customers'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['various_debtors'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['inventories'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['related_parties'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['taxes_to_be_recovered'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['projects_in_process'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['advances_to_suppliers'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['current_assets'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['other_non_current_assets'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['accounts_receivable_lp'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['investments_and_cxc_lP'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['land_real_estate'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['machinery_equipment'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['transportation_equipment'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['office_team'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['computer_equipment'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['accumulated_depreciation'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['other_assets'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['fixed_assets'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['installation_expense_amortization'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['deferred_tax'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['deposits_in_guarantee'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['deferred_assets'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['total_active'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['stfl_plus_pclp'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['providers'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['p_related_parties'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['taxes_paying_cp'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['various_creditors'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['advance_customers'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_in_short_time'] = $_POST['blucogComments'][$i]; $i++;
            
            $blucogCmnt['ltfl'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_various_creditors'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_deferred_tax'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['laboral_obligations'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['cxp_other_lp_liabilities'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_long_term_liabilities'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['totally_passive'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['social_capital'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['legal_reserve'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['contributions_to_capitalize'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['share_subscription_premium'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['other_capital_accounts'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['acumulated_utilities'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['profit_year'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_stockholders_equity'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['pst_liabilities_capital'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['months_understood'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['avg_monthly_sales'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['net_sales'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['sales_cost'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['gross_profit'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['admin_expenses'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['selling_expenses'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['total_opr_cost'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['operating_income'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['fs_expenses'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['fn_products'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['profit_or_loss'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['monetoty_position'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['other_expenses'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['extraordinary_items'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['profit_before_imps'] = $_POST['blucogComments'][$i]; $i++;

            $blucogCmnt['prov_of_it'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['other_prov'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['net_profit'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['applied_depreciation'] = $_POST['blucogComments'][$i]; $i++;
            $blucogCmnt['amortization_applied'] = $_POST['blucogComments'][$i]; $i++;
            
            // $blucogCmnt['rtnd_erng_asset_ratio'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['equity_to_asset_ratio'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['current_ratio'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['debt_service_ratio'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['rtrn_on_asset_ratio'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['signal_sum'] = $_POST['blucogComments'][$i]; $i++;
            // $blucogCmnt['index'] = $_POST['blucogComments'][$i]; $i++;

            // save blucog comments in db
            if(trim($_POST['audit_firm_name'][0]) != ""){
            $this->fs_history->saveBluCogCmnts($blucogCmnt, $id);
            }
            // echo "<pre>";echo $fetch_data;die;
            // if ($fetch_data == count($_POST['conf_sqr_amt'])) {
                $message = "Data Saved";
                $success = true;
            // } else {
            //     $message = "Error in some rows.";
            //     $success = false;
            // }
        } else {
            $message = "No Data Entered.";
            $success = false;
        }
        $output['message'] = $message;
        $output['success'] = $success;
        $output['lastUpdatedTime'] = $this->fs_history->lastUpdatedTime($id);
        // SELECT max(modified_on) FROM `tbl_cj_financial_data` WHERE history_id=2
        echo json_encode($output);
    }

    function fetch_template_detail()
    {
        //print_r($_POST['start']);
        //die;
        //$this->load->model("crud_model");
        $fetch_data = $this->fs_history->make_datatables();
        /*echo"<pre>";
        print_r($fetch_data);
        die;*/
        $data = array();
        $startNo = $_POST['start'] + 1;
        foreach ($fetch_data as $key => $value) {

            // $custormer_data = $this->bank_summary_level_data->getCustomerNameByHistoryId($value->id);
            // $txn_data = $this->bank_customer_txn_data->fetchCustomerTxnDataForCategorization($value->id);
            //$case_error_log_data = $this->case_error_log->getRecordByHistoryId($value->id);

            $minutes = abs(strtotime($value->created_on) - time()) / 60;
            $sub_array = array();
            $sub_array[] = "<td><input type='checkbox' class='chkbox' value='".$value->id."'/></td>";
            $sub_array[] = $startNo++;

            /*if($value->type=='single'){
            if($value->original_pdf_file_name==""){
                $original_pdf_file_name = $value->file_name;
                
            }else{
                $original_pdf_file_name = $value->original_pdf_file_name;
            } 
            //$sub_array[] = '<td><a href="'.$this->config->item('assets').'uploads/bank_statement/'.$value->file_name.'" title="Download" target="_blank">'.substr($original_pdf_file_name,0,20).'</a></td>';
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.' ').'" title="Download" target="_blank">'.$value->unique_id.'</a></td>';
                           
        }else if($value->type=='multiple'){ 
            //$sub_array[] = '<td><a href="'.$this->config->item('assets').'uploads/bulk_upload/'.$value->folder_name.'/'.$value->original_pdf_file_name.'" title="Download" target="_blank">'.substr($value->original_pdf_file_name,0,20).'</a></td>';
            $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download" target="_blank">'.$value->unique_id.'</a></td>';
        }*/
            // $custormer_data = '';
            
            if($value->status==0){
                if ($this->session->userdata('user_role') == 4 || $this->session->userdata('user_role') == 5) {
                // $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
                    $sub_array[] = '<td><a href="' . base_url('fs-precheckform/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                } else if ($value->qa_user_id == $this->session->userdata('user_id')) {
                    $sub_array[] = '<td><a href="' . base_url('fs-precheckform/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                    // $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
                } else {
                    $sub_array[] = '<td><a href="' . base_url('fs-precheckform/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                }
            }else{
                if ($this->session->userdata('user_role') == 4 || $this->session->userdata('user_role') == 5) {
                // $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
                    $sub_array[] = '<td><a href="' . base_url('fs-excelsheet/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                } else if ($value->qa_user_id == $this->session->userdata('user_id')) {
                    $sub_array[] = '<td><a href="' . base_url('fs-excelsheet/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                    // $sub_array[] = '<td><a href="'.base_url('spread-detail/'.$value->id.'').'" title="Download">'.$value->unique_id.'</a></td>';
                } else {
                    $sub_array[] = '<td><a href="' . base_url('fs-excelsheet/') . $this->url_encrypt($value->id) . '" title="Download">' . $value->unique_id . '</a></td>';
                }
            }
            
            $sub_array[] = $value->business_name;
            $sub_array[] = $value->case_type==null?'N/A':$value->case_type;
            if($value->status==0){
                $status = 'New';
            }else if($value->status==1){
                $status = 'Accepted';
            }else if($value->status==2){
                $status = 'Rejected';
            }
            
            // $sub_array[] = ($custormer_data->native_vs_non_native == '') ? '' : $custormer_data->native_vs_non_native;
            // $sub_array[] = "asdf";

            $sub_array[] = $value->created_on;
            $sub_array[] = $value->upload_user_name;
            // if($custormer_data!=''){
            //     $spreading_status = 'Done';
            // }
            // else if($minutes < 5 && $custormer_data==''){
            //     $spreading_status = 'In process';
            // }
            // else if($minutes > 5 && $custormer_data==''){
            //     $spreading_status = 'Fail';
            // }
            // if ($value->status == NULL || $value->status == 0) {
                // if(!empty($custormer_data) && !empty($value->txn_id)){
                //     $spreading_status = 'Done';
                // }
                // else if($minutes < 1 && (empty($custormer_data) || empty($value->txn_id))){
                //     $spreading_status = 'Ready for execute';
                // }
                // else if($minutes > 1 && (empty($custormer_data) || empty($value->txn_id))){
                // $spreading_status = 'Spreading';
                // }
            // } else {
                // if($value->status==2 && !empty($custormer_data) && !empty($value->txn_id)){
                //     $spreading_status = 'Done';
                // }
                // else if($value->status==1){
                //     $spreading_status = 'In process';
                // }
                // else if($value->status==2 && (empty($custormer_data) || empty($value->txn_id))){
                // $spreading_status = 'Spreading';
                // }
            // }
            // $sub_array[] = $spreading_status;

            // if($value->log_id){
            //     $ac_num = explode(',', $value->ac_num);
            //     $hol_name = explode(',', $value->hol_name);
            //     $ac_type = explode(',', $value->ac_type);
            //     $bn_nm = explode(',', $value->bn_nm);
            //     $bn_add = explode(',', $value->bn_add);
            //     $bn_cty = explode(',', $value->bn_cty);
            //     $bn_st = explode(',', $value->bn_st);
            //     $bn_zp = explode(',', $value->bn_zp);
            //     $curr_bal = explode(',', $value->curr_bal);
            //     $st_dt = explode(',', $value->st_dt);
            //     $en_dt = explode(',', $value->en_dt);
            //     $clo_bal = explode(',', $value->clo_bal);
            //     $chk_sm = explode(',', $value->chk_sm);
            //     $tpl_nt_fn = explode(',', $value->tpl_nt_fn);
            //     if(in_array(0, $ac_num) || in_array(0, $hol_name) || in_array(0, $ac_type) || in_array(0, $bn_nm) || in_array(0, $bn_add) || in_array(0, $bn_cty) || in_array(0, $bn_st) || in_array(0, $bn_zp) || in_array(0, $curr_bal) || in_array(0, $st_dt) || in_array(0, $en_dt) || in_array(0, $clo_bal) || in_array(0, $chk_sm) || in_array(0, $tpl_nt_fn)){
            //         $sub_array[] = 'Yes';
            //     }
            //     else{
            //         $sub_array[] = 'No';
            //     }
            // }
            // else{
            //     $sub_array[] = 'Not Found';
            // }

//            if($value->submit_by_qa=='0'){
//                $workflow_status = 'Spreading';
//            }else if($value->click_to_send=='1'){
//                //if($value->success_type=='success'){
//                    $workflow_status = 'Complete';
//                //}
//            }else if($value->click_to_send=='1'){
//                $workflow_status = 'Rejected-downstream';
//            }else if($value->submit_by_qa=='1'){
//                $workflow_status = 'QA';
//            }
//            $sub_array[] = $workflow_status;
                $sub_array[] = $status;

            //$sub_array[] = '';
            if($this->common_model->checkUserPermission(17,false)) {
                if($value->qa_user_id==0 && $value->submit_by_qa=='1'){
                    $sub_array[] = '<button type="button" class="assigned_to_me" id="assigend_'.($value->id).'" data-id="'.($value->id).'" style="font-size: 10px !important;">Assign to me</button>';
                }else{
                    $sub_array[] = $value->qa_user_name;

                }
            }
            //$sub_array[] = 'Done';//$value->type;
            //$sub_array[] = '';
            /*if($value->downloaded_file_name!="" && $value->type=='single'){
            if($this->session->userdata('user_id')!=3){
                $sub_array[] = '<a href="'.$this->config->item('assets').'uploads/bank_statement_excel/'.$value->downloaded_file_name.'" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
            }else{
                $sub_array[] = '<a href="Bank_statement/createExcel/'.$value->id.'" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
            }
         }else if($value->type=='multiple'){ 
            if($value->status==0){
                $sub_array[] = 'Ready for execution';
            }else if($value->status==1){
                $sub_array[] = 'In progress';
            }else if($value->status==2){
                $sub_array[] = '<a href="'.$this->config->item('assets').'uploads/bulk_upload/'.$value->folder_name.'/'.$value->folder_name.'.zip" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>';
             } 
         }*/

            //$sub_array[] = '<a href="#" title="Download Input"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>';


            //$sub_array[] = '<a href="Bank_statement/createExcel/'.$value->id.'" title="Download Output"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>';
            //$sub_array[] = '<a href="javascript:void(0)">Refresh</a>';
            // if( ($value->status==NULL || $value->status==0) && $value->type=='multiple'){
            //     $sub_array[] = '<td><a href="'.base_url('bulk-upload-spread/'.$value->id).'" target="_blank" title="Refresh"><img src="https://img.icons8.com/ios/35/4a90e2/available-updates.png"/></a></td>';
            // }
            // else{
            //     $sub_array[] = '';
            // }
            $data[] = $sub_array;
        }
        $output = array(
            "draw"            =>   intval($_POST["draw"]),
            "recordsTotal"    =>   '', //$this->tpl_history->get_all_data(),
            "recordsFiltered" =>   $this->fs_history->get_filtered_data(), // count($data),
            "data"            =>   $data
        );
        echo json_encode($output);
    }

    function assigned_case()
    {
        $output = array();
        $id = $this->input->get('history_id');
        // echo $this->url_decrypt("$id");
        // echo "$id";die;
        $error = '';
        $output['assigned'] = false;
        $output['name'] = '';
        if ($id) {
            $fsHistRecord = $this->fs_history->getSingleRecordById($id);
            if ($fsHistRecord->qa_user_id == 0) {
                $assignData = array();
                $assignData['qa_user_id'] = $this->session->userdata('user_id');
                $affected_rows = $this->fs_history->updateRecords($id, $assignData);
                if ($affected_rows == 1) {
                    $userDetails = $this->tpl_user->editUser($this->session->userdata('user_id'));
                    $output['name'] = $userDetails->first_name . ' ' . $userDetails->last_name;
                    $output['assigned'] = true;
                    $output['history_id'] = $this->url_encrypt($id);
                    $error = 'Case assigned to you successfully';
                } else {
                    $output['assigned'] = false;
                    $error = "Something went wrong!";
                }
            }
        } else {
            $error = "Something went wrong!";
        }


        $output['html'] = $error;
        $output['success'] = true;
        echo json_encode($output);
        die();
    }
}
