

<?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;

?>





<!-- Bootstrap CSS-->
    <!--link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css"-->
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/grasp_mobile_progress_circle-1.0.0.min.css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
 <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
 <!-- JavaScript for Charts and Videos -->
     <script>
    let currentUnreadCount = <?php echo count_unread_message(); ?>;
    
     // Initialize with the value passed from controller
  //  let currentUnreadCount = <?php echo $unread_count ?? 0; ?>;
    
    // Rest of your JavaScript code remains the same...
    let titleUpdateInterval;
    let isTitleLocked = false;
    
   const POLL_INTERVAL = 5000; // 5 seconds for message count
const FULL_REFRESH_INTERVAL = 60000; // 60 seconds for full refresh

let lastFullRefresh = Date.now();

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        window.location.reload(true); // true forces reload from server
    }, 60000);
});

function updateTitleWithUnreadCount() {
  fetch('<?php echo site_url("messages/get_unread_count"); ?>?t=' + Date.now())
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            // Check if we need full refresh
            if (Date.now() - lastFullRefresh >= FULL_REFRESH_INTERVAL) {
                lastFullRefresh = Date.now();
                window.location.reload();
                return;
            }
            
            // Your existing update logic
            if (data.unread_count !== currentUnreadCount) {
                currentUnreadCount = data.unread_count;
                const newTitle = `(${data.unread_count}) Dashboard`;
                document.title = newTitle;
                lockTitle(newTitle);
                
                if (data.unread_count > currentUnreadCount) {
                    showNewMessageNotification(data.unread_count);
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            setTimeout(updateTitleWithUnreadCount, POLL_INTERVAL);
        });
}
    function scheduleNextUpdate(delay) {
        clearInterval(titleUpdateInterval);
        titleUpdateInterval = setTimeout(updateTitleWithUnreadCount, delay);
    }

    function updateDocumentTitle(count) {
        const newTitle = `(${count}) Dashboard`;
        
        // Update title
        document.title = newTitle;
        
        // Lock the title to prevent modifications
        if (!isTitleLocked) {
            lockTitle(newTitle);
        }
    }

    function lockTitle(title) {
        try {
            Object.defineProperty(document, 'title', {
                get: function() { return title; },
                set: function(newTitle) {
                    // Only allow updates that match our pattern
                    if (/^\(\d+\) Dashboard$/.test(newTitle)) {
                        title = newTitle;
                    }
                    return title;
                },
                configurable: true
            });
            isTitleLocked = true;
        } catch (e) {
            console.warn("Title lock failed:", e);
        }
    }

    function showNewMessageNotification(count) {
        if (!("Notification" in window)) return;
        
        if (Notification.permission === "granted") {
            new Notification(`New messages!`, {
                body: `You have ${count} unread messages`,
                icon: '/path/to/icon.png'
            });
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    showNewMessageNotification(count);
                }
            });
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial title
        updateDocumentTitle(currentUnreadCount);
        
        // Start polling
        updateTitleWithUnreadCount();
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            isPageVisible = !document.hidden;
            if (isPageVisible) {
                // Immediate update when page becomes visible
                updateTitleWithUnreadCount();
            }
        });
    });

    // Clean up when page unloads
    window.addEventListener('beforeunload', function() {
        clearInterval(titleUpdateInterval);
    });

</script>



   <style>
/* Wrapper Custom Style */
.wrapper {
    background: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px) !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 30px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    animation: floaty 3s ease-in-out infinite !important;
    transition: all 0.3s ease !important;
}

.wrapper:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 25px 50px rgba(0, 123, 255, 0.25) !important;
}

.wrapper .icon i {
    font-size: 30px !important;
    color: #007bff !important;
    transition: transform 0.3s ease !important;
}

.wrapper:hover .icon i {
    transform: rotate(20deg) scale(1.3) !important;
}

/* Floating animation */
@keyframes floaty {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}
body, html {
    height: 100%;
    width: 100%;
  }

  .wrapper {
    display: flex;
    height: 100vh;
    width: 100vw;
  }

  .sidebar {
    width: 250px;
    background-color: #2c2c2c;
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 1000;
  }

  .main-content {
    margin-left: 250px; /* same as sidebar width */
    width: calc(100% - 250px);
    padding: 20px;
  }

.container, .container-fluid {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
  .sidebar {
    width: 250px !important;
    max-width: 250px !important;
  }

  .main-content {
    margin-left: 250px !important;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 100% !important;
      position: relative;
    }
    .main-content {
      margin-left: 0 !important;
    }
  }
