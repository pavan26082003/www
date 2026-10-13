<?php

namespace App\Controllers;

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

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

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

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/client", $view_data);

     }

      function vendor() {

         $view_data['client_id'] =$this->login_user->client_id;

         $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/vendor", $view_data);

     }


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


function modal_formbrand_edit() {
       

        $view_data['model_info'] = $this->Items_model->get_one($this->request->getPost('id'));
        $view_data['brand_dropdown'] = $this->Item_categories_model->get_dropdown_list(array("title"));


        return $this->template->view('items/modal_formbrand_edit', $view_data);

    }





function product() {

         $view_data['client_id'] =$this->login_user->client_id;

        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("items/product", $view_data);

     }

/*----------------------select_app----------------------------*/


function display($modelNumber, $pageNo, $user_id)
{


   
$components = $this->Items_model->getUiComponentsByModelAndPage($modelNumber, $pageNo, $user_id);

$userID = null;


if (!empty($components)) {
    
    foreach ($components as $component) {
        
        $command_to_find = 'Data'; // example command to find, adjust as needed

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
          /*  echo "The UI type for '$command_to_find' in component ID {$component['id']} is '$cmd_ui' paramname {$component['parameter_name']} 
			permission $ui_permission ui_range_min $ui_range_min ui_range_max $ui_range_max ui_step $ui_step ui_datatype $ui_datatype.  $UserID
<br>";*/
} else {
echo "Command '$command_to_find' not found or UI type is undefined for component ID {$component['id']}.

<br>";
}
}
} else {
echo "No components found.";

}


//echo "UserID: " . $UserID . "<br>";


    return view('items/page', [
        'components' => $components,
        'modelNumber' => $modelNumber, 
        'pageNo' => $pageNo,
        'userID' => $UserID
    ]);
}



function selectModel($userId)
{

session_start();
$user_id = $_SESSION['user_id'];

$modelsWithPages = $this->Items_model->getModelsWithPagesByUserId($user_id);
return view('items/select_model', ['modelsWithPages' => $modelsWithPages]);

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
    $modelwithparameter = $this->Items_model->getparametername($userId); 
    
    if ($modelsWithIds !== false) {
        $data['modelIds'] = [];
        foreach ($modelsWithIds->getResultArray() as $model) {
            $data['modelIds'][] = $model['node_id']; 
        }
    } 

    if ($modelwithnos !== false) {
        $data['modelnos'] = [];
        foreach ($modelwithnos->getResultArray() as $model) {
            $data['modelnos'][] = $model['model_name']; 
        }
    } 



     if ($modelwithproducts !== false) {
        $data['modelproducts'] = [];
        foreach ($modelwithproducts->getResultArray() as $model) {
            $data['modelproducts'][] = $model['product_name']; 
        }
    } 

    if ($modelwithparameter !== false) {
        $data['modelparameters'] = [];
        foreach ($modelwithparameter->getResultArray() as $model) {
            $data['modelparameters'][] = $model['parameter_name']; 
        }
    } 






         else {
      //  echo "No models found for the user.";
        return;     }

    return view('items/select_device_model', $data);
}










/***********************************************************/

