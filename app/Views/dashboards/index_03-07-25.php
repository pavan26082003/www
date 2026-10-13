



<?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- Lock the title to prevent JavaScript modifications -->

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
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            padding: 40px 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 0;
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
    background-color: #ffffff; /* White background */
    border: 1px solid #e0e0e0; /* Subtle border */
    border-radius: 10px; /* Rounded corners */
    padding: 20px; /* Ample padding */
    text-align: center; /* Center content */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
}

.count-widget:hover {
    transform: translateY(-5px); /* Lift effect on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Enhanced shadow on hover */
}

.count-number {
    font-size: 2.5rem; /* Large font size for numbers */
    font-weight: 700; /* Bold font weight */
    color: #2575fc; /* Blue color for numbers */
}

.count-title {
    font-size: 1rem; /* Medium font size for titles */
    color: #666; /* Medium gray for titles */
}











    </style>
</head>
<body>
    
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




    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <a href="clients/index" class="text-decoration-none">
                <div class="count-widget p-4 rounded shadow-sm border">
                    <div class="count-number h2 text-dark"><?php echo htmlspecialchars($vendorCount); ?></div>
                    <div class="count-title text-muted">Vendors</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="user_details/index" class="text-decoration-none">
                <div class="count-widget p-4 rounded shadow-sm border">
                    <div class="count-number h2 text-dark"><?php echo htmlspecialchars($appusers); ?></div>
                    <div class="count-title text-muted">App Users</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="items/index?search=Online" class="text-decoration-none">
                <div class="count-widget p-4 rounded shadow-sm border">
                    <div class="count-number h2 text-dark"><?php echo htmlspecialchars($onlineproducts); ?></div>
                    <div class="count-title text-muted">Online Products</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="items/index?search=Offline" class="text-decoration-none">
                <div class="count-widget p-4 rounded shadow-sm border">
                    <div class="count-number h2 text-dark"><?php echo htmlspecialchars($offlineproducts); ?></div>
                    <div class="count-title text-muted">Offline Products</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="items/index" class="text-decoration-none">
                <div class="count-widget p-4 rounded shadow-sm border">
                    <div class="count-number h2 text-dark"><?php echo htmlspecialchars($totalmodules); ?></div>
                    <div class="count-title text-muted">Total Modules</div>
                </div>
            </a>
        </div>
    </div>
