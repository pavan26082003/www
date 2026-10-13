<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\StickerModel;

class Sticker extends Controller
{
    // ================= Save Fields =================
    public function saveFields()
    {
        $request = service('request');
        $data = $request->getJSON(true);

//print_r($data);

        if (!$data || !is_array($data)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No data received or invalid format'
            ])->setStatusCode(400);
        }

        $stickerModel = new StickerModel();

        $modelname = $data['modelname'] ?? '';
       // $user_id   = $data['user_id'] ?? session()->get('user_id');
        $user_id   = '177';

        // Check if this modelname already exists
        $existing = $stickerModel->where('modelname', $modelname)->first();

        // JS se comma-separated data
        $field_name   = $data['field_name'] ?? '';
        $field_value  = $data['field_value'] ?? '';
        $input_type   = $data['input_type'] ?? '';
        $keyword_type = $data['keyword_type'] ?? '';

        if ($existing) {
            // Update existing row
            $stickerModel->update($existing['id'], [
                'field_name'   => $field_name,
                'field_value'  => $field_value,
                'input_type'   => $input_type,
                'keyword_type' => $keyword_type,
                'user_id'      => $user_id,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            $stickerId = $existing['sticker_id'];
            $message = 'Existing sticker form updated successfully!';
        } else {
            // Insert new row
            $stickerId = time(); // unique sticker_id
            $stickerModel->insert([
                'sticker_id'   => $stickerId,
                'field_name'   => $field_name,
                'field_value'  => $field_value,
                'input_type'   => $input_type,
                'keyword_type' => $keyword_type,
                'user_id'      => $user_id,
                'modelname'    => $modelname,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            $message = 'New sticker form saved successfully!';
        }

        return $this->response->setJSON([
            'status'      => 'success',
            'message'     => $message,
            'sticker_id'  => $stickerId,
            'field_name'  => $field_name,
            'field_value' => $field_value
        ]);
    }

    // ================= Fetch Fields =================
    public function getFields()
    {
        $stickerModel = new StickerModel();
        $fields = $stickerModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'fields' => $fields
        ]);
    }

    // ================= Sticker Form View =================
    public function sticker_form($id = null)
    {
        $modelno   = $this->request->getGet('modelno');
        $modelname = $this->request->getGet('modelname');
        $category  = $this->request->getGet('category');

        return view('items/sticker_form', [
            'id'        => $id,
            'modelno'   => $modelno,
            'modelname' => $modelname,
            'category'  => $category
        ]);
    }

    // ================= View Sticker Form =================
    public function viewStickerForm($modelname)
    {
        $stickerModel = new StickerModel();
        $form = $stickerModel->where('modelname', $modelname)->first();

        return view('items/view_sticker_form', [
            'form' => $form
        ]);
    }
}
