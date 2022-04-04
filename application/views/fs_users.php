<?php   $currentPage='users';
        include('header.php'); ?>
<?php include('navigation.php'); 
$roles = fs_getUserRole();
?>
<style>
    .dataTables_wrapper table.dataTable td{
        padding-right: 30px;
    }
    .dataTables_wrapper table.dataTable td svg{
        cursor: pointer;
    }
    .dataTables_wrapper table.dataTable td .delete{
        fill: #F44336;
        stroke: #F44336;
    }
    .dataTables_wrapper table.dataTable td.active{
        color: #4CAF50;
        font-weight: 600;
    }
    .dataTables_wrapper table.dataTable td.inactive{
        color: #F44336;
        font-weight: 600;
    }
    .customModal .modal-dialog .modal-content .modal-body .updateContainer{
        justify-content: center;
        margin-top: 34px;
    }
    .modal-header{
        justify-content: center;
        padding: 14px;
    }
    .modal-header h5{
        text-align: center;
        font-size: 18px;
        margin: 0;
    }
    .modal-body .changePassword{
        display: flex;
        align-items: center;
    }
    .modal-body .changePassword input{
        width: 48%;
        margin-right: 26px;
    }
    .modal-body .changePassword p{
        color: #006FCF!important;
        font-weight: 500;
        margin: 0!important;
        cursor: pointer;
        font-size: 12px!important;
        padding: 6px 0;
    }
</style>
<div class="main <?php if($this->session->userdata('data-type-collapse') == 0) echo 'mainSmall'; ?>">
    <?php include('topbar.php'); ?>
    <div class="scrollContainer">
        <table id="datatable" class="table history" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>User Role</th>
                    <th>Status</th>
                    <th class="noSort"></th>
                    <th class="noSort"></th>
                </tr>
            </thead>
            <tbody>
                <?php /*foreach($users as $key=>$user){ ?>
                <tr>
                    <td><?php echo$key+1;?></td>
                    <td><?php echo $user->first_name.' '.$user->last_name;?></td>
                    <td><?php echo $user->email;?></td>
                    <td><?php echo $user->gender;?></td>
                    <td><?php echo $user->type;?></td>
                    <?php if($user->status=='Active'){ ?>
                    	<td class="active">Active</td>
                	<?php }else if($user->status=='Inactive'){ ?>
                    	<td class="inactive">Inactive</td>
                	<?php } ?>
                    <td onclick="editUser('<?php echo$user->id;?>')"><svg data-toggle="modal" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.119 22"><path class="a" d="M21.484 13.373a.58.58 0 00-.58.58v5.148a1.741 1.741 0 01-1.739 1.739H2.9a1.741 1.741 0 01-1.739-1.739V3.994A1.741 1.741 0 012.9 2.254h5.146a.58.58 0 100-1.159H2.9a2.9 2.9 0 00-2.9 2.9v15.106a2.9 2.9 0 002.9 2.9h16.265a2.9 2.9 0 002.9-2.9v-5.148a.58.58 0 00-.581-.58zm0 0"></path><path class="a" d="M9.065 10.323l8.465-8.465 2.73 2.73-8.462 8.465zm0 0M7.684 14.434l3.017-.836-2.181-2.181zm0 0M21.014.423a1.451 1.451 0 00-2.05 0l-.615.615 2.73 2.73.615-.615a1.451 1.451 0 000-2.05zm0 0"></path></svg></td>
                    <td onclick="deleteUser('<?php echo$user->id;?>')" title="delete"><svg class="delete" data-toggle="modal" viewBox="-40 0 427 427.00131" xmlns="http://www.w3.org/2000/svg"><path d="m232.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/><path d="m114.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/><path d="m28.398438 127.121094v246.378906c0 14.5625 5.339843 28.238281 14.667968 38.050781 9.285156 9.839844 22.207032 15.425781 35.730469 15.449219h189.203125c13.527344-.023438 26.449219-5.609375 35.730469-15.449219 9.328125-9.8125 14.667969-23.488281 14.667969-38.050781v-246.378906c18.542968-4.921875 30.558593-22.835938 28.078124-41.863282-2.484374-19.023437-18.691406-33.253906-37.878906-33.257812h-51.199218v-12.5c.058593-10.511719-4.097657-20.605469-11.539063-28.03125-7.441406-7.421875-17.550781-11.5546875-28.0625-11.46875h-88.796875c-10.511719-.0859375-20.621094 4.046875-28.0625 11.46875-7.441406 7.425781-11.597656 17.519531-11.539062 28.03125v12.5h-51.199219c-19.1875.003906-35.394531 14.234375-37.878907 33.257812-2.480468 19.027344 9.535157 36.941407 28.078126 41.863282zm239.601562 279.878906h-189.203125c-17.097656 0-30.398437-14.6875-30.398437-33.5v-245.5h250v245.5c0 18.8125-13.300782 33.5-30.398438 33.5zm-158.601562-367.5c-.066407-5.207031 1.980468-10.21875 5.675781-13.894531 3.691406-3.675781 8.714843-5.695313 13.925781-5.605469h88.796875c5.210937-.089844 10.234375 1.929688 13.925781 5.605469 3.695313 3.671875 5.742188 8.6875 5.675782 13.894531v12.5h-128zm-71.199219 32.5h270.398437c9.941406 0 18 8.058594 18 18s-8.058594 18-18 18h-270.398437c-9.941407 0-18-8.058594-18-18s8.058593-18 18-18zm0 0"/><path d="m173.398438 154.703125c-5.523438 0-10 4.476563-10 10v189c0 5.519531 4.476562 10 10 10 5.523437 0 10-4.480469 10-10v-189c0-5.523437-4.476563-10-10-10zm0 0"/></svg></td>
                </tr>
                <?php }*/ ?>
            </tbody>
        </table>
    </div>
