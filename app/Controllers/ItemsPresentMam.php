<?php

namespace App\Controllers;

use App\Models\Items_model;

//use WebSocket\Client;

//require 'vendor/autoload.php';
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

   

        //load items list view
  /*  function online_products() {
    $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

    // Only online products (internet = 'true')
    $options = array("internet" => "true");
    $view_data['items'] = $this->Items_model->get_details(array("internet" => "true"));

    return $this->template->rander("items/index", $view_data);
}*/



function online_products() {
    $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

    // Only online products (internet = 'true')
    $view_data['items'] = $this->Items_model->get_online_details();

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

$imageData = $this->buildMappedCategoryImageUrls($user_id);
$view_data['category_images'] = $imageData['images'];
$view_data['default_category_image'] = $imageData['default'];

          return $this->template->rander("items/mapped", $view_data);

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
     
     
     
     
    /* function efactor_phase1() {

    return $this->template->rander("items/efactor_phase1");

}*/










function getUserIdFromSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }

    return null;
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
        $image_url = 'http://localhost/factor_17-05/files/brandLogo/' . $image_filename;  
      //  $image_url = 'https://efactor.tech/files/brand_logo/' . $image_filename;
          
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


 $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes&node_id=' . $node_id; // Construct the URL
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


    function controldisplay($modelNumber, $pageNo, $node_id)
    {

session_start();
    $user_id = $_SESSION['user_id'];



     $url = 'https://efactor.tech/v8/newapi.php?action=test_nodes&node_id=' . $node_id; // Construct the URL
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








function efactor_phase1() {

    session_start();

    $user_id = $_SESSION['user_id'];

    // GET ALL IDS
    $view_data['node_ids'] =
        $this->Items_model->get_vendor_node_ids($user_id);

    return $this->template->rander(
        "items/efactor_phase1",
        $view_data
    );

}


function efactor_phase3() {

    session_start();

    $user_id = $_SESSION['user_id'];

    // GET ALL IDS
    $view_data['node_ids'] =
        $this->Items_model->get_vendor_node_ids($user_id);

    return $this->template->rander(
        "items/specifications",
        $view_data
    );

}



function update_assign_node_status()
{

    $node_id = $this->request->getPost('node_id');

    $result = $this->Items_model->update_node_assign_status($node_id);

    if($result){

        echo "Node Assigned Successfully";

    }else{

        echo "Database Update Failed";

    }

}


public function unassign_node_id()
{

    $node_id = $this->request->getPost('node_id');

    $this->Items_model->unassign_node_id($node_id);

    echo "NODE UNASSIGNED";

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

    echo "Function started\n";
    echo "User ID from session: " . $user_id . "\n";
    echo "User ID param: " . $userId . "\n";
    echo "Model No param: " . $modelno . "\n";

    echo "Fetching modelsWithPages...\n";
    $modelsWithPages = $this->Items_model->getConfirmModelsWithPagesByUserId($user_id, $modelno);
    print_r($modelsWithPages);

    echo "Fetching modelsWithPages2...\n";
    $modelsWithPages2 = $this->Items_model->getConfirmModelsWithPagesByUserId2($user_id, $modelno);
    print_r($modelsWithPages2);

   /* echo "Calling Confirm method...\n";
    $confirmResult = $this->Items_model->Confirm($modelsWithPages, $modelsWithPages2);
    echo "Confirm Result:\n";
    print_r($confirmResult);*/


   echo "Calling Confirm method...\n";

   if (!empty($modelsWithPages) && !empty($modelsWithPages2)) {
   // Call with both params
   $confirmResult = $this->Items_model->Confirm($modelsWithPages, $modelsWithPages2);
   } else {
   // Call without params
   $confirmResult = $this->Items_model->Confirm_without_pages($modelno);
   }

echo "Confirm Result:\n";
print_r($confirmResult);





    if ($confirmResult === "Parameters updated successfully") {
        echo "Branch: Parameters updated successfully\n";
        // return redirect()->to(site_url('items/vendor'))->with('success', "Parameters updated successfully.");
    } elseif ($confirmResult === "Parameters already updated") {
        echo "Branch: Parameters already updated\n";
        return redirect()->to(site_url('items/vendor'))->with('error', "Parameters already updated");
    } elseif ($confirmResult === true) {
        echo "Branch: Table created successfully\n";
        return redirect()->to(site_url('items/vendor'))->with('success', "Table created successfully");
    } elseif ($confirmResult === false) {
        echo "Branch: Unable to create table\n";
        return redirect()->to(site_url('items/vendor'))->with('error', "Unable to create table");
    } else {
        echo "Branch: Unknown result — returning false\n";
        return false;
    }
}


/*****************************************************************/


function pagesave($value)
{


redirect('https://efactor.tech/mqtt.php');



}






public function selectId($userId)
{


        
    $modelsWithIds = $this->Items_model->getModelsWithIdsByUserId($userId);
    $modelwithnos = $this->Items_model->getModelnos($userId);
    $modelwithproducts = $this->Items_model->getproductname($userId);
   // $modelwithparameter = $this->Items_model->getparametername($userId); 

//print_r($modelsWithIds);
//print_r($modelwithnos);
    
    if ($modelsWithIds !== false) {
        $data['modelIds'] = [];
        foreach ($modelsWithIds->getResultArray() as $model) {
            $data['modelIds'][] = $model['node_id']; 
        }
    } 

    $data['models'] = []; 
    if ($modelwithnos !== false) {
        foreach ($modelwithnos->getResultArray() as $model) {
            $data['models'][$model['modelname']] = $model['product_name']; 
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


















public function copy_api()
{
    session_start();

    $user_id = $_SESSION['user_id'] ?? 0;

    $model_names   = $this->Items_model->extract_model_name($user_id);
    $product_names = $this->Items_model->extract_products_name($user_id);
    $page_names    = $this->Items_model->extract_pages_names($user_id);

    return view('items/copy_api', [
        'models'    => $model_names,
        'Products'  => $product_names,
        'page_name' => $page_names
    ]);
}






/*

public function saveconfig()
{
    session_start();
    $user_id = $_SESSION['user_id']; 

    // Collect POST data
    $data = [
        "from_model"     => $this->request->getPost('from_model'),
        "to_model"       => $this->request->getPost('to_model'),
        "existing_param" => $this->request->getPost('existing_param'),
        "new_param"      => $this->request->getPost('new_param'),
        "user_id"        => $user_id
    ];

    // Basic validation
    if (empty($data['from_model']) || empty($data['to_model']) || empty($data['existing_param']) || empty($data['new_param'])) {
        return redirect()->back()->with('error', 'All fields are required');
    }

    // Call the model to handle copying
    $result = $this->Items_model->copy_parameter($data);

    // Return response based on model result
    if ($result) {
        return redirect()->back()->with('success', 'Parameter copied successfully!');
    } else {
        return redirect()->back()->with('error', 'Copy failed');
    }
}

*/



function saveconfig() {
    session_start();
    $user_id = $_SESSION['user_id']; 
    
 $item_data = array(
        "from_model"     => $this->request->getPost('from_model'),
        "to_model"       => $this->request->getPost('to_model'),
        "existing_param" => $this->request->getPost('existing_param'),
        "new_param"      => $this->request->getPost('new_param'),
        "user_id"        => $user_id
    );
    
     
    echo "Data api saved successfully!";
 
      
   // $item_data = $this->Items_model->saveconfig($item_data, $user_id);

$item_data = $this->Items_model->copy_parameter($item_data); 
}






/*
function saveconfig() {
    session_start();
    $user_id = $_SESSION['user_id']; 

    $item_data = array(
        "from_model"     => $this->request->getPost('from_model'),
        "to_model"       => $this->request->getPost('to_model'),
        "existing_param" => $this->request->getPost('existing_param'),
        "new_param"      => $this->request->getPost('new_param'),
        "user_id"        => $user_id
    );

    // Call model function
    $item_data = $this->Items_model->copy_parameter($item_data); 

    // Print result
    echo "<pre>";
    print_r($item_data);
    echo "</pre>";

    echo "Data api saved successfully!";
}
*/

/************************************************/



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



/*
public function modal_config()
{
    session_start();
    $user_id = $_SESSION['user_id'];

    $selectedmodelNumber = $this->request->getVar('modelNumber');
    $pageNo = $this->request->getVar('pageNo');

    $model_names = $this->Items_model->extract_model_name($user_id, $selectedmodelNumber, $pageNo);
    $page_names = $this->Items_model->extract_page_names($user_id, $selectedmodelNumber, $pageNo);
    $node_ids = $this->Items_model->extract_node_id($user_id, $selectedmodelNumber, $pageNo);
    $router_ids = $this->Items_model->extract_router_id($user_id);
    $params = $this->Items_model->extract_parameters($user_id, $selectedmodelNumber, $pageNo);
    $defaults = $this->Items_model->extract_default_names($user_id, $selectedmodelNumber, $pageNo);

    echo "<pre>";
    print_r([
        'user_id' => $user_id,
        'selectedmodelNumber' => $selectedmodelNumber,
        'pageNo' => $pageNo,
        'models' => $model_names,
        'pageNames' => $page_names,
        'nodeIds' => $node_ids,
        'router_Ids' => $router_ids,
        'params' => $params,
        'defaults' => $defaults
    ]);
    echo "</pre>";
    exit;
}

*/


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

/*public function modal_pages($id)
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
}   */  //09-07-26




/*
public function modal_pages($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');
    $parameter_names = $this->Items_model->extract_modelname_config($id, $user_id);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno, $id);
    return view('items/modal_pages', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'page_names' => $page_names,
    ]);
}  
*/



public function modal_pages($id)
{
    session_start();

    $user_id = $_SESSION['user_id'];
    $modelno = $this->request->getVar('modelno');

  /*  echo "<pre>";
    echo "User ID: " . $user_id . "<br>";
    echo "Model No: " . $modelno . "<br>";
    echo "Modal ID: " . $id . "<br><br>";*/

    $parameter_names = $this->Items_model->extract_modelname_config($id, $user_id);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno, $id);

  /*  echo "<h3>Parameter Names</h3>";
    print_r($parameter_names);

    echo "<h3>Page Names</h3>";
    print_r($page_names);
    echo "</pre>";

    exit; // Stop execution to see the output*/

    return view('items/modal_pages', [
        'user_id'    => $user_id,
        'modal_id'   => $id,
        'modelno'    => $modelno,
        'params'     => $parameter_names,
        'page_names' => $page_names,
    ]);
}




 

/*public function modal_pages_data($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    $modelno = $this->request->getVar('modelno');
    $page_names = $this->Items_model->extract_pages($user_id, $modelno, $id);
    $pages = array();

    foreach ($page_names as $pageRow) {
        if (empty($pageRow['page_names'])) {
            continue;
        }

        foreach (explode(',', $pageRow['page_names']) as $pageName) {
            $pageName = trim((string) $pageName);

            if ($pageName !== '' && !in_array($pageName, $pages, true)) {
                $pages[] = $pageName;
            }
        }
    }

    return $this->response->setJSON(array(
        'success' => true,
        'pages' => $pages
    ));
}*/



public function modal_pages_data($id)
{
    session_start();

    $user_id = $_SESSION['user_id'];
    $modelno = $this->request->getVar('modelno');

    $page_names = $this->Items_model->extract_pages($user_id, $modelno, $id);

    $pages = [];

    foreach ($page_names as $pageRow) {

        log_message('debug', 'DB Row: ' . json_encode($pageRow));

        if (empty($pageRow['page_names'])) {
            continue;
        }

        $pageArray = explode(',', $pageRow['page_names']);

        log_message('debug', 'Extracted Pages: ' . json_encode($pageArray));

        foreach ($pageArray as $pageName) {

            $pageName = trim($pageName);

            if ($pageName != '' && !in_array($pageName, $pages, true)) {
                $pages[] = $pageName;
            }
        }
    }

    return $this->response->setJSON([
        'success' => true,
        'modelno' => $modelno,
        'user_id' => $user_id,
        'raw_db_data' => $page_names,
        'final_pages' => $pages
    ]);
}




/*


public function api_page_linking($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');
    echo "Model No: " . $modelno . "<br>";

    $parameter_names = $this->Items_model->extract_apis($user_id, $modelno);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno);

 

    return view('items/modal_api_page_linking', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'pages' => $page_names,
    ]);
}*/  //09-07-26


public function api_page_linking($id)
{
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $modelno = $this->request->getVar('modelno');

    $parameter_names = $this->Items_model->getApiParameterNamesForLinking($id, $user_id, $modelno);
    $page_names = $this->Items_model->extract_pages($user_id, $modelno, $id);
    $saved_mapping = $this->Items_model->getPageParameterMapping($modelno);

    return view('items/modal_api_page_linking', [
        'user_id' => $user_id,
        'modal_id' => $id,
        'modelno' => $modelno,
        'params' => $parameter_names,
        'pages' => $page_names,
        'saved_mapping' => $saved_mapping,
    ]);
}



/*
public function submit_page_params()
{
    $cmd_parameters = $this->request->getPost('cmd_parameter');
    $uiPageNos      = $this->request->getPost('uiPageNo_cmd');
    $pageNames      = $this->request->getPost('page_name');
    $modelno        = $this->request->getPost('modelno');

    // 🔍 Debug log (optional - shows post data in writable/logs/)
    log_message('debug', print_r($this->request->getPost(), true));

    // ✅ Input validation
    if (!$modelno || !is_array($cmd_parameters) || !is_array($uiPageNos) || !is_array($pageNames)) {
        return $this->response->setJSON(['error' => 'Invalid or missing input data']);
    }

    $linkingData = [];  // pageName => [param1, param2]
    $pageNameMap = [];  // pageNo => pageName

    foreach ($cmd_parameters as $index => $param) {
        $pageNo = $uiPageNos[$index] ?? null;

        if ($pageNo === null) continue;

        // ✏️ Page name fallback if blank
        $pageName = isset($pageNames[$pageNo]) && trim($pageNames[$pageNo]) !== ''
                    ? trim($pageNames[$pageNo])
                    : "Page " . ($pageNo + 1);

        $pageNameMap[$pageNo] = $pageName;

        // 📦 Push param into page
        if (!isset($linkingData[$pageName])) {
            $linkingData[$pageName] = [];
        }

        if (!in_array($param, $linkingData[$pageName])) {
            $linkingData[$pageName][] = $param;
        }
    }

    // ✅ Save to DB via model
    $this->Items_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData);

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Data saved in JSON format for model: ' . $modelno
    ]);
}*/  //09-07-26





public function submit_page_params()
{
    $cmd_parameters = $this->request->getPost('cmd_parameter');
    $uiPageNos      = $this->request->getPost('uiPageNo_cmd');
    $pageNames      = $this->request->getPost('page_name');
    $sequences      = $this->request->getPost('parameter_sequence');
    $modelno        = $this->request->getPost('modelno');
    $modal_id       = $this->request->getPost('modal_id');

    // 🔍 Debug log (optional - shows post data in writable/logs/)
    log_message('debug', print_r($this->request->getPost(), true));

    // ✅ Input validation
    if (!$modelno || !is_array($cmd_parameters) || !is_array($uiPageNos) || !is_array($pageNames) || !is_array($sequences)) {
        return $this->response->setJSON(['error' => 'Invalid or missing input data']);
    }

    $linkingData = [];
    $pageNameMap = [];

    foreach ($cmd_parameters as $index => $param) {
        $param = trim((string) $param);
        $pageNo = isset($uiPageNos[$index]) ? trim((string) $uiPageNos[$index]) : '';
        $sequence = isset($sequences[$index]) ? (int) $sequences[$index] : ($index + 1);

        if ($param === '' || $pageNo === '') {
            continue;
        }

        // ✏️ Page name fallback if blank
        $pageName = isset($pageNames[$pageNo]) && trim($pageNames[$pageNo]) !== ''
                    ? trim($pageNames[$pageNo])
                    : "Page " . $pageNo;

        $pageNameMap[$pageNo] = $pageName;

        // Use page number as canonical key so dynamic rows never collapse.
        if (!isset($linkingData[$pageNo])) {
            $linkingData[$pageNo] = [];
        }

        $linkingData[$pageNo][] = [
            'parameter' => $param,
            'sequence' => max(1, $sequence),
            'page_no' => $pageNo,
            'page_name' => $pageName,
        ];
    }

    // ✅ Save to DB via model
    foreach ($linkingData as $pageName => $rows) {
        usort($rows, function ($a, $b) {
            return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
        });

        $linkingData[$pageName] = array_values($rows);
    }

    if (empty($linkingData)) {
        return $this->response->setJSON(['error' => 'Please select at least one parameter']);
    }

    $flattenedRows = [];
    foreach ($linkingData as $pageNo => $rows) {
        foreach ($rows as $rowIndex => $row) {
            $parameter = trim((string) ($row['parameter'] ?? ''));
            if ($parameter === '') {
                continue;
            }
            $resolvedPageNo = trim((string) ($row['page_no'] ?? $pageNo));
            $resolvedPageName = trim((string) ($row['page_name'] ?? ($pageNameMap[$resolvedPageNo] ?? ('Page ' . $resolvedPageNo))));
            $flattenedRows[] = [
                'page_no' => $resolvedPageNo,
                'page_name' => $resolvedPageName,
                'parameter' => $parameter,
                'sequence' => (int) ($row['sequence'] ?? ($rowIndex + 1)),
                'api' => [],
            ];
        }
    }
    usort($flattenedRows, function ($a, $b) {
        $pageCompare = ((int) ($a['page_no'] ?? 0)) <=> ((int) ($b['page_no'] ?? 0));
        if ($pageCompare !== 0) {
            return $pageCompare;
        }
        return ((int) ($a['sequence'] ?? 0)) <=> ((int) ($b['sequence'] ?? 0));
    });

    $session = session();
    $session->setTempdata('view_ui_mapping_' . $modelno, [
        'model_name' => $modelno,
        'page_names_array' => $pageNameMap,
        'linking_data_array' => $linkingData,
        'rows' => $flattenedRows,
    ], 300);

    $this->Items_model->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData);

    return redirect()->to(get_uri("items/view_page_ui/" . (int) $modal_id . "?modelno=" . rawurlencode($modelno) . "&schema_sync=1"));
}




public function view_page_ui($id = 0)
{
    $modelno = $this->request->getVar('modelno');
    $model_data = $this->Items_model->getPageParameterMapping($modelno);
    $session = session();
    $freshMapping = $session->getTempdata('view_ui_mapping_' . $modelno);
    if (is_array($freshMapping) && !empty($freshMapping['rows']) && is_array($freshMapping['rows'])) {
        $model_data = $freshMapping;
    }
    $uiTheme = $this->Items_model->getDeviceUiTheme($modelno) ?: ['theme' => [], 'schema' => []];
    $savedSchema = $this->Items_model->getDeviceUiSchema($modelno);
    if (empty($uiTheme['schema']) && is_array($savedSchema)) {
        $uiTheme['schema'] = $savedSchema;
    }
    $rowParametersJson = $this->request->getGet('row_parameters');
    $isRowView = false;

    if ($rowParametersJson) {
        $rowParameters = json_decode($rowParametersJson, true);

        if (is_array($rowParameters) && count($rowParameters)) {
            $pageNo = trim((string) $this->request->getGet('row_page_no'));
            $pageName = trim((string) $this->request->getGet('row_page_name'));
            $sequence = (int) $this->request->getGet('row_sequence');
            $sequence = $sequence > 0 ? $sequence : 1;
            $pageNo = $pageNo !== '' ? $pageNo : '1';
            $pageName = $pageName !== '' ? $pageName : ('Page ' . $pageNo);
            $rows = [];

            foreach ($rowParameters as $index => $parameter) {
                $parameter = trim((string) $parameter);

                if ($parameter === '') {
                    continue;
                }

                $rows[] = [
                    'page_no' => $pageNo,
                    'page_name' => $pageName,
                    'parameter' => $parameter,
                    'sequence' => $sequence + $index,
                ];
            }

            if (!empty($rows)) {
                $model_data = ['rows' => $rows];
                $isRowView = true;
            }
        }
    }

    return view('items/view_page_ui', [
        'modal_id' => $id,
        'modelno' => $modelno,
        'model_data' => $model_data,
        'is_row_view' => $isRowView,
        // Keep the public device schema aligned whenever the complete linked UI is rendered.
        'schema_sync' => !$isRowView,
        'ui_theme' => $uiTheme,
        'device_info_api_url' => trim((string) $this->request->getGet('info_api_url')) ?: get_uri('api/device-ui-info/' . rawurlencode((string) $modelno)),
        'schema_api_url' => get_uri('api/device-ui-schema/' . rawurlencode((string) $modelno)),
        'parameters_api_url' => get_uri('api/device-ui-schema/' . rawurlencode((string) $modelno) . '/parameters'),
    ]);
}

/*
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
}*/


/*
public function auto_test($id){ 
    session_start();
    $user_id = $_SESSION['user_id'];
    $modelno = $this->request->getVar('modelno'); 
    $parameter_names = $this->Items_model->extract_params($id, $user_id);
    
    
    
    
    <pre>
<?php
echo "User ID: ";
print_r($user_id);

echo "\nID: ";
print_r($id);

echo "\nModel No: ";
print_r($modelno);

echo "\nParameters:\n";
print_r($parameters);
?>
</pre>


    return view('items/auto_test', [
        'user_id' => $user_id,
        'id' => $id,
        'modelno' => $modelno,
        'parameters' => $parameter_names,
    ]);
}


*/

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




public function Check_display_status()
{
    header('Content-Type: application/json');

    $node_id = $this->input->post('node_id');

   /* echo "\n--- DEBUG START ---\n";
    echo "Node ID Received: ";
    print_r($node_id);
    echo "\n";*/

    if (empty($node_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Node ID missing'
        ]);
        return;
    }

    $status = $this->Items_model->get_node_internet_status($node_id);

   /* echo "Model Returned Status: ";
    var_dump($status);
    echo "\n";*/

    if ($status === null) {
        echo json_encode([
            'status' => 'offline',
            'reason' => 'Node not found'
        ]);
        return;
    }

    echo json_encode([
        'status' => $status ? 'online' : 'offline'
    ]);

   // echo "\n--- DEBUG END ---\n";
}









/*
public function Fw_Update($model_number)
{
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    $node_id = $this->request->getVar('node_id');

    $url = "https://efactor.tech/v8/fw_update.php?" . http_build_query([
        'node_id' => $node_id
    ]);

    // Call URL
    $response = file_get_contents($url);

    return $response;
}*/



public function fw_update()
{

session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    $node_id = $this->request->getVar('node_id');
    $url = "https://efactor.tech/v8/check_ota_status.php?node_id=" . $node_id;

    // Use cURL instead of file_get_contents
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
}



public function enableBrand($model_number)
{
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    // Get parameters
    $node_id = $this->request->getVar('node_id');
    $userid = $this->request->getVar('userid') ?? $user_id;

    // Build the URL
    $url = "https://efactor.tech/v8/brand_ui.php?" . http_build_query([
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




public function savebrand()   //24-07-25
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

        

     $data = [
            'name' => $this->request->getPost('name'),
            'brandlogo' => $brandlogo,
            'user_id' => $user_id
        ];










        // Insert into DB
        $db = \Config\Database::connect();
    //    $db->table('brands')->insert($data);


foreach ($data as $key => $value) {
    $data[$key] = ($value === '') ? null : $value;
}


 $builder = $db->table('brands');
$inserted = $builder->insert($data);

if ($inserted) {
print_r($data);
    echo "<h3 style='color:green;'>✅ Brand Saved Successfully!</h3>";
} else {
    echo "<p style='color:red;'>❌ Failed to insert data. DB Error: " . implode(', ', $db->error()) . "</p>";
}




        // Success Message
        echo "<h3 style='color:green;'>✅ Brand Saved Successfully!</h3>";
        echo "<h4>📋 Submitted Details:</h4>";
        echo "<table border='1' cellpadding='5'>";
        foreach ($data as $key => $value) {
            echo "<tr><td><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</strong></td><td>" . htmlspecialchars($value ?? '') . "</td></tr>";
        }
        echo "</table>";

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
  // $targetDirectory = "/Program Files/Ampps/www/factor_17-05/files/brandLogo/";

$targetDirectory = "/var/www/html/files/brand_logo/";
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
       // echo "Sorry, there was an error uploading your file.";
         echo "Successfully created product name";
    }
    
}
   
    $id = $this->request->getPost('id');
    $name = $this->request->getPost('name');
    $prodimg = $originalFileName;
 echo $name;
 echo $id;
echo $user_id;
echo $prodimg;
        $result = $this->Items_model->save_prod($id,$name,$prodimg,$user_id);
 
}



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
        $view_data['model_info'] = $this->Items_model->get_product_cat_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('items/modal_formcat', $view_data);
    }

     //modal_vendorproduct
      function modal_vendorproduct() {

          $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);

          if (!$user_id) {
              return $this->response->setStatusCode(401)->setBody("Session user_id missing");
          }

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




