<?php $currentPage='dashboard';
      include('header.php'); ?>
<?php include('navigation.php'); 
$analysts = $this->fs_history->getAllAnalysts();
        ?>
 <div class="modal fade customModal" id="assignAnalyst">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: unset;max-width:531px">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="form-signin ajax_form form-fields imprtExcelForm" id="ajax_form" onSubmit="return validations();" action="<?php echo site_url('Fs_dashboard/assignAnalyst'); ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                        <p style="margin: 14px 0 ;">Assign cases to Analyst</p>
                        <!-- <input type="file" name="imprtExcelFile" class="form-control" id="imprtExcelFile" style="height: auto;"> -->
                        <select name="analyst_id" id="selectAnalystId">
                            <option value="-1">Select Analyst</option>
                            <?php for($i=0; $i < count($analysts);$i++){ ?>
                                <option value="<?php echo $analysts[$i]->id;?>"><?php echo $analysts[$i]->first_name.' '.$analysts[$i]->last_name;?></option>
                                <?php } ?>
                        </select>
                        <input type="hidden" name="case_ids" id="case_ids">
                        <div class="updateContainer" style="justify-content: center;">
                            <button type="submit">Submit</button>
                            <button type="button" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div class="scrollContainer">
        <div class="statusContainer">
            <!-- <div class="status one">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 53 43.63"><g><path d="M36.712 32.025v2.343H11.326a3.044 3.044 0 00-1.841.614l-5.97 4.631v-36.1h33.2v10.658l3.515-3.961v-7.2A3.036 3.036 0 0037.214 0H3.013A3 3 0 000 3.013v37.548A3.027 3.027 0 001.674 43.3a2.971 2.971 0 001.339.335 3.305 3.305 0 001.841-.614l6.472-5.133h25.888a3.036 3.036 0 003.013-3.013v-6.197l-2.232 2.511a3.419 3.419 0 01-1.283.836z"/><path d="M10.266 11.996h19.639a1.763 1.763 0 001.785-1.785 1.8 1.8 0 00-1.785-1.786H10.266a1.8 1.8 0 00-1.785 1.785 1.835 1.835 0 001.785 1.786zM31.691 18.69a1.8 1.8 0 00-1.786-1.785H10.266a1.8 1.8 0 00-1.785 1.785 1.763 1.763 0 001.785 1.785h19.639a1.763 1.763 0 001.786-1.785zM8.536 27.172a1.763 1.763 0 001.785 1.785h16.961l.614-3.515h-17.63a1.738 1.738 0 00-1.73 1.73zM33.687 21.728l10.457-11.68 4.988 4.465-10.457 11.68zM52.668 9.094L49.209 5.97a.977.977 0 00-1.395.056L45.75 8.369l4.966 4.463 2.064-2.343a1.034 1.034 0 00-.112-1.395zM31.189 30.798l4.24-1.227a1.215 1.215 0 00.442-.282l1.227-1.339-4.961-4.461-1.227 1.339a1 1 0 00-.223.5l-.725 4.352a1 1 0 001.227 1.118z"/></g></svg>
                <h4><span>Last Week</span><?php echo$countLastWeek;?><span>spreaded</span></h4>
            </div>
            <div class="status two">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 369.999 464"><path d="M326.216 464h-20.14v-.09h-278.2A28.127 28.127 0 010 439.317V24.594A28.127 28.127 0 0127.879 0h271.006a9.75 9.75 0 017.121 2.949l61.043 61.04A10.1 10.1 0 01369.938 70l.005.06V70.109c0 .027.005.055.007.082v.018c.026.307.04.61.04.9v128.03a10.07 10.07 0 11-20.139 0V81.18H316.91a28.14 28.14 0 01-28.094-28.121V20.141H27.879a7.963 7.963 0 00-7.955 7.952v407.724a7.963 7.963 0 007.955 7.952h314a7.976 7.976 0 007.981-7.952V279a10.07 10.07 0 1120.14 0v156.817a28.139 28.139 0 01-28.121 28.094h-15.663V464zm-17.26-429.618v18.677a7.975 7.975 0 007.951 7.98h18.706zm-9.135 373.052H70.614a10.081 10.081 0 01-10.069-10.071V212.855a10.083 10.083 0 0110.069-10.071h229.207a10.082 10.082 0 0110.07 10.071v184.508a10.082 10.082 0 01-10.07 10.071zM195.286 361.3v25.992h94.464V361.3h-94.464zm-114.6 0v25.992h94.459V361.3zm0-46.133v25.992h94.459v-25.992zm114.6 0v25.992h94.464v-25.993h-94.464zm0-46.134v25.993h94.464v-25.994h-94.464zm-114.6 0v25.993h94.459v-25.994zm114.6-46.105v25.962h94.464v-25.963h-94.464zm-114.6 0v25.962h94.459v-25.963zm279.244 26.211a10.082 10.082 0 01-10.07-10.071v-.2a10.07 10.07 0 1120.139 0v.2a10.081 10.081 0 01-10.069 10.071zm-60.108-83.4h-125.8a10.082 10.082 0 01-10.07-10.071V114.92a10.082 10.082 0 0110.07-10.071h125.8a10.082 10.082 0 0110.07 10.071v40.75a10.082 10.082 0 01-10.071 10.071zm-115.727-40.747V145.6H289.75v-20.608H184.095z"/></svg>
                <h4><span>Total</span><?php echo$countSpreading;?><span>Spreaded</span></h4>
            </div>
            <div class="status three">
                <svg class="template" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 367.6 367.6"><path d="M328.6 81.6c-.4 0-.4-.4-.8-.8s-.4-.8-.8-1.2L258.2 2.4c-.4-.4-1.2-.8-1.6-1.2-.4 0-.4-.4-.8-.4-.8-.4-2-.8-3.2-.8H83.8C59 0 38.6 20.4 38.6 45.2v277.2c0 24.8 20.4 45.2 45.2 45.2h200c24.8 0 45.2-20.4 45.2-45.2v-238c0-.8-.4-2-.4-2.8zm-68.4-54.4l44.4 50h-44.4v-50zM313.8 322c0 16.8-13.2 30.4-30 30.4h-200c-16.8 0-30-13.6-30-30V44.8c0-16.8 13.6-30 30-30H245v69.6c0 4 3.2 7.6 7.6 7.6h61.2v230z"/><path d="M92.6 92h66.8c4 0 7.6-3.2 7.6-7.6s-3.2-7.6-7.6-7.6H92.6c-4 0-7.6 3.2-7.6 7.6s3.6 7.6 7.6 7.6zM159.4 275.6H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6h66.8c4 0 7.6-3.2 7.6-7.6 0-4-3.6-7.6-7.6-7.6zM85 134.8c0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4-3.2-7.6-7.6-7.6H92.6c-4 0-7.6 3.2-7.6 7.6zM271 164.8H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4.4-3.2-7.6-7.6-7.6zM271 202H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4.4-3.2-7.6-7.6-7.6zM271 239.2H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4-3.2-7.6-7.6-7.6z"/></svg>
                <h4><span>NO. of</span><?php echo$countTemplate; ?><span>template</span></h4>
            </div>
            <div class="status four">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31.314 51.95"><path d="M25.017 19.594a9.377 9.377 0 003.384-6.694V4.916h.41a2.009 2.009 0 002-2V2.5a2.009 2.009 0 00-2-2H2.557a2.009 2.009 0 00-2 2v.41a2.009 2.009 0 002 2h.361v8.036a9.271 9.271 0 003.383 6.642l7.782 5.712v1.343l-7.786 5.713A9.137 9.137 0 002.913 39v8.036h-.41a2.009 2.009 0 00-2 2v.41a2.009 2.009 0 002 2h26.254a2.009 2.009 0 002-2v-.41a2.009 2.009 0 00-2-2h-.361v-7.987a9.331 9.331 0 00-3.383-6.692l-7.749-5.709V25.3zm3.74 28.249a1.181 1.181 0 011.182 1.182v.411a1.189 1.189 0 01-1.182 1.181H2.503a1.189 1.189 0 01-1.182-1.182v-.411a1.189 1.189 0 011.182-1.182h26.254zm-4.789-.808H7.317v-7.016a6.058 6.058 0 012.115-4.2l6.232-4.6 6.195 4.6a6.01 6.01 0 012.115 4.2zm-7.4-19.865L24.483 33a8.478 8.478 0 013.06 6v8.036h-2.737v-7.017a6.768 6.768 0 00-2.45-4.851L15.912 30.4a.362.362 0 00-.485 0l-6.481 4.764a6.884 6.884 0 00-2.45 4.851v7.016H3.684v-8.069a8.645 8.645 0 013.06-6l7.949-5.834a.478.478 0 00.162-.323v-1.75a.351.351 0 00-.162-.323L6.744 18.9a8.407 8.407 0 01-3.06-6V4.916h20.3a.385.385 0 00.41-.41.385.385 0 00-.41-.41H2.503a1.189 1.189 0 01-1.182-1.182V2.5a1.189 1.189 0 011.182-1.179h26.254A1.189 1.189 0 0129.943 2.5v.41a1.189 1.189 0 01-1.186 1.19h-.821a.385.385 0 00-.41.41v8.434a8.526 8.526 0 01-3.06 6l-7.909 5.825a.479.479 0 00-.162.323v1.754a.391.391 0 00.176.323z" fill="#fff" stroke="#fff"/><path d="M22.157 15.999H9.195a.423.423 0 00-.41.286.489.489 0 00.162.448l6.481 4.764a.663.663 0 00.249.087.806.806 0 00.286-.087l2.563-1.878a.411.411 0 00.087-.56.456.456 0 00-.572-.087l-2.326 1.717-5.262-3.869h11.705a.41.41 0 100-.821z" fill="#fff" stroke="#fff"/></svg>
                <h4><span>Average</span><?php echo$avgSpreadTime; ?><span>spreaded time</span></h4>
            </div> -->
        </div>

        <div style="text-align: right">
            <button class="btn-assign" id="assign_to_analyst">Assign</button>
            <!-- <a style="display: inline-block;margin-top: 20px;margin-bottom: -8px;font-size: 13px;color: #006FCF;font-weight: 500;" target="_blank" href="<?php echo base_url('bulk-upload-spread'); ?>">Refresh</a> -->
       </div>
        <table id="datatable" class="table history" style="table-layout: fixed">
            <thead>
                <tr>
                    <th class="noSort"></th>
                    <th class="noSort">No.</th>
                    <th class="noSort">Unique ID</th>
                    <th class="noSort">Business Name</th>
                    <th class="noSort">Case Type</th>
                    <!-- <th class="noSort">Bank Name</th> -->
                    <th class="noSort">Upload Date</th>
                    <th class="noSort">Analyst</th>
                    <!-- <th class="noSort">Spreading Status</th> -->
                    <!-- <th class="noSort">Field Error</th> -->
                    <th class="noSort">Status</th>
                    <?php if($this->common_model->checkUserPermission(17,false)){ ?>
                    <th class="noSort" style="width:120px;"></th>
                    <?php } ?>
                    <!--<th class="noSort">QA Status</th>
                    <th class="noSort">Download Input</th>
                    <th class="noSort">Download Output</th>
                    <th class="noSort">Refresh Status</th> -->
                    <!-- <th class="noSort"></th> -->
                </tr>
            </thead>
            <tbody>
                <?php /*foreach($allHistory as $key=>$value){ ?>
                <tr>
                    <td><?php echo$key+1;?></td>
                    <?php if($value->type=='single'){
                              if($value->original_pdf_file_name==""){
                                    $original_pdf_file_name = $value->file_name; 
                                    
                                }else{
                                    $original_pdf_file_name = $value->original_pdf_file_name;
                                } ?>
                                <td><a href="<?php echo $this->config->item('assets')?>uploads/bank_statement/<?php echo $value->file_name; ?>" title="Download" target="_blank"><?php echo substr($original_pdf_file_name,0,20);?></a></td>
                                
                    <?php }else if($value->type=='multiple'){ 
                        ?>
                        <td><a href="<?php echo $this->config->item('assets')?>uploads/bulk_upload/<?php echo $value->folder_name; ?>/<?php echo $value->original_pdf_file_name; ?>" title="Download" target="_blank"><?php echo substr($value->original_pdf_file_name,0,20);?></a></td>
                    <?php } ?>
                    
                    <td><?php echo$value->bank_name;?></td>
                    <td><?php echo$value->created_on;?></td>
                    <td></td>
                    <td>1</td>
                    <td><?php echo$value->type;?></td>
                    <td>
                    <?php if($value->downloaded_file_name!="" && $value->type=='single'){ ?>
                        <a href="<?php echo $this->config->item('assets')?>uploads/bank_statement_excel/<?php echo $value->downloaded_file_name; ?>" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>
                    <?php }else if($value->type=='multiple'){ 
                        if($value->status==0){
                            echo"Ready for execution";
                        }else if($value->status==1){
                            echo"In progress";
                        }else if($value->status==2){ ?>
                            <a href="<?php echo $this->config->item('assets')?>uploads/bulk_upload/<?php echo $value->folder_name; ?>/<?php echo $value->folder_name; ?>.zip" title="Download"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"/><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"/></svg></a>
                     <?php } ?>
                    
                    <?php }?>
                    </td>
                    <td><button>Add</button></td>
                </tr>
                <?php }*/ ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
