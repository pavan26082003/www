<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | IoT Solutions</title>
    <style>
        /* General Reset */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #89CFF0, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        .about a {
            padding: 10px 25px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            font-weight: 600;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .about a:hover {
            background-color: #0056b3;
        }

        /* Main Container */
        .main-container {
            display: flex;
            width: 90%;
            max-width: 1200px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 80px;
        }

        /* Left Side (Center Box) */
        .center-box {
            flex: 1;
            background-color: #007BFF;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }

        .center-box strong {
            font-size: 2.5rem;
            margin-bottom: 20px;
            display: block;
            font-weight: 700;
        }

        .center-box small {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Right Side (Login Form) */
        .right-side {
            flex: 1;
            padding: 40px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .right-side form {
            width: 100%;
            max-width: 400px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .button {
            padding: 10px;
            background: #f0f0f0;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }

        .button.selected {
            background: #DD571C;
            color: #fff;
            border: 2px solid #DD571C;
            cursor: default;
        }

        .button img {
            width: 30px;
            height: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-bottom: 2px solid #ddd;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007BFF;
        }

        .form-group .icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 25px;
            height: 25px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #DD571C;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #c94a10;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                width: 100%;
                margin-top: 60px;
            }

            .center-box, .right-side {
                padding: 20px;
            }

            .center-box strong {
                font-size: 2rem;
            }

            .center-box small {
                font-size: 0.9rem;
            }

            .button-container {
                flex-direction: column;
            }

            .button {
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <img src="https://efactor.tech/files/home/logo.jpg" alt="IoT Solutions Logo">
        </div>
        <div class="about">
            <a href="https://efactor.tech/efactor/About.html" class="about1">About</a>
        </div>
    </header>

    <div class="main-container">
                <div class="right-side">
            <form id="signin-form" class="general-form" role="form" onsubmit="return validateSelection()">
                <div id="selected-user-type" style="font-size: 18px; margin-bottom: 20px;">
                    Please select a user type
                </div>

                <div class="button-container">
                    <div class="button" onclick="selectButton(this)" title="User">
                        <img src="https://efactor.tech/files/home/person.png" alt="User">
                    </div>
                    <div class="button" onclick="selectButton(this)" title="Brand">
                        <img src="https://efactor.tech/files/home/client.png" alt="Brand">
                    </div>
                    <div class="button" onclick="selectButton(this)" title="Developer">
                        <img src="https://efactor.tech/files/home/devloper.png" alt="Developer">
                    </div>
                    <div class="button" onclick="selectButton(this)" title="Manufacturer">
                        <img src="https://efactor.tech/files/home/manufacturer.png" alt="Manufacturer">
                    </div>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <img class="icon" src="https://efactor.tech/files/home/email.png" alt="Email Icon">
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <img class="icon" src="https://efactor.tech/files/home/eye.png" alt="Eye Icon" onclick="togglePassword()">
                </div>

                <input type="hidden" id="user_type" name="user_type" value="">

                <button class="btn-login" type="submit">Sign In</button>
            </form>
        </div>
    </div>

    <script>
        function selectButton(button) {
            document.querySelectorAll('.button').forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');

            const selectedUserType = button.getAttribute('title');
            document.getElementById('user_type').value = selectedUserType;

            let displayName = selectedUserType.charAt(0).toUpperCase() + selectedUserType.slice(1);
            if (selectedUserType === "devloper") {
                displayName = "Developer";
            } else if (selectedUserType === "staff") {
                displayName = "User";
            }

            document.getElementById('selected-user-type').innerText = "User Type: " + displayName;
        }

        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.querySelector('.form-group .icon');
            if (password.type === "password") {
                password.type = "text";
                eyeIcon.src = "https://efactor.tech/files/home/eyeon.png";
            } else {
                password.type = "password";
                eyeIcon.src = "https://efactor.tech/files/home/eye.png";
            }
        }

        function validateSelection() {
            const userType = document.getElementById('user_type').value;
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