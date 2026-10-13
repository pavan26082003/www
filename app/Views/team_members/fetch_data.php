<?php
header('Content-Type: application/json');

// 1. Updated connection to use the 'profile' database
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "EIS@24dbCOS"); 
define("DB_NAME", "profile");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$action = $_POST['action'] ?? ($_GET['action'] ?? null);
$user_id = $_POST['user_id'] ?? ($_GET['user_id'] ?? null);

// --- BRANCH: GET FULL PROFILE ---
if ($action === 'get_full_profile') {
    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "User ID required"]);
        exit;
    }
    
    // Now selecting from the unified 'users' table
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "data" => $row]);
    } else {
        echo json_encode(["success" => false, "message" => "Profile not found"]);
    }
    exit;
}

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit;
}

// --- BRANCH: FETCH DESIGN ---
if ($action === 'get_design') {
    // pulling 'json_data' directly from 'users' table
    $stmt = $conn->prepare("SELECT json_data FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $data = json_decode($row['json_data'], true); 
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        echo json_encode(["success" => false, "message" => "No design found"]);
    }
    exit;
}

// --- BRANCH: SAVE STUDIO DESIGN ---
if ($action === 'save_design') {
    $new_card_json = $_POST['json_data'] ?? '{}';
    $new_card = json_decode($new_card_json, true);
    
    // Fetch existing cards from 'users' table
    $stmt = $conn->prepare("SELECT json_data FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $all_cards = [];
    if ($row = $result->fetch_assoc()) {
        $existing_data = json_decode($row['json_data'], true);
        if (is_array($existing_data)) {
            $all_cards = array_values($existing_data);
        }
    }
    
    $all_cards[] = $new_card; 
    $final_json = json_encode(array_values($all_cards));

    // Update the 'json_data' column in 'users'
    $stmt = $conn->prepare("UPDATE users SET json_data = ? WHERE user_id = ?");
    $stmt->bind_param("ss", $final_json, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Card added to library!"]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
    exit;
}

// --- BRANCH: UPDATE PROFILE DATA ---
$user_id       = $_POST['user_id'] ?? '';
$first_name    = $_POST['first_name'] ?? '';
$last_name     = $_POST['last_name'] ?? '';
$email         = $_POST['email'] ?? '';
$phone         = $_POST['phone'] ?? '';
$address       = $_POST['address'] ?? '';
$gender        = $_POST['gender'] ?? '';


$about_notes    = $_POST['notes'] ?? '';

// --- FILE UPLOAD LOGIC ---
// 1. Get the path sent from JavaScript
$profile_image = $_POST['current_image_path'] ?? ''; 

// 2. SAFETY CHECK: If the UI sent a "blob:" link, clear it!
// This prevents temporary browser links from being saved in your database.
if (strpos($profile_image, 'blob:') !== false) {
    $profile_image = '1234'; 
}

// 3. Check if a new file was actually uploaded (The Cropped Image)
if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['profile_image_file']['tmp_name'];
    $originalName = $_FILES['profile_image_file']['name'];
    
    // Generate a unique filename using user ID and current time
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    
    // Set the folder path
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/files/grid/';
    if (!is_dir($uploadDir)) { 
        mkdir($uploadDir, 0777, true); 
    }
    
    $full_save_path = $uploadDir . $filename;

    // Move the file to your 'files/grid/' folder
    if (move_uploaded_file($tmpName, $full_save_path)) {
        // Success! Set the path that will be saved in the Database
        $profile_image = 'files/grid/' . $filename;
    }
}
// --- END FILE UPLOAD LOGIC ---

// Handling Date of Birth (Standardizing for DB)
$raw_dob = $_POST['dob'] ?? '';
$dob = null;
if (!empty($raw_dob)) {
    $dateObj = DateTime::createFromFormat('d/m/Y', $raw_dob);
    if (!$dateObj) { $dateObj = date_create($raw_dob); } 
    if ($dateObj) { $dob = $dateObj->format('Y-m-d'); }
}

// Handling Social Media JSON
$raw_social = $_POST['social_links'] ?? '';
$links_array = array_filter(array_map('trim', explode(',', $raw_social)));
$social_json = json_encode($links_array); 

// Database Update - Saves the URL path string directly
$stmt = $conn->prepare("UPDATE users SET 
            first_name = ?, last_name = ?, 
            business_phone = ?, mailing_address = ?, 
            date_of_birth = ?, social_media_links = ?, 
            profile_photo_url = ?, gender = ?, profile_summary = ?
            WHERE user_id = ?");

// --- CHANGE THIS LINE (Remove the extra "s" and the duplicate variable) ---
$stmt->bind_param("sssssssssi", 
    $first_name, 
    $last_name, 
    $phone, 
    $address, 
    $dob, 
    $social_json, 
    $profile_image, 
    $gender, 
    $about_notes, 
    $user_id
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Profile Updated Successfully!", 
        "path" => $profile_image // This returns the clean URL to your script
    ]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
$stmt->close();
$conn->close();
?>
