<?php

namespace App\Models;

if (!class_exists(View_page_model::class, false)) {
    foreach (array('View_page_model.php', 'view_page_model.php') as $fileName) {
        $modelFile = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($modelFile)) {
            require_once $modelFile;
            break;
        }
    }
}

/**
 * Model for the View Page UI screen only.
 * Device theme, schema, and the linked-page preview are loaded through this class.
 */
class View_page_ui_model extends View_page_model
{
}
