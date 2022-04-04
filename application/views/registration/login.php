<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="utf-8"/>
<title>Bank Statement</title>
<!-- <link rel="icon" href="<?php echo $this->config->item('assets'); ?>global/img/sport_fav.png" sizes="192x192" /> -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<?php $this->load->view('includes/common'); ?>
<link href="<?php echo $this->config->item('assets'); ?>css/login-soft.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('assets'); ?>css/components-md.css" id="" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-md login">
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="<?php echo site_url('admin'); ?>"></a>
</div>

<?php
	if ($this->session->flashdata('message')) {
		echo '<div class="alert alert-success">' . $this->session->flashdata('message') . '</div>';
	}
?>

<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->

<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form ajax_form" action="<?php echo site_url('login'); ?>" method="post">
		<h3 class="form-title">Login to your account</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="on" placeholder="Username" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
			</div>
		</div>
		<div class="form-actions">
			<!-- <label class="checkbox">
			<input type="checkbox" name="remember" value="1"/> Remember me </label> -->
			<button type="submit" class="btn blue pull-right">
			Login <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
		
		<!-- <div class="forget-password">
			<h4>Forgot your password ?</h4>
			<p>
				click <a href="<?php echo site_url('forgot-password'); ?>" id="forget-password">
				here </a>
				to reset your password.
			</p>
		</div> -->
		
	</form>
	<!-- END LOGIN FORM -->
	
</div>
<!-- END LOGIN -->


<script src="<?php echo $this->config->item('assets'); ?>js/jquery.backstretch.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('assets'); ?>js/metronic.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('assets'); ?>js/layout.js" type="text/javascript"></script>
<script>
	jQuery(document).ready(function() {     
	  Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	       // init background slide images
	       $.backstretch([
	        
	        "<?php echo $this->config->item('assets'); ?>img/1.jpg",   
	        "<?php echo $this->config->item('assets'); ?>img/2.jpg",
	        "<?php echo $this->config->item('assets'); ?>img/3.jpg",
	        "<?php echo $this->config->item('assets'); ?>img/4.jpg",
	        "<?php echo $this->config->item('assets'); ?>img/5.jpg"
	        ], {
	          fade: 1000,
	          duration: 2000
	    }
	    );
	});
</script>


</body>
</html>