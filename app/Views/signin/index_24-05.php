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
            border: none;
        }


       


        .right-side {
            position: relative;
            background: rgb(240, 240, 240);
            border: 2px solid, black;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 20px rgb(0, 0, 0, 0.6);
            text-align: center;
            z-index: 2;
            position: absolute;
            margin-top: 120px;
            margin-left: 900px;
            
        }

 
          .text {
             position: absolute;
             z-index: 1;
             background-color: rgb(167, 228, 247);
             width: 50%;
             height: 100vh;
             margin-top: -10px;
             margin-left: -10px;

             
               }
        
        body{
           /* background-color: #f0f1ff; */
           background-color: #9ecb3b;
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
         <img src="https://efactor.tech/files/home/img.png" class="text">
    </div>
 
   
    
     



</body>

</html>
