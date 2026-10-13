<div class="row">
  <div class="col-md-6 offset-md-3">
    <div class="card bg-white mb15 text-right">
      <div class="card-header">
        <h2>Sign in</h2>
      </div>
      <div class="card-body p30 rounded-bottom">
        <form action="signin/authenticate" method="post" id="signin-form">
          <div class="form-group">
            <input type="email" id="email" name="email" class="form-control p10" placeholder="Email" required>
          </div>
          <div class="form-group">
            <input type="password" id="password" name="password" class="form-control p10" placeholder="Password" required>
          </div>
          <input type="hidden" name="redirect" value="<?php echo $redirect ?? ''; ?>">
          <!-- Additional form fields or elements can be added here -->
          
          <!-- ReCAPTCHA integration can be added here -->

          <button type="submit" class="w-100 btn btn-lg btn-primary">Sign in</button>
        </form>
        <div class="mt5"><a href="signin/request_reset_password">Forgot password?</a></div>
        <?php if (!get_setting("disable_client_signup")) { ?>
          <div class="mt20">Don't have an account? <a href="signup">Sign up</a></div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#signin-form").submit(function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      // Additional JavaScript logic for form submission can be added here
      $.ajax({
        type: "POST",
        url: $(this).attr('action'),
        data: formData,
        success: function(response) {
          // Handle successful form submission
        },<div class="card" style="background-color: #3498db;">
    <div class="card-header text-center">
        <!-- Remove the heading below -->
        <!--<h2><?php echo app_lang('signin'); ?></h2>-->
    </div>
    <div class="card-body p30 rounded-bottom">
        <?php echo form_open("signin/authenticate", array("id" => "signin-form", "class" => "general-form", "role" => "form")); ?>

        <?php
        $session = \Config\Services::session();
        $signin_validation_errors = $session->getFlashdata("signin_validation_errors");
        if ($signin_validation_errors && is_array($signin_validation_errors)) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($signin_validation_errors as $validation_error) { ?>
                    <i data-feather="alert-circle" class="icon-16"></i>
                    <?php echo $validation_error; ?>
                    <br />
                <?php } ?>
            </div>
        <?php } ?>
        <div class="form-group">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "class" => "form-control p10",
                "placeholder" => app_lang('email'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
                "data-rule-email" => true,
                "data-msg-email" => app_lang("enter_valid_email")
            ));
            ?>
        </div>
        <div class="form-group">
            <?php
            echo form_password(array(
                "id" => "password",
                "name" => "password",
                "class" => "form-control p10",
                "placeholder" => app_lang('password'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
            <div class="password-toggle">
                <input type="checkbox" id="show-password-toggle">
                <label for="show-password-toggle"></label>
            </div>
        </div>
        <input type="hidden" name="redirect" value="<?php
        if (isset($redirect)) {
            echo $redirect;
        }
        ?>" />


        <?php echo view("signin/re_captcha"); ?>

        <button class="w-100 btn btn-lg" style="background-color: #efeef1;" type="submit"><?php echo app_lang('signin'); ?></button>

        <?php echo form_close(); ?>
        <div class="mt5"><?php echo anchor("signin/request_reset_password", app_lang("forgot_password")); ?></div>

        <?php if (!get_setting("disable_client_signup")) { ?>
            <div class="mt20"><?php echo app_lang("you_dont_have_an_account") ?> &nbsp;
                <?php
                //echo anchor("signup", app_lang("signup")); 
                ?>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#signin-form").appForm({ajaxSubmit: false, isModal: false});

        $("#show-password-toggle").change(function () {
            var passwordField = $("#password");
            var passwordFieldType = passwordField.attr('type');

            // Toggle password visibility
            if (passwordFieldType === 'password') {
                passwordField.attr('type', 'text');
            } else {
                passwordField.attr('type', 'password');
            }
        });
    });
</script>

        error: function(xhr, status, error) {
          // Handle errors during form submission
        }
      });
    });
  });
</script>
