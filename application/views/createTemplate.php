<?php   $currentPage='template';
        include('header.php'); ?>
<?php include('navigation.php'); ?>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
<style>
#data_file .form-signin #text_viewer {
    width: 100%;
    height: 530px;
    min-width: 1200px;
}
/* width */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.modal-backdrop.show {
    opacity: 0.5;
}
</style>
    <div class="scrollContainer">
        <div class="buttonsContainer">
            <form class="form-signin ajax_form" action="<?php echo site_url('Bank_statement/uploadPdfFile'); ?>" method="post">
                <div class="uploadContainer" style="display:none;">
                    <div class="uploadBox">
                        <label>Upload file to create template</label>
                        <div class="upload">
                            <span id="filename"></span>
                            <button for="upload">Browse</button>
                            <input type="file" name="image_name" class="form-control-file" id="upload">
                        </div>
                    </div>
                    <button type="submit" class="customButton">Upload</button>
                </div>
            </form>
            <div class="updateContainer" id="action_button">
                <button type="button" class="customButton" data-toggle="modal" data-target="#myModal" style="background-color:#FF5F5F">Cancel</button>
                <button type="submit" class="customButton" id="createTemplateBtn">Save</button>
            </div>
        </div>
        <div class="row" id="data_file">
            <div class="col-md-6">
                <form class="form-signin template-form">
                    <p>Pdf viewer</p>
                    <object id="pdf_viewer" data="<?php echo site_url('assets/uploads/bank_statement/'.$upload_pdf_file); ?>" type="application/pdf">
                        <p>Alternative text - include a link <a href="test.pdf">to the PDF!</a></p>
                    </object>
                </form>  
                <!-- <form class="form-signin template-form" style="margin-top: 30px;">
                    <p>Text viewer</p>
                    <object id="text_viewer" data="" type="application/pdf">
                        <p></p>
                    </object>
                </form> -->
            </div>  
            <div class="col-md-6">
                <form class="form-signin ajax_form form-fields" id="ajax_form" action="<?php echo site_url('Bank_statement/addString'); ?>" method="post">
                    <div class="formBox">
                        <p class="heading mt0">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>General Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group create-bank">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name">
                            </div>
                            <div class="form-group">
                                <label for="account_number_string">Account number string</label>
                                <input type="text" class="form-control" id="account_number_string" name="account_number_string" placeholder="Account number string">
                            </div>
                            <div class="form-group">
                                <label for="end_line_no">Template Content offset</label>
                                <input type="text" class="form-control" id="end_line_no" name="end_line_no" placeholder="Content end line no">
                            </div>
                            <div class="form-group">
                                <label for="txn_start_from">Transaction Start From</label>
                                <input type="text" class="form-control" id="txn_start_from" name="txn_start_from" placeholder="Transaction Start From">
                            </div>
                            <div class="form-group">
                                <label for="unique_string">Unique String</label>
                                <input type="text" class="form-control" id="unique_string" name="unique_string" placeholder="Unique String">
                            </div>
                            <div class="form-group">
                                <label for="remove_string">Remove String</label>
                                <textarea class="form-control" id="remove_string" name="remove_string" placeholder="Remove String" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="ignore_string">Ignore String</label>
                                <textarea class="form-control" id="ignore_string" name="ignore_string" placeholder="Ignore String" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="pages">Pages</label>
                                <input type="text" class="form-control" id="pages" name="pages" placeholder="Pages">
                                <input type="hidden" value="1" class="form-control" id="template-action" name="template-action">
                                <input type="hidden" class="form-control" id="file-name" name="file-name" value="<?php echo$upload_pdf_file;?>">
                                <input type="hidden" class="form-control" id="text-file-name" name="text-file-name" value="<?php echo$convert_text_file;?>">
                            </div>
                            <div class="form-group">
                                <label for="bank_type">Bank Type</label>
                                <select class="form-control" id="bank_type" name="bank_type">
                                    <option value="1" selected="selected">American Bank</option>
                                    <option value="2">Mexican Bank</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bank_stmt_format">Bank statement Format</label>
                                <select class="form-control" id="bank_stmt_format" name="bank_stmt_format">
                                    <option value="1" selected="selected">Normal</option>
                                    <option value="2">Credit Debit</option>
                                    <option value="3">Daily Ending Banlance</option>
                                </select>
                            </div>
                        </div>

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Credit Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="credit_start_string">Credit Start String</label>
                                <input type="text" class="form-control" id="credit_start_string" name="credit_start_string" placeholder="Credit Start String">
                            </div>
                            <div class="form-group">
                                <label for="credit_table_format">Credit Format</label>
                                <textarea class="form-control " id="credit_table_format" name="credit_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>
                            <div class="form-group">
                                <label for="credit_end_string">Credit End String</label>
                                <input type="text" class="form-control" id="credit_end_string" name="credit_end_string" placeholder="Credit End String">
                            </div>
                        </div>

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Debit Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="debit_start_string">Debit Start String</label>
                                <input type="text" class="form-control" id="debit_start_string" name="debit_start_string" placeholder="Debit Start String">
                            </div>
                            <div class="form-group">
                                <label for="debit_table_format">Debit Format</label>
                                <textarea class="form-control " id="debit_table_format" name="debit_table_format" rows="3">date,description,amount</textarea>
                                
                            </div>
                            <div class="form-group">
                                <label for="debit_end_string">Debit End String</label>
                                <input type="text" class="form-control" id="debit_end_string" name="debit_end_string" placeholder="Debit End String">
                            </div>
                        </div>

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Check Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="checks_start_string">Check Start String</label>
                                <input type="text" class="form-control" id="checks_start_string" name="checks_start_string" placeholder="Check Start String">
                            </div>
                            <div class="form-group">
                                <label for="cheque_table_format">Check Format</label>
                                <textarea class="form-control " id="cheque_table_format" name="cheque_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>
                            <div class="form-group">
                                <label for="checks_end_string">Check End String</label>
                                <input type="text" class="form-control" id="checks_end_string" name="checks_end_string" placeholder="Check End String">
                            </div>
                            <div class="form-group">
                                <label for="fetch_check_from_desc">Check Description String</label>
                                <input type="text" class="form-control" id="fetch_check_from_desc" name="fetch_check_from_desc" placeholder="Check Description String">
                            </div>
                        </div>
                        
                        <!-- Other Transactions -->
                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Transaction Details</span>
                        </p>
                        <div class="section">
                            <!-- Section 1 -->
                            <div class="form-group">
                                <label for="txn_sec_1">Other Transaction Section 1</label>
                                <input type="text" class="form-control" id="txn_sec_1" name="txn_sec_1" placeholder="Other Transaction Section 1">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_1_start_string">Transaction Start String</label>
                                <input type="text" class="form-control" id="txn_1_start_string" name="txn_1_start_string" placeholder="Transaction Start String">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_1_table_format">Transaction Format</label>
                                <textarea class="form-control" id="txn_1_table_format" name="txn_1_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>

                            <div class="form-group">
                                <label for="txn_1_end_string">Transaction End String</label>
                                <input type="text" class="form-control" id="txn_1_end_string" name="txn_1_end_string" placeholder="Transaction End String">
                            </div>
                            <div class="form-group">
                                <label for="txn_1_type">Transaction Type</label>
                                <select class="form-control" id="txn_1_type" name="txn_1_type">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                            <!-- Section 1 -->
                            <!-- Section 2 -->
                            <div class="form-group">
                                <label for="txn_sec_2">Other Transaction Section 2</label>
                                <input type="text" class="form-control" id="txn_sec_2" name="txn_sec_2" placeholder="Other Transaction Section 2">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_2_start_string">Transaction Start String</label>
                                <input type="text" class="form-control" id="txn_2_start_string" name="txn_2_start_string" placeholder="Transaction Start String">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_2_table_format">Transaction Format</label>
                                <textarea class="form-control" id="txn_2_table_format" name="txn_2_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>

                            <div class="form-group">
                                <label for="txn_2_end_string">Transaction End String</label>
                                <input type="text" class="form-control" id="txn_2_end_string" name="txn_2_end_string" placeholder="Transaction End String">
                            </div>
                            <div class="form-group">
                                <label for="txn_2_type">Transaction Type</label>
                                <select class="form-control" id="txn_2_type" name="txn_2_type">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                            <!-- Section 2 -->
                            <!-- Section 3 -->
                            <div class="form-group">
                                <label for="txn_sec_1">Other Transaction Section 3</label>
                                <input type="text" class="form-control" id="txn_sec_3" name="txn_sec_3" placeholder="Other Transaction Section 3">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_1_start_string">Transaction Start String</label>
                                <input type="text" class="form-control" id="txn_3_start_string" name="txn_3_start_string" placeholder="Transaction Start String">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_3_table_format">Transaction Format</label>
                                <textarea class="form-control" id="txn_3_table_format" name="txn_3_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>

                            <div class="form-group">
                                <label for="txn_3_end_string">Transaction End String</label>
                                <input type="text" class="form-control" id="txn_3_end_string" name="txn_3_end_string" placeholder="Transaction End String">
                            </div>
                            <div class="form-group">
                                <label for="txn_3_type">Transaction Type</label>
                                <select class="form-control" id="txn_3_type" name="txn_3_type">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                            <!-- Section 3 -->
                            <!-- Section 4 -->
                            <div class="form-group">
                                <label for="txn_sec_4">Other Transaction Section 4</label>
                                <input type="text" class="form-control" id="txn_sec_4" name="txn_sec_4" placeholder="Other Transaction Section1">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_4_start_string">Transaction Start String</label>
                                <input type="text" class="form-control" id="txn_4_start_string" name="txn_4_start_string" placeholder="Transaction Start String">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_4_table_format">Transaction Format</label>
                                <textarea class="form-control" id="txn_4_table_format" name="txn_4_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>

                            <div class="form-group">
                                <label for="txn_4_end_string">Transaction End String</label>
                                <input type="text" class="form-control" id="txn_4_end_string" name="txn_4_end_string" placeholder="Transaction End String">
                            </div>
                            <div class="form-group">
                                <label for="txn_4_type">Transaction Type</label>
                                <select class="form-control" id="txn_4_type" name="txn_4_type">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                            <!-- Section 4 -->
                            <!-- Section 5 -->
                            <div class="form-group">
                                <label for="txn_sec_5">Other Transaction Section 5</label>
                                <input type="text" class="form-control" id="txn_sec_5" name="txn_sec_5" placeholder="Other Transaction Section 5">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_5_start_string">Transaction Start String</label>
                                <input type="text" class="form-control" id="txn_5_start_string" name="txn_5_start_string" placeholder="Transaction Start String">
                            </div>
                            
                            <div class="form-group">
                                <label for="txn_5_table_format">Transaction Format</label>
                                <textarea class="form-control" id="txn_5_table_format" name="txn_5_table_format" rows="2">date,description,amount</textarea>
                                
                            </div>

                            <div class="form-group">
                                <label for="txn_5_end_string">Transaction End String</label>
                                <input type="text" class="form-control" id="txn_5_end_string" name="txn_5_end_string" placeholder="Transaction End String">
                            </div>
                            <div class="form-group">
                                <label for="txn_5_type">Transaction Type</label>
                                <select class="form-control" id="txn_5_type" name="txn_5_type">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                            <!-- Section 5 -->
                        </div>
                        <!-- Transaction 1 get From Regex -->

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Service Fees Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="service_fee_title_1">Service fees</label>
                                <input type="text" class="form-control" id="service_fee_title_1" name="service_fee_title_1" placeholder="Service fees">
                            </div>
                            <div class="form-group">
                                <label for="service_fee_pattern_1">Service fees Pattern</label>
                                <input type="text" class="form-control" id="service_fee_pattern_1" name="service_fee_pattern_1" placeholder="Service Fee Pattern">
                            </div>
                            <div class="form-group">
                                <label for="service_fee_type_1">Service fees Type</label>
                                <select class="form-control" id="service_fee_type_1" name="service_fee_type_1">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                        </div>

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Other Bank Fees Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="service_fee_title_2">Other bank fee</label>
                                <input type="text" class="form-control" id="service_fee_title_2" name="service_fee_title_2" placeholder="Other bank fee">
                            </div>
                            <div class="form-group">
                                <label for="service_fee_pattern_2">Other bank fee Pattern</label>
                                <input type="text" class="form-control" id="service_fee_pattern_2" name="service_fee_pattern_2" placeholder="Other bank fee Pattern">
                            </div>
                            <div class="form-group">
                                <label for="service_fee_type_2">Other bank fee Type</label>
                                <select class="form-control" id="service_fee_type_2" name="service_fee_type_2">
                                    <option value="cr" selected="selected">Credit</option>
                                    <option value="dr">Debit</option>
                                </select>
                            </div>
                        </div>
                        <!-- End Transaction -->

                        <p class="heading">
                            <span  onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Bank Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="account_holder_name">Account Holder Name</label>
                                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" placeholder="Account Holder Name">
                            </div>
                            <div class="form-group">
                                <label for="account_type">Account Type</label>
                                <input type="text" class="form-control" id="account_type" name="account_type" placeholder="Account Type">
                            </div>
                        	<div class="form-group">
                                <label for="account_type">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" placeholder="Currency">
                            </div>
                            <div class="form-group">
                                <label for="name_of_bank">Name Of Bank</label>
                                <input type="text" class="form-control" id="name_of_bank" name="name_of_bank" placeholder="Name Of Bank">
                            </div>
                            <div class="form-group">
                                <label for="bank_address">Bank Address</label>
                                <input type="text" class="form-control" id="bank_address" name="bank_address" placeholder="Bank Address">
                            </div>
                            <div class="form-group">
                                <label for="bank_city">Bank City</label>
                                <input type="text" class="form-control" id="bank_city" name="bank_city" placeholder="Bank City">
                            </div>
                            <div class="form-group">
                                <label for="bank_state">Bank State</label>
                                <input type="text" class="form-control" id="bank_state" name="bank_state" placeholder="Bank State">
                            </div>
                            <div class="form-group">
                                <label for="bank_zip">Bank Zip</label>
                                <input type="text" class="form-control" id="bank_zip" name="bank_zip" placeholder="Bank Zip">
                            </div>
                            <!--<div class="form-group">
                                <label for="current_balance">Current Balance</label>
                                <input type="text" class="form-control" id="current_balance" name="current_balance" placeholder="Current Balance">
                            </div>-->
                        </div>

                        <p class="heading">
                            <span  onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Date Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="start_date">Start Date of Transaction</label>
                                <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date of Transaction">
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date of Transaction</label>
                                <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date of Transaction">
                            </div>
                        </div>

                        <p class="heading">
                            <span onclick="$(this).parent().toggleClass('minus')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28H28v11a2 2 0 01-4 0V28H13.5a2 2 0 010-4H24V14a2 2 0 014 0v10h10.5a2 2 0 010 4z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><path d="M26 0C11.664 0 0 11.663 0 26s11.664 26 26 26 26-11.663 26-26S40.336 0 26 0zm12.5 28h-25a2 2 0 010-4h25a2 2 0 010 4z"/></svg>
                            </span>
                            <span>Balance Details</span>
                        </p>
                        <div class="section">
                            <div class="form-group">
                                <label for="open_balance">Opening Balance</label>
                                <input type="text" class="form-control" id="open_balance" name="open_balance" placeholder="Opening Balance">
                            </div>
                            <div class="form-group">
                                <label for="close_balance">Closing Balance</label>
                                <input type="text" class="form-control" id="close_balance" name="close_balance" placeholder="Closing Balance">
                            </div>
                        </div>
                    </div>
                </form>  
            </div>
            <div class="col-sm-12">
                <form class="form-signin template-form" style="margin-top: 30px;overflow-x:auto">
                    <p>Text viewer</p>
                    <object id="text_viewer" data="<?php echo site_url('assets/uploads/bank_statement/'.$convert_text_file); ?>" type="application/pdf">
                        <p></p>
                    </object>
                </form> 
            </div>
        </div>
    </div>