<?php } else { ?>







        <div class="dashboard-header text-center py-5">
            <h1 class="display-4 font-weight-bold text-dark mb-3">Welcome, <?php echo htmlspecialchars($appusername); ?></h1>
            
        </div>

        <!-- Count Widgets -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <a href="items/index" class="text-decoration-none">
                    <div class="count-widget">
                        <div class="count-number"><?php echo htmlspecialchars($totalusermodules); ?></div>
                        <div class="count-title">Total User Modules</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="items/index?search=Online" class="text-decoration-none">
                    <div class="count-widget">
                        <div class="count-number"><?php echo htmlspecialchars($totaluseronlinemodules); ?></div>
                        <div class="count-title">Online Modules</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="items/index?search=Offline" class="text-decoration-none">
                    <div class="count-widget">
                        <div class="count-number"><?php echo htmlspecialchars($totaluserofflinemodules); ?></div>
                        <div class="count-title">Offline Modules</div>
                    </div>
                </a>
            </div>
        </div>

       <div class="row mb-4">
    <!-- Add Model Button -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="wrapper count-title d-flex justify-content-between align-items-center p-3 shadow-sm border" id="addmodel">
            <div class="icon">
                <i class="fas fa-plus text-primary h4 mb-0"></i>
            </div>
            <div class="name text-center">
                <strong class="text-uppercase d-block text-dark">Add Model</strong>
                <div class="download-link mt-2">
                    <a href="<?php echo site_url('items/vendor'); ?>" class="btn btn-outline-primary btn-sm w-100">
                        Add Model
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- APP ID Generation Button -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="wrapper count-title d-flex justify-content-between align-items-center p-3 shadow-sm border" id="addmodel">
            <div class="icon">
                <i class="fas fa-plus text-primary h4 mb-0"></i>
            </div>
            <div class="name text-center">
                <strong class="text-uppercase d-block text-dark">APP ID Generation</strong>
                <div class="download-link mt-2">
                    <a href="<?php echo site_url('items/vendor'); ?>" class="btn btn-outline-primary btn-sm w-100">
                        APP ID Generation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="wrapper count-title d-flex flex-column justify-content-between align-items-center p-3 shadow-sm border" id="apkUpload">
                    <div class="icon mb-2">
                        <i class="fas fa-upload text-success h4 mb-0"></i>
                    </div>
                    <div class="name text-center w-100">
                        <strong class="text-uppercase d-block text-dark">App Registration</strong>
                        <form id="apkUploadForm" class="mt-2">
                            <div class="form-group mb-2">
                                <input type="file" class="form-control-file d-none" id="apkFile" accept=".apk" required>
                                <label for="apkFile" class="btn btn-outline-success btn-sm w-100">Choose APK</label>
                                <small id="fileName" class="form-text text-muted">No file chosen</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm w-100">Register</button>
                        </form>
                        <div id="registrationResult" class="mt-2 d-none">
                            <p class="mb-1 text-center"><strong>Unique ID:</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="uniqueId" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyUniqueId()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<?php } ?>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Devices Overview</div>
                    <div class="card-body">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Modules Distribution</div>
                    <div class="card-body">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video and Download Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="video-section">
                    <h4>Video Tutorials</h4>
                    <a href="#" class="video-link" onclick="showVideo('installVideo')">APP INSTALL VIDEO</a>
                    <a href="#" class="video-link" onclick="showVideo('peerVideo')">PEER TO PEER</a>
                    <a href="#" class="video-link" onclick="showVideo('pingVideo')">PING</a>
                    <a href="#" class="video-link" onclick="showVideo('networkVideo')">ADDING DEVICE TO NETWORK</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="download-section">
                    <h4>Download API</h4>
                    <p>File Name: API_Specification.xlsx</p>
                    <a href="https://efactor.tech/appstore/API_Specification.xlsx" class="btn btn-primary" download>Download</a>
                </div>
                <div class="download-section mt-4">
                    <h4>Efactor Library</h4>
                    <a href="https://efactor.tech/v8/EfactorLibrary.html" class="btn btn-primary" target="_blank">Open</a>
                </div>
            </div>


           <div class="mt-3">
            <h5>Download Files:</h5>
            <ul class="list-unstyled">
                <li>
                    <a href="https://efactor.tech/v8/ProvisionActivity.java" download="ProvisionActivity.java" class="btn btn-outline-secondary btn-sm">
                        Download ProvisionActivity.java
                    </a>
                </li>
                <li class="mt-2">
                    <a href="https://efactor.tech/v8/activity_provision.xml" download="activity_provision.xml" class="btn btn-outline-secondary btn-sm">
                        Download activity_provision.xml
                    </a>
                </li>
            </ul>
        </div>







        </div>

        <!-- Video Display Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <video id="installVideo" controls>
                    <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <video id="peerVideo" controls>
                    <source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <video id="pingVideo" controls>
                    <source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <video id="networkVideo" controls>
                    <source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>

    <!-- JavaScript for Charts and Videos -->
    <script>
        // Line Chart
        const lineChartCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineChartCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Devices',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                        },
                    },
                },
            },
        });

        // Doughnut Chart (Modules)
        const doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
        const doughnutChart = new Chart(doughnutChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['WiFi', 'Gateway', 'RF'],
                datasets: [{
                    label: 'Modules',
                    data: [30, 50, 20],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                },
            },
        });

        // Video Toggle Function
        function showVideo(videoId) {
            const videos = ['installVideo', 'peerVideo', 'pingVideo', 'networkVideo'];
            videos.forEach(id => {
                const videoElement = document.getElementById(id);
                if (id === videoId) {
                    videoElement.style.display = 'block';
                    videoElement.play();
                } else {
                    videoElement.style.display = 'none';
                    videoElement.pause();
                }
            });
        }

        // Initially hide all videos
        document.addEventListener('DOMContentLoaded', () => {
            const videos = document.querySelectorAll('video');
            videos.forEach(video => video.style.display = 'none');
        });

        // Toggle Chat Box
        function toggleChatBox() {
            const chatBox = document.getElementById('chatBox');
            chatBox.classList.toggle('show');
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
                    
                    // You would typically submit the form here using AJAX
                    // For example:
                    /*
                    const formData = new FormData();
                    formData.append('apk', fileInput.files[0]);
                    
                    fetch('your-server-endpoint', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('uniqueId').value = data.uniqueId;
                        document.getElementById('registrationResult').classList.remove('d-none');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Registration failed');
                    });
                    */
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