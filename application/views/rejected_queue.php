<?php $currentPage='rejected_queue';
      include('header.php'); ?>
<?php include('navigation.php'); ?>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div class="scrollContainer">

        <table id="datatable" class="table history">
            <thead>
                <tr>
                    <th class="noSort">No.</th>
                    <th class="noSort">Case ID</th>
                    <th class="noSort">Customer Name</th>
                    <th class="noSort">Case Type</th>
                    <!-- <th class="noSort">Bank Name</th> -->
                    <th class="noSort">Upload Date</th>
                    <th class="noSort">Uploaded By</th>
                    <th class="noSort">Spreading Status</th>
                    <th class="noSort">Workflow Status</th>
                    <!--<th class="noSort">QA Status</th>
                    <th class="noSort">Download Input</th>
                    <th class="noSort">Download Output</th>
                    <th class="noSort">Refresh Status</th> -->
                </tr>
            </thead>
            <tbody>
               <?php /*?><tr>
                    <td>1</td>
                    <td><a href="#">213523554</a></td>
                    <td>Native</td>
                    <td>American Bank</td>
                    <td>John Doe</td>
                    <td>12/10/2020</td>
                    <td>Admin</td>
                    <td>Done</td>
                    <td>Done</td>
                    <!--<td>Done</td>
                    <td>
                        <a href="#" title="Download Input"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>
                    </td>
                    <td>
                        <a href="#" title="Download Output"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>
                    </td>-->
                    <td>
                        <a href="#" title="Upload"><svg style="width: 24px;fill: #006FCF;stroke: #006FCF;stroke-width: 0.5px;transform:rotate(180deg)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.596 25.596"><path class="a" d="M25.35 12.798A12.548 12.548 0 1012.798 25.35 12.563 12.563 0 0025.35 12.798zm-23.843 0a11.294 11.294 0 1111.291 11.294A11.307 11.307 0 011.504 12.798z"></path><path class="a" d="M13.243 18.694l3.526-3.526a.626.626 0 00-.885-.885l-2.456 2.456V7.348a.63.63 0 10-1.259 0v9.386l-2.456-2.456a.626.626 0 00-.885.885l3.526 3.526a.636.636 0 00.89.005z"></path></svg></a>
                    </td>
                    <!--<td>
                        <a href="javascript:void(0)">Refresh</a>
                    </td>-->
               </tr><?php */ ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
<script>

$('#datatable').DataTable({
    "scrollX": true,
    "language": {
        paginate: {
          next: '<img src="./assets/images/arrow-right.svg">',
          previous: '<img src="./assets/images/arrow-left.svg">',
        },
        "infoFiltered": ""
      },
      "columnDefs": [
        { "width": "30px", "targets": 0 },
        { "width": "80px", "targets": 1 },
        { "width": "250px", "targets": 2 },
        { "width": "60px", "targets": 3 },
        { "width": "120px", "targets": 4 },
        { "width": "100px", "targets": 5 },
        { "width": "100px", "targets": 6 },
        { "width": "100px", "targets": 7 },
        // { "width": "150px", "targets": 8 }
        ],
      // Processing indicator
      "processing": true,
      // DataTables server-side processing mode
      "serverSide": true,
      // Initial no order.
      "order": [],
      // Load data from an Ajax source
      "ajax": {
          "url": "<?php echo base_url('Rejected_queue/fetch_template_detail'); ?>",
          "type": "POST"
      },
      
     
});

</script>