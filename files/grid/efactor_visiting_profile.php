<?php
// 1. Database Connection
$conn = new mysqli("localhost", "root", "Facteno@2025!", "factor_db2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Fetch the dynamic data for  (User 177)
$user_id = "177"; 
$sql = "SELECT first_name, last_name, profile_image FROM user_business_profiles WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 3. Create the variables
$real_name  = $row['first_name'] . ' ' . $row['last_name'];
$real_image = $row['profile_image'];

// 4. YOUR ORIGINAL ARRAY (Updated with Dynamic Name and Image)
$cardData = [
    'name'      => $real_name,  // UPDATED: Now dynamic
    'profile_image' => $real_image, // ADDED: Now dynamic
    'pronouns'  => 'she/her',
    'title'     => 'Product Lead · eFactor | Ex-Microsoft | Visiting card specialist',
    'badge'     => 'eFactor pro',
    'stats'     => ['followers' => '', 'connections' => '', 'eFactor' => ''],
    'efactor'   => 'eFactor premium · verified · visible via QR scan',
    'website'   => '',
    'posts'     => [
       
       
      
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $real_name ?>'s eFactor • LinkedIn style visiting card & feed (PHP dynamic)</title>
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        body {
            background: #f4f2ee;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 28px 20px;
        }

        /* main LinkedIn-like grid */
        .linkedin-wrapper {
            max-width: 1280px;
            width: 100%;
            display: grid;
            grid-template-columns: 280px 1fr 320px;
            gap: 24px;
        }

        /* ========== LEFT SIDEBAR ========== */
        .left-sidebar {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ----- ONLINE VISITING CARD (hyper personalised) ----- */
        .profile-visiting-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            overflow: hidden;
            border: 1px solid #e6e9ec;
            transition: all 0.2s;
        }

        .profile-banner {
            height: 85px;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #5f7fb0);
            position: relative;
        }

        .profile-pic-large {
            position: absolute;
            bottom: -36px;
            left: 22px;
            width: 82px;
            height: 82px;
            background: white;
            border-radius: 50%;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f5fd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .profile-pic-large i {
            font-size: 4.2rem;
            color: #1e3c72;
        }

        .profile-info-card {
            padding: 48px 22px 22px 22px;
        }

        .profile-info-card h2 {
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-info-card h2 i {
            font-size: 1.3rem;
            color: #0a66c2;
            background: #e3f0ff;
            padding: 4px;
            border-radius: 50%;
        }

        .profile-info-card .title {
            font-size: 1rem;
            color: #4d5b6b;
            margin-bottom: 10px;
            font-weight: 500;
            border-bottom: 1px solid #edf0f3;
            padding-bottom: 14px;
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .profile-stats-grid {
            display: flex;
            justify-content: space-between;
            background: #f9fcff;
            padding: 14px 12px;
            border-radius: 18px;
            margin: 10px 0 12px;
            border: 1px solid #dae6f5;
        }

        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-weight: 700;
            font-size: 1.2rem;
            color: #1e2b3a;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #66778b;
        }

        .qr-tag {
            background: #ecf3fa;
            padding: 14px 14px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 16px 0 12px;
            border: 1px dashed #1e3c72;
        }

        .qr-tag i {
            font-size: 1.7rem;
            color: #1e3c72;
        }

        .qr-tag .badge {
            background: white;
            border-radius: 60px;
            padding: 6px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e3c72;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .efactor-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #eef3fa;
            border-radius: 50px;
            padding: 12px 16px;
            margin-top: 14px;
            font-size: 0.95rem;
            border: 1px solid #b9d3f0;
        }

        .efactor-badge i {
            color: #1e3c72;
            font-size: 1.3rem;
        }

        .visiting-card-footer {
            font-size: 0.8rem;
            color: #476788;
            margin-top: 8px;
            display: flex;
            gap: 14px;
            border-top: 1px dashed #cbd6e4;
            padding-top: 14px;
        }

        /* left navigation */
        .side-menu-card {
            background: white;
            border-radius: 20px;
            padding: 10px 0;
            border: 1px solid #e6e9ec;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            color: #1f2b38;
            font-weight: 500;
            font-size: 0.95rem;
            transition: 0.1s;
            cursor: default;
        }

        .menu-item:hover {
            background: #f0f4f9;
        }

        .menu-item i {
            color: #4b637b;
            width: 24px;
            font-size: 1.2rem;
        }

        .menu-item.active {
            background: #e4f0fe;
            border-left: 4px solid #1e3c72;
            font-weight: 600;
        }

        /* ========== MAIN FEED (elaborate posts) ========== */
        .main-feed {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .compose-card {
            background: white;
            border-radius: 20px;
            padding: 16px 20px;
            border: 1px solid #e6e9ec;
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .compose-avatar {
            width: 52px;
            height: 52px;
            background: #d9e7f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3c72;
            font-size: 2.2rem;
        }

        .compose-input {
            flex: 1;
            background: #f4f7fa;
            border: 1px solid #cfdceb;
            border-radius: 40px;
            padding: 14px 22px;
            font-size: 1rem;
            color: #3c5a7a;
            cursor: default;
        }

        /* post cards */
        .post-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e6e9ec;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .post-header {
            display: flex;
            align-items: center;
            padding: 18px 22px 8px 22px;
        }

        .post-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #d6e4f2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #1e3c72;
            margin-right: 14px;
            flex-shrink: 0;
        }

        .post-author {
            flex: 1;
        }

        .post-author h4 {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .post-author .meta {
            font-size: 0.8rem;
            color: #6f7d8e;
            display: flex;
            gap: 14px;
            align-items: center;
            margin-top: 4px;
        }

        .permission-tag {
            background: #f2f8ff;
            border-radius: 40px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid #c0d2eb;
            color: #1a4972;
        }
        .permission-tag.restricted {
            background: #fff3e2;
            border-color: #e6b57e;
            color: #ac4f0e;
        }
        .permission-tag.efactor {
            background: #e3f3e9;
            border-color: #8fc9a8;
            color: #156b41;
        }

        .post-content {
            padding: 10px 22px 16px 22px;
            font-size: 1rem;
            line-height: 1.5;
            color: #1f2b38;
        }

        .post-stats {
            padding: 0 22px 10px 22px;
            font-size: 0.85rem;
            color: #647687;
            display: flex;
            gap: 24px;
            border-bottom: 1px solid #ecf0f4;
        }

        /* comment section */
        .comment-section {
            background: #fafcff;
            padding: 18px 22px 18px;
        }

        .comment-permission-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #edf5ff;
            padding: 10px 16px;
            border-radius: 30px;
            margin-bottom: 18px;
            font-size: 0.9rem;
            border: 1px solid #bfd6f2;
            color: #1a3f66;
        }

        .comment-thread {
            margin-bottom: 16px;
        }
        .single-comment {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .comment-avatar-small {
            width: 40px;
            height: 40px;
            background: #cddef5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #1e3c72;
        }
        .comment-bubble {
            background: white;
            padding: 12px 18px;
            border-radius: 24px;
            font-size: 0.95rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            flex: 1;
            border: 1px solid #e7edf5;
        }
        .comment-bubble strong {
            font-weight: 700;
            margin-right: 8px;
            color: #132b44;
        }
        .comment-bubble .meta {
            font-size: 0.7rem;
            color: #6b849e;
            margin-top: 6px;
        }

        .comment-input-area {
            display: flex;
            gap: 12px;
            align-items: center;
            background: white;
            border: 1px solid #cad9ec;
            border-radius: 40px;
            padding: 8px 18px;
            margin-top: 10px;
        }
        .comment-input-area input {
            border: none;
            flex: 1;
            outline: none;
            font-size: 0.95rem;
            background: transparent;
        }
        .comment-input-area i {
            color: #1e3c72;
            font-size: 1.2rem;
            cursor: default;
        }
        .comment-input-area.disabled {
            background: #f2f5f8;
            border-color: #bcc9da;
            opacity: 0.8;
        }

        .guest-note {
            font-size: 0.8rem;
            background: #f2f6fc;
            border-radius: 20px;
            padding: 10px 16px;
            margin-top: 12px;
        }

        /* ========== RIGHT SIDEBAR ========== */
        .right-sidebar {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .qr-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e6e9ec;
            padding: 24px 16px 18px;
            text-align: center;
        }

        .qr-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #14273e;
        }

        .qr-code-display {
            background: #f4f9ff;
            width: 160px;
            height: 160px;
            margin: 12px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 32px;
            border: 3px solid white;
            box-shadow: 0 10px 18px rgba(0,50,80,0.1);
        }

        .qr-code-display i {
            font-size: 7rem;
            color: #1e3c72;
        }

        .qr-footer {
            display: flex;
            justify-content: center;
            gap: 16px;
            background: #f2f7fd;
            border-radius: 50px;
            padding: 12px;
            font-size: 0.9rem;
        }

        .efactor-online {
            background: white;
            border-radius: 24px;
            padding: 20px 18px;
            border: 1px solid #e6e9ec;
        }

        .efactor-online h4 {
            font-size: 1.1rem;
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .online-person {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }
        .online-person i {
            font-size: 2rem;
            color: #1e4d7c;
        }
        .green-dot {
            width: 10px;
            height: 10px;
            background: #31a24c;
            border-radius: 50%;
            margin-left: auto;
        }

        .permission-legend {
            background: white;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid #e6e9ec;
        }
        .legend-item {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        hr {
            border: 0.5px solid #e1e9f2;
            margin: 12px 0;
        }

        /* custom badges */
        .name-detail {
            display: flex;
            flex-direction: column;
        }
        .badge-premium {
            background: #f1c40f;
            color: #1e2f4a;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 0.6rem;
            font-weight: 600;
            margin-left: 8px;
        }




/* --- 1. GLOBAL BASE STYLES (Applies to everything) --- */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #f4f2ee; overflow-x: hidden; }

/* --- 2. DESKTOP GRID (Your original design) --- */
.linkedin-wrapper {
    display: grid;
    grid-template-columns: 280px 1fr 320px;
    gap: 24px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 20px;
}

/* --- 3. MEDIUM SCREEN ADJUSTMENT (Laptops/Small Desktop) --- */
@media (max-width: 1200px) {
    .linkedin-wrapper { grid-template-columns: 240px 1fr 280px; gap: 15px; }
}

/* --- 4. MOBILE & TABLET STACKING (The "Fix Everything" Block) --- */
@media (max-width: 992px) {
    /* Stack layout */
    .linkedin-wrapper {
        display: block !important; 
        padding: 10px;
    }

    /* Reset columns so they don't force height */
    .left-sidebar, .main-feed, .right-sidebar {
        height: auto !important;
        overflow-y: visible !important;
        width: 100% !important;
        margin-bottom: 24px !important; /* Space between columns */
    }

    /* Ensure all cards are flexible */
    .profile-visiting-card, .post-card, .compose-container, .qr-card, .side-menu-card {
        width: 100% !important;
        margin-bottom: 15px !important;
    }

    /* Shrink all images to prevent horizontal cut-off */
    img {
        max-width: 100% !important;
        height: auto !important;
    }

    /* Modal full-screen for mobile */
    #visitingCardModal > div {
        width: 95% !important;
        height: 90% !important;
        margin: 5% auto !important;
    }
}




    </style>
</head>
<body>


    <div class="linkedin-wrapper">
        <!-- LEFT COLUMN: Sudha's visiting card & nav (dynamic from PHP) -->
        <div class="left-sidebar">

<img src="grid2.jpeg" alt="GRID" style="
    width: 100%;       /* Allows it to fill its container */
    max-width: 500px;  /* INCREASED from 380px to make it wider */
    height: auto; 
    max-height: 85px; /* Adjusted height slightly to maintain proportions */
    display: block; 
    margin: 0 auto; 
    object-fit: contain;
    border-radius : 20px;
">

            <!-- ONLINE VISITING CARD for SUDHA (detailed) -->
            <div class="profile-visiting-card">
                <div class="profile-banner"><div class="profile-pic-large" style="background-image: url('<?= htmlspecialchars($cardData['profile_image']) ?>'); background-size: cover; background-position: center;">
    <?php if(empty($cardData['profile_image'])): ?>
        <i class="fas fa-user"></i> <?php endif; ?>
</div>
                </div>


                <div class="profile-info-card">
                    <h2>
            <span contenteditable="true" 
        onblur="saveField('name', this.innerText)" 
         style="border-bottom:  cursor: text;">
        <?= htmlspecialchars($cardData['name']) ?>
             </span> 
         <span contenteditable="true" 
       onblur="saveField('pronouns', this.innerText)" 
    style="font-weight:400; font-size:1rem;  cursor: text;">
         (<?= htmlspecialchars($cardData['pronouns']) ?>)
        </span> 
 <i class="fas fa-check-circle" style="color:#0a66c2;"></i>
         </h2>

    

  <div id="sidebarExperienceDisplay" style="margin: 15px 0;  padding-top: 5px;">
        <div id="latestExpContent">
            </div>
    </div>


<div class="exp-summary-trigger" 

     onclick="openExperienceModal()".style.display='block'" 

     style="cursor:pointer; padding:8px; margin-top:10px; border:1px dashed #0a66c2; border-radius:6px; background:#f9fbff; font-size: 0.85rem;">

    <i class="fas fa-plus-circle"></i> Add/Edit Experience

</div>




                    <!-- detailed stats -->
                    <div class="profile-stats-grid">
                        <div class="stat-item"><span class="stat-number"><?= $cardData['stats']['followers'] ?></span> <span class="stat-label">followers</span></div>
                        <div class="stat-item"><span class="stat-number"><?= $cardData['stats']['connections'] ?></span> <span class="stat-label">connections</span></div>
                        <div class="stat-item"><span class="stat-number"><?= $cardData['stats']['eFactor'] ?></span> <span class="stat-label">eFactor</span></div>
                    </div>

                    <!-- QR / visiting card highlight -->
                    <div class="qr-tag">
                        <i class="fas fa-qrcode"></i>
                        <span class="badge"><i class="far fa-address-card"></i> <?= $real_name ?>'s eFactor card</span>
                        <i class="fas fa-share-alt" style="color:#3c647e;"></i>
                    </div>

                    <!-- eFactor details -->
                    <div class="efactor-badge">
                        <i class="fas fa-id-card"></i>
                        <span><strong><?= htmlspecialchars($cardData['efactor']) ?></strong></span>
                    </div>

                    <!-- extra visiting card features -->
                    <div class="visiting-card-footer">
                        <span><i class="fas fa-globe"></i> <?= htmlspecialchars($cardData['website']) ?></span>
                        <span><i class="fas fa-qrcode"></i> scan to connect</span>
                    </div>
                </div>
            </div>

            <!-- navigation -->
            <div class="side-menu-card">
                <div class="menu-item active"><i class="fas fa-home"></i> Home</div>
               <div class="menu-item" onclick="openVisitingCardModal()" style="cursor:pointer;">
    <i class="fas fa-id-card"></i> My visiting card
</div>


<div id="visitingCardModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8);">
    <div style="width:1300px; height:750px; background:white; margin:30px auto; border-radius:12px; position:relative; overflow:hidden; padding:30px;">
        <span onclick="document.getElementById('visitingCardModal').style.display='none'" style="position:absolute; right:25px; top:15px; cursor:pointer; font-size:30px;">&times;</span>
        <h3>My Visiting Cards</h3>
        
        <div id="modal-card-container" style="
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 40px; 
            padding: 20px;
            overflow-y: auto; 
            max-height: 650px;">
        </div>
    </div>
</div>


                <div class="menu-item"><i class="fas fa-user-friends"></i> eFactor network</div>
                <div class="menu-item"><i class="fas fa-camera"></i> Scan QR</div>
                <div class="menu-item"><i class="fas fa-shield-alt"></i> Privacy & permissions</div>
            </div>
        </div>

        <!-- MAIN FEED: posts with elaborate comment permissions (dynamically generated) -->
        <div class="main-feed">
            <!-- compose (static) -->
            <div class="compose-card">
                <div class="compose-avatar" style="background-image: url('<?= $real_image ?>'); background-size: cover; background-position: center;">
    <?php if(empty($real_image)): ?><i class="fas fa-user"></i><?php endif; ?>
</div>
                <div class="compose-input">What would you like to share,<?= $row['first_name'] ?>? (demo permissions below)</div>
            </div>



<div class="compose-container" style="background: #ffffff; border: 1px solid #e1e4e8; padding: 20px; border-radius: 12px; margin-bottom: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
    <div id="preview-area" style="margin-bottom: 20px;"></div>
    
    <textarea id="postContent" placeholder="What would you like to share, <?= htmlspecialchars($row['first_name']) ?>?" style="width: 100%; min-height: 80px; border: 1px solid #ddd; border-radius: 8px; padding: 12px; font-size: 15px; outline: none; resize: vertical; margin-bottom: 15px; font-family: inherit;"></textarea>

    <div class="compose-actions" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f2f5; padding-top: 15px;">
        <div class="media-buttons">
           <button type="button" onclick="openFolder()" style="background: #f0f2f5; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-weight: 500; color: #444;">📷 Imag</button>
           <input type="file" id="imgUpload" hidden accept="image/*" onchange="handleFileSelect(event)">
            
           
            <input type="file" id="vidUpload" hidden accept="video/*" multiple onchange="previewFiles(event)">
        </div>

        <div class="post-controls">
            <button type="button" onclick="cancelPost()" style="background: transparent; border: none; padding: 8px 16px; cursor: pointer; color: #65676b; font-weight: 600;">Cancel</button>
            <button type="button" onclick="submitPost()" style="background: #007bff; border: none; color: white; padding: 8px 24px; border-radius: 20px; cursor: pointer; font-weight: bold; transition: background 0.2s;">Post</button>
        </div>
    </div>
</div>

<div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 12px; width: 450px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 15px;">Crop Your Image</h3>
        
        <div style="width: 100%; height: 300px; background: #eee; overflow: hidden; border-radius: 8px; margin-bottom: 20px;">
            <img id="modalPreview" src="" style="display: block; max-width: 100%;">
        </div>

        <div style="display: flex; justify-content: space-between; gap: 10px;">
            <button onclick="closeModal()" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd; cursor: pointer; background: #f5f5f5;">Cancel</button>
            <button onclick="confirmModalPost()" style="flex: 1; padding: 10px; border-radius: 8px; border: none; background: #007bff; color: white; font-weight: bold; cursor: pointer;">Crop & Import</button>
        </div>
    </div>
</div>





<div id="main-feed" style="max-width: 100%; margin: 0 auto;"></div>
            <?php foreach ($cardData['posts'] as $post): ?>
            <div class="post-card">
                <div class="post-header">
                   <div class="post-avatar" style="background-image: url('<?= $real_image ?>'); background-size: cover; background-position: center;">
    <?php if(empty($real_image)): ?><i class="fas fa-user"></i><?php endif; ?>
</div>
<div class="post-author">
    <h4><?= $real_name ?> <span class="badge-premium"><?= htmlspecialchars($cardData['badge']) ?></span></h4>
                        <div class="meta">
                            <span><?= $post['time'] ?></span>
                            <?php if ($post['privacy'] == 'public'): ?>
                                <span class="permission-tag"><i class="fas fa-globe"></i> public · comments open</span>
                            <?php elseif ($post['privacy'] == 'restricted'): ?>
                                <span class="permission-tag restricted"><i class="fas fa-user-lock"></i> eFactor & connections only</span>
                            <?php elseif ($post['privacy'] == 'qr_visitor'): ?>
                                <span class="permission-tag efactor"><i class="fas fa-eye"></i> visitor mode (read + limited)</span>
                            <?php elseif ($post['privacy'] == 'efactor_only'): ?>
                                <span class="permission-tag efactor"><i class="fas fa-id-badge"></i> eFactor members can ask questions</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="post-content">
                    <p><?= $post['content'] ?></p>
                </div>
                <?php if (!empty($post['likes']) || !empty($post['comments'])): ?>
                <div class="post-stats">
                    <?php if (!empty($post['likes'])): ?><span><i class="fas fa-heart" style="color:#d51067;"></i> <?= $post['likes'] ?> likes</span><?php endif; ?>
                    <?php if (!empty($post['comments'])): ?><span><i class="fas fa-comment"></i> <?= $post['comments'] ?> comments</span><?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- comment section -->
                <div class="comment-section">
                    <div class="comment-permission-banner">
                        <?= $post['permission_banner'] ?>
                    </div>
                    <div class="comment-thread">
                        <?php foreach ($post['comments_list'] as $comment): ?>
                        <div class="single-comment">
                            <div class="comment-avatar-small"><i class="fas fa-user"></i></div>
                            <div class="comment-bubble">
                                <strong><?= htmlspecialchars($comment['name']) ?></strong> <?= htmlspecialchars($comment['text']) ?>
                                <?php if (!empty($comment['badge'])): ?><span class="meta">· <?= htmlspecialchars($comment['badge']) ?></span><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (isset($post['input_disabled']) && $post['input_disabled']): ?>
                    <div class="comment-input-area disabled">
                        <i class="fas fa-lock" style="color:#919baa;"></i>
                        <input type="text" placeholder="<?= htmlspecialchars($post['comment_placeholder']) ?>" disabled>
                        <i class="fas fa-ban" style="color:#a6b3c5;"></i>
                    </div>
                    <?php else: ?>
                    <div class="comment-input-area">
                        <i class="fas fa-smile"></i>
                        <input type="text" placeholder="<?= htmlspecialchars($post['comment_placeholder']) ?>" value="">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($post['guest_note'])): ?>
                    <div class="guest-note">
                        <?= $post['guest_note'] ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT SIDEBAR: QR & eFactor details -->
        <div class="right-sidebar">
            <!-- QR visiting card block (elaborate) -->
            <div class="qr-card">
                <h3><i class="fas fa-id-card" style="color:#1e3c72;"></i> <?= $real_name ?>'s visiting card</h3>
                <div class="qr-code-display">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="qr-footer">
                    <span><i class="fas fa-download"></i> save as PNG</span>
                    <span><i class="fas fa-share"></i> share</span>
                </div>
                <p style="margin-top:12px; font-size:0.9rem;"><i class="fas fa-camera"></i> Scan this QR to see <?= $real_name ?>'s profile with permission-based comments.</p>
            </div>

            <!-- eFactor online members -->
            <div class="efactor-online">
                <h4><i class="fas fa-users" style="color:#1e3c72;"></i> eFactor users online</h4>
                <div class="online-person">
                    <i class="fas fa-user-circle"></i> <span><strong>Priya</strong> · product</span> <span class="green-dot"></span>
                </div>
                <div class="online-person">
                    <i class="fas fa-user-circle"></i> <span><strong>Rahul Sharma</strong> · dev</span> <span class="green-dot"></span>
                </div>
                <div class="online-person">
                    <i class="fas fa-user-circle"></i> <span><strong>Anjali</strong> · designer</span> <span class="green-dot"></span>
                </div>
                <div class="online-person">
                    <i class="fas fa-user-circle"></i> <span><strong>Karthik</strong> · eFactor lead</span>
                </div>
                <hr>
                <div style="font-size:0.85rem; background:#f2f8ff; padding:8px; border-radius:30px; text-align:center;">
                    <i class="fas fa-id-card"></i> 5 of your connections use eFactor visiting cards
                </div>
            </div>

            <!-- Permissions legend (detailed) -->
            <div class="permission-legend">
                <h5 style="margin-bottom:12px;"><i class="fas fa-key"></i> permission levels used</h5>
                <div class="legend-item"><i class="fas fa-globe" style="width:24px;"></i> Public (anyone can comment)</div>
                <div class="legend-item"><i class="fas fa-user-lock" style="width:24px;"></i> eFactor + connections only</div>
                <div class="legend-item"><i class="fas fa-eye" style="width:24px;"></i> Guest / QR scan: read-only</div>
                <div class="legend-item"><i class="fas fa-id-badge" style="width:24px;"></i> eFactor members can reply</div>
                <hr>
                <p style="font-size:0.85rem;"><i class="fas fa-qrcode"></i> <?= $real_name ?>’s visiting card shows different posts based on your permission (eFactor / guest).</p>
            </div>
        </div>
    </div>

    <!-- subtle footer hint -->
    <div style="position: fixed; bottom: 10px; left: 20px; background: white; border-radius: 40px; padding: 8px 20px; font-size:0.8rem; box-shadow:0 2px 10px rgba(0,0,0,0.05); border:1px solid #ccc;">
        <i class="fas fa-id-card" style="color:#1e3c72;"></i> <strong><?= $real_name ?>'s eFactor</strong> — online visiting card & permission demo (PHP dynamic)
    </div>



<div id="chat-trigger" style="position: fixed; bottom: 20px; right: 20px; background: #007bff; color: white; padding: 15px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    <i class="fas fa-comment-dots" style="font-size: 24px;"></i>
</div>

<div id="chat-modal" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 320px; height: 400px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 1000; flex-direction: column;">
    <div style="background: #1e3c72; color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between;">


        <span>Messages</span>
        <i class="fas fa-times" onclick="toggleChat()" style="cursor: pointer;"></i>
    </div>
    
    <div id="message-container" style="flex: 1; padding: 10px; overflow-y: auto; background: #f9f9f9;">
        <div id="empty-state" style="text-align: center; color: #666; margin-top: 50px;">
            <p>No conversations yet</p>
        </div>
    </div>

    <div style="padding: 10px; border-top: 1px solid #ddd; display: flex; align-items: center; gap: 8px;">
        <input type="text" id="chat-input" placeholder="Type a message..." style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 20px; outline: none;">
        <i class="fas fa-paper-plane" onclick="sendMessage()" style="color: #1e3c72; cursor: pointer; font-size: 18px;"></i>
    </div>
</div>


<div id="expModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.7); backdrop-filter:blur(8px); transition: all 0.3s ease;">
    <div style="width:1000px; height:700px; background:#ffffff; margin:20px auto; padding:40px; border-radius:24px; position:relative; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); box-sizing: border-box; display: flex; flex-direction: column;">
        
        <span onclick="document.getElementById('expModal').style.display='none'" style="position:absolute; right:25px; top:20px; cursor:pointer; font-size:24px; color:#64748b; transition:0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='#64748b'">&times;</span>

        <div id="listView" style="display: flex; flex-direction: column; height: 100%;">
            <h2 style="margin-top:0; font-size: 1.5rem; color: #1e293b; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 25px;">Experience & Education</h2>
            
            <div style="display: flex; gap: 40px; flex-grow: 1; overflow: hidden;">
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <h4 style="margin:0 0 20px 0; color: #0a66c2; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 10px;">
                        <span style="background: #e0edff; padding: 8px; border-radius: 8px;"><i class="fas fa-briefcase"></i></span> Work History
                    </h4>
                    <div id="workResultList" style="overflow-y:auto; flex-grow: 1; padding-right:5px;"></div>
                </div>

                <div style="width: 1px; background: linear-gradient(to bottom, transparent, #e2e8f0, transparent);"></div>

                <div style="flex: 1; display: flex; flex-direction: column;">
                    <h4 style="margin:0 0 20px 0; color: #c26a0a; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 10px;">
                        <span style="background: #fff4e5; padding: 8px; border-radius: 8px;"><i class="fas fa-graduation-cap"></i></span> Education
                    </h4>
                    <div id="educationResultList" style="overflow-y:auto; flex-grow: 1; padding-left:5px;"></div>
                </div>
            </div>

            <button onclick="showFormView()" style="margin-top:30px; width:100%; padding:16px; background:#0a66c2; color:white; border:none; border-radius:12px; font-weight:700; font-size:1rem; cursor:pointer; box-shadow: 0 4px 6px -1px rgba(10, 102, 194, 0.2); transition: 0.2s;">
                + Add New Experience
            </button>
        </div>

       <div id="formView" style="display:none; flex-direction: column; height: 100%; overflow: hidden;">
    <h2 style="margin: 0 0 15px 0; font-size: 1.1rem; color: #1e293b; flex-shrink: 0;">Add to your Profile</h2>
    
    <div style="overflow-y: auto; height: 500px; padding-right: 15px; margin-bottom: 10px;">
        
        <div style="background: #f0f7ff; padding: 15px; border-radius: 10px; margin-bottom: 15px; border: 1px solid #e0edff;">
            <h4 style="margin:0 0 10px 0; color: #0a66c2;"><i class="fas fa-briefcase"></i> Work Experience</h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <input type="text" id="expCompany" placeholder="Company Name *" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <input type="text" id="expTitle" placeholder="Job Title *" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
               <select id="expType" style="padding:10px; border:1px solid #ccc; border-radius:6px; background: white;">
    <option value="">Employment Type *</option>
    <option value="full-time">Full-time</option>
    <option value="part-time">Part-time</option>
    <option value="self-employed">Self-employed</option>
    <option value="freelance">Freelance</option>
    <option value="internship">Internship</option>
    <option value="trainee">Trainee</option>
</select>
                <input type="text" id="expLocation" placeholder="Location *" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <div style="display:flex; flex-direction:column; gap:2px;">
                    <label style="font-size:0.65rem; color:#666; font-weight:bold;">START DATE *</label>
                    <input type="date" id="expStart" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div style="display:flex; flex-direction:column; gap:2px;">
                    <label style="font-size:0.65rem; color:#666; font-weight:bold;">END DATE *</label>
                    <input type="date" id="expEnd" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div id="expExtra" style="display:none; grid-column:span 2; margin-top:5px;">
                    <textarea id="expDescription" placeholder="Work Description (Optional)" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; font-family:inherit; height:80px; box-sizing:border-box;"></textarea>
                </div>
                <span onclick="toggleExtra('expExtra')" style="color:#0a66c2; font-size:0.8rem; cursor:pointer; grid-column:span 2;">+ Show more fields</span>
            </div>
        </div>

        <div style="background: #fff9f0; padding: 15px; border-radius: 10px; border: 1px solid #fff4e5; margin-bottom: 5px;">
            <h4 style="margin:0 0 10px 0; color: #c26a0a;"><i class="fas fa-graduation-cap"></i> Education</h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <input type="text" id="eduSchool" placeholder="School/University *" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <input type="text" id="eduDegree" placeholder="Degree *" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <input type="text" id="eduField" placeholder="Field of Study " style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <input type="text" id="eduGrade" placeholder="Grade / GPA " style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                <div style="display:flex; flex-direction:column; gap:2px;">
                    <label style="font-size:0.65rem; color:#666; font-weight:bold;">START DATE *</label>
                    <input type="date" id="eduStart" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div style="display:flex; flex-direction:column; gap:2px;">
                    <label style="font-size:0.65rem; color:#666; font-weight:bold;">END DATE *</label>
                    <input type="date" id="eduEnd" style="padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div id="eduExtra" style="display:none; grid-column:span 2; margin-top:5px;">
                    <textarea id="eduDescription" placeholder="Education Description (Optional)" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; font-family:inherit; height:80px; box-sizing:border-box;"></textarea>
                </div>
                <span onclick="toggleExtra('eduExtra')" style="color:#c26a0a; font-size:0.8rem; cursor:pointer; grid-column:span 2;">+ Show more fields</span>
            </div>
        </div>
    </div> 
    <div style="display:flex; gap:10px; background: white; padding-top: 15px; border-top: 1px solid #eee; flex-shrink: 0; margin-top: auto;">
        <button onclick="saveDualHistory()" style="flex:2; padding:14px; background:#0a66c2; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">Save All</button>
        <button onclick="showListView()" style="flex:1; padding:14px; background:#eee; border-radius:8px; border:none; cursor:pointer;">Cancel</button>
    </div>
</div>

    </div>
</div>
</body>
</html>


<script>
// This looks at the browser URL (e.g., profile.php?id=471)
const urlParams = new URLSearchParams(window.location.search);
const currentUserId = urlParams.get('id') || "<?= $user_id ?>";

let cropper; // This will hold the "scissors" tool
let croppedBlob = null; // This will hold the "cut" photo
let selectedFilesArray = []; // This stores ALL images
let tempFileData = null;     // This holds the image currently in the modal



function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


/*

function openFolder() {
    document.getElementById('imgUpload').click();
} */



function openFolder() {
    document.getElementById('imgUpload').click();
}


// 1. Updated handleFileSelect: Shows the FULL image immediately
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('modalPreview');
            img.src = e.target.result;
            img.style.display = 'block';
            document.getElementById('imageModal').style.display = 'flex';

            if (cropper) cropper.destroy(); 
            
            cropper = new Cropper(img, {
                aspectRatio: 1,      // Keeps it square
                viewMode: 1,         // Restricts crop box to within the image
                autoCropArea: 1,     // Sets crop box to 100% of the image initially
                dragMode: 'move',    // Allows you to move the image
                responsive: true,
                restore: false,
            });
        };
        reader.readAsDataURL(file);
    }
}

// 2. Updated confirmModalPost: Resizes specifically to 500x500
function confirmModalPost() {
    if (!cropper) return;

    // Fixed size at 500x500 for better performance and consistency
    const canvas = cropper.getCroppedCanvas({
        width: 500,  
        height: 500
    });
    
    canvas.toBlob((blob) => {
        // Create the file object for your selectedFilesArray
        const croppedFile = new File([blob], "post_image.jpg", { type: "image/jpeg" });

        selectedFilesArray.push(croppedFile);

        // Update your UI preview area
        const previewArea = document.getElementById('preview-area');
        previewArea.style.display = "flex";
        previewArea.style.flexWrap = "wrap";
        previewArea.style.gap = "10px";

        const wrapper = document.createElement('div');
        wrapper.style.cssText = "position: relative; width: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 8px; background: #fff;";
        
        const imageUrl = URL.createObjectURL(blob);
        wrapper.innerHTML = `
            <img src="${imageUrl}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 4px;">
            <button onclick="this.parentElement.remove(); removeFileFromArray(this.parentElement.getAttribute('data-index'))" 
                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 12px;">&times;</button>
        `;
        wrapper.setAttribute('data-index', selectedFilesArray.length - 1);
        previewArea.appendChild(wrapper);

        closeModal();
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }, 'image/jpeg', 0.8); // 0.8 quality provides a great balance for a 500px image
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('imgUpload').value = ""; 
}


function removeFileFromArray(index) {
    selectedFilesArray.splice(index, 1);
    // Refresh preview indices
    const wrappers = document.querySelectorAll('#preview-area > div');
    wrappers.forEach((wrapper, i) => {
        wrapper.setAttribute('data-index', i);
    });
}






/*
function submitPost() {
    const content = document.getElementById('postContent').value;
    
    // 1. Validation: Don't post if both text and images are empty
    if (!content.trim() && selectedFilesArray.length === 0) {
        alert("Please add some text or an image to your post.");
        return;
    }

    // 2. Prepare the FormData
    const formData = new FormData();
    formData.append('action', 'save_post');
    formData.append('content', content);
    formData.append('id', currentUserId);

    // 3. Convert the array of images into a JSON string
    // This sends the entire list of images side-by-side to the backend
    formData.append('attached_images', JSON.stringify(selectedFilesArray)); 

    // 4. Call your PHP bridge (process_data.php)
    fetch('process_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // 5. Handle the response
        if (data.status === 'success') {
            // Clear the composer and reload to show the new post
            cancelPost();
            location.reload();
        } else {
            alert("Database Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error submitting post:", error);
        alert("Something went wrong. Please try again.");
    });
}*/


// Update your submitPost function:
function submitPost() {
    const content = document.getElementById('postContent').value;
    
    if (!content.trim() && selectedFilesArray.length === 0) {
        alert("Please add some text or an image to your post.");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'save_post');
    formData.append('content', content);
    formData.append('id', currentUserId);
    
    // Append each file individually
    selectedFilesArray.forEach((file, index) => {
        formData.append(`images[]`, file);
    });

    fetch('process_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            cancelPost();
            location.reload();
        } else {
            alert("Database Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error submitting post:", error);
        alert("Something went wrong. Please try again.");
    });
}







// All your original UI code preserved exactly as you had it
function renderFullPostCard(content, image, postId, likeCount, hasLiked, comments =[], timestamp) {
    const feed = document.getElementById('main-feed');
    const card = document.createElement('div');
    card.className = 'post-card'; 
    card.setAttribute('data-post-id', postId);
    card.style.cssText = `
        width: 675px;
        margin: 0 auto 20px auto;
        background: white;
        border-radius: 8px;
        border: 1px solid #e1e4e8;
        position: relative; /* Added this to help with positioning */
    `;

    const isLiked = (hasLiked == 1);
    const iconClass = isLiked ? 'fas fa-heart' : 'far fa-heart';
    const iconColor = isLiked ? '#e74c3c' : '#666'; 
    const pointerEvents = isLiked ? 'none' : 'auto'; 

    let commentsHTML = '';
    comments.forEach(comment => {
        commentsHTML += `
            <div style="margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #cddef5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-user" style="color: #1e3c72; font-size: 18px;"></i>
                </div>
                <div style="background: white; padding: 10px 14px; border-radius: 18px; border: 1px solid #e1e4e8; flex-grow: 1;">
                    <div style="font-size: 15px; color: black;">${comment.comment_text}</div>
                </div>
            </div>`;
    });

    card.innerHTML = `
        <div class="post-header" style="padding: 15px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <div class="post-avatar" style="width: 45px; height: 45px; border-radius: 50%; background-image: url('<?= $real_image ?>'); background-size: cover; background-position: center; margin-right: 12px;">
                    <?php if(empty($real_image)): ?><i class="fas fa-user"></i><?php endif; ?>
                </div>
                <div class="post-author">
                    <h4 style="margin: 0; font-size: 15px;"><?= $real_name ?> <span class="badge-premium" style="font-size: 11px; background: #f3f3f3; padding: 2px 5px; border-radius: 4px;">eFactor pro</span></h4>
                    <div class="meta" style="font-size: 12px; color: #666;">
                        <span class="time-ago" data-time="${timestamp}"></span>
                    </div>
                </div>
            </div>
           <div style="position: relative; margin-left: auto;">
                <div onclick="const menu = this.nextElementSibling; menu.style.display = menu.style.display === 'none' ? 'block' : 'none';" 
                     style="cursor: pointer; padding: 8px; color: #666; font-size: 18px; line-height: 1;">
                    <i class="fas fa-ellipsis-v"></i>
                </div>

                <div style="
                    display: none; 
                    position: absolute; 
                    right: 0; 
                    top: 35px; 
                    background: white; 
                    border: 1px solid #e1e4e8; 
                    border-radius: 8px; 
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
                    z-index: 1000; 
                    min-width: 140px;
                    overflow: hidden;">
                    
                    <div onclick="deletePost(${postId})" 
                         style="padding: 12px 15px; color: #ff4d4d; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: background 0.2s;"
                         onmouseover="this.style.background='#fff5f5'" 
                         onmouseout="this.style.background='white'">
                        <i class="fas fa-trash-alt"></i> 
                        <span style="font-weight: 500;">Delete Post</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="post-content" style="padding: 0 15px 15px 15px;">
            <p style="margin: 0 0 10px 0;">${content.replace(/\n/g, '<br>')}</p>

            <div class="post-stats" style="padding: 10px 15px; border-bottom: 1px solid #e1e4e8; display: flex; align-items: center; gap: 20px;">
                <span class="like-btn" onclick="toggleLike(this, ${postId})" style="cursor: pointer; font-size: 14px; color: #666; display: flex; align-items: center; gap: 5px; pointer-events: ${pointerEvents};">
                    <i class="${iconClass}" style="color: ${iconColor};"></i> 
                    <span class="like-count" style="color: #000;">${likeCount || 0}</span> likes
                </span>
                <span class="comment-trigger" onclick="focusComment(this)" style="cursor: pointer; font-size: 14px; color: #666; display: flex; align-items: center; gap: 5px;">
                    <i class="far fa-comment"></i> 
                    <span class="comment-count">${comments ? comments.length : 0}</span> comments
                </span>
            </div>

         

    ${(() => {
        // 1. If there is no image data, return nothing
        if (!image || image === "[]" || image === '""') return '';

        try {
            // 2. Turn the string from the database into a real list (Array)
            let imagesArray = [];
            if (typeof image === 'string' && image.startsWith('[')) {
                imagesArray = JSON.parse(image);
            } else {
                imagesArray = [image]; // Handle older single images
            }

            // 3. Create the HTML for the images
            if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                // If there is more than 1 image, make a grid
                if (imagesArray.length > 1) {
                    let gridHtml = '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 12px; width: 100%;">';
                    imagesArray.forEach(imgSrc => {
                        gridHtml += `
                            <div style="width: 100%; border-radius: 8px; overflow: hidden; border: 1px solid #eee; aspect-ratio: 1/1;">
                                <img src="${imgSrc}" style="width: 100%; height: 100%; object-fit: cover; display: block;" onclick="window.open(this.src, '_blank')">
                            </div>`;
                    });
                    gridHtml += '</div>';
                    return gridHtml;
                } 
                
                // If there is only 1 image, show it full width
                return `
                    <div style="width: 100%; margin-top: 12px; border-radius: 8px; overflow: hidden; border: 1px solid #eee;">
                        <img src="${imagesArray[0]}" style="width: 100%; height: auto; display: block;" onclick="window.open(this.src, '_blank')">
                    </div>`;
            }
        } catch (e) {
            console.error("Image parsing error:", e);
            return `<img src="${image}" style="width: 100%; border-radius: 8px; margin-top: 12px;">`;
        }
        return '';
    })()}
</div>
        </div>

        <div class="comment-section" style="padding: 15px; border-top: 1px solid #e1e4e8;">
            <div class="comment-thread" style="margin-bottom: 10px; max-height: 200px; overflow-y: auto; padding-right: 5px;">${commentsHTML}</div>
            <div class="comment-input-area" style="position: relative; display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 4px 8px; border-radius: 20px; border: 1px solid #ccc;">
                <i class="fas fa-smile" onclick="toggleEmojiPicker(this)" style="color: #1e3c72; cursor: pointer; padding-left:10px;"></i>
                <div class="emoji-picker" style="display: none; position: absolute; bottom: 50px; left: 0; background: white; border: 1px solid #d1d8e0; padding: 12px; border-radius: 12px; z-index: 1000; box-shadow: 0 8px 16px rgba(0,0,0,0.15); width: 220px; grid-template-columns: repeat(5, 1fr); gap: 10px;">
                    <span onclick="insertEmoji('😊', this)" style="cursor: pointer; font-size: 20px;">😊</span>
                    <span onclick="insertEmoji('👍', this)" style="cursor: pointer; font-size: 20px;">👍</span>
                    <span onclick="insertEmoji('❤️', this)" style="cursor: pointer; font-size: 20px;">❤️</span>
                    <span onclick="insertEmoji('👏', this)" style="cursor: pointer; font-size: 20px;">👏</span>
                    <span onclick="insertEmoji('🚀', this)" style="cursor: pointer; font-size: 20px;">🚀</span>
                    <span onclick="insertEmoji('💡', this)" style="cursor: pointer; font-size: 20px;">💡</span>
                    <span onclick="insertEmoji('🤝', this)" style="cursor: pointer; font-size: 20px;">🤝</span>
                    <span onclick="insertEmoji('⭐', this)" style="cursor: pointer; font-size: 20px;">⭐</span>
                    <span onclick="insertEmoji('✅', this)" style="cursor: pointer; font-size: 20px;">✅</span>
                    <span onclick="insertEmoji('💬', this)" style="cursor: pointer; font-size: 20px;">💬</span>
                    <span onclick="insertEmoji('🔥', this)" style="cursor: pointer; font-size: 20px;">🔥</span>
                    <span onclick="insertEmoji('✨', this)" style="cursor: pointer; font-size: 20px;">✨</span>
                    <span onclick="insertEmoji('🎉', this)" style="cursor: pointer; font-size: 20px;">🎉</span>
                    <span onclick="insertEmoji('🙏', this)" style="cursor: pointer; font-size: 20px;">🙏</span>
                    <span onclick="insertEmoji('🤔', this)" style="cursor: pointer; font-size: 20px;">🤔</span>
                </div>
                <input type="text" class="comment-text-input" placeholder="Write a comment..." style="flex: 1; border: none; outline: none; background: transparent; padding: 5px;">
                <i class="fas fa-paper-plane" onclick="postComment(this)" style="color: #1e3c72; cursor: pointer; padding-right:10px;"></i>
            </div>
        </div>
    `;
    feed.append(card);
updateTimeLabels();
}



/*
function cancelPost() {
    document.getElementById('postContent').value = "";
    document.getElementById('preview-area').innerHTML = "";
    document.getElementById('imgUpload').value = "";
    selectedFileData = null;
}*/



function cancelPost() {
    document.getElementById('postContent').value = "";
    document.getElementById('preview-area').innerHTML = "";
    document.getElementById('imgUpload').value = "";
    selectedFilesArray = [];
    selectedFileData = null;
    tempFileData = null;
}


function postComment(iconElement) {
    const postCard = iconElement.closest('.post-card');
    const postId = postCard.getAttribute('data-post-id');
    const inputField = postCard.querySelector('.comment-text-input');
    const commentText = inputField.value.trim();

    if (commentText === "") return;

    // Send data to PHP
    const formData = new FormData();
    formData.append('action', 'save_comment');
    formData.append('post_id', postId);
    formData.append('comment_text', commentText);

formData.append('user_id', currentUserId);


    fetch('process_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update UI count
            const countSpan = postCard.querySelector('.comment-count');
            countSpan.innerText = parseInt(countSpan.innerText) + 1;

            // Append to thread
            const thread = postCard.querySelector('.comment-thread');
            const newComment = document.createElement('div');
            newComment.style.cssText = "margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;";
            newComment.innerHTML = `
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #cddef5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-user" style="color: #1e3c72; font-size: 18px;"></i>
                </div>
                <div style="background: white; padding: 10px 14px; border-radius: 18px; border: 1px solid #e1e4e8; flex-grow: 1;">
                    <div style="font-size: 15px; font-weight: 600; color: #132b44;">
                        You <span style="font-weight: 400; color: black; margin-left: 5px;">${commentText}</span>
                    </div>
                </div>
            `;
            thread.appendChild(newComment);
            inputField.value = "";
        } else {
            alert("Error saving comment.");
        }
    });
}
// Toggle the display of the emoji picker
function toggleEmojiPicker(icon) {
    const picker = icon.parentElement.querySelector('.emoji-picker');
    // Change 'block' to 'grid' to maintain your 3-row layout
    picker.style.display = (picker.style.display === 'none' || picker.style.display === '') ? 'grid' : 'none';
}