public function modal_api($itemid = 0)
{
    session_start();
    $user_id = $_SESSION['user_id'];

    $_SESSION['itemid'] = $itemid;

    $this->validate_submitted_data([
        "id" => "numeric"
    ]);

    $view_data['itemid'] = $itemid;

    $api_details_result = $this->Items_model->default_form_values($itemid);

    if ($api_details_result) {
        $api_details_row = $api_details_result->getRow();

        if ($api_details_row) {
            $view_data['brand'] = $api_details_row->brand;
            $view_data['category'] = $api_details_row->category;
            $view_data['model_name'] = $api_details_row->modelname;
            $view_data['modelno'] = $api_details_row->modelno;

            // ✅ Extract default API parameter names using model number
            $paramNames = $this->Items_model->extract_api_default_names($user_id, $api_details_row->modelno);
         $paramNames = $this->Items_model->extract_api_default_names($user_id, $api_details_row->modelname);
            $view_data['paramNames'] = $paramNames;
               //  print_r($view_data);

          //echo $paramNames;

        } else {
            echo "API details row not found.";
            return;
        }
    } else {
        echo "API details result not found.";
        return;
    }

    $view_data['id'] = $this->request->getPost('id');
    $view_data['categories_dropdown'] = $this->Items_model->get_cats($user_id)->getResult();

    return $this->template->view('items/modal_api', $view_data);

}




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
         //   $this->Items_model->save_selected_values($data);
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
/*public function modal_productinfo($node_id) {
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
}*/  //original code 



public function modal_productinfo($node_id) {
    $id = $this->request->getPost('id'); 
    $data = $this->Items_model->getDataBynodeId($node_id)->getRow();

    if ($data) {
        $product_details = get_object_vars($data);

        if ($product_details) {
            $device_type = $data->device_type;
            $view_data['product_details'] = $this->_make_product_row($product_details, $device_type);

            $view_data['node_id'] = $node_id;
            $view_data['model_number'] = $data->model_number ?? '';
            $view_data['user_id'] = $data->user_id ?? '';
            $view_data['api_info'] = $this->Items_model->modal_apisinfo($node_id, $data->model_number ?? '', $data->user_id ?? '');
            $view_data['user_type'] = $this->Items_model->apisinfo_user_type($this->login_user->id ?? $user_id ?? '');

            
            $is_admin = $this->login_user->is_admin;  
            if ($is_admin) {
                return $this->template->view('items/User_Products', $view_data);
            } else {
                $partial_row = $this->_make_partial_product_row($product_details, $device_type);
                $view_data['product_details'] = $partial_row;

                $view_data['node_id'] = $node_id;
                $view_data['model_number'] = $data->model_number ?? '';
                $view_data['user_id'] = $data->user_id ?? '';
                $view_data['api_info'] = $this->Items_model->modal_apisinfo($node_id, $data->model_number ?? '', $data->user_id ?? '');
                $view_data['user_type'] = $this->Items_model->apisinfo_user_type($this->login_user->id ?? $user_id ?? '');
                return $this->template->view('items/User_Products', $view_data);
            }
        }
    }

    return "Product details not found for node ID: $node_id";
}   //pavan index






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

/*public function modal_apisinfo($node_id) {
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
}*/    //original_code




public function modal_apisinfo($node_id, $model_number, $user_id)
{ 
if($model_number == "EFDEFLT V1.0"){
$sql = "
            SELECT name,power FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
         return $this->db->query($sql)->getResultArray();
}else{
    $check_vendor_product_sql = "
        SELECT COUNT(*) AS count 
        FROM vendorproducts 
        WHERE modelname = '$model_number' 
    ";
     
    $vendor_product_result = $this->db->query($check_vendor_product_sql)->getRowArray();
 
    if ($vendor_product_result['count'] > 0) { 
        $sql = "
            SELECT * FROM `$model_number`
            WHERE node_id = '$node_id' 
        ";
    } else { 
        $sql = "
            SELECT * FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        if($model_number == "EFACT V1.0" || $model_number == "EFACT V1.1" || $model_number == "EFACT V1.2"){
        $sql = "
            SELECT baudrate,uartmsgid,errorcode,name,power,speed,direction,rpm,current,fan_properties,fan_resistance,fan_lmin,fan_lmax,
            fan_frequency,fan_pairs,fan_open_loop_current,fan_power_limit,fan_torque_limit,fan_speed1,fan_speed2,fan_speed3,fan_speed4,
            fan_speed5,fan_speed6,fan_speed7,TZ_properties,BR_properties,UMI_properties,EC_properties,CM_properties,TU_properties,
            LC_properties,display_properties
            FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        }else if($model_number == "EFCKER V1.0"){
        $sql = "
            SELECT baudrate,uartmsgid,errorcode,name,power,cook_time,cookermode,cook_run_temp,cook_max_temp,cooker_properties,recipe,
            pre_time_const,keep_warm_timer,dishes,Pre_timer_constant_mode,Pre_timer,Pre_temperature,Coooker_max_temperature,
            Cooker_constant_mode,Keep_Warm_timer_1,dish,pre_timer_wait_temperature_constant_mode,pre_timer_waiting_time,
            pre_timer_Waiting_temperature,warm_constant_cut_off,cook_wait_temp_constant_mode,cook_waiting_time,cook_Waiting_temperature,
            warm_wait_temp_constant_mode,warm_waiting_time,warm_Waiting_temperature,TZ_properties,BR_properties,UMI_properties,
            EC_properties,CM_properties,TU_properties,LC_properties,display_properties
            FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        }else if($model_number == "EFSWPCP V1.0"){
         $sql = "
            SELECT baudrate,uartmsgid,errorcode,name,power,current,total_watt,swpcp_1ph_properties,high_current_cutoff,
            LOCK_ENABLE_DISABLE,starter_relay_off_timer,start_high_current_cutoff,start_low_current_cutoff,Run_high_current_cutoff,
            Run_low_current_cutoff,high_voltage_cutoff,low_voltage_cutoff,ultra_high_voltage_cutoff,ultra_high_current_cutoff,
            USER_SET_MAX_CURRENT_CUTOFF_LIMIT,USER_AND_DEVICE_MAX_CURRENT_LIMIT,TZ_properties,BR_properties,UMI_properties,
            EC_properties,CM_properties,TU_properties,LC_properties,display_properties
            FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        }else if($model_number == "EFACTOR V1.0"){
        $sql = "
            SELECT baudrate,uartmsgid,errorcode,name,power,bed_number,nurse,personal_assistant,doctor,nurse_resp,
            personal_assistant_resp,doctor_resp
            FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        }else{
        $sql = "
            SELECT name,power FROM `efactorproduct`
            WHERE node_id = '$node_id' AND user_id = '$user_id'
        ";
        }
    } 
    return $this->db->query($sql)->getResultArray();
}

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
      //  return $this->template->view('items/modal_command_status', $view_data);
          return $this->template->view('items/modal_command', $view_data);

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
            
}//original code

/*
function savevendorproduct() {

    session_start();
    $user_id = $_SESSION['user_id'];

    $item_data = [
        "brand"       => $this->request->getPost('brand'),
        "category"    => $this->request->getPost('category'),
        "modelname"   => $this->request->getPost('modelname'),
        "modelno"     => $this->request->getPost('modelno'),
        "version"     => $this->request->getPost('version'),
        "batch"       => $this->request->getPost('batch'),
        "description" => $this->request->getPost('description')
    ];

    echo "<pre>";
    print_r($item_data);
    echo "User ID: " . $user_id . "\n";
    echo "</pre>";
    exit;

    $result = $this->Items_model->savevendorproduct($item_data, $user_id);
}

*/

/*
function savevendorproduct() {

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

    // 🔍 Print data
    echo "<pre>";
    print_r($item_data);
    echo "</pre>";
    exit;

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


*/
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



/*

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
        
        
        
        
        
        */
        
        
        
        
/*        public function specificationsapi()
{
    // Allow JSON requests
    header('Content-Type: application/json');

    try {
        // Get raw JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validate JSON
        if (!$data) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid JSON input'
            ]);
        }

        // Basic validation
        if (!isset($data['test_suite']) || empty($data['test_suite'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No test suite data received'
            ]);
        }

        // Optional: Load model
        // $this->load->model('Items_model'); // CI3
        // OR in CI4:
        // $itemsModel = new \App\Models\Items_model();

        $savedTests = [];

        foreach ($data['test_suite'] as $test) {

            // Prepare main test data
            $testData = [
                'test_id'          => $test['test_id'] ?? null,
                'parameter_name'  => $test['parameter_name'] ?? null,
                'instance_number' => $test['instance_number'] ?? null,
                'model_number'    => $test['model_number'] ?? null,
                'user_id'         => $test['user_id'] ?? null,
                'created_at'      => $test['created_at'] ?? date('Y-m-d H:i:s')
            ];
            
            
            

            // 👉 Save main test (example)
            // $insertId = $itemsModel->insertTest($testData);

            // 👉 Save commands
            
            
            if (!empty($test['commands'])) {
                foreach ($test['commands'] as $cmd) {
                    $cmdData = [
                        'test_id'      => $test['test_id'],
                        'command_name' => $cmd['command_name'] ?? null,
                        'value'        => $cmd['value'] ?? null,
                        'duration'     => $cmd['duration_seconds'] ?? null,
                        'min_range'    => $cmd['min_range'] ?? null,
                        'max_range'    => $cmd['max_range'] ?? null,
                        'byte_position'=> $cmd['byte_position'] ?? null
                    ];

                    // $itemsModel->insertCommand($cmdData);
                }
            }
            

            // 👉 Save expected responses
            
            
            if (!empty($test['expected_responses'])) {
                foreach ($test['expected_responses'] as $resp) {
                    $respData = [
                        'test_id'  => $test['test_id'],
                        'expected' => $resp['expected_value'] ?? null,
                        'timeout'  => $resp['timeout_seconds'] ?? null,
                        'min_range'=> $resp['min_range'] ?? null,
                        'max_range'=> $resp['max_range'] ?? null
                    ];

                    // $itemsModel->insertResponse($respData);
                }
            }

            // 👉 Save error conditions
            if (!empty($test['error_conditions'])) {
                foreach ($test['error_conditions'] as $err) {
                    $errData = [
                        'test_id'      => $test['test_id'],
                        'error_value'  => $err['error_value'] ?? null,
                        'error_timeout'=> $err['error_timeout'] ?? null
                    ];

                    // $itemsModel->insertError($errData);
                }
            }

            $savedTests[] = $test['test_id'];
        }

        // Final success response
        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'message' => 'Test configurations received successfully',
            'total_received' => count($data['test_suite']),
            'test_ids' => $savedTests,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON([
            'status' => 'error',
            'message' => 'Server error',
            'error' => $e->getMessage()
        ]);
    }
}


*/

