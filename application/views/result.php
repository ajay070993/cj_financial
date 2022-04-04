<?php $currentPage='result';
      include('header.php'); ?>
<?php include('navigation.php'); ?>
<style type="text/css">
.wrong_right_list {
  list-style: none;
  margin-bottom: 0;
  padding: 0;
}
.wrong_right_list li {
  margin-bottom: 8px;
  display: flex;
  align-items: flex-start;
}
.wrong_right_list li span:first-child {
  margin-right: 10px;
  font-weight: 400;
  width: 20px;
  margin-top: 4px;
}
.wrong_right_list li span.right {
  color: #168039;
}
.wrong_right_list li span.wrong {
  color: #df4230;
}
.bs-example{
	margin: 20px;
}
.accordion .fa{
	margin-right: 0.5rem;
}

</style>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div id="send_btn_loader"  style="display: none;">
        <center><img src="<?php echo $this->config->item('assets'); ?>images/loading_img.gif" style="margin-top: -40px;"></center>
    </div>
    <div class="scrollContainer">
        <div class="excel_content_box">
            <ul>
                <!-- <li><a href="dashboard.php"><svg style="fill:#323E49;width:32px;cursor:pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M492 236H68.442l70.164-69.824c7.829-7.792 7.859-20.455.067-28.284-7.792-7.83-20.456-7.859-28.285-.068l-104.504 104c-.007.006-.012.013-.018.019-7.809 7.792-7.834 20.496-.002 28.314.007.006.012.013.018.019l104.504 104c7.828 7.79 20.492 7.763 28.285-.068 7.792-7.829 7.762-20.492-.067-28.284L68.442 276H492c11.046 0 20-8.954 20-20s-8.954-20-20-20z"/></svg></a></li> -->
                <li>
                    <span>Case Id: </span>
                    <span><?php echo $unique_id;?></span>
                </li>
                <li style="display: flex;">
                    <span>Status: </span>
                    <span>Spreading : <?php echo $spreading_status;?></span>
                </li>
            
                <?php if($error_count > 0){ ?>
                    <li style="display: flex;">
                        <span>Error: </span>
                        <span class="error_status" data-toggle="modal" data-id="<?php echo $history_id;?>" id="showErrorMsg"><?php echo $error_count;?> Error</span>
                    </li>
                <?php } ?>

                <li>
                    <span></span>
                    <span>Workflow : <?php echo $workflow_status;?></span>
                </li>
                <?php if($this->session->userdata('user_role')!=2){ ?>
                    <li>
                        <span></span>

                        <?php if($category_count==0){?>
                        <span>Categorization : in progress <a target="_blank" href="<?php echo base_url('Get_categories?id='.$history_id.'');?>">{Refresh}</a> </span>
                        <?php } else { ?>
                        <span>Categorization : Done </span>
                        <?php } ?>
                    </li>
                <?php } ?>
                <li>
                    <span>Customer Name: </span>
                    <span><?php echo $business_name;?></span>
                </li>
            </ul>
        </div>

        <form class="ajax_form" action="<?php echo base_url('result/EditSpreadedFileData'); ?>" method="post">
		<div class="right_box" style="text-align: right;">
            <!-- <button style="background: #56C593;" class="disabled">Save</button> -->
            <?php if(count($json_responce)>0){ ?>
                <a href="#" id="downstream_response">Downstream Response</a>
            <?php } ?>
        </div>
        <?php
                /*
                 * echo"<pre>";
                 * print_r($validate_results);
                 * echo"</pre>";
                 */
                $validate_array = array();
                foreach ($validate_results as $key => $result) {
                    if ($key == 0 || $result->checksum == 0) {
                        $validate_array['checksum'] = $result->checksum;
                    }
                    if ($key == 0 || $result->count_cr == 0) {
                        $validate_array['count_cr'] = $result->count_cr;
                    }
                    if ($key == 0 || $result->total_cr == 0) {
                        $validate_array['total_cr'] = $result->total_cr;
                    }
                    if ($key == 0 || $result->count_dr == 0) {
                        $validate_array['count_dr'] = $result->count_dr;
                    }
                    if ($key == 0 || $result->total_dr == 0) {
                        $validate_array['total_dr'] = $result->total_dr;
                    }
                    if ($key == 0 || $result->closing_balance == 0) {
                        $validate_array['closing_balance'] = $result->closing_balance;
                    }
                    if ($key == 0 || $result->start_date == 0) {
                        $validate_array['start_date'] = $result->start_date;
                    }
                    if ($key == 0 || $result->end_date == 0) {
                        $validate_array['end_date'] = $result->end_date;
                    }
                    if ($key == 0 || $result->txn_date == 0) {
                        $validate_array['txn_date'] = $result->txn_date;
                    }
                    if ($key == 0 || $result->currency == 0) {
                        $validate_array['currency'] = $result->currency;
                    }
                    if ($key == 0 || $result->date_format == 0) {
                        $validate_array['date_format'] = $result->date_format;
                    }
                    if ($key == 0 || $result->is_txn_date == 0) {
                        $validate_array['is_txn_date'] = $result->is_txn_date;
                    }
                    if ($key == 0 || $result->is_txn_amt == 0) {
                        $validate_array['is_txn_amt'] = $result->is_txn_amt;
                    }
                    if ($key == 0 || $result->is_txn_description == 0) {
                        $validate_array['is_txn_description'] = $result->is_txn_description;
                    }
                    if ($key == 0 || $result->is_acc_holder_name == 0) {
                        $validate_array['is_acc_holder_name'] = $result->is_acc_holder_name;
                    }
                    if ($key == 0 || $result->is_acc_number == 0) {
                        $validate_array['is_acc_number'] = $result->is_acc_number;
                    }
                    if ($key == 0 || $result->is_acc_type == 0) {
                        $validate_array['is_acc_type'] = $result->is_acc_type;
                    }
                    if ($key == 0 || $result->is_bank_name == 0) {
                        $validate_array['is_bank_name'] = $result->is_bank_name;
                    }
                    /*if ($key == 0 || $result->acc_type == 0) {
                        $validate_array['acc_type'] = $result->acc_type;
                    }*/
                }
                
                $isPopUp = false;
                if(in_array(0, $validate_array)){
                    $isPopUp = true;
                }
                 /*echo"<pre>";
                 print_r($validate_array);
                 echo"</pre>";
                 die;*/
                 
            ?>
        <div class="button_container">
            <!--  <div class="error_box">
                <div class="icon_box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 0C114.508 0 0 114.497 0 256c0 141.493 114.497 256 256 256 141.492 0 256-114.497 256-256C512 114.507 397.503 0 256 0zm0 472c-119.384 0-216-96.607-216-216 0-119.385 96.607-216 216-216 119.384 0 216 96.607 216 216 0 119.385-96.607 216-216 216z"/><path d="M343.586 315.302L284.284 256l59.302-59.302c7.81-7.81 7.811-20.473.001-28.284-7.812-7.811-20.475-7.81-28.284 0L256 227.716l-59.303-59.302c-7.809-7.811-20.474-7.811-28.284 0-7.81 7.811-7.81 20.474.001 28.284L227.716 256l-59.302 59.302c-7.811 7.811-7.812 20.474-.001 28.284 7.813 7.812 20.476 7.809 28.284 0L256 284.284l59.303 59.302c7.808 7.81 20.473 7.811 28.284 0s7.81-20.474-.001-28.284z"/></svg>
                </div>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Id quod eveniet commodi quas .</p>
            </div>-->

            <!--Download/upload excel section -->
            
            <?php if($this->session->userdata('user_role')==2){ ?>
                <?php if($submit_by_qa=='0'){ ?>
                    <div class="left_box">

                        <?php if($this->common_model->checkUserPermission(16,false)) { ?>
                            <?php if($history_type=='single'){ ?>
                                <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bank_statement/'.$history_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                            <?php } else if($history_type=='multiple'){ ?>
                                <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bulk_upload/'.$bulk_upload_folder_name.'/'.$original_pdf_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                            <?php } ?>
                        <?php } ?>

                        <?php if($this->common_model->checkUserPermission(5,false)) { ?>
                        <button type="button" value="Download" onClick="window.location.href='<?php echo base_url('Bank_statement/createExcel/'.$history_id);?>'" style="background: #006FCF;display: inline-flex;align-items: center;    padding: 12px 10px 10px;">Download Spreaded File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(6,false)) { ?>
                        <button type="button" style="background: #FF2C9C;display: inline-flex;align-items: center;    padding: 12px 10px 10px;" class="upload" id="upload_file">Upload Corrected Spread File<svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;transform: rotate(180deg);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } ?>
                         <?php
                            if(count($validate_results)>0){ ?>
                    		<button type="button" style="padding: 12px 10px;height: 36px;" data-toggle="modal"
        					data-target="#dataValidation">Data Validation Checks</button>
                		<?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if($this->session->userdata('user_role')==3){?>
                <?php if($submit_by_qa=='0' || ($click_to_send=='0' || $json_responce->success_type=='error')){ ?>
                    <div class="left_box">

                        <?php if($this->common_model->checkUserPermission(16,false)) { ?>
                            <?php if($history_type=='single'){ ?>
                                <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bank_statement/'.$history_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                            <?php } else if($history_type=='multiple'){ ?>
                                <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bulk_upload/'.$bulk_upload_folder_name.'/'.$original_pdf_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                            <?php } ?>
                        <?php } ?>

                        <?php if($this->common_model->checkUserPermission(5,false)) { ?>
                        <button type="button" value="Download" onClick="window.location.href='<?php echo base_url('Bank_statement/createExcel/'.$history_id);?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Spreaded File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(6,false)) { ?>
                        <button type="button" style="background: #FF2C9C;display: inline-flex;align-items: center;padding: 14px 12px 10px;" class="upload" id="upload_file">Upload Corrected Spread File<svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;transform: rotate(180deg);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } ?>
                         <?php
                                if(count($validate_results)>0){?>
                        		<button type="button" style="padding: 12px 10px;height: 36px;" data-toggle="modal"
            					data-target="#dataValidation">Data Validation Checks</button>
                    		<?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if($this->session->userdata('user_role')!=2 && $this->session->userdata('user_role')!=3){?>
            	<?php if($click_to_send=='0' || $json_responce->success_type=='error'){ ?>
                <div class="left_box">

                    <?php if($this->common_model->checkUserPermission(16,false)) { ?>
                        <?php if($history_type=='single'){ ?>
                            <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bank_statement/'.$history_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } else if($history_type=='multiple'){ ?>
                            <button type="button" value="Download" onClick="window.location.href='<?php echo $this->config->item('assets').'uploads/bulk_upload/'.$bulk_upload_folder_name.'/'.$original_pdf_file_name;?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Input File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                        <?php } ?>
                    <?php } ?>
                    

                    <?php if($this->common_model->checkUserPermission(5,false)) { ?>
                    <button type="button" value="Download" onClick="window.location.href='<?php echo base_url('Bank_statement/createExcel/'.$history_id);?>'" style="background: #006FCF;display: inline-flex;align-items: center;padding: 14px 12px 10px;">Download Spreaded File <svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                    <?php } ?>
                    <?php if($this->common_model->checkUserPermission(6,false)) { ?>
                    <button type="button" style="background: #FF2C9C;display: inline-flex;align-items: center;padding: 14px 12px 10px;" class="upload" id="upload_file">Upload Corrected Spread File<svg style="width: 20px;fill: #fff;stroke: #fff;stroke-width: 0.5px;margin-left: 10px;margin-top: -4px;transform: rotate(180deg);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></button>
                    <?php } ?>
                     <?php
                        if(count($validate_results)>0){?>
                		<button type="button" style="padding: 12px 10px;height: 36px;" data-toggle="modal"
    					data-target="#dataValidation">Data Validation Checks</button>
            		<?php } ?>
                </div>
                <?php } ?>
            <?php } ?>
           
            <!--End-->

            <!--button section -->
            <!-- edit button section -->
                <?php /*if($this->session->userdata('user_role')==2){ ?>
                    <?php if($submit_by_qa=='0'){ ?>
                        <?php if($this->common_model->checkUserPermission(8,false)) { ?>
                        <button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                            <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                        </button>
                        <?php } ?>
                    <?php } ?>
                <?php }*/ ?>
    
                <?php /*if($this->session->userdata('user_role')==3){?>
                    <?php if($click_to_send=='0'){ ?>
                        <?php if($this->common_model->checkUserPermission(8,false)) { ?>
                            <button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                                <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                            </button>
                        <?php } ?>
                    <?php } ?>
                <?php }*/ ?>
    
                <?php /*if($this->session->userdata('user_role')!=2 && $this->session->userdata('user_role')!=3){?>
                    <button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                        <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                    </button>
                <?php }*/ ?>
                <!-- end-->
            <?php if($this->session->userdata('user_role')==2){ ?>
                <?php if($submit_by_qa=='0'){ ?>
                    <div class="right_box">
                    	<?php /* if($this->common_model->checkUserPermission(8,false)) { ?>
                        <button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                            <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                        </button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(1,false)) { ?>
                            <button style="background: #56C593;" id="save_btn" class="disabled" type="submit">Save</button>
                        <?php } */?>
                        <?php if($this->common_model->checkUserPermission(3,false)) { ?>
                        <button style="background: #B57DFF;" id="submit_btn" type="button" submit_to_qa="1">Submit to QA</button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(2,false)) { ?>
                        <button type="button" id="cancel_btn" onclick="callBackCommonImportExcel();" style="background: #FF5F5F;">Cancel</button>
                        <?php } ?>
                    </div>

                <?php } else { ?>
                	<?php if(count($json_responce)>0){ 
                	    if($json_responce->success_type=='success'){ 
                	        echo"completed";
                	    }else if($json_responce->success_type=='error'){
                	        echo"rejected-downstream";
                	    }
                	}else{ ?>
                    	Case submitted to QA
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php if($this->session->userdata('user_role')==3){?>
                <?php if($click_to_send=='0' || $json_responce->success_type=='error'){ ?>
                    <div class="right_box">
                        <!-- <button style="background: #56C593;" class="disabled">Save</button> -->
                        <?php /* if($this->common_model->checkUserPermission(8,false)) { ?>
                            <button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                                <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                            </button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(1,false)) { ?>
                            <button style="background: #56C593;" id="save_btn" class="disabled" type="submit">Save</button>
                        <?php } */ ?>
                        <?php if($this->common_model->checkUserPermission(4,false)) { ?>
                        <button type="button" style="background: #E4CA4B;" id="send_btn" click_to_send="1">Send</button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(2,false)) { ?>
                        <button type="button" id="cancel_btn" onclick="callBackCommonImportExcel();" style="background: #FF5F5F;">Cancel</button>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    Case Sent to Downstream
                <?php } ?>
            <?php } ?>

            <?php if($this->session->userdata('user_role')!=2 && $this->session->userdata('user_role')!=3){?>
                <?php if($click_to_send=='0' || $json_responce->success_type=='error'){ ?>
                    <div class="right_box">
                        <!-- <button style="background: #56C593;" class="disabled">Save</button> -->
                        <?php /*?><button onclick="removeDisabled();" id="edit_btn" class="edit" title="Edit Table">
                            <svg style="width: 16px;fill: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.873 477.873"><path d="M392.533 238.937c-9.426 0-17.067 7.641-17.067 17.067V426.67c0 9.426-7.641 17.067-17.067 17.067H51.2c-9.426 0-17.067-7.641-17.067-17.067V85.337c0-9.426 7.641-17.067 17.067-17.067H256c9.426 0 17.067-7.641 17.067-17.067S265.426 34.137 256 34.137H51.2C22.923 34.137 0 57.06 0 85.337V426.67c0 28.277 22.923 51.2 51.2 51.2h307.2c28.277 0 51.2-22.923 51.2-51.2V256.003c0-9.425-7.641-17.066-17.067-17.066z"></path><path d="M458.742 19.142C446.488 6.886 429.867.002 412.536.004c-17.341-.05-33.979 6.846-46.199 19.149L141.534 243.937a17.254 17.254 0 00-4.113 6.673l-34.133 102.4c-2.979 8.943 1.856 18.607 10.799 21.585 1.735.578 3.552.873 5.38.875 1.832-.003 3.653-.297 5.393-.87l102.4-34.133c2.515-.84 4.8-2.254 6.673-4.13l224.802-224.802c25.515-25.512 25.518-66.878.007-92.393zm-24.139 68.277L212.736 309.286l-66.287 22.135 22.067-66.202L390.468 43.353c12.202-12.178 31.967-12.158 44.145.044a31.2148 31.2148 0 019.12 21.955 31.043 31.043 0 01-9.13 22.067z"></path></svg>
                        </button>
                        <?php if($this->common_model->checkUserPermission(1,false)) { ?>
                            <button style="background: #56C593;" id="save_btn" class="disabled" type="submit">Save</button>
                        <?php } */ ?>
                        <?php if($submit_by_qa=='0'){ ?>
                            <?php if($this->common_model->checkUserPermission(3,false)) { ?>
                            <button style="background: #B57DFF;" id="submit_btn" type="button" submit_to_qa="1">Submit to QA</button>
                            <?php } ?>
                        <?php } else { ?>
                        	<?php if(count($json_responce)>0){ 
                        	    if($json_responce->success_type=='success'){ 
                        	        echo"completed";
                        	    }else if($json_responce->success_type=='error'){
                        	        echo"rejected-downstream";
                        	    }
                        	}else{ ?>
                            	Case submitted to QA
                            <?php } ?>
                    	<?php } ?>
                        <?php if($this->common_model->checkUserPermission(4,false)) { ?>
                        <button type="button" style="background: #E4CA4B;" id="send_btn" click_to_send="1">Send</button>
                        <?php } ?>
                        <?php if($this->common_model->checkUserPermission(2,false)) { ?>
                        <button type="button" id="cancel_btn" onclick="callBackCommonImportExcel();" style="background: #FF5F5F;">Cancel</button>
                        <?php } ?>
                    </div>
                 <?php } ?>
            <?php } ?>
            <!-- end-->

        </div>
        <div class="content_container">
            <ul class="excel_tabs" id="tabs_listing">
                <li data-tab="one" class="active">Summary Level Data</li>
                <li data-tab="two" >Transaction</li>
                
                <!-- <?php if($this->common_model->checkUserPermission(7,false)) { ?>
                <li data-tab="three">Categories</li>
                <?php } ?> -->
    
                
                <!-- <li data-tab="four">Contact</li>
                <li data-tab="five">Data</li>
                <li data-tab="six">Composition</li> -->
            </ul>
    
            <div class="excel_tab_box one">
                <table class="table excel_table one" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 350px">Unique ID</th>
                            <th style="width: 150px">Account#</th>
                            <th style="width: 320px">Account Holder Name</th>
                            <th style="width: 250px">Account Type</th>
                            <th style="width: 250px">Name of Bank</th>
                            <th style="width: 250px">Bank Address</th>
                            <th style="width: 200px">Bank City</th>
                            <th style="width: 120px">Bank State</th>
                            <th style="width: 100px">Bank Zip</th>
                            <th style="width: 120px">Current Balance</th>
                            <th style="width: 100px">Start Date</th>
                            <th style="width: 100px">End Date</th>
                            <th style="width: 120px">Open Balance</th>
                            <th style="width: 120px">Closing Balance</th>
                            <th style="width: 150px">Total Deposits</th>
                            <th style="width: 100px">Count Deposits</th>
                            <th style="width: 150px">Total Withdrawals</th>
                            <th style="width: 150px">Count Withdrawals</th>
                            <th style="width: 150px">Native vs Non Native</th>
                            <th style="width: 150px">Check Sum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($summary_level_data)) {
                            foreach ($summary_level_data as $key => $value) {
                                $a = 0;$b = 0;$c=0;
                                $b = number_format(($value->open_balance + $value->total_deposits) - $value->total_withdrawals, 2, '.', '');
                                $c = number_format(($b - $value->closing_balance), 2, '.', '');
    
                        ?>
                        <input type="hidden" name="summry_data_row_id[]" value="<?php echo $value->id;?>">
                        <input type="hidden" name="summry_data_file_no[]" value="<?php echo $value->file_no;?>">
                        <tr>
                            <td>
                                <input type="text" disabled="" name="summry_data_unique_id[]" class="form-control" value="<?php echo $unique_id;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_account_number[]" class="form-control remove_disabled" value="<?php echo $value->account_number;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_account_holder_name[]" class="form-control remove_disabled" value="<?php echo $value->account_holder_name;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_account_type[]" class="form-control remove_disabled" value="<?php echo $value->account_type;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_name_of_bank[]" class="form-control remove_disabled" value="<?php echo $value->name_of_bank;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_bank_address[]" class="form-control remove_disabled" value="<?php echo $value->bank_address;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_bank_city[]" class="form-control remove_disabled" value="<?php echo $value->bank_city;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_bank_state[]" class="form-control remove_disabled" value="<?php echo $value->bank_state;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_bank_zip[]" class="form-control remove_disabled" value="<?php echo $value->bank_zip;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_current_balance[]" class="form-control remove_disabled" value="<?php echo $value->current_balance;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_start_date[]" class="form-control remove_disabled" value="<?php echo $value->start_date;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_end_date[]" class="form-control remove_disabled" value="<?php echo $value->end_date;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_open_balance[]" class="form-control remove_disabled" value="<?php echo $value->open_balance;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_closing_balance[]" class="form-control remove_disabled" value="<?php echo $value->closing_balance;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_total_deposits[]" class="form-control remove_disabled" value="<?php echo $value->total_deposits;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_count_deposits[]" class="form-control remove_disabled" value="<?php echo $value->count_deposits;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_total_withdrawals[]" class="form-control remove_disabled" value="<?php echo $value->total_withdrawals;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_count_withdrawals[]" class="form-control remove_disabled" value="<?php echo $value->count_withdrawals;?>" style="text-align: right">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_native_vs_non_native[]" class="form-control remove_disabled" value="<?php echo $value->native_vs_non_native;?>">
                            </td>
                            <td>
                                <input type="text" disabled="" name="summry_data_check_sum[]" class="form-control" value="<?php echo $value->check_sum;?>" style="text-align: right">
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
    
            <div class="excel_tab_box two" id="txn_tab"  style="display: none;">
                <center><img src="<?php echo $this->config->item('assets'); ?>images/loading_img.gif" style="margin-top: -40px;"></center>
                
            </div>
        </div>
        <div id="categories_tab_tbl">
        
        </div>

        </form>
        
        <div class="scrollTop">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240.823 240.823"><path d="M57.633 129.007L165.93 237.268c4.752 4.74 12.451 4.74 17.215 0 4.752-4.74 4.752-12.439 0-17.179l-99.707-99.671 99.695-99.671c4.752-4.74 4.752-12.439 0-17.191-4.752-4.74-12.463-4.74-17.215 0L57.621 111.816c-4.679 4.691-4.679 12.511.012 17.191z"/></svg>
        </div>
    </div>
</div>

<!-- <div class="browse_modal" id="upload_file_modal" style="display: none">
    <div class="backdrop"></div>
    <div class="content">
        <form id="import_excel_file" class="ajax_form" action="<?php echo base_url('result/uploadCorrectExcel/'.$history_id); ?>" method="post" >
            
            <div class="box">
                <svg class="close_modal" viewBox="0 0 329.26933 329" xmlns="http://www.w3.org/2000/svg"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"/></svg>
                <button type="button" class="upload">Browse<input onchange="return validateFilesExtension(this)" type="file" name="exl_file_name"></button>
                <button type="submit" class="upload" id="exl_file_name_submit">Upload</button>
                <p class="msg" style="display: none">Your file is successfully uploaded.</p>
            </div>
        </form>
    </div>
</div> -->

<div class="browse_modal" id="upload_file_modal" style="display: none">
	<div class="backdrop"></div>
	<div class="content">
		<form id="import_excel_file" class="ajax_form" action="<?php echo base_url('result/uploadCorrectExcel/'.$history_id); ?>" method="post">

			<div class="box">
				<svg class="close_modal" viewBox="0 0 329.26933 329"
					xmlns="http://www.w3.org/2000/svg">
					<path
						d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0" /></svg>
				<div class="upload_container">
					<div class="upload">
						<span id="filename_select_exl"></span>
						<button for="upload">Browse</button>
						<input type="file" onchange="return validateFilesExtension(this)"
							class="form-control-file" name="exl_file_name" id="exl_file_name">
					</div>
					<button type="submit" class="upload_submit"
						id="exl_file_name_submit">Upload</button>
					<p class="msg" style="display: none">Your file is successfully
						uploaded.</p>
				</div>

			</div>
		</form>
	</div>
</div>

<div class="browse_modal" id="downstream_response_modal" style="display: none">
    <div class="backdrop"></div>
    <div class="content">
        <form id="import_excel_file" class="ajax_form" action="<?php echo base_url('result/uploadCorrectExcel/'.$history_id); ?>" method="post" >
            
            <div class="box">
                <svg class="close_modal" viewBox="0 0 329.26933 329" xmlns="http://www.w3.org/2000/svg"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"/></svg>
                <?php 
                if(count($json_responce)>0){
                    echo "<pre>";
                    echo json_encode(json_decode($json_responce->RESPONSE_JSON), JSON_PRETTY_PRINT);
                    echo "</pre>";
                }
                ?>
            </div>
        </form>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade errorModal" id="errorModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <svg class="close" data-dismiss="modal" aria-label="Close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47.971 47.971"><path d="M28.228 23.986L47.092 5.122a2.998 2.998 0 000-4.242 2.998 2.998 0 00-4.242 0L23.986 19.744 5.121.88a2.998 2.998 0 00-4.242 0 2.998 2.998 0 000 4.242l18.865 18.864L.879 42.85a2.998 2.998 0 104.242 4.241l18.865-18.864L42.85 47.091c.586.586 1.354.879 2.121.879s1.535-.293 2.121-.879a2.998 2.998 0 000-4.242L28.228 23.986z"/></svg>
        <!-- Modal body -->
        <div class="modal-body">
            <svg class="error" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 0C114.508 0 0 114.497 0 256c0 141.493 114.497 256 256 256 141.492 0 256-114.497 256-256C512 114.507 397.503 0 256 0zm0 472c-119.384 0-216-96.607-216-216 0-119.385 96.607-216 216-216 119.384 0 216 96.607 216 216 0 119.385-96.607 216-216 216z"/><path d="M343.586 315.302L284.284 256l59.302-59.302c7.81-7.81 7.811-20.473.001-28.284-7.812-7.811-20.475-7.81-28.284 0L256 227.716l-59.303-59.302c-7.809-7.811-20.474-7.811-28.284 0-7.81 7.811-7.81 20.474.001 28.284L227.716 256l-59.302 59.302c-7.811 7.811-7.812 20.474-.001 28.284 7.813 7.812 20.476 7.809 28.284 0L256 284.284l59.303 59.302c7.808 7.81 20.473 7.811 28.284 0s7.81-20.474-.001-28.284z"/></svg>
            <p id="error_msg"></p>
        </div>
      </div>
    </div>
</div>

<!-- The Modal -->

<div class="modal fade" id="dataValidation">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Data Validation Checks</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<ul class="wrong_right_list">
					<li>
          	<?php if($validate_array['checksum']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['checksum']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            
            <span>1. Check on balances at summary level - (Open balance
							+ Total $ Deposits - Total $ Withdrawals ) - Closing Balance = 0,
							also called as Checksum check </span>
					</li>
					<li>
            <?php if($validate_array['count_cr']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['count_cr']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>2. Total # Credits at Summary Level = Total # of
							Credits in transaction Level</span>
					</li>
					<li>
            <?php if($validate_array['total_cr']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['total_cr']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>3. Total $ Credits at Summary Level = Sum of all
							Credits at Transaction Level </span>
					</li>
					<li>
            <?php if($validate_array['count_dr']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['count_dr']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>4. Total # Debits at Summary Level = Total # of Debits
							in transaction Level </span>
					</li>
					<li>
            <?php if($validate_array['total_dr']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['total_dr']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>5. Total $ Debits at Summary Level = Sum of all Debits
							at Transaction Level </span>
					</li>
					<li>
            <?php if($validate_array['closing_balance']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['closing_balance']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>6. Closing balance at Summary Level = Available
							balance in the last row of transaction level spread </span>
					</li>
					<li>
            <?php if($validate_array['start_date']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['start_date']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>7. Monthly Statement Start should be present</span>
					</li>
					<li>
            <?php if($validate_array['end_date']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['end_date']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>8. Monthly Statement End Date should be present </span>
					</li>
					<li>
            <?php if($validate_array['txn_date']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['txn_date']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>9. All transactions for a particular Account should be
							present between Start and End Date</span>
					</li>
					<li>
            <?php if($validate_array['currency']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['currency']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>10. Transaction Currency Should be Same for all
							Transactions for a Particular Account </span>
					</li>
					<li>
            <?php if($validate_array['date_format']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['date_format']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>11. Date Format is in the correct format of MM/DD/YYYY
					</span>
					</li>
					<li>
            <?php if($validate_array['is_txn_date']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_txn_date']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>12. Transaction Date is always populated and cannot be
							left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_txn_amt']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_txn_amt']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>13. Transaction Amount is always populated and cannot
							be left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_txn_description']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_txn_description']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>14. Transaction Description is always populated and
							cannot be left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_acc_holder_name']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_acc_holder_name']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>15. Labels like Acct holder name not left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_acc_number']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_acc_number']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>16. Labels like Account # not left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_acc_type']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_acc_type']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>17. Labels like Acct type not left blank </span>
					</li>
					<li>
            <?php if($validate_array['is_bank_name']==1){?>
            	<span class="right"><i class="fa fa-check"
							aria-hidden="true"></i></span>
        	<?php }else if($validate_array['is_bank_name']==0){ ?>
        		<span class="wrong"><i class="fa fa-times" aria-hidden="true"></i></span>
        	<?php }?>
            <span>18. Labels like Name of bank not left blank </span>
					</li>
					
				</ul>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>

		</div>
	</div>
</div>

<?php include('footer.php'); ?>
<script>
$("#upload_file").click(function(){
    $("#upload_file_modal").fadeIn();
})

$("#downstream_response").click(function(){
    $("#downstream_response_modal").fadeIn();
})

$(".close_modal").click(function(){
    $(".browse_modal").fadeOut();
})

$(".scrollContainer").scroll(function() {
    if ($(this).scrollTop() > 50 ) {
        $('.scrollTop:hidden').stop(true, true).fadeIn();
    } else {
        $('.scrollTop').stop(true, true).fadeOut();
    }
});
$(function(){$(".scrollTop").click(function(){$(".scrollContainer").animate({scrollTop:$(".excel_content_box").offset().top},"1000");return false})})

// $(function(){
//     $(".navigation").addClass("navSmall");
//     $(".main").toggleClass("mainSmall");
// });
$('#datatable1,#datatable2,#datatable3,#datatable4,#datatable5,#datatable6').DataTable({
    "scrollX": true,
    "language": {
        paginate: {
          next: '<img src="./assets/images/arrow-right.svg">',
          previous: '<img src="./assets/images/arrow-left.svg">'  
        }
      }
});

$('.excel_tabs').on('click', 'li', function() {
    var value = $(this).attr("data-tab");
    $(".excel_tabs li").removeClass("active");
    $(this).addClass("active");
    $(".excel_tab_box").hide();
    $(".excel_tab_box."+value).show();
    $('#datatable1,#datatable2,#datatable3,#datatable4,#datatable5,#datatable6').DataTable().columns.adjust().draw();
})
</script>

<script type="text/javascript">
    var isPopUp = '<?php echo $isPopUp ?>';
    //alert(isPopUp);
    if(isPopUp){
    	$('#dataValidation').modal('show');
    }

    function callBackCommonImportExcel(){
        location.reload();
    }

    $("#edit_btn").click(function(event){
       event.preventDefault();
       $('.remove_disabled').prop("disabled", false);
       $("#save_btn").removeClass("disabled");
    });

function validateFilesExtension(fld) 
{
    if(!/(\.xlsx|\.xls|\.XLSX|\.XLS)$/i.test(fld.value)) 
    {            
        toastr.error('Invalid File type.Only Excel file supported.');
        $(fld).val(''); 
        fld.focus();
        return false;
    }else{
        var filename = $('#exl_file_name').val();
        if (filename.substring(3,11) == 'fakepath') {
            filename = filename.substring(12);
        } // Remove c:\fake at beginning from localhost chrome
        $('#filename_select_exl').html(filename);
    }
}

$("#submit_btn").click(function(){
	$("#send_btn_loader").show();
	$(".scrollContainer").hide();
    var submit_to_qa = $(this).attr('submit_to_qa');
    var history_id = "<?php echo $history_id;?>";
    var error_count = "<?php echo $error_count;?>";
    if(error_count > 0){
        if (confirm("Warning: Error has not resolved for this statement. Do you want to submit?")) {
            var surl = siteurl+'result/submitToQa?submit_to_qa='+submit_to_qa+'&history_id='+history_id; 
            $.getJSON(surl,function(response){
                if (response.success) {    
                	$("#send_btn_loader").hide();
                	$(".scrollContainer").show();         
                    window.location.href = '<?php echo base_url();?>';         
                }        
            }); 
            return false;
        }
    }
    else{
        if (confirm("Once case is submitted, you can not edit the case. Do you want to submit?")) {
            var surl = siteurl+'result/submitToQa?submit_to_qa='+submit_to_qa+'&history_id='+history_id; 
            $.getJSON(surl,function(response){
                if (response.success) {  
                	$("#send_btn_loader").hide();
                	$(".scrollContainer").show();        
                    window.location.href = '<?php echo base_url();?>';         
                }        
            }); 
            return false;
        }
    }
});

$("#send_btn").click(function(){
    var click_to_send = $(this).attr('click_to_send');
    var history_id = "<?php echo $history_id;?>";
    var error_count = "<?php echo $error_count;?>";
    if(error_count > 0){
        if (confirm("Warning: Error has not resolved for this statement. Do you want to send?")) {
        	$("#send_btn_loader").show();
            $(".scrollContainer").hide();
            var surl = siteurl+'result/sendToOther?click_to_send='+click_to_send+'&history_id='+history_id; 
            var sendReq = $.getJSON(surl,function(response){
                if (response.success) { 
                	$("#send_btn_loader").hide();
                    $(".scrollContainer").show();        
                    location.reload();             
                }        
            }); 
            setTimeout(function(){ 
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
    else{
        if (confirm("Once case is Sent to Downstream, you can not edit the case. Do you want to send?")) {
        	$("#send_btn_loader").show();
            $(".scrollContainer").hide();
            var surl = siteurl+'result/sendToOther?click_to_send='+click_to_send+'&history_id='+history_id; 
            var sendReq = $.getJSON(surl,function(response){
                if (response.success) {    
                	$("#send_btn_loader").hide();
                    $(".scrollContainer").show();     
                    location.reload();           
                }        
            }); 
            setTimeout(function(){ 
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


$('.double-scroll').doubleScroll({
	contentElement: undefined, // Widest element, if not specified first child element will be used
	scrollCss: {                
		'overflow-x': 'auto',
		'overflow-y': 'hidden'
	},
	contentCss: {
		'overflow-x': 'auto',
		'overflow-y': 'hidden'
	},
	onlyIfScroll: true, // top scrollbar is not shown if the bottom one is not present
	resetOnWindowResize: false // recompute the top ScrollBar requirements when the window is resized
});

</script>

<?php if($this->common_model->checkUserPermission(7,false)) { ?>
<script type="text/javascript">
    $(document).ready(function(){
        var history_id = "<?php echo $history_id;?>";
        var surl = siteurl+'result/getAjaxCategory?history_id='+history_id;
        $.getJSON(surl,function(response){
            if (response.success) 
            {
                for (i = 0; i < response.fileSerialNum; i++) {
                    var text = "<li data-tab='three_"+(response.fileSerialNum-i)+"'>Categories Mnth"+(response.fileSerialNum-i)+"</li>";
                    $("#tabs_listing li:nth-child(2)").after(text);
                } 
                var text = '';  var text1 = '';  
                var text1 = "<li data-tab='three_"+response.fileSerialNumConsolidated+"'>Categories Consolidated</li>";
                $("#tabs_listing li:nth-child(2)").after(text1); 
                $('#categories_tab_tbl').html(response.html);                 
            }
        });
    });
</script>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){        
        $('#edit_btn').hide(); 
        var history_id = "<?php echo $history_id;?>";
        var surl = siteurl+'result/getAjaxTranscation?history_id='+history_id;
        $.getJSON(surl,function(response){
            if (response.success) 
            {
                $('#edit_btn').show(); 
                $('#txn_tab').html(response.html);                 
            }
        });
    });
</script>

<script type="text/javascript">
$("#showErrorMsg").click(function(){
    var history_id = $(this).attr('data-id');
    var surl = siteurl+'result/showErrorMsgPopup?history_id='+history_id; 
    $.getJSON(surl,function(response){
        if (response.success) { 
            $('#errorModal').modal('show');
            $('#error_msg').html(response.html);         
        }        
    }); 
    return false;  
});
</script>