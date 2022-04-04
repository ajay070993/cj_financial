function findSearchType(param){
    //console.log(param);
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
var textFiles = [];
function bankCreditDebitTxn(data) {
	console.log(data,"BankCreditDebitTxns");
	var monthArray = ["jan", "feb", "mar", "apr", "may", "jun", "jul","aug", "sep", "oct", "nov", "dec"];
	var lastDate = "";
	var user_type = data.user_type;
	console.log(data);
	var account_holder_name = '';
	var begining_bal = "No";
  	var chkBgnBal = false;
  	var account_num = "No";
  	var chkAcctNum = false;
  	var closing_bal="Yes";
  	var chkclsBal = false;
  	var txn_type = "";
  	
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
	
	var account_number = '';
	
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
	
	if(data.check_all_pdf_process!=undefined && data.check_all_pdf_process){
		var check_all_pdf_process = data.newFolderName;
	}else{
		var check_all_pdf_process = false;
	}
	
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
	}
	//var getBeginingBalance = '626160.71';
	//alert(getBeginingBalance);
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
    
    var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	var dateRegex = new RegExp("^[0-9\-/.]+$");
    
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
		if(data.bank_data_val.bank_id == 185){
			data_string = data_string.replace(/[^\x00-\x7F]/g, "");
		}
		var array = data_string.split("\n");
		//console.log(array);
    	for(var i=0;i<array.length;i++){
        	
            var bankData =array[i].split(/  +/g);
            
            bankData = bankData.filter(Boolean);
            
            if(txn_start=="No"){
    			for(k=0;k<bankData.length;k++){
    				//if(bankData[k].trim()==txn_start_string.trim()){
					let tnx_start_string_split = txn_start_string.split("|").map(s => s.trim());
					tnx_start_string_split = tnx_start_string_split.filter(Boolean);
    				if(tnx_start_string_split.length > 0  && tnx_start_string_split.includes(bankData[k].trim())){
    					console.log("Transactions start from line number "+i);
    					txn_start = "Yes";
    				}
    			}
			}

			if(txn_start=="Yes" && stopTxn==0){
				var tnx_close_string_split = txn_close_string.split("|").map(s => s.trim());
				tnx_close_string_split = tnx_close_string_split.filter(Boolean);
    			for(k=0;k<bankData.length;k++){
    				//if(bankData[k].trim()==txn_close_string.trim()){
					
					if(tnx_close_string_split.length > 0  && tnx_close_string_split.includes(bankData[k].trim())){
    					//console.log("Transactions end from line number "+i);
    					txn_start = "No";
    					if(data.bank_data_val.bank_id==184){
    						stopTxn = 1;
    					}
    				}
					
					
    			}
			}
			
			if(txn_start=="Yes"){
				
				
				bankData = bankData.map(Function.prototype.call, String.prototype.trim);
				bankData = bankData.filter(Boolean);
				
				
				var endAmt = "";
				var crDrAmt = "";
				
				
				//Peoples Bank
				if(data.bank_data_val.bank_id == 151){
					bankData =array[i].split(/ +/g);
					
					var removeItem_array = ["*","E","*E","R","*R"];

					for (var j = 0; j < removeItem_array.length; j++) {
						var removeItem = removeItem_array[j];

						if(jQuery.inArray(removeItem, bankData) !== -1){		
							bankData = bankData.map(s => s.trim());				
							bankData = jQuery.grep(bankData, function(value) {
							  return value != removeItem;
							});
						}
					}
					
					bankData = bankData.filter(Boolean);
					
					if (bankData[bankData.length-1] != undefined) {
						var filterAmount = bankData[bankData.length-1].trim().replace(/[$,]+/g,'');
    				
						if((/\d+\-\d+/.test(bankData[bankData.length-2])) && ((intRegex.test(filterAmount) 
							|| floatRegex.test(filterAmount)) && filterAmount.length>0 && filterAmount.indexOf('.') != -1) 
							&& (/\d+/.test(bankData[bankData.length-3])) ) {
							array[i] = bankData.splice(0,bankData.length-3).join(" ");
						}
					}

				}
				
				
				if(bankData[bankData.length-1]!="" && bankData[bankData.length-1]!=undefined){
					//console.log(bankData.length);
					var endAmt = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
				}
				if(bankData[bankData.length-2]!="" && bankData[bankData.length-2]!=undefined){
					var crDrAmt = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
				}
				/*console.log(bankData);
				console.log("crDrAmt");
				console.log(crDrAmt);
				console.log(dateValidation(crDrAmt));
				console.log("endAmt");
				console.log(endAmt);
				console.log(dateValidation(endAmt));*/
				//console.log(bankData);
				//if(data.bank_data_val.bank_id==155 || data.bank_data_val.bank_id==184 || data.bank_data_val.bank_id==183 || data.bank_data_val.bank_id==186){
				if(jQuery.inArray(data.bank_data_val.bank_id, ["155","184","183","186"]) !== -1){
					if(data.bank_data_val.bank_id==184){
						if(bankData[bankData.length-1]!=undefined && parseInt(bankData[bankData.length-1])!=0){
							var removeItem = 0;
							bankData = jQuery.grep(bankData, function(value) {
							  return value != removeItem;
							});
						}else if(bankData[bankData.length-1]!=undefined && parseInt(bankData[bankData.length-1])==0 && parseInt(bankData[bankData.length-2])==0 && bankData.length>=6 && parseInt(bankData[bankData.length-1])==0){
							bankData.pop();
						}
						
						
						//console.log("N!rdesh");
						//console.log(bankData);
						if(bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>4){
							var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
							var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
							if(floatRegex.test(amt3) && amt3.length>0 && amt3.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1){
								bankData.splice(1, 1);
							}
						}
						
					}
					if(data.bank_data_val.bank_id==155 || data.bank_data_val.bank_id==184){
						if(data.bank_data_val.bank_id==155){
							var removeItem = 0;
							bankData = jQuery.grep(bankData, function(value) {
							  return value != removeItem;
							});
						}
						
						
						var afirmeMonth = '';
						var afirmeYear = '';
						if(end_date.indexOf('-') != -1){
							var afirmeDate = end_date.split("-");
							var afirmeMonth = afirmeDate[0];
							var afirmeYear = afirmeDate[2];
						}
						
						if(end_date.indexOf('/') != -1){
							var afirmeDate = end_date.split("/");
							var afirmeMonth = afirmeDate[0];
							var afirmeYear = afirmeDate[2];
						}
						
						if(bankData[0]!=undefined && bankData[0]!="" && bankData[0]<32){
							bankData[0] = bankData[0]+'/'+afirmeMonth+'/'+afirmeYear;
							console.log(bankData);
						}
						//console.log(bankData);
						//return;
					}else if(data.bank_data_val.bank_id==183){
						var bankData =array[i].split(/ +/g);
						var afirmeMonth = '';
						var afirmeYear = '';
						if(end_date.indexOf('-') != -1){
							var banregioDate = end_date.split("-");
							var banregioMonth = banregioDate[0];
							var banregioYear = banregioDate[2];
						}
						
						if(end_date.indexOf('/') != -1){
							var banregioDate = end_date.split("/");
							var banregioMonth = banregioDate[0];
							var banregioYear = banregioDate[2];
						}
						
						bankData = jQuery.grep(bankData, function(n, i){
							  return (n !== "" && n != null);
						});
						if(bankData[0]!=undefined && bankData[0]!="" && bankData[0]<32){
							bankData[0] = bankData[0]+'/'+banregioMonth+'/'+banregioYear;
							console.log(bankData);
							//console.log(bankData);
							//bankData[0] = '01/01/2019';
							//console.log(bankData);
						}
					}else if(data.bank_data_val.bank_id==186){
						var bankData =array[i].split(/ +/g);
						if(bankData[bankData.length-4]!=undefined && bankData[bankData.length-3]!=undefined && bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>5){
							//console.log(bankData,"FIRST");
							var amt4 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
							var amt3 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
							var amt2 = bankData[bankData.length-3].trim().replace(/[$,-]+/g,'');
							var amt1 = bankData[bankData.length-4].trim().replace(/[$,-]+/g,'');
							if(floatRegex.test(amt1) && amt1.length>0 && amt1.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1 && !floatRegex.test(amt3) || amt3.indexOf('.') == -1 && !floatRegex.test(amt4) && amt4.indexOf('.') == -1){
								bankData.splice(bankData.length-2, 2);
							}
						}
						
						if(bankData[bankData.length-3]!=undefined && bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>4){
							//console.log(bankData,"SECOND");
							var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
							var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
							var amt1 = bankData[bankData.length-3].trim().replace(/[$,-]+/g,'');
							if(floatRegex.test(amt1) && amt1.length>0 && amt1.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1 && !floatRegex.test(amt3) || amt3.indexOf('.') == -1){
								bankData.pop();
							}
						}
						
						if(bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>4){
							//console.log(bankData,"THIRD");
							var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
							var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
							if(floatRegex.test(amt3) && amt3.length>0 && amt3.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1){
								if(bankData[1]!=undefined){
									if(intRegex.test(bankData[1])){
										bankData.splice(bankData.length-3, 1);
									}
								}
							}
						}
						
						var hsbcMexicanMonth = '';
						var hsbcMexicanYear = '';
						if(end_date.indexOf('-') != -1){
							var hsbcMexicanDate = end_date.split("-");
							var hsbcMexicanMonth = hsbcMexicanDate[0];
							var hsbcMexicanYear = hsbcMexicanDate[2];
						}
						
						if(end_date.indexOf('/') != -1){
							var hsbcMexicanDate = end_date.split("/");
							var hsbcMexicanMonth = hsbcMexicanDate[0];
							var hsbcMexicanYear = hsbcMexicanDate[2];
						}
						
						bankData = jQuery.grep(bankData, function(n, i){
							  return (n !== "" && n != null);
						});
						if(bankData[0]!=undefined && bankData[0]!="" && bankData[0]<32){
							bankData[0] = hsbcMexicanMonth+'/'+bankData[0]+'/'+hsbcMexicanYear;
							
							if(bankData[bankData.length-1]!=undefined && bankData[bankData.length-2]!=undefined){
								var amt2 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
								var amt1 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
								if(bankData[0]!=undefined && bankData[0].trim().length<11 && bankData[0].trim().indexOf('/') != -1 && floatRegex.test(amt2) && amt2.indexOf('.') != -1 && amt1.indexOf('.') == -1){
									var naxtBankData =array[i+1].split(/ +/g);
									naxtBankData = jQuery.grep(naxtBankData, function(n, i){
										  return (n !== "" && n != null);
									});
									console.log(naxtBankData,"naxtBankData");
									if(naxtBankData[0]!=undefined){
										var filterTxnAmt = naxtBankData[0].trim().replace(/[$,-]+/g,'');
										if(floatRegex.test(filterTxnAmt) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
											i = i+1;
											var bankData = bankData.concat(naxtBankData[0]);
											console.log(bankData,"Full Array");
										}
									}
								}
							}
							//console.log(bankData);
							//bankData[0] = '01/01/2019';
							//console.log(bankData);
						}
					}
				}else{
					/*Logic scotiobank*/
					if(data.bank_data_val.bank_id==180){
						if(bankData[bankData.length-1]!=undefined && bankData.length>1){
							var filterAmount = bankData[bankData.length-1].trim().replace(/[$,]+/g,'');
							if(bankData[0]!=undefined && bankData[0].trim().length<11 && (bankData[0].trim().indexOf('/') != -1 || bankData[0].trim().indexOf('-') != -1) && !intRegex.test(filterAmount) && !floatRegex.test(filterAmount) && filterAmount.indexOf('.') == -1){
								console.log("bankData");
								console.log(bankData);
								console.log("NextData");
								var naxtBankData =array[i+1].split(/  +/g);
								console.log(bankData);
								if(naxtBankData[naxtBankData.length-1]!=undefined){
									var filterTxnAmt = naxtBankData[naxtBankData.length-1].trim().replace(/[$,-]+/g,'');
									if(floatRegex.test(filterTxnAmt) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
										i = i+1;
										var bankData = bankData.concat(naxtBankData);
										console.log("Full Array");
										console.log(bankData);
									}
								}
							}
						}
						if(bankData[bankData.length-1]!=undefined){
							var filterTxnAmt = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
							//console.log(bankData);
							var refRegex = new RegExp("^[0-9\%/.]+$");
							if(refRegex.test(bankData[bankData.length-3]) && (intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
								console.log(bankData.length-3);
								bankData.splice(bankData.length-3, 1);
								console.log(bankData);
							}
						}
					}else{
						if(bankData[0]!=undefined && bankData[0].indexOf('/') == -1 && bankData[0].indexOf('-') == -1 && dateValidation(crDrAmt) && dateValidation(endAmt)){
							var isDate = false;
							$.each(monthArray, function( index, value ) {
								
								if(bankData[0].toLowerCase().indexOf(value) != -1){
									isDate = true;
									var result = formatStringDate(bankData[0]);
					    			if(result){
					    				lastDate = result;
					    				bankData[0] = result;
					    			}
								}
							})
							
							if(isDate===false){
								bankData[0] = lastDate;
							}
							
						}else{
							var bankData =array[i].split(/ +/g);
							bankData = bankData.map(Function.prototype.call, String.prototype.trim);
							bankData = bankData.filter(Boolean);
						}
					}
				}
				
				if(data.bank_data_val.bank_id==144 && /^[0-9]{1,10}$/.test(bankData[1]) ){
					bankData[1] = "";
				}
				
				if(data.bank_data_val.bank_id==177){
					var removeItem = 0;
					bankData = jQuery.grep(bankData, function(value) {
					  return value != removeItem;
					});
					if(bankData[bankData.length-1]!=undefined){
						var filterTxnAmt = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						console.log(bankData);
						if((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
							bankData.splice(1, 1);
							console.log(bankData);
						}
					}
				}
				
				if(data.bank_data_val.bank_id==182){
					if(bankData[bankData.length-1]!=undefined && bankData[1]!=undefined){
						var filterTxnAmt = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						console.log(bankData);
						if(intRegex.test(bankData[1]) && (intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
							bankData.splice(1, 1);
							console.log(bankData);
						}
					}
					
				}
				
				//Banco Base
				if(data.bank_data_val.bank_id==181){
					bankData =array[i].split(/ +/g);
            		bankData = bankData.filter(Boolean);
            		
            		if(bankData[bankData.length-1]!=undefined){
            			var filterTxnAmt = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
            		}
					if(!(bankData[0]!=undefined && bankData[0].trim().length<11 && dateRegex.test(bankData[0].trim()) && (bankData[0].trim().indexOf('/') != -1 || bankData[0].trim().indexOf('-') != -1))  
						&&
						!((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1)
						&& (bankData[0]!=undefined && (bankData[0].trim() == "TRANSFERENCIA" || bankData[0].trim() == "SPEI"))
						){

						var nextdescription="";

						while(!(bankData[0]!=undefined && bankData[0].trim().length<11 && dateRegex.test(bankData[0].trim()) && (bankData[0].trim().indexOf('/') != -1 || bankData[0].trim().indexOf('-') != -1))){
							nextdescription = nextdescription + bankData.join(" ");
						    
							i++;
							bankData =array[i].split(/ +/g);
            				bankData = bankData.filter(Boolean);
            				if(tnx_close_string_split.length > 0  && tnx_close_string_split.includes(array[i].split(/  +/g))){
		    					i--;
		    					break;
		    				}
            				
						}				
						bankData.splice(1,0,nextdescription);						
					}

				}
				
				if(data.bank_data_val.bank_id==188){
					if(bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>4){
						var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						if(floatRegex.test(amt3) && amt3.length>0 && amt3.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1){
							if(bankData[1]!=undefined){
								if(intRegex.test(bankData[1])){
									bankData.splice(1, 1);
								}
							}
						}
					}
				}
				
				if(data.bank_data_val.bank_id == 185){
					//console.log("QQQQ");
					console.log(bankData);
					if(bankData[0]!=undefined && bankData[bankData.length-3]!=undefined && bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>5){
						var amt5 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt4 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						var amt3 = bankData[bankData.length-3].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-4].trim().replace(/[$,-]+/g,'');
						var amt1 = bankData[bankData.length-5].trim().replace(/[$,-]+/g,'');
						if(bankData[0].indexOf('/') != -1 && floatRegex.test(amt1) && amt1.length>0 && amt1.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1 && !floatRegex.test(amt3) && !floatRegex.test(amt4) && !floatRegex.test(amt5)){
							bankData.splice(bankData.length-2, 3);
						}
						var amt4 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt3 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-3].trim().replace(/[$,-]+/g,'');
						var amt1 = bankData[bankData.length-4].trim().replace(/[$,-]+/g,'');
						if(bankData[0].indexOf('/') != -1 && floatRegex.test(amt1) && amt1.length>0 && amt1.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1 && !floatRegex.test(amt3) && !floatRegex.test(amt4)){
							bankData.splice(bankData.length-2, 2);
						}
						
						var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						var amt1 = bankData[bankData.length-3].trim().replace(/[$,-]+/g,'');
						if(bankData[0].indexOf('/') != -1 && floatRegex.test(amt1) && amt1.length>0 && amt1.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1 && !floatRegex.test(amt3)){
							bankData.pop();
						}
					}
					console.log(bankData);
					if(bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData.length>4){
						var amt3 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						if(floatRegex.test(amt3) && amt3.length>0 && amt3.indexOf('.') != -1 && floatRegex.test(amt2) && amt2.length>0 && amt2.indexOf('.') != -1){
							if(bankData[1]!=undefined){
								if(intRegex.test(bankData[1])){
									bankData.splice(1, 1);
								}
							}
						}
					}
					
					
				}
				
				if(data.bank_data_val.bank_id == 179){
					if(bankData[0]!=undefined && bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined){
						var amt1 = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'');
						var amt2 = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'');
						if(bankData[0].trim().indexOf('-') != -1 && floatRegex.test(amt1) && amt1.indexOf('.') != -1 && !floatRegex.test(amt2)){
							
							i +=1;
							var increment = false;
							var endingBal =array[i].split(/  +/g);
							endingBal = endingBal.filter(Boolean);
							//console.log(endingBal,"endingBal");
							if(endingBal.length==1){
								if(endingBal[0]!=undefined){
									var endingBalAmt = endingBal[0].trim().replace(/[$,-]+/g,'');
									if(floatRegex.test(endingBalAmt) && endingBalAmt.indexOf('.') != -1){
										console.log(endingBalAmt,"endingBalAmt");
										bankData.splice(bankData.length+1, 0, endingBalAmt);
										console.log(bankData,"Final array");
										increment = true;
									}
								}
							}
							if(increment==false){
								i -=1;
							}
						}
					}
					
				}
				
				if(bankData.length>=4){
					var checkEndAMt = 0;
					var checkNextkey=15;
					//Inbursa scan code remove
					/*console.log(bankData);
					var arrayKey = 0;
					for(k=0;k<bankData.length-1;k++){
						var chkTxnAmt = bankData[k].trim().replace(/[$,-]+/g,'');
						
						if((intRegex.test(chkTxnAmt) || floatRegex.test(chkTxnAmt)) && chkTxnAmt.length>0 && chkTxnAmt.indexOf('.') != -1 && checkNextkey+1==k && checkEndAMt==1){
							console.log("Checking");
							console.log(k-2);
							console.log(bankData.length);
							console.log(bankData);
							bankData = bankData.splice(k-2,bankData.length);
							bankData = bankData.reverse();
							console.log(bankData);
							console.log("ENDING");
							
							checkEndAMt++;
						}
						if((intRegex.test(chkTxnAmt) || floatRegex.test(chkTxnAmt)) && chkTxnAmt.length>0 && chkTxnAmt.indexOf('.') != -1 && checkNextkey==0){
							checkNextkey = k;
							checkEndAMt++;
						}
							
					}
					console.log(bankData);*/
					
					var filterData = [];
					filterData[1] = '';
					filterData[0] = '';
					//filterData[txnAmt] = '';
					
					if(bankData[0]!=undefined && bankData[0].trim().length<11 && dateRegex.test(bankData[0].trim()) && (bankData[0].trim().indexOf('/') != -1 || bankData[0].trim().indexOf('-') != -1)){
						filterData[0] = bankData[0].trim();
					}
					for(k=1;k<bankData.length-2;k++){
						filterData[1] += 	' '+bankData[k];				
					}
					
					
					var filterTxnAmt = bankData[bankData.length-2].trim().replace(/[$,-]+/g,'').trim();
					if((intRegex.test(filterTxnAmt) || floatRegex.test(filterTxnAmt)) && filterTxnAmt.length>0 && filterTxnAmt.indexOf('.') != -1){
						/*if(bankData[bankData.length-2].indexOf('-') != -1){
							console.log("IF");
							filterData[2] = -Math.abs(parseFloat(bankData[bankData.length-2].trim().replace(/[$,]+/g,'')));
						}else{
							console.log("ELSE");
							filterData[2] = parseFloat(bankData[bankData.length-2].trim().replace(/[$,]+/g,''));
						}*/
						filterData[2] = parseFloat(bankData[bankData.length-2].trim().replace(/[$,-]+/g,'').trim());
					}
					
					var isFilterTxnBalance =true;
					var filterTxnBalance = bankData[bankData.length-1].trim().replace(/[$,-]+/g,'').replace('(', '').replace(')', '').trim();
					//console.log(filterTxnBalance);
					if((intRegex.test(filterTxnBalance) || floatRegex.test(filterTxnBalance)) && filterTxnBalance.length>0 && filterTxnBalance.indexOf('.') != -1){
						//console.log(bankData[bankData.length-1]);
						if(bankData[bankData.length-1].indexOf('-') != -1){
							filterData[3] = -Math.abs(parseFloat(bankData[bankData.length-1].trim().replace(/[$,-]+/g,'').replace('(', '').replace(')', '').trim()));
						}else if(bankData[bankData.length-1].indexOf('(') != -1 && bankData[bankData.length-1].indexOf(')') != -1){
							filterData[3] = -Math.abs(parseFloat(bankData[bankData.length-1].trim().replace(/[$,-]+/g,'').replace('(', '').replace(')', '').trim()));
						}else{
							filterData[3] = parseFloat(bankData[bankData.length-1].trim().replace(/[$,]+/g,'').replace('(', '').replace(')', '').trim());
						}
						//filterData[3] = Math.abs(parseFloat(bankData[bankData.length-1].trim().replace(/[$,-]+/g,'').replace('(', '').replace(')', '')));
						//console.log(filterData[3]);
					}else{
						isFilterTxnBalance = false;
					}
					
					if(filterData[0]!='' && filterData[0].length<11 && filterData[0].length>2 && dateRegex.test(filterData[0]) && (filterData[0].indexOf('/') != -1 || filterData[0].indexOf('-') != -1) && isFilterTxnBalance){
						/*console.log(filterData);
						console.log(parseFloat(getBeginingBalance));
						console.log(parseFloat(filterData[2]).toFixed(2));
						console.log(filterData[3]);
						console.log("fdfdf");*/
						if((parseFloat(getBeginingBalance)-parseFloat(filterData[2])).toFixed(2)==filterData[3]){
							//console.log("Debit");
							txn_type = "dr";
						}else if((parseFloat(getBeginingBalance)+parseFloat(filterData[2])).toFixed(2)==filterData[3]){
							//console.log("Credit");
							txn_type = 'cr';
						}else{
							//console.log('else');
							//console.log(parseFloat(getBeginingBalance));
							//console.log(parseFloat(filterData[2]));
							//console.log(parseFloat(filterData[3]));
							txn_type = "cr";
						}
						//console.log(filterData);
						transactions.push({
				            "date":filterData[0],
				            "description":filterData[1],
				            "amount":filterData[2],
				            "type":txn_type
				        })
				        getBeginingBalance = filterData[3];
					}else if(transactions.length>0 && data.bank_data_val.bank_id!=186){
						var extraDesc = "";
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
									extraDesc +=bankData[k]+" ";
								}
							}
						}
						if(extraDesc!=""){
							transactions[transactions.length-1].description += ' '+extraDesc;
	    					
	    				}
					}
					
				}else if(transactions.length>0 && data.bank_data_val.bank_id!=186){
					var staticArrayMultivia = ['54924','60482','61111'];
					var staticArraySantander = ['9834781','9997070','0132160','9467849','9662553','9686100'];
					//console.log("QQQQQQ");
					//console.log(bankData);
					if(jQuery.inArray(bankData[0], staticArrayMultivia) !== -1 && bankData.length==1 && data.bank_data_val.bank_id==184){
						
					}else if(jQuery.inArray(bankData[0], staticArraySantander) !== -1 && bankData.length==1 && data.bank_data_val.bank_id==188){
						
					}else if(data.bank_data_val.bank_id==186 && intRegex.test(bankData[0])){
						
					}else{
						var extraDesc = "";
						var bankData =array[i].split(/  +/g);
						var addDesc = true;
						if(data.bank_data_val.bank_id==155){
							bankData = jQuery.grep(bankData, function(n, i){
								  return (n !== "" && n != null);
							});
							
							console.log(bankData[bankData.length-1]);
							console.log(bankData.length);
							console.log(bankData[bankData.length-1]);
							if(bankData[bankData.length-1]!=undefined && bankData[bankData.length-1]!="" && bankData.length>1 && intRegex.test(bankData[bankData.length-1].trim()) && bankData[bankData.length-1].indexOf('.') == -1 && bankData[bankData.length-1].indexOf(',') == -1){
								bankData.splice(bankData.length-1, 1);
							}
							
						}
						
						
						if(data.bank_data_val.bank_id==180 && bankData[bankData.length-2]!=undefined && bankData[bankData.length-1]!=undefined && bankData[bankData.length-2]=="COBRANZA VIA DEPOSITO REFERENC" && parseInt(bankData[bankData.length-1])==0){
							bankData.pop();
						}
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
				}
				
			}  
    	}
    	console.log(transactions);
    	
    	var data_json = {
    			"bank_id":data.bank_data_val.bank_id,
    			"upload_pdf_file":data.extractData.upload_pdf_file,
    			"original_pdf_file_name":data.extractData.original_pdf_file_name,
    			"split_page_num_array":data.extractData.split_page_num_array,
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
                "bank_date_format":data.bank_data_val.bank_date_format,
                "currency":data.bank_data_val.currency,
                "newFolderName":newFolderName,
                "history_id":data.history_id,
                "zipFileName":data.zipFileName,
                "accType":accType,
                "check_all_pdf_process":check_all_pdf_process,
                "multiple_account":multiple_account,
            };

            //var surl = siteurl+'Bank_statement/createXLSBankStatement';  
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
    					if(txtSplitFileName[q]!=undefined && jQuery.inArray(txtSplitFileName[q], textFiles) === -1){
    						textFiles.push(txtSplitFileName[q]);
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
    						console.log(data_json,"QWERTY");
    						//response.multiple_account = true;
            				$.post(data.eurl,data_json, function(response) {
            					console.log('callFromView');
            					
            					console.log("Third");
            	                //console.log(response);
            					createXLSBankStatement(response);
            					return true;
            				}, 'json');
    					}else{
        					q = 0;
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

function formatStringDate(date) {
	var isDate = false;
	var monthArray = ["jan", "feb", "mar", "apr", "may", "jun", "jul","aug", "sep", "oct", "nov", "dec"];
	if(date){
		var dateArray = date.split(" ");
		dateArray = dateArray.filter(function(str) {
			return /\S/.test(str);
		});
		//console.log(dateArray);
		for (i = 0; i < dateArray.length; i++) {
			if(jQuery.inArray(dateArray[i].toLowerCase(), monthArray) !== -1){
				//console.log(dateArray[i].toLowerCase());
				isDate = true;
				break;
			}
		}
	}else{
		return false;
	}
	
	if(isDate){
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2){ 
			month = '0' + month;
		}
		
		if (day.length < 2){
			day = '0' + day;
		}
		//console.log(year);
		return [month,day].join('/');
		//return [day, month].join('/');
	}else{
		return false;
	}
}

function dateValidation(date){
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	if(((intRegex.test(date) || floatRegex.test(date)) && date.length>0 && date.indexOf('.') != -1)){
		return true;
	}
	return false;
}