/*

public function specificationsapi()
{
    try {
        // Allow only POST
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Method not allowed. Only POST requests are accepted.'
                ]);
        }

        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validate JSON format
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid JSON format: ' . json_last_error_msg()
                ]);
        }

        // Validate test suite exists
        if (empty($data['test_suite'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'No test suite data received. The request must contain a test_suite array.'
                ]);
        }

        $savedTests = [];
        $validationErrors = [];

        // Process each test in the suite
        foreach ($data['test_suite'] as $index => $test) {
            
            // Basic validation
            if (empty($test['test_id']) || empty($test['parameter_name'])) {
                $validationErrors[] = [
                    'index' => $index,
                    'message' => 'Missing required fields: test_id or parameter_name',
                    'test_data' => $test
                ];
                continue;
            }

            // Prepare main test data for database insertion
            $testData = [
                'test_id'          => $test['test_id'],
                'parameter_name'   => $test['parameter_name'],
                'instance_number'  => $test['instance_number'] ?? 1,
                'model_number'     => $test['model_number'] ?? null,
                'user_id'          => $test['user_id'] ?? null,
                'created_at'       => $test['created_at'] ?? date('Y-m-d H:i:s')
            ];

            // Process commands if present
            $commands = [];
            if (!empty($test['commands']) && is_array($test['commands'])) {
                foreach ($test['commands'] as $cmd) {
                    $commands[] = [
                        'command_name' => $cmd['command_name'] ?? null,
                        'value' => $cmd['value'] ?? null,
                        'duration_seconds' => $cmd['duration_seconds'] ?? null,
                        'min_range' => $cmd['min_range'] ?? null,
                        'max_range' => $cmd['max_range'] ?? null,
                        'byte_position' => $cmd['byte_position'] ?? null
                    ];
                }
            }

            // Process expected responses if present
            $expectedResponses = [];
            if (!empty($test['expected_responses']) && is_array($test['expected_responses'])) {
                foreach ($test['expected_responses'] as $resp) {
                    $expectedResponses[] = [
                        'expected_value' => $resp['expected_value'] ?? null,
                        'timeout_seconds' => $resp['timeout_seconds'] ?? null,
                        'min_range' => $resp['min_range'] ?? null,
                        'max_range' => $resp['max_range'] ?? null
                    ];
                }
            }

            // Process error conditions if present
            $errorConditions = [];
            if (!empty($test['error_conditions']) && is_array($test['error_conditions'])) {
                foreach ($test['error_conditions'] as $err) {
                    $errorConditions[] = [
                        'error_value' => $err['error_value'] ?? null,
                        'error_timeout' => $err['error_timeout'] ?? null
                    ];
                }
            }

            // Here you would typically save to database
            // Example: $this->test_model->insert_test($testData, $commands, $expectedResponses, $errorConditions);
            
            // For debugging - log the processed data
            log_message('debug', 'Processed test: ' . json_encode([
                'test_data' => $testData,
                'commands_count' => count($commands),
                'responses_count' => count($expectedResponses),
                'errors_count' => count($errorConditions)
            ]));

            $savedTests[] = [
                'test_id' => $test['test_id'],
                'parameter_name' => $test['parameter_name'],
                'instance_number' => $test['instance_number'] ?? 1,
                'commands_count' => count($commands),
                'responses_count' => count($expectedResponses)
            ];
        }

        // Prepare the response
        $response = [
            'status' => 'success',
            'message' => 'Test configurations processed successfully',
            'total_received' => count($data['test_suite']),
            'total_processed' => count($savedTests),
            'test_ids' => array_column($savedTests, 'test_id'),
            'processed_tests' => $savedTests,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Add validation errors if any
        if (!empty($validationErrors)) {
            $response['status'] = 'partial_success';
            $response['message'] = 'Some tests had validation errors and were skipped';
            $response['validation_errors'] = $validationErrors;
            $response['skipped_count'] = count($validationErrors);
        }

        // Return JSON response
        return $this->response
            ->setStatusCode(200)
            ->setJSON($response);

    } catch (\Exception $e) {
        // Log the error
        log_message('error', 'API Error in specificationsapi: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());

        // Return error response
        return $this->response
            ->setStatusCode(500)
            ->setJSON([
                'status' => 'error',
                'message' => 'An internal server error occurred',
                'error_details' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Please contact support',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
    }
}
*/






  public function specificationsapi()
    {
        try {
            // Set CORS headers
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
            $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
            
            // Handle preflight OPTIONS request
            if ($this->request->getMethod() === 'options') {
                return $this->response->setStatusCode(200);
            }
            
            // Allow only POST
            if ($this->request->getMethod() !== 'post') {
                return $this->response
                    ->setStatusCode(405)
                    ->setContentType('application/json')
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Method not allowed. Only POST requests are accepted.',
                        'method' => $this->request->getMethod()
                    ]);
            }

            // Get JSON input
            $input = file_get_contents('php://input');
            
            // Log for debugging (optional)
            log_message('info', 'API Request received: ' . substr($input, 0, 500));
            
            // Check if input is empty
            if (empty($input)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'No input data received'
                    ]);
            }
            
            $data = json_decode($input, true);

            // Validate JSON format
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid JSON format: ' . json_last_error_msg(),
                        'received_input' => substr($input, 0, 200)
                    ]);
            }

            // Validate test suite exists
            if (empty($data['test_suite']) || !is_array($data['test_suite'])) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'No test suite data received',
                        'received_keys' => array_keys($data)
                    ]);
            }

            $savedTests = [];
            $validationErrors = [];
            $processedTests = [];

            // Process each test in the suite
            foreach ($data['test_suite'] as $index => $test) {
                
                // Basic validation
                if (empty($test['test_id']) || empty($test['parameter_name'])) {
                    $validationErrors[] = [
                        'index' => $index,
                        'test_id' => $test['test_id'] ?? 'missing',
                        'parameter_name' => $test['parameter_name'] ?? 'missing',
                        'message' => 'Missing required fields'
                    ];
                    continue;
                }

                // Prepare main test data
                $testData = [
                    'test_id'          => $test['test_id'],
                    'parameter_name'   => $test['parameter_name'],
                    'instance_number'  => $test['instance_number'] ?? 1,
                    'model_number'     => $test['model_number'] ?? null,
                    'user_id'          => $test['user_id'] ?? null,
                    'created_at'       => $test['created_at'] ?? date('Y-m-d H:i:s')
                ];

                // Process commands
                $commands = [];
                if (!empty($test['commands']) && is_array($test['commands'])) {
                    foreach ($test['commands'] as $cmd) {
                        $commands[] = [
                            'command_name' => $cmd['command_name'] ?? null,
                            'value' => $cmd['value'] ?? null,
                            'duration_seconds' => isset($cmd['duration_seconds']) ? floatval($cmd['duration_seconds']) : null,
                            'min_range' => isset($cmd['min_range']) ? floatval($cmd['min_range']) : null,
                            'max_range' => isset($cmd['max_range']) ? floatval($cmd['max_range']) : null,
                            'byte_position' => $cmd['byte_position'] ?? null
                        ];
                    }
                }

                // Process expected responses
                $expectedResponses = [];
                if (!empty($test['expected_responses']) && is_array($test['expected_responses'])) {
                    foreach ($test['expected_responses'] as $resp) {
                        $expectedResponses[] = [
                            'expected_value' => $resp['expected_value'] ?? null,
                            'timeout_seconds' => isset($resp['timeout_seconds']) ? floatval($resp['timeout_seconds']) : null,
                            'min_range' => isset($resp['min_range']) ? floatval($resp['min_range']) : null,
                            'max_range' => isset($resp['max_range']) ? floatval($resp['max_range']) : null
                        ];
                    }
                }

                // Process error conditions
                $errorConditions = [];
                if (!empty($test['error_conditions']) && is_array($test['error_conditions'])) {
                    foreach ($test['error_conditions'] as $err) {
                        $errorConditions[] = [
                            'error_value' => $err['error_value'] ?? null,
                            'error_timeout' => isset($err['error_timeout']) ? floatval($err['error_timeout']) : null
                        ];
                    }
                }

                // Store processed test data
                $processedTests[] = [
                    'test_id' => $test['test_id'],
                    'parameter_name' => $test['parameter_name'],
                    'instance_number' => $test['instance_number'] ?? 1,
                    'commands_count' => count($commands),
                    'responses_count' => count($expectedResponses),
                    'errors_count' => count($errorConditions)
                ];
                
                $savedTests[] = $test['test_id'];
            }

            // Prepare success response
            $response = [
                'status' => empty($validationErrors) ? 'success' : 'partial_success',
                'message' => empty($validationErrors) 
                    ? 'All test configurations processed successfully' 
                    : 'Some tests had validation errors and were skipped',
                'total_received' => count($data['test_suite']),
                'total_processed' => count($savedTests),
                'total_errors' => count($validationErrors),
                'test_ids' => $savedTests,
                'processed_tests' => $processedTests,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // Add validation errors if any
            if (!empty($validationErrors)) {
                $response['validation_errors'] = $validationErrors;
            }

            // Return JSON response - NO ECHO STATEMENTS HERE!
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setJSON($response);

        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'API Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Return error response
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'An internal server error occurred',
                    'error_details' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Contact support',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
        }
    }
    
 




 public function specificationsapi_debug()
{
    try {
        // Allow only POST for consistency
        if ($this->request->getMethod() !== 'post') {
            die('This endpoint only accepts POST requests');
        }

        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Display debug information
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>API Debug - Auto Test Studio</title>";
        echo "<style>
            body { font-family: 'Courier New', monospace; background: #1e293b; color: #e2e8f0; padding: 20px; margin: 0; }
            .container { max-width: 1400px; margin: 0 auto; }
            h1 { color: #6366f1; border-bottom: 2px solid #6366f1; padding-bottom: 10px; }
            h2 { color: #a5b4fc; margin-top: 25px; }
            h3 { color: #94a3b8; margin-top: 20px; }
            pre { background: #0f172a; padding: 15px; border-radius: 8px; overflow-x: auto; border-left: 3px solid #6366f1; }
            .success { color: #10b981; }
            .error { color: #ef4444; }
            .info { color: #f59e0b; }
            .box { background: #0f172a; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
            .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px; }
            .badge-success { background: #10b981; color: white; }
            .badge-warning { background: #f59e0b; color: white; }
            .timestamp { color: #64748b; font-size: 12px; margin-top: 20px; text-align: center; }
        </style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        
        echo "<h1>🔧 API Debug Information</h1>";
        
        // Request Info
        echo "<div class='box'>";
        echo "<h2>📡 Request Information</h2>";
        echo "<pre>";
        echo "Method: " . $this->request->getMethod() . "\n";
        echo "Content-Type: " . $this->request->getHeaderLine('Content-Type') . "\n";
        echo "Content-Length: " . strlen($input) . " bytes\n";
        echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        echo "</pre>";
        echo "</div>";
        
        // Raw Input
        echo "<div class='box'>";
        echo "<h2>📦 Raw Input (First 1000 chars)</h2>";
        echo "<pre>" . htmlspecialchars(substr($input, 0, 1000)) . "</pre>";
        if (strlen($input) > 1000) {
            echo "<p class='info'>⚠️ Input truncated. Total length: " . strlen($input) . " bytes</p>";
        }
        echo "</div>";
        
        // JSON Validation
        echo "<div class='box'>";
        echo "<h2>✅ JSON Validation</h2>";
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<p class='success'>✓ Valid JSON format</p>";
        } else {
            echo "<p class='error'>✗ Invalid JSON: " . json_last_error_msg() . "</p>";
        }
        echo "</div>";
        
        if ($data !== null) {
            // Parsed Data Structure
            echo "<div class='box'>";
            echo "<h2>📊 Data Structure</h2>";
            echo "<pre>";
            echo "Top-level keys: " . implode(', ', array_keys($data)) . "\n";
            echo "Total test suite items: " . count($data['test_suite'] ?? []) . "\n";
            echo "Timestamp: " . ($data['timestamp'] ?? 'Not provided') . "\n";
            echo "Source: " . ($data['source'] ?? 'Not provided') . "\n";
            echo "Version: " . ($data['version'] ?? 'Not provided') . "\n";
            echo "</pre>";
            echo "</div>";
            
            // Test Suite Details
            if (!empty($data['test_suite'])) {
                echo "<div class='box'>";
                echo "<h2>🧪 Test Suite Details</h2>";
                echo "<p>Total tests: <strong>" . count($data['test_suite']) . "</strong></p>";
                
                foreach ($data['test_suite'] as $index => $test) {
                    $status = !empty($test['test_id']) && !empty($test['parameter_name']) ? 'success' : 'error';
                    $badgeClass = $status === 'success' ? 'badge-success' : 'badge-warning';
                    $statusText = $status === 'success' ? '✓ Valid' : '⚠️ Missing fields';
                    
                    echo "<h3>Test #" . ($index + 1) . " <span class='badge $badgeClass'>$statusText</span></h3>";
                    echo "<pre>";
                    echo "Test ID: " . ($test['test_id'] ?? '<span class="error">MISSING</span>') . "\n";
                    echo "Parameter Name: " . ($test['parameter_name'] ?? '<span class="error">MISSING</span>') . "\n";
                    echo "Instance Number: " . ($test['instance_number'] ?? '1') . "\n";
                    echo "Model Number: " . ($test['model_number'] ?? 'Not specified') . "\n";
                    echo "User ID: " . ($test['user_id'] ?? 'Not specified') . "\n";
                    echo "Created At: " . ($test['created_at'] ?? 'Not specified') . "\n";
                    
                    // Commands - FIXED
                    echo "\nCommands (" . count($test['commands'] ?? []) . "):\n";
                    if (!empty($test['commands'])) {
                        foreach ($test['commands'] as $cmd) {
                            echo "  - " . ($cmd['command_name'] ?? 'Unknown') . ": " . ($cmd['value'] ?? 'N/A') . 
                                 " (duration: " . ($cmd['duration_seconds'] ?? 'N/A') . "s)\n";
                        }
                    } else {
                        echo "  No commands configured\n";
                    }
                    
                    // Expected Responses - FIXED
                    echo "\nExpected Responses (" . count($test['expected_responses'] ?? []) . "):\n";
                    if (!empty($test['expected_responses'])) {
                        foreach ($test['expected_responses'] as $resp) {
                            echo "  - Expected: " . ($resp['expected_value'] ?? 'N/A') . 
                                 " (timeout: " . ($resp['timeout_seconds'] ?? 'N/A') . "s)\n";
                        }
                    } else {
                        echo "  No responses configured\n";
                    }
                    
                    // Error Conditions - FIXED
                    echo "\nError Conditions (" . count($test['error_conditions'] ?? []) . "):\n";
                    if (!empty($test['error_conditions'])) {
                        foreach ($test['error_conditions'] as $err) {
                            echo "  - Error value: " . ($err['error_value'] ?? 'N/A') . 
                                 " (timeout: " . ($err['error_timeout'] ?? 'N/A') . "s)\n";
                        }
                    } else {
                        echo "  No error conditions configured\n";
                    }
                    echo "</pre>";
                }
                echo "</div>";
                
                // Full JSON dump
                echo "<div class='box'>";
                echo "<h2>📄 Complete Data Dump</h2>";
                echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . "</pre>";
                echo "</div>";
            }
        }
        
        echo "<div class='timestamp'>";
        echo "Debug generated at: " . date('Y-m-d H:i:s') . " | Environment: " . ENVIRONMENT;
        echo "</div>";
        
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
        
    } catch (\Exception $e) {
        echo "<h2 class='error'>Error:</h2>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>File: " . $e->getFile() . ":" . $e->getLine() . "</pre>";
        exit;
    }
}








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

/*
function save_model_config() {
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
 
      
  //  $item_data = $this->Items_model->saveconfig($item_data, $user_id); 
}
*/
   
   
/*   function save_model_config() {
    session_start();
    $user_id = $_SESSION['user_id'];


    
    $item_data = array(
        "modelSelect" => $this->request->getPost('modelSelect'), 
        "pageSelect" => $this->request->getPost('pageSelect'),
        "textInput" => $this->request->getPost('routerSelect'),
        "statusSelect" => $this->request->getPost('statusSelect'), 
        "ModeSelect" => $this->request->getPost('ModeSelect'), 
        "LevelSelect" => $this->request->getPost('LevelSelect'), 
         "config_keys" =>  $this->request->getPost('configKeys'), 
        "configValues" => $this->request->getPost('configValues'), 
        "remoteaddress" => $this->request->getPost('inputBox'),
    );
    
    

    echo "<pre>";
    print_r($item_data);
    echo "</pre>";

    $item_data = $this->Items_model->save_model_config($item_data, $user_id); 
}
   
   
 */  //19-06-26
 
 
 
 
 
 
private function _make_item_row1_vendor($data) {
    $getValue = function ($aliases, $default = '') use ($data) {
        foreach ($aliases as $alias) {
            if (isset($data->{$alias}) && $data->{$alias} !== '') {
                return $data->{$alias};
            }
        }

        return $default;
    };

    $id = (int) $getValue(array('id'), 0);
    $modelNameValue = $getValue(array('model_name', 'modelname', 'modal_name', 'name'), 'N/A');
    $brandValue = $getValue(array('brand_name', 'brand'), 'No Brand');
    $categoryValue = $getValue(array('category_name', 'category', 'product_name', 'product_type'), 'N/A');
    $productValue = $getValue(array('product_name', 'category_name', 'category', 'product_type'), $categoryValue);
    $modelNoValue = $getValue(array('model_no', 'modelno', 'model_number'), $modelNameValue);
    $versionValue = $getValue(array('version', 'model_version'), 'N/A');
    $batchValue = $getValue(array('batch', 'Batch'), 'N/A');

    $modelNo = rawurlencode((string) $modelNoValue);
    $modelName = rawurlencode((string) $modelNameValue);
    $category = rawurlencode((string) $categoryValue);
    return array(
        'id' => $id,
        'model_name' => $modelNameValue,
        'brand_name' => $brandValue,
        'category_name' => $categoryValue,
        'product_name' => $productValue,
        'model_no' => $modelNoValue,
        'version' => $versionValue,
        'batch' => $batchValue,
        'edit_url' => get_uri("items/viewapis/" . $id),
        'copy_url' => get_uri("items/modal_api/" . $id),
        'view_url' => get_uri("items/viewapis/" . $id),
        'delete_url' => get_uri("items/deletevendorproduct"),
        'actions' => array(
            array('label' => 'Add API/Product', 'icon' => 'fa-plus-circle', 'url' => get_uri("items/modal_api/" . $id), 'class' => 'modal-action', 'title' => 'Add API/Product'),
            array('label' => 'View APIs', 'icon' => 'fa-eye', 'url' => get_uri("items/viewapis/" . $id), 'class' => ''),
            array('label' => 'Add Pages', 'icon' => 'fa-file-text', 'url' => get_uri("items/modal_pages/" . $id . "?modelno=" . $modelNo), 'class' => 'modal-action', 'title' => 'Add Page Names'),
            array('label' => 'Linking Page', 'icon' => 'fa-link', 'url' => get_uri("items/api_page_linking/" . $id . "?modelno=" . $modelNo), 'class' => ''),
            array('label' => 'Linked View UI', 'icon' => 'fa-eye', 'url' => get_uri("items/view_page_ui/" . $id . "?modelno=" . $modelNo), 'class' => ''),
            array('label' => 'Specifications', 'icon' => 'fa-cogs', 'url' => get_uri("items/specifications/" . $id . "?modelno=" . $modelNo), 'class' => ''),
            array('label' => 'Add API Frame Format', 'icon' => 'fa-plus-circle', 'url' => get_uri("items/api_frame_numbers/" . $id), 'class' => 'modal-action', 'title' => 'Add API Frame Format'),
            array('label' => 'Add Description', 'icon' => 'fa-plus-circle', 'url' => get_uri("items/description_api/" . $id), 'class' => 'modal-action', 'title' => 'Add Description'),
            array('label' => 'Auto Test Form', 'icon' => 'fa-flask', 'url' => get_uri("items/auto_test/" . $id . "?modelno=" . $modelNo), 'class' => ''),
            array('label' => 'Auto Test Start', 'icon' => 'fa-flask', 'url' => get_uri("items/auto_test_start/" . $id . "?modelno=" . $modelNo), 'class' => ''),
            array('label' => 'View UI Type', 'icon' => 'fa-desktop', 'url' => get_uri("items/callDisplayFunction/" . $modelNo . "?category=" . $category), 'class' => ''),
            array('label' => 'Device Select', 'icon' => 'fa-cube', 'url' => get_uri("items/callDisplayId"), 'class' => ''),
            array('label' => 'Sticker Update', 'icon' => 'fa-sticky-note', 'url' => get_uri("items/sticker_update/" . $id . "?modelno=" . $modelNo . "&category=" . $category . "&modelname=" . $modelName), 'class' => ''),
            array('label' => 'Confirm', 'icon' => 'fa-check', 'url' => get_uri("items/callConfirmDisplayFunction/" . $modelNo), 'class' => 'confirm-action'),
            array('label' => 'Warranty Card', 'icon' => 'fa-shield', 'url' => 'https://efactor.tech/warranty_form.html?modelname=' . $modelName, 'class' => 'external'),
            array('label' => 'Delete', 'icon' => 'fa-trash', 'url' => get_uri("items/deletevendorproduct"), 'class' => 'delete-action')
        )
    );
}
 
 
 
 
function save_model_config() {
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
    echo "Item Data: <pre>";
    print_r($item_data);
    echo "</pre>";
      
    $item_data = $this->Items_model->saveconfig($item_data, $user_id); 
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
    
    $itemid = $_SESSION['itemid'] ?? null;
    
    
            $id = $this->request->getPost('id');
            $rand=rand('0000','9999');
            $file_name = $rand.'_'.$this->request->getPost("item_files");
            $target_path = get_setting("timeline_file_path");
            $file_info = move_temp_file($file_name, $target_path);
    
    

    
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
            "itemid" => $itemid,
    
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
            
    echo "Data saved successfully!";
    
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
    echo "</pre>";
    
    echo "<script>alert('Data saved successfully!');</script>";
    echo "Data saved successfully!";
    echo "<script>
  //  location.href='https://efactor.tech/index.php/items/vendor';
    </script>";
    } 
    
    else {
       // echo "<script>alert('Error saving data to database!');</script>";
             echo "Error saving data to database!";
    
        }
    }




/*
public function submitTestConfiguration()
{
    ob_start();
    
    header('Content-Type: application/json');

    try {
        $json = file_get_contents("php://input");

        if (!$json) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No input received']);
            exit;
        }

        $all_data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg()
            ]);
            exit;
        }

        // ✅ DEBUG OUTPUT
        $debugOutput = "=========== API TEST ===========\n\n";

        foreach ($all_data as $i => $test) {

            // ✅ GET MAIN PARAMETER (instead of all sub-parameters)
            $mainParam = '';

            // 🔹 Check COMMAND
            foreach ($test['command'] ?? [] as $cmd) {
                if (!empty($cmd['mainparameter'])) {
                    $mainParam = $cmd['mainparameter'];
                    break;
                }
            }

            // 🔹 If not found, check RESPONSE
            if (empty($mainParam)) {
                foreach ($test['response'] ?? [] as $resp) {
                    if (!empty($resp['mainparameter'])) {
                        $mainParam = $resp['mainparameter'];
                        break;
                    }
                }
            }

            // 🔹 If not found, check ERROR
            if (empty($mainParam)) {
                foreach ($test['error'] ?? [] as $err) {
                    if (!empty($err['mainparameter'])) {
                        $mainParam = $err['mainparameter'];
                        break;
                    }
                }
            }

            // 🔹 Fallback
            $paramString = $mainParam ?: 'No Parameter';

            // ✅ TITLE

            $debugOutput .= "API TEST " . ($i + 1) . " (" . $paramString . ")\n";

            // ✅ KEEP ORIGINAL STRUCTURE
            $debugOutput .= "\nCOMMAND:\n" . print_r($test['command'] ?? [], true);
            $debugOutput .= "\nRESPONSE:\n" . print_r($test['response'] ?? [], true);
            $debugOutput .= "\nERROR:\n" . print_r($test['error'] ?? [], true);
            $debugOutput .= "\n---------------------------\n\n";
        }


        ob_end_clean();
      
        echo json_encode([
            'success' => true,
            'print_r_output' => $debugOutput
        ]);
 

        exit;

    } catch (\Throwable $e) {
        if (ob_get_length()) ob_end_clean();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}




*/


/*********************************************************************/


/*
public function submitTestConfiguration()
{
    ob_start();
    header('Content-Type: application/json');

    try {
        $json = file_get_contents("php://input");

        if (!$json) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No input received']);
            exit;
        }

        $all_data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg()
            ]);
            exit;
        }

        // ✅ Extract metadata (FROM REQUEST OR SESSION)
        $payload = [
            'user_id' => $_POST['user_id'] ?? $_GET['user_id'] ?? 177,
            'modelno' => $_POST['modelno'] ?? $_GET['modelno'] ?? 'DEFAULT',
            'id'      => $_POST['id'] ?? null,
            'tests'   => $all_data
        ];

        // ✅ Send to model
        $result = $this->Items_model->saveTestConfiguration($payload);

        ob_end_clean();
        echo json_encode($result);
        exit;

    } catch (\Throwable $e) {
        if (ob_get_length()) ob_end_clean();

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}


*/





public function submitTestConfiguration()
{
    header('Content-Type: application/json');

    try {
        $json = file_get_contents("php://input");

        if (!$json) {
            echo json_encode(['success' => false, 'message' => 'No input received']);
            exit;
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            exit;
        }

        // 🔥 DEBUG (VERY IMPORTANT)
        log_message('error', 'DATA RECEIVED: ' . json_encode($data));

        // ✅ CALL DB
        $this->Items_model->submitTestConfiguration_db($data);

        echo json_encode(['success' => true]);
        exit;

    } catch (\Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}


public function auto_test($id){ 
    session_start();
    $user_id = $_SESSION['user_id'];

    // ✅ Get full item details
    $item = $this->Items_model->get_item_with_details($id);

    // ✅ GET VALUES (better use $_GET)
    $modelno = $_GET['modelno'] ?? null;
    $modelname = $_GET['modelname'] ?? null;

    // ✅ Get parameters
    $parameter_names = $this->Items_model->extract_params($id, $user_id);

    // ✅ FETCH SAVED DATA
    $savedConfig = $this->Items_model->getSavedConfiguration($user_id, $modelno);

    // ✅ LOAD VIEW (IMPORTANT)
    return view('items/auto_test', [
        'user_id' => $user_id,
        'id' => $id,
        'modelno' => $modelno,
        'modelname' => $modelname,
        'parameters' => $parameter_names,
        'savedConfig' => $savedConfig
    ]);
}


public function auto_test_Start($id){ 
    session_start();
    $user_id = $_SESSION['user_id'];

    // ✅ Get full item details
    $item = $this->Items_model->get_item_with_details($id);

    // ✅ GET VALUES (better use $_GET)
    $modelno = $_GET['modelno'] ?? null;
    $modelname = $_GET['modelname'] ?? null;

    // ✅ Get parameters
    $parameter_names = $this->Items_model->extract_params($id, $user_id);

    // ✅ FETCH SAVED DATA
    $savedConfig = $this->Items_model->getSavedConfiguration($user_id, $modelno);

    // ✅ LOAD VIEW (IMPORTANT)
    return view('items/auto_test_start', [
        'user_id' => $user_id,
        'id' => $id,
        'modelno' => $modelno,
        'modelname' => $modelname,
        'parameters' => $parameter_names,
        'savedConfig' => $savedConfig
    ]);
}



/*
// Additional endpoint to retrieve configurations
public function getTestConfigurations($configId = null)
{
    header('Content-Type: application/json');
    
    try {
      //  $testModel = new TestConfigurationModel($this->db);
        
        if ($configId) {
            // Get single configuration
            $config = $this->Items_model->getTestConfiguration($configId);
            
            if ($config) {
                echo json_encode([
                    'success' => true,
                    'data' => $config
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Configuration not found'
                ]);
            }
        } else {
            // Get all configurations with pagination
            $limit = $_GET['limit'] ?? 50;
            $offset = $_GET['offset'] ?? 0;
            
            $result = $this->Items_model->getAllTestConfigurations($limit, $offset);
            echo json_encode($result);
        }
        
    } catch (\Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Additional endpoint to query by parameter type
public function getParametersByType($parameterType)
{
    header('Content-Type: application/json');
    
    try {
      //  $testModel = new TestConfigurationModel($this->db);
        $instanceId = $_GET['instance_id'] ?? null;
        
        $result = $this->Items_model->getParametersByType($parameterType, $instanceId);
        echo json_encode($result);
        
    } catch (\Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


*/


/******************************************************************************/


/*
public function saveapi() {
    // Get user ID from session
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    $itemid = $_SESSION['itemid'] ?? null;
    
    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "User not authenticated"]);
        return;
    }
    
    try {
        // ============================
        // DEBUG: PRINT ALL INPUT DATA
        // ============================
        echo "<h2>DEBUG: ALL POST DATA</h2>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        // Also print specific arrays
        echo "<h3>Command Frame Rows Check:</h3>";
        $cmd_frame_size = (int)$this->request->getPost('cmd_noofrows');
        echo "cmd_noofrows: " . $cmd_frame_size . "<br>";
        
        for ($i = 1; $i <= $cmd_frame_size; $i++) {
            echo "<h4>Command Row {$i}:</h4>";
            echo "startBytePosition_cmd_{$i}: " . $this->request->getPost("startBytePosition_cmd_{$i}") . "<br>";
            echo "fieldDescription_cmd_{$i}: " . $this->request->getPost("fieldDescription_cmd_{$i}") . "<br>";
            echo "dataType_cmd_{$i}: " . $this->request->getPost("dataType_cmd_{$i}") . "<br>";
            echo "uiType_cmd_{$i}: " . $this->request->getPost("uiType_cmd_{$i}") . "<br>";
            echo "defaultValue_cmd_{$i}: " . $this->request->getPost("defaultValue_cmd_{$i}") . "<br>";
        }
        
        echo "<h3>Response Frame Rows Check:</h3>";
        $resp_frame_size = (int)$this->request->getPost('resp_noofrows');
        echo "resp_noofrows: " . $resp_frame_size . "<br>";
        
        echo "<h3>Error Frame Rows Check:</h3>";
        $error_frame_size = (int)$this->request->getPost('error_noofrows');
        echo "error_noofrows: " . $error_frame_size . "<br>";
        
        // ============================
        // 1. PREPARE MAIN API DATA
        // ============================
        $item_data = array(
            "brandname" => $this->request->getPost('brandname'),
            "productname" => $this->request->getPost('productname'),
            "modelname" => $this->request->getPost('modelname'),
            "productdescription" => $this->request->getPost('productdescription'),
            "numberofpages" => $this->request->getPost('numberofpages'),
            "Sequence" => $this->request->getPost('Sequence'),
            "pagenames" => $this->request->getPost('pagenames'),
            "date" => $this->request->getPost('date') ?: date('Y-m-d'),
            "itemid" => $itemid,
            
            // Parameter name (from step 1)
            "parametername" => $this->request->getPost('parametername'),
            
            // Command Frame Header Data
            "cmd_parametername" => $this->request->getPost('cmd_parametername'),
            "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),
            "cmd_frametype" => $this->request->getPost('cmd_frametype'),
            "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
            "cmd_noofrows" => $this->request->getPost('cmd_noofrows'),
            "cmd_frame_trigger_options" => $this->request->getPost('cmd_frame_trigger_options'),
            "cmd_multipleui" => $this->request->getPost('cmd_multipleui'),
            "cmd_triggerui" => $this->request->getPost('cmd_triggerui'),
            "cmd_voice_command" => $this->request->getPost('cmd_voice_command'),
            "cmd_voice_control_instruction" => $this->request->getPost('cmd_voice_control_instruction'),
            "cmd_remarks" => $this->request->getPost('cmd_remarks'),
            
            // Response Frame Header Data
            "resp_parametername" => $this->request->getPost('resp_parametername'),
            "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),
            "resp_frametype" => $this->request->getPost('resp_frametype'),
            "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
            "resp_noofrows" => $this->request->getPost('resp_noofrows'),
            "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
            "resp_multipleui" => $this->request->getPost('resp_multipleui'),
            "resp_triggerui" => $this->request->getPost('resp_triggerui'),
            "resp_voice_command" => $this->request->getPost('resp_voice_command'),
            "resp_voice_control_instruction" => $this->request->getPost('resp_voice_control_instruction'),
            "resp_remarks" => $this->request->getPost('resp_remarks'),
            
            // Error Frame Header Data
            "error_parametername" => $this->request->getPost('error_parametername'),
            "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),
            "error_frametype" => $this->request->getPost('error_frametype'),
            "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
            "error_noofrows" => $this->request->getPost('error_noofrows'),
            "error_frametrigger" => $this->request->getPost('error_frametrigger'),
            "error_multipleui" => $this->request->getPost('error_multipleui'),
            "error_triggerui" => $this->request->getPost('error_triggerui'),
            "error_voice_command" => $this->request->getPost('error_voice_command'),
            "error_voice_control_instruction" => $this->request->getPost('error_voice_control_instruction'),
            "error_remarks" => $this->request->getPost('error_remarks'),
            
            // Timestamps
            "created_by" => $user_id,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_by" => $user_id,
            "updated_at" => date('Y-m-d H:i:s')
        );
        
        // Debug: Print main item data
        echo "<h2>DEBUG: Main Item Data</h2>";
        echo "<pre>";
        print_r($item_data);
        echo "</pre>";
        
        // ============================
        // 2. PREPARE COMMAND FRAME ROWS
        // ============================
        $additional_data = array();
        $cmd_frame_size = (int)$this->request->getPost('cmd_noofrows');
        echo "<h2>DEBUG: Command Frame Size = {$cmd_frame_size}</h2>";
        
        for ($i = 1; $i <= $cmd_frame_size; $i++) {
            // Get field values - use both naming patterns for compatibility
            $startByte = $this->request->getPost("startBytePosition_cmd_{$i}") ?? 
                        $this->request->getPost("cmd_startBytePosition_{$i}");
            
            $endByte = $this->request->getPost("endBytePosition_cmd_{$i}") ?? 
                      $this->request->getPost("cmd_endBytePosition_{$i}");
            
            echo "<h3>DEBUG: Command Row {$i}</h3>";
            echo "startByte: {$startByte}<br>";
            echo "endByte: {$endByte}<br>";
            
            // Skip if no start byte (empty row)
            if (empty($startByte) && empty($endByte)) {
                echo "Skipping row {$i} - no data<br>";
                continue;
            }
            
            // Process array fields (if they exist)
            $asperuiData = $this->request->getPost("asperui_cmd_{$i}") ?? "";
            $asperuiArray = !empty($asperuiData) ? array_map('trim', explode(',', $asperuiData)) : [];
            $as_per_ui_array = !empty($asperuiArray) ? '[' . implode('][', $asperuiArray) . ']' : '';
            
            $sno_asperuiData = $this->request->getPost("sno_asperui_cmd_{$i}") ?? "";
            $sno_asperuiArray = !empty($sno_asperuiData) ? array_map('trim', explode(',', $sno_asperuiData)) : [];
            $sno_as_per_ui_array = !empty($sno_asperuiArray) ? '[' . implode('][', $sno_asperuiArray) . ']' : '';
            
            $asperuivalueData = $this->request->getPost("asperuivalue_cmd_{$i}") ?? "";
            $asperuivalueArray = !empty($asperuivalueData) ? array_map('trim', explode(',', $asperuivalueData)) : [];
            $as_per_ui_value_array = !empty($asperuivalueArray) ? '[' . implode('][', $asperuivalueArray) . ']' : '';
            
            $row_data = array(
                "sno" => $i,
                "startBytePosition" => $startByte,
                "endBytePosition" => $endByte,
                "fieldDescription" => $this->request->getPost("fieldDescription_cmd_{$i}") ?? 
                                     $this->request->getPost("cmd_fieldDescription_{$i}"),
                "numberOfBytes" => $this->request->getPost("noOfBytes_cmd_{$i}") ?? 
                                   $this->request->getPost("cmd_numberOfBytes_{$i}"),
                "bitSize" => $this->request->getPost("bitSize_cmd_{$i}") ?? 
                             $this->request->getPost("cmd_bitSize_{$i}"),
                "dataType" => $this->request->getPost("dataType_cmd_{$i}") ?? 
                              $this->request->getPost("cmd_dataType_{$i}"),
                "valueDefinition" => $this->request->getPost("valueDefinition_cmd_{$i}") ?? 
                                     $this->request->getPost("cmd_valueDefinition_{$i}"),
                "displayValueMin" => $this->request->getPost("displayMin_cmd_{$i}") ?? 
                                     $this->request->getPost("cmd_displayValueMin_{$i}"),
                "displayValueMax" => $this->request->getPost("displayMax_cmd_{$i}") ?? 
                                     $this->request->getPost("cmd_displayValueMax_{$i}"),
                "valueInHexMin" => $this->request->getPost("hexMin_cmd_{$i}") ?? 
                                   $this->request->getPost("cmd_valueInHexMin_{$i}"),
                "valueInHexMax" => $this->request->getPost("hexMax_cmd_{$i}") ?? 
                                   $this->request->getPost("cmd_valueInHexMax_{$i}"),
                "uiType" => $this->request->getPost("uiType_cmd_{$i}") ?? 
                            $this->request->getPost("cmd_uiType_{$i}"),
                "uiId" => $this->request->getPost("uiId_cmd_{$i}") ?? 
                          $this->request->getPost("cmd_uiId_{$i}"),
                "asperui" => $as_per_ui_array,
                "sno_asperui" => $sno_as_per_ui_array,
                "asperuivalue" => $as_per_ui_value_array,
                "uiPageNo" => $this->request->getPost("uiPageNo_cmd_{$i}") ?? 
                              $this->request->getPost("cmd_uiPageNo_{$i}"),
                "uiDescriptionInOut" => $this->request->getPost("uiInOut_cmd_{$i}") ?? 
                                        $this->request->getPost("cmd_uiDescriptionInOut_{$i}"),
                "sliderStepValue" => $this->request->getPost("sliderStep_cmd_{$i}") ?? 
                                     $this->request->getPost("cmd_sliderStepValue_{$i}"),
                "defaultvalue" => $this->request->getPost("defaultValue_cmd_{$i}") ?? 
                                  $this->request->getPost("cmd_defaultvalue_{$i}"),
                "developerNotes" => $this->request->getPost("developerNotes_cmd_{$i}") ?? 
                                    $this->request->getPost("cmd_developerNotes_{$i}"),
                "frame_type" => "cmd",
                "created_by" => $user_id,
                "created_at" => date('Y-m-d H:i:s')
            );
            
            $additional_data[] = $row_data;
            
            // Debug row data
            echo "<pre>Row {$i} Data: ";
            print_r($row_data);
            echo "</pre>";
        }
        
        // ============================
        // 3. PREPARE RESPONSE FRAME ROWS
        // ============================
        $respframe_data = array();
        $resp_frame_size = (int)$this->request->getPost('resp_noofrows');
        echo "<h2>DEBUG: Response Frame Size = {$resp_frame_size}</h2>";
        
        for ($i = 1; $i <= $resp_frame_size; $i++) {
            $startByte = $this->request->getPost("startBytePosition_resp_{$i}") ?? 
                        $this->request->getPost("resp_startBytePosition_{$i}");
            
            $endByte = $this->request->getPost("endBytePosition_resp_{$i}") ?? 
                      $this->request->getPost("resp_endBytePosition_{$i}");
            
            echo "<h3>DEBUG: Response Row {$i}</h3>";
            echo "startByte: {$startByte}<br>";
            echo "endByte: {$endByte}<br>";
            
            // Skip if no start byte (empty row)
            if (empty($startByte) && empty($endByte)) {
                echo "Skipping response row {$i} - no data<br>";
                continue;
            }
            
            // Process array fields
            $asperuiData = $this->request->getPost("asperui_resp_{$i}") ?? "";
            $asperuiArray = !empty($asperuiData) ? array_map('trim', explode(',', $asperuiData)) : [];
            $resp_as_per_ui_array = !empty($asperuiArray) ? '[' . implode('][', $asperuiArray) . ']' : '';
            
            $sno_asperuiData = $this->request->getPost("sno_asperui_resp_{$i}") ?? "";
            $sno_asperuiArray = !empty($sno_asperuiData) ? array_map('trim', explode(',', $sno_asperuiData)) : [];
            $resp_sno_as_per_ui_array = !empty($sno_asperuiArray) ? '[' . implode('][', $sno_asperuiArray) . ']' : '';
            
            $asperuivalueData = $this->request->getPost("asperuivalue_resp_{$i}") ?? "";
            $asperuivalueArray = !empty($asperuivalueData) ? array_map('trim', explode(',', $asperuivalueData)) : [];
            $resp_as_per_ui_value_array = !empty($asperuivalueArray) ? '[' . implode('][', $asperuivalueArray) . ']' : '';
            
            $row_data = array(
                "sno" => $i,
                "startBytePosition" => $startByte,
                "endBytePosition" => $endByte,
                "fieldDescription" => $this->request->getPost("fieldDescription_resp_{$i}") ?? 
                                     $this->request->getPost("resp_fieldDescription_{$i}"),
                "numberOfBytes" => $this->request->getPost("noOfBytes_resp_{$i}") ?? 
                                   $this->request->getPost("resp_numberOfBytes_{$i}"),
                "bitSize" => $this->request->getPost("bitSize_resp_{$i}") ?? 
                             $this->request->getPost("resp_bitSize_{$i}"),
                "dataType" => $this->request->getPost("dataType_resp_{$i}") ?? 
                              $this->request->getPost("resp_dataType_{$i}"),
                "valueDefinition" => $this->request->getPost("valueDefinition_resp_{$i}") ?? 
                                     $this->request->getPost("resp_valueDefinition_{$i}"),
                "displayValueMin" => $this->request->getPost("displayMin_resp_{$i}") ?? 
                                     $this->request->getPost("resp_displayValueMin_{$i}"),
                "displayValueMax" => $this->request->getPost("displayMax_resp_{$i}") ?? 
                                     $this->request->getPost("resp_displayValueMax_{$i}"),
                "valueInHexMin" => $this->request->getPost("hexMin_resp_{$i}") ?? 
                                   $this->request->getPost("resp_valueInHexMin_{$i}"),
                "valueInHexMax" => $this->request->getPost("hexMax_resp_{$i}") ?? 
                                   $this->request->getPost("resp_valueInHexMax_{$i}"),
                "uiType" => $this->request->getPost("uiType_resp_{$i}") ?? 
                            $this->request->getPost("resp_uiType_{$i}"),
                "uiId" => $this->request->getPost("uiId_resp_{$i}") ?? 
                          $this->request->getPost("resp_uiId_{$i}"),
                "asperui" => $resp_as_per_ui_array,
                "sno_asperui" => $resp_sno_as_per_ui_array,
                "asperuivalue" => $resp_as_per_ui_value_array,
                "uiPageNo" => $this->request->getPost("uiPageNo_resp_{$i}") ?? 
                              $this->request->getPost("resp_uiPageNo_{$i}"),
                "uiDescriptionInOut" => $this->request->getPost("uiInOut_resp_{$i}") ?? 
                                        $this->request->getPost("resp_uiDescriptionInOut_{$i}"),
                "sliderStepValue" => $this->request->getPost("sliderStep_resp_{$i}") ?? 
                                     $this->request->getPost("resp_sliderStepValue_{$i}"),
                "defaultvalue" => $this->request->getPost("defaultValue_resp_{$i}") ?? 
                                  $this->request->getPost("resp_defaultvalue_{$i}"),
                "developerNotes" => $this->request->getPost("developerNotes_resp_{$i}") ?? 
                                    $this->request->getPost("resp_developerNotes_{$i}"),
                "frame_type" => "resp",
                "created_by" => $user_id,
                "created_at" => date('Y-m-d H:i:s')
            );
            
            $respframe_data[] = $row_data;
            
            // Debug row data
            echo "<pre>Response Row {$i} Data: ";
            print_r($row_data);
            echo "</pre>";
        }
        
        // ============================
        // 4. PREPARE ERROR FRAME ROWS
        // ============================
        $errorframe_data = array();
        $error_frame_size = (int)$this->request->getPost('error_noofrows');
        echo "<h2>DEBUG: Error Frame Size = {$error_frame_size}</h2>";
        
        for ($i = 1; $i <= $error_frame_size; $i++) {
            $startByte = $this->request->getPost("startBytePosition_error_{$i}") ?? 
                        $this->request->getPost("error_startBytePosition_{$i}");
            
            $endByte = $this->request->getPost("endBytePosition_error_{$i}") ?? 
                      $this->request->getPost("error_endBytePosition_{$i}");
            
            echo "<h3>DEBUG: Error Row {$i}</h3>";
            echo "startByte: {$startByte}<br>";
            echo "endByte: {$endByte}<br>";
            
            // Skip if no start byte (empty row)
            if (empty($startByte) && empty($endByte)) {
                echo "Skipping error row {$i} - no data<br>";
                continue;
            }
            
            // Process array fields
            $asperuiData = $this->request->getPost("asperui_error_{$i}") ?? "";
            $asperuiArray = !empty($asperuiData) ? array_map('trim', explode(',', $asperuiData)) : [];
            $error_as_per_ui_array = !empty($asperuiArray) ? '[' . implode('][', $asperuiArray) . ']' : '';
            
            $sno_asperuiData = $this->request->getPost("sno_asperui_error_{$i}") ?? "";
            $sno_asperuiArray = !empty($sno_asperuiData) ? array_map('trim', explode(',', $sno_asperuiData)) : [];
            $error_sno_as_per_ui_array = !empty($sno_asperuiArray) ? '[' . implode('][', $sno_asperuiArray) . ']' : '';
            
            $asperuivalueData = $this->request->getPost("asperuivalue_error_{$i}") ?? "";
            $asperuivalueArray = !empty($asperuivalueData) ? array_map('trim', explode(',', $asperuivalueData)) : [];
            $error_as_per_ui_value_array = !empty($asperuivalueArray) ? '[' . implode('][', $asperuivalueArray) . ']' : '';
            
            $row_data = array(
                "sno" => $i,
                "startBytePosition" => $startByte,
                "endBytePosition" => $endByte,
                "fieldDescription" => $this->request->getPost("fieldDescription_error_{$i}") ?? 
                                     $this->request->getPost("error_fieldDescription_{$i}"),
                "numberOfBytes" => $this->request->getPost("noOfBytes_error_{$i}") ?? 
                                   $this->request->getPost("error_numberOfBytes_{$i}"),
                "bitSize" => $this->request->getPost("bitSize_error_{$i}") ?? 
                             $this->request->getPost("error_bitSize_{$i}"),
                "dataType" => $this->request->getPost("dataType_error_{$i}") ?? 
                              $this->request->getPost("error_dataType_{$i}"),
                "valueDefinition" => $this->request->getPost("valueDefinition_error_{$i}") ?? 
                                     $this->request->getPost("error_valueDefinition_{$i}"),
                "displayValueMin" => $this->request->getPost("displayMin_error_{$i}") ?? 
                                     $this->request->getPost("error_displayValueMin_{$i}"),
                "displayValueMax" => $this->request->getPost("displayMax_error_{$i}") ?? 
                                     $this->request->getPost("error_displayValueMax_{$i}"),
                "valueInHexMin" => $this->request->getPost("hexMin_error_{$i}") ?? 
                                   $this->request->getPost("error_valueInHexMin_{$i}"),
                "valueInHexMax" => $this->request->getPost("hexMax_error_{$i}") ?? 
                                   $this->request->getPost("error_valueInHexMax_{$i}"),
                "uiType" => $this->request->getPost("uiType_error_{$i}") ?? 
                            $this->request->getPost("error_uiType_{$i}"),
                "uiId" => $this->request->getPost("uiId_error_{$i}") ?? 
                          $this->request->getPost("error_uiId_{$i}"),
                "asperui" => $error_as_per_ui_array,
                "sno_asperui" => $error_sno_as_per_ui_array,
                "asperuivalue" => $error_as_per_ui_value_array,
                "uiPageNo" => $this->request->getPost("uiPageNo_error_{$i}") ?? 
                              $this->request->getPost("error_uiPageNo_{$i}"),
                "uiDescriptionInOut" => $this->request->getPost("uiInOut_error_{$i}") ?? 
                                        $this->request->getPost("error_uiDescriptionInOut_{$i}"),
                "sliderStepValue" => $this->request->getPost("sliderStep_error_{$i}") ?? 
                                     $this->request->getPost("error_sliderStepValue_{$i}"),
                "defaultvalue" => $this->request->getPost("defaultValue_error_{$i}") ?? 
                                  $this->request->getPost("error_defaultvalue_{$i}"),
                "developerNotes" => $this->request->getPost("developerNotes_error_{$i}") ?? 
                                    $this->request->getPost("error_developerNotes_{$i}"),
                "frame_type" => "error",
                "created_by" => $user_id,
                "created_at" => date('Y-m-d H:i:s')
            );
            
            $errorframe_data[] = $row_data;
            
            // Debug row data
            echo "<pre>Error Row {$i} Data: ";
            print_r($row_data);
            echo "</pre>";
        }
        
        // Debug: Print all arrays
        echo "<h2>DEBUG: Final Arrays</h2>";
        echo "<h3>Command Data (count: " . count($additional_data) . "):</h3>";
        echo "<pre>";
        print_r($additional_data);
        echo "</pre>";
        
        echo "<h3>Response Data (count: " . count($respframe_data) . "):</h3>";
        echo "<pre>";
        print_r($respframe_data);
        echo "</pre>";
        
        echo "<h3>Error Data (count: " . count($errorframe_data) . "):</h3>";
        echo "<pre>";
        print_r($errorframe_data);
        echo "</pre>";
        
        // ============================
        // 5. SAVE DATA TO DATABASE
        // ============================
        // Clean empty arrays
        $additional_data = !empty($additional_data) ? $additional_data : [];
        $respframe_data = !empty($respframe_data) ? $respframe_data : [];
        $errorframe_data = !empty($errorframe_data) ? $errorframe_data : [];
        
        // Get the ID if editing existing record
        $id = $this->request->getPost('id');
        
        echo "<h2>DEBUG: Attempting to save data</h2>";
        echo "ID: " . ($id ? $id : "New record") . "<br>";
        echo "Command rows to save: " . count($additional_data) . "<br>";
        echo "Response rows to save: " . count($respframe_data) . "<br>";
        echo "Error rows to save: " . count($errorframe_data) . "<br>";
        
        // Save to database via model
        $result = $this->Items_model->saveapi($item_data, $additional_data, $respframe_data, $errorframe_data, $user_id, $id);
        
        if ($result) {
            echo "<h2 style='color: green;'>SUCCESS: Data saved! ID: {$result}</h2>";
            echo json_encode([
                "success" => true,
                "message" => "API configuration saved successfully!",
                "id" => $result
            ]);
        } else {
            echo "<h2 style='color: red;'>ERROR: Failed to save data</h2>";
            echo json_encode([
                "success" => false,
                "message" => "Error saving data to database!"
            ]);
        }
        
    } catch (Exception $e) {
        echo "<h2 style='color: red;'>EXCEPTION: " . $e->getMessage() . "</h2>";
        echo "<pre>Trace: " . $e->getTraceAsString() . "</pre>";
        
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
}


*/


/*

public function saveapi()
{
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json');

    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $master_json = $this->request->getPost('full_config_json');
        $all_data = json_decode($master_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($all_data)) {
            throw new \Exception('Invalid full_config_json received.');
        }

        $mainData = array(
            'itemid'              => $this->request->getPost('itemid'),
            'brandname'           => $this->request->getPost('brandname'),
            'productname'         => $this->request->getPost('productname'),
            'modelname'           => $this->request->getPost('modelname'),
            'date'                => $this->request->getPost('date'),
            'parameter_name'      => $this->request->getPost('parameter_name'),
            'page_number'         => $this->request->getPost('page_number'),
            'sequence_number'     => $this->request->getPost('sequence_number'),
            'command_frame_count' => $this->request->getPost('command_frame_count'),
            'response_frame_count'=> $this->request->getPost('response_frame_count'),
            'error_frame_count'   => $this->request->getPost('error_frame_count'),
            'page_names'          => $this->request->getPost('page_names'),
            'product_description' => $this->request->getPost('product_description')
        );

        $editConfigId = (int) ($this->request->getPost('config_id') ?? 0);

        // Save into new normalized DB tables.
        if ($editConfigId > 0) {
            $treeConfigId = $this->Items_model->replaceApiTreeConfig($editConfigId, $mainData, $all_data, $user_id);
        } else {
            $treeConfigId = $this->Items_model->saveApiTreeConfig($mainData, $all_data, $user_id);
        }
        
        
  

        $saveCheck = $this->Items_model->getApiTreeSaveCheck($treeConfigId);
        // Keep your existing preview popup behavior.
        // ob_start();

        // echo "====================================================\n";
        // echo "         FULL PRODUCT CONFIGURATION SUMMARY         \n";
        // echo "====================================================\n";
        
        
        

        $dbNameRow = $this->Items_model->db->query('SELECT DATABASE() AS db_name')->getRowArray();
$connectedDbName = isset($dbNameRow['db_name']) ? $dbNameRow['db_name'] : 'UNKNOWN';

ob_start();

echo "====================================================\n";
echo "         FULL PRODUCT CONFIGURATION SUMMARY..        \n";
echo "====================================================\n";

echo "CONNECTED DATABASE : " . ($saveCheck['connected_database'] ?? 'UNKNOWN') . "\n";
echo "MAIN ROW FOUND     : " . ($saveCheck['main_row_found'] ?? 0) . "\n";
echo "FRAME ROWS SAVED   : " . ($saveCheck['frame_rows_saved'] ?? 0) . "\n";
echo "FIELD ROWS SAVED   : " . ($saveCheck['field_rows_saved'] ?? 0) . "\n";
echo "----------------------------------------------------\n";
echo "CONNECTED DATABASE : " . $connectedDbName . "\n";


        echo "====================================================\n";
echo "FULL JSON DATA (\$all_data)\n";
echo "====================================================\n";
print_r($all_data);
echo "\n\n";



        echo "DATABASE CONFIG ID: " . ($treeConfigId ?: 'N/A') . "\n";
        echo "BRAND NAME    : " . ($mainData['brandname'] ?: 'N/A') . "\n";
        echo "PRODUCT NAME  : " . ($mainData['productname'] ?: 'N/A') . "\n";
        echo "MODEL NAME    : " . ($mainData['modelname'] ?: 'N/A') . "\n";
        echo "DATE          : " . ($mainData['date'] ?: 'N/A') . "\n";
        echo "PARAMETER NAME: " . ($mainData['parameter_name'] ?: 'N/A') . "\n";
        echo "PAGE NUMBER   : " . ($mainData['page_number'] ?: 'N/A') . "\n";
        echo "SEQUENCE NO   : " . ($mainData['sequence_number'] ?: 'N/A') . "\n";
        echo "DESCRIPTION   : " . ($mainData['product_description'] ?: 'N/A') . "\n";

        $cmd_count = count($all_data['commands'] ?? array());
        $res_count = count($all_data['responses'] ?? array());
        $err_count = count($all_data['errors'] ?? array());

        echo "----------------------------------------------------\n";
        echo "NO. OF COMMAND FRAMES  : " . $cmd_count . "\n";
        echo "NO. OF RESPONSE FRAMES : " . $res_count . "\n";
        echo "NO. OF ERROR FRAMES    : " . $err_count . "\n";
        echo "----------------------------------------------------\n\n";

        $sections = array(
            'commands'  => 'COMMAND FRAMES',
            'responses' => 'RESPONSE FRAMES',
            'errors'    => 'ERROR FRAMES'
        );

        foreach ($sections as $key => $title) {
            echo "[SECTION] " . $title . " (" . count($all_data[$key] ?? array()) . ")\n";

            if (!empty($all_data[$key]) && is_array($all_data[$key])) {
                foreach ($all_data[$key] as $index => $frame) {
                    echo "--- Frame #" . $index . " ---\n";

                    echo "META DATA:\n";
                    print_r(isset($frame['meta']) ? $frame['meta'] : 'No Meta Data');

                    echo "\nROW DATA (TABLE):\n";
                    print_r(isset($frame['rows']) ? $frame['rows'] : 'No Row Data');

                    echo "\n";
                }
            } else {
                echo "N/A\n";
            }

            echo "\n";
        }

        echo "================ END OF REPORT ================";

        $final_report = ob_get_clean();

        // echo json_encode(array(
        //     'success'        => true,
        //     'message'        => 'API configuration saved successfully.',
        //     'config_id'      => $treeConfigId,
        //     'print_r_output' => $final_report
        // ));

        echo json_encode(array(
    'success'        => true,
    'message'        => $editConfigId > 0 ? 'API configuration updated successfully.' : 'API configuration saved successfully.',
    'config_id'      => $treeConfigId,
    'save_check'     => $saveCheck,
    'print_r_output' => $final_report
));

        exit;

    } catch (\Throwable $e) {
        if (ob_get_length()) {
            ob_clean();
        }

        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage()
        ));

        exit;
    }
}
*/  //pavan_form


/********************************************************/

/*
public function saveapi() {

    // Clear any previous output
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json');

    try {

        $master_json = $this->request->getPost('full_config_json');
        $all_data = json_decode($master_json, true);

        // POST values
        $brand       = $this->request->getPost('brandname');
        $product     = $this->request->getPost('productname');
        $model       = $this->request->getPost('modelname');
        $date        = $this->request->getPost('date');
        $param_name  = $this->request->getPost('parameter_name');
        $page_num    = $this->request->getPost('page_number');
        $seq_num     = $this->request->getPost('sequence_number');
        $description = $this->request->getPost('product_description');

        // Start output buffer
        ob_start();

        echo "====================================================\n";
        echo "         FULL PRODUCT CONFIGURATION SUMMARY         \n";
        echo "====================================================\n";

        echo "BRAND NAME    : " . ($brand ?: 'N/A') . "\n";
        echo "PRODUCT NAME  : " . ($product ?: 'N/A') . "\n";
        echo "MODEL NAME    : " . ($model ?: 'N/A') . "\n";
        echo "DATE          : " . ($date ?: 'N/A') . "\n";
        echo "PARAMETER NAME: " . ($param_name ?: 'N/A') . "\n";
        echo "PAGE NUMBER   : " . ($page_num ?: 'N/A') . "\n";
        echo "SEQUENCE NO   : " . ($seq_num ?: 'N/A') . "\n";
        echo "DESCRIPTION   : " . ($description ?: 'N/A') . "\n";

        $cmd_count = count($all_data['commands'] ?? []);
        $res_count = count($all_data['responses'] ?? []);
        $err_count = count($all_data['errors'] ?? []);

        echo "----------------------------------------------------\n";
        echo "NO. OF COMMAND FRAMES  : $cmd_count\n";
        echo "NO. OF RESPONSE FRAMES : $res_count\n";
        echo "NO. OF ERROR FRAMES    : $err_count\n";
        echo "----------------------------------------------------\n\n";

        $sections = [
            'commands'  => 'COMMAND',
            'responses' => 'RESPONSE',
            'errors'    => 'ERROR'
        ];

        foreach ($sections as $key => $label) {

            echo "[SECTION] $label FRAMES (" . count($all_data[$key] ?? []) . ")\n";

            if (!empty($all_data[$key])) {

                foreach ($all_data[$key] as $index => $frame) {

                    echo "--- Frame #$index ---\n";

                    echo "META DATA:\n";
                    print_r($frame['meta'] ?? 'No Meta Data');

                    echo "\nROW DATA (TABLE):\n";
                    print_r($frame['rows'] ?? 'No Row Data');

                    echo "\n";
                }

            } else {
                echo "N/A\n";
            }

            echo "\n";
        }

        echo "================ END OF REPORT ================";

        $final_report = ob_get_clean();

        // Return JSON response
        echo json_encode([
            "success" => true,
            "print_r_output" => $final_report
        ]);

        exit;

    } catch (\Throwable $e) {

        if (ob_get_length()) {
            ob_clean();
        }

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);

        exit;
    }
}

*/   // New 10-03-26


/********************************************************/   
   function saveapi_edit() {
    
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
    
    echo "Data saved successfully!";
    
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
    echo "</pre>";
    
    echo "Data saved successfully!";
    echo "<script>
  //  location.href='https://efactor.tech/index.php/items/vendor';
    </script>";
    
    } 
    
    else {
           
            echo "Error saving data to database!";
    
        }
    }


/*
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
*/

public function save_pages()
{
    $pageNames  = $this->request->getPost('input_box');
    $extensions = $this->request->getPost('dropdown');

    $userId  = $this->request->getPost('user_id');
    $modelNo = $this->request->getPost('modelno');
    $modalId = $this->request->getPost('modal_id');

    $pages = [];

    if (!empty($pageNames)) {

        foreach ($pageNames as $key => $pageName) {

            $pageName = trim($pageName);

            if ($pageName == '') {
                continue;
            }

            $extension = isset($extensions[$key]) ? trim($extensions[$key]) : '';

            $pages[] = $pageName . "." . $extension;
        }
    }

    $pageData = [
        'user_id'  => $userId,
        'modelno'  => $modelNo,
        'modal_id' => $modalId
    ];

    $result = $this->Items_model->save_pages($pages, $pageData);

    if ($result) {

        $savedPages = $this->Items_model->extract_pages($userId, $modelNo, $modalId);

        $pageList = [];

        foreach ($savedPages as $row) {

            if (!empty($row['page_names'])) {

                foreach (explode(',', $row['page_names']) as $page) {

                    $page = trim($page);

                    if ($page != '') {
                        $pageList[] = $page;
                    }
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pages saved successfully.',
            'pages'   => array_values(array_unique($pageList))
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Database Error'
    ]);
}


/*******************************************************************/

/*
public function saveapi_edit() {
    header('Content-Type: application/json');
    
    try {
        // 1. Get the JSON data sent from JS
        // We prioritize 'full_config_json' but fallback to 'structured_frame_data'
        $json_raw = $this->request->getPost('full_config_json') ?: $this->request->getPost('structured_frame_data');
        $all_data = json_decode($json_raw, true);

        // 2. Capture Header Information (POST keys from your JS productInfo object)
        // 2. Capture Header Information (Updated to match your JS FormData keys)
$brand        = $this->request->getPost('brandname');
$product      = $this->request->getPost('productname');
$model        = $this->request->getPost('modelname');
$date         = $this->request->getPost('date');

// These are the keys your JS is actually sending:
$param_name   = $this->request->getPost('cmd_parametername'); 
$page_num     = $this->request->getPost('total_command_frames'); 
$seq_num      = $this->request->getPost('Sequence'); 
$description  = $this->request->getPost('product_description');
        // 3. Start Output Buffering for the "Pretty Report"
        ob_start();
        echo "====================================================\n";
        echo "       SAVED CONFIGURATION REPORT (EDIT MODE)       \n";
        echo "====================================================\n";
        
        echo "BRAND NAME    : " . ($brand ?: 'N/A') . "\n";
        echo "PRODUCT NAME  : " . ($product ?: 'N/A') . "\n";
        echo "MODEL NAME    : " . ($model ?: 'N/A') . "\n";
        echo "DATE          : " . ($date ?: 'N/A') . "\n";
        echo "PARAMETER NAME: " . ($param_name ?: 'N/A') . "\n";
        echo "PAGE NUMBER   : " . ($page_num ?: 'N/A') . "\n";
        echo "SEQUENCE NO   : " . ($seq_num ?: 'N/A') . "\n";
        echo "DESCRIPTION   : " . ($description ?: 'N/A') . "\n";
        
        // Use the structure from your structured_frame_data
        $commands  = $all_data['commands'] ?? $all_data['commandFrames'] ?? [];
        $responses = $all_data['responses'] ?? $all_data['responseFrames'] ?? [];
        $errors    = $all_data['errors'] ?? $all_data['errorFrames'] ?? [];

        echo "----------------------------------------------------\n";
        echo "NO. OF COMMAND FRAMES  : " . count($commands) . "\n";
        echo "NO. OF RESPONSE FRAMES : " . count($responses) . "\n";
        echo "NO. OF ERROR FRAMES    : " . count($errors) . "\n";
        echo "----------------------------------------------------\n\n";

        // 4. Map the sections for iteration
        $sections = [
            'COMMAND'  => $commands,
            'RESPONSE' => $responses,
            'ERROR'    => $errors
        ];

                
                
      
        
      

      foreach ($sections as $label => $frames) {
    echo "[[ SECTION: $label ]]\n";
    
    // Determine prefix based on section
    $prefix = ($label == 'COMMAND') ? 'cmd' : (($label == 'RESPONSE') ? 'resp' : 'error');

    if (!empty($frames)) {
        foreach ($frames as $index => $frameRows) {
            echo "--- Frame #" . ($index + 1) . " ---\n";
            
            // 1. Calculate actual rows for THIS specific frame
            $actualRows = is_array($frameRows) ? count($frameRows) : 0;
            
            // 2. FORCE Size to match the Row Count for THIS specific frame
            // This ensures if rows = 2, size = 2; if rows = 1, size = 1.
            $frameSize = $actualRows;

            echo "FRAME CONFIGURATION:\n";
            // Matches: cmd_parametername
            echo "  Parameter Name    : " . ($this->request->getPost($prefix . '_parametername') ?: 'N/A') . "\n";
            
            // Matches: cmd_dataflow
            echo "  Data Flow         : " . ($this->request->getPost($prefix . '_dataflow') ?: 'N/A') . "\n";
            
            // Matches: cmd_frametype
            echo "  Frame Type        : " . ($this->request->getPost($prefix . '_frametype') ?: $label) . "\n";
            
            // Matches: cmd_framesizeinbytes
            echo "  Size (Bytes)      : " . $frameSize . " [Auto Calculated]\n";
            
            echo "  Total Rows        : " . $actualRows . " [Actual Count]\n";
            
            // Matches: cmd_frametrigger
            echo "  Frame Trigger     : " . ($this->request->getPost($prefix . '_frametrigger') ?: 'N/A') . "\n";
            
            // Matches: cmd_multipleui
            echo "  Multiple UI       : " . ($this->request->getPost($prefix . '_multipleui') ?: 'false') . "\n";
            
            // Matches: cmd_triggerui
            echo "  Trigger UI        : " . ($this->request->getPost($prefix . '_triggerui') ?: 'false') . "\n";
            //
            // Matches: cmd_ok_button
             echo "  OK Button         : " . ($this->request->getPost($prefix . '_ok_button') ?: 'N/A') . "\n";
            
            // Matches: cmd_voicecommand
            echo "  Voice Command     : " . ($this->request->getPost($prefix . '_voicecommand') ?: 'N/A') . "\n";
            
            // Matches: cmd_voice_instruction
            echo "  Voice Instruction : " . ($this->request->getPost($prefix . '_voice_instruction') ?: 'N/A') . "\n";
            
            // Matches: cmd_remarks
            echo "  Remarks           : " . ($this->request->getPost($prefix . '_remarks') ?: 'N/A') . "\n";
            echo "----------------------------------------------------\n";

            echo "[ROW DATA]:\n";
            // Cleaning up row output to remove messy arrays if bitwise is present
            $cleanedFrame = [];
            foreach ($frameRows as $rowKey => $row) {
                if (!empty($row['bitwiseData'])) {
                    // Only keep bitwise if it's a bitwise row
                    $cleanedFrame[$rowKey] = ['bitwiseData' => $row['bitwiseData']];
                } else {
                    // Remove empty bitwiseData from bytewise rows for a cleaner report
                    unset($row['bitwiseData']);
                    $cleanedFrame[$rowKey] = $row;
                }
            }
            print_r($cleanedFrame);
            echo "\n";
        }
    } else {
        echo "No frames configured for $label.\n";
    }
    echo "\n";
}




        echo "================ END OF EDIT REPORT ================";
        $final_report = ob_get_clean();

        // 5. Return JSON Response
        echo json_encode([
            'success' => true,
            'message' => 'Configuration updated successfully!',
            'print_r_output' => $final_report,
            'data' => $all_data // Optional: sending back the parsed data for the UI
        ]);
        exit;

    } catch (Exception $e) {
        if (ob_get_length()) ob_end_clean();
        echo json_encode([
            'success' => false, 
            'message' => 'Server Error: ' . $e->getMessage()
        ]);
        exit;
    }
}


*/  //New 10-03-26





/********************************************************************/

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
        $modelno   = $this->request->getGet('modelno');
        $modelname = $this->request->getGet('modelname');
        $category  = $this->request->getGet('category');

        $db      = \Config\Database::connect();
        $builder = $db->table('efactorproduct');

        // Sirf node_id select karenge
        $builder->select('node_id');
        $builder->where('model_name', $modelname);
        $query   = $builder->get();
        $nodeIds = array_column($query->getResultArray(), 'node_id'); // 🔑 direct node_id array ban gaya

        return view('items/sticker_stu', [
            'modelno'   => $modelno,
            'modelname' => $modelname,
            'category'  => $category,
            'id'        => $id,
            'nodeIds'   => $nodeIds, // dropdown ke liye bheja
        ]);
    }



/*

public function uploadStickerImage()
    {

        $file = $this->request->getFile('image');
        $userId = $this->request->getPost('user_id');

        if (!$file->isValid()) return $this->response->setJSON(['status' => 'error']);

        $folder = FCPATH . "files/sticker_studio_images/$userId/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $count = count(glob($folder . "img*")) + 1;
        $name = "img" . str_pad($count, 2, "0", STR_PAD_LEFT) . "." . $file->getExtension();

        $file->move($folder, $name);

        $url = base_url("files/sticker_studio_images/$userId/$name");

        $db = \Config\Database::connect();
        $db->table("sticker_images")->insert([
            'user_id' => $userId,
            'file_name' => $name,
            'url' => $url
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'image' => ['url' => $url]
        ]);
    }
*/


// public function sticker_print($id = null) {
//     session_start();
//     $user_id = $_SESSION['user_id'] ?? null;

//     if (!$user_id) return redirect()->to('/login');

//     $modelno = $this->request->getGet('modelno'); 
//     $modelname = $this->request->getGet('modelname'); 
//     $category = $this->request->getGet('category'); 

//     $device_types = $this->Items_model->extract_device_types($user_id, $modelno);

//     $table_name = "ef-002fmn02";  

//     return view('items/sticker_stu', [
//         'modelno' => $modelno,
//         'modelname' => $modelname,
//         'category' => $category,
//         'id' => $id,
//         'deviceTypes' => $device_types,
//         'tableName' => $table_name
//     ]);
// }



/*

public function sticker_print($id = null)
{


 session_start();
    $user_id = $_SESSION['user_id']; 
    
    
    $modelno   = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
    $category  = $this->request->getGet('category');

    $db      = \Config\Database::connect();
    $builder = $db->table('efactorproduct');

    // Sirf node_id select karenge
    $builder->select('node_id');
    $builder->where('model_name', $modelname);
    $query   = $builder->get();
    $nodeIds = array_column($query->getResultArray(), 'node_id'); // 🔑 direct node_id array ban gaya

    return view('items/sticker_stu', [
        'modelno'   => $modelno,
        'modelname' => $modelname,
        'category'  => $category,
        'id'        => $id,
        'nodeIds'   => $nodeIds, // dropdown ke liye bheja
    ]);
}

*/







/*
public function sticker_print($id = null)
{
    session_start();
    $user_id = $_SESSION['user_id']; 

    $db      = \Config\Database::connect();
    $builder = $db->table('efactorproduct');

    // EXACT match of:
    // SELECT node_id FROM efactorproduct WHERE model_name='EFDEFLT V1.0' AND user_id='544'
    $builder->select('node_id');
    $builder->where('model_name', 'EFDEFLT V1.0');
    $builder->where('user_id', $user_id);

    $query   = $builder->get();
    $nodeIds = array_column($query->getResultArray(), 'node_id');

    return view('items/sticker_stu', [
        'id'      => $id,
        'nodeIds' => $nodeIds,
    ]);
}




*/












public function getNodeId()
{
    $modelname = $this->request->getGet('modelname'); // URL se modelname aayega
    if (!$modelname) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Model name required',
            'node_ids' => []
        ]);
    }

    $db      = \Config\Database::connect();
    $builder = $db->table('efactorproduct');
    $builder->select('node_id');
    $builder->where('model_name', $modelname);
    $query   = $builder->get();
    $rows    = $query->getResultArray();

    // Sirf node_id ki list banao
    $nodeIds = array_column($rows, 'node_id');

    return $this->response->setJSON([
        'status'   => 'success',
        'node_ids' => $nodeIds
    ]);
}


// public function sticker_form($id = null)
// {
//     session_start();
//     $user_id = $_SESSION['user_id'] ?? null;

//    if (!$user_id) return redirect()->to('/login');

//     $modelno   = $this->request->getGet('modelno'); 
//     $modelname = $this->request->getGet('modelname'); 
//     $category  = $this->request->getGet('category'); 

//     return view('items/sticker_form', [
//         'id'        => $id,
//         'modelno'   => $modelno,
//         'modelname' => $modelname,
//         'category'  => $category
//     ]);
// }

public function sticker_edit()
{
    $data = [
        'stickerId' => $this->request->getGet('id') ?? null,
        'modelno'   => $this->request->getGet('modelno') ?? '',
        'modelname' => $this->request->getGet('modelname') ?? '',
        'category'  => $this->request->getGet('category') ?? ''
    ];

    return view('items/sticker_edit', $data);
}






// public function getNodeId() {
//     session_start();
//     $user_id = $_SESSION['user_id'] ?? null;
//     $model_name = $this->request->getGet('modelno');

//     if (!$user_id || !$model_name) {
//         return $this->response->setJSON(['error' => 'Missing parameters', 'node_ids' => []]);
//     }

//     $table_name = "EF-002FMN02";

//     $map = ['EFGATEW V1.0' => 'EF-002FMN02'];
//     $model_name_db = $map[$model_name] ?? $model_name;   $model_name;


//     // Use log_message instead of echo
//     log_message('debug', 'Model Name DB: ' . $model_name_db);
//     log_message('debug', 'User ID: ' . $user_id);
//     log_message('debug', 'Table Name: ' . $table_name);

//     $rows = $this->Items_model->get_nodeid_by_user_model($user_id, $model_name_db, $table_name);
//     $node_ids = array_column($rows, 'node_id');

//     return $this->response->setJSON([
//         'tableName' => $table_name,
//         'modelname' => $model_name,
//         'user_id' => $user_id,
//         'node_ids' => $node_ids
//     ]);
// }

/*
public function sticker_display($id = null)
{

session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    $modelno = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
    $category = $this->request->getGet('category');

    // Example: Size rules per model (ye DB se bhi fetch kar sakte ho)
    $sizeRules = [
        'test' => ['width' => 128, 'height' => 250],
        'Fan'  => ['width' => 128, 'height' => 296],
        'AC'   => ['width' => 400, 'height' => 300],
    ];

    // Default size
    $allowedWidth = 1500;
    $allowedHeight = 1000;

    if (isset($sizeRules[$modelname])) {
        $allowedWidth = $sizeRules[$modelname]['width'];
        $allowedHeight = $sizeRules[$modelname]['height'];
    }

//echo $user_id;

$DisplayIds = $this->Items_model->getDisplaynodeIds($user_id);


print_r($DisplayIds);


    return view('items/display_lable', [
        'modelno'       => $modelno,
        'modelname'     => $modelname,
        'category'      => $category,
        'id'            => $id,
        'allowedWidth'  => $allowedWidth,
        'allowedHeight' => $allowedHeight,
        'node_ids'   =>  $DisplayIds
    ]);






}*/



public function sticker_display($id = null)
{
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        return redirect()->to('/login');
    }

    $modelno   = $this->request->getGet('modelno');
    $modelname = $this->request->getGet('modelname');
    $category  = $this->request->getGet('category');

    // Size rules
    $sizeRules = [
        'test' => ['width' => 128, 'height' => 250],
        'Fan'  => ['width' => 128, 'height' => 296],
        'AC'   => ['width' => 400, 'height' => 300],
    ];

    $allowedWidth  = 1500;
    $allowedHeight = 1000;

    if (isset($sizeRules[$modelname])) {
        $allowedWidth  = $sizeRules[$modelname]['width'];
        $allowedHeight = $sizeRules[$modelname]['height'];
    }

    // 🔹 Get node IDs with status
    $displayNodes = $this->Items_model->getDisplaynodeIds($user_id);
    
   // print_r($displayNodes);
    
    



    return view('items/display_lable', [
        'modelno'       => $modelno,
        'modelname'     => $modelname,
        'category'      => $category,
        'id'            => $id,
        'allowedWidth'  => $allowedWidth,
        'allowedHeight' => $allowedHeight,
        'displayNodes'  => $displayNodes
    ]);
}














/*

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


*/



/***********************************************/

/*function mapDisplayResolution($width, $height)
{
    // Normalize orientation
    if ($width > $height) {
        $tmp = $width;
        $width = $height;
        $height = $tmp;
    }

    // 2.1 inch ≈ 250x500
    if ($width >= 220 && $width <= 280 && $height >= 450 && $height <= 550) {
        return [128, 250];
    }

    // 2.9 inch ≈ 250x592
    if ($width >= 220 && $width <= 280 && $height >= 560 && $height <= 630) {
        return [128, 296];
    }

    // 4.2 inch ≈ 600x800
    if ($width >= 580 && $width <= 650 && $height >= 750 && $height <= 850) {
        return [400, 300];
    }

    // Fallback
    return [(int)($width / 2), (int)($height / 2)];
}

*/

public function label_controller()
{
    header('Content-Type: application/json');

    $node_id = $_POST['node_id'] ?? '';
 //$width = $_POST['width'] ?? ''; 
//$height = $_POST['height'] ?? '';

   $width  = (int)($_POST['width'] ?? 0);
$height = (int)($_POST['height'] ?? 0);

// Normalize orientation
if ($width > $height) {
    [$width, $height] = [$height, $width];
}



 if ($width >= 220 && $width <= 280 && $height >= 450 && $height <= 550) {
       
     $width  = 128;
     $height = 250;
 
    }

    // 2.9 inch ≈ 250x592
    if ($width >= 220 && $width <= 280 && $height >= 560 && $height <= 630) {
       
       $width  = 128;
     $height = 296;

    }

    // 4.2 inch ≈ 600x800
  
    if ($width >= 580 && $width <= 650 && $height >= 750 && $height <= 850) {
       $width  = 400;
     $height = 300;

    }




    if (empty($_FILES['imageUpload']['name'])) {
        echo json_encode(['status'=>'error','msg'=>'No image uploaded']);
        exit;
    }

    $uploadDir = '/var/www/html/files/brand_logo/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $imageName = time().'_'.basename($_FILES['imageUpload']['name']);
    $filePath  = $uploadDir . $imageName;

    if (!move_uploaded_file($_FILES['imageUpload']['tmp_name'], $filePath)) {
        echo json_encode(['status'=>'error','msg'=>'File upload failed']);
        exit;
    }

    //clearstatcache();
    //usleep(300000); // filesystem sync

    $imageParam = urlencode($imageName);

  /* $ota_url = "https://efactor.tech/v8/paper_display_ota.php"
        . "?width={$width}&height={$height}"
        . "&node_id={$node_id}"
        . "&image={$imageParam}";

    $ota = @file_get_contents($ota_url);

    echo json_encode([
        'status' => 'success',
        'ota_url'=> $ota_url,
        'path'   => $filePath,
        'ota'    => $ota
    ]);
    exit;*/


set_time_limit(80);
ini_set('max_execution_time', 80);

$ota_url = "https://efactor.tech/v8/paper_display_ota.php"
    . "?width=" . urlencode($width)
    . "&height=" . urlencode($height)
    . "&node_id=" . urlencode($node_id)
    . "&image=" . urlencode($imageParam);

$context = stream_context_create([
    "http" => [
        "method"  => "GET",
        "timeout" => 70,
        "header"  => "User-Agent: PHP\r\n"
    ]
]);

$ota = file_get_contents($ota_url, false, $context); // remove @ for debug

// Capture response headers
$responseHeaders = $http_response_header ?? [];

echo json_encode([
    'status'   => $ota !== false ? 'success' : 'failed',
    'ota_url'  => $ota_url,
    'path'     => $filePath,
    'ota_body' => $ota,                // ✅ response from URL
    'headers'  => $responseHeaders,    // ✅ response headers
    'context'  => stream_context_get_options($context) // ✅ context options
], JSON_PRETTY_PRINT);

exit;


}



/**********************************************/

        function savecat() {
        $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);

        if (!$user_id) {
            return $this->response->setJSON(array("success" => false, "message" => "Session expired. Please login again."));
        }

        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return $this->response->setJSON(array("success" => false, "message" => "Product name is required."));
        }

        $targetDirectory = $this->getBrandLogoUploadDir();
        if (!is_dir($targetDirectory) && !@mkdir($targetDirectory, 0755, true)) {
            return $this->response->setJSON(array("success" => false, "message" => "Unable to create upload folder."));
        }

        $prodimg = '';
        if (isset($_FILES["prodimg"]) && $_FILES["prodimg"]["error"] === UPLOAD_ERR_OK) {
            $check = @getimagesize($_FILES["prodimg"]["tmp_name"]);
            if ($check !== false) {
                $fileExtension = strtolower(pathinfo($_FILES["prodimg"]["name"], PATHINFO_EXTENSION));
                $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                if (in_array($fileExtension, $allowed, true)) {
                    $newFileName = uniqid('prod_', true) . '.' . $fileExtension;
                    $targetFile = $targetDirectory . $newFileName;
                    if (@move_uploaded_file($_FILES["prodimg"]["tmp_name"], $targetFile)) {
                        $prodimg = $newFileName;
                    }
                }
            }
        }

        if ($prodimg === '') {
            $uploadError = isset($_FILES["prodimg"]["error"]) ? (int) $_FILES["prodimg"]["error"] : -1;
            $message = "Please upload a valid product image.";
            if ($uploadError === UPLOAD_ERR_NO_FILE) {
                $message = "Please select a product image before saving.";
            } elseif ($uploadError > 0 && $uploadError !== UPLOAD_ERR_OK) {
                $message = "Image upload failed. Please try another image.";
            }
            return $this->response->setJSON(array("success" => false, "message" => $message));
        }

        $result = $this->Items_model->save_cat($name, $prodimg, $user_id);

        if (is_string($result)) {
            return $this->response->setJSON(array("success" => false, "message" => $result));
        }

        $insertId = is_numeric($result) ? (int) $result : 0;
        if (!$insertId) {
            $products = $this->Items_model->get_cats($user_id)->getResult();
            foreach ($products as $item) {
                if ($item->name === $name) {
                    $insertId = (int) $item->id;
                    break;
                }
            }
        }

        $newProduct = $insertId ? $this->Items_model->get_cattwo($insertId)->getRow() : null;

        return $this->response->setJSON(array(
            "success" => true,
            "id" => $insertId,
            "data" => $newProduct ? $this->_make_item_row3($newProduct) : array($name, '', ''),
            "message" => "Product \"" . $name . "\" saved successfully!"
        ));
        }

    private function getBrandLogoUploadDir() {
        $candidates = array();

        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $candidates[] = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'brand_logo' . DIRECTORY_SEPARATOR;
        }

        $candidates[] = dirname(ROOTPATH) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'brand_logo' . DIRECTORY_SEPARATOR;
        $candidates[] = ROOTPATH . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'brand_logo' . DIRECTORY_SEPARATOR;

        foreach ($candidates as $dir) {
            $real = realpath($dir);
            if ($real && is_dir($real)) {
                return $real . DIRECTORY_SEPARATOR;
            }
        }

        $fallback = $candidates[0];
        if (!is_dir($fallback)) {
            @mkdir($fallback, 0755, true);
        }

        return $fallback;
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






// private function _make_item_row61($data) {


// $internetStatus = $data->internet;
//     switch ($internetStatus) {
//         case 'true':
//             $modeImage = '<img src="https://efactor.tech/files/home/green.png" style="width:20px;height:20px">';
//             $internet_data='online';
//             break;
//         case 'false':
//             $modeImage = '<img src="https://efactor.tech/files/home/red.png" style="width:20px;height:20px">';
//             $internet_data='offline';
//             break;
//         default:
//             $modeImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:20px;height:20px" alt="Default">';
//             $internet_data='';
//     }




// $deviceName =$data->device_type;

//     switch ($deviceName) {
//         case 'fan':
//             $deviceImage = '<img src="https://efactor.tech/files/home/fan.png" style="width:30px;height:30px">';
//             break;
//         case 'light':
//             $deviceImage = '<img src="https://efactor.tech/files/home/light.png" style="width:30px;height:30px">';
//             break;
//         case 'swpcp':
//             $deviceImage = '<img src="https://efactor.tech/files/home/swpcp.jpg" style="width:30px;height:30px">';
//             break;
//         case 'cooker':
//             $deviceImage = '<img src="https://efactor.tech/files/home/cooker.jpg" style="width:30px;height:30px">';
//             break;

//        case 'switch':
//             $deviceImage = '<img src="https://efactor.tech/files/home/switch.jpg" style="width:30px;height:30px">';
//             break;

//        case 'outlet':
//             $deviceImage = '<img src="https://efactor.tech/files/home/outlet.jpg" style="width:30px;height:30px">';
//             break;


        
//        case 'exhaust_hood':
//             $deviceImage = '<img src="https://efactor.tech/files/home/exhaust_hood.jpg" style="width:30px;height:30px">';
//             break;

//         case 'gateway':
//             $deviceImage = '<img src="https://efactor.tech/files/home/gateway.png" style="width:30px;height:30px">';
//             break;
            
//        case 'HBMDNC':
//             $deviceImage = '<img src="https://efactor.tech/files/home/HBMDNC.jpg" style="width:70px;height:35px">';
//             break;
            
//         default:
//             $deviceImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
//     }







// return array(
     
//             $modeImage,
//             $deviceImage,
//             $data->node_id,           
//             $data->device_type,
//             $data->model_name,
//             $data->internet,
//             $data->device_time,
//             $data->latitude,
//         $data->longitude,
//             $data->user_id,
//             $data->phone, 
//             $data->email,
            





//                      modal_anchor(get_uri("items/modal_apisinfo/" . $data->node_id. "?model_number=" . $data->model_name . "&user_id=" . $data->user_id), "APIs Info", array("class" => "edit", "title" => 'Api Info', "data-post-id" => $data->id))






          
    
  
 
// );

//    }


private $mappedCategoryImageMap = null;

private function resolveMappedFileUrl($filename, $folder = 'brand_logo') {
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $filename)) {
        return $filename;
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

    return $scheme . '://' . $host . '/files/' . trim($folder, '/') . '/' . ltrim($filename, '/');
}

private function getMappedCategoryImageMap($user_id) {
    if ($this->mappedCategoryImageMap !== null) {
        return $this->mappedCategoryImageMap;
    }

    $this->mappedCategoryImageMap = [];
    $rawImages = $this->Items_model->get_category_device_images($user_id);

    foreach ($rawImages as $key => $filename) {
        $this->mappedCategoryImageMap[$key] = $this->resolveMappedFileUrl($filename, 'brand_logo');
    }

    return $this->mappedCategoryImageMap;
}

private function buildMappedCategoryImageUrls($user_id) {
    $map = $this->getMappedCategoryImageMap($user_id);

    return [
        'images' => $map,
        'default' => $this->resolveMappedFileUrl('blank.jpeg', 'home'),
    ];
}

private function buildMappedDeviceImageHtml($device_type, $category_image_map = []) {
    $key = strtolower(trim((string) $device_type));
    $url = '';

    if ($key !== '' && !empty($category_image_map[$key])) {
        $url = $category_image_map[$key];
    } elseif ($key !== '') {
        $url = $this->resolveMappedFileUrl($key . '.png', 'home');
        if (!$url) {
            $url = $this->resolveMappedFileUrl($key . '.jpg', 'home');
        }
    }

    if ($url === '') {
        $url = $this->resolveMappedFileUrl('blank.jpeg', 'home');
    }

    $alt = htmlspecialchars($device_type ?: 'Device', ENT_QUOTES, 'UTF-8');

    return '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="width:30px;height:30px" alt="' . $alt . '">';
}

private function _make_item_row61($data, $category_image_map = [])
{
    $internetStatus = $data->internet;
    switch ($internetStatus) {
        case 'true':
            $modeImage = '<img src="' . $this->resolveMappedFileUrl('green.png', 'home') . '" style="width:20px;height:20px">';
            break;
        case 'false':
            $modeImage = '<img src="' . $this->resolveMappedFileUrl('red.png', 'home') . '" style="width:20px;height:20px">';
            break;
        default:
            $modeImage = '<img src="' . $this->resolveMappedFileUrl('blank.jpeg', 'home') . '" style="width:20px;height:20px" alt="Default">';
    }

    $deviceImage = $this->buildMappedDeviceImageHtml($data->device_type ?? '', $category_image_map);

    return array(
        $modeImage,
        $deviceImage,
        $data->node_id,
        $data->device_type,
        $data->model_name,
        $data->internet,
        $data->device_time,
        $data->latitude,
        $data->longitude,
        $data->user_id,
        $data->phone,
        $data->email,
        '',
        $data->id
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


private function _make_item_row6($data) {
return array(
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
    
            $data->payload,

           
            $data->direction,
            $data->Hue,
            $data->name,
            $data->power,
            $data->speed,
             $data->baudrate,
            $data->link_comm_mode,

            $data->timestamp,
            $data->output,



            $data->brightness,
            $data->rpm,
            $data->power_watt,
            $data->current,
            $data->bus_voltage,

            $data->ac_volt,
            $data->user_set_max,
            $data->device_lock,
            $data->device_set_max,

            $data->power_fact,


            $data->total_watt,

            $data->serialno,
           
            $data->led_indication,
           
            $data->TZ_properties,
            $data->BR_properties,

            $data->UMI_properties,

           $data->EC_properties,

            $data->CM_properties,
            $data->TU_properties,
            $data->LC_properties,
           
            $data->cook_time,

            $data->cookermode,
            $data->cook_run_temp,
            $data->cook_max_temp,
            $data->ping,

            $data->cooker_properties,
            $data->recipe,
            $data->pre_time_const,

            $data->keep_warm_timer,
            $data->dishes,
          
 
 
);

   }









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

          //  $result[] = $this->_make_item_row4($data);

   $row['brand_name'] = $data->name;

  if (!empty($data->brandlogo)) {
            // Construct relative or full URL
            $row['brandlogo_url'] = base_url("files/brand_logo/" . $data->brandlogo);
        } else {
            $row['brandlogo_url'] = ''; // or default image path like base_url('files/default.png')
        }


$row['options'] = modal_anchor(get_uri("items/modal_formbrand/" . $data->id), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => "Edit")) . 
                  js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => "Delete", "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/delete_brand"), "data-action" => "delete"));

        $result[] = $row;





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


/*
   //list_data1vendor
   function list_data1vendor() {

        $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);

        if (!$user_id) {
            return $this->response->setJSON(array("data" => array(), "error" => "Session user_id missing"));
        }

        $list_data = $this->Items_model->get_vendorproducts($user_id)->getResult();

        $result = array();
        foreach ($list_data as $data) {

            $result[] = $this->_make_item_row1_vendor($data);

        }

        return $this->response->setJSON(array("data" => $result));


   }*/  //pavan_form
   
   
   
   
   function get_vendorproducts($user_id) {
    $table = $this->db->prefixTable('vendorproducts');
    $fields = $this->db->getFieldNames('vendorproducts');
    $fieldMap = array();

    foreach ($fields as $field) {
        $fieldMap[strtolower($field)] = $field;
    }

    $pickField = function ($candidates) use ($fieldMap) {
        foreach ($candidates as $candidate) {
            $key = strtolower($candidate);
            if (isset($fieldMap[$key])) {
                return $fieldMap[$key];
            }
        }

        return null;
    };

    $selectField = function ($candidates, $alias, $default = '') use ($pickField) {
        $field = $pickField($candidates);

        if ($field) {
            return "`" . $field . "` AS `" . $alias . "`";
        }

        return "'" . addslashes($default) . "' AS `" . $alias . "`";
    };

    $idField = $pickField(array('id', 'vendorproduct_id', 'vendor_product_id'));
    $userField = $pickField(array('user_id', 'userid', 'userId', 'created_by'));
    $modelNameField = $pickField(array('modelname', 'model_name', 'modal_name', 'name'));
    $modelNoField = $pickField(array('modelno', 'model_no', 'model_number', 'modelname', 'model_name'));

    $select = array(
        $idField ? "`" . $idField . "` AS `id`" : "0 AS `id`",
        $selectField(array('modelname', 'model_name', 'modal_name', 'name'), 'model_name'),
        $selectField(array('brand', 'brand_name'), 'brand_name'),
        $selectField(array('category', 'category_name', 'product_name', 'product_type'), 'category_name'),
        $selectField(array('category', 'category_name', 'product_name', 'product_type'), 'product_name'),
        $selectField(array('modelno', 'model_no', 'model_number'), 'model_no'),
        $selectField(array('model_version', 'version'), 'version', 'N/A'),
        $selectField(array('Batch', 'batch'), 'batch', 'N/A')
    );

    $whereParts = array();

    if ($userField) {
        $whereParts[] = "`" . $userField . "` = " . $this->db->escape($user_id);
    }

    $defaultColumns = array_filter(array_unique(array($modelNameField, $modelNoField)));
    foreach ($defaultColumns as $defaultColumn) {
        $whereParts[] = "`" . $defaultColumn . "` = 'EFDEFLT V1.0'";
    }

    if (!$whereParts) {
        return $this->db->query("SELECT " . implode(", ", $select) . " FROM $table WHERE 1 = 0");
    }

    $sql = "SELECT " . implode(", ", $select) . " FROM $table WHERE " . implode(" OR ", $whereParts);

    return $this->db->query($sql);
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
          
             

        
 


modal_anchor(get_uri("items/modal_command/" . $data->model_name . "/" . $data->parameter_name. "/" . $data->product_name ), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))
    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deleteapi/" . $data->model_name . "/" . $data->parameter_name. "/" . $data->product_name), "data-action" => "delete"))



        );

    }



    /* prepare a row of item list table */

    private function _make_item_row3($data) {

log_message('debug', 'Product Image: ' . print_r($data->Product_image, true));

        return array(
         //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

           $data->name,
           $data->Product_image 
           ? '<img src="' . base_url('files/brand_logo/' . $data->Product_image) . '" alt="Product Image" style="max-width: 100px; max-height: 100px;">'
           : '<div style="width: 80px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">No Image</div>',
         
         modal_anchor(get_uri("items/modal_formcat_edit/".$data->id), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))   
          .js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletecat"), "data-action" => "delete"))
       );


        }



 private function _make_item_row4($data) {

    return array(
        $data->name,
        !empty($data->brandlogo) 
           ? '<img src="https://efactor.tech/files/brandLogo/' . $data->brandlogo . '" alt="Brand Logo" style="max-width: 100px; max-height: 100px;">'
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
        
        
        


public function product_description()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user_id = $_SESSION['user_id'] ?? null;

    $db = \Config\Database::connect();
    $builder = $db->table('product_description');

    if ($this->request->getMethod() === 'post') {
        try {
            $images = [];
            $files = $this->request->getFiles();

            // Custom folder path
            $uploadPath = 'C:/Program Files/Ampps/www/files/Add decription Image/';

            if (!empty($files['item_images'])) {
                foreach ($files['item_images'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move($uploadPath, $newName); // Save file in custom folder
                        $images[] = $newName;               // DB me sirf filename save
                    }
                }
            }

            $modal_name = $this->request->getPost('modelname') ?? '';
            $option_type = $this->request->getPost('option_type') ?? '';

            $data = [
                'modal_name'        => $modal_name,
                'model_no'          => $this->request->getPost('modelno') ?? '',
                'qr_selection'      => $option_type, // save selected option value
                'product_name'      => $this->request->getPost('product_name') ?? 'Default Product',
                'brand_name'        => $this->request->getPost('brand') ?? 'Default Brand',
                'images'            => !empty($images) ? json_encode($images) : json_encode(['no-image.jpg']),
                'description'       => $this->request->getPost('description') ?? '',
                'short_description' => $this->request->getPost('short_description') ?? '',
                'created_at'        => date('Y-m-d H:i:s')
            ];

            // Check if modal_name already exists
            $existing = $builder->where('modal_name', $modal_name)->get()->getRow();

            if($existing){
                // Update existing row
                $builder->where('modal_name', $modal_name)->update($data);
                $insertId = $existing->id;
            } else {
                // Insert new row
                $builder->insert($data);
                $insertId = $db->insertID();
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'id'      => $insertId,
                'message' => 'Product description saved successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Product Description Save Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'DB Insert failed. Check logs for details.'
            ]);
        }
    }

    // GET request → load form
    $modelno   = $this->request->getGet('modelno') ?? '';
    $modelname = $this->request->getGet('modelname') ?? '';
    $category  = $this->request->getGet('category') ?? '';
    $brand     = $this->request->getGet('brand') ?? '';

    return view('items/product_description', [
        'modelno'   => $modelno,
        'modelname' => $modelname,
        'category'  => $category,
        'brand'     => $brand,
        'user_id'   => $user_id
    ]);
}


