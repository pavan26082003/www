<?php

namespace App\Controllers;

//use WebSocket\Client;

// require 'vendor/autoload.php';
use WebSocket\Client;

class Items extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");
    }

    protected function validate_access_to_items() {
        $access_invoice = $this->get_access_info("invoice");
        $access_estimate = $this->get_access_info("estimate");

        //don't show the items if invoice/estimate module is not enabled
        if (!(get_setting("module_invoice") == "1" || get_setting("module_estimate") == "1" )) {
            app_redirect("forbidden");
        }

        if ($this->login_user->is_admin) {
            return true;
        } else if ($access_invoice->access_type === "all" || $access_estimate->access_type === "all") {
            return true;
        } else {
            app_redirect("forbidden");
        }
    }
    
    protected function validate_access_to_items2() {
    $access_invoice = $this->get_access_info("invoice");
    $access_estimate = $this->get_access_info("estimate");

    // Check if either invoice module or estimate module is enabled
    if (!(get_setting("module_invoice") == "1" || get_setting("module_estimate") == "1" )) {
        app_redirect("forbidden"); // Redirect to forbidden page if neither module is enabled
    }

    // Check if the logged-in user is an admin
    if ($this->login_user->is_admin) {
        return true; // Admin has access
    } 
    // Check access type for invoice or estimate
    else if ($access_invoice->access_type === "all" || $access_estimate->access_type === "all") {
        return true; // User has all access to either invoice or estimate
    } 
    // Check if any logged-in user has access
    else if ($this->login_user->id) {
        return true; // User is logged in
    } 
     else if ($this->login_user->id) {
        return true; // User is logged in
    } 
    else {
        app_redirect("forbidden"); // Redirect to forbidden page for other cases
    }
}
    //load items list view
    function index() {
        //$this->access_only_team_members();
        //$this->validate_access_to_items();

        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/index", $view_data);
    }

    function vnsettings() {

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/vnsettings", $view_data);

     }


      function mapped() {

session_start();
$user_id = $_SESSION['user_id'];

      
$brand_data = $this->Items_model->get_brand_products($user_id)->getResult();

 /*         
$result = array();
        foreach ($brand_data as $data) {
            $result[] = $this->_make_item_row61($data);
           //  print_r($result);
             
        }
        echo json_encode(array("data" => $result));


*/


      

          return $this->template->rander("items/mapped", $brand_data);

     }




     function ajax() {




     	$title=$this->request->getPost('title');

     	$item_info = $this->Items_model->get_modeldetails($title)->getRow();

     	return $item_info->modelno;
     }

      function ajax1() {
     	$title=$this->request->getPost('title');

     	$item_info = $this->Items_model->get_uiddetails($title)->getRow();

     	return $item_info->uid;
     	
     }

     function client() {



//$view_data['categories_dropdown'] = $this->_get_categories_dropdown();

       // return $this->template->rander("items/index", $view_data);





     //    $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

      //  return $this->template->rander("items/client", $view_data);

        return $this->template->rander("items/index", $view_data);

     }

      function vendor() {

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/vendor", $view_data);

     }

/*
public function mapped() {
        // Get brand products where vendor_name is DHRIO
        $data['brand_products'] = $this->items_model->get_brand_products();
        
        // Load view
        $this->load->view('mapped', $data);
    }
*/

function getUserIdFromSession() {

        session_start();
  
    if (isset($_SESSION['user_id'])) {
                return $_SESSION['user_id'];
    } else {
                return null;
    }
}



function brand() {

$view_data['client_id'] =$this->login_user->client_id;
$view_data['categories_dropdown'] = $this->_get_categories_dropdown();
return $this->template->rander("items/brand", $view_data);

     }


function modal_formbrand() {
       

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['brand_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));
        return $this->template->view('items/modal_formbrand', $view_data);



    }


    function modal_formbrand_edit($itemid = 0) {

        $view_data['itemid']=$itemid;
      //  $view_data['model_info'] = $this->Items_model->get_two($itemid);
        $view_data['brand_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));
        $api_details_result = $this->Items_model->get_two($itemid);
 
if ($api_details_result) {
        $api_details_row = $api_details_result->getRow();

        if ($api_details_row) {
            $view_data['name'] = $api_details_row->name;
           
            
            $image_filename = $api_details_row->brandlogo;
       // $image_url = 'http://localhost/factor_17-05/files/brandLogo/' . $image_filename;  
      $image_url = 'https://efactor.tech/files/brand_logo/' . $image_filename;
          
        $view_data['brandlogo'] = $image_url;
        }
    }

        return $this->template->view('items/modal_formbrand_edit', $view_data);
    }

    function modal_formcat_edit($itemid = 0) {

        $view_data['itemid']=$itemid;
      //  $view_data['model_info'] = $this->Items_model->get_two($itemid);
      $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));
        $api_details_result = $this->Items_model->get_cattwo($itemid);
 
if ($api_details_result) {
        $api_details_row = $api_details_result->getRow();

        if ($api_details_row) {
            $view_data['name'] = $api_details_row->name;
           
            
            $image_filename = $api_details_row->Product_image;
     //   $image_url = 'http://localhost/factor_17-05/files/brandLogo/' . $image_filename;  
        $image_url = 'https://efactor.tech/files/brand_logo/' . $image_filename;
          
        $view_data['prodimg'] = $image_url;
        }
    }

        return $this->template->view('items/modal_formcat_edit', $view_data);
    }




function product() {

         $view_data['client_id'] =$this->login_user->client_id;

        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/product", $view_data);

     }
     
     

/*----------------------select_app----------------------------*/







function display($modelNumber, $pageNo, $category, $node_id, $user_id)
{
    if(empty($node_id)){
        $components = $this->Items_model->getUiComponentsByModelAndPage($modelNumber, $pageNo, $user_id);
        $userID = null;
        
        if (!empty($components)) {


            
            foreach ($components as $component) {
                
                $command_to_find = 'Data';
        
                $cmd_names = explode(',', $component['cmd_fielddescription']);
                $ui_types = explode(',', $component['cmd_uitype']);
                $ui_permissions= explode(',', $component['cmd_uidescriptioninout']);
          $ui_step_value=explode(',',$component['cmd_sliderstepvalue']);
          $ui_range_min_value=explode(',',$component['cmd_displayvaluemin']);
          $ui_range_max_value=explode(',',$component['cmd_displayvaluemax']);
          $ui_datatypes=explode(',',$component['cmd_datatype']);
          $UserID=$component['user_id'];
                
                $position = array_search($command_to_find, $cmd_names);
                
                
                if ($position !== false && isset($ui_step_value[$position])) {
                    $ui_step = $ui_step_value[$position];
                    
                }
                    
                if ($position !== false && isset($ui_range_min_value[$position])) {
                    $ui_range_min = $ui_range_min_value[$position];
                    
                }
                
                if ($position !== false && isset($ui_range_max_value[$position])) {
                    $ui_range_max = $ui_range_max_value[$position];
                }
                
                if($position!==false && isset($ui_datatypes[$position])){
                    $ui_datatype=$ui_datatypes[$position];
                }
            
                if ($position !== false && isset($ui_permissions[$position])) {
                    $ui_permission = $ui_permissions[$position];
                }
                
                if ($position !== false && isset($ui_types[$position])) {
                    $cmd_ui = $ui_types[$position];
        
        } else {
        
        }
        }
        } else {
        echo "No components found.";
        
        }
         $compt = [];
        
            return view('items/page', [
                'components' => $components,
                'modelNumber' => $modelNumber,
                'pageNo' => $pageNo,
                'userID' => $UserID,
                'compt' => $compt,
                 
            ]); 
    }else{


 $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes_app&node_id=' . $node_id; // Construct the URL
 $response = file_get_contents($url);
 $additionalData = [
    'node_id' => $node_id,
    'model_name' => $modelNumber,
    'page_names' => $pageNo,
    'userID' => $UserID,
];
$components['additionalData'] = $additionalData;



  
    return view('items/page', [
        'compt' => $components,  
        'response' => $response,
        'userID' => $user_id,  
    ]);   
} 
    }




/*-----------------------------------------------------*/

/*

public function display($modelNumber, $pageNo, $category, $node_id, $user_id)
{
    // Enable error reporting during development (remove in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    log_message('debug', '--- Entered display() function ---');
    log_message('debug', "Parameters: modelNumber = $modelNumber, pageNo = $pageNo, category = $category, node_id = $node_id, user_id = $user_id");

    if (empty($node_id)) {
        log_message('debug', 'Node ID is empty, loading components from database...');

        $components = $this->Items_model->getUiComponentsByModelAndPage($modelNumber, $pageNo, $user_id);
        $UserID = null;

        if (!empty($components)) {


     echo 'if';


            log_message('debug', 'Components retrieved from model: ' . print_r($components, true));

            foreach ($components as $component) {
                log_message('debug', 'Processing component: ' . print_r($component, true));

                $cmd_names = explode(',', $component['cmd_fielddescription']);
                $ui_types = explode(',', $component['cmd_uitype']);
                $ui_permissions = explode(',', $component['cmd_uidescriptioninout']);
                $ui_step_value = explode(',', $component['cmd_sliderstepvalue']);
                $ui_range_min_value = explode(',', $component['cmd_displayvaluemin']);
                $ui_range_max_value = explode(',', $component['cmd_displayvaluemax']);
                $ui_datatypes = explode(',', $component['cmd_datatype']);
                $UserID = $component['user_id'];

                $command_to_find = 'Data';
                $position = array_search($command_to_find, $cmd_names);

                if ($position !== false) {
                    $ui_step = $ui_step_value[$position] ?? null;
                    $ui_range_min = $ui_range_min_value[$position] ?? null;
                    $ui_range_max = $ui_range_max_value[$position] ?? null;
                    $ui_datatype = $ui_datatypes[$position] ?? null;
                    $ui_permission = $ui_permissions[$position] ?? null;
                    $cmd_ui = $ui_types[$position] ?? null;

                    log_message('debug', "Matched component at position $position: Step=$ui_step, Min=$ui_range_min, Max=$ui_range_max, Type=$ui_datatype, UI=$cmd_ui, Permission=$ui_permission");
                } else {
                    log_message('debug', "'Data' not found in cmd_fielddescription");
                }
            }
        } else {
            log_message('error', 'No components found for given model and page.');
            echo "No components found.";
        }

        $compt = [];

        log_message('debug', 'Loading view with components.');
        return view('items/page', [
            'components' => $components,
            'modelNumber' => $modelNumber,
            'pageNo' => $pageNo,
            'userID' => $UserID,
            'compt' => $compt,
        ]);
    } else {
        log_message('debug', 'Node ID is provided, loading from external API.');

echo 'else';

        $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes_app&node_id=' . $node_id;
        log_message('debug', "Calling API: $url");

        try {
            $response = file_get_contents($url);

            if ($response === false) {
                log_message('error', "API call failed: $url");
                $response = 'API call failed';
            } else {
                log_message('debug', "API Response: $response");
            }
        } catch (Exception $e) {
            log_message('error', 'Exception while calling API: ' . $e->getMessage());
            $response = 'Exception: ' . $e->getMessage();
        }

        $additionalData = [
            'node_id' => $node_id,
            'model_name' => $modelNumber,
            'page_names' => $pageNo,
        ];

        $components['additionalData'] = $additionalData;

        log_message('debug', 'Loading view with API response.');
        return view('items/page', [
            'compt' => $components,
            'response' => $response,
            'userID' => $user_id,
        ]);
    }

    log_message('debug', '--- End of display() function ---');
}





*/




/*----------------------------------------------------*/

/*
    function display($modelNumber, $pageNo, $category, $node_id, $user_id)
{
 
 $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes_app&node_id=' . $node_id; // Construct the URL
 $response = file_get_contents($url);
$additionalData = [
    'node_id' => $node_id,
    'model_name' => $modelNumber,
    'page_names' => $pageNo,
];
$components['additionalData'] = $additionalData;
 
    return view('items/page', [
        'components' => $components, 
        'response' => $response, 
        'userID' => $user_id,  
    ]);   
    
    }
*/








    function controldisplay($modelNumber, $pageNo, $node_id)
    {

session_start();
    $user_id = $_SESSION['user_id'];



     $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes_app&node_id=' . $node_id; // Construct the URL
     $response = file_get_contents($url);
     
    $additionalData = [
        'node_id' => $node_id,
        'modelNumber' => $modelNumber,
        'pageNo' => $pageNo,
        'userID' => $user_id
        
    ];


 
    $components['additionalData'] = $additionalData;
   // 'response' => $response,  
    return view('items/control_page', [
        'components' => $components, 
        'response' => $response, 
    ]);

        
        }



/*--------------------------------------------------------------*/
/*


function display($modelNumber, $pageNo, $user_id)
{
    // Get UI components from the model
    $components = $this->Items_model->getUiComponentsByModelAndPage($modelNumber, $pageNo, $user_id);

    $userID = null;

    if (!empty($components)) {
        // Establish WebSocket connection
        try {
            $client = new Client("ws://efactor.tech:33060");

            // Send an initial message to the server
            $client->send("Hello WebSocket Server!");

            echo "Connected to WebSocket server.\n";

            while (true) {
                foreach ($components as $component) {
                    $command_to_find = 'Data'; // Example command to find, adjust as needed

                    $cmd_names = explode(',', $component['cmd_fielddescription']);
                    $ui_types = explode(',', $component['cmd_uitype']);
                    $ui_permissions = explode(',', $component['cmd_uidescriptioninout']);
                    $ui_step_value = explode(',', $component['cmd_sliderstepvalue']);
                    $ui_range_min_value = explode(',', $component['cmd_displayvaluemin']);
                    $ui_range_max_value = explode(',', $component['cmd_displayvaluemax']);
                    $ui_datatypes = explode(',', $component['cmd_datatype']);
                    $UserID = $component['user_id'];

                    $position = array_search($command_to_find, $cmd_names);

                    $ui_step = $position !== false && isset($ui_step_value[$position]) ? $ui_step_value[$position] : null;
                    $ui_range_min = $position !== false && isset($ui_range_min_value[$position]) ? $ui_range_min_value[$position] : null;
                    $ui_range_max = $position !== false && isset($ui_range_max_value[$position]) ? $ui_range_max_value[$position] : null;
                    $ui_datatype = $position !== false && isset($ui_datatypes[$position]) ? $ui_datatypes[$position] : null;
                    $ui_permission = $position !== false && isset($ui_permissions[$position]) ? $ui_permissions[$position] : null;
                    $cmd_ui = $position !== false && isset($ui_types[$position]) ? $ui_types[$position] : null;

                    if ($position !== false) {
                        // Receive the WebSocket response
                        try {
                            $webSocketResponse = $client->receive();

                            // Print and process the WebSocket response
                            echo "Received from server: " . $webSocketResponse . "\n";

                            echo "The UI type for '$command_to_find' in component ID {$component['id']} is '$cmd_ui' paramname {$component['parameter_name']} 
                            permission $ui_permission ui_range_min $ui_range_min ui_range_max $ui_range_max ui_step $ui_step ui_datatype $ui_datatype.  $UserID<br>";

                            // Process the received data as needed, e.g., updating the UI or database
                            
                        } catch (ConnectionException $e) {
                            echo "WebSocket connection warning: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "Command '$command_to_find' not found or UI type is undefined for component ID {$component['id']}<br>";
                    }
                }

                // Sleep for a short duration to avoid busy-waiting and allow continuous reading
                usleep(100000); // 100 milliseconds
            }

            // Close WebSocket connection after processing
            $client->close();
        } catch (Exception $e) {
            echo "WebSocket connection failed: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "No components found.<br>";
    }

    // Return the view with components (if you still need to render the page after processing)
    return view('items/page', [
        'components' => $components,
        'modelNumber' => $modelNumber,
        'pageNo' => $pageNo,
        'userID' => $UserID
    ]);
}



*/


/*------------------------------------------------------------*/
function selectModel($userId, $modelno, $category)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelsWithPages = $this->Items_model->getModelsWithPagesByUserId($user_id, $modelno, $category);
    $Product_image = $this->Items_model->getCatimgWithModelsByUserId($user_id, $modelno, $category);
    //return view('items/select_model', ['modelsWithPages' => $modelsWithPages]);
    $modelNumber = $modelno;
    $pageNo = '';
    $node_id = $this->Items_model->selecteddisplay($modelNumber, $pageNo, $category, $user_id);

    return view('items/select_model', [
        'modelsWithPages' => $modelsWithPages,
        'category' => $category,
        'cat_image' => $Product_image,
        'user_id' => $user_id,
        'node_id' => $node_id
    ]);
}

function controlselectModel($model_number, $node_id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
 
    $modelsWithPages = $this->Items_model->controlgetModelsWithPagesByUserId($model_number, $node_id);

    return view('items/control_select_model', [
        'modelsWithPages' => $modelsWithPages,
        'node_id' => $node_id  
    ]);
}

function selectConfirmModel($userId, $modelno)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelsWithPages = $this->Items_model->getConfirmModelsWithPagesByUserId($user_id, $modelno);
    $modelsWithPages2 = $this->Items_model->getConfirmModelsWithPagesByUserId2($user_id, $modelno);
    

    $confirmResult = $this->Items_model->Confirm($modelsWithPages,$modelsWithPages2);

   if ($confirmResult === "Parameters updated successfully") {
        return redirect()->to(site_url('items/vendor'))->with('success', "Parameters updated successfully.");
    } elseif($confirmResult === "Parameters already updated") {
        return redirect()->to(site_url('items/vendor'))->with('error', "Parameters already updated");
    }elseif ($confirmResult === true){
        return redirect()->to(site_url('items/vendor'))->with('success', "Table created successfully");
    }elseif ($confirmResult === false){
        return redirect()->to(site_url('items/vendor'))->with('error', "Unable to create table");
    }else {
return false;
    }
}


function pagesave($value)
{


redirect('https://efactor.tech/mqtt.php');

/*$input = json_decode($this->input->raw_input_stream, true);

        // Extract parameters
        $paramName = isset($input['paramName']) ? $input['paramName'] : null;
        $value = isset($input['value']) ? $input['value'] : null;

        if ($paramName && $value !== null) {
            // Save the value using the model
            //$this->Item_model->saveValue($paramName, $value);

            // Print the values
           echo "paramName: $paramName, value: $value";

            // Redirect to the specified URL
            redirect('https://efactor.tech/mqtt.php');
        } else {
            // Send an error response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Invalid request data.']))
                ->set_status_header(400);
        }

*/



}






/***********************************************************/

/*function selectId($userId)
{

session_start();
$user_id = $_SESSION['user_id'];

$modelsWithIds = $this->Items_model->getModelsWithIdsByUserId($user_id);

echo $modelsWithIds;

return view('items/select_model', ['modelsWithPages' => $modelsWithIds]);

}*/


/*
public function selectId($userId)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelsWithIds = $this->Items_model->getModelsWithIdsByUserId($userId); 
    
    if ($modelsWithIds !== false) {
        
        return view('items/select_model', ['modelsWithIds' => $modelsWithIds]);
    } else {
        
        return view('error_view');
    }
}
*/



 /*

public function selectId($userId)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelsWithIds = $this->Items_model->getModelsWithIdsByUserId($userId); 
    
    if ($modelsWithIds !== false) {
        foreach ($modelsWithIds->getResultArray() as $model) {
            echo $model['node_id'] . '<br>'; // Assuming 'node_id' is the key for your ID field

       
        }
    } else {
        echo "No models found for the user.";
    }

return view('items/select_model', $data);
}
*/


public function selectId($userId)
{


        
    $modelsWithIds = $this->Items_model->getModelsWithIdsByUserId($userId);
    $modelwithnos = $this->Items_model->getModelnos($userId);
    $modelwithproducts = $this->Items_model->getproductname($userId);
   // $modelwithparameter = $this->Items_model->getparametername($userId); 
    
    if ($modelsWithIds !== false) {
        $data['modelIds'] = [];
        foreach ($modelsWithIds->getResultArray() as $model) {
            $data['modelIds'][] = $model['node_id']; 
        }
    } 
/*
    if ($modelwithnos !== false) {
        $data['modelnos'] = [];
        foreach ($modelwithnos->getResultArray() as $model) {
            $data['modelnos'][] = $model['model_name']; 
        }
        if (!in_array("EFDEFLT V1.0", $data['modelnos'])) {
            $data['modelnos'][] = "EFDEFLT V1.0";
        }
    } */
    $data['models'] = []; 
    if ($modelwithnos !== false) {
        foreach ($modelwithnos->getResultArray() as $model) {
            $data['models'][$model['model_name']] = $model['product_name']; 
        }
        if (!array_key_exists('EFDEFLT V1.0', $data['models'])) {
            $data['models']['EFDEFLT V1.0'] = 'input-output'; 
        }
    }



     if ($modelwithproducts !== false) {
        $data['modelproducts'] = [];
        foreach ($modelwithproducts->getResultArray() as $model) {
            $data['modelproducts'][] = $model['product_name']; 
        }
    } 
  
         else {
      //  echo "No models found for the user.";
        return;     }

    return view('items/select_device_model', $data);
}










/***********************************************************/

