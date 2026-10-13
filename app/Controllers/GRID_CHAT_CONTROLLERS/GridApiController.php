<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

use CodeIgniter\RESTful\ResourceController;

class GridApiController extends ResourceController
{
    public function me()
    {
        $session = session();

        // Read the user ID from the EXISTING authenticated session.
        // Do NOT accept user_id from the frontend.
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ]);
        }

        // Connect to the existing factor database (read-only query)
        try {
            $db = \Config\Database::connect('factor_db1');
            $db->initialize();
        } catch (\Throwable $e) {
            $db = \Config\Database::connect();
        }

        $builder = $db->table('users');

        // Select ONLY the fields required by GRID (no user_id exposed)
        $builder->select('grid_id, first_name, last_name');

        $builder->where('id', $userId);
        $builder->where('status', 'active');
        $builder->where('deleted', 0);

        $user = $builder->get()->getRow();

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'User not found.'
                ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'grid_id'    => $user->grid_id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name
            ]
        ]);
    }
}