/*

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
              <!--a class="dropdown-item" data-act="ajax-modal" data-title="Add API/Product" data-action-url="' . get_uri("items/modal_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API/Product</a-->
              
             <a class="dropdown-item" data-act="ajax-modal" data-title="Add API Frame Format" data-action-url="' . get_uri("items/api_frame_numbers/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API Frame Format</a>, 
            <a class="dropdown-item" data-act="ajax-modal" data-title="Add API/Product" data-action-url="' . get_uri("items/modal_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API/Product</a>,

  
            <a class="dropdown-item" data-act="ajax-modal" data-title="Add Description" data-action-url="' . get_uri("items/description_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add Description</a>,


                <a class="dropdown-item" data-act="ajax-modal" data-title="Add Page Names" data-action-url="' . get_uri("items/modal_pages/" . $data->id . "?modelno=" . $data->modelname) . '" href="#"><i class="fa fa-file-text"></i> Add Pages</a>
                <a class="dropdown-item" href="' . get_uri("items/api_page_linking/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-link"></i> Linking Page</a>
                <a class="dropdown-item" href="' . get_uri("items/specifications/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-cogs"></i> Specifications</a>
                <a class="dropdown-item" href="' . get_uri("items/auto_test/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-flask"></i> Auto Test</a>
                <a class="dropdown-item" href="' . get_uri("items/callDisplayFunction/" . $data->modelno . "?category=" . $data->category) . '"><i class="fa fa-desktop"></i> View UI Type</a>
                <a class="dropdown-item" href="' . get_uri("items/callDisplayId") . '"><i class="fa fa-cube"></i> Device Select</a>
                <a class="dropdown-item" href="' . get_uri("items/sticker_update/" . $data->id . "?modelno=" . $data->modelno . "&category=" . $data->category . "&modelname=" . urlencode($data->modelname)) . '"><i class="fa fa-sticky-note"></i> Sticker Update</a>
                <a class="dropdown-item" href="#" onclick="if(confirm(\'Do you want to create the table?\')) { window.location.href=\'' . get_uri("items/callConfirmDisplayFunction/" . $data->modelno) . '\'; } return false;"><i class="fa fa-check"></i> Confirm</a>

                <!-- ⭐ NEW WARRANTY CARD BUTTON -->
                <a class="dropdown-item" target="_blank" href="https://efactor.tech/warranty_form.html?modelname=' . urlencode($data->modelname) . '">
                    <i class="fa fa-shield"></i> Warranty Card
                </a>

                <a class="dropdown-item text-danger" href="#" onclick="if(confirm(\'Are you sure you want to delete this?\')) { window.location.href=\'' . get_uri("items/deletevendorproduct?id=" . $data->id) . '\'; } return false;"><i class="fa fa-trash"></i> Delete</a>
            </div>
        </div>'
    );
}


*/