// Insert the selected emoji into the input field
function insertEmoji(emoji, element) {
    const inputArea = element.closest('.comment-input-area');
    const inputField = inputArea.querySelector('.comment-text-input');
    inputField.value += emoji;
    
    // Hide picker after selection
    element.parentElement.style.display = 'none';
}


function toggleLike(element, postId) {
    const formData = new FormData();
    formData.append('action', 'update_like');
    formData.append('post_id', postId);

formData.append('user_id', currentUserId);

    fetch('process_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update the UI with the new count returned from the DB
            const countSpan = element.querySelector('.like-count');
            countSpan.innerText = data.new_count;
            
            // Toggle visual state to RED
            const icon = element.querySelector('i');
            icon.className = 'fas fa-heart'; // Filled heart
            icon.style.color = '#e74c3c'; // Red color
            
            // Disable the button so they don't click it again
            element.style.pointerEvents = 'none'; 
        } else if (data.message === 'Already liked') {
            alert("You have already liked this post!");
        }
    })
    .catch(error => console.error('Error:', error));
}

function focusComment(element) {
    const postCard = element.closest('.post-card');
    const inputField = postCard.querySelector('.comment-text-input');
    inputField.focus();
}



function updateTimeLabels() {
    document.querySelectorAll('.time-ago').forEach(label => {
        const timeValue = label.getAttribute('data-time');
        if (!timeValue) return;

        // Detect if the time is a timestamp number or a date string
        let postTime;
        if (!isNaN(timeValue)) {
            postTime = parseInt(timeValue);
        } else {
            postTime = new Date(timeValue.replace(/-/g, "/")).getTime(); // Fix for database strings
        }

        const now = new Date().getTime();
        const diff = Math.floor((now - postTime) / 1000);
        
        let timeText = "";
        if (diff < 60) timeText = "Just now";
        else if (diff < 3600) timeText = Math.floor(diff/60) + " mins ago";
        else if (diff < 86400) timeText = Math.floor(diff/3600) + " hours ago";
        else timeText = Math.floor(diff/86400) + " days ago";

        label.innerText = timeText;
    });
}
// Run it immediately and then refresh every minute
updateTimeLabels();
setInterval(updateTimeLabels, 60000);

