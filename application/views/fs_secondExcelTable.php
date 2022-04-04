<?php
// echo "<pre>";
// // print_r($excelData);
// // die;
// echo "</pre>";
?>
<style>
    .excel_tab_box.two {
        display: none;
    }

    #tbl_container.tabular_format {
        border: 1px solid #ddd;
        padding-bottom: 10px;
    }

    table#tabular_format.custom_table tr td.head {
        font-weight: 600;
        background: #f4f4f4;
    }

    table#tabular_format.custom_table tr.head2 {
        background: #f4f4f4;
    }

    table#tabular_format.custom_table tr.head td {
        text-align: center !important;
        font-weight: 600;
        background: #f4f4f4;
    }

    table#tabular_format.custom_table tr.head2 td,
    table#tabular_format.custom_table tr th {
        padding: 6px;
    }

    table#tabular_format.custom_table tr td {
        padding: 4px;
    }

    table#tabular_format.custom_table tr td.num {
        text-align: right;
        font-family: 'Roboto Mono', monospace;
    }

    table#tabular_format.custom_table tr td input {
        background: #fff;
    }
</style>

<table class="table custom_table no_border" id="tabular_format">
    <thead>
    <tr class="head">
        <td>&nbsp;</td>
        <td colspan="9"></td>
        <td colspan="48">Balance General</td>
        <td colspan="21">Esado de Resultados</td>
    </tr>

    <tr class="head">
        <td>&nbsp;</td>
        <td colspan="9">General Information</td>
        <td colspan="25">Activo</td>
        <td colspan="14">Pasivo</td>
        <td colspan="8">Capital Contable</td>
        <td colspan="1">&nbsp;</td>
        <td colspan="21">&nbsp;</td>
    </tr>

    <tr class="head2">
        <td class="head">Field Name</td>
        <td>Opportunity_Id</td>
        <td>Customer Name</td>
        <td>RFC Number</td>
        <td>Audited/Not Audited</td>
        <td>Audit Firm Name</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;Year&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>Annual / Parcial</td>
        <td>Month (If Parcial)</td>
        <td>Auditor Opinion (Desfavorable/Favorable/Con Salvedades)</td>
        <td>Caja y Bancos</td>
        <td>Clientes</td>
        <td>Deudores Diversos</td>
        <td>Inventarios</td>
        <!-- M  total 78 fields in excel -->
        <!-- N -->
        <td>Partes Relacionadas</td>
        <td>Impuestos por recuperar</td>
        <td>Proyectos en Proceso</td>
        <td>Anticipo a Proveedores</td>
        <td>Activo Circulante</td>
        <td>Otros Activos No Circulantes</td>
        <td>Cuentas por Cobrar L.P.</td>
        <td>Inversiones y CxC L.P.</td>
        <td>Terrenos e Inmuebles</td>
        <td>Maquinaria y Equipo</td>
        <td>Equipo de Transporte</td>
        <td>Equipo de Oficina</td>
        <td>Equipo de Computo</td>
        <td>Depreciación Acumulada</td>
        <td>Otros Activos (No Maquinaria)</td>
        <td>Activo Fijo</td>
        <td>Gasto de Instalación - Amortización</td>
        <!-- AD -->
        <!-- AE -->
        <td>Impuestos Dieferidos</td>
        <td>Depósitos en Garantía</td>
        <td>Activo Diferido</td>
        <td>Activo Total</td>
        <td>Pasivo Financiero Corto Plazo + (PCLP)</td>
        <!-- <td>No usar este renglón (Ocultar)</td> -->
        <td>Proveedores</td>
        <td>Partes Relacionadas</td>
        <td>Impuestos por Pagar C.P.</td>
        <td>Acreedores Diversos</td>
        <td>Anticipo de Clientes</td>
        <td>Pasivo a Corto Plazo</td>
        <td>Pasivo Financiero Largo Plazo</td>
        <!-- <td>No usar este renglón (Ocultar)</td> -->
        <td>Acreedores Diversos</td>
        <td>Impuestos Diferidos</td>
        <td>Obligaciones Laborales</td>
        <td>CxP y Otros Pasivos L.P.</td>
        <td>Pasivo a Largo Plazo</td>
        <td>Pasivo Total</td>
        <td>Capital Social</td>
        <td>Reserva Legal</td>
        <td>Aportaciones por Capitalizar</td>
        <td>Prima en Suscripcion de Acciones</td>
        <td>Otras Cuentas de Capital (Actualización)</td>
        <td>Utilidades Acumuladas</td>
        <td>Utilidad del Ejercicio</td>
        <td>Capital Contable</td>
        <!-- BF -->
        <!-- BG -->
        <td>Pasivo + Capital</td>
        <td>MESES COMPRENDIDOS</td>
        <td>PROMEDIO VENTAS MENSUALES</td>
        <td>Ventas Netas</td>
        <td>Costo de Ventas</td>
        <td>UTILIDAD BRUTA</td>
        <td>Gastos de Administración</td>
        <td>Gastos de Ventas</td>
        <td>Total Gastos de Operación</td>
        <td>UTILIDAD OPERACIÓN</td>
        <td>Gastos Financieros</td>
        <td>(Productos Financieros)</td>
        <td>(Utilidad) o Pérdida Cambiaria</td>
        <td>Posición Monetaria</td>
        <td>Otros Gastos (Productos)</td>
        <td>Partidas Extraodinarias</td>
        <td>UTILIDAD ANTES IMPS</td>
        <td>Provisión de ISR y PTU</td>
        <td>Otras Provisiones</td>
        <td>UTILIDAD NETA</td>
        <td>Depreciación Aplicada en Resultados</td>
        <td>Amortización Aplicada en Resultados</td>
    </tr>
    </thead>

    <tbody id="tabular_data">
        <?php
        $titleAr = array("Input", "UGH Cmnts");

        for ($i = 0; $i < count($excelData); $i++) { ?>
            <tr>
                <td class="head"><?php echo formatExcelTxtData($titleAr[$i]); ?></td>
                <td><?php echo $businessNameInfo['unique_id'] ?></td>
                <td><?php echo $businessNameInfo['business_name'] ?></td>
                <td><?php echo $businessNameInfo['rfc_number'] ?></td>
                <td><?php if (formatExcelTxtData($excelData[$i]['is_audited']) == "1") {
                    echo "Audited";
                }else if(formatExcelTxtData($excelData[$i]['is_audited']) == "0"){
                    echo "Not Audited";
                }else{
                    echo "";
                }
                ?></td>
                <td><?php echo formatExcelTxtData($excelData[$i]['audit_firm_name']) ?></td>
                <td><input disabled type="month" value="<?php echo formatExceldate($excelData[$i]['conf_sqr_amt']) ?>"></td>
                <td class="num"><?php echo "" ?></td>
                <td class="num"><?php echo "" ?></td>
                <td><?php echo formatExcelTxtData($excelData[$i]['audit_opinion']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['cash_and_banks']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['customers']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['various_debtors']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['inventories']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['related_parties']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['taxes_to_be_recovered']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['projects_in_process']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['advances_to_suppliers']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['current_assets']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['other_non_current_assets']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['accounts_receivable_lp']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['investments_and_cxc_lP']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['land_real_estate']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['machinery_equipment']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['transportation_equipment']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['office_team']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['computer_equipment']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['accumulated_depreciation']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['other_assets']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['fixed_assets']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['installation_expense_amortization']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['deferred_tax']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['deposits_in_guarantee']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['deferred_assets']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['total_active']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['stfl_plus_pclp']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['providers']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['p_related_parties']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['taxes_paying_cp']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['various_creditors']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['advance_customers']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_in_short_time']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['ltfl']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_various_creditors']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_deferred_tax']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['laboral_obligations']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['cxp_other_lp_liabilities']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_long_term_liabilities']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['totally_passive']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['social_capital']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['legal_reserve']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['contributions_to_capitalize']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['share_subscription_premium']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['other_capital_accounts']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['acumulated_utilities']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['profit_year']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_stockholders_equity']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['pst_liabilities_capital']) ?></td>
                <td class="num"><?php echo formatExcelTxtData($excelData[$i]['months_understood']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['avg_monthly_sales']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['net_sales']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['sales_cost']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['gross_profit']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['admin_expenses']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['selling_expenses']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['total_opr_cost']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['operating_income']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['fs_expenses']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['fn_products']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['profit_or_loss']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['monetoty_position']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['other_expenses']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['extraordinary_items']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['profit_before_imps']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['prov_of_it']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['other_prov']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['net_profit']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['applied_depreciation']) ?></td>
                <td class="num"><?php echo formatExcelNumData($excelData[$i]['amortization_applied']) ?></td>
            </tr>

        <?php } ?>
    </tbody>
