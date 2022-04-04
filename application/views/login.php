<?php include('header.php'); ?>

<section style="width: 100%">
  <div class="loginContainer">
    <a href="<?php echo site_url(); ?>" class="logo"><img style="width:auto" src="<?php echo $this->config->item('assets'); ?>images/logo.png" class="img-fluid"></a>
    <div class="loginForm" id="loginForm">
      <h1>Login</h1>
      <form class="ajax_form" action="<?php echo site_url('login'); ?>" method="post">
        <div class="form-group">
          <label for="email">Email</label>
          <div class="inputBox">
            <input type="text" class="form-control" name="email" id="email" placeholder="Enter your email">
            <span class="borderAni"></span>
          </div>
        </div>

        <div class="form-group">
          <label for="pwa">Password</label>
          <div class="inputBox"  style="position: relative;">
            <input type="password" class="form-control" name="password" id="pwa" placeholder="Enter your password">
            <span class="input-group-text fa fa-eye toggle-password" style="background-color:#0000;border:none;position:absolute;top:0;right:8px;cursor: pointer; padding: .25rem;"></span>
<span class="borderAni"></span>
          </div>
          <span class="borderAni"></span>
        </div>
        <div class="g-recaptcha" data-sitekey="6Lc-qfUUAAAAAGwQn-tpdi2p_aLt9nZbM2A9_0OY"></div>
        <div class="btn_container">
          <button type="submit" class="btn btn-primary">Login</button>
          <a id="fgetPassword" class="link_btn" >Forgot password?</a>
        </div>
      </form>
    </div>
    <div class="loginForm" id="forgotForm" style="display:none">
      <h1>Forgot Password</h1>
      <form class="ajax_form" action="<?php echo site_url('registration/forgotPassword'); ?>" method="post">
        <div class="form-group">
          <label for="email">Email</label>
          <div class="inputBox">
            <input type="text" class="form-control" name="email" id="fgetEmail" placeholder="Enter your email">
            <span class="borderAni"></span>
          </div>
        </div>
        <div class="btn_container">
          <button type="submit" class="btn btn-primary">Submit</button>
          <a id="fgetLogin" class="link_btn" >Login</a>
        </div>
      </form>
    </div>
    <div class="img"></div>

    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti2.png" class="gra1">
    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti1.png" class="gra2">
    <img src="<?php echo $this->config->item('assets'); ?>images/graffiti3.png" class="gra3">
  </div>
</section>
<script>
$("#fgetPassword").click(function() {
  $("#forgotForm").show();
  $("#loginForm").hide();
  return;
});

$("#fgetLogin").click(function() {
  $("#forgotForm").hide();
  $("#loginForm").show();
  return;
});

$(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#pwa");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

//var widgetId = grecaptcha.render(container);
//grecaptcha.reset(widgetId);
</script>
<?php include('footer.php'); ?>