public function modal_config(){
    session_start();
    $user_id = $_SESSION['user_id']; 
      
    $selectedmodelNumber = $this->request->getVar('modelNumber');
    $pageNo = $this->request->getVar('pageNo');
   // echo $selectedmodelNumber."selected model here";
   //  echo $pageNo."selected page here";
    $model_names = $this->Items_model->extract_model_name($user_id,$selectedmodelNumber,$pageNo);
    $page_names = $this->Items_model->extract_page_names($user_id,$selectedmodelNumber,$pageNo);
    $node_ids = $this->Items_model->extract_node_id($user_id,$selectedmodelNumber,$pageNo);
    $router_ids = $this->Items_model->extract_router_id($user_id);
    $params = $this->Items_model->extract_parameters($user_id,$selectedmodelNumber,$pageNo);
    $defaults = $this->Items_model->extract_default_names($user_id,$selectedmodelNumber,$pageNo);
 
    return view('items/modal_config', [
        'models' => $model_names, 
        'pageNo' => $pageNo, 
        'pageNames' => $page_names, 
        'nodeIds' => $node_ids,
        'router_Ids' => $router_ids,
        'params' => $params,
        'defaults' => $defaults,
        'selectedmodelNumber' => $selectedmodelNumber,
    ]); 
}

public function modal_paring_config(){
    session_start();
    $user_id = $_SESSION['user_id']; 
      
    $selectedmodelNumber = $this->request->getVar('modelNumber');
    $pageNo = $this->request->getVar('pageNo');
    $model_names = $this->Items_model->extract_model_name($user_id);
    $page_names = $this->Items_model->extract_page_names($user_id);
    $node_ids = $this->Items_model->extract_node_id($user_id);
    $router_ids = $this->Items_model->extract_router_id($user_id);
    $params = $this->Items_model->extract_parameters($user_id);
    $defaults = $this->Items_model->extract_default_names($user_id);
 
    return view('items/modal_paring_config', [
        'models' => $model_names, 
        'pageNo' => $pageNo, 
        'pageNames' => $page_names, 
        'nodeIds' => $node_ids,
        'router_Ids' => $router_ids,
        'params' => $params,
        'defaults' => $defaults,
        'selectedmodelNumber' => $selectedmodelNumber,
    ]); 
}

public function specifications($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');
    $parameter_names = $this->Items_model->extract_modelname_config($id, $user_id);
    return view('items/specifications', [
        'user_id' => $user_id,
        'id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
    ]);
} 

public function modal_pages($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');
    $parameter_names = $this->Items_model->extract_modelname_config($id, $user_id);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno);
    return view('items/modal_pages', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'page_names' => $page_names,
    ]);
}   

public function api_page_linking($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');
    $parameter_names = $this->Items_model->extract_apis($user_id, $modelno);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno);
    return view('items/modal_api_page_linking', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'pages' => $page_names,
    ]);
}

public function auto_test($id){ 
    session_start();
    $user_id = $_SESSION['user_id'];
    $modelno = $this->request->getVar('modelno'); 
    $parameter_names = $this->Items_model->extract_params($id, $user_id);
    return view('items/auto_test', [
        'user_id' => $user_id,
        'id' => $id,
        'modelno' => $modelno,
        'parameters' => $parameter_names,
    ]);
}

public function callDisplayFunction($modelno)
{
    session_start();
    $user_id = $_SESSION['user_id'];

    $category = $this->request->getVar('category');

    return $this->selectModel($user_id, $modelno, $category);
}


public function controlDisplayFunction($model_number)
{
    session_start();
    $user_id = $_SESSION['user_id'];

    $node_id = $this->request->getVar('node_id');
    $userid = $this->request->getVar('userid');
    if ( $user_id == $userid){
        if($model_number == 'EFDEFLT V1.0'){
            $userid = 1;
            return $this->controlselectModel($model_number, $node_id);
        }else{
        return $this->controlselectModel($model_number, $node_id);
        }
    }elseif($user_id == 1){
        if($model_number == 'EFDEFLT V1.0'){
            $userid = 1;
            return $this->controlselectModel($model_number, $node_id);
        }else{
        return $this->controlselectModel($model_number, $node_id);
        }
    }else{
 return false;
    }

  //  return $this->controlselectModel($user_id, $model_number, $node_id);
}




public function ChatDisplayFunction($model_number)
{
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    // Get parameters
    $node_id = $this->request->getVar('node_id');
    $userid = $this->request->getVar('userid') ?? $user_id;

    // Build the URL
    $url = "https://efactor.tech/v8/call_services.php?" . http_build_query([
        'model_number' => $model_number,
        'user_id' => $userid,
        'node_id' => $node_id
    ]);

    // Redirect directly to the URL
    return redirect()->to($url);
}













public function callConfirmDisplayFunction($modelno)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    return $this->selectConfirmModel($user_id, $modelno);
}


function callDisplayId()
    {


session_start();
$user_id = $_SESSION['user_id'];

        return $this->selectId($user_id);
    }



/*function callpageFunction()
    {
        $modelNumber = '32'; // Provide manual value for model number
        $pageNo = 1; // Provide manual value for page number
        return $this->display($modelNumber, $pageNo);
         
    }
*/

public function callpageFunction($modelNumber, $pageNo)
{
session_start();
$user_id = $_SESSION['user_id'];
$category = $this->request->getVar('category');
  
$node_id = $this->Items_model->selecteddisplay($modelNumber, $pageNo, $category, $user_id);

    return $this->display($modelNumber, $pageNo, $category, $node_id, $user_id);
}





public function controlpageFunction($modelNumber, $pageNo)
{
    session_start();
    $user_id = $_SESSION['user_id'];


    // Get the node_id from the query string
    $node_id = $this->request->getVar('node_id');

    // Now $modelNumber, $pageNo, and $node_id are available
    return $this->controldisplay($modelNumber, $pageNo,  $node_id);
}


/**************************************************************/



/**************************************************************/

/*
    function savebrand() {

 //$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];

       $name= $this->request->getPost('name');

          $brand_id = $this->Items_model->save_brand($name, $user_id);

            echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";


 }

*/



/*
function savebrand() {
    session_start();
    $user_id = $_SESSION['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $targetDirectory = "/var/www/html/files/brand_logo/";
     $originalFileName = basename($_FILES["brandlogo"]["name"]);
    $targetFile = $targetDirectory . $originalFileName;

    // Check if the uploaded file exists
    if (!isset($_FILES["brandlogo"])) {
        echo "No file uploaded.";
        exit;
    }
 
    // Check if target directory exists
    if (!file_exists($targetDirectory)) {
        echo "Target directory does not exist.";
        exit;
    }

    // Move the uploaded file to the target directory
    $tempFile = $_FILES["brandlogo"]["tmp_name"];
    if (move_uploaded_file($tempFile, $targetFile)) {
        echo "The file " . htmlspecialchars(basename($_FILES["brandlogo"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
   
    $name = $this->request->getPost('name');
    $brandlogo = $originalFileName;
//http://localhost/factor_17-05/index.php/items/product#
        $result = $this->Items_model->save_brand($name,$brandlogo,$user_id);

    // Check the result
    if ($result !== true) {
       //  location.href='http://localhost/factor_17-05/index.php/items/brand';
                $this->session->setFlashdata('error', $result);
       // echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";
       echo "<script>
     location.href='https://efactor.tech/index.php/items/brand';
   </script>";
    }else{
        echo "<script>
       location.href='https://efactor.tech/index.php/items/brand';
     </script>";
    }
}   */



//STOP
//STOP

//CODE FOR DB SAVE NOT FOR PRINT

// function savebrand() {
//     session_start();
//     $user_id = $_SESSION['user_id'];

//     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//         $targetDirectory = "/var/www/html/files/brand_logo/";

//         // Create directory if missing
//         if (!file_exists($targetDirectory)) {
//             mkdir($targetDirectory, 0755, true);
//         }

//         // Check if file was uploaded
//         if (!isset($_FILES["brandlogo"]) || $_FILES["brandlogo"]["error"] != UPLOAD_ERR_OK) {
//             die("Error: No file uploaded or upload failed.");
//         }

//         // Verify it's an image
//         $check = getimagesize($_FILES["brandlogo"]["tmp_name"]);
//         if ($check === false) {
//             die("Error: File is not an image.");
//         }

//         // Generate a unique filename
//         $fileExtension = pathinfo($_FILES["brandlogo"]["name"], PATHINFO_EXTENSION);
//         $newFileName = uniqid() . '.' . $fileExtension;
//         $targetFile = $targetDirectory . $newFileName;

//         // Move the file
//         if (move_uploaded_file($_FILES["brandlogo"]["tmp_name"], $targetFile)) {
//             $name = $this->request->getPost('name');
//             $brandlogo = $newFileName;
//             $result = $this->Items_model->save_brand($name, $brandlogo, $user_id);

//             if ($result !== true) {
//                 $this->session->setFlashdata('error', $result);
//             }
//             echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";
//         } else {
//             die("Error: Failed to move uploaded file. Check permissions.");
//         }
//     }
// }
//CODE FOR BOTH SAVE & PRINT 
// function savebrand()
// {
//     session_start();
//     $user_id = $_SESSION['user_id'];

//     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//         $targetDirectory = "/var/www/html/files/brand_logo/";

//         // Create directory if missing
//         if (!file_exists($targetDirectory)) {
//             mkdir($targetDirectory, 0755, true);
//         }

//         // Check if file was uploaded
//         if (!isset($_FILES["brandlogo"]) || $_FILES["brandlogo"]["error"] != UPLOAD_ERR_OK) {
//             die("Error: No file uploaded or upload failed.");
//         }

//         // Verify it's an image
//         $check = getimagesize($_FILES["brandlogo"]["tmp_name"]);
//         if ($check === false) {
//             die("Error: File is not an image.");
//         }

//         // Generate a unique filename
//         $fileExtension = pathinfo($_FILES["brandlogo"]["name"], PATHINFO_EXTENSION);
//         $newFileName = uniqid() . '.' . $fileExtension;
//         $targetFile = $targetDirectory . $newFileName;

//         // Move the file
//         if (move_uploaded_file($_FILES["brandlogo"]["tmp_name"], $targetFile)) {
//             // ✅ Save these 3 things in DB exactly like before
//             $name = $this->request->getPost('name');
//             $brandlogo = $newFileName;
//             $result = $this->Items_model->save_brand($name, $brandlogo, $user_id);

//             if ($result !== true) {
//                 $this->session->setFlashdata('error', $result);
//             }

//             // ✅ PRINT - no redirect - print all POST data INCLUDING extra fields
//             echo "<h3 style='color:green;'>✅ Brand Saved Successfully!</h3>";
//             echo "<p>Image saved as: <strong>$newFileName</strong></p>";
//             echo "<h4>📝 Here is what you submitted:</h4>";
//             echo "<table border='1' cellpadding='5'>";

//             // Print the 3 fields saved
//             echo "<tr><td><strong>Name</strong></td><td>" . htmlspecialchars($name) . "</td></tr>";
//             echo "<tr><td><strong>Brandlogo</strong></td><td>" . htmlspecialchars($newFileName) . "</td></tr>";
//             echo "<tr><td><strong>User ID</strong></td><td>" . htmlspecialchars($user_id) . "</td></tr>";

//             // Print all other POST inputs
//             foreach ($_POST as $key => $value) {
//                 if ($key != 'name') {
//                     echo "<tr><td><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
//                 }
//             }

//             echo "</table>";
//         } else {
//             die("Error: Failed to move uploaded file. Check permissions.");
//         }
//     }
// }

// public function savebrand()
// {
//     session_start();
//     $user_id = $_SESSION['user_id'];

//     $brandlogo = null;

//     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//         $targetDirectory = "/var/www/html/files/brand_logo/";

//         // Make sure directory exists
//         if (!file_exists($targetDirectory)) {
//             mkdir($targetDirectory, 0755, true);
//         }

//         // Check if a file was actually uploaded and is valid
//         if (isset($_FILES["brandlogo"]) && $_FILES["brandlogo"]["error"] == UPLOAD_ERR_OK) {
//             // Optional validation: check it's an image
//             $check = getimagesize($_FILES["brandlogo"]["tmp_name"]);
//             if ($check !== false) {
//                 $fileExtension = pathinfo($_FILES["brandlogo"]["name"], PATHINFO_EXTENSION);
//                 $newFileName = uniqid() . '.' . $fileExtension;
//                 $targetFile = $targetDirectory . $newFileName;

//                 if (move_uploaded_file($_FILES["brandlogo"]["tmp_name"], $targetFile)) {
//                     $brandlogo = $newFileName;
//                 } else {
//                     echo "<p style='color:red;'>Error: Failed to move uploaded file. Check permissions.</p>";
//                 }
//             } else {
//                 echo "<p style='color:red;'>Warning: Uploaded file is not an image. Skipping upload.</p>";
//             }
//         }

//         // ✅ Gather all form inputs
//         $data = [
//             'name' => $this->request->getPost('name'),
//             'brandlogo' => $brandlogo,
//             'website_url' => $this->request->getPost('website_url'),
//             'social_media' => $this->request->getPost('social_media'),
//             'server_url' => $this->request->getPost('server_url'),
//             'app_download_links' => $this->request->getPost('app_download_links'),
//             'server_name' => $this->request->getPost('server_name'),
//             'server_type' => $this->request->getPost('server_type'),
//             'server_creation_date' => $this->request->getPost('server_creation_date'),
//             'server_member_count' => $this->request->getPost('server_member_count'),
//             'server_verification_status' => $this->request->getPost('server_verification_status'),
//             'server_invite_link' => $this->request->getPost('server_invite_link'),
//             'brand_description' => $this->request->getPost('brand_description'),
//             'key_products' => $this->request->getPost('key_products'),
//             'headquarters_location' => $this->request->getPost('headquarters_location'),
//             'parent_company' => $this->request->getPost('parent_company'),
//             'contact_email' => $this->request->getPost('contact_email'),
//             'partnerships' => $this->request->getPost('partnerships'),
//             'user_id' => $user_id
//         ];

//         // ✅ Save to DB
//         // $this->db->table('brands')->insert($data);
//         // ✅ Save to DB
// $db = \Config\Database::connect();
// $db->table('brands')->insert($data);


//         // ✅ Confirmation to user
//         echo "<h3 style='color:green;'>✅ Brand Saved Successfully!</h3>";
//         echo "<table border='1' cellpadding='5'>";

//         foreach ($data as $key => $value) {
//             echo "<tr><td><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
//         }

//         echo "</table>";
//     }
// }
// image save to folder file

public function savebrand()
{
    session_start();
    $user_id = $_SESSION['user_id'];

    $brandlogo = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   //  $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . "/files/UPLOADED BRAND-LOGO/";

     $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . "/files/brand_logo/";


        // Create directory if it doesn't exist
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Handle file upload (optional image)
        if (isset($_FILES["brandlogo"]) && $_FILES["brandlogo"]["error"] == UPLOAD_ERR_OK) {
            $check = getimagesize($_FILES["brandlogo"]["tmp_name"]);
            if ($check !== false) {
                $fileExtension = pathinfo($_FILES["brandlogo"]["name"], PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExtension;
                $targetFile = $targetDirectory . $newFileName;

                if (move_uploaded_file($_FILES["brandlogo"]["tmp_name"], $targetFile)) {
                    $brandlogo = $newFileName;
                } else {
                    echo "<p style='color:red;'>❌ Error: Failed to move uploaded file.</p>";
                    exit;
                }
            } else {
                echo "<p style='color:red;'>⚠️ Warning: Uploaded file is not a valid image. Skipping upload.</p>";
            }
        }

        // Collect all form inputs
        $data = [
            'name' => $this->request->getPost('name'),
            'brandlogo' => $brandlogo,
            'website_url' => $this->request->getPost('website_url'),
            'social_media' => $this->request->getPost('social_media'),
            'server_url' => $this->request->getPost('server_url'),
            'app_download_links' => $this->request->getPost('app_download_links'),
            'server_name' => $this->request->getPost('server_name'),
            'server_type' => $this->request->getPost('server_type'),
            'server_creation_date' => $this->request->getPost('server_creation_date'),
            'server_member_count' => $this->request->getPost('server_member_count'),
            'server_verification_status' => $this->request->getPost('server_verification_status'),
            'server_invite_link' => $this->request->getPost('server_invite_link'),
            'brand_description' => $this->request->getPost('brand_description'),
            'key_products' => $this->request->getPost('key_products'),
            'headquarters_location' => $this->request->getPost('headquarters_location'),
            'parent_company' => $this->request->getPost('parent_company'),
            'contact_email' => $this->request->getPost('contact_email'),
            'partnerships' => $this->request->getPost('partnerships'),
            'user_id' => $user_id
        ];

        // Insert into DB
        $db = \Config\Database::connect();
        $db->table('brands')->insert($data);

        // Success Message
        echo "<h3 style='color:green;'>✅ Brand Saved Successfully!</h3>";
        echo "<h4>📋 Submitted Details:</h4>";
        echo "<table border='1' cellpadding='5'>";
        foreach ($data as $key => $value) {
            echo "<tr><td><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";

        // Show image if uploaded
        // if ($brandlogo) {
        //     echo "<p><strong>🖼️ Uploaded Logo:</strong><br><img src='/files/brand_logo/$brandlogo' height='100'></p>";
        // }
        if ($brandlogo) {
    $encoded_logo = urlencode($brandlogo);
    echo "<p><strong>🖼️ Uploaded Logo:</strong><br>
    <img src='https://efactor.tech/files/brand_logo/$encoded_logo' height='100'></p>";
}

    }
}


function savebrand2() {
    session_start();
    $user_id = $_SESSION['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $targetDirectory = "/var/www/html/files/brand_logo/";
   // $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
  // $targetDirectory = "/Program Files/Ampps/www/factor_17-05/files/brandLogo/";
  $originalFileName = basename($_FILES["brandlogo"]["name"]);
    $targetFile = $targetDirectory . $originalFileName;

    // Check if the uploaded file exists
    if (!isset($_FILES["brandlogo"])) {
        echo "No file uploaded.";
        exit;
    }
 
    // Check if target directory exists
    if (!file_exists($targetDirectory)) {
        echo "Target directory does not exist.";
        exit;
    }

    // Move the uploaded file to the target directory
    $tempFile = $_FILES["brandlogo"]["tmp_name"];
    if (move_uploaded_file($tempFile, $targetFile)) {
        echo "The file " . htmlspecialchars(basename($_FILES["brandlogo"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
   
    $id = $this->request->getPost('id');
    $brandlogo = $originalFileName;
 
        $result = $this->Items_model->save_brand($id,$brandlogo,$user_id);
 
}

function saveprod() {
    session_start();
    $user_id = $_SESSION['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 //  $targetDirectory = "/var/www/html/files/brand_logo/";
   // $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
   $targetDirectory = "/Program Files/Ampps/www/factor_17-05/files/brandLogo/";
  $originalFileName = basename($_FILES["prodimg"]["name"]);
    $targetFile = $targetDirectory . $originalFileName;

    // Check if the uploaded file exists
    if (!isset($_FILES["prodimg"])) {
        echo "No file uploaded.";
        exit;
    }
 
    // Check if target directory exists
    if (!file_exists($targetDirectory)) {
        echo "Target directory does not exist.";
        exit;
    }

    // Move the uploaded file to the target directory
    $tempFile = $_FILES["prodimg"]["tmp_name"];
    if (move_uploaded_file($tempFile, $targetFile)) {
        echo "The file " . htmlspecialchars(basename($_FILES["prodimg"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
}
   
    $id = $this->request->getPost('id');
    $name = $this->request->getPost('name');
    $prodimg = $originalFileName;
 //echo $name;
// echo $id;
        $result = $this->Items_model->save_prod($id,$name,$prodimg,$user_id);
 
}

/*
function savebrand_edit() {

session_start();
$user_id = $_SESSION['user_id'];


       $name= $this->request->getPost('name');

          $brand_id = $this->Items_model->save_brand_edit($name, $user_id);

            echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";



        }

*/





public function savebrand_edit()
    {

session_start();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDirectory = "/var/www/html/files/brand_logo/";
     // $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
    // $targetDirectory = "/Program Files/Ampps/www/factor_17-05/files/brandLogo/";
    $originalFileName = basename($_FILES["brandlogo"]["name"]);
      $targetFile = $targetDirectory . $originalFileName;
  
      // Check if the uploaded file exists
      if (!isset($_FILES["brandlogo"])) {
          echo "No file uploaded.";
          exit;
      }
   
      // Check if target directory exists
      if (!file_exists($targetDirectory)) {
          echo "Target directory does not exist.";
          exit;
      }
  
      // Move the uploaded file to the target directory
      $tempFile = $_FILES["brandlogo"]["tmp_name"];
      if (move_uploaded_file($tempFile, $targetFile)) {
          echo "The file " . htmlspecialchars(basename($_FILES["brandlogo"]["name"])) . " has been uploaded.";
      } else {
          echo "Sorry, there was an error uploading your file.";
      }
  }

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $brandlogo = $originalFileName;
       $user_id = session()->get('user_id'); // Assuming user ID is stored in session
 
       
        $result = $this->Items_model->save_brand_edit($id, $name, $brandlogo, $user_id);

        
        if ($result === true) {
            return redirect()->to(site_url('items/brand'))->with('success', 'Brand updated successfully.');
        } else {
            
            return redirect()->to(site_url('items/brand'))->with('error', $result);
        }
  }





      function categories() {

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/categories", $view_data);

     }

    //get categories dropdown
    private function _get_categories_dropdown() {
        $categories = $this->Item_categories_model->get_all_where(array("deleted" => 0), 0, 0, "title")->getResult();

        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->title);
        }

        return json_encode($categories_dropdown);
    }

    /* load item modal */



     function modal_formcat() {
       

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_formcat', $view_data);
    }

   //modal_vendorproduct
      function modal_vendorproduct() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];

          $view_data['categories_dropdown'] =  $this->Items_model->get_cats($user_id)->getResult();
           $view_data['brand_dropdown'] =  $this->Items_model->get_brands($user_id)->getResult();
           return $this->template->view('items/modal_vendorproduct', $view_data);
      }
      
       function modal_vendor_admin_product() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];

          $view_data['categories_dropdown'] =  $this->Items_model->get_cats($user_id)->getResult();
          $view_data['brand_dropdown'] =  $this->Items_model->get_brands($user_id)->getResult();
          $view_data['ids'] =  $this->Items_model->get_ids()->getResult();
           return $this->template->view('items/modal_vendor_admin_product', $view_data);
      }

    function modal_form() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_form', $view_data);
    }

    //modal_files
    function modal_files() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_files', $view_data);
    }

     function modal_addmapped() {
       // $this->access_only_team_members();
       // $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] =  $this->Items_model->get_prods()->getResult();
        //$this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_addmapped', $view_data);
    }



/**********modal_api**************************************/

     function modal_api($itemid = 0) {

//$user_id = getUserIdFromSession();  

session_start();
$user_id = $_SESSION['user_id'];     

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $view_data['itemid']=$itemid;
        $api_details_result = $this->Items_model->default_form_values($itemid);
     
        if ($api_details_result) {
                $api_details_row = $api_details_result->getRow();
        
                if ($api_details_row) {
                    $view_data['brand'] = $api_details_row->brand;
                    $view_data['category'] = $api_details_row->category;
                    $view_data['modelno'] = $api_details_row->modelno;
                } else {
                    // Handle case when API details row is not found
                    echo "API details row not found.";
                }
            } else {
                // Handle case when API details result is not found
                echo "API details result not found.";
            }
    
         $view_data['id'] = $this->request->getPost('id');
         $view_data['categories_dropdown'] =  $this->Items_model->get_cats($user_id)->getResult();
         return $this->template->view('items/modal_api', $view_data);

    }



/***************************************************************/
/*--------------------------------------------------------------*/



     public function selected_data() {
     if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $modelId = $_POST['model_id'];
    $modelNo = $_POST['model_no'];
    $productName = $_POST['product_name'];
    //$parameterName = $_POST['parameter_name'];

    
     echo "Model  : <br>";
    echo "Model ID: " . $modelId . "<br>";
    echo "Model No: " . $modelNo . "<br>";
    echo "Product Name: " . $productName . "<br>";
    //echo "Parameter Name: " . $parameterName . "<br>";
    
}
      
      
     $data = array(
            'model_id' => $modelId,
            'model_no' => $modelNo,
            'product_name' => $productName,
        );

        // Save the data using the model
       // $this->Items_model->save_selected_values($data);

       
       if (isset($_POST['action'])) {
        if ($_POST['action'] === 'assign') {
            $this->Items_model->save_selected_values($data);
            $result = $this->Items_model->save_selected_values($data);
         if ($result == false) {
                echo "This node_id is already asigned to another Product.";
          }else{
            echo "Data assigned successfully.<br>";
         }
        } elseif ($_POST['action'] === 'deselect') {
            $this->Items_model->save_deselected_values($data); 
            echo "Data deselected successfully.<br>";
        }
    }

        // Redirect or load a view after saving
       // return redirect()->to('/items/callDisplayId');// Redirect to the desired page after saving

    }


    


