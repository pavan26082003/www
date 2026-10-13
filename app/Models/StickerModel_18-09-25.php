<?php

namespace App\Models;

use CodeIgniter\Model;

class StickerModel extends Model
{
    protected $table      = 'sticker_fields';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'sticker_id',    // ✅ unique form ID
        'field_name',    // "Sticker Form"
        'field_value',   // all keywords in single line
        'input_type',    // manual / fixed
        'keyword_type',  // constant / fixed
        'user_id',       // optional
        'modelname',     // optional
        'created_at'     // manually set
    ];

    public $timestamps = false; // hum khud created_at set kar rahe hain
}
