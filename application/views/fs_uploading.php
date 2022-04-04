<?php
// $this->session->userdata['type_of_upload'] = 1;
if ($this->session->userdata('type_of_upload') == 1) {
	$currentPage = 'spreading';
	include('header.php');
?>
<?php
	include('navigation.php');
} else { ?>
	<script src="<?php echo $this->config->item('assets'); ?>js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankCreditDebitTxn.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankEndingDailyBalTxn.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankAmtLastLineDescTxn.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankAmtBlankLastLineTxn.js"></script>
	<script type="text/javascript">
		var site_url = '<?php echo base_url(); ?>';
		var base_url = site_url;
		var siteurl = site_url;
	</script>
<?php } ?>

<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
	<?php if ($this->session->userdata('type_of_upload') == 1) { ?>
		<?php include('topbar.php'); ?>
	<?php } ?>
	<div class="scrollContainer">
		<div class="spread">
			<form id="convert_form" class="form-signin ajax_form" action="<?php echo base_url('Fs_uploading/cjFinancialStatement'); ?>" method="post" enctype="multipart/form-data">
				<!-- <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bank_id">Bank</label>
                        <select class="form-control" id="bank_id" name="bank_id" onchange="hidedownload();">
                            <option value="">Select Bank</option>
        					<?php foreach ($allBanks as $key => $value) { ?>
        					  <option value="<?php echo $value->id ?>"><?php echo $value->bank_name ?></option>
        					<?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="uploadBox">
                            <label>Upload file</label>
                            <div class="upload">
                                <span id="filename"></span>
                                <button for="upload">Browse</button>
                                <input type="file" name="image_name" class="form-control-file" id="upload">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit">Convert File</button>
                </div>
            </div> -->
				<!-- <div class="radio_box">
				<label class="custom_radio">Native
					<input type="radio" checked="checked" name="native_radio" value="native">
					<span class="checkmark"></span>
				</label>
				<label class="custom_radio">Non Native
					<input type="radio" name="native_radio" value="nonnative">
					<span class="checkmark"></span>
				</label>
			</div> -->
				<?php if ($this->session->userdata('type_of_upload') == 1) { ?>
					<div class="dropBox">
						<span class="file-msg text-center" id="spreadFile">Drag and Drop files here <br>or Click Here</span>
						<input class="file-input" type="file" name="image_name" id="spreadDrop">
					</div>
					<button type="submit" id="spread_file" class="upload_btn">Upload File</button>
				<?php } ?>
			</form>

			<div class="stepProgress">
				<div class="stepSection" id="progress_bar_1" style="display: none;">
					<div class="progressBox">
						<span class="loader" style="display: none"></span>
						<svg class="success" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<polyline class="path check" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
						</svg>

						<svg class="failure" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<line class="path line" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
							<line class="path line" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
						</svg>
					</div>
					<div class="content">
						<p class="detectText">Detecting Bank Statement</p>
						<p class="text" id="template_name" style="display: none"></p>
					</div>
				</div>
				<div class="stepSection" id="progress_bar_2" style="display: none;">
					<div class="progressBox">
						<span class="loader" style="display: none"></span>
						<svg class="success" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<polyline class="path check" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
						</svg>

						<svg class="failure" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<line class="path line" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
							<line class="path line" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
						</svg>
					</div>
					<?php if ($this->session->userdata('type_of_upload') == 1) { ?>
						<div class="content">
							<p class="detectText">Spreading Bank Statement</p>
							<div class="downloadBox" style="display: none">
								<div>
									<p><span>File Name:- </span><span class="upload_template_file_name"></span></p>
									<p><span>Checksum:- </span><span class=check_sum></span></p>
									<p><span>Total Deposits:- </span><span class=total_deposits></span></p>
									<p><span>Count Deposits:- </span><span class=count_deposits></span></p>
									<p><span>Total Withdrawals:- </span><span class=total_withdrawals></span></p>
									<p><span>Count Withdrawals:- </span><span class=count_withdrawals></span></p>
								</div>
								<a id="dwnld_excel" href="<?php echo base_url() ?>Bank_statement/createXLSBankStatement" title="Download File">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596">
										<path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z" />
										<path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z" />
									</svg>
									<span>Download</span>
								</a>
							</div>
						</div>
					<?php } ?>
					<div class="isComplete"></div>
				</div>
				<div class="stepSection" id="progress_bar_3" style="display: none;">
					<div class="progressBox">
						<span class="loader" style="display: none"></span>
						<svg class="success" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<polyline class="path check" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
						</svg>

						<svg class="failure" style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
							<line class="path line" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
							<line class="path line" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
						</svg>
					</div>
					<div class="content">
						<p class="detectText">Spreading</p>
						<div class="secondStep" style="display: none">
							<form id="convert_form" class="form-signin ajax_form" action="<?php echo base_url('Bank_statement/convertBankStatement'); ?>" method="post">
								<div class="selectBank">
									<div class="form-group">
										<label for="bank_id">Bank</label>
										<select class="form-control" id="bank_id" name="bank_id" onchange="hidedownload();">
											<option value="">Select Bank</option>
											<?php foreach ($allBanks as $key => $value) { ?>
												<option value="<?php echo $value->id ?>"><?php echo $value->bank_name ?></option>
											<?php } ?>
										</select>
										<input type="hidden" class="form-control convert_text_file" name="convert_text_file">
										<input type="hidden" class="form-control upload_pdf_file" name="upload_pdf_file">
										<input type="hidden" class="form-control original_pdf_file_name" name="original_pdf_file_name">
									</div>
									<button>Convert</button>
								</div>
							</form>
							<?php if ($this->session->userdata('user_role') == 1) { ?>
								<span>Or</span>
								<form id="convert_form" action="<?php echo site_url('Templates/createTemplates'); ?>" method="post">
									<button>Create new template</button>
									<input type="hidden" class="form-control convert_text_file" name="convert_text_file">
									<input type="hidden" class="form-control upload_pdf_file" name="upload_pdf_file">
									<input type="hidden" class="form-control original_pdf_file_name" name="original_pdf_file_name">
								</form>
							<?php } ?>
						</div>
					</div>
					<div class="isComplete"></div>
				</div>
				<button id="animationCall" style="display: none;">Animation</button>
			</div>
		</div>
	</div>

</div>
<script type="text/javascript">
	$('.radio_box input[type="radio"]').click(function() {
		var radioValue = $("input[name='native_radio']:checked").val();
		if (radioValue == "native") {
			$(".upload_btn").text("Upload File");
		} else {
			$(".upload_btn").text("Upload Spreaded File");
		}
	});


	var user_type = '<?php echo $this->session->userdata('type'); ?>';
	var eurl = "<?php echo base_url('Bank_statement/callFromView') ?>";
	var monthArray = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Sept", "Oct", "Nov", "Dec"];
	var q = 0;

	function findSearchType(param) {
		//console.log(arr);

		var param = param.substring(
			param.lastIndexOf("[pre]") + 5,
			param.lastIndexOf("[/pre]")
		);
		//open_bal_pre = true;
		return param;

	}

	function clbckCjClrForm(data) {
		$('form#convert_form')[0].reset();
		$(".spread.dropBox").removeClass("spread");
		$("#spreadFile").html("Drag and Drop files here <br>or Click Here");
	}


	function createXLSBankStatement(data) {
		// console.log(data);
		var multiple_account = false;
		if (data.multiple_account != undefined && data.multiple_account) {
			multiple_account = true;
		}

		if (data.newFolderName != undefined && data.newFolderName) {
			var newFolderName = data.newFolderName;
		} else {
			var newFolderName = "";
		}

		if (data.accType != undefined && data.accType) {
			var accType = data.accType;
		} else {
			var accType = "";
		}

		/*if(data.check_all_pdf_process!=undefined && data.check_all_pdf_process){
			var check_all_pdf_process = data.newFolderName;
		}else{
			var check_all_pdf_process = "";
		}*/

		if (data.countPdfExt == data.count) {
			var check_all_pdf_process = data.newFolderName;
		} else {
			var check_all_pdf_process = "";
		}

		if (data.zip) {
			$("#progress_bar_1").hide();
			$("#progress_bar_2").hide();
			$("#progress_bar_3").hide();
		} else if (data.bank_name != "") {
			$("#progress_bar_1").removeClass("isProgress");
			$("#progress_bar_1 .progressBox .loader").hide();
			$("#progress_bar_1").addClass("isRight");
			$("#progress_bar_1 .progressBox .success,#progress_bar_1 .content .text").fadeIn();
			$("#template_name").text(data.bank_name);
			$("#progress_bar_3").hide();
		} else {
			$("#progress_bar_1").removeClass("isProgress");
			$("#progress_bar_1 .progressBox .loader").hide();
			$("#progress_bar_1").addClass("isRight");
			$("#progress_bar_1 .progressBox .success,#progress_bar_1 .content .text").fadeIn();
			$("#template_name").text('Template not found');
			$("#progress_bar_2").hide();
			$("#bank_id").val('');
			$("#progress_bar_3").show();
			$("#progress_bar_3").addClass("isProgress");
			$("#progress_bar_3 .progressBox .loader").fadeIn();
			$("#progress_bar_3").removeClass("isProgress");
			$("#progress_bar_3 .progressBox .loader").hide();
			$("#progress_bar_3").addClass("isWrong");
			$("#progress_bar_3 .progressBox .failure, #progress_bar_3 .content .secondStep").fadeIn();
			$(".convert_text_file").val(data.textFileName);
			$(".upload_pdf_file").val(data.extractData.upload_pdf_file);
			$(".original_pdf_file_name").val(data.extractData.original_pdf_file_name);
			return false;
		}
		var begining_bal = "No";
		var chkBgnBal = false;
		var account_num = "No";
		var chkAcctNum = false;
		var closing_bal = "Yes";
		var chkclsBal = false;
		var checkNumMissing = false;
		var customerReference = false;
		var bankReference = false;
		var pncReferenceCr = false;
		//var arr = [];
		//var arr = ["Beginning balance on June 1, 2017", "$35,142.77", "# of deposits/credits: 20"]
		//var account_number = data.bank_data_val.account_number_string;
		//var open_balance = data.bank_data_val.open_balance;

		//findSearchType(open_balance,arr);


		/*var txn_start_from = data.bank_data_val.txn_start_from;
		if(txn_start_from==undefined && txn_start_from==""){
			txn_start_from = 0;
		}*/
		var credit_format = data.bank_data_val.credit_table_format.split(',');
		if (data.bank_data_val.bank_id == 156 || data.bank_data_val.bank_id == 30 || data.bank_data_val.bank_id == 103 || data.bank_data_val.bank_id == 111 || data.bank_data_val.bank_id == 120 || data.bank_data_val.bank_id == 139) {
			data.user_type = user_type;
			data.eurl = eurl;
			bankEndingDailyBalTxn(data);
		} else if (data.bank_data_val.bank_id == 178) {
			data.user_type = user_type;
			data.eurl = eurl;
			bankAmtLastLineDescTxn(data);
		} else if (data.bank_data_val.bank_id == 187) {
			data.user_type = user_type;
			data.eurl = eurl;
			bankAmtBlankLastLineTxn(data);
		} else if (credit_format.length >= 5) {
			data.user_type = user_type;
			data.eurl = eurl;
			bankCreditDebitTxn(data);
		} else {


			var account_number = '';
			if (data.extractData.account_number_string == '') {
				//account_number_str = findSearchType(data.bank_data_val.account_number_string);
			} else if (data.extractData.account_number_string != '') {
				account_num = "Yes";
				account_number = data.extractData.account_number_string;
			}

			var begining_balance = '';
			if (data.extractData.begining_balance == '') {
				//begining_balance_str = findSearchType(data.bank_data_val.begining_balance);
			} else if (data.extractData.begining_balance != '') {
				begining_bal = "Yes";
				begining_balance = data.extractData.begining_balance;
			}

			var closing_balance = '';
			if (data.extractData.closing_balance == '') {
				//closing_balance_str = findSearchType(data.bank_data_val.closing_balance);
			} else if (data.extractData.closing_balance != '') {
				closing_bal = "Yes";
				closing_balance = data.extractData.closing_balance;
			}

			var service_fee_1 = "";
			var service_fee_title_1 = "";
			var service_fee_type_1 = "";
			if (data.extractData.service_fee_pattern_1 != '') {
				service_fee_1 = data.extractData.service_fee_pattern_1;
				service_fee_title_1 = data.bank_data_val.service_fee_title_1;
				service_fee_type_1 = data.bank_data_val.service_fee_type_1;
			}

			var service_fee_2 = "";
			var service_fee_title_2 = "";
			var service_fee_type_2 = "";
			if (data.extractData.service_fee_pattern_2 != '') {
				service_fee_2 = data.extractData.service_fee_pattern_2;
				service_fee_title_2 = data.bank_data_val.service_fee_title_2;
				service_fee_type_2 = data.bank_data_val.service_fee_type_2;
			}

			var start_date = '';
			var end_date = '';
			start_date = data.extractData.start_date;
			end_date = data.extractData.end_date;
			var data_ignore_string = data.bank_data_val.ignore_string;
			if (data_ignore_string) {
				var ignoreArray = data_ignore_string.split("|");
			}

			var pages = '';
			if (data.extractData.pages == '') {
				//pages_str = findSearchType(data.bank_data_val.pages);
			} else if (data.extractData.pages != '') {
				pages = data.extractData.pages;
			}
			/*if(data.string_record.bank_id==1){
				dataSpreadingBOAmerica(data);
			}
			if(data.string_record.bank_id==2){
				dataSpreadingJPMorgan(data);
			}*/
			//var filepath = siteurl+'assets/uploads/bank_statement/boa.txt';
			var filepath = siteurl + 'assets/uploads/bank_statement/' + data.textFileName;
			/*for Credit*/
			var credits = [];
			var credits_start = "No";
			var credit_start_string = data.bank_data_val.credit_start_string;
			var credit_close_string = data.bank_data_val.credit_end_string;
			var credit_format = data.bank_data_val.credit_table_format.split(',');

			var crDate = '';
			var crDesc = '';
			var crAmount = '';

			$.each(credit_format, function(key, val) {
				if (val.trim() == 'date') {
					crDate = key;
				}
				if (val.trim() == 'description') {
					crDesc = key;
				}
				if (val.trim() == 'amount') {
					crAmount = key;
				}
			});

			/*End Credit*/

			/*for Debit*/
			var debits = [];
			var debit_start = "No";
			var debit_start_string = data.bank_data_val.debit_start_string;
			var debit_close_string = data.bank_data_val.debit_end_string;
			var debit_format = data.bank_data_val.debit_table_format.split(',');



			var drDate = '';
			var drDesc = '';
			var drAmount = '';

			$.each(debit_format, function(key, val) {
				if (val.trim() == 'date') {
					drDate = key;
				}
				if (val.trim() == 'description') {
					drDesc = key;
				}
				if (val.trim() == 'amount') {
					drAmount = key;
				}
			});

			/*End Debit*/

			/*for Check*/
			var checks = [];
			var checks_start = "No";
			var checks_start_string = data.bank_data_val.checks_start_string;
			var checks_close_string = data.bank_data_val.checks_end_string;
			var checks_format = data.bank_data_val.cheque_table_format.split(',');



			var ckDate = '';
			var ckNumber = '';
			var ckAmount = '';

			$.each(checks_format, function(key, val) {
				if (val.trim() == 'date') {
					ckDate = key;
				}
				if (val.trim() == 'check') {
					ckNumber = key;
				}
				if (val.trim() == 'amount') {
					ckAmount = key;
				}
			});

			/*End Check*/

			/*for Other Transaction*/
			/**Section1*/
			var txn_1_start = "No";
			var txn_1_start_string = data.bank_data_val.txn_1_start_string;
			var txn_1_end_string = data.bank_data_val.txn_1_end_string;
			var txn_1_table_format = data.bank_data_val.txn_1_table_format.split(',');
			var txn_1_type = data.bank_data_val.txn_1_type;



			var txn_1_Date = '';
			var txn_1_Desc = '';
			var txn_1_Amount = '';

			$.each(txn_1_table_format, function(key, val) {
				if (val.trim() == 'date') {
					txn_1_Date = key;
				}
				if (val.trim() == 'description') {
					txn_1_Desc = key;
				}
				if (val.trim() == 'amount') {
					txn_1_Amount = key;
				}
			});

			/**Section 2*/
			var txn_2_start = "No";
			var txn_2_start_string = data.bank_data_val.txn_2_start_string;
			var txn_2_end_string = data.bank_data_val.txn_2_end_string;
			var txn_2_table_format = data.bank_data_val.txn_2_table_format.split(',');
			var txn_2_type = data.bank_data_val.txn_2_type;



			var txn_2_Date = '';
			var txn_2_Desc = '';
			var txn_2_Amount = '';

			$.each(txn_2_table_format, function(key, val) {
				if (val.trim() == 'date') {
					txn_2_Date = key;
				}
				if (val.trim() == 'description') {
					txn_2_Desc = key;
				}
				if (val.trim() == 'amount') {
					txn_2_Amount = key;
				}
			});

			/**Section 3*/
			var txn_3_start = "No";
			var txn_3_start_string = data.bank_data_val.txn_3_start_string;
			var txn_3_end_string = data.bank_data_val.txn_3_end_string;
			var txn_3_table_format = data.bank_data_val.txn_3_table_format.split(',');
			var txn_3_type = data.bank_data_val.txn_3_type;



			var txn_3_Date = '';
			var txn_3_Desc = '';
			var txn_3_Amount = '';

			$.each(txn_3_table_format, function(key, val) {
				if (val.trim() == 'date') {
					txn_3_Date = key;
				}
				if (val.trim() == 'description') {
					txn_3_Desc = key;
				}
				if (val.trim() == 'amount') {
					txn_3_Amount = key;
				}
			});

			/**Section 4*/
			var txn_4_start = "No";
			var txn_4_start_string = data.bank_data_val.txn_4_start_string;
			var txn_4_end_string = data.bank_data_val.txn_4_end_string;
			var txn_4_table_format = data.bank_data_val.txn_4_table_format.split(',');
			var txn_4_type = data.bank_data_val.txn_4_type;



			var txn_4_Date = '';
			var txn_4_Desc = '';
			var txn_4_Amount = '';

			$.each(txn_4_table_format, function(key, val) {
				if (val.trim() == 'date') {
					txn_4_Date = key;
				}
				if (val.trim() == 'description') {
					txn_4_Desc = key;
				}
				if (val.trim() == 'amount') {
					txn_4_Amount = key;
				}
			});

			/**Section 5*/
			var txn_5_start = "No";
			var txn_5_start_string = data.bank_data_val.txn_5_start_string;
			var txn_5_end_string = data.bank_data_val.txn_5_end_string;
			var txn_5_table_format = data.bank_data_val.txn_5_table_format.split(',');
			var txn_5_type = data.bank_data_val.txn_5_type;



			var txn_5_Date = '';
			var txn_5_Desc = '';
			var txn_5_Amount = '';

			$.each(txn_5_table_format, function(key, val) {
				if (val.trim() == 'date') {
					txn_5_Date = key;
				}
				if (val.trim() == 'description') {
					txn_5_Desc = key;
				}
				if (val.trim() == 'amount') {
					txn_5_Amount = key;
				}
			});

			/*End for Other Transaction*/

			var service_fees = [];
			var account_holder_name = '';
			var intRegex = /^\d+$/;
			var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
			var pos_withdrawals = 0;
			var pos_deposits = 0;
			var pos_description = 0;


			$.get(filepath, function(data_string) {

				var array = data_string.split("\n");

				for (var i = 0; i < array.length; i++) {

					var bankData = array[i].split(/  +/g);
					console.log(bankData);
					for (k = 0; k < bankData.length; k++) {
						if (bankData[k].trim() == 'Daily Balance' || bankData[k].trim() == 'Daily ledger balances' || bankData[k].trim() == 'DAILY BALANCE SUMMARY' || bankData[k].trim() == 'Daily Ledger Balance' || bankData[k].trim() == 'Balance Activity' || bankData[k].trim() == 'DAILY BALANCE' || bankData[k].trim() == 'Checking Account Daily Balances') {
							credits_start = "No";
							checks_start = "No";
							debit_start = "No";
							txn_1_start = "No";
							txn_2_start = "No";
							txn_3_start = "No";
							txn_4_start = "No";
							txn_5_start = "No";
						}
					}

					/**For credit */

					if (credits_start == "No") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == credit_start_string.trim()) {
								var is_credits_start = false;
								credits_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (credits_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == credit_close_string.trim()) {
								credits_start = "No";
							}
						}
					}



					if (credits_start == "Yes") {
						//console.log("Credit Yes",bankData);
						/*if(data.bank_data_val.bank_id==99){
    				for(k=0;k<bankData.length;k++){
    					var result = formatStringDate(bankData[k]);
    	    			if(result){
    	    				bankData[k] = result;
    	    			}
    				}
				}*/

						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {
								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}


						//City National Bank
						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');
							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {

								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;
								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == credit_close_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							} else if (bankData.length == 4 && (/^[0-9]{1,20}$/.test(bankData[2]))) {
								bankData.splice(2, 1);
							}

						}

						//Signature Bank
						if (data.bank_data_val.bank_id == 125 || data.bank_data_val.bank_id == 140) {
							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);

							for (k = 0; k < bankData.length; k++) {
								if (monthArray.includes(bankData[k].trim())) {
									var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
									if (result) {
										bankData[k] = result;
										bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
										break;
									}
								}
							}
						}

						// Bank of the West , Eagle Bank , United Bank, Farmers & Merchants
						if (data.bank_data_val.bank_id == 127 || data.bank_data_val.bank_id == 142 || data.bank_data_val.bank_id == 150 || data.bank_data_val.bank_id == 160) {
							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);
						}


						var intRegex = /^\d+$/;
						var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
						var filterData = [];
						filterData[crDesc] = '';
						filterData[crDate] = '';

						bankData = bankData.filter(Boolean);
						if (bankData.length >= credit_format.length) {
							var getDate = false;
							bankData = bankData.filter(Boolean);

							if (bankData[2] == 'Customer reference') {
								customerReference = true;
							}
							if (bankData[3] == 'Bank reference') {
								bankReference = true;
							}

							if (customerReference == true && bankReference == true) {
								if (bankData.length == 5) {
									if (/^[0-9]{1,20}$/.test(bankData[3])) {
										bankData.splice(3, 1);
									}
								}
								if (bankData.length == 4) {
									if (/^[0-9]{1,20}$/.test(bankData[2])) {
										bankData.splice(2, 1);
									}
								}

							}

							if (data.bank_data_val.bank_id == 116) {
								//console.log(bankData);
								if (bankData[2] == 'Availability Bank Reference') {
									customerReference = true;
								}
								if (bankData[3] == 'Customer Reference') {
									bankReference = true;
								}

								if (customerReference == true && bankReference == true) {
									console.log(bankData);
									var chkPos = 4;
									var isHyphen = false;
									for (k = 0; k < bankData.length; k++) {
										if (isHyphen) {
											//bankData.splice(k, 0, '- '+bankData[k]);
											bankData[k] = '- ' + bankData[k];
											isHyphen = false;
										}
										if (bankData[k] == 0) {
											chkPos = k;
											isHyphen = true;
										}
									}
									console.log(bankData);
									if (/^[0-9]{1,20}$/.test(bankData[4]) && bankData[4].indexOf('.') == -1) {
										bankData.splice(chkPos, 1);
									}

									if (/^[0-9]{1,20}$/.test(bankData[3]) && bankData[4].indexOf('.') == -1) {
										bankData.splice(chkPos - 1, 1);
									}


								}


							}

							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}


							if (data.bank_data_val.bank_id == 100 || data.bank_data_val.bank_id == 61 || data.bank_data_val.bank_id == 99 || data.bank_data_val.bank_id == 154) {
								//console.log(bankData);
								for (k = 0; k < bankData.length; k++) {
									var result = formatStringDate(bankData[k]);
									if (result) {
										bankData[k] = result;
									} //For U.S. Bank
									if ((data.bank_data_val.bank_id == 61 || data.bank_data_val.bank_id == 99) && (/^[0-9]{6,20}$/.test(bankData[k].trim()))) {
										bankData[k] = "DEPOSITS";
									}

								}
								//console.log(bankData);
								//console.log(bankData);
								if (bankData[0] == 'Credits' && bankData[1].length < 11 && (bankData[1].indexOf('/') != -1 || bankData[1].indexOf('-') != -1)) {
									bankData.splice(0, 1);
								}

								//US Bank
								if (data.bank_data_val.bank_id == 61) {
									if (bankData[0] != undefined && (/^[0-9]{1,6}$/.test(bankData[0].trim()))) {
										bankData[0] = ""
									} else if (bankData[3] != undefined && (/^[0-9]{1,6}$/.test(bankData[3].trim()))) {
										bankData[3] = ""
									}
									if (bankData[4] != undefined && (/^[0-9]{1,6}$/.test(bankData[4].trim()))) {
										bankData[4] = ""
									}

									bankData = bankData.filter(Boolean);
								}

								if (bankData.length == credit_format.length * 2 || data.bank_data_val.bank_id == 154) {
									//var rightSide = bankData.splice(3,6);
									//var leftSide = bankData.splice(0,3);
									bankData = bankData.map(s => s.trim());
									if (jQuery.inArray("|", bankData) !== -1) {
										var rightSide = bankData.splice(bankData.indexOf("|") + 1, bankData.length);
										var leftSide = bankData.splice(0, bankData.indexOf("|"));

									} else {
										var rightSide = bankData.splice(3, 6);
										var leftSide = bankData.splice(0, 3);
									}
									//console.log(leftSide);
									//console.log(rightSide);
									if (leftSide != undefined && leftSide.length >= credit_format.length) {
										bankData = leftSide;
										for (k = 0; k < bankData.length; k++) {
											var filterAmount = bankData[k].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												filterData[crAmount] = Math.abs(bankData[k].trim().replace(/[$,]+/g, ''));
											} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && getDate == false) {
												filterData[crDate] = bankData[k];
												//getDate = true;
											} else {
												if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
													filterData[crDesc] += bankData[k] + ' ';
												} else {
													filterData[crDesc] += '';
												}
											}
										}

										if (filterData.length == credit_format.length && filterData[crDate] != "" && filterData[crDate].length < 11 && (filterData[crDate].indexOf('/') != -1 || filterData[crDate].indexOf('-') != -1)) {
											is_credits_start = true;
											credits.push({
												"date": filterData[crDate],
												"description": filterData[crDesc],
												"amount": filterData[crAmount],
												"type": 'cr'
											})
										} else if (credits.length > 0 && is_credits_start) {
											var addDesc = true;
											var extraDesc = "";
											for (k = 0; k < bankData.length; k++) {
												if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
													$.each(ignoreArray, function(index, value) {
														if (value.indexOf("[rgx]") != -1) {
															sliceValue = value.slice(6, -7);
															var regex = new RegExp(sliceValue);
															var str = bankData[k].trim();
															if (regex.test(str)) {
																addDesc = false;
															}
														}
													});
													if (addDesc) {
														extraDesc += bankData[k] + " ";
													}
												}
											}

											if (extraDesc != "") {
												credits[credits.length - 1].description += ' ' + extraDesc;
											}

										}
									}
									bankData = rightSide;
									var filterData = [];
									filterData[crDesc] = '';
									filterData[crDate] = '';
								} else {
									if (data.bank_data_val.bank_id == 100) {
										var bankData = array[i].split(/ +/g);
										bankData = bankData.filter(Boolean);
										if (/^[0-9]{1,20}$/.test(bankData[2]) && bankData[2].indexOf('.') == -1) {
											bankData.splice(2, 1);
										}
									}
								}
							}

							if (data.bank_data_val.bank_id == 21) {
								var bankData = array[i].split(/ +/g);
							}
							/*if(data.bank_data_val.bank_id==61){
						var bankData =array[i].split(/ +/g);
						bankData = bankData.filter(Boolean);
						var dateConcat = '';
						console.log(bankData);
						var nextVal = '';
						for(k=0;k<bankData.length;k++){
							if(jQuery.inArray(bankData[k], monthArray) !== -1){
								dateConcat += bankData[k];
								var nextVal = k+1
							}

							if(k==nextVal){
								bankData[0] = dateConcat+' '+bankData[k];
								bankData.splice(k, 1);
							}
						}
						console.log(bankData);
	    				for(k=0;k<bankData.length;k++){
	    					var result = formatStringDate(bankData[k]);
	    	    			if(result){
	    	    				bankData[k] = result;
	    	    			}
	    				}
	    				console.log(bankData);
					}*/
							//console.log(rightSide);
							//console.log(bankData);
							for (k = 0; k < bankData.length; k++) {
								var filterAmount = bankData[k].trim().replace(/[$,]+/g, '');
								if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									filterData[crAmount] = Math.abs(bankData[k].trim().replace(/[$,]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && getDate == false) {
									filterData[crDate] = bankData[k];
									getDate = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[crDesc] += bankData[k] + ' ';
									} else {
										filterData[crDesc] += '';
									}
								}

							}


							if (filterData.length == credit_format.length && filterData[crDate] != "" && filterData[crDate].length < 11 && (filterData[crDate].indexOf('/') != -1 || filterData[crDate].indexOf('-') != -1)) {
								is_credits_start = true;
								credits.push({
									"date": filterData[crDate],
									"description": filterData[crDesc],
									"amount": filterData[crAmount],
									"type": 'cr'
								})
							} else if (credits.length > 0 && is_credits_start) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}

								if (extraDesc != "") {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}

							}
						} else if (credits.length > 0 && is_credits_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {
								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}
							}

							if (extraDesc != "") {
								credits[credits.length - 1].description += ' ' + extraDesc;
							}

						}

					}
					/**END For credit */

					var bankData = array[i].split(/  +/g);
					/**START For debit */
					if (debit_start == "No") {
						for (k = 0; k < bankData.length; k++) {
							if (debit_start_string.trim() != "" && bankData[k].trim() == debit_start_string.trim()) {
								var is_debit_start = false;
								debit_start = "Yes";
								checks_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (debit_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == debit_close_string.trim()) {
								debit_start = "No";
							}
						}
					}

					if (debit_start == "Yes") {
						bankData = bankData.filter(Boolean);


						var intRegex = /^\d+$/;
						var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
						var filterData = [];
						filterData[drDesc] = '';
						filterData[drDate] = '';

						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k]);
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}
						//console.log(bankData);

						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {

								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}



						//City National Bank
						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');
							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {

								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;
								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == debit_close_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							}
						}

						//Signature Bank and BMO Harris
						if (data.bank_data_val.bank_id == 125 || data.bank_data_val.bank_id == 140) {
							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);

							for (k = 0; k < bankData.length; k++) {
								if (monthArray.includes(bankData[k].trim())) {
									var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
									if (result) {
										bankData[k] = result;
										bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
										break;
									}
								}
							}
						}

						// Eagle Bank , United Bank, Farmers & Merchants
						if (data.bank_data_val.bank_id == 142 || data.bank_data_val.bank_id == 150 || data.bank_data_val.bank_id == 160) {
							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);
						}

						if (bankData.length == debit_format.length - 1) {
							if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								if (matchArray) {
									var amount = matchArray[0];
									var desc = bankData[1].replace(amount, '');
									bankData[drDate] = bankData[drDate]
									bankData[drAmount] = amount;
									bankData[drDesc] = desc;
								}
							}
						}
						if (bankData.length >= debit_format.length) {
							var drFirstDate = false;
							if (bankData[2] == 'Customer reference') {
								customerReference = true;
							}
							if (bankData[3] == 'Bank reference') {
								bankReference = true;
							}

							if (customerReference == true && bankReference == true) {
								if (bankData.length == 5) {
									if (/^[0-9]{1,20}$/.test(bankData[3])) {
										bankData.splice(3, 1);
									}
								}
								if (bankData.length == 4) {
									if (/^[0-9]{1,20}$/.test(bankData[2])) {
										bankData.splice(2, 1);
									}
								}
							}

							if (data.bank_data_val.bank_id == 116) {
								var chkPos = 4;
								var ckFirstDate = false;
								//console.log(bankData);
								var isHyphen = false;
								for (k = 0; k < bankData.length; k++) {
									if (isHyphen) {
										//bankData.splice(k, 0, '- '+bankData[k]);
										bankData[k] = '- ' + bankData[k];
										isHyphen = false;
									}
									if (bankData[k] == 0) {
										isHyphen = true;
									}
								}

								if (bankData[2] == 'Availability Bank Reference') {
									customerReference = true;
								}
								if (bankData[3] == 'Customer Reference') {
									bankReference = true;
								}

								if (customerReference == true && bankReference == true) {
									if (/^[0-9]{1,20}$/.test(bankData[4]) && filterAmount.indexOf('.') == -1) {
										if (/^[0-9]{1,20}$/.test(bankData[3]) && bankData[3].indexOf('.') == -1) {
											bankData.splice(chkPos - 1, 1);
										}
										//console.log(bankData);
										if (bankData[3] > 0) {
											bankData[4] = '- ' + bankData[4];
											//console.log(bankData);
											filterData[3] = "";

											for (k = 0; k < bankData.length; k++) {

												var filterAmount = bankData[k].trim().replace(/[$,]+/g, '');
												if (k == 3) {
													filterData[2] = bankData[k];
												} else if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
													filterData[1] = Math.abs(bankData[k].trim().replace(/[$,]+/g, ''));
												} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && ckFirstDate == false) {
													filterData[0] = bankData[k];
													ckFirstDate = true;
												} else {
													if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
														filterData[3] += bankData[k] + ' ';
													}
												}

											}


											if (filterData[0].length < 11 && (filterData[0].indexOf('/') != -1 || filterData[0].indexOf('-') != -1) && filterData[1] != undefined) {
												//console.log(filterData);
												checks.push({
													"date": filterData[0],
													"cheque_no": filterData[2],
													"amount": filterData[1],
													"description": filterData[3],
													"type": 'cr'
												})
											}
											continue;
										} else {
											if (/^[0-9]{1,20}$/.test(bankData[chkPos - 1]) && bankData[chkPos - 1].indexOf('.') == -1) {
												bankData.splice(chkPos - 1, 1);
											}
										}


									}


									//console.log(bankData);
								}


							}

							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}

							//US Bank
							if (data.bank_data_val.bank_id == 61) {
								bankData = array[i].split(/ /g);
								bankData = bankData.filter(Boolean);
								for (k = 0; k < bankData.length; k++) {
									if (monthArray.includes(bankData[k].trim())) {
										var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
										if (result) {
											bankData[k] = result;
											bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
											// Remove reference number
											if (/^[0-9]{1,20}$/.test(bankData[bankData.length - 2])) {
												bankData.splice(bankData.length - 2, 1);
											}
											break;
										}
									}

								}
							}



							if (data.bank_data_val.bank_id == 100) {
								var bankData = array[i].split(/ +/g);
								bankData = bankData.filter(Boolean);
								if (/^[0-9]{1,20}$/.test(bankData[2]) && bankData[2].indexOf('.') == -1) {
									bankData.splice(2, 1);
								}
							}

							for (k = 0; k < bankData.length; k++) {
								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '').replace(/[^\u0000-\u007F]+/g, '');

								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									//console.log(Math.abs(bankData[k].replace('$', '').replace(',', '')));
									filterData[drAmount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, '').replace(/[^\u0000-\u007F]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && drFirstDate == false) {
									filterData[drDate] = bankData[k];
									drFirstDate = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[drDesc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == debit_format.length && filterData[drDate].length < 11 && (filterData[drDate].indexOf('/') != -1 || filterData[drDate].indexOf('-') != -1)) {
								is_debit_start = true;
								debits.push({
									"date": filterData[drDate],
									"description": filterData[drDesc],
									"amount": filterData[drAmount],
									"type": 'dr'
								})
							} else if (debits.length > 0 && is_debit_start) {

								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													//console.log(str);
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}
								if (extraDesc != "") {
									debits[debits.length - 1].description += ' ' + extraDesc;
								}
							}
						} else if (debits.length > 0 && is_debit_start) {

							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {
								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												//console.log(str);
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}
							}
							if (extraDesc != "") {
								debits[debits.length - 1].description += ' ' + extraDesc;
							}
						}

					}
					/**END For debit */

					/**START For check */
					if (checks_start == "No") {
						for (k = 0; k < bankData.length; k++) {
							//if(checks_start_string.trim()!="" && bankData[k].trim()==checks_start_string.trim()){
							let checks_start_string_split = checks_start_string.split("|").map(s => s.trim());
							checks_start_string_split = checks_start_string_split.filter(Boolean);
							if (checks_start_string_split.length > 0 && checks_start_string_split.includes(bankData[k].trim())) {
								checks_start = "Yes";
								debit_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (checks_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == checks_close_string.trim()) {
								checks_start = "No";
							}
						}
					}

					if (checks_start == "Yes") {

						var intRegex = /^\d+$/;
						var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
						var filterData = [];
						var rightSide = [];
						var leftSide = [];
						filterData[ckNumber] = '';


						if (/\s(\d{1,2})\s*\-\s*(\d{1,2})\s/g.test(array[i])) {
							bankData = array[i].replace(/(\s\d{1,2})\s*\-\s*(\d{1,2})\s/g, "$1\-$2").split(/  +/g)
						}

						bankData = bankData.filter(Boolean);

						//Signature bank and BMO Harris
						if (data.bank_data_val.bank_id == 125 || data.bank_data_val.bank_id == 140) {
							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);
							for (k = 0; k < bankData.length; k++) {
								if (monthArray.includes(bankData[k].trim())) {
									var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
									if (result) {
										bankData[k] = result;
										bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
									}
								}
							}
						}

						for (k = 0; k < bankData.length; k++) {
							var result = formatStringDate(bankData[k]);
							if (result) {
								bankData[k] = result;
							}
						}



						/**For Blank Check number*/
						//bankData = bankData.filter(e => String(e).trim());
						if (data.bank_data_val.bank_id == 1 || data.bank_data_val.bank_id == 147 || data.bank_data_val.bank_id == 150) {
							if (bankData.length == 5) {
								var filterAmount = bankData[1].trim().replace('$', '').replace(',', '').replace('-', '');
								if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1) && (intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									bankData.splice(ckNumber, 0, "Check_number_not_found");
									checkNumMissing = true;
								}
							}
							if (bankData.length == 5) {
								var filterAmount = bankData[4].trim().replace('$', '').replace(',', '').replace('-', '');
								if (bankData[3].length < 11 && (bankData[3].indexOf('/') != -1 || bankData[3].indexOf('-') != -1) && (intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									bankData.splice(ckNumber + checks_format.length, 0, "Check_number_not_found");
									checkNumMissing = true;
								}
							}
						}
						if (data.bank_data_val.bank_id == 10) {
							if (bankData.length == 8) {

								var bankData = array[i].split(/ +/g);
								bankData = bankData.filter(Boolean);
								if (bankData[1].length < 11 && (bankData[1].indexOf('/') != -1 || bankData[1].indexOf('-') != -1)) {
									bankData.splice(0, 0, "Check_number_not_found");
									checkNumMissing = true;
								}

								if (bankData.length == 8) {
									bankData.splice(0, 0, "Check_number_not_found");
									checkNumMissing = true;
								}
								if (jQuery.inArray("*", bankData) !== -1 || jQuery.inArray("^", bankData) !== -1) {
									var removeItem = "*";

									bankData = jQuery.grep(bankData, function(value) {
										return value != removeItem;
									});

									var removeItem = "^";

									bankData = jQuery.grep(bankData, function(value) {
										return value != removeItem;
									});
									//console.log(bankData);
								}

							}

							if (bankData.length == 5) {
								var bankData = array[i].split(/ +/g);
							}
						}

						//First Citizen Bank
						if (data.bank_data_val.bank_id == 137) {

							if (bankData.length == 8) {
								array[i] = array[i].replace(/\s(\d)\s(\d)\s-\s(\d)\s(\d)\s/g, " $1$2\-$3$4 ");

								bankData = array[i].split(/ +/g);
								bankData = bankData.filter(Boolean);
								if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
									bankData.splice(0, 0, "Check_number_not_found");
									checkNumMissing = true;
								}

							}
						}


						/**End Check Number*/
						if (bankData.length + 1 == checks_format.length) {
							var bankData = array[i].split(/ +/g);
						}

						//Citizen Bank
						if (bankData.length == 1 && data.bank_data_val.bank_id == 112) {
							var temp_date = [];
							var temp_amt = [];
							var temp_ckno = [];
							while (bankData.length == 1) {
								var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
								if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
								} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
								} else {
									temp_ckno.push(bankData[0]);
								}
								i++;
								bankData = array[i].split(/  +/g);
								bankData = bankData.filter(Boolean);
							}
							if (temp_date.length == temp_amt.length) {
								for (var k = 0; k < temp_amt.length; k++) {
									checks.push({
										"date": temp_date[k],
										"cheque_no": temp_ckno[k],
										"amount": temp_amt[k],
										"type": 'cr'
									})
								}
							}
							i--;
						}

						if (bankData.length >= checks_format.length) {

							if (checkNumMissing == false && data.bank_data_val.bank_id != 99 && data.bank_data_val.bank_id != 61 && data.bank_data_val.bank_id != 125 && data.bank_data_val.bank_id != 140 && data.bank_data_val.bank_id != 145) {
								var bankData = array[i].split(/ +/g);
								bankData = bankData.filter(function(str) {
									return /\S/.test(str);
								});
							}

							//console.log(bankData);
							/*if(jQuery.inArray("*", bankData) !== -1 || jQuery.inArray("^", bankData) !== -1 || jQuery.inArray("i", bankData) !== -1){
								var removeItem = "*";

								bankData = jQuery.grep(bankData, function(value) {
								  return value != removeItem;
								});

								var removeItem = "^";

								bankData = jQuery.grep(bankData, function(value) {
								  return value != removeItem;
								});

								var removeItem = "i";

								bankData = jQuery.grep(bankData, function(value) {
								  return value != removeItem;
								});
								
							}*/

							var removeItem_array = ["*", "^", "i", "#", "|"];

							for (var j = 0; j < removeItem_array.length; j++) {
								var removeItem = removeItem_array[j];

								if (jQuery.inArray(removeItem, bankData) !== -1) {
									bankData = bankData.map(s => s.trim());
									bankData = jQuery.grep(bankData, function(value) {
										return value != removeItem;
									});
								}
							}

							if (data.bank_data_val.bank_id == 154) {
								if (bankData.length == 2) {
									var filterAmount = bankData[1].trim().replace('$', '').replace(',', '').replace('-', '');
									if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1) && (intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										bankData.splice(ckNumber, 0, "Check_number_not_found");
										checkNumMissing = true;
									}
								}
								if (bankData.length == 5) {
									var filterAmount = bankData[1].trim().replace('$', '').replace(',', '').replace('-', '');
									if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1) && (intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										bankData.splice(ckNumber, 0, "Check_number_not_found");
										checkNumMissing = true;
									}
								}
								if (bankData.length == 5) {
									var filterAmount = bankData[4].trim().replace('$', '').replace(',', '').replace('-', '');
									if (bankData[3].length < 11 && (bankData[3].indexOf('/') != -1 || bankData[3].indexOf('-') != -1) && (intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										bankData.splice(ckNumber + checks_format.length, 0, "Check_number_not_found");
										checkNumMissing = true;
									}
								}
							}

							/*If amount not found*/
							if (bankData.length == 8) {

								if (bankData[ckDate] != undefined && bankData[ckDate].length < 11 && (bankData[ckDate].indexOf('/') != -1 || bankData[ckDate].indexOf('-') != -1)) {
									if (bankData[ckAmount] != undefined && bankData[ckAmount].length < 11 && (bankData[ckAmount].indexOf('/') != -1 || bankData[ckAmount].indexOf('-') != -1) && bankData[ckNumber].indexOf('.') != -1) {
										bankData.splice(1, 0, "Check_number_not_found");
									}
								}

								if (bankData[ckDate + 3] != undefined && bankData[ckDate + 3].length < 11 && (bankData[ckDate + 3].indexOf('/') != -1 || bankData[ckDate + 3].indexOf('-') != -1)) {
									if (bankData[ckAmount + 3] != undefined && bankData[ckAmount + 3].length < 11 && (bankData[ckAmount + 3].indexOf('/') != -1 || bankData[ckAmount + 3].indexOf('-') != -1) && bankData[ckNumber + 3].indexOf('.') != -1) {
										bankData.splice(4, 0, "Check_number_not_found");
									}
								}

								if (bankData[ckDate + 6] != undefined && bankData[ckDate + 6].length < 11 && (bankData[ckDate + 6].indexOf('/') != -1 || bankData[ckDate + 6].indexOf('-') != -1)) {
									if (bankData[ckAmount + 6] != undefined && bankData[ckAmount + 6].length < 11 && (bankData[ckAmount + 6].indexOf('/') != -1 || bankData[ckAmount + 6].indexOf('-') != -1) && bankData[ckNumber + 6].indexOf('.') != -1) {
										bankData.splice(7, 0, "Check_number_not_found");
									}
								}

								if (bankData[ckDate] != undefined && bankData[ckDate].length < 11 && (bankData[ckDate].indexOf('/') != -1 || bankData[ckDate].indexOf('-') != -1)) {
									if (bankData[ckAmount] != undefined && bankData[ckAmount].length < 11 && (bankData[ckAmount].indexOf('/') != -1 || bankData[ckAmount].indexOf('-') != -1) && bankData[ckNumber].indexOf('.') == -1) {
										bankData.splice(2, 0, "amout_not_found");
									}
								}

								if (bankData[ckDate + 3] != undefined && bankData[ckDate + 3].length < 11 && (bankData[ckDate + 3].indexOf('/') != -1 || bankData[ckDate + 3].indexOf('-') != -1)) {
									if (bankData[ckAmount + 3] != undefined && bankData[ckAmount + 3].length < 11 && (bankData[ckAmount + 3].indexOf('/') != -1 || bankData[ckAmount + 3].indexOf('-') != -1) && bankData[ckNumber + 3].indexOf('.') == -1) {
										bankData.splice(5, 0, "amout_not_found");
									}
								}

								if (bankData[ckDate + 6] != undefined && bankData[ckDate + 6].length < 11 && (bankData[ckDate + 6].indexOf('/') != -1 || bankData[ckDate + 6].indexOf('-') != -1)) {
									if (bankData[ckAmount + 6] != undefined && bankData[ckAmount + 6].length < 11 && (bankData[ckAmount + 6].indexOf('/') != -1 || bankData[ckAmount + 6].indexOf('-') != -1) && bankData[ckNumber + 6].indexOf('.') == -1) {
										bankData.splice(8, 0, "amout_not_found");
									}
								}

								//console.log(bankData);

							}

							if (bankData.length == 8 && data.bank_data_val.bank_id == 100) {
								console.log(bankData);
								if (bankData[5] != undefined && bankData[5].length < 11 && (bankData[5].indexOf('/') != -1 || bankData[5].indexOf('-') != -1) && bankData[7] != undefined && bankData[7].length < 11 && (bankData[7].indexOf('/') != -1 || bankData[7].indexOf('-') != -1)) {
									bankData.splice(6, 0, "Check_number_not_found");
								}
								console.log(bankData);
							}

							if (bankData.length == 7 && data.bank_data_val.bank_id == 160) {

								if (bankData[ckDate] != undefined && bankData[ckDate].length < 11 && (bankData[ckDate].indexOf('/') != -1 || bankData[ckDate].indexOf('-') != -1)) {
									if (bankData[ckAmount] != undefined && bankData[ckAmount].length < 11 && (bankData[ckAmount].indexOf('/') != -1 || bankData[ckAmount].indexOf('-') != -1) && bankData[ckNumber].indexOf('.') != -1) {
										bankData.splice(ckNumber, 0, "Check_number_not_found");
									}
								}

								if (bankData[ckDate + 3] != undefined && bankData[ckDate + 3].length < 11 && (bankData[ckDate + 3].indexOf('/') != -1 || bankData[ckDate + 3].indexOf('-') != -1)) {
									if (bankData[ckAmount + 3] != undefined && bankData[ckAmount + 3].length < 11 && (bankData[ckAmount + 3].indexOf('/') != -1 || bankData[ckAmount + 3].indexOf('-') != -1) && bankData[ckNumber + 3].indexOf('.') != -1) {
										bankData.splice(ckNumber + 3, 0, "Check_number_not_found");
									}
								}

								if (bankData[ckDate + 6] != undefined && bankData[ckDate + 6].length < 11 && (bankData[ckDate + 6].indexOf('/') != -1 || bankData[ckDate + 6].indexOf('-') != -1)) {
									if (bankData[ckAmount + 6] != undefined && bankData[ckAmount + 6].length < 11 && (bankData[ckAmount + 6].indexOf('/') != -1 || bankData[ckAmount + 6].indexOf('-') != -1) && bankData[ckNumber + 6].indexOf('.') != -1) {
										bankData.splice(ckNumber + 6, 0, "Check_number_not_found");
									}
								}
							}

							if (bankData.length >= checks_format.length && bankData.length < checks_format.length * 2) {
								var leftSide = bankData;

							}


							if (bankData.length >= checks_format.length * 2 && bankData.length < checks_format.length * 3) {
								var half_length = Math.ceil(bankData.length / 2);
								var middleSide = bankData.splice(half_length, bankData.length);
								var leftSide = bankData.splice(0, half_length);

							}


							if (bankData.length >= checks_format.length * 3 && bankData.length < checks_format.length * 4) {
								bankData = bankData.filter(Boolean);
								if (bankData.length == 8) {

									for (k = 0; k < bankData.length; k++) {
										var date_amt = bankData[k].split(' ');
										if (date_amt.length == 2) {
											amount = checkAmount(date_amt[0]);
											date = checkDate(date_amt[1]);
											if (amount != false && date != false) {
												bankData[k] = amount.toString();
												bankData.splice(k + 1, 0, date);
											} else if (date != false && amount != false) {
												bankData[k] = date;
												bankData.splice(k + 1, 0, amount.toString());
											}
										}
									}
								}
								var half_length = Math.ceil(bankData.length / 3);

								var rightSide = bankData.splice(half_length * 2, bankData.length);
								var middleSide = bankData.splice(half_length, half_length * 2);
								var leftSide = bankData.splice(0, half_length);
							}

							if (bankData.length >= checks_format.length * 4) {
								bankData = bankData.filter(Boolean);

								if (bankData.length == 12) {
									var half_length = Math.ceil(bankData.length / 4);

									var rightSide = bankData.splice(half_length * 3, bankData.length);
									var middleSide2 = bankData.splice(half_length * 2, half_length * 3);
									var middleSide = bankData.splice(half_length, half_length * 2);
									var leftSide = bankData.splice(0, half_length);
								}

							}

							if (leftSide != undefined && leftSide.length >= checks_format.length) {
								var filterData = [];
								filterData[ckNumber] = '';
								filterData[ckDate] = '';
								leftSide = leftSide.filter(Boolean);

								for (k = 0; k < leftSide.length; k++) {

									var filterAmount = leftSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, "");
									if (k == ckNumber) {
										filterData[ckNumber] = leftSide[k];
									} else if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										filterData[ckAmount] = Math.abs(leftSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, ""));
									} else if (leftSide[k].length < 11 && (leftSide[k].indexOf('/') != -1 || leftSide[k].indexOf('-') != -1)) {
										filterData[ckDate] = leftSide[k];
									}

								}
								if (filterData[ckDate].length < 11 && (filterData[ckDate].indexOf('/') != -1 || filterData[ckDate].indexOf('-') != -1) && filterData[ckAmount] != undefined) {
									checks.push({
										"date": filterData[ckDate],
										"cheque_no": filterData[ckNumber],
										"amount": filterData[ckAmount],
										"type": 'cr'
									})
								}
								leftSide = [];
							}

							if (middleSide != undefined && middleSide.length >= checks_format.length) {
								var filterData = [];
								filterData[ckNumber] = '';
								filterData[ckDate] = '';
								middleSide = middleSide.filter(Boolean);
								for (k = 0; k < middleSide.length; k++) {

									var filterAmount = middleSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, "");
									if (k == ckNumber) {
										filterData[ckNumber] = middleSide[k];
									} else if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										filterData[ckAmount] = Math.abs(middleSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, ""));
									} else if (middleSide[k].length < 11 && (middleSide[k].indexOf('/') != -1 || middleSide[k].indexOf('-') != -1)) {
										filterData[ckDate] = middleSide[k];
									}

								}

								if (filterData[ckDate].length < 11 && (filterData[ckDate].indexOf('/') != -1 || filterData[ckDate].indexOf('-') != -1) && filterData[ckAmount] != undefined) {
									checks.push({
										"date": filterData[ckDate],
										"cheque_no": filterData[ckNumber],
										"amount": filterData[ckAmount],
										"type": 'cr'
									})
								}
								middleSide = [];

							}

							if (middleSide2 != undefined && middleSide2.length >= checks_format.length) {
								var filterData = [];
								filterData[ckNumber] = '';
								filterData[ckDate] = '';
								middleSide2 = middleSide2.filter(Boolean);
								for (k = 0; k < middleSide2.length; k++) {

									var filterAmount = middleSide2[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, "");
									if (k == ckNumber) {
										filterData[ckNumber] = middleSide2[k];
									} else if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										filterData[ckAmount] = Math.abs(middleSide2[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, ""));
									} else if (middleSide2[k].length < 11 && (middleSide2[k].indexOf('/') != -1 || middleSide2[k].indexOf('-') != -1)) {
										filterData[ckDate] = middleSide2[k];
									}

								}

								if (filterData[ckDate].length < 11 && (filterData[ckDate].indexOf('/') != -1 || filterData[ckDate].indexOf('-') != -1) && filterData[ckAmount] != undefined) {
									checks.push({
										"date": filterData[ckDate],
										"cheque_no": filterData[ckNumber],
										"amount": filterData[ckAmount],
										"type": 'cr'
									})
								}
								middleSide2 = [];

							}

							if (rightSide != undefined && rightSide.length >= checks_format.length) {
								var filterData = [];
								filterData[ckDate] = '';
								filterData[ckNumber] = '';
								rightSide = rightSide.filter(Boolean);
								for (k = 0; k < rightSide.length; k++) {

									var filterAmount = rightSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, "");
									if (k == ckNumber) {
										filterData[ckNumber] = rightSide[k];
									} else if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
										filterData[ckAmount] = Math.abs(rightSide[k].trim().replace(/[$,]+/g, '').replace(/[^\u0000-\u007F]+/, ""));
									} else if (rightSide[k].length < 11 && (rightSide[k].indexOf('/') != -1 || rightSide[k].indexOf('-') != -1)) {
										filterData[ckDate] = rightSide[k];
									}

								}

								if (filterData[ckDate].length < 11 && (filterData[ckDate].indexOf('/') != -1 || filterData[ckDate].indexOf('-') != -1) && filterData[ckAmount] != undefined) {
									checks.push({
										"date": filterData[ckDate],
										"cheque_no": filterData[ckNumber],
										"amount": filterData[ckAmount],
										"type": 'cr'
									})
								}
								rightSide = [];
							}

						}

					}
					/**END For Checks */

					/**START For Other Txn 1 */

					var bankData = array[i].split(/  +/g);

					/*if(data.bank_data_val.bank_id==99){
				for(k=0;k<bankData.length;k++){
					var result = formatStringDate(bankData[k]);
	    			if(result){
	    				bankData[k] = result;
	    			}
				}

			}*/

					if (txn_1_start == "No" && txn_1_start_string.trim() != "") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_1_start_string.trim()) {
								var is_txn_1_start = false;
								txn_1_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								credits_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (txn_1_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_1_end_string.trim()) {
								txn_1_start = "No";
							}
						}
					}

					if (txn_1_start == "Yes") {

						var intRegex = /^\d+$/;
						var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

						if (/\s(\d{1,2})\s*\-\s*(\d{1,2})\s/g.test(array[i])) {
							bankData = array[i].replace(/(\s\d{1,2})\s*\-\s*(\d{1,2})\s/g, "$1\-$2").split(/  +/g)
						}

						//Comerica Bank
						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k].replace(/[0-9]+/g, " $&"));
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}

						//Bank of West , Huntington National bank
						if (data.bank_data_val.bank_id == 127 || data.bank_data_val.bank_id == 137 || data.bank_data_val.bank_id == 145 || data.bank_data_val.bank_id == 147) {
							bankData = array[i].split(/  +/g);
							bankData = bankData.filter(Boolean);

							let filter_date = "";
							let filter_amount = 0;
							let desc = "";
							for (var k = 0; k < bankData.length; k++) {
								var filterAmount = bankData[k].trim().replace(/[$,]+/g, '');
								if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									filter_amount = (Math.abs(bankData[k].trim().replace(/[$,]+/g, '')));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1)) {
									filter_date = bankData[k].replace(/ +/g, "");
								} else {
									desc = desc + " " + bankData[k]
								}

								if (filter_date != "" && filter_amount != 0) {
									credits.push({
										"date": filter_date,
										"description": (data.bank_data_val.bank_id == 145) ? 'Deposits : ' + desc : "Deposits",
										"amount": filter_amount,
										"type": 'cr'
									})
									filter_date = "";
									filter_amount = 0;
									desc = "";
								}
							}
							continue;
						}

						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {

								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}



						//City National Bank				
						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');

							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {


								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;



								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == txn_1_end_string.trim()) || (temp_bankData[0].trim() == checks_start_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							}

						}


						//console.log(bankData);
						var filterData = [];
						filterData[txn_1_Desc] = '';
						filterData[txn_1_Date] = '';

						if (bankData.length >= txn_1_table_format.length) {
							var is_txn_1_date = false;

							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}

							//US Bank
							if (data.bank_data_val.bank_id == 61) {
								bankData = array[i].split(/ /g);
								bankData = bankData.filter(Boolean);
								for (k = 0; k < bankData.length; k++) {
									if (monthArray.includes(bankData[k].trim())) {
										var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
										if (result) {
											bankData[k] = result;
											bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
											// Remove reference number
											if (/^[0-9]{1,20}$/.test(bankData[bankData.length - 2])) {
												bankData.splice(bankData.length - 2, 1);
											}
											break;
										}
									}

								}
							}

							for (k = 0; k < bankData.length; k++) {


								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '');
								var intRegex = /^\d+$/;
								var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									//console.log(Math.abs(bankData[k].replace('$', '').replace(',', '')));
									filterData[txn_1_Amount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && is_txn_1_date == false) {
									filterData[txn_1_Date] = bankData[k];
									is_txn_1_date = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[txn_1_Desc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == txn_1_table_format.length && filterData[txn_1_Date].length < 11 && (filterData[txn_1_Date].indexOf('/') != -1 || filterData[txn_1_Date].indexOf('-') != -1)) {
								is_txn_1_start = true;
								if (txn_1_type == 'dr') {
									debits.push({
										"date": filterData[txn_1_Date],
										"description": filterData[txn_1_Desc],
										"amount": filterData[txn_1_Amount],
										"type": 'dr'
									})
								} else {
									credits.push({
										"date": filterData[txn_1_Date],
										"description": filterData[txn_1_Desc],
										"amount": filterData[txn_1_Amount],
										"type": 'cr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_1_start) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}
								if (extraDesc != "") {
									if (txn_1_type == 'dr') {
										debits[debits.length - 1].description += ' ' + extraDesc;
									} else {
										credits[credits.length - 1].description += ' ' + extraDesc;
									}
								}
							}
						} else if ((debits.length > 0 || credits.length > 0) && is_txn_1_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {

								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}
							}
							if (extraDesc != "") {
								if (txn_1_type == 'dr') {
									debits[debits.length - 1].description += ' ' + extraDesc;
								} else {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}
							}
						}
					}

					/**START For Other Txn 2 */

					if (txn_2_start == "No" && txn_2_start_string.trim() != "") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_2_start_string.trim()) {
								var is_txn_2_start = false;
								txn_2_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (txn_2_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_2_end_string.trim()) {
								txn_2_start = "No";
							}
						}
					}

					if (txn_2_start == "Yes") {

						var filterData = [];
						var intRegex = /^\d+$/;
						var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
						var dateRegex = new RegExp("^[0-9\-/.]+$");
						filterData[txn_2_Desc] = '';
						filterData[txn_2_Date] = '';

						if (/\s(\d{1,2})\s*\-\s*(\d{1,2})\s/g.test(array[i])) {
							bankData = array[i].replace(/(\s\d{1,2})\s*\-\s*(\d{1,2})\s/g, "$1\-$2").split(/  +/g)
						}

						//Comerica Bank
						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k].replace(/[0-9]+/g, " $&"));
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}

						// Huntington National bank
						if (data.bank_data_val.bank_id == 147) {

							bankData = array[i].split(/ /g);
							bankData = bankData.filter(Boolean);

							if (array[i].indexOf('Waives') != -1) {
								pos_deposits = array[i].indexOf('Waives');
							}

							if (array[i].indexOf('Service') != -1) {
								pos_withdrawals = array[i].indexOf('Service');
							}

							if (array[i].indexOf('Description') != -1) {
								pos_description = array[i].indexOf('Description');
							}

							if (bankData[0] != undefined && bankData[0] != '' && bankData[0].length < 11 && dateRegex.test(bankData[0]) && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {

								let withdrawalAmt = array[i].substring(pos_withdrawals, pos_deposits).trim().replace('$', '').replace(',', '').replace('-', '');
								let depositAmt = array[i].substring(pos_deposits, pos_description).trim().replace('$', '').replace(',', '').replace('-', '');

								let temDescription = bankData.splice(2, bankData.length - 1).join(" ");

								if (depositAmt != "" && (intRegex.test(depositAmt) || floatRegex.test(depositAmt)) && depositAmt.length > 0 && depositAmt.indexOf('.') != -1) {
									console.log("deposits");
									depositAmt = parseFloat(depositAmt.trim().replace('$', '').replace(',', ''));
									is_txn_2_start = true
									txn_2_type = 'cr'
									credits.push({
										"date": bankData[0],
										"description": temDescription + ' : Waives and Discounts (+)',
										"amount": depositAmt,
										"type": 'cr'
									})

								} else if (withdrawalAmt != "" && (intRegex.test(withdrawalAmt) || floatRegex.test(withdrawalAmt)) && withdrawalAmt.length > 0 && withdrawalAmt.indexOf('.') != -1) {
									console.log("withdrawalAmt");
									withdrawalAmt = parseFloat(withdrawalAmt.trim().replace('$', '').replace(',', ''));
									is_txn_2_start = true
									txn_2_type = 'dr'
									debits.push({
										"date": bankData[0],
										"description": temDescription + ' : Service Charge (-)',
										"amount": withdrawalAmt,
										"type": 'dr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_2_start && bankData[0] != undefined) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}

								}
								if (extraDesc != "") {
									if (txn_2_type == 'dr') {
										debits[debits.length - 1].description = debits[debits.length - 1].description.replace(': Service Charge (-)', extraDesc + ': Service Charge (-)');
									} else {
										credits[credits.length - 1].description = credits[credits.length - 1].description.replace(': Waives and Discounts (+)' + extraDesc + ': Waives and Discounts (+)');
									}
								}

							}
							continue;

						}

						//U.S Bank check table
						if (data.bank_data_val.bank_id == 61) {
							bankData = bankData.filter(Boolean);
							var temp_bankData = array[i].split(/ /g);
							temp_bankData = temp_bankData.filter(Boolean);
							var temp_date = "";
							var temp_amt = "";
							var intRegex = /^\d+$/;
							var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
							console.log("tem", temp_bankData);
							for (k = 0; k < temp_bankData.length; k++) {
								var filterAmount = temp_bankData[k].trim().replace(/[$,-]+/g, '');
								if (monthArray.includes(temp_bankData[k].trim())) {
									var result = formatStringDate(temp_bankData[k] + ' ' + temp_bankData[k + 1].match(/\d/g).join(""));
									if (result) {
										temp_date = result;
									}
								} else if (floatRegex.test(Math.abs(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									temp_amt = temp_bankData[k];
								}
							}

							if (temp_date != '' && temp_amt != '') {
								checks.push({
									"date": temp_date,
									"cheque_no": bankData[0],
									"amount": Math.abs(temp_amt.trim().replace(/[$,-]+/g, '')),
									"description": bankData[bankData.length - 2].replace(temp_amt, '') + " - " + bankData[bankData.length - 1],
									"type": 'cr'
								})
							}
							continue;
						}


						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {

								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}


						//City National Bank
						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');
							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {

								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;
								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == txn_2_end_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							}
						}


						if (bankData.length >= txn_2_table_format.length) {
							var is_txn_2_date = false;
							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}

							//US Bank
							if (data.bank_data_val.bank_id == 61) {
								bankData = array[i].split(/ /g);
								bankData = bankData.filter(Boolean);
								for (k = 0; k < bankData.length; k++) {
									if (monthArray.includes(bankData[k].trim())) {
										var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
										if (result) {
											bankData[k] = result;
											bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
											// Remove reference number
											if (/^[0-9]{1,20}$/.test(bankData[bankData.length - 2])) {
												bankData.splice(bankData.length - 2, 1);
											}
											break;
										}
									}

								}
							}

							for (k = 0; k < bankData.length; k++) {


								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '');
								var intRegex = /^\d+$/;
								var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									//console.log(Math.abs(bankData[k].replace('$', '').replace(',', '')));
									filterData[txn_2_Amount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && is_txn_2_date == false) {
									filterData[txn_2_Date] = bankData[k];
									is_txn_2_date = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[txn_2_Desc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == txn_2_table_format.length && filterData[txn_2_Date].length < 11 && (filterData[txn_2_Date].indexOf('/') != -1 || filterData[txn_2_Date].indexOf('-') != -1)) {
								is_txn_2_start = true;
								if (txn_2_type == 'dr') {
									debits.push({
										"date": filterData[txn_2_Date],
										"description": filterData[txn_2_Desc],
										"amount": filterData[txn_2_Amount],
										"type": 'dr'
									})
								} else {
									credits.push({
										"date": filterData[txn_2_Date],
										"description": filterData[txn_2_Desc],
										"amount": filterData[txn_2_Amount],
										"type": 'cr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_2_start) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}

								}
								if (extraDesc != "") {
									if (txn_2_type == 'dr') {
										debits[debits.length - 1].description += ' ' + extraDesc;
									} else {
										credits[credits.length - 1].description += ' ' + extraDesc;
									}
								}

							}
						} else if ((debits.length > 0 || credits.length > 0) && is_txn_2_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {

								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}

							}
							if (extraDesc != "") {
								if (txn_2_type == 'dr') {
									debits[debits.length - 1].description += ' ' + extraDesc;
								} else {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}
							}

						}
					}

					/**START For Other Txn 3 */

					if (txn_3_start == "No" && txn_3_start_string.trim() != "") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_3_start_string.trim()) {
								var is_txn_3_start = false;
								txn_3_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_4_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (txn_3_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_3_end_string.trim()) {
								txn_3_start = "No";
							}
						}
					}

					if (txn_3_start == "Yes") {

						var filterData = [];
						filterData[txn_3_Desc] = '';
						filterData[txn_3_Date] = '';

						if (/\s(\d{1,2})\s*\-\s*(\d{1,2})\s/g.test(array[i])) {
							bankData = array[i].replace(/(\s\d{1,2})\s*\-\s*(\d{1,2})\s/g, "$1\-$2").split(/  +/g)
						}

						//Comerica Bank
						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k].replace(/[0-9]+/g, " $&"));
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}

						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {

								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}


						//City National Bank

						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');
							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {

								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;
								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == txn_3_end_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							}
						}

						if (bankData.length >= txn_3_table_format.length) {
							var is_txn_3_date = false;
							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}

							//US Bank
							if (data.bank_data_val.bank_id == 61) {
								bankData = array[i].split(/ /g);
								bankData = bankData.filter(Boolean);
								for (k = 0; k < bankData.length; k++) {
									if (monthArray.includes(bankData[k].trim())) {
										var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
										if (result) {
											bankData[k] = result;
											bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
											// Remove reference number
											if (/^[0-9]{1,20}$/.test(bankData[bankData.length - 2])) {
												bankData.splice(bankData.length - 2, 1);
											}
											break;
										}
									}

								}
							}



							for (k = 0; k < bankData.length; k++) {

								//bankData[k].trim().replace(/[$,-]+/g,'');
								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '');
								var intRegex = /^\d+$/;
								var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									filterData[txn_3_Amount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && is_txn_3_date == false) {
									filterData[txn_3_Date] = bankData[k];
									is_txn_3_date = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[txn_3_Desc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == txn_3_table_format.length && filterData[txn_3_Date].length < 11 && (filterData[txn_3_Date].indexOf('/') != -1 || filterData[txn_3_Date].indexOf('-') != -1)) {
								is_txn_3_start = true;
								if (txn_3_type == 'dr') {
									debits.push({
										"date": filterData[txn_3_Date],
										"description": filterData[txn_3_Desc],
										"amount": filterData[txn_3_Amount],
										"type": 'dr'
									})
								} else {
									credits.push({
										"date": filterData[txn_3_Date],
										"description": filterData[txn_3_Desc],
										"amount": filterData[txn_3_Amount],
										"type": 'cr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_3_start) {

								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}
								if (extraDesc != "") {
									if (txn_3_type == 'dr') {
										debits[debits.length - 1].description += ' ' + extraDesc;
									} else {
										credits[credits.length - 1].description += ' ' + extraDesc;
									}
								}
							}
						} else if ((debits.length > 0 || credits.length > 0) && is_txn_3_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {

								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}

							}
							if (extraDesc != "") {
								if (txn_3_type == 'dr') {
									debits[debits.length - 1].description += ' ' + extraDesc;
								} else {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}
							}
						}
					}

					/**START For Other Txn 4 */

					if (txn_4_start == "No" && txn_4_start_string.trim() != "") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_4_start_string.trim()) {
								var is_txn_4_start = false;
								txn_4_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_5_start = "No";
							}
						}
					}

					if (txn_4_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_4_end_string.trim()) {
								txn_4_start = "No";
							}
						}
					}

					if (txn_4_start == "Yes") {
						var filterData = [];
						filterData[txn_4_Desc] = '';
						filterData[txn_4_Date] = '';

						if (/\s(\d{1,2})\s*\-\s*(\d{1,2})\s/g.test(array[i])) {
							bankData = array[i].replace(/(\s\d{1,2})\s*\-\s*(\d{1,2})\s/g, "$1\-$2").split(/  +/g)
						}

						//Comerica Bank
						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k].replace(/[0-9]+/g, " $&"));
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}

						//Citizen Bank 				
						if (data.bank_data_val.bank_id == 112) {
							bankData = bankData.filter(Boolean);

							if (bankData.length == 1) {

								var temp_date = [];
								var temp_amt = [];
								var temp_des = [];
								if (bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
									temp_date.push(bankData[0]);
									i++;

									while (bankData.length == 1) {
										bankData = array[i].split(/  +/g);
										bankData = bankData.filter(Boolean);
										i++;
										if (bankData.length == 1) {
											var filterAmount = bankData[0].trim().replace(/[$,]+/g, '');
											if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
												temp_amt.push(Math.abs(bankData[0].trim().replace(/[$,]+/g, '')));
											} else if (bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) {
												temp_date.push(bankData[0]);
											} else {
												temp_des.push(bankData[0]);
											}

										}
									}
									for (var k = 0; k < temp_des.length; k++) {
										credits.push({
											"date": temp_date[k],
											"description": temp_des[k],
											"amount": temp_amt[k],
											"type": 'cr'
										})
									}
									i--;
								}
							} else if (bankData.length > 0 && bankData[drDate].length < 11 && (bankData[drDate].indexOf('/') != -1 || bankData[drDate].indexOf('-') != -1)) {
								var matchArray = bankData[1].match(/[\d,]*\.\d+/);
								var amount = matchArray[0];
								var desc = bankData[1].replace(amount, '');
								bankData[drDate] = bankData[drDate]
								bankData[drAmount] = amount;
								bankData[drDesc] = desc + " " + (bankData[2] != undefined ? bankData[2] : "");
							}

						}


						//City National Bank

						if (data.bank_data_val.bank_id == 119 && bankData.length > 0) {
							var filterAmount = bankData[bankData.length - 1].trim().replace(/[$,]+/g, '');
							if ((bankData[0].length < 11 && (bankData[0].indexOf('/') != -1 || bankData[0].indexOf('-') != -1)) && !((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1)) {

								let desc = bankData.splice(2, bankData.length).join(" ");
								bankData.splice(bankData.length, 0, desc);
								bankData = bankData.filter(Boolean);
								i++;
								while (1) {
									temp_bankData = array[i].split(/  +/g);
									temp_bankData = temp_bankData.filter(Boolean);
									i++;
									if ((temp_bankData[0].length < 11 && (temp_bankData[0].indexOf('/') != -1 || temp_bankData[0].indexOf('-') != -1)) || (temp_bankData[0].trim() == txn_4_end_string.trim())) {
										break;
									} else {
										var filterAmount = temp_bankData[temp_bankData.length - 1].trim().replace(/[$,]+/g, '');
										if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
											bankData[bankData.length] = temp_bankData[temp_bankData.length - 1];
										}
										desc = temp_bankData.splice(0, temp_bankData.length - 1).join();
										bankData.splice(bankData.length - 1, 0, desc);

									}
								}
								i = i - 2;
							}
						}


						if (bankData.length >= txn_4_table_format.length) {
							var is_txn_4_date = false;


							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}

							//US Bank
							if (data.bank_data_val.bank_id == 61) {
								bankData = array[i].split(/ /g);
								bankData = bankData.filter(Boolean);
								for (k = 0; k < bankData.length; k++) {
									if (monthArray.includes(bankData[k].trim())) {
										var result = formatStringDate(bankData[k] + ' ' + bankData[k + 1].match(/\d/g).join(""));
										if (result) {
											bankData[k] = result;
											bankData[k + 1] = bankData[k + 1].replace(/^\d+/g, '');
											// Remove reference number
											if (/^[0-9]{1,20}$/.test(bankData[bankData.length - 2])) {
												bankData.splice(bankData.length - 2, 1);
											}
											break;
										}
									}

								}
							}

							for (k = 0; k < bankData.length; k++) {

								//bankData[k].trim().replace(/[$,-]+/g,'');
								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '');
								var intRegex = /^\d+$/;
								var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									filterData[txn_4_Amount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1) && is_txn_4_date == false) {
									filterData[txn_4_Date] = bankData[k];
									is_txn_4_date = true;
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[txn_4_Desc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == txn_4_table_format.length && filterData[txn_4_Date].length < 11 && (filterData[txn_4_Date].indexOf('/') != -1 || filterData[txn_4_Date].indexOf('-') != -1)) {
								is_txn_4_start = true;
								if (txn_4_type == 'dr') {
									debits.push({
										"date": filterData[txn_4_Date],
										"description": filterData[txn_4_Desc],
										"amount": filterData[txn_4_Amount],
										"type": 'dr'
									})
								} else {
									credits.push({
										"date": filterData[txn_4_Date],
										"description": filterData[txn_4_Desc],
										"amount": filterData[txn_4_Amount],
										"type": 'cr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_4_start) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}
								if (extraDesc != "") {
									if (txn_4_type == 'dr') {
										debits[debits.length - 1].description += ' ' + extraDesc;
									} else {
										credits[credits.length - 1].description += ' ' + extraDesc;
									}
								}
							}
						} else if ((debits.length > 0 || credits.length > 0) && is_txn_4_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {

								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}
							}
							if (extraDesc != "") {
								if (txn_4_type == 'dr') {
									debits[debits.length - 1].description += ' ' + extraDesc;
								} else {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}
							}
						}
					}

					/**START For Other Txn 5 */

					if (txn_5_start == "No" && txn_5_start_string.trim() != "") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_5_start_string.trim()) {
								var is_txn_5_start = false;
								txn_5_start = "Yes";
								checks_start = "No";
								debit_start = "No";
								credits_start = "No";
								txn_1_start = "No";
								txn_2_start = "No";
								txn_3_start = "No";
								txn_4_start = "No";
							}
						}
					}

					if (txn_5_start == "Yes") {
						for (k = 0; k < bankData.length; k++) {
							if (bankData[k].trim() == txn_5_end_string.trim()) {
								//console.log("txn_5_end_string found on line number "+i);
								txn_5_start = "No";
							}
						}
					}

					if (txn_5_start == "Yes") {
						var filterData = [];
						filterData[txn_5_Desc] = '';
						filterData[txn_5_Date] = '';
						//Comerica Bank
						if (data.bank_data_val.bank_id == 99) {
							for (k = 0; k < bankData.length; k++) {
								var result = formatStringDate(bankData[k].replace(/[0-9]+/g, " $&"));
								if (result) {
									bankData[k] = result;
									bankData[bankData.length - 1] = '';
									break;
								}
							}
						}

						if (bankData.length >= txn_5_table_format.length) {

							//PNC bank (remove reference number)
							if (data.bank_data_val.bank_id == 117 || data.bank_data_val.bank_id == 124) {
								if (bankData.length == 6 || bankData.length == 5) {
									bankData.splice(bankData.length - 2, 2);
								} else {
									bankData.splice(bankData.length - 1, 1);
								}

							}

							//Union bank (remove reference number)
							if (data.bank_data_val.bank_id == 121 || data.bank_data_val.bank_id == 123) {
								var lastIndex = bankData[bankData.length - 2].lastIndexOf(" ");

								bankData[bankData.length - 2] = bankData[bankData.length - 2].substring(0, lastIndex);
								bankData = bankData.filter(Boolean);

							}



							for (k = 0; k < bankData.length; k++) {


								var filterAmount = bankData[k].trim().replace(/[$,-]+/g, '');
								var intRegex = /^\d+$/;
								var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
								if ((intRegex.test(Math.abs(filterAmount)) || floatRegex.test(Math.abs(filterAmount))) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
									filterData[txn_5_Amount] = Math.abs(bankData[k].trim().replace(/[$,-]+/g, ''));
								} else if (bankData[k].length < 11 && (bankData[k].indexOf('/') != -1 || bankData[k].indexOf('-') != -1)) {
									filterData[txn_5_Date] = bankData[k];
								} else {
									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										filterData[txn_5_Desc] += bankData[k] + ' ';
									}
								}

							}

							if (filterData.length == txn_5_table_format.length && filterData[txn_5_Date].length < 11 && (filterData[txn_5_Date].indexOf('/') != -1 || filterData[txn_5_Date].indexOf('-') != -1)) {
								is_txn_5_start = true;
								if (txn_5_type == 'dr') {
									debits.push({
										"date": filterData[txn_5_Date],
										"description": filterData[txn_5_Desc],
										"amount": filterData[txn_5_Amount],
										"type": 'dr'
									})
								} else {
									credits.push({
										"date": filterData[txn_5_Date],
										"description": filterData[txn_5_Desc],
										"amount": filterData[txn_5_Amount],
										"type": 'cr'
									})

								}
							} else if ((debits.length > 0 || credits.length > 0) && is_txn_5_start) {
								var addDesc = true;
								var extraDesc = "";
								for (k = 0; k < bankData.length; k++) {

									if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
										$.each(ignoreArray, function(index, value) {
											if (value.indexOf("[rgx]") != -1) {
												sliceValue = value.slice(6, -7);
												var regex = new RegExp(sliceValue);
												var str = bankData[k].trim();
												if (regex.test(str)) {
													addDesc = false;
												}
											}
										});
										if (addDesc) {
											extraDesc += bankData[k] + " ";
										}
									}
								}
								if (extraDesc != "") {
									if (txn_5_type == 'dr') {
										debits[debits.length - 1].description += ' ' + extraDesc;
									} else {
										credits[credits.length - 1].description += ' ' + extraDesc;
									}
								}
							}
						} else if ((debits.length > 0 || credits.length > 0) && is_txn_5_start) {
							var addDesc = true;
							var extraDesc = "";
							for (k = 0; k < bankData.length; k++) {

								if (bankData[k].trim() != undefined && bankData[k].trim() != '') {
									$.each(ignoreArray, function(index, value) {
										if (value.indexOf("[rgx]") != -1) {
											sliceValue = value.slice(6, -7);
											var regex = new RegExp(sliceValue);
											var str = bankData[k].trim();
											if (regex.test(str)) {
												addDesc = false;
											}
										}
									});
									if (addDesc) {
										extraDesc += bankData[k] + " ";
									}
								}
							}
							if (extraDesc != "") {
								if (txn_5_type == 'dr') {
									debits[debits.length - 1].description += ' ' + extraDesc;
								} else {
									credits[credits.length - 1].description += ' ' + extraDesc;
								}
							}
						}
					}

				}
				console.log(credits);
				console.log(debits);
				console.log(checks);

				var data_json = {
					"type": 1,
					"bank_id": data.bank_data_val.bank_id,
					"upload_pdf_file": data.extractData.upload_pdf_file,
					"original_pdf_file_name": data.extractData.original_pdf_file_name,
					"split_page_num_array": data.extractData.split_page_num_array,
					"page_array": data.extractData.page_array,
					"isCompleteMultiAcc": data.extractData.isCompleteMultiAcc,
					"account_number": account_number,
					"service_fees": service_fees,
					"name": name,
					"account_holder_name": data.extractData.account_holder_name,
					"account_type": data.extractData.account_type,
					"bank_date_format": data.bank_data_val.bank_date_format,
					"currency": data.bank_data_val.currency,
					"account_ownership": data.extractData.account_ownership,
					"name_of_bank": data.extractData.name_of_bank,
					"bank_address": data.extractData.bank_address,
					"bank_city": data.extractData.bank_city,
					"bank_state": data.extractData.bank_state,
					"bank_zip": data.extractData.bank_zip,
					"start_date": start_date,
					"end_date": end_date,
					"open_balance": begining_balance,
					"begining_balance": begining_balance,
					"closing_balance": closing_balance,
					"pages": pages,
					"service_fee_1": service_fee_1,
					"service_fee_title_1": service_fee_title_1,
					"service_fee_type_1": service_fee_type_1,
					"service_fee_2": service_fee_2,
					"service_fee_title_2": service_fee_title_2,
					"service_fee_type_2": service_fee_type_2,
					"multiple_account": multiple_account,
					"uploadedXlsFileName": data.uploadedXlsFileName,
					"newFolderName": newFolderName,
					"zipFileName": data.zipFileName,
					"history_id": data.history_id,
					"accType": accType,
					"check_all_pdf_process": check_all_pdf_process,
					"countPdfExt": data.countPdfExt,
					"count": data.count,
					//"begining_balance": begining_balance,
					"credits": credits,
					"debits": debits,
					"checks": checks,
				};
				//console.log(data_json);
				//console.log("Second");
				if (user_type == 1) {
					var surl = siteurl + 'Bank_statement/createXLSBankStatement';
				} else {
					var surl = siteurl + 'Bank_statement/createXLSBankStatementNewFormat';
				}

				$.post(surl, data_json, function(response) {
					console.log('createXLSBankStatement');
					//console.log(response);
					var url = siteurl + 'assets/uploads/bank_statement_excel/' + response.filename;
					$('.downloadBox').show();
					$('.downloadBox .upload_template_file_name').text(response.filename);
					$('.downloadBox .check_sum').text(response.check_sum);
					$('.downloadBox .total_deposits').text(response.total_deposits);
					$('.downloadBox .count_deposits').text(response.count_deposits);
					$('.downloadBox .total_withdrawals').text(response.total_withdrawals);
					$('.downloadBox .count_withdrawals').text(response.count_withdrawals);
					$('.downloadBox').children().show();
					$('#dwnld_excel').attr('href', url);
					$('#upload').val(null);
					$('#bank_id option:eq(0)').attr('selected', 'selected');
					document.getElementById("convert_form").reset();
					q++;
					console.log("Second");
					console.log(q);
					if (data.txtSplitFileName != undefined && data.txtSplitFileName) {
						var txtSplitFileName = data.txtSplitFileName;
						if (txtSplitFileName.length > 1 && txtSplitFileName.length >= q + 1) {
							if (txtSplitFileName[q] != undefined) {
								var data_json = {
									"bank_id": data.bank_id,
									"txtFilename": txtSplitFileName[q],
									"txtSplitFileName": txtSplitFileName,
									"uploadedXlsFileName": response.filename,
									"page_array": response.page_array,
									"newFolderName": response.newFolderName,
									"checkAllPdfProcess": response.checkAllPdfProcess,
								};
								var eurl = "<?php echo base_url('Bank_statement/callFromView') ?>";
								$.post(eurl, data_json, function(response) {
									console.log('callFromView');
									console.log("Third");
									//console.log(response);
									createXLSBankStatement(response);
									return true;
								}, 'json');
							}
						} else {
							q = 0;
						}
					}

					return true;
				}, 'json');

				$("#progress_bar_2").show();
				$("#progress_bar_2").addClass("isProgress");
				$("#progress_bar_2 .progressBox .loader").fadeIn();
				$("#progress_bar_2").removeClass("isProgress");
				$("#progress_bar_2 .progressBox .loader").hide();
				$("#progress_bar_2").addClass("isRight");
				$("#progress_bar_2 .progressBox .success,#progress_bar_2 .content .downloadBox").fadeIn();

			});
		}

	}
