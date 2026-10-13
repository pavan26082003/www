<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font -->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/grasp_mobile_progress_circle-1.0.0.min.css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet -->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/favicon.ico">

    <style>
        /* Reset and Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .help-page-container {
            background: #f0f2f5;
            padding: 30px 0 60px 0; /* Added bottom padding for scroll space */
            min-height: 100vh;
        }

        .container-fluid {
            max-width: 100%;
            padding: 0 30px;
        }

        /* Search Box - Full Width & Longer */
        /* ==========================
   Search Section
========================== */

.help-search-box-container {
    background: linear-gradient(135deg, #E8F0FF 0%, #DCE7FF 100%);
    border: 1px solid rgba(78, 124, 223, 0.18);
    padding: 50px 40px;
    border-radius: 20px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 12px 32px rgba(78, 124, 223, 0.12);
    transition: all 0.35s ease;
}

.help-search-box-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 50px rgba(78, 124, 223, 0.18);
}

/* Decorative Background */

.help-search-box-container::before {
    content: '';
    position: absolute;
    top: -140px;
    right: -100px;
    width: 320px;
    height: 320px;
    background: rgba(78, 124, 223, 0.10);
    border-radius: 50%;
    pointer-events: none;
    transition: all 0.4s ease;
}

.help-search-box-container::after {
    content: '';
    position: absolute;
    bottom: -120px;
    left: -80px;
    width: 240px;
    height: 240px;
    background: rgba(78, 124, 223, 0.07);
    border-radius: 50%;
    pointer-events: none;
    transition: all 0.4s ease;
}

.help-search-box-container:hover::before {
    transform: scale(1.1);
}

.help-search-box-container:hover::after {
    transform: scale(1.15);
}

/* Heading */

.help-search-box-container h2 {
    color: #1E3A5F;
    font-size: 2.6rem;
    font-weight: 700;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
}

.help-search-box-container h2 i {
    color: #4E7CDF;
    margin-right: 15px;
}

/* Search Box */

.help-search-box-container .search-box {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1001;
}

.search-input-group {
    position: relative;
    width: 100%;
}

/* Search Input */

.help-search-box-container .search-box input {
    width: 100%;
    background: #ffffff;
    border: 1px solid #DCE7FF;
    border-radius: 60px;
    padding: 20px 35px;
    font-size: 1.1rem;
    color: #1E3A5F;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.help-search-box-container .search-box input::placeholder {
    color: #8B95A7;
}

.help-search-box-container .search-box input:focus {
    outline: none;
    border-color: #4E7CDF;
    box-shadow: 0 0 0 4px rgba(78, 124, 223, 0.12);
}

/* Search Button */

.help-search-box-container .search-box button {
    position: absolute;
    right: 8px;
    top: 8px;
    background: #4E7CDF;
    border: none;
    border-radius: 60px;
    padding: 14px 40px;
    color: #ffffff;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 6px 18px rgba(78, 124, 223, 0.20);
}

.help-search-box-container .search-box button:hover {
    background: #3566E6;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(78, 124, 223, 0.30);
}

/* Responsive */