function callDisplayFunction()
    {


session_start();
$user_id = $_SESSION['user_id'];

        return $this->selectModel($userId);
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

    // Now $modelNumber and $pageNo are dynamically received from the URL
    return $this->display($modelNumber, $pageNo, $user_id);
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




function savebrand() {
    session_start();
    $user_id = $_SESSION['user_id'];

echo $user_id;
    $name = $this->request->getPost('name');

        $result = $this->Items_model->save_brand($name, $user_id);

    // Check the result
    if ($result === true) {
        
        echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";
    } else {
                $this->session->setFlashdata('error', $result);
        
        echo "<script>location.href='https://efactor.tech/index.php/items/brand';</script>";
    }
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


        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
      //  $user_id = session()->get('user_id'); // Assuming user ID is stored in session


echo $id;
echo $name;
echo $user_id;

       
        $result = $this->Items_model->save_brand_edit($id, $name, $user_id);

        
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
           return $this->template->view('items/modal_vendorproduct', $view_data);
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

     function modal_api() {

//$user_id = getUserIdFromSession();  

session_start();
$user_id = $_SESSION['user_id'];     

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

    
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
    $parameterName = $_POST['parameter_name'];

    
     echo "Model  : <br>";
    echo "Model ID: " . $modelId . "<br>";
    echo "Model No: " . $modelNo . "<br>";
    echo "Product Name: " . $productName . "<br>";
    echo "Parameter Name: " . $parameterName . "<br>";
    
}
      
      
     $data = array(
            'model_id' => $modelId,
            'model_no' => $modelNo,
            'product_name' => $productName,
            'parameter_name' => $parameterName
        );

        // Save the data using the model
        $this->Items_model->save_selected_values($data);


        // Redirect or load a view after saving
       // return redirect()->to('/items/callDisplayId');// Redirect to the desired page after saving

    }


    


/*-------------------------------------------------------------*/

public function modal_productinfo($node_id) {
    
    $id = $this->request->getPost('id'); 
    $data = $this->Items_model->getDataBynodeId($node_id);

    // Check if data is retrieved
    if ($data) {
       
        $product_details = $data->getRowArray();

        // If product details are found, populate view data and return the view
        if ($product_details) {
         //   $view_data['id'] = $id;
         //   $view_data['node_id'] = $node_id;
            $view_data['product_details'] = $this->_make_product_row($product_details); // Convert object to array
         //    $view_data['categories_dropdown'] = $this->Items_model->get_cats()->getResult();
            return $this->template->view('items/User_Products', $view_data);
        }
    }

        return "Product details not found for node ID: $node_id";
}

private function _make_product_row($data) {
    return array(
        'category' => $data['category'],
        'module_type' => $data['module_type'],
        'vendor_id' => $data['vendor_id'],
        'mac' => $data['mac'],
        'payload' => $data['payload'],
        'vendor_name' => $data['vendor_name'],
        'access_token' => $data['access_token'],
        'request_id' => $data['request_id'],
        'ipaddress' => $data['ipaddress'],
        'time' => $data['time'],
        'phone' => $data['phone'],
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
        'ssid' => $data['ssid'],
        'wifi_pwd' => $data['wifi_pwd']
    );
}




/********************************************************************************/


public function modal_apisinfo($node_id) {
$id = $this->request->getPost('id'); 
    $data = $this->Items_model->getDataBynodeId($node_id);

    // Check if data is retrieved
    if ($data) {
       
        $api_details = $data->getRowArray();

        // If product details are found, populate view data and return the view
        if ($api_details) {
         //   $view_data['id'] = $id;
         //   $view_data['node_id'] = $node_id;
            $view_data['api_details'] = $this->_make_product_row12($api_details); // Convert object to array
         //    $view_data['categories_dropdown'] = $this->Items_model->get_cats()->getResult();
            return $this->template->view('items/APIs', $view_data);
        }
    }

        return "Product details not found for node ID: $node_id";

      //  return $this->template->view('items/APIs', $view_data);





}




private function _make_product_row12($data) {
    return array(
        'direction' => $data['direction'],
        'Hue' => $data['Hue'],
        'name' => $data['name'],
        'power' => $data['power'],
        'speed' => $data['speed'],
        'baudrate' => $data['baudrate'],
        'link_comm_mode' => $data['link_comm_mode'],
        'timestamp' => $data['timestamp'],
        'output' => $data['output'],
        'brightness' => $data['brightness'],
        'rpm' => $data['rpm'],
        'power_watt' => $data['power_watt'],
        'current' => $data['current'],
        'bus_voltage' => $data['bus_voltage'],
        'ac_volt' => $data['ac_volt'],
        'user_set_max' => $data['user_set_max'],
        'device_lock' => $data['device_lock'],
        'device_set_max' => $data['device_set_max'],
        'power_fact' => $data['power_fact'],
        'total_watt' => $data['total_watt'],
        'serialno' => $data['serialno'],
        'led_indication' => $data['led_indication'],
        'TZ_properties' => $data['TZ_properties'],
        'BR_properties' => $data['BR_properties'],
        'UMI_properties' => $data['UMI_properties'],
        'EC_properties' => $data['EC_properties'],
        'CM_properties' => $data['CM_properties'],
        'TU_properties' => $data['TU_properties'],
        'LC_properties' => $data['LC_properties'],
        'cook_time' => $data['cook_time'],
        'cookermode' => $data['cookermode'],
        'cook_run_temp' => $data['cook_run_temp'],
        'cook_max_temp' => $data['cook_max_temp'],
        'ping' => $data['ping'],
        'cooker_properties' => $data['cooker_properties'],
        'recipe' => $data['recipe'],
        'pre_time_const' => $data['pre_time_const'],
        'keep_warm_timer' => $data['keep_warm_timer'],
        'dishes' => $data['dishes']
    );
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
            $view_data['brand_logo'] = $api_details_row->brand_logo;
            $view_data['productname'] = $api_details_row->product_name;
            $view_data['modelname'] = $api_details_row->model_name;
            $view_data['product_description'] = $api_details_row->product_description;
            $view_data['numberofpages'] = $api_details_row->number_of_pages;
            $view_data['pagenames'] = $api_details_row->page_names;
            $view_data['date'] = $api_details_row->date;
            $view_data['itemid'] = $api_details_row->itemid;
            $view_data['filename'] = $api_details_row->filename;
            $view_data['apidate'] = $api_details_row->apidate;

              $view_data['cmd_parametername'] = $api_details_row->parameter_name;
            $view_data['cmd_dataflow'] = $api_details_row->cmd_dataflow;
            $view_data['cmd_frametype'] = $api_details_row->cmd_frametype;
            $view_data['cmd_framesizeinbytes'] = $api_details_row->cmd_framesize;
            $view_data['cmd_noofrows'] = $api_details_row->cmd_noofrows;
            $view_data['cmd_frametrigger'] = $api_details_row->cmd_frametrigger;
            $view_data['cmd_multipleui'] = $api_details_row->cmd_multipleui;
            
            $view_data['resp_dataflow'] = $api_details_row->resp_dataflow;
            $view_data['resp_frametype'] = $api_details_row->resp_frametype;
            $view_data['resp_framesizeinbytes'] = $api_details_row->resp_framesize;
            $view_data['resp_noofrows'] = $api_details_row->resp_noofrows;
            $view_data['resp_frametrigger'] = $api_details_row->resp_frametrigger;
            $view_data['resp_multipleui'] = $api_details_row->resp_multipleui;

            $view_data['error_dataflow'] = $api_details_row->error_dataflow;
            $view_data['error_frametype'] = $api_details_row->error_frametype;
            $view_data['error_framesizeinbytes'] = $api_details_row->error_framesize;
            $view_data['error_noofrows'] = $api_details_row->error_noofrows;
            $view_data['error_frametrigger'] = $api_details_row->error_frametrigger;
            $view_data['error_multipleui'] = $api_details_row->error_multipleui;



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
           $view_data['uiDescriptionInOut_cmd'] = explode(',', $api_details_row->cmd_uidescriptioninout);
           $view_data['sliderStepValue_cmd'] = explode(',', $api_details_row->cmd_sliderstepvalue);
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
           $view_data['resp_asperui'] = explode(',', $api_details_row->cmd_asperui);
           $view_data['uiDescriptionInOut_resp'] = explode(',', $api_details_row->resp_uiDescriptionInOut);
           $view_data['sliderStepValue_resp'] = explode(',', $api_details_row->resp_sliderStepValue);
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
           $view_data['error_asperui'] = explode(',', $api_details_row->cmd_asperui);
           $view_data['uiDescriptionInOut_error'] = explode(',', $api_details_row->error_uiDescriptionInOut);
           $view_data['sliderStepValue_error'] = explode(',', $api_details_row->error_sliderStepValue);
           $view_data['developerNotes_error'] = explode(',', $api_details_row->error_developerNotes);



                         
 /*          
print_r($api_details_row);
(print_r($view_data['fieldDescription_cmd']);
print_r($view_data['fieldDescription_resp']);
print_r($view_data['fieldDescription_error']);
*/



//print_r($view_data);




        } else {
            // Handle case when API details row is not found
            echo "API details row not found.";
        }
    } else {
        // Handle case when API details result is not found
        echo "API details result not found.";
    }

    // Return the view with the view data
   return $this->template->view('items/modal_command', $view_data);
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
        "category" => $this->request->getPost('category'),
        "modelname" => $this->request->getPost('modelname'),
        "modelno" => $this->request->getPost('modelno'),
        "description" => $this->request->getPost('description')
    ];

        $result = $this->Items_model->savevendorproduct($item_data, $user_id);

        if (is_string($result)) {
               $view_data['error'] = $result;
    } else {
                echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";
        return;
    }
     
        return $this->template->rander("items/vendor", $view_data);
            
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