/*-------------------------------------------------------------*/
public function modal_productinfo($node_id) {
    $id = $this->request->getPost('id'); 
    $data = $this->Items_model->getDataBynodeId($node_id);

    if ($data) {
        $product_details = get_object_vars($data);

        if ($product_details) {
            $device_type = $product_details['device_type'];
            $view_data['product_details'] = $this->_make_product_row($product_details, $device_type);

            
            $is_admin = $this->login_user->is_admin;  
            if ($is_admin) {
                return $this->template->view('items/User_Products', $view_data);
            } else {
                $partial_row = $this->_make_partial_product_row($product_details, $device_type);
                $view_data['product_details'] = $partial_row;
                return $this->template->view('items/User_Products', $view_data);
            }
        }
    }

    return "Product details not found for node ID: $node_id";
}

private function _make_product_row($data, $device_type) {
    $row = array(
        'prod_category' => $data['prod_category'],
        'module_type' => $data['module_type'],
        'vendor_id' => $data['vendor_id'],
        'mac' => $data['mac'],
        'payload' => $data['payload'],
        'vendor_name' => $data['vendor_name'],
        'access_token' => $data['access_token'],
        'request_id' => $data['request_id'],
        'ipaddress' => $data['ipaddress'],
        'time' => $data['time'],
        'product_status' => $data['product_status'],
        'current_devicetype' => $data['current_devicetype'],
        'share_user' => $data['share_user'],
        'user_priority' => $data['user_priority'],
        'email' => $data['email'],
        'app_version' => $data['app_version'],
        'share_req_id' => $data['share_req_id'],
        'connect_type' => $data['connect_type'],
        'share_req_status' => $data['share_req_status'],
        'add_node_time' => $data['add_node_time'],
        'share_time' => $data['share_time'],
        'group_name' => $data['group_name'],
        'group_req_id' => $data['group_req_id'],  
        'ssid' => $data['ssid'],
        'wifi_pwd' => $data['wifi_pwd'],
        'secret_key' => $data['secret_key'],
        'uartmsgid' => $data['uartmsgid'],
        'errorcode' => $data['errorcode'],
        'comm_mode' => $data['comm_mode'],
        'req_status' => $data['req_status'],
        'link_devices' => $data['link_devices'],
        'mob_device_token' => $data['mob_device_token'],
        'uid' => $data['uid'],
        'category_id' => $data['category_id'],
        'deleted' => $data['deleted'],
        'mqtt_auth' => $data['mqtt_auth'],
        'check_latitude' => $data['check_latitude'],
        'check_longitude' => $data['check_longitude'],
        'fw_version' => $data['fw_version'],
        'device_time' => $data['device_time'],
        'add_node_time' => $data['add_node_time'],
        'product_status' => $data['product_status'],
        'current_devicetype' => $data['current_devicetype'],
        'signup_latitude' => $data['signup_latitude'],
        'signup_longitude' => $data['signup_longitude'],
        'remote_pair_properties' => $data['remote_pair_properties'],
        'reload_default' => $data['reload_default'],
    );

    return $row;
}

private function _make_partial_product_row($data, $device_type) {
   
    $partial_row = array(
         'prod_category' => $data['prod_category'],
        'module_type' => $data['module_type'],
        'vendor_id' => $data['vendor_id'],
        'mac' => $data['mac'],
        'payload' => $data['payload'],
        'vendor_name' => $data['vendor_name'],
        'request_id' => $data['request_id'],
        'ipaddress' => $data['ipaddress'],
        'time' => $data['time'],
        'product_status' => $data['product_status'],
        'current_devicetype' => $data['current_devicetype'],
        'share_user' => $data['share_user'],
        'user_priority' => $data['user_priority'],
        'email' => $data['email'],
        'app_version' => $data['app_version'],
        'share_req_id' => $data['share_req_id'],
        'connect_type' => $data['connect_type'],
        'share_req_status' => $data['share_req_status'],
        'add_node_time' => $data['add_node_time'],
        'share_time' => $data['share_time'],
        'group_name' => $data['group_name'],
        'group_req_id' => $data['group_req_id'],  
        'ssid' => $data['ssid'],
        'uartmsgid' => $data['uartmsgid'],
        'errorcode' => $data['errorcode'],
        'comm_mode' => $data['comm_mode'],
        'req_status' => $data['req_status'],
        'link_devices' => $data['link_devices'],
        'uid' => $data['uid'],
        'category_id' => $data['category_id'],
        'deleted' => $data['deleted'],
        'check_latitude' => $data['check_latitude'],
        'check_longitude' => $data['check_longitude'],
        'fw_version' => $data['fw_version'],
        'device_time' => $data['device_time'],
        'add_node_time' => $data['add_node_time'],
        'product_status' => $data['product_status'],
        'current_devicetype' => $data['current_devicetype'],
        'signup_latitude' => $data['signup_latitude'],
        'signup_longitude' => $data['signup_longitude'],
        'remote_pair_properties' => $data['remote_pair_properties'],
        'reload_default' => $data['reload_default'],
    );

    return $partial_row;
}
 
/********************************************************************************/

public function modal_apisinfo($node_id) {
 session_start();
$current_user_id = $_SESSION['user_id'];   

    $id = $this->request->getPost('id'); 
    $model_number = $this->request->getVar('model_number'); 
    $user_id = $this->request->getVar('user_id'); 
     $apis_info = $this->Items_model->modal_apisinfo($node_id,$model_number,$user_id);
     $current_user_type = $this->Items_model->apisinfo_user_type($current_user_id);
  return view('items/APIs', [
        'user_id' => $user_id,
        'id' => $id,
        'model_number' => $model_number,
        'node_id' => $node_id,
        'api_info' => $apis_info,
        'user_type' => $current_user_type,
        'current_user_id' => $current_user_id
    ]);
}

 


public function online_productinfo() {


        $id = $this->request->getPost('id');
        
                $view_data['node_id'] = $node_id; // Assign node_id
        $view_data['id'] = $id; // Assign id

        $view_data['categories_dropdown'] = $this->Items_model->get_cats($user_id)->getResult(); // Adjust this method as needed
        return $this->template->view('items/online', $view_data);
    
    }












/*****************modal_command************************/
    

public function modal_command($modelname, $parameter_name, $product_name) {

session_start();
$user_id = $_SESSION['user_id'];    


  $view_data['categories_dropdown'] = $this->Items_model->get_cats($user_id)->getResult();

   
$api_details_result = $this->Items_model->get_details_api($modelname, $parameter_name, $product_name);
 
if ($api_details_result) {
        $api_details_row = $api_details_result->getRow();

        if ($api_details_row) {
            $view_data['brandname'] = $api_details_row->brand_name;
           
            
            $image_filename = $api_details_row->brand_logo;
      //  $image_url = 'http://localhost/factor_17-05/files/brandLogo/' . $image_filename;  
        $image_url = 'https://efactor.tech/files/brand_logo/' . $image_filename;
    
        $view_data['brandlogo'] = $image_url;
        
       
            $view_data['productname'] = $api_details_row->product_name;
            
            $image_filenames = $api_details_row->device_image;
      //  $image_url2 = 'http://localhost/factor_17-05/files/brandLogo/' . $image_filenames;
          $image_url2 = 'https://efactor.tech/files/brand_logo/' . $image_filenames;

        $view_data['displayedImage'] = $image_url2;
            
            $view_data['modelname'] = $api_details_row->model_name;
            $view_data['product_description'] = $api_details_row->product_description;
            $view_data['numberofpages'] = $api_details_row->number_of_pages;
            $view_data['Sequence'] = $api_details_row->sequence_no;
            $view_data['pagenames'] = $api_details_row->page_names;
            $view_data['date'] = $api_details_row->date;
            $view_data['itemid'] = $api_details_row->itemid;
            $view_data['filename'] = $api_details_row->filename;
            $view_data['apidate'] = $api_details_row->apidate;

            $view_data['cmd_parametername'] = $api_details_row->parameter_name;
            $view_data['cmd_dataflow'] = $api_details_row->cmd_dataflow;
            $view_data['cmd_framesizeinbytes'] = $api_details_row->cmd_framesize;
            $view_data['cmd_noofrows'] = $api_details_row->cmd_noofrows;
            $view_data['cmd_frametrigger'] = $api_details_row->cmd_frametrigger;
            $view_data['cmd_multipleui'] = $api_details_row->cmd_multipleui;
            $view_data['cmd_triggerui'] = $api_details_row->cmd_triggerui;
            
            $view_data['resp_dataflow'] = $api_details_row->resp_dataflow;
            $view_data['resp_framesizeinbytes'] = $api_details_row->resp_framesize;
            $view_data['resp_noofrows'] = $api_details_row->resp_noofrows;
            $view_data['resp_frametrigger'] = $api_details_row->resp_frametrigger;
            $view_data['resp_multipleui'] = $api_details_row->resp_multipleui;
            $view_data['resp_triggerui'] = $api_details_row->resp_triggerui;

            $view_data['error_dataflow'] = $api_details_row->error_dataflow;
            $view_data['error_framesizeinbytes'] = $api_details_row->error_framesize;
            $view_data['error_noofrows'] = $api_details_row->error_noofrows;
            $view_data['error_frametrigger'] = $api_details_row->error_frametrigger;
            $view_data['error_multipleui'] = $api_details_row->error_multipleui;
            $view_data['error_triggerui'] = $api_details_row->error_triggerui;



            $view_data['startBytePosition_cmd'] = explode(',', $api_details_row->cmd_startbytePosition);
           $view_data['endBytePosition_cmd'] = explode(',', $api_details_row->cmd_endbytePosition);
           $view_data['fieldDescription_cmd'] = explode(',', $api_details_row->cmd_fielddescription);
           $view_data['numberOfBytes_cmd'] = explode(',', $api_details_row->cmd_numberOfbytes);
           $view_data['bitSize_cmd'] = explode(',', $api_details_row->cmd_bitsize);
           $view_data['dataType_cmd'] = explode(',', $api_details_row->cmd_datatype);
           $view_data['valueDefinition_cmd'] = explode(',', $api_details_row->cmd_valuedefinition);
           $view_data['displayValueMin_cmd'] = explode(',', $api_details_row->cmd_displayvaluemin);
           $view_data['displayValueMax_cmd'] = explode(',', $api_details_row->cmd_displayvaluemax);
           $view_data['valueInHexMin_cmd'] = explode(',', $api_details_row->cmd_valueinhexmin);
           $view_data['valueInHexMax_cmd'] = explode(',', $api_details_row->cmd_valueinhexmax);
           $view_data['uiType_cmd'] = explode(',', $api_details_row->cmd_uitype);
           $view_data['uiId_cmd'] = explode(',', $api_details_row->cmd_uiid);
           $view_data['uiPageNo_cmd'] = explode(',', $api_details_row->cmd_uipageno);
           $view_data['cmd_asperui'] = explode(',', $api_details_row->cmd_asperui);
           $view_data['cmd_sno_asperui'] = explode(',', $api_details_row->cmd_sno_asperui);
           $view_data['cmd_asperuivalue'] = explode(',', $api_details_row->cmd_asperui_values);
           $view_data['uiDescriptionInOut_cmd'] = explode(',', $api_details_row->cmd_uidescriptioninout);
           $view_data['sliderStepValue_cmd'] = explode(',', $api_details_row->cmd_sliderstepvalue);
           $view_data['defaultvalue_cmd'] = explode(',', $api_details_row->cmd_defaultvalue);
           $view_data['developerNotes_cmd'] = explode(',', $api_details_row->cmd_developernotes);



           $view_data['startBytePosition_resp'] = explode(',', $api_details_row->resp_startBytePosition);
           $view_data['endBytePosition_resp'] = explode(',', $api_details_row->resp_endBytePosition);
           $view_data['fieldDescription_resp'] = explode(',', $api_details_row->resp_fieldDescription);
           $view_data['numberOfBytes_resp'] = explode(',', $api_details_row->resp_numberOfBytes);
           $view_data['bitSize_resp'] = explode(',', $api_details_row->resp_bitSize);
           $view_data['dataType_resp'] = explode(',', $api_details_row->resp_dataType);
           $view_data['valueDefinition_resp'] = explode(',', $api_details_row->resp_valueDefinition);
           $view_data['displayValueMin_resp'] = explode(',', $api_details_row->resp_displayValueMin);
           $view_data['displayValueMax_resp'] = explode(',', $api_details_row->resp_displayValueMax);
           $view_data['valueInHexMin_resp'] = explode(',', $api_details_row->resp_valueInHexMin);
           $view_data['valueInHexMax_resp'] = explode(',', $api_details_row->resp_valueInHexMax);
           $view_data['uiType_resp'] = explode(',', $api_details_row->resp_uiType);
           $view_data['uiId_resp'] = explode(',', $api_details_row->resp_uiId);
           $view_data['uiPageNo_resp'] = explode(',', $api_details_row->resp_uiPageNo);
           $view_data['resp_asperui'] = explode(',', $api_details_row->resp_asperui);
           $view_data['resp_sno_asperui'] = explode(',', $api_details_row->resp_sno_asperui);
           $view_data['resp_asperuivalue'] = explode(',', $api_details_row->resp_asperui_values);
           $view_data['uiDescriptionInOut_resp'] = explode(',', $api_details_row->resp_uiDescriptionInOut);
           $view_data['sliderStepValue_resp'] = explode(',', $api_details_row->resp_sliderStepValue);
           $view_data['defaultvalue_resp'] = explode(',', $api_details_row->resp_defaultvalue);
           $view_data['developerNotes_resp'] = explode(',', $api_details_row->resp_developerNotes);



           $view_data['startBytePosition_error'] = explode(',', $api_details_row->error_startBytePosition);
           $view_data['endBytePosition_error'] = explode(',', $api_details_row->error_endBytePosition);
           $view_data['fieldDescription_error'] = explode(',', $api_details_row->error_fieldDescription);
           $view_data['numberOfBytes_error'] = explode(',', $api_details_row->error_numberOfBytes);
           $view_data['bitSize_error'] = explode(',', $api_details_row->error_bitSize);
           $view_data['dataType_error'] = explode(',', $api_details_row->error_dataType);
           $view_data['valueDefinition_error'] = explode(',', $api_details_row->error_valueDefinition);
           $view_data['displayValueMin_error'] = explode(',', $api_details_row->error_displayValueMin);
           $view_data['displayValueMax_error'] = explode(',', $api_details_row->error_displayValueMax);
           $view_data['valueInHexMin_error'] = explode(',', $api_details_row->error_valueInHexMin);
           $view_data['valueInHexMax_error'] = explode(',', $api_details_row->error_valueInHexMax);
           $view_data['uiType_error'] = explode(',', $api_details_row->error_uiType);
           $view_data['uiId_error'] = explode(',', $api_details_row->error_uiId);
           $view_data['uiPageNo_error'] = explode(',', $api_details_row->error_uiPageNo);
           $view_data['error_asperui'] = explode(',', $api_details_row->error_asperui);
           $view_data['error_sno_asperui'] = explode(',', $api_details_row->error_sno_asperui);
           $view_data['error_asperuivalue'] = explode(',', $api_details_row->error_asperui_values);
           $view_data['uiDescriptionInOut_error'] = explode(',', $api_details_row->error_uiDescriptionInOut);
           $view_data['sliderStepValue_error'] = explode(',', $api_details_row->error_sliderStepValue);
           $view_data['defaultvalue_error'] = explode(',', $api_details_row->error_defaultvalue);
           $view_data['developerNotes_error'] = explode(',', $api_details_row->error_developerNotes);



        } else {
            // Handle case when API details row is not found
            echo "API details row not found.";
        }
    } else {
        // Handle case when API details result is not found
        echo "API details result not found.";
    }

    if($parameter_name == "Command Get Status"){
        return $this->template->view('items/modal_command_status', $view_data);
    }else{
   return $this->template->view('items/modal_command', $view_data);
    }
}




/************************************************************************************/

/**************************************************************************************/

function modal_response() {
       

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

         $view_data['id'] = $this->request->getPost('id');
         $view_data['categories_dropdown'] =  $this->Items_model->get_cats()->getResult();
         return $this->template->view('items/modal_response', $view_data);
        // return $this->template->view('items/modal_command', $view_data);
    }

function modal_error() {
       

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

      

         $view_data['id'] = $this->request->getPost('id');
         $view_data['categories_dropdown'] =  $this->Items_model->get_cats()->getResult();
         return $this->template->view('items/modal_error', $view_data);
        // return $this->template->view('items/modal_command', $view_data);
}


/****************************************************/

 function modal_mapped() {
       

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_mapped', $view_data);
}


      function modal_test() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_test', $view_data);
    }

    /* add or edit an item */

      function save_files() {

        $rand=rand('0000','9999');

        $file_name = $rand.'_'.$this->request->getPost("item_files");
        $target_path = get_setting("timeline_file_path");
        $file_info = move_temp_file($file_name, $target_path);

         if($file_info)
         {
       echo "<script>alert('Successfully Uploaded');location.href='https://efactor.tech/index.php/items';</script>";
         }
      }

       function savetesting() {

         $this->access_only_team_members();
        $this->validate_access_to_items();

         $id = $this->request->getPost('id');

          $item_data = array(
            "teststatus" => $this->request->getPost('teststatus'),
            "testremarks" => $this->request->getPost('testremarks'),
            "testdate" => date("Y-m-d h:i A")
            
        );

          $item_id = $this->Items_model->ci_save($item_data, $id);

           $options = array("id" => $item_id);
            $item_info = $this->Items_model->get_details($options)->getRow();

           echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info),  'message' => 'Tested Successfully'));


       }


       //savemapped
       function savemapped() {

         $id = $this->request->getPost('id');

          $item_data = array(
            "serialno" => $this->request->getPost('serialno'),
            "uid" => $this->request->getPost('uid'),
            "modelno" => $this->request->getPost('modelno')          
        );

          $item_id = $this->Items_model->savemapped($item_data);
          echo "<script>location.href='https://efactor.tech/index.php/items/mapped';</script>";
}

       //savevendorproduct
         /*    function savevendorproduct() {

         $id = $this->request->getPost('id');

          $item_data = array(
            "category" => $this->request->getPost('category'),
          //  "name" => $this->request->getPost('name'),
            "modelname" => $this->request->getPost('modelname'),
            "modelno" => $this->request->getPost('modelno'),
            "description" => $this->request->getPost('description')
           
            
        );

          $item_id = $this->Items_model->savevendorproduct($item_data);

          echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";

       }*/





function savevendorproduct() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];


    $id = $this->request->getPost('id');

    $item_data = [
        "brand" => $this->request->getPost('brand'),
        "category" => $this->request->getPost('category'),
        "modelname" => $this->request->getPost('modelname'),
        "modelno" => $this->request->getPost('modelno'),
        "description" => $this->request->getPost('description')
    ];

        $result = $this->Items_model->savevendorproduct($item_data, $user_id);

        if (is_string($result)) {
               $view_data['error'] = $result;
    } else {
        $this->getstatusfunt22($item_data, $user_id);
                echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";
        return;
    }
     
        return $this->template->rander("items/vendor", $view_data);
            
}