/*
private function _make_item_row1_vendor($data) {
    return array(
        $data->modelname,
        $data->brand,
        $data->category,
        $data->modelno,
        $data->model_version,
        $data->Batch,

        '<div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenu' . $data->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu' . $data->id . '">

                <a class="dropdown-item" href="' . get_uri("items/viewapis/" . $data->id) . '"><i class="fa fa-eye"></i> View APIs</a>

                <a class="dropdown-item" data-act="ajax-modal" data-title="Add API Frame Format" data-action-url="' . get_uri("items/api_frame_numbers/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API Frame Format</a>

                <a class="dropdown-item" data-act="ajax-modal" data-title="Add API/Product" data-action-url="' . get_uri("items/modal_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add API/Product</a>

                <a class="dropdown-item" data-act="ajax-modal" data-title="Add Description" data-action-url="' . get_uri("items/description_api/" . $data->id) . '" href="#"><i class="fa fa-plus-circle"></i> Add Description</a>

                <a class="dropdown-item" data-act="ajax-modal" data-title="Add Page Names" data-action-url="' . get_uri("items/modal_pages/" . $data->id . "?modelno=" . $data->modelname) . '" href="#"><i class="fa fa-file-text"></i> Add Pages</a>

                <a class="dropdown-item" href="' . get_uri("items/api_page_linking/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-link"></i> Linking Page</a>

                <a class="dropdown-item" href="' . get_uri("items/specifications/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-cogs"></i> Specifications</a>

                <a class="dropdown-item" href="' . get_uri("items/auto_test/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-flask"></i> Auto Test Form</a>
                
                <a class="dropdown-item" href="' . get_uri("items/auto_test_start/" . $data->id . "?modelno=" . $data->modelno) . '"><i class="fa fa-flask"></i> Auto Test Start</a>

                <a class="dropdown-item" href="' . get_uri("items/callDisplayFunction/" . $data->modelno . "?category=" . $data->category) . '"><i class="fa fa-desktop"></i> View UI Type</a>

                <a class="dropdown-item" href="' . get_uri("items/callDisplayId") . '"><i class="fa fa-cube"></i> Device Select</a>

                <a class="dropdown-item" href="' . get_uri("items/sticker_update/" . $data->id . "?modelno=" . $data->modelno . "&category=" . $data->category . "&modelname=" . urlencode($data->modelname)) . '"><i class="fa fa-sticky-note"></i> Sticker Update</a>

                <a class="dropdown-item" href="#" onclick="if(confirm(\'Do you want to create the table?\')) { window.location.href=\'' . get_uri("items/callConfirmDisplayFunction/" . $data->modelno) . '\'; } return false;"><i class="fa fa-check"></i> Confirm</a>

                <a class="dropdown-item" target="_blank" href="https://efactor.tech/warranty_form.html?modelname=' . urlencode($data->modelname) . '">
                    <i class="fa fa-shield"></i> Warranty Card
                </a>

                <a class="dropdown-item text-danger" href="#" onclick="if(confirm(\'Are you sure you want to delete this?\')) { window.location.href=\'' . get_uri("items/deletevendorproduct?id=" . $data->id) . '\'; } return false;"><i class="fa fa-trash"></i> Delete</a>

            </div>
        </div>'
    );
}
*/



         
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

        //   echo "Model Name: $modelname<br>";
        //      $modelname = 'motorV1.0';
            $result[] = $this->_make_item_row2($data, $modelname);
        }
        echo json_encode(array("data" => $result));
    }
    
    
    
  /*  function list_data2($itemid = 0)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
   

    $list_data = $this->Items_model->getApiTreeConfigList($itemid, $user_id)->getResult();

    

    $result = array();

    foreach ($list_data as $data) {
        $result[] = $this->_make_api_tree_row($data);
    }

    echo json_encode(array("data" => $result));
} */ //01-06-26


