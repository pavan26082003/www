<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EfactorIoE</title>
    <style>
        * {
        
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
        
            min-height: 100vh;
            background: linear-gradient(to bottom, #89CFF0, white);
            display: flex;
            flex-direction: column;
        }

    header {
    /* Remove height and padding */
    padding: 0; /* No extra padding */
    background-color: #f5f5f5;
    display: flex;
    justify-content: flex-start; /* Align from left */
    align-items: center; /* Keep vertically centered */
}


        .logo img {
            height: 40px;
            width: auto;
            margin-top: 35px;
        }

       .about-btn {
    padding: 10px 25px;
    background: rgba(255, 255, 255, 0.2);
    color: #000;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.about-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(120deg, #f7971e, #ffd200);
    z-index: -1;
    transition: all 0.4s ease;
    opacity: 0.3;
}

.about-btn:hover::before {
    left: 0;
    opacity: 1;
}

.about-btn:hover {
    color: white;
    transform: scale(1.1); /* Animates the button to grow */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Bigger shadow on hover */
}


        /* Main Content Layout */
        .main-container {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
        }

        /* Left Side (Orange) */
        .left-side {
            flex: 1;
            min-width: 300px;
            background-color: #DD571C;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
        }

        .left-content {
            max-width: 600px;
            text-align: center;
        }

        .welcome-message {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-message h2 {
            color: white;
            margin-bottom: 15px;
            font-size: 1.3rem;
            text-align: center;
        }

        .welcome-message p {
            margin-bottom: 10px;
            line-height: 1.6;
            font-size: 0.9rem;
            text-align: justify;
        }

        .main-heading-container {
            margin-top: 30px;
        }

        .main-heading {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .sub-heading {
            font-size: clamp(1rem, 1.5vw, 1.2rem);
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .qr-scan {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 30px;
            backdrop-filter: blur(5px);
        }

        .qr-scan p {
            margin-right: 10px;
            font-size: clamp(0.9rem, 1.2vw, 1rem);
            font-weight: 500;
        }

        .qr-scan img {
            width: 80px;
            height: auto;
        }

        /* Right Side (White) */
        .right-side {
            flex: 1;
            min-width: 300px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .right-content {
            width: 100%;
            max-width: 400px;
        }

        /* Form Styles */
        .button-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .button {
            padding: 10px;
            background: #f0f0f0;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 70px;
            height: 70px;
        }

        .button.selected {
            background: #DD571C;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .button img {
            max-width: 100%;
            height: auto;
        }

        #selected-user-type {
            font-size: 1.1rem;
            margin: 15px 0;
            text-align: center;
            font-weight: 500;
        }

        .form-group {
            margin: 20px 0;
            width: 100%;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #333;
            font-size: 1rem;
            background-color: transparent;
        }

        .form-control:focus {
            outline: none;
            border-bottom-color: #DD571C;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            right: 10px;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #DD571C;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 30px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background-color: #c04b16;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .left-side, .right-side {
                min-width: 100%;
            }
            
            .left-side {
                padding: 40px 20px;
            }
            
            .qr-scan {
                margin: 30px auto 0;
            }
            
            .button {
                width: 60px;
                height: 60px;
            }

            .welcome-message {
                margin: 0 0 30px;
            }
        }

        @media (max-width: 480px) {
            .main-heading {
                font-size: 1.8rem;
            }
            
            .sub-heading {
                font-size: 1rem;
            }
            
            .button-container {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
            }
            
            .button {
                width: 55px;
                height: 55px;
            }
            
            .logo img {
                height: 30px;
            }

            .welcome-message {
                padding: 15px;
            }
        }
        .qr-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 40px; /* adjust as needed */
}

.qr-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    max-width: 260px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.qr-card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
}

.qr-content p {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 10px;
}

.qr-content img {
    width: 120px;
    height: auto;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.qr-card:hover img {
    transform: rotate(2deg) scale(1.05);
}
.about-btn {
    padding: 10px 25px;
    background: rgba(255, 255, 255, 0.2);
    color: #000;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.right-side .about-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
}


.about-btn:hover::before {
    left: 0;
    opacity: 1;
}

.about-btn:hover {
    color: white;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}
/* Add this inside your existing <style> tag */

@keyframes fadeSlideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeSlideLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.welcome-message {
    animation: fadeSlideUp 1s ease-out forwards;
    opacity: 0; /* initial state for animation */
}

.main-heading-container {
    animation: fadeSlideLeft 1s ease-out forwards;
    opacity: 0;
    animation-delay: 1s;
}
.welcome-message {
    opacity: 0;
    animation: fadeSlideUp 1s ease-out forwards;
    animation-delay: 0.3s;
}

.main-heading-container {
    opacity: 0;
    animation: fadeSlideLeft 1s ease-out forwards;
    animation-delay: 1s;
}
.right-side {

    flex: 1;
    min-width: 300px;
    background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)),
                url('https://i.postimg.cc/PJxzm4ns/IMG-20250702-WA0010.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
     position: relative;
}
.left-side {
    flex: 1;
    min-width: 300px;
    background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)),
                url('https://media.istockphoto.com/id/1155114366/photo/data-analysis-for-business-and-finance-concept.jpg?s=1024x1024&w=is&k=20&c=u_PA2i0fl7cSA9vXFAZQohd83masYGDTUnPIv2qWneA');
    background-size: cover;
    background-position: center;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    
}
.right-side .about-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
    padding: 10px 25px;
    background: rgba(255, 255, 255, 0.2);
    color: #000;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease-in-out;
}
.qr-note {
  margin-top: 12px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #ffeb3b;
  background-color: rgba(0, 0, 0, 0.4);
  padding: 6px 12px;
  border-radius: 8px;
  text-align: center;
  opacity: 0;
  animation: fadeInHighlight 1s ease forwards;
  animation-delay: 1.5s;
}