<style>
    .btn-assign{
        padding: 10px;
        background: #006fcf;
        border: none;
        color: #ffffff;
        line-height: 12px;
        font-size: 12px;
        border-radius: 4px;
        outline: none;
        box-shadow: none;
    }
</style>
<script>
function downloadExcel(tpl_history_id){
	var url = siteurl+'Bank_statement/createExcel';
	var data_json = {
			"tpl_history_id":tpl_history_id,
	}
	$.post(url,data_json, function(response) {
		
	}, 'json');
}

var dataTable = $('#datatable').DataTable({
    "scrollX": true,
    "language": {
        paginate: {
          next: '<img src="./assets/images/arrow-right.svg">',
          previous: '<img src="./assets/images/arrow-left.svg">',
        },
        "infoFiltered": ""
      },
      "columnDefs": [
        { "width": "40px", "targets": 0 },
        { "width": "130px", "targets": 1 },
        { "width": "200px", "targets": 2 },
        { "width": "80px", "targets": 3 },
        { "width": "110px", "targets": 4 },
        { "width": "110px", "targets": 5 },
        // { "width": "100px", "targets": 6 },
        // { "width": "60px", "targets": 7 },
        // { "width": "100px", "targets": 8 },
        // { "width": "50px", "targets": 9 },
    ],
      // Processing indicator
      "processing": true,
      // DataTables server-side processing mode
      "serverSide": true,
      // Initial no order.
      "order": [],
      // Load data from an Ajax source
      "ajax": {
          "url": "<?php echo base_url('fs_dashboard/fetch_template_detail'); ?>",
          "type": "POST",
		  "data": function(data){
              // Read values for filters
              var assigned_filter = $('#assigned_filter').val();
              var workflow_filter = $('#workflow_filter').val();
              data.assigned_filter = assigned_filter;
              data.workflow_filter = workflow_filter;
           }
      },
      
     
});
//workflow status
$(function () {
    $("#datatable_filter").append("<select class='form-control filter_select' id='workflow_filter' onchange='filterData1();'><option value='All'>All</option><option value='New'>New</option><option value='Accepted'>Accepted</option><option value='Rejected'>Rejected</option><option value='System-Fail'>System-Fail</option><option value='2nd Review'>2nd Review</option></select>");
})

