<div class="excel_tab_box three_<?php echo $fileSerialNum;?>" style="display: none;">
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
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - card']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - card']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (UBER)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (uber)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (uber)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Didi)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (didi)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (didi)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Rappi)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (rappi)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (rappi)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Sin Delantal)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (sin delantal)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (sin delantal)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Sales - Non Card (Other)"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (other)']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - non card (other)']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Cash Deposit"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['cash deposit']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['cash deposit']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Refund/Reversals"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['refund/reversals']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['refund/reversals']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Intra Account Transfer"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['intra account transfer']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['intra account transfer']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="NG Check"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['ng check']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['ng check']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loans"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['loans']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['loans']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Investment Income"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['investment income']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['investment income']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Insurance Claim"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['insurance claim']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['insurance claim']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Miscellaneous Credits"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['miscellaneous credits']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['miscellaneous credits']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Total"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - card']['count'] + $crAmtArray['sales - non card (uber)']['count'] + $crAmtArray['sales - non card (didi)']['count'] + $crAmtArray['sales - non card (rappi)']['count'] + $crAmtArray['sales - non card (sin delantal)']['count'] + $crAmtArray['sales - non card (other)']['count'] + $crAmtArray['cash deposit']['count'] + $crAmtArray['refund/reversals']['count'] + $crAmtArray['intra account transfer']['count'] + $crAmtArray['ng check']['count'] + $crAmtArray['loans']['count'] + $crAmtArray['investment income']['count'] + $crAmtArray['insurance claim']['count'] + $crAmtArray['miscellaneous credits']['count'];?>"></td>


                                <td><input type="text" class="form-control" disabled="" value="<?php echo $crAmtArray['sales - card']['amt']  + $crAmtArray['sales - non card (uber)']['amt'] + $crAmtArray['sales - non card (didi)']['amt'] + $crAmtArray['sales - non card (rappi)']['amt'] + $crAmtArray['sales - non card (sin delantal)']['amt'] + $crAmtArray['sales - non card (other)']['amt'] + $crAmtArray['cash deposit']['amt'] + $crAmtArray['refund/reversals']['amt'] + $crAmtArray['intra account transfer']['amt'] + $crAmtArray['ng check']['amt'] + $crAmtArray['loans']['amt'] + $crAmtArray['investment income']['amt'] + $crAmtArray['insurance claim']['amt'] + $crAmtArray['miscellaneous credits']['amt'];?>"></td>
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
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['vendor payments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['vendor payments']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Salaries & Benefits"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['salaries & benefits']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['salaries & benefits']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Rent"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['rent']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['rent']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Taxes"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['taxes']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['taxes']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Insurance"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['insurance']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['insurance']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Cash Withdrawal"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['cash withdrawal']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['cash withdrawal']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Card Processor Fees"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['card processor fees']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['card processor fees']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Chargeback"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['chargeback']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['chargeback']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Credit Card Payments"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['credit card payments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['credit card payments']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Lenders"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - lenders']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - lenders']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Mortgage"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - mortgage']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - mortgage']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Loan Repayment/EMI - Auto Finance"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - auto finance']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['loan repayment/emi - auto finance']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Intra Account Transfer"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['intra account transfer']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['intra account transfer']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - NG"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - ng']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - ng']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - Overdraft"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - overdraft']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - overdraft']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Fees - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['fees - others']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Investments"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['investments']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['investments']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Deposited Check Return"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['deposited check return']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['deposited check return']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Miscellaneous Debit"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['miscellaneous debit']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['miscellaneous debit']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Airlines"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - airlines']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - airlines']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Hotels"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - hotels']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - hotels']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Car Rental"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - car rental']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - car rental']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Travel Expenses - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['travel expenses - others']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Telephone"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - telephone']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - telephone']['amt'];?>"></td>
                            </tr>

                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Internet"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - internet']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - internet']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - TV"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - tv']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - tv']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Power"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - power']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - power']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Water"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - water']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - water']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Utilities - Others"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['utilities - others']['amt'];?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" disabled="" value="Total"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['vendor payments']['count'] + $drAmtArray['salaries & benefits']['count'] + $drAmtArray['rent']['count'] + $drAmtArray['taxes']['count'] + $drAmtArray['insurance']['count'] + $drAmtArray['cash withdrawal']['count'] + $drAmtArray['card processor fees']['count']+ $drAmtArray['chargeback']['count'] + $drAmtArray['credit card payments']['count'] + $drAmtArray['loan repayment/emi - lenders']['count'] + $drAmtArray['loan repayment/emi - mortgage']['count'] + $drAmtArray['loan repayment/emi - auto finance']['count'] + $drAmtArray['intra account transfer']['count'] + $drAmtArray['fees - ng']['count'] + $drAmtArray['fees - overdraft']['count'] + $drAmtArray['fees - others']['count'] + $drAmtArray['investments']['count'] + $drAmtArray['deposited check return']['count'] + $drAmtArray['miscellaneous debit']['count'] + $drAmtArray['travel expenses - airlines']['count'] + $drAmtArray['travel expenses - hotels']['count'] + $drAmtArray['travel expenses - car rental']['count'] + $drAmtArray['travel expenses - others']['count'] + $drAmtArray['utilities - telephone']['count'] + $drAmtArray['utilities - internet']['count'] + $drAmtArray['utilities - tv']['count'] + $drAmtArray['utilities - power']['count'] + $drAmtArray['utilities - water']['count'] + $drAmtArray['utilities - others']['count'];?>"></td>
                                <td><input type="text" class="form-control" disabled="" value="<?php echo $drAmtArray['vendor payments']['amt'] + $drAmtArray['salaries & benefits']['amt'] + $drAmtArray['rent']['amt'] + $drAmtArray['taxes']['amt'] + $drAmtArray['insurance']['amt'] + $drAmtArray['cash withdrawal']['amt'] + $drAmtArray['card processor fees']['amt']+ $drAmtArray['chargeback']['amt'] + $drAmtArray['credit card payments']['amt'] + $drAmtArray['loan repayment/emi - lenders']['amt'] + $drAmtArray['loan repayment/emi - mortgage']['amt'] + $drAmtArray['loan repayment/emi - auto finance']['amt'] + $drAmtArray['intra account transfer']['amt'] + $drAmtArray['fees - ng']['amt'] + $drAmtArray['fees - overdraft']['amt'] + $drAmtArray['fees - others']['amt'] + $drAmtArray['investments']['amt'] + $drAmtArray['deposited check return']['amt'] + $drAmtArray['miscellaneous debit']['amt'] + $drAmtArray['travel expenses - airlines']['amt'] + $drAmtArray['travel expenses - hotels']['amt'] + $drAmtArray['travel expenses - car rental']['amt'] + $drAmtArray['travel expenses - others']['amt'] + $drAmtArray['utilities - telephone']['amt'] + $drAmtArray['utilities - internet']['amt'] + $drAmtArray['utilities - tv']['amt'] + $drAmtArray['utilities - power']['amt'] + $drAmtArray['utilities - water']['amt'] + $drAmtArray['utilities - others']['amt'];?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>    
            </tr>
        </tbody>
    </table>
</div>