</div>

<!-- Pop up Code -->
<!--<div class="modal fade" id="templateModalCenter" tabindex="-1" role="dialog" aria-labelledby="templateModalCenterTitle" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="create_new_template" data-dismiss="modal">Create new Template</button>
        <input type="hidden" class="form-control" id="hidden_pdf_file_name" value="">
    	<input type="hidden" class="form-control" id="hidden_text_file_name" value="">
      </div>
    </div>
  </div>
</div>-->

<div class="modal fade templateModal" id="templateModalCenter">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <p>Template found with name</p>
            <div class="updateContainer">
                <button type="button" class="btn btn-primary" id="create_new_template" data-dismiss="modal">Create New Template</button>
                <input type="hidden" class="form-control" id="hidden_pdf_file_name" value="">
                <input type="hidden" class="form-control" id="hidden_text_file_name" value="">
                <button class="btn btn-primary" id="go_for_spreading">Go For Spreading</a>
            </div>
      </div>
    </div>
  </div>
</div>

<!-- End pop up code -->
<!-- The Modal -->
<div class="modal fade alertModal" id="savedModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body">
            <p>Are you sure you want to leave?</p>
            <div class="updateContainer">
                <a type="button" class="customButton" href="<?php echo site_url('templates'); ?>">Yes</a>
                <a type="submit" class="customButton" data-dismiss="modal" aria-label="Close">No</a>
            </div>
        </div>
      </div>
    </div>
  </div>
