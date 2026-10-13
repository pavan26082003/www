<?php

namespace App\Controllers;

use App\Libraries\Imap;

class Settings extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
        app_redirect('settings/general');
    }

    function general() {
        $tzlist = \DateTimeZone::listIdentifiers();
        $view_data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $view_data['timezone_dropdown'][$zone] = $zone;
        }

        $view_data['language_dropdown'] = get_language_list();

        $view_data["currency_dropdown"] = get_international_currency_code_dropdown();
         $this->efactor_modules();
        return $this->template->rander("settings/general", $view_data);
    }

public function efactor_modules()
{
    session_start();
    $user_id = $_SESSION['user_id']; 
      
    $nodes = $this->Settings_model->extract_node_ids($user_id); 
     $assigned_node = $this->Settings_model->assigned_node_ids($user_id); 
 
    return view('settings/general', [
        'nodes' => $nodes,
        'assigned_node' => $assigned_node  
    ]); 
}

  
function save_modules() {
 
    session_start();
    $user_id = $_SESSION['user_id']; 
    
            $item_data = array(
            "userType" => $this->request->getPost('userType'), 
            "userID" => $this->request->getPost('userID'),
            "serialSelection" => $this->request->getPost('serialSelection'),
            "manualSelection" => $this->request->getPost('manualSelection'),
            "node_id_1" => $this->request->getPost('node_id_1'),
            "node_id_2" => $this->request->getPost('node_id_2'),
            "manual_node_id" => $this->request->getPost('manual_node_id'), 
        );
     
   
    echo "Data saved successfully!";
    
    echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";
     
    $item_data = $this->Settings_model->save_modules($item_data); 
    
    }
    
    
}

/* End of file general_settings.php */
/* Location: ./app/controllers/general_settings.php */
