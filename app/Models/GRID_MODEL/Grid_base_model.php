<?php

namespace App\Models\GRID_MODEL;

use App\Models\Crud_model;

/**
 * Grid_base_model
 * Base model for all GRID Professional Platform models.
 * Connects directly to the dedicated 'grid_db' database connection group.
 */
class Grid_base_model extends Crud_model {

    function __construct($table = null) {
        parent::__construct($table);
        $this->db = db_connect('grid');
        $this->db->query("SET sql_mode = ''");
        if ($table) {
            $this->use_table($table);
        }
    }
}
