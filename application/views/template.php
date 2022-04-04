<?php   $currentPage='templates';
        include('header.php'); ?>
<?php include('navigation.php'); ?>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div class="scrollContainer">
        <table id="datatable" class="table history" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>name</th>
                    <th>Created on</th>
                    <th>last modified date</th>
                    <th>uses count</th>
                    <th class="noSort"></th>
                    <th class="noSort"></th>
                </tr>
            </thead>
            <tbody>
                <?php /* foreach($data as $key=>$value){?>
                <tr>
                    <td><?php echo$key+1;?></td>
                    <td><?php echo$value->bank_name;?></td>
                    <td><?php echo$value->created_on;?></td>
                    <td><?php echo$value->updated_on;?></td>
                    <td><?php echo$value->uses_count;?></td>
                    <td><button data-toggle="modal" data-target="#cloneTplModal" data="<?php echo$value->id; ?>" class="clone">Clone</button></td>
                    <td><a href="<?php echo site_url('Templates/editTemplates').'/'.$value->bank_id; ?>" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.119 22"><path class="a" d="M21.484 13.373a.58.58 0 00-.58.58v5.148a1.741 1.741 0 01-1.739 1.739H2.9a1.741 1.741 0 01-1.739-1.739V3.994A1.741 1.741 0 012.9 2.254h5.146a.58.58 0 100-1.159H2.9a2.9 2.9 0 00-2.9 2.9v15.106a2.9 2.9 0 002.9 2.9h16.265a2.9 2.9 0 002.9-2.9v-5.148a.58.58 0 00-.581-.58zm0 0"/><path class="a" d="M9.065 10.323l8.465-8.465 2.73 2.73-8.462 8.465zm0 0M7.684 14.434l3.017-.836-2.181-2.181zm0 0M21.014.423a1.451 1.451 0 00-2.05 0l-.615.615 2.73 2.73.615-.615a1.451 1.451 0 000-2.05zm0 0"/></svg></a></td>
                </tr>
                <?php } */?>
                
            </tbody>
        </table>
    </div>
</div>

<!-- The Modal -->

<div class="modal fade customModal" id="cloneTplModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body">
            <form class="form-signin ajax_form" action="<?php echo site_url('Templates/cloneTemplate'); ?>" method="post" autocomplete="off">
                <div class="form-group">
                    <label>Clone Template</label>
                    <input type="text" name="clone_temp" class="form-control" id="clonetext">
                    <input type="hidden" name="bank_id" id="set_bank_id" class="form-control">
                </div>
                
                <div class="updateContainer">
                    <button>Submit</button>
                    <button data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
  
<!-- End pop up code -->
<?php include('footer.php'); ?>
<script>
$('#datatable').DataTable({
    "scrollX": true,
    "language": {
        paginate: {
          next: '<img src="./assets/images/arrow-right.svg">',
          previous: '<img src="./assets/images/arrow-left.svg">'  
        }
      },
      // Processing indicator
      "processing": true,
      // DataTables server-side processing mode
      "serverSide": true,
      // Initial no order.
      "order": [],
      // Load data from an Ajax source
      "ajax": {
          "url": "<?php echo base_url('Templates/fetch_template_detail'); ?>",
          "type": "POST"
      },
      //Set column definition initialisation properties
      "columnDefs": [{ 
          "targets": [0],
          "orderable": false
      }]
     
});

/*$('#datatable').DataTable({
    "scrollX": true,
    "language": {
        paginate: {
          next: '<img src="./assets/images/arrow-right.svg">',
          previous: '<img src="./assets/images/arrow-left.svg">'  
        }
      }
});*/

function closePopup(data){
	console.log(data.last_id);
	$('#cloneTplModal').modal('toggle');
	window.location.href = "<?php echo site_url('Templates/editTemplates/'); ?>"+data.last_id;
	
}

$(document).ready(function(){
	$(".clone").click(function(){
		$('#set_bank_id').val($(this).attr('data'));
		$('#clonetext').val('');
	})
    $(".dataTables_filter").append('<a href="<?php echo site_url('Templates/createTemplates'); ?>" class="createTemplate">Create Template</a>');
	
})
</script>