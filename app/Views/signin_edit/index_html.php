<!DOCTYPE html>
<html lang="en">

<head>
    <title>Efactor</title>
    <style>
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'poppins',sans-serif;
        }
        body{
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
           font-size: 1.2em;
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
          
      
        .cover_box{
            position: relative;
            width: 350px;
            height: 400px;
            background: white;
            border: 2px solid, black;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 30px rgb(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
          /*  transform: scale(1);*/
          /* overflow: hidden; */
            transition: transform .5s ease, height .2s ease, border 0.5s ease;
           
        }
        

        .cover_box:hover{
            border-color: black;
           
        }
        .cover_box.active-popup{
            transform: scale(1);
        }
        .cover_box.active{
            height: 520px;
        }
        .cover_box .form-box{
            width: 100%;
            padding: 40px;
             
        }
        .cover_box .form-box.login{
            transition: transform .50s ease;
            transform: translateX(0);
        }
        .cover_box.active .form-box.login{
            transition: none;
            transform: translateX(-400px);
        }

     
      
        .form-box h1{
            font-size: 2em;
            color: black;
            text-align: center;
        }
        .input-box{
            position: relative;
            width: 100%;
            height: 40px;
            margin: 30px 0;
        }
        .input-box img{
          
        position: absolute;
        right: 8px;
        font-size: 1.2em;
        color: #05190a;
        line-height: 45px;
        width: 25px;
        cursor: pointer;
        bottom: 8px;
       

        }
        .input-box label{
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
       .input-box input:valid~label{
        top: -20px
       }
       .input-box input{
        width: 100%;
        height: 100%;
        background: transparent;
        border : 1px solid #05190a;
        border-radius: 10px;
        outline: none;
        font-size: 1em;
        color: #05190a;
        font-weight: 600;
        padding: 0 35px 0 5px;
    }
    .input-box .icon{
        position: absolute;
        right: 8px;
        font-size: 1.2em;
        color: #05190a;
        line-height: 45px;
    }
   
    .remember-forget{
        font-size: .9em;
        color: #05190a;
        font-weight: 500;
        margin: 15px 0 5px;
        display: flex;
        justify-content: space-between;
    }
    .remember-forget label input{
        accent-color: #05190a;
        margin-right: 3px;
    }
    .remember-forget a{
        color: #05190a;
        text-decoration: underline;
    }

    .btn{
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
    .login-register{
        font-size: .9em;
    
        color: white;
        text-align: center;
        font-weight: 500;
        margin: 25px 0 0px;
    }
    .login-register p a{
        color: white;
        text-decoration: none;
        font-weight: 600;
    }
    .login-register p a:hover{
        text-decoration: underline;
    }
    .cover_box {
            display: none;
            text-align: center;
        }
        .contact-info {
            display: none;
           text-align: center;
            background-color:white;
            border-radius: 15px;
           
            
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
        .sec02{
            position: absolute;
            bottom: -600px;
            left: 0px;
            width: 100%;
            background-color: black;
            padding: 20px;
            color: #fff;
        }
        .sec03{
            position: absolute;
            bottom: -600px;
            left: 320px;
            width: 100%;
            background-color: transparent;
            padding: 20px;
            color: #fff;
        }
        .sec04{
            position: absolute;
            bottom: -565px;
            left: 700px;
            width: 100%;
            background-color: transparent;
            padding: 20px;
            color : #fff;
        }
        .sec05{
            position: absolute;
            bottom: -580px;
            left: 1050px;
            width: 100%;
            background-color: transparent;
            padding: 20px;
            color: #fff;
        }
        .sec06{
            position: absolute;
            bottom: -640px;
            left: 0px;
            width: 100%;
            background-color: #454545;
            padding: 5px;
            color: #fff;
        }
        .sec07{
            position: absolute;
            bottom: -623px;
            left: 130px;
            width: 100%;
            background-color: #454545;
            padding: 5px;
            color: #fff;
        }
      



    </style>
</head>

<body>
   
        <header>
        <div class="navigations">
            <div>
                <a href="#" onclick="displayContactInfo()" id="contactbtn">Contact Us</a>
                <a href="#">Support</a>
              <a href="#" onclick="login()" id="loginbtn">login</a>
            </div>
        </div>
    </header>
    <div class="left-side">
        <h2>Create Your IOE Product</h2>
        <p>Transform your ideas into reality with our innovative IOE solutions. Join us on the journey of creating smart and connected devices for the future.</p>
    </div>

 <div class="cover_box" id="cover_box"> 
    <span class="icon-class"></ion-icon></span>
    <div class="form-box login">
        <h1>Sign in</h1>
        <form action="#">
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                <input type="email" required>
                <label>Email</label>
            </div>
            <div class="input-box">
                <span class="icon"></span>
                <input type="Password"  id="password" name="password" required>
                <img src="eye-off-outline.svg" id="eyeo">
                <label for="password">Password</label> 
            </div>
            
            <div class="remember-forget">
                <lable><input type="checkbox">Remember me</lable>
                <a href="#">Forget Password</a>
            </div>
            <button type="submit" class="btn">Sign in</button>
            <div class="login-register">
                <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
            </div>
        </form>
    </div>
</div>
   
<div class="contact-info" id="contact-info">
    <h3>Contact Us</h3>
    <p>FACTOR E-NOVATIONS PVT LTD</p>
    <p>Address: H.No.12-13-865/25, Maruthi Nagar, Gokul Nagar, Tarnaka, Hyderabad,500017.</p>
    <p>Phone Number: 040 27177789</p>
    <p>Email: factorenovations@yahoo.com</p>
</div>

<script>
 var LoginInfoDiv = document.querySelector('.cover_box');
 var contactInfoDiv = document.querySelector('.contact-info');
 


  function login() {
    
    LoginInfoDiv.style.display = (LoginInfoDiv.style.display === 'none') ? 'block' : 'none';
    contactInfoDiv.style.display = 'none';
   
}
    function displayContactInfo() {
        
        contactInfoDiv.style.display = (contactInfoDiv.style.display === 'none') ? 'block' : 'none';
        LoginInfoDiv.style.display = 'none';
    }

</script>
<script>
let eyeo = document.getElementById("eyeo");
let password = document.getElementById("password");

eyeo.onclick = function() {
    if(password.type == "password"){
        password.type = "text"
        eyeo.src = "eye-outline.svg"
    }else{
        password.type = "password"
        eyeo.src = "eye-off-outline.svg"
    }
}
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>



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

</body>
</html>