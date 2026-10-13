<!DOCTYPE html>
<html lang="en">

<head>
    <title>Efactor</title>
    <style>
        /* Your existing styles... */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            color: #333;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #333;
            color: white;
            align-items: center;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .left-side {
            flex: 0 0 50%;
            background-color: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-side h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .left-side p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .right-side {
            flex: 0 0 50%;
            padding: 20px;
            text-align: center;
            background-color: #3498db; /* Add this line for blue background */
            color: white; /* Add this line for white text */
        }

        .form-wrapper {
            float: right;
            background-color: white; /* Add this line for white background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            display: none;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <a href="#" onclick="displayContactInfo()">Contact Us</a>
        </div>
        <div>
            <input type="text" placeholder="Search" />
            <button>Search</button>
        </div>
    </div>

    <div class="container">
        <div class="left-side">
            <h1>Create Your IOE Product</h1>
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
                <h2>Contact Us</h2>
                <p>FACTOR E-NOVATIONS PVT LTD</p>
                <p>Address: H.No.12-13-865/25, Maruthi Nagar, Gokul Nagar, Tarnaka, Hyderabad,500017.</p>
                <p>Phone Number: 040 27177789</p>
                <p>Email: factorenovations@yahoo.com</p>
            </div>
        </div>
    </div>

    <script>
        function displayContactInfo() {
            var contactInfoDiv = document.querySelector('.contact-info');
            contactInfoDiv.style.display = (contactInfoDiv.style.display === 'none') ? 'block' : 'none';
        }
    </script>
</body>

</html>
