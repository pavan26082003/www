<?php

namespace App\Models;

class Messages_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'messages';
        parent::__construct($this->table);
    }

    /*
     * prepare details info of a message
     */


 
function saveapi($message_data) {
    $table = $this->db->prefixTable('messages');
    $results = []; // Array to store query results

    // Build the SQL query
$sql = "INSERT INTO $table (from_user_id,to_user_id,subject,files,message,created_at,deleted_by_users) VALUES(?,?,?,?,?,?,?)";

    // Construct the values array
   $values = [
        $message_data['from_user_id'],
        $message_data['to_user_id'],
        $message_data['subject'],
        $message_data['files'],
        $message_data['message'],
        $message_data['created_at'],
        $message_data['deleted_by_users'],
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

 
function saveapichat($message_data) {
    $table = $this->db->prefixTable('messages');
    $results = []; // Array to store query results

    // Build the SQL query
$sql = "INSERT INTO $table (from_user_id,to_user_id,message_id,subject,message,created_at,deleted_by_users) VALUES(?,?,?,?,?,?,?,?)";

    // Construct the values array
   $values = [
        $message_data['from_user_id'],
        $message_data['to_user_id'],
         $message_data['message_id'],
        $message_data['subject'],
        $message_data['message'],
        $message_data['created_at'],
        $message_data['deleted_by_users'],
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





    function get_details($options = array()) {
        $messages_table = $this->db->prefixTable('messages');
        $users_table = $this->db->prefixTable('users');

        $mode = get_array_value($options, "mode");
      

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $messages_table.id=$id";
        }

        $message_id = get_array_value($options, "message_id");
        if ($message_id) {
            $where .= " AND $messages_table.message_id=$message_id";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND ($messages_table.from_user_id=$user_id OR $messages_table.to_user_id=$user_id) ";
        }


        $join_with = "$messages_table.from_user_id";
        $join_another = "$messages_table.to_user_id";
        if ($user_id && $mode === "inbox") {
            $where .= " AND $messages_table.message_id=0 ";
        } else if ($user_id && $mode === "sent_items") {
            $where .= " AND $messages_table.message_id=0 ";
            $join_with = "$messages_table.to_user_id";
            $join_another = "$messages_table.from_user_id";
        }

        $last_message_id = get_array_value($options, "last_message_id");
        if ($last_message_id) {
            $where .= " AND $messages_table.id>$last_message_id";
        }


        $top_message_id = get_array_value($options, "top_message_id");
        if ($top_message_id) {
            $where .= " AND $messages_table.id<$top_message_id";
        }



        $limit = get_array_value($options, "limit");
        $limit = $limit ? $limit : "30";
        $offset = get_array_value($options, "offset");
        $offset = $offset ? $offset : "0";

        $sql = "SELECT * FROM (SELECT 0 AS reply_message_id, $messages_table.*, $users_table.email AS user_name, $users_table.image AS user_image, $users_table.user_type,  another_user.email AS another_user_name, another_user.id AS another_user_id, another_user.last_online AS another_user_last_online
        FROM $messages_table
        LEFT JOIN $users_table ON $users_table.id=$join_with
        LEFT JOIN $users_table AS another_user ON another_user.id=$join_another
        WHERE $messages_table.deleted=0 $where
        ORDER BY $messages_table.id DESC  LIMIT $offset, $limit) new_message ORDER BY id ASC";

        $query = $this->db->query($sql);

        $data = new \stdClass();
        $data->result = $query->getResult();
        $data->row = $query->getRow();
        $data->found_rows = 0;

        if ($message_id) {
            $data->found_rows = $this->db->query("SELECT COUNT(id) AS found_rows FROM $messages_table WHERE $messages_table.message_id = $message_id")->getRow()->found_rows;
        }

        return $data;
    }

    /*
     * prepare inbox/sent items list
     */

//   function get_list($options = array()) {
//     $messages_table = $this->db->prefixTable('messages');
//     $users_table = $this->db->prefixTable('users');

//     $mode = get_array_value($options, "mode");
//     $user_id = get_array_value($options, "user_id");

//     if ($user_id && $mode === "inbox") {
//         $where_user = "to_user_id";
//         $select_user = "from_user_id";
//     } else if ($user_id && $mode === "sent_items") {
//         $where_user = "from_user_id";
//         $select_user = "to_user_id";
//     }

//     $where = "";
//     $user_ids = get_array_value($options, "user_ids");
//     if ($user_ids) {
//         $where .= " AND $messages_table.$select_user IN($user_ids)";
//     }

//     // Ignore sql mode here 
//     $this->db->query("SET sql_mode = ''");

//     $sql = "SELECT  y.*, $messages_table.status, $messages_table.created_at, $messages_table.files,
//              $users_table.email AS user_name, $users_table.image AS user_image, $users_table.last_online
//             FROM (
//                 SELECT max(x.id) as id, main_message_id,  subject, IF(subject='', (SELECT subject FROM $messages_table WHERE id=main_message_id) ,'') as reply_subject, $select_user
//                     FROM (SELECT id, IF(message_id=0,id,message_id) as main_message_id, subject, $select_user 
//                             FROM $messages_table
//                           WHERE deleted=0 AND $where_user=$user_id $where AND FIND_IN_SET($user_id, $messages_table.deleted_by_users) = 0) x
//                 GROUP BY main_message_id) y
//             LEFT JOIN $users_table ON $users_table.id= y.$select_user
//             LEFT JOIN $messages_table ON $messages_table.id= y.id";

//     return $this->db->query($sql);
// }

function get_list($options = array())
{
    $messages_table = $this->db->prefixTable('messages');
    $users_table = $this->db->prefixTable('users');

    $mode = get_array_value($options, "mode");
    $user_id = (int) get_array_value($options, "user_id");
    $user_ids = get_array_value($options, "user_ids");

    if (!$user_id) {
        return $this->db->query("SELECT * FROM $messages_table WHERE 1=0");
    }

    if (!in_array($mode, array("inbox", "sent_items", "all"))) {
        $mode = "inbox";
    }

    $this->db->query("SET sql_mode = ''");

    /*
     * ALL MAIL:
     * Shows every thread where current user is either sender or receiver.
     * The visible user is always the other person from the latest message.
     */
    if ($mode === "all") {
        $allowed_users_where = "";

        if ($user_ids) {
            $allowed_users_where = " AND (
                ($messages_table.from_user_id=$user_id AND $messages_table.to_user_id IN($user_ids))
                OR
                ($messages_table.to_user_id=$user_id AND $messages_table.from_user_id IN($user_ids))
            )";
        }

        $sql = "SELECT 
                    y.id,
                    y.main_message_id,
                    latest_message.subject,
                    IF(latest_message.subject='', root_message.subject, '') AS reply_subject,
                    latest_message.status,
                    latest_message.created_at,
                    latest_message.files,
                    latest_message.from_user_id,
                    latest_message.to_user_id,
                    IF(latest_message.from_user_id=$user_id, 'sent_items', 'inbox') AS row_mode,
                    $users_table.email AS user_name,
                    $users_table.image AS user_image,
                    $users_table.last_online
                FROM (
                    SELECT 
                        MAX($messages_table.id) AS id,
                        IF($messages_table.message_id=0, $messages_table.id, $messages_table.message_id) AS main_message_id
                    FROM $messages_table
                    WHERE $messages_table.deleted=0
                        AND ($messages_table.from_user_id=$user_id OR $messages_table.to_user_id=$user_id)
                        $allowed_users_where
                        AND FIND_IN_SET($user_id, $messages_table.deleted_by_users) = 0
                    GROUP BY IF($messages_table.message_id=0, $messages_table.id, $messages_table.message_id)
                ) y
                LEFT JOIN $messages_table AS latest_message ON latest_message.id = y.id
                LEFT JOIN $messages_table AS root_message ON root_message.id = y.main_message_id
                LEFT JOIN $users_table ON $users_table.id = IF(
                    latest_message.from_user_id=$user_id,
                    latest_message.to_user_id,
                    latest_message.from_user_id
                )";

        return $this->db->query($sql);
    }

    /*
     * EXISTING INBOX / SENT LOGIC:
     * Preserved, with only from_user_id/to_user_id added for safer row mode handling.
     */
    if ($mode === "inbox") {
        $where_user = "to_user_id";
        $select_user = "from_user_id";
    } else {
        $where_user = "from_user_id";
        $select_user = "to_user_id";
    }

    $where = "";

    if ($user_ids) {
        $where .= " AND $messages_table.$select_user IN($user_ids)";
    }

    $sql = "SELECT  
                y.*, 
                $messages_table.status, 
                $messages_table.created_at, 
                $messages_table.files,
                $messages_table.from_user_id,
                $messages_table.to_user_id,
                IF($messages_table.from_user_id=$user_id, 'sent_items', 'inbox') AS row_mode,
                $users_table.email AS user_name, 
                $users_table.image AS user_image, 
                $users_table.last_online
            FROM (
                SELECT 
                    MAX(x.id) AS id, 
                    main_message_id,  
                    subject, 
                    IF(subject='', (SELECT subject FROM $messages_table WHERE id=main_message_id), '') AS reply_subject, 
                    $select_user
                FROM (
                    SELECT 
                        id, 
                        IF(message_id=0, id, message_id) AS main_message_id, 
                        subject, 
                        $select_user
                    FROM $messages_table
                    WHERE deleted=0 
                        AND $where_user=$user_id 
                        $where 
                        AND FIND_IN_SET($user_id, $messages_table.deleted_by_users) = 0
                ) x
                GROUP BY main_message_id
            ) y
            LEFT JOIN $users_table ON $users_table.id = y.$select_user
            LEFT JOIN $messages_table ON $messages_table.id = y.id";

    return $this->db->query($sql);
}

/*

    function get_chat_list($options = array()) {

        $messages_table = $this->db->prefixTable('messages');
        $users_table = $this->db->prefixTable('users');

        $login_user_id = get_array_value($options, "login_user_id");

        $where = "";
        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND ($messages_table.to_user_id=$user_id OR $messages_table.from_user_id=$user_id) ";
        }

        $user_ids = get_array_value($options, "user_ids");
        if ($user_ids) {
            $where .= " AND ($messages_table.to_user_id IN($user_ids) OR $messages_table.from_user_id IN($user_ids))";
        }

        $this->db->query("SET sql_mode = ''"); //ignor sql mode here

        $sql = "SELECT $messages_table.id, $messages_table.subject, $messages_table.from_user_id, IF(another_m.mex_created_at, another_m.mex_created_at, $messages_table.created_at) AS message_time, 
                IF(another_m.status, another_m.status, $messages_table.status) AS status, (SELECT from_user_id FROM $messages_table WHERE $messages_table.id=another_m.max_id) AS last_from_user_id,
                 $users_table.email  AS user_name, $users_table.image AS user_image, $users_table.last_online
                FROM `messages`
                LEFT JOIN (SELECT MAX(id) as max_id, MAX(message_id) as mex_message_id, MAX(created_at) as mex_created_at, MAX(status) as status FROM messages WHERE deleted=0 and  message_id!=0 GROUP BY message_id) AS another_m ON $messages_table.id=another_m.mex_message_id
                LEFT JOIN users ON ($users_table.id=$messages_table.from_user_id OR $users_table.id=$messages_table.to_user_id) AND $users_table.id != $login_user_id
                WHERE $messages_table.deleted=0 AND $messages_table.message_id=0 $where AND
                FIND_IN_SET($login_user_id, $messages_table.deleted_by_users) = 0 AND ($messages_table.from_user_id=$login_user_id OR $messages_table.to_user_id=$login_user_id)
                GROUP BY id
                ORDER BY message_time DESC LIMIT 0, 30";

        return $this->db->query($sql);
    }*/




 
  function get_chat_list($options = array()) {
    $call_service_table = $this->db->prefixTable('call_service');

$login_user_id = $options['login_user_id'];
//echo "Login User ID (Method 1): " . $login_user_id . "\n";
    
    $where = "WHERE 1";
    
    // Filter by user_id or user_ids
    $user_id = get_array_value($options, "user_id");
    if ($user_id) {
        $where .= " AND ($call_service_table.to_userid = $user_id OR $call_service_table.from_userid = $user_id)";
    } else {
        $user_ids = get_array_value($options, "user_ids");
        if ($user_ids) {
            $where .= " AND ($call_service_table.to_userid IN($user_ids) OR $call_service_table.from_userid IN($user_ids))";
        }
    }
    
    // Filter by node_id if provided
    $node_id = get_array_value($options, "node_id");
    if ($node_id) {
        $where .= " AND $call_service_table.node_id = '$node_id'";
    }
    
    // Filter by ticket_number if provided
    $ticket_number = get_array_value($options, "ticket_number");
    if ($ticket_number) {
        $where .= " AND $call_service_table.ticket_number = '$ticket_number'";
    }
    
    // Exclude deleted messages unless requested
    $include_deleted = get_array_value($options, "include_deleted", false);
    if (!$include_deleted) {
        $where .= " AND $call_service_table.deleted = 0";
    }
    
    $this->db->query("SET sql_mode = ''"); //ignore sql mode


    if($login_user_id == 1)
{

    $sql = "SELECT 
                node_id,
                created_at,
                model_name,
                message,
                to_userid,
                from_userid,
                ticket_number,
                flag,
                subject,
                status,
                message_id,
                deleted,
                deleted_by_user
            FROM $call_service_table";

}

else {






$sql5 = "SELECT * FROM users WHERE id = '$login_user_id'";
        $query = $this->db->query($sql5);
        $fact = $query->getRow(); 

        $vendor_name = $fact->brand_name;


if(!empty($vendor_name))
{

$sql = "SELECT 
                node_id,
                created_at,
                model_name,
                message,
                to_userid,
                from_userid,
                ticket_number,
                flag,
                subject,
                status,
                message_id,
                deleted,
                deleted_by_user
            FROM $call_service_table where Brand_name='$vendor_name'";

}

else{


$sql = "SELECT 
                node_id,
                created_at,
                model_name,
                message,
                to_userid,
                from_userid,
                ticket_number,
                flag,
                subject,
                status,
                message_id,
                deleted,
                deleted_by_user
            FROM $call_service_table where to_userid='$login_user_id' AND from_userid='$login_user_id'";



}



}


   // print_r($sql);

    return $this->db->query($sql);
}






/*


function get_chat_list($options = array()) {
    $call_service_table = $this->db->prefixTable('call_service');
    $users_table = $this->db->prefixTable('users'); // Add users table prefix
    
    $login_user_id = $options['login_user_id'];
    $where = "WHERE 1";
    
    // Common filters (applied to all queries)
    $user_id = get_array_value($options, "user_id");
    if ($user_id) {
        $where .= " AND ($call_service_table.to_userid = " . $this->db->escape($user_id) . 
                 " OR $call_service_table.from_userid = " . $this->db->escape($user_id) . ")";
    } else {
        $user_ids = get_array_value($options, "user_ids");
        if ($user_ids) {
            $where .= " AND ($call_service_table.to_userid IN(" . $this->db->escape($user_ids) . 
                     ") OR $call_service_table.from_userid IN(" . $this->db->escape($user_ids) . ")";
        }
    }
    
    // Additional filters
    $node_id = get_array_value($options, "node_id");
    if ($node_id) {
        $where .= " AND $call_service_table.node_id = '" . $this->db->escape($node_id) . "'";
    }
    
    $ticket_number = get_array_value($options, "ticket_number");
    if ($ticket_number) {
        $where .= " AND $call_service_table.ticket_number = '" . $this->db->escape($ticket_number) . "'";
    }
    
    if (!get_array_value($options, "include_deleted", false)) {
        $where .= " AND $call_service_table.deleted = 0";
    }
    
    $this->db->query("SET sql_mode = ''"); // Ignore sql mode

    if ($login_user_id == 1) {
        // Admin view - see all records
        $sql = "SELECT 
                    node_id,
                    created_at,
                    model_name,
                    message,
                    to_userid,
                    from_userid,
                    ticket_number,
                    flag,
                    subject,
                    status,
                    message_id,
                    deleted,
                    deleted_by_user
                FROM $call_service_table
                $where";
    } else {
        // Regular user view - filter by brand
        $brand_query = $this->db->query("SELECT brand_name FROM $users_table WHERE id = " . $this->db->escape($login_user_id));
        $brand_row = $brand_query->fetch_assoc();
        $brand_name = $brand_row['brand_name'] ?? '';
        
        if (!empty($brand_name)) {
            $where .= " AND $call_service_table.Brand_name = '" . $this->db->escape($brand_name) . "'";
        } else {
            // Fallback to user's own messages if no brand found
            $where .= " AND ($call_service_table.to_userid = " . $this->db->escape($login_user_id) . 
                     " OR $call_service_table.from_userid = " . $this->db->escape($login_user_id) . ")";
        }
        
        $sql = "SELECT 
                    node_id,
                    created_at,
                    model_name,
                    message,
                    to_userid,
                    from_userid,
                    ticket_number,
                    flag,
                    subject,
                    status,
                    message_id,
                    deleted,
                    deleted_by_user
                FROM $call_service_table
                $where";
    }
    
    return $this->db->query($sql);
}










*/

function save_message($data) {
    return $this->db->table($this->db->prefixTable('call_service'))->insert($data);
}

function mark_as_read($node_id, $user_id) {
    return $this->db->table($this->db->prefixTable('call_service'))
        ->where('node_id', $node_id)
        ->where('to_userid', $user_id)
        ->where('status', 'unread')
        ->update(['status' => 'read']);
}

function get_first_message($node_id) {
    return $this->db->table($this->db->prefixTable('call_service'))
        ->where('node_id', $node_id)
        ->orderBy('created_at', 'ASC')
        ->get()
        ->getRow();
}










public function get_messages_by_node($node_id, $login_user_id)
{
    $call_service = $this->db->prefixTable('call_service');
    $users = $this->db->prefixTable('users');

    return $this->db->query("
        SELECT 
            cs.*,
            u1.first_name,
            u1.last_name,
            u1.image as user_image,
            u2.first_name as to_first_name,
            u2.last_name as to_last_name,
            u2.image as to_user_image
        FROM {$call_service} cs
        LEFT JOIN {$users} u1 ON u1.id = cs.from_userid
        LEFT JOIN {$users} u2 ON u2.id = cs.to_userid
        WHERE cs.node_id = ?
        ORDER BY cs.created_at ASC
    ", [$node_id])->getResult();
}









    /* prepare notifications of new message */

    function get_notifications($user_id, $last_message_checke_at = "0", $active_message_id = 0) {
        $messages_table = $this->db->prefixTable('messages');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        if ($active_message_id) {
            $where = " AND $messages_table.message_id!=$active_message_id";
        }


        $sql = "SELECT $messages_table.id, $messages_table.message_id, $messages_table.created_at, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS user_name, $users_table.image AS user_image
        FROM $messages_table
        LEFT JOIN $users_table ON $users_table.id=$messages_table.from_user_id
        WHERE $messages_table.deleted=0 AND $messages_table.status='unread'  AND $messages_table.to_user_id = $user_id
        AND timestamp($messages_table.created_at)>timestamp('$last_message_checke_at') $where
        ORDER BY timestamp($messages_table.created_at) DESC";
        return $this->db->query($sql);
    }

    /* update message ustats */

    function set_message_status_as_read($message_id, $user_id = 0) {
        $messages_table = $this->db->prefixTable('messages');
        $sql = "UPDATE $messages_table SET status='read' WHERE $messages_table.to_user_id=$user_id AND ($messages_table.message_id=$message_id OR $messages_table.id=$message_id)";
        return $this->db->query($sql);
    }

  function count_unread_message($user_id = 0) {
        $messages_table = $this->db->prefixTable('messages');

        $sql = "SELECT COUNT($messages_table.id) as total
        FROM $messages_table
        WHERE $messages_table.deleted=0 AND $messages_table.status='unread'  AND $messages_table.to_user_id = $user_id";
        return $this->db->query($sql)->getRow()->total;
    }


public function count_unread_chat() {
    $sql = "SELECT COUNT(*) as unread_count FROM `call_service` WHERE `status` = 'unread'";
    $query = $this->db->query($sql);
    return $query->row(); // or row_array() depending on your usage
}




    function delete_messages_for_user($message_id = 0, $user_id = 0) {
        $messages_table = $this->db->prefixTable('messages');

        $sql = "UPDATE $messages_table SET $messages_table.deleted_by_users = CONCAT($messages_table.deleted_by_users,',',$user_id)
        WHERE $messages_table.id=$message_id OR $messages_table.message_id=$message_id";
        return $this->db->query($sql);
    }

    function clear_deleted_status($message_id = 0) {
        $messages_table = $this->db->prefixTable('messages');

        $sql = "UPDATE $messages_table SET $messages_table.deleted_by_users = ''
        WHERE $messages_table.id=$message_id OR $messages_table.message_id=$message_id";
        return $this->db->query($sql);
    }


function get_users_for_messaging($options = array()) {
    $users_table = $this->db->prefixTable('users');
    $clients_table = $this->db->prefixTable('clients');

    $where = "";

    $login_user_id = get_array_value($options, "login_user_id");

    $all_members = get_array_value($options, "all_members");
    $specific_members = get_array_value($options, "specific_members");
    $member_to_clients = get_array_value($options, "member_to_clients");
    if ($all_members) {
        if ($member_to_clients) {
            $where .= " AND ($users_table.user_type='User' OR $users_table.user_type='Brand')"; // Corrected
        } else {
            $where .= " AND ($users_table.user_type='User' OR $users_table.user_type='Brand')"; // Corrected
        }
    } else if ($specific_members) {
        if (is_array($specific_members) && count($specific_members)) {
            $specific_members = join(",", $specific_members);
        } else {
            $specific_members = '0';
        }

        if ($member_to_clients) {
            $where .= " AND ($users_table.id IN($specific_members) OR $users_table.user_type='Brand')"; // Corrected
        } else {
            $where .= " AND $users_table.id IN($specific_members)";
        }
    }

    if ($member_to_clients && !$all_members && !$specific_members) {
        $where .= " AND $users_table.user_type='Brand'"; // Corrected
    }

    $client_to_members = get_array_value($options, "client_to_members");
    $client_id = get_array_value($options, "client_id");
    if ($client_to_members) {
        if ($client_id) {
            $where .= " AND (FIND_IN_SET($users_table.id, '$client_to_members') OR $users_table.client_id=$client_id)";
        } else {
            $where .= " AND FIND_IN_SET($users_table.id, '$client_to_members')";
        }
    }

    if ($client_id && !$client_to_members) {
        $where .= " AND $users_table.client_id=$client_id";
    }

    $sql = "SELECT $users_table.id, $users_table.client_id, $users_table.user_type, $users_table.email, $users_table.first_name, $users_table.last_name, $clients_table.company_name,
            $users_table.image,  $users_table.job_title, $users_table.last_online
        FROM $users_table
        LEFT JOIN $clients_table ON $clients_table.id = $users_table.client_id AND $clients_table.deleted=0
        WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.id!=$login_user_id $where
        ORDER BY $users_table.user_type, $users_table.first_name ASC";

    return $this->db->query($sql);
}

    
} 