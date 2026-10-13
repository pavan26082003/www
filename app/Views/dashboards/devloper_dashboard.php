<html>
 
<!--div id="page-content" class="page-wrapper clearfix"-->

    <!--?php
    if (count($dashboards)) {
        echo view("dashboards/dashboard_header");
    }

    echo announcements_alert_widget();
    ?-->


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
    
    <!--link rel="shortcut icon" href="https://efactor.tech/files/system/_file60ed85245779a-favicon.png"-->
<style>
.container-fluid{
 
}
 

.download{
  width:350px;
  font-size:25px;
  height:100px;
  border:none;
  margin:15px;
  text-align:center;
  line-height:1;
  padding-top:8px;
  box-shadow:3px 3px 5px rgb(66, 66, 66);
  background-color: #fff;
}

.download a{
width:200px;
height:30px;
border:none;
background-color:green;
color:white;
 padding-top:3px;
 margin-top:2px;
 
}


 .vid01{
  text-decoration:none;
  width:350px;
  font-size:20px;
  height:50px;
  border:none;
  margin:15px;
  text-align:center;
   padding-top:10px;
  cursor:pointer;
  box-shadow:5px 5px 5px rgb(60, 60, 60);
  background-color:rgb(250, 88, 29);
  color:white;
 }
 .vid01:hover{
  background-color:white;
  color:rgb(250, 88, 29);
text-decoration:none;
border:2px solid grey;
 }

.vid02{
  text-decoration:none;
 width:350px;
 font-size:20px;
 height:50px;
 border:none;
 margin:15px;
 text-align:center;
  padding-top:10px;
  box-shadow:5px 5px 5px rgb(60, 60, 60);
  background-color:rgb(250, 88, 29);
  color:white;
 cursor:pointer;
  

}
.vid02:hover{
  background-color:white;
  color:rgb(250, 88, 29);
text-decoration:none;
border:2px solid grey;
 }
.vid03{
  text-decoration:none;
 width:350px;
 font-size:20px;
 height:50px;
 border:none;
 margin:15px;
 text-align:center;
  padding-top:10px;
 cursor:pointer;
 box-shadow:5px 5px 5px rgb(60, 60, 60);
 background-color:rgb(250, 88, 29);
  color:white;
}
.vid03:hover{
  background-color:white;
  color:rgb(250, 88, 29);
text-decoration:none;
border:2px solid grey;
 }
.vid04{
  text-decoration:none;
 width:350px;
 font-size:20px;
 height:50px;
 border:none;
 margin:15px;
 text-align:center;
  padding-top:10px;
 cursor:pointer;
 box-shadow:5px 5px 5px rgb(60, 60, 60);
 background-color:rgb(250, 88, 29);
  color:white;
}
.vid04:hover{
  background-color:white;
  color:rgb(250, 88, 29);
text-decoration:none;
border:2px solid grey;
 }