</script>

<script>
	function checkAmount(amt) {
		var intRegex = /^\d+$/;
		var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
		var filterAmount = amt.trim().replace('$', '').replace(',', '');
		if ((intRegex.test(filterAmount) || floatRegex.test(filterAmount)) && filterAmount.length > 0 && filterAmount.indexOf('.') != -1) {
			return Math.abs(amt.replace('$', '').replace(',', ''));
		} else {
			return false;
		}
	}

	function formatStringDate(date) {
		var isDate = false;
		var monthArray = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
		if (date) {
			var dateArray = date.split(" ");
			dateArray = dateArray.filter(function(str) {
				return /\S/.test(str);
			});
			//console.log(dateArray);
			for (i = 0; i < dateArray.length; i++) {
				if (jQuery.inArray(dateArray[i].toLowerCase(), monthArray) !== -1) {
					//console.log(dateArray[i].toLowerCase());
					isDate = true;
					break;
				}
			}
		} else {
			return false;
		}

		if (isDate) {
			var d = new Date(date),
				month = '' + (d.getMonth() + 1),
				day = '' + d.getDate(),
				year = d.getFullYear();

			if (month.length < 2) {
				month = '0' + month;
			}

			if (day.length < 2) {
				day = '0' + day;
			}
			//console.log(year);
			return [month, day].join('/');
			//return [day, month].join('/');
		} else {
			return false;
		}
	}

	function checkDate(date) {
		if (Date.parse(date) && date.length < 11 && (date.indexOf('/') != -1 || date.indexOf('-') != -1)) {
			return date;
		} else {
			return false;
		}
	}

	function hidedownload() {
		$('.downloadBox').hide();
	}
	$('#upload').change(function() {
		var filepath = this.value;
		var m = filepath.match(/([^\/\\]+)$/);
		var filename = m[1];
		$('#filename').html(filename);
	});

	$('#spread_file').click(function() {
		if ($.trim($('#spreadDrop').val()) == '') {
			return true;
		} else {
			// $("#template_name").text("");
			// $('.downloadBox').children().hide();
			// $('.downloadBox .upload_template_file_name').text("");
			// $('.downloadBox .check_sum').text("");
			// $('.downloadBox .total_deposits').text("");
			// $('.downloadBox .count_deposits').text("");
			// $('.downloadBox .total_withdrawals').text("");
			// $('.downloadBox .count_withdrawals').text("");
			// $("#progress_bar_1").show();
			// $("#progress_bar_1").addClass("isProgress");
			// $("#progress_bar_1").removeClass("isRight");
			// $("#progress_bar_1").removeClass("isWrong");
			// $("#progress_bar_1 .progressBox .loader").fadeIn();
			// $("#progress_bar_2").show();
			// $("#progress_bar_2").removeClass("isRight");
			// $("#progress_bar_2").removeClass("isWrong");
			// $("#progress_bar_2").removeClass("isProgress");
			// $("#progress_bar_3").hide();
		}
	})

	$('#update-existing-template').click(function() {
		$('#data_file').show();
		$('#update-existing-template').show();
	})
