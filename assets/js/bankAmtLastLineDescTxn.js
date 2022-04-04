function findSearchType(param){
    console.log(param);
	if(param.includes("[pre]")){
		var param = param.substring(
				param.lastIndexOf("[pre]") + 5, 
				param.lastIndexOf("[/pre]")
    		);
		//open_bal_pre = true;
		return param;
	}
}

var q= 0;
function bankAmtLastLineDescTxn(data) {
	console.log(data);
	var multiple_account = false;
	if(data.multiple_account!=undefined && data.multiple_account){
		multiple_account = true;
	}
	if(data.newFolderName!=undefined && data.newFolderName){
	var newFolderName = data.newFolderName;
	}else{
		var newFolderName = "";
	}

	if(data.accType!=undefined && data.accType){
		var accType = data.accType;
	}else{
		var accType = "";
	}

		
	var user_type = data.user_type;
	var account_holder_name = '';
	var begining_bal = "No";
  	var chkBgnBal = false;
  	var account_num = "No";
  	var chkAcctNum = false;
  	var closing_bal="Yes";
  	var chkclsBal = false;
  	var txn_type = "";
  	var pos_withdrawals = 0;
  	var pos_deposits = 0;
  	var pos_end_daily_bal = 0;
  	
	//var filepath = siteurl+'assets/uploads/bank_statement/citibank_debit_credit.txt';
	var filepath = siteurl+'assets/uploads/bank_statement/'+data.textFileName;
	
	var transactions = [];
    var txn_start = "No";
    var begining_bal = "No";
    var txn_start_string = data.bank_data_val.credit_start_string;
    var txn_close_string = data.bank_data_val.credit_end_string;
    var txn_format = data.bank_data_val.credit_table_format.split(',');
    var getBeginingBalance;
    var actualBalance='';
    var ending_balance;
	
	var account_number = '';
	if(data.extractData.account_number_string==''){
		//account_number_str = findSearchType(data.bank_data_val.account_number_string);
	}else if(data.extractData.account_number_string!=''){
		account_num = "Yes";
		account_number = data.extractData.account_number_string;
	}

	var begining_balance = '';
	if(data.extractData.begining_balance==''){
		//begining_balance_str = findSearchType(data.bank_data_val.begining_balance);
	}else if(data.extractData.begining_balance!=''){
		begining_bal = "Yes";
		begining_balance = data.extractData.begining_balance;
		getBeginingBalance = begining_balance;
		ending_balance = begining_balance;
	}

	var closing_balance = '';
	if(data.extractData.closing_balance==''){
		//closing_balance_str = findSearchType(data.bank_data_val.closing_balance);
	}else if(data.extractData.closing_balance!=''){
		closing_bal = "Yes"; 
		closing_balance = data.extractData.closing_balance;
	}

	var pages = '';
	if(data.extractData.pages==''){
		//pages_str = findSearchType(data.bank_data_val.pages);
	}else if(data.extractData.pages!=''){
		pages = data.extractData.pages;
	}
	
	var start_date = '';
	var end_date = '';
	start_date = data.extractData.start_date;
	end_date = data.extractData.end_date;
	
	var data_ignore_string = data.bank_data_val.ignore_string;
	if(data_ignore_string){
		var ignoreArray = data_ignore_string.split("|");
	}
    
	if(data.newFolderName!=undefined && data.newFolderName){
		var newFolderName = data.newFolderName;
	}else{
		var newFolderName = "";
	}
	
	if(data.accType!=undefined && data.accType){
		var accType = data.accType;
	}else{
		var accType = "";
	}
	
	if(data.check_all_pdf_process!=undefined && data.check_all_pdf_process){
		var check_all_pdf_process = data.newFolderName;
	}else{
		var check_all_pdf_process = false;
	}
	
    var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	var dateRegex = new RegExp("^[0-9\-/.]+$");
	var monthArray = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Sept", "Oct", "Nov", "Dec"];
    
    
    var txn_open_balance = data.bank_data_val.open_balance;
    
    var txnDate = '';
	var txnDesc = '';
	var txnCredit = '';
	var txnDebit = '';
	var txnBalance = '';
	
	$.each(txn_format, function (key, val) {
		if(val.trim()=='date'){
			txnDate = key;
		}
		if(val.trim()=='description'){
			txnDesc = key;
		}
		if(val.trim()=='credit'){
			txnCredit = key;
		}
		if(val.trim()=='debit'){
			txnDebit = key;
		}
		if(val.trim()=='balance'){
			txnBalance = key;
		}
	});
	
	var stopTxn = 0;
	$.get(filepath, function(data_string) {
		
		var array = data_string.split("\n");
		
    	for(var i=0;i<array.length;i++){
        	
            var bankData =array[i].split(/  +/g);
            bankData = bankData.filter(Boolean);
            
            if(txn_start=="No" && stopTxn==0){
    			for(k=0;k<bankData.length;k++){
    				if(bankData[k].trim()==txn_start_string.trim()){
    					//console.log("Transactions start from line number "+i);
    					txn_start = "Yes";
    				}
    			}
			}

			if(txn_start=="Yes"){
				let close_string_array = txn_close_string.trim().split('|');
				//console.log("Nirdesh");
				//console.log(close_string_array);
    			for(k=0;k<bankData.length;k++){
    				//if(bankData[k].trim()==txn_close_string.trim()){
    				if(jQuery.inArray(bankData[k].trim(), close_string_array) !== -1){
    					//console.log("Transactions end from line number "+i);
    					txn_start = "No";
    					stopTxn = 1;
    				}
    			}
			}
			
			if(txn_start=="Yes"){
				var z = 0;
				var first_amt = "";
				var second_amt = "";
				var bankData =array[i].split(/ +/g);
				bankData = bankData.filter(Boolean);
				/*if(data.bank_data_val.bank_id==61){
					console.log('bankData');
					console.log(bankData);
				}*/
				//console.log("Bankd",bankData);
				if(bankData.length>=3){
					
					var filterData = [];
					filterData[1] = '';
					filterData[0] = '';
					//filterData[txnAmt] = '';
					
					//Citibanamex 
					if(data.bank_data_val.bank_id==178){
						bankData = array[i].split(/ /g);
						bankData = bankData.filter(Boolean);	
						
						for(k=0;k<bankData.length;k++){										
							if(bankData[k]!=undefined && monthArray.includes(bankData[k].trim().charAt(0).toUpperCase() + bankData[k].trim().slice(1).toLowerCase())){
								var result = formatStringDate(bankData[k]+' '+bankData[k-1]);
				    			if(result){
				    				bankData[k] = result;
				    				bankData[k-1] = "";	
				    				break;			
				    			}
							}
						}
						bankData = bankData.filter(Boolean);			
					}
					
					
					
					if(bankData[0]!=undefined && bankData[0].trim().length<11 && dateRegex.test(bankData[0]) && (bankData[0].trim().indexOf('/') != -1 || bankData[0].trim().indexOf('-') != -1)){
						filterData[0] = bankData[0].trim();

						if (bankData[bankData.length-1]!=undefined) {
							var filterTxnBalance = bankData[bankData.length-1].trim().replace(/[$,\-()]+/g,'');
							// .replace('$', '').replace(',', '').replace('-', '').replace('(', '').replace(')', '');
							var ext = false;
							while(ext===false){
								if(i>=array.length-1){
									var ext = true;
									break;
									
								}
								
								filterData[1] = filterData[1] + " "+ bankData.splice(0,bankData.length).join(" ")
								i += 1;	
								if(array[i]!=undefined){
									var bankData =array[i].split(/  +/g);
									bankData = bankData.filter(Boolean);
									bankData = bankData.map(function (el) {
										  return el.trim();
									});
									//console.log(bankData,"INNER");
									/*if(bankData[bankData.length-1]!=undefined && bankData[bankData.length-1]!=""){
										if(bankData[bankData.length-1].trim().indexOf('  ') != -1){
											var tmpArr = bankData[bankData.length-1].split('  ');
											if(tmpArr.length==2){
												var tmp_1 = tmpArr[0].trim().replace(/[$,\-()]+/g,'');
												var tmp_2 = tmpArr[1].trim().replace(/[$,\-()]+/g,'');
												if(tmp_1!=undefined && tmp_1!="" && floatRegex.test(tmp_1) && tmp_1.trim().indexOf('.') != -1 && tmp_2!=undefined && tmp_2!="" && floatRegex.test(tmp_2) && tmp_2.trim().indexOf('.') != -1){
													bankData.splice(bankData.length-1, 1);
													bankData.push(tmpArr[0], tmpArr[1]);
													console.log(bankData,"Second");
												}
											}
										}
										
									}*/
									//if (bankData[bankData.length-1]!=undefined) {
									console.log(array[i].substring(formatArray[0]-2, formatArray[1]));
									var filterDebitTxnBalance = array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,-]+/g,'');
									//var filterTxnBalance = bankData[bankData.length-1].trim().replace(/[$,\-()]+/g,'');
									// .replace('$', '').replace(/,/g, '').replace('-', '').replace('(', '').replace(')', '');
									if(floatRegex.test(filterDebitTxnBalance) && filterDebitTxnBalance.indexOf('.') != -1){
										var ext = true;
										filterTxnBalance = filterDebitTxnBalance;
									}
									
									var filterCreditTxnBalance = array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,-]+/g,'');
									if(floatRegex.test(filterCreditTxnBalance) && filterCreditTxnBalance.indexOf('.') != -1){
										var ext = true;
										filterTxnBalance = filterCreditTxnBalance;
									}
										
									//}
	
									let close_string_array = txn_close_string.trim().split('|');
									for(k=0;k<bankData.length;k++){
					    				//if(bankData[k].trim()==txn_close_string.trim()){
										if(jQuery.inArray(bankData[k].trim(), close_string_array) !== -1){
					    					i = i-1;
					    					var ext = true;
					    					break;
					    				}
					    			}
									var initDr = false;
									if(array[i].indexOf('DEPOSITOS') != -1){
										pos_deposits = array[i].indexOf('DEPOSITOS');
										initDr = true; 
									}
	
									if(array[i].indexOf('RETIROS') != -1 && initDr==true){
										pos_withdrawals = array[i].indexOf('RETIROS');
									}
									
									if(array[i].indexOf('SALDO') != -1 && initDr==true){
										pos_end_daily_bal = array[i].indexOf('SALDO');
										initDr = false;
									}
									
									var formatArray = [pos_deposits,pos_withdrawals,pos_end_daily_bal];
									var ty = ["dr","cr"];
									if (txnCredit > txnDebit) {
										formatArray[0] = pos_withdrawals;
										formatArray[1] = pos_deposits;
										
										ty[0] = "cr";
										ty[1] = "dr";
										formatArray[2] = pos_end_daily_bal;

									}
								}
							}
							filterData[1] = filterData[1].replace(filterData[0],"");

							filterData[1] = filterData[1].replace(/\s+/g,' ').trim();

							bankData.splice(0,0,"");

							if(filterData[1].trim()!=undefined && filterData[1].trim()!=''){
								$.each(ignoreArray, function (index, value) {
									if(value.indexOf("[rgx]")!=-1){
										sliceValue = value.slice(6, -7);
										var regex = new RegExp(sliceValue);
										var str=filterData[1].trim();
										if(regex.test(str)){
											filterData[1] = filterData[1].replace(sliceValue,"");
										}
									}
								});					
							}
						}
					}
					
					if(bankData[bankData.length-3]){
						var filterTxnBalance = bankData[bankData.length-3].trim().replace(/[$,\-()]+/g,'');
						// .replace('$', '').replace(',', '').replace('-', '').replace('(', '').replace(')', '');
						if((intRegex.test(filterTxnBalance) || floatRegex.test(filterTxnBalance)) && filterTxnBalance.length>0 && filterTxnBalance.indexOf('.') != -1){
							second_amt = parseFloat(bankData[bankData.length-3].trim().replace(/[$,\-()]+/g,''));
								// .replace('$', '').replace(',', '').replace('(', '').replace(')', ''));
							z = z+1;
						}
					}
					
					if(bankData[bankData.length-2]){
						var filterTxnAmt = bankData[bankData.length-2].trim().replace(/[$,\-()]+/g,'');
						// .replace('$', '').replace(',', '').replace('-', '');
						if((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
							first_amt = parseFloat(bankData[bankData.length-2].replace('$', '').replace(',', ''));
							z = z+1;
						}
					}
					
					if(bankData[bankData.length-1]){
						var filterTxnBalance = bankData[bankData.length-1].trim().replace(/[$,\-()]+/g,'')
						// .replace('$', '').replace(',', '').replace('-', '').replace('(', '').replace(')', '');
						if((intRegex.test(filterTxnBalance) || floatRegex.test(filterTxnBalance)) && filterTxnBalance.length>0 && filterTxnBalance.indexOf('.') != -1){
							second_amt = parseFloat(bankData[bankData.length-1].trim().replace(/[$,\-()]+/g,''))
								// .replace('$', '').replace(',', '').replace('(', '').replace(')', ''));
							z = z+1;
						}
					}
					
					
					
					dataLength = bankData.length-1;
					if(z==2){
						dataLength = bankData.length-2;
					}else if(z==3){
						dataLength = bankData.length-3;
					}
					
					var start_k = 1;
					if(data.bank_data_val.bank_id==156){
						var start_k = 2;
					}
					
					for(k=start_k;k<dataLength;k++){
						filterData[1] += 	' '+bankData[k];				
					}
					
					
					//CITIBanamex
					var initDr = false;
					if(array[i].indexOf('DEPOSITOS') != -1){
					    //console.log(console.log(array[i]));
					    //console.log(array[i].indexOf('DEPOSITOS'));
						pos_deposits = array[i].indexOf('DEPOSITOS');
						initDr = true; 
					}
					if(array[i].indexOf('RETIROS') != -1 && initDr==true){
						//console.log(console.log(array[i]));
					    //console.log(array[i].indexOf('RETIROS'));
					    pos_withdrawals = array[i].indexOf('RETIROS');
					}
					if(array[i].indexOf('SALDO') != -1 && initDr==true){
					    //console.log(console.log(array[i]));
					    //console.log(array[i].indexOf('SALDO'));
						pos_end_daily_bal = array[i].indexOf('SALDO');
						initDr = false;
					}
					
					/*if(z==2){
						console.log(array[i]);
						//console.log(second_amt);
						if((parseFloat(ending_balance)+parseFloat(first_amt)).toFixed(2)==second_amt){
							transactions.push({
					            "date":filterData[0],
					            "description":filterData[1],
					            "amount":first_amt,
					            "type":'cr'
					        })
					        ending_balance = (parseFloat(ending_balance)+parseFloat(first_amt)).toFixed(2);
						}else if((parseFloat(ending_balance)-parseFloat(first_amt)).toFixed(2)==second_amt){
							transactions.push({
					            "date":filterData[0],
					            "description":filterData[1],
					            "amount":first_amt,
					            "type":'dr'
					        })
					        ending_balance = (parseFloat(ending_balance)-parseFloat(first_amt)).toFixed(2);
						}
					}else{*/
						
						
						//console.log(array[i]);
					//console.log(filterData);
					var cheque_no = '';
					if(intRegex.test(bankData[1])){
						cheque_no = bankData[1];
					}
					var formatArray = [pos_deposits,pos_withdrawals,pos_end_daily_bal];
					var ty = ["dr","cr"];
					if (txnCredit > txnDebit) {
						formatArray[0] = pos_withdrawals;
						formatArray[1] = pos_deposits;
						
						ty[0] = "cr";
						ty[1] = "dr";

					}
					
					
					//console.log("Nirdesh");
					console.log(formatArray);
					console.log(filterData);
					if(filterData[0]!='' && filterData[0].length<11 && dateRegex.test(filterData[0]) && (filterData[0].indexOf('/') != -1 || filterData[0].indexOf('-') != -1)){
						var filterTxnAmt = array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,-]+/g,'');//.replace('$', '').replace(',', '').replace('-', '');
						if (filterTxnAmt.indexOf(' ') !== -1) {
							filterTxnAmt = filterTxnAmt.split(' ')[0];
						}
						if((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
							if(array[i].substring(formatArray[1], formatArray[2]).indexOf('-') != -1){
								filterData[2] = -Math.abs(parseFloat(array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,]+/g,'')));
							}else{
								filterData[2] = parseFloat(array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,]+/g,''));
							}
							
							transactions.push({
					            "date":filterData[0],
					            "description":filterData[1],
					            "amount":filterData[2],
					            "cheque_no":cheque_no,
					            "type":ty[0]
					        })
							
							
					        ending_balance = (parseFloat(ending_balance)-parseFloat(filterData[2])).toFixed(2);
							
						}
						
						var filterTxnAmt = array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,-]+/g,'');//replace('$', '').replace(',', '').replace('-', '');
						if((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
							if(array[i].substring(formatArray[0]-2, formatArray[1]).indexOf('-') != -1){
								filterData[2] = -Math.abs(parseFloat(array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,]+/g,'')));
							}else{
								filterData[2] = parseFloat(array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,]+/g,''));
							}
							
							transactions.push({
					            "date":filterData[0],
					            "description":filterData[1],
					            "amount":filterData[2],
					            "cheque_no":cheque_no,
					            "type":ty[1]
					        })
							
					        ending_balance = (parseFloat(ending_balance)+parseFloat(filterData[2])).toFixed(2);
					        
						}
					
					}else if(transactions.length>0){
						//console.log("WWWWWWW");
						//console.log(bankData);
						var checkLastAmt =array[i].split(/                           +/g);
						//console.log(checkLastAmt);
						if(checkLastAmt.length==2){
							//console.log(checkLastAmt);
							bankData.pop();
						}
						//var bankData =array[i].split(/  +/g);
						//console.log(bankData);
						var addDesc = true;
						var extraDesc = "";
						for(k=0;k<bankData.length;k++){
							if(bankData[k].trim()!=undefined && bankData[k].trim()!=''){
								$.each(ignoreArray, function (index, value) {
									if(value.indexOf("[rgx]")!=-1){
										sliceValue = value.slice(6, -7);
										var regex = new RegExp(sliceValue);
										var str=bankData[k].trim();
										if(regex.test(str)){
											//console.log(str);
											addDesc = false;	
										}
									}
								});
								if(addDesc){
									extraDesc +=bankData[k]+" ";
								}
							}
							
						}
						if(extraDesc!=""){
    						transactions[transactions.length-1].description += ' '+extraDesc;
	    				}
					}
						
					//}

				}else if(transactions.length>0){
					var bankData =array[i].split(/  +/g);
					var addDesc = true;
					for(k=0;k<bankData.length;k++){
						if(bankData[k].trim()!=undefined && bankData[k].trim()!=''){
							$.each(ignoreArray, function (index, value) {
								if(value.indexOf("[rgx]")!=-1){
									sliceValue = value.slice(6, -7);
									var regex = new RegExp(sliceValue);
									var str=bankData[k].trim();
									if(regex.test(str)){
										//console.log(str);
										addDesc = false;	
									}
								}
							});
							if(addDesc){
								transactions[transactions.length-1].description += ' '+bankData[k];
							}
						}
					}
				}				
			}  
    	}
    	console.log(transactions);
    	
    	var data_json = {
    			"bank_id":data.bank_data_val.bank_id,
    			"upload_pdf_file":data.extractData.upload_pdf_file,
    			"original_pdf_file_name":data.extractData.original_pdf_file_name,
				"page_array":data.extractData.page_array,
				"split_page_num_array":data.extractData.split_page_num_array,
				"isCompleteMultiAcc":data.extractData.isCompleteMultiAcc,
                "account_number": account_number,
                "transactions": transactions,
                "name":name,
                "name_of_bank":data.extractData.name_of_bank,
                "bank_address":data.extractData.bank_address,
                "bank_city":data.extractData.bank_city,
                "bank_state":data.extractData.bank_state,
                "bank_zip":data.extractData.bank_zip,
                "current_balance":data.extractData.current_balance,
                "start_date":start_date,
                "end_date":end_date,
                "open_balance":begining_balance,
                "begining_balance":begining_balance,
                "closing_balance":closing_balance,
                "type":2,
                "pages":pages,
                "account_holder_name":data.extractData.account_holder_name,
                "account_type":data.extractData.account_type,
                "currency":data.bank_data_val.currency,
                "newFolderName":newFolderName,
                "zipFileName":data.zipFileName,
                "history_id":data.history_id,
				"uploadedXlsFileName":data.uploadedXlsFileName,
				"multiple_account":multiple_account,
                "accType":accType,
                "check_all_pdf_process":check_all_pdf_process,
                "multiple_account":multiple_account,
            };

            //var surl = siteurl+'Bank_statement/createXLSBankStatement';
            console.log("user_type==1   ,",user_type)
            if(user_type==1){
				var surl = siteurl+'Bank_statement/createXLSBankStatement';
			}else{
				var surl = siteurl+'Bank_statement/createXLSBankStatementNewFormat';
			}
            $.post(surl,data_json, function(response) {
    			var url = siteurl+'assets/uploads/bank_statement_excel/'+response.filename;
    			$('.downloadBox').show();
    			$('.downloadBox .upload_template_file_name').text(response.filename);
                $('.downloadBox .check_sum').text(response.check_sum);
                $('.downloadBox .total_deposits').text(response.total_deposits);
                $('.downloadBox .count_deposits').text(response.count_deposits);
                $('.downloadBox .total_withdrawals').text(response.total_withdrawals);
                $('.downloadBox .count_withdrawals').text(response.count_withdrawals);
                $('.downloadBox').children().show();
    			$('#dwnld_excel').attr('href',url);
    			$('#exampleFormControlFile1').val(null);
    			$('#bank_id option:eq(0)').attr('selected', 'selected');
    			document.getElementById("convert_form").reset();
				q++;
    			console.log("Second");
    			console.log(q);
    			if(data.txtSplitFileName!= undefined && data.txtSplitFileName){
    				var txtSplitFileName =  data.txtSplitFileName;
        			if(txtSplitFileName.length>1 && txtSplitFileName.length>=q+1){
    					if(txtSplitFileName[q]!=undefined){
    						var data_json = {
            						"bank_id":data.bank_id,
            		                "txtFilename":txtSplitFileName[q],
            		                "txtSplitFileName":txtSplitFileName,
            		                "uploadedXlsFileName":response.filename,
            		                "page_array":response.page_array,
            		                "newFolderName":response.newFolderName,
            		                "checkAllPdfProcess":response.checkAllPdfProcess,
            		                "history_id":response.history_id,
            		                "multiple_account":true,
            				};
            				
            				$.post(data.eurl,data_json, function(response) {
            					console.log('callFromView');
            					console.log("Third");
            	                //console.log(response);
            					createXLSBankStatement(response);
            					return true;
            				}, 'json');
    					}
    				}else{
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
