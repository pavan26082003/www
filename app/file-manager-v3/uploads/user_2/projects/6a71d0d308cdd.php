<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* Modal specific styles - scoped to not affect the rest of the application */
#brandModalWrapper {
  --ink: #13233d;
  --muted: #64748b;
  --line: #e6edf7;
  --panel: #fff;
  --soft: #f6f8fc;
  --primary: #0f766e;
  --primary-2: #0ea5a4;
  --primary-light: #14b8a6;
  --primary-lighter: #99f6e4;
  --primary-bg: #f0fdfa;
  --primary-dark: #0d5e56;
  --success: #059669;
  --danger: #dc2626;
  --shadow: 0 14px 36px rgba(34, 55, 92, .10);
  font-family: 'Inter', Arial, sans-serif;
  color: var(--ink);
  max-width: 100%;
  padding: 0;
}

#brandModalWrapper *,
#brandModalWrapper *:before,
#brandModalWrapper *:after {
  box-sizing: border-box;
}

/* Hide any external elements that might leak in */
#brandModalWrapper .modal-backdrop {
  display: none !important;
}

/* Modal Container - Clean and Minimal */
.brand-modal-container {
  background: #ffffff;
  border-radius: 24px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  max-width: 640px;
  width: 100%;
  margin: 0 auto;
  animation: modalSlideUp 0.35s ease;
  position: relative;
}