/*
function list_data2($itemid = 0)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;

    // New table data
    $newData = $this->Items_model
                    ->getApiTreeConfigList($itemid, $user_id)
                    ->getResult();

    $result = array();

    if (!empty($newData)) {

        // Show new records
        foreach ($newData as $data) {
            $result[] = $this->_make_api_tree_row($data);
        }

    } else {

        // Fallback to old table
        $oldData = $this->Items_model
                        ->get_itemapis($itemid)
                        ->getResult();

        foreach ($oldData as $data) {
            $modelname = $data->model_name;
            $result[] = $this->_make_item_row2($data, $modelname);
        }
    }

    echo json_encode(array("data" => $result));
}

*/  //pavan_form





private function _make_api_tree_row($data) {
        $configId = (int) $data->id;
        $itemId = (int) $data->item_id;

        $action = '<button type="button" class="api-action-btn view-api-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">View API</button>';
        $update = '<button type="button" class="api-update-btn api-edit-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">Edit</button>'
            . '<button type="button" class="api-update-btn api-delete-btn" data-config-id="' . $configId . '">Delete</button>';

        $commandCount = $data->command_frame_count !== null ? $data->command_frame_count : $data->actual_command_frames;
        $responseCount = $data->response_frame_count !== null ? $data->response_frame_count : $data->actual_response_frames;
        $errorCount = $data->error_frame_count !== null ? $data->error_frame_count : $data->actual_error_frames;

        return array(
            $data->parameter_name ? $data->parameter_name : 'N/A',
            $action,
            $update
        );
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
$imageData = $this->buildMappedCategoryImageUrls($user_id);
$category_image_map = $imageData['images'];

    $result = array();
        foreach ($brand_data as $data) {
            $result[] = $this->_make_item_row61($data, $category_image_map);
        }

        echo json_encode(array(
            "data" => $result,
            "category_images" => $imageData['images'],
            "default_category_image" => $imageData['default']
        ));

}






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





