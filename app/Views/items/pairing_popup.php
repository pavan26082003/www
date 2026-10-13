<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pairing Popup</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

    <div id="pairing-container">
        <!-- The data will be populated here dynamically -->
    </div>

    <script>
        // Function to fetch pairing data from the server
        function fetchPairingData() {
            $.ajax({
                url: '<?php echo site_url("items/pairing_config"); ?>',  // Ensure this is the correct URL
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("AJAX response: ", response); // Check the response in the console
                    if (response && response.length > 0) {
                        let pairingContent = '<ul>';
                        $.each(response, function(index, data) {
                            pairingContent += `<li>Model: ${data.modelSelect}, Page: ${data.pageSelect}, Status: ${data.statusSelect}</li>`;
                        });
                        pairingContent += '</ul>';
                        
                        // Update the pairing-container with the new data
                        $('#pairing-container').html(pairingContent);
                    } else {
                        $('#pairing-container').html('<p>No data found!</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error); // Log error to console
                }
            });
        }

        // Call the function on page load to fetch data initially
        $(document).ready(function() {
            fetchPairingData();  // Fetch data when page loads

            // Polling the server every 5 seconds (5000 milliseconds)
            setInterval(fetchPairingData, 5000); // Refresh data every 5 seconds
        });
    </script>

</body>
</html>