function saveapi() {


//$user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];




        $id = $this->request->getPost('id');
        $rand=rand('0000','9999');
        $file_name = $rand.'_'.$this->request->getPost("item_files");
        $target_path = get_setting("timeline_file_path");
        $file_info = move_temp_file($file_name, $target_path);



/**********************************/

$file_name = $rand . '_' . $this->request->getPost("item_files");
    $target_path = get_setting("timeline_file_path");
    $file_info = move_temp_file($file_name, $target_path);

    // Handle brand logo file upload
    if (isset($_FILES['brandlogo']) && $_FILES['brandlogo']['error'] == 0) {
        $brand_logo_tmp = $_FILES['brand_logo']['tmp_name'];
        $brand_logo_name = $_FILES['brand_logo']['name'];
        $brand_logo_target = 'https://efactor.tech/files/brand_logo/' . $brand_logo_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($brand_logo_tmp, $brand_logo_target)) {
            $brand_logo_url = $brand_logo_target;
        } else {
            echo "Error uploading brand logo file!";
            return;
        }
    } else {
        $brand_logo_url = null; // Or set a default URL if needed
    }















/***************************************/


        $item_data = array(
        "brandname" => $this->request->getPost('brandname'),
        "productname" => $this->request->getPost('productname'),
        "modelname" => $this->request->getPost('modelname'),
        "productdescription" => $this->request->getPost('productdescription'),
        "numberofpages" => $this->request->getPost('numberofpages'),
        "pagenames" => $this->request->getPost('pagenames'),
        "date" => $this->request->getPost('date'),
        "itemid" => $this->request->getPost('id'),

         "cmd_parametername" => $this->request->getPost('cmd_parametername'),
        "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),
        "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
        "cmd_noofrows" => $this->request->getPost('cmd_noofrows'),
        "cmd_frametrigger" => $this->request->getPost('cmd_frametrigger'),
        "cmd_multipleui" => $this->request->getPost('cmd_multipleui'),

        "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),
        "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
        "resp_noofrows" => $this->request->getPost('resp_noofrows'),
        "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
        "resp_multipleui" => $this->request->getPost('resp_multipleui'),


        "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),
        "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
        "error_noofrows" => $this->request->getPost('error_noofrows'),
        "error_frametrigger" => $this->request->getPost('error_frametrigger'),
        "error_multipleui" => $this->request->getPost('error_multipleui'),
            
    );

    $additional_data = array();
    $frame_size = (int)$this->request->getPost('cmd_noofrows');
    $respframe_size = (int)$this->request->getPost('resp_noofrows');
    $errorframe_size = (int)$this->request->getPost('error_noofrows');



    for ($i = 1; $i <= $frame_size; $i++) {

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
                "cmd_asperui" => $this->request->getPost('asperui_cmd_'.$i),
                "cmd_uiPageNo" => $this->request->getPost('uiPageNo_cmd_'.$i),
                "cmd_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_cmd_'.$i),
                "cmd_sliderStepValue" => $this->request->getPost('sliderStepValue_cmd_'.$i),
                "cmd_developerNotes" => $this->request->getPost('developerNotes_cmd_'.$i)


        );
    }




 for ($i = 1; $i <= $respframe_size; $i++) {


 
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
                "resp_asperui" => $this->request->getPost('asperui_resp_'.$i),
                "resp_uiPageNo" => $this->request->getPost('uiPageNo_resp_'.$i),            
                "resp_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_resp_'.$i),
                "resp_sliderStepValue" => $this->request->getPost('sliderStepValue_resp_'.$i),
                "resp_developerNotes" => $this->request->getPost('developerNotes_resp_'.$i)


        );
    }



