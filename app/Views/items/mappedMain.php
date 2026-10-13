




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
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h1 style="color: #2c3e50; text-align: center; margin-bottom: 30px; font-family: 'Segoe UI', sans-serif;">Brand Products</h1>
    <table id="item-table" class="display" cellspacing="0" width="100%" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <thead>
            <tr style="background: linear-gradient(135deg, #3498db, #2c3e50); color: white; font-weight: 600;">
                <th style="padding: 12px 15px; text-align: left; border-radius: 4px 0 0 0;">Status</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 4px 0 0 0;">Device</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 4px 0 0 0;">Node ID</th>
                <th style="padding: 12px 15px; text-align: left;">Device Type</th>
                <th style="padding: 12px 15px; text-align: left;">Model Name</th>
                <th style="padding: 12px 15px; text-align: left;">internet</th>
                <th style="padding: 12px 15px; text-align: left;">Last seen</th>
                <th style="padding: 12px 15px; text-align: left;">lattitude</th>
                <th style="padding: 12px 15px; text-align: left;">longitude</th>
                <th style="padding: 12px 15px; text-align: left;">User Id</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 0 4px 0 0;">phone</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 0 4px 0 0;">Email</th>
                <th style="padding: 12px 15px; text-align: left; border-radius: 0 4px 0 0;">APIs Info</th>
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
                "ajax": "<?php echo site_url('items/list_data_brand'); ?>",
                "columns": [
                    { "data": 0 },
                    { "data": 1 },
                    { "data": 2 },
                    { "data": 3 },
                    { "data": 4 },
                    { "data": 5 },
                    { "data": 6 },
                    { "data": 7 },
                    { "data": 8 },
                    { "data": 9 },
                    { "data": 10 },
                    { "data": 11 },
                    { "data": 12 }
                ]
            });
        });

    </script>
</body>
</html>