</div>

<!-- The Modal -->

<div class="modal fade customModal" id="addUser">
    <form class="form-signin ajax_form form-fields" id="ajax_form" action="<?php echo site_url('Fs_users/addUser'); ?>" method="post">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 660px;margin-top: unset">
      <div class="modal-content">
          <div class="modal-header">
            <h5>Add New User</h5>
          </div>
        <!-- Modal body -->
        <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="fname" class="form-control" id="fname">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lname" class="form-control" id="lname">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" id="email">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Password Expire</label>
                            <select name="expire_pwd" class="form-control">
                                <option value="0">None</option>
                                <option value="1">1 Month</option>
                                <option value="2">2 Month</option>
                                <option value="3">3 Month</option>
                                <option value="6">6 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control newGenPass togglePassword" id="password">
                                <div class="input-group-append">
                                    <span class="input-group-text fa fa-eye toggle-password" style="cursor: pointer;padding:0 0.75rem"></span>
                                </div>
                            </div>
                            <p class="auto getNewPass">Auto Generate</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>User role</label>
                            <select name="user_role" class="form-control" id="user_role">
                            <option>Select role</option>
                            <?php foreach($roles as $key=>$value){ ?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group toggleRadioContainer">
                            <label>Gender</label>
                            <div class="toggleRadio" id="gender">
                                <label class="radioBox">
                                    <input type="radio" checked="checked" name="gender" value="Male">
                                    <span>Male</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="gender" value="Female">
                                    <span>Female</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
                        <div class="form-group checkBoxes_container">
                            <label>Excel Output Format</label>
                            <div class="toggleRadio" id="format">
                                <label class="radioBox">
                                    <input type="radio" checked="checked" name="xls_format" value="1">
                                    <span>Old Format</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="xls_format" value="2">
                                    <span>New Format</span>
                                </label>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-sm-12">
                        <div class="form-group toggleRadioContainer">
                            <label>Status</label>
                            <div class="toggleRadio" id="status">
                                <label class="radioBox">
                                    <input type="radio" checked="checked" name="status" value="Active">
                                    <span>Active</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="status" value="Inactive">
                                    <span>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="updateContainer">
                    <button>Create</button>
                    <button data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
        </div>
      </div>
    </div>
    </form>
</div>

<div class="modal fade customModal" id="editUser">
	<form class="form-signin ajax_form form-fields" id="ajax_form" action="<?php echo site_url('fs-update-user'); ?>" method="post" autocomplete="off">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 660px;margin-top: unset">
      <div class="modal-content">
          <div class="modal-header">
            <h5>Edit User</h5>
          </div>
        <!-- Modal body -->
        <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="fname" class="form-control" id="first_name">
                            <input type="hidden" name="edit_id" class="form-control" id="edit_id">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lname" class="form-control" id="last_name">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" id="email_id" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Password Expire</label>
                            <select class="form-control">
                                <option>None</option>
                                <option>1 Month</option>
                                <option>2 Month</option>
                                <option>3 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>User role</label>
                            <select name="user_role" class="form-control" id="e_user_role">
                            <option>Select role</option>
                            <?php foreach($roles as $key=>$value){ ?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="changePassword">
                                <!-- <input type="password" name="password" class="form-control" id="password"> -->
                                <p data-toggle="modal" onclick="change_password()">Reset Password</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group toggleRadioContainer">
                            <label>Gender</label>
                            <div class="toggleRadio" id="gender">
                                <label class="radioBox">
                                    <input type="radio" name="gender" value="male" id="edit_male">
                                    <span>Male</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="gender" value="female" id="edit_female">
                                    <span>Female</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
                        <div class="form-group checkBoxes_container">
                            <label>Excel Output Format</label>
                            <div class="toggleRadio" id="format">
                                <label class="radioBox">
                                    <input type="radio" name="xls_format" value="1" id="old_format">
                                    <span>Old Format</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="xls_format" value="2" id="new_format">
                                    <span>New Format</span>
                                </label>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-sm-12">
                        <div class="form-group toggleRadioContainer">
                            <label>Status</label>
                            <div class="toggleRadio" id="status">
                                <label class="radioBox">
                                    <input type="radio" name="status" value="Active" id="edit_active">
                                    <span>Active</span>
                                </label>
                                <label class="radioBox">
                                    <input type="radio" name="status" value="Inactive" id="edit_inactive">
                                    <span>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="updateContainer">
                    <button>Update</button>
                    <button data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            
        </div>
      </div>
    </div>
    </form>
</div>


<div class="modal fade customModal changePass" id="changePassword">

    <div class="modal-backdrop" style="opacity: 0.4;"></div>
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: unset;z-index: 2000;">
      <div class="modal-content">
        <div class="modal-header">
            <h5>Reset Password</h5>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <form class="form-signin ajax_form form-fields" id="ajax_form" action="<?php echo site_url('fs-update-password'); ?>" method="post" autocomplete="off">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_pass" class="form-control newGenPass togglePassword">
                                <input type="hidden" name="p_edit_id" class="form-control" id="p_edit_id">
                                <div class="input-group-append">
                                    <span class="input-group-text fa fa-eye toggle-password" style="cursor: pointer;padding: 3px 10px;"></span>
                                </div>
                            </div>
                            <p class="auto getNewPass">Auto Generate</p>
                            <!-- <div class="checkBox">
                                <label class="custom_checkBox">Email New Password to User
                                    <input type="checkbox" checked="checked" name="email_me">
                                    <span class="checkmark"></span>
                                </label>
                            </div> -->
                        </div>
                    </div>
                </div>
            
                <div class="updateContainer" style="margin-top: 16px;">
                    <button>Reset</button>
                    <button data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>


<div class="modal fade customModal" id="confirmDelete">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: unset;">
      <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body">
        <form class="form-signin ajax_form form-fields" id="ajax_form" action="<?php echo site_url('fs-delete-user'); ?>" method="post" autocomplete="off">
            <p style="margin: 14px 0 -10px;">Are you sure?</p>
            <input type="hidden" name="d_edit_id" class="form-control" id="d_edit_id">
            <div class="updateContainer">
                <button style="background: #F44336;">Delete</button>
                <button data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
         </form>   
        </div>
      </div>
    </div>
</div>

<div class="modal fade customModal" id="confirmActive">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: unset;">
      <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body">
            <p style="margin: 14px 0 -10px;">Are you sure?</p>
            <div class="updateContainer">
                <button style="background: #4CAF50;">Reactivate</button>
                <button data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
        </div>
      </div>
    </div>
</div>
<!-- End pop up code -->
<?php include('footer.php'); ?>
<script>
$(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $(".togglePassword");
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$("#gender :input").change(function(){
  console.log($(this).val());
  console.log($(this).is(':checked'));
  console.log($(this).prop("id"));
});

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
          "url": "<?php echo base_url('Fs_users/fetch_users_detail'); ?>",
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
$(document).ready(function(){
    $(".dataTables_filter").append('<a href="javascript:void(0)" data-toggle="modal" class="createTemplate" id="add-user">Add User</a>');
})

function callBackUserList(){
	$('#addUser').modal('toggle');
	location.reload(true);
}

function callBackUsers(){
	$('#editUser').modal('toggle');
	location.reload(true);
}

function callBackEditUser(){
	$('#changePassword').modal('toggle');
}

function callBackDeleteUser(){
	$('#confirmDelete').modal('toggle');
	location.reload(true);
}
$(document).ready(function() {
    $("#add-user").click(function(){
    	$("#fname").val('');
    	$("#lname").val('');
    	$("#email").val('');
    	$("#password").val('');
    	$('#addUser').modal('show');
    });
});

function editUser(id){
	var url = siteurl+'/fs-edit-user';
    console.log(url);
	var data_json = {
			"id":id,
	}
	$.post(url,data_json, function(response) {
		console.log(response);
		$("#edit_id").val(id);
		$("#first_name").val(response.first_name);
		$("#last_name").val(response.last_name);
		$("#email_id").val(response.email);
		$('#e_user_role').val(response.user_role);
		if(response.gender=='Male'){
			$("#edit_male").prop("checked", true);
		}else if(response.gender=='Female'){
			$("#edit_female").prop("checked", true);
		}

		if(response.type==1){
			$("#old_format").prop("checked", true);
		}else if(response.type==2){
			$("#new_format").prop("checked", true);
		}

		if(response.status=='Active'){
			$("#edit_active").prop("checked", true);
		}else if(response.status=='Inactive'){
			$("#edit_inactive").prop("checked", true);
		}
		
		$('#editUser').modal('show');
	}, 'json');
	
}

function deleteUser(id){
	$("#d_edit_id").val(id);
	$('#confirmDelete').modal('show');
	
}

 function change_password(){
	$('#changePassword').modal('toggle');
	$('#p_edit_id').val($("#edit_id").val());
 }


 $(".getNewPass").click(function(){
  	$('.newGenPass').val(randString());
});
	
 function randString(){
	  var dataSet = 'a-z,A-Z,0-9,#'.split(',');  
	  var possible = '';
	  if($.inArray('a-z', dataSet) >= 0){
	    possible += 'abcdefghijklmnopqrstuvwxyz';
	  }
	  if($.inArray('A-Z', dataSet) >= 0){
	    possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  }
	  if($.inArray('0-9', dataSet) >= 0){
	    possible += '0123456789';
	  }
	  if($.inArray('#', dataSet) >= 0){
	    possible += '![]{}()%&*$#^<>~@|';
	  }
	  var text = '';
	  for(var i=0; i < 32; i++) {
	    text += possible.charAt(Math.floor(Math.random() * possible.length));
	  }
	  return text;
	}
</script>