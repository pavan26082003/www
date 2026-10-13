<?php
// ========== ERROR HANDLING ==========
error_reporting(0);
ini_set('display_errors', 0);

// Start buffer
ob_start();

session_start();
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// DB Connection
$conn = new mysqli("localhost", "root", "Facteno@2025!", "profile");

if ($conn->connect_error) {
    sendError("Database connection failed");
}

// Define upload directory
$uploadDir = '/var/www/html/files/grid/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Helper functions
function cleanOutput($data) {
    while (ob_get_level()) ob_end_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function sendError($message) {
    cleanOutput(["status" => "error", "message" => $message]);
}

function sendSuccess($data = null) {
    $response = ["status" => "success"];
    if ($data !== null) $response["data"] = $data;
    cleanOutput($response);
}

// Function to save uploaded images
function saveImages($files, $uploadDir) {
    $savedImages = [];
    
    if (!isset($files['images'])) {
        return $savedImages;
    }
    
    // Handle multiple files
    $fileCount = count($files['images']['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['images']['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['images']['tmp_name'][$i];
            $originalName = $files['images']['name'][$i];
            
            // Generate unique filename
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $filePath = $uploadDir . $filename;
            
            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($extension, $allowedTypes)) {
                // Move uploaded file
                if (move_uploaded_file($tmpName, $filePath)) {
                    $savedImages[] = 'files/grid/' . $filename;
                }
            }
        }
    }
    
    return $savedImages;
}

