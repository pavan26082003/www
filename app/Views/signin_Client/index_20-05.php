<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <style>
        

        .logo img {
            padding-left: 10px;
            width: 105px;
            height: 35px;
        }

        .logo {
            display: inline-block;
            padding-top: 20px;
            position: fixed;
            z-index: 8;
             
        }



        .about {
            position: absolute;
            display: inline-block;
            right: 20px;
            padding-top: 14px;
            border-radius: 6px;
            margin-top: 15px;
        }


        .about a {
            padding: 5px 20px;
            text-decoration: none;
            box-sizing: border-box;
            border: 2PX solid rgba(0, 0, 0, 0.911);
            background-color: white;
            color: rgba(0, 0, 0, 0.911);
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: 600;
            border-radius: 5px;
            margin-right: 15px;
             
        }

        .about a:hover {
            background-color: #3b3b3b;
            color: rgb(255, 255, 255);
            border: 2PX solid rgba(255, 255, 255, 0.911);
        }


       


        .cover_box {
            position: relative;
            width: 350px;
            height: 400px;
            background: rgb(240, 240, 240);
            border: 2px solid, black;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 20px rgb(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            /*  transform: scale(1);*/
            /* overflow: hidden; */
            transition: transform .5s ease, height .2s ease, border 0.5s ease;

        }


        .cover_box:hover {
            border-color: black;

        }

        .cover_box.active-popup {
            transform: scale(1);
        }

        .cover_box.active {
            height: 520px;
        }

        .cover_box .form-box {
            width: 100%;
            padding: 40px;

        }

        .cover_box .form-box.login {
            transition: transform .50s ease;
            transform: translateX(0);
        }

        .cover_box.active .form-box.login {
            transition: none;
            transform: translateX(-400px);
        }



        .form-box h1 {
            font-size: 2em;
            color: black;
            text-align: center;
        }

        .input-box {
            position: relative;
            width: 100%;
            height: 40px;
            margin: 30px 0;
        }

        .input-box img {

            position: absolute;
            right: 8px;
            font-size: 1.2em;
            color: #000000;
            line-height: 45px;
            width: 25px;
            cursor: pointer;
            bottom: 8px;


        }

        .input-box label {
            position: absolute;
            top: 30%;
            left: 40px;
            transform: translate(-50%);
            font-size: 1em;
            color: #05190a;
            font-weight: 500;
            pointer-events: none;
            transition: .5s;
        }

        .input-box input:focus~label,
        .input-box input:valid~label {
            top: -20px
        }

        .input-box input {
            width: 250px;
            height: 100%;
            background: transparent;
            border: 1px solid #05190a;
            border-radius: 10px;
            outline: none;
            font-size: 1em;
            color: #05190a;
            font-weight: 600;
            padding: 0 25px 0 5px;
        }

        .emailimg {
            width: 50px;
            height: 30px;
        }

        .remember-forget {
            font-size: .9em;
            color: #05190a;
            font-weight: 500;
            margin: 15px 0 5px;
            display: flex;
            justify-content: space-between;
        }

        .remember-forget label input {
            accent-color: #05190a;
            margin-right: 3px;
        }

        .remember-forget a {
            color: #05190a;
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            height: 45px;
            background-color: black;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            font-size: 1em;
            color: #fff;
            font-weight: 500;
        }

        .login-register {
            font-size: .9em;

            color: black;
            text-align: center;
            font-weight: 500;
            margin: 25px 0 0px;
        }

        .login-register p a {
            color: black;
            text-decoration: none;
            font-weight: 600;
            text-decoration: underline;
            color: black;
        }

        .login-register p a:hover {
            text-decoration: underline;
            color: black;
        }

        .cover_box {
            text-align: center;
            z-index: 2;
            position: absolute;
            margin-top: 150px;
            margin-left: 830px;
        }

        .text {
             position: fixed;
             z-index: 1;
             background-color: rgb(167, 228, 247);
             width: 50%;
             height: 100vh;
             margin-top: -10px;
             margin-left: -10px;

             
               }
        
        body{
            background-color: #f0f1ff;
        }
    </style>
</head>

<body>

    <header>
        <div class="top">
            <div class="logo">
                <img src="https://efactor.tech/files/home/logo.jpg">
            </div>
            <div class="about">
                <a href="https://efactor.tech/efactor/About.html" class="about1">About</a>
            </div>
        </div>
    </header>


   


 <div class="main-container">
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