function savevendoradminproduct() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];


    $id = $this->request->getPost('id');

    $item_data = [
        "SelectModel" => $this->request->getPost('selected_model'),
        "selected_id" => $this->request->getPost('selected_id'),
        "user_id" => $this->request->getPost('user_id'),
        "ReferenceModel" => $this->request->getPost('reference_name'),
        "reference_id" => $this->request->getPost('reference_id')
    ];

        $result = $this->Items_model->savevendoradminproduct($item_data, $user_id);

        if (is_string($result)) {
               $view_data['error'] = $result;
    } else {
        $this->getstatusfunt22($item_data, $user_id);
             //   echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";
       // return;
    }
     print_r($item_data);
       // return $this->template->rander("items/vendor", $view_data);
            
}


function getstatusfunt22($item_data, $user_id){
    $id = $this->Items_model->getstatusfunt22($item_data, $user_id);
    return $this->getstatusfunt($item_data, $user_id ,$id);
}

function getstatusfunt($item_data, $user_id ,$id){
    $result = $this->Items_model->getstatusfunt($item_data, $user_id, $id);
    return $result;
}
/*************************get_api***********************************************************/



public function get_api_details() {
    
    $api_details = $this->Items_model->get_details_api()->result();
    $data = array();
    foreach ($api_details as $api) {
        $data['brand_name'] = $api->brand_name;
        $data['brand_logo'] = $api->brand_logo;
        $data['product_name'] = $api->product_name;
        $data['model_name'] = $api->model_name;
        $data['product_description'] = $api->product_description;
        $data['number_of_pages'] = $api->number_of_pages;
        $data['date'] = $api->date;
        // Add more fields as needed
    }

    
}



public function get_api_app() {
    
    $app_details = $this->Items_model->get_api_app()->result();

    $data = array();
    foreach ($app_details as $api) {
        $data['ui_options'] = $api->ui_options;
        $data['ui_type'] = $api->ui_type;
        $data['ui_name'] = $api->ui_name;
        $data['ui_cmd_value'] = $api->ui_cmd_value;
        $data['ui_sequence_no'] = $api->ui_sequence_no;
        $data['ui_range_min'] = $api->ui_range_min;
        $data['ui_data_type'] = $api->ui_data_type;
         $data['ui_range_max'] = $api->ui_range_max;
        $data['ui_permission'] = $api->ui_permission;
        $data['ui_action'] = $api->ui_action;

        
    }

}


/*--------------------------------------------------------------------------------------*/
function efactprod() {
    session_start();
    $user_id = $_SESSION['user_id'];

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];

    $property_keys = ["TZ_properties", "BR_properties", "UMI_properties", "EC_properties", "CM_properties", "TU_properties", "LC_properties", "display_properties"];
    
    foreach ($property_keys as $key) {
        if (isset($_POST[$key])) { 
            if ($_POST[$key] == 'read/write') {
                $data[$key] = 'read/write';
            } else {
                $data[$key] = '';  
            }
        } else {
            $data[$key] = '';  
        }
    }
    $item_data = array( 
             "user_id" => $this->request->getPost('user_id'),
            "node_id" => $this->request->getPost('node_id'),
            "model_number" => $this->request->getPost('model_number'), 
        );
 //print_r($data);
 //print_r($item_data);
 $apis_info_data = array_merge($data,$item_data);
 print_r($apis_info_data);
 $result = $this->Items_model->efactprod($apis_info_data);
}

}



/*--------------------------------------------------------------------------------------------*/

function specifications_gc_api() { 
    //$user_id = getUserIdFromSession(); 
    session_start(); 
    $user_id = $_SESSION['user_id']; 
    
            $id = $this->request->getPost('id');
            
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $targetDirectory = "/var/www/html/files/specifications/";
      //  $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      //  $targetDirectory = "C:/Program Files/Ampps\www/factor_17-05/files/specifications/";
      $orgFileName = basename($_FILES["guarantee_card_file"]["name"]);
        $targetFile = $targetDirectory . $orgFileName;
     
        if (!isset($_FILES["guarantee_card_file"])) {
            echo "No file uploaded.";
            exit;
        } 
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        } 
        $tempFile = $_FILES["guarantee_card_file"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["guarantee_card_file"]["name"])) . " has been uploaded.";
        } else { 
            //echo "Sorry, there was an error uploading your file.";
        }
    }
  
            $item_data = array(
            "id" => $this->request->getPost('id'),
            "modelno" => $this->request->getPost('modelno'),
            "guarantee_card" => $this->request->getPost('guarantee_card'),
            "guarantee_card_file" => $orgFileName,
        );
      
    $result = $this->Items_model->specifications_gc_api($item_data, $user_id);
    if ($result == true) {
       
     /*     
    echo "Data saved successfully!";
    
    echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";
     */
     
    //echo "<script>alert('Data saved successfully!');</script>";
    echo "Data saved successfully!";
    //echo "<script>
    //location.href='https://efactor.tech/index.php/items/vendor';
    //</script>";
    } else {
       // echo "<script>alert('Error saving data to database!');</script>";
             echo "Error saving data to database!";
    
        }
    }
  
    function specifications_vid_api() {  
        session_start(); 
        $user_id = $_SESSION['user_id']; 
        
                $id = $this->request->getPost('id');
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
           // $targetDirectory = "C:/Program Files/Ampps/www/factor_17-05/files/specifications/";  
            $targetDirectory = "/var/www/html/files/specifications/";
            if (!isset($_FILES["specific_vid_file"])) {
                echo "No files uploaded.";
                exit;
            }  
            if (!file_exists($targetDirectory)) {
                echo "Target directory does not exist.";
                exit;
            }  
            $uploadedFileNames = []; 
            foreach ($_FILES["specific_vid_file"]["name"] as $key => $originalFileName) {  
                $targetFile = $targetDirectory . basename($originalFileName);
                $tempFile = $_FILES["specific_vid_file"]["tmp_name"][$key];  
                if (move_uploaded_file($tempFile, $targetFile)) {
                    echo "The file " . htmlspecialchars(basename($originalFileName)) . " has been uploaded successfully.<br>"; 
                    $uploadedFileNames[] = $originalFileName;
                } else {
                    echo "Sorry, there was an error uploading the file " . htmlspecialchars(basename($originalFileName)) . ".<br>";
                }
            }
        }
        $uploaded_array = implode('', array_map(function($item) {
            return "[$item]";   
        }, $uploadedFileNames));
                $item_data = array(
                "id" => $this->request->getPost('id'),
                "modelno" => $this->request->getPost('modelno'), 
                "specific_vid" => $this->request->getPost('specific_vid'),
                "specific_vid_file" => $uploaded_array, 
            );
          
        $result = $this->Items_model->specifications_vid_api($item_data, $user_id);
        if ($result == true) { 
        echo "Data saved successfully!";
        //echo "<script>
        //location.href='https://efactor.tech/index.php/items/vendor';
        //</script>";
        } else {
           // echo "<script>alert('Error saving data to database!');</script>";
                 echo "Error saving data to database!";
        
            }
        }


        function specificationsapi() {  
            session_start(); 
            $user_id = $_SESSION['user_id']; 
        
            // Extract the other standard form values
            $item_data = array(
                "id" => $this->request->getPost('id'),
                "modelno" => $this->request->getPost('modelno'), 
                 
            ); 
        
            // Extract all the cmd and resp values
            // We'll loop through the posted cmd and resp arrays to capture all values dynamically
        
            $parameters = $this->request->getPost('parameters');
            $cmd_value = $this->request->getPost('cmd_value');
            $cmd_times = $this->request->getPost('cmd_time');
            $cmd_startbytes = $this->request->getPost('cmd_startbyte');
            $cmd_mins = $this->request->getPost('cmd_min');
            $cmd_maxs = $this->request->getPost('cmd_max');
            $cmd_defaults = $this->request->getPost('cmd_default');
            $cmd_param_names = $this->request->getPost('cmd_param_names');
            $cmd_datatypes = $this->request->getPost('cmd_datatype');
            $cmd_numberOfbytes = $this->request->getPost('cmd_numberOfbyte');
         
            $resp_min_values = $this->request->getPost('resp_min_value');
            $resp_max_values = $this->request->getPost('resp_max_value');
            $resp_defaults = $this->request->getPost('resp_default');
            $resp_startbytepositions = $this->request->getPost('resp_startbyte');
            $resp_param_name = $this->request->getPost('resp_param_names');
            $resp_dataTypes = $this->request->getPost('resp_dataType');
            $resp_numberOfBytes = $this->request->getPost('resp_numberOfByte');
             
            function syncParametersWithCmdNames($parameters, $cmd_param_names) { 
                $updated_cmd_param_names = [];
             
                foreach ($parameters as $key => $value) { 
                    if (isset($cmd_param_names[$key])) {
                        $updated_cmd_param_names[$key] = $cmd_param_names[$key]; 
                    } else {
                        $updated_cmd_param_names[$key] = [''];  
                    }
                }
            
                return $updated_cmd_param_names;
            }

            $cmd_values = syncParametersWithCmdNames($parameters, $cmd_value);
           // $cmd_time = syncParametersWithCmdNames($parameters, $cmd_times); 
            $cmd_startbyte = syncParametersWithCmdNames($parameters, $cmd_startbytes);  
            $cmd_min = syncParametersWithCmdNames($parameters, $cmd_mins); 
            $cmd_max = syncParametersWithCmdNames($parameters, $cmd_maxs);   
            $cmd_default = syncParametersWithCmdNames($parameters, $cmd_defaults);  
            $resp_min_value = syncParametersWithCmdNames($parameters, $resp_min_values);  
            $resp_max_value = syncParametersWithCmdNames($parameters, $resp_max_values); 
            $resp_default = syncParametersWithCmdNames($parameters, $resp_defaults);  
            $resp_startbyteposition = syncParametersWithCmdNames($parameters, $resp_startbytepositions);  

            $cmd_datatype = syncParametersWithCmdNames($parameters, $cmd_datatypes);  
            $cmd_numberOfbyte = syncParametersWithCmdNames($parameters, $cmd_numberOfbytes);  
            $resp_dataType = syncParametersWithCmdNames($parameters, $resp_dataTypes);  
            $resp_numberOfByte = syncParametersWithCmdNames($parameters, $resp_numberOfBytes);  
 
            function syncParameters($parameters, $cmd_param_names) {  
                $updated_cmd_param_names = [];  
                foreach ($parameters as $key => $value) {  
                    $updated_cmd_param_names[$key] = isset($cmd_param_names[$key]) ? $cmd_param_names[$key] : '';
                } 
             
                return $updated_cmd_param_names; 
            }  
            $cmd_time = syncParameters($parameters, $cmd_times); 
            $cmd_param_name = syncParameters($parameters, $cmd_param_names); 
            $resp_param_names = syncParameters($parameters, $resp_param_name); 
            
            function float_to_hex($f, $num_bytes) {
                $binary = pack('f', $f);   
                $hex = bin2hex($binary);  
                return str_pad($hex, $num_bytes * 2, "0", STR_PAD_LEFT);   
            }
             
            function convert_to_hex($cmd_values, $cmd_datatype, $cmd_numberOfbyte) {
                $result = [];
            
                foreach ($cmd_values as $key => $values) {
                    $value_type = $cmd_datatype[$key] ?? [];
                    $byte_sizes = $cmd_numberOfbyte[$key] ?? [];
            
                    $hex_values = [];
            
                    foreach ($values as $index => $value) {
                        $datatype = $value_type[$index] ?? null;
                        $byte_size = $byte_sizes[$index] ?? null;
            
                        if ($datatype == "UnsignedInt") { 
                            $value = (int)$value; 
                            $hex_value = str_pad(dechex($value), $byte_size * 2, "0", STR_PAD_LEFT);   
                            $hex_values[] = $hex_value;
                        } elseif ($datatype == "Float") { 
                            $hex_value = float_to_hex($value, $byte_size);
                            $hex_values[] = $hex_value;
                        } elseif ($datatype == "String") { 
                            $hex_value = '';
                            foreach (str_split($value) as $char) {
                                $hex_value .= str_pad(dechex(ord($char)), 2, "0", STR_PAD_LEFT);
                            }
                            $hex_values[] = str_pad($hex_value, $byte_size * 2, "0", STR_PAD_LEFT);  
                        } else { 
                            $hex_values[] = null;
                        }
                    }
            
                    $result[$key] = $hex_values;
                }
            
                return $result;
            }
            
            $converted_cmd_values = convert_to_hex($cmd_values, $cmd_datatype, $cmd_numberOfbyte);
            $converted_resp_min_value = convert_to_hex($resp_min_value, $resp_dataType, $resp_numberOfByte);
            $converted_resp_max_value = convert_to_hex($resp_max_value, $resp_dataType, $resp_numberOfByte);
 
            $item_data['cmd_values'] = $converted_cmd_values;
            $item_data['cmd_datatype'] = $cmd_datatype;
            $item_data['cmd_numberOfbyte'] = $cmd_numberOfbyte;
            $item_data['cmd_time'] = $cmd_time;
            $item_data['cmd_startbyte'] = $cmd_startbyte;
            $item_data['cmd_min'] = $cmd_min;
            $item_data['cmd_max'] = $cmd_max;
            $item_data['cmd_default'] = $cmd_default;
            $item_data['cmd_param_names'] = $cmd_param_name;
        
            
            $item_data['resp_min_value'] = $converted_resp_min_value;
            $item_data['resp_max_value'] = $converted_resp_max_value;
            $item_data['resp_dataType'] = $resp_dataType;
            $item_data['resp_numberOfByte'] = $resp_numberOfByte;
            $item_data['resp_default'] = $resp_default;
            $item_data['resp_default'] = $resp_default;
            $item_data['resp_startbyteposition'] = $resp_startbyteposition;
            $item_data['resp_param_names'] = $resp_param_names;
        
             
           $result = $this->Items_model->specificationsapi($item_data, $user_id);
            echo "Item Data: <pre>";
            print_r($item_data);
            echo "</pre>";
             if ($result == true) { 
                 echo "Data saved successfully!";
                
            } else {
                echo "Error saving data to database!";
             }
        }






/*function specificationsapi() { 
    //$user_id = getUserIdFromSession(); 
    session_start();
    $user_id = $_SESSION['user_id']; 
    
            $id = $this->request->getPost('id');
            
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $targetDirectory = "/var/www/html/files/specifications/";
      //  $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      //  $targetDirectory = "C:/Program Files/Ampps\www/factor_17-05/files/specifications/";
      $originalFileName = basename($_FILES["guarantee_card_file"]["name"]);
        $targetFile = $targetDirectory . $originalFileName;
     
        if (!isset($_FILES["guarantee_card_file"])) {
            echo "No file uploaded.";
            exit;
        } 
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        } 
        $tempFile = $_FILES["guarantee_card_file"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["guarantee_card_file"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
      
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $targetDirectory = "/var/www/html/files/specifications/";
     //  $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
     //  $targetDirectory = "C:/Program Files/Ampps\www/factor_17-05/files/specifications/";
      $originalFileName2 = basename($_FILES["specific_vid_file"]["name"]);
        $targetFile = $targetDirectory . $originalFileName2;
     
        if (!isset($_FILES["specific_vid_file"])) {
            echo "No file uploaded.";
            exit;
        }
      
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        } 
        $tempFile = $_FILES["specific_vid_file"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["specific_vid_file"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } 
            $item_data = array(
            "id" => $this->request->getPost('id'),
            "modelno" => $this->request->getPost('modelno'),
            "guarantee_card" => $this->request->getPost('guarantee_card'),
            "guarantee_card_file" => $originalFileName,
            "specific_vid" => $this->request->getPost('specific_vid'),
            "specific_vid_file" => $originalFileName2,
            "auto_test" => $this->request->getPost('auto_test'),
            "auto_test_params" => $this->request->getPost('auto_test_params'),
        );
     
    $result = $this->Items_model->specificationsapi($item_data, $user_id);
    if ($result == true) {
        /*     
       echo "Data saved successfully!";
       echo "Item Data: <pre>";
       print_r($item_data);
       echo "</pre>";
         
        
       //echo "<script>alert('Data saved successfully!');</script>";
       echo "Data saved successfully!";
       echo "<script>
       location.href='https://efactor.tech/index.php/items/vendor';
       </script>";
       }elseif($result == "parameter_name already exists in this model.") {
        // echo "<script>alert('Error saving data to database!');</script>";
              echo "parameter_name already exists in this model.";
              echo "<script>
    location.href='https://efactor.tech/index.php/items/vendor';
    </script>";
     
         }else {
       // echo "<script>alert('Error saving data to database!');</script>";
             echo "Error saving data to database!";
    
        }
    }*/



function saveconfig() {
    session_start();
    $user_id = $_SESSION['user_id']; 
    
    // Get the POST data
    $item_data = array(
        "modelSelect" => $this->request->getPost('modelSelect'), 
        "pageSelect" => $this->request->getPost('pageSelect'),
        "nodeSelect" => $this->request->getPost('nodeSelect'),
        "textInput" => $this->request->getPost('routerSelect'),
        "statusSelect" => $this->request->getPost('statusSelect'), 
        "ModeSelect" => $this->request->getPost('ModeSelect'), 
        "LevelSelect" => $this->request->getPost('LevelSelect'), 
         "config_keys" =>  $this->request->getPost('configKeys'), 
        "configValues" => $this->request->getPost('configValues'), 
        "remoteaddress" => $this->request->getPost('inputBox'),
    );
    
     
    echo "Data saved successfully!";
  /*  echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";*/
      
    $item_data = $this->Items_model->saveconfig($item_data, $user_id); 
}

    /*
    public function getPairingData() {
    // Example: Query to fetch data from the database
    $query = $this->db->get('pairing_table'); // Replace with your actual table name

    if ($query->num_rows() > 0) {
        return $query->result_array(); // Return data as an array
    } else {
        return []; // Return an empty array if no data found
    }
}
*/
public function get_pairing_data() { 
    $pairing_data = $this->Items_model->getPairingData(); 
    echo json_encode($pairing_data);
}

function save_pairing_config() {
    session_start();
    $user_id = $_SESSION['user_id']; 
    
       $model_no = $this->request->getPost('modelSelect');
       
    $item_data = array(
        "modelSelect" => $model_no, 
        "pageSelect" => $this->request->getPost('pageSelect'),
        "nodeSelect" => $this->request->getPost('nodeSelect'),
        "textInput" => $this->request->getPost('routerSelect'),  
        "remoteaddress" => $this->request->getPost('inputBox'),
    ); 
    $item_data = $this->Items_model->saveparingconfig($item_data, $user_id); 
     return $this->pairing_config($model_no, $user_id); 
}

public function pairing_config($model_no, $user_id) {
    $pairing_data = $this->Items_model->get_pairing_config($model_no, $user_id);
    
    if ($pairing_data) {
        // Return pairing data as JSON
        return $this->response->setJSON($pairing_data);
    } else {
        // Return an empty array if no data found
        return $this->response->setJSON([]);
    }
}

    function save_page_seq() {
    session_start();
    $user_id = $_SESSION['user_id']; 
     
    $item_data = array(
        "modelSelect" => $this->request->getPost('modelno'), 
        "pageSelect" => $this->request->getPost('modal_id'),
        "page_seq" => $this->request->getPost('addedValuesInput') 
    ); 
    print_r($item_data);
    $item_data = $this->Items_model->save_page_seq($item_data, $user_id);  
}   