@media (max-width: 768px) {

    .help-search-box-container {
        padding: 35px 20px;
    }

    .help-search-box-container h2 {
        font-size: 2rem;
    }

    .help-search-box-container .search-box input {
        padding: 18px 25px;
        font-size: 1rem;
    }

    .help-search-box-container .search-box button {
        position: relative;
        width: 100%;
        right: auto;
        top: auto;
        margin-top: 15px;
        padding: 15px;
    }
}

        /* Search Results Dropdown - FIXED */
        .search-results-container {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 9999;
            padding: 10px 0;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .search-results-container.active {
            display: block;
        }

        .search-result-item {
            padding: 12px 25px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 1rem;
            color: #333;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: #f8f9fc;
        }

        .search-result-item i {
            color: #4e73df;
            font-size: 1.2rem;
            width: 24px;
        }

        .search-result-item .result-type {
            font-size: 0.75rem;
            background: #e8ecf1;
            padding: 2px 12px;
            border-radius: 20px;
            color: #666;
            margin-left: auto;
        }

        .search-result-item.highlight {
            background: #e8ecf1;
        }

        .no-results-found {
            padding: 30px 25px;
            text-align: center;
            color: #999;
            font-size: 1rem;
        }

        .no-results-found i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
            color: #ccc;
        }

        /* Category Cards - Reduced Height, Larger Font */
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .row-equal-height .col-md-4 {
            display: flex;
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .row-equal-height .col-md-4 > a {
            width: 100%;
            display: flex;
        }

 .help-category-box {
    border-radius: 20px;
    padding: 25px 25px 20px;
    transition: all 0.35s ease;
    background: linear-gradient(135deg, #EEF3FF 0%, #E7EDFF 100%);
    border: 1px solid rgba(78, 124, 223, 0.12);
    box-shadow: 0 8px 24px rgba(78, 124, 223, 0.08);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 200px;
}

.help-category-box::before {
    content: "";
    position: absolute;
    top: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(78, 124, 223, 0.08);
    transition: all 0.4s ease;
}

/* Hover Effect */

.help-category-box:hover {
    background: linear-gradient(135deg, #C9D9FF 0%, #BECBFF 100%);
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(78, 124, 223, 0.18);
    border-color: rgba(78, 124, 223, 0.25);
}

.help-category-box:hover::before {
    background: rgba(78, 124, 223, 0.15);
    transform: scale(1.35);
}

.help-category-box .card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* ==========================
   Heading
========================== */

.help-category-box h4 {
    color: #1E3A5F !important;
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
    transition: all 0.35s ease;
}

.help-category-box:hover h4 {
    color: #1E3A5F !important;
}

.help-category-box h4 i {
    margin-right: 10px;
    color: #4E7CDF;
    transition: all 0.35s ease;
}

.help-category-box:hover h4 i {
    color: #3566E6 !important;
}

/* ==========================
   Description
========================== */

.help-category-box p {
    color: #5F5F7A !important;
    font-size: 1rem;
    line-height: 1.6;
    flex: 1;
    position: relative;
    z-index: 1;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: all 0.35s ease;
}

.help-category-box:hover p {
    color: #4E5A70 !important;
}

/* ==========================
   Button
========================== */

.help-category-box .anchor {
    color: #ffffff !important;
    background: #4E7CDF;
    border-radius: 30px;
    padding: 8px 18px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.35s ease;
    box-shadow: 0 4px 12px rgba(78, 124, 223, 0.20);
    align-self: flex-start;
    margin-top: auto;
}

.help-category-box .anchor i {
    margin-right: 8px;
    color: #ffffff;
    transition: all 0.35s ease;
}

.help-category-box:hover .anchor {
    background: #3566E6;
    color: #ffffff !important;
    box-shadow: 0 8px 20px rgba(78, 124, 223, 0.28);
}

.help-category-box:hover .anchor i {
    color: #ffffff !important;
}

.help-category-box .anchor:hover {
    background: #224ABE;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(34, 74, 190, 0.35);
}
        /* FAQ Section */
        .faq-section {
            margin-top: 50px;
            padding-bottom: 80px; /* Added extra bottom padding for last FAQ scroll */
        }

        .faq-section .section-title {
            font-size: 2.6rem;
            font-weight: 700;
            color: #1a2332;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .faq-section .section-title i {
            color: #4e73df;
            font-size: 3rem;
        }

        .faq-item {
            background: white;
            border-radius: 16px;
            margin-bottom: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e8ecf1;
        }

        .faq-item:hover {
            box-shadow: 0 8px 35px rgba(0,0,0,0.12);
        }

        /* Last FAQ item should have margin bottom */
        .faq-item:last-child {
            margin-bottom: 0;
        }

        .faq-question {
            padding: 25px 35px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #1a2332;
            font-size: 1.4rem;
            border: none;
            width: 100%;
            text-align: left;
            font-family: 'Roboto', sans-serif;
        }

        .faq-question:hover {
            background: #f8f9fc;
        }

        .faq-question span i {
            font-size: 1.6rem;
            margin-right: 15px;
        }

        .faq-question .arrow {
            transition: transform 0.3s ease;
            font-size: 1.5rem;
            color: #4e73df;
            flex-shrink: 0;
        }

        .faq-question.active .arrow {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 35px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
            background: #f8f9fc;
        }

        .faq-answer.open {
            padding: 30px 35px;
            max-height: 3000px;
        }

        .faq-answer pre {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 1.2rem;
            color: #2d3748;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 2.2;
        }

        .faq-answer pre strong {
            color: #1a2332;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .faq-answer pre ul, 
        .faq-answer pre ol {
            padding-left: 30px;
            margin: 12px 0;
        }

        .faq-answer pre li {
            margin-bottom: 10px;
            font-size: 1.15rem;
        }

        .faq-answer pre a {
            font-size: 1.2rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .help-search-box-container {
                padding: 40px 30px;
            }

            .help-search-box-container h2 {
                font-size: 2.2rem;
            }

            .help-search-box-container .search-box {
                max-width: 800px;
            }

            .help-category-box {
                height: 180px;
                padding: 22px 20px;
            }

            .help-category-box h4 {
                font-size: 1.2rem;
            }

            .help-category-box p {
                font-size: 0.95rem;
            }

            .faq-question {
                font-size: 1.2rem;
                padding: 22px 28px;
            }

            .faq-answer pre {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 15px;
            }

            .help-search-box-container {
                padding: 30px 20px;
                margin-bottom: 25px;
            }

            .help-search-box-container h2 {
                font-size: 1.8rem;
            }

            .help-search-box-container .search-box input {
                padding: 15px 20px;
                font-size: 1rem;
            }

            .help-search-box-container .search-box button {
                position: relative;
                right: 0;
                top: 0;
                width: 100%;
                margin-top: 12px;
                border-radius: 60px;
                padding: 14px;
                font-size: 1rem;
            }

            .search-results-container {
                position: absolute;
                top: calc(100% + 5px);
                max-height: 300px;
                width: 100%;
                left: 0;
                right: 0;
            }

            .row-equal-height .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .help-category-box {
                height: 170px;
                padding: 18px 18px;
            }

            .help-category-box h4 {
                font-size: 1.1rem;
            }

            .help-category-box p {
                font-size: 0.9rem;
            }

            .faq-section .section-title {
                font-size: 2rem;
            }

            .faq-question {
                padding: 20px 25px;
                font-size: 1.1rem;
            }

            .faq-answer.open {
                padding: 22px 25px;
            }

            .faq-answer pre {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .help-search-box-container {
                padding: 20px 15px;
            }

            .help-search-box-container h2 {
                font-size: 1.4rem;
            }

            .help-search-box-container .search-box input {
                padding: 12px 16px;
                font-size: 0.9rem;
            }

            .search-results-container {
                max-height: 250px;
            }

            .search-result-item {
                padding: 10px 16px;
                font-size: 0.9rem;
            }

            .row-equal-height .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .help-category-box {
                height: 160px;
                padding: 16px 15px;
            }

            .help-category-box h4 {
                font-size: 1rem;
            }

            .help-category-box p {
                font-size: 0.85rem;
            }

            .faq-section .section-title {
                font-size: 1.6rem;
            }

            .faq-question {
                padding: 16px 18px;
                font-size: 0.95rem;
            }

            .faq-answer.open {
                padding: 16px 18px;
            }

            .faq-answer pre {
                font-size: 0.9rem;
            }
        }

        /* Custom Scrollbar for Results */
        .search-results-container::-webkit-scrollbar {
            width: 6px;
        }

        .search-results-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .search-results-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 10px;
        }

        .search-results-container::-webkit-scrollbar-thumb:hover {
            background: #224abe;
        }

        /* Animation */
        .faq-item {
            animation: fadeInUp 0.5s ease both;
        }

        .faq-item:nth-child(1) { animation-delay: 0.05s; }
        .faq-item:nth-child(2) { animation-delay: 0.1s; }
        .faq-item:nth-child(3) { animation-delay: 0.15s; }
        .faq-item:nth-child(4) { animation-delay: 0.2s; }
        .faq-item:nth-child(5) { animation-delay: 0.25s; }
        .faq-item:nth-child(6) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
<div id="page-content" class="page-wrapper clearfix help-page-container <?php echo "main_" . $type ?>">
    <div class="container-fluid">
        <!-- Search Box - Full Width & Longer -->
        <div class="help-search-box-container">
            <h2>
                <i class="fa fa-lightbulb-o"></i>
                <?php
                if ($type === "knowledge_base") {
                    echo app_lang("how_can_we_help");
                } else {
                    echo app_lang("help_page_title");
                }
                ?>
            </h2>
            <div class="search-box">
                <div class="search-input-group">
                    <input type="text" id="searchInput" placeholder="Search your question..." autocomplete="off">
                    <button onclick="performSearch()"><i class="fa fa-search"></i> Search</button>
                    
                    <!-- Search Results Dropdown -->
                    <div class="search-results-container" id="searchResults">
                        <!-- Results will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Cards -->
        <div class="help-page view-container-large" style="min-height: 400px;">
            <?php
            $count = 0;
            foreach ($categories as $category) {
                if ($count % 3 === 0) {
                    echo "<div class='row row-equal-height'>";
                }
                $count++;
                ?>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <a href="<?php echo get_uri($type . "/category/" . $category->id); ?>" style="text-decoration: none; width: 100%;">
                        <div class="help-category-box">
                            <div class="card-content">
                                <h4>
                                    <i class="fa fa-folder-open-o"></i>
                                    <?php echo $category->title; ?>
                                </h4>
                                <p><?php echo nl2br($category->description); ?></p>
                            </div>
                            <span class="anchor">
                                <i class="fa fa-file-text-o"></i>
                                <?php echo $category->total_articles . " " . app_lang("articles"); ?>
                            </span>
                        </div>
                    </a>
                </div>
                <?php
                if (($count % 3 === 0) || ($count === count($categories))) {
                    echo "</div>";
                }
            }
            ?>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="section-title">
                <i class="fa fa-question-circle"></i>
                Frequently Asked Questions
            </div>

            <!-- FAQ Items (same as before) -->
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-microchip" style="color:#4e73df;"></i>How to connect a device to UART?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
<strong>🔌 To connect a device to UART, you'll typically need to identify the UART pins on both the device and the microcontroller or host system you're connecting it to, and then establish a physical and logical connection between them. Here's a step-by-step guide:</strong>

<strong>1. Identify UART Pins:</strong>
   ◦ Determine the UART pins on your device. These are usually labeled as TX (transmit), RX (receive), and sometimes ground (GND).
   ◦ Consult your device's datasheet, user manual, or schematic to locate these pins. They may be in the form of solder pads, headers, or connectors.

<strong>2. Determine Microcontroller UART Pins:</strong>
   ◦ Identify the UART pins on your microcontroller or host system. These will also include TX, RX, and GND pins.
   ◦ Refer to the datasheet or pinout diagram of your microcontroller to find the UART pins.

<strong>3. Connect TX to RX and RX to TX:</strong>
   ◦ Connect the TX pin of the device to the RX pin of the microcontroller, and vice versa. This ensures that data transmitted by one device is received by the other, and vice versa.
   ◦ Connect the ground (GND) pins of both devices together to establish a common ground reference.

<strong>4. Check Voltage Levels:</strong>
   ◦ Ensure that the voltage levels of the UART signals are compatible between the device and the microcontroller.
   ◦ Most microcontrollers operate at 3.3V or 5V logic levels, so check if level shifting or voltage regulation is needed.

<strong>5. Secure Connections:</strong>
   ◦ Use appropriate connectors, wires, or soldering techniques to establish secure and reliable connections between the devices.
   ◦ Avoid loose connections or short circuits that could cause damage to the devices or affect communication reliability.

<strong>6. Configure UART Settings:</strong>
   ◦ Configure the UART module of your microcontroller or host system with the appropriate settings such as baud rate, data bits, parity, and stop bits.
   ◦ Ensure that the UART settings match those expected by the device to enable successful communication.

<strong>7. Test Communication:</strong>
   ◦ Write test code on the microcontroller to send data to the device via UART and receive responses.
   ◦ Verify that the device responds correctly to UART commands and that communication is reliable.
   ◦ Use debugging tools such as serial monitors or oscilloscopes to monitor UART traffic and troubleshoot any issues.

<strong>8. Integrate into Application:</strong>
   ◦ Integrate UART communication into your application code, allowing the microcontroller to interact with the device as required.
   ◦ Implement error handling and recovery mechanisms to ensure robust communication in real-world scenarios.
</pre>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-plug" style="color:#4e73df;"></i>Compatibility and Device Integration?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
<strong>🔗 Compatibility and Device Integration </strong>

   • Have you confirmed that your devices are compatible with the app?
   • Are you encountering any issues when trying to connect or integrate your IoT devices with the app?
   • Have you checked if your devices require any firmware updates for compatibility?
</pre>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-user-circle" style="color:#4e73df;"></i>Account and Settings Management?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
<strong>👤 Account and Settings</strong>

   • Are you logged into the app with the correct account?
   • Have you checked your app settings to ensure they are configured correctly?
   • Do you need assistance in managing your account or adjusting app settings?
</pre>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-exchange" style="color:#4e73df;"></i>Why is it essential to connect TX to RX and RX to TX?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
Connecting TX to RX and RX to TX enables <strong>bidirectional communication</strong> between devices. Data transmitted by one device's TX pin is received by the other device's RX pin, allowing for <strong>two-way data exchange</strong>.
</pre>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-wifi" style="color:#4e73df;"></i>How to connecting devices over Wifi?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
                    Connect via WiFi module or chip on the microcontroller and configure WiFi settings for network access.
                    </pre>
                </div>
            </div>

            <div class="faq-item" id="faq6">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><i class="fa fa-headphones" style="color:#4e73df;"></i>Technical Support?</span>
                    <span class="arrow"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <pre>
                    For more queries, send an email to <a href="mailto:admin@efactor.io" style="color:#4e73df; text-decoration:underline;">admin@efactor.io</a>.
                    Tell us a little bit about your problem, and we'll email you back.
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search Data
    const searchData = [
        // FAQ Items
        { title: 'How to connect a device to UART', type: 'FAQ', icon: 'fa-microchip', url: '#faq1' },
        { title: 'Compatibility and Device Integration', type: 'FAQ', icon: 'fa-plug', url: '#faq2' },
        { title: 'Account and Settings Management', type: 'FAQ', icon: 'fa-user-circle', url: '#faq3' },
        { title: 'Why connect TX to RX and RX to TX?', type: 'FAQ', icon: 'fa-exchange', url: '#faq4' },
        { title: 'How to connect devices over WiFi', type: 'FAQ', icon: 'fa-wifi', url: '#faq5' },
        { title: 'Technical Support', type: 'FAQ', icon: 'fa-headphones', url: '#faq6' },
        
        // Category Cards (add your actual categories here)
        <?php foreach ($categories as $category): ?>
        { 
            title: '<?php echo addslashes($category->title); ?>', 
            type: 'Category', 
            icon: 'fa-folder-open-o',
            url: '<?php echo get_uri($type . "/category/" . $category->id); ?>'
        },
        <?php endforeach; ?>
    ];

    // Perform Search
    function performSearch() {
        const input = document.getElementById('searchInput');
        const query = input.value.toLowerCase().trim();
        const resultsContainer = document.getElementById('searchResults');
        
        if (query === '') {
            resultsContainer.classList.remove('active');
            resultsContainer.innerHTML = '';
            return;
        }

        // Filter results
        const results = searchData.filter(item => 
            item.title.toLowerCase().includes(query)
        );

        // Display results
        if (results.length > 0) {
            let html = '';
            results.forEach(item => {
                html += `
                    <div class="search-result-item" onclick="navigateTo('${item.url}')">
                        <i class="fa ${item.icon}"></i>
                        <span>${item.title}</span>
                        <span class="result-type">${item.type}</span>
                    </div>
                `;
            });
            resultsContainer.innerHTML = html;
            resultsContainer.classList.add('active');
        } else {
            resultsContainer.innerHTML = `
                <div class="no-results-found">
                    <i class="fa fa-search"></i>
                    No results found for "<strong>${query}</strong>"
                </div>
            `;
            resultsContainer.classList.add('active');
        }
    }

    // Navigate to result
    function navigateTo(url) {
        if (url.startsWith('#')) {
            // FAQ item - scroll to it
            const element = document.querySelector(url);
            if (element) {
                // Calculate offset for better scrolling position
                const offset = 100;
                const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
                window.scrollTo({
                    top: elementPosition - offset,
                    behavior: 'smooth'
                });
                // Open the FAQ
                const question = element.querySelector('.faq-question');
                if (question) {
                    toggleFAQ(question);
                }
            }
        } else {
            // Category link - navigate
            window.location.href = url;
        }
        // Close results
        document.getElementById('searchResults').classList.remove('active');
        document.getElementById('searchInput').value = '';
    }

    // Search on keyup with debounce
    let searchTimeout;
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('searchResults');
            const input = document.getElementById('searchInput');
            if (!container.contains(e.target) && e.target !== input) {
                container.classList.remove('active');
            }
        });

        // Handle Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstResult = document.querySelector('.search-result-item');
                if (firstResult) {
                    firstResult.click();
                }
            }
        });
    });

    // FAQ Toggle Function - FIXED for last FAQ
    function toggleFAQ(element) {
        const answer = element.nextElementSibling;
        const arrow = element.querySelector('.arrow');
        const isOpen = answer.classList.contains('open');
        const faqItem = element.closest('.faq-item');
        
        // Close all other FAQs
        const allAnswers = document.querySelectorAll('.faq-answer');
        const allArrows = document.querySelectorAll('.faq-question .arrow');
        
        allAnswers.forEach(ans => {
            ans.classList.remove('open');
            // Remove padding when closed
            ans.style.paddingTop = '0';
            ans.style.paddingBottom = '0';
        });
        allArrows.forEach(arr => {
            arr.style.transform = 'rotate(0deg)';
        });
        document.querySelectorAll('.faq-question').forEach(q => {
            q.classList.remove('active');
        });
        
        // Toggle the clicked FAQ
        if (!isOpen) {
            answer.classList.add('open');
            answer.style.paddingTop = '30px';
            answer.style.paddingBottom = '30px';
            if (arrow) {
                arrow.style.transform = 'rotate(180deg)';
            }
            element.classList.add('active');
            
            // Scroll to show the full FAQ content with smooth animation
            setTimeout(function() {
                const offset = 120;
                const elementPosition = faqItem.getBoundingClientRect().top + window.pageYOffset;
                const answerHeight = answer.scrollHeight;
                
                // If it's the last FAQ, scroll a bit more to show full content
                const isLastFAQ = faqItem === document.querySelector('.faq-item:last-child');
                const extraOffset = isLastFAQ ? 60 : 0;
                
                window.scrollTo({
                    top: elementPosition - offset - extraOffset,
                    behavior: 'smooth'
                });
            }, 100);
        } else {
            // When collapsing, remove padding
            answer.style.paddingTop = '0';
            answer.style.paddingBottom = '0';
        }
    }

    // Legacy functions
    function q1() { toggleFAQ(document.querySelector('.faq-item:nth-child(1) .faq-question')); }
    function q2() { toggleFAQ(document.querySelector('.faq-item:nth-child(2) .faq-question')); }
    function q3() { toggleFAQ(document.querySelector('.faq-item:nth-child(3) .faq-question')); }
    function q4() { toggleFAQ(document.querySelector('.faq-item:nth-child(4) .faq-question')); }
    function q5() { toggleFAQ(document.querySelector('.faq-item:nth-child(5) .faq-question')); }
    function q6() { toggleFAQ(document.querySelector('.faq-item:nth-child(6) .faq-question')); }
</script>

</body>
</html>