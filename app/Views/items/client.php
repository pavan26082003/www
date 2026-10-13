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








<?php
// Start the session
session_start();

// Set user ID in session
$user_id = $login_user->id;
$_SESSION['user_id'] = $user_id;

// Get the 'search' parameter from the URL
$searchQuery = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        #page-content {
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-x: auto; /* Allow horizontal scrolling */
        }

        h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Table Styling */
        #item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 600px; /* Minimum width for the table */
        }

        #item-table th {
            background-color: #34495e;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        #item-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #555;
        }

        #item-table tr:hover {
            background-color: #f1f3f5;
            transition: background-color 0.2s ease;
        }

        /* Button Styling */
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-default {
            background-color: #3498db;
            color: #fff;
            border: none;
        }

        .btn-default:hover {
            background-color: #2980b9;
        }

        /* Search Bar Styling */
        .dataTables_filter input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 250px;
            margin-bottom: 15px;
        }

        .dataTables_filter input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
    </style>
</head>
<body>
    <div id="page-content" class="page-wrapper clearfix">
        <div class="card">
            <h1>Products</h1>
            <table id="item-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            // Retrieve the search query from PHP
            var searchQuery = "<?php echo $searchQuery; ?>";

            // Initialize DataTable
            var table = $("#item-table").DataTable({
                "ajax": "<?php echo_uri('items/list_data'); ?>",
                "order": [[0, 'desc']],
                "columns": [
                    { title: "User", className: "text-right w100" },
                    { title: "Auto Test", className: "text-right w100" },
                    { title: "Mode", className: "text-right w100" },
                    { title: "Node ID" },
                    { title: "Brand", className: "text-right w100" },
                    { title: "Brand Logo", className: "text-right w100" },
                    { title: "Device Type", className: "text-right w100" },
                    { title: "Model Number", className: "text-right w100" },
                    { title: "Product Name", className: "text-right w100" },
                    { title: "Device", className: "text-right w100" },
                    { title: "User ID", className: "text-right w100" },
                    { title: "Phone", className: "text-right w100" },
                    { title: "Internet", className: "text-right w100" },
                    { title: "Power", className: "text-right w100" },
                    { title: "Latitude", className: "text-right w100" },
                    { title: "Longitude", className: "text-right w100" },
                    { title: "Signin Latitude", className: "text-right w100" },
                    { title: "Signin Longitude", className: "text-right w100" },
                    { title: "Node Latitude", className: "text-right w100" },
                    { title: "Node Longitude", className: "text-right w100" },
                    { title: "Product Info", className: "text-right w100" },
                    { title: "APIs Info", className: "text-right w100" },
                    { title: "Control", className: "text-right w100" }
                ],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                },
                "scrollX": true, // Enable horizontal scrolling
                "responsive": true // Make the table responsive
            });

            // Apply the search query
            if (searchQuery) {
                table.search(searchQuery).draw();
            }
        });
    </script>
</body>
</html>