<!-- End pop up code -->
<script type="text/javascript">
	$(function(){
		$(".navigation").addClass("navSmall");
		$(".main").toggleClass("mainSmall");
	});



    function getUploadFile(data) {
    	$('#hidden_pdf_file_name').val(data.file_name);
        $('#hidden_text_file_name').val(data.text_file_name);
        /*var surl = siteurl + 'assets/uploads/bank_statement/' + data.file_name;
        $("#pdf_viewer").attr("data", surl);
        var text_file = siteurl + 'assets/uploads/bank_statement/' + data.text_file_name;
        $("#text_viewer").attr("data", text_file);*/
        //$('#data_file').show();
        //$('#update-existing-template').hide();
        
        if(data.bank_name!=""){
        	//$(".modal-body").html("Template found with name <b>"+data.bank_name+"</b><br/><a href='<?php //echo site_url('spreading'); ?>'>Go for spreading</a>");
        	$(".modal-body p").html("Template found with name <b>"+data.bank_name+"</b>");
        	$("#go_for_spreading").attr("href", "<?php echo site_url('spreading'); ?>");
        	$('#templateModalCenter').modal('toggle');
        }else{
        	$(".modal-body p").html("No template matches for upload PDF!");
        	$("#go_for_spreading").hide();
        	$('#templateModalCenter').modal('toggle');
        }
    }
    //$('#templateModalCenter').modal('toggle');
