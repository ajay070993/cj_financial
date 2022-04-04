<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="utf-8"/>
<title><?php echo getSiteOption('site_title',true); ?></title>
<link rel="icon" href="<?php echo $this->config->item('admin_assets'); ?>global/img/sport_fav.png" sizes="192x192" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>

<?php $this->load->view('admin/includes/common'); ?>

<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo $this->config->item('admin_assets'); ?>global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('admin_assets'); ?>admin/pages/css/login-soft.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo $this->config->item('admin_assets'); ?>global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('admin_assets'); ?>global/css/plugins-md.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('admin_assets'); ?>admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?php echo $this->config->item('admin_assets'); ?>admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('admin_assets'); ?>admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-md login">
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="<?php echo site_url('admin'); ?>">
			<!-- <img src="http://dummyimage.com/120x50/080208/e00000&text=LOGO+HERE" alt="logo" class="logo-default"/> -->
	<img src="<?php echo $this->config->item('admin_assets'); ?>global/img/sport_logo.png" alt="logo" class="logo-default"/>
	</a>
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="forgot-form ajax_form" action="" method="post">
		<h3 class="form-title">Get new password ?</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">New password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Enter new password" name="new_password"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Re-enter password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re enter password" name="confirm_password"/>
			</div>
		</div>			
		<div class="form-actions">			
			<button type="submit" class="btn blue pull-right">
			Get a new password <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
		
		<div class="forget-password">
			<span>Click <a style="color: #fff;" href="<?php echo site_url('admin/login'); ?>" id="forget-password">
				here </a> to go login page</span>
		</div>
		
	</form>
	<!-- END LOGIN FORM -->
		
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 <?php echo getSiteOption('copyright_text',true); ?>	
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo $this->config->item('admin_assets'); ?>global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('admin_assets'); ?>global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $this->config->item('admin_assets'); ?>global/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $this->config->item('admin_assets'); ?>global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('admin_assets'); ?>admin/layout/scripts/layout.js" type="text/javascript"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {     
  Metronic.init(); // init metronic core components
Layout.init(); // init current layout
  
       // init background slide images
       $.backstretch([
        
        "<?php echo $this->config->item('admin_assets'); ?>admin/pages/media/bg/1.jpg",   
        "<?php echo $this->config->item('admin_assets'); ?>admin/pages/media/bg/2.jpg",
        "<?php echo $this->config->item('admin_assets'); ?>admin/pages/media/bg/3.jpg",
        "<?php echo $this->config->item('admin_assets'); ?>admin/pages/media/bg/4.jpg",
        "<?php echo $this->config->item('admin_assets'); ?>admin/pages/media/bg/5.jpg"
        ], {
          fade: 1000,
          duration: 2000
    }
    );
});
</script>
<!-- END JAVASCRIPTS -->

</body>

<!-- END BODY -->

<!-- Mirrored from www.keenthemes.com/preview/metronic/theme/templates/admin4_material_design/login_soft.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Jul 2015 09:17:02 GMT -->
</html>