@keyframes modalSlideUp {
  from { opacity: 0; transform: translateY(30px) scale(0.97); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Close Button */
.brand-modal-close {
  position: absolute;
  top: 16px;
  right: 18px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: rgba(255, 255, 255, 0.85);
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  color: #64748b;
  transition: all 0.25s ease;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  backdrop-filter: blur(4px);
}

.brand-modal-close:hover {
  background: #ffffff;
  color: #0f766e;
  transform: rotate(90deg) scale(1.05);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.brand-modal-close i {
  font-size: 16px;
}

/* Modal Header - Clean with accent color */
.brand-modal-header {
  background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
  padding: 24px 32px 20px;
  padding-right: 65px;
  border-bottom: 2px solid #99f6e4;
  position: relative;
}

.brand-modal-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0d5e56;
  margin: 0 0 6px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-modal-header h2 i {
  color: #0f766e;
  font-size: 1.4rem;
}

.brand-modal-header p {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 400;
}

/* Modal Body */
.brand-modal-body {
  padding: 28px 32px 16px;
  background: #ffffff;
}

.hidden { display: none !important; }

/* Choice Section */
.choice-section {
  text-align: center;
  padding: 8px 0 4px;
}

.choice-section h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 6px;
}

.choice-section .sub-text {
  color: #94a3b8;
  margin-bottom: 24px;
  font-size: 0.9rem;
}

.choice-btn-group {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 12px;
}

.choice-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 32px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  min-width: 170px;
  justify-content: center;
}

.choice-btn i {
  font-size: 1.1rem;
}

.choice-btn-yes {
  background: linear-gradient(135deg, #10b981, #34d399);
  color: white;
}

.choice-btn-yes:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(16, 185, 129, 0.35);
}

.choice-btn-no {
  background: linear-gradient(135deg, #f97316, #fb923c);
  color: white;
}

.choice-btn-no:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(249, 115, 22, 0.35);
}

.choice-btn:active {
  transform: translateY(0) scale(0.97);
}

/* Form Section Title */
.form-section-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-section-title i {
  font-size: 1.2rem;
}

/* Form Groups */
.form-group {
  margin-bottom: 16px;
}

.form-group label {
  font-weight: 600;
  color: #334155;
  font-size: 0.85rem;
  margin-bottom: 5px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-group label i {
  color: #0f766e;
  font-size: 0.8rem;
  width: 16px;
}

.form-control {
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.9rem;
  transition: all 0.25s ease;
  font-family: 'Inter', sans-serif;
  background: #fafbfc;
  color: #1e293b;
}

.form-control:focus {
  border-color: #0f766e;
  box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
  outline: none;
  background: #ffffff;
}

.form-control::placeholder {
  color: #b0bcca;
}

/* Step Indicators */
.step-indicator {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: #f0fdfa;
  border-radius: 12px;
  margin-bottom: 18px;
  border-left: 4px solid #0f766e;
}

.step-indicator i {
  font-size: 1.1rem;
  color: #0f766e;
}

.step-indicator-text {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

/* OTP Message */
#otp-message {
  display: none;
  padding: 12px 16px;
  background: #d1fae5;
  border-radius: 10px;
  color: #065f46;
  font-weight: 600;
  margin: 14px 0;
  border-left: 4px solid #10b981;
  animation: slideDown 0.3s ease;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
}

#otp-message i {
  font-size: 1.1rem;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

#otp-box {
  display: none;
  margin-top: 14px;
  animation: slideDown 0.3s ease;
}

/* Buttons */
.btn {
  padding: 10px 24px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.25s ease;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
}

.btn i {
  font-size: 0.95rem;
}

.btn-info {
  background: linear-gradient(135deg, #06b6d4, #3b82f6);
  color: white;
  box-shadow: 0 4px 14px rgba(6, 182, 212, 0.25);
}

.btn-info:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(6, 182, 212, 0.35);
  color: white;
}

.btn-success {
  background: linear-gradient(135deg, #10b981, #34d399);
  color: white;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
}

.btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
  color: white;
}

.btn-primary {
  background: linear-gradient(135deg, #0f766e, #0ea5a4);
  color: white;
  box-shadow: 0 4px 14px rgba(15, 118, 110, 0.25);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(15, 118, 110, 0.35);
  color: white;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover {
  background: #e2e8f0;
  transform: translateY(-2px);
}

.btn:active {
  transform: translateY(0) scale(0.97);
}

/* Image Preview */
.image-preview-box {
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid #e2e8f0;
  display: inline-block;
  max-width: 100%;
  background: #f8fafc;
  padding: 4px;
}

.image-preview-box img {
  display: block;
  max-height: 100px;
  max-width: 100%;
  border-radius: 8px;
}

/* Collapsible Sections */
.collapsible-wrapper {
  margin-bottom: 12px;
}

.collapsible-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #f8fafc;
  border-radius: 10px 10px 0 0;
  cursor: pointer;
  transition: all 0.25s ease;
  border: 1.5px solid #e2e8f0;
  border-bottom: none;
  font-weight: 600;
  color: #334155;
  user-select: none;
  font-size: 0.9rem;
}

.collapsible-header:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.collapsible-header i:first-child {
  color: #0f766e;
  font-size: 1rem;
}

.collapsible-header .ml-auto {
  margin-left: auto;
  transition: transform 0.3s ease;
  font-size: 0.8rem;
  color: #94a3b8;
}

.collapsible-header[aria-expanded="true"] .ml-auto {
  transform: rotate(180deg);
}

.collapse .card {
  border: none;
  border-radius: 0 0 10px 10px;
  background: #fafbfc;
  border: 1.5px solid #e2e8f0;
  border-top: none;
}

.collapse .card-body {
  padding: 16px 18px;
}

/* Modal Footer */
.modal-footer {
  padding: 18px 32px 24px;
  background: #ffffff;
  border-top: 1.5px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.modal-footer .btn {
  min-width: 100px;
}

/* Row */
.row {
  margin: 0 -6px;
}

.row > [class*="col-"] {
  padding: 0 6px;
}

.required-star {
  color: #ef4444;
}

/* Responsive */
@media (max-width: 640px) {
  .brand-modal-container { 
    border-radius: 16px; 
    max-width: 100%;
  }
  
  .brand-modal-header { 
    padding: 18px 18px 14px;
    padding-right: 50px;
  }
  
  .brand-modal-header h2 { 
    font-size: 1.25rem; 
  }
  
  .brand-modal-body { 
    padding: 20px 16px 12px; 
  }
  
  .brand-modal-close {
    top: 12px;
    right: 12px;
    width: 32px;
    height: 32px;
    font-size: 14px;
  }
  
  .choice-btn { 
    padding: 12px 20px; 
    font-size: 0.85rem; 
    min-width: 140px; 
  }
  
  .modal-footer { 
    flex-direction: column-reverse; 
    padding: 14px 16px 18px;
  }
  
  .modal-footer .btn { 
    width: 100%; 
  }
  
  .form-section-title { 
    font-size: 1rem; 
  }
  
  .step-indicator { 
    padding: 10px 14px; 
  }
  
  .step-indicator-text { 
    font-size: 0.82rem; 
  }
  
  .collapsible-header { 
    padding: 10px 14px; 
    font-size: 0.85rem; 
  }
  
  .collapse .card-body { 
    padding: 14px; 
  }
}

@media (max-width: 400px) {
  .choice-btn-group { 
    flex-direction: column; 
    align-items: center; 
  }
  
  .choice-btn { 
    width: 100%; 
  }
}
</style>

<div id="brandModalWrapper">
  <div class="brand-modal-container">
    
    <!-- MODAL HEADER -->
    <div class="brand-modal-header">
      <!-- Close Button -->
      <button type="button" class="brand-modal-close" onclick="closeModal()" aria-label="Close modal" title="Close">
        <i class="fas fa-times" aria-hidden="true"></i>
      </button>
      
      <h2><i class="fas fa-tags" aria-hidden="true"></i> Add New Brand</h2>
      <p>Fill in the details to register your brand</p>
    </div>

    <div class="brand-modal-body">

      <!-- CHOICE SECTION -->
      <div id="choiceSection" class="choice-section">
        <h3>Does this brand have a server?</h3>
        <p class="sub-text">Choose the option that best describes your brand setup</p>
        <div class="choice-btn-group">
          <button class="choice-btn choice-btn-yes" onclick="showForm('yes')" aria-label="Yes, brand has a server">
            <i class="fas fa-server" aria-hidden="true"></i> Yes, Has Server
          </button>
          <button class="choice-btn choice-btn-no" onclick="showForm('no')" aria-label="No, brand has no server">
            <i class="fas fa-store" aria-hidden="true"></i> No Server
          </button>
        </div>
      </div>

      <!-- FORM FOR BRAND - YES (has server) -->
      <div id="brandYesForm" class="hidden">
        <div class="form-section-title">
          <i class="fas fa-server" style="color:#0f766e;" aria-hidden="true"></i>
          Brand With Server
        </div>
        <?php echo form_open_multipart(get_uri("items/savebrand"), array("id" => "yes-form", "class" => "general-form", "role" => "form")); ?>
          <input type="hidden" name="brand_choice" value="Yes">

          <!-- STEP 1: Email + IP -->
          <div id="step-email-ip">
            <div class="step-indicator">
              <i class="fas fa-envelope" aria-hidden="true"></i>
              <div class="step-indicator-text">Step 1 — Verify your identity via OTP</div>
            </div>
            <div class="form-group">
              <label for="input_email"><i class="fas fa-envelope" aria-hidden="true"></i> Email Address <span class="required-star">*</span></label>
              <input type="email" id="input_email" placeholder="Enter your email address" class="form-control" required aria-required="true">
            </div>
            <div class="form-group">
              <label for="input_ip"><i class="fas fa-network-wired" aria-hidden="true"></i> IP Address <span class="required-star">*</span></label>
              <input type="text" id="input_ip" placeholder="Enter IP address" class="form-control" required aria-required="true">
            </div>
            <button type="button" class="btn btn-info" onclick="sendOtp()">
              <i class="fas fa-paper-plane" aria-hidden="true"></i> Send OTP
            </button>
          </div>

          <div id="otp-message">
            <i class="fas fa-check-circle" aria-hidden="true"></i> OTP sent successfully! Please check your email.
          </div>

          <!-- STEP 2: OTP -->
          <div id="otp-box">
            <div class="step-indicator">
              <i class="fas fa-key" aria-hidden="true"></i>
              <div class="step-indicator-text">Step 2 — Enter the OTP from your email</div>
            </div>
            <div class="form-group">
              <label for="input_otp"><i class="fas fa-lock" aria-hidden="true"></i> One-Time Password <span class="required-star">*</span></label>
              <input type="text" id="input_otp" placeholder="Enter OTP" class="form-control" aria-label="Enter OTP">
            </div>
            <button type="button" class="btn btn-success" onclick="verifyOtp()">
              <i class="fas fa-shield-alt" aria-hidden="true"></i> Verify OTP
            </button>
          </div>

          <!-- STEP 3: Brand Details -->
          <div id="step-brand-details" style="display:none;">
            <div class="step-indicator">
              <i class="fas fa-tag" aria-hidden="true"></i>
              <div class="step-indicator-text">Step 3 — Enter brand details</div>
            </div>

            <div class="form-group">
              <label for="name_yes"><i class="fas fa-tag" aria-hidden="true"></i> Brand Name <span class="required-star">*</span></label>
              <input type="text" name="name" id="name_yes" class="form-control" placeholder="e.g. Apple, Samsung" required aria-required="true">
            </div>

            <div class="form-group">
              <label for="brandlogo_yes"><i class="fas fa-image" aria-hidden="true"></i> Brand Logo <span style="color:#94a3b8; font-weight:400;">(Optional)</span></label>
              <input type="file" class="form-control" id="brandlogo_yes" name="brandlogo" accept="image/*" onchange="showImagePreview(this, 'brandlogo-preview-yes')" aria-label="Upload brand logo">
              <div id="brandlogo-preview-yes-wrap" class="image-preview-box" style="display:none; margin-top:10px;">
                <img id="brandlogo-preview-yes" alt="Logo preview">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="brand_category_yes"><i class="fas fa-folder" aria-hidden="true"></i> Brand Category</label>
                  <input type="text" name="brand_category" id="brand_category_yes" class="form-control" placeholder="e.g. Electronics">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="year_established_yes"><i class="fas fa-calendar" aria-hidden="true"></i> Year Established</label>
                  <input type="number" name="year_established" id="year_established_yes" class="form-control" placeholder="e.g. 2010" min="1800" max="2099">
                </div>
              </div>
            </div>

            <!-- DIGITAL PRESENCE -->
            <div class="collapsible-wrapper">
              <div class="collapsible-header" data-toggle="collapse" data-target="#digitalPresence" aria-expanded="false" aria-controls="digitalPresence" role="button">
                <i class="fas fa-globe" aria-hidden="true"></i>
                <span>1) Digital Presence</span>
                <i class="fas fa-chevron-down ml-auto" aria-hidden="true"></i>
              </div>
              <div class="collapse" id="digitalPresence">
                <div class="card card-body">
                  <div class="form-group"><label for="website_url"><i class="fas fa-link" aria-hidden="true"></i> Official Website URL</label><input type="url" name="website_url" id="website_url" class="form-control" placeholder="https://yourbrand.com"></div>
                  <div class="form-group"><label for="social_media"><i class="fas fa-share-alt" aria-hidden="true"></i> Social Media Profiles</label><input type="text" name="social_media" id="social_media" class="form-control" placeholder="Instagram, Twitter/X, Facebook, etc."></div>
                  <div class="form-group"><label for="server_url"><i class="fas fa-server" aria-hidden="true"></i> Server URL</label><input type="url" name="server_url" id="server_url" class="form-control" placeholder="https://server.yourbrand.com"></div>
                  <div class="form-group"><label for="app_download_links"><i class="fas fa-mobile-alt" aria-hidden="true"></i> App Download Links</label><input type="url" name="app_download_links" id="app_download_links" class="form-control" placeholder="https://play.google.com/..."></div>
                </div>
              </div>
            </div>

            <!-- SERVER INFORMATION -->
            <div class="collapsible-wrapper">
              <div class="collapsible-header" data-toggle="collapse" data-target="#serverInfo" aria-expanded="false" aria-controls="serverInfo" role="button">
                <i class="fas fa-database" aria-hidden="true"></i>
                <span>2) Server Information</span>
                <i class="fas fa-chevron-down ml-auto" aria-hidden="true"></i>
              </div>
              <div class="collapse" id="serverInfo">
                <div class="card card-body">
                  <div class="form-group"><label for="server_name"><i class="fas fa-server" aria-hidden="true"></i> Server Name</label><input type="text" name="server_name" id="server_name" class="form-control" placeholder="Enter Server Name"></div>
                  <div class="form-group"><label for="server_type"><i class="fas fa-tag" aria-hidden="true"></i> Server Type</label><input type="text" name="server_type" id="server_type" class="form-control" placeholder="e.g., community, support, official"></div>
                  <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="server_creation_date"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Creation Date</label><input type="date" name="server_creation_date" id="server_creation_date" class="form-control"></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="server_member_count"><i class="fas fa-users" aria-hidden="true"></i> Member Count</label><input type="number" name="server_member_count" id="server_member_count" class="form-control" placeholder="e.g. 5000"></div></div>
                  </div>
                  <div class="form-group"><label for="server_verification_status"><i class="fas fa-check-circle" aria-hidden="true"></i> Verification Status</label><input type="text" name="server_verification_status" id="server_verification_status" class="form-control" placeholder="Verified / Unverified"></div>
                  <div class="form-group"><label for="server_invite_link"><i class="fas fa-link" aria-hidden="true"></i> Invite Link</label><input type="url" name="server_invite_link" id="server_invite_link" class="form-control" placeholder="https://..."></div>
                </div>
              </div>
            </div>

            <!-- ADDITIONAL INFORMATION -->
            <div class="collapsible-wrapper">
              <div class="collapsible-header" data-toggle="collapse" data-target="#additionalInfo" aria-expanded="false" aria-controls="additionalInfo" role="button">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                <span>3) Additional Information</span>
                <i class="fas fa-chevron-down ml-auto" aria-hidden="true"></i>
              </div>
              <div class="collapse" id="additionalInfo">
                <div class="card card-body">
                  <div class="form-group"><label for="brand_description"><i class="fas fa-align-left" aria-hidden="true"></i> Brand Description</label><textarea name="brand_description" id="brand_description" rows="3" class="form-control" placeholder="Describe your brand..."></textarea></div>
                  <div class="form-group"><label for="key_products"><i class="fas fa-box" aria-hidden="true"></i> Key Products/Services</label><input type="text" name="key_products" id="key_products" class="form-control" placeholder="e.g. Smartphones, Laptops"></div>
                  <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="headquarters_location"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Headquarters</label><input type="text" name="headquarters_location" id="headquarters_location" class="form-control" placeholder="City, Country"></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="parent_company"><i class="fas fa-building" aria-hidden="true"></i> Parent Company</label><input type="text" name="parent_company" id="parent_company" class="form-control" placeholder="Parent company name"></div></div>
                  </div>
                  <div class="form-group"><label for="contact_email"><i class="fas fa-envelope" aria-hidden="true"></i> Contact Email</label><input type="email" name="contact_email" id="contact_email" class="form-control" placeholder="contact@brand.com"></div>
                  <div class="form-group"><label for="partnerships"><i class="fas fa-handshake" aria-hidden="true"></i> Partnerships</label><input type="text" name="partnerships" id="partnerships" class="form-control" placeholder="Partner companies or collaborations"></div>
                </div>
              </div>
            </div>
          </div><!-- /step-brand-details -->

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="goBack()">
              <i class="fas fa-arrow-left" aria-hidden="true"></i> Back
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save" aria-hidden="true"></i> Save Brand
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>

      <!-- FORM FOR BRAND - NO (no server) -->
      <div id="brandNoForm" class="hidden">
        <div class="form-section-title">
          <i class="fas fa-store" style="color:#f97316;" aria-hidden="true"></i>
          Brand Without Server
        </div>
        <?php echo form_open_multipart(get_uri("items/savebrand"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
          <input type="hidden" name="brand_choice" value="No">

          <div class="form-group">
            <label for="name"><i class="fas fa-tag" aria-hidden="true"></i> Brand Name <span class="required-star">*</span></label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Apple, Samsung" required aria-required="true">
          </div>

          <div class="form-group">
            <label for="brandlogo"><i class="fas fa-image" aria-hidden="true"></i> Brand Logo <span style="color:#94a3b8; font-weight:400;">(Optional)</span></label>
            <input type="file" class="form-control" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this, 'brandlogo-preview')" aria-label="Upload brand logo">
            <div id="brandlogo-preview-wrap" class="image-preview-box" style="display:none; margin-top:10px;">
              <img id="brandlogo-preview" alt="Logo preview">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="brand_category"><i class="fas fa-folder" aria-hidden="true"></i> Brand Category</label>
                <input type="text" id="brand_category" name="brand_category" class="form-control" placeholder="e.g. Electronics">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="year_established"><i class="fas fa-calendar" aria-hidden="true"></i> Year Established</label>
                <input type="number" id="year_established" name="year_established" class="form-control" placeholder="e.g. 2010" min="1800" max="2099">
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="goBack()">
              <i class="fas fa-arrow-left" aria-hidden="true"></i> Back
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save" aria-hidden="true"></i> Save Brand
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>

    </div><!-- /.brand-modal-body -->
  </div><!-- /.brand-modal-container -->
</div>

<script>
  // Close modal function
  function closeModal() {
    // Try to find the closest modal and close it
    var modal = document.getElementById('brandModalWrapper');
    if (modal) {
      // Check if it's inside a Bootstrap modal
      var bootstrapModal = modal.closest('.modal');
      if (bootstrapModal) {
        // Try Bootstrap modal hide
        if (typeof $(bootstrapModal).modal === 'function') {
          $(bootstrapModal).modal('hide');
        }
      }
      
      // Check if there's a parent modal container
      var modalContainer = modal.closest('.modal-container, .modal-content, .modal-dialog');
      if (modalContainer) {
        var parentModal = modalContainer.closest('.modal');
        if (parentModal && typeof $(parentModal).modal === 'function') {
          $(parentModal).modal('hide');
        }
      }
      
      // Fallback: try to close via the app's modal system
      if (window.parent && window.parent.closeModal && typeof window.parent.closeModal === 'function') {
        window.parent.closeModal();
      }
      
      // If it's in an iframe, try to tell parent to close
      if (window.parent && window.parent !== window) {
        try {
          // Try to find the modal in parent
          var parentModalEl = window.parent.document.querySelector('.modal.in, .modal.show');
          if (parentModalEl && typeof $(parentModalEl).modal === 'function') {
            $(parentModalEl).modal('hide');
          }
        } catch(e) {
          // Cross-origin or other error
        }
      }
      
      // Simple hide as last resort
      var container = modal.closest('.modal-dialog, .modal-content, .modal');
      if (container) {
        container.style.display = 'none';
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.style.display = 'none';
        }
      }
    }
    
    // Try to trigger the close button of the modal
    $('.modal .close, .modal [data-dismiss="modal"]').trigger('click');
  }

  function showForm(choice) {
    document.getElementById('choiceSection').style.display = 'none';
    if(choice === 'yes') {
      document.getElementById('brandYesForm').classList.remove('hidden');
    } else {
      document.getElementById('brandNoForm').classList.remove('hidden');
    }
  }

  function goBack() {
    document.getElementById('brandYesForm').classList.add('hidden');
    document.getElementById('brandNoForm').classList.add('hidden');
    document.getElementById('choiceSection').style.display = 'block';
  }

  function showImagePreview(input, previewId) {
    var preview = document.getElementById(previewId);
    var wrap = document.getElementById(previewId + '-wrap');
    var file = input.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onloadend = function () {
        preview.src = reader.result;
        if (wrap) wrap.style.display = 'inline-block';
        else { preview.style.display = 'block'; }
      };
      reader.readAsDataURL(file);
    } else {
      preview.src = '#';
      if (wrap) wrap.style.display = 'none';
      else preview.style.display = 'none';
    }
  }

  function sendOtp() {
    const email = document.getElementById('input_email').value.trim();
    const ip = document.getElementById('input_ip').value.trim();

    if (!email || !ip) {
      alert("Please enter email and IP address.");
      return;
    }

    const payload = { email: email, ip_address: ip };

    fetch("https://efactor.tech/call.php?action=send_otp", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      console.log("Send OTP Response:", data);
      if (data.status === 'success') {
        document.getElementById('step-email-ip').style.display = 'none';
        document.getElementById('otp-message').style.display = 'flex';
        document.getElementById('otp-box').style.display = 'block';
      } else {
        alert(data.description || 'OTP sending failed!');
      }
    })
    .catch(err => {
      console.error("Send OTP Error:", err);
      alert('Something went wrong while sending OTP.');
    });
  }

  function verifyOtp() {
    const otp = document.getElementById('input_otp').value.trim();
    const email = document.getElementById('input_email').value.trim();

    if (!otp || !email) {
      alert('Please enter OTP and Email.');
      return;
    }

    const payload = { email: email, verification_code: otp };

    fetch('https://efactor.tech/call.php?action=verify_otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      console.log("Verify OTP Response:", data);
      if (data.status === 'success') {
        alert("✅ OTP Verified!");
        document.getElementById('otp-box').style.display = 'none';
        document.getElementById('otp-message').style.display = 'none';
        document.getElementById('step-brand-details').style.display = 'block';
      } else {
        alert(data.description || '❌ Invalid OTP!');
      }
    })
    .catch(error => {
      console.error('Error verifying OTP:', error);
      alert('Verification failed!');
    });
  }
</script>