</script>
<script type="text/javascript">
$(document).ready(function(){
    $("#createTemplateBtn").click(function(){        
        $("#ajax_form").submit(); // Submit the form
    });
    $("#create_new_template").click(function(){
        $("#data_file").show();
        var hidden_pdf_file_name = $('#hidden_pdf_file_name').val();
        var hidden_text_file_name = $('#hidden_text_file_name').val();
    	var surl = siteurl + 'assets/uploads/bank_statement/' + hidden_pdf_file_name;
        $("#pdf_viewer").attr("data", surl);
        var text_file = siteurl + 'assets/uploads/bank_statement/' + hidden_text_file_name;
        $("#text_viewer").attr("data", text_file);
        //$('#data_file').show();
        //$('#update-existing-template').hide();
        $('#file-name').val(hidden_pdf_file_name);
        $('#text-file-name').val(hidden_text_file_name);
    	$(".updateContainer").show();
    });
});
</script>
<?php if($create_template){ ?>
<script type="text/javascript">
$(document).ready(function(){
	$('.heading').toggleClass('minus');
	$("#data_file").hide();
	$('#action_button').hide();
	$('.uploadContainer').show();
	
});

var isType = false;
$("input").keypress(function(){
	/*$("form#ajax_form :input").each(function(){
	 var input = $(this).val();
	 //alert($(this).)
	 if(input!=""){ //myModal
		 $('#myModal').modal('toggle');
	 }
	 
	});*/
	isType = true;
});
//textarea
$("textarea").keypress(function(){
	isType = true;
});

$(".sidebar_link").click(function () {
	  //alert(isType); 
	  if(isType){
		 $('#savedModal').modal('toggle');
		 return false;
	  }
})
</script>
<?php }?>
<?php include('footer.php'); ?>