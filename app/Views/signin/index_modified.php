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

        .right-side input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .right-side input:focus {
            border-color: #007BFF;
        }

        .right-side button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .right-side button:hover {
            background-color: #0056b3;
        }

        .right-side a {
            color: #007BFF;
            text-decoration: none;
            font-size: 0.9rem;
            display: block;
            text-align: center;
            margin-top: 10px;
            transition: color 0.3s ease;
        }

        .right-side a:hover {
            color: #0056b3;
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
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <img src="https://efactor.tech/logo.png" alt="IoT Solutions Logo">
        </div>
        <div class="about">
            <a href="https://efactor.tech/efactor/About.html" class="about1">About</a>
        </div>
    </header>

    <div class="main-container">
        <div class="center-box">
            <div>
                <strong>Create Your IoT Product</strong>
                <small>Transform your ideas into reality with our innovative IoT solutions. Join us on the journey of creating smart and connected devices for the future.</small>
            </div>
        </div>
        <div class="right-side">
            <!-- PHP login form code -->
            <?php
            if (isset($form_type) && $form_type == "request_reset_password") {
                echo view("signin/reset_password_form");
            } else if (isset($form_type) && $form_type == "new_password") {
                echo view('signin/new_password_form');
            } else {
                echo view("signin/signin_form");
            }
            ?>
        </div>
    </div>

</body>

</html>