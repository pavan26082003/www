<?php

namespace App\Controllers;

use App\Models\Modal_api_model;

/**
 * Dedicated controller for app/Views/items/modal_api.php.
 *
 * This controller owns only the Add/Edit API configuration workflow:
 * - open modal_api.php
 * - save / replace normalized API configuration
 * - delete configuration
 * - return vendor/product image metadata used by the modal
 */
class modal_api_controller extends Security_Controller
{
    private Modal_api_model $apiConfigurationModel;

    public function __construct()
    {
        parent::__construct();

        $class = '\\App\\Models\\Modal_api_model';
        if (!class_exists($class, false)) {
            foreach (array('Modal_api_model.php', 'modal_api_model.php') as $fileName) {
                $modelFile = APPPATH . 'Models/' . $fileName;
                if (is_file($modelFile)) {
                    require_once $modelFile;
                    break;
                }
            }
        }

        $this->apiConfigurationModel = new $class();
    }

    private function currentUserId(): ?int
    {
        $userId = session()->get('user_id') ?? ($this->login_user->id ?? null);
        return $userId ? (int) $userId : null;
    }

    public function modal($itemId = 0)
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return $this->response
                ->setStatusCode(401)
                ->setBody('Session user_id missing');
        }

        $itemId = (int) $itemId;
        if ($itemId <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setBody('Invalid item id.');
        }

        $product = $this->apiConfigurationModel->getProductMetaRow($itemId, $userId);
        if (!$product) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('API details row not found.');
        }

        $editConfigId = (int) ($this->request->getGet('edit_config_id') ?? 0);

        // Existing Parameter names come ONLY from Command Get Status -> Response fields.
        $existingParameterNames = $this->apiConfigurationModel
            ->getCommandGetStatusResponseParameterNames($itemId, $userId);

        $viewData = [
            'itemid'           => $itemId,
            'id'               => $itemId,
            'edit_config_id'   => $editConfigId,
            'edit_config_data' => null,
            'existing_parameter_names' => $existingParameterNames,

            'brand'         => (string) ($product->brand ?? ''),
            'category'      => (string) ($product->category ?? ''),
            'model_name'    => (string) ($product->modelname ?? ''),
            'modelno'       => (string) ($product->modelno ?? ''),
            'version'       => (string) ($product->version ?? 'N/A'),
            'batch'         => (string) ($product->batch ?? 'N/A'),
            'brand_image'   => (string) ($product->brand_image ?? ''),
            'product_image' => (string) ($product->product_image ?? ''),
        ];

        if ($editConfigId > 0) {
            $editConfig = $this->apiConfigurationModel
                ->getApiTreeConfigForEdit($editConfigId, $userId);

            if (
                !$editConfig ||
                (int) ($editConfig['product']['itemid'] ?? 0) !== $itemId
            ) {
                return $this->response
                    ->setStatusCode(404)
                    ->setBody('API configuration not found for editing.');
            }

            $viewData['edit_config_data'] = $editConfig;

            if (!empty($editConfig['product']['brandname'])) {
                $viewData['brand'] = $editConfig['product']['brandname'];
            }

            if (!empty($editConfig['product']['productname'])) {
                $viewData['category'] = $editConfig['product']['productname'];
            }

            if (!empty($editConfig['product']['modelname'])) {
                $viewData['model_name'] = $editConfig['product']['modelname'];
            }
        }

        return $this->template->view('items/modal_api', $viewData);
    }

    public function save()
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Session user_id missing.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        try {
            $masterJson = (string) $this->request->getPost('full_config_json');
            $allData = json_decode($masterJson, true);

            if (
                $masterJson === '' ||
                json_last_error() !== JSON_ERROR_NONE ||
                !is_array($allData)
            ) {
                throw new \InvalidArgumentException(
                    'Invalid full_config_json received.'
                );
            }

            foreach (['commands', 'responses', 'errors'] as $group) {
                if (!isset($allData[$group]) || !is_array($allData[$group])) {
                    $allData[$group] = [];
                }
            }

            $mainData = [
                'itemid'              => $this->request->getPost('itemid'),
                'brandname'           => $this->request->getPost('brandname'),
                'productname'         => $this->request->getPost('productname'),
                'modelname'           => $this->request->getPost('modelname'),
                'date'                => $this->request->getPost('date'),
                'parameter_name'      => $this->request->getPost('parameter_name'),
                'page_number'         => $this->request->getPost('page_number'),
                'sequence_number'     => $this->request->getPost('sequence_number'),

                // The actual frame arrays are authoritative.
                // This guarantees a deleted Response/Error frame is saved as 0.
                'command_frame_count'  => count($allData['commands']),
                'response_frame_count' => count($allData['responses']),
                'error_frame_count'    => count($allData['errors']),

                'page_names'          => $this->request->getPost('page_names'),
                'product_description' => $this->request->getPost('product_description'),
            ];

            $editConfigId = (int) ($this->request->getPost('config_id') ?? 0);

            if ($editConfigId > 0) {
                $configId = $this->apiConfigurationModel->replaceApiTreeConfig(
                    $editConfigId,
                    $mainData,
                    $allData,
                    $userId
                );
            } else {
                $configId = $this->apiConfigurationModel->saveApiTreeConfig(
                    $mainData,
                    $allData,
                    $userId
                );
            }

            $saveCheck = $this->apiConfigurationModel
                ->getApiTreeSaveCheck($configId);

            return $this->response->setJSON([
                'success'   => true,
                'message'   => $editConfigId > 0
                    ? 'API configuration updated successfully.'
                    : 'API configuration saved successfully.',
                'config_id' => (int) $configId,
                'data'      => [
                    'configId' => (int) $configId,
                    'config_id' => (int) $configId,
                ],
                'save_check' => $saveCheck,
                'database'   => $this->apiConfigurationModel
                    ->getConnectedDatabaseName(),
                'csrfHash'   => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'modal_api_controller::save failed: ' . $e->getMessage()
            );

            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrfHash' => csrf_hash(),
            ]);
        }
    }

    public function delete($configId = 0)
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Session user_id missing.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $configId = (int) $configId;
        $editConfig = $this->apiConfigurationModel
            ->getApiTreeConfigForEdit($configId, $userId);

        if (!$editConfig) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'API configuration not found.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $itemId = (int) ($editConfig['product']['itemid'] ?? 0);
        $deleted = $this->apiConfigurationModel
            ->deleteApiTreeConfig($configId, $userId);

        return $this->response
            ->setStatusCode($deleted ? 200 : 400)
            ->setJSON([
                'success' => (bool) $deleted,
                'message' => $deleted
                    ? 'API deleted successfully.'
                    : 'Unable to delete API config.',
                'redirect_url' => $deleted && $itemId > 0
                    ? site_url('items/viewapis/' . $itemId)
                    : null,
                'csrfHash' => csrf_hash(),
            ]);
    }

    /**
     * Lightweight image metadata endpoint used only by modal_api.php.
     * It replaces the dependency on Items::list_data1vendor().
     */
    public function vendorMeta($itemId = 0)
    {
        $userId = $this->currentUserId();

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'data' => [],
                'message' => 'Session user_id missing.',
            ]);
        }

        $product = $this->apiConfigurationModel
            ->getProductMetaRow((int) $itemId, $userId);

        if (!$product) {
            return $this->response->setJSON(['data' => []]);
        }

        return $this->response->setJSON([
            'data' => [[
                'id'            => (int) ($product->id ?? $itemId),
                'brand_name'    => (string) ($product->brand ?? ''),
                'product_name'  => (string) ($product->category ?? ''),
                'model_name'    => (string) ($product->modelname ?? ''),
                'model_no'      => (string) ($product->modelno ?? ''),
                'brand_image'   => (string) ($product->brand_image ?? ''),
                'product_image' => (string) ($product->product_image ?? ''),
            ]],
        ]);
    }
}