window.onload = function() {
    console.log("Loading posts for user:", currentUserId);
    
    const feed = document.getElementById('main-feed');
    if (!feed) {
        console.error("main-feed element not found!");
        return;
    }
    
    feed.innerHTML = '<div style="text-align:center; padding:50px;">Loading posts...</div>';
    
    // 1. Fetch posts from your PHP bridge
    fetch(`process_data.php?action=get_posts&id=${currentUserId}`)
    .then(response => response.json())
    .then(res => {
        console.log("API Response:", res);

        if (res.status !== 'success') {
            feed.innerHTML = `<div style="text-align:center; padding:50px;">Error: ${res.message}</div>`;
            return;
        }

        const posts = res.data;

        if (posts && posts.length > 0) {
            feed.innerHTML = ''; // Clear the loading message

            posts.forEach(post => {
                // 2. WE USE YOUR BEAUTIFUL UI FUNCTION HERE
                // This ensures the Name, Profile Image, and Delete Button show up
                renderFullPostCard(
                    post.post_text || '', 
                    post.attached_image_url || '[]', 
                    post.post_id, 
                    post.like_count || 0, 
                    post.has_liked || 0, 
                    post.comments || [], 
                    post.created_at,
                    post // We pass the whole 'post' object so the function can see the profile photo
                );
            });

        } else {
            feed.innerHTML = '<div style="text-align:center; padding:50px;">No posts yet</div>';
        }
    })
    .catch(error => {
        console.error("Error:", error);
        feed.innerHTML = '<div style="text-align:center; padding:50px;">Error loading posts</div>';
    });
};