</script>
<?php //echo $bulk_upload."<br>";
?>
<?php if (isset($bulk_upload) && $bulk_upload) { ?>
	<script type="text/javascript">
		var bulkUpload = '<?php echo addslashes(json_encode($output)); ?>';

		var data = JSON.parse(bulkUpload);
		console.log(data);
		console.log("Nirdesh");
		if (data.multiple_account == true && data.queue_no > 0 && data.multiple_process == true) {
			if (data.queue_no == 1) {
				var data_1 = data;
			} else if (data.queue_no == 2) {
				var data_2 = data;
			} else if (data.queue_no == 3) {
				var data_3 = data;
			} else if (data.queue_no == 4) {
				var data_4 = data;
			} else if (data.queue_no == 5) {
				var data_5 = data;
			} else if (data.queue_no == 6) {
				var data_6 = data;
			} else if (data.queue_no == 7) {
				var data_7 = data;
			}

			if (data.queue_no == 1) {
				//createXLSBankStatement(data_1);
				setTimeout(function() {
					createXLSBankStatement(data_1);
				}, 10000);
			} else if (data.queue_no == 2) {
				setTimeout(function() {
					createXLSBankStatement(data_2);
				}, 20000);
			} else if (data.queue_no == 3) {
				setTimeout(function() {
					createXLSBankStatement(data_3);
				}, 30000);
			} else if (data.queue_no == 4) {
				setTimeout(function() {
					createXLSBankStatement(data_4);
				}, 40000);
			} else if (data.queue_no == 5) {
				setTimeout(function() {
					createXLSBankStatement(data_5);
				}, 50000);
			} else if (data.queue_no == 6) {
				setTimeout(function() {
					createXLSBankStatement(data_6);
				}, 60000);
			} else if (data.queue_no == 7) {
				setTimeout(function() {
					createXLSBankStatement(data_7);
				}, 70000);
			}
			/*else{
			    	createXLSBankStatement(data);
			    }*/
		} else if (data.multiple_account == true && data.multiple_process == false) {
			createXLSBankStatement(data);
		} else if (data.multiple_account == false) {
			createXLSBankStatement(data);
		}
		//setTimeout(function(){ createXLSBankStatement(data); }, 10000);
		//createXLSBankStatement(data);
	</script>
<?php } ?>

<?php if ($this->session->userdata('type_of_upload') == 1) { ?>
	<?php include('footer.php'); ?>
<?php } ?>