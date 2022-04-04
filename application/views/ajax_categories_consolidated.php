
<div class="excel_tab_box three_<?php echo $fileSerialNum1;?>" style="display: none;">
    <table class="table excel_table" style="width:100%;border: none!important;">
        <tbody>
            <tr>
                <td style="border-bottom: none!important;vertical-align: top;">
                    <table class="table excel_table category_table" style="margin-bottom: 0;border-right: none!important;">
                        <thead>
                            <tr>
                                <th style="background: #56C593;">Credit - Categories</th>
                                <th style="background: #56C593;">Count of Txn</th>
                                <th style="background: #56C593;">Amount (in MXN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Card"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - card']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - card']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (UBER)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (uber)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (uber)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Didi)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (didi)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (didi)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Rappi)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (rappi)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (rappi)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Sin Delantal)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (sin delantal)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (sin delantal)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Other)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (other)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - non card (other)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Cash Deposit"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['cash deposit']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['cash deposit']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Refund/Reversals"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['refund/reversals']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['refund/reversals']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Intra Account Transfer"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['intra account transfer']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['intra account transfer']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="NG Check"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['ng check']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['ng check']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loans"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['loans']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['loans']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Investment Income"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['investment income']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['investment income']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Insurance Claim"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['insurance claim']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['insurance claim']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Miscellaneous Credits"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['miscellaneous credits']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['miscellaneous credits']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Total"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - card']['count'] + $totalCrAmtArray['sales - non card (uber)']['count'] + $totalCrAmtArray['sales - non card (didi)']['count'] + $totalCrAmtArray['sales - non card (rappi)']['count'] + $totalCrAmtArray['sales - non card (sin delantal)']['count'] + $totalCrAmtArray['sales - non card (other)']['count'] + $totalCrAmtArray['cash deposit']['count'] + $totalCrAmtArray['refund/reversals']['count'] + $totalCrAmtArray['intra account transfer']['count'] + $totalCrAmtArray['ng check']['count'] + $totalCrAmtArray['loans']['count'] + $totalCrAmtArray['investment income']['count'] + $totalCrAmtArray['insurance claim']['count'] + $totalCrAmtArray['miscellaneous credits']['count'];?>"></td>


                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalCrAmtArray['sales - card']['amt'] + $totalCrAmtArray['sales - non card (uber)']['amt'] + $totalCrAmtArray['sales - non card (didi)']['amt'] + $totalCrAmtArray['sales - non card (rappi)']['amt'] + $totalCrAmtArray['sales - non card (sin delantal)']['amt'] + $totalCrAmtArray['sales - non card (other)']['amt'] + $totalCrAmtArray['cash deposit']['amt'] + $totalCrAmtArray['refund/reversals']['amt'] + $totalCrAmtArray['intra account transfer']['amt'] + $totalCrAmtArray['ng check']['amt'] + $totalCrAmtArray['loans']['amt'] + $totalCrAmtArray['investment income']['amt'] + $totalCrAmtArray['insurance claim']['amt'] + $totalCrAmtArray['miscellaneous credits']['amt'];?>"></td>
                            </tr>

                        </tbody>
                    </table>
                </td>
                <td style="border-bottom: none!important;">
                    <table class="table excel_table category_table" style="margin-bottom: 0">
                        <thead>
                            <tr>
                                <th style="background: #FF5F5F;">Debit - Categories</th>
                                <th style="background: #FF5F5F;">Count of Txn</th>
                                <th style="background: #FF5F5F;">Amount (in MXN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Vendor Payments"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['vendor payments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['vendor payments']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Salaries & Benefits"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['salaries & benefits']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['salaries & benefits']['amt'];?>"></td>
                            </tr>
                            
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Rent"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['rent']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['rent']['amt'];?>"></td>
                            </tr>
                            
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Taxes"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['taxes']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['taxes']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Insurance"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['insurance']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['insurance']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Cash Withdrawal"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['cash withdrawal']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['cash withdrawal']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Card Processor Fees"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['card processor fees']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['card processor fees']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Chargeback"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['chargeback']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['chargeback']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Credit Card Payments"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['credit card payments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['credit card payments']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Lenders"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - lenders']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - lenders']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Mortgage"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - mortgage']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - mortgage']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Auto Finance"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - auto finance']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['loan repayment/emi - auto finance']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Intra Account Transfer"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['intra account transfer']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['intra account transfer']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - NG"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - ng']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - ng']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - Overdraft"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - overdraft']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - overdraft']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['fees - others']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Investments"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['investments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['investments']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Deposited Check Return"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['deposited check return']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['deposited check return']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Miscellaneous Debit"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['miscellaneous debit']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['miscellaneous debit']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Airlines"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - airlines']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - airlines']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Hotels"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - hotels']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - hotels']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Car Rental"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - car rental']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - car rental']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['travel expenses - others']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Telephone"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - telephone']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - telephone']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Internet"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - internet']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - internet']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - TV"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - tv']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - tv']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Power"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - power']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - power']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Water"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - water']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - water']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['utilities - others']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Total"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['vendor payments']['count'] + $totalDrAmtArray['salaries & benefits']['count'] + $totalDrAmtArray['rent']['count'] + $totalDrAmtArray['taxes']['count'] + $totalDrAmtArray['insurance']['count'] + $totalDrAmtArray['cash withdrawal']['count'] + $totalDrAmtArray['card processor fees']['count']+ $totalDrAmtArray['chargeback']['count'] + $totalDrAmtArray['credit card payments']['count'] + $totalDrAmtArray['loan repayment/emi - lenders']['count'] + $totalDrAmtArray['loan repayment/emi - mortgage']['count'] + $totalDrAmtArray['loan repayment/emi - auto finance']['count'] + $totalDrAmtArray['intra account transfer']['count'] + $totalDrAmtArray['fees - ng']['count'] + $totalDrAmtArray['fees - overdraft']['count'] + $totalDrAmtArray['fees - others']['count'] + $totalDrAmtArray['investments']['count'] + $totalDrAmtArray['deposited check return']['count'] + $totalDrAmtArray['miscellaneous debit']['count'] + $totalDrAmtArray['travel expenses - airlines']['count'] + $totalDrAmtArray['travel expenses - hotels']['count'] + $totalDrAmtArray['travel expenses - car rental']['count'] + $totalDrAmtArray['travel expenses - others']['count'] + $totalDrAmtArray['utilities - telephone']['count'] + $totalDrAmtArray['utilities - internet']['count'] + $totalDrAmtArray['utilities - tv']['count'] + $totalDrAmtArray['utilities - power']['count'] + $totalDrAmtArray['utilities - water']['count'] + $totalDrAmtArray['utilities - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $totalDrAmtArray['vendor payments']['amt'] + $totalDrAmtArray['salaries & benefits']['amt'] + $totalDrAmtArray['rent']['amt']  + $totalDrAmtArray['taxes']['amt'] + $totalDrAmtArray['insurance']['amt'] + $totalDrAmtArray['cash withdrawal']['amt'] + $totalDrAmtArray['card processor fees']['amt']+ $totalDrAmtArray['chargeback']['amt'] + $totalDrAmtArray['credit card payments']['amt'] + $totalDrAmtArray['loan repayment/emi - lenders']['amt'] + $totalDrAmtArray['loan repayment/emi - mortgage']['amt'] + $totalDrAmtArray['loan repayment/emi - auto finance']['amt'] + $totalDrAmtArray['intra account transfer']['amt'] + $totalDrAmtArray['fees - ng']['amt'] + $totalDrAmtArray['fees - overdraft']['amt'] + $totalDrAmtArray['fees - others']['amt'] + $totalDrAmtArray['investments']['amt'] + $totalDrAmtArray['deposited check return']['amt'] + $totalDrAmtArray['miscellaneous debit']['amt'] + $totalDrAmtArray['travel expenses - airlines']['amt'] + $totalDrAmtArray['travel expenses - hotels']['amt'] + $totalDrAmtArray['travel expenses - car rental']['amt'] + $totalDrAmtArray['travel expenses - others']['amt'] + $totalDrAmtArray['utilities - telephone']['amt'] + $totalDrAmtArray['utilities - internet']['amt'] + $totalDrAmtArray['utilities - tv']['amt'] + $totalDrAmtArray['utilities - power']['amt'] + $totalDrAmtArray['utilities - water']['amt'] + $totalDrAmtArray['utilities - others']['amt'];?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>    
            </tr>
        </tbody>
    </table>
</div>