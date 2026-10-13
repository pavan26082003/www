<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        /* Header Styles */
        header {
            padding: 3px 7px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background-color: #F5F5F5;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .about-btn {
            padding: 8px 20px;
            background-color: white;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .about-btn:hover {
            background-color: #3b3b3b;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
    </style>
</head>

<body>
    <!-- Header with logo and about button -->
    <header>
        <div class="logo">
            <img src="https://efactor.tech/files/home/logo.jpg" alt="Efactor Logo">
        </div>
        <a href="https://efactor.tech/efactor/About.html" class="about-btn">About</a>
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
                
                <div class="qr-scan">
                    <p>Scan this secure QR code to download the app. It is compatible with Android devices only</p>
                                       <img src="https://efactor.tech/files/brand_logo/scanme.jpg" alt="QR Code">
                </div>
            </div>
        </div>

        <div class="right-side">
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
</body>
</html>