// Inputs
$user_id = $_GET['id'] ?? $_POST['id'] ?? "";
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        // ================= GET POSTS =================
        case 'get_posts':
            $user_id = $conn->real_escape_string($user_id);

            $sql = "SELECT 
                p.*, 
                UNIX_TIMESTAMP(p.created_at) * 1000 AS created_at_ms,
                CASE 
                    WHEN pl.user_id IS NOT NULL THEN 1 ELSE 0 
                END AS has_liked
                FROM posts p
                LEFT JOIN post_likes pl 
                    ON pl.post_id = p.post_id 
                    AND pl.user_id = '$user_id'
                ORDER BY p.created_at DESC";

            $result = $conn->query($sql);

            if (!$result) {
                sendError("Query failed");
            }

            $posts = [];

            while ($row = $result->fetch_assoc()) {
                $postId = (int)$row['post_id'];

                // Get comments
                $comments = [];
                $commentResult = $conn->query("SELECT * FROM comments WHERE post_id = $postId ORDER BY comment_id ASC");

                if ($commentResult) {
                    while ($comment = $commentResult->fetch_assoc()) {
                        $comments[] = $comment;
                    }
                }

                $row['comments'] = $comments;
                $posts[] = $row;
            }

            sendSuccess($posts);
            break;

        // ================= SAVE POST =================
     case 'save_post':
            $user_id = $conn->real_escape_string($user_id);
            $content = $conn->real_escape_string($_POST['content'] ?? '');
            
            // 1. OPEN SECOND CONNECTION TO GET BUSINESS PROFILE
            $conn_user = new mysqli("localhost", "root", "Facteno@2025!", "factor_db2");
            
            if (!$conn_user->connect_error) {
                $user_query = $conn_user->query("SELECT first_name, last_name, profile_image FROM user_business_profiles WHERE user_id = '$user_id'");
                $user_data = $user_query->fetch_assoc();
                
                // Combine names and handle defaults
                $f_name = $user_data['first_name'] ?? 'User';
                $l_name = $user_data['last_name'] ?? '';
                $real_name = $conn->real_escape_string(trim($f_name . ' ' . $l_name));
                $real_image = $conn->real_escape_string($user_data['profile_image'] ?? '');
                
                $conn_user->close(); // Always close the second connection
            } else {
                // Fallback if second DB fails
                $real_name = 'User';
                $real_image = '';
            }

            // 2. Handle image uploads for the post
            $savedImages = saveImages($_FILES, $uploadDir);
            $imagesJson = json_encode($savedImages);
            
            $currentTime = date('Y-m-d H:i:s');

            // 3. INSERT INTO POSTS TABLE WITH REAL DATA
            $sql = "INSERT INTO posts 
                (user_id, full_name, profile_photo_url, post_text, attached_image_url, like_count, comment_count, created_at) 
                VALUES 
                ('$user_id', '$real_name', '$real_image', '$content', '$imagesJson', 0, 0, '$currentTime')";

            if ($conn->query($sql)) {
                sendSuccess([
                    "post_id" => $conn->insert_id, 
                    "full_name" => $real_name,
                    "profile_photo_url" => $real_image,
                    "images" => $savedImages
                ]);
            } else {
                sendError("Post save failed: " . $conn->error);
            }
            break;

        // ================= LIKE =================
        case 'update_like':
            $post_id = (int)$_POST['post_id'];
            $user_id = $conn->real_escape_string($_POST['user_id']);

            $check = $conn->query("SELECT 1 FROM post_likes WHERE post_id=$post_id AND user_id='$user_id'");

            if ($check && $check->num_rows > 0) {
                sendError("Already liked");
            }

            $conn->begin_transaction();

            try {
                $conn->query("INSERT INTO post_likes (post_id, user_id) VALUES ($post_id, '$user_id')");
                $conn->query("UPDATE posts SET like_count = like_count + 1 WHERE post_id = $post_id");

                $conn->commit();

                $count = $conn->query("SELECT like_count FROM posts WHERE post_id=$post_id")
                              ->fetch_assoc()['like_count'];

                sendSuccess(["new_count" => $count]);

            } catch (Exception $e) {
                $conn->rollback();
                sendError("Like failed");
            }
            break;

        // ================= COMMENT =================
        case 'save_comment':
            $post_id = (int)$_POST['post_id'];
            $user_id = $conn->real_escape_string($_POST['user_id']);
            $text = $conn->real_escape_string($_POST['comment_text'] ?? '');

            if (!$text) sendError("Empty comment");

            $conn->begin_transaction();

            try {
                $conn->query("INSERT INTO comments (post_id, user_id, comment_text, created_at) 
                              VALUES ($post_id, '$user_id', '$text', NOW())");

                $conn->query("UPDATE posts SET comment_count = comment_count + 1 WHERE post_id = $post_id");

                $conn->commit();

                sendSuccess();

            } catch (Exception $e) {
                $conn->rollback();
                sendError("Comment failed");
            }
            break;

        // ================= DELETE =================
        case 'delete_post':
            $post_id = (int)$_POST['post_id'];
            
            // First, get the images to delete them from server
            $result = $conn->query("SELECT attached_image_url FROM posts WHERE post_id = $post_id");
            if ($result && $row = $result->fetch_assoc()) {
                $images = json_decode($row['attached_image_url'], true);
                if (is_array($images)) {
                    foreach ($images as $imagePath) {
                        $fullPath = '/var/www/html/' . $imagePath;
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
            }

            $conn->begin_transaction();

            try {
                $conn->query("DELETE FROM post_likes WHERE post_id=$post_id");
                $conn->query("DELETE FROM comments WHERE post_id=$post_id");
                $conn->query("DELETE FROM posts WHERE post_id=$post_id");

                $conn->commit();

                sendSuccess();

            } catch (Exception $e) {
                $conn->rollback();
                sendError("Delete failed");
            }
            break;


// ================= FETCH CHAT HISTORY =================
        case 'fetch_chat_history':
            $user_id = $conn->real_escape_string($_POST['user_id']);
            $receiver_id = $conn->real_escape_string($_POST['receiver_id']);

            // Fetch messages where:
            // 1. (A sent to B OR B sent to A)
            // 2. AND the sender hasn't "deleted" it for themselves
            $sql = "SELECT * FROM messages 
                    WHERE ((sender_id = '$user_id' AND receiver_id = '$receiver_id' AND is_deleted_by_sender = 0)
                    OR (sender_id = '$receiver_id' AND receiver_id = '$user_id' AND is_deleted_by_receiver = 0))
                    ORDER BY created_at ASC";

            $result = $conn->query($sql);
            $chat = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $chat[] = $row;
                }
            }
            sendSuccess($chat);
            break;

        // ================= DELETE CHAT MESSAGE =================
        case 'delete_chat_message':
            $message_id = (int)$_POST['message_id'];
            $user_id = $conn->real_escape_string($_POST['user_id']);

            // We do a "Soft Delete": 
            // If I am the sender, mark is_deleted_by_sender = 1
            // If I am the receiver, mark is_deleted_by_receiver = 1
            $sql = "UPDATE messages SET 
                    is_deleted_by_sender = CASE WHEN sender_id = '$user_id' THEN 1 ELSE is_deleted_by_sender END,
                    is_deleted_by_receiver = CASE WHEN receiver_id = '$user_id' THEN 1 ELSE is_deleted_by_receiver END
                    WHERE message_id = $message_id";

            if ($conn->query($sql)) {
                sendSuccess();
            } else {
                sendError("Delete failed: " . $conn->error);
            }
            break;
// ================= SEND CHAT MESSAGE =================
case 'send_new_chat_message':
    $sender_id = $conn->real_escape_string($user_id); 
    $receiver_id = $conn->real_escape_string($_POST['receiver_id']);
    $message_text = $conn->real_escape_string($_POST['message_text'] ?? '');

            if (empty($message_text)) {
                sendError("Message cannot be empty");
            }

            $sql = "INSERT INTO messages (sender_id, receiver_id, message_text, is_read, created_at) 
                    VALUES ('$sender_id', '$receiver_id', '$message_text', 0, NOW())";

            if ($conn->query($sql)) {
                sendSuccess([
                    "message_id" => $conn->insert_id,
                    "sender_id" => $sender_id, // Returns the ID of the current user
                    "created_at" => date('Y-m-d H:i:s')
                ]);
            } else {
                sendError("Failed to send message: " . $conn->error);
            }
            break;


// ================= FETCH VISITING CARD DATA =================
        case 'get_my_card_data':
            $user_id = $conn->real_escape_string($user_id); // Use the variable from the top of your script
            
            // Querying factor_db2 as per your setup
            $sql = "SELECT json_data FROM factor_db2.user_business_profiles WHERE user_id = '$user_id'";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Send the row containing the json_data string
                sendSuccess($row); 
            } else {
                sendError("No card data found for this user.");
            }
            break;



