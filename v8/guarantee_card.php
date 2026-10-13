<html>
<head>
    <style> 
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #ffffff;  
        }  
        .guarantee-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        } 
        form {
            width: 100%;   
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
        } 
        label {
            font-size: 20px;
            color: #333;
            display: block;   
            margin-bottom: 10px;   
        } 
        .input-container {
            display: flex;
            align-items: center;
            margin-top: 20px;
            justify-content: flex-start;
            width: 100%;
        } 
        input[type="checkbox"] {
            width: 20px;   
            height: 20px;  
            margin-right: 10px;  
        } 
        #textbox {
            width: 100%;   
            max-width: 40%;   
            height: 40px;   
            padding: 12px;
            font-size: 18px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 10px;
        } 
        #confirmButton {
            margin : 12px; 
            background-color: darkorange;
            color: white;
            border: none;
            width: 12%;   
            height: 30px; 
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }  
        #confirmButton:disabled {
            background-color: lightgray;
            color: black;
            cursor: not-allowed;
        } 
        @media only screen and (max-width: 768px) {
            .guarantee-card {
                padding: 15px;  
            }

            form {
                padding: 15px;
            } 
            #textbox {
                width: 100%;
                font-size: 18px;
                height: 50px;
            } 
            #confirmButton {
                font-size: 18px;
                height: 50px;
            } 
            input[type="checkbox"] {
                width: 25px; 
                height: 25px;
            }

            label {
                font-size: 22px;
            }
        }

        @media only screen and (max-width: 480px) {
            body {
                margin: 10px;  
            } 
            label {
                font-size: 24px;
                margin-bottom: 15px;
            } 
            #textbox {
                font-size: 18px;
                height: 55px;
            } 
            #confirmButton {
                font-size: 20px;
                height: 55px;
            } 
            input[type="checkbox"] {
                width: 35px; 
                height: 35px;
            } 
            .input-container {
                margin-top: 25px;
            }
        }
    </style>
</head>
<body>

<div class="guarantee-card">
    <div id="gc-content"></div>
</div>

<form id="guaranteeForm" action="api (29-oct).php?action=guarantee_card_success" method="POST">
    <label>
        <input type="checkbox" id="checkbox" onclick="toggleButton()"> Agree to the Terms and Conditions
    </label>
    
    <div class="input-container">
        <input type="email" id="textbox" name="email" placeholder="Enter your email here" required />
        <button id="confirmButton" disabled type="submit">Confirm</button>
    </div>
</form>

<script>
    function toggleButton() {
        const checkbox = document.getElementById('checkbox');
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.disabled = !checkbox.checked;
    }
</script>

<script>
    fetch('http://localhost/factor_17-05/app/Controllers/Items.php?extract_GC2')   
        .then(response => response.json())
        .then(data => {
            if (data.html_content) { 
                document.getElementById("gc-content").innerHTML = data.html_content;
            } else { 
                console.error("Error fetching content:", data.error);
            }
        })
        .catch(error => console.error("Error fetching data:", error));
</script>

</body>
</html>