<?php
/**
 * Add/Verify response normalizer.
 *
 * Include this once from api.php and from iot_api_creation.php.
 * It does not change device add/verify logic. It only rewrites the
 * JSON those two actions already print:
 *   - a real success becomes success:true, node_id, and a plain message
 *   - a real failure stays a failure and gains success:false when missing
 * Every other action is left untouched.
 */

if (!function_exists('ef_add_verify_capture_node_id')) {
    function ef_add_verify_capture_node_id()
    {
        $nodeId = '';
        if (isset($_GET['node_id'])) {
            $nodeId = trim((string) $_GET['node_id']);
        }
        if ($nodeId === '' && isset($_POST['node_id'])) {
            $nodeId = trim((string) $_POST['node_id']);
        }
        $GLOBALS['ef_add_verify_node_id'] = $nodeId;
    }
}

if (!function_exists('ef_add_verify_extract_json')) {
    function ef_add_verify_extract_json($buffer)
    {
        $trim = trim((string) $buffer);
        if ($trim === '') {
            return null;
        }

        $decoded = json_decode($trim, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strrpos($trim, '{');
        if ($start === false) {
            return null;
        }

        $decoded = json_decode(substr($trim, $start), true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return null;
    }
}

if (!function_exists('ef_add_verify_is_failure')) {
    function ef_add_verify_is_failure(array $json)
    {
        if (array_key_exists('success', $json) && $json['success'] === false) {
            return true;
        }

        $status = '';
        if (isset($json['status']) && !is_array($json['status'])) {
            $status = strtolower(trim((string) $json['status']));
        }

        if ($status === 'failure' || $status === 'failed' || $status === 'error') {
            return true;
        }

        if (!empty($json['error_code'])) {
            return true;
        }

        return false;
    }
}

if (!function_exists('ef_add_verify_is_success')) {
    function ef_add_verify_is_success($action, array $json)
    {
        if (ef_add_verify_is_failure($json)) {
            return false;
        }

        if (array_key_exists('success', $json) && $json['success'] === true) {
            return true;
        }

        $status = '';
        if (isset($json['status']) && !is_array($json['status'])) {
            $status = strtolower(trim((string) $json['status']));
        }
        if ($status === 'success') {
            return true;
        }

        // check_device success currently falls through with only prod_category set.
        if ($action === 'check_device' && isset($json['prod_category']) && trim((string) $json['prod_category']) !== '') {
            return true;
        }

        return false;
    }
}

if (!function_exists('ef_add_verify_failure_message')) {
    function ef_add_verify_failure_message(array $json)
    {
        foreach (array('message', 'description', 'error') as $key) {
            if (isset($json[$key]) && is_string($json[$key]) && trim($json[$key]) !== '') {
                return trim($json[$key]);
            }
        }

        return '';
    }
}

if (!function_exists('ef_normalize_add_verify_response')) {
    function ef_normalize_add_verify_response($buffer)
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : '';
        if ($action !== 'add_wifi_node' && $action !== 'check_device') {
            return $buffer;
        }

        $json = ef_add_verify_extract_json($buffer);
        if (!is_array($json)) {
            return $buffer;
        }

        if (ef_add_verify_is_failure($json)) {
            $json['success'] = false;
            $message = ef_add_verify_failure_message($json);
            if ($message !== '' && empty($json['message'])) {
                $json['message'] = $message;
            }
            return json_encode($json);
        }

        if (!ef_add_verify_is_success($action, $json)) {
            return $buffer;
        }

        $nodeId = isset($GLOBALS['ef_add_verify_node_id']) ? trim((string) $GLOBALS['ef_add_verify_node_id']) : '';
        if ($nodeId === '' && isset($json['node_id']) && !is_array($json['node_id'])) {
            $nodeId = trim((string) $json['node_id']);
        }

        $out = array(
            'success' => true,
            'status'  => 'success',
            'message' => ($action === 'check_device') ? 'Device verified' : 'Device added successfully',
        );
        if ($nodeId !== '') {
            $out['node_id'] = $nodeId;
        }
        if (isset($json['prod_category']) && trim((string) $json['prod_category']) !== '') {
            $out['prod_category'] = $json['prod_category'];
        }

        return json_encode($out);
    }
}

if (!function_exists('ef_add_verify_response_start')) {
    function ef_add_verify_response_start()
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : '';
        if ($action !== 'add_wifi_node' && $action !== 'check_device') {
            return;
        }

        ef_add_verify_capture_node_id();
        if (!headers_sent()) {
            header('X-EF-Add-Verify: 1');
        }
        ob_start('ef_normalize_add_verify_response');
    }
}

ef_add_verify_response_start();
