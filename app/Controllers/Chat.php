<?php
class ChatController {
    private $model;
    private $adminId = 1; // Fixed admin user ID

    function __construct() {
        parent::__construct();
        $this->Custom_widgets_model = model('App\Models\Custom_widgets_model');
    }






    public function index() {


 return $this->template->rander("Chat/index");
    


}







    public function handleRequest() {
        $action = $_GET['action'] ?? 'listUsers';

        switch ($action) {
            case 'listUsers':
                $this->listUsers();
                break;
            case 'viewChat':
                $userId = $_GET['user_id'] ?? 0;
                $this->viewChat($userId);
                break;
            case 'sendMessage':
                $this->sendMessage();
                break;
            default:
                $this->listUsers();
                break;
        }
    }

    private function listUsers() {
        $users = $this->model->getAllChatUsers();
        include 'view.php'; // This will show the user list
    }

    private function viewChat($userId) {
        if (!$userId) {
            header("Location: ?action=listUsers");
            exit;
        }

        $user = $this->model->getUserById($userId);
        if (!$user) {
            header("Location: ?action=listUsers");
            exit;
        }

        $messages = $this->model->getChatHistory($this->adminId, $userId);
        include 'view_chat.php'; // This will show the chat interface
    }

    private function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? 0;
            $message = $_POST['message'] ?? '';
            $ticketNumber = $_POST['ticket_number'] ?? null;

            if ($userId && $message) {
                $this->model->sendMessage($this->adminId, $userId, $message, $ticketNumber);
            }

            header("Location: ?action=viewChat&user_id=$userId");
            exit;
        }
    }
}


?>