</table>

<script>
    function dateFormat(d){
        // date = d.split("-"); // "31-12-2021"

        // dt = new Date(date[2], date[0], date[1]);
        // return dt.getFullYear() + "-" + dt.getMonth();
        // return date[2] + "-" + date[1];

        const date = new Date(d);
        let zero = "";
        if((date.getMonth()+1) < 10){
            zero = "0";
        }
        return `${date.getFullYear()}-${zero}${date.getMonth()+1}`;
    }

     function numFormat2(n) {
        let numPtrn = /[, ]+/g;

        let roundOff = Math.round((Number(n.replace(numPtrn, "")) + Number.EPSILON) * 100) / 100;
        let str = "";
        // if (roundOff === 0) {
        //     // str = "";
        //     return sumFlag ? '0.00' : "";
        // } else
        if (roundOff - Math.floor(roundOff) === 0) {
            str = ".00";
        } else if (roundOff * 10 - Math.floor(roundOff * 10) === 0) {
            str = "0";
        }
        // return isNaN(new Intl.NumberFormat('en-US').format(roundOff)) ? "0" : new Intl.NumberFormat('en-US').format(roundOff) + str ;

        return new Intl.NumberFormat('en-US').format(roundOff) + str;
    }


    // console.log($("#autosave_form").serialize());

    // if()
    // function dta(){

    //     var data = $("#autosave_form").serialize();

    // var array = (data.split("&"));

    // for(let i=0;i<array.length;i++){
    //     console.log(array[i]);
    // }

    // }
    $(document).ready(function() {
        $('#tabular_format_data').click(function(event) {
            var value = $(this).attr("data-tab");
            $(".excel_tabs li").removeClass("active");
            $(this).addClass("active");
            $(".excel_tab_box").hide();
            $(".excel_tab_box." + value).show();
            // $("#autoSaveStatus").html("<i>Saving...</i>");
            // var $submitBTN = $(this).find('button[type="submit"]');
            // var btnText = $submitBTN.html();
            // $submitBTN.attr('disabled', 'disabled');
            // var posturl = $(this).attr('action');
            // var $this = $(this).closest('form');
            // var formID = $(this).attr('id');
            // var formClass = $(this).attr('class');
            // var loadingHTML = '<i class="fa fa-spinner fa-spin fa-lg fa-fw"></i>'
            // var loadingHTML = '<svg class="spinner" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" height="22px" width="22px" fill="#456"><path d="M.01 0h24v24h-24V0z" fill="none"/><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>';
            // $submitBTN.text('');
            // $submitBTN.append(loadingHTML);
            // if (!formID)
            //     formID = formClass;
            // window.ajaxRequested = true;
            // $($this).find('.form-group').removeClass('has-error');
            // $($this).find('.help-block').hide();
            // thisform = $this;
            // $.each($this.find('input'), function(key, value) {
            //     if (!$(this).val())
            //         $(this).removeClass('edited');
            // });
            $(this).ajaxSubmit({
                url: siteurl + "Fs_dashboard/getTabularFormatData/<?php echo $id ?>",
                dataType: 'json',
                success: function(response) {
                    updateTabularFormat(response);
                },
                error: function(response) {
                    console.log("error")
                }
            });
            return false;
        });
    });


    function updateTabularFormat(response) {
        titleAr = ["Input", "UGH Cmnts"];
        // console.log(response);
        let data = "";
        
        for (let i = 0; i < response.excelData.length; i++) {
            data += (`<tr>
        <td class="head">${titleAr[i] ? titleAr[i] : ""}</td>
        <td>${response.businessNameInfo.unique_id}</td>
        <td>${response.businessNameInfo.business_name}</td>
        <td>${response.businessNameInfo.rfc_number}</td>
        <td>${response.excelData[i]['is_audited'] ? (response.excelData[i]['is_audited'] === "1" ? "Audited" : "Not Audited") : ""}</td>
        <td>${response.excelData[i]['audit_firm_name']}</td>
        <td><input disabled type="month" value="${dateFormat(response.excelData[i]['conf_sqr_amt'])}"></td>
        <td class="num"></td>
        <td class="num"></td>
        <td>${response.excelData[i]['audit_opinion']}</td>
        <td class="num">${numFormat2(response.excelData[i]['cash_and_banks'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['customers'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['various_debtors'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['inventories'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['related_parties'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['taxes_to_be_recovered'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['projects_in_process'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['advances_to_suppliers'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['current_assets'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['other_non_current_assets'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['accounts_receivable_lp'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['investments_and_cxc_lP'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['land_real_estate'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['machinery_equipment'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['transportation_equipment'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['office_team'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['computer_equipment'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['accumulated_depreciation'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['other_assets'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['fixed_assets'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['installation_expense_amortization'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['deferred_tax'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['deposits_in_guarantee'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['deferred_assets'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['total_active'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['stfl_plus_pclp'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['providers'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['p_related_parties'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['taxes_paying_cp'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['various_creditors'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['advance_customers'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_in_short_time'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['ltfl'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_various_creditors'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_deferred_tax'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['laboral_obligations'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['cxp_other_lp_liabilities'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_long_term_liabilities'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['totally_passive'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['social_capital'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['legal_reserve'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['contributions_to_capitalize'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['share_subscription_premium'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['other_capital_accounts'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['acumulated_utilities'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['profit_year'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_stockholders_equity'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['pst_liabilities_capital'])}</td>
        <td class="num">${response.excelData[i]['months_understood']}</td>
        <td class="num">${numFormat2(response.excelData[i]['avg_monthly_sales'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['net_sales'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['sales_cost'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['gross_profit'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['admin_expenses'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['selling_expenses'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['total_opr_cost'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['operating_income'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['fs_expenses'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['fn_products'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['profit_or_loss'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['monetoty_position'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['other_expenses'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['extraordinary_items'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['profit_before_imps'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['prov_of_it'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['other_prov'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['net_profit'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['applied_depreciation'])}</td>
        <td class="num">${numFormat2(response.excelData[i]['amortization_applied'])}</td>
    </tr>`);

        }

        $("tbody#tabular_data").html(data);
    }

    // $("#tabular_format_data").click(function(){

    // });
</script>