// Add this to your switch statement in process_data.php
case 'save_experience':
$user_id = $_POST['id'] ?? "";
   
    $company = $conn->real_escape_string($_POST['company']);
    $title = $conn->real_escape_string($_POST['title']); 
    $logo = $conn->real_escape_string($_POST['logo']);
    $type = $conn->real_escape_string($_POST['type']);
    $location = $conn->real_escape_string($_POST['location']);
    $start = $conn->real_escape_string($_POST['start']);
    $end = $conn->real_escape_string($_POST['end']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO experience (user_id, company_name, title, company_logo_url, employment_type, location, start_date, end_date, description) 
            VALUES ('$user_id', '$company', '$title', '$logo', '$type', '$location', '$start', '$end', '$description')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    break;

// --- UPDATE: save_dual_history to include Descriptions ---
case 'save_dual_history':
    $user_id = $conn->real_escape_string($_POST['id'] ?? "");
    $response = ["status" => "error", "message" => "No data provided"];

    if (empty($user_id)) {
        echo json_encode(["status" => "error", "message" => "User ID missing"]);
        break;
    }

    $exp_success = true;
    $edu_success = true;

    // 1. SAVE EXPERIENCE
    if (isset($_POST['has_exp']) && !empty($_POST['exp_company'])) {
        $comp  = $conn->real_escape_string($_POST['exp_company']);
        $title = $conn->real_escape_string($_POST['exp_title']);
        $type  = $conn->real_escape_string($_POST['exp_type'] ?? '');
        $loc   = $conn->real_escape_string($_POST['exp_loc'] ?? '');
        $start = $conn->real_escape_string($_POST['exp_start']);
        $end   = $conn->real_escape_string($_POST['exp_end']);
        $desc  = $conn->real_escape_string($_POST['exp_description'] ?? ''); // Added Description

        $sql1 = "INSERT INTO experience (user_id, company_name, title, employment_type, location, start_date, end_date, description) 
                 VALUES ('$user_id', '$comp', '$title', '$type', '$loc', '$start', '$end', '$desc')";
        
        if (!$conn->query($sql1)) {
            $exp_success = false;
            $response["message"] = "Exp Error: " . $conn->error;
        }
    }

    // 2. SAVE EDUCATION
    if (isset($_POST['has_edu']) && !empty($_POST['edu_school'])) {
        $sch    = $conn->real_escape_string($_POST['edu_school']);
        $deg    = $conn->real_escape_string($_POST['edu_degree']);
        $field  = $conn->real_escape_string($_POST['edu_field'] ?? '');
        $grade  = $conn->real_escape_string($_POST['edu_grade'] ?? '');
        $estart = $conn->real_escape_string($_POST['edu_start']);
        $eend   = $conn->real_escape_string($_POST['edu_end']);
        $edesc  = $conn->real_escape_string($_POST['edu_description'] ?? ''); // Added Description

        $sql2 = "INSERT INTO education (user_id, school_name, degree, field_of_study, grade, start_date, end_date, description) 
                 VALUES ('$user_id', '$sch', '$deg', '$field', '$grade', '$estart', '$eend', '$edesc')";
        
        if (!$conn->query($sql2)) {
            $edu_success = false;
            $response["message"] = "Edu Error: " . $conn->error;
        }
    }

    if ($exp_success && $edu_success) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode($response);
    }
    break;

