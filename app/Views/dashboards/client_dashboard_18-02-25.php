<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<link rel="shortcut icon" href="https://efactor.tech/files/system/_file60ed85245779a-favicon.png">
    <style>
        /* Custom CSS for Widgets */
        .dashboard-widget {
            padding: 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .count {
            font-size: 2.5rem;
            font-weight: bold;
            margin-top: 10px;
        }

        .count-widget {
            padding: 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .count-widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .count-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .count-title {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .video-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .video-links a {
            display: block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .video-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        
        <!-- Widgets Section -->
        <div class="row mt-4">
            <?php if ($login_user->is_admin): ?>
                <!-- Admin Widgets -->
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                        <h3>Vendors</h3>
                        <div class="count"><?php echo $vendorCount; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
                        <h3>App Users</h3>
                        <div class="count"><?php echo $appusers; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #ff9a9e, #fad0c4);">
                        <h3>Online Products</h3>
                        <div class="count"><?php echo $onlineproducts; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #ff6f61, #ffcccb);">
                        <h3>Offline Products</h3>
                        <div class="count"><?php echo $offlineproducts; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #4b6cb7, #182848);">
                        <h3>Total Modules</h3>
                        <div class="count"><?php echo $totalmodules; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="dashboard-widget" style="background: linear-gradient(135deg, #ffafbd, #ffc3a0);">
                        <h3>WiFi Modules</h3>
                        <div class="count"><?php echo $wifimodules; ?></div>
                    </div>
                </div>
            <?php else: ?>
                <!-- User Widgets -->
                <div class="dashboard-header text-center my-4">
                    <h1>Welcome, <?php echo htmlspecialchars($appusername); ?></h1>
                </div>

                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <a href="items/index">
                            <div class="count-widget" style="background: linear-gradient(135deg, #00c6ff, #0072ff);">
                                <div class="count-number"><?php echo htmlspecialchars($totalusermodules); ?></div>
                                <div class="count-title">Total User Modules</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="items/index?search=Online">
                            <div class="count-widget" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                                <div class="count-number"><?php echo htmlspecialchars($totaluseronlinemodules); ?></div>
                                <div class="count-title">Online Modules</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="items/index?search=Offline">
                            <div class="count-widget" style="background: linear-gradient(135deg, #ff758c, #ff7eb3);">
                                <div class="count-number"><?php echo htmlspecialchars($totaluserofflinemodules); ?></div>
                                <div class="count-title">Offline Modules</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="items/index?search=RF">
                            <div class="count-widget" style="background: linear-gradient(135deg, #ff9a9e, #fad0c4);">
                                <div class="count-number"><?php echo htmlspecialchars($totaluserrfmodules); ?></div>
                                <div class="count-title">RF Modules</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="items/index?search=wifi">
                            <div class="count-widget" style="background: linear-gradient(135deg, #a18cd1, #fbc2eb);">
                                <div class="count-number"><?php echo htmlspecialchars($totaluserwifimodules); ?></div>
                                <div class="count-title">WIFI Modules</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="items/index?search=gateway">
                            <div class="count-widget" style="background: linear-gradient(135deg, #f6d365, #fda085);">
                                <div class="count-number"><?php echo htmlspecialchars($totalusergatewaymodules); ?></div>
                                <div class="count-title">GATEWAY Modules</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Add Model Section -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="wrapper count-title d-flex justify-content-between align-items-center p-3 shadow-sm" id="addmodel" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <div class="icon">
                                <i class="fas fa-plus text-white h4 mb-0"></i>
                            </div>
                            <div class="name text-center">
                                <strong class="text-uppercase d-block text-white">Add Model</strong>
                                <div class="download-link mt-2">
                                    <a href="<?php echo site_url('items/vendor'); ?>" class="btn btn-light btn-sm w-100">
                                        Add Model
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Charts Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Devices</h5>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Modules</h5>
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Online & Offline</h5>
                        <canvas id="doughnutChart2"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Section -->
        <div class="row mt-4 video-section">
            <div class="col-md-4">
                <div class="video-links">
                    <a href="#" onclick="showVideo('install')">APP INSTALL VIDEO</a>
                    <a href="#" onclick="showVideo('peer')">PEER TO PEER</a>
                    <a href="#" onclick="showVideo('ping')">PING</a>
                    <a href="#" onclick="showVideo('network')">ADDING DEVICE TO NETWORK</a>
                </div>
                <div class="mt-3">
                    <a href="https://efactor.tech/appstore/API_Specification.xlsx" download class="btn btn-success w-100">Download API Specification</a>
                </div>
            </div>
            <div class="col-md-8">
                <video id="videoPlayer" width="100%" controls>
                    <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Data
        const lineChartData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'RF Modules',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                },
                {
                    label: 'WiFi Modules',
                    data: [2, 3, 20, 5, 1, 4],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                },
                {
                    label: 'Gateway Modules',
                    data: [3, 10, 13, 15, 22, 30],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                }
            ]
        };

        const doughnutChartData = {
            labels: ['Wifi', 'Gateway', 'RF'],
            datasets: [{
                data: [<?php echo $wifimodules; ?>, <?php echo $totalusergatewaymodules; ?>, <?php echo $totaluserrfmodules; ?>],
                backgroundColor: ['rgba(128, 0, 128, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)']
            }]
        };

        const doughnutChartData2 = {
            labels: ['Offline', 'Online'],
            datasets: [{
                data: [<?php echo $offlineproducts; ?>, <?php echo $onlineproducts; ?>],
                backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(46, 184, 46, 0.6)']
            }]
        };

        // Initialize Charts
        const lineChart = new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: lineChartData,
        });

        const doughnutChart = new Chart(document.getElementById('doughnutChart'), {
            type: 'doughnut',
            data: doughnutChartData,
        });

        const doughnutChart2 = new Chart(document.getElementById('doughnutChart2'), {
            type: 'doughnut',
            data: doughnutChartData2,
        });

        // Video Player Functionality
        function showVideo(type) {
            const videoPlayer = document.getElementById('videoPlayer');
            switch (type) {
                case 'install':
                    videoPlayer.src = 'https://efactor.tech/appstore/app_install.mp4';
                    break;
                case 'peer':
                    videoPlayer.src = 'https://efactor.tech/appstore/peer-to-peer.mp4';
                    break;
                case 'ping':
                    videoPlayer.src = 'https://efactor.tech/appstore/ping.mp4';
                    break;
                case 'network':
                    videoPlayer.src = 'https://efactor.tech/appstore/addnet.mp4';
                    break;
            }
            videoPlayer.load();
            videoPlayer.play();
        }
    </script>
</body>
</html>