 
    <div style="display: flex; justify-content: flex-end;">  
        <div class="card" style="background-color: #ffffff; width: 300px;height: 300px; padding: 25px;  ">
            <div class="card-body p30 rounded-bottom">
                <!-- Sign in text -->
                <h2 class="text-center mb-4" style="width:10%;margin-top:25%;margin-left:-60%;">  <img src="https://efactor.tech/files/home/logo.jpg"></h2>

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

                <div class="form-group mb-3" style="width: 110%; margin-left:-55%;margin-top:20%">
                 <div style="position: relative;display:flex;">
                <?php
echo form_input(array(
    "id" => "email",
    "name" => "email",
    "class" => "form-control form-control-lg",
    "placeholder" => app_lang('email'),
    "autofocus" => true,
    "data-rule-required" => true,
    "data-msg-required" => app_lang("field_required"),
    "data-rule-email" => true,
    "data-msg-email" => app_lang("enter_valid_email"),
    "style" => "height: 35px; border: none; border-bottom: 2px solid black; width: 100%; background-color: white; font-size: 16px; outline: none;"
));
?>
                    <img style="width:23px;height:18px;position:absolute;z-index:2;margin-left:86%;padding-top:8px" src="https://efactor.tech/files/home/email.png" id="mail">
                     </div>
                </div>

                <!-- Add empty div for gap -->
                <div style="height: 25px;"></div>

                <div class="form-group mb-3" style="width: 110%; margin-left:-55%">
                <div style="position: relative;display:flex;">
                   <?php
echo form_password(array(
    "id" => "password",
    "name" => "password",
    "class" => "form-control form-control-lg",
    "placeholder" => app_lang('password'),
    "data-rule-required" => true,
    "data-msg-required" => app_lang("field_required"),
    "style" => "height: 35px; border: none; border-bottom: 2px solid black; width: 100%; background: white; font-size: 16px; outline: none;"
));
?>
                     <img style="width:25px;height:px;position:absolute;z-index:2;margin-left:85%;padding-top:8px" src="https://efactor.tech/files/home/eye.png" id="eyeo">
                      
                     <script>
        let eyeo = document.getElementById("eyeo");
        let password = document.getElementById("password");

        eyeo.onclick = function () {
            if (password.type == "password") {
                password.type = "text"
                eyeo.src = "https://efactor.tech/files/home/eyeon.png"
            } else {
                password.type = "password"
                eyeo.src = "https://efactor.tech/files/home/eye.png"
            }
        }
    </script>
                </div>
                </div>
            </div>
            <input type="hidden" name="redirect" value="<?php if (isset($redirect)) { echo $redirect; } ?>" />

            <?php echo view("signin/re_captcha"); ?>

            <button class="w-100 btn btn-lg" style="background-color:#DD571C; border-radius: 15px;border:none;font-size:25px; color: white; margin-top:60px;width:110%; height:40px; margin-left:-100%" type="submit"><?php echo app_lang('signin'); ?></button>

            <?php echo form_close(); ?>
            <!--div class="mt5" style="margin-top:10px;font-size:20px"><?php echo anchor("signin/request_reset_password", app_lang("forgot_password")); ?></div >


            <?php if (!get_setting("disable_client_signup")) { ?>
                <div class="mt20" style="margin-top:10px;font-size:15px"><?php echo app_lang("you_dont_have_an_account") ?> &nbsp;
                    <?php
                    //echo anchor("signup", app_lang("signup")); 
                    ?>
                </div-->
            <?php } ?>
        </div>
    </div>
 
