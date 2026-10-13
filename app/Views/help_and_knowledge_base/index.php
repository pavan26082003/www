 
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





<html>
 <head>
 </head>
   
 <style>
  
 .qry1, .qry2, .qry3, .qry4, .qry5, .qry6 {
  height:auto;
  width:100%;
  padding:10px;
 font-size:25px;
 border:2px solid grey;
 color:grey;
 background-color:white;
 cursor: pointer;
 margin-top:10px;
 }
 .arrow{
 float:right;
 padding:5px;
 }
 
 .ans1, .ans2, .ans3, .ans4, .ans5, .ans6 {
  width: 100%;
  height: auto;
  font-size: 15px;
  display: none;
  border: 2px solid grey;
}

 
 
 </style>
 <body>
 

<div id="page-content" class="page-wrapper clearfix help-page-container <?php echo "main_" . $type ?>">
    <div id="search-box-wrapper">
      <div class="help-page view-container-large" style="min-height: 400px;">
 
        <?php
        $count = 0;

        foreach ($categories as $category) {
            if ($count % 3 === 0) {
                echo "<div class='row'>";
            }
            $count++;
            ?>
            <div class="col-md-4 col-sm-12">
                <a href="<?php echo get_uri($type . "/category/" . $category->id); ?>" style="width:300px;height:auto;margin:10px;">
                    <div class="card">
                        <!--div class="page-body p15 help-category-box" style="box-shadow:5px 5px 5px grey;background-color:rgb(12, 141, 12);"-->
                        <div class="page-body p15 help-category-box" style="box-shadow:5px 5px 5px grey;background-color:#2C3E50;">
                            <h4 style="color:white;"><?php echo $category->title; ?></h4>
                            <p style="color:white; class="text-off"><?php echo nl2br($category->description); ?></p>
                            <span style="color:white; class="anchor"><?php echo $category->total_articles . " " . app_lang("articles"); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
            if (($count % 3 === 0) || ($count === count($categories))) {
                echo "</div>";
            }
        }
        ?>
    </div>
        <div class="help-search-box-container">
            <h2><?php
                if ($type === "knowledge_base") {
                    echo app_lang("how_can_we_help");
                } else {
                    echo app_lang("help_page_title");
                }
                ?></h2>
            <?php echo view("help_and_knowledge_base/search_box", array("type" => $type)); ?>
        </div>
    </div>
<div class="help-page view-container-large" style="min-height: 400px;">
 
       
       <div class="qry1" onclick="q1()">
       How to connect a device to UART?
       <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
       <div class="ans1">
       <pre>
       To connect a device to UART, you'll typically need to identify the UART pins on both the device and the
        microcontroller or host system you're connecting it to, and then establish a physical and logical connection
         between them. Here's a step-by-step guide:
    1. Identify UART Pins:
        ◦ Determine the UART pins on your device. These are usually labeled as TX (transmit), RX (receive), and 
        sometimes ground (GND).
        ◦ Consult your device's datasheet, user manual, or schematic to locate these pins. They may be in the form of 
        solder pads, headers, or connectors.
    2. Determine Microcontroller UART Pins:
        ◦ Identify the UART pins on your microcontroller or host system. These will also include TX, RX, and GND pins.
        ◦ Refer to the datasheet or pinout diagram of your microcontroller to find the UART pins.
    3. Connect TX to RX and RX to TX:
        ◦ Connect the TX pin of the device to the RX pin of the microcontroller, and vice versa. This ensures that data
         transmitted by one device is received by the other, and vice versa.
        ◦ Connect the ground (GND) pins of both devices together to establish a common ground reference.
    4. Check Voltage Levels:
        ◦ Ensure that the voltage levels of the UART signals are compatible between the device and the microcontroller.Most 
        microcontrollers operate at 3.3V or 5V logic levels, so check if level shifting or voltage regulation is needed.
    5. Secure Connections:
        ◦ Use appropriate connectors, wires, or soldering techniques to establish secure and reliable connections
         between the devices.
        ◦ Avoid loose connections or short circuits that could cause damage to the devices or affect communication 
        reliability.
    6. Configure UART Settings:
        ◦ Configure the UART module of your microcontroller or host system with the appropriate settings such as baud
         rate, data bits, parity, and stop bits.
        ◦ Ensure that the UART settings match those expected by the device to enable successful communication.
    7. Test Communication:
        ◦ Write test code on the microcontroller to send data to the device via UART and receive responses.
        ◦ Verify that the device responds correctly to UART commands and that communication is reliable.
        ◦ Use debugging tools such as serial monitors or oscilloscopes to monitor UART traffic and 
        troubleshoot any issues.
    8. Integrate into Application:
        ◦ Integrate UART communication into your application code, allowing the microcontroller to interact with
         the device as required.
        ◦ Implement error handling and recovery mechanisms to ensure robust communication in real-world scenarios.
        </pre>
       </div>
       
       <div class="qry2" onclick="q2()">
       Compatibility and Device Integration?
       <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
        <div class="ans2">
       <pre>
    • Have you confirmed that your devices are compatible with the app?
    • Are you encountering any issues when trying to connect or integrate your IoT devices with the app?
    • Have you checked if your devices require any firmware updates for compatibility?
        </pre>
       </div>
       
        <div class="qry3" onclick="q3()">
       Account and Settings?
       <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
       <div class="ans3">
       <pre>
    • Are you logged into the app with the correct account?
    • Have you checked your app settings to ensure they are configured correctly?
    • Do you need assistance in managing your account or adjusting app settings?
        </pre>
       </div>
       
       <div class="qry4" onclick="q4()">
       Why is it essential to connect TX to RX and RX to TX?
       <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
       <div class="ans4">
       <pre>
       Connecting TX to RX and RX to TX enables bidirectional communication between devices. Data transmitted by one 
       device's TX pin is received by the other device's RX pin, allowing for two-way data exchange.
        </pre>
       </div>
       
        <div class="qry5" onclick="q5()">
       How to connecting devices over Wifi?
        <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
       <div class="ans5">
       <pre>
       Connect via Wifi module or chip on the microcontroller and configure Wifi settings for network access.
        </pre>
       </div>
       
       <div class="qry6" onclick="q6()">
       Technical Support?
       <img src="https://efactor.tech/files/home/Garrow.png" class="arrow" style="width:30px;height:40px;">
       </div>
       <div class="ans6">
       <pre>
       For more quries send a mail to "admin@efactor.io".
       Tell us littel bit about your problem we'll email you back
        </pre>
       </div>
    </div>
    
</div>
<script>
 var answer1 = document.querySelector('.ans1');
   var answer2 = document.querySelector('.ans2');
  var answer3 = document.querySelector('.ans3');
  var answer4 = document.querySelector('.ans4');
  var answer5 = document.querySelector('.ans5');
  var answer6 = document.querySelector('.ans6');

    function q1() {
           
        answer1.style.display = (answer1.style.display === 'none') ? 'block' : 'none';
       //  answer2.style.display = 'none';
         
    }
    function q2() {
           
        answer2.style.display = (answer2.style.display === 'none') ? 'block' : 'none';
        
    }
    function q3() {
           
        answer3.style.display = (answer3.style.display === 'none') ? 'block' : 'none';
        
    }
    function q4() {
           
        answer4.style.display = (answer4.style.display === 'none') ? 'block' : 'none';
        
    }
    function q5() {
           
        answer5.style.display = (answer5.style.display === 'none') ? 'block' : 'none';
        
    }
    function q6() {
           
        answer6.style.display = (answer6.style.display === 'none') ? 'block' : 'none';
        
    }

</script>
</body>
<html.