for ($i = 1; $i <= $errorframe_size; $i++) {


 
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
                "error_asperui" => $this->request->getPost('asperui_error_'.$i),
                "error_uiPageNo" => $this->request->getPost('uiPageNo_error_'.$i),      
                "error_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_error_'.$i),
                "error_sliderStepValue" => $this->request->getPost('sliderStepValue_error_'.$i),
                "error_developerNotes" => $this->request->getPost('developerNotes_error_'.$i)


        );
    }



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

} 

else {
       
        echo "Error saving data to database!";

/*echo "POST Data for Frame $i: <pre>";
print_r($additional_data[count($additional_data) - 1]); // Print the last added frame
echo "</pre>";


echo "All POST Data: <pre>";
print_r($_POST);
echo "</pre>";


// Debugging statement to print $item_data
echo "Item Data: <pre>";
print_r($item_data);
echo "</pre>";


echo "frame_size: <pre>";
print_r($frame_size);
echo "</pre>";


// Debugging statement to print $additional_data
echo "Additional Data: <pre>";
print_r($additional_data);
echo "</pre>";*/
    }
}







function saveapi_edit() {
    // Retrieve form data
    $item_data = array(
        "brandname" => $this->request->getPost('brandname'),
        "productname" => $this->request->getPost('productname'),
        "modelname" => $this->request->getPost('modelname'),
        "productdescription" => $this->request->getPost('productdescription'),
        "numberofpages" => $this->request->getPost('numberofpages'),
        "pagenames" => $this->request->getPost('pagenames'),
        "date" => $this->request->getPost('date'),


         "cmd_parametername" => $this->request->getPost('cmd_parametername'),
        "cmd_data_flow_direction" => $this->request->getPost('cmd_data_flow_direction'),
        "cmd_framesizeinbytes" => $this->request->getPost('cmd_framesizeinbytes'),
        "cmd_frametrigger" => $this->request->getPost('cmd_frametrigger'),
        "cmd_multipleui" => $this->request->getPost('cmd_multipleui'),

        "resp_data_flow_direction" => $this->request->getPost('resp_data_flow_direction'),
        "resp_framesizeinbytes" => $this->request->getPost('resp_framesizeinbytes'),
        "resp_frametrigger" => $this->request->getPost('resp_frametrigger'),
        "resp_multipleui" => $this->request->getPost('resp_multipleui'),

        "error_data_flow_direction" => $this->request->getPost('error_data_flow_direction'),
        "error_framesizeinbytes" => $this->request->getPost('error_framesizeinbytes'),
        "error_frametrigger" => $this->request->getPost('error_frametrigger'),
        "error_multipleui" => $this->request->getPost('error_multipleui'),



        );

    // Additional table data
    $additional_data = array();
    $cmdframe_size = (int)$this->request->getPost('cmd_framesizeinbytes');
    $respframe_size = (int)$this->request->getPost('resp_framesizeinbytes');
     $errorframe_size = (int)$this->request->getPost('error_framesizeinbytes');



   /* for ($i = 1; $i <= $frame_size; $i++) {


 
        $additional_data[] = array(
            "cmd_startBytePosition" => $this->request->getPost('startBytePosition_cmd'.$i),
                "cmd_endBytePosition" => $this->request->getPost('endBytePosition_cmd'.$i),
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
                "cmd_uiPageNo" => $this->request->getPost('uiPageNo_cmd_'.$i),
                "cmd_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_cmd_'.$i),
                "cmd_sliderStepValue" => $this->request->getPost('sliderStepValue_cmd_'.$i),
                "cmd_developerNotes" => $this->request->getPost('developerNotes_cmd_'.$i)


        );
    }*/



for ($i = 1; $i <= $cmdframe_size; $i++) {
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
        "cmd_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_cmd')[$i - 1],
        "cmd_sliderStepValue" => $this->request->getPost('sliderStepValue_cmd')[$i - 1],
        "cmd_developerNotes" => $this->request->getPost('developerNotes_cmd')[$i - 1]
    );
}



