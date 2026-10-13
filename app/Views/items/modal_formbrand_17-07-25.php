<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .modal-body {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 6px;
        }
        .card.card-body {
            background: #eef1f7;
            border: 1px solid #ced4da;
            border-radius: 8px;
        }
        .btn-link {
            font-weight: bold;
            font-size: 1.05rem;
            text-decoration: none;
            padding: 0.5rem 0;
            display: inline-block;
        }
        .modal-footer {
            background: #f1f3f5;
            padding: 15px 30px;
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .btn-primary {
            border-radius: 6px;
            padding: 8px 20px;
        }
        #brandlogo-preview {
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php echo form_open_multipart(get_uri("items/savebrand"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone container mt-4">
    <div class="modal-body clearfix">
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo isset($model_info->id) ? $model_info->id : ''; ?>" />

            <div class="form-group">
                <label for="name">Brand Name</label>
                <?php
                echo form_input(array(
                    "id" => "name",
                    "name" => "name",
                    "value" => isset($model_info->name) ? $model_info->name : "",
                    "class" => "form-control validate-hidden",
                    "placeholder" => "Category Name",
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>

            <div class="form-group">
                <label for="brandlogo">Brand Logo</label>
                <input type="file" class="form-control" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
                <img id="brandlogo-preview" src="#" alt="Brand Logo Preview" style="display: none; max-width: 100px; max-height: 100px;">
            </div>

            <div class="form-group">
                <label for="brand_category">Brand Category/Industry</label>
                <input type="text" id="brand_category" name="brand_category" class="form-control" placeholder="e.g., fashion, tech, food">
            </div>
            <div class="form-group">
                <label for="year_established">Year Established</label>
                <input type="number" id="year_established" name="year_established" class="form-control" placeholder="Enter Year Established">
            </div>

            <!-- DIGITAL PRESENCE -->
            <div class="form-group">
                <a class="btn btn-link text-primary" data-toggle="collapse" href="#digitalPresence" role="button" aria-expanded="false" aria-controls="digitalPresence">
                    1) Digital Presence
                </a>
                <div class="collapse" id="digitalPresence">
                    <div class="card card-body">
                        <div class="form-group"><label for="website_url">Official Website URL</label><input type="url" name="website_url" id="website_url" class="form-control" placeholder="Enter Official Website URL"></div>
                        <div class="form-group"><label for="social_media">Social Media Profiles</label><input type="text" name="social_media" id="social_media" class="form-control" placeholder="Instagram, Twitter/X, Facebook, etc."></div>
                        <div class="form-group"><label for="server_url">Server URL (if applicable)</label><input type="url" name="server_url" id="server_url" class="form-control" placeholder="Enter Server URL"></div>
                        <div class="form-group"><label for="app_download_links">App Download Links (if applicable)</label><input type="url" name="app_download_links" id="app_download_links" class="form-control" placeholder="Enter App Download Links"></div>
                    </div>
                </div>
            </div>

            <!-- SERVER INFORMATION -->
            <div class="form-group">
                <a class="btn btn-link text-primary" data-toggle="collapse" href="#serverInfo" role="button" aria-expanded="false" aria-controls="serverInfo">
                    2) Server-Specific Information (if brand has a server)
                </a>
                <div class="collapse" id="serverInfo">
                    <div class="card card-body">
                        <div class="form-group"><label for="server_name">Server Name</label><input type="text" name="server_name" id="server_name" class="form-control" placeholder="Enter Server Name"></div>
                        <div class="form-group"><label for="server_type">Server Type</label><input type="text" name="server_type" id="server_type" class="form-control" placeholder="e.g., community, support, official, fan-made"></div>
                        <div class="form-group"><label for="server_creation_date">Server Creation Date</label><input type="date" name="server_creation_date" id="server_creation_date" class="form-control"></div>
                        <div class="form-group"><label for="server_member_count">Server Member Count (current)</label><input type="number" name="server_member_count" id="server_member_count" class="form-control" placeholder="Enter Current Member Count"></div>
                        <div class="form-group"><label for="server_verification_status">Server Verification Status</label><input type="text" name="server_verification_status" id="server_verification_status" class="form-control" placeholder="Enter Verification Status"></div>
                        <div class="form-group"><label for="server_invite_link">Server Invite Link (if public)</label><input type="url" name="server_invite_link" id="server_invite_link" class="form-control" placeholder="Enter Invite Link"></div>
                    </div>
                </div>
            </div>

            <!-- ADDITIONAL INFORMATION -->
            <div class="form-group">
                <a class="btn btn-link text-primary" data-toggle="collapse" href="#additionalInfo" role="button" aria-expanded="false" aria-controls="additionalInfo">
                    3) Additional Useful Information
                </a>
                <div class="collapse" id="additionalInfo">
                    <div class="card card-body">
                        <div class="form-group"><label for="brand_description">Brand Description</label><textarea name="brand_description" id="brand_description" rows="3" class="form-control" placeholder="Enter Brand Description"></textarea></div>
                        <div class="form-group"><label for="key_products">Key Products/Services</label><input type="text" name="key_products" id="key_products" class="form-control" placeholder="Enter Key Products/Services"></div>
                        <div class="form-group"><label for="headquarters_location">Headquarters Location</label><input type="text" name="headquarters_location" id="headquarters_location" class="form-control" placeholder="Enter Headquarters Location"></div>
                        <div class="form-group"><label for="parent_company">Parent Company</label><input type="text" name="parent_company" id="parent_company" class="form-control" placeholder="Enter Parent Company"></div>
                        <div class="form-group"><label for="contact_email">Contact Email</label><input type="email" name="contact_email" id="contact_email" class="form-control" placeholder="Enter Contact Email"></div>
                        <div class="form-group"><label for="partnerships">Partnerships/Collaborations</label><input type="text" name="partnerships" id="partnerships" class="form-control" placeholder="Enter Partnerships/Collaborations"></div>
                    </div>
                </div>
            </div>

            <?php echo view("includes/dropzone_preview"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showImagePreview(input) {
    var preview = document.getElementById('brandlogo-preview');
    var file = input.files[0];
    var reader = new FileReader();
    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block';
    }
    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>