function saveapi() {


    //$user_id = getUserIdFromSession();
    
    session_start();
    $user_id = $_SESSION['user_id'];
    
    
    
    
            $id = $this->request->getPost('id');
            $rand=rand('0000','9999');
            $file_name = $rand.'_'.$this->request->getPost("item_files");
            $target_path = get_setting("timeline_file_path");
            $file_info = move_temp_file($file_name, $target_path);
    
    
    
    /*
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // $targetDirectory = "/var/www/html/files/brand_logo/";
        $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      $originalFileName = basename($_FILES["brandlogo"]["name"]);
        $targetFile = $targetDirectory . $originalFileName;
    
        // Check if the uploaded file exists
        if (!isset($_FILES["brandlogo"])) {
            echo "No file uploaded.";
            exit;
        }
     
        // Check if target directory exists
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        }
    
        // Move the uploaded file to the target directory
        $tempFile = $_FILES["brandlogo"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["brandlogo"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
      
      
      
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     //  $targetDirectory = "/var/www/html/files/brand_logo/";
       $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      $originalFileName2 = basename($_FILES["imageUpload"]["name"]);
        $targetFile = $targetDirectory . $originalFileName2;
    
        // Check if the uploaded file exists
        if (!isset($_FILES["imageUpload"])) {
            echo "No file uploaded.";
            exit;
        }
     
        // Check if target directory exists
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        }
    
        // Move the uploaded file to the target directory
        $tempFile = $_FILES["imageUpload"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["imageUpload"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
       
    */
    
            $item_data = array(
            "brandname" => $this->request->getPost('brandname'),
            //"brandlogo" => $originalFileName,
            "productname" => $this->request->getPost('productname'),
           // "imageDropdown" => $originalFileName2,
            "modelname" => $this->request->getPost('modelname'),
            "productdescription" => $this->request->getPost('productdescription'),
            "numberofpages" => $this->request->getPost('numberofpages'),
            "Sequence" => $this->request->getPost('Sequence'),
            "pagenames" => $this->request->getPost('pagenames'),
            "date" => $this->request->getPost('date'),
            "itemid" => $this->request->getPost('id'),
    
             "cmd_parametername" => $this->request->getPost('cmd_parametername'),
            "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),
            "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
            "cmd_noofrows" => $this->request->getPost('cmd_noofrows'),
            "cmd_frametrigger" => $this->request->getPost('cmd_frametrigger'),
            "cmd_multipleui" => $this->request->getPost('cmd_multipleui'),
             "cmd_triggerui" => $this->request->getPost('cmd_triggerui'),
    
            "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),
            "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
            "resp_noofrows" => $this->request->getPost('resp_noofrows'),
            "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
            "resp_multipleui" => $this->request->getPost('resp_multipleui'),
            "resp_triggerui" => $this->request->getPost('resp_triggerui'),
    
    
            "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),
            "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
            "error_noofrows" => $this->request->getPost('error_noofrows'),
            "error_frametrigger" => $this->request->getPost('error_frametrigger'),
            "error_multipleui" => $this->request->getPost('error_multipleui'),
            "error_triggerui" => $this->request->getPost('error_triggerui'),
    
                
        );
    
        $additional_data = array();
        $frame_size = (int)$this->request->getPost('cmd_noofrows');
        $respframe_size = (int)$this->request->getPost('resp_noofrows');
        $errorframe_size = (int)$this->request->getPost('error_noofrows');
    
    
    
        for ($i = 1; $i <= $frame_size; $i++) {
    
     $asperuiData = $this->request->getPost('asperui_cmd_' . $i);
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('sno_asperui_cmd' . $i);
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
         $asperuivalueData = $this->request->getPost('asperuivalue_cmd_'.$i);
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
        
            $additional_data[] = array(
                    "cmd_startBytePosition" => $this->request->getPost('startBytePosition_cmd_'.$i),
                    "cmd_endBytePosition" => $this->request->getPost('endBytePosition_cmd_'.$i),
                    "cmd_fieldDescription" => $this->request->getPost('fieldDescription_cmd_'.$i),
                    "cmd_numberOfBytes" => $this->request->getPost('numberOfBytes_cmd_'.$i),
                    "cmd_bitSize" => $this->request->getPost('bitSize_cmd_'.$i),
                    "cmd_dataType" => $this->request->getPost('dataType_cmd_'.$i),
                    "cmd_valueDefinition" => $this->request->getPost('valueDefinition_cmd_'.$i),
                    "cmd_displayValueMin" => $this->request->getPost('displayValueMin_cmd_'.$i),
                    "cmd_displayValueMax" => $this->request->getPost('displayValueMax_cmd_'.$i),
                    "cmd_valueInHexMin" => $this->request->getPost('valueInHexMin_cmd_'.$i),
                    "cmd_valueInHexMax" => $this->request->getPost('valueInHexMax_cmd_'.$i),
                    "cmd_uiType" => $this->request->getPost('uiType_cmd_'.$i),
                    "cmd_uiId" => $this->request->getPost('uiId_cmd_'.$i),
                    "cmd_asperui" => $as_per_ui_array,
                    "cmd_sno_asperui" => $sno_as_per_ui_array,
                    "cmd_asperuivalue" => $as_per_ui_value_array,
                    "cmd_uiPageNo" => $this->request->getPost('uiPageNo_cmd_'.$i),
                    "cmd_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_cmd_'.$i),
                    "cmd_sliderStepValue" => $this->request->getPost('sliderStepValue_cmd_'.$i),
                    "cmd_defaultvalue" => $this->request->getPost('defaultvalue_cmd_'.$i),
                    "cmd_developerNotes" => $this->request->getPost('developerNotes_cmd_'.$i)
            );
        }
    
    
    
    
     for ($i = 1; $i <= $respframe_size; $i++) {
    
    $asperuiData = $this->request->getPost('asperui_resp_'.$i);
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $resp_as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('sno_asperui_resp'.$i);
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $resp_sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
         $asperuivalueData = $this->request->getPost('asperuivalue_resp_'.$i);
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $resp_as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
     
            $respframe_data[] = array(
                "resp_startBytePosition" => $this->request->getPost('startBytePosition_resp_'.$i),
                    "resp_endBytePosition" => $this->request->getPost('endBytePosition_resp_'.$i),
                    "resp_fieldDescription" => $this->request->getPost('fieldDescription_resp_'.$i),
                    "resp_numberOfBytes" => $this->request->getPost('numberOfBytes_resp_'.$i),
                    "resp_bitSize" => $this->request->getPost('bitSize_resp_'.$i),
                    "resp_dataType" => $this->request->getPost('dataType_resp_'.$i),
                    "resp_valueDefinition" => $this->request->getPost('valueDefinition_resp_'.$i),
                    "resp_displayValueMin" => $this->request->getPost('displayValueMin_resp_'.$i),
                    "resp_displayValueMax" => $this->request->getPost('displayValueMax_resp_'.$i),
                    "resp_valueInHexMin" => $this->request->getPost('valueInHexMin_resp_'.$i),
                    "resp_valueInHexMax" => $this->request->getPost('valueInHexMax_resp_'.$i),
                    "resp_uiType" => $this->request->getPost('uiType_resp_'.$i),
                    "resp_uiId" => $this->request->getPost('uiId_resp_'.$i),
                    "resp_asperui" => $resp_as_per_ui_array,
                    "resp_sno_asperui" => $resp_sno_as_per_ui_array,
                    "resp_asperuivalue" => $resp_as_per_ui_value_array,
                    "resp_uiPageNo" => $this->request->getPost('uiPageNo_resp_'.$i),            
                    "resp_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_resp_'.$i),
                    "resp_sliderStepValue" => $this->request->getPost('sliderStepValue_resp_'.$i),
                    "resp_defaultvalue" => $this->request->getPost('defaultvalue_resp_'.$i),
                    "resp_developerNotes" => $this->request->getPost('developerNotes_resp_'.$i)
    
    
            );
        }
    
    
    
    for ($i = 1; $i <= $errorframe_size; $i++) {
    
    $asperuiData = $this->request->getPost('asperui_error_'.$i);
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $error_as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('sno_asperui_error'.$i);
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $error_sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
         $asperuivalueData = $this->request->getPost('asperuivalue_error_'.$i);
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $error_as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
     
            $errorframe_data[] = array(
                "error_startBytePosition" => $this->request->getPost('startBytePosition_error_'.$i),
                    "error_endBytePosition" => $this->request->getPost('endBytePosition_error_'.$i),
                    "error_fieldDescription" => $this->request->getPost('fieldDescription_error_'.$i),
                    "error_numberOfBytes" => $this->request->getPost('numberOfBytes_error_'.$i),
                    "error_bitSize" => $this->request->getPost('bitSize_error_'.$i),
                    "error_dataType" => $this->request->getPost('dataType_error_'.$i),
                    "error_valueDefinition" => $this->request->getPost('valueDefinition_error_'.$i),
                    "error_displayValueMin" => $this->request->getPost('displayValueMin_error_'.$i),
                    "error_displayValueMax" => $this->request->getPost('displayValueMax_error_'.$i),
                    "error_valueInHexMin" => $this->request->getPost('valueInHexMin_error_'.$i),
                    "error_valueInHexMax" => $this->request->getPost('valueInHexMax_error_'.$i),
                    "error_uiType" => $this->request->getPost('uiType_error_'.$i),
                    "error_uiId" => $this->request->getPost('uiId_error_'.$i),
                    "error_asperui" => $error_as_per_ui_array,
                    "error_sno_asperui" => $error_sno_as_per_ui_array,
                    "error_asperuivalue" => $error_as_per_ui_value_array,
                    "error_uiPageNo" => $this->request->getPost('uiPageNo_error_'.$i),      
                    "error_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_error_'.$i),
                    "error_sliderStepValue" => $this->request->getPost('sliderStepValue_error_'.$i),
                    "error_defaultvalue" => $this->request->getPost('defaultvalue_error_'.$i),
                    "error_developerNotes" => $this->request->getPost('developerNotes_error_'.$i)
    
    
            );

        }
    
    
    
    for ($i = 1; $i <= $errorframe_size; $i++) {
    
    $asperuiData = $this->request->getPost('asperui_error_'.$i);
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $error_as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('sno_asperui_error'.$i);
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $error_sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
         $asperuivalueData = $this->request->getPost('asperuivalue_error_'.$i);
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $error_as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
     
            $errorframe_data[] = array(
                "error_startBytePosition" => $this->request->getPost('startBytePosition_error_'.$i),
                    "error_endBytePosition" => $this->request->getPost('endBytePosition_error_'.$i),
                    "error_fieldDescription" => $this->request->getPost('fieldDescription_error_'.$i),
                    "error_numberOfBytes" => $this->request->getPost('numberOfBytes_error_'.$i),
                    "error_bitSize" => $this->request->getPost('bitSize_error_'.$i),
                    "error_dataType" => $this->request->getPost('dataType_error_'.$i),
                    "error_valueDefinition" => $this->request->getPost('valueDefinition_error_'.$i),
                    "error_displayValueMin" => $this->request->getPost('displayValueMin_error_'.$i),
                    "error_displayValueMax" => $this->request->getPost('displayValueMax_error_'.$i),
                    "error_valueInHexMin" => $this->request->getPost('valueInHexMin_error_'.$i),
                    "error_valueInHexMax" => $this->request->getPost('valueInHexMax_error_'.$i),
                    "error_uiType" => $this->request->getPost('uiType_error_'.$i),
                    "error_uiId" => $this->request->getPost('uiId_error_'.$i),
                    "error_asperui" => $error_as_per_ui_array,
                    "error_sno_asperui" => $error_sno_as_per_ui_array,
                    "error_asperuivalue" => $error_as_per_ui_value_array,
                    "error_uiPageNo" => $this->request->getPost('uiPageNo_error_'.$i),      
                    "error_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_error_'.$i),
                    "error_sliderStepValue" => $this->request->getPost('sliderStepValue_error_'.$i),
                    "error_defaultvalue" => $this->request->getPost('defaultvalue_error_'.$i),
                    "error_developerNotes" => $this->request->getPost('developerNotes_error_'.$i)
    
    
            );
        }
    
    
    $additional_data = isset($additional_data) ? $additional_data : [];
    $respframe_data = isset($respframe_data) ? $respframe_data : [];
    $errorframe_data = isset($errorframe_data) ? $errorframe_data : [];
    if ($this->Items_model->saveapi($item_data, $additional_data, $respframe_data, $errorframe_data, $user_id)) {
            
  /*  echo "Data saved successfully!";
    
    echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";
    
    echo "Additional Data: <pre>";
    print_r($additional_data);
    echo "</pre>";
    
    echo "Response Data: <pre>";
    print_r($respframe_data);
    echo "</pre>";
    
    echo "ERROR Data: <pre>";
    print_r($errorframe_data);
    echo "</pre>";*/
    
    echo "<script>alert('Data saved successfully!');</script>";
    echo "Data saved successfully!";
    echo "<script>
    location.href='https://efactor.tech/index.php/items/vendor';
    </script>";
    } 
    
    else {
       // echo "<script>alert('Error saving data to database!');</script>";
             echo "Error saving data to database!";
    
        }
    }
    
     
    function saveapi_edit() {
    
    /*
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // $targetDirectory = "/var/www/html/files/brand_logo/";
        $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      $originalFileName = basename($_FILES["brandlogo"]["name"]);
        $targetFile = $targetDirectory . $originalFileName;
    
        // Check if the uploaded file exists
        if (!isset($_FILES["brandlogo"])) {
            echo "No file uploaded.";
            exit;
        }
     
        // Check if target directory exists
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        }
    
        // Move the uploaded file to the target directory
        $tempFile = $_FILES["brandlogo"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["brandlogo"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
      
      
      
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     //  $targetDirectory = "/var/www/html/files/brand_logo/";
       $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
      $originalFileName2 = basename($_FILES["imageUpload"]["name"]);
        $targetFile = $targetDirectory . $originalFileName2;
    
        // Check if the uploaded file exists
        if (!isset($_FILES["imageUpload"])) {
            echo "No file uploaded.";
            exit;
        }
     
        // Check if target directory exists
        if (!file_exists($targetDirectory)) {
            echo "Target directory does not exist.";
            exit;
        }
    
        // Move the uploaded file to the target directory
        $tempFile = $_FILES["imageUpload"]["tmp_name"];
        if (move_uploaded_file($tempFile, $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["imageUpload"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
        */
        // Retrieve form data
        $item_data = array(
            "brandname" => $this->request->getPost('brandname'),
           //  "brandlogo" => $originalFileName,
            "productname" => $this->request->getPost('productname'),
          //  "imageDropdown" => $originalFileName2,
            "modelname" => $this->request->getPost('modelname'),
            "productdescription" => $this->request->getPost('productdescription'),
            "numberofpages" => $this->request->getPost('numberofpages'),
            "Sequence" => $this->request->getPost('Sequence'),
            "pagenames" => $this->request->getPost('pagenames'),
            "date" => $this->request->getPost('date'),
    
    
            "cmd_parametername" => $this->request->getPost('cmd_parametername'),
            "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),
            "cmd_noofrows" => $this->request->getPost('cmd_noofrows'),
            "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
            "cmd_frametrigger" => $this->request->getPost('cmd_frametrigger'),
            "cmd_multipleui" => $this->request->getPost('cmd_multipleui'),
             "cmd_triggerui" => $this->request->getPost('cmd_triggerui'),
           
    
            "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),
            "resp_noofrows" => $this->request->getPost('resp_noofrows'),
            "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
            "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
              "resp_multipleui" => $this->request->getPost('resp_multipleui'),
             "resp_triggerui" => $this->request->getPost('resp_triggerui'),
    
            "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),
             "error_noofrows" => $this->request->getPost('error_noofrows'),
            "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
            "error_frametrigger" => $this->request->getPost('error_frametrigger'),
            "error_multipleui" => $this->request->getPost('error_multipleui'),
             "error_triggerui" => $this->request->getPost('error_triggerui')
    
    
            );
    
        // Additional table data
      $additional_data = array();
    
    // Get the number of rows from the form
    $cmdframe_size = (int) $this->request->getPost('cmd_noofrows');
        $respframe_size = (int)$this->request->getPost('resp_noofrows');
         $errorframe_size = (int)$this->request->getPost('error_noofrows');
    echo "<script>console.log('cmdframe_size: " . $cmdframe_size . "');</script>";
    
     
     
    for ($i = 1; $i <= $cmdframe_size; $i++) {
    
        $asperuiData = $this->request->getPost('cmd_asperui')[$i - 1];
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('cmd_sno_asperui')[$i - 1];
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
        $asperuivalueData = $this->request->getPost('cmd_asperuivalue')[$i - 1];
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
        
        $additional_data[] = array(
            "cmd_startBytePosition" => $this->request->getPost('startBytePosition_cmd')[$i - 1],
            "cmd_endBytePosition" => $this->request->getPost('endBytePosition_cmd')[$i - 1],
            "cmd_fieldDescription" => $this->request->getPost('fieldDescription_cmd')[$i - 1],
            "cmd_numberOfBytes" => $this->request->getPost('numberOfBytes_cmd')[$i - 1],
            "cmd_bitSize" => $this->request->getPost('bitSize_cmd')[$i - 1],
            "cmd_dataType" => $this->request->getPost('dataType_cmd')[$i - 1],
            "cmd_valueDefinition" => $this->request->getPost('valueDefinition_cmd')[$i - 1],
            "cmd_displayValueMin" => $this->request->getPost('displayValueMin_cmd')[$i - 1],
            "cmd_displayValueMax" => $this->request->getPost('displayValueMax_cmd')[$i - 1],
            "cmd_valueInHexMin" => $this->request->getPost('valueInHexMin_cmd')[$i - 1],
            "cmd_valueInHexMax" => $this->request->getPost('valueInHexMax_cmd')[$i - 1],
            "cmd_uiType" => $this->request->getPost('uiType_cmd')[$i - 1],
            "cmd_uiId" => $this->request->getPost('uiId_cmd')[$i - 1],
            "cmd_uiPageNo" => $this->request->getPost('uiPageNo_cmd')[$i - 1],
            "cmd_asperui" => $as_per_ui_array,
            "cmd_sno_asperui" => $sno_as_per_ui_array,
            "cmd_asperuivalue" => $as_per_ui_value_array,
            "cmd_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_cmd')[$i - 1],
            "cmd_sliderStepValue" => $this->request->getPost('sliderStepValue_cmd')[$i - 1],
            "cmd_defaultvalue" => $this->request->getPost('defaultvalue_cmd')[$i - 1],
            "cmd_developerNotes" => $this->request->getPost('developerNotes_cmd')[$i - 1]
        );
    }
    
    
    
    
    
    
    
    for ($i = 1; $i <= $respframe_size; $i++) {
    
        $asperuiData = $this->request->getPost('resp_asperui')[$i - 1];
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('resp_sno_asperui')[$i - 1];
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
        $asperuivalueData = $this->request->getPost('resp_asperuivalue')[$i - 1];
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
        
        $respframe_data[] = array(
            "resp_startBytePosition" => $this->request->getPost('startBytePosition_resp')[$i - 1],
            "resp_endBytePosition" => $this->request->getPost('endBytePosition_resp')[$i - 1],
            "resp_fieldDescription" => $this->request->getPost('fieldDescription_resp')[$i - 1],
            "resp_numberOfBytes" => $this->request->getPost('numberOfBytes_resp')[$i - 1],
            "resp_bitSize" => $this->request->getPost('bitSize_resp')[$i - 1],
            "resp_dataType" => $this->request->getPost('dataType_resp')[$i - 1],
            "resp_valueDefinition" => $this->request->getPost('valueDefinition_resp')[$i - 1],
            "resp_displayValueMin" => $this->request->getPost('displayValueMin_resp')[$i - 1],
            "resp_displayValueMax" => $this->request->getPost('displayValueMax_resp')[$i - 1],
            "resp_valueInHexMin" => $this->request->getPost('valueInHexMin_resp')[$i - 1],
            "resp_valueInHexMax" => $this->request->getPost('valueInHexMax_resp')[$i - 1],
            "resp_uiType" => $this->request->getPost('uiType_resp')[$i - 1],
            "resp_uiId" => $this->request->getPost('uiId_resp')[$i - 1],
            "resp_uiPageNo" => $this->request->getPost('uiPageNo_resp')[$i - 1],
            "resp_asperui" => $as_per_ui_array,
            "resp_sno_asperui" => $sno_as_per_ui_array,
            "resp_asperuivalue" => $as_per_ui_value_array,
            "resp_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_resp')[$i - 1],
            "resp_sliderStepValue" => $this->request->getPost('sliderStepValue_resp')[$i - 1],
            "resp_defaultvalue" => $this->request->getPost('defaultvalue_resp')[$i - 1],
            "resp_developerNotes" => $this->request->getPost('developerNotes_resp')[$i - 1]
        );
    }
    
    
    for ($i = 1; $i <= $errorframe_size; $i++) {
    
    $asperuiData = $this->request->getPost('error_asperui')[$i - 1];
        $asperuiArray = array_map('trim', explode(',', $asperuiData));
        $as_per_ui_array = '[' . implode('][', $asperuiArray) . ']';
    
        $sno_asperuiData = $this->request->getPost('error_sno_asperui')[$i - 1];
        $sno_asperuiArray = array_map('trim', explode(',', $sno_asperuiData));
        $sno_as_per_ui_array = '[' . implode('][', $sno_asperuiArray) . ']';
        
         $asperuivalueData = $this->request->getPost('error_asperuivalue')[$i - 1];
        $asperuivalueArray = array_map('trim', explode(',', $asperuivalueData));
        $as_per_ui_value_array = '[' . implode('][', $asperuivalueArray) . ']';
        
        
       $errorframe_data[] = array(
            "error_startBytePosition" => $this->request->getPost('startBytePosition_error')[$i - 1],
            "error_endBytePosition" => $this->request->getPost('endBytePosition_error')[$i - 1],
            "error_fieldDescription" => $this->request->getPost('fieldDescription_error')[$i - 1],
            "error_numberOfBytes" => $this->request->getPost('numberOfBytes_error')[$i - 1],
            "error_bitSize" => $this->request->getPost('bitSize_error')[$i - 1],
            "error_dataType" => $this->request->getPost('dataType_error')[$i - 1],
            "error_valueDefinition" => $this->request->getPost('valueDefinition_error')[$i - 1],
            "error_displayValueMin" => $this->request->getPost('displayValueMin_error')[$i - 1],
            "error_displayValueMax" => $this->request->getPost('displayValueMax_error')[$i - 1],
            "error_valueInHexMin" => $this->request->getPost('valueInHexMin_error')[$i - 1],
            "error_valueInHexMax" => $this->request->getPost('valueInHexMax_error')[$i - 1],
            "error_uiType" => $this->request->getPost('uiType_error')[$i - 1],
            "error_uiId" => $this->request->getPost('uiId_error')[$i - 1],
            "error_uiPageNo" => $this->request->getPost('uiPageNo_error')[$i - 1],
            "error_asperui" => $as_per_ui_array,
            "error_sno_asperui" => $sno_as_per_ui_array,
            "error_asperuivalue" => $as_per_ui_value_array,
            "error_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_error')[$i - 1],
            "error_sliderStepValue" => $this->request->getPost('sliderStepValue_error')[$i - 1],
            "error_defaultvalue" => $this->request->getPost('defaultvalue_error')[$i - 1],
            "error_developerNotes" => $this->request->getPost('developerNotes_error')[$i - 1]
        );
    }
    
    
    $additional_data = isset($additional_data) ? $additional_data : [];
    $respframe_data = isset($respframe_data) ? $respframe_data : [];
    $errorframe_data = isset($errorframe_data) ? $errorframe_data : [];
    if ($this->Items_model->saveapi_edit($item_data, $additional_data, $respframe_data, $errorframe_data)) {
    
   /* echo "Data saved successfully!";
    
    echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";
    
    echo "Additional Data: <pre>";
    print_r($additional_data);
    echo "</pre>";
    
    echo "Response Data: <pre>";
    print_r($respframe_data);
    echo "</pre>";
    
    echo "ERROR Data: <pre>";
    print_r($errorframe_data);
    echo "</pre>";*/
    
    echo "Data saved successfully!";
    echo "<script>
    location.href='https://efactor.tech/index.php/items/vendor';
    </script>";
    
    } 
    
    else {
           
            echo "Error saving data to database!";
    
        }
    }