// Locate your function that handles the "Post" button click
function handleNewPost() {
    const content = document.getElementById('post-content').value;
    const image = ""; // your image logic
    
    // ... after your fetch saves to database and returns a postId ...
    const postId = response.postId; 

    // FIX: Pass Date.now() as the last argument for new posts
    renderFullPostCard(content, image, postId, 0, 0, [], Date.now()); 
    
    // Refresh labels so it says "Just now"
    updateTimeLabels();
}


function toggleChat() {
    const modal = document.getElementById('chat-modal');
    // This flips the display between 'none' and 'flex'
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}

// Attach the toggle event to the trigger icon
document.getElementById('chat-trigger').addEventListener('click', toggleChat);

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (message === "") return;

    const formData = new FormData();
    // Use the exact action name from your payload: 'send_new_chat_message'
    formData.append('action', 'send_new_chat_message'); 
    
    // Use 'id' so your PHP script assigns it to $user_id automatically
    formData.append('id', currentUserId); 
    
    formData.append('message_text', message);
    formData.append('receiver_id', '471'); 

    fetch('process_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            // Pass the message ID from the database to the UI function
            appendMessageToUI(message, 'right', 0, res.data.message_id);  
            input.value = ""; 
        } else {
            // This will now show "Invalid action" if the PHP case doesn't match
            alert("Database Error: " + res.message);
        }
    })
    .catch(err => {
        console.error("Connection Error:", err);
    });
}
// STEP 3: Define the function so the browser knows what to do
function appendMessageToUI(text, direction, isRead = 0, messageId = null) {
    const container = document.getElementById('message-container');
    const emptyState = document.getElementById('empty-state');
    
    if (emptyState) emptyState.style.display = 'none';

    const msgDiv = document.createElement('div');
    msgDiv.className = 'message-wrapper';
    if (messageId) msgDiv.setAttribute('data-id', messageId);
    
    msgDiv.style.cssText = `margin-bottom: 10px; text-align: ${direction === 'right' ? 'right' : 'left'}; position: relative;`;

    // Visual indicators for the message status
    const statusText = (isRead == 1) ? 'Seen' : 'Sent';
    const tickColor = (isRead == 1) ? '#34B7F1' : '#999';

    msgDiv.innerHTML = `
        <div style="display: inline-block; max-width: 70%; background: ${direction === 'right' ? '#dcf8c6' : '#fff'}; 
                    padding: 8px 12px; border-radius: 10px; text-align: left; box-shadow: 0 1px 1px rgba(0,0,0,0.1); border: 1px solid #eee;">
            
            <span style="font-size: 14px; color: #000;">${text}</span>
            
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; margin-top: 4px; font-size: 10px; color: #555;">
                ${statusText} <i class="fas fa-check-double" style="color: ${tickColor};"></i>
            </div>
        </div>
    `;

    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
}