</style>
  
    

    <style>
         body {
  font-family: 'Roboto', sans-serif;
  background-color: #DADADA;
  color: #333;
  margin: 0;
  padding: 0;
}


        .count-widget {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .count-widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .count-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2575fc;
        }

        .count-title {
            font-size: 1rem;
            color: #666;
        }

        .video-section {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .video-link {
            display: block;
            background-color: #2575fc;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .video-link:hover {
            background-color: #1a5bbf;
        }

        .download-section {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #2575fc;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a5bbf;
        }

        video {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2575fc;
            color: #ffffff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        .chat-icon:hover {
            background-color: #1a5bbf;
        }

        .chat-box {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .chat-box.show {
            display: block;
        }

        .chat-header {
            background-color: #2575fc;
            color: #ffffff;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .chat-header .close-btn {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            font-size: 18px;
        }

        .chat-body {
            padding: 10px;
            max-height: 300px;
            overflow-y: auto;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
        }

        .chat-footer input {
            flex: 1;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }

        .chat-footer button {
            background-color: #2575fc;
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-footer button:hover {
            background-color: #1a5bbf;
        }
.count-widget {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 40px 30px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 320px;
    margin: 20px auto;
}

.count-widget:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.count-number {
    font-size: 3.5rem;
    font-weight: 800;
    color: #1d4ed8; /* A more solid, modern blue */
    margin-bottom: 10px;
}

.count-title {
    font-size: 1.25rem;
    color: #4b5563; /* Tailwind-inspired gray tone */
    font-weight: 500;
    letter-spacing: 0.5px;
}


.dashboard-header {
    background: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 20px 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    position: relative !important;
    z-index: auto !important;
    overflow: visible !important;
}

.dashboard-header h1 {
    font-size: 20px !important;      /* 👈 Choti font size */
    font-weight: 600 !important;     /* Optional: slightly bold */
    color: #333 !important;          /* Text color */
    margin: 0 !important;            /* Remove extra space */
}


.dashboard-header {
    margin-top: 50px; /* Adjust as needed */
}


.scroll-heading {
  white-space: nowrap !important;
  overflow: hidden !important;
  position: relative !important;
  width: 100% !important;
}

.scroll-heading h1 {
  display: inline-block !important;
  font-size: 1.5rem !important;
  color: #007bff !important;
  animation: scroll-right 10s linear infinite !important;
  position: absolute;
  left: -100%;
}

@keyframes scroll-right {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}


/* Section background */
.dashboard-section {
    padding: 60px 20px;
    background: radial-gradient(circle at top left, #e0f7ff, #f0f4ff, #ffffff);
    border-radius: 30px;
    margin: 30px 0;
}

/* Each box */
.count-widget {
    background: linear-gradient(145deg, #f0faff, #ffffff);
    border-radius: 20px;
    padding: 30px 20px;
    box-shadow: 0 8px 16px rgba(0, 123, 255, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.count-widget:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 18px 30px rgba(0, 123, 255, 0.15);
    background: linear-gradient(145deg, #e6f3ff, #ffffff);
}

.count-number {
    font-size: 2.8rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 10px;
}

.count-title {
    font-size: 1rem;
    color: #333;
    font-weight: 500;
}

@media (max-width: 768px) {
    .count-widget {
        margin-bottom: 20px;
    }
}

.dashboard-section::before {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, #d0eaff, transparent 60%);
    top: -100px;
    left: -100px;
    border-radius: 50%;
    z-index: 0;
}
    </style>
</head>
<body>
     
       <div class="scroll-heading">
   <div class="container-fluid">
        <!-- Dashboard Header -->


       <?php if ($login_user->is_admin) { ?>
     
        <div class="dashboard-header text-center py-5">
            <h1 class="display-4 font-weight-bold text-dark mb-3">Welcome, <?php echo htmlspecialchars($appusername); ?></h1>


         <?php if ($appusername === 'vijay.tapadia@gmail.com'): ?>
            <img 
                src="https://efactor.tech/files/brand_logo/dhrio.jpg"
                alt="Brand Logo"
                style="max-width:200px; margin-top:20px;"
            >
        <?php endif; ?>


            
        </div>


<div class="row text-center justify-content-center">
    <div class="col-md-2 col-6 mb-4">
        <a href="clients/index" class="text-decoration-none">
            <div class="count-widget">
                <div class="count-number"><?php echo htmlspecialchars($vendorCount); ?></div>
                <div class="count-title">Vendors</div>
            </div>
        </a>
    </div>

    <div class="col-md-2 col-6 mb-4">
        <a href="user_details/index" class="text-decoration-none">
            <div class="count-widget">
                <div class="count-number"><?php echo htmlspecialchars($appusers); ?></div>
                <div class="count-title">App Users</div>
            </div>
        </a>
    </div>

    <div class="col-md-2 col-6 mb-4">
        <a href="items/index?search=Online" class="text-decoration-none">
            <div class="count-widget">
                <div class="count-number"><?php echo htmlspecialchars($onlineproducts); ?></div>
                <div class="count-title">Online Products</div>
            </div>
        </a>
    </div>

    <div class="col-md-2 col-6 mb-4">
        <a href="items/index?search=Offline" class="text-decoration-none">
            <div class="count-widget">
                <div class="count-number"><?php echo htmlspecialchars($offlineproducts); ?></div>
                <div class="count-title">Offline Products</div>
            </div>
        </a>
    </div>

    <div class="col-md-2 col-6 mb-4">
        <a href="items/index" class="text-decoration-none">
            <div class="count-widget">
                <div class="count-number"><?php echo htmlspecialchars($totalmodules); ?></div>
                <div class="count-title">Total Modules</div>
            </div>
        </a>
    </div>
</div>

<?php } else { ?>


  <div class="scroll-heading">
    <div class="dashboard-header text-center py-5">
            <h1 class="display-4 font-weight-bold text-dark mb-3">Welcome <?php echo htmlspecialchars($appusername); ?></h1>
</div>

       
    <div class="module-stats-row d-flex justify-content-center align-items-start flex-wrap gap-4 my-5">
  <div class="stat-card text-center">
    <div class="stat-circle">
      <div class="count-number"><?php echo htmlspecialchars($totalusermodules); ?></div>
    </div>
    <div class="stat-label">TOTAL USER MODULES</div>
  </div>
  <div class="stat-card text-center">
    <div class="stat-circle">
      <div class="count-number"><?php echo htmlspecialchars($totaluseronlinemodules); ?></div>
    </div>
    <div class="stat-label">ONLINE MODULES</div>
  </div>
  <div class="stat-card text-center">
    <div class="stat-circle">
      <div class="count-number"><?php echo htmlspecialchars($totaluserofflinemodules); ?></div>
    </div>
    <div class="stat-label">OFFLINE MODULES</div>
  </div>
</div>

<style>
.module-stats-row {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 2rem;
}

.stat-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 200px;
}

.stat-circle {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 2rem;
  font-weight: bold;
  color: #007bff;
  margin-bottom: 10px;
}

.stat-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.stat-link {
  text-decoration: none;
  color: inherit;
  transition: transform 0.5s ease;
  display: inline-block;
}

.stat-link:hover {
  transform: scale(1.05);
}

.stat-circle {
  width: 120px;
  height: 120px;
  margin: 0 auto 15px;
  border-radius: 50%;
  background: linear-gradient(135deg, #007bff, #66b2ff);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
  transition: 0.4s ease;
}

.count-number {
  font-size: 32px;
  font-weight: bold;
  color: #fff;
}

.stat-label {
  font-size: 14px;
  text-transform: uppercase;
  font-weight: 600;
  color: #333;
}
@keyframes floatY {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}

@keyframes floatX {
  0% { transform: translateX(0); }
  50% { transform: translateX(10px); }
  100% { transform: translateX(0); }
}

.stat-circle {
  width: 120px;
  height: 120px;
  margin: 0 auto 15px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2); /* translucent white */
  backdrop-filter: blur(8px); /* glass effect */
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: bold;
  color: #007bff; /* blue value text */
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  animation: floatY 2.8s ease-in-out infinite alternate,
             floatX 3.2s ease-in-out infinite alternate;
  transition: transform 0.4s ease;
}

</style>
<style>
body {
  background: linear-gradient(135deg, #f1f4f9, #e3f2fd);
  overflow-x: hidden;
  position: relative;
}

body::before,
body::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.2;
  z-index: -1;
  animation: floatBlob 10s ease-in-out infinite alternate;
}

body::before {
  width: 400px;
  height: 400px;
  background: #2196f3;
  top: -100px;
  left: -100px;
}

body::after {
  width: 500px;
  height: 500px;
  background: #00bcd4;
  bottom: -120px;
  right: -120px;
}

@keyframes floatBlob {
  0% { transform: translateY(0); }
  100% { transform: translateY(30px); }
}
.animated-waves {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  overflow: hidden;
  z-index: -1;
}


body {
    background-color: #f3f6fd;
    font-family: 'Segoe UI', sans-serif;
}
body .container:first-of-type::before {
  content: '';
  position: absolute;
  top: -60px;
  left: -60px;
  width: 180px;
  height: 180px;
  background: #cceeff;
  border-radius: 50%;
  opacity: 0.2;
  z-index: 0;
}

body .container:first-of-type * {
  position: relative;
  z-index: 1;
}

/* -------------------- Header -------------------- */


/* -------------------- Count Widget -------------------- */
.count-widget {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
    transition: 0.4s ease;
    animation: fadeInUp 0.6s ease;
    position: relative;
    overflow: hidden;
}

.count-widget::before {
    content: "";
    position: absolute;
    width: 120%;
    height: 100%;
    background: linear-gradient(120deg, rgba(0,123,255,0.15), transparent);
    top: 0;
    left: -100%;
    transform: skewX(-25deg);
    animation: shine 2s infinite;
}

.count-widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 123, 255, 0.3);
}

.count-number {
    font-size: 48px;
    font-weight: 700;
    color: #007bff;
}

.count-title {
    font-size: 14px;
    color: #555;
    font-weight: 600;
    text-transform: uppercase;
}

/* -------------------- Cards (Charts) -------------------- */
.card {
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    animation: fadeIn 1s ease;
}

.card-header {
    background-color: #f1f4f8;
    font-weight: 700;
    font-size: 16px;
    color: #333;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
}
#addmodel, #apkUpload {
  animation: floatIn 0.8s ease forwards;
  transform: translateY(50px);
  opacity: 0;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

#addmodel:hover, #apkUpload:hover {
  transform: translateY(-10px) scale(1.03);
  transition: 0.4s ease;
}

#addmodel .icon i, #apkUpload .icon i {
  font-size: 2rem;
  color: #007bff;
  transition: transform 0.3s ease;
}

#addmodel:hover .icon i, #apkUpload:hover .icon i {
  transform: rotate(10deg) scale(1.2);
}

.download-link a:hover,
#apkUploadForm button:hover {
  transform: scale(1.05);
  box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
  transition: 0.3s ease;
}

@keyframes floatIn {
  0% {
    transform: translateY(50px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

#apkUpload i {
  color: #28a745;
}

/* File input feedback */
#fileName {
  font-size: 0.75rem;
  color: #6c757d;
}

/* Unique ID input */
#registrationResult {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.features-row {
  background-color: #f8faff;
  border-radius: 30px;
  padding: 40px 20px;
  margin-bottom: 30px;
}
.action-tile {
  background: transparent;
  transition: all 0.4s ease;
  cursor: pointer;
  text-align: center;
  padding: 15px 10px;
  border-radius: 15px;
  position: relative;
}

.action-tile:hover {
  transform: scale(1.05) translateY(-5px);
}

.icon-circle {
  width: 60px;
  height: 60px;
  margin: 0 auto 10px auto;
  border-radius: 50%;
  background: #007bff;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.action-tile:hover .icon-circle {
  transform: rotate(10deg) scale(1.1);
}

.action-label {
  font-weight: 600;
  color: #212529;
  font-size: 0.95rem;
  margin-bottom: 5px;
}

/* Form inside tile */
.action-tile form .btn,
.action-tile form label {
  font-size: 0.8rem;
  border-radius: 20px;
}

#fileName {
  font-size: 0.75rem;
  color: #6c757d;
}
/* Base style for tile */
.action-tile {
  background: transparent;
  transition: all 0.4s ease;
  cursor: pointer;
  text-align: center;
  padding: 15px 10px;
  border-radius: 15px;
  position: relative;
}

