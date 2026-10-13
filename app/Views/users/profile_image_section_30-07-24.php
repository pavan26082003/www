<div class="box" id="profile-image-section">
   <div class="box-content w200 text-center profile-image">
    <?php
    $url = "team_members";

    if ($user_info->user_type === "client") {
        $url = "clients";
    } else if ($user_info->user_type === "lead") {
        $url = "leads";
    }
    echo form_open(get_uri($url . "/save_profile_image/" . $user_info->id), array(  "id" => "profile-image-form",
        "class" => "general-form", "role" => "form",  "enctype" => "multipart/form-data"  )); ?>
    <span class="avatar avatar-lg">
        <?php   $base_url = 'http://localhost/factor_17-05/files/brandLogo/';  $image_url = $base_url . $user_info->image; ?>
        <img   id="profile-image-preview"    src="<?php echo $image_url; ?>"   alt="Profile Image" 
            style="width: 150px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid white;">
    </span>
    <h4><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h4>

    <?php if ($login_user->is_admin || $user_info->id === $login_user->id) { ?>
    <div class="file-upload btn mt0 p0 profile-image-upload" data-bs-toggle="tooltip" title="<?php echo app_lang("upload_and_crop"); ?>" data-placement="right">
      
        <strong class="btn btn-primary" style="color:white; margin-left:22px;"> <i data-feather="camera" class="icon-16"></i> Upload</strong>
        <input id="profile_image_file" class="upload" name="profile_image_file" type="file" />
    </div>
    <?php } ?>

    <button type="submit" class="btn btn-primary">
        <span data-feather="send" class="icon-16"></span> Submit
    </button>
    <?php echo form_close(); ?>
</div>


    <div class="box-content pl15">
        <p class="p10 m0"><label class="badge bg-info large"><strong> <?php echo $user_info->job_title; ?> </strong></label></p> 

        <?php if ($show_cotact_info) { ?>
            <p class="p10 m0"><i data-feather="mail" class="icon-16"></i> <?php echo $user_info->email ? $user_info->email : "-"; ?></p> 
            <?php if ($user_info->phone || $user_info->skype) { ?>
                <p class="p10 m0">
                    <?php if ($user_info->phone) { ?>
                        <i data-feather="phone" class="icon-16"></i> <?php echo $user_info->phone; ?> <span class="mr15"></span>
                        <?php
                    }
                    if ($user_info->skype) {
                        echo view("users/svg_social_icons/skype");
                        echo " " . $user_info->skype;
                    }
                    ?>
                </p>
            <?php } ?>
        <?php } ?> 
        <div class="p10 m0 clearfix">
            <div class="float-start">
                <?php
                if ($show_social_links) {
                    echo social_links_widget($social_link);
                }
                ?>
            </div>
            <?php
            if ($user_info->id != $login_user->id) {

                $show_message_button = true;

                //don't show message button in client contact's page if user hasn't permission to send/receive message to/from client
                if ($user_info->user_type === "client") {
                    $client_message_users = get_setting("client_message_users");
                    $client_message_users_array = explode(",", $client_message_users);
                    if (!in_array($login_user->id, $client_message_users_array)) {
                        $show_message_button = false;
                    }
                } else if ($user_info->user_type === "lead") {
                    //don't show message button for lead contacts
                    $show_message_button = false;
                }

                if (isset($hide_send_message_button) && $hide_send_message_button) {
                    $show_message_button = false;
                }

                if (get_setting("module_message") && $show_message_button) {
                    echo modal_anchor(get_uri("messages/modal_form/" . $user_info->id), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_message'), array("class" => "btn btn-transparent success btn-sm", "title" => app_lang('send_message')));
                }
            }
            ?>
        </div>
    </div>
</div>
 <script>
 
 document.addEventListener('DOMContentLoaded', function() {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded.');
        return;
    }

    // Handle form submission via AJAX
    $('#profile-image-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false, // Let jQuery set content type
            processData: false, // Prevent jQuery from processing the data
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.success) {
                        // Reload the page to reflect the updated image
                        window.location.reload();
                    } else {
                        console.error('Upload failed: ' + result.error);
                    }
                } catch (e) {
                    console.error('An error occurred while processing the response.');
                }
            },
            error: function() {
                console.error('An error occurred while updating the profile image.');
            }
        });
    });
});
 
 </script>
