<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mobile Support</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            text-align: center;
            font-size: 16px;
        }
        .button {
            display: block;
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .button:active {
            transform: scale(0.98);
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        .video-btn {
            background: #3498db;
            background: linear-gradient(to right, #3498db, #2980b9);
        }
        .guide-btn {
            background: #e67e22;
            background: linear-gradient(to right, #e67e22, #d35400);
        }
        .alexa-video-btn {
            background: #8e44ad;
            background: linear-gradient(to right, #8e44ad, #663399);
        }
        .alexa-guide-btn {
            background: #2ecc71;
            background: linear-gradient(to right, #2ecc71, #27ae60);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Wi-Fi Video Button -->
        <a href="https://efactor.tech/docs/wifi_connect.mp4" 
           class="button video-btn"
           id="videoBtn">
           Wi-Fi Connect Video
        </a>
        
        <!-- User Guide Button -->
        <a href="https://efactor.tech/docs/Fan_Manual.html" 
           class="button guide-btn"
           id="guideBtn">
           User Guide (PDF)
        </a>

        <!-- Alexa Video Button -->
        <a href="https://efactor.tech/docs/alexa_login.mp4" 
           class="button alexa-video-btn"
           id="alexaVideoBtn">
           Alexa Setup Video
        </a>

        <!-- Alexa Guide Button -->
        <a href="https://efactor.tech/docs/Alexa.html" 
           class="button alexa-guide-btn"
           id="alexaGuideBtn">
           Alexa User Guide (PDF)
        </a>

    </div>

    <script>
        function isInApp() {
            return (window.navigator.userAgent.includes('MyAppName'));
        }

        if (isInApp()) {
            document.getElementById('videoBtn').href = 'myapp://open/video';
            document.getElementById('guideBtn').href = 'myapp://open/guide';
            document.getElementById('alexaVideoBtn').href = 'myapp://open/alexa_video';
            document.getElementById('alexaGuideBtn').href = 'myapp://open/alexa_guide';
        }
    </script>
</body>
</html>
