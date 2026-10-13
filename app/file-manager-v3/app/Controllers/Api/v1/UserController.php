<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Repositories\UserRepository;

class UserController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function search(): void
    {
        try {
            $userId = $this->getUserId();
            $query = isset($_GET['q']) ? trim($_GET['q']) : '';

            if (strlen($query) < 2) {
                $this->success(['users' => []], 'No search query provided');
                return;
            }

            $users = $this->userRepository->search($query, $userId);
            
            foreach ($users as &$user) {
                unset($user['password_hash']);
                unset($user['storage_used']);
                unset($user['storage_limit']);
            }
            
            $this->success(['users' => $users], 'Users found');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}