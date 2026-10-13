<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Brand</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    :root {
      --brand-primary: #0f766e;
      --brand-primary-2: #0ea5a4;
      --brand-primary-light: #14b8a6;
      --brand-primary-lighter: #99f6e4;
      --brand-primary-bg: #f0fdfa;
      --brand-primary-dark: #0d5e56;
      --brand-ink: #134e4a;
      --brand-muted: #64748b;
      --brand-line: #e2e8f0;
    }
    * { box-sizing: border-box; }
    body {
      background: transparent;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
      padding: 0;
      margin: 0;
      min-height: auto;
      color: var(--brand-ink);
      -webkit-font-smoothing: antialiased;
    }
    .brand-modal-container {
      max-width: 100%;
      margin: 0;
      background: #fff;
      border-radius: 0;
      box-shadow: none;
      overflow: hidden;
      animation: brandModalIn 0.35s ease;
    }
    @keyframes brandModalIn {
      from { opacity: 0; transform: translateY(12px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .brand-modal-header {
      background: linear-gradient(135deg, var(--brand-primary-dark) 0%, var(--brand-primary) 52%, var(--brand-primary-2) 100%);
      padding: 26px 28px;
      color: #fff;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .brand-modal-header::after {
      content: "";
      position: absolute;
      inset: auto -50px -70px auto;
      width: 180px;
      height: 180px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(153,246,228,.35) 0, transparent 68%);
      pointer-events: none;
    }
    .brand-modal-header h2 {
      font-size: 1.55rem;
      font-weight: 800;
      margin: 0 0 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      letter-spacing: -.02em;
      position: relative;
      z-index: 1;
    }
    .brand-modal-header h2 i {
      width: 42px;
      height: 42px;
      display: inline-grid;
      place-items: center;
      border-radius: 12px;
      background: rgba(255,255,255,.18);
      font-size: 1.1rem;
    }
    .brand-modal-header p {
      margin: 0;
      opacity: .92;
      font-size: .95rem;
      font-weight: 500;
      position: relative;
      z-index: 1;
    }
    .brand-modal-body { padding: 28px 28px 8px; }
    .hidden { display: none !important; }

    .choice-section { text-align: center; padding: 12px 0 8px; }
    .choice-section h3 {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--brand-ink);
      margin-bottom: 8px;
      letter-spacing: -.02em;
    }
    .choice-section .choice-hint {
      color: var(--brand-muted);
      margin-bottom: 24px;
      font-size: .95rem;
      font-weight: 500;
    }
    .choice-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      font-size: .95rem;
      font-weight: 700;
      border-radius: 12px;
      border: 1.5px solid transparent;
      cursor: pointer;
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
      margin: 0 6px;
      font-family: inherit;
      letter-spacing: .01em;
    }
    .choice-btn i { font-size: 1rem; }
    .choice-btn-yes {
      background: linear-gradient(135deg, var(--brand-primary-dark), var(--brand-primary));
      color: #fff;
      box-shadow: 0 8px 22px rgba(15,118,110,.28);
    }
    .choice-btn-yes:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(15,118,110,.34);
    }
    .choice-btn-no {
      background: #fff;
      color: var(--brand-primary);
      border-color: var(--brand-primary-lighter);
      box-shadow: 0 4px 14px rgba(15,118,110,.08);
    }
    .choice-btn-no:hover {
      transform: translateY(-2px);
      background: var(--brand-primary-bg);
      border-color: var(--brand-primary-light);
      box-shadow: 0 10px 24px rgba(15,118,110,.14);
    }

    .form-section-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--brand-ink);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      letter-spacing: -.01em;
    }
    .form-section-title i { color: var(--brand-primary); }
    .form-group label {
      font-weight: 600;
      color: #334155;
      font-size: .92rem;
      margin-bottom: 6px;
      display: block;
    }
    .form-group label i { color: var(--brand-primary); margin-right: 4px; }
    .form-control {
      border: 1.5px solid var(--brand-line);
      border-radius: 11px;
      padding: 11px 14px;
      font-size: .95rem;
      transition: border-color .2s, box-shadow .2s;
      font-family: inherit;
      color: var(--brand-ink);
    }
    .form-control:focus {
      border-color: var(--brand-primary);
      box-shadow: 0 0 0 4px rgba(15,118,110,.12);
      outline: none;
    }

    .step-indicator {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 13px 16px;
      background: linear-gradient(135deg, var(--brand-primary-bg), #ecfdf5);
      border-radius: 12px;
      margin-bottom: 18px;
      border-left: 4px solid var(--brand-primary);
    }
    .step-indicator i { font-size: 1.1rem; color: var(--brand-primary); }
    .step-indicator-text { font-weight: 600; color: #334155; font-size: .9rem; }

    #otp-message {
      display: none;
      padding: 13px 16px;
      background: linear-gradient(135deg, #ecfdf5, #d1fae5);
      border-radius: 11px;
      color: #065f46;
      font-weight: 600;
      margin: 14px 0;
      border-left: 4px solid var(--brand-primary-light);
      animation: brandModalIn .3s ease;
    }
    #otp-box { display: none; margin-top: 14px; }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 11px;
      font-weight: 700;
      font-size: .92rem;
      transition: transform .2s, box-shadow .2s, background .2s;
      border: none;
      cursor: pointer;
      font-family: inherit;
    }
    .btn-info {
      background: linear-gradient(135deg, var(--brand-primary), var(--brand-primary-2));
      color: #fff;
      box-shadow: 0 6px 18px rgba(15,118,110,.22);
    }
    .btn-info:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(15,118,110,.3);
    }
    .btn-success {
      background: linear-gradient(135deg, #059669, var(--brand-primary-light));
      color: #fff;
      box-shadow: 0 6px 18px rgba(5,150,105,.22);
    }
    .btn-success:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(5,150,105,.3);
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--brand-primary-dark), var(--brand-primary));
      color: #fff;
      box-shadow: 0 6px 18px rgba(15,118,110,.24);
    }
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 26px rgba(15,118,110,.32);
    }
    .btn-secondary {
      background: #f1f5f9;
      color: #475569;
      border: 1.5px solid #e2e8f0;
    }
    .btn-secondary:hover { background: #e2e8f0; }

    .image-preview-box {
      margin-top: 10px;
      border-radius: 12px;
      overflow: hidden;
      border: 1.5px solid var(--brand-primary-lighter);
      display: inline-block;
      max-width: 100%;
      background: var(--brand-primary-bg);
    }
    .image-preview-box img { display: block; max-height: 120px; max-width: 100%; }

    .collapsible-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 13px 16px;
      background: linear-gradient(135deg, #f8fafc, var(--brand-primary-bg));
      border-radius: 11px;
      cursor: pointer;
      transition: background .2s, border-color .2s;
      border: 1.5px solid var(--brand-line);
      margin-bottom: 10px;
      font-weight: 600;
      color: #334155;
    }
    .collapsible-header:hover {
      background: linear-gradient(135deg, var(--brand-primary-bg), #ecfdf5);
      border-color: var(--brand-primary-lighter);
    }
    .collapsible-header i { color: var(--brand-primary); font-size: 1rem; }
    .collapse .card { border: none; border-radius: 11px; background: #f8fafc; }
    .collapse .card-body { padding: 18px; }

    .modal-footer {
      padding: 18px 0 22px;
      background: transparent;
      border-top: 1.5px solid var(--brand-line);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      margin-top: 8px;
      flex-wrap: wrap;
    }
    .brand-modal-foot-actions {
      display: flex;
      gap: 10px;
      margin-left: auto;
      flex-wrap: wrap;
    }
    .brand-choice-foot {
      margin-top: 20px;
      text-align: center;
    }
    .btn-cancel {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: #f1f5f9;
      color: #475569;
      border: 1.5px solid #e2e8f0;
      border-radius: 11px;
      font-weight: 700;
      font-size: .93rem;
      cursor: pointer;
      font-family: inherit;
      transition: background .2s, border-color .2s;
    }
    .btn-cancel:hover {
      background: #e2e8f0;
      border-color: #cbd5e1;
    }

    @media (max-width: 640px) {
      .brand-modal-body { padding: 22px 18px 6px; }
      .brand-modal-header { padding: 22px 18px; }
      .brand-modal-header h2 { font-size: 1.3rem; }
      .choice-btn { padding: 12px 18px; font-size: .88rem; margin: 4px; width: 100%; justify-content: center; }
      .modal-footer { flex-direction: column-reverse; }
      .modal-footer .btn { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>

<div class="brand-modal-container">

  <!-- MODAL HEADER -->
  <div class="brand-modal-header">
    <h2><i class="fa-solid fa-tags" aria-hidden="true"></i> Add New Brand</h2>
    <p>Fill in the details to register your brand</p>
  </div>

  <div class="brand-modal-body">

    <!-- CHOICE SECTION -->
    <div id="choiceSection" class="choice-section">
      <h3>Does this brand have a server?</h3>
      <p class="choice-hint">Choose the option that best describes your brand setup</p>
      <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:16px;">
        <button type="button" class="choice-btn choice-btn-yes brand-choice-yes" aria-label="Yes, brand has a server">
          <i class="fa-solid fa-server" aria-hidden="true"></i> Yes, Has Server
        </button>
        <button type="button" class="choice-btn choice-btn-no brand-choice-no" aria-label="No, brand has no server">
          <i class="fa-solid fa-store" aria-hidden="true"></i> No Server
        </button>
      </div>
      <div class="brand-choice-foot">
        <button type="button" class="btn-cancel brand-modal-cancel" aria-label="Cancel adding brand">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancel
        </button>
      </div>
    </div>

    <!-- FORM FOR BRAND - YES (has server) -->
    <div id="items-dropzone" class="post-dropzone">
      <div id="brandYesForm" class="hidden">
        <div class="form-section-title">
          <i class="fa-solid fa-server" aria-hidden="true"></i> Brand With Server
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
              <label for="input_email"><i class="fas fa-envelope" aria-hidden="true"></i> Email Address</label>
              <input type="email" id="input_email" placeholder="Enter your email" class="form-control" required aria-required="true">
            </div>
            <div class="form-group">
              <label for="input_ip"><i class="fas fa-network-wired" aria-hidden="true"></i> IP Address</label>
              <input type="text" id="input_ip" placeholder="Enter IP address" class="form-control" required aria-required="true">
            </div>
            <button type="button" class="btn btn-info brand-send-otp">
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
              <label for="input_otp"><i class="fas fa-lock" aria-hidden="true"></i> One-Time Password</label>
              <input type="text" id="input_otp" placeholder="Enter OTP" class="form-control" aria-label="Enter OTP">
            </div>
            <button type="button" class="btn btn-success brand-verify-otp">
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
              <label for="name_yes"><i class="fas fa-tag" aria-hidden="true"></i> Brand Name <span style="color:#ef4444;">*</span></label>
              <input type="text" name="name" id="name_yes" class="form-control" placeholder="e.g. Apple, Samsung" required aria-required="true">
            </div>

            <div class="form-group">
              <label for="brandlogo_yes"><i class="fas fa-image" aria-hidden="true"></i> Brand Logo</label>
              <input type="file" class="form-control brand-image-input" id="brandlogo_yes" name="brandlogo" accept="image/*" data-preview="brandlogo-preview-yes" data-preview-wrap="brandlogo-preview-yes-wrap" aria-label="Upload brand logo">
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
            <div class="form-group">
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
            <div class="form-group">
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
            <div class="form-group">
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
            <button type="button" class="btn btn-secondary brand-modal-back">
              <i class="fas fa-arrow-left" aria-hidden="true"></i> Back
            </button>
            <div class="brand-modal-foot-actions">
              <button type="button" class="btn-cancel brand-modal-cancel">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancel
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save" aria-hidden="true"></i> Save Brand
              </button>
            </div>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <!-- FORM FOR BRAND - NO (no server) -->
    <div id="brandNoForm" class="hidden">
      <div class="form-section-title">
        <i class="fa-solid fa-store" aria-hidden="true"></i> Brand Without Server
      </div>
      <?php echo form_open_multipart(get_uri("items/savebrand"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
        <input type="hidden" name="brand_choice" value="No">

        <div class="form-group">
          <label for="name"><i class="fas fa-tag" aria-hidden="true"></i> Brand Name <span style="color:#ef4444;">*</span></label>
          <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Apple, Samsung" required aria-required="true">
        </div>

        <div class="form-group">
          <label for="brandlogo"><i class="fas fa-image" aria-hidden="true"></i> Brand Logo <span style="color:#94a3b8; font-weight:400;">(Optional)</span></label>
          <input type="file" class="form-control brand-image-input" id="brandlogo" name="brandlogo" accept="image/*" data-preview="brandlogo-preview" data-preview-wrap="brandlogo-preview-wrap" aria-label="Upload brand logo">
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
          <button type="button" class="btn btn-secondary brand-modal-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back
          </button>
          <div class="brand-modal-foot-actions">
            <button type="button" class="btn-cancel brand-modal-cancel">
              <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save" aria-hidden="true"></i> Save Brand
            </button>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>

  </div><!-- /.brand-modal-body -->
</div><!-- /.brand-modal-container -->


<script>
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
        document.getElementById('otp-message').style.display = 'block';
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

</body>
</html>