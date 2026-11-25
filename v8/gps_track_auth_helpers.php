<?php
/**
 * GPS Track auth helpers for v8/api.php
 * Upload this file next to api.php. Do NOT replace whole api.php.
 *
 * Functions:
 *  - gps_track_resolve_tenant_id($row)
 *  - gps_track_append_login_identity(&$response, $row, $userid)
 *  - gps_track_handle_refreshtoken($db, &$response)
 */

if (!function_exists('gps_track_resolve_tenant_id')) {
    /**
     * Resolve tenant_id from a users row.
     * An explicit users.tenant_id wins (anything other than the demo placeholder).
     * A blank value, or a backfilled tenant_demo_001, yields to tenant_client_{client_id}
     * when the account has a client. Accounts with no client stay tenant_demo_001.
     * That demo id is the assigned tenant for those accounts, not a missing field.
     *
     * @param array|object $row
     */
    function gps_track_resolve_tenant_id($row)
    {
        $arr = is_array($row) ? $row : (array) $row;
        $stored = isset($arr['tenant_id']) ? trim((string) $arr['tenant_id']) : '';
        $clientId = isset($arr['client_id']) ? (int) $arr['client_id'] : 0;
        if ($stored !== '' && $stored !== 'tenant_demo_001') {
            return $stored;
        }
        if ($clientId > 0) {
            return 'tenant_client_' . $clientId;
        }

        return $stored !== '' ? $stored : 'tenant_demo_001';
    }
}

if (!function_exists('gps_track_append_login_identity')) {
    /**
     * Add user_id + tenant_id onto a successful login $response.
     * Call once after userlogin sets status/tokens/user_type.
     *
     * @param array $response
     * @param array|object $row users row from login query
     * @param int|string $userid
     */
    function gps_track_append_login_identity(array &$response, $row, $userid)
    {
        $resolved = gps_track_resolve_tenant_id($row);
        $response['user_id'] = (string) $userid;
        $response['tenant_id'] = $resolved;
        gps_track_persist_tenant_id($userid, $row, $resolved);
    }
}

if (!function_exists('gps_track_persist_tenant_id')) {
    /**
     * Write the resolved tenant back when the column is empty or still the
     * demo placeholder for a client account. Existing accounts pick this up
     * on the next userlogin or refreshtoken, not only new signups.
     *
     * @param array|object $row
     */
    function gps_track_persist_tenant_id($userid, $row, $resolved)
    {
        global $db;
        $id = (int) $userid;
        $resolved = trim((string) $resolved);
        if ($id <= 0 || $resolved === '' || !is_object($db)) {
            return;
        }

        $arr = is_array($row) ? $row : (array) $row;
        $stored = isset($arr['tenant_id']) ? trim((string) $arr['tenant_id']) : '';
        if ($stored === $resolved) {
            return;
        }
        if (!gps_track_users_has_tenant_column($db)) {
            return;
        }

        $safe = $db->escape($resolved);
        $db->query("UPDATE `users` SET `tenant_id`='$safe' WHERE `id`=$id");
    }
}

if (!function_exists('gps_track_users_has_tenant_column')) {
    function gps_track_users_has_tenant_column($db)
    {
        static $has = null;
        if ($has !== null) {
            return $has;
        }
        if (!is_object($db)) {
            $has = false;
            return false;
        }

        $row = $db->get_row("SHOW COLUMNS FROM `users` LIKE 'tenant_id'");
        if (empty($row)) {
            $db->query("ALTER TABLE `users` ADD COLUMN `tenant_id` VARCHAR(191) NULL");
            $row = $db->get_row("SHOW COLUMNS FROM `users` LIKE 'tenant_id'");
        }
        $has = !empty($row);

        return $has;
    }
}

if (!function_exists('gps_track_issue_tokens')) {
    /**
     * Issue id/access/refresh JWTs the same way as userlogin.
     *
     * @return array{idtoken:string,accesstoken:string,refreshtoken:string}
     */
    function gps_track_issue_tokens($userid, $phone)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'phone_verified' => true,
            'custom:user_id' => $userid,
            'token_use' => 'id',
            'auth_time' => 1667022242,
            'exp' => 1667025842,
            'iat' => 1667022242,
            'phone' => $phone,
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, 'Efactor', true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;

        $signature_access = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, 'Efactor_access', true);
        $base64UrlSignature_access = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature_access));
        $jwt_access = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature_access;

        $signature_refresh = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, 'Efactor_refresh', true);
        $base64UrlSignature_refresh = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature_refresh));
        $jwt_refresh = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature_refresh;

        return [
            'idtoken' => $jwt,
            'accesstoken' => $jwt_access,
            'refreshtoken' => $jwt_refresh,
        ];
    }
}

if (!function_exists('gps_track_handle_refreshtoken')) {
    /**
     * Handle action=refreshtoken|userrefresh.
     * Returns true if this request was handled (caller should echo+exit).
     *
     * @param object $db v8 DB helper (escape/num_rows/get_row/query)
     * @param array $response
     */
    function gps_track_handle_refreshtoken($db, array &$response)
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : '';
        if ($action !== 'refreshtoken' && $action !== 'userrefresh') {
            return false;
        }

        $req = file_get_contents('php://input');
        $json_data = json_decode($req, true);
        $refresh_in = '';
        if (is_array($json_data)) {
            if (isset($json_data['refreshtoken'])) {
                $refresh_in = trim((string) $json_data['refreshtoken']);
            } elseif (isset($json_data['refresh_token'])) {
                $refresh_in = trim((string) $json_data['refresh_token']);
            }
        }
        if ($refresh_in === '' && isset($_POST['refreshtoken'])) {
            $refresh_in = trim((string) $_POST['refreshtoken']);
        }
        if ($refresh_in === '' && isset($_POST['refresh_token'])) {
            $refresh_in = trim((string) $_POST['refresh_token']);
        }

        if ($refresh_in === '') {
            header('HTTP/1.1 400');
            $response['status'] = 'failure';
            $response['error_code'] = '101112';
            $response['description'] = 'refreshtoken is required';
            return true;
        }

        $refresh_esc = $db->escape($refresh_in);
        $query = "SELECT * FROM `users` WHERE refresh_token='$refresh_esc' AND deleted!=1 LIMIT 1";
        $num = $db->num_rows($query);
        if ($num <= 0) {
            header('HTTP/1.1 401');
            $response['status'] = 'failure';
            $response['error_code'] = '101113';
            $response['description'] = 'Invalid refreshtoken';
            return true;
        }

        $row = $db->get_row($query);
        $phone = $row['phone'];
        $userid = $row['id'];
        $email = $row['email'];
        $usertype = $row['user_type'];

        $tokens = gps_track_issue_tokens($userid, $phone);
        $jwt = $tokens['idtoken'];
        $jwt_access = $tokens['accesstoken'];
        $jwt_refresh = $tokens['refreshtoken'];

        $db->query("update users set id_token='$jwt',access_token='$jwt_access',refresh_token='$jwt_refresh' where id='$userid'");

        $response['status'] = 'success';
        $response['description'] = 'Token refreshed';
        $response['idtoken'] = $jwt;
        $response['accesstoken'] = $jwt_access;
        $response['refreshtoken'] = $jwt_refresh;
        $response['username'] = $phone;
        $response['email'] = $email;
        $response['user_type'] = $usertype;
        gps_track_append_login_identity($response, $row, $userid);

        return true;
    }
}
