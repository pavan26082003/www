<?php

namespace App\Models;

use CodeIgniter\Model;

class Modal_api_page_linking_model extends Model
{
    /** @var View_page_model */
    private $storage;

    public function __construct()
    {
        parent::__construct();

        $class = '\\App\\Models\\View_page_model';
        if (!class_exists($class, false)) {
            foreach (array('View_page_model.php', 'view_page_model.php') as $fileName) {
                $modelFile = APPPATH . 'Models/' . $fileName;
                if (is_file($modelFile)) {
                    require_once $modelFile;
                    break;
                }
            }
        }

        $this->storage = class_exists($class, false) ? new $class() : null;
    }

    public function resolveLinkingModelNo($itemId, $requested = '')
    {
        if ($this->storage && method_exists($this->storage, 'resolveLinkingModelNo')) {
            return $this->storage->resolveLinkingModelNo((int) $itemId, $requested);
        }

        $requested = trim((string) $requested);
        if ($requested !== '') {
            return $requested;
        }

        return $this->getModelNameForItem($itemId);
    }

    public function getModelNameForItem($itemId)
    {
        if (!$this->storage || !method_exists($this->storage, 'getModelNameForItem')) {
            return '';
        }

        return $this->storage->getModelNameForItem((int) $itemId);
    }

    public function getApiParameterNamesForLinking($itemId = 0, $userId = null, $modelno = '')
    {
        if (!$this->storage || !method_exists($this->storage, 'getApiParameterNamesForLinking')) {
            return array();
        }

        $names = $this->storage->getApiParameterNamesForLinking($itemId, $userId, $modelno);
        return is_array($names) ? $names : array();
    }

    public function extract_pages($userId, $modelno, $id = null)
    {
        if (!$this->storage || !method_exists($this->storage, 'extract_pages')) {
            return array();
        }

        $pages = $this->storage->extract_pages($userId, $modelno, $id);
        return is_array($pages) ? $pages : array();
    }

    public function getPageParameterMapping($modelno)
    {
        if (!$this->storage || !method_exists($this->storage, 'getPageParameterMapping')) {
            return null;
        }

        return $this->storage->getPageParameterMapping($modelno);
    }

    public function getLinkingPageOptions($userId, $modelno, $id = null)
    {
        if (!$this->storage || !method_exists($this->storage, 'getLinkingPageOptions')) {
            return array();
        }

        $options = $this->storage->getLinkingPageOptions($userId, $modelno, $id);
        return is_array($options) ? $options : array();
    }

    public function getProductApiPageParameterRows($modelno, $userId = null, $itemId = 0)
    {
        if (!$this->storage || !method_exists($this->storage, 'getProductApiPageParameterRows')) {
            return array();
        }

        $rows = $this->storage->getProductApiPageParameterRows($modelno, $userId, $itemId);
        return is_array($rows) ? $rows : array();
    }

    public function preserveLockedProductApiLinking($modelno, array $pageNameMap, array $linkingData, array $uiRows, $userId = null, $itemId = 0)
    {
        if (!$this->storage || !method_exists($this->storage, 'preserveLockedProductApiLinking')) {
            return array($pageNameMap, $linkingData, $uiRows);
        }

        return $this->storage->preserveLockedProductApiLinking(
            $modelno,
            $pageNameMap,
            $linkingData,
            $uiRows,
            $userId,
            $itemId
        );
    }

    public function savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows = array(), $replace = false)
    {
        if (!$this->storage || !method_exists($this->storage, 'savePageParameterJsonMapping')) {
            return false;
        }

        return $this->storage->savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, $replace);
    }
}
