<?php

namespace App\Models;

class Items_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'efactorproduct';
        parent::__construct($this->table);
    }


   
    function get_details($options = array()) {
        $items_table = $this->db->prefixTable('efactorproduct');
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
        $login_user_id = get_array_value($options, "login_user_id");
        if ($login_user_id) {
            $extra_select = ", (SELECT COUNT($order_items_table.id) FROM $order_items_table WHERE $order_items_table.deleted=0 AND $order_items_table.order_id=0 AND $order_items_table.created_by=$login_user_id AND $order_items_table.item_id=$items_table.id) AS added_to_cart";
        }

        $limit_query = "";
        $limit = get_array_value($options, "limit");
        if ($limit) {
            $offset = get_array_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }

      /*  $sql = "SELECT $items_table.*, $item_categories_table.title as category_title $extra_select
        FROM $items_table
        LEFT JOIN $item_categories_table ON $item_categories_table.id= $items_table.category_id
        WHERE $items_table.deleted=0 $where
        ORDER BY $items_table.title ASC
        $limit_query";
        return $this->db->query($sql);*/


      /*$sql = "SELECT $items_table.*
        FROM $items_table
        $limit_query";
return $this->db->query($sql);*/

$sql = "SELECT * FROM efactorproduct";
return $this->db->query($sql);
    }

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


       function get_modeldetails($pname) {

         $order_table = $this->db->prefixTable('vendorproducts');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.name='$pname'";

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

         $order_table = $this->db->prefixTable('deviceapis');

        $sql = "SELECT $order_table.* FROM $order_table
        WHERE $order_table.itemid=$itemid";

         return $this->db->query($sql);

     }

     

     function get_cats() {

         $order_table = $this->db->prefixTable('productcats');

        $sql = "SELECT $order_table.* FROM $order_table";

         return $this->db->query($sql);

     }

       function get_details_mapped() {

         $order_table = $this->db->prefixTable('mapped_devices');

        $sql = "SELECT $order_table.* FROM $order_table";

         return $this->db->query($sql);

     }

     //get_prods
     function get_prods() {

         $order_table = $this->db->prefixTable('vendorproducts');

        $sql = "SELECT $order_table.* FROM $order_table";

         return $this->db->query($sql);

     }

     //get_details_vnsettings
     function get_details_vnsettings($vendor) {

        $order_table = $this->db->prefixTable('vnsettings');

        $sql = "SELECT $order_table.* FROM $order_table where vendor=$vendor";

         return $this->db->query($sql);

      }

     //get_vendorproducts
      function get_vendorproducts() {

        $order_table = $this->db->prefixTable('vendorproducts');

        $sql = "SELECT $order_table.* FROM $order_table";

         return $this->db->query($sql);

      }

     function saveapi($data) {

         $table = $this->db->prefixTable('deviceapis');

        $sql = "insert into $table set filename='".$data['filename']."',voicetag='".$data['voicetag']."',voicetagalong='".$data['voicetagalong']."',apiname='".$data['apiname']."',cmd_command='".$data['cmd_command']."',cmd_data='".$data['cmd_data']."',linear_min='".$data['linear_min']."',linear_max='".$data['linear_max']."',binary_min='".$data['binary_min']."',info_returncommand='".$data['info_returncommand']."',info_returndata='".$data['info_returndata']."',info_errorcommand='".$data['info_errorcommand']."',info_errordata='".$data['info_errordata']."',infocmd_command='".$data['infocmd_command']."',infocmd_data='".$data['infocmd_data']."',apidate='".$data['apidate']."',itemid='".$data['itemid']."'";

         return $this->db->query($sql);

     }

       function savemapped($data) {

         $table = $this->db->prefixTable('mapped_devices');

        $sql = "insert into $table set serialno='".$data['serialno']."',uid='".$data['uid']."',modelno='".$data['modelno']."'";

         return $this->db->query($sql);

     }

           function savevendorproduct($data) {

         $table = $this->db->prefixTable('vendorproducts');

        $sql = "insert into $table set category='".$data['category']."',modelname='".$data['modelname']."',modelno='".$data['modelno']."',description='".$data['description']."'";

         return $this->db->query($sql);

     }


          function deletemapped($id)
     {
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


     //save_cat
function save_cat($name)
     {

       $table = $this->db->prefixTable('productcats');

        $sql = "insert into $table set name='".$name."'";

         return $this->db->query($sql);

     }

}
