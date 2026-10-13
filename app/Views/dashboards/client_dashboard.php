

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
    <title>Internet of Everything Dashboard</title>

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
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .count-widget:hover {
            transform: translateY(-5px);
        }

        .count-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2575fc;
        }

        .count-title {
            font-size: 1.2rem;
            color: #666;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #2575fc;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 500;
        }

        .card-body {
            padding: 20px;
        }

        .video-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .video-link {
            display: block;
            background: #2575fc;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .video-link:hover {
            background: #1a5bbf;
        }

        .download-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #2575fc;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
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

       h1.display-4 {
        margin-bottom: 0;
        line-height: 1.1;
    }

    .welcome-logo {
        max-height: 200px;
        margin-top: -10px; /* reduce vertical gap */
    }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header text-center py-5">
        <!--h1 class="display-4 font-weight-bold text-dark mb-3">Welcome, <?php echo htmlspecialchars($appusername); ?></h1-->



<div class="text-center my-4">
    <h1 class="display-4 font-weight-bold text-dark">
        Welcome,
    </h1>

    <?php if ($appusername === 'vijay.tapadia@gmail.com'): ?>
        <img src="https://efactor.tech/files/brand_logo/Dhrio_update.jpg"
             alt="DHRIO Logo"
             class="img-fluid welcome-logo">
    <?php endif; ?>
    
      <?php if ($appusername === 'manoj_saini@usha.com'): ?>
                    <img 
                        src="https://efactor.tech/files/brand_logo/usha.jpeg"
                        alt="Brand Logo"
                        style="max-width:200px; margin-top:20px;"
                    >
     <?php endif; ?>
     
     <?php if ($appusername === 'rajesham0512@gmail.com'): ?>
                    <img 
                        src="https://efactor.tech/files/brand_logo/AEI.jpg"
                        alt="Brand Logo"
                        style="max-width:200px; margin-top:20px;"
                    >
     <?php endif; ?>
</div>




        
    </div>

    
        <!-- Count Widgets -->
        <div class="row mb-4">
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



      <!-- Add Model Section -->
        <div class="row mb-4">
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
        </div>


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
                datasets: [
                    {
                        label: 'WiFi Modules',
                        data: <?php echo json_encode($wifi_counts); ?>,
                        borderColor: 'rgba(128, 0, 128, 0.8)',
                        fill: false,
                    },
                    {
                        label: 'Gateway Modules',
                        data: <?php echo json_encode($gateway_counts); ?>,
                        borderColor: 'rgba(54, 162, 235, 0.8)',
                        fill: false,
                    },
                    {
                        label: 'RF Modules',
                        data: <?php echo json_encode($rf_counts); ?>,
                        borderColor: 'rgba(255, 206, 86, 0.8)',
                        fill: false,
                    }
                ]
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

        // Doughnut Chart
        const doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
        const doughnutChart = new Chart(doughnutChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['WiFi', 'Gateway', 'RF'],
                datasets: [{
                    label: 'Modules',
                    data: [<?php echo htmlspecialchars($totaluserwifimodules); ?>,
                            <?php echo htmlspecialchars($totalusergatewaymodules); ?>,
                            <?php echo htmlspecialchars($totaluserrfmodules); ?>],
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
    </script>
</body>
</html>
