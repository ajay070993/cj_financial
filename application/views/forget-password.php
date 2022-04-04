
<?php include('header.php'); ?>

<section style="width: 100%">
  <div class="loginContainer">
    <a href="<?php echo site_url(); ?>" class="logo"><img src="<?php echo $this->config->item('assets'); ?>images/logo.png" class="img-fluid"></a>
    <div class="loginForm" id="loginForm">
      <h1>Reset Password</h1>
      <form class="ajax_form" action="<?php echo site_url('update-new-password'); ?>" method="post">
        <div class="form-group">
          <label for="email">Password</label>
          <div class="inputBox">
            <input type="text" class="form-control" name="password" id="password" placeholder="Enter your password" autocomplete="new-password">
            <span class="borderAni"></span>
          </div>
        </div>

        <div class="form-group">
          <label for="pwa">Confirm password</label>
          <div class="inputBox">
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter your confirm password" autocomplete="new-password">
            <input type="hidden" class="form-control" name="forgot_password_key" value="<?php echo$forgot_password_key;?>">
            <span class="borderAni"></span>
            
          </div>
        </div>
        <div class="g-recaptcha" data-sitekey="6Lc-qfUUAAAAAGwQn-tpdi2p_aLt9nZbM2A9_0OY"></div>
        <div class="btn_container">
          <button type="submit" class="btn btn-primary" style="width: auto;padding: 11px 18px;">Update Password</button>
        </div>
		
      </form>
    </div>
    <div class="img"></div>

    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti2.png" class="gra1">
    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti1.png" class="gra2">
    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti3.png" class="gra3">
  </div>
</section>

<?php include('footer.php'); ?>