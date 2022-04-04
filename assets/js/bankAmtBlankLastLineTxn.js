var q= 0;
var textFiles = [];
function bankAmtBlankLastLineTxn(data) {
	console.log(data,"BankAmtBlankLastLine");
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
    
    var multiple_account = false;
	if(data.multiple_account!=undefined && data.multiple_account){
		multiple_account = true;
	}
	
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
    
	if(data.bank_data_val.bank_id==187){
		var citibanmexYear = '';
		if(end_date.indexOf('-') != -1){
			var citibanmexDate = end_date.split("-");
			var citibanmexYear = citibanmexDate[2];
		}
		
		if(end_date.indexOf('/') != -1){
			var citibanmexDate = end_date.split("/");
			var citibanmexYear = citibanmexDate[2];
		}
		
		if(start_date.indexOf('-') != -1){
			var banmexStartDate = start_date.split("-");
			var banmexStartYear = banmexStartDate[2];
		}
		
		if(start_date.indexOf('/') != -1){
			var banmexStartDate = start_date.split("/");
			var banmexStartYear = banmexStartDate[2];
		}
		var citibanmexStartYear = '';
		if(parseInt(banmexStartYear)<parseInt(citibanmexYear)){
			var citibanmexStartYear = parseInt(banmexStartYear);
		}
	}
    //alert(citibanmexYear);
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
	var cititxnMonth = "";
	var checkMonth = 0;
	$.get(filepath, function(data_string) {
		
		var array = data_string.split("\n");
		
    	for(var i=0;i<array.length;i++){
        	
            var bankData =array[i].split(/  +/g);
            bankData = bankData.filter(Boolean);
            
            if(txn_start=="No" && stopTxn==0){
    			for(k=0;k<bankData.length;k++){
    				if(bankData[k].trim().toLowerCase()==txn_start_string.trim().toLowerCase()){
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
				//console.log(bankData);
				var initDr = false;
				if(array[i].indexOf('RETIROS') != -1){
					//console.log(console.log(array[i]));
					//console.log('RETIROS');
				    //console.log(array[i].indexOf('RETIROS'));
					initDr = true; 
				    pos_withdrawals = array[i].indexOf('RETIROS');
				}
				
				if(array[i].indexOf('DEPÓSITOS') != -1 && initDr==true){
				    //console.log(console.log(array[i]));
				    //console.log(array[i].indexOf('DEPÓSITOS'));
					pos_deposits = array[i].indexOf('DEPÓSITOS');
				}
				
				if(array[i].indexOf('DEPÃ“SITOS') != -1 && initDr==true){
				    //console.log(console.log(array[i]));
				    //console.log(array[i].indexOf('DEPÓSITOS'));
					pos_deposits = array[i].indexOf('DEPÃ“SITOS');
				}
				
				if(array[i].indexOf('SALDO') != -1 && initDr==true){
				    //console.log(console.log(array[i]));
				    //console.log(array[i].indexOf('SALDO'));
					pos_end_daily_bal = array[i].indexOf('SALDO');
					formatArray[2] = pos_end_daily_bal;
					initDr = false;
				}
				
				
				var cheque_no = '';
				var formatArray = [pos_deposits,pos_withdrawals,pos_end_daily_bal];
				var ty = ["dr","cr"];
				if (txnCredit > txnDebit) {
					formatArray[0] = pos_withdrawals-5;
					formatArray[1] = pos_deposits;
					ty[0] = "cr";
					ty[1] = "dr";
					formatArray[2] = formatArray[2]-5;

				}
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
					//console.log(formatArray);
					var filterData = [];
					filterData[1] = '';
					filterData[0] = '';
					//filterData[txnAmt] = '';
					
					//Citibanamex 
					if(data.bank_data_val.bank_id==187){
						bankData = array[i].split(/  /g);
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
						if(citibanmexStartYear!=""){
							if(bankData[0].indexOf('/') != -1){
								var citibmxDate = bankData[0].split("/");
								cititxnMonth = citibmxDate[1];
								
								if(parseInt(checkMonth)==0){
									checkMonth = cititxnMonth;
								}
									
								if(parseInt(checkMonth)==parseInt(cititxnMonth)){
									banmexYear = citibanmexStartYear;
								}else{
									banmexYear = citibanmexYear;
								}
								
							}
						}else{
							banmexYear = citibanmexYear;
						}
						
						var txnDate = bankData[0].trim()+'/'+banmexYear;
						if(txnDate.indexOf('/') != -1){
							var bmxDate = txnDate.split("/");
							txnDate = bmxDate[1]+'/'+bmxDate[0]+'/'+bmxDate[2];
						}
						//console.log(bankData);
						if (bankData[bankData.length-1]!=undefined) {
							
							
							filterTxnBalance = "";
							var filterTxnBalance = bankData[bankData.length-1].trim().replace(/[$,\-()]+/g,'');
							// .replace('$', '').replace(',', '').replace('-', '').replace('(', '').replace(')', '');
							//console.log(bankData,'FirstData');
							if(!floatRegex.test(filterTxnBalance) || filterTxnBalance.indexOf('.') == -1){
								//console.log(bankData,'FirstData');
								//console.log(bankData)
								var txnDesc ='';
								for(k=1;k<bankData.length;k++){
									txnDesc = txnDesc+bankData[k];
								}
								var ext = false;
								var crFound = false;
								var drFound = false;
								while(ext===false){
									var foundAmt = false;
									
									i++;	
									if(i==array.length){
										break;
									}
									if(array[i]!=undefined){
										//console.log(i,'while_i_______');
										//console.log(array[i],'qqq');
										var secbankData =array[i].split(/  +/g);
										
										
										if(secbankData[0]!=undefined && secbankData[0].trim().length<11 && dateRegex.test(secbankData[0]) && (secbankData[0].trim().indexOf('/') != -1 || secbankData[0].trim().indexOf('-') != -1)){
											i--;
											//ext = true;
											break;
											
										}
										let close_string_array = txn_close_string.trim().split('|');
										for(k=0;k<secbankData.length;k++){
											if(jQuery.inArray(secbankData[k].trim(), close_string_array) !== -1){
												stopTxn = 1;
												txn_start = "No";
												var ext = true;
						    					break;
						    				}
						    			}
										
										if(array[i].indexOf('RETIROS') != -1){
											//console.log(console.log(array[i]));
											//console.log('RETIROS');
										    //console.log(array[i].indexOf('RETIROS'));
											initDr = true; 
										    pos_withdrawals = array[i].indexOf('RETIROS');
										}
										
										if(array[i].indexOf('DEPÓSITOS') != -1 && initDr==true){
										    //console.log(console.log(array[i]));
										    //console.log(array[i].indexOf('DEPÓSITOS'));
											pos_deposits = array[i].indexOf('DEPÓSITOS');
										}
										
										if(array[i].indexOf('DEPÃ“SITOS') != -1 && initDr==true){
										    //console.log(console.log(array[i]));
										    //console.log(array[i].indexOf('DEPÓSITOS'));
											pos_deposits = array[i].indexOf('DEPÃ“SITOS');
										}
										
										if(array[i].indexOf('SALDO') != -1 && initDr==true){
										    //console.log(console.log(array[i]));
										    //console.log(array[i].indexOf('SALDO'));
											pos_end_daily_bal = array[i].indexOf('SALDO');
											formatArray[2] = pos_end_daily_bal;
											initDr = false;
										}
										var formatArray = [pos_deposits,pos_withdrawals,pos_end_daily_bal];
										var ty = ["dr","cr"];
										if (txnCredit > txnDebit) {
											formatArray[0] = pos_withdrawals-5;
											formatArray[1] = pos_deposits;
											ty[0] = "cr";
											ty[1] = "dr";
											formatArray[2] = formatArray[2]-5;
	
										}
										
										if(secbankData[0]=='Detalle de Operaciones'){
											continue;
										}
										
										if(secbankData[0]=='FECHA' && secbankData[1]=='CONCEPTO'){
											continue;
										}
										
										if(drFound && txn_start == "Yes"){
											//console.log(array[i].substring(0, formatArray[0]-2).trim(),"drFound");
											transactions[transactions.length-1].description += ' '+array[i].substring(0, formatArray[0]-2).trim();
										}
										
										if(crFound && txn_start == "Yes"){
											//console.log(array[i].substring(0, formatArray[0]-2).trim(),"crFound");
											transactions[transactions.length-1].description += ' '+array[i].substring(0, formatArray[0]-2).trim();
										}
										
										if(secbankData[0]!=undefined && secbankData[0].trim().length<11 && dateRegex.test(secbankData[0]) && (secbankData[0].trim().indexOf('/') != -1 || secbankData[0].trim().indexOf('-') != -1)){
											i--;
											break;
										}
										
										
										
										//console.log(array[i].substring(0, formatArray[0]-2).trim());
										txnDesc = txnDesc +" "+ array[i].substring(0, formatArray[0]-2).trim();
										//console.log(txnDesc);
										//console.log(formatArray);
										//console.log(secbankData,'qqq');
										var filterDebitTxnBalance = array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,-]+/g,'');
										if(floatRegex.test(filterDebitTxnBalance) && filterDebitTxnBalance.indexOf('.') != -1){
											foundAmt = true;
											var drFound = true;
											//console.log(filterDebitTxnBalance,'filterDebitTxnBalance');
											transactions.push({
									            "date":txnDate,
									            "description":txnDesc,
									            "amount":filterDebitTxnBalance,
									            //"cheque_no":cheque_no,
									            "type":ty[1]
									        })
											//break;
										}
										var filterCreditTxnBalance = array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,-]+/g,'');
										if(floatRegex.test(filterCreditTxnBalance) && filterCreditTxnBalance.indexOf('.') != -1){
											foundAmt = true;
											var crFound = true;
											//console.log(filterCreditTxnBalance,'filterCreditTxnBalance');
											transactions.push({
									            "date":txnDate,
									            "description":txnDesc,
									            "amount":filterCreditTxnBalance,
									            //"cheque_no":cheque_no,
									            "type":ty[0]
									        })
											//break;
										}
										
										
										
									}	
								}
							}else if(floatRegex.test(filterTxnBalance) && filterTxnBalance.indexOf('.') != -1){
								
								if(citibanmexStartYear!=""){
									if(bankData[0].indexOf('/') != -1){
										var citibmxDate = bankData[0].split("/");
										cititxnMonth = citibmxDate[1];
										
										if(parseInt(checkMonth)==0){
											checkMonth = cititxnMonth;
										}
											
										if(parseInt(checkMonth)==parseInt(cititxnMonth)){
											banmexYear = citibanmexStartYear;
										}else{
											banmexYear = citibanmexYear;
										}
										
									}
								}else{
									banmexYear = citibanmexYear;
								}
								//var txnDate = bankData[0];
								var txnDate = bankData[0].trim()+'/'+banmexYear;
								if(txnDate.indexOf('/') != -1){
									var bmxDate = txnDate.split("/");
									txnDate = bmxDate[1]+'/'+bmxDate[0]+'/'+bmxDate[2];
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
								
								//console.log(bankData,'ElseData');
								//console.log(formatArray,'formatArray');
								var filterDebitTxnBalance = array[i].substring(formatArray[0]-2, formatArray[1]).trim().replace(/[$,-]+/g,'');
								//console.log(filterDebitTxnBalance,'filterDebitTxnBalance');
								if(floatRegex.test(filterDebitTxnBalance) && filterDebitTxnBalance.indexOf('.') != -1){
									//console.log(formatArray);
									//console.log(filterDebitTxnBalance,'else_filterDebitTxnBalance');
									transactions.push({
							            "date":txnDate,
							            "description":filterData[1],
							            "amount":filterDebitTxnBalance,
							            //"cheque_no":cheque_no,
							            "type":ty[1]
							        })
								}
								/*console.log(bankData,'ElseData');
								console.log(formatArray,'formatArray');
								console.log(array[i],'array[i]');
								console.log(formatArray[1],'formatArray[1]');
								console.log(formatArray[2],'formatArray[2]');
								console.log(array[i].substring(formatArray[1], formatArray[2]),'formatArray');*/
								
								var filterCreditTxnBalance = array[i].substring(formatArray[1], formatArray[2]).trim().replace(/[$,-]+/g,'');
								//console.log(filterCreditTxnBalance,'filterCreditTxnBalance');
								if(filterCreditTxnBalance.indexOf('   ') != -1){
									//console.log(filterCreditTxnBalance,'filterCreditTxnBalance');
									var splitBal = filterCreditTxnBalance.split(/  +/g);
									if(splitBal.length>1 && splitBal[0]!=undefined && splitBal[1]!=undefined && floatRegex.test(splitBal[0]) && splitBal[0].indexOf('.') != -1 && floatRegex.test(splitBal[1])){
										filterCreditTxnBalance = splitBal[0]
									}
									//console.log(filterCreditTxnBalance,'filterCreditTxnBalance');
								}
								if(floatRegex.test(filterCreditTxnBalance) && filterCreditTxnBalance.indexOf('.') != -1){
									//console.log(formatArray);
									//console.log(filterCreditTxnBalance,'else_filterCreditTxnBalance');
									transactions.push({
							            "date":txnDate,
							            "description":filterData[1],
							            "amount":filterCreditTxnBalance,
							            //"cheque_no":cheque_no,
							            "type":ty[0]
							        })
								}
							}
							

						}
					}
					
					
					
					

				}/*else if(transactions.length>0){
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
				}	*/	
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
    			console.log(response,"SecondBugsCheck");
    			
    			if(data.txtSplitFileName!= undefined && data.txtSplitFileName){
    				var txtSplitFileName =  data.txtSplitFileName;
        			if(txtSplitFileName.length>1 && txtSplitFileName.length>=q+1){
    					if(txtSplitFileName[q]!=undefined && jQuery.inArray(txtSplitFileName[q], textFiles) === -1){
    						textFiles.push(txtSplitFileName[q]);
    						console.log("q",txtSplitFileName);
    						console.log(q);
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
            					console.log('callFromView',q);
            	                console.log(response);
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