.action-tile:hover {
  transform: scale(1.05) translateY(-5px);
}

/* Animated glowing icon circle */
.icon-circle {
  width: 70px;
  height: 70px;
  margin: 0 auto 10px auto;
  border-radius: 50%;
  background: radial-gradient(circle at center, #4facfe, #00f2fe);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  position: relative;
  animation: pulseGlow 2s infinite ease-in-out;
  box-shadow: 0 0 15px rgba(79, 172, 254, 0.6);
  transition: transform 0.3s ease;
}

.action-tile:hover .icon-circle {
  transform: scale(1.2) rotate(10deg);
}

/* Label below icons */
.action-label {
  font-weight: 600;
  color: #212529;
  font-size: 0.95rem;
  margin-bottom: 5px;
}

/* Glow Pulse Animation */
@keyframes pulseGlow {
  0% {
    box-shadow: 0 0 10px rgba(79, 172, 254, 0.5), 0 0 20px rgba(79, 172, 254, 0.3);
  }
  50% {
    box-shadow: 0 0 20px rgba(0, 242, 254, 0.7), 0 0 30px rgba(0, 242, 254, 0.4);
  }
  100% {
    box-shadow: 0 0 10px rgba(79, 172, 254, 0.5), 0 0 20px rgba(79, 172, 254, 0.3);
  }
}

/* Button styles inside tile */
.action-tile form .btn,
.action-tile form label {
  font-size: 0.8rem;
  border-radius: 20px;
}

#fileName {
  font-size: 0.75rem;
  color: #6c757d;
}
.bg-primary-gradient {
  background: radial-gradient(circle at center, #007bff, #00c6ff);
}

.bg-warning-gradient {
  background: radial-gradient(circle at center, #ffc107, #ff8c00);
}

.bg-success-gradient {
  background: radial-gradient(circle at center, #28a745, #a0ffcb);
}
.action-tile:hover .icon-circle {
  animation: bounceIcon 0.6s;
}

@keyframes bounceIcon {
  0%   { transform: scale(1.1) translateY(0); }
  50%  { transform: scale(1.2) translateY(-5px); }
  100% { transform: scale(1.1) translateY(0); }
}
.dashboard-actions {
  background: linear-gradient(135deg, #e0f7fa, #f9f9ff);
  border-radius: 25px;
  padding: 50px 30px;
  box-shadow: inset 0 0 25px rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
  margin-bottom: 30px;
}

/* Optional: animated background circles */
.dashboard-actions::before,
.dashboard-actions::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  opacity: 0.2;
  z-index: 0;
  animation: float 6s ease-in-out infinite alternate;
}

.dashboard-actions::before {
  width: 200px;
  height: 200px;
  background: #00bcd4;
  top: -50px;
  left: -50px;
}

.dashboard-actions::after {
  width: 300px;
  height: 300px;
  background: #81d4fa;
  bottom: -100px;
  right: -100px;
}

@keyframes float {
  0% { transform: translateY(0); }
  100% { transform: translateY(-20px); }
}
#registrationResult input {
  font-size: 13px;
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 5px 8px;
}
.action-tile:nth-child(1) .icon-circle {
  background: radial-gradient(circle at center, #00c6ff, #0072ff);
  box-shadow: 0 0 15px rgba(0, 114, 255, 0.5);
  animation: blueGlow 2s infinite ease-in-out;
}

.action-tile:nth-child(2) .icon-circle {
  background: radial-gradient(circle at center, #28a745, #a0ffcb);
  box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
  animation: greenGlow 2s infinite ease-in-out;
}

.action-tile:nth-child(3) .icon-circle {
  background: radial-gradient(circle at center, #ff8c00, #ffc107);
  box-shadow: 0 0 15px rgba(255, 140, 0, 0.5);
  animation: orangeGlow 2s infinite ease-in-out;
}
/* ----------- Dashboard Action Tile Styling ----------- */
.action-tile {
  background: #ffffff;
  border-radius: 20px;
  padding: 30px 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.action-tile:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 30px rgba(0, 0, 0, 0.1);
}

/* Icon style (now non-circular) */
.icon-circle {
  font-size: 32px;
  width: 60px;
  height: 60px;
  line-height: 60px;
  margin: 0 auto 15px;
  border-radius: 12px; /* Rounded square */
  background: linear-gradient(135deg, #007bff, #00d4ff);
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Specific bg overrides if you still want custom colors */
.bg-primary {
  background: linear-gradient(135deg, #007bff, #00d4ff) !important;
}

.bg-warning {
  background: linear-gradient(135deg, #ffc107, #ffeb3b) !important;
  color: #000 !important;
}

.bg-success {
  background: linear-gradient(135deg, #28a745, #a2f9a2) !important;
}

/* Text label styling */
.action-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}
/* Compact and uniform cards */
.action-tile {
  background: #fff;
  border-radius: 20px;
  padding: 20px 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  height: 140px;         /* 👈 Compact fixed height */
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* Hover effect */
.action-tile:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Icon circle box */
.icon-circle {
  font-size: 20px;
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 10px;
  color: #fff;
}

/* Color gradients */
.bg-primary {
  background: linear-gradient(135deg, #007bff, #00d4ff) !important;
}

.bg-warning {
  background: linear-gradient(135deg, #ffc107, #ffeb3b) !important;
  color: #000 !important;
}

.bg-success {
  background: linear-gradient(135deg, #28a745, #a2f9a2) !important;
}

/* Text label */
.action-label {
  font-size: 13px;
  font-weight: 600;
  color: #222;
}

/* Equal spacing between columns */
.dashboard-actions .col-xl-2,
.dashboard-actions .col-md-4,
.dashboard-actions .col-6 {
  padding-left: 10px;
  padding-right: 10px;
}
.action-tile {
  position: relative;
  overflow: hidden; /* Ensure animation stays inside */
  background: #fff;
  border-radius: 20px;
  padding: 20px 10px;
  height: 140px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

/* Hover effect */
.action-tile:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Moving line animation using pseudo element */
.action-tile::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: repeating-linear-gradient(
    45deg,
    rgba(0, 0, 0, 0.03) 0,
    rgba(0, 0, 0, 0.03) 1px,
    transparent 1px,
    transparent 6px
  );
  animation: moveLines 2s linear infinite;
  z-index: 0;
}

/* Bring content above animation */
.action-tile * {
  position: relative;
  z-index: 1;
}

/* Animation keyframes */
@keyframes moveLines {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(10px, 10px);
  }
}
/* Base card style */
.action-tile {
  position: relative;
  border-radius: 20px;
  padding: 20px 10px;
  background: #fff;
  height: 140px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.action-tile:hover {
  transform: translateY(-6px);
}

/* Chalte phirte line effect outside the box */
.action-tile::before {
  content: '';
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  background: repeating-linear-gradient(
    45deg,
    rgba(0, 183, 255, 0.3) 0px,
    rgba(0, 183, 255, 0.3) 2px,
    transparent 2px,
    transparent 6px
  );
  background-size: 200% 200%;
  animation: stripeMove 2s linear infinite;
  z-index: 0;
  border-radius: 24px;
  pointer-events: none;
}

/* Make content stay above animation */
.action-tile * {
  position: relative;
  z-index: 1;
}

/* Animate the stripes diagonally */
@keyframes stripeMove {
  from {
    background-position: 0 0;
  }
  to {
    background-position: 40px 40px;
  }
}

        body {
            font-family: Arial, sans-serif;
            background: #f0f5ff;
            padding: 20px;
        }

        .scroll-container {
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 15px;
            position: relative;
        }

        .video-row {
            display: inline-flex;
            gap: 20px;
            animation: scroll 10s linear infinite;
        }

        .video-card {
            min-width: 300px;
            height: 400px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            transition: transform 0.3s;
        }

        .video-card:hover {
            transform: translateY(-5px);
        }

        video {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }

        .video-title {
            padding: 10px 0;
            font-weight: bold;
            font-size: 16px;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body, html {
    height: 100%;
    width: 100%;
  }

  .wrapper {
    display: flex;
    height: 100vh;
    width: 100vw;
  }

  .sidebar {
    width: 250px;
    background-color: #2c2c2c;
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 1000;
  }

  .main-content {
    margin-left: 250px; /* same as sidebar width */
    width: calc(100% - 250px);
    padding: 20px;
  }

.container, .container-fluid {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
  .sidebar {
    width: 250px !important;
    max-width: 250px !important;
  }

  .main-content {
    margin-left: 250px !important;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 100% !important;
      position: relative;
    }
    .main-content {
      margin-left: 0 !important;
    }
  }

    </style>
<div class="row justify-content-center text-center mb-5 dashboard-actions">

  <!-- 1. Add Model -->
  <div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile" onclick="location.href='<?= site_url('items/vendor'); ?>'">
      <div class="icon-circle bg-primary"><i class="fas fa-plus"></i></div>
      <div class="action-label">Add Model</div>
    </div>
  </div>

  <!-- 2. APP ID Generation -->
  <div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile" onclick="location.href='<?= site_url('items/vendor'); ?>'">
      <div class="icon-circle bg-warning text-dark"><i class="fas fa-key"></i></div>
      <div class="action-label">APP ID Generation</div>
    </div>
  </div>

  <!-- 3. App Registration -->
  <div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile">
      <div class="icon-circle bg-success"><i class="fas fa-upload"></i></div>
      <div class="action-label">App Registration</div>
      <!-- File upload form here -->
    </div>
  </div>

</div>
<div>

<?php } ?>

      <!-- Video Slideshow with Thumbnails and Animation -->
  <style>
    body {
      background: linear-gradient(to right, #eaf6f6, #f0f5ff);
      font-family: Arial, sans-serif;
      padding: 40px;
    }

    /* SCROLL CONTAINER */
    .scroll-container {
      overflow-x: auto;
      overflow-y: hidden;
      white-space: nowrap;
      padding-bottom: 15px;
    }

    /* HORIZONTAL VIDEO BOXES */
    .video-row {
      display: inline-flex;
      gap: 20px;
    }

    .video-card {
      min-width: 300px;
      height: 400px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      text-align: center;
      transition: transform 0.3s;
    }

    .video-card:hover {
      transform: translateY(-5px);
    }

    video {
      width: 100%;
      height: 320px;
      object-fit: cover;
    }

    .video-thumbnail {
      width: 100%;
      height: 220px;
      object-fit: cover;
      cursor: pointer;
    }

    .video-title {
      padding: 10px 0;
      font-weight: bold;
      font-size: 16px;
    }

    /* Nice Scrollbar */
    .scroll-container::-webkit-scrollbar {
      height: 10px;
    }

    .scroll-container::-webkit-scrollbar-thumb {
      background-color: #bbb;
      border-radius: 6px;
    }

    .scroll-container::-webkit-scrollbar-track {
      background: transparent;
    }
    .shine-heading {
  position: relative;
  display: inline-block;
  font-size: 32px;
  font-weight: bold;
  text-align: center;
  background: linear-gradient(120deg, #000, #007bff, #000);
  background-size: 200% auto;
  color: transparent;
  background-clip: text;
  -webkit-background-clip: text;
  animation: shineText 2s linear infinite;
}

@keyframes shineText {
  0% {
    background-position: 0% center;
  }
  100% {
    background-position: 200% center;
  }
}

  </style>
</head>
<body>
<center>

 <h2 class="shine-heading"> Video Tutorials</h2>
    <div class="scroll-container" id="scrollContainer">
        <div class="video-row" id="videoRow">
            <div class="video-card">
                <video id="installVideo" controls>
                    <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">
                </video>
                <div class="video-title">APP INSTALL VIDEO</div>
            </div>

            <div class="video-card">
                <video id="peerVideo" controls>
                    <source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4">
                </video>
                <div class="video-title">PEER TO PEER</div>
            </div>

            <div class="video-card">
                <video id="pingVideo" controls>
                    <source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4">
                </video>
                <div class="video-title">PING</div>
            </div>

            <div class="video-card">
                <video id="networkVideo" controls>
                    <source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4">
                </video>
                <div class="video-title">ADDING DEVICE TO NETWORK</div>
            </div>
        </div>
    </div><br><br>

        <div class="row justify-content-center">
  <div class="d-flex justify-content-center gap-4 flex-wrap">
    <div class="download-section">
        <h4>Download API</h4>
        <p>File Name: API_Specification.xlsx</p>
        <a href="https://efactor.tech/appstore/API_Specification.xlsx" class="btn btn-primary" download>Download</a>
    </div>
    <div class="download-section">
        <h4>Efactor Library</h4>
        <a href="https://efactor.tech/v8/EfactorLibrary.html" class="btn btn-primary" target="_blank">Open</a>
    </div>
  </div>
</div>
<style>
.download-section {
  position: relative;
  background: #fff;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  z-index: 1;
  min-width: 260px;
}

/* Animated diagonal stripe background ONLY behind these boxes */
.download-section::before {
  content: '';
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  background: repeating-linear-gradient(
    45deg,
    rgba(0, 140, 255, 0.35) 0px,
    rgba(0, 140, 255, 0.35) 2px,
    transparent 2px,
    transparent 6px
  );
  animation: stripesMove 3s linear infinite;
  border-radius: 24px;
  z-index: 0;
}

/* Keep text and buttons above background */
.download-section * {
  position: relative;
  z-index: 1;
}

@keyframes stripesMove {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: 40px 40px;
  }
}

.download-section {
  min-width: 230px;
  max-width: 300px;
  flex: 1;
  text-align: center;
  padding: 20px;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  background: white;
}
.download-section {
  min-width: 230px;
  max-width: 300px;
  flex: 1;
  text-align: center;
  padding: 25px 20px;
  border-radius: 15px;
  border: none;
  background: linear-gradient(145deg, #ffffff, #f0f0f0);
  box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.05), -10px -10px 30px rgba(255, 255, 255, 0.9);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.download-section:hover {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 20px 40px rgba(0, 123, 255, 0.2);
}

/* Headings styling */
.download-section h4 {
  font-weight: 600;
  margin-bottom: 10px;
  color: #333;
}

/* Animate the download button */
.download-section .btn-primary {
  position: relative;
  overflow: hidden;
  animation: pulseGlow 2s infinite;
  transition: transform 0.3s;
  z-index: 1;
}

.download-section .btn-primary::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 150%;
  height: 150%;
  background: radial-gradient(circle, rgba(0, 123, 255, 0.2), transparent 70%);
  transform: translate(-50%, -50%) scale(0.8);
  opacity: 0;
  transition: all 0.4s ease;
  border-radius: 50%;
  z-index: -1;
}

.download-section .btn-primary:hover {
  transform: scale(1.05);
  animation: pulseGlowHover 1.2s infinite;
}

.download-section .btn-primary:hover::before {
  transform: translate(-50%, -50%) scale(1.2);
  opacity: 1;
}

/* Glow animations */
@keyframes pulseGlow {
  0% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.5);
  }
  70% {
    box-shadow: 0 0 0 12px rgba(0, 123, 255, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
  }
}

@keyframes pulseGlowHover {
  0% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.9);
  }
  50% {
    box-shadow: 0 0 15px 5px rgba(0, 123, 255, 0.4);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
  }
}
.main-wrapper {
  min-height: 100vh !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  display: block;
}
</style>
      
     
<div class="animated-downloads">
  <h5 class="download-title">📁 Download Files</h5>
  <div class="download-links-row">
    <a href="https://efactor.tech/v8/ProvisionActivity.java" download="ProvisionActivity.java" class="download-btn">
      ⬇ ProvisionActivity.java
    </a>
    <a href="https://efactor.tech/v8/activity_provision.xml" download="activity_provision.xml" class="download-btn">
      ⬇ activity_provision.xml
    </a>
  </div>
</div>

</div>
        </div>
  <div style="height: 100px;"></div>
</div>
<style>
.animated-downloads {
  margin: 40px 0;
  animation: fadeInUp 0.6s ease-in-out;
  text-align: center;
}

.download-title {
  font-size: 22px;
  color: #3f51b5;
  margin-bottom: 20px;
  font-weight: 600;
}

.download-links-row {
  display: flex;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
}


.download-btn {
  display: inline-block;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  color: #fff;
  background: linear-gradient(135deg, #89f7fe, #66a6ff);
  border-radius: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(102, 166, 255, 0.2);
  position: relative;
  overflow: hidden;
}

.download-btn:hover {
  transform: translateY(-2px);
  background: linear-gradient(135deg, #66a6ff, #89f7fe);
  box-shadow: 0 6px 14px rgba(102, 166, 255, 0.35);
}

</style>

  <script>
                         //Vedio Running
        const scrollContainer = document.getElementById('scrollContainer');
        const videoRow = document.getElementById('videoRow');

        // Function to start scrolling
        function startScrolling() {
            videoRow.style.animationPlayState = 'running';
        }

        // Function to stop scrolling
        function stopScrolling() {
            videoRow.style.animationPlayState = 'paused';
        }

        // Event listeners for mouse enter and leave
        scrollContainer.addEventListener('mouseenter', stopScrolling);
        scrollContainer.addEventListener('mouseleave', startScrolling);

        // Start scrolling on page load
        startScrolling();
    
  const videos = document.querySelectorAll('video');

  function playOnlyThis(id) {
    videos.forEach(vid => {
      if (vid.id !== id) {
        vid.pause();
        vid.currentTime = 0;
      }
    });
  }

  function playVideo(img, id) {
    const vid = document.getElementById(id);
    img.style.display = 'none';
    vid.style.display = 'block';
    playOnlyThis(id);
    vid.play();
  }

  videos.forEach(video => {
    video.addEventListener('play', () => playOnlyThis(video.id));
  });
                     //vedio script end
                     
document.getElementById('apkFile').addEventListener('change', function () {
  const fileNameDisplay = document.getElementById('fileName');
  const file = this.files[0];
  fileNameDisplay.textContent = file ? file.name : 'No file chosen';
});

document.getElementById('apkUploadForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const generatedId = 'APP-' + Math.random().toString(36).substr(2, 9).toUpperCase();
  document.getElementById('uniqueId').value = generatedId;
  document.getElementById('registrationResult').classList.remove('d-none');
});

function copyUniqueId() {
  const input = document.getElementById('uniqueId');
  input.select();
  input.setSelectionRange(0, 99999);
  document.execCommand("copy");
  alert("Copied: " + input.value);
}

</script>


<script>

            // Display selected file name
            document.getElementById('apkFile').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
                document.getElementById('fileName').textContent = fileName;
            });

            // Handle form submission
            document.getElementById('apkUploadForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const fileInput = document.getElementById('apkFile');
                if (!fileInput.files.length) {
                    alert('Please select an APK file');
                    return;
                }
                
                // Simulate server request (replace with actual AJAX call)
                setTimeout(() => {
                    // Generate a mock unique ID (replace with server response)
                    const uniqueId = 'APP-' + Math.random().toString(36).substr(2, 9).toUpperCase();
                    
                    document.getElementById('uniqueId').value = uniqueId;
                    document.getElementById('registrationResult').classList.remove('d-none');
                    
                  
                }, 1000);
            });

            function copyUniqueId() {
                const uniqueIdInput = document.getElementById('uniqueId');
                uniqueIdInput.select();
                document.execCommand('copy');
                alert('Unique ID copied to clipboard!');
            }


</script>

</body>
</html>  