<?php

namespace App\Models;

if (!class_exists(View_page_model::class, false)) {
    foreach (array('View_page_model.php', 'view_page_model.php') as $fileName) {
        $modelFile = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($modelFile)) {
            require_once $modelFile;
            break;
        }
    }
}

/**
 * Model for the Modal API Page Linking screen only.
 * Saved page-to-API rows stay in this model so the linking screen does not
 * keep a second copy of the View Page UI model.
 */
class Modal_api_page_linking_model extends View_page_model
{
    public function resolveLinkingModelNo($itemId, $requested = '')
    {
        return parent::resolveLinkingModelNo((int) $itemId, $requested);
    }

    public function getModelNameForItem($itemId)
    {
        return parent::getModelNameForItem((int) $itemId);
    }

    public function getApiParameterNamesForLinking($itemId = 0, $userId = null, $modelno = '')
    {
        $names = parent::getApiParameterNamesForLinking($itemId, $userId, $modelno);
        return is_array($names) ? $names : array();
    }

    public function extract_pages($userId, $modelno, $id = null)
    {
        $pages = parent::extract_pages($userId, $modelno, $id);
        return is_array($pages) ? $pages : array();
    }

    public function getPageParameterMapping($modelno)
    {
        return parent::getPageParameterMapping($modelno);
    }

    public function getLinkingPageOptions($userId, $modelno, $id = null)
    {
        $options = parent::getLinkingPageOptions($userId, $modelno, $id);
        return is_array($options) ? $options : array();
    }

    public function getProductApiPageParameterRows($modelno, $userId = null, $itemId = 0)
    {
        $rows = parent::getProductApiPageParameterRows($modelno, $userId, $itemId);
        return is_array($rows) ? $rows : array();
    }

    public function preserveLockedProductApiLinking($modelno, array $pageNameMap, array $linkingData, array $uiRows, $userId = null, $itemId = 0)
    {
        return parent::preserveLockedProductApiLinking(
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
        return parent::savePageParameterJsonMapping($modelno, $pageNameMap, $linkingData, $uiRows, $replace);
    }
}
