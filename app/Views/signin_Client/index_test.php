<!DOCTYPE html>
<html lang="en">

<head>
    <title>Efactor</title>
    <style>
        /* Your existing styles... */
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'poppins',sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
           /* background-image: url(2.png);
          background-color: #3498db;*/
          background-color: #05190a;
            background-size: cover;
            background-position: center;
            height: 80vh;
            
        }

        .container {
            display: flex;
            height: 100vh;
        }

        header{
            background-color: black;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 100px;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
           /* background-color: transparent;*/
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99;
            display: block;
            list-style-type: disc;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            padding-inline-start: 40px;
            
        }
        header .navigations{
            float: right;
            margin-right: 20px;
        }
        header .navigations{
            display: inline-block;
            line-height: 5px;
            margin: 0 -60px;
        }
        header .navigations  a{
           position: relative;
           font-size: 0.9em;
           color: white;
           text-decoration: none;
           font-weight: 500;
           margin-left: 35px;
           padding: 7x 13px;
           border-radius: 3px;
           text-transform: uppercase;
        }
        header .navigations  a::after{
           content: '';
           position: absolute;
           left: 0;
           bottom: -6px;
           width: 100%;
           height: 3px;
           border-radius: 5px;
           transform-origin: 50% 50%;
           transform: scaleX(0);
           transition: transform .5s;
           box-shadow: 0 0 10px (255, 255, 255, 0.8);
           background: linear-gradient(to right ,white, white);
        }
        header .navigations  a:hover::after{
             transform-origin: right;
             transform: scaleX(1);
            }

            header .navigations img{
          position: absolute;
          left: 7px;
          src: "Logo.4.1.png";
          font-size: 1.2em;
          color: #05190a;
          line-height: 45px;
          width: 120px;
          height: 40px;
          bottom: 0px;
          float: left;
            }

       
    

            .contact-info {
            display: none;
           text-align: center;
            background-color:transparent;
            border-radius: 15px;
            color: white;
           
            
        }
        .left-side {
            flex: 0 0 50%;
            background-color: transparent;
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-side h2 {
            font-size: 46px;
            margin-bottom: 20px;
        }

        .left-side p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .footerSection{
          float: inline-start;
            color: #fff;
         display: flex;
            width: 100%;
            justify-content: space-evenly;
            padding-top: 20px;
           
        }
        footer{
            position: absolute;
            background-color: black;
            width: 100%;
            height: 270px;
            display: block;
           margin-bottom: -900px;
           
         
        }
        .lower{
        
            background-color: rgb(27, 27, 27);
            color: #fff;
            padding-left: 15px;
            width: 100%;
            height: 35px;
           margin-bottom: -1500px;
           display: flex;
           font-size: 15px;
        }
       
    </style>
</head>

<body>
    <header>
        <div class="navigations">
            <div>
                <a href="#" onclick="displayContactInfo()" id="contactbtn">Contact Us</a>
                <a href="#">Support</a>
              <a href="#" onclick="login()"  id="loginbtn">login</a>
              <img src="Logo.4.1.png" id="logo">
            </div>
        </div>
    </header>
    <div class="left-side">
        <h2>Create Your IOE Product</h2>
        <p>Transform your ideas into reality with our innovative IOE solutions. Join us on the journey of creating smart and connected devices for the future.</p>
    </div>
        <div class="right-side">
            <div class="form-wrapper" style="float: right;">
                <div class="form-signin">
                    <!-- Your existing sign-in form goes here -->
                    <head>
                        <?php echo view('includes/head'); ?>
                    </head>
                    <body>
                        <?php
                        if (get_setting("show_background_image_in_signin_page") === "yes") {
                            $background_url = get_file_from_setting("signin_page_background");
                            ?>
                            <style type="text/css">
                                html, body {
                                    background-image: url('<?php echo $background_url; ?>');
                                    background-size: cover;
                                }
                            </style>
                        <?php } ?>

                        <?php if (get_setting("enable_footer")) { ?>
                            <div class="scrollable-page">
                            <?php } ?>

                            <div class="form-wrapper">
                                <div class="form-signin">
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

                            <?php if (get_setting("enable_footer")) { ?>
                            </div>
                        <?php } ?>

                        <?php echo view("includes/footer"); ?>
                    </body>
                </div>
            </div>

            <div class="contact-info" id="contact-info">
                <h3>Contact Us</h3>
                <p>FACTOR E-NOVATIONS PVT LTD</p>
                <p>Address: H.No.12-13-865/25, Maruthi Nagar, Gokul Nagar, Tarnaka, Hyderabad,500017.</p>
                <p>Phone Number: 040 27177789</p>
                <p>Email: factorenovations@yahoo.com</p>
            </div>
            
        </div>
    </div>




    <script>
       var contactInfoDiv = document.querySelector('.contact-info'); 
       var LoginInfoDiv = document.querySelector('.card');

       function displayContactInfo() {
        
        contactInfoDiv.style.display = (contactInfoDiv.style.display === 'none') ? 'block' : 'none';
        LoginInfoDiv.style.display = 'none';
    }
    function login() {
    
    LoginInfoDiv.style.display = (LoginInfoDiv.style.display === 'none') ? 'block' : 'none';
    contactInfoDiv.style.display = 'none';
   
}
</script>



    <footer>
        <div class="footerSection">
    <section class="sec02">
       
    <div class="platform">
        <h4>Platform</h4>
        <br><br>
        <p> App SDK</p>
        <br>
        <p>Smart MiniApp</p>
        <br>
        <p>IOT Core</p>
        <br>
        <p>SaasFrame</p>
        <br>
        <br>
        <br>
    </div>
    </section>
    
    <section class="sec03">
        <div class="solutions">
            <h4>Solutions</h4>
            <br> <br>
            <p>Cube Private Cloud</p>
            <br>
            <p>Smart Hospitality</p>
            <br>
            <p>Smart Residential</p>
            <br>
            <p>Commercial Lighting</p>
            <br>
            <p>Smart House</p> 
            <br>
        </div>
        </section>
    
        <section class="sec04">
        <div class="solutions">
            <div class="support">
                <h4>Support</h4>
                <br><br>
                <p>Developer Community</p>
                <br>
                <p>Help Center</p>
                <br>
                <p>Online Support</p>
                <br>
                <p>Technical Support</p>
                <br>
                 </div>
        </section>
    
    
        <section class="sec05">
            <div class="company">
                <h4>Company</h4>
                <br> <br>
        <p>About Efactor</p>
        <br>
        <p>News</p>
        <br>
        <p>Security and Compilance</p>
        <br>
        <p>Investor Relations</p>
        <br>
        <p>Partner with Us</p>
            </section>
    
    
        </div>
    
        <div class="lower">
            <section class="sec06">
                <div class="Privacy Policy">
                    <h4>Privacy Policy   |</h4>
                    <p>2024 Efactor.</p>
            
                </div>
            </section>
            
            <section class="sec07">
                <div class="Terms of Use">
                    <h4>Terms of Use   |</h4>
                    
            
                </div>
            </section>
        </div>
    </footer>
</body>

</html>