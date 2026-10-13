<!DOCTYPE html>
<html>
<head>
    <title>Select Brand</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f4f4;
        }

        label {
            font-size: 18px;
            font-weight: 500;
            display: block;
            margin-bottom: 10px;
        }

        select {
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 250px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        select:disabled {
            background-color: gray;
        }
    </style>
</head>
<body>

    <label for="brandSelect">Enable Brand</label>
    <select id="brandSelect">
        <option value="">Loading brands...</option>
    </select>

    <script>
        // Load brands using fetch API from your efactor API
        fetch('https://efactor.tech/v8/api.php?action=brand_api')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('brandSelect');
        select.innerHTML = '<option value="">Select a brand</option>';

        if (data && Array.isArray(data)) {
            data.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand.id; // use actual ID
                option.text = brand.brand_name; // correct property
                select.appendChild(option);
            });
        } else {
            select.innerHTML = '<option value="">No brands found</option>';
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        document.getElementById('brandSelect').innerHTML = '<option value="">Error loading brands</option>';
    });
    </script>

</body>
</html>
