  <style>
  .button-container {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    margin-left:-63%;
}
 
.button { 
    padding: 1px 20px; 
    background: #f0f0f0;
    cursor: pointer; 
    transition: background 0.3s, color 0.3s; 
} 
 
  
.button.selected { 
      
    color: #fff;
    border: 2px solid #DD571C;
    cursor: default;  
} 
 
  
    </style>
   <div style="display: flex; justify-content: flex-end;">  
    <div class="card" style="background-color: #ffffff; width: 300px;height: 300px; padding: 25px;">
        <div class="card-body p30 rounded-bottom">
          
            <h2 class="text-center mb-4" style="width:10%;margin-top:30%;margin-left:-70%;">  
                <img src="https://efactor.tech/files/home/logo.jpg">
            </h2>

            <?php echo form_open("signin/authenticate", array("id" => "signin-form", "class" => "general-form", "role" => "form", "onsubmit" => "return validateSelection()")); ?>

<div id="selected-user-type" style="font-size: 18px; margin-bottom: 20px;margin-left:-120%;margin-top: -20px">
    Please select a user type
</div>

<div class="button-container"> 
    <div class="button" onclick="selectButton(this)" title="User">
        <img style="width:30px;height:40px" src="https://efactor.tech/files/home/person.png">
    </div>
    <div class="button" onclick="selectButton(this)" title="Brand">
        <img style="width:45px;height:40px;padding-top:4px" src="https://efactor.tech/files/home/client.png">
    </div>
    <div class="button" onclick="selectButton(this)" title="Developer">
        <img style="width:40px;height:35px;padding-top:4px" src="https://efactor.tech/files/home/devloper.png">
    </div>
    <div class="button" onclick="selectButton(this)" title="Manufacturer">
        <img style="width:40px;height:40px;padding-top:4px" src="https://efactor.tech/files/home/manufacturer.png">
    </div> 
</div>

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

<div class="form-group mb-3" style="width: 110%; margin-left:-65%;margin-top:20%">
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
        <img style="width:30px;height:25px;position:absolute;z-index:2;margin-left:86%;padding-top:8px" src="https://efactor.tech/files/home/email.png" id="mail">
    </div>
</div>

<div style="height: 25px;"></div>

<div class="form-group mb-3" style="width: 110%; margin-left:-65%">
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
        <img style="width:25px;height:25px;position:absolute;z-index:2;margin-left:85%;padding-top:8px" src="https://efactor.tech/files/home/eye.png" id="eyeo">
    </div>
</div>

<input type="hidden" id="user_type" name="user_type" value="">

<?php echo view("signin/re_captcha"); ?>

<button class="w-100 btn btn-lg" style="background-color:#DD571C;border:none;font-size:25px; color: white; margin-top:60px;width:110%; height:40px; margin-left:-120%" type="submit">
    <?php echo app_lang('signin'); ?>
</button>

<?php echo form_close(); ?>

<script> 
function selectButton(button) { 
    document.querySelectorAll('.button').forEach(btn => btn.classList.remove('selected'));
 
    button.classList.add('selected');
 
    const selectedUserType = button.getAttribute('title');
    document.getElementById('user_type').value = selectedUserType;
 
    let displayName = selectedUserType.charAt(0).toUpperCase() + selectedUserType.slice(1); // Capitalize the first letter
    if (selectedUserType === "devloper") {
        displayName = "Developer";  
    }else if (selectedUserType === "staff") {
        displayName = "User";  
    }

    document.getElementById('selected-user-type').innerText = "User Type: " + displayName;
     
    const userTypeText = document.getElementById('selected-user-type');
    userTypeText.style.color = ""; 
     
        let eyeo = document.getElementById("eyeo");
        let password = document.getElementById("password");

        eyeo.onclick = function () {
            if (password.type == "password") {
                password.type = "text";
                eyeo.src = "https://efactor.tech/files/home/eyeon.png";
            } else {
                password.type = "password";
                eyeo.src = "https://efactor.tech/files/home/eye.png";
            }
        }
    
}

function validateSelection() {
    var email = document.getElementById('email').value;
    var userType = document.getElementById('user_type').value;
 
    if (email === 'admin@efactor.io' && userType !== "User"  && userType !== "Brand"  && userType !== "Developer"  && userType !== "Manufacturer" ) { 
        document.getElementById('user_type').value = 'lead'; 
        return true;   
    }

    const userTypeText = document.getElementById('selected-user-type');
    if (!userType) {
        userTypeText.style.color = "red";   
        return false;
    } else {
        userTypeText.style.color = "";   
    }
    return true;
}
</script>
</div>
