<?php
 $modelNumber = $_GET['model_number'];
 $user_id = $_GET['user_id'];  
 $node_id = $_GET['node_id'];  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Request Example</title>
    <style>
        /* General Styles */
        body { 
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }

        .button-container {
            position: relative;
            text-align: center;
            margin-bottom: 20px;
        }

        #loadButton {
            padding: 15px 30px;
            background-color: #007BFF;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        #loadButton:hover {
            background-color: #0056b3;
        }

        #loadingMessage {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #output {
            text-align: center;
            margin-top: 20px;
            width: 90%;
            max-width: 600px;
        }

        h3 {
            font-size: 28px;
            font-weight: bold;
        }

        h4 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .test-passed {
            color: green;
        }

        .test-failed {
            color: red;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: left;
            display: inline-block;
            width: 100%;
        }

        li {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .failed-item {
            color: red;
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            #loadButton {
                font-size: 16px;
                padding: 12px 25px;
            }

            h3 {
                font-size: 24px;
            }

            h4 {
                font-size: 18px;
            }

            ul {
                width: 100%;
                padding: 10px;
            }

            .spinner {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            #loadButton {
                font-size: 14px;
                padding: 10px 20px;
            }

            h3 {
                font-size: 20px;
            }

            h4 {
                font-size: 16px;
            }

            ul {
                width: 100%;
                padding: 5px;
            }

            .spinner {
                width: 35px;
                height: 35px;
            }

            .test-passed, .test-failed {
                font-size: 18px;
            }

            li {
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <div class="button-container">
        <button id="loadButton">Start Auto Test</button>
    </div>

    <div class="loading" id="loadingMessage">
        <div class="spinner"></div>
        <p>Loading...</p>
    </div>

    <div id="output"></div>

    <script> 
       // const apiUrl = `http://localhost/factor_17-05/v6/api (29-oct).php?action=Auto_test&model_name=<!?php echo urlencode($modelNumber); ?>&user_id=<!?php echo urlencode($user_id); ?>&node_id=<!?php echo urlencode($node_id); ?->`; 
        const apiUrl = `https://efactor.tech/v8/api.php?action=Auto_test&model_name=<?php echo urlencode($modelNumber); ?>&user_id=<?php echo urlencode($user_id); ?>&node_id=<?php echo urlencode($node_id); ?>`;
        const loadButton = document.getElementById('loadButton');
        const loadingMessage = document.getElementById('loadingMessage');
        const outputDiv = document.getElementById('output');

        loadButton.addEventListener('click', async () => { 
            loadingMessage.style.display = 'block';
            loadButton.disabled = true;
            outputDiv.innerHTML = '';  

            try { 
                const response = await fetch(apiUrl); 
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                } 
                const contentType = response.headers.get("Content-Type");

                let data;
                const rawText = await response.text();  

                if (contentType && contentType.includes("application/json")) {
                    try { 
                        data = JSON.parse(rawText);
                    } catch (error) { 
                        data = rawText;
                    }
                } else { 
                    data = rawText;
                } 

                // Process and format the output
                outputDiv.innerHTML = processTestResults(data);
            } catch (error) { 
                outputDiv.textContent = `Error: ${error.message}`;
            } finally { 
                loadingMessage.style.display = 'none';
                loadButton.disabled = false;
            }
        });

        function processTestResults(data) {
            const results = Array.isArray(data) ? data : [data];
            const failedItems = results.filter(item => item.includes("Failed"));
            const allSuccess = failedItems.length === 0;
            const sucessItems = results.filter(item => item.includes("Success"));
            const allfailed = sucessItems.length === 0;

            let resultHTML = '';
            if (allSuccess) {
                if (allfailed) {
                    resultHTML += '<h3>No data found</h3>';
                } else{
                    resultHTML += '<h3 class="test-passed">Test Passed</h3>';
                }
            } else {
                resultHTML += '<h3 class="test-failed">Test Failed</h3>'; 
            }

            resultHTML += '<h4>Test Results:</h4>';
            resultHTML += '<ul>';
            results.forEach(result => {
                resultHTML += `<li>${result}</li>`;
            });
            resultHTML += '</ul>';

            return resultHTML;
        }
    </script>
</body>
</html>