function save_pages(){
$item_data = array(
    "page_name" => $this->request->getPost('input_box'), 
    "page_extension" => $this->request->getPost('dropdown') 
);

$page_data = array(
    "user_id" => $this->request->getPost('user_id'), 
    "modelno" => $this->request->getPost('modelno'),
    "modal_id" => $this->request->getPost('modal_id'), 
);
 
$combined_data = array();
 
foreach ($item_data['page_name'] as $index => $page_name) { 
    $combined_data[] = $page_name . '.' . $item_data['page_extension'][$index];
}

 
              if ($this->Items_model->save_pages($combined_data, $page_data)) { 
    echo "Data saved successfully!";
  //print_r($combined_data);
    //echo "<script>
    //location.href='https://efactor.tech/index.php/items/vendor';
    //</script>";
    }  else {
       // echo "<script>alert('Error saving data to database!');</script>";
             echo "Error saving data to database!";
    
        }
}


function savecommandapi() {


         $id = $this->request->getPost('id');
         $rand=rand('0000','9999');
         $file_name = $rand.'_'.$this->request->getPost("item_files");
         $target_path = get_setting("timeline_file_path");
         $file_info = move_temp_file($file_name, $target_path);

       $item_data = array(
            "filename" => $target_path.$file_name,
            "itemid" => $id,
            
               "cmd_parametername" => $this->request->getPost('cmd_parametername'),
               "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),    
               "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
               "cmd_frametrigger" => $this->request->getPost('cmd_frametrigger')                          
        );



          $item_id = $this->Items_model->savecommandapi($item_data);
          // $options = array("id" => $item_id);
          //  $item_info = $this->Items_model->get_details($options)->getRow();

          // echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info),  'message' => 'Tested Successfully'));

          //  echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";

}




function saveresponseapi() {


         $id = $this->request->getPost('id');


          $rand=rand('0000','9999');

        $file_name = $rand.'_'.$this->request->getPost("item_files");
        //$target_path = getcwd() . "/";//getcwd() . "/" . get_general_file_path("client", $client_id);

        $target_path = get_setting("timeline_file_path");

        //$file_name = $this->request->getPost('file_name_' . $file);

         $file_info = move_temp_file($file_name, $target_path);

       $item_data = array(
            "filename" => $target_path.$file_name,
            "itemid" => $id,
            
             //"resp_parametername" => $this->request->getPost('cmd_parametername'),
               "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),    
             //"frametype" => $this->request->getPost('frametype'),
               "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
               "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
             //"voicecommand" => $this->request->getPost('voicecommand'),
             //"date" => $this->request->getPost('date'),
             //"apidate" => date("Y-m-d h:i A")
            
        );








          $item_id = $this->Items_model->saveresponseapi($item_data);

          // $options = array("id" => $item_id);
          //  $item_info = $this->Items_model->get_details($options)->getRow();

          // echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info),  'message' => 'Tested Successfully'));

          //  echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";

          

     


       }





function saveerrorapi() {


         $id = $this->request->getPost('id');


          $rand=rand('0000','9999');

        $file_name = $rand.'_'.$this->request->getPost("item_files");
        //$target_path = getcwd() . "/";//getcwd() . "/" . get_general_file_path("client", $client_id);

        $target_path = get_setting("timeline_file_path");

        //$file_name = $this->request->getPost('file_name_' . $file);

         $file_info = move_temp_file($file_name, $target_path);

       $item_data = array(
            "filename" => $target_path.$file_name,
            "itemid" => $id,
            
             //  "resp_parametername" => $this->request->getPost('cmd_parametername'),
               "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),    
             //  "frametype" => $this->request->getPost('frametype'),
               "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
               "error_frametrigger" => $this->request->getPost('error_frametrigger'),
              // "voicecommand" => $this->request->getPost('voicecommand'),
              // "date" => $this->request->getPost('date'),

           // "apidate" => date("Y-m-d h:i A")
            
        );







          $item_id = $this->Items_model->saveerrorapi($item_data);

          // $options = array("id" => $item_id);
          //  $item_info = $this->Items_model->get_details($options)->getRow();

          // echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info),  'message' => 'Tested Successfully'));

            echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";

          

     


       }




/*


public function sticker_update($id = null)
{
    // Get modelno and modelname from query parameters
    $modelno = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
    
    // Pass both to the view
    return view('items/sticker_stu');
}*/


function sticker_update($id = null)
{
    // Get parameters from query string
    $modelno = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
    $category = $this->request->getGet('category');


        return view('items/sticker_update', [
        'modelno' => $modelno,
        'modelname' => $modelname,
        'category' => $category,
        'id' => $id
    ]);
}





public function sticker_print($id = null)
{
    // Get modelno and modelname from query parameters
    $modelno = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
     $category = $this->request->getGet('category');


    
    // Pass both to the view
     return view('items/sticker_stu', [
        'modelno' => $modelno,
        'modelname' => $modelname,
        'category' => $category,
        'id' => $id
    ]);

}



public function sticker_display($id = null)
{
    // Get modelno and modelname from query parameters
    $modelno = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
     $category = $this->request->getGet('category');


    
    // Pass both to the view
     return view('items/display_lable', [
        'modelno' => $modelno,
        'modelname' => $modelname,
        'category' => $category,
        'id' => $id
    ]);

}





public function label_controller() {
    $node_id = $_POST['node_id'] ?? '';
    $label_name = $_POST['label_name'] ?? '';

    $uploadDir = '/var/www/html/files/brand_logo/';
    $uploaded_path = '';

    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === 0) {
        $imageTmpPath = $_FILES['imageUpload']['tmp_name'];
        $imageName = basename($_FILES['imageUpload']['name']);
        $destination = $uploadDir . $imageName;

        if (move_uploaded_file($imageTmpPath, $destination)) {
            $uploaded_path = $destination;

            // Debug print
            echo "Upload successful!<br>";
            echo "Node ID: $node_id<br>";
            echo "Label Name: $label_name<br>";
            echo "Image Path: $uploaded_path<br>";

            
            $result = $this->Items_model->save_display_lable($node_id, $label_name, $uploaded_path);

            if ($result) {
                echo "<br>Label data saved successfully.";
            } else {
                echo "<br>Failed to save label data.";
            }

        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "No image uploaded or error in upload.";
    }
}









/*****************************************************************************************/

//         function savecat() {
//   // $user_id = getUserIdFromSession();
// session_start();
// $user_id = $_SESSION['user_id'];
 
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//       $targetDirectory = "/var/www/html/files/brand_logo/";
//     //  $targetDirectory = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/";
//    // $targetDirectory = "/Program Files/Ampps/www/factor_17-05/files/brandLogo/";
//     $originalFileName = basename($_FILES["prodimg"]["name"]);
//       $targetFile = $targetDirectory . $originalFileName;
  
//       if (!isset($_FILES["prodimg"])) {
//           echo "No file uploaded.";
//           exit;
//       }
//       if (!file_exists($targetDirectory)) {
//           echo "Target directory does not exist.";
//           exit;
//       }
//       $tempFile = $_FILES["prodimg"]["tmp_name"];
//       if (move_uploaded_file($tempFile, $targetFile)) {
//           echo "The file " . htmlspecialchars(basename($_FILES["prodimg"]["name"])) . " has been uploaded.";
//       } else {
//           echo "Sorry, there was an error uploading your file.";
//       }
//   }

//        $name = $this->request->getPost('name');
//        $prodimg = $originalFileName;
 
//     $result = $this->Items_model->save_cat($name,$prodimg,$user_id);

//     // Check the result
//     if ($result !== true) {
//         //  location.href='http://localhost/factor_17-05/index.php/items/brand';
//                  $this->session->setFlashdata('error', $result);
//         // echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";
//         echo "<script>
//       location.href='https://efactor.tech/index.php/items/product';
//     </script>";
//      }else{
//          echo "<script>
//         location.href='https://efactor.tech/index.php/items/product';
//       </script>";
//      }

//         }
function savecat() {
    session_start();
    $user_id = $_SESSION['user_id'];

    $prodimg = '';  
$name = $this->request->getPost('name');
$existing_prodimg = $this->request->getPost('existing_prodimg');

// ✅ Define target directory FIRST!
$targetDirectory = $_SERVER['DOCUMENT_ROOT'] . "/files/UPLOADED BRAND-LOGO/";

// ✅ Ensure folder exists
if (!is_dir($targetDirectory)) {
    mkdir($targetDirectory, 0777, true);
}

$prodimg = '';

if (isset($_FILES["prodimg"]) && $_FILES["prodimg"]["error"] == UPLOAD_ERR_OK) {
    $originalFileName = basename($_FILES["prodimg"]["name"]);
    $fileExt = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . "." . $fileExt;
    $targetFile = $targetDirectory . $newFileName;

    if (move_uploaded_file($_FILES["prodimg"]["tmp_name"], $targetFile)) {
        $prodimg = $newFileName;
    } else {
        $prodimg = '';
    }
}

// ✅ Use existing if no new file
if (empty($prodimg)) {
    $prodimg = $existing_prodimg;
}

// ✅ Finally save to DB
$result = $this->Items_model->save_cat($name, $prodimg, $user_id);


    // ✅ Get form data
    $name = $this->request->getPost('name');

    // ✅ Save to DB
    $result = $this->Items_model->save_cat($name, $prodimg, $user_id);

    // ✅ Redirect
    if ($result !== true) {
        echo "<script>location.href='https://efactor.tech/index.php/items/product';</script>";
    } else {
        echo "<script>location.href='https://efactor.tech/index.php/items/product';</script>";
    }
}


    function save() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

           if($this->request->getPost('testing')==1)
     {
         $id = $this->request->getPost('id');

          $item_data = array(
            "teststatus" => $this->request->getPost('teststatus'),
            "testremarks" => $this->request->getPost('testremarks'),
            "testdate" => date("Y-m-d h:i A")
            
        );

          $item_id = $this->Items_model->ci_save($item_data, $id);

           $options = array("id" => $item_id);
            $item_info = $this->Items_model->get_details($options)->getRow();

          // echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info),  'message' => 'Tested Successfully'));

            echo "<script>location.href='https://efactor.tech/index.php/items';</script>";

          

     }
     else
     {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "category_id" => "required",
        ));

        $id = $this->request->getPost('id');

        $item_data = array(
          //  "title" => $this->request->getPost('title'),
          //  "description" => $this->request->getPost('description'),
            "uid" => $this->request->getPost('uid'),
            "serialno" => $this->request->getPost('serialno'),
            "item_firmversion" => $this->request->getPost('item_firmversion'),
            "item_modelversion" => $this->request->getPost('item_modelversion'),
            "teststatus" => $this->request->getPost('teststatus'),
            "testremarks" => $this->request->getPost('testremarks'),
            "testdate" => $this->request->getPost('testdate'),
            "category_id" => $this->request->getPost('category_id'),
            "testedby" => $this->request->getPost('testedby'),
           // "unit_type" => $this->request->getPost('unit_type'),
            "rate" => unformat_currency($this->request->getPost('item_rate'))
           // "show_in_client_portal" => $this->request->getPost('show_in_client_portal') ? $this->request->getPost('show_in_client_portal') : ""
        );

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "item");
        $new_files = unserialize($files_data);

        if ($id) {
            $item_info = $this->Items_model->get_one($id);
            $timeline_file_path = get_setting("timeline_file_path");

            $new_files = update_saved_files($timeline_file_path, $item_info->files, $new_files);
        }

        $item_data["files"] = serialize($new_files);

        $item_id = $this->Items_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Items_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

     }
    }

    /* delete or undo an item */

    function delete() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Items_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Items_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Items_model->delete($id)) {
                $item_info = $this->Items_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }


    //deleteapiproduct
        function deleteapiproduct() {

     $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Items_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Items_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Items_model->delete($id)) {
                $item_info = $this->Items_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }



     }

     function deletecat() {

     $id = $this->request->getPost('id');
     $this->Items_model->deletecat($id);

     echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));


     }



    function deletebrand() {

     $id = $this->request->getPost('id');
     $this->Items_model->deletebrand($id);

     echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));


     }





     //deletevendorproduct
      function deletevendorproduct() {

     $id = $this->request->getPost('id');
     $this->Items_model->deletevendorproduct($id);

     echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));


     }


function deleteapi($modelname, $parameter_name, $product_name) {

    // $id = $this->request->getPost('id');
     $this->Items_model->deleteapi($modelname, $parameter_name, $product_name);

     echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));


     }







       function deletemapped() {

     $id = $this->request->getPost('id');
     $this->Items_model->deletemapped($id);

     echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));


     }

    /* list of items, prepared for datatable  */

     




function list_data_product() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $category_id = $this->request->getPost('category_id');
    $node_id = $this->request->getPost('node_id'); // Retrieve node_id from POST data

//$node_id='FFFFFFFFFFFFFFFF7EC2000000000200';
        $list_data = $this->Items_model->get_details_product($node_id)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row5($data);
             
        }
        echo json_encode(array("data" => $result));
    }


private function _make_item_row5($data) {
return array(
     
            $data->category,
            $data->module_type,
           
            $data->vendor_id,
            $data->mac,
            $data->payload,

            
          
            $data->vendor_name,



            $data->access_token,
            $data->request_id,
           
            $data->ipaddress,
           
           
            $data->time,
            $data->phone,

            $data->product_status,
            $data->current_devicetype,
          
            $data->share_user,
            $data->user_priority,
            $data->email,
            $data->app_version,
            $data->share_req_id,
            $data->connect_type,
            $data->share_req_status,

            $data->add_node_time,
            $data->ssid,
            $data->wifi_pwd 
    
  
 
);

   }




private function _make_item_row61($data) {


$internetStatus = $data->internet;
    switch ($internetStatus) {
        case 'true':
            $modeImage = '<img src="https://efactor.tech/files/home/green.png" style="width:20px;height:20px">';
            $internet_data='online';
            break;
        case 'false':
            $modeImage = '<img src="https://efactor.tech/files/home/red.png" style="width:20px;height:20px">';
            $internet_data='offline';
            break;
        default:
            $modeImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:20px;height:20px" alt="Default">';
            $internet_data='';
    }




$deviceName =$data->device_type;

    switch ($deviceName) {
        case 'fan':
            $deviceImage = '<img src="https://efactor.tech/files/home/fan.png" style="width:30px;height:30px">';
            break;
        case 'light':
            $deviceImage = '<img src="https://efactor.tech/files/home/light.png" style="width:30px;height:30px">';
            break;
        case 'swpcp':
            $deviceImage = '<img src="https://efactor.tech/files/home/swpcp.jpg" style="width:30px;height:30px">';
            break;
        case 'cooker':
            $deviceImage = '<img src="https://efactor.tech/files/home/cooker.jpg" style="width:30px;height:30px">';
            break;

       case 'switch':
            $deviceImage = '<img src="https://efactor.tech/files/home/switch.jpg" style="width:30px;height:30px">';
            break;

       case 'outlet':
            $deviceImage = '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:30px;height:30px">';
            break;


        
       case 'exhaust_hood':
            $deviceImage = '<img src="https://efactor.tech/files/home/exhaust_hood.jpg" style="width:30px;height:30px">';
            break;

        case 'gateway':
            $deviceImage = '<img src="https://efactor.tech/files/home/gateway.png" style="width:30px;height:30px">';
            break;
            
       case 'HBMDNC':
            $deviceImage = '<img src="https://efactor.tech/files/home/HBMDNC.jpg" style="width:70px;height:35px">';
            break;
            
        default:
            $deviceImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }







return array(
     
            $modeImage,
            $deviceImage,
            $data->node_id,           
            $data->device_type,
            $data->model_name,
            $data->internet,
            
            $data->latitude,
        $data->longitude,
            $data->user_id,
            $data->email,
            





                     modal_anchor(get_uri("items/modal_apisinfo/" . $data->node_id. "?model_number=" . $data->model_name . "&user_id=" . $data->user_id), "APIs Info", array("class" => "edit", "title" => 'Api Info', "data-post-id" => $data->id))






          
    
  
 
);

   }





function list_data_apis() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $category_id = $this->request->getPost('category_id');
        $options = array("category_id" => $category_id);

        $list_data = $this->Items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row6($data);
        }
        echo json_encode(array("data" => $result));
    }

/**************************************************/
/*function list_data_product() {
    $this->access_only_team_members();
    $this->validate_access_to_items();

    $category_id = $this->request->getPost('category_id');
    $node_id = $this->request->getPost('node_id'); // Retrieve node_id from the POST request

    $options = array(
        "category_id" => $category_id,
        "node_id" => $node_id // Include node_id in options
    );

    $list_data = $this->Items_model->get_details_product($options)->getResult();
    $result = array();
    foreach ($list_data as $data) {
        $result[] = $this->_make_item_row5($data, $node_id); // Pass node_id to the helper function
    }
    echo json_encode(array("data" => $result));
}*/



/**************************************************/


// private function _make_item_row6($data) {
// return array(
  /*   '12', // mode
    '12345', // node_id
   '12345',// brand
    '12345', // brand logo
    'Type A', // module_type
    'SN123456', // model_number
   '12345', // Device Name
   '12345', // Device
    '177', // user id
   '12345', // internet
    'ON', // power
    '40.712776', // latitude
    '-74.005974', // longitude
    '40.712776', // signin_latitude
    '-74.005974', // signin_longitude
    '40.712776', // node_latitude
    '-74.005974', // node_longitude
     '12', // mode
    '12345', // node_id
   '12345',// brand
    '12345', // brand logo
    'Type A', // module_type
    'SN123456', // model_number
   '12345', // Device Name
   '12345', // Device
    '177', // user id
   '12345', // internet
    'ON', // power
    '40.712776', // latitude
    '-74.005974', // longitude
    '40.712776', // signin_latitude
    '-74.005974', // signin_longitude
    '40.712776', // node_latitude
    '-74.005974', // node_longitude
     '40.712776', // latitude
    '-74.005974', // longitude
    '40.712776', // signin_latitude
    '-74.005974', // signin_longitude
    '40.712776', // node_latitude
    '-74.005974', // node_longitude    */
    
//             $data->payload,

           
//             $data->direction,
//             $data->Hue,
//             $data->name,
//             $data->power,
//             $data->speed,
//              $data->baudrate,
//             $data->link_comm_mode,

//             $data->timestamp,
//             $data->output,



//             $data->brightness,
//             $data->rpm,
//             $data->power_watt,
//             $data->current,
//             $data->bus_voltage,

//             $data->ac_volt,
//             $data->user_set_max,
//             $data->device_lock,
//             $data->device_set_max,

//             $data->power_fact,


//             $data->total_watt,

//             $data->serialno,
           
//             $data->led_indication,
           
//             $data->TZ_properties,
//             $data->BR_properties,

//             $data->UMI_properties,

//            $data->EC_properties,

//             $data->CM_properties,
//             $data->TU_properties,
//             $data->LC_properties,
           
//             $data->cook_time,

//             $data->cookermode,
//             $data->cook_run_temp,
//             $data->cook_max_temp,
//             $data->ping,

//             $data->cooker_properties,
//             $data->recipe,
//             $data->pre_time_const,

//             $data->keep_warm_timer,
//             $data->dishes,
          
 
 
// );

//    }









   function list_data20() {


//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];


        $list_data = $this->Items_model->get_cats($user_id)->getResult();
        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row3($data);
        }
        echo json_encode(array("data" => $result));

   }



