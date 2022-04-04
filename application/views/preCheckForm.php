<?php
$currentPage = 'precheckform';
include('header.php'); ?>
<?php include('navigation.php'); ?>
<div class="main <?php if ($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div id="send_btn_loader" style="display: none;">
        <center><img src="<?php echo $this->config->item('assets'); ?>images/loading_img.gif" style="margin-top: -40px;"></center>
    </div>
    <link rel="stylesheet" href="<?php echo $this->config->item('assets'); ?>css/excelsheet_css/spread.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
                .top_container_bar {
                    padding: 25px 0 10px 10px;
                    width: 100%;
                    background: #F8F7FB;
                    width: calc(100% - 400px);
                }
    </style> 
    <form id="autosave_form" class="autosave_form" action="<?php echo base_url("Fs_dashboard/savePrecheckForm/$id"); ?>" method="post">
            <div class="top_container_bar">
                    <div class="button_container" style="margin:0;align-items:flex-end">
                        <div class="left_box">
                            <p>Document Links</p>
                            <p>
                                <label>Case Type</label>
                                <select name="case_type">
                                    <option>Single Company Financial</option>
                                    <option>Multiple Company Financial</option>
                                </select>
                            </p>
                             <input id="action" type="submit" name="status" value="Accept">
                             <input id="action" type="submit" name="status" value="Reject">
                        </div>
                        <div class="right_box"></div>
                    </div>
     </div>
    </form>
    
        
    </div>