function openVisitingCardModal() {
    const modal = document.getElementById('visitingCardModal');
    if (modal) modal.style.display = 'block';
    
    const formData = new FormData();
    formData.append('action', 'get_my_card_data');
    formData.append('id', currentUserId);

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(res => {
        // res.data will contain { json_data: "..." }
        if (res.status === "success" && res.data.json_data) {
            try {
                // Parse the string stored in the database into a JSON object/array
                const cardArray = JSON.parse(res.data.json_data);
                
                // Ensure it is an array before passing to your render function
                const finalArray = Array.isArray(cardArray) ? cardArray : [cardArray];
                
                renderCardToModal(finalArray);
            } catch (e) {
                console.error("JSON Parsing Error:", e);
                alert("The card data format is incorrect.");
            }
        } else {
            console.error("Fetch Error:", res.message);
        }
    })
    .catch(err => console.error("Network Error:", err));
}


function renderCardToModal(cardsArray) {
    const container = document.getElementById('modal-card-container');
    container.innerHTML = ''; 

    cardsArray.forEach(cardDesign => {
        // Create a cell wrapper for the grid
        const cell = document.createElement('div');
        cell.style.width = "100%"; 
        cell.style.display = "flex";
        cell.style.justifyContent = "center"; // Centers the card in the column
        
        // The actual card visual
        const cardWrapper = document.createElement('div');
        cardWrapper.style.width = "540px";
        cardWrapper.style.height = "300px";
        cardWrapper.style.position = "relative";
        cardWrapper.style.border = "1px solid #ccc";
        
        cell.appendChild(cardWrapper);
        container.appendChild(cell);
        
        // Pass the wrapper to your existing function
        applyDesignToUI(cardDesign, cardWrapper);
    });
}
function applyDesignToUI(design, targetContainer) {
    // 1. Clear previous content
    targetContainer.innerHTML = '';
    
    // 2. Apply Container Styles
    targetContainer.style.position = "relative";
    targetContainer.style.overflow = "hidden"; // Ensures elements don't bleed out
    
    // Apply background and border from the root of the design object
    targetContainer.style.backgroundColor = design.backgroundColor || "#ffffff";
    
    if (design.border) {
        const b = design.border;
        targetContainer.style.border = `${b.width || 2}px ${b.style || 'solid'} ${b.color || '#333'}`;
        targetContainer.style.borderRadius = (b.radius || 0) + "px";
    }

    // 3. Render Elements
    if (design.elements && Array.isArray(design.elements)) {
        design.elements.forEach(el => {
            let newEl;

            if (el.type === 'image') {
                newEl = document.createElement('img');
                newEl.src = el.imageSrc;
                newEl.style.objectFit = "cover";
            } else if (el.type === 'line') {
                newEl = document.createElement('div');
                newEl.style.borderBottom = `2px ${el.lineStyle || 'solid'} #333`;
            } else {
                newEl = document.createElement('div');
                newEl.innerText = el.content || "";
                
                // Apply Text Styles
                if (el.textStyle) {
                    newEl.style.fontSize = el.textStyle.fontSize;
                    newEl.style.color = el.textStyle.color;
                    newEl.style.fontWeight = el.textStyle.fontWeight;
                    newEl.style.backgroundColor = el.textStyle.backgroundColor || "transparent";
                    newEl.style.textAlign = el.textStyle.textAlign || "left";
                }
            }

            // Common Positioning
            newEl.style.position = 'absolute';
            newEl.style.left = el.style.left;
            newEl.style.top = el.style.top;
            newEl.style.width = el.style.width;
            newEl.style.height = el.style.height;
            
            targetContainer.appendChild(newEl);
        });
    }
}