.video{
  display: flex;
  flex-direction: column;
}
 .vid{
  background-color: rgb(235, 240, 240);;
  width:100%;
  height:460px;
   margin-top:-4.5%;
   margin-left:-4%;
  display:flex;
    
 }
   
 .installVideo{
  position:absolute;
    width:260px;
  height:380px;
  border:none;
  display: ;
   padding:15px;
  background-color:rgb(252, 104, 50);
   margin-left:20%;
   margin-top:3%;
   
 }

 .peerVideo{
  position:absolute;
    width:260px;
  height:380px;
  border:none;
  display: none;
   padding:15px;
  background-color:rgb(252, 104, 50);
   margin-left:20%;
   margin-top :3%;
 }

 .pingVideo{
  position:absolute;
    width:260px;
  height:380px;
  border:none;
  display: none;
   padding:15px;
  background-color:rgb(252, 104, 50);
   margin-left:20%;
   margin-top:3%;
 }

 .networkVideo{
  position:absolute;
    width:260px;
  height:380px;
  border:none;
  display:none ;
   padding:15px;
  background-color:rgb(252, 104, 50);
   margin-left:20%;
   margin-top:3%;
 }
 
    #chart-container {
        width: 100%;
        margin: 10px auto;
    }
    #chart {
        width: 200px;
        height: 300px;
    }
    
   #tum,#om,#of,#addmodel,
    .element{
    
    width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:6%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
 
    }
   
   #tum,#trm,#twm,#tgm,#om,#of,
    .element{
    
   background-color:rgb(1, 129, 1);
  }
  
 /*   @media (max-width: 1500px) {
   #tum,#om,#of,#addmodel,
    .element{
        
   width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:3%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}
*/
 @media (max-width: 1500px) {
    #tum,#om,#of,#addmodel,
    .element{
       width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:6%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}

   @media (max-width: 1250px) {
    #tum,#om,#of,#addmodel,
    .element{
        width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:4%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}
   @media (max-width: 1120px) {
    #tum,#om,#of,#addmodel,
    .element{
        width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:2%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}
 
@media (max-width: 1080px) {
    #tum,#om,#of,#addmodel,
    .element{
       width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:16%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}

@media (max-width: 768px) {
   #tum,#om,#of,#addmodel,
    .element{
        width:150px;
    height:100px;
    text-align:center;
    margin:5px;
    margin-left:14%;
     box-shadow:3px 3px 5px rgb(66, 66, 66);
 background-color:rgb(47, 155, 47);
    }
}

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <body>



<!-- Counts Section -->
      <section class="dashboard-counts section-padding">
        <div class="container-fluid">
        <div class="row">
            <!-- Count item widget-->
      
          
            <div class="container-fluid">
    <div class="row">
        <?php
        // Check if the logged-in user is an admin before rendering the Vendors section
        

//$username = 'Sudha';


echo '<div class="col-12" style="color: darkorange; font-weight: bold;">';
echo '<h2>Hi	, '. $appusername .' </h2>';
echo '</div>';


  
            echo '<!-- Count item widget-->';
            echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="tum">';
            echo '<div class="wrapper count-title d-flex">';
            echo '<div class="icon"><i class="icon-product"></i></div>';
            $linkURL = 'items/index';   
echo '<a href="' . $linkURL . '" style="text-decoration: none; color: inherit;">';  
            echo '<div class="name">';
            echo '<strong class="text-uppercase" style="color:white">Total Clients Modules</strong>';
            
            // Replace $totalmodules with the actual variable representing the count of total modules
            $textColorTotalModules = ($totalmodules > 0) ? 'white' : 'white';
            echo '<div class="count-number" style="color: ' . $textColorTotalModules . ';">' . $totalusermodules . '</div>';
            
            echo '</div>';
             echo '</a>'; 
            echo '</div>';
            echo '</div>';


           /*
            echo '<!-- Count item widget-->';
            echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="trm">';
            echo '<div class="wrapper count-title d-flex">';
            echo '<div class="icon"><i class="icon-product"></i></div>';
            echo '<div class="name">';
            echo '<strong class="text-uppercase" style="color:white">Total RF Modules</strong>';
            
            // Replace $totalmodules with the actual variable representing the count of total modules
            $textColorRFModules = ($totalmodules > 0) ? 'white' : 'white';
            echo '<div class="count-number" style="color: ' . $textColorRFModules . ';">' . $totaluserrfmodules . '</div>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';


            echo '<!-- Count item widget-->';
            echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="twm">';
            echo '<div class="wrapper count-title d-flex">';
            echo '<div class="icon"><i class="icon-product"></i></div>';
            echo '<div class="name">';
            echo '<strong class="text-uppercase" style="color:white">Total WIFI Modules</strong>';
            
            // Replace $totalmodules with the actual variable representing the count of total modules
            $textColorWIFIModules = ($totalmodules > 0) ? 'white' : 'white';
            echo '<div class="count-number" style="color: ' . $textColorWIFIModules . ';">' . $totaluserwifimodules . '</div>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';






           echo '<!-- Count item widget-->';
            echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="tgm">';
            echo '<div class="wrapper count-title d-flex">';
            echo '<div class="icon"><i class="icon-product"></i></div>';
            echo '<div class="name">';
            echo '<strong class="text-uppercase" style="color:white">Gateway Modules</strong>';
            
            // Replace $totalmodules with the actual variable representing the count of total modules
            $textColorGatewayModules = ($totalmodules > 0) ? 'white' : 'white';
            echo '<div class="count-number" style="color: ' . $textColorGatewayModules . ';">' . $totalusergatewaymodules . '</div>';
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
*/

           echo '<!-- Count item widget-->';
echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="om">';
echo '<div class="wrapper count-title d-flex">';
echo '<div class="icon"><i class="icon-product"></i></div>';
$linkURL = 'items/index?search=Online';   
echo '<a href="' . $linkURL . '" style="text-decoration: none; color: inherit;">';  
echo '<div class="name">';
echo '<strong class="text-uppercase" style="color:white">Active Modules</strong>';

// Replace $totaluseronlinemodules with the actual variable representing the count of online modules
$textColorOnlineModules = ($totaluseronlinemodules > 0) ? 'white' : 'white';

// Display the count as a clickable button with the specified color
echo '<div href="active_dev.php" class="count-number" style="color: ' . $textColorOnlineModules . ';">' . $totaluseronlinemodules . '</div>';

echo '</div>';
 echo '</a>'; 
echo '</div>';
echo '</div>';

echo '<!-- Count item widget-->';
echo '<div class="col-xl-2 col-md-4 col-6" style="cursor: pointer;" id="of">';
echo '<div class="wrapper count-title d-flex">';
echo '<div class="icon"><i class="icon-product"></i></div>';
$linkURL = 'items/index?search=Offline';   
echo '<a href="' . $linkURL . '" style="text-decoration: none; color: inherit;">';  
echo '<div class="name">';
echo '<strong class="text-uppercase" style="color:white">Finished Modules</strong>';

// Replace $totaluseronlinemodules with the actual variable representing the count of online modules
$textColorOfflineModules = ($totaluserofflinemodules > 0) ? 'white' : 'white';

// Display the count as a clickable button with the specified color
echo '<div href="active_dev.php" class="count-number" style="color: ' . $textColorOfflineModules . ';">' . $totaluserofflinemodules . '</div>';

echo '</div>';
 echo '</a>'; 
echo '</div>';
echo '</div>';

 
echo '<div class="col-xl-2 col-md-4 col-6" id="addmodel" style="cursor: pointer;">';
echo '<div class="wrapper count-title d-flex">';
echo '<div class="icon"><i class="icon-info"></i></div>';
echo '<div class="name">';
echo '<strong style="color:white" class="text-uppercase">ADD MODEL</strong>';

echo '<div class="download-link">';
echo '<a href="' . site_url('items/vendor') . '" class="btn btn-primary" style="width:100px;margin-top:15px;margin-right:10px">Click</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
 /*

echo '<div class="col-xl-2 col-md-4 col-6">';
echo '<div class="wrapper count-title d-flex"  id="addbrand" >';
echo '<div class="icon"><i class="icon-info"></i></div>';
echo '<div class="name">';
echo '<strong class="text-uppercase">ADD BRAND</strong>';
echo '<div class="download-link">';
echo '<a href="' . site_url('items/brand') . '" class="btn btn-primary" style="width:100px">ADD BRAND</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';



echo '<div class="col-xl-2 col-md-4 col-6">';
echo '<div class="wrapper count-title d-flex" id="addproduct" >';
echo '<div class="icon"><i class="icon-info"></i></div>';
echo '<div class="name">';
echo '<strong class="text-uppercase">ADD PRODUCT</strong>';
echo '<div class="download-link">';
echo '<a href="' . site_url('items/product') . '" class="btn btn-primary" style="width:140px">ADD PRODUCT</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


 
echo '<div class="col-xl-2 col-md-4 col-6">';
echo '<div class="wrapper count-title d-flex" id="addmodel" >';
echo '<div class="icon"><i class="icon-info"></i></div>';
echo '<div class="name">';
echo '<strong class="text-uppercase">ADD MODEL</strong>';
echo '<div class="download-link">';
echo '<a href="' . site_url('items/vendor') . '" class="btn btn-primary" style="width:100px">ADD MODEL</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


 
/*
echo '<div class="col-xl-2 col-md-4 col-6">';
echo '<div class="wrapper count-title d-flex" id="addui" style="margin-right:20px;margin-left:100px;margin-top:30px">';
echo '<div class="icon"><i class="icon-info"></i></div>';
echo '<div class="name">';
echo '<strong class="text-uppercase">ADD UI</strong>';
echo '<div class="download-link">';
echo '<a href="' . site_url('items/page') . '" class="btn btn-primary" style="width:100px">ADD UI</a>'; // Change the href attribute here
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

*/

       
        ?>
    </div>
</div>
       
      



</section>



 <!-- Header Section-->
      <section class="dashboard-header section-padding" style="background-color:#f1f9ff">
        <div class="container-fluid">
          <div class="row d-flex align-items-md-stretch">

   <!-- Line Chart -->
            <div class="col-lg-6 col-md-12 flex-lg-last flex-md-first align-self-baseline">
              <div class="card sales-report">
                <h2 class="display h4">Devices</h2>
                <p> </p>
               <div id="chart-container">
    <canvas id="chart"></canvas>
</div>
              </div>
            </div>

            <!-- Pie Chart-->
            <div class="col-lg-3 col-md-6">
              <div class="card project-progress">
                <h2 class="display h4"style="margin-left:100px">Modules</h2>
                <p> </p>
                <div class="pie-chart" style="width:200px;height:200px;margin-left:20px">
                  <canvas id="myDoughnutChart"></canvas>

                </div>
              </div>
            </div>
            
            
             <!-- Pie Chart-->
            <div class="col-lg-3 col-md-6">
              <div class="card project-progress">
                <h2 class="display h4"style="margin-left:100px">Online & Offline</h2>
                <p> </p>
                <div class="pie-chart2" style="width:200px;height:200px;margin-left:40px">
                  <canvas id="myDoughnutChart2"></canvas>

                </div>
              </div>
            </div>
                





      <!-- Header Section-->
      <section class="dashboard-header section-padding">
        <div class="container-fluid">
          <div class="row d-flex align-items-md-stretch">


     


<div class="container">
    <div class="row">
       
    <div class="vid">
      <div class="video"> 
       <a href="#" id="vid01" class="vid01"  onclick="install()">APP INSTALL VIDEO</a>                      
       <a  href="#" id="vid02" class="vid02"   onclick="peer()">PEER TO PEER</a>
       <a  href="#" id="vid03" class="vid03"   onclick="ping1()">PING</a>
       <a  href="#" id="vid04" class="vid04"   onclick="network()">ADDING DEVICE TO NETWORK</a>  
       <div class="download">
         DOWNLOAD API SIMPLE 
         <br>
        <small>File Name : API_Specification.xlsx</small>
        <a href="https://efactor.tech/appstore/API_Specification.xlsx" download="API_Specification_V1.0.xlsx">Download</a>
    </div>     
        </div>
       <div class="fixedvideos"> 
       <video width="80%" height="65%" controls="" class="installVideo">
               <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">Your browser does not support the video tag.
        </video>
           
           <video width="81%" height="65%"  controls="" class="peerVideo">
               <source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>  
           
           <video width="81%" height="65%"  controls="" class="pingVideo">
               <source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>  
           
           <video width="80%" height="65%"  controls="" class="networkVideo">
               <source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4">Your browser does not support the video tag.
        </video>  
        </div>      
        </div>
        
    </div>

    </div>
        </div>
 
      </section>

 
      <!-- Statistics Section-->
      <!--section class="statistics">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-lg-4">
              <!-- Income-->
              <!--div class="card income text-center">
                <div class="number">126,418</div><strong class="text-primary">All Income</strong>
                <p> </p>
              </div>
            </div>
            <div class="col-lg-4">
              <!-- Monthly Usage-->
              <!--div class="card data-usage">
                <h2 class="display h4">Monthly Usage</h2>
                <div class="row d-flex align-items-center">
                  <div class="col-sm-6">
                    <div id="progress-circle" class="d-flex align-items-center justify-content-center"></div>
                  </div>
                  <div class="col-sm-6"><strong class="text-primary">80.56 Gb</strong><small>Current Plan</small><span>100 Gb Monthly</span></div>
                </div>
                <p> </p>
              </div>
            </div>
            <div class="col-lg-4">
              <!-- User Actibity-->
              <!--div class="card user-activity">
                <h2 class="display h4">User Activity</h2>
                <div class="number">210</div>
                <h3 class="h4 display">Social Users</h3>
                <div class="progress">
                  <div role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar bg-primary"></div>
                </div>
                <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>Pages Visits</span><strong>230</strong></div>
                  <div class="page-statistics-right"><span>New Visits</span><strong>73.4%</strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Updates Section -->
      <!--section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4 col-md-12">
              <!-- Recent Updates Widget          -->
              <!--div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box">News Updates</a></h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-box" aria-expanded="true" aria-controls="updates-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-box" role="tabpanel" class="collapse show">
                  <ul class="news list-unstyled">
                     
              <!-- Recent Updates Widget End-->
            <!--/div>
            <div class="col-lg-4 col-md-6">
              <!-- Daily Feed Widget-->
              <!--div id="daily-feeds" class="card updates daily-feeds">
                <div id="feeds-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"><a data-toggle="collapse" data-parent="#daily-feeds" href="#feeds-box" aria-expanded="true" aria-controls="feeds-box">Your daily Feeds </a></h2>
                  <div class="right-column">
                    <div class="badge badge-primary">messages</div><a data-toggle="collapse" data-parent="#daily-feeds" href="#feeds-box" aria-expanded="true" aria-controls="feeds-box"><i class="fa fa-angle-down"></i></a>
                  </div>
                </div>
                <div id="feeds-box" role="tabpanel" class="collapse show">
                  <div class="feed-box">
                    <ul class="feed-elements list-unstyled">
                      <!-- List-->
                      <!--li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                          <div class="feed-body d-flex justify-content-between"><a href="#" class="feed-profile"><img src="https://efactor.tech/factor/img/sudha.png" alt="person" class="img-fluid rounded-circle"></a>
                            <div class="content"><strong>Sudha</strong><small>Posted a new blog </small>
                              <div class="full-date"><small>Today 5:60 pm - 1.04.2023</small></div>
                            </div>
                          </div>
                          <div class="date"><small>5min ago</small></div>
                        </div>
                      </li>
                      <!-- List-->
                      <!--li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                          <div class="feed-body d-flex justify-content-between"><a href="#" class="feed-profile"><img src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/avatar-2.jpg" alt="person" class="img-fluid rounded-circle"></a>
                            <div class="content"><strong>Frank Williams</strong><small>Posted a new blog </small>
                              <div class="full-date"><small>Today 5:60 pm - 12.06.2014</small></div>
                              <div class="CTAs"><a href="#" class="btn btn-xs btn-dark"><i class="fa fa-thumbs-up"> </i>Like</a><a href="#" class="btn btn-xs btn-dark"><i class="fa fa-heart"> </i>Love</a></div>
                            </div>
                          </div>
                          <div class="date"><small>5min ago</small></div>
                        </div>
                      </li-->
                      <!-- List-->
                      <!--li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                          <div class="feed-body d-flex justify-content-between"><a href="#" class="feed-profile"><img src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/avatar-3.jpg" alt="person" class="img-fluid rounded-circle"></a>
                            <div class="content"><strong>Ashley Wood</strong><small>Posted a new blog </small>
                              <div class="full-date"><small>Today 5:60 pm - 12.06.2014</small></div>
                            </div>
                          </div>
                          <div class="date"><small>5min ago</small></div>
                        </div>
                      </li-->
                      <!-- List-->
                      <!--li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                          <div class="feed-body d-flex justify-content-between"><a href="#" class="feed-profile"><img src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/avatar-1.jpg" alt="person" class="img-fluid rounded-circle"></a>
                            <div class="content"><strong>Jason Doe</strong><small>Posted a new blog </small>
                              <div class="full-date"><small>Today 5:60 pm - 12.06.2014</small></div>
                            </div>
                          </div>
                          <div class="date"><small>5min ago</small></div>
                        </div>
                        <!--div class="message-card"> <small>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s. Over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</small></div-->
                        <!--div class="CTAs pull-right"><a href="#" class="btn btn-xs btn-dark"><i class="fa fa-thumbs-up"> </i>Like</a></div>
                      </li-->
                      <!-- List-->
                      <!--li class="clearfix">
                        <div class="feed d-flex justify-content-between">
                          <div class="feed-body d-flex justify-content-between"><a href="#" class="feed-profile"><img src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/avatar-6.jpg" alt="person" class="img-fluid rounded-circle"></a>
                            <div class="content"><strong>Sam Martinez</strong><small>Posted a new blog </small>
                              <div class="full-date"><small>Today 5:60 pm - 12.06.2014</small></div>
                            </div>
                          </div>
                          <div class="date"><small>5min ago</small></div>
                        </div>
                      </li-->
                    </ul>
                  </div>
                </div>
              </div>
              <!-- Daily Feed Widget End-->
            <!--/div>
            <div class="col-lg-4 col-md-6">
              <!-- Recent Activities Widget      -->
              <!--div id="recent-activities-wrapper" class="card updates activities">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"><a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box">Recent Activities</a></h2><a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="activities-box" role="tabpanel" class="collapse show">
                  <ul class="activities list-unstyled">
                    <!-- Item-->
                    <!--li>
                      <div class="row">
                        <div class="col-4 date-holder text-right">
                          <div class="icon"><i class="icon-clock"></i></div>
                          <div class="date"> <span>6:00 am</span><span class="text-info">6 hours ago</span></div>
                        </div>
                        <div class="col-8 content"><strong>Meeting</strong>
                          <p>  </p>
                        </div>
                      </div>
                    </li>
                    <!-- Item-->
                    <!--li>
                      <div class="row">
                        <div class="col-4 date-holder text-right">
                          <div class="icon"><i class="icon-clock"></i></div>
                          <div class="date"> <span>6:00 am</span><span class="text-info">6 hours ago</span></div>
                        </div>
                        <div class="col-8 content"><strong>Meeting</strong>
                          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.                </p>
                        </div>
                      </div>
                    </li-->
                    <!-- Item-->
                    <!--li>
                      <div class="row">
                        <div class="col-4 date-holder text-right">
                          <div class="icon"><i class="icon-clock"></i></div>
                          <div class="date"> <span>6:00 am</span><span class="text-info">6 hours ago</span></div>
                        </div>
                        <div class="col-8 content"><strong>Meeting</strong>
                          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.                </p>
                        </div>
                      </div>
                    </li-->
                    <!-- Item-->
                    <!--li>
                      <div class="row">
                        <div class="col-4 date-holder text-right">
                          <div class="icon"><i class="icon-clock"></i></div>
                          <div class="date"> <span>6:00 am</span><span class="text-info">6 hours ago</span></div>
                        </div>
                        <div class="col-8 content"><strong>Meeting</strong>
                          <p> </p>
                        </div>
                      </div>
                    </li-->
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>



<script>
    $(document).ready(function () {
        // Attach click event handler to the button
        $("#openCommandModal").click(function () {
            // Load modal content from modal_vendorproduct.php
            $("#modalContainer").load("modal_vendorproduct.php", function () {
                // Show the loaded modal content
                $("#addCommandModal").modal("show");
            });
        });
    });
</script>




<!--button type="button" data-toggle="collapse" data-target="#style-switch" id="style-switch-button" class="btn btn-primary btn-sm d-none d-md-inline-block"><i class="fa fa-cog fa-2x"></i></button-->
    <div id="style-switch" class="collapse">
      <h5 class="mb-3">Select theme colour</h5>
      <form class="mb-3">
        <select name="colour" id="colour" class="form-control">
          <option value="">select colour variant</option>
          <option value="default">green</option>
          <option value="red">red</option>
          <option value="violet">violet</option>
          <option value="pink">pink</option>
          <option value="sea">sea</option>
          <option value="blue">blue</option>
        </select>
      </form>
      <p><img src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/template-mac.png" alt="" class="img-fluid"></p>
      <p class="text-muted text-small">Stylesheet switching is done via JavaScript and can cause a blink while page loads. This will not happen in your production code.</p>
      <p class="text-muted text-small">Do you want more components, SCSS sources and attribution-free version? </p>
      <p class="text-center"> <a href="https://bootstrapious.com/p/bootstrap-4-dashboard-premium" class="btn btn-primary">Get Premium version</a></p>
    </div>
    <!-- JavaScript files-->
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/jquery/jquery.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/popper.js/umd/popper.min.js"> </script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/js/grasp_mobile_progress_circle-1.0.0.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/chart.js/Chart.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/js/charts-home.js"></script>
    <!-- Main File-->
    <script src="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/js/front.js"></script>


    <script>
 var install1 = document.querySelector('.installVideo');
 var peer1 = document.querySelector('.peerVideo');
 var ping = document.querySelector('.pingVideo');
 var network1 = document.querySelector('.networkVideo');
 


  function install() {
    
    install1.style.display = (install1.style.display === 'none') ? 'block' : 'none';
    peer1.style.display = 'none';
    ping.style.display = 'none';
    network1.style.display = 'none';
   
}
    function peer() {
        
        peer1.style.display = (peer1.style.display === 'none') ? 'block' : 'none';
        install1.style.display = 'none';
    ping.style.display = 'none';
    network1.style.display = 'none';

    }

    function ping1() {
        
      ping.style.display = (ping.style.display === 'none') ? 'block' : 'none';
      install1.style.display = 'none';
    peer1.style.display = 'none';
    network1.style.display = 'none';

    }


    function network() {
        
        network1.style.display = (network1.style.display === 'none') ? 'block' : 'none';
        peer1.style.display = 'none';
    ping.style.display = 'none';
    install1.style.display = 'none';
    }


</script>



  <script type="text/javascript">
    var ctx = document.getElementById('myDoughnutChart').getContext('2d');
    var myDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Wifi", "Gateway", "RF"],
            datasets: [{
                data: [
                    <?php
                    // Check if the user is admin and output the appropriate values
                    if ($login_user->is_admin) {
                        echo "$wifimodules, $gatewaymodules, $rfmodules";
                    } else {
                        echo "$totaluserwifimodules, $totalusergatewaymodules, $totaluserrfmodules";
                    }
                    ?>
                ],  
                backgroundColor: [
                    'rgba(128, 0, 128, 0.4)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(128, 0, 128, 0.6)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 70  
        }
    });
</script>
    
   <script type="text/javascript">
    var ctx = document.getElementById('myDoughnutChart2').getContext('2d');
    var myDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Offline", "Online"],
            datasets: [{
                data: [
                    <?php
                    // Check if the user is admin and output the appropriate values
                    if ($login_user->is_admin) {
                        echo "$offlineproducts, $onlineproducts";
                    } else {
                        echo "$totaluserofflinemodules, $totaluseronlinemodules";
                    }
                    ?>
                ],  
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(46, 184, 46, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(46, 184, 46, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 70  
        }
    });
</script>
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var ctxLine = document.getElementById('chart').getContext('2d');

    // Function to create a gradient color for filling
    function createGradient(ctx, color1, color2) {
        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    var chartLine = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php  echo $month_labels   ?>,
            datasets: [
                {
                    label: 'WiFi Modules',
                    data: <?php   if($login_user->is_admin){ 
                    echo $wifi_counts; 
                    }else{
                    echo $userwifi_counts; 
                    } ?>,
                    backgroundColor: createGradient(ctxLine, 'rgba(128, 0, 128, 0.2)', 'rgba(128, 0, 128, 0)'),
                    borderColor: 'rgba(128, 0, 128, 0.8)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(128, 0, 128, 0.8)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                    lineJoin: 'round'
                },
                {
                    label: 'Gateway Modules',
                    data: <?php   if($login_user->is_admin) { echo $gateway_counts;
                    }else{
                    echo $usergateway_counts; 
                    } ?>,
                    backgroundColor: createGradient(ctxLine, 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 0)'),
                    borderColor: 'rgba(54, 162, 235, 0.8)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 0.8)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                    lineJoin: 'round'
                },
                {
                    label: 'RF Modules',
                    data: <?php if( $login_user->is_admin) { echo$rf_counts; 
                    }else{
                    echo $userrf_counts; 
                    } ?>,
                    backgroundColor: createGradient(ctxLine, 'rgba(255, 206, 86, 0.4)', 'rgba(255, 206, 86, 0)'),
                    borderColor: 'rgba(255, 206, 86, 0.8)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(255, 206, 86, 0.8)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                    lineJoin: 'round'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            family: 'Arial',
                            color: '#333'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            color: '#666'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            color: '#666'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
</script>

<body>
<html>








  

