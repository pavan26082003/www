<?php

namespace App\Controllers;

use App\Models\Modal_pages_model;

class Modal_pages_controller extends Security_Controller {

    /** @var Modal_pages_model */
    protected $Modal_pages_model;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");

        $class = '\\App\\Models\\Modal_pages_model';
        if (!class_exists($class, false)) {
            foreach (array('Modal_pages_model.php', 'modal_pages_model.php') as $fileName) {
                $modelFile = APPPATH . 'Models/' . $fileName;
                if (is_file($modelFile)) {
                    require_once $modelFile;
                    break;
                }
            }
        }

        $this->Modal_pages_model = class_exists($class, false) ? new $class() : null;
    }

    private function currentUserId()
    {
        if (!empty($this->login_user) && !empty($this->login_user->id)) {
            return $this->login_user->id;
        }

        $postUserId = $this->request->getPost('user_id');
        if ($postUserId !== null && $postUserId !== '') {
            return $postUserId;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        return '';
    }

    private function flattenPageNames($pageRows)
    {
        $pages = array();

        if (!is_array($pageRows)) {
            return $pages;
        }

        foreach ($pageRows as $pageRow) {
            $rawName = trim((string) (isset($pageRow['page_names']) ? $pageRow['page_names'] : ''));
            if ($rawName === '') {
                continue;
            }

            $names = (strpos($rawName, ',') !== false) ? explode(',', $rawName) : array($rawName);

            foreach ($names as $pageName) {
                $pageName = trim((string) $pageName);
                if ($pageName !== '' && !in_array($pageName, $pages, true)) {
                    $pages[] = $pageName;
                }
            }
        }

        return $pages;
    }

    private function extractPages($user_id, $modelno, $id = null)
    {
        if (!$this->Modal_pages_model || !method_exists($this->Modal_pages_model, 'extract_pages')) {
            return array();
        }

        $pages = $this->Modal_pages_model->extract_pages($user_id, $modelno, $id);
        return is_array($pages) ? $pages : array();
    }

    public function modal_pages($id)
    {
        $user_id = $this->currentUserId();

        $modelno = trim((string) $this->request->getVar('modelno'));
        if ($this->Modal_pages_model) {
            $modelno = $this->Modal_pages_model->resolveModelNoForVendorProduct($id, $modelno);
        }
        $parameter_names = method_exists($this->Items_model, 'extract_modelname_config')
            ? $this->Items_model->extract_modelname_config($id, $user_id)
            : array();
        $page_names = $this->extractPages($user_id, $modelno, $id);
        return view('items/modal_pages', [
            'user_id' => $user_id,
            'modal_id' => $id,
            'modelno' => $modelno,
            'params' => $parameter_names,
            'page_names' => $page_names,
        ]);
    }

    public function modal_pages_data($id)
    {
        $user_id = $this->currentUserId();
        $modelno = trim((string) $this->request->getVar('modelno'));
        if ($this->Modal_pages_model) {
            $modelno = $this->Modal_pages_model->resolveModelNoForVendorProduct($id, $modelno);
        }
        $page_names = $this->extractPages($user_id, $modelno, $id);

        return $this->response->setJSON(array(
            'success' => true,
            'pages' => $this->flattenPageNames($page_names)
        ));
    }

    function save_page_seq() {
        $user_id = $this->currentUserId();

        $item_data = array(
            "modelSelect" => $this->request->getPost('modelno'),
            "pageSelect" => $this->request->getPost('modal_id'),
            "page_seq" => $this->request->getPost('addedValuesInput')
        );

        if (empty($item_data['modelSelect']) || empty($item_data['pageSelect']) || empty($item_data['page_seq'])) {
            echo "Missing page sequence data!";
            return;
        }

        if ($this->Modal_pages_model && $this->Modal_pages_model->save_page_seq($item_data, $user_id)) {
            echo "Page sequence saved successfully!";
        } else {
            echo "Error saving page sequence!";
        }
    }

    function save_pages()
    {
        $session_user_id = $this->currentUserId();
        $page_names_combined = trim((string) $this->request->getPost('page_names_combined'));
        $item_data = array(
            "page_name" => (array) $this->request->getPost('input_box'),
            "page_extension" => (array) $this->request->getPost('dropdown')
        );

        $page_data = array(
            "user_id" => $session_user_id ?: $this->request->getPost('user_id'),
            "modelno" => $this->request->getPost('modelno'),
            "modal_id" => $this->request->getPost('modal_id'),
        );

        $combined_data = array();

        if ($page_names_combined !== '') {
            foreach (explode(',', $page_names_combined) as $page_name) {
                $page_name = trim((string) $page_name);

                if ($page_name !== '') {
                    $combined_data[] = $page_name;
                }
            }
        } else {
            foreach ($item_data['page_name'] as $index => $page_name) {
                $page_name = trim((string) $page_name);
                $page_extension = isset($item_data['page_extension'][$index]) ? trim((string) $item_data['page_extension'][$index]) : '';

                if ($page_name === '' || $page_extension === '') {
                    continue;
                }

                $combined_data[] = $page_name . '.' . $page_extension;
            }
        }

        if (empty($combined_data)) {
            echo "Please add at least one page.";
            return;
        }

        if ($this->Modal_pages_model && $this->Modal_pages_model->save_pages($combined_data, $page_data)) {
            if ($this->request->isAJAX()) {
                $savedPageRows = $this->extractPages($page_data['user_id'], $page_data['modelno'], $page_data['modal_id']);
                $savedPages = array();

                foreach ($savedPageRows as $pageRow) {
                    if (empty($pageRow['page_names'])) {
                        continue;
                    }

                    foreach (explode(',', $pageRow['page_names']) as $pageName) {
                        $pageName = trim((string) $pageName);

                        if ($pageName !== '' && !in_array($pageName, $savedPages, true)) {
                            $savedPages[] = $pageName;
                        }
                    }
                }

                return $this->response->setJSON(array(
                    'success' => true,
                    'message' => 'Data saved successfully!',
                    'pages' => $savedPages
                ));
            }

            echo "Data saved successfully!";
        } else {
            $dbError = ($this->Modal_pages_model && method_exists($this->Modal_pages_model, 'get_last_db_error'))
                ? $this->Modal_pages_model->get_last_db_error()
                : array();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(array(
                    'success' => false,
                    'message' => !empty($dbError['message']) ? 'Error saving data to database! ' . $dbError['message'] : 'Error saving data to database!'
                ));
            }

            echo "Error saving data to database!";

            if (!empty($dbError['message'])) {
                echo " " . esc($dbError['message']);
            }
        }
    }

    public function delete_saved_page()
    {
        $user_id = $this->currentUserId();
        $page_name = trim((string) $this->request->getPost('page_name'));
        $modal_id = $this->request->getPost('modal_id');
        $modelno = $this->request->getPost('modelno');

        if ($page_name === '' || $modal_id === null || $modal_id === '') {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Missing required data for page deletion.'
            ));
        }

        if ($this->Modal_pages_model) {
            $this->Modal_pages_model->delete_saved_page($modal_id, $user_id, $page_name);
            $this->Modal_pages_model->delete_legacy_page_name($modal_id, $modelno, $page_name);
        }

        $savedPages = $this->flattenPageNames(
            $this->extractPages($user_id, $modelno, $modal_id)
        );

        if (!in_array($page_name, $savedPages, true)) {
            return $this->response->setJSON(array(
                'success' => true,
                'message' => 'Saved page deleted successfully.',
                'pages' => $savedPages
            ));
        }

        return $this->response->setJSON(array(
            'success' => false,
            'message' => 'Unable to delete the saved page.'
        ));
    }

    public function reorder_saved_pages()
    {
        $user_id = $this->currentUserId();
        $modal_id = $this->request->getPost('modal_id');
        $modelno = $this->request->getPost('modelno');
        $pagesJson = (string) $this->request->getPost('pages');
        $pages = json_decode($pagesJson, true);

        if (!is_array($pages)) {
            $pages = (array) $this->request->getPost('pages');
        }

        $cleanPages = array();
        foreach ($pages as $pageName) {
            $pageName = trim((string) $pageName);
            if ($pageName !== '') {
                $cleanPages[] = $pageName;
            }
        }
        $pages = array_values($cleanPages);

        if ($modal_id === null || $modal_id === '' || empty($pages)) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Missing page order data.'
            ));
        }

        $reordered = ($this->Modal_pages_model)
            ? $this->Modal_pages_model->reorder_saved_pages($modal_id, $user_id, $pages)
            : false;
        $savedPages = $this->flattenPageNames(
            $this->extractPages($user_id, $modelno, $modal_id)
        );

        if ($reordered || !empty($savedPages)) {
            return $this->response->setJSON(array(
                'success' => true,
                'message' => 'Page order saved successfully.',
                'pages' => !empty($savedPages) ? $savedPages : $pages
            ));
        }

        return $this->response->setJSON(array(
            'success' => false,
            'message' => 'Unable to save page order.'
        ));
    }
}
