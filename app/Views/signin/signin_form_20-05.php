<div style="background-color: white; padding: 50px; border-radius: 15px;">

    <div style="display: flex; justify-content: flex-end; border-radius: 15px;"> <!-- Changed to flex-end to align right -->
        <div class="card" style="background-color: #ffffff; width: 350px; border-radius: 15px; border: 1px solid black; padding: 25px;">
            <div class="card-body p30 rounded-bottom">
                <!-- Sign in text -->
                <h2 class="text-center mb-4" style="color: black;"><?php echo app_lang('signin'); ?></h2>

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

                <div class="form-group mb-3" style="width: 100%;">
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
                        "style" => "height: 40px; border-radius: 15px; width: 100%;"
                    ));
                    ?>
                </div>

                <!-- Add empty div for gap -->
                <div style="height: 25px;"></div>

                <div class="form-group mb-3" style="width: 100%;">
                    <?php
                    echo form_password(array(
                        "id" => "password",
                        "name" => "password",
                        "class" => "form-control form-control-lg",
                        "placeholder" => app_lang('password'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "style" => "height: 40px; border-radius: 15px; width: 100%;"
                    ));
                    ?>
                </div>
                <div class="password-toggle">
                    <input type="checkbox" id="show-password-toggle">
                    <label for="show-password-toggle"></label>
                </div>
            </div>
            <input type="hidden" name="redirect" value="<?php if (isset($redirect)) { echo $redirect; } ?>" />

            <?php echo view("signin/re_captcha"); ?>

            <button class="w-100 btn btn-lg" style="background-color: #FFA500; border-radius: 15px; color: white;" type="submit"><?php echo app_lang('signin'); ?></button>

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
</div>
