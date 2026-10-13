<!DOCTYPE html>
<html>
<head>
    <!-- Performance: Preconnect to CDNs -->
    <link rel="preconnect" href="https://d19m59y37dris4.cloudfront.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Bootstrap CSS (only if needed for layout) -->
    <!--<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css">-->
    
    <!-- Font Awesome - load async -->
    <!--<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">-->
    
    <!-- Google Fonts - Async with display swap -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <style>
        /* ============================================================
           ALL STYLES INLINED - Optimized
           ============================================================ */
        /* Hover effect - moves up and adds shadow */
.help-search-box-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 50px rgba(78, 124, 223, 0.18);
}

/* Decorative circles that scale on hover */
.help-search-box-container::before {
    /* Big circle in top-right */
    transition: all 0.4s ease;
}

.help-search-box-container::after {
    /* Big circle in bottom-left */
    transition: all 0.4s ease;
}

.help-search-box-container:hover::before {
    transform: scale(1.1);  /* Grows on hover */
}

.help-search-box-container:hover::after {
    transform: scale(1.15); /* Grows on hover */
}

/* Card hover effect */
.help-category-box:hover {
    background: linear-gradient(135deg, #C9D9FF 0%, #BECBFF 100%);
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(78, 124, 223, 0.18);
    border-color: rgba(78, 124, 223, 0.25);
}

/* Decorative circle in card */
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

.help-category-box:hover::before {
    background: rgba(78, 124, 223, 0.15);
    transform: scale(1.35); /* Grows on hover */
}

/* FAQ item hover */
.faq-item:hover {
    box-shadow: 0 8px 35px rgba(0,0,0,0.12);
}

/* FAQ question hover */
.faq-question:hover {
    background: #f8f9fc;
}

/* FAQ arrow rotation */
.faq-question.active .arrow {
    transform: rotate(180deg);
}

           
        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        .help-page-container {
            background: #f0f2f5;
            padding: 30px 0 60px;
            min-height: 100vh;
        }
        
        .container-fluid {
            max-width: 100%;
            padding: 0 30px;
        }
        
        /* ============================================================
           SEARCH SECTION - Optimized with better UX
           ============================================================ */
        
        .help-search-box-container {
            background: linear-gradient(135deg, #E8F0FF, #DCE7FF);
            border: 1px solid rgba(78,124,223,0.18);
            padding: 50px 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            box-shadow: 0 12px 32px rgba(78,124,223,0.12);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            isolation: isolate;
            z-index: 1;
        }
        
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
        
        .search-box {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-input-group {
            position: relative;
            width: 100%;
        }
        
        .search-box input {
            width: 100%;
            background: #fff;
            border: 1px solid #DCE7FF;
            border-radius: 60px;
            padding: 20px 35px;
            font-size: 1.1rem;
            color: #1E3A5F;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .search-box input::placeholder { color: #8B95A7; }
        .search-box input:focus {
            outline: none;
            border-color: #4E7CDF;
            box-shadow: 0 0 0 4px rgba(78,124,223,0.12);
        }
        
        .search-box button {
            position: absolute;
            right: 8px;
            top: 8px;
            background: #4E7CDF;
            border: none;
            border-radius: 60px;
            padding: 14px 40px;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 6px 18px rgba(78,124,223,0.20);
        }
        
        .search-box button:hover {
            background: #3566E6;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(78,124,223,0.30);
        }
        
        /* Search Loading Spinner */
        .search-loading {
            position: absolute;
            right: 160px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            color: #4E7CDF;
            font-size: 1.2rem;
        }
        
        .search-loading.active { display: block; }
        
        /* ============================================================
           SEARCH RESULTS - Enhanced
           ============================================================ */
        
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
        
        .search-results-container.active { display: block; }
        
        .search-result-item {
            padding: 12px 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 1rem;
            color: #333;
            transition: background 0.15s ease;
        }
        
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f8f9fc; }
        
        /* Keyboard navigation highlight */
        .search-result-item.keyboard-selected {
            background: #e8ecf1;
            border-left: 3px solid #4E7CDF;
        }
        
        .search-result-item i {
            color: #4e73df;
            font-size: 1.2rem;
            width: 24px;
            flex-shrink: 0;
        }
        
        .search-result-item .result-type {
            font-size: 0.75rem;
            background: #e8ecf1;
            padding: 2px 12px;
            border-radius: 20px;
            color: #666;
            margin-left: auto;
            flex-shrink: 0;
        }
        
        /* Highlight search terms */
        .search-result-item mark {
            background: #FFE57F;
            padding: 0 2px;
            border-radius: 2px;
            color: #1a2332;
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
        
        .search-result-count {
            padding: 8px 25px;
            font-size: 0.8rem;
            color: #888;
            background: #f8f9fc;
            border-bottom: 1px solid #eee;
        }
        
        /* ============================================================
           CATEGORY CARDS - Optimized
           ============================================================ */
        
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .row-equal-height .col-md-4 {
            display: flex;
            margin-bottom: 20px;
            padding: 0 10px;
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
        
        .row-equal-height .col-md-4 > a {
            width: 100%;
            display: flex;
            text-decoration: none;
        }
        
        .help-category-box {
            border-radius: 20px;
            padding: 45px;
            background: linear-gradient(135deg, #EEF3FF, #E7EDFF);
            border: 1px solid rgba(78,124,223,0.12);
            box-shadow: 0 8px 24px rgba(78,124,223,0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 200px;
            transition: transform 0.35s ease, box-shadow 0.35s ease, background 0.35s ease;
        }
        
        .help-category-box:hover {
            background: linear-gradient(135deg, #C9D9FF, #BECBFF);
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(78,124,223,0.18);
        }
        
        .help-category-box .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            z-index: 0;
        }
        
        .help-category-box h4 {
            color: #1E3A5F;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .help-category-box h4 i {
            margin-right: 10px;
            color: #4E7CDF;
        }
        
        .help-category-box:hover h4 i { color: #3566E6; }
        
        .help-category-box p {
            color: #5F5F7A;
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
        }
        
        .help-category-box .anchor {
            color: #fff;
            background: #4E7CDF;
            border-radius: 30px;
            padding: 8px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(78,124,223,0.20);
            align-self: flex-start;
            margin-top: auto;
            transition: background 0.35s ease, box-shadow 0.35s ease, transform 0.35s ease;
        }
        
        .help-category-box .anchor i {
            margin-right: 8px;
            color: #fff;
        }
        
        .help-category-box:hover .anchor {
            background: #3566E6;
            box-shadow: 0 8px 20px rgba(78,124,223,0.28);
        }
        
        .help-category-box .anchor:hover {
            background: #224ABE;
            transform: translateY(-2px);
        }
        
        /* ============================================================
           FAQ SECTION - Optimized
           ============================================================ */
        
        .faq-section {
            margin-top: 50px;
            padding-bottom: 80px;
            padding-left: 100px;
            padding-right: 100px;
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
            border: 1px solid #e8ecf1;
            transition: box-shadow 0.3s ease;
        }
        
        .faq-item:hover { box-shadow: 0 8px 35px rgba(0,0,0,0.12); }
        .faq-item:last-child { margin-bottom: 0; }
        
        .faq-question {
            padding: 25px 35px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            font-weight: 600;
            color: #1a2332;
            font-size: 1.4rem;
            border: none;
            width: 100%;
            text-align: left;
            font-family: 'Roboto', sans-serif;
            transition: background 0.3s ease;
        }
        
        .faq-question:hover { background: #f8f9fc; }
        
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
        
        .faq-question.active .arrow { transform: rotate(180deg); }
        
        .faq-answer {
            padding: 0 35px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1), padding 0.3s ease;
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
        
        .faq-answer pre a {
            font-size: 1.2rem;
            color: #4e73df;
            text-decoration: underline;
        }
        
        /* ============================================================
           RESPONSIVE
           ============================================================ */
        
        @media (max-width: 1024px) {
            .help-search-box-container { padding: 40px 30px; }
            .help-search-box-container h2 { font-size: 2.2rem; }
            .help-category-box { height: 180px; padding: 22px 20px; }
            .help-category-box h4 { font-size: 1.2rem; }
            .help-category-box p { font-size: 0.95rem; }
            .faq-question { font-size: 1.2rem; padding: 22px 28px; }
            .faq-answer pre { font-size: 1.05rem; }
        }
        
        @media (max-width: 768px) {
            .container-fluid { padding: 0 15px; }
            .faq-section { padding-left: 20px; padding-right: 20px; }
            
            .help-search-box-container {
                padding: 30px 20px;
                margin-bottom: 25px;
            }
            .help-search-box-container h2 { font-size: 1.8rem; }
            
            .search-box input { padding: 15px 20px; font-size: 1rem; }
            .search-box button {
                position: relative;
                width: 100%;
                right: 0;
                top: 0;
                margin-top: 12px;
                padding: 14px;
            }
            
            .row-equal-height .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .help-category-box { height: 170px; padding: 18px; }
            .help-category-box h4 { font-size: 1.1rem; }
            .help-category-box p { font-size: 0.9rem; }
            
            .faq-section .section-title { font-size: 2rem; }
            .faq-question { padding: 20px 25px; font-size: 1.1rem; }
            .faq-answer.open { padding: 22px 25px; }
            .faq-answer pre { font-size: 1rem; }
        }
        
        @media (max-width: 480px) {
            .help-search-box-container { padding: 20px 15px; }
            .help-search-box-container h2 { font-size: 1.4rem; }
            
            .search-box input { padding: 12px 16px; font-size: 0.9rem; }
            
            .row-equal-height .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .help-category-box { height: 160px; padding: 16px 15px; }
            .help-category-box h4 { font-size: 1rem; }
            .help-category-box p { font-size: 0.85rem; }
            
            .faq-section .section-title { font-size: 1.6rem; }
            .faq-question { padding: 16px 18px; font-size: 0.95rem; }
            .faq-answer.open { padding: 16px 18px; }
            .faq-answer pre { font-size: 0.9rem; }
        }
        
        /* Scrollbar */
        .search-results-container::-webkit-scrollbar { width: 6px; }
        .search-results-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .search-results-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border-radius: 10px;
        }
        
        /* Animations */
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
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div id="page-content" class="page-wrapper clearfix help-page-container <?php echo "main_" . $type ?>">
    <div id="search-box-wrapper">
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
            
            <!-- ============================================================
                 SEARCH BOX - Enhanced with loading indicator
                 ============================================================ -->
            <div class="search-box">
                <div class="search-input-group">
                    <input type="text" id="searchInput" placeholder="Search your question..." autocomplete="off">
                    <span class="search-loading" id="searchLoading">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                    <button onclick="performSearch()"><i class="fa fa-search"></i> Search</button>
                    <div class="search-results-container" id="searchResults"></div>
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
                    <a href="<?php echo get_uri($type . "/category/" . $category->id); ?>">
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
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <div class="section-title">
            <i class="fa fa-question-circle"></i>
            Frequently Asked Questions
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span><i class="fa fa-microchip" style="color:#4e73df;"></i>How to connect a device to UART?</span>
                <span class="arrow"><i class="fa fa-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <pre>
<strong>🔌 To connect a device to UART:</strong>

1. Identify TX, RX, and GND pins on your device
2. Locate TX, RX, and GND on your microcontroller
3. Connect TX→RX and RX→TX, plus GND→GND
4. Verify voltage compatibility (3.3V/5V)
5. Configure baud rate, data bits, parity, stop bits
6. Test communication with sample data
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
<strong>🔗 Compatibility:</strong>
• Confirm device compatibility with the app
• Check for connection or integration issues
• Verify if firmware updates are needed
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
<strong>👤 Account Management:</strong>
• Are you logged in with the correct account?
• Verify app settings configuration
• Need help managing your account?
</pre>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span><i class="fa fa-exchange" style="color:#4e73df;"></i>Why connect TX to RX and RX to TX?</span>
                <span class="arrow"><i class="fa fa-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <pre>
This enables <strong>bidirectional communication</strong> between devices, allowing two-way data exchange.
</pre>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span><i class="fa fa-wifi" style="color:#4e73df;"></i>How to connect devices over WiFi?</span>
                <span class="arrow"><i class="fa fa-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <pre>
Use a WiFi module on the microcontroller and configure network settings for access.
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
For more queries, email <a href="mailto:admin@efactor.io">admin@efactor.io</a>.
Describe your issue and we'll respond promptly.
</pre>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================================
    // SEARCH DATA - Built once, used many times
    // ============================================================
    const searchData = [
        { title: 'How to connect a device to UART', type: 'FAQ', icon: 'fa-microchip', url: '#faq1' },
        { title: 'Compatibility and Device Integration', type: 'FAQ', icon: 'fa-plug', url: '#faq2' },
        { title: 'Account and Settings Management', type: 'FAQ', icon: 'fa-user-circle', url: '#faq3' },
        { title: 'Why connect TX to RX and RX to TX?', type: 'FAQ', icon: 'fa-exchange', url: '#faq4' },
        { title: 'How to connect devices over WiFi', type: 'FAQ', icon: 'fa-wifi', url: '#faq5' },
        { title: 'Technical Support', type: 'FAQ', icon: 'fa-headphones', url: '#faq6' },
        <?php foreach ($categories as $category): ?>
        { 
            title: '<?php echo addslashes($category->title); ?>', 
            type: 'Category', 
            icon: 'fa-folder-open-o',
            url: '<?php echo get_uri($type . "/category/" . $category->id); ?>'
        },
        <?php endforeach; ?>
    ];

    // ============================================================
    // BUILD SEARCH INDEX for faster searching
    // ============================================================
    const searchIndex = {};
    searchData.forEach((item, index) => {
        const words = item.title.toLowerCase().split(' ');
        words.forEach(word => {
            if (!searchIndex[word]) {
                searchIndex[word] = [];
            }
            if (!searchIndex[word].includes(index)) {
                searchIndex[word].push(index);
            }
        });
    });

    // ============================================================
    // CACHE DOM ELEMENTS
    // ============================================================
    const searchInput = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('searchResults');
    const searchLoading = document.getElementById('searchLoading');
    let searchTimeout;
    let keyboardIndex = -1;

    // ============================================================
    // PERFORM SEARCH - Optimized with index
    // ============================================================
    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        
        if (!query) {
            resultsContainer.classList.remove('active');
            resultsContainer.innerHTML = '';
            searchLoading.classList.remove('active');
            return;
        }

        // Show loading indicator
        searchLoading.classList.add('active');

        // Use setTimeout to allow UI to update
        setTimeout(() => {
            // Find results using index for faster search
            const queryWords = query.split(' ');
            const matchedIndices = new Set();
            
            queryWords.forEach(word => {
                if (searchIndex[word]) {
                    searchIndex[word].forEach(idx => matchedIndices.add(idx));
                }
            });

            // If no matches found via index, do full search
            let results = [];
            if (matchedIndices.size > 0) {
                results = Array.from(matchedIndices)
                    .map(idx => searchData[idx])
                    .filter(item => {
                        // Verify all words match (AND search)
                        return queryWords.every(word => 
                            item.title.toLowerCase().includes(word)
                        );
                    });
            } else {
                // Fallback: full search
                results = searchData.filter(item => 
                    item.title.toLowerCase().includes(query)
                );
            }

            // Sort results by relevance (exact matches first)
            results.sort((a, b) => {
                const aExact = a.title.toLowerCase() === query ? 1 : 0;
                const bExact = b.title.toLowerCase() === query ? 1 : 0;
                return bExact - aExact || a.title.length - b.title.length;
            });

            // Hide loading
            searchLoading.classList.remove('active');

            // Display results
            if (results.length > 0) {
                let html = `<div class="search-result-count">Found ${results.length} result${results.length > 1 ? 's' : ''}</div>`;
                results.forEach(item => {
                    const highlightedTitle = highlightText(item.title, query);
                    html += `
                        <div class="search-result-item" data-url="${item.url}" onclick="navigateTo('${item.url}')">
                            <i class="fa ${item.icon}"></i>
                            <span>${highlightedTitle}</span>
                            <span class="result-type">${item.type}</span>
                        </div>
                    `;
                });
                resultsContainer.innerHTML = html;
                resultsContainer.classList.add('active');
                keyboardIndex = -1;
            } else {
                resultsContainer.innerHTML = `
                    <div class="no-results-found">
                        <i class="fa fa-search"></i>
                        No results found for "<strong>${query}</strong>"
                        <p style="margin-top:10px;font-size:0.9rem;color:#aaa;">
                            Try using different keywords or check your spelling
                        </p>
                    </div>
                `;
                resultsContainer.classList.add('active');
            }
        }, 100);
    }

    // ============================================================
    // HIGHLIGHT SEARCH TERMS
    // ============================================================
    function highlightText(text, query) {
        const words = query.split(' ');
        let highlighted = text;
        words.forEach(word => {
            const regex = new RegExp(`(${word})`, 'gi');
            highlighted = highlighted.replace(regex, '<mark>$1</mark>');
        });
        return highlighted;
    }

    // ============================================================
    // NAVIGATE TO RESULT
    // ============================================================
    function navigateTo(url) {
        if (url.startsWith('#')) {
            const element = document.querySelector(url);
            if (element) {
                const offset = 100;
                const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
                window.scrollTo({
                    top: elementPosition - offset,
                    behavior: 'smooth'
                });
                const question = element.querySelector('.faq-question');
                if (question) {
                    toggleFAQ(question);
                }
            }
        } else {
            window.location.href = url;
        }
        resultsContainer.classList.remove('active');
        searchInput.value = '';
        keyboardIndex = -1;
    }

    // ============================================================
    // KEYBOARD NAVIGATION for search results
    // ============================================================
    function navigateResults(direction) {
        const items = document.querySelectorAll('.search-result-item');
        if (items.length === 0) return;

        // Remove previous selection
        items.forEach(item => item.classList.remove('keyboard-selected'));

        keyboardIndex += direction;
        if (keyboardIndex < 0) keyboardIndex = items.length - 1;
        if (keyboardIndex >= items.length) keyboardIndex = 0;

        items[keyboardIndex].classList.add('keyboard-selected');
        items[keyboardIndex].scrollIntoView({ block: 'nearest' });
    }

    // ============================================================
    // EVENT LISTENERS
    // ============================================================
    
    // Search on keyup with debounce
    searchInput.addEventListener('keyup', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
        
        // Keyboard navigation
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateResults(1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateResults(-1);
        }
    });

    // Handle Enter key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (keyboardIndex >= 0) {
                const selected = document.querySelector('.search-result-item.keyboard-selected');
                if (selected) {
                    selected.click();
                    return;
                }
            }
            const firstResult = document.querySelector('.search-result-item');
            if (firstResult) firstResult.click();
        }
        
        // Escape to close results
        if (e.key === 'Escape') {
            resultsContainer.classList.remove('active');
            searchInput.blur();
        }
    });

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!resultsContainer.contains(e.target) && e.target !== searchInput) {
            resultsContainer.classList.remove('active');
        }
    });

    // Clear results when input loses focus with delay
    searchInput.addEventListener('blur', function() {
        setTimeout(() => {
            if (!resultsContainer.matches(':hover')) {
                resultsContainer.classList.remove('active');
            }
        }, 200);
    });

    // ============================================================
    // FAQ TOGGLE - Optimized
    // ============================================================
    function toggleFAQ(element) {
        const answer = element.nextElementSibling;
        const arrow = element.querySelector('.arrow');
        const isOpen = answer.classList.contains('open');
        const faqItem = element.closest('.faq-item');
        
        // Close all others
        document.querySelectorAll('.faq-answer').forEach(a => {
            a.classList.remove('open');
            a.style.paddingTop = '0';
            a.style.paddingBottom = '0';
        });
        document.querySelectorAll('.faq-question .arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('active'));
        
        if (!isOpen) {
            answer.classList.add('open');
            answer.style.paddingTop = '30px';
            answer.style.paddingBottom = '30px';
            arrow.style.transform = 'rotate(180deg)';
            element.classList.add('active');
            
            // Smooth scroll
            setTimeout(() => {
                const offset = 120;
                const elementPosition = faqItem.getBoundingClientRect().top + window.pageYOffset;
                const isLastFAQ = faqItem === document.querySelector('.faq-item:last-child');
                const extraOffset = isLastFAQ ? 60 : 0;
                window.scrollTo({
                    top: elementPosition - offset - extraOffset,
                    behavior: 'smooth'
                });
            }, 100);
        }
    }

    // ============================================================
    // KEYBOARD SHORTCUT: Ctrl+/ to focus search
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

    console.log('✅ Search optimized with index, keyboard navigation, and highlighting');
</script>
</body>
</html>