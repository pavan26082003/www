<!DOCTYPE html>
<html>
    <head>
    <title>Efactor</title>
    <style>
       .username{
        font-size: 1.3em;
        color: darkorange;
        background-color: #404040;
        width: 79.3%;
        height: 35px;
        padding-left: 10px;
       
       }
       .upperpart{
        display: flex;
        width: 80%;
        height: 90px;
        box-sizing: border-box;
        background-color: #e4d3b4;
        justify-content: space-evenly;
        padding-top: 7px;  
    }
    .module-datasheet{
        font: 1em;
        background: #eef0f3;
        text-align: center;
        display: block;
        width: 480px;
        height: 50px;
        padding-top: 20px;
        border-radius: 15px;
        line-height: 0; 
        order: 1; 
        color: black;
    }
    .api-sample{
        background-color: #eef0f3;
        font-size: 1.1em;
        text-align: center;
        row-gap: 10px;
        display: block;
        width: 480px;
        height: 50px;
        padding-top: 19px;
        border-radius: 15px;
        line-height: 0;  
        order: 2; 
        color: black;
    }
    .container{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-evenly;
       box-sizing: border-box;
       height: 450px;
       width: 80%;
      background-color: #e4d3b4;
       row-gap: 10px;
       padding-top: 10px;
       padding-left: 2px;
    }
    .total-modules{
        display: block;
        background-color:  #eef0f3;
        width: 300px;
        height: 200px;
        order: 1;
        border-radius: 15px;
        
        
    }
    .total-modules h2{
        width: 100%;
        height: 60px;
       margin: 0;
       line-height: 4;
        font-size: 1.3em;
        color: rgb(255, 255, 255);
        background-color : #404040;
     text-align: center;
     border-top-left-radius: 15px;
        border-top-right-radius: 15px;
     
     
    }
    .total-modules p{
       text-align: center;
       font-size: 3.2em;
       line-height: 0;
       color: green;
    
    }
    .total-RF{
        display: block;
        background-color: #eef0f3;
        width: 300px;
        height: 200px;
        order: 2;
        border-radius: 15px;
        
    }
    .total-RF h2{
        width: 100%;
        height: 60px;
       margin: 0;
       line-height: 4;
        font-size: 1.3em;
        color: white;
        background-color: #404040;;
     text-align: center;
     border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .total-RF p{
       justify-content: center;
       align-items: center;
       text-align: center;
       font-size: 3.2em;
       line-height: 0;
       color: green;
    }

     .wifi-modules{
        display: block;
        background-color: #eef0f3;
        width: 300px;
        height: 200px;
        order: 3;
        border-radius: 15px;
       
    }
    .wifi-modules h2{
        width: 100%;
        height: 60px;
       margin: 0;
       line-height: 4;
        font-size: 1.3em;
        color: white;
        background-color: #404040;;
        text-align: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .wifi-modules p{
       justify-content: center;
       align-items: center;
       text-align: center;
       font-size: 3.2em;
       line-height: 0;
       color: green;
    }
    .gateway-modules {
        display: block;
        background-color: #eef0f3;
        width: 300px;
        height: 200px;
        order: 4;
        border-radius: 15px;
    }
    .gateway-modules h2{
        width: 100%;
        height: 60px;
       margin: 0;
       line-height: 4;
        font-size: 1.3em;
        color: white;
        background-color: #404040;
        text-align: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .gateway-modules  p{
       justify-content: center;
       align-items: center;
       text-align: center;
       font-size: 3.2em;
       line-height: 0;
       color: green;
    }
    .online-modules{
        display: block;
        background-color: #eef0f3;
        width: 300px;
        height: 200px;
        order: 5;
        border-radius: 15px;  
        
    }
    .online-modules h2{
        width: 100%;
        height: 60px;
       margin: 0;
       line-height: 4;
        font-size: 1.4em;
        color: rgb(255, 255, 255);
        background-color: #404040;
        text-align: center;   
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        }
   
        .online-modules p{
       justify-content: center;
       align-items: center;
       text-align: center;
       font-size: 3.2em;
       line-height: 0;
       color: green;
    }
    .add{
       display: flex;
        flex-flow: row;
       flex-wrap: wrap;
       flex-direction: row;
       box-sizing: border-box;
       background-color: transparent;
       width: 300px;
       height: 200px;
       order: 6;
       align-items: center;
       justify-content: center;
       
    }
    .add-brand{
        box-sizing: border-box;
        font-size: 1.1em;
        color: white;
       background-color : #5a5959;
       width: 260px;
       height: 50px;
       border-radius: 10px;
       padding-left: 30px;
       text-align: center; 
    }
   /* .add-brand h3{
        width: 100%;
        font-size: 0.8em;
        color: white;
        background-color: transparent;
        text-align: center;
    }*/
    .add-product{
        box-sizing: border-box;
        font-size: 1.1em;
        color: white;
        background-color: #5a5959;
        text-align: center;
        padding-left: 30px;
        width: 260px;
       height: 50px;
       align-items: center;;
       border-radius: 10px;
    }
  /*  .add-product h3{
        width: 100%;
        font-size: 0.8em;
        color: white;
        background-color: transparent;
        text-align: center;
    }*/
    .add-model{
        box-sizing: border-box;
        font-size: 1.1em;
        color: white;
        background-color: #5a5959;
        text-align: center;
        width: 260px;
       height: 50px;
       align-items: center;
       padding-left: 30px;
       border-radius: 10px;
    }
    /* .add-model h3{
        width: 100%;
        font-size: 0.8em;
        color: white;
        background-color: transparent;
        text-align: center;
    } */
    .btn.btn-primary{
        
        width: 220px;
        height: 30px;
       
        background-color: rgb(38, 148, 38);
        font-size: 1.4em;
        color: white;
        border-radius: 15px;
        line-height: 2;
        
    }
    .vid{
        display: flex;
        width: 80%;
        height: 350px;
        padding-top: 30px;
        background-color: #e4d3b4;
        justify-content: space-evenly;
        
    }
    .vid01{
        padding-left: 20px;
        display: block;
        
        
        color: black;
        box-sizing: border-box;
        background-color: #eef0f3;
        border-radius: 10px;
        height: 320px;
        padding-top: 10px;

    }
    .vid01 video{
        padding-top: 15px;
        border-style: solid;
        border-color: #36454F;
    }
    .vid02{
        padding-left: 20px;
        display: block; 
        padding-top: 10px;
        color: black;
        box-sizing: border-box;
        background-color: #eef0f3;
        width: 200px;
        height: 320px;
        border-radius: 10px;

    }
    .vid02 video{
        border-style: solid;
        border-color: #36454F;
    }
    .vid03{
        padding-left: 20px;
        display: block;
        padding-top: 10px;
        color: black;
        box-sizing: border-box;
        background-color: #eef0f3;
        width: 200px;
        border-radius: 10px;
        height: 320px;

    }
    .vid03 video{
        padding-top: 15px;
        border-style: solid;
        border-color: #36454F;
    }
    .vid04{
        padding-left: 20px;
        display: block;
        padding-top: 10px;
        color: black;
        box-sizing: border-box;
        background-color: #eef0f3;
        width: 200px;
        border-radius: 10px;
        height: 320px;
      }
   .vid04 video{
    border-style: solid;
        border-color: #36454F;
   }
    

    

    </style>
    </head>

<body>
    <section class="sec01">
    
        <div class="username"> 
            <label class="username">User Name :  Efactor</label>
         </div>
    <div class="upperpart">
        <div class="api-sample">
        <label class="download-link">DOWNLOAD API SIMPLE</label>
        <P>File Name : API_Specification.xlsx</P>
        <a href="https://efactor.tech/appstore/API_Specification.xlsx" download="API_Specification_V1.0.xlsx">Download Excel</a>
    </div>
    <div class="module-datasheet">
        <label class="download-link">DOWNLOAD IOT MODULE DATA SHEET</label>
        <P>File Name : EFFWWAV1_MODULE_DATASHEET_V1.pdf</P>
        <a href="https://efactor.tech/appstore/EFWWAV1_Module_Datasheet_V1.pdf" download="EFWWAV1_Module_Datasheet_V1.pdf">Download pdf</a>
    </div>
</section>
<section class="sec02">
    <div class="container">
        <div class="total-modules">
            <h2>TOTAL USER MODULES </h2>
            <p>399</p>
        </div>
        <div class="total-RF">
            <h2>TOTAL RF MODULES</h2>
            <p>399</p>
        </div>
        <div class="wifi-modules">
            <h2>TOTAL WIFI MODULES</h2>
            <p>399</p>

        </div>
        <div class="gateway-modules">
            <h2>TOTAL GATEWAY MODULES</h2>
            <p>399</p>

        </div>
        <div class="online-modules">
            <h2>ONLINE MODULES</h2>
            <p>399</p>
        </div>
    
        <div class="add">
    <div class="add-brand">
        <a href="https://efactor.tech/index.php/items/brand" class="btn btn-primary">ADD BRAND</a>
    </div>
    <div class="add-product">
        <a href="https://efactor.tech/index.php/items/product" class="btn btn-primary">ADD PRODUCT</a>
    </div>
    <div class="add-model">
        <a href="https://efactor.tech/index.php/items/vendor" class="btn btn-primary">ADD MODEL</a>
    </div>
</div>
</div>
</div>
</section>
<section class="sec03">
    <div class="vid">
       <div class="vid01">
           <lable>APP INSTALL VIDEO</lable>
           <P>File Name: app_install.mp4</P>
           <video width="80%" height="65%" controls="">
               <source src="https://efactor.tech/appstore/app_install.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>
       </div>
       <div class="vid02">
           <lable>PEER TO PEER</lable>
           <P>File Name: peer-to-peer.mp4</P>
           <video width="81%" height="65%"  controls="">
               <source src="https://efactor.tech/appstore/peer-to-peer.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>
       </div>
       <div class="vid03">
           <lable>PING</lable>
           <P>File Name: ping.mp4</P>
           <video width="90%" height="65%" controls="">
               <source src="https://efactor.tech/appstore/ping.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>
       </div>
       <div class="vid04">
           <lable>ADDING DEVICE TO NETWORK</lable>
           <P>File Name: addnet.mp4</P>
           <video width="80%" height="65%"  controls="">
               <source src="https://efactor.tech/appstore/addnet.mp4" type="video/mp4">Your browser does not support the video tag.
           </video>
       </div>
    </div>
   </section>
   

</body>
</html>