<?php

namespace App\Models;

class Dashboards_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'dashboards';
        parent::__construct($this->table);
    }


    function get_vendorscount()
    {
        $users_table = $this->db->prefixTable("clients");
        $sql = "SELECT COUNT($users_table.id) AS total
        FROM $users_table";
        return $this->db->query($sql)->getRow()->total;    
    }

  

     public function getVendorCount() {

       $query = $this->db->query("SELECT COUNT(*) as count FROM users WHERE user_type='client'");
       $result = $query->getRow(); // Use getRow() instead of row()
       return $result->count;
    }


    /*  function get_productscount()
    {
        $users_table = $this->db->prefixTable("items");
        $sql = "SELECT COUNT($users_table.id) AS total
        FROM $users_table";
        return $this->db->query($sql)->getRow()->total;

     
    }*/


     public function getOnlineProductsCount() {
        
        $query = $this->db->query("SELECT COUNT(*) as count FROM efactorproduct WHERE internet='true'");
        $result = $query->getRow(); 
        return $result->count;
    }


      public function getOfflineProductsCount() {
        
        $query = $this->db->query("SELECT COUNT(*) as count FROM efactorproduct WHERE internet='false'");
        $result = $query->getRow(); 
        return $result->count;
    }
    
public function getRFCountByMonth() {
    // Query to get count of RF module_type month-wise
    $query = $this->db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month, 
            COUNT(*) AS count 
        FROM efactorproduct 
        WHERE module_type = 'RF' 
        GROUP BY month
        ORDER BY month
    ");
    $results = $query->getResult();
    
    $monthWiseCounts = [];
    
    foreach ($results as $row) {
        $monthWiseCounts[$row->month] = $row->count;
    }
    
    return $monthWiseCounts;
}

public function get_appuserscount() {
       
        $query = $this->db->query("SELECT COUNT(*) as count FROM users WHERE user_type='User'");
        $result = $query->getRow(); // Use getRow() instead of row()
        return $result->count;
    }




    public function get_appusername($user_id) {
    $query = $this->db->query("SELECT email FROM users WHERE id=?", [$user_id]);
    
    $result = $query->getRow();
    
     if($result){
     return $result->email;
     }else{
     return null; }
}
     

public function gettotalmodules() {
        
        $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct");
        $result = $query->getRow(); 
        return $result->rowCount;
    }



public function getExcelFilePath() {
        // Your logic to determine the Excel file path goes here
        return 'https://efactor.tech/appstore/API_Specification.xlsx';
    }



public function gettotalusermodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=?", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}


public function gettotaluserwifimodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=? AND module_type='WIFI'", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}
public function getMonthlyUserWifiCounts($user_id, $month, $year) {
    $query = $this->db->query("
        SELECT YEAR(timestamp) as year, MONTH(timestamp) as month, COUNT(*) as count 
        FROM efactorproduct   WHERE user_id = ? AND module_type = 'WIFI' GROUP BY YEAR(timestamp), MONTH(timestamp)
        ORDER BY YEAR(timestamp) DESC, MONTH(timestamp) DESC
    ", [$user_id]);

    return $query->getResult();
}

public function gettotaluserrfmodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=? AND module_type='RF'", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}
public function getMonthlyUserRFCounts($user_id, $month, $year) {
   $query = $this->db->query("
        SELECT YEAR(timestamp) as year, MONTH(timestamp) as month, COUNT(*) as count 
        FROM efactorproduct   WHERE user_id = ? AND module_type = 'RF' GROUP BY YEAR(timestamp), MONTH(timestamp)
        ORDER BY YEAR(timestamp) DESC, MONTH(timestamp) DESC
    ", [$user_id]);

    return $query->getResult();
}

public function gettotalusergatewaymodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=? AND module_type='Gateway'", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}
public function getMonthlyUserGatewayCounts($user_id, $month, $year) {
   $query = $this->db->query("
        SELECT YEAR(timestamp) as year, MONTH(timestamp) as month, COUNT(*) as count 
        FROM efactorproduct   WHERE user_id = ? AND module_type = 'Gateway' GROUP BY YEAR(timestamp), MONTH(timestamp)
        ORDER BY YEAR(timestamp) DESC, MONTH(timestamp) DESC
    ", [$user_id]);

    return $query->getResult();
}

public function gettotaluseronlinemodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=? AND internet='true'", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}
public function gettotaluserofflinemodules($user_id) {
    
    $query = $this->db->query("SELECT COUNT(*) as rowCount FROM efactorproduct WHERE user_id=? AND internet='false'", [$user_id]);
    $result = $query->getRow(); 
    return $result->rowCount;
}




     public function getwifimodules() {

 $query = $this->db->query("SELECT COUNT(*) as count FROM efactorproduct WHERE module_type='WIFI'");
        $result = $query->getRow(); 
        return $result->count;
    }
public function getMonthlyWiFiCounts($year) {
    $year = (int) $year;
    $sql = "SELECT 
                MONTH(timestamp) AS month,
                COUNT(*) AS count
            FROM 
                efactorproduct
            WHERE 
                module_type = 'WIFI'
                AND YEAR(timestamp) = ?
            GROUP BY 
                MONTH(timestamp)
            ORDER BY 
                MONTH(timestamp)";
    
    $query = $this->db->query($sql, [$year]);
    $results = $query->getResult();
    return $results;
}

 public function getgatewaymodules() {

 $query = $this->db->query("SELECT COUNT(*) as count FROM efactorproduct WHERE module_type='GATEWAY'");
        $result = $query->getRow(); 
        return $result->count;
        
    }
    
   public function getMonthlyGatewayCounts($year) {
    $year = (int) $year;
    $sql = "SELECT 
                MONTH(timestamp) AS month,
                COUNT(*) AS count
            FROM 
                efactorproduct
            WHERE 
                module_type = 'GATEWAY'
                AND YEAR(timestamp) = ?
            GROUP BY 
                MONTH(timestamp)
            ORDER BY 
                MONTH(timestamp)";
    
    $query = $this->db->query($sql, [$year]);
    $results = $query->getResult();
    return $results;
}
    
     public function getrfmodules() {

 $query = $this->db->query("SELECT COUNT(*) as count FROM efactorproduct WHERE module_type='RF'");
        $result = $query->getRow(); 
        return $result->count;
        
    }
public function getMonthlyRFCounts($year) {
    $year = (int) $year;
    $sql = "SELECT 
                MONTH(timestamp) AS month,
                COUNT(*) AS count
            FROM 
                efactorproduct
            WHERE 
                module_type = 'RF'
                AND YEAR(timestamp) = ?
            GROUP BY 
                MONTH(timestamp)
            ORDER BY 
                MONTH(timestamp)";
    
    $query = $this->db->query($sql, [$year]);
    $results = $query->getResult();
    return $results;
}

     /*   function get_appuserscount()
    {
        $users_table = $this->db->prefixTable("users");
         $sql = "SELECT COUNT($users_table.id) AS total
        FROM $users_table where is_admin!=1";
        return $this->db->query($sql)->getRow()->total;

     
    }*/

    function get_details($options = array()) {
        $dashboard_table = $this->db->prefixTable("dashboards");

        $where = "";

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $dashboard_table.user_id=$user_id";
        }

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $dashboard_table.id= $id";
        }

        $sql = "SELECT $dashboard_table.*, IF($dashboard_table.sort!=0, $dashboard_table.sort, $dashboard_table.id) AS new_sort
        FROM $dashboard_table
        WHERE $dashboard_table.deleted=0 $where
        ORDER BY new_sort DESC";

        return $this->db->query($sql);
    }

}