// Function to append message to UI with status text and ticks
function appendMessageToUI(text, direction, isRead, messageId) {
    const container = document.getElementById('message-container');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'message-wrapper';
    msgDiv.setAttribute('data-id', messageId);
    msgDiv.style.cssText = `margin-bottom: 10px; text-align: ${direction === 'right' ? 'right' : 'left'}; position: relative;`;

    // Added a CSS style block for hover effects to reveal the delete icon
    const style = `
        <style>
            .message-wrapper:hover .delete-btn { display: inline-block !important; }
            .delete-btn { display: none; cursor: pointer; color: #888; margin-right: 10px; }
            .delete-btn:hover { color: #e74c3c; }
        </style>
    `;

    const statusText = (isRead == 1) ? 'Seen' : 'Sent';
    const tickColor = (isRead == 1) ? '#34B7F1' : '#999';
    const doubleTickIcon = `<i class="fas fa-check-double" style="color: ${tickColor};"></i>`;

    msgDiv.innerHTML = style + `
        <div style="display: inline-block; max-width: 70%; background: ${direction === 'right' ? '#dcf8c6' : '#fff'}; 
                    padding: 8px 12px; border-radius: 10px; text-align: left; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
            
            <span class="delete-btn" onclick="deleteMessage(${messageId}, this)">
                <i class="fas fa-trash-alt"></i>
            </span>

            <span style="font-size: 14px; color: #000;">${text}</span>
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; margin-top: 4px; font-size: 10px; color: #555;">
                ${statusText} ${doubleTickIcon}
            </div>
        </div>
    `;
    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
}

