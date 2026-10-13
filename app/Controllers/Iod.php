<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Iod extends Controller
{
    public function index()
    {
        $uid = $this->request->getGet('UID');

        echo "UID Received: " . $uid;
    }
}

