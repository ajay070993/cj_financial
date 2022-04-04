<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Bank Statement</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <style type="text/css">
            body {
                background-color: #f5f5f5 !important;
            }
            .form-signin {
                padding: 19px 29px 50px;
                margin: 0 auto 20px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"] {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
            }
            .header {
                margin-bottom: 15px;
                overflow: hidden;
                background-color: #1e90ff;
                padding: 20px 10px;
            }
            .header a {
                float: left;
                color: #fff;
                text-align: center;
                padding: 5px;
                text-decoration: none;
                font-size: 18px; 
                line-height: 25px;
            }
            .header a.logo {
                font-size: 25px;
                font-weight: bold;
            }
            .header-right {
                float: right;
            }
            .header a:hover {
                color: #fff;
                text-decoration: none;
            }

            .display-none{
                display: none;
            }

            @media screen and (max-width: 500px) {
                .header a {
                    float: none;
                    display: block;
                    text-align: left;
                }

                .header-right {
                    float: none;
                }
            }
        </style>
        <?php $this->load->view('includes/common'); ?>
    </head>
    <body>
        <div class="header">
            <a href="javascript:" class="logo">Bank Statement</a>
            <div class="header-right">
                <a style="color: #000;margin-right: 50px;" href="<?php echo site_url('convert-bank-statement'); ?>">Go To Convert File</a>
                <a href="<?php echo site_url('Logout'); ?>">Logout&nbsp;<img title="Logout" style="height: 20px;width: 20px;" src="<?php echo $this->config->item('assets'); ?>img/logout_catalogue.png"></a>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-signin ajax_form" action="<?php echo site_url('Bank_statement/uploadPdfFile'); ?>" method="post">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="exampleFormControlFile1">Upload file to create/edit template</label>
                                <input type="file" name="image_name" class="form-control-file" id="exampleFormControlFile1">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" style="background-color: #1e90ff;border-color: #1e90ff;font-weight: bold;" class="btn btn-primary">Upload</button>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <a style="cursor: pointer;" id="update-existing-template" class="display-none">Update existing template</a>
                                <a style="cursor: pointer;" id="create-new-template" class="display-none">Create new template</a>
                            </div>
                        </div>
                    </form>  
                </div>
            </div>

            <div class="row" id="data_file" style="display: none;">
                <div class="col-md-6" >
                    <form class="form-signin" style="height: 800px;">
                        <p>Pdf viewer</p>
                        <object id="pdf_viewer" data="" type="application/pdf" style="width: 100%;height: 730px;"></object>
                    </form>  
                </div>  
                <div class="col-md-6" style="height: 797px;overflow: auto;">
                    <form class="form-signin ajax_form template-form" action="<?php echo site_url('Bank_statement/addString'); ?>" method="post">
                        <div class="form-group display-none bank-list">
                            <label for="bank_id">Bank</label>
                            <select class="form-control" id="bank_id" name="bank_id" onchange="setStringValue();">
                                <option value="">Select Your Bank</option>
                                <?php foreach ($allBanks as $key => $value) { ?>
                                    <option value="<?php echo $value->id ?>"><?php echo $value->bank_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group create-bank">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name">
                        </div>
                        <div class="form-group">
                            <label for="account_number_string">Account number string</label>
                            <input type="text" class="form-control" id="account_number_string" name="account_number_string" placeholder="Account number string">
                        </div>

                        <div class="form-group">
                            <label for="credit_start_string">Credit Start String</label>
                            <input type="text" class="form-control" id="credit_start_string" name="credit_start_string" placeholder="Credit Start String">
                        </div>
                        
                        <div class="form-group">
                            <label for="credit_table_format">Credit Format</label>
                            <textarea class="form-control rounded-0" id="credit_table_format" name="credit_table_format" rows="2">{ 'date': 'Date', 'description': 'Description', 'amount': 'Amount' }
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label for="credit_end_string">Credit End String</label>
                            <input type="text" class="form-control" id="credit_end_string" name="credit_end_string" placeholder="Credit End String">
                        </div>

                        <div class="form-group">
                            <label for="debit_start_string">Debit Start String</label>
                            <input type="text" class="form-control" id="debit_start_string" name="debit_start_string" placeholder="Debit Start String">
                        </div>
                        
						<div class="form-group">
                            <label for="debit_table_format">Debit Format</label>
                            <textarea class="form-control rounded-0" id="debit_table_format" name="debit_table_format" rows="2">{ 'date': 'Date', 'description': 'Description', 'amount': 'Amount' }
                            </textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="debit_end_string">Debit End String</label>
                            <input type="text" class="form-control" id="debit_end_string" name="debit_end_string" placeholder="Debit End String">
                        </div>

                        <div class="form-group">
                            <label for="checks_start_string">Checks Start String</label>
                            <input type="text" class="form-control" id="checks_start_string" name="checks_start_string" placeholder="Checks Start String">
                        </div>

						<div class="form-group">
                            <label for="cheque_table_format">Check Format</label>
                            <textarea class="form-control rounded-0" id="cheque_table_format" name="cheque_table_format" rows="2">{ 'date': 'Date', 'description': 'Description', 'amount': 'Amount' }
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label for="checks_end_string">Checks End String</label>
                            <input type="text" class="form-control" id="checks_end_string" name="checks_end_string" placeholder="Checks End String">
                        </div>

                        <!-- <div class="form-group">
                            <label for="se10">Se10</label>
                            <input type="text" class="form-control" id="se10" name="se10" placeholder="se10">
                        </div>
                        
                        <div class="form-group">
                            <label for="contract_nbr">Contract Nbr</label>
                            <input type="text" class="form-control" id="contract_nbr" name="contract_nbr" placeholder="Contract Nbr">
                        </div>
                        
                        <div class="form-group">
                            <label for="amort_date">Amort Date</label>
                            <input type="text" class="form-control" id="amort_date" name="amort_date" placeholder="Amort Date">
                        </div>
                        
                        <div class="form-group">
                            <label for="instant_decision_date">Instant Decision Date</label>
                            <input type="text" class="form-control" id="instant_decision_date" name="instant_decision_date" placeholder="Instant Decision Date">
                        </div> -->
                        
                        <div class="form-group">
                            <label for="account_holder_name">Account Holder Name</label>
                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" placeholder="Account Holder Name">
                        </div>
                        
                        <div class="form-group">
                            <label for="account_type">Account Type</label>
                            <input type="text" class="form-control" id="account_type" name="account_type" placeholder="Account Type">
                        </div>
                        
                        <!-- <div class="form-group">
                            <label for="account_ownership">Account Ownership</label>
                            <input type="text" class="form-control" id="account_ownership" name="account_ownership" placeholder="Account Ownership">
                        </div> -->
                        
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
                        
                        <!-- <div class="form-group">
                            <label for="current_balance">Current Balance</label>
                            <input type="text" class="form-control" id="current_balance" name="current_balance" placeholder="Current Balance">
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date">
                        </div>
                        <div class="form-group">
                            <label for="open_balance">Open Balance</label>
                            <input type="text" class="form-control" id="open_balance" name="open_balance" placeholder="Open Balance">
                        </div>
                        <div class="form-group">
                            <label for="closing_balance">Closing Balance</label>
                            <input type="text" class="form-control" id="closing_balance" name="closing_balance" placeholder="Closing Balance">
                        </div>
                        <div class="form-group">
                            <label for="total_deposits">Total Deposits</label>
                            <input type="text" class="form-control" id="total_deposits" name="total_deposits" placeholder="Total Deposits">
                        </div>
                        <div class="form-group">
                            <label for="count_deposits">Count Deposits</label>
                            <input type="text" class="form-control" id="count_deposits" name="count_deposits" placeholder="Count Deposits">
                        </div>
                        <div class="form-group">
                            <label for="total_withdrawals">Total Withdrawals</label>
                            <input type="text" class="form-control" id="total_withdrawals" name="total_withdrawals" placeholder="Total Withdrawals">
                        </div>
                        <div class="form-group">
                            <label for="count_withdrawals">Count Withdrawals</label>
                            <input type="text" class="form-control" id="count_withdrawals" name="count_withdrawals" placeholder="Count Withdrawals">
                        </div>
                        <div class="form-group">
                            <label for="total_count_check_return">Total Count Check Return</label>
                            <input type="text" class="form-control" id="total_count_check_return" name="total_count_check_return" placeholder="Total Count Check Return">
                        </div>
                        <div class="form-group">
                            <label for="total_count_inward_check_return">Total Count Inward Check Return</label>
                            <input type="text" class="form-control" id="total_count_inward_check_return" name="total_count_inward_check_return" placeholder="Total Count Inward Check Return">
                        </div>
                        <div class="form-group">
                            <label for="total_inward_check_return">Total Inward Check Return</label>
                            <input type="text" class="form-control" id="total_inward_check_return" name="total_inward_check_return" placeholder="Total Inward Check Return">
                        </div>
                        <div class="form-group">
                            <label for="total_count_outward_check_return">Total Count Outward Check Return</label>
                            <input type="text" class="form-control" id="total_count_outward_check_return" name="total_count_outward_check_return" placeholder="Total Count Outward Check Return">
                        </div>
                        <div class="form-group">
                            <label for="total_outward_check_return">Total Outward Check Return</label>
                            <input type="text" class="form-control" id="total_outward_check_return" name="total_outward_check_return" placeholder="Total Outward Check Return">
                        </div>
                        <div class="form-group">
                            <label for="count_ecs_or_emi">Count ECS Or Emi</label>
                            <input type="text" class="form-control" id="count_ecs_or_emi" name="count_ecs_or_emi" placeholder="Count ECS Or Emi">
                        </div>
                        <div class="form-group">
                            <label for="amount_ecs_or_emi">Amount ECS Or Emi</label>
                            <input type="text" class="form-control" id="amount_ecs_or_emi" name="amount_ecs_or_emi" placeholder="Amount ECS Or Emi">
                        </div>
                        <div class="form-group">
                            <label for="route">Route</label>
                            <input type="text" class="form-control" id="route" name="route" placeholder="Route">
                        </div> -->
                        <!--<div class="form-group">
                            <label for="transaction_all_level_spreading_done">Transaction All Level Spreading Done</label>
                            <input type="text" class="form-control" id="transaction_all_level_spreading_done" name="transaction_all_level_spreading_done" placeholder="Transaction All Level Spreading Done">
                        </div>-->
                        
                        <div class="form-group">
                            <label for="native_vs_non_native">Native Vs Non Native</label>
                            <input type="text" class="form-control" id="native_vs_non_native" name="native_vs_non_native" placeholder="Native Vs Non Native">
                        </div>
                        <!-- <div class="form-group">
                            <label for="check_sum">Check Sum</label>
                            <input type="text" class="form-control" id="check_sum" name="check_sum" placeholder="Check Sum">
                        </div>
                        <div class="form-group">
                            <label for="summary_and_transaction_match">Summary And Transaction Match</label>
                            <input type="text" class="form-control" id="summary_and_transaction_match" name="summary_and_transaction_match" placeholder="Summary And Transaction Match">
                        </div> -->
                        <div class="form-group">
                            <label for="pages">Pages</label>
                            <input type="text" class="form-control" id="pages" name="pages" placeholder="Pages">
                            <input type="hidden" value="1" class="form-control" id="template-action" name="template-action">
                        </div>
                        <button type="submit" style="background-color: #1e90ff;border-color: #1e90ff;font-weight: bold;" class="btn btn-primary">Save</button>

                    </form>  
                </div>
            </div>


        </div> 
    </body>
</html>


<script type="text/javascript">
    function setStringValue() {
        var bank_id = $('#bank_id').val();
        var surl = siteurl + 'Bank_statement/getBankStatementDataByBankId?bank_id=' + bank_id;
        $.getJSON(surl, function (response) {
            //console.log(response);
            if (response.success) {
                $.each( response.data, function( key, value ) {
                    if(key!='id' && key!='bank_id' ){
                        $('#'+key).val(response.data[key]);
                        //console.log(response.data[key]);
                    }
                });
            }else{
                $('.template-form').find('input:text').val('');
            }
            /**if (response.success) {
                $('#account_number_string').val(response.data.account_number_string);
                $('#credit_start_string').val(response.data.credit_start_string);
                $('#credit_end_string').val(response.data.credit_end_string);
                $('#debit_start_string').val(response.data.debit_start_string);
                $('#debit_end_string').val(response.data.debit_end_string);
                $('#checks_start_string').val(response.data.checks_start_string);
                $('#checks_end_string').val(response.data.checks_end_string);
            } else {
                $('#account_number_string').val('');
                $('#credit_start_string').val('');
                $('#credit_end_string').val('');
                $('#debit_start_string').val('');
                $('#debit_end_string').val('');
                $('#checks_start_string').val('');
                $('#checks_end_string').val('');
            }*/
        });
    }
</script>

<script type="text/javascript">
    function getUploadFile(data) {
        var surl = siteurl + 'assets/uploads/bank_statement/' + data.file_name;
        $("#pdf_viewer").attr("data", surl);
        $('#data_file').show();
        $('#update-existing-template').show();
    }

    $('#update-existing-template').click(function () {
        $('.create-bank').hide();
        $('.bank-list').show();
        $('#create-new-template').show();
        $('#update-existing-template').hide();
        $('#bank_id').val('');
        $("#template-action").val("0");
    })

    $('#create-new-template').click(function () {
        $('.create-bank').show();
        $('.bank-list').hide();
        $('#create-new-template').hide();
        $('#update-existing-template').show();
        $('.template-form').find('input:text').val('');
        $("#template-action").val("1");

    })
</script>