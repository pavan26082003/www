<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .container {
            margin-top: -20px;
            margin-left: 0;
            height: 100vh;
            width: 100%;
           background-color: #f0f1ff;
            
        }

      

        .profile {
            margin-top:20px;
            display: flex;
            width: 95%;
            height: 250px;
            border: 2px solid grey;
            background-color: #f0f5ff;
            margin-left: 25px;
            border-radius: 20px;
             
        }

        .profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-top: 25px;
            margin-left: 60px;
        }

         

        .username {
            margin-left: 115px;
            margin-top: 50px;
            font-size: 45px;
            color: grey;
        }

        .email {
            margin-top: 15px;
            margin-left: 120px;
            font-size: 22px;
            color: grey;
        }

        .phone {
            margin-top: 15px;
            margin-left: 120px;
            font-size: 20px;
            color: grey;
        }
        .display{
            margin-top:15%;
            width:90%;
            margin-left:-25%;
            
            position:absolute;
            
        }
       
       
    </style>
</head>

<body>
    <div class="container">
         
        <div class="profile">
            <div class="proupd">
                <div class="img">
                    <img src="http://localhost/factor_17-05/files/home/profile.png" id="profile-pic">
                    
                </div>
            </div>
            <div class="details">
                <div class="username" id="username-placeholder">
                Username 
            </div>
                <div class="details2">
                     <div class="email" id="email-placeholder">
                  Email 
                </div>
                      <div class="phone" id="phone-placeholder">
                      Phone 
                </div>
                </div>


<div class="content">
                <div style="overflow: auto">
                 
                <table id="item-table8" class="display" cellspacing="0" height="auto" width="100%" style="font-size:16px;border:2px solid grey;">            
                 
                </table>
            </div>
        </div>


            </div>
        </div>








        <script type="text/javascript">
$(document).ready(function () {
    
    var columnTitles = [
         
    ];

    var columns = [];
    $.each(columnTitles, function (index, title, value) {
        var newRow = $("<tr></tr>"); 
        newRow.append($("<th></th>").text("<?php echo '"+title+":' ?>")); 
        newRow.append($("<td></td>").text(value)); 
        columns.push(newRow); 
    });

    
    $.each(columns, function (index, row) {
        $('#item-table8').append(row); 
    });


    var table = {
source: '<?php echo_uri("items/user_data") ?>',
order: [[0, 'desc']],
columns: columns
};

// Assuming "#item-table6" is a container element where the data will be displayed vertically
var $container = $("#item-table8");

$.getJSON(table.source, function(data) {
console.log(data); // Log the data to see its structure
$.each(data, function(index, row) {
    // Iterate over each property in the row object
    $.each(row, function(key, value) {
        $container.append("<div><strong>Share User :</strong>"+ value[0] + "</div>");
        $container.append("<div><strong>Access Token :</strong>"+ value[1] + "</div>");
        $container.append("<div><strong>Vender Name :</strong>"+ value[2] + "</div>");
        $container.append("<div><strong>Vender ID :</strong>"+ value[3] + "</div>");
        $container.append("<div><strong>Mac :</strong>"+ value[4] + "</div>");
        $container.append("<div><strong>Request ID :</strong>"+ value[5] + "</div>");
        $container.append("<div><strong>IP Address :</strong>"+ value[6] + "</div>");
         
       


        $container.append("<div></div><br>");
    });
    // Add a line break after each row
    $container.append("<br>");
});
});







});

    
</script>
   

</body>

</html>
