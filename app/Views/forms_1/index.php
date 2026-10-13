

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







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Frame Information</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>


<body>


<h2>Product Information</h2>




<form id="productForm" action="<?php echo site_url('forms/saveProductInfo'); ?>" method="post" enctype="multipart/form-data">

<table>
    <tr>
        
        <th>Item</th>
        <th>Details</th>
        <th>Developer Notes</th>
    </tr>
    <tr>
        
        <td>Date</td>     
         <td><input type="text" value="1-Dec-23"></td>
        <td><input type="text" value=""></td>
    </tr>
    <tr>
        
        <td>Brand Name</td>
        <td><input type="text" value=""></td>
        <td><input type="text" value=""></td>
    </tr>
    <tr>
        
        <td>Brand Logo</td>
       <td>
            <input type="file" accept="image/*" id="brandLogo" name="brandLogo">
        </td>
        <td><input type="text" value=""></td>
    </tr>
    <tr>
        
        <td>Product Name</td>
        <td><input type="text" value="TV"></td>
        <td><input type="text" value=""></td>
    </tr>
    <tr>
        
        <td>Product Description</td>
        <td><input type="text" value=""></td>
        <td><input type="text" value=""></td>
    </tr>
   
 <tr>
        
        <td>Model Name</td>
        <td><input type="text" value=""></td>
        <td><input type="text" value=""></td>
    </tr>
   
 <tr>
        
        <td>Number of pages</td>
       <td><input type="text" value=""></td>
        <td><input type="text" value=""></td>
    </tr>
   
 <tr>
        
        <td>Page names</td>
        <td><input type="text" value=""></td>
        <td><input type="text" value=""></td>
    </tr>
   
     
   
</table>




<button type="button" onclick="nextPage()">Next</button>



</form>








<script>



function nextPage() {
        // Add logic to navigate to the next page
       // window.location.href = 'http://192.168.2.115/command_para.html';

     window.location.href = '<?php echo site_url("forms/saveProductInfo"); ?>';
    
    }

 
</script>












</body>
</html>


    
