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
    z-index: 2; /* Set a higher z-index for .about */
}

        .about a {
            padding: 5px 20px;
            text-decoration: none;
            box-sizing: border-box;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);  
    opacity: 0.95; 
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
          
       
        body {
  margin: 0;
  height: 100vh;  
  background: linear-gradient(to bottom, #89CFF0, white);
}
.center-box {
    width: 50%;  
    height: 100vh;  
    background-color: #DD571C;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    left: 25%;
    transform: translate(-50%, -50%);
    text-align: center;
    
    color:white;
    font-size: 50px;
     
}
.center-box small{
font-size: 20px;
}



.right-side {
 width: 50%;  
  height: 100vh;  
    position: absolute;
    background-color: #ffffff;
    text-align: center;
    display: block;
    order: 1;
    margin-left: 50%;
     
    z-index: 1;  
}
 
    </style>
</head>

<body>

    <header>
        <div class="top">
            
            <div class="about">
                <a href="https://efactor.tech/efactor/About.html" class="about1">About</a>
            </div>
        </div>
    </header>


   
<div class="content">

 <div class="main-container">
 <div class="center-box">
        <div class="logo">
              <strong> Create Your IOE Product</strong></br>
             <small> Transform your ideas into reality with our innovative IOE solutions.Join us on the journey of creating smart and connected devices for the future.</small>
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
  
    </div>
     



</body>

</html>