function list_data30() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];

        $list_data = $this->Items_model->get_brands($user_id)->getResult();
        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row4($data);
        }
        echo json_encode(array("data" => $result));

   }

   //list_data_vnsettings

    function list_data_vnsettings() {

      $list_data = $this->Items_model->get_details_vnsettings($this->login_user->client_id)->getResult();
        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row_vnsettings($data);
        }
        echo json_encode(array("data" => $result));

   }



   //list_data_mapped
   function list_data_mapped() {

      $list_data = $this->Items_model->get_details_mapped()->getResult();
        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row_mapped($data);
        }
        echo json_encode(array("data" => $result));

   }

   //list_data1vendor
   function list_data1vendor() {

//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];

     $arr=array();


        $list_data = $this->Items_model->get_vendorproducts($user_id)->getResult();

//print_r($list_data);
        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row1_vendor($data);

                   }
        echo json_encode(array("data" => $result));


   }




    function list_data1() {
        //$this->access_only_team_members();
       // $this->validate_access_to_items();

      //  $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

      $arr=array();

       $ordersdata= $this->Items_model->get_orders($this->login_user->client_id)->getResult();

       foreach ($ordersdata as $ord) {

        $ordersitems= $this->Items_model->get_items($ord->id)->getResult();

         foreach ($ordersitems as $itm) {

            array_push($arr,$itm->item_id);

         }

        }

        $category_id = $this->request->getPost('category_id');
        $options = array("category_id" => $category_id);

        $list_data = $this->Items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {

            if(in_array($data->id, $arr))

            $result[] = $this->_make_item_row1($data);
        }
        echo json_encode(array("data" => $result));
    }


    private function _make_item_row2($data, $modelname) {

 //$image_folder = 'http://localhost/factor_17-05/files/brandLogo/';
   $image_folder = 'https://efactor.tech/files/brand_logo/';

    $logo_image_path = $image_folder . $data->brand_logo;
    $image_html = '<img src="' . $logo_image_path . '" alt="' . $data->brand_logo . ' Logo" style="width:60px;height:25px"> ';


$dev_image_path = $image_folder . $data->device_image;
    $dev_html = '<img src="' . $dev_image_path . '" alt="' . $data->device_image . ' Logo" style="width:50px;height:50px"> ';
       return array(
         
            $data->brand_name,
            $image_html,
            $data->product_name,
            $dev_html,
            $data->model_name,
            $data->product_description,
            $data->number_of_pages,
            $data->date,
            $data->itemid,
            $data->filename,
            $data->apidate,

        
             $data->parameter_name,
            $data->cmd_dataflow,
            $data->cmd_framesize,
            $data->cmd_noofrows,
            $data->cmd_frametrigger,
            $data->cmd_multipleui,
            $data->cmd_triggerui,
            $data->resp_dataflow,
            $data->resp_framesize,
            $data->resp_noofrows,
            $data->resp_frametrigger,
            $data->resp_multipleui,
            $data->resp_triggerui,
            $data->error_dataflow,
            $data->error_framesize,
            $data->error_noofrows,
            $data->error_frametrigger, 
            $data->error_multipleui,
            $data->error_triggerui,  

            $data->cmd_startbytePosition,
            $data->cmd_endbytePosition,
            $data->cmd_fielddescription,
            $data->cmd_numberOfbytes,
            $data->cmd_bitsize,
            $data->cmd_datatype,
            $data->cmd_valuedefinition,
            $data->cmd_displayvaluemin,
            $data->cmd_displayvaluemax,
            $data->cmd_valueinhexmin,
            $data->cmd_valueinhexmax,
            $data->cmd_uitype,
            $data->cmd_uiid,
            $data->cmd_uipageno,
            $data->cmd_asperui,
            $data->cmd_sno_asperui,
            $data->cmd_asperui_values,
            $data->cmd_uidescriptioninout, 
            $data->cmd_sliderstepvalue,
            $data->cmd_defaultvalue,
            $data->cmd_developernotes,   



            $data->resp_startBytePosition,
            $data->resp_endBytePosition,
            $data->resp_fieldDescription,
            $data->resp_numberOfBytes,
            $data->resp_bitSize,
            $data->resp_dataType,
            $data->resp_valueDefinition,
            $data->resp_displayValueMin,
            $data->resp_displayValueMax,
            $data->resp_valueInHexMin,
            $data->resp_valueInHexMax,
            $data->resp_uiType,
            $data->resp_uiId,
            $data->resp_uiPageNo,
            $data->resp_asperui,
            $data->resp_sno_asperui,
            $data->resp_asperui_values,
            $data->resp_uiDescriptionInOut, 
            $data->resp_sliderStepValue,
            $data->resp_defaultvalue,
            $data->resp_developerNotes, 



           

            $data->error_startBytePosition,
            $data->error_endBytePosition,
            $data->error_fieldDescription,
            $data->error_numberOfBytes,
            $data->error_bitSize,
            $data->error_dataType,
            $data->error_valueDefinition,
            $data->error_displayValueMin,
            $data->error_displayValueMax,
            $data->error_valueInHexMin,
            $data->error_valueInHexMax,
            $data->error_uiType,
            $data->error_uiId,
            $data->error_uiPageNo,
            $data->error_asperui,
            $data->error_sno_asperui,
            $data->error_asperui_values,
            $data->error_uiDescriptionInOut, 
            $data->error_sliderStepValue,
            $data->error_defaultvalue,
            $data->error_developerNotes,
            $data->user_id,
          
             

        
    
/*
modal_anchor(get_uri("items/modal_command/" . $data->model_name . "/" . $data->parameter_name), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))
    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletevendorproduct"), "data-action" => "delete"))*/

/*
modal_anchor(get_uri("items/modal_command/" . $data->model_name . "/" . $data->parameter_name. "/" . $data->product_name ), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))
    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletevendorproduct"), "data-action" => "delete"))*/  //working


modal_anchor(get_uri("items/modal_command/" . $data->model_name . "/" . $data->parameter_name. "/" . $data->product_name ), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))
    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deleteapi/" . $data->model_name . "/" . $data->parameter_name. "/" . $data->product_name), "data-action" => "delete"))



        );

    }

// here we change the add product code for

    /* prepare a row of item list table */

    // private function _make_item_row3($data) {

    //     return array(
    //      //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

    //        $data->name,
    //        $data->Product_image 
    //        ? '<img src="http://localhost/factor_17-05/files/brandLogo/' . $data->Product_image . '" alt="Brand Logo" style="max-width: 100px; max-height: 100px;">'
    //        : '<div style="width: 80px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">No Image</div>',
         
    //      modal_anchor(get_uri("items/modal_formcat_edit/".$data->id), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))   
    //       .js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletecat"), "data-action" => "delete"))
    //    );


    //     }
    private function _make_item_row3($data) {
    return array(
        $data->name,
        !empty($data->Product_image)
            ? '<img src="' . base_url('files/UPLOADED BRAND-LOGO/' . $data->Product_image) . '" alt="Product Image" style="max-width: 100px; max-height: 100px;">'
            : '<div style="width: 80px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">No Image</div>',

        modal_anchor(get_uri("items/modal_formcat_edit/" . $data->id), "Edit", array(
            "class" => "edit",
            "title" => "Edit",
            "data-post-id" => $data->id
        )) .
        js_anchor("<i data-feather='x' class='icon-16'></i>", array(
            "title" => app_lang('delete'),
            "class" => "delete",
            "data-id" => $data->id,
            "data-action-url" => get_uri("items/deletecat"),
            "data-action" => "delete"
        ))
    );
}

//ye save kiya hu kal ke liye
// private function _make_item_row3($data) {
//     return array(
//         $data->name,

//         !empty($data->Product_image)
//             ? '<img src="' . base_url('files/products/' . $data->Product_image) . '" alt="Product Image" style="max-width: 100px; max-height: 100px;" onerror="this.onerror=null;this.src=\'' . base_url('assets/images/no-image.png') . '\';">'
//             : '<div style="width: 80px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">No Image</div>',

//         modal_anchor(get_uri("items/modal_formcat_edit/" . $data->id), "Edit", array(
//             "class" => "edit",
//             "title" => "Edit",
//             "data-post-id" => $data->id
//         )) .
//         js_anchor("<i data-feather=\'x\' class=\'icon-16\'></i>", array(
//             "title" => app_lang('delete'),
//             "class" => "delete",
//             "data-id" => $data->id,
//             "data-action-url" => get_uri("items/deletecat"),
//             "data-action" => "delete"
//         ))
//     );
// }




 private function _make_item_row4($data) {

    return array(
        $data->name,
        !empty($data->brandlogo) 
        //    ? '<img src="https://efactor.tech/files/brandLogo/' . $data->brandlogo . '" alt="Brand Logo" style="max-width: 100px; max-height: 100px;">'
        ? '<img src="' . base_url('files/UPLOADED BRAND-LOGO/' . $data->brandlogo) . '" alt="Brand Logo" style="max-width: 100px; max-height: 100px;">'

            : '<div style="width: 80px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">No Image</div>',
              modal_anchor(get_uri("items/modal_formbrand_edit/".$data->id), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))   
        .js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletebrand"), "data-action" => "delete"))
    );
         }














         //_make_item_row_vnsettings
         private function _make_item_row_vnsettings($data) {

             return array(
          //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

            $data->gateway,
            $data->devices);
         }

         //_make_item_row_mapped
          private function _make_item_row_mapped($data) {


             return array(
          //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

            $data->serialno,
            $data->uid,
           // $data->deviceapi,

            //nl2br($data->description),
            $data->modelno,
            //$type,
           // $data->uniqueid,
           '',
          
            modal_anchor(get_uri("items/modal_addmapped"), "Add API/Product", array("class" => "edit", "title" => 'Edit Mapped', "data-post-id" => $data->id)),

            // modal_anchor(get_uri("items/modal_api"), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)).

            anchor(get_uri("items/viewapis/" . $data->id), 'View Devices List'). 
           
             js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletemapped"), "data-action" => "delete"))
        );


          }


       
          private function _make_item_row1_vendor_1($data) {
            return array(
                $data->modelname,
                $data->brand,
                $data->category,
                $data->modelno,

                anchor(get_uri("items/viewapis/" . $data->id), 'View Apis').       
                anchor(get_uri("items/callDisplayFunction/" . $data->modelno. "?category=" . $data->category), "View UI type", array("class" => "edit", "title" => 'View UI type')).
                anchor(get_uri("items/callDisplayId"), "DEVICE SELECT", array("class" => "edit", "title" => 'DEVICE SELECT'))
            );
        }




//             private function _make_item_row1_vendor($data) {
//     if ($data->modelno === 'EFDEFLT V1.0') {
//         return $this->_make_item_row1_vendor_1($data);
//     }

//     return array(
//         $data->modelname,
//         $data->brand,
//         $data->category,
//         $data->modelno,
        
//         anchor(get_uri("items/viewapis/" . $data->id), 'View Apis').
//         modal_anchor(get_uri("items/modal_api/".$data->id), "Add API/Product", array("class" => "edit", "title" => 'Add API/Product', "data-post-id" => $data->id)).
//         modal_anchor(get_uri("items/modal_pages/" . $data->id. "?modelno=" . $data->modelname), "Add Page Names", array("class" => "edit", "title" => 'Add Page Names')).
//         anchor(get_uri("items/api_page_linking/" . $data->id. "?modelno=" . $data->modelno), "Linking Page Name", array("class" => "edit", "title" => 'Linking Page Name')).
//         anchor(get_uri("items/specifications/" . $data->id. "?modelno=" . $data->modelno), "Specifications", array("class" => "edit", "title" => 'Specifications')).
//         anchor(get_uri("items/auto_test/" . $data->id. "?modelno=" . $data->modelno), "Auto Test", array("class" => "edit", "title" => 'Auto Test')).
//         anchor(get_uri("items/callDisplayFunction/" . $data->modelno. "?category=" . $data->category), "View UI type", array("class" => "edit", "title" => 'View UI type')).
//         anchor(get_uri("items/callDisplayId"), "DEVICE SELECT", array("class" => "edit", "title" => 'DEVICE SELECT')).
        
//         anchor(
//             get_uri("items/sticker_update/" . $data->id . "?modelno=" . $data->modelno . "&category=" . $data->category . "&modelname=" . urlencode($data->modelname)), 
//             "Sticker Update", 
//             array(
//                 "class" => "edit", 
//                 "title" => 'Sticker Update'
//             )
//         ).
        
//         anchor(   
//             '#', 
//             "Confirm", 
//             array(
//                 "class" => "edit", 
//                 "title" => 'Confirm', 
//                 "onclick" => "if(confirm('Do you want to create the table?')) { window.location.href='". get_uri('items/callConfirmDisplayFunction/') . $data->modelno."'; } return false;"
//             )
//         ).
//         js_anchor(
//             "<i data-feather='x' class='icon-16'></i>",
//             array(
//                 'title' => app_lang('delete'), 
//                 "class" => "delete", 
//                 "data-id" => $data->id, 
//                 "data-action-url" => get_uri("items/deletevendorproduct"), 
//                 "data-action" => "delete",
//                 "onclick" => "if(confirm('Are you sure you want to delete this?')) { 
//                     var id = $(this).data('id'); 
//                     var url = $(this).data('action-url'); 
//                 } return false;"
//             )
//         )
//     );
// }

           // js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletevendorproduct"), "data-action" => "delete"))
private function _make_item_row1_vendor($data) {
    return array(
        $data->modelname,
        $data->brand,
        $data->category,
        $data->modelno,

        
        '<div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenu' . $data->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu' . $data->id . '">
                <a class="dropdown-item" href="' . get_uri("items/viewapis/" . $data->id) . '"><i class="fa fa-eye"></i> View APIs</a>
                <a class="dropdown-item" data-act="ajax-modal" data-title="Add API/Product" data-action-url="' . get_uri("items/modal_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API/Product</a>
                <a class="dropdown-item" data-act="ajax-modal" data-title="Add Page Names" data-action-url="' . get_uri("items/modal_pages/" . $data->id . "?modelno=" . $data->modelname) . '" href="#"><i class="fa fa-file-text"></i> Add Pages</a>
                <a class="dropdown-item" href="' . get_uri("items/api_page_linking/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-link"></i> Linking Page</a>
                <a class="dropdown-item" href="' . get_uri("items/specifications/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-cogs"></i> Specifications</a>
                <a class="dropdown-item" href="' . get_uri("items/auto_test/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-flask"></i> Auto Test</a>
                <a class="dropdown-item" href="' . get_uri("items/callDisplayFunction/" . $data->modelno . "?category=" . $data->category) . '"><i class="fa fa-desktop"></i> View UI Type</a>
                <a class="dropdown-item" href="' . get_uri("items/callDisplayId") . '"><i class="fa fa-cube"></i> Device Select</a>
                <a class="dropdown-item" href="' . get_uri("items/sticker_update/" . $data->id . "?modelno=" . $data->modelno . "&category=" . $data->category . "&modelname=" . urlencode($data->modelname)) . '"><i class="fa fa-sticky-note"></i> Sticker Update</a>
                <a class="dropdown-item" href="#" onclick="if(confirm(\'Do you want to create the table?\')) { window.location.href=\'' . get_uri("items/callConfirmDisplayFunction/" . $data->modelno) . '\'; } return false;"><i class="fa fa-check"></i> Confirm</a>
                <a class="dropdown-item text-danger" href="#" onclick="if(confirm(\'Are you sure you want to delete this?\')) { window.location.href=\'' . get_uri("items/deletevendorproduct?id=" . $data->id) . '\'; } return false;"><i class="fa fa-trash"></i> Delete</a>
            </div>
        </div>'
    );
}





         
     private function _make_item_row1($data) {
        $type = $data->unit_type ? $data->unit_type : "";

        $show_in_client_portal_icon = "";
        if ($data->show_in_client_portal && get_setting("module_order")) {
            $show_in_client_portal_icon = "<span title='" . app_lang("showing_in_client_portal") . "'><i data-feather='shopping-bag' class='icon-16'></i></span> ";
        }

        if($data->teststatus!='') 
        {
            $admin='Admin';
            $test='';
            
        }
        else
        {
            $admin='';
            
            $test= modal_anchor(get_uri("items/modal_test"), "Make Test", array("class" => "edit", "title" => 'Testing', "data-post-id" => $data->id));
        }


        return array(
          //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

            $data->serialno,
            $data->purchased_date,
            $data->uid,

            //nl2br($data->description),
            $data->category_title ? $data->category_title : "-",
            //$type,
            $data->rate
          // $data->mappedstatus,
          // anchor(get_uri("items/viewapis/" . $data->id), 'View Apis'). 
          //  modal_anchor(get_uri("items/modal_api"), "Add API/Product", array("class" => "edit", "title" => 'Add API/Product', "data-post-id" => $data->id))

           // .  modal_anchor(get_uri("items/modal_mapped"), "Add Mapped Product", array("class" => "edit", "title" => 'Add Mapped Product', "data-post-id" => $data->id))
            //. js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/delete"), "data-action" => "delete"))
        );
    }


     function list_data2($itemid = 0) {
       // $this->access_only_allowed_members();

        $list_data = $this->Items_model->get_itemapis($itemid)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $modelname = $data->model_name;

         //  echo "Model Name: $modelname<br>";
            //  $modelname = 'motorV1.0';
            $result[] = $this->_make_item_row2($data, $modelname);
        }
        echo json_encode(array("data" => $result));
    }




    function viewapis($itemid = 0) {

        $view_data['itemid']=$itemid;

        return $this->template->rander("items/viewapis", $view_data);
    }
    
    

/*******************************************************************/



function list_data_brand() {

session_start();
$user_id = $_SESSION['user_id'];

$brand_data = $this->Items_model->get_brand_products($user_id)->getResult();

    $result = array();
        foreach ($brand_data as $data) {
            $result[] = $this->_make_item_row61($data);
           //  print_r($result);
             
        }
        echo json_encode(array("data" => $result));

}




/*
function list_data() {
 //  $this->access_only_team_members();
//    $this->validate_access_to_items2();

    $category_id = $this->request->getPost('category_id');
    $options = array("category_id" => $category_id);

    $list_data = $this->Items_model->get_details($options)->getResult();
    $result = array();

//print_r($list_data);

    foreach ($list_data as $data) {

         
        if ($this->login_user->is_admin || $data->user_id == $this->login_user->id) {

//if ($this->login_user->user_type === "lead" || $this->login_user->user_type === "User" || $this->  login_user->user_type == "Brand")

//{

            $result[] = $this->_make_item_row($data);
        }
    }

    echo json_encode(array("data" => $result));
}
*/



function list_data() {
    $draw = $this->request->getPost('draw');
    $start = $this->request->getPost('start');
    $length = $this->request->getPost('length');
    $search = $this->request->getPost('search')['value'];

    // Optional: filter by category
    $category_id = $this->request->getPost('category_id');
    $options = array("category_id" => $category_id, "search" => $search, "start" => $start, "length" => $length);

    $data = $this->Items_model->get_details($options)->getResult();
    $total_count = $this->Items_model->get_count($options);

    $result = [];
    foreach ($data as $item) {
        if ($this->login_user->is_admin || $item->user_id == $this->login_user->id) {
            $result[] = $this->_make_item_row($item);
        }
    }

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $total_count,
        "recordsFiltered" => $total_count,
        "data" => $result
    ]);
}

/***********************************************************/




private function _make_item_row($data) {


    $deviceName =$data->device_type;

    switch ($deviceName) {
        case 'fan':
            $deviceImage = '<img src="https://efactor.tech/files/home/fan.png" style="width:30px;height:30px">';
            break;
        case 'light':
            $deviceImage = '<img src="https://efactor.tech/files/home/light.png" style="width:30px;height:30px">';
            break;
        case 'swpcp':
            $deviceImage = '<img src="https://efactor.tech/files/home/swpcp.jpg" style="width:30px;height:30px">';
            break;
        case 'cooker':
            $deviceImage = '<img src="https://efactor.tech/files/home/cooker.jpg" style="width:30px;height:30px">';
            break;

       case 'switch':
            $deviceImage = '<img src="https://efactor.tech/files/home/switch.jpg" style="width:30px;height:30px">';
            break;

       case 'outlet':
            $deviceImage = '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:30px;height:30px">';
            break;


        
       case 'exhaust_hood':
            $deviceImage = '<img src="https://efactor.tech/files/home/exhaust_hood.jpg" style="width:30px;height:30px">';
            break;

       case 'gateway':
            $deviceImage = '<img src="https://efactor.tech/files/home/gateway.png" style="width:30px;height:30px">';
            break;

      case 'scanner':
            $deviceImage = '<img src="https://efactor.tech/files/brand_logo/scanner.jpg" style="width:30px;height:30px">';
            break;

            
       case 'HBMDNC':
            $deviceImage = '<img src="https://efactor.tech/files/home/HBMDNC.jpg" style="width:70px;height:35px">';
            break;
            
        default:
            $deviceImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }

 
   
$autotest = $data->auto_test;
    switch ($autotest) {
        case 'Pass':
            $testimg = '<img src="https://efactor.tech/files/home/Pass.png" style="width:30px;height:30px">';
            $autotest_data='Pass';
            break;
        case 'Fail':
            $testimg = '<img src="https://efactor.tech/files/home/Fail.png" style="width:30px;height:30px">';
            $autotest_data='Fail';
            break;
        case 'null':
            $testimg = '<img src="https://efactor.tech/files/home/Pending.png" style="width:30px;height:30px">';
            $autotest_data='null';
            break;
        default:
            $testimg = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
            $autotest_data='';
    }
    
      
 $internetStatus = $data->internet;
    switch ($internetStatus) {
        case 'true':
            $modeImage = '<img src="https://efactor.tech/files/home/green.png" style="width:20px;height:20px">';
            $internet_data='online';
            break;
        case 'false':
            $modeImage = '<img src="https://efactor.tech/files/home/red.png" style="width:20px;height:20px">';
            $internet_data='offline';
            break;
        default:
            $modeImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:20px;height:20px" alt="Default">';
            $internet_data='';
    }

    $brand = $data->vendor_name;

    switch ($brand) {
        case 'EFACTOR':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/efactor.jpg" style="width:50px;height:30px">';
            break;
        case 'HIWIN':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/hiwin.jpeg" style="width:30px;height:30px">';
            break;
        case 'DHRIO':     
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/dhrio.jpg" style="width:30px;height:30px">';
            break;

        case 'exhaust_hood':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/dimplelogom.png" style="width:30px;height:30px">';
            break;
        default:
            $brand_logo = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }
  
    $user_pic =  $data->user_id;
    if(empty($user_pic) || $user_pic == "Null" || $user_pic == "NULL" || $user_pic == "null"){
     $userpic = '<img src="https://efactor.tech/files/home/NoUser.png" style="width:30px;height:30px" alt="Default">';
    }else{
     $userpic = '<img src="http://efactor.tech/files/home/person.png" style="width:30px;height:30px" alt="Default">';
    }
 
    return array(
        $userpic,
       $testimg,
        $modeImage,
        $data->node_id,
       $data->vendor_name,
        $brand_logo,
        $data->device_type,
        $data->model_number,
        $data->device_type,
        $deviceImage,
        $data->user_id,
        $data->phone,
        $internet_data,
        $data->power,
        $data->latitude,
        $data->longitude,
        $data->signin_latitude,
        $data->signin_longitude,
        $data->node_latitude,
        $data->node_longitude,




        modal_anchor(get_uri("items/modal_productinfo/" . $data->node_id), "User & Product Info", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)), // Pass node_id
              modal_anchor(get_uri("items/modal_apisinfo/" . $data->node_id. "?model_number=" . $data->model_number . "&user_id=" . $data->user_id), "APIs Info", array("class" => "edit", "title" => 'Api Info', "data-post-id" => $data->id)),
              //anchor(get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id. "&userid=" . $data->user_id), "Control", array("class" => "edit", "title" => 'Control'))
              ($data->user_id !== null || $data->user_id !== NULL  || $data->user_id !== Null) 
              ? anchor(get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), "Control", array("class" => "edit", "title" => 'Control',
               "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;")) 
              : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Control</button>',
       ($data->user_id !== null || $data->user_id !== NULL  || $data->user_id !== Null) 
              ? anchor(get_uri("items/ChatDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), "Chat", array("class" => "edit", "title" => 'Control',
               "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;")) 
              : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Chat</button>'
      );








}   


