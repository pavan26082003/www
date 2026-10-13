<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>log-in</title>

    <!-- // css properties //-->
    <style>
        * {
            margin: 0 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        .container {
            width: 1200px;
            margin: 0 auto;

        }

        .log_items {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;

        }

        /* left slider card  */

        .left-card {
            background: linear-gradient(145deg, #1f2933, #111827);
            color: #e5e7eb;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
            border-radius: 20px 0px 0px 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
        }

        .info-card {
            display: block;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            max-width: 520px;

        }

        .left-card h2 {
            margin-top: 0;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .left-card p {
            line-height: 20px;
            font-size: 13px;
            color: #d1d5db;

        }

        .left-card span {
            font-weight: bold;
            /* to make the text bold in paragraph */

        }

        .info-footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }

        /* know more button */
        .knowmore button {
            background: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* knowmore hover effect */
        .knowmore button,
        .signin-btn button {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .knowmore button:hover,
        .signin-btn button:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }

        /* downalod card slider */
        .download-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.05);
            padding: 65px 64px;
            border-radius: 18px;
            max-width: 520px;
        }

        .product-info {
            font-size: 20px;
            width: 50%;

        }

        .product-info h3 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-info p {
            font-size: 14px;
            line-height: 30px;
        }

        /* QR image  */
        .qr-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 16px;
            background: #fff;
            padding: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .left-slider {
            position: relative;
            max-width: 520px;
            min-height: 350px;
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateX(40px);
            transition: all 0.6s ease;
            pointer-events: none;
        }

        .slide.active {
            opacity: 1;
            transform: translateX(0);
            position: relative;
            pointer-events: auto;
        }

        /* DOTS */
        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
        }

        .dot {
            width: 22px;
            height: 4px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 4px;

        }

        .dot.active {
            background: #fff;
        }


        /* right silde form  */
        .right-card {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 60.5px 40px;
            background-image: url(<?= base_url('files/signin-images/bg1.png') ?>);
            background-repeat: no-repeat;
            background-position: unset;
            background-size: cover;
            border-radius: 0px 20px 20px 0px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
            overflow: hidden;

        }


        .right-card h1 {
            font-size: 25px;
            font-weight: bold;
        }

        .right-card p {
            font-size: 14px;
            line-height: 1.2rem;
        }

        .form-items {
            color: #000;
            padding: 10px 0;
            text-align: center;
            background: rgb(238 241 233 / 30%);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
        }

        /* brand logo  */
        .brand_logo {
            display: block;
            width: 150px;
            max-width: 150px;
            margin-bottom: 10px;
        }

        .brand_logo img {
            /* image adjustment */
            display: block;
            width: 100%;
            object-fit: cover;
        }

        .role-selector {
            display: flex;
            gap: 12px;
            margin: 10px 0 10px;
        }

        .role {
            /*  default role properties */
            flex: 1;
            padding: 6px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #d1d5db;
            min-width: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 2px;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }


        .role .icon {
            display: block;
            width: 35px;
            height: 35px;
            margin-bottom: 6px;
            font-size: 22px;
        }

        .role .icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }


        .role .label {
            font-size: 10px;
            font-weight: bold;
        }

        .role:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .role.active {
            background: #1a73e8;
            color: #fff;
            border-color: #1a73e8;
            box-shadow: 0 8px 22px rgba(26, 115, 232, 0.45);
        }



        .fields {
            display: flex;
            align-items: start;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
            padding: 10px 0;
        }

        .fields label {
            font-size: 14px;
        }

        .fields input {
            width: 310px;
            max-width: 350px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            margin-bottom: 14px;
            font-size: 14px;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
            max-width: 350px;
        }

        .password-wrapper input {
            width: 100%;
            padding: 12px 44px 12px 14px;
        }

        /* Toggle button */
        .toggle-password {
            position: absolute;
            right: 22px;
            top: 30%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        /* SVG icons */
        .eye {
            width: 20px;
            height: 20px;
            fill: #6b7280;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        /* Default state */
        .eye-closed {
            opacity: 0;
            transform: scale(0.85);
        }

        /* Active (show password) */
        .toggle-password.active .eye-open {
            opacity: 0;
            transform: scale(0.85);
        }

        .toggle-password.active .eye-closed {
            opacity: 1;
            transform: scale(1);
        }

        /* Hover */
        .toggle-password:hover .eye {
            fill: #1a73e8;
        }

        .forgot-password {
            width: 100%;
            max-width: 350px;
            text-align: right;
            margin-top: -4px;
            margin-bottom: 0;
        }

        .forgot-password a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #000;
            text-decoration: none;
            transition: color 0.25s ease;
        }

        /* Hover animation */
        .forgot-password a:hover {
            color: #1558c0;
        }

        .signin-btn button {
            /* sign in button */
            background: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 8px 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;

        }

        a {
            text-decoration: none;
        }



        /* ------------------------ Responsive block for all screens ----------------------------*/
        /* Tablet responsiveness (≤ 1024px) */
        @media (max-width: 1024px) {

            .container {
                width: 100%;
                padding: 0;
            }

            .log_items {
                flex-direction: column;
                align-items: stretch;
            }

            /* ORDER FIX (IMPORTANT PART) */
            .right-card {
                order: 1;
            }

            .left-card {
                order: 2;
            }


            .left-card {
                align-items: center;
                width: 100%;
                border-radius: 0px 0px 30px 30px;

            }

            .right-card {
                width: 100%;
                border-radius: 0px;
            }

            .right-card {
                margin-bottom: 0;
                padding: 48px 24px;
            }

            .left-card {
                padding: 24px;
            }

            .left-slider {
                min-height: 300px;
            }
        }



        /* Mobile responsiveness (≤ 768px) */
        @media (max-width: 768px) {


            .log_items {
                flex-direction: column;
                min-height: auto;
            }


            .right-card,
            .left-card {
                width: 100%;
                /* border-radius: 16px; */
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
            }

            .right-card {
                padding: 32px 16px;
            }

            .form-items {
                padding: 20px 16px;
            }

            .role-selector {
                gap: 8px;
            }

            .role {
                min-height: 80px;
                padding: 8px;
            }

            .fields input,
            .password-wrapper {
                max-width: 100%;
            }

            .signin-btn button {
                width: 100%;
                padding: 10px 30px;
            }
        }

        /* Small phones (≤ 480px) */
        @media (max-width: 480px) {

            .left-card {
                padding: 16px;
            }

            .info-card,
            .download-card {
                padding: 16px;
            }

            .product-info {
                width: 100%;
                text-align: center;
            }

            .left-slider {
                min-height: auto;
            }

            .role-selector {
                /* flex-direction: column; */
                gap: 6px;
            }

            .role {
                width: 100%;
                gap: 0;
                min-width: 84px;
            }

            .role .icon {
                width: 25px;
                height: 25px;
            }

            .role .label {
                font-size: 9px;
            }

            .role {
                min-height: 50px;
                padding: 5px;
            }
             .fields input {
                width:250px;
             }

            .signin-btn button {
                padding: 8px 28px;
            }

            .knowmore button {

                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<!-- ::::::::::::::::::::::::: All the Login page  cardhtml elements are here ::::::::::::::::::::::::::: -->

<body>
    <section class="login-card">
        <div class="container">
            <div class="log_items">
                <!-- ::::::::::::::::::::Left section of login page ::::::::::::::::::::: -->
                <div class="left-card">
                    <!-- ::::::::::::::::::::Left-slider block starts::::::::::::::::::::: -->
                    <div class="left-slider">
                        <!-- ::::::::::::::::::::default slide info page ::::::::::::::::::::: -->
                        <div class="info-card slide active">
                            <h2>Dear Valued User,</h2>
                            <p>We are E-Factor IoE, an emerging Indian startup dedicated to delivering a comprehensive
                                Internet
                                of
                                Everything (IoE) ecosystem. In a global market where only a select few offer holistic
                                solutions,
                                we
                                take pride in our advanced technology and innovative approach, positioning us at the
                                forefront
                                of
                                the industry.</p>
                            <p>Our journey is driven by a passionate and close-knit team, operating without venture
                                capital
                                backing.
                                Instead, we are supported by the unwavering belief and contributions of our family and
                                friends.
                                This
                                foundation empowers us to innovate with agility and maintain a user-centric focus.</p>
                            <p>We acknowledge that, during this testing phase, you may encounter certain issues as we
                                refine
                                our
                                offerings. Our limited resources mean that problem resolution might be gradual, but
                                please
                                rest
                                assured that we are committed to addressing each challenge with persistence and
                                diligence.
                                Your
                                experience is paramount to us, and we are devoted to enhancing our services to meet your
                                expectations.</p>
                            <p>Your support and feedback are invaluable to our growth and improvement. We encourage you
                                to
                                connect
                                with us through the in-app chat, email in dashboard to share your insights and
                                suggestions.
                                Collaboratively, we can refine our solutions and work towards establishing E-Factor IoE
                                as a
                                global
                                brand.</p>
                            <p>Thank you for your understanding and for being an integral part of our journey.</p>
                            <div class="info-footer">
                                <div class="ft_text">
                                    <p><strong>Warm regards,</strong></p>
                                    <p><strong>The E-Factor IoE Team</strong></p>
                                </div>
                                <!-- ::::::::::::: know moew button here::::::: -->
                                <div class="knowmore">
                                    <button onclick="window.open('https://efactor.tech/efactor/About.html', '_blank')">Know More</button>
                                </div>
                            </div>
                        </div>

                        <!-- :::::::::::Slider 2nd page to scan and download :::::::::::: -->
                        <div class="download-card slide">
                            <div class="product-info">
                                <h3>Create Your IOE Product</h3>
                                <p>Transform your ideas into reality with our innovative IOE solutions. Join us on the
                                    journey
                                    of
                                    creating smart and connected devices for the future.</p>

                            </div>
                            <p><strong>Scan to download (Android only)</strong></p>
                            <img class="qr-image" src="<?= base_url('files/signin-images/QR.png') ?>" />
                        </div>
                    </div>
                    <!-- :::::::::::Slider rectangle dots  :::::::::::: -->
                    <div class="slider-dots">
                        <span class="dot active" data-index="0"></span>
                        <span class="dot" data-index="1"></span>
                    </div>
                </div>
                <!-- ::::::::::::::::::::Right section of login page ::::::::::::::::::::: -->
                <div class="right-card">
                    
                    <!-- <form method="POST" action="<?= base_url('signin/authenticate') ?>" class="form-items"> -->
                    <?php echo form_open('signin/authenticate', array("id" => "signin-form", "onsubmit" => "return validateSelection();")); ?>
                    <?= csrf_field() ?>

                    <div  class="form-items">

                        <!-- Brand logo -->
                        <div class="brand_logo">
                            <img src="<?= base_url('files/signin-images/EFactor-logo.svg') ?>" alt="E-Factor Logo">
                        </div>

                        <!-- Heading -->
                        <div class="form-text">
                            <h1>Welcome Back!</h1>
                            <p>Sign-in to access your dashboard.</p>
                        </div>

                        <!-- Role selector -->
                        <div class="role-selector">
                            <div class="role " data-role="brand">
                                <span class="icon"><img src="<?= base_url('files/signin-images/brand1.png') ?>"></span>
                                <span class="label">Brand</span>
                            </div>
                            <div class="role" data-role="developer">
                                <span class="icon"><img src="<?= base_url('files/signin-images/developer.png') ?>"></span>
                                <span class="label">Developer</span>
                            </div>
                            <div class="role" data-role="manufacturer">
                                <span class="icon"><img src="<?= base_url('files/signin-images/manufacturer.png') ?>"></span>
                                <span class="label">Manufacturer</span>
                            </div>
                        </div>

                        <!-- Hidden role -->
                        <input type="hidden" name="user_type" id="userType" value="">


                        <!-- ERROR MESSAGE (SERVER) -->
                        <!-- ERROR MESSAGE (SERVER) -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="error-message">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>


                        <!-- Fields -->
                        <div class="fields">
                            <label>Email</label>
                            <input type="email" name="email" required placeholder="Enter your email">

                            <label>Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" required placeholder="Enter your password">

                                <button type="button" class="toggle-password" id="togglePassword"aria-label="Toggle password">
                                        <!-- Eye open -->
                                        <svg class="eye eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                                    </svg>
                                        <!-- Eye closed -->
                                        <svg class="eye eye-closed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M3 3l18 18-1.5 1.5-3.2-3.2A11.3 11.3 0 0 1 12 19C7 19 2.73 15.89 1 12a13.4 13.4 0 0 1 4.1-4.9L1.5 4.5 3 3zm9 4a4 4 0 0 1 4 4c0 .6-.14 1.16-.38 1.66l-1.56-1.56A2 2 0 0 0 12 9a2 2 0 0 0-1.1.32L9.34 7.76C9.84 7.52 10.4 7.4 11 7.4z" />
                                                    </svg>
                                </button>

                            </div>

                            <div class="forgot-password">
                                <a href="/forgot-password">Forgot password?</a>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="signin-btn">
                            <button type="submit">Sign In</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    
    <script>
            
        // ---------------Left-card login page slider
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');

            let current = 0;
            let interval;

            function showSlide(index) {
                slides[current].classList.remove('active');
                dots[current].classList.remove('active');

                current = index;

                slides[current].classList.add('active');
                dots[current].classList.add('active');
            }

            function startAutoSlide() {
                interval = setInterval(() => {
                    showSlide((current + 1) % slides.length);
                }, 5000);
            }

            startAutoSlide();


            document.querySelector('.left-slider').addEventListener('mouseenter', () => {
                clearInterval(interval);
            });

            document.querySelector('.left-slider').addEventListener('mouseleave', () => {
                startAutoSlide();
            });


        // ------------------user role selection function
        const roles = document.querySelectorAll('.role');
        const userTypeInput = document.getElementById('userType');

            roles.forEach(role => {
                role.addEventListener('click', () => {
                    roles.forEach(r => r.classList.remove('active'));
                    role.classList.add('active');
                    userTypeInput.value = role.dataset.role;
                });
            });

        // ------------------forget password function
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            togglePassword.addEventListener('click', () => {
                const show = passwordInput.type === 'password';

                passwordInput.type = show ? 'text' : 'password';
                togglePassword.classList.toggle('active', show);
            });


            function ensureUserType() {
                const email = document.querySelector('input[name="email"]').value.trim().toLowerCase();
                const userTypeInput = document.getElementById('userType');

                // admin rule
                if (email === 'admin@efactor.io') {
                    userTypeInput.value = 'lead';
                    return true;
                }

                // default end user
                if (!userTypeInput.value) {
                    userTypeInput.value = 'user';
                }

                return true;
            }

            document.querySelector('form').onsubmit = ensureUserType;


    </script>


</body>

</html>
