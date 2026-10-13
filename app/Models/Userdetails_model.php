<?php

namespace App\Models;


class Userdetails_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'users';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        
        $users_table = $this->db->prefixTable('users');
        $id = get_array_value($options, "id");
        $where='';
        if ($id) {
            $where .= "  $users_table.id=$id";
        
    $sql = "SELECT $users_table.*, $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
    $users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table WHERE $where";
        }
        else{
            $sql = "SELECT $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
    $users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table";
        }
return $this->db->query($sql);
}
function get_userdetails($options) {
        echo $id;
    $users_table = $this->db->prefixTable('users');
    $id = $options;
    $where='';
    if ($id) {
        $where .= "  $users_table.id=$id";
    
$sql = "SELECT $users_table.*
FROM $users_table WHERE $where";
    }
    else{
        $sql = "SELECT $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
$users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table";
    }
return $this->db->query($sql);
}

function get_rooms($options = array()) {
        
    $users_table = $this->db->prefixTable('rooms');
    $id = get_array_value($options, "id");
    $where='';
    if ($id) {
        $where .= "  $users_table.user_id=$id";
    
$sql = "SELECT $users_table.*, $users_table.id AS id, $users_table.roomname AS room_name, $users_table.gateway_id AS gateway
FROM $users_table WHERE $where";
    }
    else{
        $sql = "SELECT $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
$users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table";
    }
return $this->db->query($sql);
}
function get_gateway($options = array()) {
        
    $users_table = $this->db->prefixTable('gateway');
    $id = get_array_value($options, "id");
    $where='';
    if ($id) {
        $where .= "  $users_table.user_id=$id";
    
$sql = "SELECT $users_table.*
FROM $users_table WHERE $where";
    }
    else{
        $sql = "SELECT $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
$users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table";
    }
return $this->db->query($sql);
}
function get_enddevice($options = array()) {
        
    $users_table = $this->db->prefixTable('mytable');
    $id = get_array_value($options, "id");
    $where='';
    if ($id) {
        $where .= "  $users_table.user_id=$id";
    
$sql = "SELECT $users_table.*
FROM $users_table WHERE $where";
    }
    else{
        $sql = "SELECT $users_table.id AS id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS name, $users_table.email AS email, $users_table.phone AS phone, 
$users_table.address AS address,$users_table.lat AS lat,$users_table.lng AS lng
FROM $users_table";
    }
return $this->db->query($sql);
}
}

