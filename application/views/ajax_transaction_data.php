<table class="table excel_table" style="table-layout: fixed">
    <thead>
        <tr>
            <th style="width: 150px">Unique ID</th>
            <th style="width: 150px">Account#</th>
            <th style="width: 150px">Txn ID</th>
            <th style="width: 400px">Description</th>
            <th style="width: 150px">Check#</th>
            <th style="width: 100px">Txn Date</th>
            <th style="width: 120px">Txn Amount</th>
            <th style="width: 100px">Currency</th>
            <th style="width: 120px">Debit/Credit</th>
            <th style="width: 150px">Level 1</th>
            <!-- <th style="width: 150px">Level 2</th> -->
            <th style="width: 150px">Available Balance</th>
        </tr>
    </thead>
    <tbody>
        <input type="hidden" name="txn_data_history_id" value="<?php echo $history_id;?>">
        <?php 
            /*echo "<pre>";
            print_r($txn_tab_data);
            echo "</pre>";*/
            //die;
        ?>
        <?php if(!empty($txn_tab_data)) {
            //$i = 0;
            $open_balance = '';
            //$total_deposits = 0;
            //$total_withdrawals = 0;
            foreach($txn_tab_data as $key => $txn_data){
                 $open_balance = $txn_data->open_balance;
                 foreach ($txn_data->txn_data as $key => $value) {
                     /*echo "<pre>";
                     print_r($value);
                     echo "</pre>";*/
                    // if($value->txn_date >= $min_start_date){
                    //$i++;
                    /*if($open_balance==""){
                        $open_balance = $value->open_balance;
                    }*/
            ?>
                <tr>
                    <input type="hidden" name="txn_data_row_id[]" value="<?php echo $value->id;?>">
                    <input type="hidden" name="txn_data_file_no[]" value="<?php echo $value->file_no;?>">
                    <td>
                        <input type="text" disabled="" name="txn_data_unique_id[]" class="form-control" value="<?php echo $txn_data->unique_id;?>">
                    </td>
                    <td>
                        <input type="text" disabled="" name="txn_data_unique_id[]" class="form-control" value="<?php echo $txn_data->account_number;?>">
                    </td>
                    <td>
                        <input type="text" name="txn_data_txn_id[]" disabled="" class="form-control remove_disabled" value="">
                    </td>
                    <td>
                        <input type="text" name="txn_data_description[]" disabled="" class="form-control remove_disabled" value="<?php echo $value->description;?>">
                    </td>
                    <td>
                        <input type="text" name="txn_data_check[]" disabled="" class="form-control remove_disabled" value="<?php echo $value->check_no;?>">
                    </td>
                    <td>
                        <input type="text" name="txn_data_txn_date[]" disabled="" class="form-control remove_disabled" value="<?php echo $value->txn_date;?>">
                    </td>
                    <td>
                        <input type="text" name="txn_data_txn_amnt[]" disabled="" class="form-control remove_disabled" value="<?php echo $value->txn_amt;?>" style="text-align: right">
                    </td>
                    <td>
                        <input type="text" name="txn_data_currency[]" disabled="" class="form-control remove_disabled" value="<?php echo $value->txn_currency;?>">
                    </td>
                    <td>
                        <select class="form-control remove_disabled" name="txn_data_type[]" disabled="">
                            <option value="cr" <?php echo ($value->type == 'cr')?'selected':''; ?>>Credit</option>
                            <option value="dr" <?php echo ($value->type == 'dr')?'selected':''; ?>>Debit</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control remove_disabled" name="txn_data_level_1[]" disabled="">
                            <option value="">Select level</option>
                            <?php if($value->type == 'cr'){ 
                                $credit_array = creaditCategoryArray();
                                foreach($credit_array as $key => $credit_cat){
                                    $selected = '';
                                    if(strtolower($value->level_1)==strtolower($credit_cat)){
                                        $selected = "selected";
                                    }
                                ?>
                                    <option value="<?php echo strtolower($credit_cat);?>" <?php echo $selected;?>><?php echo $credit_cat;?></option>
                                <?php } ?>
                            <?php }  
                            else if($value->type == 'dr'){ 
                                $debit_array = debitCategoryArray();
                                foreach($debit_array as $key => $debit_cat){
                                    $selected = '';
                                    if(strtolower($value->level_1)==strtolower($debit_cat)){
                                        $selected = "selected";
                                    }
                                ?>
                                <option value="<?php echo strtolower($debit_cat);?>" <?php echo $selected;?> ><?php echo $debit_cat;?></option>
                            <?php } } ?>
                        </select>
                    </td>
                    <!-- <td>
                        <input type="text" name="txn_data_level_2[]" disabled="" class="form-control" value="<?php echo $value->level_2;?>">
                    </td> -->
                    <td style="text-align: right">
                    <?php 
                           if($value->type=='cr'){ 
                               $open_balance = $open_balance + $value->txn_amt; 
                               //$total_deposits = $total_deposits+$value->txn_amt;
                           }
                           if($value->type=='dr'){ 
                               $open_balance = $open_balance - $value->txn_amt; 
                               //$total_withdrawals = $total_withdrawals+$value->txn_amt;
                           }
                        ?>
                        <input type="text" disabled="" class="form-control" value="<?php echo number_format($open_balance,2); ?>">
                    </td>
                </tr>
            <?php } 
                } 
        }  
        ?>
    </tbody>
</table>