/* Animation keyframe */
@keyframes fadeInHighlight {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

    </style>
</head>

<body>
    <!-- Header with logo and about button -->
    <header>
      
        
        
    </header>

    <div class="main-container">
        <div class="left-side">
            <div class="left-content">
                <!-- Welcome Message moved to left side -->
                <div class="welcome-message">
                    <h2>Dear Valued User,</h2>
                    <p>We are E-Factor IoE, an emerging Indian startup dedicated to delivering a comprehensive Internet of Everything (IoE) ecosystem. In a global market where only a select few offer holistic solutions, we take pride in our advanced technology and innovative approach, positioning us at the forefront of the industry.​</p>
                    <p>Our journey is driven by a passionate and close-knit team, operating without venture capital backing. Instead, we are supported by the unwavering belief and contributions of our family and friends. This foundation empowers us to innovate with agility and maintain a user-centric focus.​</p>
                    <p>We acknowledge that, during this testing phase, you may encounter certain issues as we refine our offerings. Our limited resources mean that problem resolution might be gradual, but please rest assured that we are committed to addressing each challenge with persistence and diligence. Your experience is paramount to us, and we are devoted to enhancing our services to meet your expectations.​</p>
                    <p>Your support and feedback are invaluable to our growth and improvement. We encourage you to connect with us through the in-app chat, email in dashboard to share your insights and suggestions. Collaboratively, we can refine our solutions and work towards establishing E-Factor IoE as a global brand.​</p>
                    <p>Thank you for your understanding and for being an integral part of our journey.</p>
                    <p><strong>Warm regards,</strong><br>
                    <strong>The E-Factor IoE Team</strong></p>
                </div>


                <div class="main-heading-container">
                    <h1 class="main-heading">Create Your IOE Product</h1>
                    <p class="sub-heading">Transform your ideas into reality with our innovative IOE solutions. Join us on the journey of creating smart and connected devices for the future.</p>
                </div>
                
                <div class="qr-wrapper">
    <div class="qr-card">
        <div class="qr-content">
        
            <p>📲 Scan to Download the App</p>
            <img src="https://efactor.tech/files/brand_logo/scanme.jpg" alt="QR Code">
             <p class="qr-note"> Android devices only !</p>
        </div>
    </div>
</div>

            </div>
        </div>

        <div class="right-side">
        <a href="https://efactor.tech/efactor/About.html" class="about-btn">About</a>
            <div class="right-content">
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
    <script>
    window.addEventListener('load', function () {
        document.querySelector('.welcome-message').style.opacity = '1';
        document.querySelector('.main-heading-container').style.opacity = '1';
    });
</script>

</body>
</html>