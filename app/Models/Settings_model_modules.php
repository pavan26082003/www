<?php

namespace App\Models;

class Settings_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'settings';
        parent::__construct($this->table);
    }

    function get_setting($setting_name) {
        $result = $this->db_builder->getWhere(array('setting_name' => $setting_name), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->setting_value;
        }
    }



    function save_setting($setting_name, $setting_value, $type = "app") {
        $fields = array(
            'setting_name' => $setting_name,
            'setting_value' => $setting_value
        );

        $exists = $this->get_setting($setting_name);
        if ($exists === NULL) {
            $fields["type"] = $type; //type can't be updated

            return $this->db_builder->insert($fields);
        } else {
            $this->db_builder->where('setting_name', $setting_name);
            $this->db_builder->update($fields);
        }
    }
 
//find all app settings and login user's setting
    //user's settings are saved like this: user_[userId]_settings_name;
    function get_all_required_settings($user_id = 0) {
        $settings_table = $this->db->prefixTable('settings');
        $sql = "SELECT $settings_table.setting_name,  $settings_table.setting_value
        FROM $settings_table
        WHERE $settings_table.deleted=0 AND ($settings_table.type = 'app' OR ($settings_table.type ='user' AND $settings_table.setting_name LIKE 'user_" . $user_id . "_%'))";
        return $this->db->query($sql);
    }

public function extract_node_ids($user_id)
{
    $sql = "
        SELECT node_id 
        FROM efactorproduct
        WHERE user_id = 'NULL' 
    ";

    return $this->db->query($sql)->getResultArray();
}


public function assigned_node_ids($user_id)
{ 
    $sql = "
        SELECT ef.node_id, ef.user_id, u.user_type, u.email, u.phone
        FROM efactorproduct ef
        JOIN users u ON ef.user_id = u.id
        WHERE ef.user_id != 'NULL'
    "; 
    return $this->db->query($sql)->getResultArray();
}

function save_modules($data) {
    $table = $this->db->prefixTable('efactorproduct');
    $results = [];
 
    $userType = $data['userType'];
    $userID = $data['userID'];
    $serialSelection = $data['serialSelection'];
    $manualSelection = $data['manualSelection'];
    $node_id_1 = $data['node_id_1'];   
    $node_id_2 = $data['node_id_2'];
    $manual_node_id = $data['manual_node_id'];
 if($serialSelection == 'on'){ 
$last_5_digits_1 = substr($node_id_1, -5);  
$last_5_digits_2 = substr($node_id_2, -5);  
 
$start_value = hexdec($last_5_digits_1);
$end_value = hexdec($last_5_digits_2);
 
$increment_count = $end_value - $start_value;
 
for ($i = 0; $i <= $increment_count; $i++) { 

    $incremented_value = $start_value + $i;
    $incremented_value_hex = strtoupper(dechex($incremented_value));
    $incremented_value_hex = str_pad($incremented_value_hex, 5, '0', STR_PAD_LEFT);
    $new_node_id_1 = substr($node_id_1, 0, strlen($node_id_1) - 5) . $incremented_value_hex;
    if($userType == 'User'){
    $sql = "UPDATE `efactorproduct` SET 
    assigned_to = CONCAT('$userID', '(U)')  
    WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Client'){
    $sql = "UPDATE `efactorproduct` SET 
    assigned_to = CONCAT('$userID', '(C)')  
    WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Developer'){
    $sql = "UPDATE `efactorproduct` SET
    assigned_to = CONCAT('$userID', '(D)')    
    WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Manufacturer'){
    $sql = "UPDATE `efactorproduct` SET 
    assigned_to = CONCAT('$userID', '(M)')  
    WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }
     echo $sql;
    //echo "Updating node_id: $new_node_id_1\n";
}

 }else if($manualSelection == 'on'){
 foreach ($manual_node_id as $new_node_id_1) {
if($userType == 'User'){
     $sql = "UPDATE `efactorproduct` SET 
            assigned_to = CONCAT('$userID', '(U)')  
            WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Client'){
     $sql = "UPDATE `efactorproduct` SET 
            assigned_to = CONCAT('$userID', '(C)')  
            WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Developer'){
     $sql = "UPDATE `efactorproduct` SET 
            assigned_to = CONCAT('$userID', '(D)')  
            WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }else if($userType == 'Manufacturer'){
     $sql = "UPDATE `efactorproduct` SET  
            assigned_to = CONCAT('$userID', '(M)')  
            WHERE node_id = '$new_node_id_1'";
    
    $result = $this->db->query($sql);
    
    }
}
 } 
 
    $result = $this->db->query($sql);

    if (!$result) {
        log_message('error', 'Database error: ' . $this->db->error()['message']);
        $results[] = false;
    } else {
        $results[] = true;
    }

    return $results;
}
   
    
}
