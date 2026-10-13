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





<?php
// Start the session
session_start();

// Set user ID in session
$user_id = $login_user->id;
$_SESSION['user_id'] = $user_id;

//echo $user_id;

// Get the 'search' parameter from the URL
$searchQuery = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>App Users</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h1 style="color: #2c3e50; text-align: center; margin-bottom: 30px; font-family: 'Segoe UI', sans-serif;">App USers</h1>
    <table id="item-table" class="display" cellspacing="0" width="100%" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <thead>
            <tr style="background: linear-gradient(135deg, #3498db, #2c3e50); color: white; font-weight: 600;">
                <th style="padding: 12px 15px; text-align: left; border-radius: 4px 0 0 0;">User ID</th>
                <th style="padding: 12px 15px; text-align: left;">Email</th>
                
                <th style="padding: 12px 15px; text-align: left;">Phone</th>
                <th style="padding: 12px 15px; text-align: left;">Longitude</th>
                <th style="padding: 12px 15px; text-align: left;">Latitude</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 0 4px 0 0;">Signup Time</th>
            </tr>
        </thead>
    </table>

    <style>
        #item-table {
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        
        #item-table th {
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        #item-table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        #item-table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
        }
    </style>

    <script>
       $(document).ready(function() {
            $('#item-table').DataTable({
                "ajax": "<?php echo site_url('User_details/list_data_user'); ?>",
                "columns": [
                    { "data": 0 },
                    { "data": 1 },
                    { "data": 2 },
                    { "data": 3 },
                    { "data": 4 },
                    { "data": 5 }
                ]
            });
        });

    </script>
</body>
</html>