/**************************************************/


/*

private static $deviceImages = [
    'fan' => '<img src="https://efactor.tech/files/home/fan.png" style="width:30px;height:30px">',
    'light' => '<img src="https://efactor.tech/files/home/light.png" style="width:30px;height:30px">',
    'swpcp' => '<img src="https://efactor.tech/files/home/swpcp.jpg" style="width:30px;height:30px">',
    'cooker' => '<img src="https://efactor.tech/files/home/cooker.jpg" style="width:30px;height:30px">',
    'switch' => '<img src="https://efactor.tech/files/home/switch.jpg" style="width:30px;height:30px">',
    'outlet' => '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:30px;height:30px">',
    'exhaust_hood' => '<img src="https://efactor.tech/files/home/exhaust_hood.jpg" style="width:30px;height:30px">',
    'gateway' => '<img src="https://efactor.tech/files/home/gateway.png" style="width:30px;height:30px">',
    'scanner' => '<img src="https://efactor.tech/files/brand_logo/scanner.jpg" style="width:30px;height:30px">',
    'HBMDNC' => '<img src="https://efactor.tech/files/home/HBMDNC.jpg" style="width:70px;height:35px">'
];

private static $testImages = [
    'Pass' => '<img src="https://efactor.tech/files/home/Pass.png" style="width:30px;height:30px">',
    'Fail' => '<img src="https://efactor.tech/files/home/Fail.png" style="width:30px;height:30px">',
    'null' => '<img src="https://efactor.tech/files/home/Pending.png" style="width:30px;height:30px">'
];

private static $internetStatusImages = [
    'true' => '<img src="https://efactor.tech/files/home/green.png" style="width:20px;height:20px">',
    'false' => '<img src="https://efactor.tech/files/home/red.png" style="width:20px;height:20px">'
];

private static $brandLogos = [
    'EFACTOR' => '<img src="https://efactor.tech/files/brand_logo/efactor.jpg" style="width:50px;height:30px">',
    'HIWIN' => '<img src="https://efactor.tech/files/brand_logo/hiwin.jpeg" style="width:30px;height:30px">',
    'DHRIO' => '<img src="https://efactor.tech/files/brand_logo/dhrio.jpg" style="width:30px;height:30px">',
    'exhaust_hood' => '<img src="https://efactor.tech/files/brand_logo/dimplelogom.png" style="width:30px;height:30px">'
];

private function _make_item_row($data) {
    // Device image
    $deviceImage = self::$deviceImages[$data->device_type] ?? 
                  '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';

    // Auto test
    $autotest = $data->auto_test ?? 'null';
    $testimg = self::$testImages[$autotest] ?? 
              '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    $autotest_data = in_array($autotest, ['Pass', 'Fail']) ? $autotest : 'null';

    // Internet status
    $internetStatus = $data->internet ?? '';
    $modeImage = self::$internetStatusImages[$internetStatus] ?? 
                '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:20px;height:20px" alt="Default">';
    $internet_data = ($internetStatus === 'true') ? 'online' : (($internetStatus === 'false') ? 'offline' : '');

    // Brand logo
    $brand_logo = self::$brandLogos[$data->vendor_name] ?? 
                 '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';

    // User picture
    $user_pic = $data->user_id ?? '';
    $userpic = (empty($user_pic) || strtolower($user_pic) === 'null') 
              ? '<img src="https://efactor.tech/files/home/NoUser.png" style="width:30px;height:30px" alt="Default">'
              : '<img src="http://efactor.tech/files/home/person.png" style="width:30px;height:30px" alt="Default">';

    // Control button conditions
    $hasUser = !empty($data->user_id) && strtolower($data->user_id) !== 'null';
    $controlBtn = $hasUser 
        ? anchor(
            get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), 
            "Control", 
            [
                "class" => "edit", 
                "title" => 'Control',
                "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;"
            ]
        )
        : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Control</button>';

    $chatBtn = $hasUser 
        ? anchor(
            get_uri("items/ChatDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), 
            "Chat", 
            [
                "class" => "edit", 
                "title" => 'Control',
                "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;"
            ]
        )
        : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Chat</button>';

    return [
        $userpic,
        $testimg,
        $modeImage,
        $data->node_id,
        $data->vendor_name,
        $brand_logo,
        $data->device_type,
        $data->model_number,
        $data->device_type,
        $deviceImage,
        $data->user_id,
        $data->phone,
        $internet_data,
        $data->power,
        $data->latitude,
        $data->longitude,
        $data->signin_latitude,
        $data->signin_longitude,
        $data->node_latitude,
        $data->node_longitude,
        modal_anchor(
            get_uri("items/modal_productinfo/" . $data->node_id), 
            "User & Product Info", 
            [
                "class" => "edit", 
                "title" => 'Edit', 
                "data-post-id" => $data->id
            ]
        ),
        modal_anchor(
            get_uri("items/modal_apisinfo/" . $data->node_id. "?model_number=" . $data->model_number . "&user_id=" . $data->user_id), 
            "APIs Info", 
            [
                "class" => "edit", 
                "title" => 'Api Info', 
                "data-post-id" => $data->id
            ]
        ),
        $controlBtn,
        $chatBtn
    ];
}


*/






/*---------------------------------------------------------*/


/*---------------------------------------------------------*/

function list_data_client() {
  // $this->access_only_team_members();
   // $this->validate_access_to_items2();

    $category_id = $this->request->getPost('category_id');
    $options = array("category_id" => $category_id);

    $list_data = $this->Items_model->get_details_client($options)->getResult();
    $result = array();

//print_r($list_data);

    foreach ($list_data as $data_client) {

         
      //  if ($this->login_user->is_admin || $data->user_id == $this->login_user->id) {
 // if ($this->login_user->is_admin || $this->login_user->user_type === "User" || $this->login_user->user_type == "Brand") {

            $result[] = $this->_make_item_row_client($data_client);
      //  }
    }

    echo json_encode(array("data" => $result));
}



private function _make_item_row_client($data_client) {
  /*  if (is_object($data) && isset($data->device_name)) {
        $deviceName = $data->device_name;
    } else {
        $deviceName = 'default_value';
    }*/

    $deviceName =$data->device_type;

    switch ($deviceName) {
        case 'fan':
            $deviceImage = '<img src="https://efactor.tech/files/home/fan.png" style="width:30px;height:30px">';
            break;
        case 'light':
            $deviceImage = '<img src="https://efactor.tech/files/home/light.png" style="width:30px;height:30px">';
            break;
        case 'swpcp':
            $deviceImage = '<img src="https://efactor.tech/files/home/swpcp.jpg" style="width:30px;height:30px">';
            break;
        case 'cooker':
            $deviceImage = '<img src="https://efactor.tech/files/home/cooker.jpg" style="width:30px;height:30px">';
            break;

       case 'switch':
            $deviceImage = '<img src="https://efactor.tech/files/home/switch.jpg" style="width:30px;height:30px">';
            break;

       case 'outlet':
            $deviceImage = '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:30px;height:30px">';
            break;


        
       case 'exhaust_hood':
            $deviceImage = '<img src="https://efactor.tech/files/home/exhaust_hood.jpg" style="width:30px;height:30px">';
            break;

        case 'gateway':
            $deviceImage = '<img src="https://efactor.tech/files/home/gateway.png" style="width:30px;height:30px">';
            break;
            
       case 'HBMDNC':
            $deviceImage = '<img src="https://efactor.tech/files/home/HBMDNC.jpg" style="width:70px;height:35px">';
            break;
            
        default:
            $deviceImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }

  /*  if (is_object($data) && isset($data->internet)) {
        $internetStatus = $data->internet;
    } else {
        $internetStatus = 'default_value';
    }*/

   
$autotest = $data->auto_test;
    switch ($autotest) {
        case 'Pass':
            $testimg = '<img src="https://efactor.tech/files/home/Pass.png" style="width:30px;height:30px">';
            $autotest_data='Pass';
            break;
        case 'Fail':
            $testimg = '<img src="https://efactor.tech/files/home/Fail.png" style="width:30px;height:30px">';
            $autotest_data='Fail';
            break;
             case 'null':
            $testimg = '<img src="https://efactor.tech/files/home/Pending.png" style="width:30px;height:30px">';
            $autotest_data='null';
            break;
        default:
            $testimg = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
            $autotest_data='';
    }
    
      
 $internetStatus = $data->internet;
    switch ($internetStatus) {
        case 'true':
            $modeImage = '<img src="https://efactor.tech/files/home/green.png" style="width:20px;height:20px">';
            $internet_data='online';
            break;
        case 'false':
            $modeImage = '<img src="https://efactor.tech/files/home/red.png" style="width:20px;height:20px">';
            $internet_data='offline';
            break;
        default:
            $modeImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:20px;height:20px" alt="Default">';
            $internet_data='';
    }

    $brand = $data->vendor_name;

    switch ($brand) {
        case 'efactor':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/efactor.jpg" style="width:50px;height:30px">';
            break;
        case 'HIWIN':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/hiwin.jpeg" style="width:30px;height:30px">';
            break;
            case 'exhaust_hood':
            $brand_logo = '<img src="https://efactor.tech/files/brand_logo/dimplelogom.png" style="width:30px;height:30px">';
            break;
        default:
            $brand_logo = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }
  
    $user_pic =  $data->user_id;
    if(empty($user_pic) || $user_pic == "Null" || $user_pic == "NULL" || $user_pic == "null"){
    // $userpic = '<img src="http://localhost/factor_17-05/files/home/NoUser.png" style="width:30px;height:30px" alt="Default">';
     $userpic = '<img src="https://efactor.tech/files/home/NoUser.png" style="width:30px;height:30px" alt="Default">';
    }else{
    // $userpic = '<img src="http://localhost/factor_17-05/files/home/person.png" style="width:30px;height:30px" alt="Default">';
     $userpic = '<img src="https://efactor.tech/files/home/person.png" style="width:30px;height:30px" alt="Default">';
    }
 
  /*  return array(
        $userpic,
       $testimg,
        $modeImage,
        $data->node_id,
       $data->vendor_name,
        $brand_logo,
        $data->device_type,
        $data->model_number,
        $data->device_type,
        $deviceImage,
        $data->user_id,
        $data->phone,
        //modal_anchor(get_uri("items/user_info/" . $node_id), "$data->user_id", array("class" => "edit", "title" => 'USER', "data-post-id" => $data->id)),
        $internet_data,
        $data->power,
        $data->latitude,
        $data->longitude,
        $data->signin_latitude,
        $data->signin_longitude,
        $data->node_latitude,
        $data->node_longitude,




        modal_anchor(get_uri("items/modal_productinfo/" . $data->node_id), "User & Product Info", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)), // Pass node_id
              modal_anchor(get_uri("items/modal_apisinfo/" . $data->node_id. "?model_number=" . $data->model_number . "&user_id=" . $data->user_id), "APIs Info", array("class" => "edit", "title" => 'Api Info', "data-post-id" => $data->id)),
              //anchor(get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id. "&userid=" . $data->user_id), "Control", array("class" => "edit", "title" => 'Control'))
              ($data->user_id !== null || $data->user_id !== NULL  || $data->user_id !== Null) 
              ? anchor(get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), "Control", array("class" => "edit", "title" => 'Control',
               "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;")) 
              : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Control</button>'
      );
*/


return array(
    "userpic.jpg", // Manual value for $userpic
    "testimg.png", // Manual value for $testimg
    "modeImage.jpg", // Manual value for $modeImage
    12345, // Manual value for $data->node_id
    "Vendor Name", // Manual value for $data->vendor_name
    "brand_logo.png", // Manual value for $brand_logo
    "Device Type", // Manual value for $data->device_type
    "Model123", // Manual value for $data->model_number
    "Device Type", // Manual value for $data->device_type (duplicate)
    "deviceImage.jpg", // Manual value for $deviceImage
    67890, // Manual value for $data->user_id
    "123-456-7890", // Manual value for $data->phone
    // Manual value for modal_anchor (commented out)
    "Internet Data", // Manual value for $internet_data
    "On", // Manual value for $data->power
    40.7128, // Manual value for $data->latitude
    -74.0060, // Manual value for $data->longitude
    40.7128, // Manual value for $data->signin_latitude
    -74.0060, // Manual value for $data->signin_longitude
    40.7128, // Manual value for $data->node_latitude
    -74.0060, // Manual value for $data->node_longitude

    // Manual value for modal_anchor (User & Product Info)
    modal_anchor(get_uri("items/modal_productinfo/12345"), "User & Product Info", array("class" => "edit", "title" => 'Edit', "data-post-id" => 1)),

    // Manual value for modal_anchor (APIs Info)
    modal_anchor(get_uri("items/modal_apisinfo/12345?model_number=Model123&user_id=67890"), "APIs Info", array("class" => "edit", "title" => 'Api Info', "data-post-id" => 1)),

    // Manual value for anchor (Control)
    (67890 !== null || 67890 !== NULL || 67890 !== Null) 
    ? anchor(get_uri("items/controlDisplayFunction/Model123?node_id=12345&userid=67890"), "Control", array("class" => "edit", "title" => 'Control',
        "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;")) 
    : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Control</button>'
);







}   













/*---------------------------------------------------------*/
    
 /****************************************************************************/   
  
     
   
    function upload_file() {
        $this->access_only_team_members();
        upload_file_to_temp();
    }

    function validate_items_file() {
        $this->access_only_team_members();
        $file_name = $this->request->getPost("file_name");
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if (is_image_file($file_name)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_valid_image_files')));
        }
    }

    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Items_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();

        $view_data['model_info'] = $model_info;
        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view('items/view', $view_data);
    }

    function save_files_sort() {
        $this->access_only_allowed_members();
        $id = $this->request->getPost("id");
        $sort_values = $this->request->getPost("sort_values");
        if ($id && $sort_values) {
            //extract the values from the :,: separated string
            $sort_array = explode(":,:", $sort_values);

            $item_info = $this->Items_model->get_one($id);
            if ($item_info->id) {
                $updated_file_indexes = update_file_indexes($item_info->files, $sort_array);
                $item_data = array(
                    "files" => serialize($updated_file_indexes)
                );

                $this->Items_model->ci_save($item_data, $id);
            }
        }
    }

    /* store criteria */

    function grid_view($offset = 0, $limit = 20, $category_id = 0, $search = "") {
        $this->check_access_to_store();

        $options = array("login_user_id" => $this->login_user->id);

        $item_search = $this->request->getPost("item_search");
        if ($item_search) {
            $search = $this->request->getPost("search");
            $category_id = $this->request->getPost("category_id") ? $this->request->getPost("category_id") : 0;
        }

        if ($search) {
            $options["search"] = $search;
        }

        if ($category_id) {
            $options["category_id"] = $category_id;
        }

        if ($this->login_user->user_type == "client") {
            $options["show_in_client_portal"] = 1; //show all items on admin side
        }

        //get all rows
        $all_items = $this->Items_model->get_details($options)->resultID->num_rows;

        $options["offset"] = $offset;
        $options["limit"] = $limit;

        $view_data["items"] = $this->Items_model->get_details($options)->getResult();

//        $view_data["items"] = $this->Items_model->get_details1($options)->getResult();
  foreach ($view_data["items"] as $item) {
        switch ($item->device_type) {
             case 'type1':
                $item->image_url = '/path/to/type1/image.jpg';
                break;
             case 'fan':
             $item->image_url  = '<img src="https://efactor.tech/files/home/fan_org.jpg" style="width:200px;height:180px;margin:20px">';
            break;
        case 'light':
             $item->image_url  = '<img src="https://efactor.tech/files/home/light_org.jpg" style="width:120px;height:160px;margin:40px">';
            break;
        case 'swpcp':
             $item->image_url  = '<img src="https://efactor.tech/files/home/swpcp_org.jpg" style="width:200px;height:180px;margin:20px">';
            break;
        case 'cooker':
             $item->image_url  = '<img src="https://efactor.tech/files/home/cooker_org.jpg" style="width:200px;height:180px;margin:20px">';
            break;

       case 'switch':
             $item->image_url  = '<img src="https://efactor.tech/files/home/switch_org.jpg" style="width:200px;height:180px;margin:20px">';
            break;

       case 'outlet':
            $item->image_url  = '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:200px;height:180px;margin:20px">';
            break;

       case 'exhaust_hood':
             $item->image_url  = '<img src="https://efactor.tech/files/home/exhausthood.jpg" style="width:200px;height:180px;margin:20px">';
            break;

        case 'Gateway':
             $item->image_url  = '<img src="https://efactor.tech/files/home/gateway_org.jpg" style="width:140px;height:160px;margin:25px">';
            break;
            
       case 'HBMDNC':
             $item->image_url  = '<img src="https://efactor.tech/files/home/HBMDNC_org.jpg" style="width:200px;height:180px;margin:20px">';
            break;
       case 'others':
           $item->image_url  = '<img src="https://efactor.tech/files/home/green.jpg" style="width:120px;height:150px;margin:50px">';
            break;
            
        default:
    $item->image_url  ='<img src="https://efactor.tech/files/home/green.jpg" style="width:120px;height:150px;margin:50px">';

        }
    }

        $view_data["result_remaining"] = $all_items - $limit - $offset;
        $view_data["next_page_offset"] = $offset + $limit;

        $view_data["search"] = $search;
        $view_data["category_id"] = $category_id;

        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        if ($offset) { //load more view
            return $this->template->view("items/items_grid_data", $view_data);
        } else if ($item_search) { //search suggestions view
            echo json_encode(array("success" => true, "data" => $this->template->view("items/items_grid_data", $view_data)));
        } else { //default view
            return $this->template->rander("items/grid_view", $view_data);
        }
    }

    function add_item_to_cart() {
        $this->check_access_to_store();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost("id");
        $item_info = $this->Items_model->get_one($id);
        $this->check_access_to_this_item($item_info);

        $order_item_data = array(
             "title" => $item_info->device_type,
            "quantity" => 1, //add 1 item first time
           // "unit_type" => $item_info->unit_type,
           // "rate" => $item_info->rate,
          //  "total" => $item_info->rate, //since the quantity is 1
          "node_id" => $item_info->node_id,
          "module_type" => $item_info->module_type,
            "created_by" => $this->login_user->id,
            "item_id" => $id
        );

       // $save_id = $this->Order_items_model->ci_save($order_item_data);
       $save_id = $this->Order_items_model->add_item_to_cart($order_item_data);

        if ($save_id) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function count_cart_items() {
        $this->check_access_to_store();

        $cart_items_count = count($this->Order_items_model->get_all_where(array("created_by" => $this->login_user->id, "order_id" => 0, "deleted" => 0))->getResult());

        if ($cart_items_count) {
            echo json_encode(array("success" => true, "cart_items_count" => $cart_items_count));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('no_record_found')));
        }
    }

    function load_cart_items() {
        $this->check_access_to_store();

        $view_data = get_order_making_data();

        $options = array("created_by" => $this->login_user->id, "processing" => true);
        $view_data["items"] = $this->Order_items_model->get_details($options)->getResult();
        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view("items/cart/cart_items_list", $view_data);
    }

    function delete_cart_item() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "required"
        ));

        $order_item_id = $this->request->getPost("id");
        $order_item_info = $this->Order_items_model->get_one($order_item_id);
        $this->check_access_to_this_order_item($order_item_info);

        if ($this->Order_items_model->delete($order_item_id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted'), "cart_total_view" => $this->_get_cart_total_view()));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }


    function change_cart_item_quantity() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "required",
            "action" => "required"
        ));

        $id = $this->request->getPost("id");
        $action = $this->request->getPost("action");

        $item_info = $this->Order_items_model->get_one($id);
        $this->check_access_to_this_order_item($item_info);

        if ($item_info->id) {
            $quantity = $item_info->quantity;
            if ($action == "plus") {
                //plus quantity
                $quantity = $quantity + 1;
            } else if ($action == "minus" && $quantity > 1) {
                //minus quantity
                //shouldn't be less than one
                $quantity = $quantity - 1;
            }

            $data = array(
                "quantity" => $quantity,
                "total" => $item_info->rate * $quantity
            );
            $this->Order_items_model->ci_save($data, $item_info->id);

            $options = array("id" => $id);
            $view_data["item"] = $this->Order_items_model->get_details($options)->getRow();
            $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

            echo json_encode(array("success" => true, 'message' => app_lang('record_saved'), "data" => $this->template->view("items/cart/cart_item_data", $view_data), "cart_total_view" => $this->_get_cart_total_view()));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function _get_cart_total_view() {
        $view_data = get_order_making_data();
        return $this->template->view('items/cart/cart_total_section', $view_data);
    }
    
}
//chnage from hear









                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
/* End of file items.php */
/* Location: ./app/controllers/items.php */

  //stop
  //stop
  //stop
  //stop                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             