$RSSI = $data->RSSI;

// If RSSI is null or empty → show NA
if ($RSSI === null || $RSSI === '' || $RSSI == 'null' || $RSSI == 'NULL') {
    $signalText = "<span style='color:black;'>NA (Not Applicable)</span>";
} else {

    $RSSI = (int)$RSSI;

    switch (true) {
        case ($RSSI > -70):
            $signalText = "<span style='color:green;'>Good ($RSSI dBm)</span>";
            break;

        case ($RSSI <= -70):
            $signalText = "<span style='color:red;'>Weak ($RSSI dBm)</span>";
            break;

        default:
            $signalText = "<span style='color:black;'>NA (Not Applicable)</span>";
    }
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
        $data->Baud_Rate,
        $signalText,
        $data->wifi_version,
        $data->power,
        $data->latitude,
        $data->longitude,
        $data->signin_latitude,
        $data->signin_longitude,
        




     /*   modal_anchor(get_uri("items/modal_productinfo/" . $data->node_id), "User & Product Info", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)), // Pass node_id
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

            ? anchor(get_uri("items/Fw_Update/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id), "Fw_Update", array("class" => "edit", "title" => 'Control',
               "style" => "background-color: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px;")) 
              : '<button class="btn btn-default" style="background-color: RED; color: white; padding: 10px; text-decoration: none; border-radius: 5px;" disabled>Fw_Update</button>'


             );*/


modal_anchor(
        get_uri("items/modal_productinfo/" . $data->node_id),
        "User & Product Info",
        array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)
    ),

    modal_anchor(
        get_uri("items/modal_apisinfo/" . $data->node_id . "?model_number=" . $data->model_number . "&user_id=" . $data->user_id),
        "APIs Info",
        array("class" => "edit", "title" => 'Api Info', "data-post-id" => $data->id)
    ),

    // ---------- CONTROL ----------
    ($data->user_id !== null && $data->user_id !== "" && $data->user_id !== "null")
        ? anchor(
            get_uri("items/controlDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id),
            "Control",
            array("class" => "edit", "title" => 'Control',
                "style" => "background-color: green; color: white; padding: 10px; border-radius: 5px;")
        )
        : '<button class="btn btn-default" style="background-color:red;color:white;padding:10px;border-radius:5px;" disabled>Control</button>',

    // ---------- CHAT ----------
    ($data->user_id !== null && $data->user_id !== "" && $data->user_id !== "null")
        ? anchor(
            get_uri("items/ChatDisplayFunction/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id),
            "Chat",
            array("class" => "edit", "title" => 'Chat',
                "style" => "background-color: green; color: white; padding: 10px; border-radius: 5px;")
        )
        : '<button class="btn btn-default" style="background-color:red;color:white;padding:10px;border-radius:5px;" disabled>Chat</button>',

    // ---------- FW UPDATE ----------
  /*  ($data->user_id !== null && $data->user_id !== "" && $data->user_id !== "null")
        ? anchor(
            get_uri("items/Fw_Update/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id),
            "Fw_Update",
            array("class" => "edit", "title" => 'Update',
                "style" => "background-color: green; color: white; padding: 10px; border-radius: 5px;",
                "target" => "_blank"   // ← THIS OPENS NEW TAB

               )
        )
        : '<button class="btn btn-default" style="background-color:red;color:white;padding:10px;border-radius:5px;" disabled>Fw_Update</button>'*/



($data->user_id !== null && $data->user_id !== "" && $data->user_id !== "null")
    ? anchor(
        get_uri("items/Fw_Update/" . $data->model_number . "?node_id=" . $data->node_id . "&userid=" . $data->user_id),
        "Fw_Update",
        array(
            "class" => "edit",
            "title" => "Update",
            "style" => "background-color: green; color: white; padding: 10px; border-radius: 5px;",
            "target" => "_blank"   // ← THIS OPENS NEW TAB
        )
    )
    : '<button class="btn btn-default" style="background-color:red;color:white;padding:10px;border-radius:5px;" disabled>Fw_Update</button>'





);








}   


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
     $userpic = '<img src="http://localhost/factor_17-05/files/home/NoUser.png" style="width:30px;height:30px" alt="Default">';
    }else{
     $userpic = '<img src="http://localhost/factor_17-05/files/home/person.png" style="width:30px;height:30px" alt="Default">';
    }
 
  


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
    
    
    
  /*************************************************************/  
    
    
    
    public function save_view_page_ui_theme()
{
    $modelno = trim((string) $this->request->getPost('modelno'));
    $themeJson = (string) $this->request->getPost('theme_json');
    $schemaJson = (string) $this->request->getPost('schema_json');

    if ($modelno === '') {
        return $this->response->setJSON(['success' => false, 'message' => 'Model number is required.']);
    }

    $theme = json_decode($themeJson, true);
    if (!is_array($theme)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid theme JSON.']);
    }

    $schema = json_decode($schemaJson, true);
    if (!is_array($schema) || empty($schema)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid schema JSON.']);
    }

    $schemaType = isset($schema['schemaType']) ? (string) $schema['schemaType'] : '';
    $requiredSchemaKeys = $schemaType === 'device-ui'
        ? ['schemaVersion', 'schemaType', 'meta', 'theme', 'icons', 'controls', 'pages', 'navigation', 'actions', 'state', 'settings', 'ui']
        : ($schemaType === 'flutter-widget-tree'
            ? ['schemaVersion', 'schemaType', 'app', 'theme', 'navigation', 'data', 'actions', 'widgetTree']
            : ['schemaVersion', 'app', 'theme', 'navigation', 'pages', 'components', 'modals', 'actions', 'dataSources']);

    foreach ($requiredSchemaKeys as $requiredSchemaKey) {
        if (!array_key_exists($requiredSchemaKey, $schema)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Schema is missing the required "' . $requiredSchemaKey . '" definition.',
            ]);
        }
    }

    if ($schemaType === 'device-ui') {
        if (!is_array($schema['ui']['widgetTree'] ?? null) || empty($schema['ui']['widgetTree']['type']) || empty($schema['ui']['widgetTree']['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Device UI schema must include a valid ui.widgetTree root.',
            ]);
        }

        if (!is_array($schema['navigation']) || empty($schema['navigation']['type'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Device UI schema must include a valid navigation definition.',
            ]);
        }
    } elseif ($schemaType === 'flutter-widget-tree') {
        if (!is_array($schema['widgetTree']) || empty($schema['widgetTree']['type']) || empty($schema['widgetTree']['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Flutter widget schema must include a valid widgetTree root.',
            ]);
        }

        if (!is_array($schema['navigation']) || empty($schema['navigation']['type'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Flutter widget schema must include a valid navigation definition.',
            ]);
        }
    }

    $themeSaved = $this->Items_model->saveDeviceUiTheme($modelno, $theme, $schema);
    $schemaSaved = $this->Items_model->saveDeviceUiSchema($modelno, $schema);

    if (!$themeSaved && !$schemaSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Unable to save the UI theme and schema.',
        ]);
    }

    if (!$themeSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Theme saved partially, but the theme record could not be updated.',
        ]);
    }

    if (!$schemaSaved) {
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Theme saved, but the device UI schema could not be updated.',
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'UI created successfully.',
        'api_url' => get_uri('api/device-ui-schema/' . rawurlencode($modelno)),
    ]);
}
    
    
    
    
  /************************************************************************/  
    
    
    
    
    
    
    
    
    


/*

public function getFields()
{
    $itemsModel = new \App\Models\Items_model();

    $fields = $itemsModel->findAll();

    // Debug: log
    log_message('info', 'getFields fetched: ' . json_encode($fields));

    return $this->response->setJSON([
        'status' => 'success',
        'fields' => $fields
    ]);
}

*/


 // new1
/*
public function getFields()
{
    $modelname = $this->request->getGet('modelname');

    $itemsModel = new \App\Models\Items_model();

    if ($modelname) {
        $fields = $itemsModel
            ->where('modelname', $modelname)
            ->findAll();
    } else {
        $fields = $itemsModel->findAll();
    }

    return $this->response->setJSON([
        'status' => 'success',
        'fields' => $fields
    ]);
}
*/

/*
public function getFields()
    {
        $modelname = $this->request->getGet('modelname');

        $itemsModel = new \App\Models\Items_model();

        if ($modelname) {
            $fields = $itemsModel
                ->where('modelname', $modelname)
                ->findAll();
        } else {
            $fields = $itemsModel->findAll();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'fields' => $fields
        ]);
    }

*/






    // ================= Sticker Form View =================
 /*   public function sticker_form($id = null)
    {
        $modelno   = $this->request->getGet('modelno') ?? '';
        $modelname = $this->request->getGet('modelname') ?? '';
        $category  = $this->request->getGet('category') ?? '';

        // Optional: basic validation
        if (!$modelname || !$modelno) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Missing model parameters');
        }

        return view('items/sticker_form', [
            'id'        => $id,
            'modelno'   => $modelno,
            'modelname' => $modelname,
            'category'  => $category
        ]);
    }
    */
    


/***************************************************************************/

public function qr_assign_nodeids()
{
    // Get JSON data
    $json = $this->request->getJSON(true);

    if (!$json) {
        return $this->response->setJSON([
            'status'  => false,
            'message' => 'No data received'
        ]);
    }

    $nodeIds = [];

    foreach ($json as $row) {
        if (!empty($row['node_id'])) {
            $nodeIds[] = $row['node_id'];
        }
    }

    if (empty($nodeIds)) {
        return $this->response->setJSON([
            'status'  => false,
            'message' => 'No valid node_ids found'
        ]);
    }

    // Update status using Items_model
    $updated = $this->Items_model->updateNodeIdsStatus($nodeIds, 'assigned');

    return $this->response->setJSON([
        'status'  => true,
        'message' => 'Node IDs updated successfully',
        'updated_rows' => $updated
    ]);
}



