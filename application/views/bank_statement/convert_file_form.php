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
        padding: 19px 29px 29px;
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
        <a href="<?php echo site_url('Logout'); ?>">Logout&nbsp;<img title="Logout" style="height: 20px;width: 20px;" src="<?php echo $this->config->item('assets'); ?>img/logout_catalogue.png"></a>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <!-- <a href="<?php echo base_url()?>Bank_statement/createXLSBankStatement">Test</a> -->
        <iframe id="secretIFrame" src="" style="display:none; visibility:hidden;"></iframe>
        <div class="col-md-12" >

          <form id="convert_form" class="form-signin ajax_form" action="<?php echo base_url('Bank_statement/convertBankStatement'); ?>" method="post">
            <div class="form-group">
              <label for="bank_id">Bank</label>
              <select class="form-control" id="bank_id" name="bank_id" onchange="hidedownload();">
                <option value="">Select Bank</option>
                <?php foreach ($allBanks as $key => $value) { ?>
                  <option value="<?php echo $value->id ?>"><?php echo $value->bank_name ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="exampleFormControlFile1">Upload file</label>
                <input type="file" name="image_name" class="form-control-file" id="exampleFormControlFile1">
            </div>
            <button type="submit" style="background-color: #1e90ff;border-color: #1e90ff;font-weight: bold;" class="btn btn-primary">Convert File</button>
          </form>
        </div>

        <a id="dwnld_excel" style="margin-left: 20px;padding: 10px;background: dodgerblue;color: #fff;border-radius: 5px;display: none;" href="<?php echo base_url()?>Bank_statement/createXLSBankStatement">Download Excel</a>
      </div> 
    </div> 
  </body>
</html>





<script type="text/javascript">
  function createXLSBankStatement(data) {
	console.log(data);
	//
    var credits = [];
    var credits_start = "No";
    var debits = [];
    var debit_start = "No";
    var checks = [];
    var check_start = "No";
    var filepath = siteurl+'assets/uploads/bank_statement/'+data.textFileName; 
     //alert(data.string_record.bank_id);
    if(data.string_record.bank_id==1){
    	//var filepath = siteurl+'assets/uploads/bank_statement/Ryoko_test.txt';
        var credit_start_string = data.string_record.credit_start_string+"\r";
        var credit_close_string = data.string_record.credit_end_string
        var debit_start_string  = data.string_record.debit_start_string+"\r";
        var debit_close_string  = data.string_record.debit_end_string;
        var checks_start_string = data.string_record.checks_start_string+"\r";
        var checks_close_string = data.string_record.checks_end_string+"\r";
        
        var arrayCredit = data.string_record.credit_table_format.split(',');
    	
    	// var arrayCredit = { 'date': 'Date', 'description': 'Description', 'amount': 'Amount' };
    	var arrayDebit = data.string_record.debit_table_format.split(',');
    	var arrayCheque = data.string_record.cheque_table_format.split(',');
    }if(data.string_record.bank_id==2){
    	//var filepath = siteurl+'assets/uploads/bank_statement/morgan1.txt';
    	//alert(data);
    	var credit_start_string = 'DEPOSITS AND ADDITIONS';
        var credit_close_string = 'Total Deposits and Additions';
        var debit_start_string  = 'ELECTRONIC WITHDRAWALS';
        var debit_close_string  = 'Total Electronic Withdrawals';
        var checks_start_string = 'CHECKS PAID';
        var checks_close_string = 'Total Checks Paid';
    	var arrayCredit = 'date,description,amount'.split(',');
    	var arrayDebit = 'date,description,amount'.split(',');
    	var arrayCheque = 'date,description,amount'.split(',');
    }
	
	/*for Credit*/
	var countCredit = 0;
	var lengthCredit;
	var crDate = '';
	var crDesc = '';
	var crAmount = '';
	
	$.each(arrayCredit, function (key, val) {
		console.log(key);
		if(val.trim()=='date'){
			crDate = key;
		}
		if(val.trim()=='description'){
			crDesc = key;
		}
		if(val.trim()=='amount'){
			crAmount = key;
		}
		countCredit ++;
	});
	lengthCredit = countCredit;
	
	
	/*for Debit*/
	var countDebit = 0;
	var lengthDebit;
	var drDate = '';
	var drDesc = '';
	var drAmount = '';
	
	$.each(arrayDebit, function (key, val) {
		console.log(key);
		if(val.trim()=='date'){
			drDate = key;
		}
		if(val.trim()=='description'){
			drDesc = key;
		}
		if(val.trim()=='amount'){
			drAmount = key;
		}
		countDebit ++;
	});
	lengthDebit = countDebit;
	
	/*for Cheque*/
	var countCheque = 0;
	var lengthCheque;
	var chqDate = '';
	var chqDesc = '';
	var chqAmount = '';
	
	$.each(arrayCheque, function (key, val) {
		if(val.trim()=='date'){
			chqDate = key;
		}
		if(val.trim()=='description'){
			chqDesc = key;
		}
		if(val.trim()=='amount'){
			chqAmount = key;
		}
		countCheque ++;
	});
	lengthCheque = countCheque;
	
    $.get(filepath, function(data_string) {
        var array = data_string.split("\n");
        console.log(array);
        for(var i=0;i<array.length;i++){
            var array2 =array[i].split(/  +/g);
            if(data.string_record.bank_id==1){
                if(array2[0]==credit_start_string || array2[1]==credit_start_string){
                    credits_start = "Yes";
                }
                if(array2[0]==credit_close_string || array2[1]==credit_close_string){
                    credits_start = "No";
                }
                if(credits_start =="Yes"){
                	if(array2.length>3){
    					if(Date.parse(array2[0]) && !/^\d{0,4}(\.\d{0,2})?$/.test(array2[array2.length-1])){
    						array2[crDate] = array2[0];
    						for(var n=0;n<array2.length-1;n++){
    							
    							if(n!=0 && n!=1){
    								array2[crDesc] += ' '+array2[n];
    							}
    							
    						}
    						array2[crAmount] = array2[array2.length-1];
    					}
    					
    				}
    				
                    if((array2[crDate]=="" || array2[crDate]==undefined) && array2[crDesc]!="" && (array2[crAmount]=="" || array2[crAmount]==undefined)){
                    	res = array2[crDesc].match(/Page [0-9]+ of [0-9]+/g);
    					if(!res){
                            var desc = array2[crDesc].replace("continued on the next page", " ");
                            var length = credits.length;
                            if(length>0){
                                credits[length-1].description += ' '+desc;
                            }
    					}
                    }else{
    					
                        if(array2.length==lengthCredit+1){
                            array2 = array2.filter(e => String(e).trim());
                        }
    					console.log(array2);
    					
                        var timestamp = Date.parse(array2[crDate]);
                        if(isNaN(timestamp) == false &&  (array2[crAmount]!=undefined || array2[crAmount]!="")){
                            credits.push({
                                "date":array2[crDate],
                                "description":array2[crDesc],
                                "amount":array2[crAmount],
                                "type":'cr'
                            })
                        }
                    }
                }
    
                /// end
    
                // Deposits and other debits code script start
    
                if(array2[0]==debit_start_string || array2[1]==debit_start_string){
                    debit_start = "Yes";
                }
                if(array2[0]==debit_close_string || array2[1]==debit_close_string){
                    debit_start = "No";
                }
                if(debit_start =="Yes" && array2[0]!="\r"){
                	if(array2.length>3){
    					if(Date.parse(array2[0]) && !/^\d{0,4}(\.\d{0,2})?$/.test(array2[array2.length-1])){
    						array2[drDate] = array2[0];
    						for(var n=0;n<array2.length-1;n++){
    							
    							if(n!=0 && n!=1){
    								array2[drDesc] += ' '+array2[n];
    							}
    							
    						}
    						array2[drAmount] = array2[array2.length-1];
    					}
    				}
                    if((array2[drDate]=="" || array2[drDate]==undefined) && array2[drDesc]!="" && (array2[drAmount]=="" || array2[drAmount]==undefined)){
                    	res = array2[drDesc].match(/Page [0-9]+ of [0-9]+/g);
    					if(!res){
                            var desc = array2[drDesc].replace("continued on the next page", " ");
                            var length = debits.length;
                            if(length>0){
                                debits[length-1].description += ' '+desc;
                            }
    					}
                    }else{
                    	if(array2.length==2 && !isNaN(Date.parse(array2[0]))){
    						continue;
    					}
                    	array2 = array2.filter(e => String(e).trim());
                        var timestamp = Date.parse(array2[drDate]);
                        if(array2.length>3){
                            var total_length = array2.length;
                            for(var k=1;k<total_length-1;k++){
                                array2[drDesc] +=array2[k];
                            }
                            array2[drAmount] = array2[total_length-1];
                        }
                        if(isNaN(timestamp) == false && (array2[drAmount]!=undefined || array2[drAmount]!="")){
                            debits.push({
                                "date":array2[drDate],
                                "description":array2[drDesc],
                                "amount":array2[drAmount],
                                "type":'dr'
                            })
                        }
                    }
                }
    
                // Deposits and other debits code script stop
    
                ///checks code script start
    
                if(array2[0]==checks_start_string || array2[1]==checks_start_string){
                    check_start = "Yes";
                }
                if(array2[0]==checks_close_string || array2[1]==checks_close_string){
                    check_start = "No";
                }
                if(check_start =="Yes" && array2[0]!="\r"){
                    array2 = array2.filter(e => String(e).trim());
                    if(array2.length==5){
						if(Date.parse(array2[0]) && !/^\d{0,4}(\.\d{0,2})?$/.test(array2[1])){
							array2.splice(1, 0, " ");
						}
						
					}
					if(array2.length==5){
						if(Date.parse(array2[3]) && !/^\d{0,4}(\.\d{0,2})?$/.test(array4[1])){
							array2.splice(4, 0, " ");
						}
					}
                    var date = Date.parse(array2[chqDate]);
                    if(isNaN(date) == false && (array2[chqAmount]!=undefined || array2[chqAmount]!="")){
                        checks.push({
                            "date":array2[chqDate],
                            "description":array2[chqDesc],
                            "amount":array2[chqAmount],
                            "type":'chq'
                        })
                    }
                    if(array2.length >lengthCheque){
                        var date1 = Date.parse(array2[lengthCheque+chqDate]);
                        if(isNaN(date1) == false && (array2[lengthCheque+chqAmount]!=undefined || array2[lengthCheque+chqAmount]!="")){
                            checks.push({
                                "date":array2[lengthCheque+chqDate],
                                "description":array2[lengthCheque+chqDesc],
                                "amount":array2[lengthCheque+chqAmount],
                                "type":array2['chq']
                            })
                        }
                    }
                    /*if(array2.length >6){
                        var date2 = Date.parse(array2[6]);
                        if(isNaN(date2) == false && (array2[8]!=undefined || array2[8]!="")){
                            checks.push({
                                "date":array2[6],
                                "description":array2[7],
                                "amount":array2[8]
                            })
                        }
                    }*/
                }
            }
            if(data.string_record.bank_id==2){
            	if(array2[0]!=undefined && array2[0].trim()==credit_start_string.trim() || array2[1]!=undefined && array2[1].trim()==credit_start_string.trim()){
                    credits_start = "Yes";
                }
    			
                if(array2[0]!=undefined && array2[0].trim()==credit_close_string.trim() || array2[1]!=undefined && array2[1].trim()==credit_close_string.trim()){
                    credits_start = "No";
                }
    			if(credits_start =="Yes"){
    				if((array2[crDate]=="" || array2[crDate]==undefined) && array2[crDesc]!="" && (array2[crAmount]=="" || array2[crAmount]==undefined)){
    					
                        var desc = array2[crDesc];
                        var length = credits.length;
                        if(length>0){
                            credits[length-1].description += ' '+desc;
                        }
                    }else{
    					if(array2[0]=="" && Date.parse(array2[1])){
    						console.log(array2.shift());	
    					}
    					if(array2.length==3){
    						array2[crDate] = array2[0];
    						array2[crDesc] = array2[1];
    						array2[crAmount] = array2[2];
    					}
    					
    					if(array2.length==4 && Date.parse(array2[0]) && array2[3]!=undefined){
    						array2[crDate] = array2[0];
    						array2[crDesc] = array2[1].concat(' '+array2[2]);
    						array2[crAmount] = array2[3];
    					}
    					var timestamp = Date.parse(array2[crDate]);
    					if(isNaN(timestamp) == false &&  (array2[crAmount]!=undefined || array2[crAmount]!="")){
    						credits.push({
    							"date":array2[crDate],
    							"description":array2[crDesc],
    							"amount":array2[crAmount]
    						})
    					}
    				}

    			}						
    		
    		
    			
    			if(array2[0]!=undefined && array2[0].trim()==debit_start_string.trim() || array2[1]!=undefined && array2[1].trim()==debit_start_string.trim()){
    				debit_start = "Yes";
    			}
    			if(array2[0]!=undefined && array2[0].trim()==debit_close_string.trim() || array2[1]!=undefined && array2[1].trim()==debit_close_string.trim()){
    				
    				debit_start = "No";
    			}
    			
    			if(debit_start =="Yes" && array2[0]!="\r"){
    				if((array2[drDate]=="" || array2[drDate]==undefined) && array2[drDesc]!="" && (array2[drAmount]=="" || array2[drAmount]==undefined)){
                        var desc = array2[drDesc];
                        var length = debits.length;
                        if(length>0){
                            debits[length-1].description += ' '+desc;
                        }
                    }else{
    				
    					if(array2[0]=="" && Date.parse(array2[1])){
    						array2.shift();	
    					}
    					if(array2.length==3){
    						array2[drDate] = array2[0];
    						array2[drDesc] = array2[1];
    						array2[drAmount] = array2[2];
    					}
    					
    					if(array2.length==4 && Date.parse(array2[0]) && array2[3]!=undefined){
    						array2[drDate] = array2[0];
    						array2[drDesc] = array2[1].concat(' '+array2[2]);
    						array2[drAmount] = array2[3];
    					}
    					var timestamp = Date.parse(array2[drDate]);
    					if(isNaN(timestamp) == false &&  (array2[drAmount]!=undefined || array2[drAmount]!="")){
    						debits.push({
    							"date":array2[drDate],
    							"description":array2[drDesc],
    							"amount":array2[drAmount]
    						})
    					}
    				}
    			}
    			
    			if(array2[0]!=undefined && array2[0].trim()==checks_start_string.trim() || array2[1]!=undefined && array2[1].trim()==checks_start_string.trim()){
    				//alert("Yes");
    				check_start = "Yes";
    			}
    			if(array2[0]!=undefined && array2[0].trim()==checks_close_string.trim() || array2[1]!=undefined && array2[1].trim()==checks_close_string.trim()){
    				//alert("No");
    				check_start = "No";
    			}
    			
    			if(check_start =="Yes"){
    				
    				if(array2[0]=="" && array2[1] % 1 === 0){
    					array2.shift();
    					if(array2[array2.length-1]==""){
    						array2.pop();
    					}
    				}
    				
    				if(array2.length==5){
    					if(array2[array2.length-1] % 1 === 0){
    						array2.pop(-1);
    					}
    					
    				}
    				console.log(array2);
    				var date = Date.parse(array2[2]);
                    if(isNaN(date) == false && (array2[3]!=undefined || array2[3]!="")){
                        checks.push({
                            "date":array2[2],
                            "description":array2[1].concat(' '+array2[0]),
                            "amount":array2[3],
                            "type":'chq'
                        })
                    }
    			}

            }

            // checks code script stop
        }
      console.log(credits);
      console.log(debits);
      console.log(checks);

        var data_json = {
            "account_number": data.extractData.account_number_string,
            "credits": credits,
            "debits": debits,
            "checks": checks,
            "name":name,
            "se10":data.extractData.se10,
            "contract_nbr":data.extractData.contract_nbr,
            "amort_date":data.extractData.amort_date,
            "instant_decision_date":data.extractData.instant_decision_date,
            "account_holder_name":data.extractData.account_holder_name,
            "account_type":data.extractData.account_type,
            "account_ownership":data.extractData.account_ownership,
            "name_of_bank":data.extractData.name_of_bank,
            "bank_address":data.extractData.bank_address,
            "bank_city":data.extractData.bank_city,
            "bank_state":data.extractData.bank_state,
            "bank_zip":data.extractData.bank_zip,
            "current_balance":data.extractData.current_balance,
            "start_date":data.extractData.start_date,
            "end_date":data.extractData.end_date,
            "open_balance":data.extractData.open_balance,
            "closing_balance":data.extractData.closing_balance,
            "total_deposits":data.extractData.total_deposits,
            "count_deposits":data.extractData.count_deposits,
            "total_withdrawals":data.extractData.total_withdrawals,
            "count_withdrawals":data.extractData.count_withdrawals,
            "total_count_check_return":data.extractData.total_count_check_return,
            "total_count_inward_check_return":data.extractData.total_count_inward_check_return,
            "total_inward_check_return":data.extractData.total_inward_check_return,
            "total_count_outward_check_return":data.extractData.total_count_outward_check_return,
            "total_outward_check_return":data.extractData.total_outward_check_return,
            "count_ecs_or_emi":data.extractData.count_ecs_or_emi,
            "amount_ecs_or_emi":data.extractData.amount_ecs_or_emi,
            "route":data.extractData.route,
            "transaction_all_level_spreading_done":data.extractData.transaction_all_level_spreading_done,
            "native_vs_non_native":data.extractData.native_vs_non_native,
            "check_sum":data.extractData.check_sum,
            "summary_and_transaction_match":data.extractData.summary_and_transaction_match,
            "pages":data.extractData.pages,
            "begining_balance": data.extractData.begining_balance,
        };

        var surl = siteurl+'Bank_statement/createXLSBankStatement';  
        $.post(surl,data_json, function(response) {
			var url = siteurl+'assets/uploads/bank_statement_excel/'+response.filename;
            $('#dwnld_excel').show();
			$('#dwnld_excel').attr('href',url);
			$('#exampleFormControlFile1').val(null);
			$('#bank_id option:eq(0)').attr('selected', 'selected');
			document.getElementById("convert_form").reset();
        }, 'json');
    });
    return false;
  }
</script>

<script>
function hidedownload(){
	$('#dwnld_excel').hide();
}
</script>