/*
 for ($i = 1; $i <= $respframe_size; $i++) {


 
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
                "resp_uiPageNo" => $this->request->getPost('uiPageNo_resp_'.$i),
                "resp_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_resp_'.$i),
                "resp_sliderStepValue" => $this->request->getPost('sliderStepValue_resp_'.$i),
                "resp_developerNotes" => $this->request->getPost('developerNotes_resp_'.$i)


        );
    }

*/




for ($i = 1; $i <= $respframe_size; $i++) {
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
        "resp_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_resp')[$i - 1],
        "resp_sliderStepValue" => $this->request->getPost('sliderStepValue_resp')[$i - 1],
        "resp_developerNotes" => $this->request->getPost('developerNotes_resp')[$i - 1]
    );
}


for ($i = 1; $i <= $errorframe_size; $i++) {
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
        "error_uiDescriptionInOut" => $this->request->getPost('uiDescriptionInOut_error')[$i - 1],
        "error_sliderStepValue" => $this->request->getPost('sliderStepValue_error')[$i - 1],
        "error_developerNotes" => $this->request->getPost('developerNotes_error')[$i - 1]
    );
}


if ($this->Items_model->saveapi_edit($item_data, $additional_data, $respframe_data, $errorframe_data)) {
              
echo "Data saved successfully!";


echo "Item Data: <pre>";
print_r($item_data);
echo "</pre>";

/*echo "POST Data for Frame $i: <pre>";
print_r($additional_data[count($additional_data) - 1]); // Print the last added frame
echo "</pre>";

echo "All POST Data: <pre>";
print_r($_POST);
echo "</pre>";

// Debugging statement to print $item_data
echo "Item Data: <pre>";
print_r($item_data);
echo "</pre>";


echo "frame_size: <pre>";
print_r($frame_size);
echo "</pre>";


// Debugging statement to print $additional_data
echo "Additional Data: <pre>";
print_r($additional_data);
echo "</pre>";*/

    //    echo "<script>location.href='https://efactor.tech/index.php/items/vendor';</script>";
    } else {
         echo "Error saving data to database!";
/*
echo "POST Data for Frame $i: <pre>";
print_r($additional_data[count($additional_data) - 1]); // Print the last added frame
echo "</pre>";


echo "All POST Data: <pre>";
print_r($_POST);
echo "</pre>";


// Debugging statement to print $item_data
echo "Item Data: <pre>";
print_r($item_data);
echo "</pre>";


echo "frame_size: <pre>";
print_r($frame_size);
echo "</pre>";


// Debugging statement to print $additional_data
echo "Additional Data: <pre>";
print_r($additional_data);
echo "</pre>";*/
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











/*****************************************************************************************/

        function savecat() {

  // $user_id = getUserIdFromSession();

session_start();
$user_id = $_SESSION['user_id'];


       $name= $this->request->getPost('name');

          $item_id = $this->Items_model->save_cat($name, $user_id);

            echo "<script>location.href='https://efactor.tech/index.php/items/categories';</script>";


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

    function list_data() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $category_id = $this->request->getPost('category_id');
        $options = array("category_id" => $category_id);

        $list_data = $this->Items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
 }





function list_data_product() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $category_id = $this->request->getPost('category_id');
    $node_id = $this->request->getPost('node_id'); // Retrieve node_id from POST data

$node_id='FFFFFFFFFFFFFFFF7EC2000000000200';
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



       return array(
         
            $data->brand_name,
            $data->brand_logo,
            $data->product_name,
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
            $data->resp_dataflow,
            $data->resp_framesize,
            $data->resp_noofrows,
            $data->resp_frametrigger,
            $data->resp_multipleui,
            $data->error_dataflow,
            $data->error_framesize,
            $data->error_noofrows,
            $data->error_frametrigger, 
            $data->error_multipleui,       


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
            $data->cmd_uidescriptioninout, 
            $data->cmd_sliderstepvalue,
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
            $data->resp_uiDescriptionInOut, 
            $data->resp_sliderStepValue,
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
            $data->error_uiDescriptionInOut, 
            $data->error_sliderStepValue,
            $data->error_developerNotes,
            $data->user_id,
          
             'model_name' => $modelname,
             'product_name' => $productname, 

        
    
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



    /* prepare a row of item list table */

         private function _make_item_row3($data) {

         return array(
          //  modal_anchor(get_uri("items/view"), $show_in_client_portal_icon . $data->title, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),

            $data->name,
          
          modal_anchor(get_uri("items/modal_formbrand_edit"), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))   
           .js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletecat"), "data-action" => "delete"))
        );


         }



 private function _make_item_row4($data) {

         return array(
          
            $data->name,
            
          
          modal_anchor(get_uri("items/modal_formbrand_edit"), "Edit", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id))   
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


        //_make_item_row1_vendor
            private function _make_item_row1_vendor($data) {

                  return array(
            $data->modelname,
            $data->modelno,

      
           anchor(get_uri("items/viewapis/" . $data->id), 'View Apis').        
           modal_anchor(get_uri("items/modal_api"), "Add API/Product", array("class" => "edit", "title" => 'Add API/Product', "data-post-id" => $data->id)).
           anchor(get_uri("items/callDisplayFunction"), "View UI type", array("class" => "edit", "title" => 'View UI type')).
           anchor(get_uri("items/callDisplayId"), "DEVICE SELECT", array("class" => "edit", "title" => 'DEVICE SELECT'))
         . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("items/deletevendorproduct"), "data-action" => "delete"))
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

private function _make_item_row($data) {
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
        default:
            $deviceImage = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }

  /*  if (is_object($data) && isset($data->internet)) {
        $internetStatus = $data->internet;
    } else {
        $internetStatus = 'default_value';
    }*/

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

    $brand_name='Efactor';
    $brand = 'efactor';

    switch ($brand) {
        case 'efactor':
            $brand_logo = '<img src="https://efactor.tech/files/home/logo.jpg" style="width:30px;height:30px">';
            break;
        case 'Apple':
            $brand_logo = '<img src="https://efactor.tech/files/home/apple.jpeg" style="width:30px;height:30px">';
            break;
        default:
            $brand_logo = '<img src="https://efactor.tech/files/home/blank.jpeg" style="width:30px;height:30px" alt="Default">';
    }

    return array(
        $modeImage,
        $data->node_id,
        $brand_name,
        $brand_logo,
        $data->device_type,
        $data->model_number,
        $data->device_type,
        $deviceImage,
        $data->user_id,
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
              modal_anchor(get_uri("items/modal_apisinfo/" . $data->node_id), "APIs Info", array("class" => "edit", "title" => 'Edit', "data-post-id" => $data->id)),
    );
}
    
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

        //$view_data["items"] = $this->Items_model->get_details($options)->getResult();

        $view_data["items"] = $this->Items_model->get_details1($options)->getResult();

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
            "title" => $item_info->title,
            "quantity" => 1, //add 1 item first time
            "unit_type" => $item_info->unit_type,
            "rate" => $item_info->rate,
            "total" => $item_info->rate, //since the quantity is 1
            "created_by" => $this->login_user->id,
            "item_id" => $id
        );

        $save_id = $this->Order_items_model->ci_save($order_item_data);

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

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
/* End of file items.php */
/* Location: ./app/controllers/items.php */
