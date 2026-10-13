<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive IOE Platform</title>
    <style>
  .button-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin: 20px 0;
}
 
.button { 
    padding: 10px;
    background: #f0f0f0;
    cursor: pointer; 
    transition: background 0.3s, color 0.3s; 
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
} 
 
.button.selected { 
    background: #DD571C;
    color: #fff;
    border: 2px solid #DD571C;
    cursor: default;  
} 
 
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f5f5f5;
}

.login-card {
    background-color: #ffffff;
    width: 350px;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-input {
    position: relative;
    margin-bottom: 25px;
}

.form-input input {
    width: 100%;
    height: 40px;
    border: none;
    border-bottom: 2px solid #333;
    font-size: 16px;
    outline: none;
    padding-right: 30px;
}

.form-input img {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    width: 25px;
    height: 25px;
}

.login-btn {
    width: 100%;
    background-color: #DD571C;
    color: white;
    border: none;
    padding: 10px;
    font-size: 18px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
}

.user-type-text {
    font-size: 18px;
    text-align: center;
    margin: 15px 0;
}

.logo {
    text-align: center;
    margin-bottom: 20px;
}

.logo img {
    width: 120px;
}
/* Entire Login Container and Card */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 40px 20px;
    background: linear-gradient(135deg, #ffe8d6, #f6f6f6);
    animation: fadeInBackground 1s ease forwards;
}

@keyframes fadeInBackground {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.login-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    text-align: center;
    animation: slideInCard 1s ease-out;
    border: 1px solid #f5cba7;
}

@keyframes slideInCard {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Logo styling */
.login-card .logo img {
    width: 100px;
    margin-bottom: 20px;
    animation: bounceLogo 1s ease;
}

@keyframes bounceLogo {
    0% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); }
}

/* Text showing selected user type */
.user-type-text {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 25px;
    animation: fadeInText 1s ease-in-out;
}

@keyframes fadeInText {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Buttons */
.button-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    gap: 15px;
}

.button {
    width: 70px;
    height: 70px;
    background: linear-gradient(145deg, #ffffff, #f0f0f0);
    border-radius: 15px;
    border: 2px solid transparent;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.button img {
    width: 32px;
    height: auto;
    transition: transform 0.3s;
}

.button.selected {
    background: linear-gradient(135deg, #ffecd2, #fcb69f);
    border-color: #DD571C;
    animation: pulse 0.4s ease;
    box-shadow: 0 0 10px #DD571C;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}
.login-card {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.88); /* semi-transparent white */
    backdrop-filter: blur(6px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

<style>
  body {
    margin: 0;
    padding: 0;
    background: url('https://images.unsplash.com/photo-1607746882042-944635dfe10e') no-repeat center center/cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
  }

  .login-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    border-radius: 25px;
    padding: 30px 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: 350px;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .logo img {
    width: 120px;
    margin-bottom: 20px;
  }

  .user-type-text {
    color: black;
    font-weight: 600;
    margin-bottom: 20px;
    font-size: 1.1rem;
  }

  .button-container {
    display: flex;
    justify-content: center;
    gap: 15px;
  }

  .button {
    width: 70px;
    height: 70px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent;
  }

  .button:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
    border-color: #ffffff;
  }

  .button img {
    width: 30px;
    height: auto;
  }
  
</style>

</style>
<body>
<?php echo form_open('signin/authenticate', array("id" => "signin-form", "onsubmit" => "return validateSelection();")); ?>


<div class="bg-image"></div>
  <div class="login-card">
    <div class="logo">
            <img src="https://i.postimg.cc/x1NCXFqD/IMG-20250703-WA0000.jpg" alt="Efactor Logo">
        </div>
<div id="selected-user-type" class="user-type-text">
    Please select a user type
</div>

<div class="button-container"> 
    <div class="button" onclick="selectButton(this)" title="User">
                <img style="width:30px;height:30px" src="https://efactor.tech/files/home/person.png">
            </div>
    <div class="button" onclick="selectButton(this)" title="Brand">
        <img style="width:35px;height:30px" src="https://efactor.tech/files/home/client.png">
    </div>
    <div class="button" onclick="selectButton(this)" title="Developer">
        <img style="width:30px;height:25px" src="https://efactor.tech/files/home/devloper.png">
    </div>
    <div class="button" onclick="selectButton(this)" title="Manufacturer">
        <img style="width:30px;height:30px" src="https://efactor.tech/files/home/manufacturer.png">
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

        <div class="form-input">
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
                "data-msg-email" => app_lang("enter_valid_email")
            ));
            ?>
            <img src="https://efactor.tech/files/home/email.png" id="mail">
        </div>

        <div class="form-input">
            <?php
            echo form_password(array(
                "id" => "password",
                "name" => "password",
                "class" => "form-control form-control-lg",
                "placeholder" => app_lang('password'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
            <img src="https://efactor.tech/files/home/eye.png" id="eyeo">
        </div>

        <input type="hidden" id="user_type" name="user_type" value="">

        <?php echo view("signin/re_captcha"); ?>

        <button class="login-btn" type="submit">
            <?php echo app_lang('signin'); ?>
        </button>

        <?php echo form_close(); ?>
    </div>
</div>

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
</body>
</html>