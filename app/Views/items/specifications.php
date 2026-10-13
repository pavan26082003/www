<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specifications</title>
    <style> 
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
 
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
        }
 
        .form-group {
            margin-bottom: 20px;
        }

        .form-check {
    font-size: 18px;
    display: flex;
    align-items: center; 
}

.form-check-input {
    margin-right: 10px; 
    width: 20px;
    height: 20px;
}

.form-check-label {
    font-size: 18px;
    margin: 0;
}

#auto_test_params_list .form-check-label {
    font-size: 14px;  
}

        label {
            font-size: 20px;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .form-control {
            font-size: 18px;
            padding: 10px;
            width: 100%;
            border: 2px solid #ccc;
            border-radius: 4px;
        }

        .form-control:focus {
            border-color: #4caf50;
            outline: none;
        }
 
        span {
            color: red;
            font-size: 16px;
        }
 
        input[type="submit"] {
            background-color: green;
            color: white;
            font-size: 18px;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 20%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
 
        input[type="submit"], .form-control {
            transition: all 0.3s ease;
        }
 
        @media (max-width: 600px) {
            .form-container {
                width: 100%;
                padding: 20px;
            }

            label, .form-check-label {
                font-size: 16px;
            }

            input[type="submit"] {
                font-size: 16px;
            }
        }
        .form-control {
        margin-top: 5px;
        margin-bottom: 10px;
        padding: 8px;
        width: 100%;
        max-width: 300px;
    }

    .form-check {
        margin-bottom: 15px;
    }

    .form-check-input {
        margin-right: 10px;
    }

    .form-check-label {
        font-weight: normal;
    }

    .form-check-container {
        margin-top: 10px;
    }

    input[type="checkbox"].form-check-input {
        margin-top: 6px;
    }

    input[type="text"].form-control {
        width: 100%;
    }

    .extra-inputs {
        margin-top: 15px;
    }
    </style>
</head>
<body>
    <div class="form-container">
    <?php echo form_open_multipart(get_uri("items/specifications_gc_api"), array("id" => "file-form1", "class" => "general-form", "role" => "form")); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
      
    <div class="form-group">
        <span id="guarantee_carderror"></span>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="guarantee_card" name="guarantee_card" value="enable">
            <label class="form-check-label" for="guarantee_card">Guarantee Card</label>
        </div>
        <div id="file_upload_container" style="display: none;">
            <label for="guarantee_card_file">Choose a text file:</label>
            <input type="file" class="form-control" id="guarantee_card_file" name="guarantee_card_file" accept=".txt">
            <span id="file_error1" class="error-message"></span><br>
        </div>
    </div>
    <div id="GC_upload_button_container" style="display: none;">
        <input type="submit" value="Upload" name="submit">
    </div> 

<?php echo form_close(); ?>
        
        <?php echo form_open_multipart(get_uri("items/specifications_vid_api"), array("id" => "file-form2", "class" => "general-form", "role" => "form")); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
        <div class="form-group">
        <span id="specific_viderror"></span>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="specific_vid" name="specific_vid" value="enable">
            <label class="form-check-label" for="specific_vid">Videos/Images</label>
        </div>
        <div id="vid_upload_container" style="display: none;">
            <label for="specific_vid_file">Upload Video/Images:</label> 
            <input type="file" class="form-control" id="specific_vid_file" name="specific_vid_file[]" accept="image/*, video/*" multiple>
        <span id="file_error" class="error-message"></span><br>
            <span id="file_error" class="error-message"></span><br>
        </div>
    </div>
    <div id="vid_upload_button_container" style="display: none;">
        <input type="submit" value="Upload" name="submit">
    </div>
    <?php echo form_close(); ?>

    

    </div>
    <script>
        document.getElementById('guarantee_card_file').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var errorMessage = document.getElementById('file_error1');
            var validFileType = '.txt'; 
            errorMessage.textContent = ''; 
            if (!file) {
                return;
            }  
            if (file.name.slice(-4).toLowerCase() !== validFileType) {
                errorMessage.textContent = 'Invalid file type. Please upload a .txt file.';
                event.target.value = '';  
            }
        });
    </script> 
    <script>
        document.getElementById('specific_vid_file').addEventListener('change', function(event) {
            var files = event.target.files;
            var errorMessage = document.getElementById('file_error');
            var maxSize = 12 * 1024 * 1024;  
            var validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            var validVideoTypes = ['video/mp4', 'video/webm', 'video/ogg']; 
            errorMessage.textContent = ''; 
            for (var i = 0; i < files.length; i++) {
                var file = files[i];  
                if (file.size > maxSize) {
                    errorMessage.textContent = 'One or more files exceed the 12MB limit. Please upload smaller files.';
                    event.target.value = ''; 
                    return;
                }  
                if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
                    errorMessage.textContent = 'One or more files are of invalid type. Please upload only images or videos.';
                    event.target.value = '';  
                    return;
                }
            }
        }); 
    </script>
    <script> 
        const guaranteeCardCheckbox = document.getElementById('guarantee_card');
        const fileUploadContainer = document.getElementById('file_upload_container');
        const uploadButtonContainer = document.getElementById('GC_upload_button_container');

        guaranteeCardCheckbox.addEventListener('change', function() {
            if (guaranteeCardCheckbox.checked) {
                fileUploadContainer.style.display = 'block';
                uploadButtonContainer.style.display = 'block';
            } else {
                fileUploadContainer.style.display = 'none';
                uploadButtonContainer.style.display = 'none';
            }
        });
 
        const specificVidCheckbox = document.getElementById('specific_vid');
        const vidUploadContainer = document.getElementById('vid_upload_container');
        const viduploadButtonContainer = document.getElementById('vid_upload_button_container');
        specificVidCheckbox.addEventListener('change', function() {
            if (specificVidCheckbox.checked) {
                vidUploadContainer.style.display = 'block';
                viduploadButtonContainer.style.display = 'block';
            } else {
                vidUploadContainer.style.display = 'none';
                viduploadButtonContainer.style.display = 'none';
            }
        }); 
</script>
</body>
</html>
