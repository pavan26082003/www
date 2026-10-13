<?php
namespace App\Controllers;
use App\Models\vendor_modal;
/**
 * Vendor page controller extracted from the large Items controller.
 *
 * Requested filename: vendor_controller.php
 *
 * Existing vendor.php can remain unchanged if the old Items/* URLs are routed
 * to these methods in app/Config/Routes.php. Suggested compatibility routes:
 *
 * $routes->get('items/vendor', 'vendor_controller::vendor');
 * $routes->match(['get','post'], 'items/modal_vendorproduct', 'vendor_controller::modal_vendorproduct');
 * $routes->match(['get','post'], 'items/list_data1vendor', 'vendor_controller::list_data1vendor');
 * $routes->post('items/savevendorproduct', 'vendor_controller::savevendorproduct');
 * $routes->post('items/deletevendorproduct', 'vendor_controller::deletevendorproduct');
 *
 * Other actions shown in the Vendor card dropdown (View APIs, Add Pages,
 * Specifications, Auto Test, etc.) intentionally keep using Items/* because
 * their implementations belong to other parts of the existing Items module.
 */
class vendor_controller extends Security_Controller
{
    protected $Vendor_modal;
    public function __construct()
    {
        parent::__construct();
        $this->init_permission_checker('order');
        $this->Vendor_modal = new vendor_modal();
    }
    public function index()
    {
        return $this->vendor();
    }
    /** Load the existing items/vendor.php page. */
    public function vendor()
    {
        $view_data = array();
        $view_data['client_id'] = $this->login_user->client_id ?? null;
        return $this->template->rander('items/vendor', $view_data);
    }
    /** Add Model modal used by normal vendor users. */
    public function modal_vendorproduct()
    {
        $user_id = session()->get('user_id')
            ?? ($this->login_user->id ?? null);
        if (!$user_id) {
            return $this->response
                ->setStatusCode(401)
                ->setBody('Session user_id missing');
        }
        $view_data['categories_dropdown'] = $this->Vendor_modal
            ->get_cats($user_id)
            ->getResult();
        $view_data['brand_dropdown'] = $this->Vendor_modal
            ->get_brands($user_id)
            ->getResult();
        return $this->template->view('items/modal_vendorproduct', $view_data);
    }
    /**
     * Create or edit a vendor model.
     *
     * The model layer creates the default V2 APIs during a new insert, so the
     * legacy getstatusfunt22() call is deliberately not repeated here.
     */
    public function savevendorproduct()
    {
        $user_id = session()->get('user_id')
            ?? ($this->login_user->id ?? null);
        if (!$user_id) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Session user_id missing'
            ));
        }
        $id = (int) ($this->request->getPost('id') ?? 0);
        $item_data = array(
            'brand'       => $this->request->getPost('brand'),
            'category'    => $this->request->getPost('category'),
            'modelname'   => $this->request->getPost('modelname'),
            'modelno'     => $this->request->getPost('modelno'),
            'description' => $this->request->getPost('description'),
            'version'     => $this->request->getPost('version'),
            'batch'       => $this->request->getPost('batch')
        );
        if (
            trim((string) $item_data['modelname']) === '' ||
            trim((string) $item_data['modelno']) === ''
        ) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Model name and model number are required.'
            ));
        }
        // Edit Model logo support. Keep normal model edits backward compatible.
        $logoMode = strtolower(trim((string) $this->request->getPost('model_logo_mode')));
        $newLogoPath = '';
        $logoFilename = '';

        if (in_array($logoMode, array('upload', 'generate', 'keep'), true)) {
            // Require the migration: silently dropping the logo would be misleading.
            $db = \Config\Database::connect();
            if (!$db->fieldExists('model_logo', 'vendorproducts')) {
                return $this->response->setStatusCode(422)->setJSON(array(
                    'success' => false,
                    'message' => 'Database column vendorproducts.model_logo is missing. Run the included SQL migration first.'
                ));
            }

            if ($id > 0) {
                $owned = $db->table('vendorproducts')->select('id, model_logo')
                    ->where('id', $id)->where('user_id', $user_id)->get()->getRowArray();
                if (!$owned) {
                    return $this->response->setStatusCode(403)->setJSON(array(
                        'success' => false, 'message' => 'Model not found or access denied.'
                    ));
                }
                $logoFilename = trim((string) ($owned['model_logo'] ?? ''));
            }

            if ($logoMode !== 'keep') {
                try {
                    $targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'files'
                        . DIRECTORY_SEPARATOR . 'model_logo' . DIRECTORY_SEPARATOR;
                    if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                        throw new \RuntimeException('Unable to create files/model_logo directory.');
                    }
                    if (!is_writable($targetDir)) {
                        throw new \RuntimeException('files/model_logo is not writable by the PHP process.');
                    }
                    $logoFilename = 'model_' . bin2hex(random_bytes(12));

                    if ($logoMode === 'upload') {
                        $file = $this->request->getFile('model_logo_file');
                        if (!$file || !$file->isValid()) {
                            throw new \RuntimeException('Choose a valid image before saving (PNG, JPG or WEBP).');
                        }
                        if ($file->getSize() < 1 || $file->getSize() > 2 * 1024 * 1024) {
                            throw new \RuntimeException('Model logo must be 2 MB or smaller.');
                        }
                        $tempName = $file->getTempName();
                        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tempName);
                        $formats = array('image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp');
                        if (!isset($formats[$mime]) || @getimagesize($tempName) === false) {
                            throw new \RuntimeException('Invalid image. Use PNG, JPG or WEBP.');
                        }
                        $logoFilename .= '.' . $formats[$mime];
                        $file->move($targetDir, $logoFilename);
                        $newLogoPath = $targetDir . $logoFilename;
                        if (!is_file($newLogoPath)) {
                            throw new \RuntimeException('The uploaded logo could not be saved.');
                        }
                    } else {
                        // Server-generated SVG from the model name; no PHP GD extension required.
                        $modelName = trim((string) ($item_data['modelname'] ?? ''));
                        $parts = preg_split('/[\s_-]+/', $modelName, -1, PREG_SPLIT_NO_EMPTY);
                        $initials = count($parts) >= 2
                            ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
                            : strtoupper(substr($modelName, 0, 2));
                        $initials = htmlspecialchars($initials ?: 'M', ENT_QUOTES | ENT_XML1, 'UTF-8');
                        $svg = '<?xml version="1.0" encoding="UTF-8"?>'
                            . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="256" height="256">'
                            . '<defs><linearGradient id="logoGrad" x1="0" y1="0" x2="1" y2="1">'
                            . '<stop stop-color="#2B2D42"/><stop offset="1" stop-color="#6B8F71"/>'
                            . '</linearGradient></defs><rect width="256" height="256" rx="45" fill="url(#logoGrad)"/>'
                            . '<circle cx="201" cy="58" r="47" fill="#E6B980" opacity=".22"/>'
                            . '<text x="128" y="150" text-anchor="middle" font-size="84" font-weight="bold" '
                            . 'font-family="Arial,sans-serif" fill="#ffffff">' . $initials . '</text>'
                            . '<text x="128" y="210" text-anchor="middle" font-size="17" letter-spacing="4" '
                            . 'font-family="Arial,sans-serif" fill="#E6B980">MODEL</text></svg>';
                        $logoFilename .= '.svg';
                        $newLogoPath = $targetDir . $logoFilename;
                        if (file_put_contents($newLogoPath, $svg, LOCK_EX) === false) {
                            throw new \RuntimeException('Unable to generate the model logo file.');
                        }
                    }
                } catch (\Throwable $exception) {
                    if ($newLogoPath !== '' && is_file($newLogoPath)) {
                        @unlink($newLogoPath);
                    }
                    log_message('error', 'vendor model logo upload: ' . $exception->getMessage());
                    return $this->response->setStatusCode(422)->setJSON(array(
                        'success' => false, 'message' => $exception->getMessage()
                    ));
                }
            }
            if ($logoFilename !== '') {
                $item_data['model_logo'] = $logoFilename;
            }
        }

        // Keep the original persistence path: it also creates all four V2 APIs,
        // the default Status page, and page links for newly-created models.
        $result = $this->Vendor_modal->savevendorproduct(
            $item_data,
            $user_id,
            $id
        );

        if ((!is_numeric($result) || (int) $result <= 0) && $newLogoPath !== '' && is_file($newLogoPath)) {
            @unlink($newLogoPath); // no orphaned file on duplicate/DB/default-API failures
        }
        if (is_string($result)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(array(
                    'success' => false,
                    'message' => $result
                ));
            }
            $view_data['error'] = $result;
            return $this->template->rander('items/vendor', $view_data);
        }
        $savedId = (int) $result;
        if ($savedId <= 0) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Unable to save model details or create the default APIs.'
            ));
        }
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(array(
                'success' => true,
                'id'      => $savedId,
                'message' => $id
                    ? 'Model updated successfully.'
                    : 'Model saved successfully. Default APIs, Status page, and page linking created.'
            ));
        }
        return redirect()->to(get_uri('items/vendor'));
    }
    /** Delete a vendor model from the card page. */
    public function deletevendorproduct()
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        if ($id <= 0) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Invalid model ID.'
            ));
        }
        $deleted = $this->Vendor_modal->deletevendorproduct($id);
        return $this->response->setJSON(array(
            'success' => (bool) $deleted,
            'message' => $deleted
                ? app_lang('record_deleted')
                : app_lang('record_cannot_be_deleted')
        ));
    }
    /** JSON data source used by the Vendor card grid. */
    public function list_data1vendor()
    {
        $user_id = session()->get('user_id')
            ?? ($this->login_user->id ?? null);
        if (!$user_id) {
            return $this->response->setJSON(array(
                'data' => array(),
                'error' => 'Session user_id missing'
            ));
        }
        $normalizeName = static function ($value) {
            $value = trim((string) $value);
            $value = preg_replace('/\s+/', ' ', $value);
            return strtolower($value);
        };
        $db = \Config\Database::connect();
        // Brand logos are intentionally global, matching the current page.
        $brandRows = $db
            ->table('brands')
            ->select('id, name, brandlogo')
            ->get()
            ->getResult();
        $brandImageMap = array();
        foreach ($brandRows as $brand) {
            $brandName = trim((string) ($brand->name ?? ''));
            $brandLogo = trim((string) ($brand->brandlogo ?? ''));
            if ($brandName === '') {
                continue;
            }
            $brandImageMap[$normalizeName($brandName)] = $brandLogo;
        }
        $productRows = $db
            ->table('productcats')
            ->select('id, name, Product_image')
            ->get()
            ->getResult();
        $productImageMap = array();
        foreach ($productRows as $product) {
            $productName = trim((string) ($product->name ?? ''));
            $productImage = trim((string) ($product->Product_image ?? ''));
            if ($productName === '') {
                continue;
            }
            $productImageMap[$normalizeName($productName)] = $productImage;
        }
        $list_data = $this->Vendor_modal
            ->get_vendorproducts($user_id)
            ->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $row = $this->_make_item_row1_vendor($data);
            $brandName = trim((string) (
                $data->brand
                ?? $data->brand_name
                ?? $data->brandname
                ?? $row['brand_name']
                ?? ''
            ));
            if (strtolower($brandName) === 'no brand') {
                $brandName = '';
            }
            $productName = trim((string) (
                $data->category
                ?? $data->product_name
                ?? $data->product_type
                ?? $row['product_name']
                ?? ''
            ));
            if ($brandName !== '') {
                $row['brand_name'] = $brandName;
            }
            $brandFilename = '';
            if ($brandName !== '') {
                $brandFilename = $brandImageMap[$normalizeName($brandName)] ?? '';
            }
            $productFilename = '';
            if ($productName !== '') {
                $productFilename = $productImageMap[$normalizeName($productName)] ?? '';
            }
            $row['brand_image'] = $brandFilename !== ''
                ? base_url('files/brand_logo/' . rawurlencode(basename($brandFilename)))
                : '';
            $row['product_image'] = $productFilename !== ''
                ? base_url('files/brand_logo/' . rawurlencode(basename($productFilename)))
                : '';

            // The top-left card icon must display the *model* logo.
            $modelLogo = trim((string) ($data->model_logo ?? ''));
            $row['model_logo'] = $modelLogo;
            $row['model_logo_url'] = $modelLogo !== ''
                ? base_url('files/model_logo/' . rawurlencode(basename($modelLogo)))
                : '';

            $result[] = $row;
        }
        return $this->response->setJSON(array('data' => $result));
    }
    // private function _make_api_tree_row($data) {
    //     $configId = (int) $data->id;
    //     $itemId = (int) $data->item_id;
    //     $parameterLabel = $data->parameter_name ? $data->parameter_name : 'N/A';
    //     $normalizedParameter = strtolower(trim((string) $parameterLabel));
    //     $action = '<button type="button" class="api-action-btn view-api-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">View API</button>';
    //     /*
    //      * Default API edit policy:
    //      *   Command Get status -> editable
    //      *   Send Data / Display Log / Display -> read-only
    //      * Other manually-created APIs keep their normal Edit button.
    //      */
    //     $readOnlyDefaults = array(
    //         'send data',
    //         'display log',
    //         'display'
    //     );
    //     if (in_array($normalizedParameter, $readOnlyDefaults, true)) {
    //         $editControl = '<button type="button" class="api-update-btn api-edit-btn" disabled title="This default API is read-only">Locked</button>';
    //     } else {
    //         $editControl = '<button type="button" class="api-update-btn api-edit-btn" data-config-id="' . $configId . '" data-item-id="' . $itemId . '">Edit</button>';
    //     }
    //     $update = $editControl
    //         . '<button type="button" class="api-update-btn api-delete-btn" data-config-id="' . $configId . '">Delete</button>';
    //     return array(
    //         '<b>' . htmlspecialchars($parameterLabel, ENT_QUOTES, 'UTF-8') . '</b> <span class="api-version-badge api-version-v2">V2</span>',
    //         $action,
    //         $update
    //     );
    // }
    /** Build one JSON card row for items/vendor.php. */
    private function _make_item_row1_vendor($data)
    {
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
            'model_logo' => (string) $getValue(array('model_logo'), ''),
            'edit_url' => get_uri('items/viewapis/' . $id . '?modelno=' . $modelNo),
            'copy_url' => get_uri('items/modal_api/' . $id),
            'view_url' => get_uri('items/viewapis/' . $id . '?modelno=' . $modelNo),
            'delete_url' => get_uri('items/deletevendorproduct'),
            'actions' => array(
                array('label' => 'Add API/Product', 'icon' => 'fa-plus-circle', 'url' => get_uri('items/modal_api/' . $id), 'class' => 'modal-action', 'title' => 'Add API/Product'),
                array('label' => 'View APIs', 'icon' => 'fa-eye', 'url' => get_uri('items/viewapis/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'Add Pages', 'icon' => 'fa-file-text', 'url' => get_uri('items/modal_pages/' . $id . '?modelno=' . $modelNo), 'class' => 'modal-action', 'title' => 'Add Page Names'),
                array('label' => 'Linking Page', 'icon' => 'fa-link', 'url' => get_uri('items/api_page_linking/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'Linked View UI', 'icon' => 'fa-eye', 'url' => get_uri('items/view_page_ui/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'Specifications', 'icon' => 'fa-cogs', 'url' => get_uri('items/specifications/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'Add API Frame Format', 'icon' => 'fa-plus-circle', 'url' => get_uri('items/api_frame_numbers/' . $id), 'class' => 'modal-action', 'title' => 'Add API Frame Format'),
                array('label' => 'Add Description', 'icon' => 'fa-plus-circle', 'url' => get_uri('items/description_api/' . $id), 'class' => 'modal-action', 'title' => 'Add Description'),
                array('label' => 'Auto Test Form', 'icon' => 'fa-flask', 'url' => get_uri('items/auto_test/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'Auto Test Start', 'icon' => 'fa-flask', 'url' => get_uri('items/auto_test_start/' . $id . '?modelno=' . $modelNo), 'class' => ''),
                array('label' => 'View UI Type', 'icon' => 'fa-desktop', 'url' => get_uri('items/callDisplayFunction/' . $modelNo . '?category=' . $category), 'class' => ''),
                array('label' => 'Device Select', 'icon' => 'fa-cube', 'url' => get_uri('items/callDisplayId'), 'class' => ''),
                array('label' => 'Sticker Update', 'icon' => 'fa-sticky-note', 'url' => get_uri('items/sticker_update/' . $id . '?modelno=' . $modelNo . '&category=' . $category . '&modelname=' . $modelName), 'class' => ''),
                array('label' => 'Confirm', 'icon' => 'fa-check', 'url' => get_uri('items/callConfirmDisplayFunction/' . $modelNo), 'class' => 'confirm-action'),
                array('label' => 'Warranty Card', 'icon' => 'fa-shield', 'url' => 'https://efactor.tech/warranty_form.html?modelname=' . $modelName, 'class' => 'external'),
                array('label' => 'Delete', 'icon' => 'fa-trash', 'url' => get_uri('items/deletevendorproduct'), 'class' => 'delete-action')
            )
        );
    }
    /** Copy complete model/modal to a new model name. */
    public function copy_modal()
    {
        $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);
        $view_data = array();
        $models = array();
        try {
            $models = $this->Vendor_modal->get_vendorproducts($user_id)->getResult();
        } catch (\Throwable $e) {
            log_message('error', 'copy_modal get_vendorproducts failed: ' . $e->getMessage());
            $models = array();
        }
        try {
            $models = $this->enrichCopyModalModels($models);
        } catch (\Throwable $e) {
            log_message('error', 'copy_modal enrich failed: ' . $e->getMessage());
        }
        $view_data['models'] = $models;
        return $this->template->rander('items/copy_modal', $view_data);
    }
    /**
     * Attach brand/product image URLs for Copy Modal preview options.
     */
    protected function enrichCopyModalModels($models)
    {
        if (empty($models)) {
            return array();
        }
        $normalizeName = function ($value) {
            $value = trim((string) $value);
            $value = preg_replace('/\s+/', ' ', $value);
            return strtolower((string) $value);
        };
        $db = \Config\Database::connect();
        $brandImageMap = array();
        $productImageMap = array();
        try {
            if ($db->tableExists('brands')) {
                $fields = $db->getFieldNames('brands');
                $lookup = array();
                foreach ($fields as $field) {
                    $lookup[strtolower($field)] = $field;
                }
                $nameField = isset($lookup['name']) ? $lookup['name'] : null;
                $logoField = isset($lookup['brandlogo']) ? $lookup['brandlogo'] : (isset($lookup['brand_logo']) ? $lookup['brand_logo'] : null);
                if ($nameField && $logoField) {
                    foreach ($db->table('brands')->select("$nameField, $logoField")->get()->getResultArray() as $brand) {
                        $brandName = trim((string) (isset($brand[$nameField]) ? $brand[$nameField] : ''));
                        if ($brandName === '') {
                            continue;
                        }
                        $brandImageMap[$normalizeName($brandName)] = trim((string) (isset($brand[$logoField]) ? $brand[$logoField] : ''));
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'copy_modal brands map failed: ' . $e->getMessage());
        }
        try {
            if ($db->tableExists('productcats')) {
                $fields = $db->getFieldNames('productcats');
                $lookup = array();
                foreach ($fields as $field) {
                    $lookup[strtolower($field)] = $field;
                }
                $nameField = isset($lookup['name']) ? $lookup['name'] : null;
                $imageField = isset($lookup['product_image']) ? $lookup['product_image'] : (isset($lookup['prodimg']) ? $lookup['prodimg'] : null);
                if ($nameField && $imageField) {
                    foreach ($db->table('productcats')->select("$nameField, $imageField")->get()->getResultArray() as $product) {
                        $productName = trim((string) (isset($product[$nameField]) ? $product[$nameField] : ''));
                        if ($productName === '') {
                            continue;
                        }
                        $productImageMap[$normalizeName($productName)] = trim((string) (isset($product[$imageField]) ? $product[$imageField] : ''));
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'copy_modal productcats map failed: ' . $e->getMessage());
        }
        $toImageUrl = function ($filename) {
            $filename = trim((string) $filename);
            if ($filename === '') {
                return '';
            }
            if (preg_match('~^https?://~i', $filename) || strpos($filename, '/') === 0) {
                return $filename;
            }
            return base_url('files/brand_logo/' . rawurlencode(basename($filename)));
        };
        $enriched = array();
        foreach ($models as $m) {
            $row = is_object($m) ? get_object_vars($m) : (array) $m;
            $brandName = trim((string) (
                isset($row['brand_name']) ? $row['brand_name'] : (isset($row['brand']) ? $row['brand'] : '')
            ));
            $categoryName = trim((string) (
                isset($row['category_name']) ? $row['category_name'] : (isset($row['category']) ? $row['category'] : (isset($row['product_name']) ? $row['product_name'] : ''))
            ));
            $modelName = trim((string) (
                isset($row['model_name']) ? $row['model_name'] : (isset($row['modelname']) ? $row['modelname'] : (isset($row['name']) ? $row['name'] : ''))
            ));
            $modelNo = trim((string) (
                isset($row['model_no']) ? $row['model_no'] : (isset($row['modelno']) ? $row['modelno'] : $modelName)
            ));
            if (strtolower($brandName) === 'no brand') {
                $brandName = '';
            }
            $brandKey = $normalizeName($brandName);
            $productKey = $normalizeName($categoryName);
            $brandFile = ($brandKey !== '' && isset($brandImageMap[$brandKey])) ? $brandImageMap[$brandKey] : '';
            $productFile = ($productKey !== '' && isset($productImageMap[$productKey])) ? $productImageMap[$productKey] : '';
            $row['id'] = isset($row['id']) ? $row['id'] : 0;
            $row['modelname'] = $modelName;
            $row['model_name'] = $modelName;
            $row['model_no'] = $modelNo;
            $row['brand'] = $brandName;
            $row['brand_name'] = $brandName;
            $row['category'] = $categoryName;
            $row['category_name'] = $categoryName;
            $row['brand_image'] = $toImageUrl($brandFile);
            $row['product_image'] = $toImageUrl($productFile);
            $enriched[] = (object) $row;
        }
        return $enriched;
    }
    public function save_copy_modal()
    {
        $sourceId = (int) $this->request->getPost('source_modal');
        $newName = trim((string) $this->request->getPost('new_modal_name'));
        $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);
        if ($sourceId <= 0 || $newName === '') {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Select existing modal and enter new name',
            ));
        }
        try {
            $result = $this->Vendor_modal->copyModal($sourceId, $newName, $user_id);
            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'save_copy_modal failed: ' . $e->getMessage());
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Copy failed: ' . $e->getMessage(),
            ));
        }
    }
    /**
     * Preview data for Copy Modal (brand/product logos + API names).
     */
    public function copy_modal_preview()
    {
        $user_id = session()->get('user_id') ?? ($this->login_user->id ?? null);
        $id = (int) ($this->request->getGet('id') ?: $this->request->getPost('id'));
        $model = trim((string) ($this->request->getGet('model') ?: $this->request->getPost('model')));
        try {
            $data = $this->Vendor_modal->getCopyModalPreview($id, $model, $user_id);
            return $this->response->setJSON(array(
                'status' => true,
                'data' => $data,
            ));
        } catch (\Throwable $e) {
            log_message('error', 'copy_modal_preview failed: ' . $e->getMessage());
            return $this->response->setJSON(array(
                'status' => false,
                'message' => $e->getMessage(),
                'data' => array(
                    'name' => $model,
                    'brand' => '',
                    'category' => '',
                    'brand_image' => '',
                    'product_image' => '',
                    'api_names' => array(),
                ),
            ));
        }
    }
}
