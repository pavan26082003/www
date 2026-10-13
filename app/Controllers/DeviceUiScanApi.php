<?php

namespace App\Controllers;

use App\Models\DeviceScanModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class DeviceUiScanApi extends ResourceController
{
    /**
     * Scan endpoint - receives scan data from mobile app
     * 
     * @param string $company Company identifier
     * @param string $username Username
     * @return ResponseInterface
     */
    public function scan($company, $username)
    {
        // Load helper
        helper('api_response');
        
        try {
            // Get request method
            $method = $this->request->getMethod();
            
            // Only allow POST requests for actual scanning
            if ($method !== 'post') {
                return $this->respond([
                    'status' => false,
                    'message' => 'Method not allowed. Please use POST for scanning.'
                ], 405);
            }
            
            // Get JSON input
            $json = $this->request->getJSON(true);
            
            // Check if JSON is empty - means no data sent
            if (empty($json) || !$json) {
                return $this->respond([
                    'status' => false,
                    'message' => 'Not scanned yet. No data received. Send scan_value in JSON payload.'
                ], 400);
            }
            
            // Get scan value
            $scanValue = $json['scan_value'] ?? '';
            log_message('debug', 'DeviceUiScanApi scan_value: ' . var_export($scanValue, true));
            
            // Check if scan_value is empty
            if (empty($scanValue)) {
                return $this->respond([
                    'status' => false,
                    'message' => 'Not scanned. scan_value is required. Please scan a QR code or barcode.'
                ], 400);
            }
            
            // Save to database
            $model = new DeviceScanModel();
            
            $data = [
                'company' => $company,
                'username' => $username,
                'scan_value' => $scanValue,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $insertId = $model->saveScan($data);
            
            if ($insertId) {
                return $this->respond([
                    'status' => true,
                    'message' => 'Scan saved successfully',
                    'data' => [
                        'id' => $insertId,
                        'scan_value' => $scanValue,
                        'company' => $company,
                        'username' => $username,
                        'scanned_at' => date('Y-m-d H:i:s')
                    ]
                ], 201);
            } else {
                return $this->respond([
                    'status' => false,
                    'message' => 'Failed to save scan. Please try again.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'DeviceUiScanApi error: ' . $e->getMessage());
            return $this->respond([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check scan status - GET method to check if scanned
     * 
     * @param string $company Company identifier
     * @param string $username Username
     * @return ResponseInterface
     */
    public function checkStatus($company, $username)
    {
        helper('api_response');
        
        try {
            $model = new DeviceScanModel();
            
            // Get latest scan for this user/company
            $scan = $model->where('company', $company)
                         ->where('username', $username)
                         ->orderBy('created_at', 'DESC')
                         ->first();
            
            if ($scan) {
                return $this->respond([
                    'status' => true,
                    'message' => 'Scan found',
                    'data' => [
                        'scanned' => true,
                        'scan_value' => $scan['scan_value'],
                        'scanned_at' => $scan['created_at']
                    ]
                ], 200);
            } else {
                return $this->respond([
                    'status' => false,
                    'message' => 'Not scanned yet. Please scan a QR code.',
                    'data' => [
                        'scanned' => false
                    ]
                ], 200);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'DeviceUiScanApi checkStatus error: ' . $e->getMessage());
            return $this->respond([
                'status' => false,
                'message' => 'Error checking scan status'
            ], 500);
        }
    }
}