// Ensure loadChatHistory calls the updated function
function loadChatHistory() {
    const formData = new FormData();
    formData.append('action', 'fetch_chat_history');

formData.append('user_id', currentUserId); 
    formData.append('receiver_id', "471"); // The person you are talking to

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            const container = document.getElementById('message-container');
            container.innerHTML = '';
            res.data.forEach(msg => {
                const direction = (msg.sender_id == "177") ? 'right' : 'left';
                appendMessageToUI(msg.message_text, direction, msg.is_read, msg.message_id);
            });
        }
    });
}



// 3. Delete a specific message
function deleteMessage(messageId, element) {
    if (!confirm("Delete this message for you?")) return;

    const formData = new FormData();
    formData.append('action', 'delete_chat_message');
    formData.append('message_id', messageId);
    formData.append('user_id', currentUserId);

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            element.closest('.message-wrapper').remove();
        } else {
            // This will show you exactly why the DB didn't update
            alert("Database Error: " + res.message);
        }
    })
    .catch(err => console.error("Fetch error:", err));
}

// Call this to load saved messages from image_d9be70.png
loadChatHistory();



function openExperienceModal() {
    // 1. Show the modal
    document.getElementById('expModal').style.display = 'block';
    
    // 2. Fetch the list immediately so it loads the data
    fetchExperienceList(); 
    
    // 3. Ensure the List View is shown, not the form
    showListView();
}
function showListView() {
    // Show list, hide form
    document.getElementById('listView').style.display = 'block';
    document.getElementById('formView').style.display = 'none';
    
    // Optional: Refresh the list every time the modal is opened
    fetchExperienceList();
}

function showFormView() {
    // Hide list, show form
    document.getElementById('listView').style.display = 'none';
    document.getElementById('formView').style.display = 'block';
}