///<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< sticker form and sticker studio >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    /// for image uploading


    public function imageHandler()
    {
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

        $json = static function (array $data, int $code = 200) {
            return service('response')
                ->setStatusCode($code)
                ->setJSON($data);
        };

        $raw = json_decode(file_get_contents('php://input'), true);
        if (!is_array($raw)) {
            $raw = [];
        }

        $action = $this->request->getGet('action')
            ?? $this->request->getPost('action')
            ?? ($raw['action'] ?? null);

        $userId = $this->request->getGet('user_id')
            ?? $this->request->getPost('user_id')
            ?? ($raw['user_id'] ?? '');

        $userId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$userId);

        if ($userId === '') {
            return $json([
                'status' => 'error',
                'message' => 'User ID missing'
            ], 400);
        }

        $dir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sticker_studio_images' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        $urlBase = rtrim(base_url('files/sticker_studio_images/' . $userId), '/') . '/';

        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $maxSize = 5 * 1024 * 1024;

        if ($action === 'load_images') {
            $images = [];

            if (is_dir($dir)) {
                $files = glob($dir . '*') ?: [];
                natsort($files);

                foreach ($files as $filePath) {
                    if (!is_file($filePath)) {
                        continue;
                    }

                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed, true)) {
                        continue;
                    }

                    $images[] = $urlBase . rawurlencode(basename($filePath));
                }
            }

            return $this->response->setJSON($images);
        }

        if ($action === 'upload_images') {
            if (!isset($_FILES['images'])) {
                return $json([
                    'status' => 'error',
                    'message' => 'No files received'
                ], 400);
            }

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    return $json([
                        'status' => 'error',
                        'message' => 'Unable to create folder'
                    ], 500);
                }
            }

            if (!is_writable($dir)) {
                return $json([
                    'status' => 'error',
                    'message' => 'Folder not writable'
                ], 500);
            }

            $existing = glob($dir . 'img*') ?: [];
            $i = count($existing) + 1;
            $uploaded = [];

            $tmpNames = $_FILES['images']['tmp_name'] ?? [];
            $names = $_FILES['images']['name'] ?? [];
            $sizes = $_FILES['images']['size'] ?? [];
            $errors = $_FILES['images']['error'] ?? [];

            foreach ($tmpNames as $k => $tmp) {
                $error = $errors[$k] ?? UPLOAD_ERR_NO_FILE;
                $origName = $names[$k] ?? '';
                $size = (int)($sizes[$k] ?? 0);

                if ($error !== UPLOAD_ERR_OK) {
                    continue;
                }

                if (!is_uploaded_file($tmp)) {
                    continue;
                }

                if ($size <= 0 || $size > $maxSize) {
                    continue;
                }

                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }

                $newName = 'img' . str_pad((string)$i++, 3, '0', STR_PAD_LEFT) . '.' . $ext;
                $target = $dir . $newName;

                if (move_uploaded_file($tmp, $target)) {
                    $uploaded[] = $urlBase . rawurlencode($newName);
                }
            }

            if (empty($uploaded)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No valid uploads'
                ], 400);
            }

            return $json([
                'status' => 'ok',
                'images' => $uploaded
            ]);
        }

        if ($action === 'delete_images') {
            $images = $raw['images'] ?? null;

            if (!is_array($images) || empty($images)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No images selected'
                ], 400);
            }

            $deleted = 0;
            $realDir = is_dir($dir) ? realpath($dir) : false;

            foreach ($images as $imgUrl) {
                $fileName = basename((string)$imgUrl);
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

                if ($fileName === '') {
                    continue;
                }

                $filePath = $dir . $fileName;
                $realFile = file_exists($filePath) ? realpath($filePath) : false;

                if ($realDir && $realFile && str_starts_with($realFile, $realDir)) {
                    if (is_file($realFile) && @unlink($realFile)) {
                        $deleted++;
                    }
                }
            }

            return $json([
                'status' => 'ok',
                'deleted' => $deleted
            ]);
        }

        return $json([
            'status' => 'error',
            'message' => 'Invalid action'
        ], 400);
    }
    
    
    

  public function saveFields()
    {
        try {

            $request = service('request');
            $data = $request->getJSON(true);

            if (!$data || empty($data['modelname'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid modelname'
                ]);
            }

            $db = \Config\Database::connect();
            $builder = $db->table('sticker_fields');

            $modelname = trim((string) ($data['modelname'] ?? ''));

            $userId = null;
            if (isset($data['user_id']) && is_numeric($data['user_id'])) {
                $userId = (int) $data['user_id'];
            }

            $stickerId = null;
            if (isset($data['sticker_id']) && is_numeric($data['sticker_id'])) {
                $stickerId = (int) $data['sticker_id'];
            } elseif (isset($data['id']) && is_numeric($data['id'])) {
                $stickerId = (int) $data['id'];
            }

            $version = trim((string) ($data['version'] ?? ''));

            $batchId = null;
            if (isset($data['batch_id']) && is_numeric($data['batch_id'])) {
                $batchId = (int) $data['batch_id'];
            }

            $isBatchScope = ($version !== '' || ($batchId !== null && $batchId > 0));

            // ── ATTEMPT 1: full scope lookup ─────────────────────────────────
            $existingBuilder = $db->table('sticker_fields')
                ->where('modelname', $modelname);

            if ($userId !== null && $userId > 0) {
                $existingBuilder->where('user_id', $userId);
            }
            if ($stickerId !== null && $stickerId > 0) {
                $existingBuilder->where('sticker_id', $stickerId);
            }
            if ($isBatchScope) {
                if ($version !== '') {
                    $existingBuilder->where('version', $version);
                }
                if ($batchId !== null && $batchId > 0) {
                    $existingBuilder->where('batch_id', $batchId);
                }
            } else {
                $existingBuilder
                    ->groupStart()
                    ->where('version', null)
                    ->orWhere('version', '')
                    ->groupEnd()
                    ->where('batch_id', null);
            }

            $existing = $existingBuilder->orderBy('id', 'DESC')->get()->getRowArray();

            // ── ATTEMPT 2: fallback for legacy type-A (sticker_id = NULL in DB)
            // Prevents a duplicate INSERT when the row exists but wasn't found
            // because its sticker_id is NULL and we filtered WHERE sticker_id=X.
            if (empty($existing) && $stickerId !== null && $stickerId > 0 && !$isBatchScope) {
                $existingBuilder2 = $db->table('sticker_fields')
                    ->where('modelname', $modelname);

                if ($userId !== null && $userId > 0) {
                    $existingBuilder2->where('user_id', $userId);
                }
                // ← sticker_id omitted
                $existingBuilder2
                    ->groupStart()
                    ->where('version', null)
                    ->orWhere('version', '')
                    ->groupEnd()
                    ->where('batch_id', null);

                $existing = $existingBuilder2->orderBy('id', 'DESC')->get()->getRowArray();
            }

            $scopeData = array(
                'modelname' => $modelname,
                'version'   => $isBatchScope && $version !== '' ? $version : null,
                'batch_id'  => $isBatchScope && $batchId !== null && $batchId > 0 ? $batchId : null
            );

            if ($userId !== null && $userId > 0) {
                $scopeData['user_id'] = $userId;
            }
            if ($stickerId !== null && $stickerId > 0) {
                $scopeData['sticker_id'] = $stickerId;
            }

            /* LAYOUT SAVE */
            if (!empty($data['full_snapshot'])) {
                $layoutData = [
                    'full_snapshot' => $data['full_snapshot'],
                    'user_id'       => $data['user_id'] ?? null
                ];
                if ($existing) {
                    $builder->where('id', $existing['id'])->update($layoutData);
                } else {
                    $builder->insert(array_merge($scopeData, $layoutData));
                }
                return $this->response->setJSON(['status' => 'success', 'type' => 'layout']);
            }

            /* PRINT SNAPSHOT SAVE */
            if (!empty($data['print_snapshot'])) {
                $printData = [
                    'print_snapshot' => $data['print_snapshot'],
                    'user_id'        => $data['user_id'] ?? null
                ];
                if ($existing) {
                    $builder->where('id', $existing['id'])->update($printData);
                } else {
                    $builder->insert(array_merge($scopeData, $printData));
                }
                return $this->response->setJSON(['status' => 'success', 'type' => 'print']);
            }

            /* FORM SAVE */
            $formData = $data['form_data'] ?? null;

            if (!is_array($formData)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid form_data']);
            }

            $fields = $formData['fields'] ?? null;

            if (!is_array($fields)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid form fields']);
            }

            $allowedInputTypes   = ['manual', 'qr', 'datetime', 'barcode'];
            $allowedKeywordTypes = ['constant', 'variable'];
            $cleanFields = [];
            $usedNames   = [];

            foreach ($fields as $field) {
                if (!is_array($field)) continue;

                $name = trim((string)($field['name'] ?? ''));
                if ($name === '') continue;

                $value            = trim((string)($field['value']              ?? ''));
                $counting         = trim((string)($field['counting']           ?? ''));
                $inputType        = trim((string)($field['input_type']         ?? 'manual'));
                $keywordType      = trim((string)($field['keyword_type']       ?? 'variable'));
                $keywordValueType = trim((string)($field['keyword_value_type'] ?? 'variable'));

                if (!in_array($inputType,        $allowedInputTypes,   true)) $inputType = 'manual';
                if (!in_array($keywordType,      $allowedKeywordTypes, true)) $keywordType = 'variable';
                if (!in_array($keywordValueType, $allowedKeywordTypes, true)) $keywordValueType = 'variable';

                $dedupeKey = mb_strtolower($name);
                if ($name !== 'Title') {
                    if (isset($usedNames[$dedupeKey])) {
                        return $this->response->setJSON(['status' => 'error', 'message' => 'Duplicate field name: ' . $name]);
                    }
                    $usedNames[$dedupeKey] = true;
                }

                $cleanFields[] = [
                    'name'               => $name,
                    'value'              => $value,
                    'counting'           => $counting,
                    'input_type'         => $inputType,
                    'keyword_type'       => $keywordType,
                    'keyword_value_type' => $keywordValueType,
                ];
            }

            if (empty($cleanFields)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'At least one field is required']);
            }

            $jsonData = json_encode([
                'version'    => 1,
                'updated_at' => date('c'),
                'fields'     => $cleanFields
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($jsonData === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unable to encode form data']);
            }

            $saveData = [
                'form_data' => $jsonData,
                'user_id'   => $data['user_id'] ?? null,
            ];

            if ($existing) {
                $builder->where('id', $existing['id'])->update($saveData);
            } else {
                $builder->insert(array_merge($scopeData, $saveData));
            }

            return $this->response->setJSON(['status' => 'success', 'type' => 'form']);
        } catch (\Throwable $e) {
            log_message('error', 'SAVE ERROR: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    
    
    
    
    
    
    // new1

    // public function getFields()
    // {
    //     $modelname = $this->request->getGet('modelname');

    //     $itemsModel = new \App\Models\Items_model();

    //     if ($modelname) {
    //         $fields = $itemsModel
    //             ->where('modelname', $modelname)
    //             ->findAll();
    //     } else {
    //         $fields = $itemsModel->findAll();
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'fields' => $fields
    //     ]);
    // }
    
    
    
    
    
    
    


 /*   public function getFields()
    {
        try {
            $modelname = trim((string)$this->request->getGet('modelname'));
            $userId    = $this->request->getGet('user_id');

            $userId = is_numeric($userId) ? (int)$userId : null;

            $db = \Config\Database::connect();

            $builder = $db->table('sticker_fields')
                ->select(
                    'id,
                 sticker_id,
                 modelname,
                 user_id,
                 form_data,
                 full_snapshot,
                 print_snapshot,
                 created_at'
                )
                ->orderBy('id', 'DESC');

            if ($modelname !== '') {
                $builder->where('modelname', $modelname);
            }

            if ($userId !== null && $userId > 0) {
                $builder->where('user_id', $userId);
            }

            $fields = $builder->get()->getResultArray();

            foreach ($fields as &$row) {
                $decodedFormData = null;

                if (!empty($row['form_data'])) {
                    if (is_array($row['form_data'])) {
                        $decodedFormData = $row['form_data'];
                    } else {
                        $decoded = json_decode((string)$row['form_data'], true);
                        $decodedFormData = is_array($decoded) ? $decoded : null;
                    }
                }

                $row['form_data'] = $decodedFormData;
            }

            unset($row);

            return $this->response->setJSON([
                'status' => 'success',
                'fields' => $fields
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'GET FIELDS ERROR: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Unable to load sticker fields.',
                    'fields'  => []
                ]);
        }
    }
    
   */
   
   
   
   
     public function getFields()
    {
        try {
            $modelname = trim((string)$this->request->getGet('modelname'));
            $userId    = $this->request->getGet('user_id');

            $userId = is_numeric($userId) ? (int)$userId : null;

            $stickerId = $this->request->getGet('sticker_id') ?? $this->request->getGet('id');
            $version   = trim((string) $this->request->getGet('version'));
            $batchId   = $this->request->getGet('batch_id');

            $stickerId = is_numeric($stickerId) ? (int) $stickerId : null;
            $batchId   = is_numeric($batchId)   ? (int) $batchId   : null;

            $isBatchScope = ($version !== '' || ($batchId !== null && $batchId > 0));

            $db = \Config\Database::connect();

            // Helper: build the base query builder with SELECT + ORDER
            $makeBuilder = function () use ($db) {
                return $db->table('sticker_fields')
                    ->select(
                        'id,
                         sticker_id,
                         modelname,
                         version,
                         batch_id,
                         user_id,
                         form_data,
                         full_snapshot,
                         print_snapshot,
                         created_at'
                    )
                    ->orderBy('id', 'DESC');
            };

            // Helper: JSON-decode form_data in every row
            $decodeRows = function (array $rows) {
                foreach ($rows as &$row) {
                    $decodedFormData = null;
                    if (!empty($row['form_data'])) {
                        if (is_array($row['form_data'])) {
                            $decodedFormData = $row['form_data'];
                        } else {
                            $decoded = json_decode((string)$row['form_data'], true);
                            $decodedFormData = is_array($decoded) ? $decoded : null;
                        }
                    }
                    $row['form_data'] = $decodedFormData;
                }
                unset($row);
                return $rows;
            };

            // ── ATTEMPT 1: full scope query ──────────────────────────────────
            $builder = $makeBuilder();

            if ($modelname !== '') {
                $builder->where('modelname', $modelname);
            }
            if ($userId !== null && $userId > 0) {
                $builder->where('user_id', $userId);
            }
            if ($stickerId !== null && $stickerId > 0) {
                $builder->where('sticker_id', $stickerId);
            }
            if ($isBatchScope) {
                if ($version !== '') {
                    $builder->where('version', $version);
                }
                if ($batchId !== null && $batchId > 0) {
                    $builder->where('batch_id', $batchId);
                }
            } else {
                $builder
                    ->groupStart()
                    ->where('version', null)
                    ->orWhere('version', '')
                    ->groupEnd()
                    ->where('batch_id', null);
            }

            $fields = $builder->get()->getResultArray();

            // ── ATTEMPT 2: fallback — drop sticker_id filter ─────────────────
            // For old (type A) records saved before sticker_id tracking existed.
            // Those rows have sticker_id = NULL, so attempt 1 finds nothing.
            // Only triggered when: sticker_id was in the URL AND attempt 1 returned 0 rows.
            if (empty($fields) && $stickerId !== null && $stickerId > 0) {

                $builder2 = $makeBuilder();

                if ($modelname !== '') {
                    $builder2->where('modelname', $modelname);
                }
                if ($userId !== null && $userId > 0) {
                    $builder2->where('user_id', $userId);
                }
                // ← sticker_id intentionally omitted
                if ($isBatchScope) {
                    if ($version !== '') {
                        $builder2->where('version', $version);
                    }
                    if ($batchId !== null && $batchId > 0) {
                        $builder2->where('batch_id', $batchId);
                    }
                } else {
                    $builder2
                        ->groupStart()
                        ->where('version', null)
                        ->orWhere('version', '')
                        ->groupEnd()
                        ->where('batch_id', null);
                }

                $fields = $builder2->get()->getResultArray();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'fields' => $decodeRows($fields)
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'GET FIELDS ERROR: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Unable to load sticker fields.',
                    'fields'  => []
                ]);
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // ================= Sticker Form View =================
       public function sticker_form($id = null)
    {
        $modelno   = $this->request->getGet('modelno')   ?? '';
        $modelname = $this->request->getGet('modelname') ?? '';
        $category  = $this->request->getGet('category')  ?? '';

        $version = $this->request->getGet('version')  ?? '';
        $batchId = $this->request->getGet('batch_id') ?? '';
        $batchNo = $this->request->getGet('batch_no') ?? '';

        if (!$modelname || !$modelno) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Missing model parameters');
        }

        return view('items/sticker_form', [
            'id'        => $id,
            'modelno'   => $modelno,
            'modelname' => $modelname,
            'category'  => $category,
            'version'   => $version,
            'batch_id'  => $batchId,
            'batch_no'  => $batchNo
        ]);
    }
    // ================= View Sticker Form =================
      public function viewStickerForm($modelname)
    {
        $itemsModel = new \App\Models\Items_model();
        $form = $itemsModel->where('modelname', $modelname)->first();
        return view('items/view_sticker_form', ['form' => $form]);
    }


    public function sticker_stu_UserManual()
    {
        return view('items/sticker_stu_UserManual');
    }










/*
public function imageHandler()
    {
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

        $json = static function (array $data, int $code = 200) {
            return service('response')
                ->setStatusCode($code)
                ->setJSON($data);
        };

        $raw = json_decode(file_get_contents('php://input'), true);
        if (!is_array($raw)) {
            $raw = [];
        }

        $action = $this->request->getGet('action')
            ?? $this->request->getPost('action')
            ?? ($raw['action'] ?? null);

        $userId = $this->request->getGet('user_id')
            ?? $this->request->getPost('user_id')
            ?? ($raw['user_id'] ?? '');

        $userId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$userId);

        if ($userId === '') {
            return $json([
                'status' => 'error',
                'message' => 'User ID missing'
            ], 400);
        }

        $dir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sticker_studio_images' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        $urlBase = rtrim(base_url('files/sticker_studio_images/' . $userId), '/') . '/';

        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $maxSize = 5 * 1024 * 1024;

        if ($action === 'load_images') {
            $images = [];

            if (is_dir($dir)) {
                $files = glob($dir . '*') ?: [];
                natsort($files);

                foreach ($files as $filePath) {
                    if (!is_file($filePath)) {
                        continue;
                    }

                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed, true)) {
                        continue;
                    }

                    $images[] = $urlBase . rawurlencode(basename($filePath));
                }
            }

            return $this->response->setJSON($images);
        }

        if ($action === 'upload_images') {
            if (!isset($_FILES['images'])) {
                return $json([
                    'status' => 'error',
                    'message' => 'No files received'
                ], 400);
            }

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    return $json([
                        'status' => 'error',
                        'message' => 'Unable to create folder'
                    ], 500);
                }
            }

            if (!is_writable($dir)) {
                return $json([
                    'status' => 'error',
                    'message' => 'Folder not writable'
                ], 500);
            }

            $existing = glob($dir . 'img*') ?: [];
            $i = count($existing) + 1;
            $uploaded = [];

            $tmpNames = $_FILES['images']['tmp_name'] ?? [];
            $names = $_FILES['images']['name'] ?? [];
            $sizes = $_FILES['images']['size'] ?? [];
            $errors = $_FILES['images']['error'] ?? [];

            foreach ($tmpNames as $k => $tmp) {
                $error = $errors[$k] ?? UPLOAD_ERR_NO_FILE;
                $origName = $names[$k] ?? '';
                $size = (int)($sizes[$k] ?? 0);

                if ($error !== UPLOAD_ERR_OK) {
                    continue;
                }

                if (!is_uploaded_file($tmp)) {
                    continue;
                }

                if ($size <= 0 || $size > $maxSize) {
                    continue;
                }

                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }

                $newName = 'img' . str_pad((string)$i++, 3, '0', STR_PAD_LEFT) . '.' . $ext;
                $target = $dir . $newName;

                if (move_uploaded_file($tmp, $target)) {
                    $uploaded[] = $urlBase . rawurlencode($newName);
                }
            }

            if (empty($uploaded)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No valid uploads'
                ], 400);
            }

            return $json([
                'status' => 'ok',
                'images' => $uploaded
            ]);
        }

        if ($action === 'delete_images') {
            $images = $raw['images'] ?? null;

            if (!is_array($images) || empty($images)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No images selected'
                ], 400);
            }

            $deleted = 0;
            $realDir = is_dir($dir) ? realpath($dir) : false;

            foreach ($images as $imgUrl) {
                $fileName = basename((string)$imgUrl);
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

                if ($fileName === '') {
                    continue;
                }

                $filePath = $dir . $fileName;
                $realFile = file_exists($filePath) ? realpath($filePath) : false;

                if ($realDir && $realFile && str_starts_with($realFile, $realDir)) {
                    if (is_file($realFile) && @unlink($realFile)) {
                        $deleted++;
                    }
                }
            }

            return $json([
                'status' => 'ok',
                'deleted' => $deleted
            ]);
        }

        return $json([
            'status' => 'error',
            'message' => 'Invalid action'
        ], 400);
    }


*/









/*

public function imageHandler()
{
    try {
        // Ensure services are available
        if (!isset($this->request)) {
            $this->request = \Config\Services::request();
        }
        
        if (!isset($this->response)) {
            $this->response = \Config\Services::response();
        }
        
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

        $json = static function (array $data, int $code = 200) {
            return service('response')
                ->setStatusCode($code)
                ->setJSON($data);
        };

        // Handle empty input
        $rawInput = file_get_contents('php://input');
        $raw = [];
        if ($rawInput !== false) {
            $raw = json_decode($rawInput, true);
            if (!is_array($raw)) {
                $raw = [];
            }
        }

        // Get action from multiple sources
        $action = $this->request->getGet('action') 
            ?? $this->request->getPost('action') 
            ?? ($raw['action'] ?? null);
        
        $userId = $this->request->getGet('user_id')
            ?? $this->request->getPost('user_id')
            ?? ($raw['user_id'] ?? '');

        // Sanitize user ID
        $userId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$userId);

        if ($userId === '') {
            return $json([
                'status' => 'error',
                'message' => 'User ID missing or invalid'
            ], 400);
        }

        // Create directory path safely
        $basePath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sticker_studio_images';
        $dir = $basePath . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        $urlBase = rtrim(base_url('files/sticker_studio_images/' . $userId), '/') . '/';

        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $maxSize = 5 * 1024 * 1024;

        // Handle load_images action
        if ($action === 'load_images') {
            $images = [];

            if (is_dir($dir)) {
                $files = glob($dir . '*') ?: [];
                natsort($files);

                foreach ($files as $filePath) {
                    if (!is_file($filePath)) {
                        continue;
                    }

                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed, true)) {
                        continue;
                    }

                    $images[] = $urlBase . rawurlencode(basename($filePath));
                }
            }

            return $this->response->setJSON($images);
        }

        // Handle upload_images action
        if ($action === 'upload_images') {
            // Check if files were uploaded
            if (empty($_FILES) || !isset($_FILES['images'])) {
                return $json([
                    'status' => 'error',
                    'message' => 'No files received'
                ], 400);
            }

            // Create directory if it doesn't exist
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return $json([
                        'status' => 'error',
                        'message' => 'Unable to create upload directory. Check permissions.'
                    ], 500);
                }
            }

            // Check directory permissions
            if (!is_writable($dir)) {
                return $json([
                    'status' => 'error',
                    'message' => 'Upload directory is not writable'
                ], 500);
            }

            // Get existing files count
            $existing = glob($dir . 'img*') ?: [];
            $i = count($existing) + 1;
            $uploaded = [];

            // Handle both single and multiple file uploads
            $files = $this->normalizeFilesArray($_FILES['images']);
            
            foreach ($files as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }

                if (!is_uploaded_file($file['tmp_name'])) {
                    continue;
                }

                if ($file['size'] <= 0 || $file['size'] > $maxSize) {
                    continue;
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }

                $newName = 'img' . str_pad((string)$i++, 3, '0', STR_PAD_LEFT) . '.' . $ext;
                $target = $dir . $newName;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $uploaded[] = $urlBase . rawurlencode($newName);
                }
            }

            if (empty($uploaded)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No valid files were uploaded. Check file types and sizes.'
                ], 400);
            }

            return $json([
                'status' => 'ok',
                'images' => $uploaded
            ]);
        }

        // Handle delete_images action
        if ($action === 'delete_images') {
            $images = $raw['images'] ?? null;

            if (!is_array($images) || empty($images)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No images selected for deletion'
                ], 400);
            }

            $deleted = 0;
            $realDir = is_dir($dir) ? realpath($dir) : false;
            
            if (!$realDir) {
                return $json([
                    'status' => 'error',
                    'message' => 'Directory not found'
                ], 404);
            }

            foreach ($images as $imgUrl) {
                $fileName = basename((string)$imgUrl);
                // More permissive but safe pattern for filenames
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

                if ($fileName === '') {
                    continue;
                }

                $filePath = $dir . $fileName;
                $realFile = file_exists($filePath) ? realpath($filePath) : false;

                // Security check: ensure file is within the target directory
                if ($realDir && $realFile && strpos($realFile, $realDir) === 0) {
                    if (is_file($realFile) && @unlink($realFile)) {
                        $deleted++;
                    }
                }
            }

            return $json([
                'status' => 'ok',
                'deleted' => $deleted,
                'message' => "Successfully deleted $deleted file(s)"
            ]);
        }

        return $json([
            'status' => 'error',
            'message' => 'Invalid action. Allowed actions: load_images, upload_images, delete_images'
        ], 400);
        
    } catch (\Exception $e) {
        // Log the error
        log_message('error', 'Image handler error: ' . $e->getMessage());
        
        return $json([
            'status' => 'error',
            'message' => 'An internal error occurred: ' . $e->getMessage()
        ], 500);
    }
}









private function normalizeFilesArray($files)
{
    $normalized = [];
    
    if (is_array($files['name'])) {
        // Multiple files
        foreach ($files['name'] as $key => $name) {
            $normalized[] = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            ];
        }
    } else {
        // Single file
        $normalized[] = $files;
    }
    
    return $normalized;
}


*/


/*

public function imageHandler()
{
    try {
        // Ensure services are available
        if (!isset($this->request)) {
            $this->request = \Config\Services::request();
        }
        
        if (!isset($this->response)) {
            $this->response = \Config\Services::response();
        }
        
        $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

        $json = static function (array $data, int $code = 200) {
            return service('response')
                ->setStatusCode($code)
                ->setJSON($data);
        };

        // Handle empty input
        $rawInput = file_get_contents('php://input');
        $raw = [];
        if ($rawInput !== false) {
            $raw = json_decode($rawInput, true);
            if (!is_array($raw)) {
                $raw = [];
            }
        }

        // Get action from multiple sources
        $action = $this->request->getGet('action') 
            ?? $this->request->getPost('action') 
            ?? ($raw['action'] ?? null);
        
        $userId = $this->request->getGet('user_id')
            ?? $this->request->getPost('user_id')
            ?? ($raw['user_id'] ?? '');

        // Sanitize user ID
        $userId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$userId);

        if ($userId === '') {
            return $json([
                'status' => 'error',
                'message' => 'User ID missing or invalid'
            ], 400);
        }

        // Create directory path safely
        $basePath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sticker_studio_images';
        $dir = $basePath . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;
        $urlBase = rtrim(base_url('files/sticker_studio_images/' . $userId), '/') . '/';

        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $maxSize = 5 * 1024 * 1024;

        // Handle load_images action
        if ($action === 'load_images') {
            $images = [];

            if (is_dir($dir)) {
                $files = glob($dir . '*') ?: [];
                natsort($files);

                foreach ($files as $filePath) {
                    if (!is_file($filePath)) {
                        continue;
                    }

                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed, true)) {
                        continue;
                    }

                    $images[] = $urlBase . rawurlencode(basename($filePath));
                }
            }

            return $this->response->setJSON($images);
        }

        // Handle upload_images action
        if ($action === 'upload_images') {
            // Check if files were uploaded
            if (empty($_FILES) || !isset($_FILES['images'])) {
                return $json([
                    'status' => 'error',
                    'message' => 'No files received'
                ], 400);
            }

            // Create directory if it doesn't exist
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return $json([
                        'status' => 'error',
                        'message' => 'Unable to create upload directory. Check permissions.'
                    ], 500);
                }
            }

       
            // Get existing files count
            $existing = glob($dir . 'img*') ?: [];
            $i = count($existing) + 1;
            $uploaded = [];

            // Handle both single and multiple file uploads
            $files = $this->normalizeFilesArray($_FILES['images']);
            
            foreach ($files as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }

                if (!is_uploaded_file($file['tmp_name'])) {
                    continue;
                }

                if ($file['size'] <= 0 || $file['size'] > $maxSize) {
                    continue;
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }

                $newName = 'img' . str_pad((string)$i++, 3, '0', STR_PAD_LEFT) . '.' . $ext;
                $target = $dir . $newName;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $uploaded[] = $urlBase . rawurlencode($newName);
                }
            }

            if (empty($uploaded)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No valid files were uploaded. Check file types and sizes.'
                ], 400);
            }

            return $json([
                'status' => 'ok',
                'images' => $uploaded
            ]);
        }

        // Handle delete_images action
        if ($action === 'delete_images') {
            $images = $raw['images'] ?? null;

            if (!is_array($images) || empty($images)) {
                return $json([
                    'status' => 'error',
                    'message' => 'No images selected for deletion'
                ], 400);
            }

            $deleted = 0;
            $realDir = is_dir($dir) ? realpath($dir) : false;
            
            if (!$realDir) {
                return $json([
                    'status' => 'error',
                    'message' => 'Directory not found'
                ], 404);
            }

            foreach ($images as $imgUrl) {
                $fileName = basename((string)$imgUrl);
                // More permissive but safe pattern for filenames
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

                if ($fileName === '') {
                    continue;
                }

                $filePath = $dir . $fileName;
                $realFile = file_exists($filePath) ? realpath($filePath) : false;

                // Security check: ensure file is within the target directory
                if ($realDir && $realFile && strpos($realFile, $realDir) === 0) {
                    if (is_file($realFile) && @unlink($realFile)) {
                        $deleted++;
                    }
                }
            }

            return $json([
                'status' => 'ok',
                'deleted' => $deleted,
                'message' => "Successfully deleted $deleted file(s)"
            ]);
        }

        return $json([
            'status' => 'error',
            'message' => 'Invalid action. Allowed actions: load_images, upload_images, delete_images'
        ], 400);
        
    } catch (\Exception $e) {
        // Log the error
        log_message('error', 'Image handler error: ' . $e->getMessage());
        
        return $json([
            'status' => 'error',
            'message' => 'An internal error occurred: ' . $e->getMessage()
        ], 500);
    }
}*/

/**
 * Normalize files array for both single and multiple uploads
 */
private function normalizeFilesArray($files)
{
    $normalized = [];
    
    if (is_array($files['name'])) {
        // Multiple files
        foreach ($files['name'] as $key => $name) {
            $normalized[] = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            ];
        }
    } else {
        // Single file
        $normalized[] = $files;
    }
    
    return $normalized;
}






}


