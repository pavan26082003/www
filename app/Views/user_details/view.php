<div class="tab-content">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    
    <style>
        .profile-builder-container { font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a; max-width: 900px; margin: 20px auto; position: relative; }
        
        /* --- SETTINGS DROPDOWN --- */
        .settings-menu-container { position: absolute; top: 25px; right: 25px; z-index: 1000; }
        .settings-trigger { 
            width: 38px; height: 38px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: white; cursor: pointer; border: 1px solid rgba(255,255,255,0.3); transition: 0.3s;
        }
        .custom-dropdown { 
            position: absolute; right: 0; top: 45px; background: white; border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 200px; display: none; overflow: hidden; border: 1px solid #f1f5f9;
        }
        .dropdown-item { padding: 12px 18px; font-size: 14px; font-weight: 600; color: #475569; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .dropdown-item:hover { background: #f8fafc; color: #6366f1; }

        /* --- FORM STYLING --- */
       #form-wrapper { display: none; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .custom-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 6px; display: block; }
        .custom-input { padding: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: white; width: 100%; margin-bottom: 20px; outline: none; }
        .btn-save-view { width: 100%; padding: 18px; background: #0f172a; color: white; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; font-size: 16px; }

        /* --- PROFILE RESULT CARD --- */#profile-result-wrapper { display: block; background: white; border-radius: 35px; overflow: hidden; box-shadow: 0 40px 80px rgba(0,0,0,0.08); }
        .banner-sleek { height: 160px; background: #0f172a; }
        .profile-header-sleek { padding: 0 60px 30px; margin-top: -70px; display: flex; align-items: flex-end; gap: 30px; position: relative; }
        .pfp-main-sleek { width: 150px; height: 150px; border-radius: 45px; border: 7px solid white; background: #fff; object-fit: cover; display: block; }
        
        .name-section-sleek h1 { margin: 70px 0 8px; font-size: 34px; font-weight: 800; color: #0f172a; }
        .social-row-sleek { display: flex; gap: 15px; }.social-row-sleek a { 
    background: transparent !important; 
    width: auto !important; 
    height: auto !important; 
    padding: 0; 
    margin: 0; 
    color: #6366f1; 
    text-decoration: none; 
    font-size: 15px; 
}
        
        .content-grid-sleek { padding: 60px; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 60px; border-top: 1px solid #f1f5f9; }
        .label-style-sleek { font-size: 13px; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 30px; display: block; }
        
        .info-row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px; }
        .data-item label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .data-item p { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }

        .about-wrap-sleek { background: #f8fafc; padding: 30px; border-radius: 25px; border-left: 6px solid #6366f1; }
        .about-wrap-sleek p { line-height: 1.7; color: #475569; font-size: 15px; margin: 0; }

.validation-error { color: #ef4444; font-size: 11px; font-weight: 700; margin-top: -15px; margin-bottom: 15px; display: none; }
.input-error { border: 1.5px solid #ef4444 !important; }
        
        /* --- SUB-PAGE (EFACTOR STYLE) --- */
        .sub-settings-page { display: none; background: white; border-radius: 35px; padding: 60px; box-shadow: 0 40px 80px rgba(0,0,0,0.08); }
        .btn-back-profile { background: #f1f5f9; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 8px; }
        
        .account-row { display: flex; align-items: center; padding: 20px 0; border-bottom: 1px solid #f1f5f9; }
        .account-label { width: 280px; font-size: 14px; font-weight: 600; color: #64748b; }
        .account-input-clean { flex: 1; border: none; font-size: 14px; color: #0f172a; outline: none; background: transparent; }
        .btn-efactor-save { background: #1e293b; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; margin-top: 30px; }



/* --- FILES TABLE STYLING --- */
.files-action-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.btn-add-files { background: #fff; border: 1.5px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
.btn-add-files:hover { background: #f8fafc; }

.efactor-table-container { background: white; border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden; }
.efactor-table { width: 100%; border-collapse: collapse; text-align: left; }
.efactor-table th { background: #f8fafc; padding: 12px 20px; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
.efactor-table td { padding: 15px 20px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
.empty-msg { padding: 40px; text-align: center; color: #94a3b8; font-style: italic; }
/* --- UPDATED FILES ACTION STYLING --- */

/* --- NATURAL FLOW: NO FRAMES, NO COMPRESSION --- */
.file-grid-container {
    display: grid;
    /* Keeps your 4-column layout consistent */
    grid-template-columns: repeat(4, 1fr); 
    gap: 30px;
    padding: 20px;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
}

.file-card {
    position: relative;
    background: transparent;
    text-align: center;
    padding: 10px;
}

.file-card-preview {
    /* These lines let the image breathe freely */
    width: auto;
    max-width: 100%;
    max-height: 180px; /* Limits size but doesn't force it */
    
    /* This is the magic for the 'unfixed' look */
    object-fit: scale-down; 
    margin: 0 auto 10px auto;
    display: block;
    
    /* Removed backgrounds and borders to keep it 'free' */
    background: transparent;
    border: none;
    
    /* Adds a natural shadow only to the image itself */
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.08));
}

.file-card-name {
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    margin-top: 8px;
    display: block;
}

.file-card-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: 0.3s;
}

.file-card:hover .file-card-actions {
    opacity: 1;
}

.mini-action-btn {
    width: 25px;
    height: 25px;
    background: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    cursor: pointer;
}


/* --- UPDATED PROFILE IMAGE CONTAINER --- */
.pfp-wrapper-sleek {
    width: 150px;
    height: 150px;
    border-radius: 45px;
    overflow: hidden;
    margin-top: -70px;
    border: 7px solid white;
    background: #fff;
    position: relative;
    flex-shrink: 0; /* Add this line to stop the image from reducing width */
}

.pfp-main-sleek {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.pfp-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    opacity: 0;
    transition: 0.3s;
}

.pfp-wrapper-sleek:hover .pfp-overlay {
    opacity: 1;
}


/* Indicates fields are editable */
#out-name, #out-email, #out-phone, #out-dob, #out-addr, #out-bio {
    cursor: pointer;
    padding: 2px 5px;
    transition: background 0.2s;
    border-radius: 4px;
}

#out-name:hover, #out-email:hover, #out-phone:hover, #out-dob:hover, #out-addr:hover, #out-bio:hover {
    background: rgba(99, 102, 241, 0.1); /* Light indigo highlight */
    outline: 1px dashed #6366f1;
}

    </style>

    <div class="profile-builder-container">
        
      <div id="form-wrapper">
    <h2 class="form-title">Enter Your Details</h2>
    <?php echo form_open(get_uri("team_members/save_general_info/" . $user_info->id), array("id" => "general-info-form", "role" => "form", "enctype" => "multipart/form-data")); ?>
        <div class="row">
            <div class="col-md-6">
                <label class="custom-label">First Name</label>
                <input type="text" id="first_name" name="first_name" value="" placeholder="Enter First Name" class="custom-input">
            </div>
            <div class="col-md-6">
                <label class="custom-label">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="" placeholder="Enter Last Name" class="custom-input">
            </div>
          <div class="col-md-12" style="margin-bottom: 15px;">
    <label class="custom-label">Profile Image</label>
    <div style="display: flex; align-items: center; gap: 15px; background: #fdfdfd; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
        <div style="flex-grow: 1;">
            <input type="file" id="profile_image_file" name="profile_image_file" class="custom-input" style="margin-bottom: 0;" accept="image/*" onchange="previewProfileImage(this)">
        </div>
        
        <div id="image-preview-container" style="display: none; position: relative;">
            <img id="image-preview-element" src="#" alt="Preview" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #007bff;">
            <div onclick="deleteProfileImagePreview()" style="position: absolute; top: -5px; right: -5px; background: red; color: white; width: 16px; height: 16px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid #fff;">✕</div>
        </div>
    </div>
</div>
            <div class="col-md-6">
                <label class="custom-label">Email Address</label>
                <input type="email" id="email" name="email" value="" placeholder="email@example.com" class="custom-input">
            </div>
            <div class="col-md-6">
                <label class="custom-label">Phone</label>
                <input type="text" id="phone" name="phone" value="" placeholder="Phone Number" class="custom-input">
            </div>
            <div class="col-md-12">
                <label class="custom-label">Social Media Links</label>
                <div id="social-links-area">
                    <div class="row social-link-row" style="margin-bottom: 10px; display: flex; align-items: center;">
                        <div class="col-md-11">
                            <input type="text" class="custom-input social-url-input" placeholder="Paste link here" style="margin-bottom: 0;">
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div> 
                <button type="button" class="btn-add-files" onclick="addNewSocialRow()" style="margin-top: 5px;">
                    <i class="fas fa-plus-circle"></i> Add Another Link
                </button>
            </div>
            <div class="col-md-6">
                <label class="custom-label">Date of Birth</label>
                <input type="text" id="dob" name="dob" value="" placeholder="YYYY-MM-DD" class="custom-input">
            </div>
            <div class="col-md-6">
                <label class="custom-label">Gender</label>
                <select name="gender" id="gender" class="custom-input">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="custom-label">Mailing Address</label>
                <textarea id="address" name="address" class="custom-input" rows="2" placeholder="Enter Address"></textarea>
            </div>
            <div class="col-md-12">
                <label class="custom-label">Notes (About)</label>
                <textarea id="notes" name="notes" class="custom-input" rows="4" placeholder="Tell us about yourself"></textarea>
            </div>
        </div>
        <button type="submit" class="btn-save-view">Save and View Profile</button>
    <?php echo form_close(); ?>
</div>
<div class="banner-actions" style="position: absolute; top: 20px; right: 20px; display: flex; gap: 10px;">
    <div class="settings-trigger" onclick="goToSettings('job')" title="Job Info">
        <i class="fas fa-briefcase"></i>
    </div>
    
    <div class="settings-trigger" onclick="toggleDropdown()">
        <i class="fas fa-cog"></i>
    </div>
</div>

        <div id="profile-result-wrapper">
<div class="settings-menu-container">
        <div class="settings-trigger" onclick="toggleDropdown()">
            <i class="fas fa-cog"></i>
        </div>
        
        <div class="custom-dropdown" id="settingsDropdown">
            <div class="dropdown-item" onclick="goToSettings('files')"><i class="fas fa-folder-open"></i> Media Gallery</div>
            <div class="dropdown-item" onclick="goToSettings('account')"><i class="fas fa-user-lock"></i> Account Settings</div>
            <div class="dropdown-item" onclick="goToSettings('pref')"><i class="fas fa-sliders-h"></i> My Preferences</div>
        </div>

        <input type="file" id="hidden_file_trigger" style="display:none;" multiple>
    </div>


    <div class="banner-sleek"></div>
    <div class="profile-header-sleek">
        <div class="pfp-wrapper-sleek" onclick="$('#profile_image_file').click();" style="cursor: pointer; position: relative;">
            <img id="out-img" 
                 src="<?php echo get_avatar($user_info->image); ?>" 
                 onerror="this.src='https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';" 
                 class="pfp-main-sleek">
            <div class="pfp-overlay">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <div class="name-section-sleek">
            <h1 id="out-name" style="color: #000000; font-weight: 700;">Full Name</h1>
           <div id="social-links-container" class="social-row-sleek">
    </div>
        </div>
    </div>

    <div class="content-grid-sleek">
        <div>
            <span class="label-style-sleek">General Info</span>
            <div class="info-row-grid">
                <div class="data-item"><label>Email</label><p id="out-email" style="color: #000000; font-weight: 500;">Email Address</p></div>
                <div class="data-item"><label>Phone</label><p id="out-phone" style="color: #000000; font-weight: 500;">Phone Number</p></div>
            </div>
            <div class="info-row-grid">
                <div class="data-item"><label>DOB</label><p id="out-dob" style="color: #000000; font-weight: 500;">Date of Birth</p></div>
                <div class="data-item"><label>Gender</label><p id="out-gender" style="color: #000000; font-weight: 500;">Gender</p></div>
            </div>
            <div class="data-item" style="margin-top:20px;">
                <label>Mailing Address</label>
                <p id="out-addr" style="color: #000000; font-weight: 500;">Mailing Address</p>
            </div>
        </div>
        <div>
            <span class="label-style-sleek">About</span>
            <div class="about-wrap-sleek">
                <p id="out-bio" style="color: #000000;">Brief description about yourself...</p>
            </div>
        </div>
    </div>
    <div style="padding: 0 60px 40px;">
        <button class="btn-back-profile" onclick="toggleView('form')">← Edit Details</button>
    </div>
</div>
        <div id="sub-page-wrapper" class="sub-settings-page">
            <button class="btn-back-profile" onclick="toggleView('profile')"><i class="fas fa-chevron-left"></i> Back to Profile</button>
            <h2 id="sub-page-title" class="form-title">Settings</h2>
            <div id="sub-page-content"></div>
        </div>

    </div>
</div>


<div id="cropModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:12px; width:90%; max-width:500px; text-align:center;">
        <h3 style="margin-top:0;">Crop Your Profile Image</h3>
        <div style="max-height:400px; overflow:hidden; background:#eee; margin-bottom:15px;">
            <img id="cropPreviewImg" style="max-width:100%;">
        </div>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" onclick="cancelCrop()" style="padding:10px 20px; border-radius:6px; border:1px solid #ccc; cursor:pointer;">Cancel</button>
            <button type="button" onclick="confirmCrop()" style="padding:10px 20px; border-radius:6px; border:none; background:#007bff; color:white; cursor:pointer;">Apply & Save</button>
        </div>
    </div>
</div>



<script type="text/javascript">
    function toggleDropdown() { $("#settingsDropdown").fadeToggle(150); }

    function goToSettings(type) {
        $("#settingsDropdown").hide();
        // Hide EVERYTHING before showing the sub-page to prevent the "appearing below" issue
        $("#profile-result-wrapper, #form-wrapper, #sub-page-wrapper").hide();

        var html = "";
        var title = "";

        if(type === 'job') {
            title = "Job Info";
            html = `
                <div class="account-row"><span class="account-label">Job Title</span><input type="text" id="job_title_input" class="account-input-clean" placeholder="Job Title"></div>
                <div class="account-row"><span class="account-label">Salary</span><input type="text" id="salary_input" class="account-input-clean" placeholder="Salary"></div>
                <div class="account-row"><span class="account-label">Salary term</span><input type="text" id="salary_term_input" class="account-input-clean" placeholder="Salary term"></div>
                <div class="account-row"><span class="account-label">Date of hire</span><input type="text" id="hire_date_input" class="account-input-clean" placeholder="Date of hire"></div>
                <button class="btn-efactor-save" onclick="saveJobAndReturn()">
                    <i class="fas fa-check-square"></i> Save Changes
                </button>
            `;} else if(type === 'files') {
    title = "Media Gallery";
    html = `
        <div class="files-action-header" style="margin-bottom: 20px;">
            <span style="font-size: 14px; color: #64748b;">0 / 100 selected</span>
            <button class="btn-add-files" onclick="$('#hidden_file_trigger').click();">
                <i class="fas fa-upload"></i> Upload
            </button>
        </div>
        <div class="file-grid-container" id="files-grid">
            <div id="no-records-grid" style="grid-column: 1/-1; text-align: center; padding: 40px; color: #94a3b8;">
                No files uploaded yet.
            </div>
        </div>`;
        } else if(type === 'account') {
            title = "Account settings";
            html = `
                <div class="account-row"><span class="account-label">Email</span><input type="text" id="acc_email" class="account-input-clean" value="${$("#email").val()}"></div>
                <div class="account-row"><span class="account-label">Password</span><input type="password" id="acc_pass" class="account-input-clean" placeholder="At least 8 characters"></div>
                <div id="pass_error" class="validation-error">Password must be at least 8 characters.</div>
                <div class="account-row"><span class="account-label">Retype password</span><input type="password" id="acc_retype" class="account-input-clean" placeholder="Retype password"></div>
                <div id="retype_error" class="validation-error">Passwords do not match!</div>
                <button class="btn-efactor-save" onclick="validateAndSaveAccount()"><i class="fas fa-check-square"></i> Save Changes</button>
            `;
        } else if(type === 'pref') {
            title = "My preferences";
            html = `
                <div class="account-row"><span class="account-label">Notification sound volume</span><select class="account-input-clean"><option>-</option><option>Low</option><option>Medium</option><option>High</option></select></div>
                <div class="account-row"><span class="account-label">Enable web notification</span><select class="account-input-clean"><option>Yes</option><option>No</option></select></div>
                <div class="account-row"><span class="account-label">Enable email notification</span><select class="account-input-clean"><option>Yes</option><option>No</option></select></div>
                <div class="account-row"><span class="account-label">Language</span><select class="account-input-clean"><option>English</option><option>Spanish</option><option>French</option></select></div>
                <div class="account-row"><span class="account-label">Hide menus from topbar</span><input type="text" class="account-input-clean" placeholder="Hidden topbar menus"></div>
                <div class="account-row"><span class="account-label">Disable keyboard shortcuts</span><select class="account-input-clean"><option>No</option><option>Yes</option></select></div>
                <div class="account-row"><span class="account-label">Recently meaning</span><select class="account-input-clean"><option>In 1 day</option><option>In 1 week</option></select></div>
                <button class="btn-efactor-save" onclick="toggleView('profile')"><i class="fas fa-check-square"></i> Save Changes</button>`;
        }
        
        $("#sub-page-title").text(title);
        $("#sub-page-content").html(html);
        $("#sub-page-wrapper").fadeIn(250);
    }
$("#hidden_file_trigger").change(function() {
    const files = this.files;
    if (files.length > 0) {
        $("#no-records-grid").remove();
        
        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            let fileUrl = URL.createObjectURL(file);
            let isImage = file.type.startsWith('image/');
            let isVideo = file.type.startsWith('video/');

            let previewHtml = "";
            if (isImage) {
                previewHtml = `<img src="${fileUrl}" class="file-card-preview">`;
            } else if (isVideo) {
                previewHtml = `<video class="file-card-preview"><source src="${fileUrl}#t=0.1"></video>`;
            } else {
                previewHtml = `<div class="file-card-preview" style="display:flex; align-items:center; justify-content:center; background:#edf2f7;"><i class="fas fa-file-alt fa-3x" style="color:#cbd5e1;"></i></div>`;
            }

            let card = `
                <div class="file-card">
                    <div class="file-card-actions" style="z-index: 20;">
                        <a href="${fileUrl}" download="${file.name}" class="mini-action-btn download-file" style="color: #6366f1;"><i class="fas fa-download"></i></a>
                        <div class="mini-action-btn delete-file" style="color: #ef4444;"><i class="fas fa-trash"></i></div>
                    </div>
                    ${previewHtml}
                    <span class="file-card-name">${file.name}</span>
                </div>`;
            
            $("#files-grid").append(card);
        }

        // --- NEW: COUNT UPDATE LOGIC ---
        let currentCount = $("#files-grid .file-card").length;
        $(".files-action-header span").text(currentCount + " / 100 selected");
        // ------------------------------

        appAlert.success("Added to library.");
    }
});
    function toggleView(view) {
        $("#sub-page-wrapper, #profile-result-wrapper, #form-wrapper").hide();
        if(view === 'profile') $("#profile-result-wrapper").fadeIn(250);
        else if(view === 'form') $("#form-wrapper").fadeIn(250);
    }

    // --- 2. MAIN LOGIC ON DOCUMENT READY ---
    $(document).ready(function () {
        console.log("System Ready: API mode active.");

        // --- THE API SUBMISSION LOGIC --- [cite: 2026-01-21]
        $("#general-info-form").on("submit", function(e) {
            e.preventDefault(); 

            // Prepare Payload
          const formData = {
    user_id: "<?php echo $user_info->id; ?>",
    first_name: $("#first_name").val(),
    last_name: $("#last_name").val(),
    email: $("#email").val(),
    phone: $("#phone").val(),
    address: $("#address").val(),
    notes: $("#notes").val(),
    dob: $("#dob").val(),
    profile_image: $("#out-img").attr("src"),
    social_links: $(".social-url-input").map(function() { return $(this).val(); }).get().join(","),
    // --- ADD THIS LINE ---
    gender: $("#gender").val() 
};

            // CONSOLE PRINT: See the data before it leaves [cite: 2026-01-21]
            console.log("%c--- SENDING DATA TO API ---", "color: blue; font-weight: bold;");
            console.table(formData);

            $.ajax({
                url: "https://efactor.tech/app/Views/team_members/fetch_data.php", // Path from image_83ee80.png
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log("%c--- API RESPONSE ---", "color: green; font-weight: bold;");
                    console.log(response);

                    if(response.success) {
                        // AUTOMATIC UI CHANGE [cite: 2026-01-21]
                        $("#out-name").text(formData.first_name + " " + formData.last_name);
                        $("#out-email").text(formData.email || "---");
                        $("#out-phone").text(formData.phone || "---");
                        $("#out-addr").text(formData.address || "---");
                        $("#out-bio").text(formData.notes || "No description provided.");

                        appAlert.success("Profile Updated Successfully!");
                        window.toggleView('profile');
                    } else {
                        console.error("API Logic Error: " + response.message);
                        appAlert.error(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("%c--- CRITICAL SYSTEM ERROR ---", "color: red; font-weight: bold;");
                    console.log(xhr.responseText);
                    appAlert.error("Could not reach fetch_data.php file.");
                }
            });
        });





    // 2. PREVENT BLINKING / SAME-PAGE NAVIGATION
    $(document).on("click", "a[href*='#account'], a[href*='#pref']", function (e) {
        // If we are already on the profile page, don't reload, just switch
        if (window.location.href.indexOf("team_members/view") > -1) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            var hash = $(this).attr("href").split('#')[1];
            if (hash === 'account') goToSettings('account');
            if (hash === 'pref') goToSettings('pref');
        }
    });



    // Your existing form initialization
    setDatePicker("#dob");


// DELETE THIS ENTIRE BLOCK:
$("#general-info-form").appForm({
    isModal: false,
    onSuccess: function (result) {
        appAlert.success(result.message, {duration: 5000});
        
        $("#out-name").text($("#first_name").val() + " " + $("#last_name").val());
        $("#out-email").text($("#email").val() || "---");
        $("#out-phone").text($("#phone").val() || "---");
        $("#out-dob").text($("#dob").val() || "00/00/0000");
        $("#out-gender").text($("#gender option:selected").text());
        $("#out-addr").text($("#address").val() || "---");
        $("#out-bio").text($("#notes").val() || "No description provided.");

        let linkList = [];
        $(".social-url-input").each(function(index) {
            let val = $(this).val().trim();
            if(val) {
                let webLink = val.match(/^https?:\/\//) ? val : "https://" + val;
                linkList.push(`<a href="${webLink}" target="_blank" style="...">...</a>`); 
            }
        });
        toggleView('profile');
    }
});

});

$(document).on("change", "#profile_image_file", function () {
var file = this.files[0];
 if (file) {
var reader = new FileReader();
 reader.onload = function (e) {
 // This updates the Result Card image and the Form preview at the same time
 $("#out-img, .profile-image-preview img").attr("src", e.target.result);
 };
 reader.readAsDataURL(file);
  }

});

// --- IN-PLACE EDITING LOGIC ---
$(document).on("click", "#out-name, #out-email, #out-phone, #out-dob, #out-addr, #out-bio, #out-gender", function() {
    var $el = $(this);
    var currentVal = $el.text().trim().toLowerCase();
    var fieldId = $el.attr('id').replace('out-', '');
    var $editor;

    // SPECIAL CASE: GENDER DROPDOWN
    if (fieldId === "gender") {
        $editor = $("<select>", {
            class: "custom-input",
            style: "margin: 0; padding: 5px; height: auto; width: auto;"
        });
        $editor.append('<option value="male">Male</option>');
        $editor.append('<option value="female">Female</option>');
        $editor.val(currentVal); // Set to current value
    } else {
        // STANDARD CASE: TEXT INPUT
        $editor = $("<input>", {
            val: $el.text().trim(),
            type: "text",
            class: "custom-input",
            style: "margin: 0; padding: 5px; height: auto; font-size: inherit; width: 100%;"
        });
    }

    $el.hide().after($editor);
    $editor.focus();

    // Save on blur or Enter
    $editor.on("blur keydown", function(e) {
        if (e.type === "blur" || e.which === 13) {
            var newVal = $(this).val();
            var displayVal = (fieldId === "gender") ? $(this).find("option:selected").text() : newVal;
            
            $el.text(displayVal || "---").show();
            
            // SYNC back to the main form
            if (fieldId === "gender") {
                $("#gender").val(newVal).change(); // Updates the hidden select in your form
            } else if (fieldId === "name") {
                var names = newVal.split(" ");
                $("#first_name").val(names[0]);
                $("#last_name").val(names.slice(1).join(" "));
            } else {
                $("#" + (fieldId === "addr" ? "address" : fieldId === "bio" ? "notes" : fieldId)).val(newVal);
            }
            
            $(this).remove();
        }
    });
});


function addNewSocialRow() {
    const uniqueId = Date.now(); 
    const newRow = `
        <div class="row social-link-row" id="social_${uniqueId}" style="margin-bottom: 10px; display: flex; align-items: center;">
            <div class="col-md-11">
                <input type="text" class="custom-input social-url-input" placeholder="Paste link here" style="margin-bottom: 0;">
            </div>
            <div class="col-md-1">
                <i class="fas fa-trash-alt" style="color: #f87171; cursor: pointer;" onclick="$('#social_${uniqueId}').remove()"></i>
            </div>
        </div>`;
    $("#social-links-area").append(newRow);
}


// --- FIX FOR DELETE BUTTON ---
$(document).on("click", ".delete-file", function(e) {
    e.preventDefault();
    e.stopPropagation(); // Stops the video from playing when clicking delete
    $(this).closest('.file-card').fadeOut(300, function() {
        $(this).remove();
        // Update the count (e.g., 0 / 100 selected)
        let count = $("#files-grid .file-card").length;
        $(".files-action-header span").text(count + " / 100 selected");
    });
});

// --- FIX FOR DOWNLOAD BUTTON ---
$(document).on("click", ".download-file", function(e) {
    e.stopPropagation(); // Ensures the click doesn't trigger video controls
});




// --- NEW FUNCTIONS TO SYNC SETTINGS TO API --- [cite: 2026-01-29]

function saveJobAndReturn() {
    const data = {
        user_id: "<?php echo $user_info->id; ?>",
        job_title: $("#job_title_input").val(),
        salary: $("#salary_input").val(),
        salary_term: $("#salary_term_input").val(),
        hire_date: $("#hire_date_input").val()
    };

    $.post("fetch_data.php", data, function(res) {
        appAlert.success("Job Info Saved to Database!");
        toggleView('profile'); // Returns to profile automatically [cite: 2026-01-21]
    });
}

function validateAndSaveAccount() {
    const pass = $("#acc_pass").val();
    const retype = $("#acc_retype").val();

    if (pass !== "" && pass !== retype) {
        $("#retype_error").show();
        return;
    }

    const data = {
        user_id: "<?php echo $user_info->id; ?>",
        email: $("#acc_email").val(),
        password: pass
    };

    $.post("fetch_data.php", data, function(res) {
        appAlert.success("Account Updated!");
        toggleView('profile');
    });
}



// --- UPDATED: FETCH AND RENDER PROFILE DATA ---
function loadProfileData() {
    $.ajax({
        url: "https://efactor.tech/app/Views/team_members/fetch_data.php",
        type: "POST",
        data: { 
            action: 'get_full_profile', 
            user_id: "<?php echo $user_info->id; ?>" 
        },
        dataType: "json",
        success: function(response) {
            console.log("Database Response:", response);
            if(response.success && response.data) {
                const d = response.data;

                // 1. Update the "View" UI
                $("#out-name").text(d.first_name + " " + d.last_name);
                // CHANGE: profile_email -> email
                $("#out-email").text(d.email || "---"); 
                $("#out-phone").text(d.business_phone || "---");
                $("#out-addr").text(d.mailing_address || "---");
                $("#out-bio").text(d.profile_summary || "No description provided."); // Updated to match table
                $("#out-gender").text(d.gender || "---");

                // --- DOB Formatting ---
                if (d.date_of_birth) {
                    const dateParts = d.date_of_birth.split("-");
                    $("#out-dob").text(dateParts.length === 3 ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}` : d.date_of_birth);
                }

                // 2. Update the "Edit Form" inputs
                $("#first_name").val(d.first_name);
                $("#last_name").val(d.last_name);
                // CHANGE: profile_email -> email
                $("#email").val(d.email); 
                $("#phone").val(d.business_phone);
                $("#dob").val(d.date_of_birth); 
                $("#gender").val(d.gender);
                $("#address").val(d.mailing_address);
                $("#notes").val(d.profile_summary); // Updated to match table

                // --- Social Media Links ---
                if (d.social_media_links) {
                    try {
                        const links = JSON.parse(d.social_media_links);
                        $("#social_links").val(Array.isArray(links) ? links.join(", ") : links);
                        let container = $("#social-links-container");
                        container.empty(); 
                        if (Array.isArray(links)) {
                            links.forEach(function(url) {
                                if(url.trim() !== "") {
                                    container.append('<a href="' + url + '" target="_blank" style="display:block; color:blue; text-decoration:underline;">' + url + '</a>');
                                }
                            });
                        }
                    } catch (e) {
                        $("#social_links").val(d.social_media_links);
                    }
                }

                // 3. Update Profile Images
                // CHANGE: profile_image -> profile_photo_url
                if (d.profile_photo_url) { 
                    $("#out-img").attr("src", d.profile_photo_url);
                    $("#user-dropdown img").attr("src", d.profile_photo_url);
                }
            }
        },
        error: function(xhr) {
            console.error("Failed to fetch data:", xhr.responseText);
        }
    });
}

// Initialize on load
loadProfileData();



let cropper; 
let selectedBlob; // To store the cropped data

// 1. Opens the Modal when file is picked
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const modal = document.getElementById('cropModal');
            const img = document.getElementById('cropPreviewImg');
            
            img.src = e.target.result;
            modal.style.display = 'flex';

            // Initialize Cropper showing the ENTIRE image
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, {
                aspectRatio: 1,      // Forces a square
                viewMode: 1,         // Stays inside image bounds
                autoCropArea: 1,     // Starts with the full image selected
                dragMode: 'move'
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// 2. Resizes to 500x500 and saves as a Base64 string
function confirmCrop() {
    if (!cropper) return;

    // 1. Get the cropped image as a 500x500 canvas
    const canvas = cropper.getCroppedCanvas({
        width: 500,
        height: 500
    });

    // 2. Convert the canvas directly to a Base64 String (DataURL)
    // This is the "data:image/webp;base64,..." text that stays after refresh
    const base64String = canvas.toDataURL('image/webp', 0.8); 

    // 3. Update your UI previews with the permanent string
    const previewElement = document.getElementById('image-preview-element');
    const outImg = document.getElementById('out-img');
    const container = document.getElementById('image-preview-container');

    if (previewElement) previewElement.src = base64String;
    if (outImg) outImg.src = base64String;
    if (container) container.style.display = 'block';

    // 4. CRITICAL: Store this string in a hidden input so your PHP can save it
    // Make sure you have an input with this ID in your HTML form
   // Ensure you are using the correct ID for the hidden input
let hiddenInput = document.getElementById('profile_image_base64');
if (!hiddenInput) {
    hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.id = 'profile_image_base64';
    hiddenInput.name = 'profile_image'; 
    document.getElementById('general-info-form').appendChild(hiddenInput);
}

// FIX: Use .value for a standard JavaScript element, not .val
hiddenInput.value = base64String;

    console.log("Image converted to Base64 string for permanent saving.");
    closeCropModal();
}
function cancelCrop() {
    document.getElementById('profile_image_file').value = ""; // Clear input
    closeCropModal();
}

function closeCropModal() {
    document.getElementById('cropModal').style.display = 'none';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}


function deleteProfileImagePreview() {
    // 1. Clear the actual file input so the browser "forgets" the file
    const fileInput = document.getElementById('profile_image_file');
    if (fileInput) fileInput.value = ""; 

    // 2. Hide the side-box preview container
    const previewContainer = document.getElementById('image-preview-container');
    if (previewContainer) previewContainer.style.display = 'none';

    // 3. Clear the cropped data variable so your save function doesn't send it
    selectedBlob = null;

    // 4. Reset the big profile image (out-img) to your default placeholder
    const outImg = document.getElementById('out-img');
    if (outImg) {
        // Using the same default logic from your header
        outImg.src = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';
    }

    console.log("Profile image and crop data cleared.");
}
</script>
