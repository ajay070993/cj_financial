<?php
	if ($message) {
		echo '<div class="alert alert-danger">' . $message . '</div>';
	}
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">			

				<div class="row">
					<div class="col-md-6">
						<div class="caption font-red-sunglo">
							<i class="fa fa-file-image"></i>
							<span class="caption-subject bold uppercase">Change password</span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="caption font-red-sunglo">
							<a href="<?php echo base_url('admin'); ?>" class="btn green pull-right">Go Back <i class="fa fa-chevron-circle-left"></i></a>
						</div>
					</div>
				</div>
			</div>			
			<div class="portlet-body form">
				<?php echo form_open(current_url(), array('class' => 'ajax_form'));?>			
					<div class="form-body">						
						<div class="form-group form-md-line-input">
							<input type="current_password" name="current_password" class="form-control" id="form_control_password" placeholder="Enter current password" value="">
							<label for="form_control_password">Current password</label>
						</div>	

						<div class="form-group form-md-line-input">
							<input type="new_password" name="new_password" class="form-control" id="form_control_password" placeholder="Enter new password" value="">
							<label for="form_control_password">New Password</label>
						</div>
						<div class="form-group form-md-line-input">
							<input type="re_new_password" name="re_new_password" class="form-control" id="form_control_password" placeholder="Enter re-enter password" value="">
							<label for="form_control_password">Re-enter Password</label>
						</div>					
					</div>
					<div class="form-actions noborder">
						<button type="submit" class="btn green">Submit</button>
						<a href="<?php echo base_url('admin'); ?>" class="btn default">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>