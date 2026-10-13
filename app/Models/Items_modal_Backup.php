<?php

namespace App\Models;

use CodeIgniter\Model;

class Items_model extends Model
{
    protected $table      = 'sticker_fields'; // DB table
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;



protected $allowedFields = [
    'sticker_id',
    'modelname',
    'field_name',
    'field_value',
    'input_type',
    'keyword_type',
    'keyword_value_type',
    'user_id',
    'counting',
    'positions',
    'copy_positions',
    'width_mm',
    'height_mm',
    'width_px',
    'height_px',
    'orig_width_mm',
    'orig_height_mm',
    'orig_width_px',
    'orig_height_px',
    'copy_width_mm',
    'copy_height_mm',
    'copy_width_px',
    'copy_height_px',
    'field_left',
    'field_top',
    'field_scaleX',
    'field_scaleY',
    'field_rotation',
    'field_fontsize',
    'field_fontweight',
    'field_fontfamily',
    'qr_node_id',
    'qr_size_mm',
    'created_at',
];


  

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // agar updated_at column nahi hai

    public function __construct()
    {
        
        parent::__construct();
    }




   
    function get_details($options = array()) {
       /* $items_table = $this->db->prefixTable('efactorproduct');
        $order_items_table = $this->db->prefixTable('order_items');
        $item_categories_table = $this->db->prefixTable('item_categories');

        $where = "";

	//$where .= " AND $items_table.id!=$order_items_table.item_id";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $items_table.id=$id";
        }

        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeString($search);
            $where .= " AND ($items_table.title LIKE '%$search%' OR $items_table.description LIKE '%$search%')";
        }

        $show_in_client_portal = get_array_value($options, "show_in_client_portal");
        if ($show_in_client_portal) {
            $where .= " AND $items_table.show_in_client_portal=1";
        }

        $category_id = get_array_value($options, "category_id");
        if ($category_id) {
            $where .= " AND $items_table.category_id=$category_id";
        }

        $extra_select = "";
        $login_user_id = get_array_value($options, "user_id");
        if ($login_user_id) {
            $extra_select = ", (SELECT COUNT($order_items_table.id) FROM $order_items_table WHERE $order_items_table.deleted=0 AND $order_items_table.order_id=0 AND $order_items_table.created_by=$login_user_id AND $order_items_table.item_id=$items_table.id) AS added_to_cart";
        }

        $limit_query = "";
        $limit = get_array_value($options, "limit");
        if ($limit) {
            $offset = get_array_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }*/

      

$sql = "SELECT * FROM efactorproduct";
return $this->db->query($sql);
    }





function get_online_details() {
    $builder = $this->db->table('efactorproduct');
    $builder->where("internet = 'true'", null, false); // force raw condition
    $builder->where('deleted', 0); // optional if you have this column

    return $builder->get()->getResult();
}



/************************************************/

function default_form_values($itemid) {
    $sql = "SELECT * FROM vendorproducts where id='$itemid'";
    return $this->db->query($sql);    
    }






function get_details_product($node_id) {
       


$sql = "SELECT * FROM efactorproduct where node_id='$node_id'";
return $this->db->query($sql);
    }







public function get_brand_products($user_id) {




 $sql5 = "SELECT * FROM users WHERE id = '$user_id'";
        $query = $this->db->query($sql5);
        $fact = $query->getRow(); 

        $vendor_name = $fact->brand_name;
      

$sql = "SELECT internet, device_time, node_id, device_type, model_name, user_id, phone, email, latitude, longitude FROM efactorproduct where vendor_name='$vendor_name'";



return $this->db->query($sql);
    }



/**************************************************/


function save_selected_values($data) {


 $model_id = $data['model_id'];
        $product_name = $data['model_no'];
        $model_no = $data['product_name'];
     //   $parameter_name = $data['parameter_name'];
       


        if($product_name === 'EFDEFLT V1.0'){
            $sql4 = "UPDATE efactorproduct SET model_number = '$product_name' WHERE node_id = '$model_id'";
            $this->db->query($sql4);
        }else{

            $existing_brand = $this->db->table($product_name)
            ->where('node_id', $model_id)
            ->get()
            ->getRow();

if ($existing_brand) {




echo '1,,';
  
         $sql56 ="INSERT INTO `$product_name`
SET selected = 1,
    model_number = '$product_name',
    model_name = '$product_name',
    device_type = '$model_no',
    node_id = '$model_id'";

$this->db->query($sql56);

$sql4 = "UPDATE efactorproduct 
    SET model_number = '$product_name', 
        model_name = '$product_name', 
        name = '$model_no', 
        device_type = '$model_no',  
        current_devicetype = CONCAT('$model_no', SUBSTRING(current_devicetype, LOCATE('_', current_devicetype)))   
    WHERE node_id = '$model_id' 
    AND model_number = 'EFDEFLT V1.0' 
    AND device_type = 'input-output'";
$this->db->query($sql4);



} else {

echo 'else';
    $sql4 = "UPDATE efactorproduct 
    SET model_number = '$product_name', 
        model_name = '$product_name', 
        name = '$model_no', 
        device_type = '$model_no',  
        current_devicetype = CONCAT('$model_no', SUBSTRING(current_devicetype, LOCATE('_', current_devicetype)))   
    WHERE node_id = '$model_id' 
    AND model_number = 'EFDEFLT V1.0' 
    AND device_type = 'input-output'";
$this->db->query($sql4);

if ($this->db->affectedRows() > 0) {
        $sql5 = "SELECT * FROM efactorproduct WHERE node_id = '$model_id'";
        $query = $this->db->query($sql5);
        $fact = $query->getRow(); 

        $model_name = $fact->model_name;
        $current_devicetype = $fact->current_devicetype;
 
        $sql6 = "SELECT * FROM product_api WHERE model_name = '$model_name'";
        $query2 = $this->db->query($sql6);
        $initial_values = $query2->getResult(); 
      
        $mapped_values = [];
        foreach ($initial_values as $row) {
            $cmd_defaultvalue = $row->cmd_defaultvalue;
            $cmd_fieldescription = explode(',', $row->cmd_fielddescription);
            $cmd_valueinhexmin = explode(',', $row->cmd_valueinhexmin);
         
            $data_index = array_search('Data', $cmd_fieldescription);
            if ($data_index !== false) {
                $value_for_data = isset($cmd_valueinhexmin[$data_index]) ? $cmd_valueinhexmin[$data_index] : null;
         
                $default_values_array = explode(',', $cmd_defaultvalue);
                if (isset($default_values_array[$data_index])) {
                   
                    $mapped_key = trim($default_values_array[$data_index]); 
                    if ($mapped_key == 'name' || $mapped_key == 'NAME' || $mapped_key == 'Name') {
                        $mapped_values[$mapped_key] = $current_devicetype;
                    }else{
                        $mapped_values[$mapped_key] = $value_for_data;
                    }
                }
            }
        }
        //print_r($mapped_values);
        $default_params = ["
        device_type,
        brandlogo,
        product_image,
        model_name,
        model_number,
        number_of_pages,
        page_names,
        payload,
        node_id,
        module_type,
        client_name,
        client_id,
        user_name,
        user_id,
        access_token,
        request_id,
        mac,
        add_gateway,
        is_added,
        secret_key,
        TZ_properties,
        BR_properties,
        UMI_properties,
        EC_properties,
        CM_properties,
        TU_properties,
        LC_properties,
        display_properties,
        req_status,
        mob_device_token,
        ipaddress,
        current_devicetype,
        ping,
        latitude,
        longitude,
        signin_latitude,
        signin_longitude,
        node_latitude,
        node_longitude,
        phone,
        email,
        app_version,
        fw_version,
        add_node_time,
        ssid,
        wifi_pwd,
        auto_test,
        SUID,
        DUID,
        UDP_CMD,
        UDP_ACK,
        MEDIA,
        Frame,
        FID,
        UserID,
        SDUID_Enable,
        UID_DATA,
        baudrate,
        uartmsgid,
        errorcode" ];
      //  print_r($fact);
       //   print_r($default_params);
      foreach($default_params as $key){
        $dflt22 = $key;
      }
      $dflt23 = explode(',', $dflt22);
      foreach($dflt23 as $key){
        $dflt[] =  array(
            'name' => $key,
        );
      }
 // print_r($dflt);


        $cker = array();

        foreach ($fact as $key => $value) {
            $cker[] = array(
                'name' => $key,
                'value' => $value 
            ); 
        } 
         
       //  print_r($cker);
 
       foreach ($dflt as &$dfltItem) {
        foreach ($cker as $ckerItem) {
            if (trim($dfltItem['name']) === trim($ckerItem['name'])) {
                $dfltItem['value'] = $ckerItem['value'];
            }
        }
    } 
    foreach ($dflt as &$dfltItem) {
        if (trim($dfltItem['name']) === "page_names") {
            $dfltItem['value'] = "Device Communication";
        }elseif (trim($dfltItem['name']) === "baudrate") {
            $dfltItem['value'] = "04";
        }elseif (trim($dfltItem['name']) === "uartmsgid") {
            $dfltItem['value'] = "00";
        }elseif (trim($dfltItem['name']) === "errorcode") {
            $dfltItem['value'] = "00";
        }
    }

 
 
$sql3 = "INSERT INTO `$product_name` (";
$fields = [];
$values = [];
 
foreach ($dflt as $item) {
    $fields[] = $item['name'];
    if (isset($item['value']) && $item['value'] !== '') {
        $values[] = "'" . $item['value'] . "'";
    } else {
        $values[] = "NULL";  
    }
} 
foreach ($mapped_values as $key => $value) {
    $fields[] = "`" . $key . "`";    
    $values[] = "'" . $value . "'";  
} 
$sql3 .= implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")";

echo $sql3;  
$this->db->query($sql3);

}else{
    return false;
}
        }
        }
        if($product_name == 'EFDEFLT V1.0'){
            $updateQuery = "UPDATE efactorproduct 
            SET selected = CASE 
                WHEN node_id = ? AND device_type = ? AND model_name = ? THEN '1'
                ELSE '0'
            END
            WHERE device_type = ? AND model_name = ?";
        }else{
                $updateQuery = "UPDATE `$product_name` 
                SET selected = CASE 
                    WHEN node_id = ? AND device_type = ? AND model_name = ? THEN '1'
                    ELSE '0'
                END
                WHERE device_type = ? AND model_name = ?";
        }

return $this->db->query($updateQuery, array($model_id, $model_no, $product_name, $model_no, $product_name));

        
$sql1 = "UPDATE product_api 
         SET selected = '0' 
         WHERE product_name != ?";

$this->db->query($sql1, array($product_name));


$sql2 = "UPDATE product_api 
         SET node_id = ?, 
             selected = '1' 
         WHERE product_name = ? 
         AND model_name = ?";

return $this->db->query($sql2, array($model_id, $model_no, $product_name));


    }
 

    function save_deselected_values($data) {
        $model_id = $data['model_id'];
               $model_no = $data['model_no'];
               $product_name = $data['product_name'];
             //  $parameter_name = $data['parameter_name'];
               
                   $sql4 = "UPDATE efactorproduct SET model_number = 'EFDEFLT V1.0',model_name = 'EFDEFLT V1.0',name = 'input-output',device_type = 'input-output',  current_devicetype = CONCAT('input-output', SUBSTRING(current_devicetype, LOCATE('_', current_devicetype)))   WHERE node_id = '$model_id'";
                   $this->db->query($sql4);
                
      $sql3 = "DELETE FROM `$model_no` WHERE model_number = '$model_no' AND device_type = '$product_name' AND node_id = '$model_id'";
        echo $sql3;
       $this->db->query($sql3);
           }


/******************************************************************************/


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


public function apisinfo_user_type($current_user_id)
{ 
if($current_user_id == "1"){
$user_type = 'lead';
}else{
       $sql = "
            SELECT * FROM `users`
            WHERE id = '$current_user_id'
        ";
    
    $results = $this->db->query($sql)->getResultArray();

    $user_type = null; 
    if (!empty($results)) {
        $result = $results[0];  
        if (isset($result['user_type'])) {
            $user_type = $result['user_type'];  
        }
    }
}
    return $user_type;   
}






/*****************************************************************************/




public function extract_model_name($user_id)
{
    $sql = "
        SELECT modelname 
        FROM vendorproducts
        WHERE user_id = '$user_id' 
    ";

    return $this->db->query($sql)->getResultArray();
}

public function extract_page_names($user_id)
{
    $sql = "
        SELECT model_name, page_names
        FROM product_api
        WHERE user_id = '$user_id' 
    ";

    return $this->db->query($sql)->getResultArray();
}

public function extract_node_id($user_id)
{
    $sql = "
        SELECT model_number, node_id
        FROM efactorproduct
        WHERE user_id = '$user_id' 
    ";

    return $this->db->query($sql)->getResultArray();
}

public function extract_router_id($user_id)
{
    $sql = "
        SELECT model_number, node_id
        FROM efactorproduct
        WHERE user_id = '$user_id' AND module_type = 'Gateway'
    ";

    return $this->db->query($sql)->getResultArray();
}


public function extract_parameters($user_id)
{
    $sql = "
        SELECT model_name, page_names, parameter_name
        FROM product_api
        WHERE user_id = '$user_id' 
    ";

    return $this->db->query($sql)->getResultArray();
}

public function extract_default_names($user_id)
{
    $sql = "
        SELECT model_name, page_names, parameter_name, cmd_defaultvalue
        FROM product_api
        WHERE user_id = '$user_id'
    ";

    return $this->db->query($sql)->getResultArray();
}



public function extract_pages($user_id, $modelno)
{
    $sql = "
        SELECT page_names
        FROM vendorproducts
        WHERE user_id = '$user_id' AND modelno = '$modelno'
    ";

    return $this->db->query($sql)->getResultArray();
}


public function extract_product_name($user_id, $modelno)
{
    $sql = "
        SELECT product_type
        FROM vendorproducts
        WHERE user_id = '$user_id' AND modelno = '$modelno'
    ";

    return $this->db->query($sql)->getResultArray();
}



public function extract_products_name($user_id)
{
    $sql = "
        SELECT product_type
        FROM vendorproducts
        WHERE user_id = '$user_id'";

    return $this->db->query($sql)->getResultArray();
}



public function extract_pages_names($user_id)
{
    $sql = "
        SELECT page_names
        FROM product_api
        WHERE user_id = '$user_id'";

    return $this->db->query($sql)->getResultArray();
}




public function get_pairing_config($model_no, $user_id)
{
    $sql = "
        SELECT pairing_popup
        FROM pairing_config
        WHERE user_id = '$user_id' AND model_name = '$model_no'
    ";
//echo $sql;
    return $this->db->query($sql)->getResultArray();
}



/******************************************************************************/


    function get_orders($clientid) {

         $order_table = $this->db->prefixTable('orders');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.client_id=$clientid";

         return $this->db->query($sql);

     }

      function get_items($orderid) {

         $order_table = $this->db->prefixTable('order_items');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.order_id=$orderid";

         return $this->db->query($sql);

     }



public function addParamToPage($model_name, $page_no, $param) {
    $builder = $this->db->table('api_page_linking_data');
    $record = $builder->where('model_name', $model_name)->get()->getRowArray();

    if (!$record) return false;

    $pageNames   = json_decode($record['page_names'], true);
    $linkingData = json_decode($record['linking_data'], true);

    $pageName = $pageNames[$page_no] ?? null;
    if (!$pageName) return false;

    if (!isset($linkingData[$pageName])) {
        $linkingData[$pageName] = [];
    }

    
    if (in_array($param, $linkingData[$pageName])) {
        return false; // already exists
    }

    $linkingData[$pageName][] = $param;

    return $builder->where('model_name', $model_name)
                   ->update(['linking_data' => json_encode($linkingData, JSON_UNESCAPED_UNICODE)]);
}







public function updatePageName($model_name, $page_no, $new_page_name)
    {
        // Get the database builder for the table
        $builder = $this->db->table('api_page_linking_data');

        // Fetch the current record for the given model_name
        $record = $builder->where('model_name', $model_name)->get()->getRowArray();

        // If no record is found, log an error and return false
        if (empty($record)) {
            log_message('error', "Model record not found for model_name: {$model_name}");
            return false;
        }

        // Decode the JSON strings into PHP arrays
        $page_names = json_decode($record['page_names'], true);
        $linking_data = json_decode($record['linking_data'], true);

        // Check if the page number exists in the page_names array
        if (!isset($page_names[$page_no])) {
            log_message('error', "Page number {$page_no} not found in page_names for model_name: {$model_name}");
            return false;
        }

        // Store the old page name before updating
        $old_page_name = $page_names[$page_no];

        // Optional: Check if the new page name already exists for another page number
        // This prevents duplicate page names if they are meant to be unique across pages
        foreach ($page_names as $p_num => $p_name) {
            if ($p_num != $page_no && $p_name === $new_page_name) {
                log_message('error', "New page name '{$new_page_name}' already exists for page number {$p_num}.");
                return false; // Return false or throw an exception if duplicates are not allowed
            }
        }

        // Update the page_names array with the new page name
        $page_names[$page_no] = $new_page_name;

        // If the old page name was a key in linking_data, update it to the new page name
        if (isset($linking_data[$old_page_name])) {
            $linking_data[$new_page_name] = $linking_data[$old_page_name]; // Copy data to new key
            unset($linking_data[$old_page_name]); // Remove the old key
            log_message('info', "Updated linking_data key from '{$old_page_name}' to '{$new_page_name}'");
        } else {
            log_message('info', "Old page name '{$old_page_name}' not found as a key in linking_data. No linking_data key update needed.");
        }

        // Encode the updated arrays back into JSON strings
        $updated_page_names_json = json_encode($page_names);
        $updated_linking_data_json = json_encode($linking_data);

        // Prepare the data to be updated in the database
        $data_to_update = [
            'page_names' => $updated_page_names_json,
            'linking_data' => $updated_linking_data_json
        ];

        // Perform the database update
        $result = $builder->where('model_name', $model_name)->update($data_to_update);

        // Log the update operation details for debugging
        log_message('error', 'Update attempt for model: ' . $model_name . ', Data: ' . print_r($data_to_update, true));
        log_message('error', 'Database update result: ' . var_export($result, true));

        return $result; // Returns true on successful update, false otherwise
    }




public function deleteParamFromPage($model_name, $page_no, $param) {
    $builder = $this->db->table('api_page_linking_data');
    $record = $builder->where('model_name', $model_name)->get()->getRowArray();

    if (!$record) return false;

    $pageNames = json_decode($record['page_names'], true);
    $linkingData = json_decode($record['linking_data'], true);

    $pageName = $pageNames[$page_no] ?? null;
    if (!$pageName || !isset($linkingData[$pageName])) return false;

    $linkingData[$pageName] = array_values(array_diff($linkingData[$pageName], [$param]));

    return $builder->where('model_name', $model_name)
        ->update(['linking_data' => json_encode($linkingData)]);
}







function save_prod($id,$name,$prodimg,$user_id)
{  
        // Insert new brand
        $data = [
            'id' => $id,
            'name' => $name,        
            'user_id' => $user_id,
             'Product_image' => $prodimg,
        ];
        $this->db->table('productcats')->update($data);
        return true; // Success
   
    print_r($data);
}



function savebrand($data)
     {

$table = $this->db->prefixTable('brands');

 $sql = "INSERT INTO $table (name, user_id) VALUES ('$name', '$user_id')";

         return $this->db->query($sql);

 }



public function get_model_by_model_name($modelno) {
  //  echo "Model Name: " . $modelno . "<br>";

    $result = $this->db->table('api_page_linking_data')
        ->where('model_name', $modelno)
        ->get()
        ->getRowArray();

  

    return $result;
}









function save_brand($name, $user_id)
{
        $name = $this->db->escape($name);
    $user_id = $this->db->escape($user_id);

        $existing_brand = $this->db->table('brands')
                               ->where('name', $name)
                               ->where('user_id', $user_id)
                               ->get()
                               ->getRow();

    if ($existing_brand) {
        // Brand already exists for the user, return an error
        return "Brand '$name' already exists for this user.";
    } else {
        // Insert new brand
        $data = [
            'name' => $name,
            'user_id' => $user_id
        ];
        $this->db->table('brands')->insert($data);
        return true; // Success
    }
}





public function extract_apis($user_id, $modelno)
{
    $sql = "SELECT parameter_name FROM product_api WHERE model_name = ?";
    $query = $this->db->query($sql, [$modelno]);

    $results = $query->getResultArray();
    $paramNames = [];

    foreach ($results as $row) {
        $paramNames[] = $row['parameter_name'];
    }

    return $paramNames;
}



/************************************************************************************/


public function get_node_internet_status($node_id)
{
    $sql = "SELECT keyvalue FROM efactorproduct WHERE node_id = ?";
    $query = $this->db->query($sql, [$node_id]);

    $row = $query->getRowArray();

    if (!$row) {
        return null; // ❌ No node found
    }

    // Decode keyvalue JSON
    $keyvalue = json_decode($row['keyvalue'], true);

    if (!is_array($keyvalue) || !isset($keyvalue['internet'])) {
        return false; // ❌ internet key missing → OFFLINE
    }

    // ✅ true → ONLINE, false → OFFLINE
    return filter_var($keyvalue['internet'], FILTER_VALIDATE_BOOLEAN);
}


/***********************************************************************************/

public function get_parameter_row($modelName, $paramName)
{
    echo "<pre>📌 get_parameter_row() START\n";
    echo "Model: $modelName\n";
    echo "Param: $paramName\n";

    $sql = "SELECT * FROM product_api WHERE model_name = ? AND parameter_name = ?";
    $query = $this->db->query($sql, [$modelName, $paramName]);

    $row = $query->getRowArray();

    if ($row) {
        echo "✅ Row Found:\n";
        print_r($row);
    } else {
        echo "❌ No Row Found!\n";
    }

    echo "</pre>";

    return $row;
}


public function insert_new_parameter($modelName, $data, $param_name)
{
    echo "<pre>📌 insert_new_parameter() START\n";
    echo "Model Name: $modelName\n";
    echo "New Param Name: $param_name\n";

    echo "\nIncoming Data:\n";
    print_r($data);

    // Fetch model ID
   $query = $this->db->query("
    SELECT id, product_type 
    FROM vendorproducts 
    WHERE modelname = ?", 
    [$modelName]
);

$result = $query->getRow();

if (!$result) {
    echo "❌ vendorproducts entry NOT FOUND for model: $modelName\n</pre>";
    return false;
}

$item_id       = $result->id;
$product_type  = $result->product_type;
    echo "✅ vendorproducts ID found: $item_id\n";
    echo "✅ vendorproducts Product found: $product_type\n";


    // Overwrite fields
    $data['model_name']     = $modelName;
    $data['parameter_name'] = $param_name;
    $data['itemid']         = $item_id;
    $data['product_name'] = $product_type;

    echo "\nData BEFORE INSERT:\n";
    print_r($data);

    // Insert into product_api
    $insert = $this->db->table('product_api')->insert($data);

    echo $insert
        ? "✅ INSERT SUCCESS!\n"
        : "❌ INSERT FAILED!\n";

    echo "</pre>";

    return $insert;
}


public function copy_parameter($data)
{
    echo "<pre>📌 copy_parameter() START\n";
    echo "Incoming POST Data:\n";
    print_r($data);

    $from_model     = $data['from_model'];
    $to_model       = $data['to_model'];
    $existing_param = $data['existing_param'];
    $new_param      = $data['new_param'];

    echo "\n📌 Step 1: Fetching existing parameter row\n";

    $param_row = $this->get_parameter_row($from_model, $existing_param);

    if (empty($param_row)) {
        echo "❌ Source parameter NOT FOUND in $from_model\n</pre>";
        return false;
    }

    echo "\n📌 Original Row Retrieved:\n";
    print_r($param_row);

    // Replace name
    $param_row['parameter_name'] = $new_param;

    // Remove ID
    if (isset($param_row['id'])) {
        unset($param_row['id']);
        echo "\nRemoved 'id' to avoid primary key conflict.\n";
    }

    echo "\n📌 Row BEFORE INSERT:\n";
    print_r($param_row);

    echo "\n📌 Step 2: Inserting into destination model\n";

    $insert = $this->insert_new_parameter($to_model, $param_row, $new_param);

    echo $insert
        ? "\n✅ copy_parameter SUCCESS!\n"
        : "\n❌ copy_parameter FAILED during insert step!\n";

    echo "</pre>";

    return $insert;
}


/*************************************************************************************/

public function extract_api_default_names($user_id, $modelno)
{
    $sql = "SELECT cmd_defaultvalue, resp_defaultvalue FROM product_api WHERE model_name = ?";
    $query = $this->db->query($sql, [$modelno]);

    $results = $query->getResultArray();
    $paramNames = [];

    foreach ($results as $row) {
        // Explode comma-separated values if needed
        $values = explode(',', $row['cmd_defaultvalue']);
        
        // Clean and filter out empty entries
        foreach ($values as $val) {
            $cleaned = trim($val);
            if ($cleaned !== '' && !in_array($cleaned, $paramNames)) {
                $paramNames[] = $cleaned;
            }
        }
    }

    return $paramNames;
}




public function save_brand_edit($id, $name, $user_id)
    {
                $name = $this->db->escape($name);
        $user_id = $this->db->escape($user_id);

                $existing_brand = $this->db->table($this->table)
                                   ->where('name', $name)
                                   ->where('user_id', $user_id)
                                   ->where('id !=', $id) // Exclude the current brand ID from the check
                                   ->get()
                                   ->getRow();

        if ($existing_brand) {
            
            return "Brand '$name' already exists for this user.";
        } else {
                        $data = [
                'name' => $name
            ];
            $this->db->table($this->table)
                     ->where('id', $id)
                     ->update($data);
            return true; // Success
        }
    }

/*-------------select_app-------------------------*/

public function getUiComponentsByModelNumber($modelNumber)
    {
          return $this->asArray()
                    ->where('model_name', $modelNumber)
                    ->findAll();
    }



public function getModelsByUserId($userId)
{     
      return $this->asArray()
                ->where('user_id', $userId)
		->groupBy('model_name') // Ensures unique model numbers
                ->findAll();
}





public function getUiComponentsByModelAndPage($modelNumber, $pageNo, $user_id)
{
    $this->table = 'product_api';
    
    // CodeIgniter 4 version
    $query = $this->db->table($this->table)
                      ->where('model_name', $modelNumber)
                      ->where("FIND_IN_SET($pageNo, cmd_uipageno) > 0")
                      ->where('user_id', $user_id); // Adding user_id condition

    $results = $query->get()->getResultArray();

  

    return $results;
}





/*************************************************/

public function getModelsWithIdsByUserId($userId)
{
  
$sql = "select node_id from efactorproduct where user_id='$userId'";
return $this->db->query($sql);

}

public function getModelnos($userId)
{
  
$sql = "select modelname from vendorproducts where user_id='$userId'";
return $this->db->query($sql);

}


public function getproductname($userId)
{
  
$sql = "select category from vendorproducts where user_id='$userId'";
return $this->db->query($sql);

}




public function getparametername($userId)
{
  
$sql = "select parameter_name from product_api where user_id='$userId'";
return $this->db->query($sql);

}



/*************************************************/

 function get_modeldetails($user_id) {



$sql = "SELECT * FROM vendorproducts where user_id='$user_id'";

return $this->db->query($sql);





     }

     //get_uiddetails

      function get_uiddetails($pname) {

         $order_table = $this->db->prefixTable('items');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.serialno like '$pname%'";

         return $this->db->query($sql);

     }



     //get_itemapis
     function get_itemapis($itemid) {

         $order_table = $this->db->prefixTable('product_api');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.itemid=$itemid";

         return $this->db->query($sql);

     }

     





function getDataBynodeId($node_id) {



$sql = "SELECT * FROM efactorproduct where node_id='$node_id'";

return $this->db->query($sql);

}





public function getDisplaynodeIds($user_id)
{
    $rows = $this->db->table('efactorproduct')
        ->select('node_id, model_name, name, internet')
        ->where('user_id', $user_id)
        ->where('device_type', 'Display')
        ->get()
        ->getResultArray();

    $result = [];

    foreach ($rows as $row) {

        // Default device name
        $device_name = $row['name'];

        $nodeid     = $row['node_id'];
        $model_name = $row['model_name'];

        // Override name if model_name is valid
        if (!empty($model_name) && $model_name !== 'EFDEFLT V1.0') {

            $modelRow = $this->db->table($model_name)
                ->select('name')
                ->where('node_id', $nodeid)
                ->get()
                ->getRowArray();

            if (!empty($modelRow)) {
                $device_name = $modelRow['name'];
            }
        }

        $internet = strtolower(trim($row['internet']));
        $isOnline = in_array($internet, ['1', 'true', 'yes', 'on'], true);

        $result[] = [
            'node_id' => $row['node_id'],
            'name'    => $device_name,
            'status'  => $isOnline ? 'online' : 'offline'
        ];
    }

    return $result;
}




public function get_cats($user_id) {
    return $this->db->table('productcats')
        ->select('id, name, Product_image')
        ->where('user_id', $user_id)
        ->get();
}





function get_brands($user_id) {
    // Use query binding
    return $this->db->query("SELECT * FROM brands WHERE user_id=?", [$user_id]);
}




 function get_details_mapped() {

$order_table = $this->db->prefixTable('mapped_devices');
$sql = "SELECT $order_table.* FROM $order_table";
return $this->db->query($sql);

}




function get_details_api($modelname, $parameter_name, $product_name) {



//$sql = "SELECT * FROM `product_api` WHERE parameter_name = '$parameter_name' AND model_name = '$modelname'";

$sql = "SELECT * FROM `product_api` WHERE parameter_name = '$parameter_name' AND model_name = '$modelname' AND product_name = '$product_name'";
return $this->db->query($sql);

}



function get_prods() {

$order_table = $this->db->prefixTable('vendorproducts');
$sql = "SELECT $order_table.* FROM $order_table";
return $this->db->query($sql);

}

     
function get_details_vnsettings($vendor) {

$order_table = $this->db->prefixTable('vnsettings');
$sql = "SELECT $order_table.* FROM $order_table where vendor=$vendor";
return $this->db->query($sql);

}

/*
function get_vendorproducts($user_id) {


$sql = "SELECT * FROM vendorproducts where user_id='$user_id'";

return $this->db->query($sql);




}*/




function get_vendorproducts($user_id) {
    $sql = "SELECT * FROM vendorproducts WHERE user_id='$user_id' 
            UNION 
         SELECT * FROM vendorproducts WHERE modelname = 'EFDEFLT V1.0'";   

    return $this->db->query($sql);
}





public function savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData)
{
    $db = \Config\Database::connect();
    $builder = $db->table('api_page_linking_data');

    $data = [
        'model_name'   => $modelno,
        'page_names'   => json_encode($pageNameMap, JSON_UNESCAPED_UNICODE),
        'linking_data' => json_encode($linkingData, JSON_UNESCAPED_UNICODE),
        'created_at'   => date('Y-m-d H:i:s')
    ];


print_r($data);

    // ❌ No checking, always insert
    $builder->insert($data);

$linking_data_json = $data['linking_data'];
$linking_data = json_decode($linking_data_json, true);




foreach ($linking_data as $page => $params) {
    echo "$page:\n";

    foreach ($params as $param) {
        echo "- $param\n";

        // Escape values to prevent SQL injection
        $escapedPage = addslashes($page);
        $escapedParam = addslashes($param);
        $escapedModel = addslashes($modelno);

        // Page-specific logic
        if ($escapedPage == 'Page 1') {
            $escapedPage = 'Page1.Operation';
        }

        // Update query
        $sql = "UPDATE product_api 
                SET page_names = '$escapedPage' 
                WHERE parameter_name = '$escapedParam' 
                  AND model_name = '$escapedModel'";

        // Execute update
        $this->db->query($sql);
    }

    echo "\n";
}




return true;
}





/**********************************************************************************/

public function getModelsWithPagesByUserId($user_id, $modelno)
{
    $ui_table = $this->db->prefixTable('product_api');

    if($modelno === 'EFDEFLT V1.0'){
        $sql = "SELECT model_name, GROUP_CONCAT(page_names) AS page_names
        FROM $ui_table
        WHERE user_id = 1 AND model_name = ?
        GROUP BY model_name
        ORDER BY model_name ASC";
    
     $results = $this->db->query($sql, [$modelno])->getResultArray();
    
    // Initialize organized models
    $organizedModels = [];
    foreach ($results as $result) {
    $pagesArray = explode(',', $result['page_names']);
    $modelPages = [];
    
    foreach ($pagesArray as $pageName) {
        if (!isset($modelPages[$pageName])) {
            $modelPages[$pageName] = $pageName;
        }
    }
    
    $organizedModels[$result['model_name']] = $modelPages;
    }
    }else{
        // Query for the specified model
        $sql = "SELECT model_name, GROUP_CONCAT(page_names) AS page_names
                FROM $ui_table
                WHERE user_id = ? AND model_name = ?
                GROUP BY model_name
                ORDER BY model_name ASC";
    
        $results = $this->db->query($sql, [$user_id, $modelno])->getResultArray();
    
        // Initialize organized models
        $organizedModels = [];
        foreach ($results as $result) {
            $pagesArray = explode(',', $result['page_names']);
            $modelPages = [];
    
            foreach ($pagesArray as $pageName) {
                if (!isset($modelPages[$pageName])) {
                    $modelPages[$pageName] = $pageName;
                }
            }
    
            $organizedModels[$result['model_name']] = $modelPages;
        }
    }
    // Retrieve pages for the default model
    $defaultModel = 'EFDEFLT V1.1';
    $defaultSql = "SELECT GROUP_CONCAT(page_names) AS page_names
                   FROM $ui_table
                   WHERE model_name = ?";

    $defaultResult = $this->db->query($defaultSql, [$defaultModel])->getRow();

    if ($defaultResult && $defaultResult->page_names) {
        $pagesArray = explode(',', $defaultResult->page_names);
        $defaultModelPages = [];

        foreach ($pagesArray as $pageName) {
            if (!isset($defaultModelPages[$pageName])) {
                $defaultModelPages[$pageName] = $pageName;
            }
        }

        // Merge the default model pages into the relevant model if it exists
        if (isset($organizedModels[$modelno])) {
            $organizedModels[$modelno] = array_merge($organizedModels[$modelno], $defaultModelPages);
        } else {
            // If the specified model doesn't exist, add it
            $organizedModels[$modelno] = $defaultModelPages;
        }
    }

    return $organizedModels;
}

public function controlgetModelsWithPagesByUserId($model_number)
{
    $ui_table = $this->db->prefixTable('product_api');

    // Query for the specified model
    $sql = "SELECT model_name, GROUP_CONCAT(page_names) AS page_names
            FROM $ui_table
            WHERE model_name = ?
            GROUP BY model_name
            ORDER BY model_name ASC";

    $results = $this->db->query($sql, [$model_number])->getResultArray();

    // Initialize organized models
    $organizedModels = [];
    foreach ($results as $result) {
        $pagesArray = explode(',', $result['page_names']);
        $modelPages = [];

        foreach ($pagesArray as $pageName) {
            if (!isset($modelPages[$pageName])) {
                $modelPages[$pageName] = $pageName;
            }
        }

        $organizedModels[$result['model_name']] = $modelPages;
    }

    // Retrieve pages for the default model
    $defaultModel = 'EFDEFLT V1.1';
    $defaultSql = "SELECT GROUP_CONCAT(page_names) AS page_names
                   FROM $ui_table
                   WHERE model_name = ?";

    $defaultResult = $this->db->query($defaultSql, [$defaultModel])->getRow();

    if ($defaultResult && $defaultResult->page_names) {
        $pagesArray = explode(',', $defaultResult->page_names);
        $defaultModelPages = [];

        foreach ($pagesArray as $pageName) {
            if (!isset($defaultModelPages[$pageName])) {
                $defaultModelPages[$pageName] = $pageName;
            }
        }

        // Merge the default model pages into the relevant model if it exists
        if (isset($organizedModels[$model_number])) {
            $organizedModels[$model_number] = array_merge($organizedModels[$model_number], $defaultModelPages);
        } else {
            // If the specified model doesn't exist, add it
            $organizedModels[$model_number] = $defaultModelPages;
        }
    }

    return $organizedModels;
}


public function getConfirmModelsWithPagesByUserId($user_id, $modelno)
{
    $ui_table = $this->db->prefixTable('product_api');

   
    $sql = "SELECT model_name, 
    GROUP_CONCAT(cmd_defaultvalue) AS cmd_defaultvalue, 
    GROUP_CONCAT(resp_defaultvalue) AS resp_defaultvalue
FROM $ui_table
WHERE user_id = ? AND model_name = ?
GROUP BY model_name
ORDER BY model_name ASC";

    
    $results = $this->db->query($sql, [$user_id, $modelno])->getResultArray();
 
    return $results;
}







function extract_params($id, $user_id) { 
    /* $sql = "SELECT cmd_defaultvalue 
             FROM product_api 
             WHERE itemid = ? AND user_id = ?";   
     $query = $this->db->query($sql, [$id, $user_id]); 
     $parameter_names = []; 
     if ($query->getNumRows() > 0) { 
         foreach ($query->getResultArray() as $row) {
             $parameter_names[] = $row['cmd_defaultvalue'];
         }
     } 
     return $parameter_names;*/
     $sql6 = "SELECT * FROM product_api WHERE itemid = '$id' AND user_id = '$user_id'";
     $query2 = $this->db->query($sql6);
     $initial_values = $query2->getResult();
     
     $mapped_values = [];
     foreach ($initial_values as $row) {
         $parameter_name = $row->parameter_name;
         $cmd_defaultvalue = $row->cmd_defaultvalue;
         $resp_defaultvalue = $row->resp_defaultvalue;
          
         $cmd_fieldescription = explode(',', $row->cmd_fielddescription);
        $cmd_datatypes = explode(',', $row->cmd_datatype);
        $cmd_framesizes = explode(',', $row->cmd_framesize);
        $cmd_numberOfbytes = explode(',', $row->cmd_numberOfbytes);
        $cmd_valueinhexmin = explode(',', $row->cmd_displayvaluemin);
        $cmd_valueinhexmax = explode(',', $row->cmd_displayvaluemax);
        $cmduitype = explode(',', $row->cmd_uitype);
        $cmd_uidescriptioninout = explode(',', $row->cmd_uidescriptioninout);

        $resp_fielddescription = explode(',', $row->resp_fieldDescription);
        $resp_dataTypes = explode(',', $row->resp_dataType);
        $resp_numberOfBytes = explode(',', $row->resp_numberOfBytes);
        $resp_valueInHexMin = explode(',', $row->resp_displayValueMin);
        $resp_valueInHexMax = explode(',', $row->resp_displayValueMax);
        $respuiType = explode(',', $row->resp_uiType);
          
         $cmd_startbyteposition = explode(',', $row->cmd_startbytePosition);
         $cmd_endbyteposition = explode(',', $row->cmd_endbytePosition);
         $resp_startBytePosition = explode(',', $row->resp_startBytePosition);
         $resp_endBytePosition = explode(',', $row->resp_endBytePosition);
      
         if (!isset($mapped_values[$parameter_name])) {
             $mapped_values[$parameter_name] = [
                 'cmd' => [],
                 'resp' => []
             ];
         }
      
         foreach ($cmd_fieldescription as $index => $value) {
            if ($value === 'Data') { 
                $cmd_datatype = isset($cmd_datatypes[$index]) ? $cmd_datatypes[$index] : null;
                $cmd_framesize = isset($cmd_framesizes[$index]) ? $cmd_framesizes[$index] : null;

                $cmd_numberOfbyte = isset($cmd_numberOfbytes[$index]) ? $cmd_numberOfbytes[$index] : null;

                $valuemin_for_data_cmd = isset($cmd_valueinhexmin[$index]) ? $cmd_valueinhexmin[$index] : null;
                $valuemax_for_data_cmd = isset($cmd_valueinhexmax[$index]) ? $cmd_valueinhexmax[$index] : null;
                 
                $start_byte_cmd = isset($cmd_startbyteposition[$index]) ? $cmd_startbyteposition[$index] : null;
                $end_byte_cmd = isset($cmd_endbyteposition[$index]) ? $cmd_endbyteposition[$index] : null;

                $cmd_uitypes = isset($cmduitype[$index]) ? $cmduitype[$index] : null;
     
                $cmd_uidescriptioninouts = isset($cmd_uidescriptioninout[$index]) ? $cmd_uidescriptioninout[$index] : null;

                $cmd_default_values_array = explode(',', $cmd_defaultvalue);
                if (isset($cmd_default_values_array[$index])) {
                    $cmd_default = trim($cmd_default_values_array[$index]);
     
                    $mapped_values[$parameter_name]['cmd'][] = [
                        'cmd_datatype' => $cmd_datatype,
                        'cmd_framesize' => $cmd_framesize,
                        'cmd_numberOfbyte' => $cmd_numberOfbyte,
                        'cmd_min' => $valuemin_for_data_cmd,
                        'cmd_max' => $valuemax_for_data_cmd,
                        'cmd_default' => $cmd_default,
                        'cmd_startbyteposition' => $start_byte_cmd,
                        'cmd_endbyteposition' => $end_byte_cmd,
                        'cmd_uitype' => $cmd_uitypes,
                        'cmd_uidescriptioninout' => $cmd_uidescriptioninouts
                    ];
                }
            }
        }
      
        foreach ($resp_fielddescription as $index => $value) {
            if ($value === 'Data') { 
                $resp_dataType = isset($resp_dataTypes[$index]) ? $resp_dataTypes[$index] : null;

                $resp_numberOfByte = isset($resp_numberOfBytes[$index]) ? $resp_numberOfBytes[$index] : null;

                $valuemin_for_data_resp = isset($resp_valueInHexMin[$index]) ? $resp_valueInHexMin[$index] : null;
                $valuemax_for_data_resp = isset($resp_valueInHexMax[$index]) ? $resp_valueInHexMax[$index] : null;
                 
                $start_byte_resp = isset($resp_startBytePosition[$index]) ? $resp_startBytePosition[$index] : null;
                $end_byte_resp = isset($resp_endBytePosition[$index]) ? $resp_endBytePosition[$index] : null;

                $respuiTypes = isset($respuiType[$index]) ? $respuiType[$index] : null;
     
                $resp_default_values_array = explode(',', $resp_defaultvalue);
                if (isset($resp_default_values_array[$index])) {
                    $resp_default = trim($resp_default_values_array[$index]);
     
                    $mapped_values[$parameter_name]['resp'][] = [
                        'resp_dataType' => $resp_dataType,
                        'resp_numberOfByte' => $resp_numberOfByte,
                        'resp_min' => $valuemin_for_data_resp,
                        'resp_max' => $valuemax_for_data_resp,
                        'resp_default' => $resp_default,
                        'resp_startbyteposition' => $start_byte_resp,
                        'resp_endbyteposition' => $end_byte_resp,
                        'resp_uiType' => $respuiTypes
                    ];
                }
            }
        }
     }
     
     return $mapped_values;
 }






function specificationsapi($data, $user_id) {
    $table = $this->db->prefixTable('autotest');
    $results = [];  
  
   $values = [ 
        $data['id'], 
        $data['modelno'], 
        $data['cmd_values'], 
        $data['cmd_time'],     
        $data['cmd_startbyte'], 
        $data['cmd_min'],          
        $data['cmd_max'], 
        $data['cmd_default'],    
        $data['cmd_param_names'],
        $data['resp_min_value'], 
        $data['resp_max_value'],      
        $data['resp_default'],  
        $data['resp_param_names'],   
    ];  
$id = $data['id'];
$modelno = $data['modelno'];
$cmd_value = $data['cmd_values'];
$cmd_time = $data['cmd_time'];
$cmd_startbyte = $data['cmd_startbyte'];
$cmd_min = $data['cmd_min'];
$cmd_max = $data['cmd_max'];
$cmd_default = $data['cmd_default']; 
$cmd_param_names = $data['cmd_param_names']; 
$resp_min_value = $data['resp_min_value'];
$resp_max_value = $data['resp_max_value'];
$resp_default = $data['resp_default']; 
$resp_startbyteposition = $data['resp_startbyteposition']; 
$resp_param_names = $data['resp_param_names'];
 
function flattenArray($array) { 
    if (!is_array($array)) {
        return '';   
    } 
    $result = []; 
    foreach ($array as $key => $values) { 
        if (is_array($values)) { 
            $values = implode(',', $values);
        } 
        if (empty($values)) {
            $values = ' ';
        } 
        $result[] = '[' . $values . ']';  
    }  
    return implode(',', $result);  
}

//$cmd_params = array_keys($cmd_values);  
//$resp_multi_param = array_keys($resp_min_value); 
 
$cmd_values = flattenArray($cmd_value);
$cmd_times = flattenArray($cmd_time); 
$cmd_startbytes = flattenArray($cmd_startbyte);
$cmd_mins = flattenArray($cmd_min); 
$cmd_maxs = flattenArray($cmd_max);
$cmd_defaults = flattenArray($cmd_default);  
$cmd_param_name = flattenArray($cmd_param_names); 
$resp_min_values = flattenArray($resp_min_value);
$resp_max_values = flattenArray($resp_max_value);  
//$cmd_param_names = flattenArray($cmd_params); 
$resp_defaults = flattenArray($resp_default);  
$resp_startbytepositions = flattenArray($resp_startbyteposition);  
//$resp_params = flattenArray($resp_multi_param); 
$resp_param_name = flattenArray($resp_param_names);  
  
 
$sql = "INSERT INTO $table SET  
    itemid = '$id', 
    modelno = '$modelno', 
    cmd_param_names = '$cmd_param_name', 
    cmd_values = '$cmd_values', 
    cmd_time = '$cmd_times', 
    cmd_startbyte = '$cmd_startbytes', 
    cmd_min = '$cmd_mins', 
    cmd_max = '$cmd_maxs', 
    cmd_default = '$cmd_defaults', 
    resp_param_names = '$resp_param_name',  
    resp_min_value = '$resp_min_values', 
    resp_max_value = '$resp_max_values', 
    resp_default = '$resp_defaults', 
    resp_startbyteposition = '$resp_startbytepositions', 
    user_id = '$user_id'";
 echo $sql; 
    $result = $this->db->query($sql, $values);
    
    if (!$result) { 
        
        log_message('error', 'Database error: ' . $this->db->error()['message']);
        $results[] = false;   
    } else { 
        $results[] = true;  
    }  
    return $results;  
}





public function getConfirmModelsWithPagesByUserId2($user_id, $modelno)
{
    $ui_table = $this->db->prefixTable('product_api');

   
    $sql = "SELECT model_name 
        FROM product_api 
        WHERE user_id = ? AND model_name = ?";
    
    $results = $this->db->query($sql, [$user_id, $modelno])->getResultArray();
 
    return $results;
}



public function Confirm($modelsWithPages,$modelsWithPages2){
   

    $default_params = [];
    $default_params = ["  `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `device_type` VARCHAR(25) NOT NULL,
  `brandlogo` VARCHAR(25),
   `auto_testing` VARCHAR(25),
  `product_image` VARCHAR(25),
  `model_name` VARCHAR(25),
  `model_number` VARCHAR(25),
  `number_of_pages` INT,
  `page_names` VARCHAR(25),
  `payload` VARCHAR(150),
  `node_id` VARCHAR(50),
  `module_type` VARCHAR(25),
  `client_name` VARCHAR(25),
  `client_id` VARCHAR(25),
  `developer_name` VARCHAR(25),
  `developer_id` VARCHAR(25),
  `user_name` VARCHAR(25),
  `user_id` VARCHAR(25),
  `access_token` VARCHAR(1024),
  `request_id` VARCHAR(25),
  `mac` VARCHAR(25),
  `add_gateway` VARCHAR(25),
  `is_added` VARCHAR(10),
  `secret_key` VARCHAR(225),
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `TZ_properties` VARCHAR(10),
  `BR_properties` VARCHAR(10),
  `UMI_properties` VARCHAR(10),
  `EC_properties` VARCHAR(10),
  `CM_properties` VARCHAR(10),
  `TU_properties` VARCHAR(10),
  `LC_properties` VARCHAR(10),
  `display_properties` VARCHAR(10),
  `req_status` VARCHAR(25),
  `mob_device_token` VARCHAR(1024),
  `ipaddress` VARCHAR(25),
  `current_devicetype` VARCHAR(25),
  `ping` VARCHAR(10),
  `latitude` VARCHAR(50),
  `longitude` VARCHAR(50),
  `signin_latitude` VARCHAR(25),
  `signin_longitude` VARCHAR(25),
  `node_latitude` VARCHAR(25),
  `node_longitude` VARCHAR(25),
  `phone` VARCHAR(25),
  `email` VARCHAR(25),
  `app_version` VARCHAR(25),
  `fw_version` VARCHAR(25),
  `add_node_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ssid` VARCHAR(25),
  `wifi_pwd` VARCHAR(25),
  `auto_test` VARCHAR(10),
  `selected` INT(10),
  `SUID` VARCHAR(25),
  `DUID` VARCHAR(25),
  `UDP_CMD` VARCHAR(25),
  `UDP_ACK` VARCHAR(25),
  `MEDIA` VARCHAR(25),
  `Frame` VARCHAR(25),
  `FID` VARCHAR(25),
  `UserID` VARCHAR(25),
  `SDUID_Enable` VARCHAR(25),
  `UID_DATA` VARCHAR(25),
    `baudrate` VARCHAR(25),
    `uartmsgid` VARCHAR(25),
    `errorcode` VARCHAR(25)," ];
    //  print_r($default_params);
    //print_r($params);
     $columns = implode(', ', $default_params);
     $individualParams = explode(',', $default_params[0]);
     $individualParams = array_filter(array_map('trim', $individualParams));
     //print_r($individualParams);

     $parameterNames1 = explode(',', $modelsWithPages[0]['cmd_defaultvalue']);
     $parameterNames2 = explode(',', $modelsWithPages[0]['resp_defaultvalue']);
     $productName = $modelsWithPages2[0]['model_name'] ?? null; 
      $parameterNames = array_merge($parameterNames1, $parameterNames2);
     $parameterNames = array_filter(array_unique($parameterNames));
     $parameterNames = array_values($parameterNames);
//echo $productName; 

    $columnDefinitions = array_map(function($col) {
        $col = trim($col);
        $escapedCol = htmlspecialchars($col, ENT_QUOTES, 'UTF-8');
        return "`$escapedCol` VARCHAR(25)";
    }, $parameterNames);
    
    // Combine the column definitions into a single string
    $columnDefinitionsString = implode(', ', $columnDefinitions);
    //echo $columnDefinitionsString;
      $titles22 = array_merge($individualParams, $columnDefinitions);
     $titles11 = array_filter(array_unique($titles22));
     $titles00 = array_values($titles11);
     $titles = implode(', ', $titles00);
  // print_r($titles);
  //  $titles = $columns . ', ' . $columnDefinitionsString;
    // echo $titles;
   // $sql = "CREATE TABLE $productName ($columns $columnDefinitionsString)";
   
   $tableExistsQuery = "SHOW TABLES LIKE '$productName'";
   $tableExists = $this->db->query($tableExistsQuery);
   $tableExistsResult = $tableExists->getRow(); 
   //print_r($tableExistsResult);
   if ($tableExistsResult > 0) {
       
       $existingColumnsQuery = "SHOW COLUMNS FROM $productName";
       $existingColumnsResult = $this->db->query($existingColumnsQuery);
       $tableExistsResult = $existingColumnsResult->getRow(); 
       // print_r($existingColumnsResult);
       $existingColumns = [];
       foreach ($existingColumnsResult->getResultArray() as $row) {
           $existingColumns[] = $row['Field'];
       } 
       //print_r($existingColumns);
       $titlesArray = explode(',', $titles);
       $newColumns = [];
      // print_r($titlesArray);
 
       $columnNames = array_map(function($title) {
        preg_match('/`(.*?)`/', $title, $matches);
        return $matches[1] ?? null;  
    }, $titlesArray);
 //print_r($existingColumns);
 //print_r($columnNames);
   // $newColumns = array_diff($columnNames, $existingColumns);
   $existingColumnsLower = array_map('strtolower', $existingColumns);
$newColumns = array_filter($columnNames, function($column) use ($existingColumnsLower) {
    return !in_array(strtolower($column), $existingColumnsLower);
});
    
     //print_r($newColumns); 
    if (!empty($newColumns)) {
        foreach ($newColumns as $newColumn) {
            $addColumnQuery = "ALTER TABLE `$productName` ADD COLUMN `$newColumn` VARCHAR(25)"; 
            //echo $newColumn;
            $this->db->query($addColumnQuery);
        }
        return "Parameters updated successfully";
    }else{
        return "Parameters already updated";
    }
} else { 
   $sql = "CREATE TABLE `$productName` ($titles)";
       echo $sql;
    $result = $this->db->query($sql);
    if ($result) {
        return true;  
    } else {
        return false;  
    }

 }

}


/*----------------------------------------------------------------------------------*/


public function Confirm_without_pages($modelno)
{
    // Sanitize table name (avoid SQL injection by allowing only alphanumeric + underscore)
  //  $modelno = preg_replace('/[^A-Za-z0-9_]/', '', $modelno);
    $modelno = preg_replace('/[^A-Za-z0-9_ ]/', '', $modelno);

    $sql = "
        CREATE TABLE IF NOT EXISTS `$modelno` (
          `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `device_type` VARCHAR(25) NOT NULL,
          `brandlogo` VARCHAR(25),
          `auto_testing` VARCHAR(25),
          `product_image` VARCHAR(25),
          `model_name` VARCHAR(25),
          `model_number` VARCHAR(25),
          `number_of_pages` INT,
          `page_names` VARCHAR(25),
          `payload` VARCHAR(150),
          `node_id` VARCHAR(50),
          `module_type` VARCHAR(25),
          `client_name` VARCHAR(25),
          `client_id` VARCHAR(25),
          `developer_name` VARCHAR(25),
          `developer_id` VARCHAR(25),
          `user_name` VARCHAR(25),
          `user_id` VARCHAR(25),
          `access_token` VARCHAR(1024),
          `request_id` VARCHAR(25),
          `mac` VARCHAR(25),
          `add_gateway` VARCHAR(25),
          `is_added` VARCHAR(10),
          `secret_key` VARCHAR(225),
          `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `TZ_properties` VARCHAR(10),
          `BR_properties` VARCHAR(10),
          `UMI_properties` VARCHAR(10),
          `EC_properties` VARCHAR(10),
          `CM_properties` VARCHAR(10),
          `TU_properties` VARCHAR(10),
          `LC_properties` VARCHAR(10),
          `display_properties` VARCHAR(10),
          `req_status` VARCHAR(25),
          `mob_device_token` VARCHAR(1024),
          `ipaddress` VARCHAR(25),
          `current_devicetype` VARCHAR(25),
          `ping` VARCHAR(10),
          `latitude` VARCHAR(50),
          `longitude` VARCHAR(50),
          `signin_latitude` VARCHAR(25),
          `signin_longitude` VARCHAR(25),
          `node_latitude` VARCHAR(25),
          `node_longitude` VARCHAR(25),
          `phone` VARCHAR(25),
          `email` VARCHAR(25),
          `app_version` VARCHAR(25),
          `fw_version` VARCHAR(25),
          `add_node_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `ssid` VARCHAR(25),
          `wifi_pwd` VARCHAR(25),
          `auto_test` VARCHAR(10),
          `selected` INT(10),
          `SUID` VARCHAR(25),
          `DUID` VARCHAR(25),
          `UDP_CMD` VARCHAR(25),
          `UDP_ACK` VARCHAR(25),
          `MEDIA` VARCHAR(25),
          `Frame` VARCHAR(25),
          `FID` VARCHAR(25),
          `UserID` VARCHAR(25),
          `SDUID_Enable` VARCHAR(25),
          `UID_DATA` VARCHAR(25)
        )
    ";

    // Execute the SQL
    return $this->db->query($sql);
}









/*************************************************************************************/


public function insert_main_data($data) {
        // Insert main form data into the database
        $this->db->insert('product_api', $data);
        // Return the ID of the inserted row
        return $this->db->insert_id();
    }


public function insert_row_data($main_insert_id, $row_data) {
        // Add main form data ID to the row data
        $row_data['main_form_data_id'] = $main_insert_id;
        // Insert row data into the database
        $this->db->insert('product_api', $row_data);
    }


/*********************************save_api*******************************************/


function saveapi($data, $additional_data, $respframe_data, $errorframe_data, $user_id) {
    $table = $this->db->prefixTable('product_api');
    $results = []; // Array to store query results
 
    $existing_product = $this->db->table('product_api')
                      ->where('parameter_name', $data['cmd_parametername'],)
                      ->where('model_name', $data['modelname'],)
                      ->where('user_id', $user_id)
                      ->get()
                      ->getRow();
  
 if ($existing_product) { 
    return "parameter_name already exists in this model.";
} else { 
$sql = "INSERT INTO $table (brand_name, product_name, model_name, product_description, number_of_pages,sequence_no, page_names, date, itemid, parameter_name, cmd_dataflow, cmd_framesize, cmd_noofrows, cmd_frametrigger, cmd_multipleui, cmd_triggerui,resp_dataflow, resp_framesize, resp_noofrows, resp_frametrigger, resp_multipleui,resp_triggerui, error_dataflow, error_framesize, error_noofrows, error_frametrigger, error_multipleui,error_triggerui, 
cmd_startBytePosition, cmd_endBytePosition, cmd_fieldDescription, cmd_numberOfBytes, cmd_bitSize, cmd_dataType, cmd_valueDefinition, cmd_displayValueMin, cmd_displayValueMax, cmd_valueInHexMin, cmd_valueInHexMax, cmd_uiType, cmd_uiId, cmd_asperui,cmd_sno_asperui,cmd_asperui_values, cmd_uiPageNo, cmd_uiDescriptionInOut, cmd_sliderStepValue, cmd_defaultvalue, cmd_developerNotes, 
resp_startBytePosition,resp_endBytePosition, resp_fieldDescription, resp_numberOfBytes, resp_bitSize, resp_dataType, resp_valueDefinition, resp_displayValueMin, resp_displayValueMax, resp_valueInHexMin, resp_valueInHexMax, resp_uiType, resp_uiId, resp_asperui, resp_sno_asperui, resp_asperui_values, resp_uiPageNo, resp_uiDescriptionInOut, resp_sliderStepValue, resp_defaultvalue, resp_developerNotes, 
error_startBytePosition, error_endBytePosition, error_fieldDescription, error_numberOfBytes, error_bitSize, error_dataType, error_valueDefinition, error_displayValueMin, error_displayValueMax, error_valueInHexMin, error_valueInHexMax, error_uiType, error_uiId, error_asperui, error_sno_asperui, error_asperui_values,error_uiPageNo, error_uiDescriptionInOut, error_sliderStepValue, error_defaultvalue, error_developerNotes, user_id) 
VALUES(?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
}





    $cmd_startBytePosition = [];
    $cmd_endBytePosition = [];
    $cmd_fieldDescription = [];
    $cmd_numberOfBytes = [];
    $cmd_bitSize = [];
    $cmd_dataType = [];
    $cmd_valueDefinition = [];
    $cmd_displayValueMin = [];
    $cmd_displayValueMax = [];
    $cmd_valueInHexMin = [];
    $cmd_valueInHexMax = [];
    $cmd_uiType = [];
    $cmd_uiId = [];
    $cmd_asperui = [];
    $cmd_sno_asperui = [];
    $cmd_asperuivalue = [];
    $cmd_uiPageNo = [];
    $cmd_uiDescriptionInOut = [];
    $cmd_sliderStepValue = [];
    $cmd_defaultvalue = [];
    $cmd_developerNotes = [];

    $resp_startBytePosition = [];
    $resp_endBytePosition = [];
    $resp_fieldDescription = [];
    $resp_numberOfBytes = [];
    $resp_bitSize = [];
    $resp_dataType = [];
    $resp_valueDefinition = [];
    $resp_displayValueMin = [];
    $resp_displayValueMax = [];
    $resp_valueInHexMin = [];
    $resp_valueInHexMax = [];
    $resp_uiType = [];
    $resp_uiId = [];
    $resp_asperui = [];
    $resp_sno_asperui = [];
    $resp_asperuivalue = [];
    $resp_uiPageNo = [];
    $resp_uiDescriptionInOut = [];
    $resp_sliderStepValue = [];
    $resp_defaultvalue = [];
    $resp_developerNotes = [];


    $error_startBytePosition = [];
    $error_endBytePosition = [];
    $error_fieldDescription = [];
    $error_numberOfBytes = [];
    $error_bitSize = [];
    $error_dataType = [];
    $error_valueDefinition = [];
    $error_displayValueMin = [];
    $error_displayValueMax = [];
    $error_valueInHexMin = [];
    $error_valueInHexMax = [];
    $error_uiType = [];
    $error_uiId = [];
    $error_asperui = [];
    $error_sno_asperui = [];
    $error_asperuivalue = [];
    $error_uiPageNo = [];
    $error_uiDescriptionInOut = [];
    $error_sliderStepValue = [];
    $error_defaultvalue = [];
    $error_developerNotes = [];



    // Loop through additional data and extract values
    foreach ($additional_data as $cmd_data) {
        $cmd_startBytePosition[] = $cmd_data['cmd_startBytePosition'];
        $cmd_endBytePosition[] = $cmd_data['cmd_endBytePosition'];
        $cmd_fieldDescription[] = $cmd_data['cmd_fieldDescription'];
        $cmd_numberOfBytes[] = $cmd_data['cmd_numberOfBytes'];
        $cmd_bitSize[] = $cmd_data['cmd_bitSize'];
        $cmd_dataType[] = $cmd_data['cmd_dataType'];
        $cmd_valueDefinition[] = $cmd_data['cmd_valueDefinition'];
        $cmd_displayValueMin[] = $cmd_data['cmd_displayValueMin'];
        $cmd_displayValueMax[] = $cmd_data['cmd_displayValueMax'];
        $cmd_valueInHexMin[] = $cmd_data['cmd_valueInHexMin'];
        $cmd_valueInHexMax[] = $cmd_data['cmd_valueInHexMax'];
        $cmd_uiType[] = $cmd_data['cmd_uiType'];
        $cmd_uiId[] = $cmd_data['cmd_uiId'];
        $cmd_asperui[] = $cmd_data['cmd_asperui'];
        $cmd_sno_asperui[] = $cmd_data['cmd_sno_asperui'];
        $cmd_asperuivalue[] = $cmd_data['cmd_asperuivalue'];
        $cmd_uiPageNo[] = $cmd_data['cmd_uiPageNo'];
        $cmd_uiDescriptionInOut[] = $cmd_data['cmd_uiDescriptionInOut'];
        $cmd_sliderStepValue[] = $cmd_data['cmd_sliderStepValue'];
        $cmd_defaultvalue[] = $cmd_data['cmd_defaultvalue'];
        $cmd_developerNotes[] = $cmd_data['cmd_developerNotes'];
    }

    // Convert arrays to comma-separated strings
    $cmd_startBytePosition = implode(',', $cmd_startBytePosition);
    $cmd_endBytePosition = implode(',', $cmd_endBytePosition);
    $cmd_fieldDescription = implode(',', $cmd_fieldDescription);
    $cmd_numberOfBytes = implode(',', $cmd_numberOfBytes);
    $cmd_bitSize = implode(',', $cmd_bitSize);
    $cmd_dataType = implode(',', $cmd_dataType);
    $cmd_valueDefinition = implode(',', $cmd_valueDefinition);
    $cmd_displayValueMin = implode(',', $cmd_displayValueMin);
    $cmd_displayValueMax = implode(',', $cmd_displayValueMax);
    $cmd_valueInHexMin = implode(',', $cmd_valueInHexMin);
    $cmd_valueInHexMax = implode(',', $cmd_valueInHexMax);
    $cmd_uiType = implode(',', $cmd_uiType);
    $cmd_uiId = implode(',', $cmd_uiId);
    $cmd_asperui = implode(',', $cmd_asperui);
    $cmd_sno_asperui = implode(',', $cmd_sno_asperui);
    $cmd_asperuivalue = implode(',', $cmd_asperuivalue);
    $cmd_uiPageNo = implode(',', $cmd_uiPageNo);
    $cmd_uiDescriptionInOut = implode(',', $cmd_uiDescriptionInOut);
    $cmd_sliderStepValue = implode(',', $cmd_sliderStepValue);
    $cmd_defaultvalue = implode(',', $cmd_defaultvalue);
    $cmd_developerNotes = implode(',', $cmd_developerNotes);



if (is_array($respframe_data) && !empty($respframe_data)) {
    foreach ($respframe_data as $resp_data) {
        $resp_startBytePosition[] = $resp_data['resp_startBytePosition'];
        $resp_endBytePosition[] = $resp_data['resp_endBytePosition'];
        $resp_fieldDescription[] = $resp_data['resp_fieldDescription'];
        $resp_numberOfBytes[] = $resp_data['resp_numberOfBytes'];
        $resp_bitSize[] = $resp_data['resp_bitSize'];
        $resp_dataType[] = $resp_data['resp_dataType'];
        $resp_valueDefinition[] = $resp_data['resp_valueDefinition'];
        $resp_displayValueMin[] = $resp_data['resp_displayValueMin'];
        $resp_displayValueMax[] = $resp_data['resp_displayValueMax'];
        $resp_valueInHexMin[] = $resp_data['resp_valueInHexMin'];
        $resp_valueInHexMax[] = $resp_data['resp_valueInHexMax'];
        $resp_uiType[] = $resp_data['resp_uiType'];
        $resp_uiId[] = $resp_data['resp_uiId'];
        $resp_asperui[] = $resp_data['resp_asperui'];
        $resp_sno_asperui[] = $resp_data['resp_sno_asperui'];
        $resp_asperuivalue[] = $resp_data['resp_asperuivalue'];
        $resp_uiPageNo[] = $resp_data['resp_uiPageNo'];
        $resp_uiDescriptionInOut[] = $resp_data['resp_uiDescriptionInOut'];
        $resp_sliderStepValue[] = $resp_data['resp_sliderStepValue'];
        $resp_defaultvalue[] = $resp_data['resp_defaultvalue'];
        $resp_developerNotes[] = $resp_data['resp_developerNotes'];
    }

    
    $resp_startBytePosition = implode(',', $resp_startBytePosition);
    $resp_endBytePosition = implode(',', $resp_endBytePosition);
    $resp_fieldDescription = implode(',', $resp_fieldDescription);
    $resp_numberOfBytes = implode(',', $resp_numberOfBytes);
    $resp_bitSize = implode(',', $resp_bitSize);
    $resp_dataType = implode(',', $resp_dataType);
    $resp_valueDefinition = implode(',', $resp_valueDefinition);
    $resp_displayValueMin = implode(',', $resp_displayValueMin);
    $resp_displayValueMax = implode(',', $resp_displayValueMax);
    $resp_valueInHexMin = implode(',', $resp_valueInHexMin);
    $resp_valueInHexMax = implode(',', $resp_valueInHexMax);
    $resp_uiType = implode(',', $resp_uiType);
    $resp_uiId = implode(',', $resp_uiId);
    $resp_asperui = implode(',', $resp_asperui);
    $resp_sno_asperui = implode(',', $resp_sno_asperui);
    $resp_asperuivalue = implode(',', $resp_asperuivalue);
    $resp_uiPageNo = implode(',', $resp_uiPageNo);
    $resp_uiDescriptionInOut = implode(',', $resp_uiDescriptionInOut);
    $resp_sliderStepValue = implode(',', $resp_sliderStepValue);
    $resp_defaultvalue = implode(',', $resp_defaultvalue);
    $resp_developerNotes = implode(',', $resp_developerNotes);

} else {
    // Handle the case when $respframe_data is not iterable
   // echo "Response frame data is empty or not an array.";


$resp_startBytePosition = '';
    $resp_endBytePosition = '';
    $resp_fieldDescription = '';
    $resp_numberOfBytes = '';
    $resp_bitSize = '';
    $resp_dataType = '';
    $resp_valueDefinition = '';
    $resp_displayValueMin = '';
    $resp_displayValueMax = '';
    $resp_valueInHexMin = '';
    $resp_valueInHexMax = '';
    $resp_uiType = '';
    $resp_uiId = '';
    $resp_asperui = '';
    $resp_sno_asperui = '';
    $resp_asperuivalue = '';
    $resp_uiPageNo = '';
    $resp_uiDescriptionInOut = '';
    $resp_sliderStepValue = '';
    $resp_defaultvalue = '';
    $resp_developerNotes = '';

}




if (is_array($errorframe_data) && !empty($errorframe_data)) {
    foreach ($errorframe_data as $error_data) {
        $error_startBytePosition[] = $error_data['error_startBytePosition'];
        $error_endBytePosition[] = $error_data['error_endBytePosition'];
        $error_fieldDescription[] = $error_data['error_fieldDescription'];
        $error_numberOfBytes[] = $error_data['error_numberOfBytes'];
        $error_bitSize[] = $error_data['error_bitSize'];
        $error_dataType[] = $error_data['error_dataType'];
        $error_valueDefinition[] = $error_data['error_valueDefinition'];
        $error_displayValueMin[] = $error_data['error_displayValueMin'];
        $error_displayValueMax[] = $error_data['error_displayValueMax'];
        $error_valueInHexMin[] = $error_data['error_valueInHexMin'];
        $error_valueInHexMax[] = $error_data['error_valueInHexMax'];
        $error_uiType[] = $error_data['error_uiType'];
        $error_uiId[] = $error_data['error_uiId'];
        $error_asperui[] = $error_data['error_asperui'];
        $error_sno_asperui[] = $error_data['error_sno_asperui'];
        $error_asperuivalue[] = $error_data['error_asperuivalue'];
        $error_uiPageNo[] = $error_data['error_uiPageNo'];
        $error_uiDescriptionInOut[] = $error_data['error_uiDescriptionInOut'];
        $error_sliderStepValue[] = $error_data['error_sliderStepValue'];
        $error_defaultvalue[] = $error_data['error_defaultvalue'];
        $error_developerNotes[] = $error_data['error_developerNotes'];
    }

    
    $error_startBytePosition = implode(',', $error_startBytePosition);
    $error_endBytePosition = implode(',', $error_endBytePosition);
    $error_fieldDescription = implode(',', $error_fieldDescription);
    $error_numberOfBytes = implode(',', $error_numberOfBytes);
    $error_bitSize = implode(',', $error_bitSize);
    $error_dataType = implode(',', $error_dataType);
    $error_valueDefinition = implode(',', $error_valueDefinition);
    $error_displayValueMin = implode(',', $error_displayValueMin);
    $error_displayValueMax = implode(',', $error_displayValueMax);
    $error_valueInHexMin = implode(',', $error_valueInHexMin);
    $error_valueInHexMax = implode(',', $error_valueInHexMax);
    $error_uiType = implode(',', $error_uiType);
    $error_uiId = implode(',', $error_uiId);
    $error_asperui = implode(',', $error_asperui);
    $error_sno_asperui = implode(',', $error_sno_asperui);
    $error_asperuivalue = implode(',', $error_asperuivalue);
    $error_uiPageNo = implode(',', $error_uiPageNo);
    $error_uiDescriptionInOut = implode(',', $error_uiDescriptionInOut);
    $error_sliderStepValue = implode(',', $error_sliderStepValue);
    $error_defaultvalue = implode(',', $error_defaultvalue);
    $error_developerNotes = implode(',', $error_developerNotes);


} else {
    // Handle the case when $respframe_data is not iterable
   // echo "error frame data is empty or not an array.";

     $error_startBytePosition = '';
    $error_endBytePosition = '';
    $error_fieldDescription = '';
    $error_numberOfBytes = '';
    $error_bitSize = '';
    $error_dataType = '';
    $error_valueDefinition = '';
    $error_displayValueMin = '';
    $error_displayValueMax = '';
    $error_valueInHexMin = '';
    $error_valueInHexMax = '';
    $error_uiType = '';
    $error_uiId = '';
    $error_asperui = '';
    $error_sno_asperui = '';
    $error_asperuivalue = '';
    $error_uiPageNo = '';
    $error_uiDescriptionInOut = '';
    $error_sliderStepValue = '';
    $error_defaultvalue = '';
    $error_developerNotes = '';


}


    // Construct the values array
   $values = [
        
        $data['brandname'],
      //  $data['brandlogo'],
        $data['productname'],
      //  $data['imageDropdown'],
        $data['modelname'],
        $data['productdescription'],
        $data['numberofpages'],
        $data['Sequence'],
        $data['pagenames'],
        $data['date'],
        $data['itemid'], // Add itemid here



        $data['cmd_parametername'],
        $data['cmd_data_flow_direction'],
        $data['cmd_framesizeinbytes'],  
        $data['cmd_noofrows'],
         $data['cmd_frametrigger'],
         $data['cmd_multipleui'],
          $data['cmd_triggerui'],
        $data['resp_data_flow_direction'],
        $data['resp_framesizeinbytes'],
         $data['resp_noofrows'],
        $data['resp_frametrigger'],
        $data['resp_multipleui'],
        $data['resp_triggerui'],
        $data['error_data_flow_direction'],
        $data['error_framesizeinbytes'],
         $data['error_noofrows'],
        $data['error_frametrigger'],
        $data['error_multipleui'],
        $data['error_triggerui'],


     
        $cmd_startBytePosition,
        $cmd_endBytePosition,
        $cmd_fieldDescription,
        $cmd_numberOfBytes,
        $cmd_bitSize,
        $cmd_dataType,
        $cmd_valueDefinition,
        $cmd_displayValueMin,
        $cmd_displayValueMax,
        $cmd_valueInHexMin,
        $cmd_valueInHexMax,
        $cmd_uiType,
        $cmd_uiId,
        $cmd_asperui,
        $cmd_sno_asperui,
        $cmd_asperuivalue,
        $cmd_uiPageNo,     
        $cmd_uiDescriptionInOut,
        $cmd_sliderStepValue,
        $cmd_defaultvalue,
        $cmd_developerNotes,
        $resp_startBytePosition,
        $resp_endBytePosition,
        $resp_fieldDescription,
        $resp_numberOfBytes,
        $resp_bitSize,
        $resp_dataType,
        $resp_valueDefinition,
        $resp_displayValueMin,
        $resp_displayValueMax,
        $resp_valueInHexMin,
        $resp_valueInHexMax,
        $resp_uiType,
        $resp_uiId,
        $resp_asperui,
        $resp_sno_asperui,
        $resp_asperuivalue,
        $resp_uiPageNo,     
        $resp_uiDescriptionInOut,
        $resp_sliderStepValue,
        $resp_defaultvalue,
        $resp_developerNotes,
        $error_startBytePosition,
        $error_endBytePosition,
        $error_fieldDescription,
        $error_numberOfBytes,
        $error_bitSize,
        $error_dataType,
        $error_valueDefinition,
        $error_displayValueMin,
        $error_displayValueMax,
        $error_valueInHexMin,
        $error_valueInHexMax,
        $error_uiType,
        $error_uiId,
        $error_asperui,
        $error_sno_asperui,
        $error_asperuivalue,
        $error_uiPageNo,       
        $error_uiDescriptionInOut,
        $error_sliderStepValue,
        $error_defaultvalue,
        $error_developerNotes,
        $user_id
        


    ];





    // Execute the query
    $result = $this->db->query($sql, $values);

    // Check for database errors
    if (!$result) {
        // Log the database error
        log_message('error', 'Database error: ' . $this->db->error()['message']);
        $results[] = false; // Store false in the results array indicating failure
    } else {
        $results[] = true; // Store true in the results array indicating success
    }

    



    return $results; 
}
/****************************saveapi_edit********************************/


function saveapi_edit($data, $additional_data, $respframe_data, $errorframe_data) {
    
    $table = $this->db->prefixTable('product_api');
    $results = []; 


    $cmd_startBytePosition = [];
    $cmd_endBytePosition = [];
    $cmd_fieldDescription = [];
    $cmd_numberOfBytes = [];
    $cmd_bitSize = [];
    $cmd_dataType = [];
    $cmd_valueDefinition = [];
    $cmd_displayValueMin = [];
    $cmd_displayValueMax = [];
    $cmd_valueInHexMin = [];
    $cmd_valueInHexMax = [];
    $cmd_uiType = [];
    $cmd_uiId = [];
    $cmd_uiPageNo = [];
    $cmd_asperui = [];
    $cmd_sno_asperui = [];
    $cmd_asperuivalue = [];
    $cmd_uiDescriptionInOut = [];
    $cmd_sliderStepValue = [];
    $cmd_defaultvalue = [];
    $cmd_developerNotes = [];
    $resp_startBytePosition = [];
    $resp_endBytePosition = [];
    $resp_fieldDescription = [];
    $resp_numberOfBytes = [];
    $resp_bitSize = [];
    $resp_dataType = [];
    $resp_valueDefinition = [];
    $resp_displayValueMin = [];
    $resp_displayValueMax = [];
    $resp_valueInHexMin = [];
    $resp_valueInHexMax = [];
    $resp_uiType = [];
    $resp_uiId = [];
    $resp_uiPageNo = [];
    $resp_asperui = [];
    $resp_sno_asperui = [];
    $resp_asperuivalue = [];
    $resp_uiDescriptionInOut = [];
    $resp_sliderStepValue = [];
    $resp_defaultvalue = [];
    $resp_developerNotes = [];
    $error_startBytePosition = [];
    $error_endBytePosition = [];
    $error_fieldDescription = [];
    $error_numberOfBytes = [];
    $error_bitSize = [];
    $error_dataType = [];
    $error_valueDefinition = [];
    $error_displayValueMin = [];
    $error_displayValueMax = [];
    $error_valueInHexMin = [];
    $error_valueInHexMax = [];
    $error_uiType = [];
    $error_uiId = [];
    $error_uiPageNo = [];
    $error_asperui = [];
    $error_sno_asperui = [];
    $error_asperuivalue = [];
    $error_uiDescriptionInOut = [];
    $error_sliderStepValue = [];
    $error_defaultvalue = [];
    $error_developerNotes = [];

    foreach ($additional_data as $cmd_data) {
        $cmd_startBytePosition[] = $cmd_data['cmd_startBytePosition'];
        $cmd_endBytePosition[] = $cmd_data['cmd_endBytePosition'];
        $cmd_fieldDescription[] = $cmd_data['cmd_fieldDescription'];
        $cmd_numberOfBytes[] = $cmd_data['cmd_numberOfBytes'];
        $cmd_bitSize[] = $cmd_data['cmd_bitSize'];
        $cmd_dataType[] = $cmd_data['cmd_dataType'];
        $cmd_valueDefinition[] = $cmd_data['cmd_valueDefinition'];
        $cmd_displayValueMin[] = $cmd_data['cmd_displayValueMin'];
        $cmd_displayValueMax[] = $cmd_data['cmd_displayValueMax'];
        $cmd_valueInHexMin[] = $cmd_data['cmd_valueInHexMin'];
        $cmd_valueInHexMax[] = $cmd_data['cmd_valueInHexMax'];
        $cmd_uiType[] = $cmd_data['cmd_uiType'];
        $cmd_uiId[] = $cmd_data['cmd_uiId'];
        $cmd_uiPageNo[] = $cmd_data['cmd_uiPageNo'];
        $cmd_asperui[] = $cmd_data['cmd_asperui'];
        $cmd_sno_asperui[] = $cmd_data['cmd_sno_asperui'];
        $cmd_asperuivalue[] = $cmd_data['cmd_asperuivalue'];
        $cmd_uiDescriptionInOut[] = $cmd_data['cmd_uiDescriptionInOut'];
        $cmd_sliderStepValue[] = $cmd_data['cmd_sliderStepValue'];
        $cmd_defaultvalue[] = $cmd_data['cmd_defaultvalue'];
        $cmd_developerNotes[] = $cmd_data['cmd_developerNotes'];
    }

    $cmd_startBytePosition = implode(',', $cmd_startBytePosition);
    $cmd_endBytePosition = implode(',', $cmd_endBytePosition);
    $cmd_fieldDescription = implode(',', $cmd_fieldDescription);
    $cmd_numberOfBytes = implode(',', $cmd_numberOfBytes);
    $cmd_bitSize = implode(',', $cmd_bitSize);
    $cmd_dataType = implode(',', $cmd_dataType);
    $cmd_valueDefinition = implode(',', $cmd_valueDefinition);
    $cmd_displayValueMin = implode(',', $cmd_displayValueMin);
    $cmd_displayValueMax = implode(',', $cmd_displayValueMax);
    $cmd_valueInHexMin = implode(',', $cmd_valueInHexMin);
    $cmd_valueInHexMax = implode(',', $cmd_valueInHexMax);
    $cmd_uiType = implode(',', $cmd_uiType);
    $cmd_uiId = implode(',', $cmd_uiId);
    $cmd_uiPageNo = implode(',', $cmd_uiPageNo);
    $cmd_asperui = implode(',', $cmd_asperui);
    $cmd_sno_asperui = implode(',', $cmd_sno_asperui);
    $cmd_asperuivalue = implode(',', $cmd_asperuivalue);
    $cmd_uiDescriptionInOut = implode(',', $cmd_uiDescriptionInOut);
    $cmd_sliderStepValue = implode(',', $cmd_sliderStepValue);
    $cmd_defaultvalue = implode(',', $cmd_defaultvalue);
    $cmd_developerNotes = implode(',', $cmd_developerNotes);

 
    foreach ($respframe_data as $resp_data) {
        $resp_startBytePosition[] = $resp_data['resp_startBytePosition'];
        $resp_endBytePosition[] = $resp_data['resp_endBytePosition'];
        $resp_fieldDescription[] = $resp_data['resp_fieldDescription'];
        $resp_numberOfBytes[] = $resp_data['resp_numberOfBytes'];
        $resp_bitSize[] = $resp_data['resp_bitSize'];
        $resp_dataType[] = $resp_data['resp_dataType'];
        $resp_valueDefinition[] = $resp_data['resp_valueDefinition'];
        $resp_displayValueMin[] = $resp_data['resp_displayValueMin'];
        $resp_displayValueMax[] = $resp_data['resp_displayValueMax'];
        $resp_valueInHexMin[] = $resp_data['resp_valueInHexMin'];
        $resp_valueInHexMax[] = $resp_data['resp_valueInHexMax'];
        $resp_uiType[] = $resp_data['resp_uiType'];
        $resp_uiId[] = $resp_data['resp_uiId'];
        $resp_uiPageNo[] = $resp_data['resp_uiPageNo'];
        $resp_asperui[] = $resp_data['resp_asperui'];
        $resp_sno_asperui[] = $resp_data['resp_sno_asperui'];
        $resp_asperuivalue[] = $resp_data['resp_asperuivalue'];
        $resp_uiDescriptionInOut[] = $resp_data['resp_uiDescriptionInOut'];
        $resp_sliderStepValue[] = $resp_data['resp_sliderStepValue'];
        $resp_defaultvalue[] = $resp_data['resp_defaultvalue'];
        $resp_developerNotes[] = $resp_data['resp_developerNotes'];
    }

    
    $resp_startBytePosition = implode(',', $resp_startBytePosition);
    $resp_endBytePosition = implode(',', $resp_endBytePosition);
    $resp_fieldDescription = implode(',', $resp_fieldDescription);
    $resp_numberOfBytes = implode(',', $resp_numberOfBytes);
    $resp_bitSize = implode(',', $resp_bitSize);
    $resp_dataType = implode(',', $resp_dataType);
    $resp_valueDefinition = implode(',', $resp_valueDefinition);
    $resp_displayValueMin = implode(',', $resp_displayValueMin);
    $resp_displayValueMax = implode(',', $resp_displayValueMax);
    $resp_valueInHexMin = implode(',', $resp_valueInHexMin);
    $resp_valueInHexMax = implode(',', $resp_valueInHexMax);
    $resp_uiType = implode(',', $resp_uiType);
    $resp_uiId = implode(',', $resp_uiId);
    $resp_uiPageNo = implode(',', $resp_uiPageNo);
    $resp_asperui = implode(',', $resp_asperui);
    $resp_sno_asperui = implode(',', $resp_sno_asperui);
    $resp_asperuivalue = implode(',', $resp_asperuivalue);
    $resp_uiDescriptionInOut = implode(',', $resp_uiDescriptionInOut);
    $resp_sliderStepValue = implode(',', $resp_sliderStepValue);
    $resp_defaultvalue = implode(',', $resp_defaultvalue);
    $resp_developerNotes = implode(',', $resp_developerNotes);

 


    foreach ($errorframe_data as $error_data) {
        $error_startBytePosition[] = $error_data['error_startBytePosition'];
        $error_endBytePosition[] = $error_data['error_endBytePosition'];
        $error_fieldDescription[] = $error_data['error_fieldDescription'];
        $error_numberOfBytes[] = $error_data['error_numberOfBytes'];
        $error_bitSize[] = $error_data['error_bitSize'];
        $error_dataType[] = $error_data['error_dataType'];
        $error_valueDefinition[] = $error_data['error_valueDefinition'];
        $error_displayValueMin[] = $error_data['error_displayValueMin'];
        $error_displayValueMax[] = $error_data['error_displayValueMax'];
        $error_valueInHexMin[] = $error_data['error_valueInHexMin'];
        $error_valueInHexMax[] = $error_data['error_valueInHexMax'];
        $error_uiType[] = $error_data['error_uiType'];
        $error_uiId[] = $error_data['error_uiId'];
        $error_uiPageNo[] = $error_data['error_uiPageNo'];
        $error_asperui[] = $error_data['error_asperui'];
        $error_sno_asperui[] = $error_data['error_sno_asperui'];
        $error_asperuivalue[] = $error_data['error_asperuivalue'];
        $error_uiDescriptionInOut[] = $error_data['error_uiDescriptionInOut'];
        $error_sliderStepValue[] = $error_data['error_sliderStepValue'];
        $error_defaultvalue[] = $error_data['error_defaultvalue'];
        $error_developerNotes[] = $error_data['error_developerNotes'];
    }

    
    $error_startBytePosition = implode(',', $error_startBytePosition);
    $error_endBytePosition = implode(',', $error_endBytePosition);
    $error_fieldDescription = implode(',', $error_fieldDescription);
    $error_numberOfBytes = implode(',', $error_numberOfBytes);
    $error_bitSize = implode(',', $error_bitSize);
    $error_dataType = implode(',', $error_dataType);
    $error_valueDefinition = implode(',', $error_valueDefinition);
    $error_displayValueMin = implode(',', $error_displayValueMin);
    $error_displayValueMax = implode(',', $error_displayValueMax);
    $error_valueInHexMin = implode(',', $error_valueInHexMin);
    $error_valueInHexMax = implode(',', $error_valueInHexMax);
    $error_uiType = implode(',', $error_uiType);
    $error_uiId = implode(',', $error_uiId);
    $error_uiPageNo = implode(',', $error_uiPageNo);
    $error_asperui = implode(',', $error_asperui);
    $error_sno_asperui = implode(',', $error_sno_asperui);
    $error_asperuivalue = implode(',', $error_asperuivalue);
    $error_uiDescriptionInOut = implode(',', $error_uiDescriptionInOut);
    $error_sliderStepValue = implode(',', $error_sliderStepValue);
    $error_defaultvalue = implode(',', $error_defaultvalue);
    $error_developerNotes = implode(',', $error_developerNotes);




        $values = [
        
        $data['brandname'],
        $data['productname'],
        $data['modelname'],
        $data['productdescription'],
        $data['numberofpages'],
        $data['Sequence'],
        $data['pagenames'],
        $data['date'],


        $data['cmd_parametername'],
        $data['cmd_data_flow_direction'],
        $data['cmd_noofrows'],
        $data['cmd_framesizeinbytes'],
        $data['cmd_frametrigger'],
        $data['cmd_multipleui'],
        $data['cmd_triggerui'],
        
        $data['resp_data_flow_direction'],
        $data['resp_noofrows'],
        $data['resp_framesizeinbytes'],
        $data['resp_frametrigger'],
        $data['resp_multipleui'],
        $data['resp_triggerui'],
       
        $data['error_data_flow_direction'],
        $data['error_noofrows'],
        $data['error_framesizeinbytes'],
        $data['error_frametrigger'],
        $data['error_multipleui'],
        $data['error_triggerui'],
        
 
        $cmd_startBytePosition,
        $cmd_endBytePosition,
        $cmd_fieldDescription,
        $cmd_numberOfBytes,
        $cmd_bitSize,
        $cmd_dataType,
        $cmd_valueDefinition,
        $cmd_displayValueMin,
        $cmd_displayValueMax,
        $cmd_valueInHexMin,
        $cmd_valueInHexMax,
        $cmd_uiType,
        $cmd_uiId,
        $cmd_uiPageNo,
        $cmd_asperui,
        $cmd_sno_asperui,
        $cmd_asperuivalue,
        $cmd_uiDescriptionInOut,
        $cmd_sliderStepValue,
        $cmd_defaultvalue,
        $cmd_developerNotes,
        $resp_startBytePosition,
        $resp_endBytePosition,
        $resp_fieldDescription,
        $resp_numberOfBytes,
        $resp_bitSize,
        $resp_dataType,
        $resp_valueDefinition,
        $resp_displayValueMin,
        $resp_displayValueMax,
        $resp_valueInHexMin,
        $resp_valueInHexMax,
        $resp_uiType,
        $resp_uiId,
        $resp_uiPageNo,
        $resp_asperui,
        $resp_sno_asperui,
        $resp_asperuivalue,
        $resp_uiDescriptionInOut,
        $resp_sliderStepValue,
        $resp_defaultvalue,
        $resp_developerNotes,
        $error_startBytePosition,
        $error_endBytePosition,
        $error_fieldDescription,
        $error_numberOfBytes,
        $error_bitSize,
        $error_dataType,
        $error_valueDefinition,
        $error_displayValueMin,
        $error_displayValueMax,
        $error_valueInHexMin,
        $error_valueInHexMax,
        $error_uiType,
        $error_uiId,
        $error_uiPageNo,
        $error_asperui,
        $error_sno_asperui,
        $error_asperuivalue,
        $error_uiDescriptionInOut,
        $error_sliderStepValue,
        $error_defaultvalue,
        $error_developerNotes


    ];



 
$brandname = $data['brandname'];
//$brandlogo = $data['brandlogo'];
$productname = $data['productname'];
//$imageDropdown = $data['imageDropdown'];
$modelname = $data['modelname'];
$productdescription = $data['productdescription'];
$numberofpages = $data['numberofpages'];
$Sequence = $data['Sequence'];
$pagenames = $data['pagenames'];
$date = $data['date'];
 


$parameter_name = $data['cmd_parametername'];
$cmd_dataflow = $data['cmd_data_flow_direction'];
$cmd_noofrows = $data['cmd_noofrows'];
$cmd_framesize = $data['cmd_framesizeinbytes'];
$cmd_frametrigger = $data['cmd_frametrigger'];
$cmd_multipleui = $data['cmd_multipleui'];
$cmd_triggerui = $data['cmd_triggerui'];

$resp_dataflow = $data['resp_data_flow_direction'];
$resp_noofrows = $data['resp_noofrows'];
$resp_framesize = $data['resp_framesizeinbytes'];
$resp_frametrigger = $data['resp_frametrigger'];
$resp_multipleui = $data['resp_multipleui'];
$resp_triggerui = $data['resp_triggerui'];

$error_dataflow = $data['error_data_flow_direction'];
$error_noofrows = $data['error_noofrows'];
$error_framesize = $data['error_framesizeinbytes']; // Assuming itemid is part of $data array
$error_frametrigger = $data['error_frametrigger'];
$error_multipleui = $data['error_multipleui'];
$error_triggerui = $data['error_triggerui'];


$sql = "UPDATE product_api SET 
    
    brand_name = '$brandname', 
    product_name = '$productname', 
    model_name = '$modelname', 
    product_description = '$productdescription', 
    number_of_pages = '$numberofpages', 
    sequence_no = '$Sequence', 
    page_names = '$pagenames', 
    date = '$date', 

    parameter_name = '$parameter_name', 
    cmd_dataflow = '$cmd_dataflow', 
    cmd_noofrows = '$cmd_noofrows', 
    cmd_framesize = '$cmd_framesize', 
    cmd_frametrigger = '$cmd_frametrigger', 
    cmd_multipleui = '$cmd_multipleui', 
    cmd_triggerui = '$cmd_triggerui',     
    
    resp_dataflow = '$resp_dataflow', 
    resp_noofrows = '$resp_noofrows', 
    resp_framesize = '$resp_framesize', 
    resp_frametrigger = '$resp_frametrigger', 
    resp_multipleui = '$resp_multipleui', 
    resp_triggerui = '$resp_triggerui', 
    
    error_dataflow = '$error_dataflow',
    error_noofrows = '$error_noofrows', 
    error_framesize = '$error_framesize', 
    error_frametrigger = '$error_frametrigger', 
    error_multipleui = '$error_multipleui', 
    error_triggerui = '$error_triggerui', 
    
 

    cmd_startBytePosition = '$cmd_startBytePosition', 
    cmd_endBytePosition = '$cmd_endBytePosition', 
    cmd_fieldDescription = '$cmd_fieldDescription', 
    cmd_numberOfBytes = '$cmd_numberOfBytes', 
    cmd_bitSize = '$cmd_bitSize', 
    cmd_dataType = '$cmd_dataType', 
    cmd_valueDefinition = '$cmd_valueDefinition', 
    cmd_displayValueMin = '$cmd_displayValueMin', 
    cmd_displayValueMax = '$cmd_displayValueMax', 
    cmd_valueInHexMin = '$cmd_valueInHexMin', 
    cmd_valueInHexMax = '$cmd_valueInHexMax', 
    cmd_uiType = '$cmd_uiType', 
    cmd_uiId = '$cmd_uiId', 
    cmd_uiPageNo = '$cmd_uiPageNo', 
    cmd_asperui = '$cmd_asperui', 
    cmd_sno_asperui = '$cmd_sno_asperui', 
    cmd_asperui_values = '$cmd_asperuivalue', 
    cmd_uiDescriptionInOut = '$cmd_uiDescriptionInOut', 
    cmd_sliderStepValue = '$cmd_sliderStepValue', 
    cmd_defaultvalue = '$cmd_defaultvalue', 
    cmd_developerNotes = '$cmd_developerNotes', 
    resp_startBytePosition = '$resp_startBytePosition', 
    resp_endBytePosition = '$resp_endBytePosition', 
    resp_fieldDescription = '$resp_fieldDescription', 
    resp_numberOfBytes = '$resp_numberOfBytes', 
    resp_bitSize = '$resp_bitSize', 
    resp_dataType = '$resp_dataType', 
    resp_valueDefinition = '$resp_valueDefinition', 
    resp_displayValueMin = '$resp_displayValueMin', 
    resp_displayValueMax = '$resp_displayValueMax', 
    resp_valueInHexMin = '$resp_valueInHexMin', 
    resp_valueInHexMax = '$resp_valueInHexMax', 
    resp_uiType = '$resp_uiType', 
    resp_uiId = '$resp_uiId', 
    resp_uiPageNo = '$resp_uiPageNo', 
    resp_asperui = '$resp_asperui', 
    resp_sno_asperui = '$resp_sno_asperui',
    resp_asperui_values = '$resp_asperuivalue', 
    resp_uiDescriptionInOut = '$resp_uiDescriptionInOut', 
    resp_sliderStepValue = '$resp_sliderStepValue',
    resp_defaultvalue = '$resp_defaultvalue',  
    resp_developerNotes = '$resp_developerNotes', 
    error_startBytePosition = '$error_startBytePosition', 
    error_endBytePosition = '$error_endBytePosition', 
    error_fieldDescription = '$error_fieldDescription', 
    error_numberOfBytes = '$error_numberOfBytes', 
    error_bitSize = '$error_bitSize', 
    error_dataType = '$error_dataType', 
    error_valueDefinition = '$error_valueDefinition', 
    error_displayValueMin = '$error_displayValueMin', 
    error_displayValueMax = '$error_displayValueMax', 
    error_valueInHexMin = '$error_valueInHexMin', 
    error_valueInHexMax = '$error_valueInHexMax', 
    error_uiType = '$error_uiType', 
    error_uiId = '$error_uiId', 
    error_uiPageNo = '$error_uiPageNo', 
    error_asperui = '$error_asperui', 
    error_sno_asperui = '$error_sno_asperui', 
    error_asperui_values = '$error_asperuivalue', 
    error_uiDescriptionInOut = '$error_uiDescriptionInOut', 
    error_sliderStepValue = '$error_sliderStepValue', 
    error_defaultvalue = '$error_defaultvalue', 
    error_developerNotes = '$error_developerNotes' 
WHERE parameter_name = '$parameter_name' and model_name = '$modelname'";



    // Execute the query
    $result = $this->db->query($sql, $values);

    // Check for database errors
    if (!$result) {
        // Log the database error
        log_message('error', 'Database error: ' . $this->db->error()['message']);
        $results[] = false; // Store false in the results array indicating failure
    } else {
        $results[] = true; // Store true in the results array indicating success
    }

    

    return $results; 
}





/************************************************************************/



function savecommandapi($data) {


$table = $this->db->prefixTable('product_api');

        $sql = "insert into $table set parameter_name='".$data['cmd_parametername']."',
                                    cmd_dataflow='".$data['cmd_data_flow_direction']."',
                                cmd_framesize='".$data['cmd_framesizeinbytes']."',
                                     cmd_frametrigger='".$data['cmd_frametrigger']."'";
                   

return $this->db->query($sql);

}




function saveresponseapi($data) {

$table = $this->db->prefixTable('product_api');

        $sql = "insert into $table set resp_dataflow='".$data['resp_data_flow_direction']."',
                                resp_framesize='".$data['resp_framesizeinbytes']."',
                                     resp_frametrigger='".$data['resp_frametrigger']."'";
                   
return $this->db->query($sql);
}





function saveerrorapi($data) {

$table = $this->db->prefixTable('product_api');

$sql = "insert into $table set error_dataflow='".$data['error_data_flow_direction']."',
                                error_framesize='".$data['error_framesizeinbytes']."',
                                     error_frametrigger='".$data['error_frametrigger']."'";

return $this->db->query($sql);

}


/******************************************************************************/

function savemapped($data) {

$table = $this->db->prefixTable('mapped_devices');
$sql = "insert into $table set serialno='".$data['serialno']."',uid='".$data['uid']."',modelno='".$data['modelno']."'";
return $this->db->query($sql);

}




function savevendorproduct($data, $user_id) {
    $table = $this->db->table('vendorproducts');
    
    $existingProduct = $table->where('modelname', $data['modelname'])
                             ->where('modelno', $data['modelno'])
                             ->get()
                             ->getRow();
    
    
    if ($existingProduct) {
        return "Error: Product with modelname '{$data['modelname']}' and modelno '{$data['modelno']}' already exists.";
    }

    
    $insertData = [
        'category' => $data['category'],
        'modelname' => $data['modelname'],
        'modelno' => $data['modelname'],
        'product_type' => $data['category'],
        'description' => $data['description'],
        'user_id' => $user_id
    ];

    return $table->insert($insertData);
}




function deletemapped($id){

      $table = $this->db->prefixTable('mapped_devices');

        $sql = "delete from $table where id='".$id."'";

         return $this->db->query($sql);
     }


     //deleteapiproduct
        function deleteapiproduct($id)
     {
         $table = $this->db->prefixTable('deviceapis');

        $sql = "delete from $table where id='".$id."'";

         return $this->db->query($sql);
     }



function deletebrand($id)
     {

         $table = $this->db->prefixTable('brands');
         $sql = "delete from $table where id='".$id."'";
         return $this->db->query($sql);

     }



   function deletecat($id)

     {
         $table = $this->db->prefixTable('productcats');

        $sql = "delete from $table where id='".$id."'";

         return $this->db->query($sql);
     }




    function deletevendorproduct($id)

     {
         $table = $this->db->prefixTable('vendorproducts');

        $sql = "delete from $table where id='".$id."'";

         return $this->db->query($sql);
     }




function deleteapi($modelname, $parameter_name, $product_name)
{

$sql = "delete FROM `product_api` WHERE parameter_name = '$parameter_name' AND model_name = '$modelname' AND product_name = '$product_name'";
return $this->db->query($sql);

}






function save_cat($name,$prodimg, $user_id)
{

     $existing_product = $this->db->table('productcats')
                      ->where('name', $name)
                      ->where('user_id', $user_id)
                      ->get()
                      ->getRow();
 if ($existing_product) {
    // Brand already exists for the user, return an error
    return "Product '$name' already exists for this user.";
} else {
    // Insert new brand
    $data = [
        'name' => $name,
        'Product_image' => $prodimg,
        'user_id' => $user_id
    ];
    $this->db->table('productcats')->insert($data);
    return true; // Success
}
print_r($data);

}


public function get_item_with_details($id)
{
    return $this->db->table('efactorproduct i')
        ->select('i.id, i.modelno, i.category,
                  m.model_name as modelname,
                  b.brand_name as brand')
        ->join('models m', 'm.id = i.model_id', 'left')
        ->join('brands b', 'b.id = i.brand_id', 'left')
        ->where('i.id', $id)
        ->get()
        ->getRow();
}

public function getNodeIdsByModel($modelname) {
    $rows = $this->db->table($this->table)
                     ->select('node_id')
                     ->where('model_name', $modelname)
                     ->get()
                     ->getResultArray();

    return array_column($rows, 'node_id'); 
}

 public function save_product_description($data) {
        $builder = $this->db->table($this->table);
        $builder->insert($data);

        // Check if insert really happened
        if ($this->db->affectedRows() === 0) {
            log_message('error', 'DB Insert Failed! Check NOT NULL constraints and data: ' . print_r($data, true));
            return 0;
        }

        return $this->db->insertID(); // last inserted ID
    }







/*********************************************************************/
/*
public function saveStickerFields(array $data)
    {
        $builder = $this->db->table('sticker_fields');

        try {

         
            if (!empty($data['id'])) {
                $builder->where('id', $data['id'])->update($data);
                return true;
            }


            
            if (!empty($data['modelname'])) {

                $existing = $builder
                    ->where('modelname', $data['modelname'])
                    ->get()
                    ->getRowArray();

                if ($existing) {

                
                    foreach ($data as $key => $value) {
                        if ($value === null || $value === '') {
                            $data[$key] = $existing[$key] ?? null;
                        }
                    }

                    $builder->where('modelname', $data['modelname'])
                        ->update($data);

                    return true;
                }
            }


          
            $builder->insert($data);

            return $this->db->insertID();
        } catch (\Throwable $e) {

            log_message('error', '❌ saveStickerFields ERROR: ' . $e->getMessage());

            return false;
        }
    }
    
    */
    
    
    
    
    
    
public function saveStickerFields(array $data)
    {
        $builder = $this->db->table('sticker_fields');

        try {
            /*
         * Only these columns are allowed in the new JSON-safe flow.
         * Old comma-based columns are intentionally ignored:
         * field_name, field_value, counting, input_type,
         * keyword_type, keyword_value_type.
         */
            $allowed = [
                'id',
                'sticker_id',
                'modelname',
                'user_id',
                'form_data',
                'full_snapshot',
                'print_snapshot',
            ];

            $cleanData = [];

            foreach ($allowed as $key) {
                if (array_key_exists($key, $data)) {
                    $cleanData[$key] = $data[$key];
                }
            }

            if (empty($cleanData)) {
                return false;
            }

            /*
         * Normalize form_data.
         * Accepts array or JSON string.
         */
            if (array_key_exists('form_data', $cleanData)) {
                if (is_array($cleanData['form_data'])) {
                    $json = json_encode(
                        $cleanData['form_data'],
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    );

                    if ($json === false) {
                        return false;
                    }

                    $cleanData['form_data'] = $json;
                } elseif ($cleanData['form_data'] !== null) {
                    $decoded = json_decode((string)$cleanData['form_data'], true);

                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                        return false;
                    }

                    $cleanData['form_data'] = json_encode(
                        $decoded,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    );
                }
            }

            /*
         * Update by ID.
         * Never allow id itself inside update payload.
         */
            if (!empty($cleanData['id'])) {
                $id = (int)$cleanData['id'];
                unset($cleanData['id']);

                if ($id <= 0 || empty($cleanData)) {
                    return false;
                }

                $builder->where('id', $id)->update($cleanData);

                return true;
            }

            /*
         * Update by modelname.
         * This is your main flow.
         */
            if (!empty($cleanData['modelname'])) {
                $modelname = trim((string)$cleanData['modelname']);

                if ($modelname === '') {
                    return false;
                }

                $existingBuilder = $this->db->table('sticker_fields')
                    ->where('modelname', $modelname);

                /*
             * If user_id is supplied, use it also.
             * This prevents one user's sticker form from overwriting another user's form.
             */
                if (!empty($cleanData['user_id']) && is_numeric($cleanData['user_id'])) {
                    $existingBuilder->where('user_id', (int)$cleanData['user_id']);
                }

                $existing = $existingBuilder->get()->getRowArray();

                if ($existing) {
                    $updateData = $cleanData;
                    unset($updateData['id']);

                    /*
                 * Do not overwrite existing values with empty fields.
                 * Important:
                 * - If saving form_data, only form_data changes.
                 * - If saving full_snapshot, form_data remains untouched.
                 * - If saving print_snapshot, form_data remains untouched.
                 */
                    foreach ($updateData as $key => $value) {
                        if ($key === 'modelname') {
                            continue;
                        }

                        if ($value === null || $value === '') {
                            unset($updateData[$key]);
                        }
                    }

                    if (empty($updateData)) {
                        return true;
                    }

                    $this->db->table('sticker_fields')
                        ->where('id', $existing['id'])
                        ->update($updateData);

                    return true;
                }
            }

            /*
         * Insert new row.
         */
            unset($cleanData['id']);

            if (empty($cleanData['modelname'])) {
                return false;
            }

            $builder->insert($cleanData);

            return $this->db->insertID();
        } catch (\Throwable $e) {
            log_message('error', 'saveStickerFields ERROR: ' . $e->getMessage());

            return false;
        }
    }
    
/*********************************************************************/
/*  private function getDBConnection() {
        try {
            $host = "localhost";
            $dbname = "auto_test_db";
            $username = "root";
            $password = "EIS@24dbCOS";
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }*/
    
    
    
     /*  
    
  public function saveTestConfiguration($data) {
    $db = null;

    try {
        $db = $this->getDBConnection();

        $db->beginTransaction();

        // ✅ Metadata
        $userId = $data['user_id'] ?? null;
        $testId = $data['id'] ?? null;
        $modelNo = $data['modelno'] ?? null;

        if (!$userId || !$modelNo) {
            throw new \Exception("Missing required fields: user_id or modelno");
        }

        // ✅ Insert main config
        $configSql = "INSERT INTO test_configurations (user_id, modelno, test_id) 
                      VALUES (:user_id, :modelno, :test_id)";

        $configStmt = $db->prepare($configSql);
        $configStmt->execute([
            ':user_id' => $userId,
            ':modelno' => $modelNo,
            ':test_id' => $testId
        ]);

        $configId = $db->lastInsertId();

        // ✅ Parameter insert
        $paramSql = "INSERT INTO test_parameters 
                    (config_id, api_test_name, parameter_type, instance_id, 
                     byte_position, time_value, min_value, max_value, param_value) 
                    VALUES 
                    (:config_id, :api_test_name, :parameter_type, :instance_id,
                     :byte_position, :time_value, :min_value, :max_value, :param_value)";

        $paramStmt = $db->prepare($paramSql);

        $parameterCount = 0;

        foreach ($data as $key => $value) {

            // Skip metadata
            if (in_array($key, ['user_id', 'id', 'modelno'])) {
                continue;
            }

            // Match API Test keys
            if (preg_match('/^API Test \d+$/', $key)) {

                $apiTestName = $key;

                if (is_array($value)) {

                    foreach ($value as $paramType => $paramDetails) {

                        if (is_array($paramDetails)) {

                            foreach ($paramDetails as $detail) {

                                $this->insertParameter(
                                    $paramStmt,
                                    $configId,
                                    $apiTestName,
                                    $paramType,
                                    $detail
                                );

                                $parameterCount++;
                            }
                        }
                    }
                }
            }
        }

        $db->commit();

        return [
            'success' => true,
            'config_id' => $configId,
            'parameters_count' => $parameterCount,
            'message' => 'Test configuration saved successfully'
        ];

    } catch (\Exception $e) {

        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }

        return [
            'success' => false,
            'message' => 'Failed to save test configuration: ' . $e->getMessage()
        ];

    } finally {
        $db = null;
    }
}
    
    
   */
   
   
/*************************************************************/


public function submitTestConfiguration_db($data)
{
    $db = \Config\Database::connect();
    $table = 'sqlapi_test_execution_data';

    $itemid  = $data['id'] ?? null;
    $user_id = $data['user_id'] ?? null;
    $modelno = $data['modelno'] ?? null;

    $all_command  = [];
    $all_response = [];
    $all_error    = [];
    $test_names   = [];

    foreach ($data as $key => $test) {

        if (in_array($key, ['user_id','id','modelno'])) continue;

        $test_names[] = $key;

        foreach ($test as $paramName => $items) {
            foreach ($items as $item) {

                if (stripos($paramName, 'status') !== false) {
                    $all_response[] = $item;
                    continue;
                }

                if (isset($item['value'])) {
                    $all_command[] = $item;
                }

                if (!isset($item['value']) && empty($item['time'])) {
                    $all_error[] = $item;
                }
            }
        }
    }

    $insertData = [
        'itemid'        => $itemid,
        'user_id'       => $user_id,
        'modelno'       => $modelno,
        'test_index'    => implode(", ", $test_names),
        'command_data'  => !empty($all_command) ? json_encode($all_command) : null,
        'response_data' => !empty($all_response) ? json_encode($all_response) : null,
        'error_data'    => !empty($all_error) ? json_encode($all_error) : null,
        'json_data'     => json_encode($data),
        'created_at'    => date('Y-m-d H:i:s')
    ];

    // 🔥 CHECK EXISTING ROW
    $existing = $db->table($table)
        ->where('user_id', $user_id)
        ->where('modelno', $modelno)
        ->get()
        ->getRow();

    if ($existing) {
        // 🔥 UPDATE SAME ROW
        $db->table($table)
            ->where('user_id', $user_id)
            ->where('modelno', $modelno)
            ->update($insertData);
    } else {
        // 🔥 INSERT NEW ROW
        $db->table($table)->insert($insertData);
    }
}
public function getSavedConfiguration($user_id, $modelno)
{
    $db = \Config\Database::connect();
    $table = 'sqlapi_test_execution_data';

    $row = $db->table($table)
        ->where('user_id', $user_id)
        ->where('modelno', $modelno)
        ->get()
        ->getRow();

    // ❌ REMOVE THIS BLOCK
    /*
    echo "<pre>";
    print_r($row);
    exit;
    */

    if ($row) {
        return json_decode($row->json_data, true);
    }

    return null;
}
   
   
   
   
/************************************************************/
private function getDBConnection() {
    try {
        $host = "localhost";
        $dbname = "auto_test_db";
        $username = "root";
        $password = "EIS@24dbCOS";

        $pdo = new \PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        return $pdo;

    } catch (\PDOException $e) {
        throw new \Exception("Database connection failed: " . $e->getMessage());
    }
}
    
       
/*       
public function saveTestConfiguration($payload)
{
    $db = null;

    try {
        $db = $this->getDBConnection();
        $db->beginTransaction();

        // ✅ Get data from controller
        $userId = $payload['user_id'] ?? null;
        $modelNo = $payload['modelno'] ?? null;
        $testId = $payload['id'] ?? null;
        $tests = $payload['tests'] ?? [];

        if (!$userId || !$modelNo) {
            throw new \Exception("Missing required fields: user_id or modelno");
        }

        // ✅ Insert config
        $stmt = $db->prepare("
            INSERT INTO test_configurations (user_id, modelno, test_id)
            VALUES (:user_id, :modelno, :test_id)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':modelno' => $modelNo,
            ':test_id' => $testId
        ]);

        $configId = $db->lastInsertId();

        // ✅ Prepare param insert
        $paramStmt = $db->prepare("
            INSERT INTO test_parameters 
            (config_id, api_test_name, parameter_type, instance_id, 
             byte_position, time_value, min_value, max_value, param_value) 
            VALUES 
            (:config_id, :api_test_name, :parameter_type, :instance_id,
             :byte_position, :time_value, :min_value, :max_value, :param_value)
        ");
        
        
       

$count = 0;

foreach ($tests as $key => $test) {

    // Skip metadata inside JSON
    if (in_array($key, ['user_id', 'id', 'modelno'])) {
        continue;
    }

    if (preg_match('/^API Test \d+$/', $key)) {

        $apiTestName = $key;

        foreach ($test as $paramType => $paramDetails) {

            if (is_array($paramDetails)) {

                foreach ($paramDetails as $i => $detail) {

                    $mappedDetail = [
                        'instance_id'   => $detail['instance'] ?? ($i + 1),
                        'byte_position' => isset($detail['bytePosition'])
                            ? preg_replace('/\D/', '', $detail['bytePosition'])
                            : 0,
                        'time'  => $detail['time'] ?? 0,
                        'min'   => $detail['min'] ?? 0,
                        'max'   => $detail['max'] ?? 0,
                        'value' => $detail['value'] ?? ''
                    ];

                    // EXTRA SAFETY (optional but recommended)
                    if (!$mappedDetail['instance_id']) {
                        throw new Exception("instance_id missing in $apiTestName");
                    }

                    $this->insertParameter(
                        $paramStmt,
                        $configId,
                        $apiTestName,
                        $paramType,
                        $mappedDetail
                    );

                    $count++;
                }
            }
        }
    }
}

        $db->commit();

        return [
            'success' => true,
            'config_id' => $configId,
            'parameters_count' => $count
        ];

    } catch (\Exception $e) {

        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

*/



public function saveTestConfiguration($payload)
{
    $db = null;

    try {
        $db = $this->getDBConnection();
        $db->beginTransaction();

        // ✅ Get data from payload
        $userId = $payload['user_id'] ?? null;
        $modelNo = $payload['modelno'] ?? null;
        $testId = $payload['id'] ?? null;
        
        // Get tests - either from 'tests' key or use the entire payload minus metadata
        $tests = $payload['tests'] ?? [];
        
        // If tests is empty but we have other data, filter out metadata
        if (empty($tests) && !empty($payload)) {
            $tests = array_filter($payload, function($key) {
                return !in_array($key, ['user_id', 'id', 'modelno']);
            }, ARRAY_FILTER_USE_KEY);
        }

        if (!$userId || !$modelNo) {
            throw new \Exception("Missing required fields: user_id or modelno");
        }

        if (empty($tests)) {
            throw new \Exception("No test data found to save");
        }

        // ✅ Insert config
        $stmt = $db->prepare("
            INSERT INTO test_configurations (user_id, modelno, test_id)
            VALUES (:user_id, :modelno, :test_id)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':modelno' => $modelNo,
            ':test_id' => $testId
        ]);

        $configId = $db->lastInsertId();

        // ✅ Prepare param insert
        $paramStmt = $db->prepare("
            INSERT INTO test_parameters 
            (config_id, api_test_name, parameter_type, instance_id, 
             byte_position, time_value, min_value, max_value, param_value) 
            VALUES 
            (:config_id, :api_test_name, :parameter_type, :instance_id,
             :byte_position, :time_value, :min_value, :max_value, :param_value)
        ");
        
        $count = 0;

        foreach ($tests as $key => $test) {
            // Skip metadata if it somehow still exists in tests
            if (in_array($key, ['user_id', 'id', 'modelno'])) {
                continue;
            }

            // Match API Test patterns
            if (preg_match('/^API Test \d+$/', $key)) {
                $apiTestName = $key;

                foreach ($test as $paramType => $paramDetails) {
                    if (is_array($paramDetails)) {
                        foreach ($paramDetails as $i => $detail) {
                            // Ensure detail is an array
                            if (!is_array($detail)) {
                                continue;
                            }

                            $mappedDetail = [
                                'instance_id'   => $detail['instance'] ?? ($i + 1),
                                'byte_position' => isset($detail['bytePosition'])
                                    ? preg_replace('/\D/', '', $detail['bytePosition'])
                                    : 0,
                                'time'  => $detail['time'] ?? 0,
                                'min'   => $detail['min'] ?? 0,
                                'max'   => $detail['max'] ?? 0,
                                'value' => $detail['value'] ?? ''
                            ];

                            // Validate required fields
                            if (empty($mappedDetail['instance_id']) && $mappedDetail['instance_id'] !== 0) {
                                throw new Exception("instance_id missing in $apiTestName for $paramType");
                            }

                            $this->insertParameter(
                                $paramStmt,
                                $configId,
                                $apiTestName,
                                $paramType,
                                $mappedDetail
                            );

                            $count++;
                        }
                    }
                }
            }
        }

        $db->commit();

        return [
            'success' => true,
            'config_id' => $configId,
            'parameters_count' => $count,
            'message' => "Successfully saved $count parameters"
        ];

    } catch (\Exception $e) {
        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}











 
    
private function insertParameter($stmt, $configId, $apiTestName, $paramType, $detail)
{
    $stmt->execute([
        ':config_id'      => $configId,
        ':api_test_name'  => $apiTestName,
        ':parameter_type' => $paramType,
        ':instance_id'    => $detail['instance_id'],
        ':byte_position'  => $detail['byte_position'],
        ':time_value'     => $detail['time'],
        ':min_value'      => $detail['min'],
        ':max_value'      => $detail['max'],
        ':param_value'    => $detail['value']
    ]);
}
    
    /*
    
     public function getTestConfiguration($configId) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            // Get main configuration
            $configSql = "SELECT * FROM test_configurations WHERE id = :id";
            $configStmt = $db->prepare($configSql);
            $configStmt->execute([':id' => $configId]);
            $config = $configStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$config) {
                return null;
            }
            
            // Get all parameters
            $paramSql = "SELECT * FROM test_parameters WHERE config_id = :config_id 
                         ORDER BY api_test_name, instance_id, parameter_type";
            $paramStmt = $db->prepare($paramSql);
            $paramStmt->execute([':config_id' => $configId]);
            $parameters = $paramStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Reconstruct the original structure
            $result = [
                'user_id' => $config['user_id'],
                'id' => $config['test_id'],
                'modelno' => $config['modelno']
            ];
            
            // Group parameters by API test name
            $groupedParams = [];
            foreach ($parameters as $param) {
                $apiTestName = $param['api_test_name'];
                $paramType = $param['parameter_type'];
                
                if (!isset($groupedParams[$apiTestName])) {
                    $groupedParams[$apiTestName] = [];
                }
                
                if (!isset($groupedParams[$apiTestName][$paramType])) {
                    $groupedParams[$apiTestName][$paramType] = [];
                }
                
                // Reconstruct the detail object
                $detail = [
                    'defaultname' => $paramType,
                    'instance' => $param['instance_id'],
                    'bytePosition' => "Byte Position: " . $param['byte_position'],
                    'time' => (string)$param['time_value'],
                    'min' => $param['min_value'],
                    'max' => $param['max_value'],
                    'value' => $param['param_value']
                ];
                
                $groupedParams[$apiTestName][$paramType][] = $detail;
            }
            
            // Merge with result
            foreach ($groupedParams as $apiTestName => $params) {
                $result[$apiTestName] = $params;
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error getting test configuration: " . $e->getMessage());
            return null;
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
    public function getAllTestConfigurations($limit = 50, $offset = 0) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "SELECT id, user_id, modelno, test_id, created_at, updated_at 
                    FROM test_configurations 
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get parameter count for each config
            foreach ($configs as &$config) {
                $countSql = "SELECT COUNT(*) as count FROM test_parameters WHERE config_id = :config_id";
                $countStmt = $db->prepare($countSql);
                $countStmt->execute([':config_id' => $config['id']]);
                $config['parameters_count'] = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            }
            
            // Get total count
            $totalSql = "SELECT COUNT(*) as total FROM test_configurations";
            $totalStmt = $db->prepare($totalSql);
            $totalStmt->execute();
            $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'success' => true,
                'data' => $configs,
                'total' => $total
            ];
            
        } catch (Exception $e) {
            error_log("Error getting configurations: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
     public function getParametersByType($parameterType, $instanceId = null) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "SELECT tc.*, tp.* 
                    FROM test_parameters tp
                    JOIN test_configurations tc ON tp.config_id = tc.id
                    WHERE tp.parameter_type = :parameter_type";
            
            $params = [':parameter_type' => $parameterType];
            
            if ($instanceId !== null) {
                $sql .= " AND tp.instance_id = :instance_id";
                $params[':instance_id'] = $instanceId;
            }
            
            $sql .= " ORDER BY tc.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
       public function getLatestParameterValues($parameterType, $instanceId) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "SELECT tp.*, tc.created_at as test_date
                    FROM test_parameters tp
                    JOIN test_configurations tc ON tp.config_id = tc.id
                    WHERE tp.parameter_type = :parameter_type 
                      AND tp.instance_id = :instance_id
                    ORDER BY tc.created_at DESC
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':parameter_type' => $parameterType,
                ':instance_id' => $instanceId
            ]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error getting latest parameter: " . $e->getMessage());
            return null;
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
      public function deleteTestConfiguration($configId) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "DELETE FROM test_configurations WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $configId]);
            
            return [
                'success' => true,
                'message' => 'Configuration deleted successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
    
     public function updateTestConfiguration($configId, $data) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            // First delete existing parameters
            $deleteSql = "DELETE FROM test_parameters WHERE config_id = :config_id";
            $deleteStmt = $db->prepare($deleteSql);
            $deleteStmt->execute([':config_id' => $configId]);
            
            // Then save new data
            return $this->saveTestConfiguration($data);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
  
    public function getParameterStatistics($parameterType, $instanceId = null) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "SELECT 
                        COUNT(*) as total_count,
                        MIN(CAST(param_value AS DECIMAL)) as min_value,
                        MAX(CAST(param_value AS DECIMAL)) as max_value,
                        AVG(CAST(param_value AS DECIMAL)) as avg_value,
                        DATE(created_at) as test_date
                    FROM test_parameters tp
                    JOIN test_configurations tc ON tp.config_id = tc.id
                    WHERE tp.parameter_type = :parameter_type";
            
            $params = [':parameter_type' => $parameterType];
            
            if ($instanceId !== null) {
                $sql .= " AND tp.instance_id = :instance_id";
                $params[':instance_id'] = $instanceId;
            }
            
            $sql .= " GROUP BY DATE(tc.created_at) ORDER BY test_date DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
    
    
      public function searchByParameterValue($parameterType, $searchValue) {
        $db = null;
        try {
            // Get database connection
            $db = $this->getDBConnection();
            
            $sql = "SELECT DISTINCT tc.*, tp.parameter_type, tp.instance_id, tp.param_value
                    FROM test_configurations tc
                    JOIN test_parameters tp ON tc.id = tp.config_id
                    WHERE tp.parameter_type = :parameter_type 
                      AND tp.param_value LIKE :search_value
                    ORDER BY tc.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':parameter_type' => $parameterType,
                ':search_value' => "%$searchValue%"
            ]);
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            // Close connection
            $db = null;
        }
    }
    
    
    */
    






/*********************************************************************/

public function saveCanvasPosition($data)
    {
        $builder = $this->db->table($this->table);

        $existing = $builder->where('modelname', $data['modelname'])->get()->getRowArray();

        $updateData = [];

        if (isset($data['preview_left_mm'])) $updateData['preview_left_mm'] = $data['preview_left_mm'];
        if (isset($data['preview_top_mm'])) $updateData['preview_top_mm'] = $data['preview_top_mm'];
        if (isset($data['copy_left_mm']))   $updateData['copy_left_mm'] = $data['copy_left_mm'];
        if (isset($data['copy_top_mm']))    $updateData['copy_top_mm'] = $data['copy_top_mm'];

        if ($existing) {
            $builder->where('modelname', $data['modelname'])->update($updateData);
        } else {
            $insertData = array_merge(['modelname' => $data['modelname']], $updateData);
            $builder->insert($insertData);
        }

        return ['status' => 'success', 'message' => 'Canvas position saved successfully'];
    }


    // ✅ Fetch saved canvas position (convert mm → px)
    public function getCanvasPosition($modelname, $type)
    {
        $builder = $this->db->table($this->table);
        $leftCol = $type === 'original' ? 'preview_left_mm' : 'copy_left_mm';
        $topCol  = $type === 'original' ? 'preview_top_mm'  : 'copy_top_mm';

        $result = $builder->select("$leftCol, $topCol")
                          ->where('modelname', $modelname)
                          ->orderBy('id', 'DESC')
                          ->get()
                          ->getRowArray();

        if ($result && ($result[$leftCol] !== null || $result[$topCol] !== null)) {
            return [
                'status' => 'success',
                'left'   => floatval($result[$leftCol]) * 7.992,
                'top'    => floatval($result[$topCol]) * 7.992
            ];
        } else {
            return ['status' => 'error', 'message' => 'No saved position found'];
        }
    }

public function getLastPrintPage($userId, $modelname)
{
    return $this->db->table('sticker_fields')
        ->where('user_id', $userId)
        ->where('modelname', $modelname)
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
}





public function getLastLabel($userId, $modelname)
{
    return $this->db->table('sticker_fields')
        ->where('user_id', $userId)
        ->where('modelname', $modelname)
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
}



public function getLastCopyLabel($userId, $modelname)
{
    return $this->db->table('sticker_fields')
        ->where('user_id', $userId)
        ->where('modelname', $modelname)
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
}


public function updateNodeIdsStatus($nodeIds, $status)
{
    return $this->whereIn('node_id', $nodeIds)
                ->set(['qr_status' => $status])
                ->update();
}




    /**
     * Get sticker fields, optional filter by modelname
     */
   /* public function getFields($modelname = null)
    {
        if ($modelname) {
            return $this->where('modelname', $modelname)->findAll();
        }
        return $this->findAll();
    }*/
    
    
   /*public function getFields($modelname = null)
    {
        if ($modelname) {
            return $this->where('modelname', $modelname)->findAll();
        }
        return $this->findAll();
    }  */
    
    
    
    /**
     * Get sticker fields using JSON form_data only.
     * Old comma-based columns are ignored completely.
     */
    public function getFields($modelname = null, $userId = null)
    {
        try {
            $builder = $this->db->table('sticker_fields')
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

            $modelname = trim((string)$modelname);

            if ($modelname !== '') {
                $builder->where('modelname', $modelname);
            }

            if ($userId !== null && is_numeric($userId) && (int)$userId > 0) {
                $builder->where('user_id', (int)$userId);
            }

            $rows = $builder->get()->getResultArray();

            foreach ($rows as &$row) {
                $row['form_data_raw'] = $row['form_data'] ?? null;
                $row['form_data'] = null;

                if (!empty($row['form_data_raw'])) {
                    $decoded = json_decode((string)$row['form_data_raw'], true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $row['form_data'] = $decoded;
                    }
                }
            }

            unset($row);

            return $rows;
        } catch (\Throwable $e) {
            log_message('error', 'getFields ERROR: ' . $e->getMessage());

            return [];
        }
    }
    
    
    
    

}
