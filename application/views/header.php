<!doctype html>
<html lang="en">
  <head>
    <title><?php if($this->session->userdata('application_type') == "fs"){ echo "CJ Financial Statements"; } else{ echo "Xtract"; } ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo $this->config->item('assets'); ?>images/favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="<?php echo $this->config->item('assets'); ?>css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $this->config->item('assets'); ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('assets'); ?>css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('assets'); ?>css/style.css">
    <link href="<?php echo $this->config->item('assets'); ?>css/toastr.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $this->config->item('assets'); ?>js/jquery-3.4.1.min.js"></script>
    <script src="<?php echo $this->config->item('assets'); ?>js/jquery.form.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->item('assets'); ?>js/common.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->item('assets'); ?>js/toastr.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankCreditDebitTxn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankEndingDailyBalTxn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankAmtLastLineDescTxn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>js/bankAmtBlankLastLineTxn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    	var site_url = '<?php echo base_url(); ?>';
    	var base_url = site_url; 
    	var siteurl = site_url; 
    </script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
  </head>
  <body>

  <!-- <div class="loader_box">
    <div class="loader"></div>
  </div> -->