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
$first_name    = $_POST['first_name'] ?? '';
$last_name     = $_POST['last_name'] ?? '';
$email         = $_POST['email'] ?? '';
$phone         = $_POST['phone'] ?? '';
$address       = $_POST['address'] ?? '';
$gender        = $_POST['gender'] ?? '';
$profile_image = $_POST['profile_image'] ?? '';

// Handling Date of Birth
$raw_dob = $_POST['dob'] ?? '';
$dob = null;
if (!empty($raw_dob)) {
    $dateObj = DateTime::createFromFormat('d/m/Y', $raw_dob);
    if ($dateObj) { $dob = $dateObj->format('Y-m-d'); }
}

// Handling Social Media JSON
$raw_social = $_POST['social_links'] ?? '';
$links_array = array_filter(array_map('trim', explode(',', $raw_social)));
$social_json = json_encode($links_array); 

// Update logic: Since user must exist to login, we only need UPDATE
$stmt = $conn->prepare("UPDATE users SET 
            first_name = ?, last_name = ?, email = ?, 
            business_phone = ?, mailing_address = ?, 
            date_of_birth = ?, social_media_links = ?, 
            profile_photo_url = ?, gender = ?
            WHERE user_id = ?");

// Note: column names match your profile.users screenshot
$stmt->bind_param("ssssssssss", $first_name, $last_name, $email, $phone, $address, $dob, $social_json, $profile_image, $gender, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile and Design Updated!"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
