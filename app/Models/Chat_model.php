<?php
class ChatModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all users who have interacted (either as sender or receiver)
    public function getAllChatUsers() {
        $query = "SELECT DISTINCT u.id, u.username, u.email 
                  FROM users u
                  WHERE u.id IN (
                      SELECT DISTINCT from_user_id FROM call_services
                      UNION
                      SELECT DISTINCT to_user_id FROM call_services WHERE to_user_id != 1
                  ) AND u.id != 1
                  ORDER BY u.username";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get chat history between admin and a specific user
    public function getChatHistory($adminId, $userId) {
        $query = "SELECT * FROM call_services 
                  WHERE (from_user_id = :adminId AND to_user_id = :userId)
                  OR (from_user_id = :userId AND to_user_id = :adminId)
                  ORDER BY created_at ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Save a new message from admin to user
    public function sendMessage($fromUserId, $toUserId, $message, $ticketNumber = null) {
        $query = "INSERT INTO call_services 
                  (created_at, from_user_id, to_user_id, message, ticket_number, flag, model_name)
                  VALUES (NOW(), :fromUserId, :toUserId, :message, :ticketNumber, 'admin_chat', 'admin_chat')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fromUserId', $fromUserId, PDO::PARAM_INT);
        $stmt->bindParam(':toUserId', $toUserId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':ticketNumber', $ticketNumber, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Get user details by ID
    public function getUserById($userId) {
        $query = "SELECT id, username, email FROM users WHERE id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>