// --- UPDATE: get_experience (The Main List) to show ALL fields ---
case 'get_experience':
    $user_id = $conn->real_escape_string($_POST['id'] ?? "");

    // Added type, field, grade, and description to the SELECT
    $sql = "(SELECT 'work' as category, title, company_name as institution, employment_type as type, location, start_date, end_date, description, '' as field, '' as grade FROM experience WHERE user_id = '$user_id')
            UNION
            (SELECT 'school' as category, degree as title, school_name as institution, '' as type, '' as location, start_date, end_date, description, field_of_study as field, grade FROM education WHERE user_id = '$user_id')
            ORDER BY start_date DESC";

    $result = $conn->query($sql);
    $history = [];
    while($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $history]);
    break;

// --- Fetch ONLY the latest work experience (Includes Location) ---
case 'get_experience_only':
    $user_id = $conn->real_escape_string($_POST['id'] ?? "");
    $sql = "SELECT 'work' as category, title, company_name as institution, location, start_date 
            FROM experience WHERE user_id = '$user_id' ORDER BY start_date DESC LIMIT 1";
    $result = $conn->query($sql);
    $data = [];
    if($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(["status" => "success", "data" => $data]);
    break;

// --- Fetch ONLY the latest education (Includes Grade/CGPA and Dates) ---
case 'get_education_only':
    $user_id = $conn->real_escape_string($_POST['id'] ?? "");
    // Only fetching what we actually need now
    $sql = "SELECT 'school' as category, degree as title, school_name as institution, grade 
            FROM education 
            WHERE user_id = '$user_id' 
            ORDER BY start_date DESC LIMIT 1";
    $result = $conn->query($sql);
    $data = [];
    if($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(["status" => "success", "data" => $data]);
    break;






        default:
            sendError("Invalid action");
    }

} catch (Exception $e) {
    sendError("Server error: " . $e->getMessage());
}






$conn->close();
?>
