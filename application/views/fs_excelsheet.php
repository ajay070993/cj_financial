<?php
$i = 0;
// echo "<pre>";
//         print_r($id);die;
// echo "<pre>"; print_r($blucogComments[0]['']);die;
function formatExcelNumData($val)
{
    if (isset($val)) {
        // return number_format($val);
        return number_format($val, 2);
    } else {
        return "0.00";
    }
}

function formatExceldate($val)
{
    if (isset($val)) {
        // return number_format($val);
        return date("Y-m", strtotime($val));
        // number_format($val, 2);
    } else {
        return "";
    }
}

function formatExcelTxtData($val)
{
    if (isset($val)) {
        // return number_format($val);
        return $val;
    } else {
        return "";
    }
}

// echo formatExcelNumData($excelData[$i+4]['cash_and_banks']);
// die;
?>
<?php
$currentPage = 'rejected_queue';
include('header.php'); ?>
<?php include('navigation.php'); ?>
<div class="main <?php if ($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div id="send_btn_loader" style="display: none;">
        <center><img src="<?php echo $this->config->item('assets'); ?>images/loading_img.gif" style="margin-top: -40px;"></center>
    </div>
    <link rel="stylesheet" href="<?php echo $this->config->item('assets'); ?>css/excelsheet_css/spread.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <div class="modal fade customModal" id="imprtExcelModal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: unset;max-width:531px">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="form-signin ajax_form form-fields imprtExcelForm" id="ajax_form" action="<?php echo site_url('Fs_dashboard/importExcelData'); ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                        <p style="margin: 14px 0 ;">Upload Excel File to Import.</p>
                        <!-- <input type="file" name="imprtExcelFile" class="form-control" id="imprtExcelFile" style="height: auto;"> -->

                        <div class="upload">
                            <span id="filename_select_exl"></span>
                            <button type="button" for="upload">Browse</button>
                            <input type="file" onchange="return validateFilesExtension(this)" class="form-control-file" name="imprtExcelFile" id="exl_file_name">
                        </div>

                        <div class="updateContainer" style="justify-content: center;">
                            <button type="submit">Import</button>
                            <button type="button" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="autosave_form" class="autosave_form" action="<?php echo base_url("Fs_dashboard/autosaveExcelData/$id"); ?>" method="post">
        <div class="scrollContainer">

            <style>
                .main .scrollContainer .top_container_bar {
                    padding: 25px 0 10px 10px;
                    width: 100%;
                    /* padding-top: 25px; */
                    /* position: sticky;
                    top: 0; */
                    background: #F8F7FB;
                    /* margin-left:30px; */
                    width: calc(100% - 400px);
                }

                .excel_tabs {
                    -ms-flex-align: flex-end;
                    align-items: flex-end;
                }

                .scrollContainer .top_container_bar button {
                    padding: 10px;
                    background: #006fcf;
                    border: none;
                    color: #ffffff;
                    line-height: 12px;
                    font-size: 12px;
                    border-radius: 4px;
                    outline: none;
                    -webkit-box-shadow: none;
                    box-shadow: none;
                    /* margin-left: 10px; */
                }
            </style>

            <ul class="excel_tabs" id="tabs_listing" style="position: relative;">
                <li data-tab="one" class="active">Spread Template</li>
                <li data-tab="two" id="tabular_format_data">Tabular Format</li>
                <div class="top_container_bar">
                    <div class="button_container" style="margin:0;align-items:flex-end">
                        <div class="left_box">
                            <div class="btn_box">
                                <!-- <button type="button" title="Import Excel" class="mnmlBtn" onclick="imprtExcelModal();$('.imprtExcelForm')[0].reset();$('#filename_select_exl').text('')" id="import_excel_btn"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3" />
                                    </svg></button>
                                <button type="button" title="Download Excel" id="dwld_excel_btn" class="mnmlBtn" onClick="window.location.href='<?php echo base_url("Fs_dashboard/createExcel/$id"); ?>'"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5" />
                                    </svg></button> -->

                                <?php if ($this->session->userdata('user_role') == 5) { ?>
                                    <?php if (true) { ?>
                                        <?php if ($this->common_model->checkUserPermission(5, false)) { ?>
                                            <button type="button" title="Download Excel" id="dwld_excel_btn" class="mnmlBtn" onClick="window.location.href='<?php echo base_url("Fs_dashboard/createExcel/$id"); ?>'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                        <?php if ($this->common_model->checkUserPermission(6, false) && $businessNameInfo['submit_by_qa'] == '0') { ?>
                                            <button type="button" title="Import Excel" class="mnmlBtn" onclick="imprtExcelModal();$('.imprtExcelForm')[0].reset();$('#filename_select_exl').text('')" id="import_excel_btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($this->session->userdata('user_role') == 6) { ?>
                                    <?php // if ($submit_by_qa == '0' || ($click_to_send == '0' || $json_responce->success_type == 'error')) { 
                                    ?>
                                    <?php if (true) { ?>
                                        <?php if ($this->common_model->checkUserPermission(5, false)) { ?>
                                            <button type="button" title="Download Excel" id="dwld_excel_btn" class="mnmlBtn" onClick="window.location.href='<?php echo base_url("Fs_dashboard/createExcel/$id"); ?>'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                        <?php if ($this->common_model->checkUserPermission(6, false) && $businessNameInfo['click_to_send'] == '0') { ?>
                                            <button type="button" title="Import Excel" class="mnmlBtn" onclick="imprtExcelModal();$('.imprtExcelForm')[0].reset();$('#filename_select_exl').text('')" id="import_excel_btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($this->session->userdata('user_role') != 5 && $this->session->userdata('user_role') != 6) { ?>
                                    <?php // if ($click_to_send == '0' || $json_responce->success_type == 'error') { 
                                    ?>
                                    <?php if (true) { ?>
                                        <?php if ($this->common_model->checkUserPermission(5, false)) { ?>
                                            <button type="button" title="Download Excel" id="dwld_excel_btn" class="mnmlBtn" onClick="window.location.href='<?php echo base_url("Fs_dashboard/createExcel/$id"); ?>'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                        <?php if ($this->common_model->checkUserPermission(6, false) && $businessNameInfo['click_to_send'] == '0') { ?>
                                            <button type="button" title="Import Excel" class="mnmlBtn" onclick="imprtExcelModal();$('.imprtExcelForm')[0].reset();$('#filename_select_exl').text('')" id="import_excel_btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3" />
                                                </svg>
                                            </button>
                                        <?php } ?>

                                    <?php } ?>
                                <?php } ?>
                                <!-- <button type="button">Actualizar Fecha Parcial</button> -->
                                <!-- <button type="button" id="toggle" class="hide">Toggle Text</button> -->
                                <!-- <button id="saveDataBtn" title="All Changes Saved" type="submit" class="mnmlBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" height="22px" width="22px" fill="#456">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM19 18H6c-2.21 0-4-1.79-4-4 0-2.05 1.53-3.76 3.56-3.97l1.07-.11.5-.95C8.08 7.14 9.94 6 12 6c2.62 0 4.88 1.86 5.39 4.43l.3 1.5 1.53.11c1.56.1 2.78 1.41 2.78 2.96 0 1.65-1.35 3-3 3zm-9-3.82l-2.09-2.09L6.5 13.5 10 17l6.01-6.01-1.41-1.41z" />
                                </svg></button> -->
                                <span class="autosaveMeassage"></span>
                            </div>
                        </div>
                        <span id="autoSaveStatus" style="position: ;bottom:10px;right:0;font-size:12px;"><?php if ($lastUpdatedTime) echo "Last Updated on " . $lastUpdatedTime; else echo "Last Updated Not Available"; ?></span>

                        <?php if ($this->session->userdata('user_role') == 5) { ?>
                            <?php if ($businessNameInfo['submit_by_qa'] == '0') { ?>
                                <div class="right_box">
                                    <button id="saveDataBtn" type="submit" class="">Save</button>
                                    <?php if ($this->common_model->checkUserPermission(3, false)) { ?>
                                        <button style="background: #B57DFF;" id="submit_btn" type="button" submit_to_qa="1">Submit to QA</button>
                                    <?php } ?>
                                    <?php if ($this->common_model->checkUserPermission(2, false)) { ?>
                                        <!-- <button type="button" id="cancel_btn" onclick="location.reload();" style="background: #FF5F5F;">Cancel</button> -->
                                    <?php } ?>
                                </div>

                            <?php } else { ?>
                                <?php // if (count($json_responce) > 0) {
                                if ($businessNameInfo['click_to_send'] == '1') {
                                    // if ($json_responce->success_type == 'success') {
                                        echo "Case Sent to downstream";
                                        echo "<style>table.custom_table.toggle_table input,table.custom_table.toggle_table select{pointer-events: none;}</style>";
                                    // } else if ($json_responce->success_type == 'error') {
                                    //     echo "rejected-downstream";
                                    // }
                                } else { ?>
                                    <span style="background:#0000;color:#456">Case submitted to QA</span>
                                    <style>table.custom_table.toggle_table input,table.custom_table.toggle_table select{pointer-events: none;}</style>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($this->session->userdata('user_role') == 6) { ?>
                            <?php // if ($businessNameInfo['click_to_send'] == '0' || $json_responce->success_type == 'error') { ?>
                            <?php if ($businessNameInfo['click_to_send'] == '0') { ?>
                                <div class="right_box">
                                    <button id="saveDataBtn" type="submit" class="">Save</button>
                                    <?php if ($this->common_model->checkUserPermission(4, false)) { ?>
                                        <button type="button" style="background: #E4CA4B;" id="send_btn" click_to_send="1">Send</button>
                                    <?php } ?>
                                    <?php if ($this->common_model->checkUserPermission(2, false)) { ?>
                                        <!-- <button type="button" id="cancel_btn" onclick="location.reload();" style="background: #FF5F5F;">Cancel</button> -->
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                Case Sent to Downstream

                                <style>table.custom_table.toggle_table input,table.custom_table.toggle_table select{pointer-events: none;}</style>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($this->session->userdata('user_role') != 5 && $this->session->userdata('user_role') != 6) { ?>
                            <?php // if ($businessNameInfo['click_to_send'] == '0' || $json_responce->success_type == 'error') { ?>
                            <?php if ($businessNameInfo['click_to_send'] == '0') { ?>
                                <div class="right_box">
                                    <button id="saveDataBtn" type="submit" class="">Save</button>
                                    <?php if ($businessNameInfo['submit_by_qa'] == '0') { ?>
                                        <?php if ($this->common_model->checkUserPermission(3, false)) { ?>
                                            <button style="background: #B57DFF;" id="submit_btn" type="button" submit_to_qa="1">Submit to QA</button>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php // if (count($json_responce) > 0) {
                                             if ($businessNameInfo['click_to_send'] == '1') {
                                            // if ($json_responce->success_type == 'success') {
                                                echo "completed";
                                            // } else if ($json_responce->success_type == 'error') {
                                            //     echo "rejected-downstream";
                                            // }
                                        } else { ?>
                                            <span readonly style="background:#0000;color:#456">Case submitted to QA</span>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($this->common_model->checkUserPermission(4, false)) { ?>
                                        <button type="button" style="background: #E4CA4B;" id="send_btn" click_to_send="1">Send</button>
                                    <?php } ?>
                                    <?php if ($this->common_model->checkUserPermission(2, false)) { ?>
                                        <!-- <button type="button" id="cancel_btn" onclick="location.reload();" style="background: #FF5F5F;">Cancel</button> -->
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                Case Sent to Downstream
                                <style>table.custom_table.toggle_table input,table.custom_table.toggle_table select{pointer-events: none;}</style>
                            <?php } ?>
                        <?php } ?>
                    </div>


                </div>
            </ul>

            <div class="spread_template_container excel_tab_box one">
                <div class="spread_heading mb_2">
                    <table class="table custom_table top_table">
                        <tr>
                            <td width="28%" class="bold grey_bg np"><input readonly type="text" name="" class="" value="Opportunity_Id"></td>
                            <td width="44%" class="bold grey_bg np"><input readonly type="text" name="" class="" value="Customer Name"></td>
                            <td width="28%" class="bold grey_bg np"><input readonly type="text" name="" class="" value="RFC Number"></td>
                        </tr>
                        <tr>
                            <td class="bold grey_bg np"><input readonly type="text" name="" class="" id="unique_id" value="<?php echo $businessNameInfo['unique_id'] ?>"></td>
                            <td class="bold grey_bg np"><input readonly type="text" name="" class="" id="business_name" value="<?php echo $businessNameInfo['business_name'] ?>"></td>
                            <td class="bold grey_bg np"><input  type="text" name="rfc_number" class="rfc_number" id="rfc_number" value="<?php print_r($businessNameInfo['rfc_number']) ?>"></td>
                        </tr>
                    </table>
                    <div class="btn_box">
                        <!-- <button type="button" title="Import Excel" class="mnmlBtn" onclick="imprtExcelModal();$('.imprtExcelForm')[0].reset();$('#filename_select_exl').text('')" id="import_excel_btn"><svg version="1.0" xmlns="http://www.w3.org/2000/svg" class="fill" width="24" height="24" stroke-width="2" viewBox="0 0 532.000000 512.000000" fill="#456" stroke="#456" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)">
                                    <path d="M2910 5080 c-107 -22 -420 -82 -695 -134 -1672 -318 -1980 -378 -2007 -392 -62 -32 -58 100 -56 -2013 3 -1913 3 -1929 23 -1951 11 -12 30 -26 43 -31 13 -5 344 -70 735 -144 392 -74 732 -140 757 -145 185 -40 1375 -260 1404 -260 48 0 72 13 91 50 12 24 15 77 15 295 l0 265 890 0 c781 0 895 2 928 15 47 20 79 50 103 97 18 36 19 87 19 1828 0 1741 -1 1792 -19 1828 -24 47 -56 77 -103 97 -33 13 -147 15 -928 15 l-890 0 -1 263 c-1 144 -4 273 -8 286 -8 28 -45 58 -81 65 -14 2 -113 -13 -220 -34z m108 -3688 l-3 -1169 -1300 248 c-715 136 -1315 250 -1332 254 l-33 6 0 1829 c0 1007 1 1830 3 1830 1 0 601 114 1332 254 l1330 253 3 -1169 c1 -642 1 -1694 0 -2336z m1952 1168 l0 -1740 -875 0 -875 0 0 255 0 255 205 0 205 0 0 100 0 100 -205 0 -205 0 0 310 0 310 205 0 205 0 0 100 0 100 -205 0 -205 0 0 260 0 260 205 0 205 0 0 100 0 100 -205 0 -205 0 0 260 0 260 205 0 205 0 0 100 0 100 -205 0 -205 0 0 255 0 255 875 0 875 0 0 -1740z"></path>
                                    <path d="M989 3258 c259 -444 401 -688 404 -696 2 -7 -406 -656 -523 -832 -20 -30 -46 -72 -59 -92 l-23 -38 254 0 253 0 166 313 c92 171 174 337 184 367 10 30 21 59 25 63 4 5 17 -20 28 -55 12 -34 95 -202 186 -373 l164 -310 252 -3 c138 -1 249 1 247 5 -12 22 -515 846 -544 891 -18 29 -33 58 -33 66 0 7 67 123 148 257 247 408 389 643 401 667 l12 22 -235 0 -234 0 -148 -277 c-82 -153 -162 -314 -179 -358 -16 -44 -34 -82 -39 -83 -5 -2 -23 34 -41 80 -17 46 -91 208 -164 361 l-134 277 -258 0 -258 0 148 -252z"></path>
                                    <path d="M3840 3685 l0 -105 408 2 407 3 3 103 3 102 -411 0 -410 0 0 -105z"></path>
                                    <path d="M3840 2970 l0 -100 410 0 410 0 0 100 0 100 -410 0 -410 0 0 -100z"></path>
                                    <path d="M3840 2255 l0 -105 410 0 411 0 -3 103 -3 102 -407 3 -408 2 0 -105z"></path>
                                    <path d="M3840 1435 l0 -105 410 0 411 0 -3 103 -3 102 -407 3 -408 2 0 -105z"></path>
                                </g>
                            </svg></button> -->
                        <!-- <button type="button" title="Download Excel" id="dwld_excel_btn" class="mnmlBtn" onClick="window.location.href='<?php echo base_url("Fs_dashboard/createExcel/$id"); ?>'"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#456" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5" />
                            </svg></button> -->
                        <button type="button">Actualizar Fecha Parcial</button>
                        <!-- <button type="button" id="toggle" class="hide">Toggle Text</button> -->
                        <!-- <div style="display:inline-block;width:1px;background:#ddd;border-left:8px solid #0000;border-right:8px solid #0000;"></div> -->
<!-- <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#456"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"/></svg> -->
                        <button type="button" class="hide" id="toggle" title="Toggle Text"></button>



                        <!-- <button id="saveDataBtn" title="All Changes Saved" type="submit" class="mnmlBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" height="22px" width="22px" fill="#456">
                                <path d="M0 0h24v24H0V0z" fill="none" />
                                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM19 18H6c-2.21 0-4-1.79-4-4 0-2.05 1.53-3.76 3.56-3.97l1.07-.11.5-.95C8.08 7.14 9.94 6 12 6c2.62 0 4.88 1.86 5.39 4.43l.3 1.5 1.53.11c1.56.1 2.78 1.41 2.78 2.96 0 1.65-1.35 3-3 3zm-9-3.82l-2.09-2.09L6.5 13.5 10 17l6.01-6.01-1.41-1.41z" />
                            </svg></button> -->

                    </div>
                </div>
                <style>
                    button#toggle {
                        width: 50px;
                        height: 28px;
                        position: relative;
                        vertical-align: middle;
                        background-color: #006fcfcc;
                        border: none;
                        outline: none;
                        border-radius: 50px;
                    }

                    /* button#toggle:hover::before {
                        content: "Toggle Text";
                        position: absolute;
                        top: calc(100% + 5px);
                        right:0;
                        color: #fff;
                        z-index: 101;
                        background: #222;
                        width: 50px;
                        padding: 3px;
                        border-radius: 4px;
                    } */

                    button#toggle::after {
                        content: "";
                        position: absolute;
                        height: 22px;
                        width: 22px;
                        right: 3px;
                        border-radius: 50px;
                        top: 3px;
                        background-color: #fff;
                        -webkit-transition: .4s;
                        transition: .4s;
                    }

                    button.hide#toggle {
                        background-color: #c7c7c7;
                    }

                    button.hide#toggle::after {
                        -webkit-transform: translateX(-22px);
                        -ms-transform: translateX(-22px);
                        transform: translateX(-22px);
                    }

                    .scrollContainer button.mnmlBtn#saveDataBtn {
                        /* position: absolute; */
                        top: 0;
                        left: 100px;
                    }

                    .scrollContainer button.mnmlBtn {
                        background: #0000;
                        padding: 4px 10px;
                    }

                    .scrollContainer button.mnmlBtn:hover {
                        background: #0001;
                    }

                    .scrollContainer button.mnmlBtn svg.spinner {
                        /* anima */
                        -webkit-animation: loader 1s ease-in-out infinite;
                        animation: loader 0.6s ease-in-out infinite;
                    }

                    #tbl_container {
                        padding-bottom: 2px;
                    }

                    #tbl_container .scrollable {
                        min-width: 1400px;
                    }

                    .custom_table tr td:nth-child(1),
                    .custom_table tr td:nth-child(2) {
                        padding: 0 4px;
                        background: #f4f4f4;
                    }

                    .custom_table tr td.np {
                        padding: 0;
                    }

                    .custom_table tr td.np input {
                        font-size: 12px;
                    }

                    .custom_table tr td.persign,
                    .custom_table tr td.signal {
                        text-align: center;
                    }

                    .custom_table tr td input[data-formula-type="SIGNALSUM"],
                    .custom_table tr td input[data-formula-type="AVGMONTH"],
                    .custom_table tr td input[data-formula-type="SUMPERCENT"],
                    .custom_table tr td input[data-formula-type="PERCENT"],
                    .custom_table tr td input[data-formula-type="TOTALSUM"],
                    .custom_table tr td input[data-formula-type="DIVSUM"],
                    .custom_table tr td input[data-formula-type="SUM"],
                    .custom_table tr td input[data-formula-type="IFELSEIF"],
                    .custom_table tr td input[data-formula-type="GREATER"],
                    .custom_table tr td input.num {
                        font-family: 'Roboto Mono', monospace;
                        text-align: right;
                    }

                    .custom_table tr td input.red {
                        color: #ff5f5f;
                    }

                    .custom_table tr td input:read-only {
                        background: #aaa0;
                    }

                    .custom_table tr td input[data-formula-type="ASSIGN_DATE"],
                    .custom_table tr td input.pr {
                        text-align: center;
                    }

                    #tbl_container {
                        width: 100%;
                        overflow: auto;
                    }

                    .navSmall .spread_template_container .custom_table.top_table {
                        width: 60%;
                        min-width: 566px;
                    }

                    .spread_template_container .custom_table td.brdr {
                        border-left: 1px solid #e5e5e5;
                    }

                    .spread_template_container .custom_table td.cntr {
                        background: #f2f2f2;
                        text-align: center;
                    }

                    .spread_template_container .custom_table td.cntr2 {
                        text-align: center;
                    }

                    .spread_template_container .custom_table td.padding {
                        padding: 4px;
                    }

                    .spread_template_container .custom_table tr.tbl_hd_tr td {
                        padding: 4px;
                    }

                    .spread_template_container .custom_table td.hidden {
                        display: none;
                    }

                    #imprtExcelModal .upload {
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                        -ms-flex-align: center;
                        align-items: center;
                        width: 100%;
                        background: #fff;
                        border-radius: 4px;
                        border: 1px solid #ddd;
                        position: relative;
                        /* -webkit-box-shadow: 0 0 2px #ddd;
                        box-shadow: 0 0 2px #ddd; */
                        margin-bottom: 20px;
                    }


                    #imprtExcelModal .upload span {
                        padding: 7px 12px;
                        width: 78%;
                        color: #323E49;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        font-size: 12px;
                    }

                    #imprtExcelModal .upload button {
                        width: 22%;
                        padding: 10px;
                        background: #e5e5e5;
                        border: none;
                        color: #323E49;
                        line-height: 12px;
                        font-size: 12px;
                        border-top-right-radius: 4px;
                        border-bottom-right-radius: 4px;
                        outline: none;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        margin-left: 1px;
                    }

                    #imprtExcelModal .upload input {
                        position: absolute;
                        right: 0;
                        height: 100%;
                        opacity: 0;
                        width: 22%;
                    }

                    /* spread_template_container">
                <div class="spread_heading */
                    .scrollContainer {
                        padding-top: 0;
                    }

                    .spread_template_container .spread_heading {
                        /* position: sticky; */
                        top: 0;
                        background: #F8F7FB;
                        padding-bottom: 8px;
                        box-shadow: 0px 2px 6px -10px #0002;
                    }
                </style>
                <div id="tbl_container">
                    <div class="scrollable">
                        <table class="table custom_table no_border toggle_table">
                            <tr>
                                <td class="bold grey_bg b_top b_left spanish hidden">Confirmación Cuadre Importes</td>
                                <td class="bold grey_bg b_top b_left">Confirmation Square Amounts</td>
                                <td colspan="2" class="b_top" contenteditable="false"><input type="month" placeholder="YYYY-MM" id="monthYear" name="conf_sqr_amt[]" value="<?php echo formatExceldate($excelData[$i]['conf_sqr_amt']) ?>"></td>
                                <td colspan="2" class="b_top" contenteditable="false"><input type="month" placeholder="YYYY-MM" id="monthYear" name="conf_sqr_amt[]" value="<?php echo formatExceldate($excelData[$i+1]['conf_sqr_amt']) ?>"></td>
                                <td colspan="2" class="b_top" contenteditable="false"><input type="month" placeholder="YYYY-MM" id="monthYear" name="conf_sqr_amt[]" value="<?php echo formatExceldate($excelData[$i+2]['conf_sqr_amt']) ?>"></td>
                                <td colspan="2" class="b_top" contenteditable="false"><input type="month" placeholder="YYYY-MM" id="monthYear" name="conf_sqr_amt[]" value="<?php echo formatExceldate($excelData[$i+3]['conf_sqr_amt']) ?>"></td>
                                <td colspan="2" class="b_top" contenteditable="false"><input type="month" placeholder="YYYY-MM" id="monthYear" name="conf_sqr_amt[]" value="<?php echo formatExceldate($excelData[$i+4]['conf_sqr_amt']) ?>"></td>
                                <td colspan="2" rowspan="4" class="b_top" style="color:#fff;background: rgb(59,96,141);">&nbsp;&nbsp;bluCognition&nbsp;Comments&nbsp;&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Audited/Not Audited</td>
                                <td class="bold grey_bg b_left">Audited/Not Audited</td>
                                <td colspan="2">
                                    <select name="is_audited[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i]['is_audited']) == "1") {
                                                    echo "selected";
                                                } ?> value="1">Audited</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i]['is_audited']) == "0") {
                                                    echo "selected";
                                                } ?> value="0">Not Audited</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="is_audited[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 1]['is_audited']) == "1") {
                                                    echo "selected";
                                                } ?> value="1">Audited</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 1]['is_audited']) == "0") {
                                                    echo "selected";
                                                } ?> value="0">Not Audited</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="is_audited[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 2]['is_audited']) == "1") {
                                                    echo "selected";
                                                } ?> value="1">Audited</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 2]['is_audited']) == "0") {
                                                    echo "selected";
                                                } ?> value="0">Not Audited</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="is_audited[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 3]['is_audited']) == "1") {
                                                    echo "selected";
                                                } ?> value="1">Audited</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 3]['is_audited']) == "0") {
                                                    echo "selected";
                                                } ?> value="0">Not Audited</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="is_audited[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 4]['is_audited']) == "1") {
                                                    echo "selected";
                                                } ?> value="1">Audited</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 4]['is_audited']) == "0") {
                                                    echo "selected";
                                                } ?> value="0">Not Audited</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Audit Firm Name</td>
                                <td class="bold grey_bg b_left">Audit Firm Name</td>
                                <td colspan="2" contenteditable="false"><input type="text" name="audit_firm_name[]" class="" value="<?php echo $excelData[$i]['audit_firm_name'] ?>"></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="audit_firm_name[]" class="" value="<?php echo $excelData[$i + 1]['audit_firm_name'] ?>"></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="audit_firm_name[]" class="" value="<?php echo $excelData[$i + 2]['audit_firm_name'] ?>"></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="audit_firm_name[]" class="" value="<?php echo $excelData[$i + 3]['audit_firm_name'] ?>"></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="audit_firm_name[]" class="" value="<?php echo $excelData[$i + 4]['audit_firm_name'] ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden np"><input readonly type="text" name="" class="" value="Auditor Opinion (Desfavorable/Favorable/Con Salvedades)"></td>
                                <td class="bold grey_bg np b_left"><input readonly type="text" name="" class="" value="Auditor Opinion (Desfavorable/Favorable/Con Salvedades)"></td>
                                <td colspan="2">
                                    <select name="audit_opinion[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i]['audit_opinion']) == "Desfavorable") {
                                                    echo "selected";
                                                } ?> value="Desfavorable">Desfavorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i]['audit_opinion']) == "Favorable") {
                                                    echo "selected";
                                                } ?> value="Favorable">Favorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i]['audit_opinion']) == "Con Salvedades") {
                                                    echo "selected";
                                                } ?> value="Con Salvedades">Con Salvadades</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="audit_opinion[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 1]['audit_opinion']) == "Desfavorable") {
                                                    echo "selected";
                                                } ?> value="Desfavorable">Desfavorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 1]['audit_opinion']) == "Favorable") {
                                                    echo "selected";
                                                } ?> value="Favorable">Favorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 1]['audit_opinion']) == "Con Salvedades") {
                                                    echo "selected";
                                                } ?> value="Con Salvedades">Con Salvadades</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="audit_opinion[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 2]['audit_opinion']) == "Desfavorable") {
                                                    echo "selected";
                                                } ?> value="Desfavorable">Desfavorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 2]['audit_opinion']) == "Favorable") {
                                                    echo "selected";
                                                } ?> value="Favorable">Favorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 2]['audit_opinion']) == "Con Salvedades") {
                                                    echo "selected";
                                                } ?> value="Con Salvedades">Con Salvadades</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="audit_opinion[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 3]['audit_opinion']) == "Desfavorable") {
                                                    echo "selected";
                                                } ?> value="Desfavorable">Desfavorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 3]['audit_opinion']) == "Favorable") {
                                                    echo "selected";
                                                } ?> value="Favorable">Favorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 3]['audit_opinion']) == "Con Salvedades") {
                                                    echo "selected";
                                                } ?> value="Con Salvedades">Con Salvadades</option>
                                    </select>
                                </td>
                                <td colspan="2">
                                    <select name="audit_opinion[]">
                                        <option value="">--select--</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 4]['audit_opinion']) == "Desfavorable") {
                                                    echo "selected";
                                                } ?> value="Desfavorable">Desfavorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 4]['audit_opinion']) == "Favorable") {
                                                    echo "selected";
                                                } ?> value="Favorable">Favorable</option>
                                        <option <?php if (formatExcelTxtData($excelData[$i + 4]['audit_opinion']) == "Con Salvedades") {
                                                    echo "selected";
                                                } ?> value="Con Salvedades">Con Salvadades</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Balance General</td>
                                <td class="bold grey_bg b_left">Balance sheet</td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL" data-formula="33=60=2" name="balance_sheet[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL" data-formula="33=60=4" name="balance_sheet[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL" data-formula="33=60=6" name="balance_sheet[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL" data-formula="33=60=8" name="balance_sheet[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL" data-formula="33=60=10" name="balance_sheet[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Utilidad Perdida del ejercicio</td>
                                <td class="bold grey_bg b_left">Net income (loss) for the year</td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL2" data-formula="58=86=2" name="net_income_last_year[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL2" data-formula="58=86=4" name="net_income_last_year[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL2" data-formula="58=86=6" name="net_income_last_year[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL2" data-formula="58=86=8" name="net_income_last_year[]" class="ifelse" value=""></td>
                                <td class="bold grey_bg" colspan="2"><input readonly type="text" data-formula-type="EQUAL2" data-formula="58=86=10" name="net_income_last_year[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg not_remove" colspan="11">&nbsp;</td>
                                <td colspan="2" class="no_bg"></td>
                            </tr>
                            <tr class="tbl_hd_tr">
                                <td colspan="11" class="bold grey_bg text-center b_left b_top not_remove">BALANCE GENERAL - ACTIVO <span class="toggleTableBtn t1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#688097" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3zM8 12h8" />
                                        </svg></span></td>
                                <td colspan="2" contenteditable="false" style="border-top: 1px solid #ddd;" class=" np"></td>
                            </tr>
                            <tr>
                                <td width="10%" class="b_left spanish hidden"></td>
                                <td width="10%" class="b_left"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr">(%)</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr">(%)</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr">(%)</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr">(%)</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr">(%)</td>
                                <td width="11%" colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Caja y Bancos</td>
                                <td class="b_left">Cash and Banks</td>
                                <td contenteditable="false"><input type="text" name="cash_and_banks[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['cash_and_banks']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cash_and_banks[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['cash_and_banks']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="cash_and_banks[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['cash_and_banks']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cash_and_banks[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['cash_and_banks']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cash_and_banks[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['cash_and_banks']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['cash_and_banks']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Clientes</td>
                                <td class="b_left">Customers</td>
                                <td contenteditable="false"><input type="text" name="customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['customers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['customers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['customers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['customers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['customers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['customers']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Deudores Diversos</td>
                                <td class="b_left">Various debtors</td>
                                <td contenteditable="false"><input type="text" name="various_debtors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['various_debtors']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_debtors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['various_debtors']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_debtors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['various_debtors']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_debtors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['various_debtors']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_debtors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['various_debtors']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['various_debtors']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Inventarios</td>
                                <td class="b_left">Inventories</td>
                                <td contenteditable="false"><input type="text" name="inventories[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['inventories']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="inventories[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['inventories']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="inventories[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['inventories']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="inventories[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['inventories']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="inventories[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['inventories']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['inventories']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Partes Relacionadas</td>
                                <td class="b_left">Related Parties</td>
                                <td contenteditable="false"><input type="text" name="related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['related_parties']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['related_parties']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['related_parties']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['related_parties']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['related_parties']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['related_parties']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Impuestos por recuperar</td>
                                <td class="b_left">Taxes to be recovered</td>
                                <td contenteditable="false"><input type="text" name="taxes_to_be_recovered[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['taxes_to_be_recovered']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_to_be_recovered[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['taxes_to_be_recovered']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_to_be_recovered[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['taxes_to_be_recovered']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_to_be_recovered[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['taxes_to_be_recovered']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_to_be_recovered[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['taxes_to_be_recovered']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['taxes_to_be_recovered']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Proyectos en Proceso</td>
                                <td class="b_left">Projects in Process</td>
                                <td contenteditable="false"><input type="text" name="projects_in_process[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['projects_in_process']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="projects_in_process[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['projects_in_process']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="projects_in_process[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['projects_in_process']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="projects_in_process[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['projects_in_process']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="projects_in_process[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['projects_in_process']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['projects_in_process']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Anticipo a Proveedores</td>
                                <td class="b_left">Advances to suppliers</td>
                                <td contenteditable="false"><input type="text" name="advances_to_suppliers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['advances_to_suppliers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advances_to_suppliers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['advances_to_suppliers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advances_to_suppliers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['advances_to_suppliers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advances_to_suppliers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['advances_to_suppliers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advances_to_suppliers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['advances_to_suppliers']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['advances_to_suppliers']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Activo Circulantes</td>
                                <td class="bold grey_bg b_left">Current Assets</td>
                                <td class="bold grey_bg"><input readonly type="text" name="current_assets[]" class="" data-formula-type="SUM" data-formula="9:16:2" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="current_assets[]" class="" data-formula-type="SUM" data-formula="9:16:4" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="current_assets[]" class="" data-formula-type="SUM" data-formula="9:16:6" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="current_assets[]" class="" data-formula-type="SUM" data-formula="9:16:8" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="current_assets[]" class="" data-formula-type="SUM" data-formula="9:16:10" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['current_assets']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Otros Activos No Circulantes</td>
                                <td class="b_left">Other Non-Current Assets</td>
                                <td contenteditable="false"><input type="text" name="other_non_current_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['other_non_current_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_non_current_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['other_non_current_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_non_current_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['other_non_current_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_non_current_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['other_non_current_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_non_current_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['other_non_current_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['other_non_current_assets']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Cuentas por Cobrar L.P.</td>
                                <td class="b_left">Accounts Receivable LP</td>
                                <td contenteditable="false"><input type="text" name="accounts_receivable_lp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['accounts_receivable_lp']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="accounts_receivable_lp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['accounts_receivable_lp']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="accounts_receivable_lp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['accounts_receivable_lp']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="accounts_receivable_lp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['accounts_receivable_lp']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="accounts_receivable_lp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['accounts_receivable_lp']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['accounts_receivable_lp']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Inversiones y CxC L.P.</td>
                                <td class="bold grey_bg  b_left">Investments and CxC LP</td>
                                <td class="bold grey_bg"><input readonly type="text" name="investments_and_cxc_lP[]" class="" data-formula-type="SUM" data-formula="18:19:2" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="investments_and_cxc_lP[]" class="" data-formula-type="SUM" data-formula="18:19:4" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="investments_and_cxc_lP[]" class="" data-formula-type="SUM" data-formula="18:19:6" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="investments_and_cxc_lP[]" class="" data-formula-type="SUM" data-formula="18:19:8" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="investments_and_cxc_lP[]" class="" data-formula-type="SUM" data-formula="18:19:10" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['investments_and_cxc_lP']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Terrenos e Inmuebles</td>
                                <td class="b_left">Land and Real Estate</td>
                                <td contenteditable="false"><input type="text" name="land_real_estate[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['land_real_estate']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="land_real_estate[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['land_real_estate']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="land_real_estate[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['land_real_estate']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="land_real_estate[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['land_real_estate']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="land_real_estate[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['land_real_estate']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['land_real_estate']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Maquinaria y Equipo</td>
                                <td class="b_left">Machinery and equipment</td>
                                <td contenteditable="false"><input type="text" name="machinery_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['machinery_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="machinery_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['machinery_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="machinery_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['machinery_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="machinery_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['machinery_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="machinery_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['machinery_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['machinery_equipment']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Equipo de Transporte</td>
                                <td class="b_left">Transportation Equipment</td>
                                <td contenteditable="false"><input type="text" name="transportation_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['transportation_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="transportation_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['transportation_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="transportation_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['transportation_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="transportation_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['transportation_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="transportation_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['transportation_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['transportation_equipment']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Equipo de Oficina</td>
                                <td class="b_left">Office team</td>
                                <td contenteditable="false"><input type="text" name="office_team[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['office_team']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="office_team[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['office_team']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="office_team[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['office_team']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="office_team[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['office_team']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="office_team[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['office_team']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['office_team']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Equipo de Computo</td>
                                <td class="b_left">Computer equipment</td>
                                <td contenteditable="false"><input type="text" name="computer_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['computer_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="computer_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['computer_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="computer_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['computer_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="computer_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['computer_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="computer_equipment[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['computer_equipment']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['computer_equipment']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="error b_left spanish hidden">Depreciación Acumulada</td>
                                <td class="error b_left">Accumulated depreciation</td>
                                <td contenteditable="false" class="error"><input type="text" name="accumulated_depreciation[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['accumulated_depreciation']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="accumulated_depreciation[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['accumulated_depreciation']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="accumulated_depreciation[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['accumulated_depreciation']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="accumulated_depreciation[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['accumulated_depreciation']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="accumulated_depreciation[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['accumulated_depreciation']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['accumulated_depreciation']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Otros Activos (No Maquinaria)</td>
                                <td class="b_left">Other Assets (No Machinery)</td>
                                <td contenteditable="false"><input type="text" name="other_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['other_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['other_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['other_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['other_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_assets[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['other_assets']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['other_assets']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Activo Fijo</td>
                                <td class="bold grey_bg b_left">Fixed Assets</td>
                                <td class="bold grey_bg"><input readonly type="text" name="fixed_assets[]" class="" data-formula-type="SUM" data-formula="21:27:2" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="fixed_assets[]" class="" data-formula-type="SUM" data-formula="21:27:4" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="fixed_assets[]" class="" data-formula-type="SUM" data-formula="21:27:6" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="fixed_assets[]" class="" data-formula-type="SUM" data-formula="21:27:8" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="fixed_assets[]" class="" data-formula-type="SUM" data-formula="21:27:10" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['fixed_assets']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="error b_left spanish hidden">Gasto de Instalación - Amortización</td>
                                <td class="error b_left">Installation Expense - Amortization</td>
                                <td contenteditable="false" class="error"><input type="text" name="installation_expense_amortization[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['installation_expense_amortization']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="installation_expense_amortization[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['installation_expense_amortization']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="installation_expense_amortization[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['installation_expense_amortization']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="installation_expense_amortization[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['installation_expense_amortization']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false" class="error"><input type="text" name="installation_expense_amortization[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['installation_expense_amortization']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['installation_expense_amortization']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Impuestos Dieferidos</td>
                                <td class="b_left">Deferred Tax</td>
                                <td contenteditable="false"><input type="text" name="deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['deferred_tax']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['deferred_tax']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['deferred_tax']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['deferred_tax']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['deferred_tax']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['deferred_tax']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Depósitos en Garantía</td>
                                <td class="b_left">Deposits in guarantee</td>
                                <td contenteditable="false"><input type="text" name="deposits_in_guarantee[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['deposits_in_guarantee']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deposits_in_guarantee[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['deposits_in_guarantee']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deposits_in_guarantee[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['deposits_in_guarantee']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deposits_in_guarantee[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['deposits_in_guarantee']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="deposits_in_guarantee[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['deposits_in_guarantee']) ?>"></td>
                                <td class=" grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['deposits_in_guarantee']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Activo Diferido</td>
                                <td class="bold grey_bg b_left">Deferred assets</td>
                                <td class="bold grey_bg"><input readonly type="text" name="deferred_assets[]" class="" data-formula-type="SUM" data-formula="29:31:2" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="deferred_assets[]" class="" data-formula-type="SUM" data-formula="29:31:4" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="deferred_assets[]" class="" data-formula-type="SUM" data-formula="29:31:6" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="deferred_assets[]" class="" data-formula-type="SUM" data-formula="29:31:8" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="deferred_assets[]" class="" data-formula-type="SUM" data-formula="29:31:10" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="PERCENT" data-formula="33" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['deferred_assets']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Activo Total</td>
                                <td class="bold grey_bg b_left">Total active</td>
                                <td class="bold grey_bg"><input readonly type="text" name="total_active[]" class="" data-formula-type="TOTALSUM" data-formula="2+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" class="" data-formula-type="SUMPERCENT" data-formula="3+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="total_active[]" class="" data-formula-type="TOTALSUM" data-formula="4+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" class="" data-formula-type="SUMPERCENT" data-formula="5+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="total_active[]" class="" data-formula-type="TOTALSUM" data-formula="6+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" class="" data-formula-type="SUMPERCENT" data-formula="7+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="total_active[]" class="" data-formula-type="TOTALSUM" data-formula="8+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" class="" data-formula-type="SUMPERCENT" data-formula="9+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="total_active[]" class="" data-formula-type="TOTALSUM" data-formula="10+17+20+28+32" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="" class="" data-formula-type="SUMPERCENT" data-formula="11+17+20+28+32" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['total_active']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg not_remove" colspan="11">&nbsp;</td>
                                <td colspan="2" contenteditable="false" class="no_bg"></td>
                            </tr>



                            <tr class="tbl_hd_tr">
                                <td colspan="11" class="bold grey_bg text-center b_left b_top not_remove">BALANCE GENERAL - PASIVO Y CAPITAL <span class="toggleTableBtn t2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#688097" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3zM8 12h8" />
                                        </svg></span></td>
                                <td colspan="2" contenteditable="false" style="border-top: 1px solid #ddd;" class=" np"></td>
                            </tr>
                            <tr>
                                <td width="10%" class="b_left spanish hidden"></td>
                                <td width="10%" class="b_left"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="cntr"></td>
                                <td width="11%" colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden np"><input readonly type="text" value="Pasivo Financiero Corto Plazo + (PCLP)"></td>
                                <td class="np b_left"><input readonly type="text" value="Short Term Financial Liabilities + (PCLP)"></td>
                                <td contenteditable="false"><input type="text" name="stfl_plus_pclp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['stfl_plus_pclp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="stfl_plus_pclp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['stfl_plus_pclp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="stfl_plus_pclp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['stfl_plus_pclp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="stfl_plus_pclp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['stfl_plus_pclp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="stfl_plus_pclp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['stfl_plus_pclp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['stfl_plus_pclp']) ?>"></td>
                            </tr>
                            <!-- <tr>
                    <td class="b_left spanish hidden">   No usar este renglón   (Ocultar)</td>
                    <td> Do not use this line (Hide)</td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td  colspan="2" contenteditable="false"><input type="text" name="" class="" value=""></td>
                    </tr> -->
                            <tr>
                                <td class="b_left spanish hidden"> Proveedores</td>
                                <td class="b_left"> Providers</td>
                                <td contenteditable="false"><input type="text" name="providers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['providers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="providers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['providers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="providers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['providers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="providers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['providers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="providers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['providers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['providers']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Partes Relacionadas</td>
                                <td class="b_left"> Related Parties</td>
                                <td contenteditable="false"><input type="text" name="p_related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['p_related_parties']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="p_related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['p_related_parties']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="p_related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['p_related_parties']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="p_related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['p_related_parties']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="p_related_parties[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['p_related_parties']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['p_related_parties']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Impuestos por Pagar C.P.</td>
                                <td class="b_left"> Taxes for Paying CP</td>
                                <td contenteditable="false"> <input type="text" name="taxes_paying_cp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['taxes_paying_cp']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_paying_cp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['taxes_paying_cp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_paying_cp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['taxes_paying_cp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_paying_cp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['taxes_paying_cp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="taxes_paying_cp[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['taxes_paying_cp']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['taxes_paying_cp']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Acreedores Diversos</td>
                                <td class="b_left"> Various creditors</td>
                                <td contenteditable="false"><input type="text" name="various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['various_creditors']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Anticipo de Clientes</td>
                                <td class="b_left"> Advance customers</td>
                                <td contenteditable="false"><input type="text" name="advance_customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['advance_customers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advance_customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['advance_customers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advance_customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['advance_customers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advance_customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['advance_customers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="advance_customers[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['advance_customers']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['advance_customers']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Pasivo a Corto Plazo</td>
                                <td class="bold grey_bg b_left">Passive in a short time</td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_in_short_time[]" data-formula-type="SUM" data-formula="37:42:2" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_in_short_time[]" data-formula-type="SUM" data-formula="37:42:4" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_in_short_time[]" data-formula-type="SUM" data-formula="37:42:6" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" name="pst_in_short_time[]" data-formula-type="SUM" data-formula="37:42:8" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_in_short_time[]" data-formula-type="SUM" data-formula="37:42:10" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_in_short_time']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Pasivo Financiero Largo Plazo</td>
                                <td class="b_left"> Long Term Financial Liabilities</td>
                                <td contenteditable="false"><input type="text" name="ltfl[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['ltfl']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="ltfl[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['ltfl']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="ltfl[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['ltfl']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="ltfl[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['ltfl']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="ltfl[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['ltfl']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['ltfl']) ?>"></td>
                            </tr>
                            <!-- <tr>
                    <td class="b_left spanish hidden">  No usar este renglón (Ocultar)</td>
                    <td> Do not use this line (Hide)</td>
                    <td contenteditable="false"> 0.00 </td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td contenteditable="false"><input type="text" name="" class="" value=""></td>
                    <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                    <td  colspan="2" contenteditable="false"><input type="text" name="" class="" value=""></td>
                </tr> -->
                            <tr>
                                <td class="b_left spanish hidden"> Acreedores Diversos </td>
                                <td class="b_left"> Various creditors </td>
                                <td contenteditable="false"><input type="text" name="pst_various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['pst_various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['pst_various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['pst_various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['pst_various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_various_creditors[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['pst_various_creditors']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_various_creditors']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Impuestos Diferidos</td>
                                <td class="b_left"> Deferred Tax</td>
                                <td contenteditable="false"><input type="text" name="pst_deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['pst_deferred_tax']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['pst_deferred_tax']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['pst_deferred_tax']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['pst_deferred_tax']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="pst_deferred_tax[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['pst_deferred_tax']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_deferred_tax']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Obligaciones Laborales</td>
                                <td class="b_left"> Laboral obligations</td>
                                <td contenteditable="false"><input type="text" name="laboral_obligations[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['laboral_obligations']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="laboral_obligations[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['laboral_obligations']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="laboral_obligations[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['laboral_obligations']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="laboral_obligations[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['laboral_obligations']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="laboral_obligations[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['laboral_obligations']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['laboral_obligations']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> CxP y Otros Pasivos L.P. </td>
                                <td class="b_left"> CxP and Other LP Liabilities </td>
                                <td contenteditable="false"><input type="text" name="cxp_other_lp_liabilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['cxp_other_lp_liabilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cxp_other_lp_liabilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['cxp_other_lp_liabilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cxp_other_lp_liabilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['cxp_other_lp_liabilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cxp_other_lp_liabilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['cxp_other_lp_liabilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="cxp_other_lp_liabilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['cxp_other_lp_liabilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['cxp_other_lp_liabilities']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Pasivo a Largo Plazo</td>
                                <td class="bold grey_bg b_left">Long-term liabilities</td>
                                <td class="bold grey_bg"><input readonly type="text" name="pst_long_term_liabilities[]" data-formula-type="SUM" data-formula="44:48:2" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_long_term_liabilities[]" data-formula-type="SUM" data-formula="44:48:4" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_long_term_liabilities[]" data-formula-type="SUM" data-formula="44:48:6" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_long_term_liabilities[]" data-formula-type="SUM" data-formula="44:48:8" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_long_term_liabilities[]" data-formula-type="SUM" data-formula="44:48:10" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_long_term_liabilities']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Pasivo Total</td>
                                <td class="bold grey_bg b_left">Totally passive</td>
                                <td class="bold grey_bg"> <input readonly type="text" name="totally_passive[]" data-formula-type="TOTALSUM" data-formula="2+43+49" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="totally_passive[]" data-formula-type="TOTALSUM" data-formula="4+43+49" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="totally_passive[]" data-formula-type="TOTALSUM" data-formula="6+43+49" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="totally_passive[]" data-formula-type="TOTALSUM" data-formula="8+43+49" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="totally_passive[]" data-formula-type="TOTALSUM" data-formula="10+43+49" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['totally_passive']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold b_left spanish hidden cntr2">CAPITAL CONTABLE </td>
                                <td class="bold cntr2 b_left">STOCKHOLDERS 'EQUITY </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Capital Social </td>
                                <td class="b_left"> Social capital </td>
                                <td contenteditable="false"> <input type="text" name="social_capital[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['social_capital']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="social_capital[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['social_capital']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="social_capital[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['social_capital']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="social_capital[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['social_capital']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="social_capital[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['social_capital']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['social_capital']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Reserva Legal</td>
                                <td class="b_left"> Legal Reserve</td>
                                <td contenteditable="false"><input type="text" name="legal_reserve[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['legal_reserve']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="legal_reserve[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['legal_reserve']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="legal_reserve[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['legal_reserve']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="legal_reserve[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['legal_reserve']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="legal_reserve[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['legal_reserve']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['legal_reserve']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Aportaciones por Capitalizar</td>
                                <td class="b_left"> Contributions to Capitalize</td>
                                <td contenteditable="false"> <input type="text" name="contributions_to_capitalize[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['contributions_to_capitalize']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="contributions_to_capitalize[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['contributions_to_capitalize']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="contributions_to_capitalize[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['contributions_to_capitalize']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="contributions_to_capitalize[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['contributions_to_capitalize']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="contributions_to_capitalize[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['contributions_to_capitalize']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['contributions_to_capitalize']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Prima en Suscripcion de Acciones</td>
                                <td class="b_left"> Share Subscription Premium</td>
                                <td contenteditable="false"><input type="text" name="share_subscription_premium[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['share_subscription_premium']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="share_subscription_premium[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['share_subscription_premium']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="share_subscription_premium[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['share_subscription_premium']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="share_subscription_premium[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['share_subscription_premium']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="share_subscription_premium[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['share_subscription_premium']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['share_subscription_premium']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Otras Cuentas de Capital (Actualización)</td>
                                <td class="b_left"> Other Capital Accounts (Update)</td>
                                <td contenteditable="false"><input type="text" name="other_capital_accounts[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['other_capital_accounts']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_capital_accounts[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['other_capital_accounts']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_capital_accounts[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['other_capital_accounts']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_capital_accounts[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['other_capital_accounts']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_capital_accounts[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['other_capital_accounts']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['other_capital_accounts']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Utilidades Acumuladas </td>
                                <td class="b_left"> Acumulated utilities </td>
                                <td contenteditable="false"> <input type="text" name="acumulated_utilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['acumulated_utilities']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="acumulated_utilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['acumulated_utilities']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="acumulated_utilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['acumulated_utilities']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="acumulated_utilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['acumulated_utilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="acumulated_utilities[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['acumulated_utilities']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['acumulated_utilities']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"> Utilidad del Ejercicio</td>
                                <td class="b_left"> Profit for the Year</td>
                                <td contenteditable="false"> <input type="text" name="profit_year[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['profit_year']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="profit_year[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['profit_year']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="profit_year[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['profit_year']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_year[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['profit_year']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_year[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['profit_year']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['profit_year']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Capital Contable </td>
                                <td class="bold grey_bg b_left">Stockholders' Equity </td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_stockholders_equity[]" data-formula-type="SUM" data-formula="52:58:2" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_stockholders_equity[]" data-formula-type="SUM" data-formula="52:58:4" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_stockholders_equity[]" data-formula-type="SUM" data-formula="52:58:6" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_stockholders_equity[]" data-formula-type="SUM" data-formula="52:58:8" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_stockholders_equity[]" data-formula-type="SUM" data-formula="52:58:10" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="33" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_stockholders_equity']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">Pasivo + Capital</td>
                                <td class="bold grey_bg b_left">Liabilities + Capital</td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_liabilities_capital[]" data-formula-type="TOTALSUM" data-formula="2+50+59" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="SUMPERCENT" data-formula="3+43+49+59" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_liabilities_capital[]" data-formula-type="TOTALSUM" data-formula="4+50+59" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="SUMPERCENT" data-formula="5+43+49+59" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_liabilities_capital[]" data-formula-type="TOTALSUM" data-formula="6+50+59" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="SUMPERCENT" data-formula="7+43+49+59" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_liabilities_capital[]" data-formula-type="TOTALSUM" data-formula="8+50+59" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="SUMPERCENT" data-formula="9+43+49+59" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" name="pst_liabilities_capital[]" data-formula-type="TOTALSUM" data-formula="10+50+59" class="" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" name="" data-formula-type="SUMPERCENT" data-formula="11+43+49+59" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['pst_liabilities_capital']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg not_remove" colspan="11">&nbsp;</td>
                                <td colspan="2" contenteditable="false" class="no_bg"></td>
                            </tr>



                            <tr class="tbl_hd_tr">
                                <td colspan="11" class="bold grey_bg text-center b_left b_top not_remove">ESTADO DE RESULTADOS<span class="toggleTableBtn t3"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#688097" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3zM8 12h8" />
                                        </svg></span></td>
                                <td colspan="2" contenteditable="false" style="border-top: 1px solid #ddd;" class=" np"></td>
                            </tr>
                            <tr>
                                <td width="10%" class="b_left spanish hidden"></td>
                                <td width="10%" class="b_left"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%"> </td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%"> </td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%"></td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%"></td>
                                <td width="11%" colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">MESES COMPRENDIDOS</td>
                                <td class="b_left">MONTHS UNDERSTOOD</td>
                                <td><input readonly type="text" data-formula-type="ASSIGN_DATE" data-formula="MONTH" name="months_understood[]" class="" value=""> </td>
                                <td class="persign">(%)</td>
                                <td><input readonly type="text" data-formula-type="ASSIGN_DATE" data-formula="MONTH" name="months_understood[]" class="" value=""> </td>
                                <td class="persign">(%)</td>
                                <td><input readonly type="text" data-formula-type="ASSIGN_DATE" data-formula="MONTH" name="months_understood[]" class="" value=""> </td>
                                <td class="persign">(%)</td>
                                <td> <input readonly type="text" data-formula-type="ASSIGN_DATE" data-formula="MONTH" name="months_understood[]" class="" value=""> </td>
                                <td class="persign">(%)</td>
                                <td> <input readonly type="text" data-formula-type="ASSIGN_DATE" data-formula="MONTH" name="months_understood[]" class="" value=""> </td>
                                <td class="persign">(%)</td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['months_understood']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"></td>
                                <td class="b_left">&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="grey_bg b_left spanish hidden">PROMEDIO VENTAS MENSUALES</td>
                                <td class="grey_bg b_left">AVERAGE MONTHLY SALES</td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="AVGMONTH" data-formula="67/64/2" name="avg_monthly_sales[]" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="AVGMONTH" data-formula="67/64/4" name="avg_monthly_sales[]" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="AVGMONTH" data-formula="67/64/6" name="avg_monthly_sales[]" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="AVGMONTH" data-formula="67/64/8" name="avg_monthly_sales[]" class="" value=""> </td>
                                <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="AVGMONTH" data-formula="67/64/10" name="avg_monthly_sales[]" class="" value=""> </td>
                                <td class="grey_bg"><input readonly type="text" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['avg_monthly_sales']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Ventas Netas</td>
                                <td class="b_left">Net sales</td>
                                <td contenteditable="false"> <input type="text" name="net_sales[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['net_sales']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" name="" class="num" value="100"></td>
                                <td contenteditable="false"> <input type="text" name="net_sales[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['net_sales']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" name="" class="num" value="100"></td>
                                <td contenteditable="false"> <input type="text" name="net_sales[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['net_sales']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" name="" class="num" value="100"></td>
                                <td contenteditable="false"><input type="text" name="net_sales[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['net_sales']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" name="" class="num" value="100"></td>
                                <td contenteditable="false"><input type="text" name="net_sales[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['net_sales']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" name="" class="num" value="100"></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['net_sales']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Costo de Ventas</td>
                                <td class="b_left">Sales cost</td>
                                <td contenteditable="false"> <input type="text" name="sales_cost[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['sales_cost']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="sales_cost[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['sales_cost']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="sales_cost[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['sales_cost']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="sales_cost[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['sales_cost']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="sales_cost[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['sales_cost']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['sales_cost']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">UTILIDAD BRUTA</td>
                                <td class="bold grey_bg b_left">GROSS PROFIT</td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="67-68" name="gross_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="67-68" name="gross_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="67-68" name="gross_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="DIFFERENCE" data-formula="67-68" name="gross_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="67-68" name="gross_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['gross_profit']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"></td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Gastos de Administración</td>
                                <td class="b_left">Administration Expenses</td>
                                <td contenteditable="false"> <input type="text" name="admin_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['admin_expenses']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="admin_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['admin_expenses']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="admin_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['admin_expenses']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="admin_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['admin_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="admin_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['admin_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['admin_expenses']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Gastos de Ventas</td>
                                <td class="b_left">Selling expenses</td>
                                <td contenteditable="false"><input type="text" name="selling_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['selling_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="selling_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['selling_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="selling_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['selling_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="selling_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['selling_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="selling_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['selling_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['selling_expenses']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Total Gastos de Operación </td>
                                <td class="b_left">Total operation costs </td>
                                <td contenteditable="false"> <input readonly type="text" data-formula-type="TOTALSUM" data-formula="2+71+72" name="total_opr_cost[]" class="num" value="<?php // echo formatExcelNumData($excelData[$i+]['total_opr_cost'])
                                                                                                                                                                                        ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input readonly type="text" data-formula-type="TOTALSUM" data-formula="4+71+72" name="total_opr_cost[]" class="num" value="<?php // echo formatExcelNumData($excelData[$i+]['total_opr_cost'])
                                                                                                                                                                                        ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input readonly type="text" data-formula-type="TOTALSUM" data-formula="6+71+72" name="total_opr_cost[]" class="num" value="<?php // echo formatExcelNumData($excelData[$i+]['total_opr_cost'])
                                                                                                                                                                                        ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input readonly type="text" data-formula-type="TOTALSUM" data-formula="8+71+72" name="total_opr_cost[]" class="num" value="<?php // echo formatExcelNumData($excelData[$i+]['total_opr_cost'])
                                                                                                                                                                                        ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input readonly type="text" data-formula-type="TOTALSUM" data-formula="10+71+72" name="total_opr_cost[]" class="num" value="<?php // echo formatExcelNumData($excelData[$i+]['total_opr_cost'])
                                                                                                                                                                                            ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['total_opr_cost']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">UTILIDAD OPERACIÓN</td>
                                <td class="bold grey_bg b_left">OPERATING INCOME</td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="DIFFERENCE" data-formula="69-73" name="operating_income[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="69-73" name="operating_income[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="69-73" name="operating_income[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="69-73" name="operating_income[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFERENCE" data-formula="69-73" name="operating_income[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['operating_income']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"></td>
                                <td class="b_left">&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Gastos Financieros</td>
                                <td class="b_left">Financial expenses</td>
                                <td contenteditable="false"><input type="text" name="fs_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['fs_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="fs_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['fs_expenses']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="fs_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['fs_expenses']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fs_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['fs_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fs_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['fs_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['fs_expenses']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">(Productos Financieros)</td>
                                <td class="b_left">(Financial products)</td>
                                <td contenteditable="false"><input type="text" name="fn_products[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['fn_products']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fn_products[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['fn_products']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fn_products[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['fn_products']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fn_products[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['fn_products']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="fn_products[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['fn_products']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['fn_products']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">(Utilidad) o Pérdida Cambiaria </td>
                                <td class="b_left">(Profit) or Exchange Loss </td>
                                <td contenteditable="false"><input type="text" name="profit_or_loss[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['profit_or_loss']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_or_loss[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['profit_or_loss']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_or_loss[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['profit_or_loss']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_or_loss[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['profit_or_loss']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="profit_or_loss[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['profit_or_loss']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['profit_or_loss']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Posición Monetaria</td>
                                <td class="b_left">Monetary position</td>
                                <td contenteditable="false"><input type="text" name="monetoty_position[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['monetoty_position']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="monetoty_position[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['monetoty_position']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="monetoty_position[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['monetoty_position']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="monetoty_position[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['monetoty_position']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="monetoty_position[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['monetoty_position']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['monetoty_position']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Otros Gastos (Productos)</td>
                                <td class="b_left">Other Expenses (Products)</td>
                                <td contenteditable="false"><input type="text" name="other_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['other_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['other_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['other_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['other_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_expenses[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['other_expenses']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['other_expenses']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Partidas Extraodinarias</td>
                                <td class="b_left">Extraordinary Items</td>
                                <td contenteditable="false"><input type="text" name="extraordinary_items[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['extraordinary_items']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="extraordinary_items[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['extraordinary_items']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="extraordinary_items[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['extraordinary_items']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="extraordinary_items[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['extraordinary_items']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="extraordinary_items[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['extraordinary_items']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['extraordinary_items']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">UTILIDAD ANTES IMPS</td>
                                <td class="bold grey_bg b_left">PROFIT BEFORE IMPS</td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="2:74:76:81" name="profit_before_imps[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="4:74:76:81" name="profit_before_imps[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="6:74:76:81" name="profit_before_imps[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="8:74:76:80" name="profit_before_imps[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="10:74:76:81" name="profit_before_imps[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['profit_before_imps']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"></td>
                                <td class="b_left">&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden np"><input readonly type="text" value="Provisión de ISR y PTU"></td>
                                <td class="np b_left"><input readonly type="text" value="Provision of income tax and profit sharing"></td>
                                <td contenteditable="false"><input type="text" name="prov_of_it[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['prov_of_it']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="prov_of_it[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['prov_of_it']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="prov_of_it[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['prov_of_it']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="prov_of_it[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['prov_of_it']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="prov_of_it[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['prov_of_it']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['prov_of_it']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Otras Provisiones</td>
                                <td class="b_left">Other Provisions</td>
                                <td contenteditable="false"><input type="text" name="other_prov[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['other_prov']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_prov[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['other_prov']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_prov[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['other_prov']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_prov[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['other_prov']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"><input type="text" name="other_prov[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['other_prov']) ?>"></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['other_prov']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="bold grey_bg b_left spanish hidden">UTILIDAD NETA</td>
                                <td class="bold grey_bg b_left">NET PROFIT</td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="2:82:84:85" name="net_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="4:82:84:85" name="net_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="6:82:84:85" name="net_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="8:82:84:85" name="net_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="bold grey_bg"> <input readonly type="text" data-formula-type="DIFFSUM" data-formula="10:82:84:85" name="net_profit[]" class="num" value=""> </td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['net_profit']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden"></td>
                                <td class="b_left">&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" contenteditable="false" class="nill"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Depreciación Aplicada en Resultados</td>
                                <td class="b_left">Applied Depreciation in Results</td>
                                <td class="grey_bg"> <input readonly type="text" data-formula-type="ASSIGN_NUM" data-formula="88.4" name="applied_depreciation[]" class="num" value=""> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="grey_bg"> <input readonly type="text" data-formula-type="RESULT" data-formula="4-26" name="applied_depreciation[]" class="num" value=""> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="RESULT" data-formula="6-26" name="applied_depreciation[]" class="num" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="RESULT" data-formula="8-26" name="applied_depreciation[]" class="num" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="RESULT" data-formula="10-26" name="applied_depreciation[]" class="num" value=""> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['applied_depreciation']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="b_left spanish hidden">Amortización Aplicada en Resultados </td>
                                <td class="b_left">Amortization Applied to Results </td>
                                <td contenteditable="false"> <input type="text" name="amortization_applied[]" class="num" value="<?php echo formatExcelNumData($excelData[$i]['amortization_applied']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="amortization_applied[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 1]['amortization_applied']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="amortization_applied[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 2]['amortization_applied']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="amortization_applied[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 3]['amortization_applied']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td contenteditable="false"> <input type="text" name="amortization_applied[]" class="num" value="<?php echo formatExcelNumData($excelData[$i + 4]['amortization_applied']) ?>"> </td>
                                <td class="grey_bg"><input readonly type="text" data-formula-type="PERCENT" data-formula="67" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"><input type="text" name="blucogComments[]" class="blucogComments" placeholder="..." value="<?php echo formatExcelTxtData($blucogComments[0]['amortization_applied']) ?>"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg not_remove" colspan="11">&nbsp;</td>
                                <td colspan="2" contenteditable="false" class="no_bg"></td>
                            </tr>

                            <tr class="tbl_hd_tr">
                                <td colspan="11" class="bold grey_bg text-center b_top b_left not_remove" style="padding: 4px;">Index Score<span class="toggleTableBtn t4"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#688097" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3zM8 12h8" />
                                        </svg></span></td>
                                <td colspan="2" contenteditable="false" style="border-top: 1px solid #ddd;" class="np"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden" width="10%"></td>
                                <td width="10%" class="bold grey_bg b_left brdr" width="20%">Ratios</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="bold grey_bg signal">Signals</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="bold grey_bg signal">Signals</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="bold grey_bg signal">Signals</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="bold grey_bg signal">Signals</td>
                                <td width="13%" class="bold grey_bg"><input readonly type="text" data-formula-type="ASSIGN_DATE" name="" class="" value=""></td>
                                <td width="3%" class="bold grey_bg signal">Signals</td>
                                <td width="11%" colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Retained Earnings to Asset Ratio</td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="57/33/2" data-desc="ratio" name="rtnd_erng_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="93>0.8>2" name="rtnd_erng_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="57/33/4" data-desc="ratio" name="rtnd_erng_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="93>0.8>4" name="rtnd_erng_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="57/33/6" data-desc="ratio" name="rtnd_erng_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="93>0.8>6" name="rtnd_erng_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="57/33/8" data-desc="ratio" name="rtnd_erng_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="93>0.8>8" name="rtnd_erng_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="57/33/10" data-desc="ratio" name="rtnd_erng_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="93>0.8>10" name="rtnd_erng_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Equity to Asset Ratio</td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="59/33/2" data-desc="ratio" name="equity_to_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="94>0.8>2" name="equity_to_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="59/33/4" data-desc="ratio" name="equity_to_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="94>0.8>4" name="equity_to_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="59/33/6" data-desc="ratio" name="equity_to_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="94>0.8>6" name="equity_to_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="59/33/8" data-desc="ratio" name="equity_to_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="94>0.8>8" name="equity_to_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="59/33/10" data-desc="ratio" name="equity_to_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="94>0.8>10" name="equity_to_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Current Ratio</td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="17/43/2" data-desc="ratio" name="current_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="95>4>2" name="current_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="17/43/4" data-desc="ratio" name="current_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="95>4>4" name="current_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="17/43/6" data-desc="ratio" name="current_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="95>4>6" name="current_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="17/43/8" data-desc="ratio" name="current_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="95>4>8" name="current_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="17/43/10" data-desc="ratio" name="current_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="95>4>10" name="current_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Debt Service Ratio</td>
                                <td><input readonly type="text" data-formula-type="DIVSUM" data-formula="2:74:37:44:48" name="debt_service_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="96>10>2" name="debt_service_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="DIVSUM" data-formula="4:74:37:44:48" name="debt_service_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="96>10>4" name="debt_service_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="DIVSUM" data-formula="6:74:37:44:48" name="debt_service_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="96>10>6" name="debt_service_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="DIVSUM" data-formula="8:74:37:44:48" name="debt_service_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="96>10>8" name="debt_service_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="DIVSUM" data-formula="10:74:37:44:48" name="debt_service_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="96>10>10" name="debt_service_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Return on Asset Ratio</td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="86/33/2" data-desc="ratio" name="rtrn_on_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="97>0.2>2" name="rtrn_on_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="86/33/4" data-desc="ratio" name="rtrn_on_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="97>0.2>4" name="rtrn_on_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="86/33/6" data-desc="ratio" name="rtrn_on_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="97>0.2>6" name="rtrn_on_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="86/33/8" data-desc="ratio" name="rtrn_on_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="97>0.2>8" name="rtrn_on_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" data-formula-type="AVGMONTH" data-formula="86/33/10" data-desc="ratio" name="rtrn_on_asset_ratio[]" class="" value=""></td>
                                <td><input readonly type="text" data-formula-type="GREATER" data-formula="97>0.2>10" name="rtrn_on_asset_ratio_sgnl[]" class="ifelse" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Signal Sum</td>
                                <td><input readonly type="text" data-formula-type="SIGNALSUM" data-formula="93:97:3" name="signal_sum[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="num" value=""></td>
                                <td><input readonly type="text" data-formula-type="SIGNALSUM" data-formula="93:97:5" name="signal_sum[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="num" value=""></td>
                                <td><input readonly type="text" data-formula-type="SIGNALSUM" data-formula="93:97:7" name="signal_sum[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="num" value=""></td>
                                <td><input readonly type="text" data-formula-type="SIGNALSUM" data-formula="93:97:9" name="signal_sum[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="num" value=""></td>
                                <td><input readonly type="text" data-formula-type="SIGNALSUM" data-formula="93:97:11" name="signal_sum[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="num" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                            <tr>
                                <td class="padding no_bg index_sc hidden"></td>
                                <td class="bold b_left brdr">Index</td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="IFELSEIF" data-formula="98>0>2" name="index[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="IFELSEIF" data-formula="98>0>4" name="index[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="IFELSEIF" data-formula="98>0>6" name="index[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="IFELSEIF" data-formula="98>0>8" name="index[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="" value=""></td>
                                <td class="bold grey_bg"><input readonly type="text" data-formula-type="IFELSEIF" data-formula="98>0>10" name="index[]" class="ifelse" value=""></td>
                                <td><input readonly type="text" name="" class="" value=""></td>
                                <td colspan="2" contenteditable="false"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tbl_container" class="tabular_format spread_template_container excel_tab_box two">
                <?php include('fs_secondExcelTable.php') ?>
            </div>
        </div>
</div>
</form>
<?php include('footer.php'); ?>
<script>
    //   <script>
    $('.excel_tabs').on('click', 'li', function() {
        var value = $(this).attr("data-tab");
        $(".excel_tabs li").removeClass("active");
        $(this).addClass("active");
        $(".excel_tab_box").hide();
        $(".excel_tab_box." + value).show();
    })

    function imprtExcelModal() {
        // $("#d_edit_id").val(id);
        $('#imprtExcelModal').modal('show');
    }
    $('#imprtExcelModal .updateContainer button[aria-label=Close]').click(function() {
        $('.imprtExcelForm')[0].reset();
    });

    function clbckImportExcel(data) {
        // console.log(data.businessNameInfo.unique_id);
        $('.imprtExcelForm')[0].reset();
        // $('.custom_table.top_table tr td input#unique_id').val(data.businessNameInfo.unique_id);
        // $('.custom_table.top_table tr td input#business_name').val(data.businessNameInfo.business_name);
        // console.log(data.businessNameInfo.business_name);
        console.log(data.excelData);
        let is_audited;
        for (let i = 0; i < data.excelData.length; i++) {
            if (data.excelData[i].audit_firm_name) {
                $(`.toggle_table tr td[data-cell=0_${i+2}] input`).val(data.excelData[i].conf_sqr_amt);
                if (data.excelData[i].is_audited === "Audited") {
                    is_audited = 1;
                } else if (data.excelData[i].is_audited === "Not Audited") {
                    is_audited = 0;
                } else {
                    is_audited = "";
                }
                $(`.toggle_table tr td[data-cell=1_${i+2}] select`).val(is_audited);
                $(`.toggle_table tr td[data-cell=2_${i+2}] input`).val(data.excelData[i].audit_firm_name);
                $(`.toggle_table tr td[data-cell=3_${i+2}] select`).val(data.excelData[i].audit_opinion);
            }
        }

        for (let i = 0; i < data.excelData.length; i++) {
            if (data.excelData[i].audit_firm_name) {
                console.log(data.excelData[i].conf_sqr_amt);
                // if(true){

                $(`.toggle_table tr td[data-cell=9_${(i*2)+2}] input`).val(data.excelData[i].cash_and_banks);
                $(`.toggle_table tr td[data-cell=10_${(i*2)+2}] input`).val(data.excelData[i].customers);
                $(`.toggle_table tr td[data-cell=11_${(i*2)+2}] input`).val(data.excelData[i].various_debtors);
                $(`.toggle_table tr td[data-cell=12_${(i*2)+2}] input`).val(data.excelData[i].inventories);
                $(`.toggle_table tr td[data-cell=13_${(i*2)+2}] input`).val(data.excelData[i].related_parties);
                $(`.toggle_table tr td[data-cell=14_${(i*2)+2}] input`).val(data.excelData[i].taxes_to_be_recovered);
                $(`.toggle_table tr td[data-cell=15_${(i*2)+2}] input`).val(data.excelData[i].projects_in_process);
                $(`.toggle_table tr td[data-cell=16_${(i*2)+2}] input`).val(data.excelData[i].advances_to_suppliers);

                $(`.toggle_table tr td[data-cell=18_${(i*2)+2}] input`).val(data.excelData[i].other_non_current_assets);
                $(`.toggle_table tr td[data-cell=19_${(i*2)+2}] input`).val(data.excelData[i].accounts_receivable_lp);

                $(`.toggle_table tr td[data-cell=21_${(i*2)+2}] input`).val(data.excelData[i].land_real_estate);
                $(`.toggle_table tr td[data-cell=22_${(i*2)+2}] input`).val(data.excelData[i].machinery_equipment);
                $(`.toggle_table tr td[data-cell=23_${(i*2)+2}] input`).val(data.excelData[i].transportation_equipment);
                $(`.toggle_table tr td[data-cell=24_${(i*2)+2}] input`).val(data.excelData[i].office_team);
                $(`.toggle_table tr td[data-cell=25_${(i*2)+2}] input`).val(data.excelData[i].computer_equipment);
                $(`.toggle_table tr td[data-cell=26_${(i*2)+2}] input`).val(data.excelData[i].accumulated_depreciation);
                $(`.toggle_table tr td[data-cell=27_${(i*2)+2}] input`).val(data.excelData[i].other_assets);

                $(`.toggle_table tr td[data-cell=29_${(i*2)+2}] input`).val(data.excelData[i].installation_expense_amortization);
                $(`.toggle_table tr td[data-cell=30_${(i*2)+2}] input`).val(data.excelData[i].deferred_tax);
                $(`.toggle_table tr td[data-cell=31_${(i*2)+2}] input`).val(data.excelData[i].deposits_in_guarantee);

                //         // pasivo table
                $(`.toggle_table tr td[data-cell=37_${(i*2)+2}] input`).val(data.excelData[i].stfl_plus_pclp);
                $(`.toggle_table tr td[data-cell=38_${(i*2)+2}] input`).val(data.excelData[i].providers);
                $(`.toggle_table tr td[data-cell=39_${(i*2)+2}] input`).val(data.excelData[i].p_related_parties);
                $(`.toggle_table tr td[data-cell=40_${(i*2)+2}] input`).val(data.excelData[i].taxes_paying_cp);
                $(`.toggle_table tr td[data-cell=41_${(i*2)+2}] input`).val(data.excelData[i].various_creditors);
                $(`.toggle_table tr td[data-cell=42_${(i*2)+2}] input`).val(data.excelData[i].advance_customers);

                $(`.toggle_table tr td[data-cell=44_${(i*2)+2}] input`).val(data.excelData[i].ltfl);
                $(`.toggle_table tr td[data-cell=45_${(i*2)+2}] input`).val(data.excelData[i].pst_various_creditors);
                $(`.toggle_table tr td[data-cell=46_${(i*2)+2}] input`).val(data.excelData[i].pst_deferred_tax);
                $(`.toggle_table tr td[data-cell=47_${(i*2)+2}] input`).val(data.excelData[i].laboral_obligations);
                $(`.toggle_table tr td[data-cell=48_${(i*2)+2}] input`).val(data.excelData[i].cxp_other_lp_liabilities);

                $(`.toggle_table tr td[data-cell=52_${(i*2)+2}] input`).val(data.excelData[i].social_capital);
                $(`.toggle_table tr td[data-cell=53_${(i*2)+2}] input`).val(data.excelData[i].legal_reserve);
                $(`.toggle_table tr td[data-cell=54_${(i*2)+2}] input`).val(data.excelData[i].contributions_to_capitalize);
                $(`.toggle_table tr td[data-cell=55_${(i*2)+2}] input`).val(data.excelData[i].share_subscription_premium);
                $(`.toggle_table tr td[data-cell=56_${(i*2)+2}] input`).val(data.excelData[i].other_capital_accounts);
                $(`.toggle_table tr td[data-cell=57_${(i*2)+2}] input`).val(data.excelData[i].acumulated_utilities);
                $(`.toggle_table tr td[data-cell=58_${(i*2)+2}] input`).val(data.excelData[i].profit_year);

                //         // result table
                $(`.toggle_table tr td[data-cell=67_${(i*2)+2}] input`).val(data.excelData[i].net_sales);
                $(`.toggle_table tr td[data-cell=68_${(i*2)+2}] input`).val(data.excelData[i].sales_cost);

                $(`.toggle_table tr td[data-cell=71_${(i*2)+2}] input`).val(data.excelData[i].admin_expenses);
                $(`.toggle_table tr td[data-cell=72_${(i*2)+2}] input`).val(data.excelData[i].selling_expenses);

                $(`.toggle_table tr td[data-cell=76_${(i*2)+2}] input`).val(data.excelData[i].fs_expenses);
                $(`.toggle_table tr td[data-cell=77_${(i*2)+2}] input`).val(data.excelData[i].fn_products);
                $(`.toggle_table tr td[data-cell=78_${(i*2)+2}] input`).val(data.excelData[i].profit_or_loss);
                $(`.toggle_table tr td[data-cell=79_${(i*2)+2}] input`).val(data.excelData[i].monetoty_position);
                $(`.toggle_table tr td[data-cell=80_${(i*2)+2}] input`).val(data.excelData[i].other_expenses);
                $(`.toggle_table tr td[data-cell=81_${(i*2)+2}] input`).val(data.excelData[i].extraordinary_items);

                $(`.toggle_table tr td[data-cell=84_${(i*2)+2}] input`).val(data.excelData[i].prov_of_it);
                $(`.toggle_table tr td[data-cell=85_${(i*2)+2}] input`).val(data.excelData[i].other_prov);

                $(`.toggle_table tr td[data-cell=89_${(i*2)+2}] input`).val(data.excelData[i].amortization_applied);

            }
        }

        console.log(data.blucogCmnt.length);
        let i = 0;
        for (const cmnt in data.blucogCmnt) {
            document.querySelectorAll("input.blucogComments")[i].value = data.blucogCmnt[cmnt];
            i++;
        }

        // for(let i=0; i < data.blucogCmnt.length; i++){
        //     $("input.blucogComments")[i].val(data.blucogCmnt["N"+i]);
        // }
        // console.log(data.blucogCmnt['N2']);
        // console.log($("input.blucogComments")[0]);

        $('#imprtExcelModal .updateContainer button[aria-label=Close]').click();
        $("input[type=text].num:read-write").each(function() {
            updateNumFormat($(this));
            // console.log()
        });

        updateCellValue();
        $("#saveDataBtn").click();

        $("#tabs_listing li[data-tab=one]").click();
    }


    $("#toggle").click(function() {
        if ($(this).hasClass('hide')) {
            $(".toggle_table tr td.spanish:not(.not_remove):first-child").removeClass("hidden");
            $(".toggle_table tr td.index_sc:not(.not_remove):first-child").removeClass("hidden");
            $(".toggle_table tr td.not_remove").attr("colspan", '12');
            $(".toggle_table tr td:nth-child(2)").removeClass("b_left");
            $(this).removeClass('hide')

        } else {
            $(".toggle_table tr td.spanish:not(.not_remove):first-child").addClass("hidden");
            $(".toggle_table tr td.index_sc:not(.not_remove):first-child").addClass("hidden");
            $(".toggle_table tr td.not_remove").attr("colspan", '11');
            $(".toggle_table tr td:nth-child(2)").addClass("b_left");
            $(this).addClass('hide')
        }
    })
    // $("#toggle").click()

    $('.toggleTableBtn').click(function() {
        $(this).toggleClass('hide');
        if (!$(this).hasClass('hide')) {
            $(this).children().html('<path d="M3 3h18v18H3zM8 12h8"/>')
        } else {
            $(this).children().html('<path d="M3 3h18v18H3zM12 8v8m-4-4h8"/>')
        }
        let a = 9;
        let z = 34;
        if ($(this).hasClass('t2')) {
            a = 37;
            z = 61;
        } else if ($(this).hasClass('t3')) {
            a = 64;
            z = 90;
        } else if ($(this).hasClass('t4')) {
            a = 93;
            z = 100;
        }

        for (let i = a; i <= z; i++) {
            $(`table.toggle_table tr:nth-child(${i})`).toggle();
            // $(`table.toggle_table tr:nth-child(${i})`).fadeToggle();
        }
    });


    // $(document).ready(function(){
    //         $('.toggle_table tbody').on('click', 'td', function () {
    // 	    console.log( $(this).parent().find('td').index(this) + "_" + $(this).parent().parent().find('tr').index($(this).parent()));
    //         });
    //     });

    $('.toggle_table tbody tr td').each(function() {
        let col = $(this).parent().find('td').index($(this));
        let row = $(this).parent().parent().find('tr').index($(this).parent());

        $(this).attr('data-cell', `${row}_${col}`);
        //  updateExcelColumn($(this).children())
    })

    function perFormat(val) {
        if (val === Infinity || val === -Infinity || val === NaN) {
            return 0;
        }
        return val;
    }

    function numFormat(n, sumFlag = 0) {
        let numPtrn = /[, ]+/g;

        let roundOff = Math.round((Number(n.replace(numPtrn, "")) + Number.EPSILON) * 100) / 100;
        let str = "";
        // if (roundOff === 0) {
        //     // str = "";
        //     return sumFlag ? '0.00' : "";
        // } else
        if (roundOff - Math.floor(roundOff) === 0) {
            str = ".00";
        } else if (roundOff * 10 - Math.floor(roundOff * 10) === 0) {
            str = "0";
        }
        // return isNaN(new Intl.NumberFormat('en-US').format(roundOff)) ? "0" : new Intl.NumberFormat('en-US').format(roundOff) + str ;

        return new Intl.NumberFormat('en-US').format(roundOff) + str;
    }

    function updateCellValue() {

        $(".main").css("pointer-events", "none");

        const d = new Date();
        let cell = 2;
        let numPattern = /[, ]+/g;
        let formula;
        let row, date, fm, sum, fmTotal, totalSum, fmPer, totalDif, dif, per, avg, per1, per2, sumPercent;
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        // let check = 0;
        $('.toggle_table tr td input[data-formula-type]:read-only').each(function() {
            formula = $(this).attr('data-formula-type');
            // check++;

            if (formula === "ASSIGN_DATE") {
                // let rowPos = /[, ]+/g;
                row = 0;
                date = new Date($(`.toggle_table tr td[data-cell=${row}_${cell}]`).children().val());

                if ($(this).attr('data-formula') === "MONTH") {

                    $(this).val(`${isNaN(date.getMonth() + 1) ? "" : date.getMonth() + 1}`);
                } else {
                    $(this).val(`${months[date.getMonth()] === undefined ? "" : months[date.getMonth()]}-${isNaN(date.getFullYear()) ? "" : date.getFullYear()}`);
                }
                cell++;
                // console.log(row, cell);
                if (cell > 6) {
                    cell = 2;
                }
            }

            if (formula === "SUM") {
                fm = $(this).attr('data-formula').split(":");
                sum = 0;
                // per = 0;
                // var numPattern = /[, ]+/g;
                for (let i = Number(fm[0]); i <= Number(fm[1]); i++) {
                    sum += Number($(`.toggle_table tr td[data-cell=${i}_${Number(fm[2])}]`).children().val().replace(numPattern, ""));
                }
                sum = isNaN(sum) ? 0 : sum;
                $(this).val(numFormat(`${sum}`, 1));
            }

            if (formula === "TOTALSUM") {
                fmTotal = $(this).attr('data-formula').split("+"); // 2+17+20+28+32
                totalSum = 0;
                // let numPattern = /[, ]+/g;
                for (let i = 1; i < fmTotal.length; i++) {
                    totalSum += Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[i])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, ""));
                }
                totalSum = isNaN(totalSum) ? 0 : totalSum;
                $(this).val(numFormat(`${totalSum}`));

            }

            if (formula === "AVGMONTH") {
                fmPer = $(this).parent().attr('data-cell').split("_"); // 9_3
                // console.log(fmPer);
                fm = $(this).attr('data-formula').split('/'); // fixed base value for average month 67/64/2
                avg = 0;
                // let numPattern = /[, ]+/g;
                avg = ((Number($(`.toggle_table tr td[data-cell=${Number(fm[0])}_${Number(fm[2])}]`).children().val().replace(numPattern, "")) / Number($(`.toggle_table tr td[data-cell=${Number(fm[1])}_${Number(fm[2])}]`).children().val().replace(numPattern, ""))));
                avg = isNaN(avg) ? 0 : avg;

                if ($(this).attr('data-desc') == "ratio") {
                    $(this).val(avg.toFixed(9));
                } else {
                    avg = avg === Infinity ? 0 : avg;
                    $(this).val(numFormat(`${avg}`, 1));
                }
            }

            if (formula === "DIFFERENCE") {
                fmPer = $(this).parent().attr('data-cell').split("_"); // 9_3
                fmTotal = $(this).attr('data-formula').split("-"); // 79-80
                totalDif = 0;
                // console.log(fmTotal);
                // console.log(fmPer);
                // let numPattern = /[, ]+/g;
                totalDif = Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[0])}_${Number(fmPer[1])}]`).children().val().replace(numPattern, "")) - Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[1])}_${Number(fmPer[1])}]`).children().val().replace(numPattern, ""));
                totalDif = isNaN(totalDif) ? 0 : totalDif;
                $(this).val(numFormat(`${totalDif}`, 1));
            }


            if (formula === "DIFFSUM") {
                fmTotal = $(this).attr('data-formula').split(":"); // 2:74:76:81 => col 2 >> 74 - (76 + 77 + 78 + 79 + 80 + 81)
                totalSum = 0;
                // let numPattern = /[, ]+/g;
                // dif = fmTotal[1];
                dif = Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[1])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, ""))
                for (let i = fmTotal[2]; i <= fmTotal[fmTotal.length - 1]; i++) {
                    // console.log(i, " a");
                    totalSum += Number($(`.toggle_table tr td[data-cell=${Number(i)}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, ""));
                }
                totalSum = dif - totalSum;
                totalSum = totalSum ? totalSum : 0;
                totalSum = isNaN(totalSum) ? 0 : totalSum;

                // console.log(totalSum);
                $(this).val(numFormat(`${totalSum}`, 1));
            }


            if (formula === "DIVSUM") {
                fmTotal = $(this).attr('data-formula').split(":"); // 2:74:37:44:48 => col 2 >> 74 / (37 + 44 + 48)
                totalSum = 0;
                // let numPattern = /[, ]+/g;
                // dif = fmTotal[1];
                dif = Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[1])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, ""))
                for (let i = 2; i < fmTotal.length; i++) {
                    // console.log(i, " a");
                    totalSum += Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[i])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, ""));
                }
                totalSum = dif / totalSum;
                totalSum = isNaN(totalSum) ? 0 : totalSum;
                totalSum = totalSum === Infinity ? 0 : totalSum;
                // console.log(totalSum);
                // console.log(typeof totalSum);
                // $(this).val(numFormat(`${totalSum}`, 1));
                $(this).val(totalSum.toFixed(9));

            }


            if (formula === "RESULT") {
                fmTotal = $(this).attr('data-formula').split("-"); // 4-33 => (33_4 - 33_2)* -1
                totalDif = 0;
                // console.log(fmTotal);
                // let numPattern = /[, ]+/g;
                totalDif = Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[1])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, "")) - Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[1])}_${Number(fmTotal[0])-2}]`).children().val().replace(numPattern, ""));
                // sum = isNaN(sum) ? "ERR" : sum;
                $(this).val(numFormat(`${totalDif}`));
            }

        });

        // const d2 = new Date();
        // console.log(d2.getTime() - d.getTime());


        $('.toggle_table tr td input[data-formula-type]:read-only').each(function() {
            formula = $(this).attr('data-formula-type');
            if (formula === "PERCENT") {
                fmPer = $(this).parent().attr('data-cell').split("_"); // 9_3
                fm = $(this).attr('data-formula'); // fixed base value for percentage
                per = 0;
                // let numPattern = /[, ]+/g;
                per = ((Number($(`.toggle_table tr td[data-cell=${Number(fmPer[0])}_${Number(fmPer[1])-1}]`).children().val().replace(numPattern, "")) / Number($(`.toggle_table tr td[data-cell=${Number(fm)}_${Number(fmPer[1])-1}]`).children().val().replace(numPattern, ""))) * 100);
                per = isNaN(per) ? 0 : per;
                per = perFormat(per);
                $(this).val(per.toFixed(2));
            }

            if (formula === "SUMPERCENT") {
                fmTotal = $(this).attr('data-formula').split("+"); // 2+17+20+28+32
                sumPercent = 0;
                // console.log(fmTotal);
                let numPattern = /[%]/g;
                for (let i = 1; i < fmTotal.length; i++) {
                    sumPercent += (Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[i])}_${Number(fmTotal[0])}]`).children().val().replace(numPattern, "")));
                }
                sumPercent = isNaN(sumPercent) ? 0 : sumPercent;
                $(this).val(Math.round(sumPercent));
            }

            if (formula === "ASSIGN_NUM") {
                fmTotal = $(this).attr('data-formula').split("."); // 88.3
                totalDif = 0;
                // console.log(fmTotal);
                // let numPattern = /[, ]+/g;
                totalDif = Number($(`.toggle_table tr td[data-cell=${Number(fmTotal[0])}_${Number(fmTotal[1])}]`).children().val().replace(numPattern, ""));
                // sum = isNaN(sum) ? "ERR" : sum;
                $(this).val(numFormat(`${totalDif}`));
            }

        });

        // const d3 = new Date();
        // console.log(d3.getTime() - d2.getTime());


        $('.toggle_table tr td input[class=ifelse][data-formula-type]:read-only').each(function() {
            formula = $(this).attr('data-formula-type');
            if (formula === "EQUAL") {
                fmPer = $(this).attr('data-formula').split("="); // fixed base value for percentage
                // let numPattern = /[, ]+/g;
                // console.log(fmPer);
                per1 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[0])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                per2 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[1])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                if (per1 == per2) {
                    $(this).val("Correcto");
                } else {
                    $(this).val("(NO CUADRA)");
                }
            }

            if (formula === "EQUAL2") {
                fmPer = $(this).attr('data-formula').split("="); // fixed base value for percentage
                // let numPattern = /[, ]+/g;
                // console.log(fmPer);
                per1 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[0])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                per2 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[1])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                if (per1 == per2) {
                    $(this).val("Correcto");
                } else {
                    $(this).val("(Dif./Bal. y Edo.Res.)");
                }
            }

            if (formula === "GREATER") {
                fmPer = $(this).attr('data-formula').split(">"); // 110>10>2
                // let numPattern = /[, ]+/g;
                // // console.log(fmPer);
                per1 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[0])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                // // per2 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[1])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                // console.log(Number(fmPer[1]));
                if (per1 > Number(fmPer[1])) {
                    $(this).val(1);
                } else {
                    $(this).val(0);
                }
            }

            if (formula === "SIGNALSUM") {
                fm = $(this).attr('data-formula').split(":"); // 93:97:2
                sum = 0;
                for (let i = Number(fm[0]); i <= Number(fm[1]); i++) {
                    sum += Number($(`.toggle_table tr td[data-cell=${i}_${Number(fm[2])}]`).children().val().replace(numPattern, ""));
                }
                sum = isNaN(sum) ? 0 : sum;
                $(this).val(sum);
            }

            if (formula === "IFELSEIF") {
                fmPer = $(this).attr('data-formula').split(">"); // 112>10>2
                // let numPattern = /[, ]+/g;
                // console.log(fmPer);
                per1 = Number($(`.toggle_table tr td[data-cell=${Number(fmPer[0])}_${Number(fmPer[2])}]`).children().val().replace(numPattern, ""));
                if (per1 == 0) {
                    $(this).val(1);
                } else if (per1 <= 3) {
                    $(this).val(2);
                } else if (per1 <= 5) {
                    $(this).val(3);
                } else {
                    $(this).val(4);
                }
            }

        });
        const d4 = new Date();
        console.log(d4.getTime() - d.getTime());
        $(".main").css("pointer-events", "auto");
    }


    updateCellValue();

    function emptyRowWarning(e) {
        let i = e.parent().attr('data-cell').split("_")[1];
        if (!$(`.toggle_table tr td[data-cell=2_${(i/2)+1}]`).children().val()) {
            toastr.error('Audit Firm Name is required');
            // console.log($(`.toggle_table tr td[data-cell=2_${(i/2)+1}]`).children().val());
            e.blur();
            return;
        }

    }

    // function updateNumFormat(e) {
    //     // let vl = e.val();

    //     let numPattern = /[, ]+/g;
    //     let vl = Number(e.val().replace(numPattern, ""));


    //     vl = isNaN(vl) ? 0 : vl;
    //     e.val(numFormat(`${vl}`))
    //     // console.log(numFormat(`123`));
    // }
    function updateNumFormat(e) {
        let vl = e.val();

        if (vl[0] === "=") {
            vl = vl.replace("=", "");
            vl = eval(vl);

        } else {
            let numPattern = /[, ]+/g;
            vl = Number(vl.replace(numPattern, ""));

        }


        vl = isNaN(vl) ? 0 : vl;
        // if(vl < 0){
        //     e.addClass("red");
        // }else{
        //     e.removeClass("red");
        // }
        e.val(numFormat(`${vl}`))
        // console.log(numFormat(`123`));
    }
    // $('.toggle_table tbody td input[type=text].num').each(updateNumFormat($(this)))

    const debounce = (func, delay) => {
        let debounceTimer
        return function() {
            const context = this
            const args = arguments
            clearTimeout(debounceTimer)
            debounceTimer
                = setTimeout(() => func.apply(context, args), delay)
        }
    }

    const throttleFunction = (func, delay) => {
        // Previously called time of the function
        let prev = 0;
        return (...args) => {
            // Current called time of the function
            let now = new Date().getTime();

            // Logging the difference between previously
            // called and current called timings
            // console.log(now-prev, delay);

            // If difference is greater than delay call
            // the function again.
            if (now - prev > delay) {
                prev = now;

                // ... is the spread operator here
                // returning the function with the
                // array of arguments
                return func(...args);
            }
        }
    }
    // btn.addEventListener("click", );

    $('.toggle_table tbody td input:enabled').attr('onchange', 'updateCellValue($(this));$("#saveDataBtn").click();');
    // $('.toggle_table tbody td input:enabled').change(throttleFunction(() => {
    //     updateCellValue();
    //     $("#saveDataBtn").click();
    // }, 6000));

    $('.toggle_table tbody td input[type=text].num').attr('onfocusout', 'updateNumFormat($(this))');
    $('.custom_table tr select').attr('onchange', '$("#saveDataBtn").click();');

    $('.toggle_table tbody td input.num:enabled').attr('onfocus', 'emptyRowWarning($(this))');

    // $('.toggle_table tbody td input[type=text].num').each
    
    $('.toggle_table tbody td').on('keypress', 'input', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            //   console.log(e.keyCode);
            let row = $(this).parent().parent().parent().find('tr').index($(this).parent().parent());
            let col = $(this).parent().parent().find('td').index($(this).parent());

            //   console.log(row + "_" + col);

            if ($(`.toggle_table tbody tr td[data-cell='${row+1}_${col}'] input`).is(':read-only')) {
                row++;
                // e.focusout();
            }
            // if(!$(`.toggle_table tbody tr td[data-cell='${row+1}_${col}'] input`)){
            //     row++;
            // }

            $('.toggle_table tbody tr')[row + 1].querySelectorAll('td')[col].firstElementChild.focus();
        }

    });
    
    $('.rfc_number').on('keypress', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            //   console.log(e.keyCode);
            let row = $(this).parent().parent().parent().find('tr').index($(this).parent().parent());
            let col = $(this).parent().parent().find('td').index($(this).parent());

            //   console.log(row + "_" + col);

            if ($(`.toggle_table tbody tr td[data-cell='${row+1}_${col}'] input`).is(':read-only')) {
                row++;
                // e.focusout();
            }
            // if(!$(`.toggle_table tbody tr td[data-cell='${row+1}_${col}'] input`)){
            //     row++;
            // }

            $('.toggle_table tbody tr')[row + 1].querySelectorAll('td')[col].firstElementChild.focus();
            $('#saveDataBtn').click()
        }
        
    });
</script>


<!-- custom ajax for each and every field -->

<script>
    var ajaxRequested = false;
    $(document).ready(function() {
        $('#autosave_form').submit(function(event) {
            $("#autoSaveStatus").html("<i>Saving...</i>");
            var $submitBTN = $(this).find('button[type="submit"]');
            var btnText = $submitBTN.html();
            $submitBTN.attr('disabled', 'disabled');
            var posturl = $(this).attr('action');
            var $this = $(this).closest('form');
            var formID = $(this).attr('id');
            var formClass = $(this).attr('class');
            var loadingHTML = '<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i>'
            // var loadingHTML = '<svg class="spinner" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" height="22px" width="22px" fill="#456"><path d="M.01 0h24v24h-24V0z" fill="none"/><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>';
            $submitBTN.text('');
            $submitBTN.append(loadingHTML);
            if (!formID)
                formID = formClass;
            window.ajaxRequested = true;
            $($this).find('.form-group').removeClass('has-error');
            $($this).find('.help-block').hide();
            thisform = $this;
            $.each($this.find('input'), function(key, value) {
                if (!$(this).val())
                    $(this).removeClass('edited');
            });
            $(this).ajaxSubmit({
                url: posturl,
                dataType: 'json',
                success: function(response) {
                    $submitBTN.html(btnText);
                    checkTosterResponse(response);
                    response.formID = formID;
                    $submitBTN.removeAttr('disabled');
                    $submitBTN.find('.fa-spin').remove();
                    window.ajaxRequested = false;
                    $($this).find('.ajax_alert').removeClass('alert-danger').removeClass('alert-success');
                    $($this).find('.ajax_alert').fadeOut('fast')
                    if (response.success) {
                        $($this).find('.ajax_alert').addClass('alert-success');
                        window.madeChangeInForm = false;
                    } else {
                        $($this).find('.ajax_alert').addClass('alert-danger');
                    }
                    if (response.message) {
                        if (response.notify) {
                            showNotifyMessages(response);
                            $($this).find('.ajax_alert').fadeIn('slow').children('.ajax_message').html(response.message);
                        } else {
                            $($this).find('.ajax_alert').fadeIn('slow').children('.ajax_message').html(response.message);
                        }
                    }
                    if (response.redirectURL)
                        window.location.href = response.redirectURL;
                    if (response.scrollToThisForm)
                        scrollToElement('#' + formID, 1000);
                    if (response.selfReload)
                        window.location.reload();
                    if (response.resetForm)
                        $($this).resetForm();
                    if (response.callBackFunction)
                        callBackMe(response.callBackFunction, response);
                    $(thisform).find('.form-group').removeClass('has-error');
                    // $submitBTN.html(btnText);
                    if(response.lastUpdatedTime){   
                        $("#autoSaveStatus").html(`Last Updated on ${response.lastUpdatedTime}`);
                    }else{
                        $("#autoSaveStatus").html(`Last Updated Not Available`);
                    }
                },
                error: function(response) {
                    $submitBTN.html(btnText);
                    $submitBTN.removeAttr('disabled');
                    $submitBTN.find('.fa-spin').remove();
                }
            });
            return false;
        });
    });

    function validateFilesExtension(fld) {
        if (!/(\.xlsx|\.xls|\.XLSX|\.XLS)$/i.test(fld.value)) {
            toastr.error('Invalid File type.Only Excel file supported.');
            $(fld).val('');
            $('#filename_select_exl').text('')
            // fld.focus();
            return false;
        } else {
            var filename = $('#exl_file_name').val();
            if (filename.substring(3, 11) == 'fakepath') {
                filename = filename.substring(12);
            } // Remove c:\fake at beginning from localhost chrome
            $('#filename_select_exl').html(filename);
        }
    }


    $("#submit_btn").click(function() {
        var submit_to_qa = $(this).attr('submit_to_qa');
        var history_id = "<?php echo $id; ?>";
        var error_count = "<?php echo $error_count; ?>";
        if (error_count > 0) {
            if (confirm("Warning: Error has not resolved for this statement. Do you want to submit?")) {
                $("#send_btn_loader").show();
                $(".scrollContainer").hide();
                var surl = siteurl + 'Fs_result/submitToQa?submit_to_qa=' + submit_to_qa + '&history_id=' + history_id;
                $.getJSON(surl, function(response) {
                    if (response.success) {
                        $("#send_btn_loader").hide();
                        $(".scrollContainer").show();
                        window.location.href = '<?php echo base_url('fs-dashboard'); ?>';
                    }
                });
                return false;
            }
        } else {
            if (confirm("Once case is submitted, you can not edit the case. Do you want to submit?")) {
                $("#send_btn_loader").show();
                $(".scrollContainer").hide();
                var surl = siteurl + 'Fs_result/submitToQa?submit_to_qa=' + submit_to_qa + '&history_id=' + history_id;
                $.getJSON(surl, function(response) {
                    if (response.success) {
                        $("#send_btn_loader").hide();
                        $(".scrollContainer").show();
                        window.location.href = '<?php echo base_url(); ?>';
                    }
                });
                return false;
            }
        }
    });

    $("#send_btn").click(function() {
        var click_to_send = $(this).attr('click_to_send');
        var history_id = "<?php echo $id; ?>";
        var error_count = "<?php echo $error_count; ?>";
        if (error_count > 0) {
            if (confirm("Warning: Error has not resolved for this statement. Do you want to send?")) {
                $("#send_btn_loader").show();
                $(".scrollContainer").hide();
                var surl = siteurl + 'Fs_result/sendToOther?click_to_send=' + click_to_send + '&history_id=' + history_id;
                var sendReq = $.getJSON(surl, function(response) {
                    if (response.success) {
                        $("#send_btn_loader").hide();
                        $(".scrollContainer").show();
                        location.reload();
                    }
                });
                setTimeout(function() {
                    sendReq.abort();
                    $("#send_btn_loader").hide();
                    $(".scrollContainer").show();
                    location.reload();
                }, 15000);

                return false;
            } else {
                $("#send_btn_loader").hide();
                $(".scrollContainer").show();
                return false;
            }
        } else {
            if (confirm("Once case is Sent to Downstream, you can not edit the case. Do you want to send?")) {
                $("#send_btn_loader").show();
                $(".scrollContainer").hide();
                var surl = siteurl + 'Fs_result/sendToOther?click_to_send=' + click_to_send + '&history_id=' + history_id;
                var sendReq = $.getJSON(surl, function(response) {
                    if (response.success) {
                        $("#send_btn_loader").hide();
                        $(".scrollContainer").show();
                        location.reload();
                    }
                });
                setTimeout(function() {
                    sendReq.abort();
                    $("#send_btn_loader").hide();
                    $(".scrollContainer").show();
                    location.reload();
                }, 15000);

                return false;
            } else {
                $("#send_btn_loader").hide();
                $(".scrollContainer").show();
                return false;
            }
        }
    });

    function reRender(){
        let k = $('#tbl_container .scrollable table').html();
        $('#tbl_container .scrollable table').html("");
        setTimeout(() => {
            $('#tbl_container .scrollable table').html(k);

        }, 50);
    }
    // setInterval(() => {
    //     $("#saveDataBtn").click();
    // }, 20000)
</script>


<script>

    $.fn.formToJson = function () {
    form = $(this);

    var formArray = form.serializeArray();
    var jsonOutput = {};

    $.each(formArray, function (i, element) {
        var elemNameSplit = element['name'].split('[');
        var elemObjName = 'jsonOutput';

        $.each(elemNameSplit, function (nameKey, value) {
            if (nameKey != (elemNameSplit.length - 1)) {
                if (value.slice(value.length - 1) == ']') {
                    if (value === ']') {
                        elemObjName = elemObjName + '[' + Object.keys(eval(elemObjName)).length + ']';
                    } else {
                        elemObjName = elemObjName + '[' + value;
                    }
                } else {
                    elemObjName = elemObjName + '.' + value;
                }

                if (typeof eval(elemObjName) == 'undefined')
                    eval(elemObjName + ' = {};');
            } else {
                if (value.slice(value.length - 1) == ']') {
                    if (value === ']') {
                        eval(elemObjName + '[' + Object.keys(eval(elemObjName)).length + '] = \'' + element['value'].replace("'", "\\'") + '\';');
                    } else {
                        eval(elemObjName + '[' + value + ' = \'' + element['value'].replace("'", "\\'") + '\';');
                    }
                } else {
                    eval(elemObjName + '.' + value + ' = \'' + element['value'].replace("'", "\\'") + '\';');
                }
            }
        });
    });

    return jsonOutput;
}

// console.log(JSON.stringify($("#autosave_form").formToJson()));
let formData = (($("#autosave_form").formToJson()));

delete formData["blucogComments"];

console.log(formData);
</script>