function filterData1(){
  dataTable.draw();
}

//Filter Add
$(function () {
    $("#datatable_filter").append("<select class='form-control filter_select' id='assigned_filter' onchange='filterData();'><option value='All'>All</option><option value='Assigned to me'>Assigned to me</option><option value='Unassigned'>Unassigned</option></select>");
})

function filterData(){
  dataTable.draw();
}

function validations(){
    var analyst_id = $("#selectAnalystId").val();
    if(analyst_id==-1){
        alert("Please select analyst");
         preventDefault();
        return false;
    }else{
        $('#assignAnalyst').modal('hide');
        $.ajax({
           type: "POST",
           url: "<?php echo base_url('fs_dashboard/fetch_template_detail'); ?>",
           data:  {}, 
           success: function(data)
           {
            dataTable.ajax.reload();   /// reloads the table
           }
       });
    }
}

var selected_array = [];
var lastChecked = null;
$(document).on('click', '.chkbox', function (e) {
   //    $("#selected_rows").empty();
   if (!lastChecked) {
         lastChecked = this;
         //return;
   }
   if (e.shiftKey) {
         var start = $('.chkbox').index(this);
         var end = $('.chkbox').index(lastChecked);
         $('.chkbox').slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', lastChecked.checked);
         for (var i = end; i <= start; i++) {
            if (selected_array.indexOf($('.chkbox')[i].value) == -1) {
                selected_array.push($('.chkbox')[i].value);
            }
        }
    } else {
         var check_value = $(this).val();
         console.log(check_value)
         if (selected_array.indexOf(check_value) == -1) {
             selected_array.push(check_value);
         } else {
             var index = selected_array.indexOf(check_value);
             selected_array.splice(index, 1);
         }
    }
    if (selected_array.length > 0) {
        console.log(selected_array);
        
    }
    lastChecked = this;
 });
 $(document).on('click', '#assign_to_analyst', function () {
   if(selected_array.length>0){
        $('#assignAnalyst').modal('show');
        $('#case_ids').val(selected_array);
   }else{
       alert("Please select cases.");
   }
 })
$(document).on('click', '.assigned_to_me', function () {
    var history_id = $(this).attr('data-id');
    var tdObj = $(this);
    
    //return true;
    var surl = siteurl+'Fs_dashboard/assigned_case?history_id='+history_id;
    $.getJSON(surl,function(response){
        if (response.success) {
            if(response.assigned) {
            	$("#assigend_"+history_id).hide();
            	tdObj.closest('tr').find('td:nth-child(2)').html('<a href="<?php echo base_url('fs-excelsheet/')?>'+response.history_id+'">'+tdObj.closest('tr').find('td:nth-child(2)').text()+'</a>');
            	//alert(tdObj.closest('tr').find('td:nth-child(2)').text());
            	tdObj.closest('td').text(response.name);
            	//tdObj.find('td:first-child').text(response.name);
            	//alert(tdObj.closest('tr').find('td:second-child').text());
            	//console.log(tdObj.closest('tr'));
            }else{
            	alert(response.html);  
            }   
        }        
    }); 
    return false;  
});


</script>