function saveAndDisplay() {
    // 1. Safety check
    const companyInput = document.getElementById('expCompany');
    if (!companyInput) return; 

    // 2. Build Payload
    let formData = new FormData();
    formData.append('action', 'save_experience');
formData.append('id', currentUserId);
    formData.append('company', companyInput.value);
    formData.append('title', document.getElementById('expTitle').value);
    formData.append('logo', document.getElementById('expLogo').value);
    formData.append('type', document.getElementById('expType').value);
    formData.append('location', document.getElementById('expLocation').value);
    formData.append('start', document.getElementById('expStart').value);
    formData.append('end', document.getElementById('expEnd').value);
    formData.append('description', document.getElementById('expDescription').value);

    // 3. Execute
    fetch('process_data.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            showListView(); // Back to list on success
        } else {
            alert('Database Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => console.error('Fetch error:', err));
}

function fetchExperienceList() {
    let formData = new FormData();
    formData.append('action', 'get_experience');
    formData.append('id', currentUserId);

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        const workCol = document.getElementById('workResultList');
        const eduCol = document.getElementById('educationResultList');
        
        workCol.innerHTML = '';
        eduCol.innerHTML = ''; 
        
        if(res.status === 'success' && res.data) {
            res.data.forEach(item => {
                const isWork = item.category === 'work';
                
                // 1. Mandatory Info: Dates
                const dateRange = `${item.start_date} — ${item.end_date || 'Present'}`;

                // 2. Extra Details (Type, Location, Field, Grade)
                let metaInfo = '';
                if (isWork) {
                    metaInfo = `
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; font-size: 0.8rem; color: #64748b;">
                            <span><i class="fas fa-briefcase"></i> ${item.type || 'N/A'}</span>
                            <span><i class="fas fa-map-marker-alt"></i> ${item.location || 'N/A'}</span>
                        </div>`;
                } else {
                    metaInfo = `
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; font-size: 0.8rem; color: #64748b;">
                            ${item.field ? `<span><i class="fas fa-book"></i> ${item.field}</span>` : ''}
                            ${item.grade ? `<span><i class="fas fa-medal"></i> Grade: ${item.grade}</span>` : ''}
                        </div>`;
                }

                // 3. The Description Block
                const descriptionHtml = (item.description && item.description.trim() !== "") 
                    ? `<div style="margin-top: 12px; padding-top: 10px; border-top: 1px border-top: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                        ${item.description}
                       </div>` 
                    : '';

                const cardHtml = `
                <div style="padding: 18px; border: 1px solid #f1f5f9; border-radius: 14px; margin-bottom: 15px; background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-left: 4px solid ${isWork ? '#0a66c2' : '#c26a0a'};">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div style="font-weight: 700; font-size: 1.05rem; color: #1e293b;">${item.title}</div>
                            <div style="font-size: 0.95rem; color: #000; font-weight: 600;">${item.institution}</div>
                        </div>
                        <i class="fas fa-edit" style="color: #cbd5e1; cursor: pointer; font-size: 0.9rem;"></i>
                    </div>

                    <div style="margin-top: 6px; font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 5px;">
                        <i class="far fa-calendar-alt"></i> ${dateRange}
                    </div>

                    ${metaInfo}
                    ${descriptionHtml}
                </div>`;

                if (isWork) {
                    workCol.innerHTML += cardHtml;
                } else {
                    eduCol.innerHTML += cardHtml;
                }
            });

            // Empty state handling
            if (workCol.innerHTML === '') workCol.innerHTML = '<p style="color:#94a3b8; text-align:center; padding:15px;">No work history found.</p>';
            if (eduCol.innerHTML === '') eduCol.innerHTML = '<p style="color:#94a3b8; text-align:center; padding:15px;">No education history found.</p>';
        }
    })
    .catch(err => console.error("Error loading history:", err));
}
function loadSidebarExperience() {
    let formData = new FormData();
    formData.append('action', 'get_experience_only'); 
    formData.append('id', currentUserId);

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(workData => {
        const display = document.getElementById('latestExpContent'); 
        
        // 1. Remove the gap at the top
        display.style.marginTop = "-10px"; 

        // PRIORITY 1: Work Experience
        if(workData.status === 'success' && workData.data.length > 0 && workData.data[0] !== null) {
            const job = workData.data[0];
            display.innerHTML = `
                <div style="font-size: 0.95rem; line-height: 1.3; color: #000;">
                    <div style="font-weight: 700;">
                        <i class="fas fa-briefcase" style="color: #0a66c2; font-size: 0.85rem; margin-right: 5px;"></i>
                        ${job.title}
                    </div>
                    <div style="font-size: 0.88rem; color: #333; font-weight: 500; margin-top: 2px;">
                        ${job.institution}
                    </div>
                    <div style="font-size: 0.82rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-map-marker-alt" style="font-size: 0.75rem;"></i> ${job.location || 'Remote'}
                    </div>
                </div>`;
        } 
        // PRIORITY 2: Education
        else {
            let eduForm = new FormData();
            eduForm.append('action', 'get_education_only');
            eduForm.append('id', currentUserId);

            fetch('process_data.php', { method: 'POST', body: eduForm })
            .then(res => res.json())
            .then(eduData => {
                if(eduData.status === 'success' && eduData.data.length > 0 && eduData.data[0] !== null) {
                    const edu = eduData.data[0];
                    display.innerHTML = `
                        <div style="font-size: 0.95rem; line-height: 1.3; color: #000;">
                            <div style="font-weight: 700;">
                                <i class="fas fa-graduation-cap" style="color: #c26a0a; font-size: 0.9rem; margin-right: 5px;"></i>
                                ${edu.title}
                            </div>
                            <div style="font-size: 0.88rem; color: #333; font-weight: 500; margin-top: 2px;">
                                ${edu.institution}
                            </div>
                            ${edu.grade ? `
                            <div style="font-size: 0.82rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-medal" style="font-size: 0.75rem;"></i> CGPA: ${edu.grade}
                            </div>` : ''}
                        </div>`;
                } else {
                    display.innerHTML = '<span style="color:#94a3b8; font-size: 0.85rem;">Update your profile</span>';
                }
            });
        }
    });
}
// 1. Load it when the page opens
window.addEventListener('DOMContentLoaded', loadSidebarExperience);

// 2. IMPORTANT: Update the sidebar every time you save a new experience
// Add 'loadSidebarExperience();' inside your existing saveAndDisplay() function's success block.



// Function to show/hide extra fields
function toggleExtra(id) {
    const el = document.getElementById(id);
    el.style.display = (el.style.display === 'none') ? 'grid' : 'none';
}

function saveDualHistory() {
    let formData = new FormData();
    formData.append('action', 'save_dual_history');
    formData.append('id', currentUserId);

    let isValid = true;

    // Helper to apply/reset red border
    const validateField = (id) => {
        const el = document.getElementById(id);
        if (!el || el.value.trim() === "" || el.value === "0") {
            el.style.border = "2px solid #ff4d4d"; // Red Mark
            isValid = false;
            return false;
        } else {
            el.style.border = "1px solid #e2e8f0"; // Reset
            return true;
        }
    };

    // --- SECTION 1: WORK EXPERIENCE ---
    const expComp = document.getElementById('expCompany').value.trim();
    if (expComp !== "") {
        // Mandatory fields (Red mark if empty)
        const v1 = validateField('expCompany');
        const v2 = validateField('expTitle');
        const v3 = validateField('expType');
        const v4 = validateField('expLocation');
        const v5 = validateField('expStart');
        const v6 = validateField('expEnd');

        if (v1 && v2 && v3 && v4 && v5 && v6) {
            formData.append('has_exp', '1');
            formData.append('exp_company', expComp);
            formData.append('exp_title', document.getElementById('expTitle').value);
            formData.append('exp_type', document.getElementById('expType').value);
            formData.append('exp_loc', document.getElementById('expLocation').value);
            formData.append('exp_start', document.getElementById('expStart').value);
            formData.append('exp_end', document.getElementById('expEnd').value);
            
            // ADDED: Capture Description (Optional)
            const expDesc = document.getElementById('expDescription'); // Ensure this ID exists in HTML
            formData.append('exp_description', expDesc ? expDesc.value : '');
        }
    }

    // --- SECTION 2: EDUCATION ---
    const eduSch = document.getElementById('eduSchool').value.trim();
    if (eduSch !== "") {
        // Mandatory fields (Red mark if empty)
        const v7 = validateField('eduSchool');
        const v8 = validateField('eduDegree');
        const v9 = validateField('eduStart');
        const v10 = validateField('eduEnd');

        if (v7 && v8 && v9 && v10) {
            formData.append('has_edu', '1');
            formData.append('edu_school', eduSch);
            formData.append('edu_degree', document.getElementById('eduDegree').value);
            formData.append('edu_start', document.getElementById('eduStart').value);
            formData.append('edu_end', document.getElementById('eduEnd').value);
            
            // Optional fields (Captured but no red mark)
            formData.append('edu_field', document.getElementById('eduField').value);
            formData.append('edu_grade', document.getElementById('eduGrade').value);
            
            // ADDED: Capture Education Description (Optional)
            const eduDesc = document.getElementById('eduDescription'); // Ensure this ID exists in HTML
            formData.append('edu_description', eduDesc ? eduDesc.value : '');
        }
    }

    if (!isValid) {
        alert("Please fill in all required fields marked in red.");
        return; 
    }

    if (!formData.has('has_exp') && !formData.has('has_edu')) {
        alert("Please enter at least one Work or Education entry.");
        return;
    }

    fetch('process_data.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('expModal').style.display = 'none'; 
            fetchExperienceList(); 
            loadSidebarExperience(); 
            
            // Reset fields
            document.querySelectorAll('#formView input, #formView select, #formView textarea').forEach(input => {
                input.value = '';
                input.style.border = "1px solid #e2e8f0";
            });
        } else {
            alert("Error: " + data.message);
        }
    });
}



function deletePost(postId) {
    // This is the alert you requested
    if (confirm("Are you sure you want to delete this post permanently?")) {
        const formData = new FormData();
        formData.append('action', 'delete_post');
        formData.append('post_id', postId);

        fetch('process_data.php', { // Make sure this matches your PHP filename
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Post deleted successfully!");
                location.reload(); // Refresh the page to show it's gone
            } else {
                alert("Error deleting post: " + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}


</script>
